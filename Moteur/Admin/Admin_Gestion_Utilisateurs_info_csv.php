<?PHP
header("Content-Type: application/csv-tab-delimited-table"); 
header("Content-disposition: filename=liste_utilisateur_role.csv"); 

/*******************************************************************
   Interface Gestion des Utilisateurs
   Version 1.0.0    
  25/02/2010 - VGU - Creation fichier
**************************************************Guibert Vincent***
*******************************************************************/

require("../cf/conf_outil_icdc.php"); 
require("../cf/fonctions.php");
$rq_info="
SELECT COUNT(`ROLE_ID`) AS `NB` FROM `moteur_role`
";
$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
$tab_rq_info = mysql_fetch_assoc($res_rq_info);
$total_ligne_rq_info=mysql_num_rows($res_rq_info); 
$colspan=3+$tab_rq_info['NB'];
mysql_free_result($res_rq_info);
$rq_info_role="SELECT * FROM `moteur_role` ORDER BY `ROLE` ASC";
$res_rq_info_role = mysql_query($rq_info_role, $mysql_link) or die(mysql_error());
$tab_rq_info_role = mysql_fetch_assoc($res_rq_info_role);
$total_ligne_rq_info_role=mysql_num_rows($res_rq_info_role); 
$j=0;
echo 'Login;Nom;Prenom;';
      if($colspan!=3){
      	do {
      		echo $tab_rq_info_role['ROLE'].';';
	} while ($tab_rq_info_role = mysql_fetch_assoc($res_rq_info_role));
	$ligne= mysql_num_rows($res_rq_info_role);
	if($ligne > 0) {
		mysql_data_seek($res_rq_info_role, 0);
		$tab_rq_info_role = mysql_fetch_assoc($res_rq_info_role);
	}
      }
      echo "\n";

      $rq_info="
      SELECT `UTILISATEUR_ID`, `LOGIN`, `NOM`, `PRENOM`
      FROM `moteur_utilisateur`
      WHERE `ENABLE` IN ('Y','N')
      ORDER BY `NOM`, `PRENOM`
       ";
      $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
      $tab_rq_info = mysql_fetch_assoc($res_rq_info);
      $total_ligne_rq_info=mysql_num_rows($res_rq_info); 
    do {
      $ID=$tab_rq_info['UTILISATEUR_ID'];
      $LOGIN=str_replace(";", ":", html_entity_decode(stripslashes($tab_rq_info['LOGIN'])));
      $NOM=str_replace(";", ":", html_entity_decode(stripslashes($tab_rq_info['NOM'])));
      $PRENOM=str_replace(";", ":", html_entity_decode(stripslashes($tab_rq_info['PRENOM'])));

      echo $LOGIN.';'.$NOM.';'.$PRENOM.';';
        do {
        	$ROLE_ID=$tab_rq_info_role['ROLE_ID'];
        	$rq_info_role_user="
        	SELECT * 
        	FROM `moteur_role_utilisateur` 
        	WHERE `ROLE_ID`='".$ROLE_ID."' 
        	AND `UTILISATEUR_ID`='".$ID."'
        	AND `ROLE_UTILISATEUR_ACCES`='0'";
		$res_rq_info_role_user = mysql_query($rq_info_role_user, $mysql_link) or die(mysql_error());
		$tab_rq_info_role_user = mysql_fetch_assoc($res_rq_info_role_user);
		$total_ligne_rq_info_role_user=mysql_num_rows($res_rq_info_role_user);
		if($total_ligne_rq_info_role_user==0){
			echo ';';
		}else{
			echo 'OK;';
		}
		mysql_free_result($res_rq_info_role_user);
      		
	} while ($tab_rq_info_role = mysql_fetch_assoc($res_rq_info_role));
	$ligne= mysql_num_rows($res_rq_info_role);
	if($ligne > 0) {
		mysql_data_seek($res_rq_info_role, 0);
		$tab_rq_info_role = mysql_fetch_assoc($res_rq_info_role);
	}     
	echo "\n";
    } while ($tab_rq_info = mysql_fetch_assoc($res_rq_info));
    $ligne= mysql_num_rows($res_rq_info);
    if($ligne > 0) {
      mysql_data_seek($res_rq_info, 0);
      $tab_rq_info = mysql_fetch_assoc($res_rq_info);
    }

mysql_free_result($res_rq_info_role);
mysql_close(); 
?>