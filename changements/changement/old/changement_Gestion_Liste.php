<?PHP
//redirection si acces dirrect
if(substr_count($_SERVER['PHP_SELF'], 'index.php')==0){
  header("Location: ../");
  exit();
}
/*******************************************************************
   Interface Gestion des changements
   Version 1.0.0  
  01/10/2010 - VGU - Creation fichier
**************************************************Guibert Vincent***
*******************************************************************/

require("./cf/conf_outil_icdc.php"); 

if(isset($_GET['date'])){
  $date=$_GET['date'];
}else{
  $date=date("Ym", mktime(0, 0, 0, date("m"), date("d"), date("Y")));  
}
if(isset($_GET['histo'])){
  $histo=$_GET['histo'];
  if(isset($_GET['annee'])){
    $annee=$_GET['annee'];
  }else{
    $annee=date("Y");  
  }
  if(isset($_GET['mois'])){
    $mois=$_GET['mois'];
  }else{
    $mois=date("m");  
  }
  $date=$annee.''.$mois;
    $rq_liste_date_info="
    SELECT DISTINCT (`CHANGEMENT_DATE`) AS `CHANGEMENT_DATE`
    FROM `changement_date` 
    WHERE 
    `CHANGEMENT_DATE` LIKE '".$date."%' 
    AND `ENABLE`='0'
    ORDER BY `CHANGEMENT_DATE` ASC";
}else{
  $date=date("Ymd", mktime(0, 0, 0, date("m"), date("d"), date("Y")));
  $jour=substr($date,6,2);
  $mois=substr($date,4,2);
  $annee=substr($date,0,4);
  $date_rapport=date("Ymd", mktime(0, 0, 0, date("m"), date("d")-1, date("Y")));

  $rq_liste_date_info="
  SELECT DISTINCT (`CHANGEMENT_DATE`) AS `CHANGEMENT_DATE`
  FROM `changement_date` 
  WHERE 
  `CHANGEMENT_DATE`>='".$date_rapport."' 
  AND `ENABLE`='0' 
  ORDER BY `CHANGEMENT_DATE` ASC";

}
//echo $rq_liste_date_info.'</BR>';
$res_rq_liste_date_info = mysql_query($rq_liste_date_info, $mysql_link) or die(mysql_error());
$tab_rq_liste_date_info = mysql_fetch_assoc($res_rq_liste_date_info);
$total_ligne_rq_liste_date_info=mysql_num_rows($res_rq_liste_date_info); 

// Début page HTML
echo '
<div align="center">
';

$numLigne=0;
$nbinter=0;

