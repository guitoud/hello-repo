<?PHP
//redirection si acces dirrect
if(substr_count($_SERVER['PHP_SELF'], 'index.php')==0){
  header("Location: ../");
  exit();
}
/*******************************************************************
   Interface Recherche d un id
   Version 1.0.0  
  18/10/2010 - VGU - Creation fichier
**************************************************Guibert Vincent***
*******************************************************************/

require("./cf/conf_outil_icdc.php"); 
require_once("./cf/fonctions.php");
$j=0;
$ID='';
$page_test=0;
$STOP=0;
$Recherche_ID='';
$Recherche_ID_info=0;

if(isset($_GET['action'])){
  $action=$_GET['action'];
}else{
  $action='Recherche';
}

$tab_var=$_POST;

if(isset($_POST['action'])){
  $action=$_POST['action'];
}

if(isset($_GET['ID'])){
  if(is_numeric($_GET['ID'])){
    $ID=$_GET['ID'];
  }
}

if(isset($tab_var['ID'])){
  if(is_numeric($tab_var['ID'])){
    $ID=$tab_var['ID'];
  }
}
if(isset($_POST['ID'])){
  $ID=$_POST['ID'];
}

if(empty($tab_var['btn'])){
}else{


  # Cas RAZ
  if($tab_var['btn']=="RAZ"){
    $Recherche_ID='';
    $action='Recherche';
  }
  
  # Cas Recherche
  if($tab_var['btn']=="Recherche"){
    $Recherche_ID=addslashes(trim(htmlentities($tab_var['Recherche_ID'])));  

      $rq_info_id="
      SELECT `CHANGEMENT_LISTE_ID` 
      FROM `changement_liste`
      WHERE `CHANGEMENT_LISTE_ID`='".$Recherche_ID."'
      AND `ENABLE`=0";
      $res_rq_info_id = mysql_query($rq_info_id, $mysql_link) or die(mysql_error());
      $tab_rq_info_id = mysql_fetch_assoc($res_rq_info_id);
      $total_ligne_rq_info_id=mysql_num_rows($res_rq_info_id);
      mysql_free_result($res_rq_info_id);
      if($total_ligne_rq_info_id!=0){
        echo '
        <script language="JavaScript">
        url=("./index.php?ITEM=changement_Modif_Changement&action=Modif&ID='.$Recherche_ID.'");
        window.location=url;
        </script>
        ';
      }else{
        $STOP=1;
      }
  }

}

echo '
<!--Début page HTML -->  
<div align="center">

<form method="post" name="frm_recherche" id="frm_recherche" action="index.php?ITEM='.$_GET['ITEM'].'">
<table class="table_inc">
	<tr align="center" class="titre">
	  <td colspan="2"><h2>&nbsp;[&nbsp;Recherche d\'un changement&nbsp;]&nbsp;</h2></td>
	</tr>
	';
  $j++;
  if ($j%2) { $class = "pair";}else{$class = "impair";} 
	echo '
	<tr class='.$class.'>
    <td align="left">&nbsp;numero : &nbsp;</td>
    <td align="left"><input name="Recherche_ID" type="text" value="'.stripslashes($Recherche_ID).'" size="50"/>
    ';
    if($STOP==1){
      echo '</BR><font color=#993333><b>Ce changement n\'existe pas.</b></font>';
    }
    echo '
    </td>
	</tr>
	<tr class="titre">
    <td colspan="2" align="center">
    <h2>
    <input name="btn" type="submit" id="btn" value="Recherche">
    <input type="hidden" name="ID" value="'.$ID.'">
    <input type="hidden" name="action" id="action" value="'.$action.'">
    </h2>
    </td>
	</tr>
	</table>
</form>
</div>';
mysql_close($mysql_link); 
?>