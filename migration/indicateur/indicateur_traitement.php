<?php
//redirection si acces dirrect
if(substr_count($_SERVER['PHP_SELF'], 'index.php')==0){
  header("Location: ../"); 
  exit(); 
}
$j=0;
$Mois_critere=date("m"); 
$Annee_critere=date("Y"); 
if($Mois_critere==1){
	$Mois_critere=12;
	$Annee_critere=$Annee_critere-1;
}else{
	$Mois_critere=$Mois_critere-1;
}

$REF='';
$DATE='';
$ACTION='';
$ACTION_NEW='';
$STATUS='';
$ACTEUR='';
$ENV='';
$NATURE='';
$DEMANDE='';
$DUREE='';
$DATE_ANNEE='';
$DATE_MOIS='';
$DATE_SEMAINE='';
$DATE_JOUR='';
$DATE_JOURSEM='';
$DATE_PREVUE='';
$EN_VERSION='';
$EN_HVP='';
$EN_HVH='';
$APPLI='';
$SOGETI='';
$POIDS='';
$NIVEAU='';
$DATE_INDICATEUR='';
$paramJourHVH = 4;
require_once("./cf/autre_fonctions.php");
// donne des information sur une date anne , mois , jour, semaine, numero du jour dans la semaine
function Return_info_date($date_en)
{
	$date_en=str_replace(',','',$date_en);
	$liste = explode(' ', $date_en); // lecture du separateur ;
	$jour_en=$liste[0];
	if(is_numeric($jour_en)){
		$jour_en=$liste[0];
		$mois_en=$liste[1];
		$annee_en=$liste[2];
	}else{
		$jour_en=$liste[1];
		$mois_en=$liste[0];
		$annee_en=$liste[2];
	}
	$mois_nb["January"] = "01";
	$mois_nb["February"] = "02";
	$mois_nb["March"] = "03";
	$mois_nb["April"] = "04";
	$mois_nb["May"] = "05";
	$mois_nb["June"] = "06";
	$mois_nb["July"] = "07";
	$mois_nb["August"] = "08";
	$mois_nb["September"] = "09";
	$mois_nb["October"] = "10";
	$mois_nb["November"] = "11";
	$mois_nb["December"] = "12";
	$mois_en_nb=$mois_nb[$mois_en];
	if($jour_en < 10){
		$jour_en='0'.$jour_en;
	}
	$semaine_nb=date("W", mktime(0, 0, 0, $mois_en_nb, $jour_en, $annee_en));
	$jour_semaine_nb=date("N", mktime(0, 0, 0, $mois_en_nb, $jour_en, $annee_en));
	return array($jour_en,$mois_en_nb,$annee_en,$semaine_nb,$jour_semaine_nb);	
}
// donne le status
function Return_status($STATUS,$mysql_link)
{
	$rq_info="
	SELECT `INDICATEUR_STATUS_INFO` 
	FROM `indicateur_status` 
	WHERE `INDICATEUR_STATUS_LIB`='".$STATUS."'
	LIMIT 1";
	$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
	$tab_rq_info = mysql_fetch_assoc($res_rq_info);
	$total_ligne_rq_info=mysql_num_rows($res_rq_info);
	mysql_free_result($res_rq_info);
	if($total_ligne_rq_info==0){
		$rq_info="
		SELECT `INDICATEUR_STATUS_INFO` 
		FROM `indicateur_status` 
		WHERE `INDICATEUR_STATUS_LIB`='ACTION INCONNUE'
		LIMIT 1";
		$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
		$tab_rq_info = mysql_fetch_assoc($res_rq_info);
		$total_ligne_rq_info=mysql_num_rows($res_rq_info);
		mysql_free_result($res_rq_info);
		$STATUS=$tab_rq_info['INDICATEUR_STATUS_INFO'];
	}else{
		$STATUS=$tab_rq_info['INDICATEUR_STATUS_INFO'];
	}
	return $STATUS;
}
// donne l environnement
function Return_env($ENVIRONNEMENT,$mysql_link)
{
	$rq_info="
	SELECT `INDICATEUR_ENVIRONNEMENT_INFO` 
	FROM `indicateur_environnement` 
	WHERE `INDICATEUR_ENVIRONNEMENT_LIB`='".$ENVIRONNEMENT."'
	LIMIT 1";
	$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
	$tab_rq_info = mysql_fetch_assoc($res_rq_info);
	$total_ligne_rq_info=mysql_num_rows($res_rq_info);
	mysql_free_result($res_rq_info);
	if($total_ligne_rq_info==0){
		$rq_info="
		SELECT `INDICATEUR_ENVIRONNEMENT_INFO` 
		FROM `indicateur_environnement` 
		WHERE `INDICATEUR_ENVIRONNEMENT_LIB`='ACTION INCONNUE'
		LIMIT 1";
		$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
		$tab_rq_info = mysql_fetch_assoc($res_rq_info);
		$total_ligne_rq_info=mysql_num_rows($res_rq_info);
		mysql_free_result($res_rq_info);
		$ENVIRONNEMENT=$tab_rq_info['INDICATEUR_ENVIRONNEMENT_INFO'];
	}else{
		$ENVIRONNEMENT=$tab_rq_info['INDICATEUR_ENVIRONNEMENT_INFO'];
	}
	return $ENVIRONNEMENT;
}
// donne la nature
function Return_nature($NATURE,$mysql_link)
{
	$rq_info="
	SELECT `INDICATEUR_NATURE_INFO` 
	FROM `indicateur_nature` 
	WHERE `INDICATEUR_NATURE_LIB`='".$NATURE."'
	LIMIT 1";
	$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
	$tab_rq_info = mysql_fetch_assoc($res_rq_info);
	$total_ligne_rq_info=mysql_num_rows($res_rq_info);
	mysql_free_result($res_rq_info);
	if($total_ligne_rq_info==0){
		$rq_info="
		SELECT `INDICATEUR_NATURE_INFO` 
		FROM `indicateur_nature` 
		WHERE `INDICATEUR_NATURE_LIB`='ACTION INCONNUE'
		LIMIT 1";
		$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
		$tab_rq_info = mysql_fetch_assoc($res_rq_info);
		$total_ligne_rq_info=mysql_num_rows($res_rq_info);
		mysql_free_result($res_rq_info);
		$NATURE=$tab_rq_info['INDICATEUR_NATURE_INFO'];
	}else{
		$NATURE=$tab_rq_info['INDICATEUR_NATURE_INFO'];
	}
	return $NATURE;
}
// donne le poids en valeur
function Return_poids_valeur($MOT_CLES,$mysql_link)
{
	if($MOT_CLES==''){
		$VALEUR=0;
		$INDICATEUR_REGLE_ID=0;
	}else{
		$VALEUR=0;
		$INDICATEUR_REGLE_ID=0;
		$rq_info="
		SELECT `INDICATEUR_REGLE_ID`, `INDICATEUR_REGLE_POIDS`, `INDICATEUR_REGLE_INFO`
		FROM `indicateur_regles` 
		WHERE `INDICATEUR_REGLE_TYPE`='ODTI' AND 
		`ENABLE`=0 
		ORDER BY `INDICATEUR_REGLE_POIDS` DESC"; 
	        $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
	        $tab_rq_info = mysql_fetch_assoc($res_rq_info);
	        $total_ligne_rq_info=mysql_num_rows($res_rq_info);
	        do {
	        	$INDICATEUR_REGLE_INFO=$tab_rq_info['INDICATEUR_REGLE_INFO'];
	        	$test=substr_count(strtoupper($MOT_CLES), strtoupper($INDICATEUR_REGLE_INFO));
	        	if($test>0){
	        		$VALEUR=$tab_rq_info['INDICATEUR_REGLE_POIDS'];
	        		$INDICATEUR_REGLE_ID=$tab_rq_info['INDICATEUR_REGLE_ID'];
	        		break;
	        	}
	        } while ($tab_rq_info = mysql_fetch_assoc($res_rq_info));
	        $ligne= mysql_num_rows($res_rq_info);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info, 0);
	          $tab_rq_info = mysql_fetch_assoc($res_rq_info);
	        }
	        mysql_free_result($res_rq_info);
	}
	return array($VALEUR,$INDICATEUR_REGLE_ID);
}
// donne l'action
function Return_action($RESUME,$MOT_CLES,$mysql_link)
{
	$ACTION='';
	$liste = explode(' ', $RESUME); // lecture du s&eacute;parateur ;
	$ACTION_test=$liste[0];
	$rq_info="
	SELECT `INDICATEUR_ACTION_ID`,`INDICATEUR_ACTION_INFO`,`INDICATEUR_ACTION_INFO_NEW`
	FROM `indicateur_action` 
	WHERE `INDICATEUR_ACTION_LIB`='".$ACTION_test."'
	AND `INDICATEUR_ACTION_TYPE`='RESUME'
	LIMIT 1";
	$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
	$tab_rq_info = mysql_fetch_assoc($res_rq_info);
	$total_ligne_rq_info=mysql_num_rows($res_rq_info);
	mysql_free_result($res_rq_info);
	if($total_ligne_rq_info!=0){
		$ACTION=$tab_rq_info['INDICATEUR_ACTION_INFO'];
		$ACTION_NEW=$tab_rq_info['INDICATEUR_ACTION_INFO_NEW'];
		$ACTION_ID=$tab_rq_info['INDICATEUR_ACTION_ID'];
	}
	if($ACTION==''){
		$rq_info="
		SELECT `INDICATEUR_ACTION_ID`,`INDICATEUR_ACTION_LIB`, `INDICATEUR_ACTION_INFO`,`INDICATEUR_ACTION_INFO_NEW`
		FROM `indicateur_action`
		WHERE `INDICATEUR_ACTION_TYPE`='MOT_CLE'
		ORDER BY `INDICATEUR_ACTION_ORDRE` ASC"; 
	        $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
	        $tab_rq_info = mysql_fetch_assoc($res_rq_info);
	        $total_ligne_rq_info=mysql_num_rows($res_rq_info);
	        do {
	        	$LIB=$tab_rq_info['INDICATEUR_ACTION_LIB'];
	        	$ACTION_TEST=$tab_rq_info['INDICATEUR_ACTION_INFO'];
	        	$ACTION_NEW=$tab_rq_info['INDICATEUR_ACTION_INFO_NEW'];
	        	$ACTION_ID=$tab_rq_info['INDICATEUR_ACTION_ID'];
	        	$test=substr_count(strtoupper($RESUME), strtoupper($LIB))+substr_count(strtoupper($MOT_CLES), strtoupper($LIB));
	        	if($test>0){
	        		$ACTION=$ACTION_TEST;
	        		break;
	        	}
	        } while ($tab_rq_info = mysql_fetch_assoc($res_rq_info));
	        $ligne= mysql_num_rows($res_rq_info);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info, 0);
	          $tab_rq_info = mysql_fetch_assoc($res_rq_info);
	        }
	        mysql_free_result($res_rq_info);
	}
        if($ACTION==''){
		$rq_info="
		SELECT `INDICATEUR_ACTION_ID`,`INDICATEUR_ACTION_INFO` ,`INDICATEUR_ACTION_INFO_NEW`
		FROM `indicateur_action` 
		WHERE `INDICATEUR_ACTION_LIB`='ACTION INCONNUE' 
		AND `INDICATEUR_ACTION_TYPE`='RESUME'
		LIMIT 1";
		$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
		$tab_rq_info = mysql_fetch_assoc($res_rq_info);
		$total_ligne_rq_info=mysql_num_rows($res_rq_info);
		$ACTION=$tab_rq_info['INDICATEUR_ACTION_INFO'];
		$ACTION_NEW=$tab_rq_info['INDICATEUR_ACTION_INFO_NEW'];
		$ACTION_ID=$tab_rq_info['INDICATEUR_ACTION_ID'];
		mysql_free_result($res_rq_info);
	}
	return array($ACTION,$ACTION_NEW,$ACTION_ID);
}
// donne si la date prevue fait partie des Versions
function Return_info_en_version($NATURE,$DATE,$mysql_link)
{
	if($NATURE=='V'){
		$rq_info="
		SELECT `DATE` 
		FROM `indicateur_version_date` 
		WHERE `DATE`='".$DATE."' AND
		`TYPE` ='V'
		LIMIT 1";
		$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
		$tab_rq_info = mysql_fetch_assoc($res_rq_info);
		$total_ligne_rq_info=mysql_num_rows($res_rq_info);
		mysql_free_result($res_rq_info);
		if($total_ligne_rq_info==0){
			$EN_VERSION='F';
		}else{
			$EN_VERSION='V';
		}
	}else{
		$EN_VERSION='F';
	}
	
	return $EN_VERSION;
}
// donne si la date prevue fait partie des LUP
function Return_info_en_LUP($DATE,$mysql_link)
{
	$rq_info="
	SELECT `DATE` 
	FROM `indicateur_version_date` 
	WHERE `DATE`='".$DATE."' AND
	`TYPE` ='LUP'
	LIMIT 1";
	$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
	$tab_rq_info = mysql_fetch_assoc($res_rq_info);
	$total_ligne_rq_info=mysql_num_rows($res_rq_info);
	mysql_free_result($res_rq_info);
	if($total_ligne_rq_info==0){
		$EN_LUP='F';
	}else{
		$EN_LUP='V';
	}

	return $EN_LUP;
}
// donne si la date prevue fait partie des Hors-Versions planifies
function Return_info_en_hvp($DATE,$mysql_link)
{
	$rq_info="
	SELECT `DATE` 
	FROM `indicateur_version_date` 
	WHERE `DATE`='".$DATE."' AND
	`TYPE` ='HVP'
	LIMIT 1";
	$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
	$tab_rq_info = mysql_fetch_assoc($res_rq_info);
	$total_ligne_rq_info=mysql_num_rows($res_rq_info);
	mysql_free_result($res_rq_info);
	if($total_ligne_rq_info==0){
		$EN_HVP='F';
	}else{
		$EN_HVP='V';
	}

	return $EN_HVP;
}
// donne le poids et le niveau
function Return_NIVEAU_POIDS($DUREE,$mysql_link)
{
	if($DUREE==0){
		$NIVEAU='VIDE';
		$POIDS=0;
	}else{
		$rq_complex_info="SELECT * FROM `indicateur_duree` WHERE '".$DUREE."' >= `INDICATEUR_DUREE_TEMPS_MINI` AND '".$DUREE."' < `INDICATEUR_DUREE_TEMPS_MAX` AND `ENABLE` =0 LIMIT 1 ";
		$res_rq_complex_info = mysql_query($rq_complex_info, $mysql_link) or die(mysql_error());
		$tab_rq_complex_info = mysql_fetch_assoc($res_rq_complex_info);
		$total_ligne_rq_complex_info=mysql_num_rows($res_rq_complex_info);
		if($total_ligne_rq_complex_info==0){
			$NIVEAU='VIDE';
			$POIDS=0;
		}else{
			$NIVEAU=$tab_rq_complex_info['INDICATEUR_DUREE_LIB'];
			$POIDS=$tab_rq_complex_info['INDICATEUR_DUREE_POIDS'];
		}
		mysql_free_result($res_rq_complex_info);
	}
	return array($NIVEAU,$POIDS);
}
// donne l'acteur et si sogeti
function Return_acteur($ASSIGNE,$mysql_link)
{
	if($ASSIGNE==''){
		$total_ligne_rq_info=0;
	}else{
		$liste = explode(' ', $ASSIGNE); // lecture du s&eacute;parateur ;
		$PRENOM=$liste[0];
		$NOM=$liste[1];
		$rq_info="
		SELECT LEFT(UPPER(`LOGIN`),3) AS `LOGIN` ,UPPER(`SOCIETE`) AS `SOCIETE`
		FROM `moteur_utilisateur` 
		WHERE UPPER(`NOM`)=UPPER('".$NOM."') AND UPPER(`PRENOM`)=UPPER('".$PRENOM."')
		LIMIT 1";
		$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
		$tab_rq_info = mysql_fetch_assoc($res_rq_info);
		$total_ligne_rq_info=mysql_num_rows($res_rq_info);
		mysql_free_result($res_rq_info);
	}
	if($total_ligne_rq_info==0){
		$ASSIGNE="VIDE";
		$SOGETI="N";
	}else{
		$ASSIGNE=$tab_rq_info['LOGIN'];
		if($tab_rq_info['SOCIETE']=='SOGETI'){
			$SOGETI="Y";
		}else{
			$SOGETI="N";
		}
		
	}
	return array($ASSIGNE,$SOGETI);
}

