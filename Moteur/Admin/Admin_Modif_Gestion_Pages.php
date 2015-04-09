<?PHP
//redirection si acces dirrect
if(substr_count($_SERVER['PHP_SELF'], 'index.php')==0){
  header("Location: ../"); 
  exit(); 
}
/*******************************************************************
   Interface Gestion des droits sur une pages
   Version 1.0.0    
  24/12/2009 - VGU - Creation fichier
**************************************************Guibert Vincent***
*******************************************************************/

require("./cf/conf_outil_icdc.php"); 
require("./cf/fonctions.php");
$j=0;
$ITEM='';
$URLP='';
$LEGEND='';
$ID='';
$action="Modif";
if(isset($_GET['ID'])){
  $ID=$_GET['ID'];
}
if(isset($_GET['begin'])){
  $begin=$_GET['begin'];
}else{
  $begin=0;
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
  }
}
if(isset($tab_var['begin'])){
  if(is_numeric($tab_var['begin'])){
    $begin=$tab_var['begin'];
  }
}
if(empty($tab_var['btn'])){
}else{
  
  # Cas Modifier
  if($tab_var['btn']=="Modifier"){
    $rq_role_info_modif="
    SELECT `ROLE_ID` ,`ROLE` FROM `moteur_role`";
    $res_rq_role_info_modif = mysql_query($rq_role_info_modif, $mysql_link) or die(mysql_error());
    $tab_rq_role_info_modif = mysql_fetch_assoc($res_rq_role_info_modif);
    $total_ligne_rq_role_info_modif=mysql_num_rows($res_rq_role_info_modif);
    do {
      $ROLE_ID=$tab_rq_role_info_modif['ROLE_ID'];
      $ROLE_DBB=$tab_rq_role_info_modif['ROLE'];
      $ROLE='ROLE_';
      $ROLE .=$tab_rq_role_info_modif['ROLE'];
      $ROLE_info=$tab_var[$ROLE];
  
      //echo 'ROLE = '.$ROLE_info.'<br/>';
      
      $rq_role_page_droit="
      SELECT `DROIT_ID` FROM `moteur_droit` WHERE `PAGES_ID`='".$ID."' AND `ROLE_ID`='".$ROLE_ID."'";
      $res_rq_role_page_droit = mysql_query($rq_role_page_droit, $mysql_link) or die(mysql_error());
      $tab_rq_role_page_droit = mysql_fetch_assoc($res_rq_role_page_droit);
      $total_ligne_rq_role_page_droit=mysql_num_rows($res_rq_role_page_droit);
      if($total_ligne_rq_role_page_droit==0){
        if($ROLE_DBB=='ROOT'){
          $ROLE_info='OK';
          //ajoute les droits
          $sql="INSERT INTO `moteur_droit` 
          ( `DROIT_ID` , `ROLE_ID` , `PAGES_ID` , `DROIT` )
          VALUES ( NULL , '".$ROLE_ID."', '".$ID."', '".$ROLE_info."');";
          mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
          
          $TABLE_SQL_SQL='moteur_droit';       
          historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');

        }else{
          if($ROLE_info=='OK'){
            //ajoute les droits
            $sql="INSERT INTO `moteur_droit` 
            ( `DROIT_ID` , `ROLE_ID` , `PAGES_ID` , `DROIT` )
            VALUES ( NULL , '".$ROLE_ID."', '".$ID."', '".$ROLE_info."');";
            mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
            
            $TABLE_SQL_SQL='moteur_droit';       
            historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');
          
          }
        }
       
      }else{
        //modification les droits
        if($ROLE_DBB=='ROOT'){
          $ROLE_info='OK';
          $sql="UPDATE `moteur_droit` SET `DROIT` = '".$ROLE_info."' WHERE `PAGES_ID` ='".$ID."' AND `ROLE_ID`='".$ROLE_ID."'LIMIT 1";
          mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
          
          $TABLE_SQL_SQL='moteur_droit';       
          historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');
          
        }else{
          if($ROLE_info=='OK'){
            $sql="UPDATE `moteur_droit` SET `DROIT` = '".$ROLE_info."' WHERE `PAGES_ID` ='".$ID."' AND `ROLE_ID`='".$ROLE_ID."'LIMIT 1";
            mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
            
            $TABLE_SQL_SQL='moteur_droit';       
            historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');
            
          }else{
            $sql="DELETE FROM `moteur_droit` WHERE `PAGES_ID` ='".$ID."' AND `ROLE_ID`='".$ROLE_ID."' LIMIT 1";
            mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
            
            $TABLE_SQL_SQL='moteur_droit';       
            historique_sql_new($sql,$TABLE_SQL_SQL,'DELETE');
            
            $sql="OPTIMIZE TABLE `moteur_droit` ";
            mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
            
            $TABLE_SQL_SQL='moteur_droit';       
            historique_sql_new($sql,$TABLE_SQL_SQL,'OPTIMIZE');
            
          }
        }
        
      }
    
    } while ($tab_rq_role_info_modif = mysql_fetch_assoc($res_rq_role_info_modif));
    $ligne= mysql_num_rows($res_rq_role_info_modif);
    if($ligne > 0) {
      mysql_data_seek($res_rq_role_info_modif, 0);
      $tab_rq_role_info_modif = mysql_fetch_assoc($res_rq_role_info_modif);
    }
    
    $LEGEND_txt=addslashes(trim($tab_var['txt_LEGEND']));
    $LEGEND_MENU_txt=addslashes(trim($tab_var['txt_LEGEND_MENU']));
    $PAGES_INFO_txt=addslashes(trim($tab_var['txt_PAGES_INFO']));
    $ITEM_txt=addslashes(trim($tab_var['txt_ITEM']));
    $STOP_LEGENDe=0;
    if($LEGEND_txt==''){
      $STOP_LEGENDe=1;
    }
    if($LEGEND_MENU_txt==''){
      $STOP_LEGENDe=1;
    }
    if($PAGES_INFO_txt==''){
      $STOP_LEGENDe=1;
    }
    if($STOP_LEGENDe!=1){
      $sql="UPDATE `moteur_pages` 
      SET `LEGEND` = '".$LEGEND_txt."',
      `ITEM` = '".$ITEM_txt."',
      `LEGEND_MENU` = '".$LEGEND_MENU_txt."',
      `PAGES_INFO` = '".$PAGES_INFO_txt."',
      `ENABLE` = '0'
       WHERE `PAGES_ID` ='".$ID."' LIMIT 1";
      mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
      
      $TABLE_SQL_SQL='moteur_pages';       
      historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');

    }
    
    echo'
    <script language="JavaScript">
    url=("./index.php?ITEM=Admin_Gestion_Pages&begin='.$begin.'#'.$ID.'");
    window.location=url;
    </script>
    ';

    }
  # Cas Supprimer
  if($tab_var['btn']=="Supprimer"){

  	$rq_page_info_id="
	SELECT `PAGES_ID`,`ENABLE` FROM `moteur_pages` WHERE `PAGES_ID` ='".$ID."'";
	$res_rq_page_info_id = mysql_query($rq_page_info_id, $mysql_link) or die(mysql_error());
	$tab_rq_page_info_id = mysql_fetch_assoc($res_rq_page_info_id);
	$total_ligne_rq_page_info_id=mysql_num_rows($res_rq_page_info_id);
	$ENABLE=$tab_rq_page_info_id ['ENABLE'];
	mysql_free_result($res_rq_page_info_id);
	
  	if($total_ligne_rq_page_info_id==0){
		echo'
		<script language="JavaScript">
		url=("./index.php?ITEM=Admin_Gestion_Pages");
		window.location=url;
		</script>';
  	}else{
  		if($ENABLE==0){
	  	        $sql="UPDATE `moteur_pages` 
			SET `ENABLE` = '1'
			WHERE `PAGES_ID` ='".$ID."' LIMIT 1";
			mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
	        
		        $TABLE_SQL_SQL='moteur_pages';       
		        historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');
		        
		        $sql="OPTIMIZE TABLE `moteur_pages` ";
			mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
			
			$TABLE_SQL_SQL='moteur_pages';       
		        historique_sql_new($sql,$TABLE_SQL_SQL,'OPTIMIZE');
	  	
		  	$sql="DELETE FROM `moteur_sous_menu` WHERE `PAGES_ID` ='".$ID."' ";
			mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
			
			$TABLE_SQL_SQL='moteur_sous_menu';       
			historique_sql_new($sql,$TABLE_SQL_SQL,'DELETE');
			
			$sql="OPTIMIZE TABLE `moteur_sous_menu` ";
			mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
			
			$TABLE_SQL_SQL='moteur_sous_menu';       
			historique_sql_new($sql,$TABLE_SQL_SQL,'OPTIMIZE');
		  	
			$sql="DELETE FROM `moteur_droit` WHERE `PAGES_ID` ='".$ID."' ";
			mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
			
			$TABLE_SQL_SQL='moteur_droit';       
			historique_sql_new($sql,$TABLE_SQL_SQL,'DELETE');
			
			$sql="OPTIMIZE TABLE `moteur_droit` ";
			mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
			
			$TABLE_SQL_SQL='moteur_droit';       
			historique_sql_new($sql,$TABLE_SQL_SQL,'OPTIMIZE');
		}else{
			$sql="DELETE FROM `moteur_pages` WHERE `PAGES_ID` ='".$ID."' LIMIT 1";
			mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
	        
		        $TABLE_SQL_SQL='moteur_pages';       
		        historique_sql_new($sql,$TABLE_SQL_SQL,'DELETE');
		        
		        $sql="OPTIMIZE TABLE `moteur_pages` ";
			mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
			
			$TABLE_SQL_SQL='moteur_pages';       
		        historique_sql_new($sql,$TABLE_SQL_SQL,'OPTIMIZE');
		}
		
		echo'
		<script language="JavaScript">
		url=("./index.php?ITEM=Admin_Gestion_Pages");
		window.location=url;
		</script>';
	}
  }
}
$rq_page_info="
SELECT `PAGES_ID` , `ITEM` , `URLP` , `LEGEND`, `LEGEND_MENU`, `PAGES_INFO` FROM `moteur_pages` WHERE `PAGES_ID`='".$ID."'";
$res_rq_page_info = mysql_query($rq_page_info, $mysql_link) or die(mysql_error());
$tab_rq_page_info = mysql_fetch_assoc($res_rq_page_info);
$total_ligne_rq_page_info=mysql_num_rows($res_rq_page_info);
$ITEM=$tab_rq_page_info['ITEM'];
$URLP=$tab_rq_page_info['URLP'];
$LEGEND=$tab_rq_page_info['LEGEND'];
$LEGEND_MENU=$tab_rq_page_info['LEGEND_MENU'];
$PAGES_INFO=$tab_rq_page_info['PAGES_INFO'];


