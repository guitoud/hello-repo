<?PHP
//redirection si acces dirrect
if(substr_count($_SERVER['PHP_SELF'], 'index.php')==0){
  header("Location: ../"); 
  exit(); 
}
/*******************************************************************
   Interface FAR action
   Version 1.0.0 
  05/10/2010 - VGU - Creation fichier
**************************************************Guibert Vincent***
*******************************************************************/

require("./cf/conf_outil_icdc.php"); 
require_once("./cf/fonctions.php");
require_once("./cf/autre_fonctions.php");
 
require_once('./changement/changement_Conf_Mail.php') ; 

$ENV =substr($_SERVER["SCRIPT_FILENAME"], 1, 1);

$j=0;

$STOP=0;
$STOP_NB_INC=0;
$STOP_COM=0;
$DATE_MODIFICATION=date("d/m/Y H:i:s");
$DATE_DU_JOUR=date("Ymd");
$Commentaire='';
$Incident='';
$Incident_num='';
if(isset($_GET['action'])){
  $action=$_GET['action'];
}else{
  $action='Ajout';
}

$tab_var=$_POST;
if(isset($_GET['ID'])){
  if(is_numeric($_GET['ID'])){
    $ID=$_GET['ID'];
  }
}


//echo 'ID = '.$ID.'<br>';
if(isset($tab_var['ID'])){
  if(is_numeric($tab_var['ID'])){
    $ID=$tab_var['ID'];
  }
}
if($action=='Info'){
	$readonly_var='readonly="readonly"';
	$disabled_var='DISABLED';
}else{
	$readonly_var='';
	$disabled_var='';
}

$rq_info_id="
SELECT `CHANGEMENT_LISTE_DATE_DEBUT`, `CHANGEMENT_LISTE_HEURE_DEBUT` 
FROM `changement_liste` 
WHERE `CHANGEMENT_LISTE_ID`='".$ID."'";
$res_rq_info_id = mysql_query($rq_info_id, $mysql_link) or die(mysql_error());
$tab_rq_info_id = mysql_fetch_assoc($res_rq_info_id);
$total_ligne_rq_info_id=mysql_num_rows($res_rq_info_id);
if($total_ligne_rq_info_id==0){
  $DATE_DEBUT='';
  $HEURE_DEBUT='';
  $HEURE_DEBUT_heure='';
  $HEURE_DEBUT_minutes='';
  $HEURE_DEBUT_seconde='';
  $DATE_DEBUT_jour='';
  $DATE_DEBUT_mois='';
  $DATE_DEBUT_annee='';
}else{
  $DATE_DEBUT=$tab_rq_info_id['CHANGEMENT_LISTE_DATE_DEBUT'];
  $HEURE_DEBUT=str_replace('h',':',$tab_rq_info_id['CHANGEMENT_LISTE_HEURE_DEBUT']);
  $HEURE_DEBUT_heure=substr($HEURE_DEBUT,0,2);
  $HEURE_DEBUT_minutes=substr($HEURE_DEBUT,3,2);
  $HEURE_DEBUT_seconde='00';
  $HEURE_DEBUT.=':00';
  $DATE_DEBUT_jour=substr($DATE_DEBUT,6,2);
  $DATE_DEBUT_mois=substr($DATE_DEBUT,4,2);
  $DATE_DEBUT_annee=substr($DATE_DEBUT,0,4); 
  $DATE_DEBUT=$DATE_DEBUT_jour.'/'.$DATE_DEBUT_mois.'/'.$DATE_DEBUT_annee;
  $DATE_DEBUT.=' '.$HEURE_DEBUT;
}
mysql_free_result($res_rq_info_id);
$rq_info_id="
SELECT `CHANGEMENT_LISTE_DATE_FIN`, `CHANGEMENT_LISTE_HEURE_FIN` 
FROM `changement_liste` 
WHERE `CHANGEMENT_LISTE_ID`='".$ID."'";
$res_rq_info_id = mysql_query($rq_info_id, $mysql_link) or die(mysql_error());
$tab_rq_info_id = mysql_fetch_assoc($res_rq_info_id);
$total_ligne_rq_info_id=mysql_num_rows($res_rq_info_id);
if($total_ligne_rq_info_id==0){
  $DATE_FIN='';
  $HEURE_FIN='';
  $HEURE_FIN_heure='';
  $HEURE_FIN_minutes='';
  $HEURE_FIN_seconde='';
  $DATE_FIN_jour='';
  $DATE_FIN_mois='';
  $DATE_FIN_annee='';
}else{
  $DATE_FIN=$tab_rq_info_id['CHANGEMENT_LISTE_DATE_FIN'];
  $HEURE_FIN=str_replace('h',':',$tab_rq_info_id['CHANGEMENT_LISTE_HEURE_FIN']);
  $HEURE_FIN_heure=substr($HEURE_FIN,0,2);
  $HEURE_FIN_minutes=substr($HEURE_FIN,3,2);
  $HEURE_FIN_seconde='00';
  $HEURE_FIN.=':00';
  $DATE_FIN_jour=substr($DATE_FIN,6,2);
  $DATE_FIN_mois=substr($DATE_FIN,4,2);
  $DATE_FIN_annee=substr($DATE_FIN,0,4); 
  $DATE_FIN=$DATE_FIN_jour.'/'.$DATE_FIN_mois.'/'.$DATE_FIN_annee;
  $DATE_FIN.=' '.$HEURE_FIN;
}
mysql_free_result($res_rq_info_id);
$rq_info_id="
SELECT 
`moteur_trace`.`MOTEUR_TRACE_DATE`
FROM `moteur_trace`,`changement_status`
WHERE 
`moteur_trace`.`MOTEUR_TRACE_CATEGORIE`='Changement'
AND `moteur_trace`.`MOTEUR_TRACE_ETAT`=`changement_status`.`CHANGEMENT_STATUS_ID` 
AND `moteur_trace`.`MOTEUR_TRACE_REF_ID`='".$ID."'
AND `changement_status`.`CHANGEMENT_STATUS`='Inscrit'
ORDER BY `moteur_trace`.`MOTEUR_TRACE_DATE_TRI` DESC
LIMIT 1
";
$res_rq_info_id = mysql_query($rq_info_id, $mysql_link) or die(mysql_error());
$tab_rq_info_id = mysql_fetch_assoc($res_rq_info_id);
$total_ligne_rq_info_id=mysql_num_rows($res_rq_info_id);
if($total_ligne_rq_info_id==0){
  $DATE_INSCRIPTION='';
  $HEURE_INSCRIPTION_heure='';
  $HEURE_INSCRIPTION_minutes='';
  $HEURE_INSCRIPTION_seconde='';
  $DATE_INSCRIPTION_jour='';
  $DATE_INSCRIPTION_mois='';
  $DATE_INSCRIPTION_annee='';
}else{
  $DATE_INSCRIPTION=$tab_rq_info_id['MOTEUR_TRACE_DATE'];
  $HEURE_INSCRIPTION_heure=substr($DATE_INSCRIPTION,11,2);
  $HEURE_INSCRIPTION_minutes=substr($DATE_INSCRIPTION,14,2);
  $HEURE_INSCRIPTION_seconde=substr($DATE_INSCRIPTION,17,2);
  $DATE_INSCRIPTION_jour=substr($DATE_INSCRIPTION,0,2);
  $DATE_INSCRIPTION_mois=substr($DATE_INSCRIPTION,3,2);
  $DATE_INSCRIPTION_annee=substr($DATE_INSCRIPTION,6,4);

}
mysql_free_result($res_rq_info_id);

