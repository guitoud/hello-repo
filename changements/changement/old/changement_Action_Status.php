<?PHP
//redirection si acces dirrect
if(substr_count($_SERVER['PHP_SELF'], 'index.php')==0){
  header("Location: ../");
  exit();
}
/*******************************************************************
   Interface Ajout status
   Version 1.0.0 
  24/03/2010 - VGU - Creation fichier
**************************************************Guibert Vincent***
*******************************************************************/

require("./cf/conf_outil_icdc.php"); 
require_once("./cf/fonctions.php");
$j=0;
$ID='';
$page_test=0;
$STOP=0;
$STOP_INFO_CHANGEMENT_STATUS=0;
$CHANGEMENT_STATUS='';
$aff_modif=0;

if(isset($_GET['action'])){
  $action=$_GET['action'];
}else{
  $action='Ajout';
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
    $CHANGEMENT_STATUS='';
    $action='Ajout';
    $_GET['ITEM']='changement_Ajout_Status';
  }
  
  # Cas Ajouter
  if($tab_var['btn']=="Ajouter"){
    $CHANGEMENT_STATUS=addslashes(trim(htmlentities($tab_var['txt_CHANGEMENT_STATUS'])));  
    if($CHANGEMENT_STATUS==''){
      $STOP=1;
    }
    if($STOP==0){
      $rq_status_info="
      SELECT `CHANGEMENT_STATUS_ID` 
      FROM `changement_status`
      WHERE `CHANGEMENT_STATUS`='".$CHANGEMENT_STATUS."'
      AND `ENABLE`=0";
      $res_rq_status_info = mysql_query($rq_status_info, $mysql_link) or die(mysql_error());
      $tab_rq_status_info = mysql_fetch_assoc($res_rq_status_info);
      $total_ligne_rq_status_info=mysql_num_rows($res_rq_status_info);
      if($total_ligne_rq_status_info!=0){
        $STOP_INFO_CHANGEMENT_STATUS=1;
      }else{
        //ajoute l status si non presente dans bdd
        
        $sql="
        INSERT INTO `changement_status`( `CHANGEMENT_STATUS_ID` , `CHANGEMENT_STATUS` ,`ENABLE`)
        VALUES ( NULL , '".$CHANGEMENT_STATUS."','0' );";
        mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
        
        $TABLE_SQL_SQL='changement_status';       
        historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');
                
        echo '
        <script language="JavaScript">
        url=("./index.php?ITEM=changement_Gestion_Status");
        window.location=url;
        </script>
        ';
      }
      mysql_free_result($res_rq_status_info);
    }else{
      if($page_test!=3){
        $page_test=2;
      }
    }
  }
  
  # Cas Supprimer
  if($tab_var['btn']=="Supprimer"){
      $rq_status_info="
      SELECT `CHANGEMENT_STATUS_ID` 
      FROM `changement_liste` 
      WHERE `CHANGEMENT_STATUS_ID`='".$ID."'";
      $res_rq_status_info = mysql_query($rq_status_info, $mysql_link) or die(mysql_error());
      $tab_rq_status_info = mysql_fetch_assoc($res_rq_status_info);
      $total_ligne_rq_status_info=mysql_num_rows($res_rq_status_info);
      mysql_free_result($res_rq_status_info);
      if($total_ligne_rq_status_info!=0){
        $STOP_INFO_CHANGEMENT_STATUS=2;
        $rq_status_info="
        SELECT `CHANGEMENT_STATUS_ID` FROM `changement_status` WHERE `CHANGEMENT_STATUS_ID`='".$ID."'";
        $res_rq_status_info = mysql_query($rq_status_info, $mysql_link) or die(mysql_error());
        $tab_rq_status_info = mysql_fetch_assoc($res_rq_status_info);
        $total_ligne_rq_status_info=mysql_num_rows($res_rq_status_info);
        $CHANGEMENT_STATUS=$tab_rq_status_info['CHANGEMENT_STATUS'];
      }else{
        //supprime la status si pas d utilisation de celle-ci
        
        $sql="UPDATE `changement_status` SET `ENABLE` = '1' WHERE `CHANGEMENT_STATUS_ID` ='".$ID."' LIMIT 1 ;";
        mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
        
        $TABLE_SQL_SQL='changement_status';       
        historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');
       
        $sql="OPTIMIZE TABLE `changement_status`";
        mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
        
        $TABLE_SQL_SQL='changement_status';       
        historique_sql_new($sql,$TABLE_SQL_SQL,'OPTIMIZE');
        
        echo '
        <script language="JavaScript">
        url=("./index.php?ITEM=changement_Gestion_Status");
        window.location=url;
        </script>
        ';
      }
      
  }
  # Cas Modifier
  if($tab_var['btn']=="Modifier"){
    $CHANGEMENT_STATUS=addslashes(trim(htmlentities($tab_var['txt_CHANGEMENT_STATUS'])));

    if($CHANGEMENT_STATUS==''){
      $STOP=1;
    }
    if($STOP==0){
      $rq_status_info="
      SELECT `CHANGEMENT_STATUS_ID` 
      FROM `changement_liste` 
      WHERE `CHANGEMENT_STATUS_ID`='".$ID."'";
      $res_rq_status_info = mysql_query($rq_status_info, $mysql_link) or die(mysql_error());
      $tab_rq_status_info = mysql_fetch_assoc($res_rq_status_info);
      $total_ligne_rq_status_info=mysql_num_rows($res_rq_status_info);
      mysql_free_result($res_rq_status_info);
      if($total_ligne_rq_status_info!=0){
        $STOP_INFO_CHANGEMENT_STATUS=3;
        $rq_status_info="
        SELECT `CHANGEMENT_STATUS_ID` FROM `changement_status` WHERE `CHANGEMENT_STATUS_ID`='".$ID."'";
        $res_rq_status_info = mysql_query($rq_status_info, $mysql_link) or die(mysql_error());
        $tab_rq_status_info = mysql_fetch_assoc($res_rq_status_info);
        $total_ligne_rq_status_info=mysql_num_rows($res_rq_status_info);
        $CHANGEMENT_STATUS=$tab_rq_status_info['CHANGEMENT_STATUS'];
      }else{
        $rq_status_info="
        SELECT `CHANGEMENT_STATUS_ID` FROM `changement_status` WHERE `CHANGEMENT_STATUS_ID`='".$ID."'";
        $res_rq_status_info = mysql_query($rq_status_info, $mysql_link) or die(mysql_error());
        $tab_rq_status_info = mysql_fetch_assoc($res_rq_status_info);
        $total_ligne_rq_status_info=mysql_num_rows($res_rq_status_info);
        if($total_ligne_rq_status_info!=0){     
          $sql="
          UPDATE `changement_status` SET 
          `CHANGEMENT_STATUS` = '".$CHANGEMENT_STATUS."'
          WHERE `CHANGEMENT_STATUS_ID` ='".$ID."' LIMIT 1 ;";
          mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());     
          
          $TABLE_SQL_SQL='changement_status';       
          historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');
                    
          echo '
          <script language="JavaScript">
          url=("./index.php?ITEM=changement_Gestion_Status");
          window.location=url;
          </script>
          ';
          }
      }
      mysql_free_result($res_rq_status_info);
    }else{
      if($page_test!=3){
        $page_test=2;
      }
    }
  }
}
if($action=="Modif"){
  if(isset($_GET['ID'])){
    if(is_numeric($_GET['ID'])){
      $ID=$_GET['ID'];
      $rq_status_info="
      SELECT `CHANGEMENT_STATUS_ID` , `CHANGEMENT_STATUS`
      FROM `changement_status`
      WHERE `CHANGEMENT_STATUS_ID`='".$ID."'";
      $res_rq_status_info = mysql_query($rq_status_info, $mysql_link) or die(mysql_error());
      $tab_rq_status_info = mysql_fetch_assoc($res_rq_status_info);
      $total_ligne_rq_status_info=mysql_num_rows($res_rq_status_info);
      $CHANGEMENT_STATUS=$tab_rq_status_info['CHANGEMENT_STATUS'];
    }
  }
  if(isset($_POST['ID'])){
    if(is_numeric($_POST['ID'])){
      $ID=$_POST['ID'];
      $rq_status_info="
      SELECT `CHANGEMENT_STATUS_ID` , `CHANGEMENT_STATUS`
      FROM `changement_status`
      WHERE `CHANGEMENT_STATUS_ID`='".$ID."'";
      $res_rq_status_info = mysql_query($rq_status_info, $mysql_link) or die(mysql_error());
      $tab_rq_status_info = mysql_fetch_assoc($res_rq_status_info);
      $total_ligne_rq_status_info=mysql_num_rows($res_rq_status_info);
      $CHANGEMENT_STATUS=$tab_rq_status_info['CHANGEMENT_STATUS'];
    }
  }
  $rq_status_info="
  SELECT `CHANGEMENT_STATUS_ID` 
  FROM `changement_liste` 
  WHERE `CHANGEMENT_STATUS_ID`='".$ID."'";
  $res_rq_status_info = mysql_query($rq_status_info, $mysql_link) or die(mysql_error());
  $tab_rq_status_info = mysql_fetch_assoc($res_rq_status_info);
  $total_ligne_rq_status_info=mysql_num_rows($res_rq_status_info);
  if($total_ligne_rq_status_info!=0){
    $aff_modif=1;
  }else{
    $aff_modif=0;
  }
  mysql_free_result($res_rq_status_info);
}
echo '
<!--Début page HTML -->  
<div align="center">

