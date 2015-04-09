<?PHP
# redirection si acces dirrect
if(substr_count($_SERVER['PHP_SELF'], 'index.php')==0){
	header("Location: ../");
	exit();
}
/*******************************************************************
Interface changement action
Version 1.0.0
29/09/2010 - VGU - Creation fichier
**************************************************Guibert Vincent***
*******************************************************************/

require("./cf/conf_outil_icdc.php");
require_once("./cf/fonctions.php");
$j=0;
$DATE_TEST=0;
$Date_Inter_Fin='';
$Date_Inter_Debut='';
$ID_APPLI_MODIF='';
$information='';
$APPLI_TEST=0;
$INTER_TEST=0;
$STOP_information=0;
$STOP=0;
$STOP_insert=0;
$DATE_I=date("d/m/Y H:i:s");
$MOIS_Inter_Debut=substr($DATE_I,3,2);
$ANNEE_Inter_Debut=substr($DATE_I,6,4);

if(isset($_GET['action'])){
	$action=$_GET['action'];
}
else{
	$action='Ajout';
}

$tab_var=$_POST;
if(isset($tab_var['INTER'])){
	$action='Modif';
}

if(isset($_GET['ID'])){
	if(is_numeric($_GET['ID'])){
		$ID=$_GET['ID'];
	}
}

if(isset($tab_var['ID'])){
	if(is_numeric($tab_var['ID'])){
		$ID=$tab_var['ID'];
	}
}

if(isset($_SESSION['LOGIN'])){
	$LOGIN=$_SESSION['LOGIN'];
	$rq_Selectionner_user ="SELECT `UTILISATEUR_ID`,`LOGIN`, `NOM`, `PRENOM`
	FROM `moteur_utilisateur`
	WHERE `LOGIN` = '".$LOGIN."'";
	$res_rq_Selectionner_user = mysql_query($rq_Selectionner_user, $mysql_link) or die(mysql_error());
	$tab_rq_Selectionner_user = mysql_fetch_assoc($res_rq_Selectionner_user);
	$total_ligne_Selectionner_user = mysql_num_rows($res_rq_Selectionner_user);
	if($total_ligne_Selectionner_user==0){
		$NOM='';
		$UTILISATEUR_ID=0;
		$PRENOM='';
		$LOGIN='';
	}else{
	$NOM=$tab_rq_Selectionner_user['NOM'];
	$UTILISATEUR_ID=$tab_rq_Selectionner_user['UTILISATEUR_ID'];
	$PRENOM=$tab_rq_Selectionner_user['PRENOM'];
	$LOGIN=$tab_rq_Selectionner_user['LOGIN'];
}
mysql_free_result($res_rq_Selectionner_user);
}else{
$NOM='';
$UTILISATEUR_ID=0;
$PRENOM='';
$LOGIN='';
}

