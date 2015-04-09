<?PHP
//redirection si acces dirrect
if(substr_count($_SERVER['PHP_SELF'], 'index.php')==0){
  header("Location: ../"); 
  exit();
}
/*******************************************************************
   Interface changement action
   Version 1.0.0 
  22/11/2010 - VGU - Creation fichier
**************************************************Guibert Vincent***
*******************************************************************/

require("./cf/conf_outil_icdc.php"); 
require_once("./cf/fonctions.php");
require_once("./cf/autre_fonctions.php");

require_once('./changement/changement_Conf_Mail.php') ; 

$ENV =substr($_SERVER["SCRIPT_FILENAME"], 1, 1);
//$ENV='x';
$j=0;

$CHANGEMENT_STATUS='';
$DATE_MODIFICATION=date("d/m/Y H:i:s");
$DATE_DU_JOUR=date("Ymd");
if(isset($_GET['action'])){
  $action=$_GET['action'];
}else{
  $action='';
}
$tab_var=$_POST;
if(isset($_GET['ID'])){
  if(is_numeric($_GET['ID'])){
    $ID=$_GET['ID'];
  }
}
if(isset($tab_var['ID'])){
  if(is_numeric($tab_var['ID'])){
    $ID=$tab_var['ID'];
    $_GET['ID']=$ID;
  }
}
if(isset($tab_var['action'])){
    $action=$tab_var['action'];
    $_GET['action']=$action;
}

