<?PHP
header("Content-Type: application/csv-tab-delimited-table"); 
header("Content-disposition: filename=liste_page_menu_role.csv"); 
/*******************************************************************
   Interface Gestion des pages
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
$colspan=2+$tab_rq_info['NB'];
mysql_free_result($res_rq_info);
$rq_info_role="SELECT * FROM `moteur_role` ORDER BY `ROLE` ASC";
$res_rq_info_role = mysql_query($rq_info_role, $mysql_link) or die(mysql_error());
$tab_rq_info_role = mysql_fetch_assoc($res_rq_info_role);
$total_ligne_rq_info_role=mysql_num_rows($res_rq_info_role); 
$j=0;
echo 'ITEM;fichier;LEGENDe;Menu;';

      if($colspan!=2){
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
      SELECT `PAGES_ID`, `ITEM`, `URLP`, `LEGEND`
      FROM `moteur_pages`
      WHERE `ENABLE`=0
      ORDER BY `URLP`, `ITEM`
       ";
      $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
      $tab_rq_info = mysql_fetch_assoc($res_rq_info);
      $total_ligne_rq_info=mysql_num_rows($res_rq_info); 
    do {
      $ID=$tab_rq_info['PAGES_ID'];
      $ITEM=str_replace(";", ":", html_entity_decode(stripslashes($tab_rq_info['ITEM'])));
      $URLP=str_replace(";", ":", html_entity_decode(stripslashes($tab_rq_info['URLP'])));
      $LEGEND=str_replace(";", ":", html_entity_decode(stripslashes($tab_rq_info['LEGEND'])));
      echo $ITEM.';'.$URLP.';'.$LEGEND.';';
        $rq_info_menu="
	SELECT `moteur_menu`.`NOM_MENU` 
	FROM `moteur_sous_menu`,`moteur_menu` 
	WHERE `moteur_sous_menu`.`MENU_ID`= `moteur_menu`.`MENU_ID` AND 
	`moteur_sous_menu`.`PAGES_ID`='".$ID."'";
	$res_rq_info_menu = mysql_query($rq_info_menu, $mysql_link) or die(mysql_error());
	$tab_rq_info_menu = mysql_fetch_assoc($res_rq_info_menu);
	$total_ligne_rq_info_menu=mysql_num_rows($res_rq_info_menu);
	if($total_ligne_rq_info_menu==0){
		echo ';';
	}else{
		echo str_replace(";", ":", html_entity_decode(stripslashes($tab_rq_info_menu['NOM_MENU']))).';';
	}
	mysql_free_result($res_rq_info_menu);
        do {
        	$ROLE_ID=$tab_rq_info_role['ROLE_ID'];
        	$rq_info_role_user="
        	SELECT * 
        	FROM `moteur_droit` 
        	WHERE `ROLE_ID`='".$ROLE_ID."' 
        	AND `PAGES_ID`='".$ID."'
        	AND `DROIT`='OK'";
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