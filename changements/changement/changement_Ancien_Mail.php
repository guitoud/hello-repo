<?php
//redirection si acces dirrect
if(substr_count($_SERVER['PHP_SELF'], 'index.php')==0){
  header("Location: ../"); 
  exit();
}
$ENV =substr($_SERVER["SCRIPT_FILENAME"], 1, 1);
require_once("./cf/fonctions.php"); 
$j=0;
if(isset($_GET['begin'])){
  $begin=$_GET['begin'];
}else{
  $begin=0;
}

echo '
<a name="hautdepage">
<div align="center">
<table class="table_inc" cellspacing="2" cellpading="0">
	<tr align="center" class="titre">
		<td align="center" colspan="7" >
		<center>
		<b>Cliquer sur le document souhaite</b>
		</center>
		</td>
	</tr>
	<tr align="center" class="titre">
		<td align="center"><b>Date</b></td>
		<td align="center"><b>Utilisateur</b></td>
		<td align="center"><b>n&deg; du Changement</b></td>
		<td align="center"><b>Status</b></td>
		<td align="center"><b>Titre du Changement</b></td>
		<td align="center"><b>Destinataire</b></td>
		<td align="center"><b>Fichier</b></td>
	</tr>';
			$rq_info="
		SELECT COUNT(`CHANGEMENT_MAIL_TRACE_ID`) AS `NB` 
		FROM `changement_mail_trace`
		";

	$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
	$tab_rq_info = mysql_fetch_assoc($res_rq_info);
	$total_ligne_rq_info=mysql_num_rows($res_rq_info); 
	$NB_ALL=$tab_rq_info['NB'];
	mysql_free_result($res_rq_info);
		$rq_info="
		SELECT 
		`changement_mail_trace`.`CHANGEMENT_MAIL_TRACE_ID`, 
		`moteur_utilisateur`.`NOM`,	
		`moteur_utilisateur`.`PRENOM`,
		`changement_mail_trace`.`CHANGEMENT_MAIL_TRACE_DATE`, 
		`changement_status`.`CHANGEMENT_STATUS`, 
		`changement_mail_trace`.`CHANGEMENT_MAIL_TRACE_CHANGEMENT_ID`, 
		`changement_liste`.`CHANGEMENT_LISTE_LIB`,
		`changement_mail_trace`.`CHANGEMENT_MAIL_TRACE_DEST`, 
		`changement_mail_trace`.`CHANGEMENT_MAIL_TRACE_ARCHIVE`
		FROM `changement_mail_trace`,`moteur_utilisateur`,`changement_liste`,`changement_status`
		WHERE `moteur_utilisateur`.`UTILISATEUR_ID`=`changement_mail_trace`.`CHANGEMENT_MAIL_TRACE_UTILISATEUR_ID`
		AND `changement_liste`.`CHANGEMENT_LISTE_ID`=`changement_mail_trace`.`CHANGEMENT_MAIL_TRACE_CHANGEMENT_ID`
		AND `changement_mail_trace`.`CHANGEMENT_MAIL_TRACE_TYPE`=`changement_status`.`CHANGEMENT_STATUS_ID`
		ORDER BY `changement_mail_trace`.`CHANGEMENT_MAIL_TRACE_DATE` DESC
		LIMIT ".$begin.",".$Var_max_resultat_page.";";
	$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
	$tab_rq_info = mysql_fetch_assoc($res_rq_info);
	$total_ligne_rq_info=mysql_num_rows($res_rq_info); 
	if ($total_ligne_rq_info==0){
		$j++;
		if ($j%2) { $class = "pair";}else{$class = "impair";} 
		echo '
		<tr align="center" class='.$class.'>
		  <td align="left" colspan="7">&nbsp;Aucune archive disponible&nbsp;</a></td>
		</tr>';
	}else{
      
        do {
          $NB_MAIL=0;
          $MAIL_LIB='';
          $ID=$tab_rq_info['CHANGEMENT_MAIL_TRACE_CHANGEMENT_ID'];
          $MAIL = explode(";", $tab_rq_info['CHANGEMENT_MAIL_TRACE_DEST']);
          $NB_TOTAL_MAIL=count($MAIL);
            for($NB_MAIL=0;$NB_MAIL < $NB_TOTAL_MAIL;$NB_MAIL++)
            {
              $MAIL_LIB.=$MAIL[$NB_MAIL].'</BR>';
            }
            $Lignefch='./old_changement/'.$tab_rq_info['CHANGEMENT_MAIL_TRACE_ARCHIVE'];
          $j++;
          if ($j%2) { $class = "pair";}else{$class = "impair";} 
          echo '
          <tr class="'.$class.'">
            <td align="center">&nbsp;'.$tab_rq_info['CHANGEMENT_MAIL_TRACE_DATE'].'&nbsp;</td>
            <td align="center">&nbsp;'.$tab_rq_info['PRENOM'].' '.$tab_rq_info['NOM'].'&nbsp;</td>
            <td align="center">&nbsp;<a class="LinkDef" href="./index.php?ITEM=changement_Info_Changement&action=Info&ID='.$ID.'">'.$ID.'</a>&nbsp;</td>
            <td align="center">&nbsp;'.$tab_rq_info['CHANGEMENT_STATUS'].'&nbsp;</td>
            <td align="center">&nbsp;'.stripslashes($tab_rq_info['CHANGEMENT_LISTE_LIB']).'&nbsp;</td>
            <td align="center">'.$MAIL_LIB.'</td>
            <td align="center">&nbsp;';
            if (is_file("$Lignefch")){
              echo '<a class="LinkDef" href="'.$Lignefch.'" target="_blank">'.$tab_rq_info['CHANGEMENT_MAIL_TRACE_ARCHIVE'].'</a>';
            }else{
              echo 'pas de fichier '.$tab_rq_info['CHANGEMENT_MAIL_TRACE_ARCHIVE'];
            }
            echo '&nbsp;</td>
          </tr>';

        } while ($tab_rq_info = mysql_fetch_assoc($res_rq_info));
        $ligne= mysql_num_rows($res_rq_info);
        if($ligne > 0) {
          mysql_data_seek($res_rq_info, 0);
          $tab_rq_info = mysql_fetch_assoc($res_rq_info);
        }
      }
      mysql_free_result($res_rq_info);
      echo '
	<tr align="center" class="titre">
		<td align="center" colspan="7" >&nbsp;</td>
	</tr>';
	if($NB_ALL > $Var_max_resultat_page){
	echo '
	<tr align="center" class="titre">
          <td align="center" colspan="7">&nbsp;';
          makeListLink($NB_ALL,$Var_max_resultat_page,"./index.php?ITEM=changement_Ancien_Mail",1);
          echo '&nbsp;</td>
        </tr>
        <tr align="center" class="titre">
          <td align="center" colspan="7">&nbsp;</td>
        </tr>';
        }
        echo '
      </table>
</div>';

mysql_close($mysql_link);
?>