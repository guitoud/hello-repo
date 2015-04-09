<?PHP
//redirection si acces dirrect
if(substr_count($_SERVER['PHP_SELF'], 'index.php')==0){
  header("Location: ../");
  exit();
}
/*******************************************************************
   Interface Gestion des changements 
   Version 1.0.0
  14/10/2010 - VGU - Creation fichier
**************************************************Guibert Vincent***
*******************************************************************/

require("./cf/conf_outil_icdc.php"); 
require_once("./cf/fonctions.php");
if(isset($_GET['begin'])){
  $begin=$_GET['begin'];
}else{
  $begin=0;
}
if($_GET['ITEM']=='changement_Gestion_Liste_Inscrit'){
  $rq_info_CHANGEMENT_STATUS_ID="
  SELECT `CHANGEMENT_STATUS_ID` 
  FROM `changement_status` 
  WHERE `CHANGEMENT_STATUS` ='Inscrit' 
  AND `ENABLE`='0'
  LIMIT 1";
  $res_rq_info_CHANGEMENT_STATUS_ID = mysql_query($rq_info_CHANGEMENT_STATUS_ID, $mysql_link) or die(mysql_error());
  $tab_rq_info_CHANGEMENT_STATUS_ID = mysql_fetch_assoc($res_rq_info_CHANGEMENT_STATUS_ID);
  $total_ligne_rq_info_CHANGEMENT_STATUS_ID=mysql_num_rows($res_rq_info_CHANGEMENT_STATUS_ID);
  $CHANGEMENT_STATUS_ID=$tab_rq_info_CHANGEMENT_STATUS_ID['CHANGEMENT_STATUS_ID'];
  mysql_free_result($res_rq_info_CHANGEMENT_STATUS_ID);
  $CHANGEMENT_STATUS_sql='`changement_liste`.`CHANGEMENT_STATUS_ID`='.$CHANGEMENT_STATUS_ID.' AND';
}else{
  $CHANGEMENT_STATUS_sql="";
}
if(isset($_GET['order'])){
	$order=$_GET['order'];
  switch ($_GET['order']) {
  case "id":
      $order_sql="`changement_liste`.`CHANGEMENT_LISTE_ID`";
      break;
  case "iddesc":
      $order_sql="`changement_liste`.`CHANGEMENT_LISTE_ID` DESC";
      break;
  case "demandeur":
      $order_sql="`moteur_utilisateur`.`NOM`,`moteur_utilisateur`.`PRENOM`";
      break;
  case "demandeurdesc":
      $order_sql="`moteur_utilisateur`.`NOM` DESC,`moteur_utilisateur`.`PRENOM` DESC";
      break;
  case "status":
      $order_sql="`changement_status`.`CHANGEMENT_STATUS`";
      break;
  case "statusdesc":
      $order_sql="`changement_status`.`CHANGEMENT_STATUS` DESC";
      break;
  case "type":
      $order_sql="`changement_demande`.`CHANGEMENT_DEMANDE_LIB`";
      break;
  case "typedesc":
      $order_sql="`changement_demande`.`CHANGEMENT_DEMANDE_LIB` DESC";
      break;
  case "debut":
      $order_sql="`changement_liste`.`CHANGEMENT_LISTE_DATE_DEBUT`";
      break;
  case "debutdesc":
      $order_sql="`changement_liste`.`CHANGEMENT_LISTE_DATE_DEBUT` DESC";
      break;
  case "fin":
      $order_sql="`changement_liste`.`CHANGEMENT_LISTE_DATE_FIN`";
      break;
  case "findesc":
      $order_sql="`changement_liste`.`CHANGEMENT_LISTE_DATE_FIN` DESC";
      break;
  default:
      $order_sql="`changement_liste`.`CHANGEMENT_LISTE_DATE_DEBUT` DESC";
      break;
  }
}else{
  $order='';
  $order_sql="`changement_liste`.`CHANGEMENT_LISTE_DATE_DEBUT` DESC";
}
$date_limit_url=15;
if(isset($_GET['date_limit'])){
  $date_limit=$_GET['date_limit'];
  if($date_limit!=''){
    $DATE_LIMIT=date("Ymd", mktime(0, 0, 0, date("m"), date("d")-$date_limit, date("Y")));
    $DATE_LIMIT_SQL="`changement_liste`.`CHANGEMENT_LISTE_DATE_DEBUT` >= '".$DATE_LIMIT."' AND";	
  }else{
    $DATE_LIMIT_SQL="";
    $date_limit='';
  }
}else{
  $DATE_LIMIT_SQL="";
  $date_limit='';
}


