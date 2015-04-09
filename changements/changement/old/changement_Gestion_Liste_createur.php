<?PHP
//redirection si acces dirrect
if(substr_count($_SERVER['PHP_SELF'], 'index.php')==0){
  header("Location: ../");
  exit();
}
/*******************************************************************
   Interface Gestion des changements
   Version 1.0.0  
  05/10/2010 - VGU - Creation fichier
**************************************************Guibert Vincent***
*******************************************************************/

require("./cf/conf_outil_icdc.php"); 
// gestion des droits d'acces
if(isset($_SESSION['LOGIN'])){
	$LOGIN=$_SESSION['LOGIN'];
	$rq_info="
	SELECT `moteur_role_utilisateur`.`ROLE_UTILISATEUR_ACCES` 
	FROM `moteur_role_utilisateur`,`moteur_utilisateur`,`moteur_role`
	WHERE 
	`moteur_role_utilisateur`.`UTILISATEUR_ID`=`moteur_utilisateur`.`UTILISATEUR_ID` AND
	`moteur_role_utilisateur`.`ROLE_ID`=`moteur_role`.`ROLE_ID` AND
	`moteur_utilisateur`.`LOGIN`='".$LOGIN."' AND
	(`moteur_role`.`ROLE`='ROOT' OR `moteur_role`.`ROLE`='ADMIN-CHANGEMENT') AND
	`moteur_role_utilisateur`.`ROLE_UTILISATEUR_ACCES`=0
	LIMIT 1
	";
	$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
	$tab_rq_info = mysql_fetch_assoc($res_rq_info);
	$total_ligne_rq_info=mysql_num_rows($res_rq_info); 
	if($total_ligne_rq_info==0){
		$ROLE=1;
	}else{
		$ROLE=$tab_rq_info['ROLE_UTILISATEUR_ACCES'];
	}
	mysql_free_result($res_rq_info);
	$rq_Selectionner_user ="
	SELECT `UTILISATEUR_ID`,`LOGIN`, `NOM`, `PRENOM` 
	FROM `moteur_utilisateur` 
	WHERE `LOGIN` = '".$LOGIN."'
	";
	$res_rq_Selectionner_user = mysql_query($rq_Selectionner_user, $mysql_link) or die(mysql_error());
	$tab_rq_Selectionner_user = mysql_fetch_assoc($res_rq_Selectionner_user);
	$total_ligne_Selectionner_user = mysql_num_rows($res_rq_Selectionner_user);
	if($total_ligne_Selectionner_user==0){
		$NOM='';
		$UTILISATEUR_ID=0;
		$PRENOM='';
		$LOGIN='';
	}else{
		$NOM=$tab_rq_Selectionner_user['NOM'];
		$UTILISATEUR_ID=$tab_rq_Selectionner_user['UTILISATEUR_ID'];
		$PRENOM=$tab_rq_Selectionner_user['PRENOM'];
		$LOGIN=$tab_rq_Selectionner_user['LOGIN'];
	}
	mysql_free_result($res_rq_Selectionner_user);
}else{
	$NOM='';
	$UTILISATEUR_ID=0;
	$PRENOM='';
	$LOGIN='';
	$ROLE=1;
}
if($ROLE==1){
	$ROLE_SQL="`changement_liste`.`CHANGEMENT_LISTE_UTILISATEUR_ID`='".$UTILISATEUR_ID."' AND ";
}else{
	$ROLE_SQL="";
}

$date=date("Ymd");  

// Début page HTML
echo '
<div align="center">
';

$numLigne=0;
$nbinter=0;

