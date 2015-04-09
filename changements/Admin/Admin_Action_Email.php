<?PHP
//redirection si acces dirrect
if(substr_count($_SERVER['PHP_SELF'], 'index.php')==0){
  header("Location: ../");
  exit();
}
/*******************************************************************
   Interface Modif du mot de passe.
   Version 1.1.0  
  20/10/2010 - VGU - Creation fichier
**************************************************Guibert Vincent***
*******************************************************************/

require("./cf/conf_outil_icdc.php"); 
require_once("./cf/fonctions.php");
$j=0;
$ID='';
$page_test=0;
$STOP=0;
$STOP_INFO_LOGIN=0;
$LOGIN=$_SESSION['LOGIN'];
$EMAIL='';
$action='Modif';


$tab_var=$_POST;

if(isset($_POST['action'])){
  $action=$_POST['action'];
}

if(empty($tab_var['btn'])){
}else{


  # Cas OK
  if($tab_var['btn']=="OK"){
    $LOGIN=$_SESSION['LOGIN'];
    $txt_EMAIL=trim($tab_var['txt_EMAIL']);
    	$rq_user_info="
	SELECT `UTILISATEUR_ID`,`NOM`, `PRENOM`,`EMAIL_FULL` FROM `moteur_utilisateur` WHERE `LOGIN`='".$LOGIN."' LIMIT 1";
	$res_rq_user_info = mysql_query($rq_user_info, $mysql_link) or die(mysql_error());
	$tab_rq_user_info = mysql_fetch_assoc($res_rq_user_info);
	$total_ligne_rq_user_info=mysql_num_rows($res_rq_user_info);
	$NOM=$tab_rq_user_info['NOM'];
	$PRENOM=$tab_rq_user_info['PRENOM'];
	$UTILISATEUR_ID=$tab_rq_user_info['UTILISATEUR_ID'];
	$EMAIL=$tab_rq_user_info['EMAIL_FULL'];
	mysql_free_result($res_rq_user_info);
    if($txt_EMAIL==''){
	$STOP=1;
    }
    if(substr_count($txt_EMAIL, '@')!=1){
    	$STOP=1;
    }
    $EMAIL_MINI=str_replace(substr($txt_EMAIL,strpos($txt_EMAIL,"@")),'',$txt_EMAIL);
    if($STOP==0){
    		$sql="
    		UPDATE `moteur_utilisateur` SET 
    		`EMAIL_FULL` = '".$txt_EMAIL."',
    		`EMAIL` = '".$EMAIL_MINI."'
    		WHERE `UTILISATEUR_ID` ='".$UTILISATEUR_ID."' LIMIT 1 ;
    		";
		mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
			
		$TABLE_SQL_SQL='moteur_utilisateur';       
		historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');
        echo '
        <script language="JavaScript">
        url=("./index.php");
        window.location=url;
        </script>
        ';
      }

    }
      /*# Cas Mise a Jour
  if($tab_var['btn']=="Mise a Jour"){
    $LOGIN=$_SESSION['LOGIN'];
    $txt_EMAIL=trim($tab_var['txt_EMAIL']);
    	$rq_user_info="
	SELECT `UTILISATEUR_ID`,`NOM`, `PRENOM`,`EMAIL_FULL` FROM `moteur_utilisateur` WHERE `LOGIN`='".$LOGIN."' LIMIT 1";
	$res_rq_user_info = mysql_query($rq_user_info, $mysql_link) or die(mysql_error());
	$tab_rq_user_info = mysql_fetch_assoc($res_rq_user_info);
	$total_ligne_rq_user_info=mysql_num_rows($res_rq_user_info);
	$NOM=$tab_rq_user_info['NOM'];
	$PRENOM=$tab_rq_user_info['PRENOM'];
	$UTILISATEUR_ID=$tab_rq_user_info['UTILISATEUR_ID'];
	$EMAIL=$tab_rq_user_info['EMAIL_FULL'];
	mysql_free_result($res_rq_user_info);
    if($txt_EMAIL==''){
	$STOP=1;
    }
    if(substr_count($txt_EMAIL, '@')!=1){
    	$STOP=1;
    }
    $EMAIL_MINI=str_replace(substr($txt_EMAIL,strpos($txt_EMAIL,"@")),'',$txt_EMAIL);
    if($STOP==0){
    		$sql="
    		UPDATE `moteur_utilisateur` SET 
    		`EMAIL_FULL` = '".$txt_EMAIL."',
    		`EMAIL` = '".$EMAIL_MINI."'
    		WHERE `UTILISATEUR_ID` ='".$UTILISATEUR_ID."' LIMIT 1 ;
    		";
		mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
			
		$TABLE_SQL_SQL='moteur_utilisateur';       
		historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');
        echo '
        <script language="JavaScript">
        url=("./index.php");
        window.location=url;
        </script>
        ';
      }

    }*/
    
}
$rq_user_info="
SELECT `NOM`, `PRENOM`, `COMPLEMENT`,`EMAIL_FULL` FROM `moteur_utilisateur` WHERE `LOGIN`='".$LOGIN."' LIMIT 1";
$res_rq_user_info = mysql_query($rq_user_info, $mysql_link) or die(mysql_error());
$tab_rq_user_info = mysql_fetch_assoc($res_rq_user_info);
$total_ligne_rq_user_info=mysql_num_rows($res_rq_user_info);
$NOM=$tab_rq_user_info['NOM'];
$PRENOM=$tab_rq_user_info['PRENOM'];
$EMAIL=$tab_rq_user_info['EMAIL_FULL'];
$COMPLEMENT=$tab_rq_user_info['COMPLEMENT'];
mysql_free_result($res_rq_user_info);
if($EMAIL==''){
	if($COMPLEMENT==''){
		$EMAIL=$PRENOM.'.'.$NOM.'@caissedesdepots.fr';		
	}else{
		$EMAIL=$PRENOM.'.'.$NOM.'-e@caissedesdepots.fr';	
	}
	
}
echo '
<!--Début page HTML -->  
<div align="center">

<form method="post" name="frm_utilisateur" id="frm_utilisateur" action="index.php?ITEM=Admin_Action_Email">
<table class="table_inc">
        <tr align="center" class="titre">
                                <td colspan="2"><h2>
                                &nbsp;[&nbsp;V&eacute;rification de l\'email de '.$PRENOM.' '.$NOM.'&nbsp;]&nbsp;
                                </BR>&nbsp;Lors de votre premi&egrave;re acc&egrave;s &agrave; l\'outil.&nbsp;
                                </h2></td>
        </tr>';
  $j++;
  if ($j%2) { $class = "pair";}else{$class = "impair";} 
	echo '
	<tr class='.$class.'>
    <td align="left">&nbsp;Email&nbsp;</td>
    <td align="left"><input name="txt_EMAIL" type="text" value="'.$EMAIL.'" size="100"/></td>
	</tr>';
	$j++;
echo '
	<tr class="paire">
    <td colspan="2" align="center">
    <font color=#993333><b>Attention au -e@ pour les externes</b></font>
    </td>
	</tr>
	<tr class="titre">
    <td colspan="2" align="center">
    <h2>';
    if($action=='Modif'){
    	echo '<input name="btn" type="submit" id="btn" value="OK">';
    	//echo '<input name="btn" type="submit" id="btn" value="Mise a Jour">';
    } 
    echo '
    <input type="hidden" name="ID" value="'.$ID.'">
    <input type="hidden" name="action" id="action" value="'.$action.'">
    </h2>
    </td>
	</tr>

	
	</table>
</form>
</div>';
mysql_close(); 
?>