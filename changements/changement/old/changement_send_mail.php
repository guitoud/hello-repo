<?php
//redirection si acces dirrect
if(substr_count($_SERVER['PHP_SELF'], 'index.php')==0){
  header("Location: ../"); 
  exit(); 
}
$j=0;
require("./cf/conf_outil_icdc.php"); 
require_once("./cf/fonctions.php");
require_once("./cf/autre_fonctions.php");
 
require_once('./changement/changement_Conf_mail.php') ; 

$ENV =substr($_SERVER["SCRIPT_FILENAME"], 1, 1);
?>
<script language="javascript" type="text/javascript" src="lib/tiny_mce/tiny_mce.js"></script>
<script language="javascript" type="text/javascript">
tinyMCE.init({
mode : "textareas",
theme : "simple"
});
</script>
<?PHP
if(isset($_GET['type'])){
  $type=$_GET['type'];
}else{
  $type='vide';
}

$tab_var=$_POST;
if(isset($_GET['ID'])){
  if(is_numeric($_GET['ID'])){
    $ID=$_GET['ID'];
  }
}
//echo 'ID = '.$ID.'<br>';
if(isset($tab_var['ID'])){
  if(is_numeric($tab_var['ID'])){
    $ID=$tab_var['ID'];
    $_GET['ID']=$ID;
  }
}
if(isset($tab_var['type'])){
    $type=$tab_var['type'];
    $_GET['type']=$type;
}
$MAIL_DEST='';
$MAIL_OBJET='';
$MAIL_COMMENTAIRE='';
if (empty($tab_var)){
}else{
if(empty($tab_var['btn'])){
}else{
# Cas RAZ
# On vide les champs de saisie
	if ($tab_var['btn']=="RAZ"){
		$MAIL_DEST='';
		$MAIL_OBJET='';
		$MAIL_COMMENTAIRE='';
	}


}
}	
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
	ORDER BY `moteur_trace`.`MOTEUR_TRACE_DATE` DESC";
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
		$MOTEUR_TRACE_CHANGEMENT_STATUS=$tab_rq_info_id['CHANGEMENT_STATUS'];
		$MOTEUR_TRACE_NOM=$tab_rq_info_id['NOM'];
		$MOTEUR_TRACE_PRENOM=$tab_rq_info_id['PRENOM'];
		$AFF_SPAN.=$MOTEUR_TRACE_DATE.' - '.$MOTEUR_TRACE_PRENOM.' '.$MOTEUR_TRACE_NOM.' - '.$MOTEUR_TRACE_ACTION.' - '.$MOTEUR_TRACE_TABLE.' - '.$MOTEUR_TRACE_CHANGEMENT_STATUS.'</BR>';
		 } while ($tab_rq_info_id = mysql_fetch_assoc($res_rq_info_id));
	        $ligne= mysql_num_rows($res_rq_info_id);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_id, 0);
	          $tab_rq_info_id = mysql_fetch_assoc($res_rq_info_id);
	        }
	}
	mysql_free_result($res_rq_info_id);
echo '
<form name="changement_send_mail" method="post" action="index.php?item=changement_send_mail">
<center>
<table class="table_inc" cellspacing="1" cellpading="0">
  <tr align="center" class="titre" class="impair">
  	<tr align="center" class="titre">
		<td align=center colspan=2 ><h2>&nbsp;[&nbsp;&nbsp;';
		echo 'Envoi du mail pour le changement n&deg; <a href="#" class="infobulledroite">'.$ID.'<span>'.$AFF_SPAN.'</span></a>';
		echo '&nbsp;]&nbsp;</h2>&nbsp;</td>
	</tr>';
	$j++;
	if ($j%2) { $class = "pair";}else{$class = "impair";} 
	echo '
	<tr class="'.$class.'">
    <td align="left">
    	Destinataire :
    </td>
    <td align="left">
       <input type=text name="MAIL_DEST" size=100 value="'.$MAIL_DEST.'">
    </td>
  </tr>';
	$j++;
	if ($j%2) { $class = "pair";}else{$class = "impair";} 
	echo '
	<tr class="'.$class.'">
    <td align="left">
    	<div id="ligne">Objet du mail : </div>
    </td>
    <td align="left">
    <input type=text name="objet" size=100 value="'.$MAIL_OBJET.'">
    </td>
  </tr>
  ';
	$j++;
	if ($j%2) { $class = "pair";}else{$class = "impair";} 
	echo '
	<tr class="'.$class.'">
  <td colspan=2 align="left"> 
  <center><textarea name="MAIL_COMMENTAIRE" cols="70" rows="15" id="MAIL_COMMENTAIRE">'.$MAIL_COMMENTAIRE.'</textarea></center>
  </td>
  </tr>
  <tr class="titre">
  <td colspan=2 align="center">
  <input type="hidden" name="ID" value="'.$ID.'">
  <input type="hidden" name="type" value="'.$type.'">
  <input name="btn" name="btn" type="submit" value="Apercu">
  </td>
  </tr>
<tr align="center" class="titre">
	<td align=center colspan=2 >&nbsp;</td>
</tr>
</table>
</center>
</form>';

mysql_close($mysql_link); 
?>