$rq_info_id="
SELECT 
`moteur_trace`.`MOTEUR_TRACE_DATE`
FROM `moteur_trace`,`changement_status`
WHERE 
`moteur_trace`.`MOTEUR_TRACE_CATEGORIE`='Changement'
AND `moteur_trace`.`MOTEUR_TRACE_ETAT`=`changement_status`.`CHANGEMENT_STATUS_ID` 
AND `moteur_trace`.`MOTEUR_TRACE_REF_ID`='".$ID."'
AND `changement_status`.`CHANGEMENT_STATUS`='Termin&eacute;'
ORDER BY `moteur_trace`.`MOTEUR_TRACE_DATE_TRI` DESC
LIMIT 1
";
$res_rq_info_id = mysql_query($rq_info_id, $mysql_link) or die(mysql_error());
$tab_rq_info_id = mysql_fetch_assoc($res_rq_info_id);
$total_ligne_rq_info_id=mysql_num_rows($res_rq_info_id);
if($total_ligne_rq_info_id==0){
  $DATE_TERMNINE='';
  $HEURE_VALIDATION_heure='';
  $HEURE_VALIDATION_minutes='';
  $HEURE_VALIDATION_seconde='';
  $DATE_TERMNINE_jour='';
  $DATE_TERMNINE_mois='';
  $DATE_TERMNINE_annee='';
}else{
  $DATE_TERMNINE=$tab_rq_info_id['MOTEUR_TRACE_DATE'];
  $HEURE_VALIDATION_heure=substr($DATE_TERMNINE,11,2);
  $HEURE_VALIDATION_minutes=substr($DATE_TERMNINE,14,2);
  $HEURE_VALIDATION_seconde=substr($DATE_TERMNINE,17,2);
  $DATE_TERMNINE_jour=substr($DATE_TERMNINE,0,2);
  $DATE_TERMNINE_mois=substr($DATE_TERMNINE,3,2);
  $DATE_TERMNINE_annee=substr($DATE_TERMNINE,6,4);
}
mysql_free_result($res_rq_info_id);
$rq_info_id="
SELECT 
`moteur_trace`.`MOTEUR_TRACE_DATE`
FROM `moteur_trace`,`changement_status`
WHERE 
`moteur_trace`.`MOTEUR_TRACE_CATEGORIE`='Changement'
AND `moteur_trace`.`MOTEUR_TRACE_ETAT`=`changement_status`.`CHANGEMENT_STATUS_ID` 
AND `moteur_trace`.`MOTEUR_TRACE_REF_ID`='".$ID."'
AND `changement_status`.`CHANGEMENT_STATUS`='Valid&eacute;'
ORDER BY `moteur_trace`.`MOTEUR_TRACE_DATE_TRI` DESC
LIMIT 1
";
$res_rq_info_id = mysql_query($rq_info_id, $mysql_link) or die(mysql_error());
$tab_rq_info_id = mysql_fetch_assoc($res_rq_info_id);
$total_ligne_rq_info_id=mysql_num_rows($res_rq_info_id);
if($total_ligne_rq_info_id==0){
  $DATE_VALIDATION='';
  $HEURE_VALIDATION_heure='';
  $HEURE_VALIDATION_minutes='';
  $HEURE_VALIDATION_seconde='';
  $DATE_VALIDATION_jour='';
  $DATE_VALIDATION_mois='';
  $DATE_VALIDATION_annee='';
}else{
  $DATE_VALIDATION=$tab_rq_info_id['MOTEUR_TRACE_DATE'];
  $HEURE_VALIDATION_heure=substr($DATE_VALIDATION,11,2);
  $HEURE_VALIDATION_minutes=substr($DATE_VALIDATION,14,2);
  $HEURE_VALIDATION_seconde=substr($DATE_VALIDATION,17,2);
  $DATE_VALIDATION_jour=substr($DATE_VALIDATION,0,2);
  $DATE_VALIDATION_mois=substr($DATE_VALIDATION,3,2);
  $DATE_VALIDATION_annee=substr($DATE_VALIDATION,6,4);
}
mysql_free_result($res_rq_info_id);


$timestamp_DATE_DEBUT=mktime($HEURE_DEBUT_heure, $HEURE_DEBUT_minutes, $HEURE_DEBUT_seconde, $DATE_DEBUT_mois, $DATE_DEBUT_jour, $DATE_DEBUT_annee);
$timestamp_DATE_FIN=mktime($HEURE_FIN_heure, $HEURE_FIN_minutes, $HEURE_FIN_seconde, $DATE_FIN_mois, $DATE_FIN_jour, $DATE_FIN_annee);

$timestamp_DATE_INSCRIPTION=mktime($HEURE_INSCRIPTION_heure, $HEURE_INSCRIPTION_minutes, $HEURE_INSCRIPTION_seconde, $DATE_INSCRIPTION_mois, $DATE_INSCRIPTION_jour, $DATE_INSCRIPTION_annee);

if($DATE_VALIDATION_annee==''){
  $timestamp_DATE_VALIDATION=0;
}else{
  $timestamp_DATE_VALIDATION=mktime($HEURE_VALIDATION_heure, $HEURE_VALIDATION_minutes, $HEURE_VALIDATION_seconde, $DATE_VALIDATION_mois, $DATE_VALIDATION_jour, $DATE_VALIDATION_annee);
}


if($DATE_TERMNINE_annee==''){
  $timestamp_DATE_TERMNINE=0;
}else{
  $timestamp_DATE_TERMNINE=mktime($HEURE_VALIDATION_heure, $HEURE_VALIDATION_minutes, $HEURE_VALIDATION_seconde, $DATE_TERMNINE_mois, $DATE_TERMNINE_jour, $DATE_TERMNINE_annee);
}

