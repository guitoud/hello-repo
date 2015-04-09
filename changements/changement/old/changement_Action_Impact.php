<?PHP
//redirection si acces dirrect
if(substr_count($_SERVER['PHP_SELF'], 'index.php')==0){
  header("Location: ../");
  exit();
}
/*******************************************************************
   Interface Ajout impact
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
$STOP_INFO_CHANGEMENT_IMPACT=0;
$CHANGEMENT_IMPACT='';
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
    $CHANGEMENT_IMPACT='';
    $action='Ajout';
    $_GET['ITEM']='changement_Ajout_Impact';
  }
  
  # Cas Ajouter
  if($tab_var['btn']=="Ajouter"){
    $CHANGEMENT_IMPACT=addslashes(trim(htmlentities($tab_var['txt_CHANGEMENT_IMPACT'])));  
    if($CHANGEMENT_IMPACT==''){
      $STOP=1;
    }
    if($STOP==0){
      $rq_impact_info="
      SELECT `CHANGEMENT_IMPACT_ID` 
      FROM `changement_impact`
      WHERE `CHANGEMENT_IMPACT`='".$CHANGEMENT_IMPACT."'
      AND `ENABLE`=0";
      $res_rq_impact_info = mysql_query($rq_impact_info, $mysql_link) or die(mysql_error());
      $tab_rq_impact_info = mysql_fetch_assoc($res_rq_impact_info);
      $total_ligne_rq_impact_info=mysql_num_rows($res_rq_impact_info);
      if($total_ligne_rq_impact_info!=0){
        $STOP_INFO_CHANGEMENT_IMPACT=1;
      }else{
        //ajoute l impact si non presente dans bdd
        
        $sql="
        INSERT INTO `changement_impact`( `CHANGEMENT_IMPACT_ID` , `CHANGEMENT_IMPACT` ,`ENABLE`)
        VALUES ( NULL , '".$CHANGEMENT_IMPACT."','0' );";
        mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
        
        $TABLE_SQL_SQL='changement_impact';       
        historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');
                
        echo '
        <script language="JavaScript">
        url=("./index.php?ITEM=changement_Gestion_Impact");
        window.location=url;
        </script>
        ';
      }
      mysql_free_result($res_rq_impact_info);
    }else{
      if($page_test!=3){
        $page_test=2;
      }
    }
  }
  
  # Cas Supprimer
  if($tab_var['btn']=="Supprimer"){
      $rq_impact_info="
      SELECT `CHANGEMENT_IMPACT_ID` 
      FROM `changement_liste` 
      WHERE `CHANGEMENT_IMPACT_ID`='".$ID."'";
      $res_rq_impact_info = mysql_query($rq_impact_info, $mysql_link) or die(mysql_error());
      $tab_rq_impact_info = mysql_fetch_assoc($res_rq_impact_info);
      $total_ligne_rq_impact_info=mysql_num_rows($res_rq_impact_info);
      mysql_free_result($res_rq_impact_info);
      if($total_ligne_rq_impact_info!=0){
        $STOP_INFO_CHANGEMENT_IMPACT=2;
        $rq_impact_info="
        SELECT `CHANGEMENT_IMPACT_ID` FROM `changement_impact` WHERE `CHANGEMENT_IMPACT_ID`='".$ID."'";
        $res_rq_impact_info = mysql_query($rq_impact_info, $mysql_link) or die(mysql_error());
        $tab_rq_impact_info = mysql_fetch_assoc($res_rq_impact_info);
        $total_ligne_rq_impact_info=mysql_num_rows($res_rq_impact_info);
        $CHANGEMENT_IMPACT=$tab_rq_impact_info['CHANGEMENT_IMPACT'];
      }else{
        //supprime l\'impact si pas d utilisation de celle-ci
        
        $sql="UPDATE `changement_impact` SET `ENABLE` = '1' WHERE `CHANGEMENT_IMPACT_ID` ='".$ID."' LIMIT 1 ;";
        mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
        
        $TABLE_SQL_SQL='changement_impact';       
        historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');
       
        $sql="OPTIMIZE TABLE `changement_impact`";
        mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
        
        $TABLE_SQL_SQL='changement_impact';       
        historique_sql_new($sql,$TABLE_SQL_SQL,'OPTIMIZE');
        
        echo '
        <script language="JavaScript">
        url=("./index.php?ITEM=changement_Gestion_Impact");
        window.location=url;
        </script>
        ';
      }
      
  }
  # Cas Modifier
  if($tab_var['btn']=="Modifier"){
    $CHANGEMENT_IMPACT=addslashes(trim(htmlentities($tab_var['txt_CHANGEMENT_IMPACT'])));

    if($CHANGEMENT_IMPACT==''){
      $STOP=1;
    }
    if($STOP==0){
      $rq_impact_info="
      SELECT `CHANGEMENT_IMPACT_ID` 
      FROM `changement_liste` 
      WHERE `CHANGEMENT_IMPACT_ID`='".$ID."'";
      $res_rq_impact_info = mysql_query($rq_impact_info, $mysql_link) or die(mysql_error());
      $tab_rq_impact_info = mysql_fetch_assoc($res_rq_impact_info);
      $total_ligne_rq_impact_info=mysql_num_rows($res_rq_impact_info);
      mysql_free_result($res_rq_impact_info);
      if($total_ligne_rq_impact_info!=0){
        $STOP_INFO_CHANGEMENT_IMPACT=3;
        $rq_impact_info="
        SELECT `CHANGEMENT_IMPACT_ID` FROM `changement_impact` WHERE `CHANGEMENT_IMPACT_ID`='".$ID."'";
        $res_rq_impact_info = mysql_query($rq_impact_info, $mysql_link) or die(mysql_error());
        $tab_rq_impact_info = mysql_fetch_assoc($res_rq_impact_info);
        $total_ligne_rq_impact_info=mysql_num_rows($res_rq_impact_info);
        $CHANGEMENT_IMPACT=$tab_rq_impact_info['CHANGEMENT_IMPACT'];
      }else{
        $rq_impact_info="
        SELECT `CHANGEMENT_IMPACT_ID` FROM `changement_impact` WHERE `CHANGEMENT_IMPACT_ID`='".$ID."'";
        $res_rq_impact_info = mysql_query($rq_impact_info, $mysql_link) or die(mysql_error());
        $tab_rq_impact_info = mysql_fetch_assoc($res_rq_impact_info);
        $total_ligne_rq_impact_info=mysql_num_rows($res_rq_impact_info);
        if($total_ligne_rq_impact_info!=0){     
          $sql="
          UPDATE `changement_impact` SET 
          `CHANGEMENT_IMPACT` = '".$CHANGEMENT_IMPACT."'
          WHERE `CHANGEMENT_IMPACT_ID` ='".$ID."' LIMIT 1 ;";
          mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());     
          
          $TABLE_SQL_SQL='changement_impact';       
          historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');
                    
          echo '
          <script language="JavaScript">
          url=("./index.php?ITEM=changement_Gestion_Impact");
          window.location=url;
          </script>
          ';
          }
      }
      mysql_free_result($res_rq_impact_info);
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
      $rq_impact_info="
      SELECT `CHANGEMENT_IMPACT_ID` , `CHANGEMENT_IMPACT`
      FROM `changement_impact`
      WHERE `CHANGEMENT_IMPACT_ID`='".$ID."'";
      $res_rq_impact_info = mysql_query($rq_impact_info, $mysql_link) or die(mysql_error());
      $tab_rq_impact_info = mysql_fetch_assoc($res_rq_impact_info);
      $total_ligne_rq_impact_info=mysql_num_rows($res_rq_impact_info);
      $CHANGEMENT_IMPACT=$tab_rq_impact_info['CHANGEMENT_IMPACT'];
    }
  }
  if(isset($_POST['ID'])){
    if(is_numeric($_POST['ID'])){
      $ID=$_POST['ID'];
      $rq_impact_info="
      SELECT `CHANGEMENT_IMPACT_ID` , `CHANGEMENT_IMPACT`
      FROM `changement_impact`
      WHERE `CHANGEMENT_IMPACT_ID`='".$ID."'";
      $res_rq_impact_info = mysql_query($rq_impact_info, $mysql_link) or die(mysql_error());
      $tab_rq_impact_info = mysql_fetch_assoc($res_rq_impact_info);
      $total_ligne_rq_impact_info=mysql_num_rows($res_rq_impact_info);
      $CHANGEMENT_IMPACT=$tab_rq_impact_info['CHANGEMENT_IMPACT'];
    }
  }
  $rq_impact_info="
  SELECT `CHANGEMENT_IMPACT_ID` 
  FROM `changement_liste` 
  WHERE `CHANGEMENT_IMPACT_ID`='".$ID."'";
  $res_rq_impact_info = mysql_query($rq_impact_info, $mysql_link) or die(mysql_error());
  $tab_rq_impact_info = mysql_fetch_assoc($res_rq_impact_info);
  $total_ligne_rq_impact_info=mysql_num_rows($res_rq_impact_info);
  if($total_ligne_rq_impact_info!=0){
    $aff_modif=1;
  }else{
    $aff_modif=0;
  }
  mysql_free_result($res_rq_impact_info);
}
echo '
<!--Début page HTML -->  
<div align="center">

<form method="post" name="frm_impact" id="frm_impact" action="index.php?ITEM='.$_GET['ITEM'].'">
<table class="table_inc">
	<tr align="center" class="titre">';
    if($action=="Ajout"){
      echo '
				<td colspan="2"><h2>&nbsp;[&nbsp;Ajout d\'un impact&nbsp;]&nbsp;</h2></td>';
    }
    if($action=="Modif"){
      echo '
				<td colspan="2">
				<h2>&nbsp;[&nbsp;Modification d\'un impact&nbsp;]&nbsp;</h2>
				';
				if($aff_modif==1){
          echo '&nbsp;Pas d\'action possible car on utilise l\'impact&nbsp;';
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
    <td align="left">&nbsp;impact&nbsp;</td>
    <td align="left"><input name="txt_CHANGEMENT_IMPACT" type="text" value="'.stripslashes($CHANGEMENT_IMPACT).'" size="50"/></td>
	</tr>
	';
	if($STOP_INFO_CHANGEMENT_IMPACT==1){
    echo '
    <tr class="titre">
      <td colspan="2" align="center"><h2>&nbsp;L\'impact '.$CHANGEMENT_IMPACT.' existe d&eacute;j&agrave;, merci de choisir une autre impact.&nbsp;</h2></td>
    </tr>';
	}
	if($STOP_INFO_CHANGEMENT_IMPACT==2){
    echo '
    <tr class="titre">
      <td colspan="2" align="center"><h2>&nbsp;Impossible de faire la suppression de l\'impact '.$CHANGEMENT_IMPACT.' car on l\'utilise pour une sous-impact.&nbsp;</h2></td>
    </tr>';
	}
	if($STOP_INFO_CHANGEMENT_IMPACT==3){
    echo '
    <tr class="titre">
      <td colspan="2" align="center"><h2>&nbsp;Impossible de faire la modification de l\'impact '.$CHANGEMENT_IMPACT.' car on l\'utilise pour une sous-impact.&nbsp;</h2></td>
    </tr>';
	}
	$STOP_INFO_CHANGEMENT_IMPACT=0;
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
    <td colspan="2" align="center"><h2>&nbsp;[&nbsp;<a href="./index.php?ITEM=changement_Gestion_Impact">Retour - Liste des impacts</a>&nbsp;]&nbsp;</h2></td>
	</tr>
	</table>
</form>
<br/><br/>
</div>';

mysql_close(); 
?>