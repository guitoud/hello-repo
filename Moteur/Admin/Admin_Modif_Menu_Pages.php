<?PHP
//redirection si acces dirrect
if(substr_count($_SERVER['PHP_SELF'], 'index.php')==0){
  header("Location: ../"); 
  exit(); 
}
/*******************************************************************
   Interface Gestion des menus d'une pages
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
$ORDRE_INFO='';
$action="Modif";
if(isset($_GET['ID'])){
  $ID=$_GET['ID'];
}



$tab_var=$_POST;
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

if(empty($tab_var['btn'])){
}else{
  
  # Cas Modifier
  if($tab_var['btn']=="Modifier"){
    $NEW_MENU_ID=$tab_var['MENU'];

    $rq_menu_info="
    SELECT `SOUS_MENU_ID`, `MENU_ID`, `PAGES_ID` ,`ORDRE` FROM `moteur_sous_menu` WHERE `PAGES_ID`='".$ID."' ";
    $res_rq_menu_info = mysql_query($rq_menu_info, $mysql_link) or die(mysql_error());
    $tab_rq_menu_info = mysql_fetch_assoc($res_rq_menu_info);
    $total_ligne_rq_menu_info=mysql_num_rows($res_rq_menu_info);
    if($total_ligne_rq_menu_info!=0){
      ## la page a un nemu
      $SOUS_MENU_ID_OLD=$tab_rq_menu_info['SOUS_MENU_ID'];
      $MENU_ID_OLD=$tab_rq_menu_info['MENU_ID'];
      $PAGES_ID_OLD=$tab_rq_menu_info['PAGES_ID'];
      $ORDRE_OLD=$tab_rq_menu_info['ORDRE'];
      if($NEW_MENU_ID==0){
        # la page n'a plus de menu
        $sql="DELETE FROM `moteur_sous_menu` 
        WHERE `SOUS_MENU_ID`='".$SOUS_MENU_ID_OLD."' LIMIT 1";
        mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
        
        $TABLE_SQL_SQL='moteur_sous_menu';       
        historique_sql_new($sql,$TABLE_SQL_SQL,'DELETE');
                
        $sql="OPTIMIZE TABLE `moteur_sous_menu` ";
        mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
        
        $TABLE_SQL_SQL='moteur_sous_menu';       
        historique_sql_new($sql,$TABLE_SQL_SQL,'OPTIMIZE');
        
      }else{
        if($NEW_MENU_ID==$MENU_ID_OLD){
          # le menu ne change pas , juste l'ordre
          $NEW_ORDRE=$tab_var['txt_ORDRE'];
          if($NEW_ORDRE!=$ORDRE_OLD){
            if(is_numeric($NEW_ORDRE)){
              ## si ordre numerique on test s il appartient a un autre menu
              $rq_sous_menu_ordre="
              SELECT `SOUS_MENU_ID`, `ORDRE` FROM `moteur_sous_menu` WHERE `MENU_ID`='".$MENU_ID_OLD."' AND `ORDRE`='".$NEW_ORDRE."'";
              $res_rq_sous_menu_ordre = mysql_query($rq_sous_menu_ordre, $mysql_link) or die(mysql_error());
              $tab_rq_sous_menu_ordre = mysql_fetch_assoc($res_rq_sous_menu_ordre);
              $total_ligne_rq_sous_menu_ordre=mysql_num_rows($res_rq_sous_menu_ordre);
              ## si ordre n'appartient pas à un autre sous menu on met à jour
              if($total_ligne_rq_sous_menu_ordre==0){
                $sql="
                UPDATE `moteur_sous_menu` SET 
                `ORDRE` =  '".$NEW_ORDRE."'
                WHERE `SOUS_MENU_ID` ='".$SOUS_MENU_ID_OLD."' LIMIT 1";
                mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
                
                $TABLE_SQL_SQL='moteur_sous_menu';       
                historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');
                
              }else{
                ## si ordre appartient à un autre sous menu on met à jour les 2 menus
                $SOUS_MENU_ID_AUTRE=$tab_rq_sous_menu_ordre['SOUS_MENU_ID'];
                $ORDRE_AUTRE=$tab_rq_sous_menu_ordre['ORDRE'];
                $sql="
                UPDATE `moteur_sous_menu` SET 
                `ORDRE` =  '".$ORDRE_OLD."'
                WHERE `SOUS_MENU_ID` ='".$SOUS_MENU_ID_AUTRE."' LIMIT 1";
                mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
                
                $TABLE_SQL_SQL='moteur_sous_menu';       
                historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');
                
                $sql="
                UPDATE `moteur_sous_menu` SET 
                `ORDRE` =  '".$NEW_ORDRE."'
                WHERE `SOUS_MENU_ID` ='".$SOUS_MENU_ID_OLD."' LIMIT 1";
                mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
                
                $TABLE_SQL_SQL='moteur_sous_menu';       
                historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');
                
              }
            }
          }
        }else{
          # le menu change
          $sql="DELETE FROM `moteur_sous_menu` 
          WHERE `SOUS_MENU_ID`='".$SOUS_MENU_ID_OLD."' LIMIT 1";
          mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
          
          $TABLE_SQL_SQL='moteur_sous_menu';       
          historique_sql_new($sql,$TABLE_SQL_SQL,'DELETE');
          
          $rq_max_sous_menu="SELECT MAX( `ORDRE` ) AS `MAX` FROM `moteur_sous_menu` WHERE `MENU_ID`='".$NEW_MENU_ID."'";
          $res_rq_max_sous_menu = mysql_query($rq_max_sous_menu, $mysql_link) or die(mysql_error());
          $tab_rq_max_sous_menu = mysql_fetch_assoc($res_rq_max_sous_menu);
          $total_ligne_rq_max_sous_menu=mysql_num_rows($res_rq_max_sous_menu);
          $ORDRE_TXT=$tab_rq_max_sous_menu['MAX']+1;
          $sql="INSERT INTO `moteur_sous_menu` ( `SOUS_MENU_ID` , `MENU_ID` , `PAGES_ID` , `ORDRE` )
          VALUES (
          NULL , '".$NEW_MENU_ID."', '".$ID."', '".$ORDRE_TXT."'
          );";
          mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
          
          $TABLE_SQL_SQL='moteur_sous_menu';       
          historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');
          
        }
      }
    }else{
      if($NEW_MENU_ID!=0){
        # la page possède maintenant un menu
        $rq_max_sous_menu="SELECT MAX( `ORDRE` ) AS `MAX` FROM `moteur_sous_menu` WHERE `MENU_ID`='".$NEW_MENU_ID."'";
        $res_rq_max_sous_menu = mysql_query($rq_max_sous_menu, $mysql_link) or die(mysql_error());
        $tab_rq_max_sous_menu = mysql_fetch_assoc($res_rq_max_sous_menu);
        $total_ligne_rq_max_sous_menu=mysql_num_rows($res_rq_max_sous_menu);
        $ORDRE_TXT=$tab_rq_max_sous_menu['MAX']+1;
        $sql="INSERT INTO `moteur_sous_menu` ( `SOUS_MENU_ID` , `MENU_ID` , `PAGES_ID` , `ORDRE` )
        VALUES (
        NULL , '".$NEW_MENU_ID."', '".$ID."', '".$ORDRE_TXT."'
        );";
        mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
        
        $TABLE_SQL_SQL='moteur_sous_menu';       
        historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');
        
      }
    }		
   echo '
    <script language="JavaScript">
    url=("./index.php?ITEM=Admin_Gestion_Menus");
    window.location=url;
    </script>
    ';

    }
}
$rq_page_info="
SELECT `PAGES_ID` , `ITEM` , `URLP` , `LEGEND` FROM `moteur_pages` WHERE `PAGES_ID`='".$ID."' AND `ENABLE`=0";
$res_rq_page_info = mysql_query($rq_page_info, $mysql_link) or die(mysql_error());
$tab_rq_page_info = mysql_fetch_assoc($res_rq_page_info);
$total_ligne_rq_page_info=mysql_num_rows($res_rq_page_info);
$ITEM=$tab_rq_page_info['ITEM'];
$URLP=$tab_rq_page_info['URLP'];
$LEGEND=$tab_rq_page_info['LEGEND'];

$rq_menu="
SELECT `MENU_ID`, `NOM_MENU` FROM `moteur_menu` ORDER BY `ORDRE`";
$res_rq_menu = mysql_query($rq_menu, $mysql_link) or die(mysql_error());
$tab_rq_menu = mysql_fetch_assoc($res_rq_menu);
$total_ligne_rq_menu=mysql_num_rows($res_rq_menu);

echo '
<!--Début page HTML -->  
<div align="center">

<form method="post" name="frm_pages" id="frm_pages" action="index.php?ITEM=Admin_Modif_Menu_Pages">
	<table class="table_inc">
	<tr align="center" class="titre">';
    if($action=="Modif"){
      echo '<td colspan="2"><h2>&nbsp;[&nbsp;Modification du menu d\'une page.&nbsp;]&nbsp;</h2></td>';
    }
    echo '
	</tr>';
  $j++;
  if ($j%2) { $class = "pair";}else{$class = "impair";} 
	echo '
	<tr class='.$class.'>
    <td align="left">&nbsp;ITEM&nbsp;</td>
    <td align="left">'.stripslashes($ITEM).'</td>
  </tr>
  ';
  $j++;
  if ($j%2) { $class = "pair";}else{$class = "impair";} 
	echo '
	<tr class='.$class.'>
    <td align="left">&nbsp;URLP&nbsp;</td>
    <td align="left">'.stripslashes($URLP).'</td>
  </tr>';
  $j++;
  if ($j%2) { $class = "pair";}else{$class = "impair";} 
	echo '
	<tr class='.$class.'>
    <td align="left">&nbsp;L&eacute;gende&nbsp;</td>
    <td align="left">'.stripslashes($LEGEND).'</td>
	</tr>';
    $rq_menu_info="
    SELECT `SOUS_MENU_ID`, `MENU_ID`, `PAGES_ID`FROM `moteur_sous_menu` WHERE `PAGES_ID`='".$ID."' ";
    $res_rq_menu_info = mysql_query($rq_menu_info, $mysql_link) or die(mysql_error());
    $tab_rq_menu_info = mysql_fetch_assoc($res_rq_menu_info);
    $total_ligne_rq_menu_info=mysql_num_rows($res_rq_menu_info);
    if($total_ligne_rq_menu_info!=0){
      $SOUS_MENU_ACCES='KO';
    }else{
      $SOUS_MENU_ACCES='OK';
    }
    echo '
    <tr class='.$class.'>
      <td align="left">&nbsp;MENU : Aucun menu&nbsp;</td>
      <td align="left">&nbsp;oui&nbsp;<INPUT type=radio name="MENU" value="0"';if($SOUS_MENU_ACCES=='OK'){echo 'CHECKED';} echo '>&nbsp;</td>
    </tr>'; 
	
	do {
    $j++;
    if ($j%2) { $class = "pair";}else{$class = "impair";} 
    $MENU_ID=$tab_rq_menu['MENU_ID'];
    $NOM_MENU=$tab_rq_menu['NOM_MENU'];
      
    $rq_menu_info="
    SELECT `SOUS_MENU_ID`, `MENU_ID`, `PAGES_ID` ,`ORDRE` FROM `moteur_sous_menu` WHERE `PAGES_ID`='".$ID."' AND `MENU_ID`='".$MENU_ID."'";
    $res_rq_menu_info = mysql_query($rq_menu_info, $mysql_link) or die(mysql_error());
    $tab_rq_menu_info = mysql_fetch_assoc($res_rq_menu_info);
    $total_ligne_rq_menu_info=mysql_num_rows($res_rq_menu_info);
    if($total_ligne_rq_menu_info==0){
      $SOUS_MENU_ACCES='KO';
      $ORDRE_INFO='';
    }else{
      $SOUS_MENU_ACCES='OK';
      $ORDRE_INFO='- Ordre : <input name="txt_ORDRE" type="text" value="'.stripslashes($tab_rq_menu_info['ORDRE']).'" size="2"/>';
    }
    echo '
    <tr class='.$class.'>
      <td align="left">&nbsp;MENU : '.$NOM_MENU.'&nbsp;</td>
      <td align="left">&nbsp;oui&nbsp;<INPUT type=radio name="MENU" value="'.$MENU_ID.'"';if($SOUS_MENU_ACCES=='OK'){echo 'CHECKED';} echo '>&nbsp;'.$ORDRE_INFO.'</td>
    </tr>';  
	
	} while ($tab_rq_menu = mysql_fetch_assoc($res_rq_menu));
  $ligne= mysql_num_rows($res_rq_menu);
  if($ligne > 0) {
    mysql_data_seek($res_rq_menu, 0);
    $tab_rq_menu = mysql_fetch_assoc($res_rq_menu);
  }
  echo'	
	<tr class="titre">
    <td colspan="2" align="center">
      <h2>';
      if(acces_sql()!="L"){
            echo '<input name="btn" type="submit" id="btn" value="Modifier">';
      }
      echo '
      <input type="hidden" name="ID" value="'.$ID.'">
      <input type="hidden" name="ITEM" value="Admin_Modif_Gestion_memu_Pages">
      </h2>
    </td>
  </tr>
	<tr class="titre">
    <td colspan="2" align="center"><h2>&nbsp;[&nbsp;<a href="./index.php?ITEM=Admin_Gestion_Menus">Retour - Liste des Menus</a>&nbsp;]&nbsp;</h2></td>
	</tr>
	</table>
</form>
<br/><br/>
</div>';

mysql_free_result($res_rq_menu);
mysql_free_result($res_rq_page_info);
mysql_close(); 
?>