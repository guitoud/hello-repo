<?PHP
//redirection si acces dirrect
if(substr_count($_SERVER['PHP_SELF'], 'index.php')==0){
  header("Location: ../"); 
  exit(); 
}
/*******************************************************************
   Interface Gestion des Utilisateurs
   Version 1.0.0  
  25/02/2010 - VGU - Creation fichier
**************************************************Guibert Vincent***
*******************************************************************/

require("./cf/conf_outil_icdc.php"); 
require_once("./cf/fonctions.php");
$rq_info="
SELECT COUNT(`ROLE_ID`) AS `NB` FROM `moteur_role`
";
$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
$tab_rq_info = mysql_fetch_assoc($res_rq_info);
$total_ligne_rq_info=mysql_num_rows($res_rq_info); 
$colspan=4+$tab_rq_info['NB'];
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
      <td align="center" colspan="'.$colspan.'">&nbsp;[&nbsp;<a href="./index.php?ITEM=Admin_Gestion_Utilisateurs">Retour</a>&nbsp;]&nbsp;</td>
    </tr>
    <tr align="center" class="titre">
      <td align="center"><b>&nbsp;Login&nbsp;</b></td>
      <td align="center"><b>&nbsp;Nom&nbsp;</b></td>
      <td align="center"><b>&nbsp;Pr&eacute;nom&nbsp;</b></td>
      <td align="center"><b>&nbsp;Acc&egrave;s&nbsp;</b></td>';
      if($colspan!=3){
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
      SELECT `UTILISATEUR_ID`, `LOGIN`, `NOM`, `PRENOM`,`ACCES`,`TYPE_LOGIN`
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
      $ACCES=$tab_rq_info['ACCES'];
      $TYPE_LOGIN=$tab_rq_info['TYPE_LOGIN'];
      if($ACCES=='L'){
        $ACCES='L';
        $class_acces="orange";
      }else{
        $ACCES='L / E';
        $class_acces="vert";
      }
      	$rq_info_id="
        SELECT 
        `moteur_trace`.`MOTEUR_TRACE_UTILISATEUR_ID`, 
        `moteur_trace`.`MOTEUR_TRACE_DATE`, 
        `moteur_trace`.`MOTEUR_TRACE_REF_ID`, 
        `moteur_trace`.`MOTEUR_TRACE_ACTION`, 
        `moteur_trace`.`MOTEUR_TRACE_ETAT`,
        `moteur_utilisateur`.`LOGIN`, 
        `moteur_utilisateur`.`NOM`, 
        `moteur_utilisateur`.`PRENOM` 
        FROM `moteur_trace`,`moteur_utilisateur`
        WHERE 
        `moteur_trace`.`MOTEUR_TRACE_CATEGORIE`='Login'
        AND `moteur_trace`.`MOTEUR_TRACE_UTILISATEUR_ID`=`moteur_utilisateur`.`UTILISATEUR_ID` 
        AND `moteur_trace`.`MOTEUR_TRACE_REF_ID`='".$ID."'
        ORDER BY `moteur_trace`.`MOTEUR_TRACE_DATE_TRI` DESC
        LIMIT 20";
        $res_rq_info_id = mysql_query($rq_info_id, $mysql_link) or die(mysql_error());
        $tab_rq_info_id = mysql_fetch_assoc($res_rq_info_id);
        $total_ligne_rq_info_id=mysql_num_rows($res_rq_info_id);
        $AFF_SPAN='';
        if($total_ligne_rq_info_id!=0){
          do {
          
          $MOTEUR_TRACE_DATE=$tab_rq_info_id['MOTEUR_TRACE_DATE'];
          $MOTEUR_TRACE_ACTION=$tab_rq_info_id['MOTEUR_TRACE_ACTION'];
          $NOM=$tab_rq_info_id['NOM'];
          $PRENOM=$tab_rq_info_id['PRENOM'];
          $AFF_SPAN.=$MOTEUR_TRACE_DATE.' - '.$PRENOM.' '.$NOM.' - '.$MOTEUR_TRACE_ACTION.'</BR>';
           } while ($tab_rq_info_id = mysql_fetch_assoc($res_rq_info_id));
                $ligne= mysql_num_rows($res_rq_info_id);
                if($ligne > 0) {
                  mysql_data_seek($res_rq_info_id, 0);
                  $tab_rq_info_id = mysql_fetch_assoc($res_rq_info_id);
                }
        }else{
          $AFF_SPAN='Pas d\'acces';
        }
        mysql_free_result($res_rq_info_id);
        
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="left"><a class="LinkDef" href=./index.php?ITEM=Admin_Modif_Utilisateur&action=Modif&ID='.$ID.'>&nbsp;'.stripslashes($LOGIN).'&nbsp;</a></td>
        <td align="left"><a href="#" class="infobullegauche">&nbsp;<font color="#000000">'.stripslashes($NOM).'</font>&nbsp;<span>'.$AFF_SPAN.'</span></a></td>
        <td align="left">&nbsp;'.stripslashes($PRENOM).'&nbsp;</td>
        <td align="center" class="'.$class_acces.'">&nbsp;'.stripslashes($TYPE_LOGIN).' - '.stripslashes($ACCES).'&nbsp;</td>';
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