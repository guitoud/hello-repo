<?PHP
//redirection si acces dirrect
if(substr_count($_SERVER['PHP_SELF'], 'index.php')==0){
  header("Location: ../"); 
  exit(); 
}
/*******************************************************************
   Interface Gestion des Utilisateurs
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
      <td align="center" colspan="8"><b>&nbsp;[&nbsp;<a href=./index.php?ITEM=Admin_Ajout_Utilisateur>Ajout d\'un Utilisateur</a>&nbsp;]&nbsp;-&nbsp;[&nbsp;<a href=./index.php?ITEM=Admin_Gestion_Utilisateurs_info>Liste des utilisateurs avec role</a>&nbsp;]&nbsp;</b></td>
    </tr>
    <tr align="center" class="titre">
      <td align="center"><b>&nbsp;Login&nbsp;</b></td>
      <td align="center"><b>&nbsp;Nom&nbsp;</b></td>
      <td align="center"><b>&nbsp;Pr&eacute;nom&nbsp;</b></td>
      <td align="center"><b>&nbsp;Email&nbsp;</b></td>
      <td align="center"><b>&nbsp;Soci&eacute;t&eacute;e&nbsp;</b></td>
      <td align="center"><b>&nbsp;R&ocirc;le&nbsp;</b></td>
      <td align="center"><b>&nbsp;Acc&egrave;s&nbsp;</b></td>
      <td align="center"><b>&nbsp;Actif&nbsp;</b></td>
    </tr>';

      $rq_info="
      SELECT `UTILISATEUR_ID`, `LOGIN`, `NOM`, `PRENOM`, `EMAIL_FULL`,`SOCIETE`,`ACCES`,`TYPE_LOGIN`, `ENABLE` 
      FROM `moteur_utilisateur`
      WHERE `ENABLE` IN ('Y','N')
      ORDER BY `NOM`, `PRENOM`
       ";
      $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
      $tab_rq_info = mysql_fetch_assoc($res_rq_info);
      $total_ligne_rq_info=mysql_num_rows($res_rq_info); 
    do {
      $ID=$tab_rq_info['UTILISATEUR_ID'];
      $LOGIN=$tab_rq_info['LOGIN'];
      $NOM=$tab_rq_info['NOM'];
      $PRENOM=$tab_rq_info['PRENOM'];
      $EMAIL=$tab_rq_info['EMAIL_FULL'];
      $SOCIETE=$tab_rq_info['SOCIETE'];
      $ACCES=$tab_rq_info['ACCES'];
      $TYPE_LOGIN=$tab_rq_info['TYPE_LOGIN'];
      $ENABLE=$tab_rq_info['ENABLE'];
      if($ENABLE=='Y'){
        $ENABLE='Oui';
         $class_ENABLE="vert";
      }else{
        $ENABLE='Non';
        $class_ENABLE="orange";
      }
      if($ACCES=='L'){
        $ACCES='Lecture';
        $class_acces="orange";
      }else{
        $ACCES='Lecture / Ecriture';
        $class_acces="vert";
      }
      
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="left"><a class="LinkDef" href=./index.php?ITEM=Admin_Modif_Utilisateur&action=Modif&ID='.$ID.'>&nbsp;'.stripslashes($LOGIN).'&nbsp;</a></td>
        <td align="left">&nbsp;'.stripslashes($NOM).'&nbsp;</td>
        <td align="left">&nbsp;'.stripslashes($PRENOM).'&nbsp;</td>
        <td align="left">&nbsp;'.stripslashes($EMAIL).'&nbsp;</td>
        <td align="left">&nbsp;'.stripslashes($SOCIETE).'&nbsp;</td>
        <td align="center">';
        $rq_info_role="
      SELECT `moteur_role`.`ROLE`
      FROM `moteur_role`,`moteur_role_utilisateur`
      WHERE
      `moteur_role`.`ROLE_ID`=`moteur_role_utilisateur`.`ROLE_ID` AND
      `moteur_role_utilisateur`.`UTILISATEUR_ID`='".$ID."' AND
      `moteur_role_utilisateur`.`ROLE_UTILISATEUR_ACCES`='0'
      ORDER BY `moteur_role`.`ROLE`
       ";
      $res_rq_info_role = mysql_query($rq_info_role, $mysql_link) or die(mysql_error());
      $tab_rq_info_role = mysql_fetch_assoc($res_rq_info_role);
      $total_ligne_rq_info_role=mysql_num_rows($res_rq_info_role); 
      if($total_ligne_rq_info_role==0){
      		echo '&nbsp;';
      }else{
	    do {
	    	$ROLE=$tab_rq_info_role['ROLE'];
	    	echo '&nbsp;'.$ROLE.'&nbsp;<BR/>';
	    	} while ($tab_rq_info_role = mysql_fetch_assoc($res_rq_info_role));
	    $ligne= mysql_num_rows($res_rq_info_role);
	    if($ligne > 0) {
	      mysql_data_seek($res_rq_info_role, 0);
	      $tab_rq_info_role = mysql_fetch_assoc($res_rq_info_role);
	    }
      }
	        echo '</td>
	        <td align="center" class="'.$class_acces.'">&nbsp;'.stripslashes($TYPE_LOGIN).' - '.stripslashes($ACCES).'&nbsp;</td>
	        <td align="center" class="'.$class_ENABLE.'">&nbsp;'.stripslashes($ENABLE).'&nbsp;</td>
	      </tr>';
	        
	
    } while ($tab_rq_info = mysql_fetch_assoc($res_rq_info));
    $ligne= mysql_num_rows($res_rq_info);
    if($ligne > 0) {
      mysql_data_seek($res_rq_info, 0);
      $tab_rq_info = mysql_fetch_assoc($res_rq_info);
    }

    
    echo '
    <tr align="center" class="titre">
      <td align="center" colspan="8">&nbsp;</td>
    </tr>
  </table>
</div>
';
mysql_close(); 
?>