if(isset($_SESSION['LOGIN'])){
	$LOGIN=$_SESSION['LOGIN'];
	$rq_Selectionner_user ="
	SELECT `UTILISATEUR_ID`,`LOGIN`, `NOM`, `PRENOM` 
	FROM `moteur_utilisateur` 
	WHERE `LOGIN` = '".$LOGIN."'";
	$res_rq_Selectionner_user = mysql_query($rq_Selectionner_user, $mysql_link) or die(mysql_error());
	$tab_rq_Selectionner_user = mysql_fetch_assoc($res_rq_Selectionner_user);
	$total_ligne_rq_Selectionner_demande_type = mysql_num_rows($res_rq_Selectionner_user);
	if($total_ligne_rq_Selectionner_demande_type==0){
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
	$rq_info="
	SELECT `moteur_role_utilisateur`.`ROLE_UTILISATEUR_ACCES` 
	FROM `moteur_role_utilisateur`,`moteur_utilisateur`,`moteur_role`
	WHERE 
	`moteur_role_utilisateur`.`UTILISATEUR_ID`=`moteur_utilisateur`.`UTILISATEUR_ID` AND
	`moteur_role_utilisateur`.`ROLE_ID`=`moteur_role`.`ROLE_ID` AND
	`moteur_utilisateur`.`LOGIN`='".$LOGIN."' AND
	(`moteur_role`.`ROLE`='ROOT' OR `moteur_role`.`ROLE`='ADMIN-CHANGEMENT') AND
	`moteur_role_utilisateur`.`ROLE_UTILISATEUR_ACCES`=0
	LIMIT 1";
	$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
	$tab_rq_info = mysql_fetch_assoc($res_rq_info);
	$total_ligne_rq_info=mysql_num_rows($res_rq_info); 
	if($total_ligne_rq_info==0){
		$ROLE=1;
	}else{
		$ROLE=$tab_rq_info['ROLE_UTILISATEUR_ACCES'];
	}
}else{
	$NOM='';
	$UTILISATEUR_ID=0;
	$PRENOM='';
	$LOGIN='';
	$ROLE=1;
}

if(empty($tab_var['btn'])){
}else{
# Cas NON
  if($tab_var['btn']=="Non"){
  	echo '
        <script language="JavaScript">
        url=("./index.php?ITEM=changement_Modif_Changement&action=Modif&ID='.$ID.'");
        window.location=url;
        </script>
        ';
        
  }
# Cas Oui
  if($tab_var['btn']=="Oui"){
  # Cas Abandon
  if($tab_var['action']=="Abandon"){
    $action='Modif';
    $CHANGEMENT_ID=$ID;
	
	$rq_info_status="
	SELECT `CHANGEMENT_STATUS_ID` 
	FROM `changement_status` 
	WHERE `CHANGEMENT_STATUS` = 'Abandonn&eacute;'
	AND `ENABLE`='0'";
	$res_rq_info_status = mysql_query($rq_info_status, $mysql_link) or die(mysql_error());
	$tab_rq_info_status = mysql_fetch_assoc($res_rq_info_status);
	$total_ligne_rq_info_status=mysql_num_rows($res_rq_info_status);
	if($total_ligne_rq_info_status==0){
		$sql="INSERT INTO `changement_status` (`CHANGEMENT_STATUS_ID` ,`CHANGEMENT_STATUS`, `ENABLE` )VALUES (NULL , 'Abandonn&eacute;', '0');";
		mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
	        $TABLE_SQL_SQL='changement_status';       
	        historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');
	        $rq_info_status="
		SELECT `CHANGEMENT_STATUS_ID` 
		FROM `changement_status` 
		WHERE `CHANGEMENT_STATUS` = 'Abandonn&eacute;'
		AND `ENABLE`='0'";
		$res_rq_info_status = mysql_query($rq_info_status, $mysql_link) or die(mysql_error());
		$tab_rq_info_status = mysql_fetch_assoc($res_rq_info_status);
		$total_ligne_rq_info_status=mysql_num_rows($res_rq_info_status);
	}
	$CHANGEMENT_STATUS_ID=$tab_rq_info_status['CHANGEMENT_STATUS_ID'];
	mysql_free_result($res_rq_info_status); 
	
	$sql="
	UPDATE `changement_liste` SET 
	`CHANGEMENT_STATUS_ID` = '".$CHANGEMENT_STATUS_ID."'
	WHERE `CHANGEMENT_LISTE_ID` ='".$ID."' LIMIT 1 ;";
	
	mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
	$TABLE_SQL_SQL='changement_liste';       
	historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');
	
	$sql="UPDATE `changement_date` SET `ENABLE` = '1' WHERE `CHANGEMENT_ID` = '".$CHANGEMENT_ID."'";
	mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
	$TABLE_SQL_SQL='changement_date';       
	historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');
	
	$TRACE_CATEGORIE='Changement';
	$TRACE_TABLE='changement_liste';
	$TRACE_REF_ID=$CHANGEMENT_ID;
	$TRACE_ACTION='Modif';
	$TRACE_ETAT=$CHANGEMENT_STATUS_ID;
	moteur_trace($TRACE_CATEGORIE,$TRACE_TABLE,$TRACE_REF_ID,$TRACE_ACTION,$TRACE_ETAT);

// mail pour l'abandon ///			

    echo '
      <script language="JavaScript">
      url=("./index.php?ITEM=changement_Send_Mail&type=abandon&ID='.$ID.'");
      window.location=url;
      </script>
      ';
  }
    
 
# Cas ReInscription
  if($tab_var['action']=="ReInscription"){
    $action='Modif';
    $CHANGEMENT_ID=$ID;
	
	$rq_info_status="
	SELECT `CHANGEMENT_STATUS_ID` 
	FROM `changement_status` 
	WHERE `CHANGEMENT_STATUS` = 'Inscrit'
	AND `ENABLE`='0'";
	$res_rq_info_status = mysql_query($rq_info_status, $mysql_link) or die(mysql_error());
	$tab_rq_info_status = mysql_fetch_assoc($res_rq_info_status);
	$total_ligne_rq_info_status=mysql_num_rows($res_rq_info_status);
	if($total_ligne_rq_info_status==0){
		$sql="INSERT INTO `changement_status` (`CHANGEMENT_STATUS_ID` ,`CHANGEMENT_STATUS`, `ENABLE` )VALUES (NULL , 'Inscrit', '0');";
		mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
	        $TABLE_SQL_SQL='changement_status';       
	        historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');
	        $rq_info_status="
		SELECT `CHANGEMENT_STATUS_ID` 
		FROM `changement_status` 
		WHERE `CHANGEMENT_STATUS` = 'Inscrit'
		AND `ENABLE`='0'";
		$res_rq_info_status = mysql_query($rq_info_status, $mysql_link) or die(mysql_error());
		$tab_rq_info_status = mysql_fetch_assoc($res_rq_info_status);
		$total_ligne_rq_info_status=mysql_num_rows($res_rq_info_status);
	}
	$CHANGEMENT_STATUS_ID=$tab_rq_info_status['CHANGEMENT_STATUS_ID'];
	mysql_free_result($res_rq_info_status); 
	$sql="
	UPDATE `changement_liste` SET 
	`CHANGEMENT_STATUS_ID` = '".$CHANGEMENT_STATUS_ID."',
	`CHANGEMENT_LISTE_UTILISATEUR_ID`= '".$UTILISATEUR_ID."',
	`CHANGEMENT_LISTE_DATE_MODIFICATION`= '".$DATE_MODIFICATION."'
	WHERE `CHANGEMENT_LISTE_ID` ='".$ID."' LIMIT 1 ;";
	
	mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
	$TABLE_SQL_SQL='changement_liste';       
	historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');
	
	
	$TRACE_CATEGORIE='Changement';
	$TRACE_TABLE='changement_liste';
	$TRACE_REF_ID=$CHANGEMENT_ID;
	$TRACE_ACTION='Modif';
	$TRACE_ETAT=$CHANGEMENT_STATUS_ID;
	moteur_trace($TRACE_CATEGORIE,$TRACE_TABLE,$TRACE_REF_ID,$TRACE_ACTION,$TRACE_ETAT);
	        	
// mail de ReInscription		

	echo '
	<script language="JavaScript">
	url=("./index.php?ITEM=changement_Send_Mail&type=ReInscription&ID='.$ID.'");
	window.location=url;
	</script>
	';


  }
  
  # Cas Brouillon
  if($tab_var['action']=="Brouillon"){
    $action='Modif';
    $CHANGEMENT_ID=$ID;
		$rq_info_status="
		SELECT `CHANGEMENT_STATUS_ID` 
		FROM `changement_status` 
		WHERE `CHANGEMENT_STATUS` = 'Brouillon'
		AND `ENABLE`='0'";
		$res_rq_info_status = mysql_query($rq_info_status, $mysql_link) or die(mysql_error());
		$tab_rq_info_status = mysql_fetch_assoc($res_rq_info_status);
		$total_ligne_rq_info_status=mysql_num_rows($res_rq_info_status);
		if($total_ligne_rq_info_status==0){
			$sql="INSERT INTO `changement_status` (`CHANGEMENT_STATUS_ID` ,`CHANGEMENT_STATUS`, `ENABLE` )VALUES (NULL , 'Brouillon', '0');";
	        	mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
		        $TABLE_SQL_SQL='changement_status';       
		        historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');
		        $rq_info_status="
			SELECT `CHANGEMENT_STATUS_ID` 
			FROM `changement_status` 
			WHERE `CHANGEMENT_STATUS` = 'Brouillon'
			AND `ENABLE`='0'";
			$res_rq_info_status = mysql_query($rq_info_status, $mysql_link) or die(mysql_error());
			$tab_rq_info_status = mysql_fetch_assoc($res_rq_info_status);
			$total_ligne_rq_info_status=mysql_num_rows($res_rq_info_status);
		}
		$CHANGEMENT_STATUS_ID=$tab_rq_info_status['CHANGEMENT_STATUS_ID'];
		mysql_free_result($res_rq_info_status); 
		$sql="
		UPDATE `changement_liste` SET 
		`CHANGEMENT_STATUS_ID` = '".$CHANGEMENT_STATUS_ID."',
		`CHANGEMENT_LISTE_UTILISATEUR_ID`= '".$UTILISATEUR_ID."',
		`CHANGEMENT_LISTE_DATE_MODIFICATION`= '".$DATE_MODIFICATION."'
		WHERE `CHANGEMENT_LISTE_ID` ='".$ID."' LIMIT 1 ;";

		mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
		$TABLE_SQL_SQL='changement_liste';       
		historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');
	
		
		$TRACE_CATEGORIE='Changement';
    $TRACE_TABLE='changement_liste';
    $TRACE_REF_ID=$CHANGEMENT_ID;
    $TRACE_ACTION='Modif';
    $TRACE_ETAT=$CHANGEMENT_STATUS_ID;
    moteur_trace($TRACE_CATEGORIE,$TRACE_TABLE,$TRACE_REF_ID,$TRACE_ACTION,$TRACE_ETAT);

// mail de Brouillon		

		echo '
      <script language="JavaScript">
      url=("./index.php?ITEM=changement_Send_Mail&type=brouillon&ID='.$ID.'");
      window.location=url;
      </script>
      ';


  }

# Cas Validation
  if($tab_var['action']=="Validation"){
    $action='Modif';
    $CHANGEMENT_ID=$ID;

		$rq_info_status="
		SELECT `CHANGEMENT_STATUS_ID` 
		FROM `changement_status` 
		WHERE `CHANGEMENT_STATUS` = 'Valid&eacute;'
		AND `ENABLE`='0'";
		$res_rq_info_status = mysql_query($rq_info_status, $mysql_link) or die(mysql_error());
		$tab_rq_info_status = mysql_fetch_assoc($res_rq_info_status);
		$total_ligne_rq_info_status=mysql_num_rows($res_rq_info_status);
		if($total_ligne_rq_info_status==0){
			$sql="INSERT INTO `changement_status` (`CHANGEMENT_STATUS_ID` ,`CHANGEMENT_STATUS`, `ENABLE` )VALUES (NULL , 'Valid&eacute;', '0');";
	        	mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
		        $TABLE_SQL_SQL='changement_status';       
		        historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');
		        $rq_info_status="
			SELECT `CHANGEMENT_STATUS_ID` 
			FROM `changement_status` 
			WHERE `CHANGEMENT_STATUS` = 'Valid&eacute;'
			AND `ENABLE`='0'";
			$res_rq_info_status = mysql_query($rq_info_status, $mysql_link) or die(mysql_error());
			$tab_rq_info_status = mysql_fetch_assoc($res_rq_info_status);
			$total_ligne_rq_info_status=mysql_num_rows($res_rq_info_status);
		}
		$CHANGEMENT_STATUS_ID=$tab_rq_info_status['CHANGEMENT_STATUS_ID'];
		mysql_free_result($res_rq_info_status); 
		$sql="
		UPDATE `changement_liste` SET 
		`CHANGEMENT_STATUS_ID` = '".$CHANGEMENT_STATUS_ID."'
		WHERE `CHANGEMENT_LISTE_ID` ='".$ID."' LIMIT 1 ;";

		mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
		$TABLE_SQL_SQL='changement_liste';       
		historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');
	
		
		$TRACE_CATEGORIE='Changement';
    $TRACE_TABLE='changement_liste';
    $TRACE_REF_ID=$CHANGEMENT_ID;
    $TRACE_ACTION='Modif';
    $TRACE_ETAT=$CHANGEMENT_STATUS_ID;
    moteur_trace($TRACE_CATEGORIE,$TRACE_TABLE,$TRACE_REF_ID,$TRACE_ACTION,$TRACE_ETAT);
	        	
// mail de validation			

		echo '
      <script language="JavaScript">
      url=("./index.php?ITEM=changement_Send_Mail&type=validation&ID='.$ID.'");
      window.location=url;
      </script>
      ';
  }
}
}
$rq_info_id="
        SELECT
        `moteur_trace`.`MOTEUR_TRACE_UTILISATEUR_ID`,
        `moteur_trace`.`MOTEUR_TRACE_DATE`,
        `moteur_trace`.`MOTEUR_TRACE_CATEGORIE`,
        `moteur_trace`.`MOTEUR_TRACE_TABLE`,
        `moteur_trace`.`MOTEUR_TRACE_REF_ID`,
        `moteur_trace`.`MOTEUR_TRACE_ACTION`,
        `changement_status`.`CHANGEMENT_STATUS`,
        `moteur_utilisateur`.`LOGIN`,
        `moteur_utilisateur`.`NOM`,
        `moteur_utilisateur`.`PRENOM`
        FROM `moteur_trace`,`moteur_utilisateur`,`changement_status`
        WHERE
        `moteur_trace`.`MOTEUR_TRACE_CATEGORIE`='Changement'
        AND `moteur_trace`.`MOTEUR_TRACE_UTILISATEUR_ID`=`moteur_utilisateur`.`UTILISATEUR_ID`
        AND `moteur_trace`.`MOTEUR_TRACE_ETAT`=`changement_status`.`CHANGEMENT_STATUS_ID`
        AND `moteur_trace`.`MOTEUR_TRACE_REF_ID`='".$ID."'
        ORDER BY `moteur_trace`.`MOTEUR_TRACE_DATE` DESC";
        $res_rq_info_id = mysql_query($rq_info_id, $mysql_link) or die(mysql_error());
        $tab_rq_info_id = mysql_fetch_assoc($res_rq_info_id);
        $total_ligne_rq_info_id=mysql_num_rows($res_rq_info_id);
        $AFF_SPAN='';
        if($total_ligne_rq_info_id!=0){
                do {

                $MOTEUR_TRACE_DATE=$tab_rq_info_id['MOTEUR_TRACE_DATE'];
                $MOTEUR_TRACE_CATEGORIE=$tab_rq_info_id['MOTEUR_TRACE_CATEGORIE'];
                $MOTEUR_TRACE_TABLE=str_replace('changement_','',$tab_rq_info_id['MOTEUR_TRACE_TABLE']);
                $MOTEUR_TRACE_REF_ID=$tab_rq_info_id['MOTEUR_TRACE_REF_ID'];
                $MOTEUR_TRACE_ACTION=$tab_rq_info_id['MOTEUR_TRACE_ACTION'];
                $MOTEUR_TRACE_CHANGEMENT_STATUS=$tab_rq_info_id['CHANGEMENT_STATUS'];
                $MOTEUR_TRACE_NOM=$tab_rq_info_id['NOM'];
                $MOTEUR_TRACE_PRENOM=$tab_rq_info_id['PRENOM'];
                $AFF_SPAN.=$MOTEUR_TRACE_DATE.' - '.$MOTEUR_TRACE_PRENOM.' '.$MOTEUR_TRACE_NOM.' - '.$MOTEUR_TRACE_ACTION.' - '.$MOTEUR_TRACE_TABLE.' - '.$MOTEUR_TRACE_CHANGEMENT_STATUS.'</BR>';
                 } while ($tab_rq_info_id = mysql_fetch_assoc($res_rq_info_id));
                $ligne= mysql_num_rows($res_rq_info_id);
                if($ligne > 0) {
                  mysql_data_seek($res_rq_info_id, 0);
                  $tab_rq_info_id = mysql_fetch_assoc($res_rq_info_id);
                }
        }
        mysql_free_result($res_rq_info_id);

echo '
<!--D&eacute;but page HTML -->  
<div align="center">
<form method="post" name="frm_inter" id="frm_inter" action="./index.php?ITEM='.$_GET['ITEM'].'">
  <table class="table_inc">
  <tr align="center" class="titre">
    <td colspan="4"><h2>&nbsp;[&nbsp;';
      switch ($action){
                case "Abandon":
                        echo 'Abandon du changement n&deg; <a href="#" class="infobulledroite">'.$ID.'<span>'.$AFF_SPAN.'</span></a>';
                break;
                case "Brouillon":
                        echo 'Remise en Brouillon du changement n&deg; <a href="#" class="infobulledroite">'.$ID.'<span>'.$AFF_SPAN.'</span></a>';
                break;
                case "ReInscription":
			echo 'ReInscription du changement n&deg; <a href="#" class="infobulledroite">'.$ID.'<span>'.$AFF_SPAN.'</span></a>';
                break;
                case "Validation":
			echo 'Validation du changement n&deg; <a href="#" class="infobulledroite">'.$ID.'<span>'.$AFF_SPAN.'</span></a>';
                break;   
        }

   echo '
   &nbsp;]&nbsp;</h2></td>		
   </tr>
   <tr class="titre">
	<td colspan="4" align="center">
	<input name="btn" type="submit" id="btn" value="Oui"> - <input name="btn" type="submit" id="btn" value="Non">
	<input type="hidden" name="ID" value="'.$ID.'">
	<input type="hidden" name="action" value="'.$action.'">
	</td>
	</tr>
	<tr class="titre">
	<td colspan="4" align="center">
		&nbsp;[&nbsp;<a href="./index.php?ITEM=changement_Modif_Changement&action=Modif&ID='.$ID.'">Retour</a>&nbsp;]&nbsp;';
		echo '
	</td>
	</tr>
</table>
</form>
</div>
';
mysql_close($mysql_link); 
?>