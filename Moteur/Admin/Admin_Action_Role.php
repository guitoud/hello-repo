<?PHP
//redirection si acces dirrect
if(substr_count($_SERVER['PHP_SELF'], 'index.php')==0){
  header("Location: ../");
  exit();
}
/*******************************************************************
   Interface Ajout de role
   Version 1.0.0    
  08/01/2010 - VGU - Creation fichier
**************************************************Guibert Vincent***
*******************************************************************/

require("./cf/conf_outil_icdc.php"); 
require("./cf/fonctions.php");
$j=0;
$ID='';
$info_id='';
$page_test=0;
$STOP=0;
$NOM_ROLE_TXT='';

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
    $NOM_ROLE_TXT='';
    $ID='';
    $action=='Ajout';
  }
  
  # Cas Ajouter
  if($tab_var['btn']=="Ajouter"){
    $NOM_ROLE_TXT=str_replace('&lt;','<',str_replace('&gt;','>',addslashes(trim(htmlentities($tab_var['txt_NOM_ROLE'])))));
    ## Stop si NOM_ROLE_TXT vide
    if($NOM_ROLE_TXT==''){
      $STOP=1;
    }
    
    if($STOP==0){
      $rq_role_info="SELECT `ROLE_ID` , `ROLE` FROM `moteur_role` WHERE `ROLE`='".$NOM_ROLE_TXT."'";
      $res_rq_role_info = mysql_query($rq_role_info, $mysql_link) or die(mysql_error());
      $tab_rq_role_info = mysql_fetch_assoc($res_rq_role_info);
      $total_ligne_rq_role_info=mysql_num_rows($res_rq_role_info);
      if($total_ligne_rq_role_info!=0){
         ## role deja present
        $page_test=1;
      }else{
        //ajoute le role si non presente dans bdd
        $sql="INSERT INTO `moteur_role` 
        ( `ROLE_ID` , `ROLE` )
        VALUES ( NULL , '".$NOM_ROLE_TXT."');";
        mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
        
        $TABLE_SQL_SQL='moteur_role';       
        historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');
        
       echo '
        <script language="JavaScript">
        url=("./index.php?ITEM=Admin_Gestion_Role");
        window.location=url;
        </script>
        ';
      }
      mysql_free_result($res_rq_role_info);
    }else{
      if($page_test!=3){
        $page_test=2;
      }
    }
  }
  
  
  # Cas Modifier
  if($tab_var['btn']=="Modifier"){
    $ID=$tab_var['ID'];
    $NOM_ROLE_TXT=str_replace('&lt;','<',str_replace('&gt;','>',addslashes(trim(htmlentities($tab_var['txt_NOM_ROLE'])))));
    ## Stop si NOM_ROLE_TXT vide
    if($NOM_ROLE_TXT==''){
      $STOP=1;
    }
    if($STOP!=1){
      $rq_role_info="SELECT `ROLE_ID` FROM `moteur_role_utilisateur` WHERE `ROLE_ID`='".$ID."'";
      $res_rq_role_info = mysql_query($rq_role_info, $mysql_link) or die(mysql_error());
      $tab_rq_role_info = mysql_fetch_assoc($res_rq_role_info);
      $total_ligne_rq_role_info=mysql_num_rows($res_rq_role_info);
      if($total_ligne_rq_role_info!=0){
         ## role deja present
        $page_test=1;
      }else{

    $sql="
    UPDATE `moteur_role` SET 
    `ROLE` =  '".$NOM_ROLE_TXT."'
    WHERE `ROLE_ID` ='".$ID."' LIMIT 1";
    mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());

    $TABLE_SQL_SQL='moteur_role';       
    historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');
 
    echo '
    <script language="JavaScript">
      url=("./index.php?ITEM=Admin_Gestion_Role");
      window.location=url;
    </script>
    ';
    }
    }
    $action='Modif';
  }
  
  
    # Cas Supprimer
  if($tab_var['btn']=="Supprimer"){
    $ID=$tab_var['ID'];
    
    $sql="DELETE FROM `moteur_role_utilisateur` 
    WHERE `ROLE_ID`='".$ID."' LIMIT 1";
    mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
    
    $TABLE_SQL_SQL='moteur_role_utilisateur';       
    historique_sql_new($sql,$TABLE_SQL_SQL,'DELETE');
    
    $sql="OPTIMIZE TABLE `moteur_role_utilisateur` ";
    mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
    
    $TABLE_SQL_SQL='moteur_role_utilisateur';       
    historique_sql_new($sql,$TABLE_SQL_SQL,'OPTIMIZE');
    
    $sql="DELETE FROM `moteur_role` 
    WHERE `ROLE_ID`='".$ID."' LIMIT 1";
    mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
    
    $TABLE_SQL_SQL='moteur_role';       
    historique_sql_new($sql,$TABLE_SQL_SQL,'DELETE');
    
    $sql="OPTIMIZE TABLE `moteur_role` ";
    mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
    
    $TABLE_SQL_SQL='moteur_role';       
    historique_sql_new($sql,$TABLE_SQL_SQL,'OPTIMIZE');

    echo '
    <script language="JavaScript">
      url=("./index.php?ITEM=Admin_Gestion_Role");
      window.location=url;
    </script>
    ';
  }
  
}