$rq_role_info="
SELECT `ROLE_ID` ,`ROLE` FROM `moteur_role` ORDER BY `ROLE`";
$res_rq_role_info = mysql_query($rq_role_info, $mysql_link) or die(mysql_error());
$tab_rq_role_info = mysql_fetch_assoc($res_rq_role_info);
$total_ligne_rq_role_info=mysql_num_rows($res_rq_role_info);


echo '

<!--Début page HTML -->  
<div align="center">

<form method="post" name="frm_pages" id="frm_pages" action="index.php?ITEM=Admin_Modif_Gestion_Pages">
	<table class="table_inc">
	<tr align="center" class="titre">';

    if($action=="Modif"){
      echo '
				<td colspan="2"><h2>&nbsp;[&nbsp;Modification des droits d\'une page.&nbsp;]&nbsp;</h2></td>';
    }

  echo '
	</tr>
	<tr class="impair">
    <td align="left">&nbsp;ITEM&nbsp;</td>
    <td align="left"><input name="txt_ITEM" type="text" value="'.stripslashes($ITEM).'" size="50"/></td>
	</tr>
	<tr class="pair">
    <td align="left">&nbsp;URLP&nbsp;</td>
    <td align="left">'.stripslashes($URLP).'</td>
	</tr>
	<tr class="impair">
    <td align="left">&nbsp;L&eacute;gende&nbsp;</td>
    <td align="left"><input name="txt_LEGEND" type="text" value="'.stripslashes($LEGEND).'" size="50"/></td>
	</tr>
	<tr class="pair">
    <td align="left">&nbsp;L&eacute;gende dans le menu&nbsp;</td>
    <td align="left"><input name="txt_LEGEND_MENU" type="text" value="'.stripslashes($LEGEND_MENU).'" size="50"/></td>
	</tr>
	<tr class="impair">
    <td align="left">&nbsp;Information&nbsp;</td>
    <td align="left"><input name="txt_PAGES_INFO" type="text" value="'.stripslashes($PAGES_INFO).'" size="50"/></td>
	</tr>';
	do {
    $j++;
    if ($j%2) { $class = "pair";}else{$class = "impair";} 
    $ROLE=$tab_rq_role_info['ROLE'];
    $ROLE_ID=$tab_rq_role_info['ROLE_ID'];
    $DROIT='KO';
    $rq_droit_role_info="
    SELECT `DROIT` FROM `moteur_droit` WHERE `PAGES_ID`='".$ID."' AND `ROLE_ID`='".$ROLE_ID."'";
    $res_rq_droit_role_info = mysql_query($rq_droit_role_info, $mysql_link) or die(mysql_error());
    $tab_rq_droit_role_info = mysql_fetch_assoc($res_rq_droit_role_info);
    $total_ligne_rq_droit_role_info=mysql_num_rows($res_rq_droit_role_info);
    if($total_ligne_rq_droit_role_info!=0){
      $DROIT=$tab_rq_droit_role_info['DROIT'];
    }
    mysql_free_result($res_rq_droit_role_info);

    echo '
    <tr class='.$class.'>
      <td align="left">&nbsp;'.$ROLE.'&nbsp;</td>
      <td align="left">&nbsp;oui&nbsp;<INPUT type=radio name="ROLE_'.$ROLE.'" value="OK"';if($DROIT=='OK'){echo 'CHECKED';} echo '>&nbsp; /&nbsp;non&nbsp;<INPUT type=radio name="ROLE_'.$ROLE.'" value="KO"'; if($DROIT=='KO'){echo 'CHECKED';} echo '></td>
    </tr>';
	
	} while ($tab_rq_role_info = mysql_fetch_assoc($res_rq_role_info));
  $ligne= mysql_num_rows($res_rq_role_info);
  if($ligne > 0) {
    mysql_data_seek($res_rq_role_info, 0);
    $tab_rq_role_info = mysql_fetch_assoc($res_rq_role_info);
  }
  echo '
	<tr class="titre">
    <td colspan="2" align="center">
      <h2>
      ';
      if(acces_sql()!="L"){
      	echo '
      <input name="btn" type="submit" id="btn" value="Modifier">
      <input name="btn" type="submit" id="btn" value="Supprimer"> 
      ';
	}
	echo '
      <input type="hidden" name="ID" value="'.$ID.'">
      <input type="hidden" name="begin" value="'.$begin.'">
      <input type="hidden" name="ITEM" value="Admin_Modif_Gestion_Pages">
      </h2>
    </td>
	</tr>
	<tr class="titre">
	<td colspan="2" align="center">
		<h2>&nbsp;[&nbsp;<a href="./index.php?ITEM=Admin_Gestion_Pages&begin='.$begin.'#'.$ID.'">Retour - Liste des Pages</a>&nbsp;]&nbsp;</h2>	</td>
	</tr>
	</table>
</form>
<br/><br/>
</div>';

mysql_free_result($res_rq_role_info);
mysql_free_result($res_rq_page_info);
mysql_close(); 
?>