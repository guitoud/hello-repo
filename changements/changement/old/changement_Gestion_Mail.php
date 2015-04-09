<?PHP
//redirection si acces dirrect
if(substr_count($_SERVER['PHP_SELF'], 'index.php')==0){
  header("Location: ../"); 
  exit(); 
}
/*******************************************************************
   Interface Gestion des mail
   Version 1.0.0   
  24/09/2010 - VGU - Creation fichier
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
      <td align="center" colspan="3"><b>&nbsp;[&nbsp;<a href=./index.php?ITEM=changement_Ajout_Mail>Ajout d\'un Mail</a>&nbsp;]&nbsp;</b></td>
    </tr>
    <tr align="center" class="titre">
      <td align="center"><b>&nbsp;Mail&nbsp;</b></td>
      <td align="center"><b>&nbsp;Status&nbsp;</b></td>
      <td align="center"><b>&nbsp;Type demande&nbsp;</b></td>
    </tr>';

      $rq_info="
     SELECT 
     `changement_mail`.`CHANGEMENT_MAIL_ID`, 
     `changement_mail`.`CHANGEMENT_MAIL_LIB`, 
     `changement_status`.`CHANGEMENT_STATUS`,
     `changement_demande`.`CHANGEMENT_DEMANDE_LIB`,
     `changement_mail`.`ENABLE`
     FROM `changement_mail` ,`changement_status`,`changement_demande`
     WHERE `changement_mail`.`ENABLE` ='0'
     AND `changement_mail`.`CHANGEMENT_STATUS_ID`=`changement_status`.`CHANGEMENT_STATUS_ID`
     AND `changement_mail`.`CHANGEMENT_DEMANDE_ID`=`changement_demande`.`CHANGEMENT_DEMANDE_ID`
     ORDER BY `changement_demande`.`CHANGEMENT_DEMANDE_LIB`,`changement_mail`.`CHANGEMENT_MAIL_LIB`,`changement_status`.`CHANGEMENT_STATUS`
       ";
      $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
      $tab_rq_info = mysql_fetch_assoc($res_rq_info);
      $total_ligne_rq_info=mysql_num_rows($res_rq_info); 
      if ($total_ligne_rq_info==0){
        $j++;
        if ($j%2) { $class = "pair";}else{$class = "impair";} 
        echo '
        <tr align="center" class='.$class.'>
          <td align="left" colspan="3">&nbsp;Pas de Mail dans la base.&nbsp;</td>
        </tr>';
      }else{
        $CHANGEMENT_DEMANDE_LIB_old='';
      do {
        $ID=$tab_rq_info['CHANGEMENT_MAIL_ID'];
        $CHANGEMENT_MAIL_LIB=$tab_rq_info['CHANGEMENT_MAIL_LIB'];
        $CHANGEMENT_STATUS=$tab_rq_info['CHANGEMENT_STATUS'];
        $CHANGEMENT_DEMANDE_LIB=$tab_rq_info['CHANGEMENT_DEMANDE_LIB'];
        $j++;
        if ($j%2) { $class = "pair";}else{$class = "impair";} 
        if($CHANGEMENT_DEMANDE_LIB_old!=''){
        if($CHANGEMENT_DEMANDE_LIB!=$CHANGEMENT_DEMANDE_LIB_old){
        echo '
        <tr align="center" class="titre">
          <td align="center" colspan="3">&nbsp;'.$CHANGEMENT_DEMANDE_LIB.'&nbsp;</td>
        </tr>';
        }
        }else{
        echo '
        <tr align="center" class="titre">
          <td align="center" colspan="3">&nbsp;'.$CHANGEMENT_DEMANDE_LIB.'&nbsp;</td>
        </tr>';
	}
        echo '
        <tr align="center" class='.$class.'>
          <td align="left"><a class="LinkDef" href=./index.php?ITEM=changement_Modif_Mail&action=Modif&ID='.$ID.'>&nbsp;'.stripslashes($CHANGEMENT_MAIL_LIB ).'&nbsp;</a></td>
          <td align="left">&nbsp;'.stripslashes($CHANGEMENT_STATUS).'&nbsp;</td>
          <td align="left">&nbsp;'.stripslashes($CHANGEMENT_DEMANDE_LIB).'&nbsp;</td>
        </tr>';
        $CHANGEMENT_DEMANDE_LIB_old=$CHANGEMENT_DEMANDE_LIB;

      } while ($tab_rq_info = mysql_fetch_assoc($res_rq_info));
      $ligne= mysql_num_rows($res_rq_info);
      if($ligne > 0) {
        mysql_data_seek($res_rq_info, 0);
        $tab_rq_info = mysql_fetch_assoc($res_rq_info);
      }
    }
    echo '
    <tr align="center" class="titre">
      <td align="center" colspan="3">&nbsp;</td>
    </tr>
  </table>
</div>
';