echo'
<table class="table_inc">
  <tr align="center" class="titre">
    <td align="center" colspan="10"><h2>&nbsp;[&nbsp;<a href="./index.php?ITEM=changement_Calendrier&amp;m='.$mois.'&amp;y='.$annee.'">Calendrier</a>&nbsp;]&nbsp;-[&nbsp;<a href="#Bas_de_page">Fin</a>&nbsp;]&nbsp;</h2></td>
  </tr>';

    $numLigne=0;
    $nbstop=0;
    if($total_ligne_rq_liste_date_info!=0){
    do {
      $nbinter=0;
      $nbstop=0;

      $date= $tab_rq_liste_date_info['CHANGEMENT_DATE'];
      $jour=substr($date,6,2);
      $mois=substr($date,4,2);
      $annee=substr($date,0,4);
      
      $NomJour=date('D',mktime(12, 0, 0, $mois, $jour, $annee));
      switch ($NomJour)
      {
        case "Mon": $NomJour = "Lundi"; break;
        case "Tue": $NomJour = "Mardi"; break;
        case "Wed": $NomJour = "Mercredi"; break;
        case "Thu": $NomJour = "Jeudi"; break;
        case "Fri": $NomJour = "Vendredi"; break;
        case "Sat": $NomJour = "Samedi"; break;
        case "Sun": $NomJour = "Dimanche"; break;
      }

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
      `changement_liste`.`CHANGEMENT_LISTE_DATE_MODIFICATION` , 
      `changement_liste`.`CHANGEMENT_LISTE_LIB`,
      `changement_demande`.`CHANGEMENT_DEMANDE_LIB`,
      `changement_status`.`CHANGEMENT_STATUS` ,
      `changement_status`.`CHANGEMENT_STATUS_COULEUR_FOND` ,
      `changement_status`.`CHANGEMENT_STATUS_COULEUR_TEXT`
      FROM `changement_liste` , `moteur_utilisateur`, `changement_date`,`changement_status`,`changement_demande`
      WHERE 
      `changement_liste`.`CHANGEMENT_STATUS_ID`=`changement_status`.`CHANGEMENT_STATUS_ID` AND
      `changement_liste`.`CHANGEMENT_DEMANDE_ID`=`changement_demande`.`CHANGEMENT_DEMANDE_ID` AND
      `changement_liste`.`CHANGEMENT_LISTE_ID`=`changement_date`.`CHANGEMENT_ID` AND 
      `changement_date`.`CHANGEMENT_DATE`='".$date."' AND 
      `moteur_utilisateur`.`UTILISATEUR_ID` = `changement_liste`.`CHANGEMENT_LISTE_UTILISATEUR_ID` AND 
      `changement_liste`.`ENABLE` = '0' AND
      `changement_date`.`ENABLE` = '0'
      ORDER BY `changement_liste`.`CHANGEMENT_LISTE_ID` ASC 
      ";
      
      $res_rq_changement_info = mysql_query($rq_changement_info, $mysql_link) or die(mysql_error());
      $tab_rq_changement_info = mysql_fetch_assoc($res_rq_changement_info);
      $total_ligne_rq_changement_info=mysql_num_rows($res_rq_changement_info);
//      echo $rq_changement_info.' - '.$total_ligne_rq_changement_info.'<BR/><BR/>';
      if($total_ligne_rq_changement_info!=0){
        do {

        $ID=$tab_rq_changement_info['CHANGEMENT_LISTE_ID'];  
        $LOGIN=$tab_rq_changement_info['LOGIN'];
	$NOM=$tab_rq_changement_info['NOM'];
	$PRENOM=$tab_rq_changement_info['PRENOM'];
	$STATUS=$tab_rq_changement_info['CHANGEMENT_STATUS'];
	$CHANGEMENT_STATUS_COULEUR_FOND=$tab_rq_changement_info['CHANGEMENT_STATUS_COULEUR_FOND'];
	$CHANGEMENT_STATUS_COULEUR_TEXT=$tab_rq_changement_info['CHANGEMENT_STATUS_COULEUR_TEXT'];
	$CHANGEMENT_DEMANDE_LIB=$tab_rq_changement_info['CHANGEMENT_DEMANDE_LIB'];

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
	$DATE_MODIFICATION=$tab_rq_changement_info['CHANGEMENT_LISTE_DATE_MODIFICATION'];
	$LIB=$tab_rq_changement_info['CHANGEMENT_LISTE_LIB'];
	
  $nbre_car = strlen($LIB);
  if ($nbre_car < 25){
    $LIB = $LIB;
  }else{
    $LIB = substr($LIB,0,25);
    $LIB = $LIB.'...';
  }
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
            echo'
              <tr align="center" class="titre">
                <td align="center" colspan="10"><h2>&nbsp;Changement du '.$NomJour.' '.$jour.'/'.$mois.'/'.$annee.'&nbsp;</h2></td>
              </tr>';
            echo '
              <tr align="center" class="titre">
                <td align="center">&nbsp;Identifiant&nbsp;</td>
                <td align="center">&nbsp;Demandeur&nbsp;</td>
                <td align="center">&nbsp;Status&nbsp;</td>
                <td align="center">&nbsp;Type de demande&nbsp;</td>
                <td align="center">&nbsp;Date&nbsp;<br/>&nbsp;D&eacute;but&nbsp;</td>
                <td align="center">&nbsp;Heure&nbsp;<br/>&nbsp;D&eacute;but&nbsp;</td>
                <td align="center">&nbsp;Date&nbsp;<br/>&nbsp;Fin&nbsp;</td>
                <td align="center">&nbsp;Heure&nbsp;<br/>&nbsp;Fin&nbsp;</td>
                <td align="center">&nbsp;Libell&eacute;&nbsp;</td>
                <td align="center">&nbsp;FAR&nbsp;</td>
              </tr>
              ';
              $nbstop=1;
            }
            $numLigne = $numLigne + 1;
            $nbinter++;
        if ($numLigne%2) { $class = "pair";}else{$class = "impair";} 
        if($STATUS=='Abandonn&eacute;'||$STATUS=='Clotur&eacute;'){
          $ITEM_url='changement_Info_Changement';
          $Action_url='Info';
        }else{
          $ITEM_url='changement_Modif_Changement';
          $Action_url='Modif';
        }
        if($STATUS=='Abandonn&eacute;'){
        
        echo'
              <tr align="center" class="'.$class.'">
                <td align="center"><strike><a name="'.$ID.'"></a>&nbsp;'.$ID.'&nbsp;</strike></td>
                <td align="center"><strike>&nbsp;'.stripslashes($PRENOM).' '.stripslashes($NOM).'&nbsp;</strike></td>
                <td align="center" bgcolor="#'.$CHANGEMENT_STATUS_COULEUR_FOND.'"><FONT COLOR="#'.$CHANGEMENT_STATUS_COULEUR_TEXT.'"><strike>&nbsp;'.stripslashes($STATUS).'&nbsp;</strike></FONT></td>
                <td align="center"><strike>&nbsp;'.stripslashes($CHANGEMENT_DEMANDE_LIB).'&nbsp;</strike></td>
                <td align="center"><strike>&nbsp;'.$DATE_DEBUT.'&nbsp;</strike></td>
                <td align="center"><strike>&nbsp;'.$HEURE_DEBUT.'&nbsp;</strike></td>
                <td align="center"><strike>&nbsp;'.$DATE_FIN.'&nbsp;</strike></td>
                <td align="center"><strike>&nbsp;'.$HEURE_FIN.'&nbsp;</strike></td>
                <td align="center"><strike>&nbsp;'.stripslashes($LIB).'&nbsp;</strike></td>
                <td align="center"><strike>&nbsp;'.stripslashes($FAR).'&nbsp;</strike></td>
              </tr>';
        }else{
              echo'
              <tr align="center" class="'.$class.'">
                <td align="center"><a name="'.$ID.'"></a><a class="LinkDef" href="./index.php?ITEM='.$ITEM_url.'&action='.$Action_url.'&ID='.$ID.'">&nbsp;'.$ID.'&nbsp;</a></td>
                <td align="center">&nbsp;'.stripslashes($PRENOM).' '.stripslashes($NOM).'&nbsp;</td>
                <td align="center" bgcolor="#'.$CHANGEMENT_STATUS_COULEUR_FOND.'"><FONT COLOR="#'.$CHANGEMENT_STATUS_COULEUR_TEXT.'">&nbsp;'.stripslashes($STATUS).'&nbsp;</FONT></td>
                <td align="center">&nbsp;'.stripslashes($CHANGEMENT_DEMANDE_LIB).'&nbsp;</td>
                <td align="center">&nbsp;'.$DATE_DEBUT.'&nbsp;</td>
                <td align="center">&nbsp;'.$HEURE_DEBUT.'&nbsp;</td>
                <td align="center">&nbsp;'.$DATE_FIN.'&nbsp;</td>
                <td align="center">&nbsp;'.$HEURE_FIN.'&nbsp;</td>
                <td align="center">&nbsp;'.stripslashes($LIB).'&nbsp;</td>
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
        

      } while ($tab_rq_liste_date_info = mysql_fetch_assoc($res_rq_liste_date_info));
      $ligne= mysql_num_rows($res_rq_liste_date_info);
      if($ligne > 0) {
        mysql_data_seek($res_rq_liste_date_info, 0);
        $tab_rq_liste_date_info = mysql_fetch_assoc($res_rq_liste_date_info);
      }
      }else{
        $numLigne = $numLigne + 1;
        if ($numLigne%2) { $class = "pair";}else {$class = "impair";}
        echo'<tr align="center" class="'.$class.'">
            <td align="center" colspan="10">&nbsp;Pas d\'information&nbsp;</td>
          </tr>';
      }

		echo '
		<tr align="center" class="titre">
 		  <td align="center" colspan="10"><h2>&nbsp;[&nbsp;<a href="#Haut_de_page">D&eacute;but</a>&nbsp;]&nbsp;</h2></td>
    </tr>
		</table>';

		echo'
    </div>';
mysql_free_result($res_rq_liste_date_info);
mysql_close($mysql_link); 
?>
