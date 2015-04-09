<?PHP
//redirection si acces dirrect
if(substr_count($_SERVER['PHP_SELF'], 'index.php')==0){
  header("Location: ../");
  exit();
}
/*******************************************************************
   Interface Ajout date version
   Version 1.0.0   
  28/12/2009 - VGU - Creation fichier
**************************************************Guibert Vincent***
*******************************************************************/

require("./cf/conf_outil_icdc.php"); 
require_once("./cf/fonctions.php");
$j=0;
$ID='';
$page_test=0;
$STOP=0;
$STOP_INFO_DATE_VERSION=0;
$DATE_VERSION='';
$aff_modif=0;
$txt_DATE_VERSION=date("d/m/Y"); 
$TYPE='V';

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
    $DATE_VERSION='';
    $txt_DATE_VERSION=date("d/m/Y"); 
    $TYPE='V'; 
    $action='Ajout';
    $_GET['ITEM']='indicateur_Ajout_date_version';
  }
  
  # Cas Ajouter
  if($tab_var['btn']=="Ajouter"){
    $DATE_VERSION=addslashes(trim(htmlentities($tab_var['txt_DATE_VERSION'])));  
    $TYPE=addslashes(trim(htmlentities($tab_var['TYPE']))); 
    $jour=substr($DATE_VERSION,0,2);
    $mois=substr($DATE_VERSION,3,2);
    $annee=substr($DATE_VERSION,6,4);
    if($DATE_VERSION==''){
      $DATE_VERSION='';
    }else{
      $DATE_VERSION=$annee*10000+$mois*100+$jour;
    }
    if($DATE_VERSION==''){
      $STOP=1;
    }
    if($STOP==0){
      $rq_info="
      SELECT * 
      FROM `indicateur_version_date` 
      WHERE `DATE`='".$DATE_VERSION."'";
      $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
      $tab_rq_info = mysql_fetch_assoc($res_rq_info);
      $total_ligne_rq_info=mysql_num_rows($res_rq_info);
      if($total_ligne_rq_info!=0){
        $STOP_INFO_DATE_VERSION=1;
      }else{
        //ajoute la date si non presente dans bdd
        
        $sql="
        INSERT INTO `indicateur_version_date` ( `ID` , `DATE` ,`TYPE`)
        VALUES ( NULL , '".$DATE_VERSION."','".$TYPE."' );";
        mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
                
        $TABLE_SQL_SQL='indicateur_version_date';       
        historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');
        
        echo '
        <script language="JavaScript">
        url=("./index.php?ITEM=indicateur_Gestion_date_version");
        window.location=url;
        </script>
        ';
      }
      mysql_free_result($res_rq_info);
    }else{
      if($page_test!=3){
        $page_test=2;
      }
    }
  }
  # Cas Supprimer
  if($tab_var['btn']=="Supprimer"){

        //supprime la date
        
        $sql="DELETE FROM `indicateur_version_date` WHERE `ID` = '".$ID."' LIMIT 1;";
        mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
        
        $TABLE_SQL_SQL='indicateur_version_date';       
        historique_sql_new($sql,$TABLE_SQL_SQL,'DELETE');
       
        $sql="OPTIMIZE TABLE `indicateur_version_date` ";
        mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
        
        $TABLE_SQL_SQL='indicateur_version_date';       
        historique_sql_new($sql,$TABLE_SQL_SQL,'OPTIMIZE');
        
        echo '
        <script language="JavaScript">
        url=("./index.php?ITEM=indicateur_Gestion_date_version");
        window.location=url;
        </script>
        ';      
  }

}
if($action=="Modif"){
  if(isset($_GET['ID'])){
    if(is_numeric($_GET['ID'])){
      $ID=$_GET['ID'];
      $rq_info="
      SELECT *
      FROM `indicateur_version_date` 
      WHERE `ID`='".$ID."'";
      $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
      $tab_rq_info = mysql_fetch_assoc($res_rq_info);
      $total_ligne_rq_info=mysql_num_rows($res_rq_info);
      $DATE_VERSION=$tab_rq_info['DATE'];
      $jour=substr($DATE_VERSION,6,2);
      $mois=substr($DATE_VERSION,4,2);
      $annee=substr($DATE_VERSION,0,4);
      $txt_DATE_VERSION=$jour.'/'.$mois.'/'.$annee;
      $TYPE=$tab_rq_info['TYPE'];
    }
  }
  if(isset($_POST['ID'])){
    if(is_numeric($_POST['ID'])){
      $ID=$_POST['ID'];
      $rq_info="
      SELECT *
      FROM `indicateur_version_date` 
      WHERE `ID`='".$ID."'";
      $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
      $tab_rq_info = mysql_fetch_assoc($res_rq_info);
      $total_ligne_rq_info=mysql_num_rows($res_rq_info);
      $DATE_VERSION=$tab_rq_info['DATE'];
      $jour=substr($DATE_VERSION,6,2);
      $mois=substr($DATE_VERSION,4,2);
      $annee=substr($DATE_VERSION,0,4);
      $txt_DATE_VERSION=$jour.'/'.$mois.'/'.$annee;
      $TYPE=$tab_rq_info['TYPE'];
    }
  }
  mysql_free_result($res_rq_info);
}
echo '
<!--Début page HTML -->  
<div align="center">