$rq_info="
SELECT COUNT(`CHANGEMENT_LISTE_ID`) AS `NB`
FROM `changement_liste`
WHERE 
".$DATE_LIMIT_SQL."
".$CHANGEMENT_STATUS_sql."
`ENABLE` = '0'
";
$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
$tab_rq_info = mysql_fetch_assoc($res_rq_info);
$total_ligne_rq_info=mysql_num_rows($res_rq_info); 
$NB_ALL=$tab_rq_info['NB'];
mysql_free_result($res_rq_info);
// gestion des droits d'acces
if(isset($_SESSION['LOGIN'])){
	$LOGIN=$_SESSION['LOGIN'];
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
}

$date=date("Ymd");  

// Début page HTML
echo '
<div align="center">
';

$numLigne=0;
$nbinter=0;
$DATE_MODIFICATION=date("YmdHis");
echo'
<table class="table_inc">
  <tr align="center" class="titre">
    <td align="center" colspan="12">';
    if($CHANGEMENT_STATUS_sql==''){
    echo '
    <h2>&nbsp;Liste de l\'ensemble des changements&nbsp;</h2>
      Changement r&eacute;cent (-'.$date_limit_url.' jours) <a href="./changement/changement_Gestion_Liste_all_csv.php?date_limit='.$date_limit_url.'&temp_date='.$DATE_MODIFICATION.'"><img src="./img/logo_excel.png" border="0"/></a>
      &nbsp;&nbsp;-&nbsp;&nbsp;Ensemble des changements <a href="./changement/changement_Gestion_Liste_all_csv.php?temp_date='.$DATE_MODIFICATION.'"><img src="./img/logo_excel.png" border="0"/></a>';
      }else{
      echo '<h2>&nbsp;Liste de l\'ensemble des Inscriptions&nbsp;</h2>';
      }
      echo '
    </td>
  </tr>
  ';
     if($NB_ALL > $Var_max_resultat_page_limit){
     	$var_url="./index.php?ITEM=".$_GET['ITEM']."&order=".$order."&date_limit=".$date_limit;
     	echo '
  <tr align="center" class="titre">
     <td align="center" colspan="12">&nbsp;';
        makeListLink($NB_ALL,$Var_max_resultat_page_limit,$var_url,1);
      
      echo '&nbsp;</td>
  </tr>';
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
      `changement_liste`.`CHANGEMENT_LISTE_LIB` ,
      `changement_demande`.`CHANGEMENT_DEMANDE_LIB`,
      `changement_status`.`CHANGEMENT_STATUS` ,
      `changement_status`.`CHANGEMENT_STATUS_COULEUR_FOND` ,
      `changement_status`.`CHANGEMENT_STATUS_COULEUR_TEXT`
      FROM `changement_liste` , `moteur_utilisateur`,`changement_status`,`changement_demande`
      WHERE 
      `changement_liste`.`CHANGEMENT_STATUS_ID`=`changement_status`.`CHANGEMENT_STATUS_ID` AND
      `changement_liste`.`CHANGEMENT_DEMANDE_ID`=`changement_demande`.`CHANGEMENT_DEMANDE_ID` AND
      `moteur_utilisateur`.`UTILISATEUR_ID` = `changement_liste`.`CHANGEMENT_LISTE_UTILISATEUR_ID` AND 
      ".$DATE_LIMIT_SQL."
      ".$CHANGEMENT_STATUS_sql."
      `changement_liste`.`ENABLE` = '0'
      ORDER BY ".$order_sql." 
      LIMIT ".$begin.",".$Var_max_resultat_page_limit.";
      ";
      //echo $rq_changement_info;
      $res_rq_changement_info = mysql_query($rq_changement_info, $mysql_link) or die(mysql_error());
      $tab_rq_changement_info = mysql_fetch_assoc($res_rq_changement_info);
      $total_ligne_rq_changement_info=mysql_num_rows($res_rq_changement_info);
      if($total_ligne_rq_changement_info!=0){
        echo '
              <tr align="center" class="titre">
                <td align="center">&nbsp;Identifiant
                <a class="LinkModif" href="./index.php?ITEM='.$_GET['ITEM'].'&date_limit='.$date_limit.'&order=iddesc">&#9660;</a>
                <a class="LinkModif" href="./index.php?ITEM='.$_GET['ITEM'].'&date_limit='.$date_limit.'&order=id">&#9650;</a>
                &nbsp;
                </td>
                <td align="center">&nbsp;Demandeur
                <a class="LinkModif" href="./index.php?ITEM='.$_GET['ITEM'].'&date_limit='.$date_limit.'&order=demandeurdesc">&#9660;</a>
		<a class="LinkModif" href="./index.php?ITEM='.$_GET['ITEM'].'&date_limit='.$date_limit.'&order=demandeur">&#9650;</a>
                &nbsp;
                </td>
                <td align="center">&nbsp;Status
                <a class="LinkModif" href="./index.php?ITEM='.$_GET['ITEM'].'&date_limit='.$date_limit.'&order=statusdesc">&#9660;</a>
		<a class="LinkModif" href="./index.php?ITEM='.$_GET['ITEM'].'&date_limit='.$date_limit.'&order=status">&#9650;</a>
                &nbsp;
                </td>
                <td align="center">&nbsp;Type de demande
                <a class="LinkModif" href="./index.php?ITEM='.$_GET['ITEM'].'&date_limit='.$date_limit.'&order=typedesc">&#9660;</a>
		<a class="LinkModif" href="./index.php?ITEM='.$_GET['ITEM'].'&date_limit='.$date_limit.'&order=type">&#9650;</a>
                &nbsp;
                </td>
                <td align="center">&nbsp;Date&nbsp;D&eacute;but
                <a class="LinkModif" href="./index.php?ITEM='.$_GET['ITEM'].'&date_limit='.$date_limit.'&order=debutdesc">&#9660;</a>
		<a class="LinkModif" href="./index.php?ITEM='.$_GET['ITEM'].'&date_limit='.$date_limit.'&order=debut">&#9650;</a>
                &nbsp;
                </td>
                <td align="center">&nbsp;Heure&nbsp;</BR>&nbsp;D&eacute;but&nbsp;</td>
                <td align="center">&nbsp;Date&nbsp;Fin
                <a class="LinkModif" href="./index.php?ITEM='.$_GET['ITEM'].'&date_limit='.$date_limit.'&order=findesc">&#9660;</a>
		<a class="LinkModif" href="./index.php?ITEM='.$_GET['ITEM'].'&date_limit='.$date_limit.'&order=fin">&#9650;</a>
                &nbsp;
                </td>
                <td align="center">&nbsp;Heure&nbsp;</BR>&nbsp;Fin&nbsp;</td>
                <td align="center">&nbsp;Titre du changement&nbsp;</td>
                <td align="center">&nbsp;FAR&nbsp;</td>
                <td align="center">&nbsp;Fiche&nbsp;</BR>&nbsp;Bilan&nbsp;</td>
                <td align="center">&nbsp;Compte&nbsp;</BR>&nbsp;Rendu&nbsp;</td>
              </tr>
              ';
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
	$LIB=html_entity_decode($tab_rq_changement_info['CHANGEMENT_LISTE_LIB']);
	
  $nbre_car = strlen($LIB);
  if ($nbre_car < 30){
    $LIB = $LIB;
  }else{
    $LIB = substr($LIB,0,30);
    $LIB = $LIB.'...';
  }
  $LIB=htmlentities($LIB);

	$rq_info_detail="
	SELECT `CHANGEMENT_LISTE_ID`
	FROM `changement_far`
	WHERE `CHANGEMENT_LISTE_ID` ='".$ID."' 
	AND `ENABLE` = '0'
	";
	$res_rq_info_detail = mysql_query($rq_info_detail, $mysql_link) or die(mysql_error());
	$tab_rq_info_detail = mysql_fetch_assoc($res_rq_info_detail);
	$total_ligne_rq_info_detail=mysql_num_rows($res_rq_info_detail);
	$FAR='Non';
	if($total_ligne_rq_info_detail!=0){
		$FAR='Oui';
	}
	mysql_free_result($res_rq_info_detail);
	
	$rq_info_detail="
	SELECT `CHANGEMENT_LISTE_ID`
	FROM `changement_bilan`
	WHERE `CHANGEMENT_LISTE_ID` ='".$ID."' 
	AND `ENABLE` = '0'
	";
	$res_rq_info_detail = mysql_query($rq_info_detail, $mysql_link) or die(mysql_error());
	$tab_rq_info_detail = mysql_fetch_assoc($res_rq_info_detail);
	$total_ligne_rq_info_detail=mysql_num_rows($res_rq_info_detail);
	$FicheBilan='Non';
	if($total_ligne_rq_info_detail!=0){
		$FicheBilan='Oui';
	}
	mysql_free_result($res_rq_info_detail);
	
	$rq_info_detail="
	SELECT `CHANGEMENT_LISTE_ID`
	FROM `changement_compte_rendu`
	WHERE `CHANGEMENT_LISTE_ID` ='".$ID."' 
	AND `ENABLE` = '0'
	";
	$res_rq_info_detail = mysql_query($rq_info_detail, $mysql_link) or die(mysql_error());
	$tab_rq_info_detail = mysql_fetch_assoc($res_rq_info_detail);
	$total_ligne_rq_info_detail=mysql_num_rows($res_rq_info_detail);
	$CompteRendu='Non';
	if($total_ligne_rq_info_detail!=0){
		$CompteRendu='Oui';
	}
	mysql_free_result($res_rq_info_detail);
	
	$rq_info_id="
	SELECT 
	`moteur_trace`.`MOTEUR_TRACE_UTILISATEUR_ID`, 
	`moteur_trace`.`MOTEUR_TRACE_DATE`, 
	`moteur_trace`.`MOTEUR_TRACE_CATEGORIE`, 
	`moteur_trace`.`MOTEUR_TRACE_TABLE`, 
	`moteur_trace`.`MOTEUR_TRACE_REF_ID`, 
	`moteur_trace`.`MOTEUR_TRACE_ACTION`, 
	`changement_status`.`CHANGEMENT_STATUS`,
	`moteur_utilisateur`.`LOGIN`, 
	`moteur_utilisateur`.`NOM`, 
	`moteur_utilisateur`.`PRENOM` 
	FROM `moteur_trace`,`moteur_utilisateur`,`changement_status`
	WHERE 
	`moteur_trace`.`MOTEUR_TRACE_CATEGORIE`='Changement'
	AND `moteur_trace`.`MOTEUR_TRACE_UTILISATEUR_ID`=`moteur_utilisateur`.`UTILISATEUR_ID` 
	AND `moteur_trace`.`MOTEUR_TRACE_ETAT`=`changement_status`.`CHANGEMENT_STATUS_ID` 
	AND `moteur_trace`.`MOTEUR_TRACE_REF_ID`='".$ID."'
	ORDER BY `moteur_trace`.`MOTEUR_TRACE_DATE_TRI` DESC";
	$res_rq_info_id = mysql_query($rq_info_id, $mysql_link) or die(mysql_error());
	$tab_rq_info_id = mysql_fetch_assoc($res_rq_info_id);
	$total_ligne_rq_info_id=mysql_num_rows($res_rq_info_id);
	$AFF_SPAN='';
	
	if($total_ligne_rq_info_id!=0){
	          
		do {
		
		$MOTEUR_TRACE_DATE=$tab_rq_info_id['MOTEUR_TRACE_DATE'];
		$MOTEUR_TRACE_CATEGORIE=$tab_rq_info_id['MOTEUR_TRACE_CATEGORIE'];
		$MOTEUR_TRACE_TABLE=str_replace('changement_','',$tab_rq_info_id['MOTEUR_TRACE_TABLE']);
		$MOTEUR_TRACE_REF_ID=$tab_rq_info_id['MOTEUR_TRACE_REF_ID'];
		$MOTEUR_TRACE_ACTION=$tab_rq_info_id['MOTEUR_TRACE_ACTION'];
		$CHANGEMENT_STATUS=$tab_rq_info_id['CHANGEMENT_STATUS'];
		$MOTEUR_TRACE_NOM=$tab_rq_info_id['NOM'];
		$MOTEUR_TRACE_PRENOM=$tab_rq_info_id['PRENOM'];
		$AFF_SPAN.=$MOTEUR_TRACE_DATE.' - '.$MOTEUR_TRACE_PRENOM.' '.$MOTEUR_TRACE_NOM.' - '.$MOTEUR_TRACE_ACTION.' - '.$MOTEUR_TRACE_TABLE.' - '.$CHANGEMENT_STATUS.'</BR>';
		 } while ($tab_rq_info_id = mysql_fetch_assoc($res_rq_info_id));
	        $ligne= mysql_num_rows($res_rq_info_id);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_id, 0);
	          $tab_rq_info_id = mysql_fetch_assoc($res_rq_info_id);
	        }
	}
	mysql_free_result($res_rq_info_id);

        $numLigne = $numLigne + 1;
        $nbinter = $nbinter + 1;
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
                <td align="center"><a name="'.$ID.'"></a>&nbsp;<a href="./index.php?ITEM='.$ITEM_url.'&action='.$Action_url.'&ID='.$ID.'" class="infobullegauche"><font color="#000000">'.$ID.'</font><span>'.$AFF_SPAN.'</span></a>&nbsp;</td>
                <td align="center"><strike>&nbsp;'.stripslashes($PRENOM).' '.stripslashes($NOM).'&nbsp;</strike></td>
                <td align="center" bgcolor="#'.$CHANGEMENT_STATUS_COULEUR_FOND.'"><FONT COLOR="#'.$CHANGEMENT_STATUS_COULEUR_TEXT.'"><strike>&nbsp;'.stripslashes($STATUS).'&nbsp;</strike></FONT></td>
                <td align="center"><strike>&nbsp;'.stripslashes($CHANGEMENT_DEMANDE_LIB).'&nbsp;</strike></td>
                <td align="center"><strike>&nbsp;'.datebdd_nomjour($DATE_DEBUT).' '.$DATE_DEBUT.'&nbsp;</strike></td>
                <td align="center"><strike>&nbsp;'.$HEURE_DEBUT.'&nbsp;</strike></td>
                <td align="center"><strike>&nbsp;'.datebdd_nomjour($DATE_FIN).' '.$DATE_FIN.'&nbsp;</strike></td>
                <td align="center"><strike>&nbsp;'.$HEURE_FIN.'&nbsp;</strike></td>
                <td align="center"><strike>&nbsp;'.stripslashes($LIB).'&nbsp;</strike></td>
                <td align="center"><strike>&nbsp;'.stripslashes($FAR).'&nbsp;</strike></td>
                <td align="center"><strike>&nbsp;'.stripslashes($FicheBilan).'&nbsp;</strike></td>
                <td align="center"><strike>&nbsp;'.stripslashes($CompteRendu).'&nbsp;</strike></td>
              </tr>';
        }else{
              echo'
              <tr align="center" class="'.$class.'">
                <td align="center"><a name="'.$ID.'"></a>&nbsp;<a href="./index.php?ITEM='.$ITEM_url.'&action='.$Action_url.'&ID='.$ID.'" class="infobullegauche"><font color="#000000">'.$ID.'</font><span>'.$AFF_SPAN.'</span></a>&nbsp;</td>
                <td align="center">&nbsp;'.stripslashes($PRENOM).' '.stripslashes($NOM).'&nbsp;</td>
                <td align="center" bgcolor="#'.$CHANGEMENT_STATUS_COULEUR_FOND.'"><FONT COLOR="#'.$CHANGEMENT_STATUS_COULEUR_TEXT.'">&nbsp;'.stripslashes($STATUS).'&nbsp;</FONT></td>
                <td align="center">&nbsp;'.stripslashes($CHANGEMENT_DEMANDE_LIB).'&nbsp;</td>
                <td align="center">&nbsp;'.datebdd_nomjour($DATE_DEBUT).' '.$DATE_DEBUT.'&nbsp;</td>
                <td align="center">&nbsp;'.$HEURE_DEBUT.'&nbsp;</td>
                <td align="center">&nbsp;'.datebdd_nomjour($DATE_FIN).' '.$DATE_FIN.'&nbsp;</td>
                <td align="center">&nbsp;'.$HEURE_FIN.'&nbsp;</td>
                <td align="center">&nbsp;'.stripslashes($LIB).'&nbsp;</td>
                <td align="center">&nbsp;'.stripslashes($FAR).'&nbsp;</td>
                <td align="center">&nbsp;'.stripslashes($FicheBilan).'&nbsp;</td>
                <td align="center">&nbsp;'.stripslashes($CompteRendu).'&nbsp;</td>
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
            <td align="center" colspan="12">&nbsp;Pas d\'information&nbsp;</td>
          </tr>';
        }
        if($nbinter!=0){
     if($NB_ALL > $Var_max_resultat_page_limit){
     	$var_url="./index.php?ITEM=".$_GET['ITEM']."&order=".$order."&date_limit=".$date_limit;
     	echo '
  <tr align="center" class="titre">
     <td align="center" colspan="12">&nbsp;';
        makeListLink($NB_ALL,$Var_max_resultat_page_limit,$var_url,1);
      echo '&nbsp;</td>
  </tr>';
}
	echo '<tr align="center" class="titre">
            <td align="center" colspan="12">&nbsp;</td>
          </tr>';
        }else{
         echo '  
          <tr align="center" class="titre">
            <td align="center" colspan="12">&nbsp;</td>
          </tr>';
        }
		echo '
		</table>
    </div>';

mysql_close($mysql_link); 
?>