if(empty($tab_var['btn'])){
}else{

	# Cas Ajouter
	if($tab_var['btn']=="Ajouter"){

	$Date_Inter_Debut=addslashes(trim(htmlentities($tab_var['txt_Date_Inter_Debut'])));
	$Date_Inter_Fin=addslashes(trim(htmlentities($tab_var['txt_Date_Inter_Fin'])));
	$information=addslashes(trim(htmlentities($tab_var['txt_information'])));
	$ID_APPLI=$tab_var['application'];
	$ID_APPLI_MODIF=$tab_var['application'];

	# test des jours
	$jour='';
	$mois='';
	$annee='';
	$jour=substr($Date_Inter_Debut,0,2);
	$mois=substr($Date_Inter_Debut,3,2);
	$annee=substr($Date_Inter_Debut,6,4);
	$Date_Inter_Debut_bdd=$annee.''.$mois.''.$jour;
	$jour='';
	$mois='';
	$annee='';
	$jour=substr($Date_Inter_Fin,0,2);
	$mois=substr($Date_Inter_Fin,3,2);
	$annee=substr($Date_Inter_Fin,6,4);
	$Date_Inter_Fin_bdd=$annee.''.$mois.''.$jour;

	if($Date_Inter_Fin_bdd < $Date_Inter_Debut_bdd){
		$STOP=1;
		$DATE_TEST=1;
	}
	
	if($ID_APPLI=='0'){
		$STOP=1;
		$APPLI_TEST=1;
	}

	# test information
	if($information==''){
		$STOP=1;
		$STOP_information=1;
	}

	if($STOP==0){
		$sql="INSERT INTO `va_intervention` (`VA_INTERVENTION_ID` ,`VA_INTERVENTION_UTILISATEUR_ID` ,`VA_INTERVENTION_DATE_DEBUT` ,`VA_INTERVENTION_DATE_FIN`,`VA_INTERVENTION_DATE_CREATION` ,`VA_INTERVENTION_LIBELLE` ,`ENABLE`,`VA_INTERVENTION_CODE_APPLI` )VALUES (NULL , '".$UTILISATEUR_ID." ', '".$Date_Inter_Debut_bdd."', '".$Date_Inter_Fin_bdd."', '".$DATE_I."', '".$information."', '0', '".$ID_APPLI."');";
		mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
		$TABLE_SQL_SQL='va_intervention';
		historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');
		$sql="OPTIMIZE TABLE `va_intervention` ";
		mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
		$TABLE_SQL_SQL='va_intervention';
		historique_sql_new($sql,$TABLE_SQL_SQL,'OPTIMIZE');
		$rq_info="
		SELECT `VA_INTERVENTION_ID`
		FROM `va_intervention`
		WHERE `VA_INTERVENTION_UTILISATEUR_ID` ='".$UTILISATEUR_ID."'
		AND `VA_INTERVENTION_DATE_DEBUT` ='".$Date_Inter_Debut_bdd."'
		AND `VA_INTERVENTION_DATE_FIN` ='".$Date_Inter_Fin_bdd."'
		AND `VA_INTERVENTION_LIBELLE` ='".$information."'
		AND `VA_INTERVENTION_CODE_APPLI` = '".$ID_APPLI."'
		AND `ENABLE` ='0'
		LIMIT 1";
		$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
		$tab_rq_info = mysql_fetch_assoc($res_rq_info);
		$total_ligne_rq_info=mysql_num_rows($res_rq_info);
		$INTERVENTION_ID=$tab_rq_info['VA_INTERVENTION_ID'];
		$TRACE_CATEGORIE='va';
	        $TRACE_TABLE='va_intervention';
	        $TRACE_REF_ID=$INTERVENTION_ID;
	        $TRACE_ACTION='Ajout';
	        $TRACE_ETAT='';
	        moteur_trace($TRACE_CATEGORIE,$TRACE_TABLE,$TRACE_REF_ID,$TRACE_ACTION,$TRACE_ETAT);

		mysql_free_result($res_rq_info);
		# ajout date pour le calendrier
		$DATE_INSERT=$Date_Inter_Debut_bdd;

		while ($STOP_insert != 1) {
			if($DATE_INSERT == $Date_Inter_Fin_bdd ){
				$STOP_insert=1;
			}
			$date=$DATE_INSERT;
			$jour=substr($date,6,2);
			$mois=substr($date,4,2);
			$annee=substr($date,0,4);
			$INTERVENTION_SEMAINE=date("W", mktime(12, 0, 0, $mois, $jour , $annee));
			$rq_date_histo_info="
			SELECT `VA_DATE_ID`
			FROM `va_date`
			WHERE `VA_DATE` ='".$DATE_INSERT."'
			AND `VA_INTERVENTION_ID` ='".$INTERVENTION_ID."'
			AND `VA_DATE_SEMAINE` ='".$INTERVENTION_SEMAINE."'
			AND `ENABLE` ='0'
			LIMIT 1";
			$res_rq_date_histo_info = mysql_query($rq_date_histo_info, $mysql_link) or die(mysql_error());
			$tab_rq_date_histo_info = mysql_fetch_assoc($res_rq_date_histo_info);
			$total_ligne_rq_date_histo_info=mysql_num_rows($res_rq_date_histo_info);
			mysql_free_result($res_rq_date_histo_info);
			if($total_ligne_rq_date_histo_info==0){
				$sql="
				INSERT INTO `va_date` (`VA_DATE_ID` ,`VA_DATE` ,`VA_DATE_SEMAINE` ,`VA_INTERVENTION_ID` ,`ENABLE` )
				VALUES (NULL , '".$DATE_INSERT."', '".$INTERVENTION_SEMAINE."', '".$INTERVENTION_ID."', '0');";
				mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
				$TABLE_SQL_SQL='va_date';
				historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');
			}
			$DATE_INSERT=date("Ymd", mktime(12, 0, 0, $mois, $jour + 1, $annee));

		}
		$sql="OPTIMIZE TABLE `va_date` ";
		mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
		$TABLE_SQL_SQL='va_date';
		historique_sql_new($sql,$TABLE_SQL_SQL,'OPTIMIZE');
		$MOIS_Inter_Debut=substr($Date_Inter_Debut_bdd,4,2);
		$ANNEE_Inter_Debut=substr($Date_Inter_Debut_bdd,0,4);
		echo '
		<script language="JavaScript">
		url=("./index.php?ITEM=inter_va_Calendrier&m='.$MOIS_Inter_Debut.'&y='.$ANNEE_Inter_Debut.'");
		window.location=url;
		</script>
		';
	}
	}
	$_GET['action']="Ajout";

	# Cas Modifier
	if($tab_var['btn']=="Modifier"){

		$Date_Inter_Debut=addslashes(trim(htmlentities($tab_var['txt_Date_Inter_Debut'])));
		$Date_Inter_Fin=addslashes(trim(htmlentities($tab_var['txt_Date_Inter_Fin'])));
		$information=addslashes(trim(htmlentities($tab_var['txt_information'])));
		$ID_APPLI=$tab_var['application'];
		
		# test des jours
		$jour='';
		$mois='';
		$annee='';
		$jour=substr($Date_Inter_Debut,0,2);
		$mois=substr($Date_Inter_Debut,3,2);
		$annee=substr($Date_Inter_Debut,6,4);
		$Date_Inter_Debut_bdd=$annee.''.$mois.''.$jour;
		$jour='';
		$mois='';
		$annee='';
		$jour=substr($Date_Inter_Fin,0,2);
		$mois=substr($Date_Inter_Fin,3,2);
		$annee=substr($Date_Inter_Fin,6,4);
		$Date_Inter_Fin_bdd=$annee.''.$mois.''.$jour;
		
		$ID_INTER=$tab_var['INTER'];
		$req_inter_base="
		SELECT *
		FROM va_intervention
		WHERE `VA_INTERVENTION_ID`='".$ID_INTER."';";
		$res_rq_inter_base = mysql_query($req_inter_base, $mysql_link) or die(mysql_error());
		$tab_rq_inter_base = mysql_fetch_assoc($res_rq_inter_base);
		$BASE_DATE_DEB=$tab_rq_inter_base['VA_INTERVENTION_DATE_DEBUT'];
		$BASE_DATE_FIN=$tab_rq_inter_base['VA_INTERVENTION_DATE_FIN'];
		$BASE_LIBELLE=$tab_rq_inter_base['VA_INTERVENTION_LIBELLE'];
		$BASE_APPLI=$tab_rq_inter_base['VA_INTERVENTION_CODE_APPLI'];
		
		if($BASE_DATE_DEB==$Date_Inter_Debut_bdd){
			if($BASE_DATE_FIN==$Date_Inter_Fin_bdd){
				$EXEC_REQ_TDATE=0;
				if($BASE_LIBELLE==$information){
					if($BASE_APPLI==$ID_APPLI){
						$EXEC_REQ_UPDATE_TINTER=0;
					}else{
					$EXEC_REQ_UPDATE_TINTER=1;
					}
				}else{
				$EXEC_REQ_UPDATE_TINTER=1;
				}
			}else{
			$EXEC_REQ_UPDATE_TINTER=1;
			$EXEC_REQ_TDATE=1;
			}
		}else{
		$EXEC_REQ_UPDATE_TINTER=1;
		$EXEC_REQ_TDATE=1;
		}
		
		if($EXEC_REQ_UPDATE_TINTER==1){
			if($Date_Inter_Fin_bdd < $Date_Inter_Debut_bdd){
				$STOP=1;
				$DATE_TEST=1;
			}
			if($ID_APPLI==''){
				$STOP=1;
				$APPLI_TEST=1;
			}
			if($ID_APPLI=='0'){
				$STOP=1;
				$APPLI_TEST=1;
			}
			if($information==''){
				$STOP=1;
				$STOP_information=1;
			}
			if($STOP==0){
				$sql="
				UPDATE `va_intervention`
				set `VA_INTERVENTION_UTILISATEUR_ID`='".$UTILISATEUR_ID."',`VA_INTERVENTION_DATE_DEBUT`='".$Date_Inter_Debut_bdd."',`VA_INTERVENTION_DATE_FIN`='".$Date_Inter_Fin_bdd."',`VA_INTERVENTION_LIBELLE`='".$information."',`ENABLE`='0',`VA_INTERVENTION_CODE_APPLI`='".$ID_APPLI."'
				where `VA_INTERVENTION_ID`='".$ID_INTER."';";
				mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
				$TABLE_SQL_SQL='va_intervention';
				historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');
				$sql="OPTIMIZE TABLE `va_intervention` ";
				mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
				$TABLE_SQL_SQL='va_intervention';
				historique_sql_new($sql,$TABLE_SQL_SQL,'OPTIMIZE');
				$rq_info="
				SELECT `VA_INTERVENTION_ID`
				FROM `va_intervention`
				WHERE `VA_INTERVENTION_UTILISATEUR_ID` ='".$UTILISATEUR_ID."'
				AND `VA_INTERVENTION_DATE_DEBUT` ='".$Date_Inter_Debut_bdd."'
				AND `VA_INTERVENTION_DATE_FIN` ='".$Date_Inter_Fin_bdd."'
				AND `VA_INTERVENTION_LIBELLE` ='".$information."'
				AND `VA_INTERVENTION_CODE_APPLI` = '".$ID_APPLI."'
				AND `ENABLE` ='0'
				LIMIT 1";
				$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
				$tab_rq_info = mysql_fetch_assoc($res_rq_info);
				$total_ligne_rq_info=mysql_num_rows($res_rq_info);
				$INTERVENTION_ID=$tab_rq_info['VA_INTERVENTION_ID'];
				$TRACE_CATEGORIE='va';
			        $TRACE_TABLE='va_intervention';
			        $TRACE_REF_ID=$INTERVENTION_ID;
			        $TRACE_ACTION='Modification';
			        $TRACE_ETAT='';
			        moteur_trace($TRACE_CATEGORIE,$TRACE_TABLE,$TRACE_REF_ID,$TRACE_ACTION,$TRACE_ETAT);
				mysql_free_result($res_rq_info);
				# ajout date pour le calendrier
				$DATE_INSERT=$Date_Inter_Debut_bdd;
				
				if ($EXEC_REQ_TDATE==1){
					# drop des entrées de va_date correspondant à l'inter modifée
					$req_suppr_date="
					DELETE
					from `va_date`
					where `VA_INTERVENTION_ID`=".$ID_INTER.";";
					mysql_query($req_suppr_date) or die('Erreur SQL !'.$req_suppr_date.''.mysql_error());
					$TABLE_SQL_SQL='va_date';
					historique_sql_new($req_suppr_date,$TABLE_SQL_SQL,'DELETE');
					
					# insert dans la table va_date
					while ($STOP_insert != 1) {
						if($DATE_INSERT == $Date_Inter_Fin_bdd ){
							$STOP_insert=1;
						}
						$date=$DATE_INSERT;
						$jour=substr($date,6,2);
						$mois=substr($date,4,2);
						$annee=substr($date,0,4);
						$INTERVENTION_SEMAINE=date("W", mktime(12, 0, 0, $mois, $jour , $annee));
						$rq_date_histo_info="
						SELECT `VA_DATE_ID`
						FROM `va_date`
						WHERE `VA_DATE` ='".$DATE_INSERT."'
						AND `VA_INTERVENTION_ID` ='".$INTERVENTION_ID."'
						AND `VA_DATE_SEMAINE` ='".$INTERVENTION_SEMAINE."'
						AND `ENABLE` ='0'
						LIMIT 1";
						$res_rq_date_histo_info = mysql_query($rq_date_histo_info, $mysql_link) or die(mysql_error());
						$tab_rq_date_histo_info = mysql_fetch_assoc($res_rq_date_histo_info);
						$total_ligne_rq_date_histo_info=mysql_num_rows($res_rq_date_histo_info);
						mysql_free_result($res_rq_date_histo_info);
						if($total_ligne_rq_date_histo_info==0){
							$sql="
							INSERT INTO `va_date` (`VA_DATE_ID` ,`VA_DATE` ,`VA_DATE_SEMAINE` ,`VA_INTERVENTION_ID` ,`ENABLE` )
							VALUES (NULL , '".$DATE_INSERT."', '".$INTERVENTION_SEMAINE."', '".$INTERVENTION_ID."', '0');";
							mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
							$TABLE_SQL_SQL='va_date';
							historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');
						}
						$DATE_INSERT=date("Ymd", mktime(12, 0, 0, $mois, $jour + 1, $annee));
		
					}
					$sql="OPTIMIZE TABLE `va_date` ";
					mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
					$TABLE_SQL_SQL='va_date';
					historique_sql_new($sql,$TABLE_SQL_SQL,'OPTIMIZE');
				}
				$MOIS_Inter_Debut=substr($Date_Inter_Debut_bdd,4,2);
				$ANNEE_Inter_Debut=substr($Date_Inter_Debut_bdd,0,4);
				echo '
				<script language="JavaScript">
				url=("./index.php?ITEM=inter_va_Calendrier&m='.$MOIS_Inter_Debut.'&y='.$ANNEE_Inter_Debut.'");
				window.location=url;
				</script>
				';
			}
		}else{
		$MOIS_Inter_Debut=substr($Date_Inter_Debut_bdd,4,2);
		$ANNEE_Inter_Debut=substr($Date_Inter_Debut_bdd,0,4);
		echo '
		<script language="JavaScript">
		url=("./index.php?ITEM=inter_va_Calendrier&m='.$MOIS_Inter_Debut.'&y='.$ANNEE_Inter_Debut.'");
		window.location=url;
		</script>
		';
	}
	$_GET['action']="Modif";	
	}
	
	
	# Cas Suppression
	if($tab_var['btn']=="Supprimer"){
		$ID_INTER=$tab_var['INTER'];
		if ($ID_INTER!=''){
			# drop de l'inter dans la table `va_intervention`
			$sql_suppr_inter="
			UPDATE `va_intervention`
			set `ENABLE`=1
			where `VA_INTERVENTION_ID`=".$ID_INTER.";";
			mysql_query($sql_suppr_inter) or die('Erreur SQL !'.$sql_suppr_inter.''.mysql_error());
			$TABLE_SQL_SQL='va_intervention';
			historique_sql_new($sql_suppr_inter,$TABLE_SQL_SQL,'DELETE');
			$TRACE_CATEGORIE='va';
		        $TRACE_TABLE='va_intervention';
		        $TRACE_REF_ID=$ID_INTER;
		        $TRACE_ACTION='Suppression';
		        $TRACE_ETAT='';
		        moteur_trace($TRACE_CATEGORIE,$TRACE_TABLE,$TRACE_REF_ID,$TRACE_ACTION,$TRACE_ETAT);
						
			# drop des entrées de va_date correspondant à l'inter modifée
			$req_suppr_date="
			UPDATE `va_date`
			set `ENABLE`=1
			where `VA_INTERVENTION_ID`=".$ID_INTER.";";
			mysql_query($req_suppr_date) or die('Erreur SQL !'.$req_suppr_date.''.mysql_error());
			$TABLE_SQL_SQL='va_date';
			historique_sql_new($req_suppr_date,$TABLE_SQL_SQL,'DELETE');
			
			# optimize des tables
			$sql="OPTIMIZE TABLE `va_intervention` ";
			mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
			$TABLE_SQL_SQL='va_intervention';
			historique_sql_new($sql,$TABLE_SQL_SQL,'OPTIMIZE');
			$sql="OPTIMIZE TABLE `va_date` ";
			mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
			$TABLE_SQL_SQL='va_date';
			historique_sql_new($sql,$TABLE_SQL_SQL,'OPTIMIZE');
			echo '
			<script language="JavaScript">
			url=("./index.php?ITEM=inter_va_Gestion_liste");
			window.location=url;
			</script>
			';
		}
	}
}

