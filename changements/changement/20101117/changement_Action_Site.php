<?PHP
//redirection si acces dirrect
if(substr_count($_SERVER['PHP_SELF'], 'index.php')==0){
  header("Location: ../");
  exit();
}
/*******************************************************************
   Interface Ajout site
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
$STOP_INFO_CHANGEMENT_SITE=0;
$CHANGEMENT_SITE='';
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
    $CHANGEMENT_SITE='';
    $action='Ajout';
    $_GET['ITEM']='changement_Ajout_Site';
  }
  
  # Cas Ajouter
  if($tab_var['btn']=="Ajouter"){
    $CHANGEMENT_SITE=addslashes(trim(htmlentities($tab_var['txt_CHANGEMENT_SITE'])));  
    if($CHANGEMENT_SITE==''){
      $STOP=1;
    }
    if($STOP==0){
      $rq_site_info="
      SELECT `CHANGEMENT_SITE_ID` 
      FROM `changement_site`
      WHERE `CHANGEMENT_SITE`='".$CHANGEMENT_SITE."'
      AND `ENABLE`=0";
      $res_rq_site_info = mysql_query($rq_site_info, $mysql_link) or die(mysql_error());
      $tab_rq_site_info = mysql_fetch_assoc($res_rq_site_info);
      $total_ligne_rq_site_info=mysql_num_rows($res_rq_site_info);
      if($total_ligne_rq_site_info!=0){
        $STOP_INFO_CHANGEMENT_SITE=1;
      }else{
        //ajoute l site si non presente dans bdd
        
        $sql="
        INSERT INTO `changement_site`( `CHANGEMENT_SITE_ID` , `CHANGEMENT_SITE` ,`ENABLE`)
        VALUES ( NULL , '".$CHANGEMENT_SITE."','0' );";
        mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
        
        $TABLE_SQL_SQL='changement_site';       
        historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');
                
        echo '
        <script language="JavaScript">
        url=("./index.php?ITEM=changement_Gestion_Site");
        window.location=url;
        </script>
        ';
      }
      mysql_free_result($res_rq_site_info);
    }else{
      if($page_test!=3){
        $page_test=2;
      }
    }
  }
  
  # Cas Supprimer
  if($tab_var['btn']=="Supprimer"){
      $rq_site_info="
      SELECT `CHANGEMENT_FAR_ID` 
      FROM `changement_far`
      WHERE `CHANGEMENT_FAR_CONFIG_ID` IN (
      SELECT `CHANGEMENT_FAR_CONFIG_ID` 
      FROM `changement_far_config` 
      WHERE `CHANGEMENT_FAR_CONFIG_TABLE` = 'changement_site'
      )
      AND `CHANGEMENT_FAR_INFO_AUTRE_ID`='".$ID."'";
      $res_rq_site_info = mysql_query($rq_site_info, $mysql_link) or die(mysql_error());
      $tab_rq_site_info = mysql_fetch_assoc($res_rq_site_info);
      $total_ligne_rq_site_info=mysql_num_rows($res_rq_site_info);
      mysql_free_result($res_rq_site_info);
      if($total_ligne_rq_site_info!=0){
        $STOP_INFO_CHANGEMENT_SITE=2;
        $rq_site_info="
        SELECT `CHANGEMENT_SITE_ID` FROM `changement_site`WHERE `CHANGEMENT_SITE_ID`='".$ID."'";
        $res_rq_site_info = mysql_query($rq_site_info, $mysql_link) or die(mysql_error());
        $tab_rq_site_info = mysql_fetch_assoc($res_rq_site_info);
        $total_ligne_rq_site_info=mysql_num_rows($res_rq_site_info);
        $CHANGEMENT_SITE=$tab_rq_site_info['CHANGEMENT_SITE'];
      }else{
        //supprime la site si pas d utilisation de celle-ci
        
        $sql="UPDATE `changement_site` SET `ENABLE` = '1' WHERE `CHANGEMENT_SITE_ID` ='".$ID."' LIMIT 1 ;";
        mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
        
        $TABLE_SQL_SQL='changement_site';       
        historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');
       
        $sql="OPTIMIZE TABLE `changement_site`";
        mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
        
        $TABLE_SQL_SQL='changement_site';       
        historique_sql_new($sql,$TABLE_SQL_SQL,'OPTIMIZE');
        
        echo '
        <script language="JavaScript">
        url=("./index.php?ITEM=changement_Gestion_Site");
        window.location=url;
        </script>
        ';
      }
      
  }
  # Cas Modifier
  if($tab_var['btn']=="Modifier"){
    $CHANGEMENT_SITE=addslashes(trim(htmlentities($tab_var['txt_CHANGEMENT_SITE'])));

    if($CHANGEMENT_SITE==''){
      $STOP=1;
    }
    if($STOP==0){
      $rq_site_info="
      SELECT `CHANGEMENT_FAR_ID` 
      FROM `changement_far`
      WHERE `CHANGEMENT_FAR_CONFIG_ID` IN (
      SELECT `CHANGEMENT_FAR_CONFIG_ID` 
      FROM `changement_far_config` 
      WHERE `CHANGEMENT_FAR_CONFIG_TABLE` = 'changement_site'
      )
      AND `CHANGEMENT_FAR_INFO_AUTRE_ID`='".$ID."'";
      $res_rq_site_info = mysql_query($rq_site_info, $mysql_link) or die(mysql_error());
      $tab_rq_site_info = mysql_fetch_assoc($res_rq_site_info);
      $total_ligne_rq_site_info=mysql_num_rows($res_rq_site_info);
      mysql_free_result($res_rq_site_info);
      if($total_ligne_rq_site_info!=0){
        $STOP_INFO_CHANGEMENT_SITE=3;
        $rq_site_info="
        SELECT `CHANGEMENT_SITE_ID` FROM `changement_site`WHERE `CHANGEMENT_SITE_ID`='".$ID."'";
        $res_rq_site_info = mysql_query($rq_site_info, $mysql_link) or die(mysql_error());
        $tab_rq_site_info = mysql_fetch_assoc($res_rq_site_info);
        $total_ligne_rq_site_info=mysql_num_rows($res_rq_site_info);
        $CHANGEMENT_SITE=$tab_rq_site_info['CHANGEMENT_SITE'];
      }else{
        $rq_site_info="
        SELECT `CHANGEMENT_SITE_ID` FROM `changement_site`WHERE `CHANGEMENT_SITE_ID`='".$ID."'";
        $res_rq_site_info = mysql_query($rq_site_info, $mysql_link) or die(mysql_error());
        $tab_rq_site_info = mysql_fetch_assoc($res_rq_site_info);
        $total_ligne_rq_site_info=mysql_num_rows($res_rq_site_info);
        if($total_ligne_rq_site_info!=0){     
          $sql="
          UPDATE `changement_site` SET 
          `CHANGEMENT_SITE` = '".$CHANGEMENT_SITE."'
          WHERE `CHANGEMENT_SITE_ID` ='".$ID."' LIMIT 1 ;";
          mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());     
          
          $TABLE_SQL_SQL='changement_site';       
          historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');
                    
          echo '
          <script language="JavaScript">
          url=("./index.php?ITEM=changement_Gestion_Site");
          window.location=url;
          </script>
          ';
          }
      }
      mysql_free_result($res_rq_site_info);
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
      $rq_site_info="
      SELECT `CHANGEMENT_SITE_ID` , `CHANGEMENT_SITE`
      FROM `changement_site`
      WHERE `CHANGEMENT_SITE_ID`='".$ID."'";
      $res_rq_site_info = mysql_query($rq_site_info, $mysql_link) or die(mysql_error());
      $tab_rq_site_info = mysql_fetch_assoc($res_rq_site_info);
      $total_ligne_rq_site_info=mysql_num_rows($res_rq_site_info);
      $CHANGEMENT_SITE=$tab_rq_site_info['CHANGEMENT_SITE'];
    }
  }
  if(isset($_POST['ID'])){
    if(is_numeric($_POST['ID'])){
      $ID=$_POST['ID'];
      $rq_site_info="
      SELECT `CHANGEMENT_SITE_ID` , `CHANGEMENT_SITE`
      FROM `changement_site`
      WHERE `CHANGEMENT_SITE_ID`='".$ID."'";
      $res_rq_site_info = mysql_query($rq_site_info, $mysql_link) or die(mysql_error());
      $tab_rq_site_info = mysql_fetch_assoc($res_rq_site_info);
      $total_ligne_rq_site_info=mysql_num_rows($res_rq_site_info);
      $CHANGEMENT_SITE=$tab_rq_site_info['CHANGEMENT_SITE'];
    }
  }
  $rq_site_info="
  SELECT `CHANGEMENT_FAR_ID` 
  FROM `changement_far`
  WHERE `CHANGEMENT_FAR_CONFIG_ID` IN (
  SELECT `CHANGEMENT_FAR_CONFIG_ID` 
  FROM `changement_far_config` 
  WHERE `CHANGEMENT_FAR_CONFIG_TABLE` = 'changement_site'
  )
  AND `CHANGEMENT_FAR_INFO_AUTRE_ID`='".$ID."'";
  $res_rq_site_info = mysql_query($rq_site_info, $mysql_link) or die(mysql_error());
  $tab_rq_site_info = mysql_fetch_assoc($res_rq_site_info);
  $total_ligne_rq_site_info=mysql_num_rows($res_rq_site_info);
  if($total_ligne_rq_site_info!=0){
    $aff_modif=1;
  }else{
    $aff_modif=0;
  }
  mysql_free_result($res_rq_site_info);
}
echo '
<!--Début page HTML -->  
<div align="center">

<form method="post" name="frm_site" id="frm_site" action="index.php?ITEM='.$_GET['ITEM'].'">
<table class="table_inc">
	<tr align="center" class="titre">';
    if($action=="Ajout"){
      echo '
				<td colspan="2"><h2>&nbsp;[&nbsp;Ajout d\'un site&nbsp;]&nbsp;</h2></td>';
    }
    if($action=="Modif"){
      echo '
				<td colspan="2">
				<h2>&nbsp;[&nbsp;Modification d\'un site&nbsp;]&nbsp;</h2>
				';
				if($aff_modif==1){
          echo '&nbsp;Pas d\'action possible car on utilise le site&nbsp;';
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
    <td align="left">&nbsp;site&nbsp;</td>
    <td align="left"><input name="txt_CHANGEMENT_SITE" type="text" value="'.stripslashes($CHANGEMENT_SITE).'" size="50"/></td>
	</tr>
	';
	if($STOP_INFO_CHANGEMENT_SITE==1){
    echo '
    <tr class="titre">
      <td colspan="2" align="center"><h2>&nbsp;Le site '.$CHANGEMENT_SITE.' existe d&eacute;j&agrave;, merci de choisir une autre site.&nbsp;</h2></td>
    </tr>';
	}
	if($STOP_INFO_CHANGEMENT_SITE==2){
    echo '
    <tr class="titre">
      <td colspan="2" align="center"><h2>&nbsp;Impossible de faire la suppression du site '.$CHANGEMENT_SITE.' car on l\'utilise pour une sous-site.&nbsp;</h2></td>
    </tr>';
	}
	if($STOP_INFO_CHANGEMENT_SITE==3){
    echo '
    <tr class="titre">
      <td colspan="2" align="center"><h2>&nbsp;Impossible de faire la modification du site '.$CHANGEMENT_SITE.' car on l\'utilise pour une sous-site.&nbsp;</h2></td>
    </tr>';
	}
	$STOP_INFO_CHANGEMENT_SITE=0;
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
    <td colspan="2" align="center"><h2>&nbsp;[&nbsp;<a href="./index.php?ITEM=changement_Gestion_Site">Retour - Liste des sites</a>&nbsp;]&nbsp;</h2></td>
	</tr>
	</table>
</form>
</div>';

mysql_close($mysql_link); 
?>