echo'
<table class="table_inc">
  <tr align="center" class="titre">
    <td align="center" colspan="10"><h2>&nbsp;[&nbsp;<a href="#Bas_de_page">Fin</a>&nbsp;]&nbsp;</h2></td>
  </tr>';

    $numLigne=0;
    $nbstop=0;
    $nbinter=0;
    $nbstop=0;

      $rq_changement_info="
      SELECT 
      `changement_liste`.`CHANGEMENT_LISTE_ID`, 
      `moteur_utilisateur`.`UTILISATEUR_ID` , 
      `moteur_utilisateur`.`LOGIN` , 
      `moteur_utilisateur`.`NOM` , 
      `moteur_utilisateur`.`PRENOM` , 
      `changement_liste`.`CHANGEMENT_LISTE_ID` , 
      `changement_liste`.`CHANGEMENT_LISTE_UTILISATEUR_ID` , 
      `changement_liste`.`CHANGEMENT_LISTE_DATE_DEBUT` , 
      `changement_liste`.`CHANGEMENT_LISTE_DATE_FIN` , 
      `changement_liste`.`CHANGEMENT_LISTE_HEURE_DEBUT` , 
      `changement_liste`.`CHANGEMENT_LISTE_HEURE_FIN` , 
      `changement_liste`.`CHANGEMENT_LISTE_DATE_CREATION` , 
      `changement_liste`.`CHANGEMENT_LISTE_INFORMATION` , 
      `changement_liste`.`CHANGEMENT_LISTE_LIB` ,
      `changement_status`.`CHANGEMENT_STATUS`
      FROM `changement_liste` , `moteur_utilisateur`,`changement_status`
      WHERE 
      ".$ROLE_SQL."
      `changement_liste`.`CHANGEMENT_STATUS_ID`=`changement_status`.`CHANGEMENT_STATUS_ID` AND
      `moteur_utilisateur`.`UTILISATEUR_ID` = `changement_liste`.`CHANGEMENT_LISTE_UTILISATEUR_ID` AND 
      `changement_liste`.`ENABLE` = '0'
      ORDER BY `changement_liste`.`CHANGEMENT_LISTE_DATE_DEBUT` DESC 
      ";
      //echo $rq_changement_info;
      $res_rq_changement_info = mysql_query($rq_changement_info, $mysql_link) or die(mysql_error());
      $tab_rq_changement_info = mysql_fetch_assoc($res_rq_changement_info);
      $total_ligne_rq_changement_info=mysql_num_rows($res_rq_changement_info);
      if($total_ligne_rq_changement_info!=0){
        do {

        $ID=$tab_rq_changement_info['CHANGEMENT_LISTE_ID'];  
        $LOGIN=$tab_rq_changement_info['LOGIN'];
	$NOM=$tab_rq_changement_info['NOM'];
	$PRENOM=$tab_rq_changement_info['PRENOM'];
	$STATUS=$tab_rq_changement_info['CHANGEMENT_STATUS'];
	$DATE_DEBUT=$tab_rq_changement_info['CHANGEMENT_LISTE_DATE_DEBUT'];
	$DATE_DEBUT_jour=substr($DATE_DEBUT,6,2);
	$DATE_DEBUT_mois=substr($DATE_DEBUT,4,2);
	$DATE_DEBUT_annee=substr($DATE_DEBUT,0,4); 
	$DATE_DEBUT=$DATE_DEBUT_jour.'/'.$DATE_DEBUT_mois.'/'.$DATE_DEBUT_annee;
	$DATE_FIN=$tab_rq_changement_info['CHANGEMENT_LISTE_DATE_FIN'];
	$DATE_FIN_jour=substr($DATE_FIN,6,2);
	$DATE_FIN_mois=substr($DATE_FIN,4,2);
	$DATE_FIN_annee=substr($DATE_FIN,0,4); 
	$DATE_FIN=$DATE_FIN_jour.'/'.$DATE_FIN_mois.'/'.$DATE_FIN_annee;
	$HEURE_DEBUT=$tab_rq_changement_info['CHANGEMENT_LISTE_HEURE_DEBUT'];
	$HEURE_FIN=$tab_rq_changement_info['CHANGEMENT_LISTE_HEURE_FIN'];
	$DATE_CREATION=$tab_rq_changement_info['CHANGEMENT_LISTE_DATE_CREATION'];
	$INFORMATION=$tab_rq_changement_info['CHANGEMENT_LISTE_INFORMATION'];
	$LIB=$tab_rq_changement_info['CHANGEMENT_LISTE_LIB'];

	$rq_far_info="
	SELECT `CHANGEMENT_FAR_ID`
	FROM `changement_far`
	WHERE `CHANGEMENT_LISTE_ID` ='".$ID."' 
	AND `ENABLE` = '0'
	";
	$res_rq_far_info = mysql_query($rq_far_info, $mysql_link) or die(mysql_error());
	$tab_rq_far_info = mysql_fetch_assoc($res_rq_far_info);
	$total_ligne_rq_far_info=mysql_num_rows($res_rq_far_info);
	$FAR='Non';
	$FAR_action='Ajout';
	$FAR_ITEM='changement_Ajout_FAR';
	if($total_ligne_rq_far_info!=0){
		$FAR='Oui';
		$FAR_action='Modif';
		$FAR_ITEM='changement_Modif_FAR';
	}
	mysql_free_result($res_rq_far_info);

            if($nbstop==0){
            echo '
              <tr align="center" class="titre">
                <td align="center">&nbsp;Identifiant&nbsp;</td>
                <td align="center">&nbsp;Demandeur&nbsp;</td>
                <td align="center">&nbsp;Status&nbsp;</td>
                <td align="center">&nbsp;Date&nbsp;<br/>&nbsp;D&eacute;but&nbsp;</td>
                <td align="center">&nbsp;Heure&nbsp;<br/>&nbsp;D&eacute;but&nbsp;</td>
                <td align="center">&nbsp;Date&nbsp;<br/>&nbsp;Fin&nbsp;</td>
                <td align="center">&nbsp;Heure&nbsp;<br/>&nbsp;Fin&nbsp;</td>
                <td align="center">&nbsp;Libell&eacute;&nbsp;</td>
                <td align="center">&nbsp;Information&nbsp;</td>
                <td align="center">&nbsp;FAR&nbsp;</td>
              </tr>
              ';
              $nbstop=1;
            }
            $numLigne = $numLigne + 1;
            $nbinter++;
        if ($numLigne%2) { $class = "pair";}else{$class = "impair";} 

        if($STATUS=='Annul&eacute;e'){
        
        echo'
              <tr align="center" class="'.$class.'">
                <td align="center"><strike><a name="'.$ID.'"></a>&nbsp;'.$ID.'&nbsp;</strike></td>
                <td align="center"><strike>&nbsp;'.stripslashes($PRENOM).' '.stripslashes($NOM).'&nbsp;</strike></td>
                <td align="center"><strike>&nbsp;'.stripslashes($STATUS).'&nbsp;</strike></td>
                <td align="center"><strike>&nbsp;'.$DATE_DEBUT.'&nbsp;</strike></td>
                <td align="center"><strike>&nbsp;'.$HEURE_DEBUT.'&nbsp;</strike></td>
                <td align="center"><strike>&nbsp;'.$DATE_FIN.'&nbsp;</strike></td>
                <td align="center"><strike>&nbsp;'.$HEURE_FIN.'&nbsp;</strike></td>
                <td align="center"><strike>&nbsp;'.stripslashes($LIB).'&nbsp;</strike></td>
                <td align="center"><strike>&nbsp;'.stripslashes($INFORMATION).'&nbsp;</strike></td>
                <td align="center"><strike>&nbsp;'.stripslashes($FAR).'&nbsp;</strike></td>
              </tr>';
        }else{
              echo'
              <tr align="center" class="'.$class.'">
                <td align="center"><a name="'.$ID.'"></a>&nbsp;'.$ID.'&nbsp;</td>
                <td align="center">&nbsp;'.stripslashes($PRENOM).' '.stripslashes($NOM).'&nbsp;</td>
                <td align="center">&nbsp;';
                if($STATUS=='Cr&eacute;ation'){
                	echo '<font color=#993333><b>'.stripslashes($STATUS).'</b></font>';
                }else{
                	echo stripslashes($STATUS);
        	}	
                echo '&nbsp;</td>
                <td align="center">&nbsp;'.$DATE_DEBUT.'&nbsp;</td>
                <td align="center">&nbsp;'.$HEURE_DEBUT.'&nbsp;</td>
                <td align="center">&nbsp;'.$DATE_FIN.'&nbsp;</td>
                <td align="center">&nbsp;'.$HEURE_FIN.'&nbsp;</td>
                <td align="center">&nbsp;'.stripslashes($LIB).'&nbsp;</td>
                <td align="center">&nbsp;'.stripslashes($INFORMATION).'&nbsp;</td>
                <td align="center"><a class="LinkDef" href="./index.php?ITEM='.$FAR_ITEM.'&action='.$FAR_action.'&ID='.$ID.'">&nbsp;'.stripslashes($FAR).'&nbsp;</a></td>
              </tr>';
          }
        } while ($tab_rq_changement_info = mysql_fetch_assoc($res_rq_changement_info));
        $ligne= mysql_num_rows($res_rq_changement_info);
        if($ligne > 0) {
          mysql_data_seek($res_rq_changement_info, 0);
          $tab_rq_changement_info = mysql_fetch_assoc($res_rq_changement_info);
        }
        mysql_free_result($res_rq_changement_info);
        
        }else{
          $numLigne = $numLigne + 1;
          if ($numLigne%2) { $class = "pair";}else {$class = "impair";}
           echo'<tr align="center" class="'.$class.'">
            <td align="center" colspan="10">&nbsp;Pas d\'information&nbsp;</td>
          </tr>';
        }
        if($nbinter!=0){
          if($nbinter<=1){
            $nbinter_info="changement";
          }else{
            $nbinter_info="changements";
          }
          echo '  
          <tr align="center" class="titre">
            <td align="center" colspan="10">Il y a '.$nbinter.' '.$nbinter_info.'.</td>
          </tr> 
          <tr align="center" >
            <td align="center" colspan="10">&nbsp;</td>
          </tr>';
        }else{
         echo '  
          <tr align="center" class="titre">
            <td align="center" colspan="10">Il n\'y a pas d\'intervention.</td>
          </tr> 
          <tr align="center" >
            <td align="center" colspan="10">&nbsp;</td>
          </tr>';
        }
		echo '
		<tr align="center" class="titre">
 		  <td align="center" colspan="10"><h2>&nbsp;[&nbsp;<a href="#Haut_de_page">D&eacute;but</a>&nbsp;]&nbsp;</h2></td>
    </tr>
		</table>';

		echo'
		<br/><br/>
    </div>';

mysql_close($mysql_link); 
?>