# Cas action ajout
if($_GET['action']=="Ajout"){

	if(isset($_POST['txt_Date_Inter_Fin'])){
		$Date_Inter_Fin=$_POST['txt_Date_Inter_Fin'];
	}else{
		$Date_Inter_Fin=date("d/m/Y");
		if(isset($_POST['txt_Date_Inter_Debut'])){
			$Date_Inter_Debut=$_POST['txt_Date_Inter_Debut'];
			$MOIS_Inter_Debut=substr($Date_Inter_Debut,3,2);
			$ANNEE_Inter_Debut=substr($Date_Inter_Debut,6,4);
		}else{
			if(isset($_GET['ANNEE'])){
				$ANNEE_Inter_Debut=$_GET['ANNEE'];
			}else{
				$ANNEE_Inter_Debut=date("Y");
			}
			if(isset($_GET['MOIS'])){
				$MOIS_Inter_Debut=$_GET['MOIS'];
				if(strlen($MOIS_Inter_Debut) == 1 ){
					$MOIS_Inter_Debut='0'.$MOIS_Inter_Debut;
				}
			}else{
				$MOIS_Inter_Debut=date("m");
			}
			if(isset($_GET['JOUR'])){
				$JOUR_Inter_Debut=$_GET['JOUR'];
				if(strlen($JOUR_Inter_Debut) == 1 ){
					$JOUR_Inter_Debut='0'.$JOUR_Inter_Debut;
				}
			}else{
				$JOUR_Inter_Debut=date("d");
			}
			$Date_Inter_Debut=$JOUR_Inter_Debut.'/'.$MOIS_Inter_Debut.'/'.$ANNEE_Inter_Debut;
			$Date_Inter_Fin=$JOUR_Inter_Debut.'/'.$MOIS_Inter_Debut.'/'.$ANNEE_Inter_Debut;
		}
	}
	if(isset($_POST['application'])){
		$ID_APPLI=$_POST['application'];
	}
}

