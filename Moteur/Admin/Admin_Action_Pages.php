<?PHP
//redirection si acces dirrect
if(substr_count($_SERVER['PHP_SELF'], 'index.php')==0){
  header("Location: ../");
  exit();
}
/*******************************************************************
   Interface Ajout de pages
   Version 1.0.0    
  24/12/2009 - VGU - Creation fichier
**************************************************Guibert Vincent***
*******************************************************************/

require("./cf/conf_outil_icdc.php"); 
require("./cf/fonctions.php");
$j=0;
$ID='';
$page_test=0;
$STOP=0;
$ITEM='';
$URLP='';
$LEGEND='';
$LEGEND_MENU='';
$PAGES_INFO='';
$info_id='';

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
    $ITEM='';
    $URLP='';
    $LEGEND='';
    $action=='Ajout';
  }
  
  # Cas Ajouter
  if($tab_var['btn']=="Ajouter"){
    $ITEM=str_replace(' ','_',trim(htmlentities(addslashes($tab_var['txt_ITEM']))));
    $URLP=trim(htmlentities(addslashes($tab_var['txt_URLP'])));
    $LEGEND=trim(htmlentities(addslashes($tab_var['txt_LEGEND'])));
    $LEGEND_MENU=trim(htmlentities(addslashes($tab_var['txt_LEGEND_MENU'])));
    $PAGES_INFO =trim(htmlentities(addslashes($tab_var['txt_PAGES_INFO'])));
    if($ITEM==''){
      $STOP=1;
    }
    if($LEGEND==''){
      $STOP=1;
    }
    if($LEGEND_MENU==''){
      $STOP=1;
    }
    if($PAGES_INFO==''){
      $STOP=1;
    }
    if($URLP==''){
      $STOP=1;
    }else{
      $filename=str_replace('/index.php','/',$_SERVER["SCRIPT_FILENAME"]).$URLP;
      if (file_exists($filename)) {
        //print "Le fichier $filename existe";
      }else{
        //print "Le fichier $filename n'existe pas";
        $STOP=1;
        $page_test=3;
      }
      
    }

    if($STOP==0){
      $rq_page_info="
      SELECT `PAGES_ID` , `ITEM` , `URLP` , `LEGEND`,`LEGEND_MENU`, `PAGES_INFO` FROM `moteur_pages` WHERE `ITEM`='".$ITEM."' AND `ENABLE`=0";
      $res_rq_page_info = mysql_query($rq_page_info, $mysql_link) or die(mysql_error());
      $tab_rq_page_info = mysql_fetch_assoc($res_rq_page_info);
      $total_ligne_rq_page_info=mysql_num_rows($res_rq_page_info);
      if($total_ligne_rq_page_info!=0){
        $page_test=1;
      }else{
        //ajoute la page si non presente dans bdd
        $sql="INSERT INTO `moteur_pages` 
        ( `PAGES_ID` , `ITEM` , `URLP` , `LEGEND` , `LEGEND_MENU`,`PAGES_INFO` ,`ENABLE`)
        VALUES ( NULL , '".$ITEM."', '".$URLP."', '".$LEGEND."', '".$LEGEND_MENU."', '".$PAGES_INFO."', '0');";
        mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
        
        $TABLE_SQL_SQL='moteur_pages';       
        historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');
        
        $rq_id_info="
        SELECT `PAGES_ID` 
        FROM `moteur_pages` 
        WHERE 
        `ITEM`='".$ITEM."' AND
        `URLP`='".$URLP."' AND
        `LEGEND`='".$LEGEND."' AND
        `LEGEND_MENU`='".$LEGEND_MENU."' AND
        `ENABLE`=0
        LIMIT 1";
        $res_rq_id_info = mysql_query($rq_id_info, $mysql_link) or die(mysql_error());
        $tab_rq_id_info = mysql_fetch_assoc($res_rq_id_info);
        $total_ligne_rq_id_info=mysql_num_rows($res_rq_id_info);
        $ID=$tab_rq_id_info['PAGES_ID'];
        mysql_free_result($res_rq_id_info);

        $rq_id_info="
        SELECT `ROLE_ID` ,`ROLE` FROM `moteur_role`";
        $res_rq_id_info = mysql_query($rq_id_info, $mysql_link) or die(mysql_error());
        $tab_rq_id_info = mysql_fetch_assoc($res_rq_id_info);
        $total_ligne_rq_id_info=mysql_num_rows($res_rq_id_info);
        do {
          $ROLE_ID=$tab_rq_id_info['ROLE_ID'];
          $ROLE_DBB=$tab_rq_id_info['ROLE'];
          $ROLE_info='KO';
          if($ROLE_DBB=='ROOT'){
            $ROLE_info='OK';
            //ajoute les droits
            $sql="INSERT INTO `moteur_droit` 
            ( `DROIT_ID` , `ROLE_ID` , `PAGES_ID` , `DROIT` )
            VALUES ( NULL , '".$ROLE_ID."', '".$ID."', '".$ROLE_info."');";
            mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
            
            $TABLE_SQL_SQL='moteur_droit';       
            historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');
            
          }
        } while ($tab_rq_id_info = mysql_fetch_assoc($res_rq_id_info));
        $ligne= mysql_num_rows($res_rq_id_info);
        if($ligne > 0) {
          mysql_data_seek($res_rq_id_info, 0);
          $tab_rq_id_info = mysql_fetch_assoc($res_rq_id_info);
        }
        echo '
        <script language="JavaScript">
        url=("./index.php?ITEM=Admin_Gestion_Pages");
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
    $ITEM=trim(htmlentities(addslashes($tab_var['txt_ITEM'])));
    $URLP=trim(htmlentities(addslashes($tab_var['txt_URLP'])));
    $LEGEND=trim(htmlentities(addslashes($tab_var['txt_LEGEND'])));
    $LEGEND_MENU=trim(htmlentities(addslashes($tab_var['txt_LEGEND_MENU'])));
    $PAGES_INFO =trim(htmlentities(addslashes($tab_var['txt_PAGES_INFO '])));

    echo '
    <script language="JavaScript">
      url=("./index.php?ITEM=Admin_Gestion_Pages");
      window.location=url;
    </script>
    ';
    $action='Modif';
  }
}
echo '
<!--Début page HTML -->  
<div align="center">

<form method="post" name="frm_pages" id="frm_pages" action="index.php?ITEM='.$_GET['ITEM'].'">
	<table class="table_inc">
	<tr align="center" class="titre">';
		
    if($action=="Ajout"){
      echo '
				<td colspan="2"><h2>&nbsp;[&nbsp;Ajout d\'une Page&nbsp;]&nbsp;</h2></td>';
    }
    if($action=="Modif"){
      echo '
				<td colspan="2"><h2>&nbsp;[&nbsp;Modification d\'une Page&nbsp;]&nbsp;</h2></td>';
    }
  echo '

	</tr>
	<tr class="pair">
    <td align="left">&nbsp;ITEM&nbsp;</td>
    <td align="left"><input name="txt_ITEM" type="text" value="'.stripslashes($ITEM).'" size="50"/></td>
	</tr>
	<tr class="impair">
    <td align="left">&nbsp;URLP&nbsp;</td>
    <td align="left"><input name="txt_URLP" type="text" value="'.stripslashes($URLP).'" size="50"/></td>
	</tr>
	<tr class="pair">
    <td align="left">&nbsp;L&eacute;gende&nbsp;</td>
    <td align="left"><input name="txt_LEGEND" type="text" value="'.stripslashes($LEGEND).'" size="50"/></td>
	</tr>
	<tr class="impair">
    <td align="left">&nbsp;L&eacute;gende dans le menu&nbsp;</td>
    <td align="left"><input name="txt_LEGEND_MENU" type="text" value="'.stripslashes($LEGEND_MENU).'" size="50"/></td>
	</tr>
	<tr class="pair">
    <td align="left">&nbsp;Information&nbsp;</td>
    <td align="left"><input name="txt_PAGES_INFO" type="text" value="'.stripslashes($PAGES_INFO).'" size="50"/></td>
	</tr>
	';
	if($page_test==1){
    echo '
    <tr class="titre">
      <td colspan="2" align="center"><h2>&nbsp;ITEM d&eacute;j&agrave; dans pr&eacute;sent.&nbsp;</h2></td>
    </tr>';
	}
	if($page_test==2){
    echo '
    <tr class="titre">
      <td colspan="2" align="center"><h2>&nbsp;Merci de saisir l\'ensemble des Champs.&nbsp;</h2></td>
    </tr>';
	}
	if($page_test==3){
    echo '
    <tr class="titre">
      <td colspan="2" align="center"><h2>&nbsp;Le fichier '.$URLP.' n\'existe pas.&nbsp;</h2></td>
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
    	if(acces_sql()!="L"){
    		echo '<input name="btn" type="submit" id="btn" value="Modifier">';
	}
	echo '<input name="btn" type="submit" id="btn" value="RAZ">';
    } 
    echo '
    <input type="hidden" name="ID" value="'.$ID.'">
    </h2>
    </td>
  </tr>
	<tr class="titre">
    <td colspan="2" align="center"><h2>&nbsp;[&nbsp;<a href="./index.php?ITEM=Admin_Gestion_Pages">Retour - Liste des Pages</a>&nbsp;]&nbsp;</h2></td>
	</tr>
	</table>
</form>
<br/><br/>
</div>';

mysql_close(); 
?>