<?PHP
//redirection si acces dirrect
if(substr_count($_SERVER['PHP_SELF'], 'index.php')==0){
  header("Location: ../"); 
  exit(); 
}
/*******************************************************************
   Interface Gestion des pages
   Version 1.0.0    
  25/02/2010 - VGU - Creation fichier
**************************************************Guibert Vincent***
*******************************************************************/

require("./cf/conf_outil_icdc.php"); 
require("./cf/fonctions.php");
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
echo '
 <div align="center">
	<br/>
  <table class="table_inc" cellspacing="1" cellpading="0">
    <tr align="center" class="titre">
      <td align="center" colspan="'.$colspan.'">&nbsp;[&nbsp;<a href="./index.php?ITEM=Admin_Gestion_Pages">Retour</a>&nbsp;]&nbsp;</td>
    </tr>
    <tr align="center" class="titre">
      <td align="center"><b>&nbsp;ITEM&nbsp;</b></BR>&nbsp;fichier&nbsp;</b></BR>&nbsp;L&eacute;gende&nbsp;</b></td>
      <td align="center"><b>&nbsp;Menu&nbsp;</b></td>';
      if($colspan!=2){
      	do {
		$ROLE=str_replace("-", "&nbsp;</BR>&nbsp;",$tab_rq_info_role['ROLE']);
		echo '<td align="center"><b>&nbsp;'.$ROLE.'&nbsp;</b></td>';
      		
	} while ($tab_rq_info_role = mysql_fetch_assoc($res_rq_info_role));
	$ligne= mysql_num_rows($res_rq_info_role);
	if($ligne > 0) {
		mysql_data_seek($res_rq_info_role, 0);
		$tab_rq_info_role = mysql_fetch_assoc($res_rq_info_role);
	}
      }
      echo '
    </tr>';

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
      $ITEM=$tab_rq_info['ITEM'];
      $URLP=$tab_rq_info['URLP'];
      $LEGEND=$tab_rq_info['LEGEND'];
      //
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="left"><a class="LinkDef" href=./index.php?ITEM=Admin_Modif_Gestion_Pages&action=Modif&ID='.$ID.'>&nbsp;'.stripslashes($ITEM).'&nbsp;</a></BR>&nbsp;'.stripslashes($URLP).'&nbsp;</BR>&nbsp;'.stripslashes($LEGEND).'&nbsp;</td>';
        $rq_info_menu="
	SELECT `moteur_menu`.`NOM_MENU` 
	FROM `moteur_sous_menu`,`moteur_menu` 
	WHERE `moteur_sous_menu`.`MENU_ID`= `moteur_menu`.`MENU_ID` AND 
	`moteur_sous_menu`.`PAGES_ID`='".$ID."'";
	$res_rq_info_menu = mysql_query($rq_info_menu, $mysql_link) or die(mysql_error());
	$tab_rq_info_menu = mysql_fetch_assoc($res_rq_info_menu);
	$total_ligne_rq_info_menu=mysql_num_rows($res_rq_info_menu);
	if($total_ligne_rq_info_menu==0){
		echo '<td align="center">&nbsp;&nbsp;</td>';
	}else{
		echo '<td align="center">&nbsp;'.stripslashes($tab_rq_info_menu['NOM_MENU']).'&nbsp;</td>';
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
			echo '<td align="center">&nbsp;&nbsp;</td>';
		}else{
			echo '<td align="center">&nbsp;OK&nbsp;</td>';
		}
		mysql_free_result($res_rq_info_role_user);
      		
	} while ($tab_rq_info_role = mysql_fetch_assoc($res_rq_info_role));
	$ligne= mysql_num_rows($res_rq_info_role);
	if($ligne > 0) {
		mysql_data_seek($res_rq_info_role, 0);
		$tab_rq_info_role = mysql_fetch_assoc($res_rq_info_role);
	}
	echo '
	</tr>';
    } while ($tab_rq_info = mysql_fetch_assoc($res_rq_info));
    $ligne= mysql_num_rows($res_rq_info);
    if($ligne > 0) {
      mysql_data_seek($res_rq_info, 0);
      $tab_rq_info = mysql_fetch_assoc($res_rq_info);
    }
    echo '
    <tr align="center" class="titre">
      <td align="center" colspan="'.$colspan.'">&nbsp;</td>
    </tr>
  </table>
</div>
';
mysql_free_result($res_rq_info_role);
mysql_close(); 
?>