<?PHP
//redirection si acces dirrect
if(substr_count($_SERVER['PHP_SELF'], 'index.php')==0){
  header("Location: ../"); 
  exit(); 
}
/*******************************************************************
   Interface Gestion des Menus
   Version 1.0.0  
  24/12/2009 - VGU - Creation fichier
**************************************************Guibert Vincent***
*******************************************************************/
require("./cf/conf_outil_icdc.php"); 
require_once("./cf/fonctions.php");
$j=0;
echo '
 <div align="center">
	<br/>
  <table class="table_inc" cellspacing="0" cellpading="0">
    <tr align="center" class="titre">
      <td align="center" colspan="5"><b>&nbsp;[&nbsp;<a href=./index.php?ITEM=Admin_Ajout_Menu>Ajout d\'un Menu</a>&nbsp;]&nbsp;</b></td>
    </tr>
    <tr align="center" class="titre">
      <td align="center"><b>&nbsp;Ordre&nbsp;</b></td>
      <td align="center"><b>&nbsp;Menu&nbsp;</b></td>
      <td align="center"><b>&nbsp;Ordre&nbsp;</b></td>
      <td align="center"><b>&nbsp;Sous Menu&nbsp;</b></td>
      <td align="center"><b>&nbsp;ITEM&nbsp;</b></td>
    </tr>';
    $j++;
    if ($j%2) { $class = "pair";}else{$class = "impair";} 
    echo '
    <tr align="center" class='.$class.'>
      <td align="left">&nbsp;0&nbsp;</td>
      <td align="left"><a class="LinkDef" href=./index.php?ITEM=Admin_Gestion_Menus>&nbsp;Aucun menu&nbsp;</a></td>
      <td align="left" colspan="3">&nbsp;</td>
    </tr>';
    $rq_sous_menu="
    SELECT `LEGEND_MENU` , `ITEM`, `PAGES_ID`
    FROM `moteur_pages`
    WHERE `PAGES_ID` NOT
    IN (
      SELECT `moteur_pages`.`PAGES_ID`
      FROM `moteur_sous_menu` , `moteur_pages`
      WHERE `moteur_sous_menu`.`PAGES_ID` = `moteur_pages`.`PAGES_ID`
      AND `ENABLE`=0
    ) AND 
    `ENABLE`=0
    ";
    $res_rq_sous_menu = mysql_query($rq_sous_menu, $mysql_link) or die(mysql_error());
    $tab_rq_sous_menu = mysql_fetch_assoc($res_rq_sous_menu);
    $total_ligne_rq_sous_menu=mysql_num_rows($res_rq_sous_menu);
    if($total_ligne_rq_sous_menu!=0){
      $ORDRE=0;
      do {
        $ORDRE='';
        $ID=$tab_rq_sous_menu['PAGES_ID'];
        $LEGEND_MENU=$tab_rq_sous_menu['LEGEND_MENU'];
        $ITEM=$tab_rq_sous_menu['ITEM'];
        echo '
        <tr align="center" class='.$class.'>
          <td align="left" colspan="2">&nbsp;</td>
          <td align="left">&nbsp;'.stripslashes($ORDRE).'&nbsp;</td>
          <td align="left"><a class="LinkDef" href=./index.php?ITEM=Admin_Modif_Menu_Pages&ID='.$ID.'>'.stripslashes($LEGEND_MENU).'</a></td>
          <td align="left"><a class="LinkDef" href=./index.php?ITEM=Admin_Modif_Gestion_Pages&begin=0&ID='.$ID.'>'.stripslashes($ITEM).'</a></td>
        </tr>';

      } while ($tab_rq_sous_menu = mysql_fetch_assoc($res_rq_sous_menu));
      $ligne= mysql_num_rows($res_rq_sous_menu);
      if($ligne > 0) {
        mysql_data_seek($res_rq_sous_menu, 0);
        $tab_rq_sous_menu = mysql_fetch_assoc($res_rq_sous_menu);
      }
    }

    $rq_menu="
    SELECT `MENU_ID`, `NOM_MENU`,`MENU_INFO`, `ORDRE`,`ORDRE_DEFAULT` FROM `moteur_menu` ORDER BY `ORDRE`";
    $res_rq_menu = mysql_query($rq_menu, $mysql_link) or die(mysql_error());
    $tab_rq_menu = mysql_fetch_assoc($res_rq_menu);
    $total_ligne_rq_menu=mysql_num_rows($res_rq_menu);
    do {
      $MENU_ID=$tab_rq_menu['MENU_ID'];
      $NOM_MENU=$tab_rq_menu['NOM_MENU'];
      $ORDRE=$tab_rq_menu['ORDRE'];
      $ORDRE_DEFAULT=$tab_rq_menu['ORDRE_DEFAULT'];
      $MENU_INFO=$tab_rq_menu['MENU_INFO'];
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="left">&nbsp;'.stripslashes($ORDRE).'&nbsp;-&nbsp;'.stripslashes($ORDRE_DEFAULT).'&nbsp;</td>
        <td align="left"><b><a class="LinkDef" href=./index.php?ITEM=Admin_Modif_Menu&action=Modif&ID='.$MENU_ID.'>'.stripslashes($NOM_MENU).'</a></b></td>
        <td align="left" colspan="3">'.stripslashes($MENU_INFO).'</td>
      </tr>';
        $rq_sous_menu="
        SELECT `moteur_pages`.`PAGES_ID`, `moteur_pages`.`LEGEND_MENU`, `moteur_pages`.`ITEM`, `moteur_sous_menu`.`ORDRE` 
        FROM `moteur_sous_menu`, `moteur_pages` WHERE
        `moteur_sous_menu`.`PAGES_ID`=`moteur_pages`.`PAGES_ID` AND 
        `moteur_sous_menu`.`MENU_ID`='".$MENU_ID."' AND
        `moteur_pages`.`ENABLE`=0
        ORDER BY `moteur_sous_menu`.`ORDRE`";
        $res_rq_sous_menu = mysql_query($rq_sous_menu, $mysql_link) or die(mysql_error());
        $tab_rq_sous_menu = mysql_fetch_assoc($res_rq_sous_menu);
        $total_ligne_rq_sous_menu=mysql_num_rows($res_rq_sous_menu);
        if($total_ligne_rq_sous_menu!=0){
          do {
            $ID=$tab_rq_sous_menu['PAGES_ID'];
            $LEGEND_MENU=$tab_rq_sous_menu['LEGEND_MENU'];
            $ORDRE=$tab_rq_sous_menu['ORDRE'];
            $ITEM=$tab_rq_sous_menu['ITEM'];
            echo '
            <tr align="center" class='.$class.'>
              <td align="left" colspan="2">&nbsp;</td>
              <td align="left">&nbsp;'.stripslashes($ORDRE).'&nbsp;</td>
              <td align="left"><a class="LinkDef" href=./index.php?ITEM=Admin_Modif_Menu_Pages&ID='.$ID.'>'.stripslashes($LEGEND_MENU).'</a></td>
              <td align="left"><a class="LinkDef" href=./index.php?ITEM=Admin_Modif_Gestion_Pages&begin=0&ID='.$ID.'>'.stripslashes($ITEM).'</a></td>
            </tr>';

          } while ($tab_rq_sous_menu = mysql_fetch_assoc($res_rq_sous_menu));
          $ligne= mysql_num_rows($res_rq_sous_menu);
          if($ligne > 0) {
            mysql_data_seek($res_rq_sous_menu, 0);
            $tab_rq_sous_menu = mysql_fetch_assoc($res_rq_sous_menu);
          }
        }else{
          echo '
          <tr align="center" class='.$class.'>
            <td align="left" colspan="2">&nbsp;</td>
            <td align="left" colspan="3">Pas de page dans le Menu '.stripslashes($NOM_MENU).'</td>
          </tr>';
        }

    } while ($tab_rq_menu = mysql_fetch_assoc($res_rq_menu));
    $ligne= mysql_num_rows($res_rq_menu);
    if($ligne > 0) {
      mysql_data_seek($res_rq_menu, 0);
      $tab_rq_menu = mysql_fetch_assoc($res_rq_menu);
    }
    echo '
    <tr align="center" class="titre">
      <td align="center" colspan="5">&nbsp;</td>
    </tr>
  </table>
</div>
';
mysql_close($mysql_link); 
?>