<form method="post" name="frm" id="frm" action="index.php?ITEM='.$_GET['ITEM'].'">
  <table class="table_inc">
    <tr align="center" class="titre">';
    if($action=="Ajout"){
      echo '
      <td colspan="2"><h2>&nbsp;[&nbsp;Ajout d\'une date de versioning&nbsp;]&nbsp;</h2></td>';
    }
    if($action=="Modif"){
      echo '
      <td colspan="2"><h2>&nbsp;[&nbsp;Modification d\'une date de versioning&nbsp;]&nbsp;</h2></td>';
    }
    echo '
    </tr>';
    $j++;
    if ($j%2) { $class = "pair";}else{$class = "impair";} 
    echo '
    <tr class='.$class.'>
      <td align="left">&nbsp;Date&nbsp;</td>
      <td align="left">
      <input name="txt_DATE_VERSION" type="text" readonly value="'.$txt_DATE_VERSION.'" size="10"/>
      <a href="#" onClick=" window.open(\'./cf/calendrier/pop.php?frm=frm&amp;ch=txt_DATE_VERSION&amp;DATE_URL='.$txt_DATE_VERSION.'\',\'calendrier\',\'width=350,height=160,scrollbars=0\').focus();">
      <img src="./cf/calendrier/petit_calendrier.png" border="0"/></a>
      </td>
    </tr>';
    $j++;
    if ($j%2) { $class = "pair";}else{$class = "impair";} 
    echo '
    <tr class='.$class.'>
      <td align="left">&nbsp;Type&nbsp;</td>
      <td align="left">
      &nbsp;En Version&nbsp;<INPUT type=radio name="TYPE" value="V"';if($TYPE=='V'){echo 'CHECKED';} echo '>
      &nbsp; /&nbsp;LUP&nbsp;<INPUT type=radio name="TYPE" value="LUP"'; if($TYPE=='LUP'){echo 'CHECKED';} echo '>
      &nbsp; /&nbsp;Hors-Versions planifiés&nbsp;<INPUT type=radio name="TYPE" value="HVP"'; if($TYPE=='HVP'){echo 'CHECKED';} echo '>
      </td>
    </tr>
    ';
    if($STOP_INFO_DATE_VERSION==1){
    echo '
    <tr class="titre">
      <td colspan="2" align="center"><h2>&nbsp;La date '.$DATE_VERSION.' existe d&eacute;j&agrave;.&nbsp;</h2></td>
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
    <h2>';
    if($action=='Ajout'){
    if(acces_sql()!="L"){
    	echo '<input name="btn" type="submit" id="btn" value="Ajouter">';
    }
    echo '<input name="btn" type="submit" id="btn" value="RAZ">';
    }
    if($action=='Modif'){
      if($aff_modif==0){
      	if(acces_sql()!="L"){
        	echo '<input name="btn" type="submit" id="btn" value="Supprimer">';
	}
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
    <td colspan="2" align="center"><h2>&nbsp;[&nbsp;<a href="./index.php?ITEM=indicateur_Gestion_date_version">Retour - Liste des date de version</a>&nbsp;]&nbsp;</h2></td>
	</tr>
	</table>
</form>
<br/><br/>
</div>';

mysql_close(); 
?>