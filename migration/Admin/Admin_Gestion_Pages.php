<?PHP
//redirection si acces dirrect
if(substr_count($_SERVER['PHP_SELF'], 'index.php')==0){
  header("Location: ../"); 
  exit(); 
}
/*******************************************************************
   Interface Gestion des pages
   Version 1.0.0  
  24/12/2009 - VGU - Creation fichier
**************************************************Guibert Vincent***
*******************************************************************/

require("./cf/conf_outil_icdc.php"); 
require_once("./cf/fonctions.php");
$j=0;
if(isset($_GET['begin'])){
  $begin=$_GET['begin'];
}else{
  $begin=0;
}
$rq_info="
SELECT COUNT(`PAGES_ID`) AS `NB`
FROM `moteur_pages`
";
$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
$tab_rq_info = mysql_fetch_assoc($res_rq_info);
$total_ligne_rq_info=mysql_num_rows($res_rq_info); 
$NB_ALL=$tab_rq_info['NB'];
mysql_free_result($res_rq_info);
 echo '
 <div align="center">
	<br/>
  <table class="table_inc" cellspacing="0" cellpading="0">
    <tr align="center" class="titre">
      <td align="center" colspan="6"><b>&nbsp;[&nbsp;<a href=./index.php?ITEM=Admin_Ajout_Pages>Ajout d\'une Page</a>&nbsp;]&nbsp;-&nbsp;[&nbsp;<a href=./index.php?ITEM=Admin_Gestion_Pages_info>Liste des pages avec menu et role</a>&nbsp;]&nbsp;</b></td>
    </tr>
    <tr align="center" class="titre">
      <td align="center" colspan="6">&nbsp;';
      makeListLink($NB_ALL,$Var_max_resultat_page_limit,"./index.php?ITEM=Admin_Gestion_Pages",1);
      echo '&nbsp;</td>
    </tr>
    <tr align="center" class="titre">
      <td align="center"><b>&nbsp;ITEM&nbsp;</b></td>
      <td align="center"><b>&nbsp;L\'information sur la page&nbsp;</b><BR/><b>&nbsp;L&eacute;gende&nbsp;</b></td>
      <td align="center"><b>&nbsp;L&eacute;gende du menu&nbsp;</b></td>
      <td align="center"><b>&nbsp;Role&nbsp;</b></td>
      <td align="center"><b>&nbsp;Droit&nbsp;</b></td>
      <td align="center"><b>&nbsp;Actif&nbsp;</b></td>
    </tr>';

    
      $rq_info="
      SELECT * 
      FROM `moteur_pages` 
      ORDER BY `URLP`, `ITEM`
      LIMIT ".$begin.",".$Var_max_resultat_page_limit.";";
      $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
      $tab_rq_info = mysql_fetch_assoc($res_rq_info);
      $total_ligne_rq_info=mysql_num_rows($res_rq_info); 
      do {
      $ID=$tab_rq_info['PAGES_ID'];
      $ITEM=$tab_rq_info['ITEM'];
      $LEGEND=$tab_rq_info['LEGEND'];
      $LEGEND_MENU=$tab_rq_info['LEGEND_MENU'];
      $URLP=$tab_rq_info['URLP'];
      $PAGES_INFO=$tab_rq_info['PAGES_INFO'];
      $ENABLE=$tab_rq_info['ENABLE'];
      if($ITEM==''){
          $ITEM='default';
        }
      if($ENABLE==0){
      	$ENABLE='OUI';
      }else{
      	$ENABLE='NON';
      }
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="left"><a name="'.$ID.'"><b><a class="LinkDef" href=./index.php?ITEM=Admin_Modif_Gestion_Pages&begin='.$begin.'&ID='.$ID.'>&nbsp;'.stripslashes($ITEM).'&nbsp;</a></b></td>
        <td align="left"><b>&nbsp;'.stripslashes($PAGES_INFO).'&nbsp;</b><BR/><b>&nbsp;'.stripslashes($LEGEND).'&nbsp;</b></td>
        <td align="left">&nbsp;'.stripslashes($LEGEND_MENU).'&nbsp;</td>
        <td align="center" colspan="2">&nbsp;</td>
        <td align="center">&nbsp;'.$ENABLE.'&nbsp;</td>
      </tr>
      <tr align="center" class='.$class.'>
        <td align="center">&nbsp;</td>
        <td align="left">&nbsp;'.stripslashes($URLP).'&nbsp</td>
        <td align="center" colspan="4">&nbsp;</td>
      </tr>';
      $rq_info_role="
      SELECT `ROLE`,`ROLE_ID` 
      FROM  `moteur_role` 
      ORDER BY `ROLE`
       ";
      $res_rq_info_role = mysql_query($rq_info_role, $mysql_link) or die(mysql_error());
      $tab_rq_info_role = mysql_fetch_assoc($res_rq_info_role);
      $total_ligne_rq_info_role=mysql_num_rows($res_rq_info_role); 
      do {
      	$ROLE_ID=$tab_rq_info_role['ROLE_ID'];
	$rq_info_role_droit="
	SELECT `DROIT` 
	FROM  `moteur_droit` 
	WHERE `ROLE_ID`='".$ROLE_ID."' AND
	`PAGES_ID`='".$ID."'";
	$res_rq_info_role_droit = mysql_query($rq_info_role_droit, $mysql_link) or die(mysql_error());
	$tab_rq_info_role_droit = mysql_fetch_assoc($res_rq_info_role_droit);
	$total_ligne_rq_info_role_droit=mysql_num_rows($res_rq_info_role_droit); 
	if($total_ligne_rq_info_role_droit==0){
		$DROIT='KO';
	}else{
		$DROIT=$tab_rq_info_role_droit['DROIT'];
	}
        $ROLE=$tab_rq_info_role['ROLE'];
        if($DROIT=='OK'){
          $DROIT='<b>'.$DROIT.'</b>';
        }
        echo '
        <tr align="center" class='.$class.'>
          <td align="center" colspan="3">&nbsp;</td>
          <td align="left">&nbsp;'.stripslashes($ROLE).'&nbsp;</td>
          <td align="left">&nbsp;'.stripslashes($DROIT).'&nbsp;</td>
          <td align="center">&nbsp;</td>
        </tr>';

      } while ($tab_rq_info_role = mysql_fetch_assoc($res_rq_info_role));
      $ligne= mysql_num_rows($res_rq_info_role);
      if($ligne > 0) {
        mysql_data_seek($res_rq_info_role, 0);
        $tab_rq_info_role = mysql_fetch_assoc($res_rq_info_role);
      }

    } while ($tab_rq_info = mysql_fetch_assoc($res_rq_info));
    $ligne= mysql_num_rows($res_rq_info);
    if($ligne > 0) {
      mysql_data_seek($res_rq_info, 0);
      $tab_rq_info = mysql_fetch_assoc($res_rq_info);
    }
    echo '   
     <tr align="center" class="titre">
      <td align="center" colspan="6">&nbsp;</td>
    </tr>    
    <tr align="center" class="titre">
      <td align="center" colspan="6">&nbsp;';
      makeListLink($NB_ALL,$Var_max_resultat_page_limit,"./index.php?ITEM=Admin_Gestion_Pages",1);
      echo '&nbsp;</td>
    </tr>
    <tr align="center" class="titre">
      <td align="center" colspan="6">&nbsp;</td>
    </tr>
  </table>
</div>
';

mysql_close(); 

?>