<form method="post" name="frm_status" id="frm_status" action="index.php?ITEM='.$_GET['ITEM'].'">
<table class="table_inc">
	<tr align="center" class="titre">';
    if($action=="Ajout"){
      echo '
				<td colspan="2"><h2>&nbsp;[&nbsp;Ajout d\'un status&nbsp;]&nbsp;</h2></td>';
    }
    if($action=="Modif"){
      echo '
				<td colspan="2">
				<h2>&nbsp;[&nbsp;Modification d\'un status&nbsp;]&nbsp;</h2>
				';
				if($aff_modif==1){
          echo '&nbsp;Pas d\'action possible car on utilise le status&nbsp;';
				}
				echo '
				</td>';
    }
echo'
	</tr>
	';
  $j++;
  if ($j%2) { $class = "pair";}else{$class = "impair";} 
	echo '
	<tr class='.$class.'>
    <td align="left">&nbsp;status&nbsp;</td>
    <td align="left"><input name="txt_CHANGEMENT_STATUS" type="text" value="'.stripslashes($CHANGEMENT_STATUS).'" size="50"/></td>
	</tr>
	';
	if($STOP_INFO_CHANGEMENT_STATUS==1){
    echo '
    <tr class="titre">
      <td colspan="2" align="center"><h2>&nbsp;Le status '.$CHANGEMENT_STATUS.' existe d&eacute;j&agrave;, merci de choisir une autre status.&nbsp;</h2></td>
    </tr>';
	}
	if($STOP_INFO_CHANGEMENT_STATUS==2){
    echo '
    <tr class="titre">
      <td colspan="2" align="center"><h2>&nbsp;Impossible de faire la suppression du status '.$CHANGEMENT_STATUS.' car on l\'utilise pour une sous-status.&nbsp;</h2></td>
    </tr>';
	}
	if($STOP_INFO_CHANGEMENT_STATUS==3){
    echo '
    <tr class="titre">
      <td colspan="2" align="center"><h2>&nbsp;Impossible de faire la modification du status '.$CHANGEMENT_STATUS.' car on l\'utilise pour une sous-status.&nbsp;</h2></td>
    </tr>';
	}
	$STOP_INFO_CHANGEMENT_STATUS=0;
	if($page_test==2){
    echo '
    <tr class="titre">
      <td colspan="2" align="center"><h2>&nbsp;Merci de saisir l\'ensemble des Champs.&nbsp;</h2></td>
    </tr>';
	}
echo '
	<tr class="titre">
    <td colspan="2" align="center">
    <h2>';
    if($action=='Ajout'){
    echo '
    <input name="btn" type="submit" id="btn" value="Ajouter">
    <input name="btn" type="submit" id="btn" value="RAZ">
    ';}
    if($action=='Modif'){
      if($aff_modif==0){
        echo '
        <input name="btn" type="submit" id="btn" value="Modifier">
        <input name="btn" type="submit" id="btn" value="Supprimer">
        ';
				}
    echo '
    <input name="btn" type="submit" id="btn" value="RAZ">
    ';} 
    echo '
    <input type="hidden" name="ID" value="'.$ID.'">
    <input type="hidden" name="action" id="action" value="'.$action.'">
    </h2>
    </td>
	</tr>
	<tr class="titre">
    <td colspan="2" align="center"><h2>&nbsp;[&nbsp;<a href="./index.php?ITEM=changement_Gestion_Status">Retour - Liste des statuss</a>&nbsp;]&nbsp;</h2></td>
	</tr>
	</table>
</form>
</div>';

mysql_close($mysql_link); 
?>