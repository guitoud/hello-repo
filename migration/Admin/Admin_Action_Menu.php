<?PHP
//redirection si acces dirrect
if(substr_count($_SERVER['PHP_SELF'], 'index.php')==0){
  header("Location: ../");
  exit();
}
/*******************************************************************
   Interface Ajout de menu
   Version 1.0.0  
  24/12/2009 - VGU - Creation fichier
**************************************************Guibert Vincent***
*******************************************************************/

require("./cf/conf_outil_icdc.php"); 
require_once("./cf/fonctions.php");
$j=0;
$ID='';
$info_id='';
$page_test=0;
$STOP=0;
$NOM_MENU_TXT='';
$ORDRE_TXT='';
$ORDRE_DEFAULT_TXT='G';

if(isset($_GET['action'])){
  $action=$_GET['action'];
}else{
  $action='Ajout';
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


  # Cas RAZ
  if($tab_var['btn']=="RAZ"){
    $NOM_MENU_TXT='';
    $ORDRE_TXT='';
    $ORDRE_DEFAULT_TXT='G';
    $ID='';
    $action=='Ajout';
  }
  
  # Cas Ajouter
  if($tab_var['btn']=="Ajouter"){
    $NOM_MENU_TXT=str_replace('&lt;','<',str_replace('&gt;','>',addslashes(trim(htmlentities($tab_var['txt_NOM_MENU'])))));
    $MENU_INFO_TXT=str_replace('&lt;','<',str_replace('&gt;','>',addslashes(trim(htmlentities($tab_var['txt_MENU_INFO'])))));
    $ORDRE_TXT=addslashes(trim(htmlentities($tab_var['txt_ORDRE'])));
    $ORDRE_DEFAULT_TXT=addslashes(trim(htmlentities($tab_var['txt_ORDRE_DEFAULT'])));
    ## Stop si NOM_MENU vide
    if($NOM_MENU_TXT==''){
      $STOP=1;
    }
    
    if($STOP==0){
      $rq_page_info="
      SELECT `MENU_ID` , `NOM_MENU` FROM `moteur_menu` WHERE `NOM_MENU`='".$NOM_MENU_TXT."'";
      $res_rq_page_info = mysql_query($rq_page_info, $mysql_link) or die(mysql_error());
      $tab_rq_page_info = mysql_fetch_assoc($res_rq_page_info);
      $total_ligne_rq_page_info=mysql_num_rows($res_rq_page_info);
      if($total_ligne_rq_page_info!=0){
         ## menu deja present
        $page_test=1;
      }else{
        if($ORDRE_TXT=''){
          ## si ordre vide on prend le numero suivant du max
          $rq_max_menu="SELECT MAX( `ORDRE` ) AS `MAX` FROM `moteur_menu` ";
          $res_rq_max_menu = mysql_query($rq_max_menu, $mysql_link) or die(mysql_error());
          $tab_rq_max_menu = mysql_fetch_assoc($res_rq_max_menu);
          $total_ligne_rq_max_menu=mysql_num_rows($res_rq_max_menu);
          $ORDRE_TXT=$tab_rq_max_menu['MAX']+1;
        }else{
          if(!is_numeric($ORDRE_TXT)){
            ## si ordre non numerique on prend le numero suivant du max
            $rq_max_menu="SELECT MAX( `ORDRE` ) AS `MAX` FROM `moteur_menu` ";
            $res_rq_max_menu = mysql_query($rq_max_menu, $mysql_link) or die(mysql_error());
            $tab_rq_max_menu = mysql_fetch_assoc($res_rq_max_menu);
            $total_ligne_rq_max_menu=mysql_num_rows($res_rq_max_menu);
            $ORDRE_TXT=$tab_rq_max_menu['MAX']+1;
          }else{
            $rq_menu_ordre="
            SELECT `MENU_ID` , `NOM_MENU`, `ORDRE` FROM `moteur_menu` WHERE `ORDRE`='".$ORDRE_TXT."'";
            $res_rq_menu_ordre = mysql_query($rq_menu_ordre, $mysql_link) or die(mysql_error());
            $tab_rq_menu_ordre = mysql_fetch_assoc($res_rq_menu_ordre);
            $total_ligne_rq_menu_ordre=mysql_num_rows($res_rq_menu_ordre);
            if($total_ligne_rq_menu_ordre!=0){
              ## si ordre deja present on prend le numero suivant du max
              $rq_max_menu="SELECT MAX( `ORDRE` ) AS `MAX` FROM `moteur_menu` ";
              $res_rq_max_menu = mysql_query($rq_max_menu, $mysql_link) or die(mysql_error());
              $tab_rq_max_menu = mysql_fetch_assoc($res_rq_max_menu);
              $total_ligne_rq_max_menu=mysql_num_rows($res_rq_max_menu);
              $ORDRE_TXT=$tab_rq_max_menu['MAX']+1;
            }
          }
        }
        //ajoute le mune si non presente dans bdd
        $sql="INSERT INTO `moteur_menu` 
        ( `MENU_ID` , `NOM_MENU` , `ORDRE` ,`MENU_INFO`,`ORDRE_DEFAULT` )
        VALUES ( NULL , '".$NOM_MENU_TXT."', '".$ORDRE_TXT."', '".$MENU_INFO_TXT."', '".$ORDRE_DEFAULT_TXT."');";
        mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
        
        $TABLE_SQL_SQL='moteur_menu';       
        historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');
        
       echo '
        <script language="JavaScript">
        url=("./index.php?ITEM=Admin_Gestion_Menus");
        window.location=url;
        </script>
        ';
      }
      mysql_free_result($res_rq_page_info);
    }else{
      if($page_test!=3){
        $page_test=2;
      }
    }
  }
  
  # Cas Modifier
  if($tab_var['btn']=="Modifier"){
    $ID=$tab_var['ID'];
    $NOM_MENU_TXT=str_replace('&lt;','<',str_replace('&gt;','>',addslashes(trim(htmlentities($tab_var['txt_NOM_MENU'])))));
    $MENU_INFO_TXT=str_replace('&lt;','<',str_replace('&gt;','>',addslashes(trim(htmlentities($tab_var['txt_MENU_INFO'])))));
    $ORDRE_TXT=addslashes(trim(htmlentities($tab_var['txt_ORDRE'])));
    $ORDRE_DEFAULT_TXT=addslashes(trim(htmlentities($tab_var['txt_ORDRE_DEFAULT'])));
    ## Stop si NOM_MENU vide
    if($NOM_MENU_TXT==''){
      $STOP=1;
    }
    if($STOP!=1){
    $rq_menu_ordre="
    SELECT `MENU_ID` , `NOM_MENU`, `ORDRE` FROM `moteur_menu` WHERE `MENU_ID`='".$ID."'";
    $res_rq_menu_ordre = mysql_query($rq_menu_ordre, $mysql_link) or die(mysql_error());
    $tab_rq_menu_ordre = mysql_fetch_assoc($res_rq_menu_ordre);
    $total_ligne_rq_menu_ordre=mysql_num_rows($res_rq_menu_ordre);
    $NOM_MENU_BDD=$tab_rq_menu_ordre['NOM_MENU'];
    $ORDRE_BDD=$tab_rq_menu_ordre['ORDRE'];
    ## si l'ordre ne change pas un met a jour ne nom du menu
    if($ORDRE_BDD==$ORDRE_TXT){
      $sql="
      UPDATE `moteur_menu` SET 
      `NOM_MENU` =  '".$NOM_MENU_TXT."' ,
      `MENU_INFO` = '".$MENU_INFO_TXT."',
      `ORDRE_DEFAULT` = '".$ORDRE_DEFAULT_TXT."'
      WHERE `MENU_ID` ='".$ID."' LIMIT 1";
      mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
      
      $TABLE_SQL_SQL='moteur_menu';       
      historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');
            
    }else{
      if(!is_numeric($ORDRE_TXT)){
        ## si ordre non numerique on prend le numero suivant du max
        $rq_max_menu="SELECT MAX( `ORDRE` ) AS `MAX` FROM `moteur_menu` ";
        $res_rq_max_menu = mysql_query($rq_max_menu, $mysql_link) or die(mysql_error());
        $tab_rq_max_menu = mysql_fetch_assoc($res_rq_max_menu);
        $total_ligne_rq_max_menu=mysql_num_rows($res_rq_max_menu);
        $ORDRE_TXT=$tab_rq_max_menu['MAX']+1;
      }else{
        ## si ordre numerique on test s il appartient a un autre menu
        $rq_menu_ordre="
        SELECT `MENU_ID` , `ORDRE` FROM `moteur_menu` WHERE `ORDRE`='".$ORDRE_TXT."'";
        $res_rq_menu_ordre = mysql_query($rq_menu_ordre, $mysql_link) or die(mysql_error());
        $tab_rq_menu_ordre = mysql_fetch_assoc($res_rq_menu_ordre);
        $total_ligne_rq_menu_ordre=mysql_num_rows($res_rq_menu_ordre);
        ## si ordre n'appartient pas à un autre menu on met à jour
        if($total_ligne_rq_menu_ordre==0){
          $sql="
          UPDATE `moteur_menu` SET 
          `NOM_MENU` =  '".$NOM_MENU_TXT."', 
          `ORDRE` =  '".$ORDRE_TXT."',
          `MENU_INFO` = '".$MENU_INFO_TXT."',
          `ORDRE_DEFAULT` = '".$ORDRE_DEFAULT_TXT."'
          WHERE `MENU_ID` ='".$ID."' LIMIT 1";
          mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
          
          $TABLE_SQL_SQL='moteur_menu';       
          historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');
          
        }else{
          ## si ordre appartient à un autre menu on met à jour les 2 menus
          ## exemple
          ## menu_id 1 ordre 1 devient ordre 2 alors le menu avec l'ordre 2 prend l'ordre 1
          $MENU_ID_AUTRE=$tab_rq_menu_ordre['MENU_ID'];
          $ORDRE_AUTRE=$tab_rq_menu_ordre['ORDRE'];
          $sql="
          UPDATE `moteur_menu` SET 
          `ORDRE` =  '".$ORDRE_BDD."'
          WHERE `MENU_ID` ='".$MENU_ID_AUTRE."' LIMIT 1";
          mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
          
          $TABLE_SQL_SQL='moteur_menu';       
          historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');
          
          $sql="
          UPDATE `moteur_menu` SET 
          `NOM_MENU` =  '".$NOM_MENU_TXT."', 
          `ORDRE` =  '".$ORDRE_TXT."',
          `ORDRE_DEFAULT` = '".$ORDRE_DEFAULT_TXT."'
          WHERE `MENU_ID` ='".$ID."' LIMIT 1";
          mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
          
          $TABLE_SQL_SQL='moteur_menu';       
          historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');
          
        }
      }
    }
    echo '
    <script language="JavaScript">
      url=("./index.php?ITEM=Admin_Gestion_Menus");
      window.location=url;
    </script>
    ';
    }

    $action='Modif';
  }
    # Cas Supprimer
  if($tab_var['btn']=="Supprimer"){
    $ID=$tab_var['ID'];
    $sql="DELETE FROM `moteur_menu` 
    WHERE `MENU_ID`='".$ID."' LIMIT 1";
    mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
    
    $TABLE_SQL_SQL='moteur_menu';       
    historique_sql_new($sql,$TABLE_SQL_SQL,'DELETE');
    
    $sql="OPTIMIZE TABLE `moteur_menu` ";
    mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
    
    $TABLE_SQL_SQL='moteur_menu';       
    historique_sql_new($sql,$TABLE_SQL_SQL,'OPTIMIZE');

    echo '
    <script language="JavaScript">
      url=("./index.php?ITEM=Admin_Gestion_Menus");
      window.location=url;
    </script>
    ';
  }
}

