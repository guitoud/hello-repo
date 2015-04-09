<?PHP
header("Content-Type: application/csv-tab-delimited-table"); 
header("Content-disposition: filename=liste_des_changements.csv"); 
/*******************************************************************
   Interface liste des changements
   Version 1.0.0  
  15/11/2010 - VGU - Creation fichier
**************************************************Guibert Vincent***
*******************************************************************/

require("../cf/conf_outil_icdc.php"); 
require("../cf/fonctions.php");

if(isset($_GET['date_limit'])){
  $date_limit=$_GET['date_limit'];
  if($date_limit!=''){
    $DATE_LIMIT=date("Ymd", mktime(0, 0, 0, date("m"), date("d")-$date_limit, date("Y")));
    $DATE_LIMIT_SQL="`changement_liste`.`CHANGEMENT_LISTE_DATE_DEBUT` >= '".$DATE_LIMIT."' AND";	
  }else{
    $DATE_LIMIT_SQL="";
    $date_limit='';
  }
}else{
  $DATE_LIMIT_SQL="";
  $date_limit='';
}


$rq_info="
SELECT COUNT(`CHANGEMENT_LISTE_ID`) AS `NB`
FROM `changement_liste`
WHERE 
".$DATE_LIMIT_SQL."
`ENABLE` = '0'
";
$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
$tab_rq_info = mysql_fetch_assoc($res_rq_info);
$total_ligne_rq_info=mysql_num_rows($res_rq_info); 
$NB_ALL=$tab_rq_info['NB'];
mysql_free_result($res_rq_info);
// gestion des droits d'acces
if(isset($_SESSION['LOGIN'])){
	$LOGIN=$_SESSION['LOGIN'];
	$rq_Selectionner_user ="
	SELECT `UTILISATEUR_ID`,`LOGIN`, `NOM`, `PRENOM` 
	FROM `moteur_utilisateur` 
	WHERE `LOGIN` = '".$LOGIN."'
	";
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

$date=date("Ymd");  

$numLigne=0;
$nbinter=0;

      $rq_changement_info="
      SELECT 
      `changement_liste`.`CHANGEMENT_LISTE_ID`, 
      `moteur_utilisateur`.`UTILISATEUR_ID` , 
      `moteur_utilisateur`.`LOGIN` , 
      `moteur_utilisateur`.`NOM` , 
      `moteur_utilisateur`.`PRENOM` , 
      `changement_liste`.`CHANGEMENT_LISTE_ID` , 
      `changement_liste`.`CHANGEMENT_LISTE_UTILISATEUR_ID` , 
      `changement_liste`.`CHANGEMENT_LISTE_DATE_DEBUT` , 
      `changement_liste`.`CHANGEMENT_LISTE_DATE_FIN` , 
      `changement_liste`.`CHANGEMENT_LISTE_HEURE_DEBUT` , 
      `changement_liste`.`CHANGEMENT_LISTE_HEURE_FIN` , 
      `changement_liste`.`CHANGEMENT_LISTE_DATE_MODIFICATION` , 
      `changement_liste`.`CHANGEMENT_LISTE_LIB` ,
      `changement_demande`.`CHANGEMENT_DEMANDE_LIB`,
      `changement_status`.`CHANGEMENT_STATUS` ,
      `changement_status`.`CHANGEMENT_STATUS_COULEUR_FOND` ,
      `changement_status`.`CHANGEMENT_STATUS_COULEUR_TEXT`
      FROM `changement_liste` , `moteur_utilisateur`,`changement_status`,`changement_demande`
      WHERE 
      `changement_liste`.`CHANGEMENT_STATUS_ID`=`changement_status`.`CHANGEMENT_STATUS_ID` AND
      `changement_liste`.`CHANGEMENT_DEMANDE_ID`=`changement_demande`.`CHANGEMENT_DEMANDE_ID` AND
      `moteur_utilisateur`.`UTILISATEUR_ID` = `changement_liste`.`CHANGEMENT_LISTE_UTILISATEUR_ID` AND 
      ".$DATE_LIMIT_SQL."
      `changement_liste`.`ENABLE` = '0'
      ORDER BY `changement_liste`.`CHANGEMENT_LISTE_DATE_DEBUT` DESC";
      $res_rq_changement_info = mysql_query($rq_changement_info, $mysql_link) or die(mysql_error());
      $tab_rq_changement_info = mysql_fetch_assoc($res_rq_changement_info);
      $total_ligne_rq_changement_info=mysql_num_rows($res_rq_changement_info);
      if($total_ligne_rq_changement_info!=0){
        echo 'Identifiant;Demandeur;Status;Type de demande;Date de Debut - Heure de Debut;Date de Fin - Heure de Fin;Titre du changement;';
        echo "\n";
        do {

        $ID=$tab_rq_changement_info['CHANGEMENT_LISTE_ID'];  
        $LOGIN=str_replace(";", ":", html_entity_decode($tab_rq_changement_info['LOGIN']));
	$NOM=str_replace(";", ":", html_entity_decode($tab_rq_changement_info['NOM']));
	$PRENOM=str_replace(";", ":", html_entity_decode($tab_rq_changement_info['PRENOM']));
	$STATUS=str_replace(";", ":", html_entity_decode($tab_rq_changement_info['CHANGEMENT_STATUS']));
	$CHANGEMENT_DEMANDE_LIB=str_replace(";", ":", html_entity_decode($tab_rq_changement_info['CHANGEMENT_DEMANDE_LIB']));	
	$DATE_DEBUT=$tab_rq_changement_info['CHANGEMENT_LISTE_DATE_DEBUT'];
	$DATE_DEBUT_jour=substr($DATE_DEBUT,6,2);
	$DATE_DEBUT_mois=substr($DATE_DEBUT,4,2);
	$DATE_DEBUT_annee=substr($DATE_DEBUT,0,4); 
	$DATE_DEBUT=$DATE_DEBUT_jour.'/'.$DATE_DEBUT_mois.'/'.$DATE_DEBUT_annee;
	$DATE_FIN=$tab_rq_changement_info['CHANGEMENT_LISTE_DATE_FIN'];
	$DATE_FIN_jour=substr($DATE_FIN,6,2);
	$DATE_FIN_mois=substr($DATE_FIN,4,2);
	$DATE_FIN_annee=substr($DATE_FIN,0,4); 
	$DATE_FIN=$DATE_FIN_jour.'/'.$DATE_FIN_mois.'/'.$DATE_FIN_annee;
	$HEURE_DEBUT=$tab_rq_changement_info['CHANGEMENT_LISTE_HEURE_DEBUT'];
	$HEURE_FIN=$tab_rq_changement_info['CHANGEMENT_LISTE_HEURE_FIN'];
	$DATE_MODIFICATION=$tab_rq_changement_info['CHANGEMENT_LISTE_DATE_MODIFICATION'];
	$LIB=str_replace("\n", " ",str_replace("\r", " ",str_replace(";", ":", html_entity_decode($tab_rq_changement_info['CHANGEMENT_LISTE_LIB']))));

              echo ''.$ID.';'.stripslashes($PRENOM).' '.stripslashes($NOM).';'.stripslashes($STATUS).';'.stripslashes($CHANGEMENT_DEMANDE_LIB).';'.datebdd_nomjour($DATE_DEBUT).' '.$DATE_DEBUT.' '.$HEURE_DEBUT.';'.datebdd_nomjour($DATE_FIN).' '.$DATE_FIN.' '.$HEURE_FIN.';'.stripslashes($LIB).';';
              echo "\n";

        } while ($tab_rq_changement_info = mysql_fetch_assoc($res_rq_changement_info));
        $ligne= mysql_num_rows($res_rq_changement_info);
        if($ligne > 0) {
          mysql_data_seek($res_rq_changement_info, 0);
          $tab_rq_changement_info = mysql_fetch_assoc($res_rq_changement_info);
        }
        mysql_free_result($res_rq_changement_info);
        
        }else{
           echo'Pas d\'information;';
          echo "\n";
        }
mysql_close($mysql_link); 
?>
