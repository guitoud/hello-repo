<?PHP
//redirection si acces dirrect
if(substr_count($_SERVER['PHP_SELF'], 'index.php')==0){
  header("Location: ../");
  exit();
}
/*******************************************************************
   Interface Ajout perimetre
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
$STOP_INFO_CHANGEMENT_PERIMETRE=0;
$CHANGEMENT_PERIMETRE='';
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
    $CHANGEMENT_PERIMETRE='';
    $action='Ajout';
    $_GET['ITEM']='changement_Ajout_Perimetre';
  }
  
  # Cas Ajouter
  if($tab_var['btn']=="Ajouter"){
    $CHANGEMENT_PERIMETRE=addslashes(trim(htmlentities($tab_var['txt_CHANGEMENT_PERIMETRE'])));  
    if($CHANGEMENT_PERIMETRE==''){
      $STOP=1;
    }
    if($STOP==0){
      $rq_perimetre_info="
      SELECT `CHANGEMENT_PERIMETRE_ID` 
      FROM `changement_perimetre`
      WHERE `CHANGEMENT_PERIMETRE`='".$CHANGEMENT_PERIMETRE."'
      AND `ENABLE`=0";
      $res_rq_perimetre_info = mysql_query($rq_perimetre_info, $mysql_link) or die(mysql_error());
      $tab_rq_perimetre_info = mysql_fetch_assoc($res_rq_perimetre_info);
      $total_ligne_rq_perimetre_info=mysql_num_rows($res_rq_perimetre_info);
      if($total_ligne_rq_perimetre_info!=0){
        $STOP_INFO_CHANGEMENT_PERIMETRE=1;
      }else{
        //ajoute l perimetre si non presente dans bdd
        
        $sql="
        INSERT INTO `changement_perimetre`( `CHANGEMENT_PERIMETRE_ID` , `CHANGEMENT_PERIMETRE` ,`ENABLE`)
        VALUES ( NULL , '".$CHANGEMENT_PERIMETRE."','0' );";
        mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
        
        $TABLE_SQL_SQL='changement_perimetre';       
        historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');
                
        echo '
        <script language="JavaScript">
        url=("./index.php?ITEM=changement_Gestion_Perimetre");
        window.location=url;
        </script>
        ';
      }
      mysql_free_result($res_rq_perimetre_info);
    }else{
      if($page_test!=3){
        $page_test=2;
      }
    }
  }
  
  # Cas Supprimer
  if($tab_var['btn']=="Supprimer"){
      $rq_perimetre_info="
      SELECT `CHANGEMENT_PERIMETRE_ID` 
      FROM `changement_liste` 
      WHERE `CHANGEMENT_PERIMETRE_ID`='".$ID."'";
      $res_rq_perimetre_info = mysql_query($rq_perimetre_info, $mysql_link) or die(mysql_error());
      $tab_rq_perimetre_info = mysql_fetch_assoc($res_rq_perimetre_info);
      $total_ligne_rq_perimetre_info=mysql_num_rows($res_rq_perimetre_info);
      mysql_free_result($res_rq_perimetre_info);
      if($total_ligne_rq_perimetre_info!=0){
        $STOP_INFO_CHANGEMENT_PERIMETRE=2;
        $rq_perimetre_info="
        SELECT `CHANGEMENT_PERIMETRE_ID` FROM `changement_perimetre`WHERE `CHANGEMENT_PERIMETRE_ID`='".$ID."'";
        $res_rq_perimetre_info = mysql_query($rq_perimetre_info, $mysql_link) or die(mysql_error());
        $tab_rq_perimetre_info = mysql_fetch_assoc($res_rq_perimetre_info);
        $total_ligne_rq_perimetre_info=mysql_num_rows($res_rq_perimetre_info);
        $CHANGEMENT_PERIMETRE=$tab_rq_perimetre_info['CHANGEMENT_PERIMETRE'];
      }else{
        //supprime la perimetre si pas d utilisation de celle-ci
        
        $sql="UPDATE `changement_perimetre` SET `ENABLE` = '1' WHERE `CHANGEMENT_PERIMETRE_ID` ='".$ID."' LIMIT 1 ;";
        mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
        
        $TABLE_SQL_SQL='changement_perimetre';       
        historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');
       
        $sql="OPTIMIZE TABLE `changement_perimetre`";
        mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
        
        $TABLE_SQL_SQL='changement_perimetre';       
        historique_sql_new($sql,$TABLE_SQL_SQL,'OPTIMIZE');
        
        echo '
        <script language="JavaScript">
        url=("./index.php?ITEM=changement_Gestion_Perimetre");
        window.location=url;
        </script>
        ';
      }
      
  }
  # Cas Modifier
  if($tab_var['btn']=="Modifier"){
    $CHANGEMENT_PERIMETRE=addslashes(trim(htmlentities($tab_var['txt_CHANGEMENT_PERIMETRE'])));

    if($CHANGEMENT_PERIMETRE==''){
      $STOP=1;
    }
    if($STOP==0){
      $rq_perimetre_info="
      SELECT `CHANGEMENT_PERIMETRE_ID` 
      FROM `changement_liste` 
      WHERE `CHANGEMENT_PERIMETRE_ID`='".$ID."'";
      $res_rq_perimetre_info = mysql_query($rq_perimetre_info, $mysql_link) or die(mysql_error());
      $tab_rq_perimetre_info = mysql_fetch_assoc($res_rq_perimetre_info);
      $total_ligne_rq_perimetre_info=mysql_num_rows($res_rq_perimetre_info);
      mysql_free_result($res_rq_perimetre_info);
      if($total_ligne_rq_perimetre_info!=0){
        $STOP_INFO_CHANGEMENT_PERIMETRE=3;
        $rq_perimetre_info="
        SELECT `CHANGEMENT_PERIMETRE_ID` FROM `changement_perimetre`WHERE `CHANGEMENT_PERIMETRE_ID`='".$ID."'";
        $res_rq_perimetre_info = mysql_query($rq_perimetre_info, $mysql_link) or die(mysql_error());
        $tab_rq_perimetre_info = mysql_fetch_assoc($res_rq_perimetre_info);
        $total_ligne_rq_perimetre_info=mysql_num_rows($res_rq_perimetre_info);
        $CHANGEMENT_PERIMETRE=$tab_rq_perimetre_info['CHANGEMENT_PERIMETRE'];
      }else{
        $rq_perimetre_info="
        SELECT `CHANGEMENT_PERIMETRE_ID` FROM `changement_perimetre`WHERE `CHANGEMENT_PERIMETRE_ID`='".$ID."'";
        $res_rq_perimetre_info = mysql_query($rq_perimetre_info, $mysql_link) or die(mysql_error());
        $tab_rq_perimetre_info = mysql_fetch_assoc($res_rq_perimetre_info);
        $total_ligne_rq_perimetre_info=mysql_num_rows($res_rq_perimetre_info);
        if($total_ligne_rq_perimetre_info!=0){     
          $sql="
          UPDATE `changement_perimetre` SET 
          `CHANGEMENT_PERIMETRE` = '".$CHANGEMENT_PERIMETRE."'
          WHERE `CHANGEMENT_PERIMETRE_ID` ='".$ID."' LIMIT 1 ;";
          mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());     
          
          $TABLE_SQL_SQL='changement_perimetre';       
          historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');
                    
          echo '
          <script language="JavaScript">
          url=("./index.php?ITEM=changement_Gestion_Perimetre");
          window.location=url;
          </script>
          ';
          }
      }
      mysql_free_result($res_rq_perimetre_info);
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
      $rq_perimetre_info="
      SELECT `CHANGEMENT_PERIMETRE_ID` , `CHANGEMENT_PERIMETRE`
      FROM `changement_perimetre`
      WHERE `CHANGEMENT_PERIMETRE_ID`='".$ID."'";
      $res_rq_perimetre_info = mysql_query($rq_perimetre_info, $mysql_link) or die(mysql_error());
      $tab_rq_perimetre_info = mysql_fetch_assoc($res_rq_perimetre_info);
      $total_ligne_rq_perimetre_info=mysql_num_rows($res_rq_perimetre_info);
      $CHANGEMENT_PERIMETRE=$tab_rq_perimetre_info['CHANGEMENT_PERIMETRE'];
    }
  }
  if(isset($_POST['ID'])){
    if(is_numeric($_POST['ID'])){
      $ID=$_POST['ID'];
      $rq_perimetre_info="
      SELECT `CHANGEMENT_PERIMETRE_ID` , `CHANGEMENT_PERIMETRE`
      FROM `changement_perimetre`
      WHERE `CHANGEMENT_PERIMETRE_ID`='".$ID."'";
      $res_rq_perimetre_info = mysql_query($rq_perimetre_info, $mysql_link) or die(mysql_error());
      $tab_rq_perimetre_info = mysql_fetch_assoc($res_rq_perimetre_info);
      $total_ligne_rq_perimetre_info=mysql_num_rows($res_rq_perimetre_info);
      $CHANGEMENT_PERIMETRE=$tab_rq_perimetre_info['CHANGEMENT_PERIMETRE'];
    }
  }
  $rq_perimetre_info="
  SELECT `CHANGEMENT_PERIMETRE_ID` 
  FROM `changement_liste` 
  WHERE `CHANGEMENT_PERIMETRE_ID`='".$ID."'";
  $res_rq_perimetre_info = mysql_query($rq_perimetre_info, $mysql_link) or die(mysql_error());
  $tab_rq_perimetre_info = mysql_fetch_assoc($res_rq_perimetre_info);
  $total_ligne_rq_perimetre_info=mysql_num_rows($res_rq_perimetre_info);
  if($total_ligne_rq_perimetre_info!=0){
    $aff_modif=1;
  }else{
    $aff_modif=0;
  }
  mysql_free_result($res_rq_perimetre_info);
}
echo '
<!--Début page HTML -->  
<div align="center">

<form method="post" name="frm_perimetre" id="frm_perimetre" action="index.php?ITEM='.$_GET['ITEM'].'">
<table class="table_inc">
	<tr align="center" class="titre">';
    if($action=="Ajout"){
      echo '
				<td colspan="2"><h2>&nbsp;[&nbsp;Ajout d\'un perimetre&nbsp;]&nbsp;</h2></td>';
    }
    if($action=="Modif"){
      echo '
				<td colspan="2">
				<h2>&nbsp;[&nbsp;Modification d\'un perimetre&nbsp;]&nbsp;</h2>
				';
				if($aff_modif==1){
          echo '&nbsp;Pas d\'action possible car on utilise le perimetre&nbsp;';
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
    <td align="left">&nbsp;perimetre&nbsp;</td>
    <td align="left"><input name="txt_CHANGEMENT_PERIMETRE" type="text" value="'.stripslashes($CHANGEMENT_PERIMETRE).'" size="50"/></td>
	</tr>
	';
	if($STOP_INFO_CHANGEMENT_PERIMETRE==1){
    echo '
    <tr class="titre">
      <td colspan="2" align="center"><h2>&nbsp;Le perimetre '.$CHANGEMENT_PERIMETRE.' existe d&eacute;j&agrave;, merci de choisir une autre perimetre.&nbsp;</h2></td>
    </tr>';
	}
	if($STOP_INFO_CHANGEMENT_PERIMETRE==2){
    echo '
    <tr class="titre">
      <td colspan="2" align="center"><h2>&nbsp;Impossible de faire la suppression du perimetre '.$CHANGEMENT_PERIMETRE.' car on l\'utilise pour une sous-perimetre.&nbsp;</h2></td>
    </tr>';
	}
	if($STOP_INFO_CHANGEMENT_PERIMETRE==3){
    echo '
    <tr class="titre">
      <td colspan="2" align="center"><h2>&nbsp;Impossible de faire la modification du perimetre '.$CHANGEMENT_PERIMETRE.' car on l\'utilise pour une sous-perimetre.&nbsp;</h2></td>
    </tr>';
	}
	$STOP_INFO_CHANGEMENT_PERIMETRE=0;
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
    <td colspan="2" align="center"><h2>&nbsp;[&nbsp;<a href="./index.php?ITEM=changement_Gestion_Perimetre">Retour - Liste des perimetres</a>&nbsp;]&nbsp;</h2></td>
	</tr>
	</table>
</form>
<br/><br/>
</div>';

mysql_close(); 
?>