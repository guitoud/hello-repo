<?PHP
//redirection si acces dirrect
if(substr_count($_SERVER['PHP_SELF'], 'index.php')==0){
  header("Location: ../"); 
  exit(); 
}
/*******************************************************************
   Interface Gestion des technologie
   Version 1.0.0  
  24/03/2010 - VGU - Creation fichier
**************************************************Guibert Vincent***
*******************************************************************/

require("./cf/conf_outil_icdc.php"); 
require_once("./cf/fonctions.php");
$j=0;
$ID='';
echo '
 <div align="center">
  <table class="table_inc" cellspacing="1" cellpading="0">
    <tr align="center" class="titre">
      <td align="center" colspan="1"><b>&nbsp;[&nbsp;<a href=./index.php?ITEM=changement_Ajout_Technologie>Ajout d\'une technologie</a>&nbsp;]&nbsp;</b></td>
    </tr>
    <tr align="center" class="titre">
      <td align="center"><b>&nbsp;Technologie&nbsp;</b></td>
    </tr>';

      $rq_info="
      SELECT *
      FROM `changement_technologie`
      WHERE `ENABLE`=0
      ORDER BY `CHANGEMENT_TECHNOLOGIE`
       ";
      $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
      $tab_rq_info = mysql_fetch_assoc($res_rq_info);
      $total_ligne_rq_info=mysql_num_rows($res_rq_info); 
      if ($total_ligne_rq_info==0){
        $j++;
        if ($j%2) { $class = "pair";}else{$class = "impair";} 
        echo '
        <tr align="center" class='.$class.'>
          <td align="left">&nbsp;Pas de technologie dans la base.&nbsp;</td>
        </tr>';
      }else{
      
      do {
        $ID=$tab_rq_info['CHANGEMENT_TECHNOLOGIE_ID'];
        $CHANGEMENT_TECHNOLOGIE=$tab_rq_info['CHANGEMENT_TECHNOLOGIE'];
        $j++;
        if ($j%2) { $class = "pair";}else{$class = "impair";} 
        echo '
        <tr align="center" class='.$class.'>
          <td align="left"><a class="LinkDef" href=./index.php?ITEM=changement_Modification_Technologie&action=Modif&ID='.$ID.'>&nbsp;'.stripslashes($CHANGEMENT_TECHNOLOGIE).'&nbsp;</a></td>
        </tr>';

      } while ($tab_rq_info = mysql_fetch_assoc($res_rq_info));
      $ligne= mysql_num_rows($res_rq_info);
      if($ligne > 0) {
        mysql_data_seek($res_rq_info, 0);
        $tab_rq_info = mysql_fetch_assoc($res_rq_info);
      }
    }
    echo '
    <tr align="center" class="titre">
      <td align="center" colspan="1">&nbsp;</td>
    </tr>
  </table>
</div>
';
mysql_close($mysql_link); 
?>