$rq_menu_info="
SELECT `MENU_ID` , `NOM_MENU`, `ORDRE` ,`MENU_INFO`,`ORDRE_DEFAULT` FROM `moteur_menu` WHERE `MENU_ID`='".$ID."'";
$res_rq_menu_info = mysql_query($rq_menu_info, $mysql_link) or die(mysql_error());
$tab_rq_menu_info = mysql_fetch_assoc($res_rq_menu_info);
$total_ligne_rq_menu_info=mysql_num_rows($res_rq_menu_info);
$NOM_MENU_TXT=$tab_rq_menu_info['NOM_MENU'];
$MENU_INFO_TXT=$tab_rq_menu_info['MENU_INFO'];
$ORDRE_TXT=$tab_rq_menu_info['ORDRE'];
$ORDRE_DEFAULT_TXT=$tab_rq_menu_info['ORDRE_DEFAULT'];
echo '
<!--Début page HTML -->  
<div align="center">

<form method="post" name="frm_menus" id="frm_menus" action="index.php?ITEM='.$_GET['ITEM'].'">
	<table class="table_inc">
	<tr align="center" class="titre">';
		
    if($action=="Ajout"){
      echo '
				<td colspan="2"><h2>&nbsp;[&nbsp;Ajout d\'un Menu&nbsp;]&nbsp;</h2></td>';
    }
    if($action=="Modif"){
      echo '
				<td colspan="2"><h2>&nbsp;[&nbsp;Modification d\'un Menu&nbsp;]&nbsp;</h2></td>';
    }
  echo '

	</tr>
	<tr class="impair">
    <td align="left">&nbsp;Nom du Menu&nbsp;</td>
    <td align="left"><input name="txt_NOM_MENU" type="text" value="'.stripslashes($NOM_MENU_TXT).'" size="50"/></td>
	</tr>
	<tr class="pair">
    <td align="left">&nbsp;Information&nbsp;</td>
    <td align="left"><input name="txt_MENU_INFO" type="text" value="'.stripslashes($MENU_INFO_TXT).'" size="50"/></td>
	</tr>
	<tr class="impair">
    <td align="left">&nbsp;Ordre&nbsp;</td>
    <td align="left"><input name="txt_ORDRE" type="text" value="'.stripslashes($ORDRE_TXT).'" size="2"/></td>
	</tr>
	<tr class="pair">
    <td align="left">&nbsp;Ordre Default&nbsp;</td>
    <td align="left">
    <INPUT type="radio" name="txt_ORDRE_DEFAULT" value="G"';
    if($ORDRE_DEFAULT_TXT=='G'){echo ' CHECKED';} 
    echo '>&nbsp;Gauche&nbsp;/&nbsp;Droite&nbsp;
    <INPUT type="radio" name="txt_ORDRE_DEFAULT" value="D"'; 
    if($ORDRE_DEFAULT_TXT=='D'){echo ' CHECKED';} 
    echo '>    
    </td>
	</tr>';
	if($page_test==1){
    echo '
    <tr class="titre">
      <td colspan="2" align="center"><h2>&nbsp;Menu d&eacute;j&agrave; dans pr&eacute;sent.&nbsp;</h2></td>
    </tr>';
	}
	if($page_test==2){
    echo '
    <tr class="titre">
      <td colspan="2" align="center"><h2>&nbsp;Merci de saisir l\'ensemble des Champs.&nbsp;</h2></td>
    </tr>';
	}
	echo '
	<tr class="titre">
    <td colspan="2" align="center">
    <h2>
    <input type="hidden" name="info_id" value="'.$info_id.'">';
    if($action=='Ajout'){
      	if(acces_sql()!="L"){
      		echo '<input name="btn" type="submit" id="btn" value="Ajouter">';
	}
      echo '<input name="btn" type="submit" id="btn" value="RAZ">';
    }
    if($action=='Modif'){
      $rq_sous_menu_info="
      SELECT `SOUS_MENU_ID` FROM `moteur_sous_menu` WHERE `MENU_ID`='".$ID."'";
      $res_rq_sous_menu_info = mysql_query($rq_sous_menu_info, $mysql_link) or die(mysql_error());
      $tab_rq_sous_menu_info = mysql_fetch_assoc($res_rq_sous_menu_info);
      $total_ligne_rq_sous_menu_info=mysql_num_rows($res_rq_sous_menu_info);
      	if(acces_sql()!="L"){
      		echo '<input name="btn" type="submit" id="btn" value="Modifier">';
	}
      ## on affiche le bouton supprimer que si le nemu n'a pas de sous menu.
      if($total_ligne_rq_sous_menu_info==0){
      	if(acces_sql()!="L"){
        	echo '<input name="btn" type="submit" id="btn" value="Supprimer">';
	}
      }
      echo '<input name="btn" type="submit" id="btn" value="RAZ">';
    } 
    echo '
    <input type="hidden" name="ID" value="'.$ID.'">
    </h2>
    </td>
  </tr>
	<tr class="titre">
    <td colspan="2" align="center"><h2>&nbsp;[&nbsp;<a href="./index.php?ITEM=Admin_Gestion_Menus">Retour - Liste des Menus</a>&nbsp;]&nbsp;</h2></td>
	</tr>
	</table>
</form>
</div>';

mysql_close($mysql_link); 
?>