if (empty($_POST)){
}else{
	if(!empty($_POST['execution'])){
		$ANNEE=$_POST['Annee'];
		$MOIS=$_POST['Mois'];
		$Mois_critere=$MOIS; 
		$Annee_critere=$ANNEE; 
		$AMMEE_MIN=$ANNEE;
		$AMMEE_MAX=$ANNEE;
		$MOIS_MIN=$MOIS-1;
		$MOIS_MAX=$MOIS+1;
		if($MOIS==1){
			$MOIS_MIN=12;
			$AMMEE_MIN=$ANNEE-1;
		}
		if($MOIS==12){
			$MOIS_MAX=1;
			$AMMEE_MAX=$ANNEE+1;
		}
		if($MOIS < 10){
			$MOIS='0'.$MOIS;		
		}
		if($MOIS_MIN < 10){
			$MOIS_MIN='0'.$MOIS_MIN;		
		}
		if($MOIS_MAX < 10){
			$MOIS_MAX='0'.$MOIS_MAX;		
		}
		$DATE_INDICATEUR=$ANNEE.$MOIS;
		$DATE_MIN=$AMMEE_MIN.$MOIS_MIN;
		$DATE_MAX=$AMMEE_MAX.$MOIS_MAX;
		
// on supprimme les donn&eacute;es si l'on a deja fait un calcul		
		$sql="DELETE FROM `indicateur_extract_archive` WHERE `DATE_INDICATEUR` ='".$DATE_INDICATEUR."'";
		mysql_query($sql, $mysql_link) or die('Erreur SQL !'.$sql.''.mysql_error());
		$OptTable=mysql_query("OPTIMIZE TABLE `indicateur_extract_archive`",$mysql_link);
		
		$sql="DELETE FROM `indicateur_calcul` WHERE `DATE_INDICATEUR` ='".$DATE_INDICATEUR."'";
		mysql_query($sql, $mysql_link) or die('Erreur SQL !'.$sql.''.mysql_error());
		$OptTable=mysql_query("OPTIMIZE TABLE `indicateur_calcul`",$mysql_link);
// on traite l import
		
	  $rq_info_extract="SELECT * FROM `indicateur_extract_brut`"; 
          $res_rq_info_extract = mysql_query($rq_info_extract, $mysql_link) or die(mysql_error());
          $tab_rq_info_extract = mysql_fetch_assoc($res_rq_info_extract);
          $total_ligne_rq_info_extract=mysql_num_rows($res_rq_info_extract); 
          if ($total_ligne_rq_info_extract==0){
            $erreur="Pas d\'information dans la base.";
          }else{
          
          do {
		$REF=addslashes($tab_rq_info_extract['REF']);
		$RESUME=addslashes($tab_rq_info_extract['RESUME']);
		$STATUS=addslashes($tab_rq_info_extract['STATUS']);
		$DATE_CREATION=addslashes($tab_rq_info_extract['DATE_CREATION']);
		$ASSIGNE=addslashes($tab_rq_info_extract['ASSIGNE']);
		$DATE_FIN_RELLE=addslashes($tab_rq_info_extract['DATE_FIN_RELLE']);
		$DATE_PREVUE=addslashes($tab_rq_info_extract['DATE_PREVUE']);
		$ENVIRONNEMENT=addslashes($tab_rq_info_extract['ENVIRONNEMENT']);
		$LAST_UPDATE=addslashes($tab_rq_info_extract['LAST_UPDATE']);
		$MOT_CLES=addslashes($tab_rq_info_extract['MOT_CLES']);
		$NATURE=addslashes($tab_rq_info_extract['NATURE']);
		$NO_DEMANDE=addslashes($tab_rq_info_extract['NO_DEMANDE']);
		$DEMANDE=$NO_DEMANDE;
		$TEMPS=addslashes($tab_rq_info_extract['TEMPS']);
		$DUREE=$TEMPS;
		$DATE_CREATION_DDE=addslashes($tab_rq_info_extract['DATE_CREATION_DDE']);
		$DATE_DEBUT_RELLE=addslashes($tab_rq_info_extract['DATE_DEBUT_RELLE']);
		$DATE_MAJ_DDE=addslashes($tab_rq_info_extract['DATE_MAJ_DDE']);
		$DATE_SOUHAITEE=addslashes($tab_rq_info_extract['DATE_SOUHAITEE']);
		$DEMANDEUR=addslashes($tab_rq_info_extract['DEMANDEUR']);
		$DOCUMENT_ATTACHES=addslashes($tab_rq_info_extract['DOCUMENT_ATTACHES']);
		$DOMAINE_INTERVENTION=addslashes($tab_rq_info_extract['DOMAINE_INTERVENTION']);
		$ENTITE_MOA=addslashes($tab_rq_info_extract['ENTITE_MOA']);
		$ENTITE_MOE=addslashes($tab_rq_info_extract['ENTITE_MOE']);
		$GROUPE_DE_TRAVAIL=addslashes($tab_rq_info_extract['GROUPE_DE_TRAVAIL']);
		$IMPLANTATION_GEO=addslashes($tab_rq_info_extract['IMPLANTATION_GEO']);
		$IMPLANTATION_DEMANDE=addslashes($tab_rq_info_extract['IMPLANTATION_DEMANDE']);
		$MANAGER_GROUP_TRAVAIL=addslashes($tab_rq_info_extract['MANAGER_GROUP_TRAVAIL']);
		$MESSAGE=addslashes($tab_rq_info_extract['MESSAGE']);
		$NOM_APPLICATION=addslashes($tab_rq_info_extract['NOM_APPLICATION']);
		$ORDRE_INTERVENTION=addslashes($tab_rq_info_extract['ORDRE_INTERVENTION']);
		$REF_DEROGATION=addslashes($tab_rq_info_extract['REF_DEROGATION']);
		$SERVICE=addslashes($tab_rq_info_extract['SERVICE']);
		$TELEPHONE=addslashes($tab_rq_info_extract['TELEPHONE']);
		$TEMPS_J_ASSISTANT=addslashes($tab_rq_info_extract['TEMPS_J_ASSISTANT']);
		$TEMPS_J=addslashes($tab_rq_info_extract['TEMPS_J']);
		$COMMENTAIRE_DESCRIPTION=addslashes($tab_rq_info_extract['COMMENTAIRE_DESCRIPTION']);
		
		// cas particulier avant nomenclature
			   if($REF=='203670'){
			   	$DATE_FIN_RELLE='January 27, 2011 03:06 PM';
			   	$RESUME='LIV.COMPOSANT.LUP FHA';
			   	$ENVIRONNEMENT ='Exploitation (Bordeaux)';
			   }
			   if($REF=='203463'){
			   	$RESUME='LIV.COMPOSANT.LUP - X - VP6 - DDI 2010-449 Fichier des cotisations CGM';
			   }
		// fin cas particulier avant nomenclature
		
		########## Bloc De sauvegarde dans la base ###########
		list($DATE_J, $DATE_M,$DATE_A,$DATE_S,$DATE_JS) = Return_info_date($DATE_CREATION);
		$DATE_TEST=$DATE_A.$DATE_M;
            	if($DATE_TEST >= $DATE_MIN){
            		if($DATE_TEST <= $DATE_MAX){
		            	$sql="INSERT INTO `indicateur_extract_archive` (`ID`, `REF` ,`RESUME` ,`STATUS` ,`DATE_CREATION` ,`ASSIGNE` ,`DATE_FIN_RELLE` ,`DATE_PREVUE` ,`ENVIRONNEMENT` ,`LAST_UPDATE` ,`MOT_CLES` ,`NATURE` ,`NO_DEMANDE` ,`TEMPS` ,`DATE_INDICATEUR`,`COMMENTAIRE_DESCRIPTION`,`DATE_CREATION_DDE`,`DATE_DEBUT_RELLE`,`DATE_MAJ_DDE`,`DATE_SOUHAITEE`,`DEMANDEUR`,`DOCUMENT_ATTACHES`,`DOMAINE_INTERVENTION`,`ENTITE_MOA`,`ENTITE_MOE`,`GROUPE_DE_TRAVAIL`,`IMPLANTATION_GEO`,`IMPLANTATION_DEMANDE`,`MANAGER_GROUP_TRAVAIL`,`MESSAGE`,`NOM_APPLICATION`,`ORDRE_INTERVENTION`,`REF_DEROGATION`,`SERVICE`,`TELEPHONE`,`TEMPS_J_ASSISTANT`,`TEMPS_J` )
				VALUES ( NULL, '".$REF."', '".$RESUME."', '".$STATUS."', '".$DATE_CREATION."', '".$ASSIGNE."', '".$DATE_FIN_RELLE."', '".$DATE_PREVUE."', '".$ENVIRONNEMENT."', '".$LAST_UPDATE."', '".$MOT_CLES."', '".$NATURE."', '".$NO_DEMANDE."', '".$TEMPS."', '".$DATE_INDICATEUR."', '".$COMMENTAIRE_DESCRIPTION."', '".$DATE_CREATION_DDE."', '".$DATE_DEBUT_RELLE."', '".$DATE_MAJ_DDE."', '".$DATE_SOUHAITEE."', '".$DEMANDEUR."', '".$DOCUMENT_ATTACHES."', '".$DOMAINE_INTERVENTION."', '".$ENTITE_MOA."', '".$ENTITE_MOE."', '".$GROUPE_DE_TRAVAIL."', '".$IMPLANTATION_GEO."', '".$IMPLANTATION_DEMANDE."', '".$MANAGER_GROUP_TRAVAIL."', '".$MESSAGE."', '".$NOM_APPLICATION."', '".$ORDRE_INTERVENTION."', '".$REF_DEROGATION."', '".$SERVICE."', '".$TELEPHONE."', '".$TEMPS_J_ASSISTANT."', '".$TEMPS_J."');";
				mysql_query($sql, $mysql_link) or die('Erreur SQL !'.$sql.''.mysql_error());	

			    ########## Bloc De traitement des informations ###########
			   // echo 'REF : '.$REF.'</BR>';
		// on traite les dates
			   if($DATE_FIN_RELLE==''){$DATE_FIN_RELLE=$LAST_UPDATE;}
		
			   list($DATE_JOUR, $DATE_MOIS,$DATE_ANNEE,$DATE_SEMAINE,$DATE_JOURSEM) = Return_info_date($DATE_FIN_RELLE);
			   $DATE=$DATE_ANNEE.$DATE_MOIS.$DATE_JOUR;
			   $DATE_INFO=$DATE_ANNEE.$DATE_MOIS;
			   if($DATE_PREVUE!=''){
				   list($jour_DATE_PREVUE_nb, $mois_DATE_PREVUE_nb,$annee_DATE_PREVUE_nb,$semaine_DATE_PREVUE_nb,$jour_semaine_DATE_PREVUE_nb) = Return_info_date($DATE_PREVUE);
				   $DATE_PREVUE=$annee_DATE_PREVUE_nb.$mois_DATE_PREVUE_nb.$jour_DATE_PREVUE_nb;
			   }
		// on traite le status
			   $STATUS=Return_status($STATUS,$mysql_link);
		// on traite l acteur
			   list($ACTEUR,$SOGETI)=Return_acteur($ASSIGNE,$mysql_link);
		// on traite l environnement
			   $ENV=Return_env($ENVIRONNEMENT,$mysql_link);
		// on traite la nature
			   $NATURE=Return_nature($NATURE,$mysql_link);
		// on traite l action
			   list($ACTION,$ACTION_NEW,$ACTION_ID)=Return_action($RESUME,$MOT_CLES,$mysql_link);
			   $EN_LUP=Return_info_en_LUP($DATE,$mysql_link);
			   if($ACTION_NEW=='L'){
			   	if($EN_LUP=='V'){
			   		$ACTION_NEW='L_LUP';	
			   	} 
			   }
		// cas particulier avant nomenclature
			   if($REF=='203361'){
			   	$ACTION_NEW='L';
			   }
		// fin cas particulier avant nomenclature
		
		// on traite l applis  
		            $liste = explode(' ', $MOT_CLES); // lecture du s&eacute;parateur ;
		            //$APPLI=strtoupper(substr($MOT_CLES, 0, 3)); 
		            $APPLI=strtoupper($liste[0]); 
		// on traite le EN_HVH
			    $EN_HVH='F';
			    if($paramJourHVH==$DATE_JOURSEM){
			    	$EN_HVH='V';
			    }
		// on traite le EN_HVP
			    $EN_HVP='F';
		       	    $EN_HVP=Return_info_en_hvp($DATE,$mysql_link);
		       	    if($EN_HVP=='V'){
				$EN_HVH='F';
			    }
		
		// on traite le EN_VERSION
			    $EN_VERSION='F';
		       	    $EN_VERSION=Return_info_en_version($NATURE,$DATE,$mysql_link);
		       	    if($EN_VERSION=='V'){
		       	    	$EN_HVP='F';
				$EN_HVH='F';
			    }
		// on traite le poids et le niveau
			list($NIVEAU,$POIDS)=Return_NIVEAU_POIDS($DUREE,$mysql_link);
		// on traire la valeur
			list($VALEUR,$INDICATEUR_REGLE_ID)=Return_poids_valeur($MOT_CLES,$mysql_link);

			  // echo '</BR>';
			   
			   $sql="INSERT INTO `indicateur_calcul` (`ID` ,`REF` , `DATE` ,`ACTION` ,`STATUS` ,`ACTEUR` ,`ENV` ,`NATURE` ,`DEMANDE` ,`DUREE` ,`DATE_ANNEE` ,`DATE_MOIS` ,`DATE_SEMAINE` ,`DATE_JOUR` ,`DATE_JOURSEM` ,`DATE_PREVUE` ,`EN_VERSION` ,`EN_HVP` ,`EN_HVH` ,`APPLI` ,`SOGETI`,`POIDS`,`NIVEAU` ,`VALEUR` ,`INDICATEUR_REGLE_ID` ,`DATE_INDICATEUR`,`ACTION_NEW`,`INDICATEUR_ACTION_ID` )
			   VALUES ( NULL, '".$REF."', '".$DATE."', '".$ACTION."', '".$STATUS."', '".$ACTEUR."', '".$ENV."', '".$NATURE."', '".$DEMANDE."', '".$DUREE."', '".$DATE_ANNEE."', '".$DATE_MOIS."', '".$DATE_SEMAINE."', '".$DATE_JOUR."', '".$DATE_JOURSEM."', '".$DATE_PREVUE."', '".$EN_VERSION."', '".$EN_HVP."', '".$EN_HVH."', '".$APPLI."', '".$SOGETI."', '".$POIDS."', '".$NIVEAU."','".$VALEUR."','".$INDICATEUR_REGLE_ID."', '".$DATE_INDICATEUR."', '".$ACTION_NEW."', '".$ACTION_ID."');";
			   mysql_query($sql, $mysql_link) or die('Erreur SQL !'.$sql.''.mysql_error());
	   
	   		}	
        	}
          } while ($tab_rq_info_extract = mysql_fetch_assoc($res_rq_info_extract));
          $ligne= mysql_num_rows($res_rq_info_extract);
          if($ligne > 0) {
            mysql_data_seek($res_rq_info_extract, 0);
            $tab_rq_info_extract = mysql_fetch_assoc($res_rq_info_extract);
          }
        }
        mysql_free_result($res_rq_info_extract);
        $OptTable=mysql_query("OPTIMIZE TABLE `indicateur_calcul`",$mysql_link);
        
        echo '
        <script language="JavaScript">
        url=("./index.php?ITEM=indicateur_menu");
        window.location=url;
        </script>
        ';
        
	}
}
echo '
<center>
<form method="POST" action="./index.php?ITEM=indicateur_traitement">
<table class="table_inc" cellspacing="0" cellpading="0">
  <tr align="center" class="titre">
    <td align="center" colspan="2">&nbsp;</td>
  </tr>
  <tr align="center" class="titre">
    <td align="center" colspan="2">&nbsp;traitement des indicateurs ODTI&nbsp;</td>
  </tr>
  <tr align="center" class="titre">
    <td align="center" colspan="2">&nbsp;</td>
  </tr>
  <tr align="center" class="impair">
    <td align="center">
       <u>Ann&eacute;e</u> : 
       <select name="Annee" id="Annee" value="">';
       $Annee_temp=$Annee_critere-4;
       for ($k=0;$k<=4; $k++)
       {
	       echo '<option '; if($Annee_critere == $Annee_temp){echo "selected ";} echo 'value="'.$Annee_temp.'">'.$Annee_temp.'</option>';
	       $Annee_temp=$Annee_temp+1;	
       }
         echo '
       </select>
    </td>
    <td align="center"><u>Mois</u> : 
       <select name="Mois" id="Mois" value="">
        ';
           // gestion de l'affichage par defaut dans la liste deroulante du mois en cours	
           
           for ($k=0; $k<sizeof($Tab_des_Mois); $k++)
           {
             $m=$k+1;
               echo '<option '; if($Mois_critere == $m){echo "selected ";} echo 'value="'.$m.'">'.$Tab_des_Mois[$k].'</option>'."\n";
           }
         echo '
       </select>
     </td>
  </tr>
  <tr align="center" class="titre">
    <td align="center" colspan="2">&nbsp;</td>
  </tr>
  <tr align="center" class="titre">
    <td align="center" colspan="2">&nbsp;<input type="submit" name="execution" value="Execution du traitement">&nbsp;</td>
  </tr>
  <tr align="center" class="titre">
    <td align="center" colspan="2">&nbsp;</td>
  </tr>
</table>     
</form>
</center>
';
mysql_close($mysql_link);
?>