$rq_role_info="
SELECT `ROLE_ID` , `ROLE` FROM `moteur_role` WHERE `ROLE_ID`='".$ID."'";
$res_rq_role_info = mysql_query($rq_role_info, $mysql_link) or die(mysql_error());
$tab_rq_role_info = mysql_fetch_assoc($res_rq_role_info);
$total_ligne_rq_role_info=mysql_num_rows($res_rq_role_info);
$NOM_ROLE_TXT=$tab_rq_role_info['ROLE'];
echo '
<!--Début page HTML -->  
<div align="center">

<form method="post" name="frm_role" id="frm_role" action="index.php?ITEM='.$_GET['ITEM'].'">
	<table class="table_inc">
	<tr align="center" class="titre">';
		
    if($action=="Ajout"){
      echo '
				<td colspan="2"><h2>&nbsp;[&nbsp;Ajout d\'un R&ocirc;le&nbsp;]&nbsp;</h2></td>';
    }
    if($action=="Modif"){
      echo '
				<td colspan="2"><h2>&nbsp;[&nbsp;Modification d\'un R&ocirc;le&nbsp;]&nbsp;</h2></td>';
    }
  echo '

	</tr>
	<tr class="impair">
    <td align="left">&nbsp;Nom du R&ocirc;le&nbsp;</td>
    <td align="left"><input name="txt_NOM_ROLE" type="text" value="'.stripslashes($NOM_ROLE_TXT).'" size="50"/></td>
	</tr>';
	if($page_test==1){
    echo '
    <tr class="titre">
      <td colspan="2" align="center"><h2>&nbsp;R&ocirc;le d&eacute;j&agrave; dans pr&eacute;sent.&nbsp;</h2></td>
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
    	
    $rq_role_info="SELECT `ROLE_ID` FROM `moteur_role_utilisateur` WHERE `ROLE_ID`='".$ID."' AND `ROLE_UTILISATEUR_ACCES`=0";
    $res_rq_role_info = mysql_query($rq_role_info, $mysql_link) or die(mysql_error());
    $tab_rq_role_info = mysql_fetch_assoc($res_rq_role_info);
    $total_ligne_rq_role_info=mysql_num_rows($res_rq_role_info);
    	if(acces_sql()!="L"){
      		echo '<input name="btn" type="submit" id="btn" value="Modifier">';
	}
      ## on affiche le bouton supprimer que si le nemu n'a pas de sous menu.
      if($total_ligne_rq_role_info==0){
      	 $rq_role_info="SELECT COUNT(`ROLE_ID`) AS `NB` FROM `moteur_droit` WHERE `ROLE_ID`='".$ID."'";
         $res_rq_role_info = mysql_query($rq_role_info, $mysql_link) or die(mysql_error());
         $tab_rq_role_info = mysql_fetch_assoc($res_rq_role_info);
         $total_ligne_rq_role_info=mysql_num_rows($res_rq_role_info);
         if($tab_rq_role_info['NB']==0){
         	if(acces_sql()!="L"){
           		echo '<input name="btn" type="submit" id="btn" value="Supprimer">';
        	}
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
    <td colspan="2" align="center"><h2>&nbsp;[&nbsp;<a href="./index.php?ITEM=Admin_Gestion_Role">Retour - Liste des R&ocirc;les</a>&nbsp;]&nbsp;</h2></td>
	</tr>
	</table>
</form>
<br/><br/>
</div>';

mysql_close($mysql_link); 
?>