if(isset($tab_var['action'])){
  $action=$tab_var['action'];
}
if(isset($_SESSION['LOGIN'])){
	$LOGIN=$_SESSION['LOGIN'];
	$rq_Selectionner_user ="
	SELECT `UTILISATEUR_ID`,`LOGIN`, `NOM`, `PRENOM` 
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
	$rq_info="
	SELECT `moteur_role_utilisateur`.`ROLE_UTILISATEUR_ACCES` 
	FROM `moteur_role_utilisateur`,`moteur_utilisateur`,`moteur_role`
	WHERE 
	`moteur_role_utilisateur`.`UTILISATEUR_ID`=`moteur_utilisateur`.`UTILISATEUR_ID` AND
	`moteur_role_utilisateur`.`ROLE_ID`=`moteur_role`.`ROLE_ID` AND
	`moteur_utilisateur`.`LOGIN`='".$LOGIN."' AND
	(`moteur_role`.`ROLE`='ROOT' OR `moteur_role`.`ROLE`='ADMIN-CHANGEMENT' ) AND
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

$rq_info_id="
SELECT `changement_status`.`CHANGEMENT_STATUS` 
FROM `changement_liste` , `changement_status` 
WHERE `changement_liste`.`CHANGEMENT_LISTE_ID` = '".$ID."'
AND `changement_liste`.`CHANGEMENT_STATUS_ID` = `changement_status`.`CHANGEMENT_STATUS_ID` 
AND `changement_liste`.`ENABLE` = '0' 
LIMIT 1";
$res_rq_info_id = mysql_query($rq_info_id, $mysql_link) or die(mysql_error());
$tab_rq_info_id = mysql_fetch_assoc($res_rq_info_id);
$total_ligne_rq_info_id=mysql_num_rows($res_rq_info_id);

$CHANGEMENT_STATUS=$tab_rq_info_id['CHANGEMENT_STATUS'];	
mysql_free_result($res_rq_info_id);
$rq_info_id="
SELECT `CHANGEMENT_COMPTE_RENDU_ID` 
FROM `changement_compte_rendu` 
WHERE `CHANGEMENT_LISTE_ID`='".$ID."' 
AND `ENABLE`='0'";
$res_rq_info_id = mysql_query($rq_info_id, $mysql_link) or die(mysql_error());
$tab_rq_info_id = mysql_fetch_assoc($res_rq_info_id);
$total_ligne_rq_info_id=mysql_num_rows($res_rq_info_id);
if($total_ligne_rq_info_id==0){
  $action='Ajout';
}else{
  if($CHANGEMENT_STATUS!='Clotur&eacute;'){
    $action='Modif';
  }else{
    $action='Info';
  }
}
mysql_free_result($res_rq_info_id);


if(empty($tab_var['btn'])){
}else{

  # Cas Ajouter
  if($tab_var['btn']=="Ajouter"){
  	$ID=$tab_var['ID'];
  	$CHANGEMENT_ID=$ID;
  	$DATE_INSCRIPTION_DATE_DEBUT=$timestamp_DATE_DEBUT-$timestamp_DATE_INSCRIPTION;
   $DATE_VALIDATION_DATE_DEBUT=$timestamp_DATE_DEBUT-$timestamp_DATE_VALIDATION;      	   
   $DATE_INSCRIPTION_DATE_VALIDATION=$timestamp_DATE_VALIDATION-$timestamp_DATE_INSCRIPTION;
   $DATE_FIN_DATE_TERMNINE=$timestamp_DATE_TERMNINE-$timestamp_DATE_FIN;
   if(isset($tab_var['Commentaire'])){
    $Commentaire=$tab_var['Commentaire'];
   }
   if(isset($tab_var['Incident'])){
    $Incident=$tab_var['Incident'];
   }
   if(isset($tab_var['Incident_num'])){
    $Incident_num=$tab_var['Incident_num'];
   }
   if($Incident==''){
    $STOP=1;
   }
   if($Incident=='Oui'){
    if($Incident_num==''){
      $STOP=1;
      $STOP_NB_INC=1;
     }
   }
   if($Commentaire==''){
    $STOP=1;
    $STOP_COM=1;
   }
   if($STOP==0){
    $sql="
    INSERT INTO `changement_compte_rendu` (`CHANGEMENT_COMPTE_RENDU_ID` ,`CHANGEMENT_LISTE_ID` ,`CHANGEMENT_COMPTE_RENDU_UTILISATEUR_ID` ,`CHANGEMENT_COMPTE_RENDU_INC` ,`CHANGEMENT_COMPTE_RENDU_NB_INC` ,`CHANGEMENT_COMPTE_RENDU_COM` ,`CHANGEMENT_COMPTE_RENDU_TEMPS_I_V` ,`CHANGEMENT_COMPTE_RENDU_TEMPS_I_D` ,`CHANGEMENT_COMPTE_RENDU_TEMPS_V_D` ,`CHANGEMENT_COMPTE_RENDU_TEMPS_F_T` ,`ENABLE`)VALUES (NULL , '".$ID."', '".$UTILISATEUR_ID."', '".$Incident."', '".$Incident_num."', '".$Commentaire."', '".$DATE_INSCRIPTION_DATE_VALIDATION."', '".$DATE_INSCRIPTION_DATE_DEBUT."', '".$DATE_VALIDATION_DATE_DEBUT."', '".$DATE_FIN_DATE_TERMNINE."', '0');";

    mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
    $TABLE_SQL_SQL='changement_compte_rendu';       
    historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');    
    $sql="OPTIMIZE TABLE `changement_compte_rendu`;";

    mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
    $TABLE_SQL_SQL='changement_compte_rendu';       
    historique_sql_new($sql,$TABLE_SQL_SQL,'OPTIMIZE'); 
    
    $rq_info_CHANGEMENT_STATUS_ID="
      SELECT 
      `CHANGEMENT_STATUS_ID`
      FROM `changement_liste`
      WHERE 
      `CHANGEMENT_LISTE_ID` ='".$ID."' 
      AND `ENABLE` ='0'
      LIMIT 1";
      $res_rq_info_CHANGEMENT_STATUS_ID = mysql_query($rq_info_CHANGEMENT_STATUS_ID, $mysql_link) or die(mysql_error());
      $tab_rq_info_CHANGEMENT_STATUS_ID = mysql_fetch_assoc($res_rq_info_CHANGEMENT_STATUS_ID);
      $total_ligne_rq_info_CHANGEMENT_STATUS_ID=mysql_num_rows($res_rq_info_CHANGEMENT_STATUS_ID);
      $CHANGEMENT_STATUS_ID=$tab_rq_info_CHANGEMENT_STATUS_ID['CHANGEMENT_STATUS_ID'];
      mysql_free_result($res_rq_info_CHANGEMENT_STATUS_ID);
      $TRACE_ETAT=$CHANGEMENT_STATUS_ID;

      $TRACE_CATEGORIE='Changement';
      $TRACE_TABLE='changement_compte_rendu';
      $TRACE_REF_ID=$ID;
      $TRACE_ACTION='Ajout';
      moteur_trace($TRACE_CATEGORIE,$TRACE_TABLE,$TRACE_REF_ID,$TRACE_ACTION,$TRACE_ETAT);
    echo '
        <script language="JavaScript">
        url=("./index.php?ITEM=changement_Info_Changement&action=Info&ID='.$ID.'");
        window.location=url;
        </script>
        ';
    
   }
    $_GET['action']="Ajout";
  }
  # Cas Cloturer
  if($tab_var['btn']=="Cloturer"){
  	$ID=$tab_var['ID'];
  	$CHANGEMENT_ID=$ID;
  	$rq_info="
  	SELECT `MOTEUR_TRACE_ID` 
  	FROM `moteur_trace`
  	WHERE `MOTEUR_TRACE_CATEGORIE`='Changement' 
  	AND `MOTEUR_TRACE_TABLE`='changement_compte_rendu'
  	AND `MOTEUR_TRACE_REF_ID`='".$ID."'
    LIMIT 1";
    $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
    $tab_rq_info = mysql_fetch_assoc($res_rq_info);
    $total_ligne_rq_info=mysql_num_rows($res_rq_info);
    mysql_free_result($res_rq_info);
  	
   $DATE_INSCRIPTION_DATE_DEBUT=$timestamp_DATE_DEBUT-$timestamp_DATE_INSCRIPTION;
   $DATE_VALIDATION_DATE_DEBUT=$timestamp_DATE_DEBUT-$timestamp_DATE_VALIDATION;      	   
   $DATE_INSCRIPTION_DATE_VALIDATION=$timestamp_DATE_VALIDATION-$timestamp_DATE_INSCRIPTION;
   $DATE_FIN_DATE_TERMNINE=$timestamp_DATE_TERMNINE-$timestamp_DATE_FIN;
   if(isset($tab_var['Commentaire'])){
    $Commentaire=$tab_var['Commentaire'];
   }
   if(isset($tab_var['Incident'])){
    $Incident=$tab_var['Incident'];
   }
   if(isset($tab_var['Incident_num'])){
    $Incident_num=$tab_var['Incident_num'];
   }
   if($Incident==''){
    $STOP=1;
   }
   if($Incident=='Oui'){
    if($Incident_num==''){
      $STOP=1;
      $STOP_NB_INC=1;
     }
   }
   if($Commentaire==''){
    $STOP=1;
    $STOP_COM=1;
   }
   if($STOP==0){
   if($total_ligne_rq_info==0){
    $sql="
    INSERT INTO `changement_compte_rendu` (`CHANGEMENT_COMPTE_RENDU_ID` ,`CHANGEMENT_LISTE_ID` ,`CHANGEMENT_COMPTE_RENDU_UTILISATEUR_ID` ,`CHANGEMENT_COMPTE_RENDU_INC` ,`CHANGEMENT_COMPTE_RENDU_NB_INC` ,`CHANGEMENT_COMPTE_RENDU_COM` ,`CHANGEMENT_COMPTE_RENDU_TEMPS_I_V` ,`CHANGEMENT_COMPTE_RENDU_TEMPS_I_D` ,`CHANGEMENT_COMPTE_RENDU_TEMPS_V_D` ,`CHANGEMENT_COMPTE_RENDU_TEMPS_F_T` ,`ENABLE`)VALUES (NULL , '".$ID."', '".$UTILISATEUR_ID."', '".$Incident."', '".$Incident_num."', '".$Commentaire."', '".$DATE_INSCRIPTION_DATE_VALIDATION."', '".$DATE_INSCRIPTION_DATE_DEBUT."', '".$DATE_VALIDATION_DATE_DEBUT."', '".$DATE_FIN_DATE_TERMNINE."', '0');";

    mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
    $TABLE_SQL_SQL='changement_compte_rendu';       
    historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');    
    $sql="OPTIMIZE TABLE `changement_compte_rendu`;";

    mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
    $TABLE_SQL_SQL='changement_compte_rendu';       
    historique_sql_new($sql,$TABLE_SQL_SQL,'OPTIMIZE'); 

	$TRACE_ACTION='Ajout';
   }else{
   
   $rq_info="
    SELECT 
    `CHANGEMENT_COMPTE_RENDU_ID`
    FROM `changement_compte_rendu`
    WHERE 
    `CHANGEMENT_LISTE_ID` ='".$ID."' 
    AND `ENABLE` ='0'
    AND `CHANGEMENT_COMPTE_RENDU_INC` = '".$Incident."'
    AND `CHANGEMENT_COMPTE_RENDU_NB_INC` = '".$Incident_num."'
    AND `CHANGEMENT_COMPTE_RENDU_COM` = '".$Commentaire."'
    LIMIT 1";
    $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
    $tab_rq_info = mysql_fetch_assoc($res_rq_info);
    $total_ligne_rq_info=mysql_num_rows($res_rq_info);
    mysql_free_result($res_rq_info);
    if($total_ligne_rq_info==0){
      $sql="
      UPDATE `changement_compte_rendu` 
      SET 
      `CHANGEMENT_COMPTE_RENDU_UTILISATEUR_ID` = '".$UTILISATEUR_ID."',
      `CHANGEMENT_COMPTE_RENDU_INC` = '".$Incident."',
      `CHANGEMENT_COMPTE_RENDU_NB_INC` = '".$Incident_num."',
      `CHANGEMENT_COMPTE_RENDU_COM` = '".$Commentaire."',
      `CHANGEMENT_COMPTE_RENDU_TEMPS_I_V` = '".$DATE_INSCRIPTION_DATE_VALIDATION."',
      `CHANGEMENT_COMPTE_RENDU_TEMPS_I_D` = '".$DATE_INSCRIPTION_DATE_DEBUT."',
      `CHANGEMENT_COMPTE_RENDU_TEMPS_V_D` = '".$DATE_VALIDATION_DATE_DEBUT."',
      `CHANGEMENT_COMPTE_RENDU_TEMPS_F_T` = '".$DATE_FIN_DATE_TERMNINE."' 
      WHERE `CHANGEMENT_LISTE_ID` ='".$ID."'
       AND ENABLE='0';";

      mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
      $TABLE_SQL_SQL='changement_compte_rendu';       
      historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');    
      
      $sql="OPTIMIZE TABLE `changement_compte_rendu`;";
      mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
      $TABLE_SQL_SQL='changement_compte_rendu';       
      historique_sql_new($sql,$TABLE_SQL_SQL,'OPTIMIZE'); 
    
	$TRACE_ACTION='Modif';
    }else{
      $sql="
      UPDATE `changement_compte_rendu` 
      SET 
      `CHANGEMENT_COMPTE_RENDU_TEMPS_I_V` = '".$DATE_INSCRIPTION_DATE_VALIDATION."',
      `CHANGEMENT_COMPTE_RENDU_TEMPS_I_D` = '".$DATE_INSCRIPTION_DATE_DEBUT."',
      `CHANGEMENT_COMPTE_RENDU_TEMPS_V_D` = '".$DATE_VALIDATION_DATE_DEBUT."',
      `CHANGEMENT_COMPTE_RENDU_TEMPS_F_T` = '".$DATE_FIN_DATE_TERMNINE."' 
      WHERE `CHANGEMENT_LISTE_ID` ='".$ID."'
       AND ENABLE='0';";

      mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
      $TABLE_SQL_SQL='changement_compte_rendu';       
      historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');    
      
      $sql="OPTIMIZE TABLE `changement_compte_rendu`;";
      mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
      $TABLE_SQL_SQL='changement_compte_rendu';       
      historique_sql_new($sql,$TABLE_SQL_SQL,'OPTIMIZE'); 
    }  
   }
    $rq_info_status="
		SELECT `CHANGEMENT_STATUS_ID` 
		FROM `changement_status` 
		WHERE `CHANGEMENT_STATUS` = 'Clotur&eacute;'
		AND `ENABLE`='0'";
		$res_rq_info_status = mysql_query($rq_info_status, $mysql_link) or die(mysql_error());
		$tab_rq_info_status = mysql_fetch_assoc($res_rq_info_status);
		$total_ligne_rq_info_status=mysql_num_rows($res_rq_info_status);
		if($total_ligne_rq_info_status==0){
			$sql="INSERT INTO `changement_status` (`CHANGEMENT_STATUS_ID` ,`CHANGEMENT_STATUS`, `ENABLE` )VALUES (NULL , 'Clotur&eacute;', '0');";
	        	mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
		        $TABLE_SQL_SQL='changement_status';       
		        historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');
		        $rq_info_status="
			SELECT `CHANGEMENT_STATUS_ID` 
			FROM `changement_status` 
			WHERE `CHANGEMENT_STATUS` = 'Clotur&eacute;'
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
    $TRACE_TABLE='changement_compte_rendu';
    $TRACE_REF_ID=$CHANGEMENT_ID;
    
    $TRACE_ETAT=$CHANGEMENT_STATUS_ID;
    moteur_trace($TRACE_CATEGORIE,$TRACE_TABLE,$TRACE_REF_ID,$TRACE_ACTION,$TRACE_ETAT);
	        	
// mail de Cloture		

      echo '
      <script language="JavaScript">
      url=("./index.php?ITEM=changement_Send_Mail&type=cloture&ID='.$ID.'");
      window.location=url;
      </script>
      ';
   
   }
    $_GET['action']="Info";
  }
  # Cas Modifier
  if($tab_var['btn']=="Modifier"){
  	$ID=$tab_var['ID'];
  	$CHANGEMENT_ID=$ID;
  	$DATE_INSCRIPTION_DATE_DEBUT=$timestamp_DATE_DEBUT-$timestamp_DATE_INSCRIPTION;
   $DATE_VALIDATION_DATE_DEBUT=$timestamp_DATE_DEBUT-$timestamp_DATE_VALIDATION;      	   
   $DATE_INSCRIPTION_DATE_VALIDATION=$timestamp_DATE_VALIDATION-$timestamp_DATE_INSCRIPTION;
   $DATE_FIN_DATE_TERMNINE=$timestamp_DATE_TERMNINE-$timestamp_DATE_FIN;
   if(isset($tab_var['Commentaire'])){
    $Commentaire=$tab_var['Commentaire'];
   }
   if(isset($tab_var['Incident'])){
    $Incident=$tab_var['Incident'];
   }
   if(isset($tab_var['Incident_num'])){
    $Incident_num=$tab_var['Incident_num'];
   }
   if($Incident==''){
    $STOP=1;
   }
   if($Incident=='Oui'){
    if($Incident_num==''){
      $STOP=1;
      $STOP_NB_INC=1;
     }
   }
   if($Commentaire==''){
    $STOP=1;
    $STOP_COM=1;
   }
   if($STOP==0){
   $rq_info="
    SELECT 
    `CHANGEMENT_COMPTE_RENDU_ID`
    FROM `changement_compte_rendu`
    WHERE 
    `CHANGEMENT_LISTE_ID` ='".$ID."' 
    AND `ENABLE` ='0'
    AND `CHANGEMENT_COMPTE_RENDU_INC` = '".$Incident."'
    AND `CHANGEMENT_COMPTE_RENDU_NB_INC` = '".$Incident_num."'
    AND `CHANGEMENT_COMPTE_RENDU_COM` = '".$Commentaire."'
    LIMIT 1";
    $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
    $tab_rq_info = mysql_fetch_assoc($res_rq_info);
    $total_ligne_rq_info=mysql_num_rows($res_rq_info);
    mysql_free_result($res_rq_info);
    if($total_ligne_rq_info==0){
      $sql="
      UPDATE `changement_compte_rendu` 
      SET 
      `CHANGEMENT_COMPTE_RENDU_UTILISATEUR_ID` = '".$UTILISATEUR_ID."',
      `CHANGEMENT_COMPTE_RENDU_INC` = '".$Incident."',
      `CHANGEMENT_COMPTE_RENDU_NB_INC` = '".$Incident_num."',
      `CHANGEMENT_COMPTE_RENDU_COM` = '".$Commentaire."',
      `CHANGEMENT_COMPTE_RENDU_TEMPS_I_V` = '".$DATE_INSCRIPTION_DATE_VALIDATION."',
      `CHANGEMENT_COMPTE_RENDU_TEMPS_I_D` = '".$DATE_INSCRIPTION_DATE_DEBUT."',
      `CHANGEMENT_COMPTE_RENDU_TEMPS_V_D` = '".$DATE_VALIDATION_DATE_DEBUT."',
      `CHANGEMENT_COMPTE_RENDU_TEMPS_F_T` = '".$DATE_FIN_DATE_TERMNINE."' 
      WHERE `CHANGEMENT_LISTE_ID` ='".$ID."'
       AND ENABLE='0';";

      mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
      $TABLE_SQL_SQL='changement_compte_rendu';       
      historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');    
      
      $sql="OPTIMIZE TABLE `changement_compte_rendu`;";
      mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
      $TABLE_SQL_SQL='changement_compte_rendu';       
      historique_sql_new($sql,$TABLE_SQL_SQL,'OPTIMIZE'); 
    
      $rq_info_CHANGEMENT_STATUS_ID="
      SELECT 
      `CHANGEMENT_STATUS_ID`
      FROM `changement_liste`
      WHERE 
      `CHANGEMENT_LISTE_ID` ='".$ID."' 
      AND `ENABLE` ='0'
      LIMIT 1";
      $res_rq_info_CHANGEMENT_STATUS_ID = mysql_query($rq_info_CHANGEMENT_STATUS_ID, $mysql_link) or die(mysql_error());
      $tab_rq_info_CHANGEMENT_STATUS_ID = mysql_fetch_assoc($res_rq_info_CHANGEMENT_STATUS_ID);
      $total_ligne_rq_info_CHANGEMENT_STATUS_ID=mysql_num_rows($res_rq_info_CHANGEMENT_STATUS_ID);
      $CHANGEMENT_STATUS_ID=$tab_rq_info_CHANGEMENT_STATUS_ID['CHANGEMENT_STATUS_ID'];
      mysql_free_result($res_rq_info_CHANGEMENT_STATUS_ID);
      $TRACE_ETAT=$CHANGEMENT_STATUS_ID;

      $TRACE_CATEGORIE='Changement';
      $TRACE_TABLE='changement_compte_rendu';
      $TRACE_REF_ID=$ID;
      $TRACE_ACTION='Modif';
      moteur_trace($TRACE_CATEGORIE,$TRACE_TABLE,$TRACE_REF_ID,$TRACE_ACTION,$TRACE_ETAT);
    }else{
      $sql="
      UPDATE `changement_compte_rendu` 
      SET 
      `CHANGEMENT_COMPTE_RENDU_TEMPS_I_V` = '".$DATE_INSCRIPTION_DATE_VALIDATION."',
      `CHANGEMENT_COMPTE_RENDU_TEMPS_I_D` = '".$DATE_INSCRIPTION_DATE_DEBUT."',
      `CHANGEMENT_COMPTE_RENDU_TEMPS_V_D` = '".$DATE_VALIDATION_DATE_DEBUT."',
      `CHANGEMENT_COMPTE_RENDU_TEMPS_F_T` = '".$DATE_FIN_DATE_TERMNINE."' 
      WHERE `CHANGEMENT_LISTE_ID` ='".$ID."'
       AND ENABLE='0';";

      mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
      $TABLE_SQL_SQL='changement_compte_rendu';       
      historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');    
      
      $sql="OPTIMIZE TABLE `changement_compte_rendu`;";
      mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
      $TABLE_SQL_SQL='changement_compte_rendu';       
      historique_sql_new($sql,$TABLE_SQL_SQL,'OPTIMIZE'); 
    }
    echo '
        <script language="JavaScript">
        url=("./index.php?ITEM=changement_Info_Changement&action=Info&ID='.$ID.'");
        window.location=url;
        </script>
        ';
    
   }
    $_GET['action']="Modif";
  }

}

//# Cas action Info
if($action=="Info"){
  $rq_info_cr_id="
  SELECT 
  `CHANGEMENT_COMPTE_RENDU_ID`,
  `CHANGEMENT_LISTE_ID`, 
  `CHANGEMENT_COMPTE_RENDU_INC`, 
  `CHANGEMENT_COMPTE_RENDU_NB_INC`, 
  `CHANGEMENT_COMPTE_RENDU_COM`, 
  `CHANGEMENT_COMPTE_RENDU_TEMPS_I_V`, 
  `CHANGEMENT_COMPTE_RENDU_TEMPS_I_D`, 
  `CHANGEMENT_COMPTE_RENDU_TEMPS_V_D`, 
  `CHANGEMENT_COMPTE_RENDU_TEMPS_F_T`
  FROM `changement_compte_rendu` 
  WHERE `CHANGEMENT_LISTE_ID`= '".$ID."'
  AND `ENABLE` = '0'
	";
	$res_rq_info_cr_id = mysql_query($rq_info_cr_id, $mysql_link) or die(mysql_error());
	$tab_rq_info_cr_id = mysql_fetch_assoc($res_rq_info_cr_id);
	$total_ligne_rq_info_cr_id=mysql_num_rows($res_rq_info_cr_id);
	$CHANGEMENT_COMPTE_RENDU_ID=$tab_rq_info_cr_id['CHANGEMENT_COMPTE_RENDU_ID'];
	$CHANGEMENT_LISTE_ID=$tab_rq_info_cr_id['CHANGEMENT_LISTE_ID'];
	$Incident=$tab_rq_info_cr_id['CHANGEMENT_COMPTE_RENDU_INC'];
	$Incident_num=$tab_rq_info_cr_id['CHANGEMENT_COMPTE_RENDU_NB_INC'];
	$Commentaire=$tab_rq_info_cr_id['CHANGEMENT_COMPTE_RENDU_COM'];
	$CHANGEMENT_COMPTE_RENDU_TEMPS_I_V=$tab_rq_info_cr_id['CHANGEMENT_COMPTE_RENDU_TEMPS_I_V'];
	$CHANGEMENT_COMPTE_RENDU_TEMPS_I_D=$tab_rq_info_cr_id['CHANGEMENT_COMPTE_RENDU_TEMPS_I_D'];
	$CHANGEMENT_COMPTE_RENDU_TEMPS_V_D=$tab_rq_info_cr_id['CHANGEMENT_COMPTE_RENDU_TEMPS_V_D'];
	$CHANGEMENT_COMPTE_RENDU_TEMPS_F_T=$tab_rq_info_cr_id['CHANGEMENT_COMPTE_RENDU_TEMPS_F_T'];
	

	mysql_free_result($res_rq_info_cr_id);
}
//# Cas action Modif
if($action=="Modif"){
  $rq_info_cr_id="
  SELECT 
  `CHANGEMENT_COMPTE_RENDU_ID`,
  `CHANGEMENT_LISTE_ID`, 
  `CHANGEMENT_COMPTE_RENDU_INC`, 
  `CHANGEMENT_COMPTE_RENDU_NB_INC`, 
  `CHANGEMENT_COMPTE_RENDU_COM`, 
  `CHANGEMENT_COMPTE_RENDU_TEMPS_I_V`, 
  `CHANGEMENT_COMPTE_RENDU_TEMPS_I_D`, 
  `CHANGEMENT_COMPTE_RENDU_TEMPS_V_D`, 
  `CHANGEMENT_COMPTE_RENDU_TEMPS_F_T`
  FROM `changement_compte_rendu` 
  WHERE `CHANGEMENT_LISTE_ID`= '".$ID."'
  AND `ENABLE` = '0'
	";
	$res_rq_info_cr_id = mysql_query($rq_info_cr_id, $mysql_link) or die(mysql_error());
	$tab_rq_info_cr_id = mysql_fetch_assoc($res_rq_info_cr_id);
	$total_ligne_rq_info_cr_id=mysql_num_rows($res_rq_info_cr_id);
	$CHANGEMENT_COMPTE_RENDU_ID=$tab_rq_info_cr_id['CHANGEMENT_COMPTE_RENDU_ID'];
	$CHANGEMENT_LISTE_ID=$tab_rq_info_cr_id['CHANGEMENT_LISTE_ID'];
	$Incident=$tab_rq_info_cr_id['CHANGEMENT_COMPTE_RENDU_INC'];
	$Incident_num=$tab_rq_info_cr_id['CHANGEMENT_COMPTE_RENDU_NB_INC'];
	$Commentaire=$tab_rq_info_cr_id['CHANGEMENT_COMPTE_RENDU_COM'];
	$CHANGEMENT_COMPTE_RENDU_TEMPS_I_V=$tab_rq_info_cr_id['CHANGEMENT_COMPTE_RENDU_TEMPS_I_V'];
	$CHANGEMENT_COMPTE_RENDU_TEMPS_I_D=$tab_rq_info_cr_id['CHANGEMENT_COMPTE_RENDU_TEMPS_I_D'];
	$CHANGEMENT_COMPTE_RENDU_TEMPS_V_D=$tab_rq_info_cr_id['CHANGEMENT_COMPTE_RENDU_TEMPS_V_D'];
	$CHANGEMENT_COMPTE_RENDU_TEMPS_F_T=$tab_rq_info_cr_id['CHANGEMENT_COMPTE_RENDU_TEMPS_F_T'];
	

	mysql_free_result($res_rq_info_cr_id);
}

echo '
<!--D&eacute;but page HTML -->  
<div align="center">

<form method="post" name="frm_inter" id="frm_inter" action="./index.php?ITEM='.$_GET['ITEM'].'">
  <table class="table_inc">';
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
	ORDER BY `moteur_trace`.`MOTEUR_TRACE_DATE_TRI` DESC";
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
	echo '<tr align="center" class="titre">
        <td colspan="2"><h2>&nbsp;[&nbsp;';
    if($action=="Ajout"){
      echo '
      Cr&eacute;ation de la fiche compte rendu du changement n&deg; <a href="#" class="infobulledroite">'.$ID.'<span>'.$AFF_SPAN.'</span></a>';
    }
    if($action=="Modif"){
        if($ROLE==0){
          echo 'Modification de la fiche compte rendu du changement n&deg; <a href="#" class="infobulledroite">'.$ID.'<span>'.$AFF_SPAN.'</span></a>';
        }else{
          echo 'Information de la fiche compte rendu du changement n&deg; <a href="#" class="infobulledroite">'.$ID.'<span>'.$AFF_SPAN.'</span></a>';
        }
    }
    if($action=="Info"){
          echo 'Information de la fiche compte rendu du changement n&deg; <a href="#" class="infobulledroite">'.$ID.'<span>'.$AFF_SPAN.'</span></a>';
    }
    
    echo '&nbsp;]&nbsp;</h2></td>		
      </tr>';

		   
		   
		   if($action=='Info'){
          $timestamp=$CHANGEMENT_COMPTE_RENDU_TEMPS_I_D;
        }else{  
          $timestamp=$timestamp_DATE_DEBUT-$timestamp_DATE_INSCRIPTION;
        }
		   if($timestamp < 0){
          $signe='-';
        }else{
          $signe='';
        }
        $timestamp = abs($timestamp);
        $diff_jours = floor($timestamp / 86400); //Calcul des jours écoulées/restantes
        $timestamp = $timestamp - ($diff_jours * 86400);
        $diff_heure = floor($timestamp / 3600); //Calcul des heures écoulées/restantes
        $timestamp = $timestamp - ($diff_heure * 3600);
        $diff_min = floor($timestamp / 60); //Calcul des minutes écoulées/restantes
        $timestamp = $timestamp - ($diff_min * 60);
        $diff_sec = floor($timestamp); //Calcul des secondes écoulées/restantes
        $DATE_INSCRIPTION_DATE_DEBUT=$signe.' '.$diff_jours.' j '.$diff_heure.' h '.$diff_min .' min '.$diff_sec .' sec'; 

		   if($action=='Info'){
          $timestamp=$CHANGEMENT_COMPTE_RENDU_TEMPS_V_D;
        }else{  
          $timestamp=$timestamp_DATE_DEBUT-$timestamp_DATE_VALIDATION;
        }
		   if($timestamp < 0){
          $signe='-';
        }else{
          $signe='';
        }
        $timestamp = abs($timestamp);
        $diff_jours = floor($timestamp / 86400); //Calcul des jours écoulées/restantes
        $timestamp = $timestamp - ($diff_jours * 86400);
        $diff_heure = floor($timestamp / 3600); //Calcul des heures écoulées/restantes
        $timestamp = $timestamp - ($diff_heure * 3600);
        $diff_min = floor($timestamp / 60); //Calcul des minutes écoulées/restantes
        $timestamp = $timestamp - ($diff_min * 60);
        $diff_sec = floor($timestamp); //Calcul des secondes écoulées/restantes
        $DATE_VALIDATION_DATE_DEBUT=$signe.' '.$diff_jours.' j '.$diff_heure.' h '.$diff_min .' min '.$diff_sec .' sec'; 
        	   
		   
		   if($action=='Info'){
          $timestamp=$CHANGEMENT_COMPTE_RENDU_TEMPS_I_V;
        }else{  
          $timestamp=$timestamp_DATE_VALIDATION-$timestamp_DATE_INSCRIPTION;
        }
		   if($timestamp < 0){
          $signe='-';
        }else{
          $signe='';
        }
        $timestamp = abs($timestamp);
        $diff_jours = floor($timestamp / 86400); //Calcul des jours écoulées/restantes
        $timestamp = $timestamp - ($diff_jours * 86400);
        $diff_heure = floor($timestamp / 3600); //Calcul des heures écoulées/restantes
        $timestamp = $timestamp - ($diff_heure * 3600);
        $diff_min = floor($timestamp / 60); //Calcul des minutes écoulées/restantes
        $timestamp = $timestamp - ($diff_min * 60);
        $diff_sec = floor($timestamp); //Calcul des secondes écoulées/restantes
        $DATE_INSCRIPTION_DATE_VALIDATION=$signe.' '.$diff_jours.' j '.$diff_heure.' h '.$diff_min .' min '.$diff_sec .' sec'; 
        
        if($action=='Info'){
          $timestamp=$CHANGEMENT_COMPTE_RENDU_TEMPS_F_T;
        }else{  
          $timestamp=$timestamp_DATE_TERMNINE-$timestamp_DATE_FIN;
        }
		   if($timestamp < 0){
          $signe='-';
        }else{
          $signe='';
        }
        $timestamp = abs($timestamp);
        $diff_jours = floor($timestamp / 86400); //Calcul des jours écoulées/restantes
        $timestamp = $timestamp - ($diff_jours * 86400);
        $diff_heure = floor($timestamp / 3600); //Calcul des heures écoulées/restantes
        $timestamp = $timestamp - ($diff_heure * 3600);
        $diff_min = floor($timestamp / 60); //Calcul des minutes écoulées/restantes
        $timestamp = $timestamp - ($diff_min * 60);
        $diff_sec = floor($timestamp); //Calcul des secondes écoulées/restantes
        $DATE_FIN_DATE_TERMNINE=$signe.' '.$diff_jours.' j '.$diff_heure.' h '.$diff_min .' min '.$diff_sec .' sec'; 

		   

       $j++;
		    if ($j%2) { $class = "pair";}else{$class = "impair";} 
		  echo '
		    <tr class="'.$class.'">
		     <td align="left">&nbsp;Incident induit&nbsp;*&nbsp;</td>
		     <td align="left">&nbsp;';
              echo '
              <INPUT type="radio" '.$disabled_var.' name="Incident" value="Oui"';
              if($Incident=='Oui'){echo ' CHECKED';} 
              echo '>
              &nbsp;Oui&nbsp;/&nbsp;Non&nbsp;
              <INPUT type="radio" '.$disabled_var.' name="Incident" value="Non"'; 
              if($Incident=='Non'){echo ' CHECKED';} 
              echo '>&nbsp;-&nbsp;';
              if($action!='Info'){
              	echo '<input '.$readonly_var.' id="Incident_num" name="Incident_num" type="text" value="'.stripslashes($Incident_num).'" size="20" maxlength="20"/>&nbsp;';
              }else{
              	echo stripslashes($Incident_num);
              }
              echo '
              </td>
		   </tr>';

		    $j++;
		    if ($j%2) { $class = "pair";}else{$class = "impair";} 
		  echo '
		    <tr class="'.$class.'">
		     <td align="left">&nbsp;Commentaires&nbsp;*&nbsp;</td>
		     <td align="left">&nbsp;';
		     if($action!='Info'){
		     	echo '<textarea '.$readonly_var.' id="Commentaire" name="Commentaire" cols="70" rows="2">'.stripslashes($Commentaire).'</textarea>';
		}else{
		echo nl2br(stripslashes($Commentaire));
		}
		 echo '    	
		     &nbsp;</td>
		   </tr>';
		   if($ROLE==0){
		   $j++;
		    if ($j%2) { $class = "pair";}else{$class = "impair";} 
        echo '
		    <tr class="'.$class.'">
		     <td align="left">&nbsp;Date d\'inscription&nbsp;</td>
		     <td align="left">&nbsp;'.$DATE_INSCRIPTION.'
		     &nbsp;</td>
		   </tr>';
		   $j++;
		    if ($j%2) { $class = "pair";}else{$class = "impair";} 
        echo '
		    <tr class="'.$class.'">
		     <td align="left">&nbsp;Date de validation&nbsp;</td>
		     <td align="left">&nbsp;'.$DATE_VALIDATION.'
		     &nbsp;</td>
		   </tr>';
		   $j++;
		    if ($j%2) { $class = "pair";}else{$class = "impair";} 
        echo '
		    <tr class="'.$class.'">
		     <td align="left">&nbsp;Date de d&eacute;but&nbsp;</td>
		     <td align="left">&nbsp;'.$DATE_DEBUT.'
		     &nbsp;</td>
		   </tr>';
		   $j++;
		    if ($j%2) { $class = "pair";}else{$class = "impair";} 
        echo '
		    <tr class="'.$class.'">
		     <td align="left">&nbsp;Date de fin&nbsp;</td>
		     <td align="left">&nbsp;'.$DATE_FIN.'
		     &nbsp;</td>
		   </tr>';
		   
		   $j++;
		    if ($j%2) { $class = "pair";}else{$class = "impair";} 
        echo '
		    <tr class="'.$class.'">
		     <td align="left">&nbsp;Delai de pr&eacute;venance&nbsp;:&nbsp;</BR>&nbsp;(Diff&eacute;rence entre la date de d&eacute;but et la date d\'inscription)&nbsp;</td>
		     <td align="left">&nbsp;'.$DATE_INSCRIPTION_DATE_DEBUT.'
		     &nbsp;</td>
		   </tr>';
		   /*
		   $j++;
		    if ($j%2) { $class = "pair";}else{$class = "impair";} 
        echo '
		    <tr class="'.$class.'">
		     <td align="left">&nbsp;Diff&eacute;rence entre :&nbsp;</BR>&nbsp;la date de d&eacute;but et la date de validation&nbsp;</td>
		     <td align="left">&nbsp;'.$DATE_VALIDATION_DATE_DEBUT.'
		     &nbsp;</td>
		   </tr>';
		   $j++;
		    if ($j%2) { $class = "pair";}else{$class = "impair";} 
        echo '
		    <tr class="'.$class.'">
		     <td align="left">&nbsp;Diff&eacute;rence entre :&nbsp;</BR>&nbsp;la date d\'inscription et la date de validation&nbsp;</td>
		     <td align="left">&nbsp;'.$DATE_INSCRIPTION_DATE_VALIDATION.'
		     &nbsp;</td>
		   </tr>';
		   $j++;
		    if ($j%2) { $class = "pair";}else{$class = "impair";} 
        echo '
		    <tr class="'.$class.'">
		     <td align="left">&nbsp;Diff&eacute;rence entre :&nbsp;</BR>&nbsp;la date de fin et la date termin&eacute;&nbsp;</td>
		     <td align="left">&nbsp;'.$DATE_FIN_DATE_TERMNINE.'
		     &nbsp;</td>
		   </tr>';*/
		}
		   
  if($STOP_NB_INC==1){
   	$j++;
	   if ($j%2) { $class = "pair";}else{$class = "impair";} 
	   echo '
	   <tr class="'.$class.'">
	    <td align="center" colspan="4"><font color=#993333><b>Merci de saisir le num&eacute;ro d\'incident.</b></font>
	    </td>
	   </tr>';
   }
  if($STOP_COM==1){
   	$j++;
	   if ($j%2) { $class = "pair";}else{$class = "impair";} 
	   echo '
	   <tr class="'.$class.'">
	    <td align="center" colspan="4"><font color=#993333><b>Merci de saisir le Commentaire.</b></font>
	    </td>
	   </tr>';
   }
   if($STOP==1){
   	$j++;
	   if ($j%2) { $class = "pair";}else{$class = "impair";} 
	   echo '
	   <tr class="'.$class.'">
	    <td align="center" colspan="4"><font color=#993333><b>Merci de saisir L\'ensemble des champs obligatoire.</b></font>
	    </td>
	   </tr>';
   }
   echo '
   <tr class="titre">
	<td colspan="2" align="center">
	<h2>';
	if($action=='Ajout'){
		echo '
		<input type="hidden" name="action" value="Ajout">
		<input type="hidden" name="ID" value="'.$ID.'">';
		if($CHANGEMENT_STATUS=='Termin&eacute;'){
      echo '<input name="btn" type="submit" id="btn" value="Cloturer">'; 
		}else{
      echo '<input name="btn" type="submit" id="btn" value="Ajouter">'; 
		}
	}
	if($action=='Modif'){
		echo '
		<input type="hidden" name="ID" value="'.$ID.'">
		<input type="hidden" name="action" value="'.$action.'">';
		if($CHANGEMENT_STATUS=='Termin&eacute;'){
      echo '<input name="btn" type="submit" id="btn" value="Cloturer">'; 
		}else{
      echo '<input name="btn" type="submit" id="btn" value="Modifier">'; 
		}
		
		
	}
	if($action=='Info'){
		echo '
		<input type="hidden" name="ID" value="'.$ID.'">
		<input type="hidden" name="action" value="'.$action.'">';
	}
	echo '
  </h2>
	</td>
	</tr>
	<tr class="titre">
	<td colspan="2" align="center">';
	if($action=="Info"){
		echo '<h2>&nbsp;[&nbsp;<a href="./index.php?ITEM=changement_Info_Changement&action=Info&ID='.$ID.'">Retour</a>&nbsp;]&nbsp;</h2>';
        }else{
        	echo '<h2>&nbsp;[&nbsp;<a href="./index.php?ITEM=changement_Modif_Changement&action=Modif&ID='.$ID.'">Retour</a>&nbsp;]&nbsp;</h2>';
	}
	echo '</td>
	</tr>
</table>
</form>
</div>
';

mysql_close($mysql_link); 
?>