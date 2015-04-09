<?PHP
echo ' 
<!--D&eacute;but page HTML --> 
<div align="center"> 
  <table width="100%" height="20" border="0" cellpadding="0" cellspacing="0" class="in_Contenu">
    <tr>
      <td>	
	      <div class="titre_in_Contenu">
			      	Bienvenue sur le Portail Moteur
	      </div>
      <td>	
    </tr>
    <tr>
      <td>	
	      <div class="texte_gauche_in_Contenu">
	      	Ce portail est en cours d\'&eacute;laboration.<br>
	      	De nouvelles fonctionnalit&eacute;s apparaitront au fur et &agrave; mesure de leurs cr&eacute;ations et de leurs mises en forme.<br><br>
      	  <b>Liste des outils actuellement disponibles</b>
';
require("./cf/conf_outil_icdc.php"); 
$j=0;

$filename = './Admin/historique.html';

if (file_exists($filename)) {
    $DATE_VERSION=date('d/m/Y');  
    $inF = fopen($filename,"r");
    while (!feof($inF)) {
    $ligne=fgets($inF, 4096);
    if(substr_count($ligne, 'Derniere Mise a jour du portail : le')==1){
    	$DATE_VERSION=str_replace('<br>', '',str_replace('Derniere Mise a jour du portail : le ', '', $ligne));  
    	break;
    }
    }
    fclose($inF);
} else {
    $DATE_VERSION='??';  
}
if($_SESSION['VALID']=='OK'){
echo '
<table class="table_inc" width="100%" >
<tr>
<td>';
  $rq_info_menu="
  SELECT `moteur_menu`.`MENU_ID`, `moteur_menu`.`NOM_MENU`, `moteur_menu`.`MENU_INFO`
  FROM `moteur_menu`,`moteur_sous_menu` 
  WHERE `moteur_sous_menu`.`MENU_ID`=`moteur_menu`.`MENU_ID` AND 
  UPPER(`moteur_menu`.`NOM_MENU`) IN ('ADMINISTRATION') AND
  `PAGES_ID` IN (
  SELECT `moteur_pages`.`PAGES_ID`
  FROM `moteur_droit`, `moteur_pages`
  WHERE
  `moteur_pages`.`PAGES_ID`=`moteur_droit`.`PAGES_ID` AND
  `moteur_droit`.`ROLE_ID` IN(
  SELECT `moteur_role_utilisateur`.`ROLE_ID` 
  FROM `moteur_role_utilisateur`,`moteur_utilisateur` 
  WHERE `moteur_utilisateur`.`UTILISATEUR_ID`=`moteur_role_utilisateur`.`UTILISATEUR_ID` AND
  `moteur_utilisateur`.`LOGIN`='".$LOGIN."' AND 
  `moteur_role_utilisateur`.`ROLE_UTILISATEUR_ACCES`='0'
  ) AND
  `moteur_droit`.`DROIT`='OK' AND
  `moteur_pages`.`ENABLE`=0
  ORDER BY `moteur_pages`.`PAGES_ID`
  ) 
  GROUP BY `moteur_menu`.`MENU_ID` ORDER BY `moteur_menu`.`ORDRE`
   ";
  $res_rq_info_menu = mysql_query($rq_info_menu, $mysql_link) or die(mysql_error());
  $tab_rq_info_menu = mysql_fetch_assoc($res_rq_info_menu);
  $total_ligne_rq_info_menu=mysql_num_rows($res_rq_info_menu); 
  if($total_ligne_rq_info_menu!=0){
  do {
    $MENU_ID=$tab_rq_info_menu['MENU_ID'];
    $NOM_MENU=$tab_rq_info_menu['NOM_MENU'];
    $MENU_INFO=$tab_rq_info_menu['MENU_INFO'];
    
        $rq_info_sous_menu="
          SELECT 
          `moteur_pages`.`PAGES_ID`, `moteur_pages`.`ITEM` , `moteur_pages`.`LEGEND`  , `moteur_pages`.`LEGEND_MENU`,`moteur_pages`.`PAGES_INFO`
          FROM `moteur_pages`,`moteur_sous_menu`
          WHERE 
          `moteur_sous_menu`.`PAGES_ID`=`moteur_pages`.`PAGES_ID` AND
          `moteur_sous_menu`.`MENU_ID`='".$MENU_ID."' AND
          `moteur_pages`.`ITEM` NOT LIKE 'vide_%' AND
          `moteur_sous_menu`.`PAGES_ID` IN (
            SELECT `moteur_pages`.`PAGES_ID`
            FROM `moteur_droit`, `moteur_pages`
            WHERE 
            `moteur_pages`.`PAGES_ID`=`moteur_droit`.`PAGES_ID` AND
            `moteur_droit`.`ROLE_ID` IN(
	    SELECT `moteur_role_utilisateur`.`ROLE_ID` 
	    FROM `moteur_role_utilisateur`,`moteur_utilisateur` 
	    WHERE `moteur_utilisateur`.`UTILISATEUR_ID`=`moteur_role_utilisateur`.`UTILISATEUR_ID` AND
	    `moteur_utilisateur`.`LOGIN`='".$LOGIN."' AND 
	    `moteur_role_utilisateur`.`ROLE_UTILISATEUR_ACCES`='0'
	    ) AND
            `moteur_droit`.`DROIT`='OK' AND
            `moteur_pages`.`ENABLE`=0
            ORDER BY `moteur_pages`.`PAGES_ID`
          ) AND
          `moteur_pages`.`ENABLE`=0
          GROUP BY `moteur_sous_menu`.`SOUS_MENU_ID` 
          ORDER BY `moteur_sous_menu`.`ORDRE`
           ";
          $res_rq_info_sous_menu = mysql_query($rq_info_sous_menu, $mysql_link) or die(mysql_error());
          $tab_rq_info_sous_menu = mysql_fetch_assoc($res_rq_info_sous_menu);
          $total_ligne_rq_info_sous_menu=mysql_num_rows($res_rq_info_sous_menu); 
          if($total_ligne_rq_info_sous_menu!=0){        
            echo '
      <ul type="circle">
        <li>'.stripslashes($MENU_INFO).'</li>
        <ul type="square">
          ';
          do {
            $sous_menu_PAGES_ID=$tab_rq_info_sous_menu['PAGES_ID'];
            $sous_menu_ITEM=$tab_rq_info_sous_menu['ITEM'];
            $sous_menu_LEGEND=$tab_rq_info_sous_menu['LEGEND_MENU'];
            $PAGES_INFO=$tab_rq_info_sous_menu['PAGES_INFO'];
            echo '<li><a class="LinkDef" href="./index.php?ITEM='.$sous_menu_ITEM.'">'.stripslashes($PAGES_INFO).'</a></li>';
            
          } while ($tab_rq_info_sous_menu = mysql_fetch_assoc($res_rq_info_sous_menu));
          $ligne= mysql_num_rows($res_rq_info_sous_menu);
          if($ligne > 0) {
            mysql_data_seek($res_rq_info_sous_menu, 0);
            $tab_rq_info_sous_menu = mysql_fetch_assoc($res_rq_info_sous_menu);
          }
        echo '   	  		
      </ul>
    </ul>
  ';
    }
  } while ($tab_rq_info_menu = mysql_fetch_assoc($res_rq_info_menu));
  $ligne= mysql_num_rows($res_rq_info_menu);
  if($ligne > 0) {
    mysql_data_seek($res_rq_info_menu, 0);
    $tab_rq_info_menu = mysql_fetch_assoc($res_rq_info_menu);
  }
  mysql_free_result($res_rq_info_sous_menu);
  }
mysql_free_result($res_rq_info_menu);

echo '
</td>
<td>';
  $rq_info_menu="
  SELECT `moteur_menu`.`MENU_ID`, `moteur_menu`.`NOM_MENU`, `moteur_menu`.`MENU_INFO`
  FROM `moteur_menu`,`moteur_sous_menu` 
  WHERE `moteur_sous_menu`.`MENU_ID`=`moteur_menu`.`MENU_ID` AND 
  UPPER(`moteur_menu`.`NOM_MENU`) NOT IN ('ADMINISTRATION') AND
  `PAGES_ID` IN (
  SELECT `moteur_pages`.`PAGES_ID`
  FROM `moteur_droit`, `moteur_pages`
  WHERE
  `moteur_pages`.`PAGES_ID`=`moteur_droit`.`PAGES_ID` AND
  `moteur_droit`.`ROLE_ID` IN(
  SELECT `moteur_role_utilisateur`.`ROLE_ID` 
  FROM `moteur_role_utilisateur`,`moteur_utilisateur` 
  WHERE `moteur_utilisateur`.`UTILISATEUR_ID`=`moteur_role_utilisateur`.`UTILISATEUR_ID` AND
  `moteur_utilisateur`.`LOGIN`='".$LOGIN."' AND 
  `moteur_role_utilisateur`.`ROLE_UTILISATEUR_ACCES`='0'
  ) AND
  `moteur_droit`.`DROIT`='OK' AND
  `moteur_pages`.`ENABLE`=0
  ORDER BY `moteur_pages`.`PAGES_ID`
  ) 
  GROUP BY `moteur_menu`.`MENU_ID` ORDER BY `moteur_menu`.`ORDRE`
   ";
  $res_rq_info_menu = mysql_query($rq_info_menu, $mysql_link) or die(mysql_error());
  $tab_rq_info_menu = mysql_fetch_assoc($res_rq_info_menu);
  $total_ligne_rq_info_menu=mysql_num_rows($res_rq_info_menu); 
  if($total_ligne_rq_info_menu!=0){
  do {
    $MENU_ID=$tab_rq_info_menu['MENU_ID'];
    $NOM_MENU=$tab_rq_info_menu['NOM_MENU'];
    $MENU_INFO=$tab_rq_info_menu['MENU_INFO'];

        $rq_info_sous_menu="
          SELECT 
          `moteur_pages`.`PAGES_ID`, `moteur_pages`.`ITEM` , `moteur_pages`.`LEGEND`  , `moteur_pages`.`LEGEND_MENU`,`moteur_pages`.`PAGES_INFO`
          FROM `moteur_pages`,`moteur_sous_menu`
          WHERE 
          `moteur_sous_menu`.`PAGES_ID`=`moteur_pages`.`PAGES_ID` AND
          `moteur_sous_menu`.`MENU_ID`='".$MENU_ID."' AND
          `moteur_pages`.`ITEM` NOT LIKE 'vide_%' AND
          `moteur_sous_menu`.`PAGES_ID` IN (
            SELECT `moteur_pages`.`PAGES_ID`
            FROM `moteur_droit`, `moteur_pages`
            WHERE 
            `moteur_pages`.`PAGES_ID`=`moteur_droit`.`PAGES_ID` AND
            `moteur_droit`.`ROLE_ID` IN(
	    SELECT `moteur_role_utilisateur`.`ROLE_ID` 
	    FROM `moteur_role_utilisateur`,`moteur_utilisateur` 
	    WHERE `moteur_utilisateur`.`UTILISATEUR_ID`=`moteur_role_utilisateur`.`UTILISATEUR_ID` AND
	    `moteur_utilisateur`.`LOGIN`='".$LOGIN."' AND 
	    `moteur_role_utilisateur`.`ROLE_UTILISATEUR_ACCES`='0'
	    ) AND
            `moteur_droit`.`DROIT`='OK' AND
            `moteur_pages`.`ENABLE`=0
            ORDER BY `moteur_pages`.`PAGES_ID`
          ) AND
          `moteur_pages`.`ENABLE`=0
          GROUP BY `moteur_sous_menu`.`SOUS_MENU_ID` 
          ORDER BY `moteur_sous_menu`.`ORDRE`
           ";
          $res_rq_info_sous_menu = mysql_query($rq_info_sous_menu, $mysql_link) or die(mysql_error());
          $tab_rq_info_sous_menu = mysql_fetch_assoc($res_rq_info_sous_menu);
          $total_ligne_rq_info_sous_menu=mysql_num_rows($res_rq_info_sous_menu); 
          if($total_ligne_rq_info_sous_menu!=0){
                echo '
    <ul type="circle">
      <li>'.stripslashes($MENU_INFO).'</li>
      <ul type="square">
        ';
         
          do {
            $sous_menu_PAGES_ID=$tab_rq_info_sous_menu['PAGES_ID'];
            $sous_menu_ITEM=$tab_rq_info_sous_menu['ITEM'];
            $sous_menu_LEGEND=$tab_rq_info_sous_menu['LEGEND_MENU'];
            $PAGES_INFO=$tab_rq_info_sous_menu['PAGES_INFO'];
            echo '<li><a class="LinkDef" href="./index.php?ITEM='.$sous_menu_ITEM.'">'.stripslashes($PAGES_INFO).'</a></li>';
            
          } while ($tab_rq_info_sous_menu = mysql_fetch_assoc($res_rq_info_sous_menu));
          $ligne= mysql_num_rows($res_rq_info_sous_menu);
          if($ligne > 0) {
            mysql_data_seek($res_rq_info_sous_menu, 0);
            $tab_rq_info_sous_menu = mysql_fetch_assoc($res_rq_info_sous_menu);
          }
        echo '   	  		
      </ul>
    </ul>
  ';
  }
  } while ($tab_rq_info_menu = mysql_fetch_assoc($res_rq_info_menu));
  $ligne= mysql_num_rows($res_rq_info_menu);
  if($ligne > 0) {
    mysql_data_seek($res_rq_info_menu, 0);
    $tab_rq_info_menu = mysql_fetch_assoc($res_rq_info_menu);
  }
  mysql_free_result($res_rq_info_sous_menu);
  }
mysql_free_result($res_rq_info_menu);

echo '
</td>
</tr>
</table>';
}
mysql_close(); 
echo '
					<br/>
	      	<div align="center">
	      		Pour toute remarque, information ou demande d\'&eacute;volution concernant ce portail, merci d\'envoyer un mail en cliquant sur l\'enveloppe<br/><br/>
	      		<a class="LinkDef" href="mailto:Vincent.Guibert-e@caissedesdepots.fr?subject=[Portail]&body=Bonjour,"><img src="img/enveloppe.gif" border="0" height="30"></a>
	        </div>
	        <div align="center">
	      		La version du portail est celle du : '.$DATE_VERSION.'
	      		';
	      		/*
	      		if(substr_count($_SERVER["SCRIPT_FILENAME"], '/php/iab_va/')==1){
				echo '<BR/>';
				$filename = './extract/portail_ope_copie_prod.sql.gz';
				
				if (file_exists($filename)) {
					echo "La base date du : " . date ("d/m/Y H:i:s.", filemtime($filename));
				}else{
					echo "Pas d'information sur la date de récupération des données";
				}
			}*/
			echo '
	        </div>
				</div>
	    </td>
    </tr>
  </table>
</div>';
?>