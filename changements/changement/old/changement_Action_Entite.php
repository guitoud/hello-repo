<?PHP
//redirection si acces dirrect
if(substr_count($_SERVER['PHP_SELF'], 'index.php')==0){
  header("Location: ../");
  exit();
}
/*******************************************************************
   Interface Ajout entite
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
$STOP_INFO_CHANGEMENT_ENTITE=0;
$CHANGEMENT_ENTITE='';
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
    $CHANGEMENT_ENTITE='';
    $action='Ajout';
    $_GET['ITEM']='changement_Ajout_Entitee';
  }
  
  # Cas Ajouter
  if($tab_var['btn']=="Ajouter"){
    $CHANGEMENT_ENTITE=addslashes(trim(htmlentities($tab_var['txt_CHANGEMENT_ENTITE'])));  
    if($CHANGEMENT_ENTITE==''){
      $STOP=1;
    }
    if($STOP==0){
      $rq_entite_info="
      SELECT `CHANGEMENT_ENTITE_ID` 
      FROM `changement_entite`
      WHERE `CHANGEMENT_ENTITE`='".$CHANGEMENT_ENTITE."'
      AND `ENABLE`=0";
      $res_rq_entite_info = mysql_query($rq_entite_info, $mysql_link) or die(mysql_error());
      $tab_rq_entite_info = mysql_fetch_assoc($res_rq_entite_info);
      $total_ligne_rq_entite_info=mysql_num_rows($res_rq_entite_info);
      if($total_ligne_rq_entite_info!=0){
        $STOP_INFO_CHANGEMENT_ENTITE=1;
      }else{
        //ajoute l entite si non presente dans bdd
        
        $sql="
        INSERT INTO `changement_entite`( `CHANGEMENT_ENTITE_ID` , `CHANGEMENT_ENTITE` ,`ENABLE`)
        VALUES ( NULL , '".$CHANGEMENT_ENTITE."','0' );";
        mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
        
        $TABLE_SQL_SQL='changement_entite';       
        historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');
                
        echo '
        <script language="JavaScript">
        url=("./index.php?ITEM=changement_Gestion_Entitee");
        window.location=url;
        </script>
        ';
      }
      mysql_free_result($res_rq_entite_info);
    }else{
      if($page_test!=3){
        $page_test=2;
      }
    }
  }
  
  # Cas Supprimer
  if($tab_var['btn']=="Supprimer"){
      $rq_entite_info="
      SELECT `CHANGEMENT_ENTITE_ID` 
      FROM `changement_liste` 
      WHERE `CHANGEMENT_ENTITE_ID`='".$ID."'";
      $res_rq_entite_info = mysql_query($rq_entite_info, $mysql_link) or die(mysql_error());
      $tab_rq_entite_info = mysql_fetch_assoc($res_rq_entite_info);
      $total_ligne_rq_entite_info=mysql_num_rows($res_rq_entite_info);
      mysql_free_result($res_rq_entite_info);
      if($total_ligne_rq_entite_info!=0){
        $STOP_INFO_CHANGEMENT_ENTITE=2;
        $rq_entite_info="
        SELECT `CHANGEMENT_ENTITE_ID` FROM `changement_entite` WHERE `CHANGEMENT_ENTITE_ID`='".$ID."'";
        $res_rq_entite_info = mysql_query($rq_entite_info, $mysql_link) or die(mysql_error());
        $tab_rq_entite_info = mysql_fetch_assoc($res_rq_entite_info);
        $total_ligne_rq_entite_info=mysql_num_rows($res_rq_entite_info);
        $CHANGEMENT_ENTITE=$tab_rq_entite_info['CHANGEMENT_ENTITE'];
      }else{
        //supprime l\'entit&eacute;esi pas d utilisation de celle-ci
        
        $sql="UPDATE `changement_entite` SET `ENABLE` = '1' WHERE `CHANGEMENT_ENTITE_ID` ='".$ID."' LIMIT 1 ;";
        mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
        
        $TABLE_SQL_SQL='changement_entite';       
        historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');
       
        $sql="OPTIMIZE TABLE `changement_entite`";
        mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
        
        $TABLE_SQL_SQL='changement_entite';       
        historique_sql_new($sql,$TABLE_SQL_SQL,'OPTIMIZE');
        
        echo '
        <script language="JavaScript">
        url=("./index.php?ITEM=changement_Gestion_Entitee");
        window.location=url;
        </script>
        ';
      }
      
  }
  # Cas Modifier
  if($tab_var['btn']=="Modifier"){
    $CHANGEMENT_ENTITE=addslashes(trim(htmlentities($tab_var['txt_CHANGEMENT_ENTITE'])));

    if($CHANGEMENT_ENTITE==''){
      $STOP=1;
    }
    if($STOP==0){
      $rq_entite_info="
      SELECT `CHANGEMENT_ENTITE_ID` 
      FROM `changement_liste` 
      WHERE `CHANGEMENT_ENTITE_ID`='".$ID."'";
      $res_rq_entite_info = mysql_query($rq_entite_info, $mysql_link) or die(mysql_error());
      $tab_rq_entite_info = mysql_fetch_assoc($res_rq_entite_info);
      $total_ligne_rq_entite_info=mysql_num_rows($res_rq_entite_info);
      mysql_free_result($res_rq_entite_info);
      if($total_ligne_rq_entite_info!=0){
        $STOP_INFO_CHANGEMENT_ENTITE=3;
        $rq_entite_info="
        SELECT `CHANGEMENT_ENTITE_ID` FROM `changement_entite` WHERE `CHANGEMENT_ENTITE_ID`='".$ID."'";
        $res_rq_entite_info = mysql_query($rq_entite_info, $mysql_link) or die(mysql_error());
        $tab_rq_entite_info = mysql_fetch_assoc($res_rq_entite_info);
        $total_ligne_rq_entite_info=mysql_num_rows($res_rq_entite_info);
        $CHANGEMENT_ENTITE=$tab_rq_entite_info['CHANGEMENT_ENTITE'];
      }else{
        $rq_entite_info="
        SELECT `CHANGEMENT_ENTITE_ID` FROM `changement_entite` WHERE `CHANGEMENT_ENTITE_ID`='".$ID."'";
        $res_rq_entite_info = mysql_query($rq_entite_info, $mysql_link) or die(mysql_error());
        $tab_rq_entite_info = mysql_fetch_assoc($res_rq_entite_info);
        $total_ligne_rq_entite_info=mysql_num_rows($res_rq_entite_info);
        if($total_ligne_rq_entite_info!=0){     
          $sql="
          UPDATE `changement_entite` SET 
          `CHANGEMENT_ENTITE` = '".$CHANGEMENT_ENTITE."'
          WHERE `CHANGEMENT_ENTITE_ID` ='".$ID."' LIMIT 1 ;";
          mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());     
          
          $TABLE_SQL_SQL='changement_entite';       
          historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');
                    
          echo '
          <script language="JavaScript">
          url=("./index.php?ITEM=changement_Gestion_Entitee");
          window.location=url;
          </script>
          ';
          }
      }
      mysql_free_result($res_rq_entite_info);
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
      $rq_entite_info="
      SELECT `CHANGEMENT_ENTITE_ID` , `CHANGEMENT_ENTITE`
      FROM `changement_entite`
      WHERE `CHANGEMENT_ENTITE_ID`='".$ID."'";
      $res_rq_entite_info = mysql_query($rq_entite_info, $mysql_link) or die(mysql_error());
      $tab_rq_entite_info = mysql_fetch_assoc($res_rq_entite_info);
      $total_ligne_rq_entite_info=mysql_num_rows($res_rq_entite_info);
      $CHANGEMENT_ENTITE=$tab_rq_entite_info['CHANGEMENT_ENTITE'];
    }
  }
  if(isset($_POST['ID'])){
    if(is_numeric($_POST['ID'])){
      $ID=$_POST['ID'];
      $rq_entite_info="
      SELECT `CHANGEMENT_ENTITE_ID` , `CHANGEMENT_ENTITE`
      FROM `changement_entite`
      WHERE `CHANGEMENT_ENTITE_ID`='".$ID."'";
      $res_rq_entite_info = mysql_query($rq_entite_info, $mysql_link) or die(mysql_error());
      $tab_rq_entite_info = mysql_fetch_assoc($res_rq_entite_info);
      $total_ligne_rq_entite_info=mysql_num_rows($res_rq_entite_info);
      $CHANGEMENT_ENTITE=$tab_rq_entite_info['CHANGEMENT_ENTITE'];
    }
  }
  $rq_entite_info="
  SELECT `CHANGEMENT_ENTITE_ID` 
  FROM `changement_liste` 
  WHERE `CHANGEMENT_ENTITE_ID`='".$ID."'";
  $res_rq_entite_info = mysql_query($rq_entite_info, $mysql_link) or die(mysql_error());
  $tab_rq_entite_info = mysql_fetch_assoc($res_rq_entite_info);
  $total_ligne_rq_entite_info=mysql_num_rows($res_rq_entite_info);
  if($total_ligne_rq_entite_info!=0){
    $aff_modif=1;
  }else{
    $aff_modif=0;
  }
  mysql_free_result($res_rq_entite_info);
}
echo '
<!--Début page HTML -->  
<div align="center">

<form method="post" name="frm_entite" id="frm_entite" action="index.php?ITEM='.$_GET['ITEM'].'">
<table class="table_inc">
	<tr align="center" class="titre">';
    if($action=="Ajout"){
      echo '
				<td colspan="2"><h2>&nbsp;[&nbsp;Ajout d\'une entit&eacute;e&nbsp;]&nbsp;</h2></td>';
    }
    if($action=="Modif"){
      echo '
				<td colspan="2">
				<h2>&nbsp;[&nbsp;Modification d\'une entit&eacute;e&nbsp;]&nbsp;</h2>
				';
				if($aff_modif==1){
          echo '&nbsp;Pas d\'action possible car on utilise l\'entit&eacute;e&nbsp;';
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
    <td align="left">&nbsp;entite&nbsp;</td>
    <td align="left"><input name="txt_CHANGEMENT_ENTITE" type="text" value="'.stripslashes($CHANGEMENT_ENTITE).'" size="50"/></td>
	</tr>
	';
	if($STOP_INFO_CHANGEMENT_ENTITE==1){
    echo '
    <tr class="titre">
      <td colspan="2" align="center"><h2>&nbsp;L\'entit&eacute;e '.$CHANGEMENT_ENTITE.' existe d&eacute;j&agrave;, merci de choisir une autre entite.&nbsp;</h2></td>
    </tr>';
	}
	if($STOP_INFO_CHANGEMENT_ENTITE==2){
    echo '
    <tr class="titre">
      <td colspan="2" align="center"><h2>&nbsp;Impossible de faire la suppression de l\'entit&eacute;e'.$CHANGEMENT_ENTITE.' car on l\'utilise pour une sous-entite.&nbsp;</h2></td>
    </tr>';
	}
	if($STOP_INFO_CHANGEMENT_ENTITE==3){
    echo '
    <tr class="titre">
      <td colspan="2" align="center"><h2>&nbsp;Impossible de faire la modification de l\'entit&eacute;e'.$CHANGEMENT_ENTITE.' car on l\'utilise pour une sous-entite.&nbsp;</h2></td>
    </tr>';
	}
	$STOP_INFO_CHANGEMENT_ENTITE=0;
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
    <td colspan="2" align="center"><h2>&nbsp;[&nbsp;<a href="./index.php?ITEM=changement_Gestion_Entitee">Retour - Liste des entit&eacute;es</a>&nbsp;]&nbsp;</h2></td>
	</tr>
	</table>
</form>
<br/><br/>
</div>';

mysql_close(); 
?>