# Cas action modifier
if($_GET['action']=="Modif"){
	
	if(isset($_GET['INTER'])){
	$ID_INTER=$_GET['INTER'];
	}else{
		$ID_INTER=$tab_var['INTER'];
	}
	$req_modif_inter = "
	select *
	from `va_intervention`
	where `VA_INTERVENTION_ID` = ".$ID_INTER."
	";

	$res_req_modif_inter = mysql_query($req_modif_inter, $mysql_link) or die(mysql_error());
	$tab_req_modif_inter = mysql_fetch_assoc($res_req_modif_inter);
	$total_ligne_req_modif_inter = mysql_num_rows($res_req_modif_inter);

	$ID_APPLI_MODIF=$tab_req_modif_inter['VA_INTERVENTION_CODE_APPLI'];
	$information=$tab_req_modif_inter['VA_INTERVENTION_LIBELLE'];
	$DATE_DEB=$tab_req_modif_inter['VA_INTERVENTION_DATE_DEBUT'];
	$deb_jour = substr($DATE_DEB,6,2);
	$deb_mois = substr($DATE_DEB,4,2);
	$deb_year = substr($DATE_DEB,0,4);
	$Date_Inter_Debut = ($deb_jour.'/'.$deb_mois.'/'.$deb_year);
	$DATE_FIN=$tab_req_modif_inter['VA_INTERVENTION_DATE_FIN'];
	$fin_jour = substr($DATE_FIN,6,2);
	$fin_mois = substr($DATE_FIN,4,2);
	$fin_year = substr($DATE_FIN,0,4);
	$Date_Inter_Fin = ($fin_jour.'/'.$fin_mois.'/'.$fin_year);;
}
# Cas action Informations
if($_GET['action']=="Info"){
	
	if(isset($_GET['INTER'])){
	$ID_INTER=$_GET['INTER'];
	}else{
		$ID_INTER=$tab_var['INTER'];
	}
	$req_info_inter = "
	select *
	from `va_intervention`
	where `VA_INTERVENTION_ID` = ".$ID_INTER."
	";

	$res_req_info_inter = mysql_query($req_info_inter, $mysql_link) or die(mysql_error());
	$tab_req_info_inter = mysql_fetch_assoc($res_req_info_inter);
	$total_ligne_req_info_inter = mysql_num_rows($res_req_info_inter);

	$ID_APPLI_INFO=$tab_req_info_inter['VA_INTERVENTION_CODE_APPLI'];
	$information=$tab_req_info_inter['VA_INTERVENTION_LIBELLE'];
	$DATE_DEB=$tab_req_info_inter['VA_INTERVENTION_DATE_DEBUT'];
	$deb_jour = substr($DATE_DEB,6,2);
	$deb_mois = substr($DATE_DEB,4,2);
	$deb_year = substr($DATE_DEB,0,4);
	$Date_Inter_Debut = ($deb_jour.'/'.$deb_mois.'/'.$deb_year);
	$DATE_FIN=$tab_req_info_inter['VA_INTERVENTION_DATE_FIN'];
	$fin_jour = substr($DATE_FIN,6,2);
	$fin_mois = substr($DATE_FIN,4,2);
	$fin_year = substr($DATE_FIN,0,4);
	$Date_Inter_Fin = ($fin_jour.'/'.$fin_mois.'/'.$fin_year);;
}
if($action=="Info"){
	echo '
	<!--D&eacute;but page HTML -->
	<div align="center">
	<form method="post" name="frm_inter" id="frm_inter" action="./index.php?ITEM='.$_GET['ITEM'].'">
	<table class="table_inc">
	<tr align="center" class="titre">
	<td colspan="2"><h2>&nbsp;[&nbsp;Informations sur une Intervention&nbsp;]&nbsp;</h2></td>
	</tr>
	';
	
	$j++;
	if ($j%2) { $class = "pair";}else{$class = "impair";}
	echo '
	<tr class="'.$class.'">
	<td align="left">&nbsp;Date de D&eacute;but&nbsp;</td>
	<td align="left">
	<input name="txt_Date_Inter_Debut" type="text" readonly value="'.$Date_Inter_Debut.'" size="10"/>
	</td>
	</tr>';
	
	$j++;
	if ($j%2) { $class = "pair";}else{$class = "impair";}
	echo '
	<tr class="'.$class.'">
	<td align="left">&nbsp;Date de Fin&nbsp;</td>
	<td align="left">
	<input name="txt_Date_Inter_Fin" type="text" readonly value="'.$Date_Inter_Fin.'" size="10"/>
	</td>
	</tr>';
	
	$j++;
	if ($j%2) { $class = "pair";}else{$class = "impair";}
	echo'
	<tr class="'.$class.'">
	<td align="left">&nbsp;Application concernée&nbsp;</td>
	<td align="left">
	<input name="txt_Appli" type="text" readonly value="'.$ID_APPLI_INFO.'" size="10"/>
	</td>
	</tr>
	';

	$j++;
	if ($j%2) { $class = "pair";}else{$class = "impair";}
	echo '
	<tr class="'.$class.'">
	<td align="left">&nbsp;Libellé de l\'intervention&nbsp;</td>
	<td align="left" colspan="1"><textarea READONLY id="txt_information" name="txt_information" cols=40 rows=2>'.stripslashes($information).'
	</textarea>
	</td>
	</tr>';
	
	echo '
	<tr class="titre">
	<td colspan="2" align="center">
	<h2>&nbsp;[&nbsp;<a href="./index.php?ITEM=inter_va_Gestion_Liste_all">Retour - Liste Admin</a>&nbsp;]&nbsp;</h2>
	</td>
	</tr>
	</table>
	</form>
	<br/><br/>
	</div>
	';
}
if($action=="Ajout"){
	echo '
	<!--D&eacute;but page HTML -->
	<div align="center">
	<form method="post" name="frm_inter" id="frm_inter" action="./index.php?ITEM='.$_GET['ITEM'].'">
	<table class="table_inc">
	<tr align="center" class="titre">
	<td colspan="2"><h2>&nbsp;[&nbsp;Ajout d\'une Intervention de VA&nbsp;]&nbsp;</h2></td>
	</tr>
	';
	
	$j++;
	if ($j%2) { $class = "pair";}else{$class = "impair";}
	echo '
	<tr class="'.$class.'">
	<td align="left">&nbsp;Date de D&eacute;but&nbsp;</td>
	<td align="left">
	<input name="txt_Date_Inter_Debut" type="text" readonly value="'.$Date_Inter_Debut.'" size="10"/>';?>
	<a href="#" onClick=" window.open('./cf/calendrier/pop.php?frm=frm_inter&amp;ch=txt_Date_Inter_Debut','calendrier','width=350,height=160,scrollbars=0').focus();">
	<img src="./cf/calendrier/petit_calendrier.png" border="0"/></a><?PHP echo '
	</td>
	</tr>';
	
	$j++;
	if ($j%2) { $class = "pair";}else{$class = "impair";}
	echo '
	<tr class="'.$class.'">
	<td align="left">&nbsp;Date de Fin&nbsp;</td>
	<td align="left">
	<input name="txt_Date_Inter_Fin" type="text" readonly value="'.$Date_Inter_Fin.'" size="10"/>';?>
	<a href="#" onClick=" window.open('./cf/calendrier/pop.php?frm=frm_inter&amp;ch=txt_Date_Inter_Fin','calendrier','width=350,height=160,scrollbars=0').focus();">
	<img src="./cf/calendrier/petit_calendrier.png" border="0"/></a><?PHP echo '
	</td>
	</tr>';
	
	if($DATE_TEST==1){
		$j++;
		if ($j%2) { $class = "pair";}else{$class = "impair";}
		echo '
		<tr class="'.$class.'">
		<td align="center" colspan="2"><font color=#993333><b>Erreurs Sur les dates</b></font>
		</td>
		</tr>';
	}
	
	$j++;
	if ($j%2) { $class = "pair";}else{$class = "impair";}
	echo'
	<tr class="'.$class.'">
	<td align="left">&nbsp;Application concernée&nbsp;</td>
	<td align="left">
	<SELECT name="application" size="1" id="appli" onChange="">
	<OPTION value="0"></OPTION>
	';
	//require("./cf/conf_portail_ope.php");
	$req_liste_appli = "
	select `id_appli`, `libelle_appli`
	from `referentiel_appli`
	where `id_appli` != 'Tout'
	order by `id_appli` ASC ;";
	$res_req_liste_appli = mysql_query($req_liste_appli, $mysql_link) or die(mysql_error());
	$tab_req_liste_appli = mysql_fetch_assoc($res_req_liste_appli);
	$total_ligne_req_liste_appli = mysql_num_rows($res_req_liste_appli);

	do
	{
		$ID_APPLI = $tab_req_liste_appli['id_appli'];
		$LIBELLE_APPLI = $tab_req_liste_appli['libelle_appli'];
		if($ID_APPLI_MODIF==$ID_APPLI){
			echo'
			<OPTION value="'.$ID_APPLI.'" selected="selected">'.$ID_APPLI.' --- '.$LIBELLE_APPLI.'</OPTION>
			';
		}
		else{
		echo'
		<OPTION value="'.$ID_APPLI.'">'.$ID_APPLI.' --- '.$LIBELLE_APPLI.'</OPTION>
		';
	}
	}while ($tab_req_liste_appli = mysql_fetch_assoc($res_req_liste_appli));
	echo'
	</SELECT>
	</td>
	</tr>
	';
	
	if($APPLI_TEST==1){
		$j++;
		if ($j%2) { $class = "pair";}else{$class = "impair";}
		echo '
		<tr class="'.$class.'">
		<td align="center" colspan="2"><font color=#993333><b>Probleme selection appli</b></font>
		</td>
		</tr>';
	}

	$j++;
	if ($j%2) { $class = "pair";}else{$class = "impair";}
	echo '
	<tr class="'.$class.'">
	<td align="left">&nbsp;Libellé de l\'intervention&nbsp;</td>
	<td align="left" colspan="1"><textarea id="txt_information" name="txt_information" cols=45 rows=2>'.stripslashes($information).'
	</textarea>
	</td>
	</tr>';
	
	if($STOP_information==1){
		$j++;
		if ($j%2) { $class = "pair";}else{$class = "impair";}
		echo '
		<tr class="'.$class.'">
		<td align="center" colspan="2"><font color=#993333><b>L\'information est vide.</b></font>
		</td>
		</tr>';
	}
	echo '
	<tr class="titre">
	<td colspan="2" align="center">
	<h2>
	<input name="btn" type="submit" id="btn" value="Ajouter">
	</h2>
	</td>
	<tr class="titre">
	<td colspan="2" align="center">
	<h2>&nbsp;[&nbsp;<a href="./index.php?ITEM=inter_va_Calendrier&m='.$MOIS_Inter_Debut.'&y='.$ANNEE_Inter_Debut.'">Retour - Calendrier</a>&nbsp;]&nbsp;</h2>
	</td>
	</tr>
	</table>
	</form>
	</div>
	';
}
if($action=="Modif"){
	echo '
	<!--D&eacute;but page HTML -->
	<div align="center">
	<form method="post" name="frm_inter" id="frm_inter" action="./index.php?ITEM='.$_GET['ITEM'].'">
	<table class="table_inc">
	<tr align="center" class="titre">
	<td colspan="2"><h2>&nbsp;[&nbsp;Modification d\'une Intervention de VA&nbsp;]&nbsp;</h2></td>
	</tr>';
		
	$j++;
	if ($j%2) { $class = "pair";}else{$class = "impair";}
	echo '
	<tr class="'.$class.'">
	<td align="left">&nbsp;Date de D&eacute;but&nbsp;</td>
	<td align="left" colspan="1">
	<input name="txt_Date_Inter_Debut" type="text" readonly value="'.$Date_Inter_Debut.'" size="10"/>';?>
	<a href="#" onClick=" window.open('./cf/calendrier/pop.php?frm=frm_inter&amp;ch=txt_Date_Inter_Debut','calendrier','width=350,height=160,scrollbars=0').focus();">
	<img src="./cf/calendrier/petit_calendrier.png" border="0"/></a><?PHP echo '
	</td>
	</tr>';
	
	$j++;
	if ($j%2) { $class = "pair";}else{$class = "impair";}
	echo '
	<tr class="'.$class.'">
	<td align="left">&nbsp;Date de Fin&nbsp;</td>
	<td align="left" colspan="1">
	<input name="txt_Date_Inter_Fin" type="text" readonly value="'.$Date_Inter_Fin.'" size="10"/>';?>
	<a href="#" onClick=" window.open('./cf/calendrier/pop.php?frm=frm_inter&amp;ch=txt_Date_Inter_Fin','calendrier','width=350,height=160,scrollbars=0').focus();">
	<img src="./cf/calendrier/petit_calendrier.png" border="0"/></a><?PHP echo '
	</td>
	</tr>';
	
	if($DATE_TEST==1){
		$j++;
		if ($j%2) { $class = "pair";}else{$class = "impair";}
		echo '
		<tr class="'.$class.'">
		<td align="center" colspan="2"><font color=#993333><b>Erreurs Sur les dates</b></font>
		</td>
		</tr>';
	}
	
	$j++;
	if ($j%2) { $class = "pair";}else{$class = "impair";}
	//require("./cf/conf_portail_ope.php");
	$req_liste_appli = "
	select `id_appli`, `libelle_appli`
	from `referentiel_appli`
	where `id_appli` != 'Tout'
	order by `id_appli` ASC ;";
	$res_req_liste_appli = mysql_query($req_liste_appli, $mysql_link) or die(mysql_error());
	$tab_req_liste_appli = mysql_fetch_assoc($res_req_liste_appli);
	$total_ligne_req_liste_appli = mysql_num_rows($res_req_liste_appli);
	echo'
	<tr class="'.$class.'">
	<td align="left">&nbsp;Application concernée&nbsp;</td>
	<td align="left" colspan="4">
	<SELECT name="application" size="1" id="appli" onChange="">
	<OPTION value="0"></OPTION>
	';
	do
	{
		$ID_APPLI = $tab_req_liste_appli['id_appli'];
		$LIBELLE_APPLI = $tab_req_liste_appli['libelle_appli'];
		if($ID_APPLI_MODIF==$ID_APPLI){
			echo'
			<OPTION value="'.$ID_APPLI.'" selected="selected">'.$ID_APPLI.' --- '.$LIBELLE_APPLI.'</OPTION>
			';
		}
		else{
		echo'
		<OPTION value="'.$ID_APPLI.'">'.$ID_APPLI.' --- '.$LIBELLE_APPLI.'</OPTION>
		';
	}
	}
	while ($tab_req_liste_appli = mysql_fetch_assoc($res_req_liste_appli));
	echo'
	</SELECT>
	</td>
	</tr>
	';
	
	if($APPLI_TEST==1){
		$j++;
		if ($j%2) { $class = "pair";}else{$class = "impair";}
		echo '
		<tr class="'.$class.'">
		<td align="center" colspan="2"><font color=#993333><b>Probleme selection appli</b></font>
		</td>
		</tr>';
	}
	
	$j++;
	if ($j%2) { $class = "pair";}else{$class = "impair";}
	echo '
	<tr class="'.$class.'">
	<td align="left">&nbsp;Libellé de l\'intervention&nbsp;</td>
	<td align="left" colspan="1"><textarea id="txt_information" name="txt_information" cols=45 rows=2>'.stripslashes($information).'
	</textarea>
	</td>
	</tr>';
	
	if($STOP_information==1){
		$j++;
		if ($j%2) { $class = "pair";}else{$class = "impair";}
		echo '
		<tr class="'.$class.'">
		<td align="center" colspan="2"><font color=#993333><b>L\'information est vide.</b></font>
		</td>
		</tr>';
	}
	
	echo '
	<tr class="titre">
	<td colspan="2" align="center">
	<h2>
	<input type="hidden" name="INTER" value="'.$ID_INTER.'">
	<input name="btn" type="submit" id="btn" value="Modifier">
	<input name="btn" type="submit" id="btn" value="Supprimer">
	</h2>
	</td>
	</tr>
	
	<tr class="titre">
	<td colspan="2" align="center">
	<h2>&nbsp;[&nbsp;<a href="./index.php?ITEM=inter_va_Gestion_Liste">Retour - Liste Interventions</a>&nbsp;]&nbsp;</h2>
	</td>
	</tr>
	
	</table>
	</form>
	</div>
	';
}
mysql_close();
?>