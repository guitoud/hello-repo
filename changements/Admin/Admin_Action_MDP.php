<?PHP
//redirection si acces dirrect
if(substr_count($_SERVER['PHP_SELF'], 'index.php')==0){
  header("Location: ../");
  exit();
}
/*******************************************************************
   Interface Modif du mot de passe.
   Version 1.1.0  
  24/12/2009 - VGU - Creation fichier
**************************************************Guibert Vincent***
*******************************************************************/

require("./cf/conf_outil_icdc.php"); 
require_once("./cf/fonctions.php");
$j=0;
$ID='';
$page_test=0;
$STOP=0;
$STOP_INFO_MDP=0;
$STOP_INFO_MDP_NEW=0;
$STOP_INFO_LOGIN=0;
$LOGIN=$_SESSION['LOGIN'];
$MDP='';
$MDP_NEW='';
$MDP_NEW_VERIF='';
$test='';
$action='Modif';


$tab_var=$_POST;
if(isset($_GET['test'])){
  $test=$_GET['test'];
}

if(isset($_POST['action'])){
  $action=$_POST['action'];
}

if(empty($tab_var['btn'])){
}else{


  # Cas RAZ
  if($tab_var['btn']=="RAZ"){
    $LOGIN=$_SESSION['LOGIN'];
    $MDP='';
    $MDP_NEW='';
    $MDP_NEW_VERIF='';
    $action='Modif';
  }

  # Cas Modifier
  if($tab_var['btn']=="Modifier"){
    $LOGIN=$_SESSION['LOGIN'];
    $MDP=md5(addslashes(trim($tab_var['txt_MDP'])));
    $MDP_NEW=addslashes(trim($tab_var['txt_MDP_NEW']));
    $MDP_NEW_VERIF=addslashes(trim($tab_var['txt_MDP_NEW_VERIF']));
    
    if($LOGIN==''){
      $STOP=1;
    }
    $rq_info="
    SELECT `LOGIN`
    FROM `moteur_utilisateur` 
    WHERE `LOGIN`='".$LOGIN."' AND
    `MDP_MD5`='".$MDP."' AND
    `ENABLE`='Y'
    ";
    $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
    $tab_rq_info = mysql_fetch_assoc($res_rq_info);
    $total_ligne_rq_info=mysql_num_rows($res_rq_info);
    if($total_ligne_rq_info==0){
      $STOP=1;
      $STOP_INFO_MDP=1;
    }else{
      if($MDP_NEW==$MDP_NEW_VERIF){
        if(strlen($MDP_NEW)<=5){
          $STOP=1;
          $STOP_INFO_MDP_NEW=2;
        }
      }else{
        $STOP=1;
        $STOP_INFO_MDP_NEW=1;
      }
    }

    if($STOP==0){
      $rq_user_info="
      SELECT `UTILISATEUR_ID` FROM `moteur_utilisateur` WHERE `LOGIN`='".$LOGIN."' LIMIT 1";
      $res_rq_user_info = mysql_query($rq_user_info, $mysql_link) or die(mysql_error());
      $tab_rq_user_info = mysql_fetch_assoc($res_rq_user_info);
      $total_ligne_rq_user_info=mysql_num_rows($res_rq_user_info);
      if($total_ligne_rq_user_info!=0){    
        $UTILISATEUR_ID=$tab_rq_user_info['UTILISATEUR_ID'];
        if($STOP_INFO_MDP_NEW==0){
          $MDP_NEW_SQL=md5($MDP_NEW);
          $sql="
          UPDATE `moteur_utilisateur` SET 
          `MDP_MD5` = '".$MDP_NEW_SQL."'
          WHERE `UTILISATEUR_ID` ='".$UTILISATEUR_ID."' LIMIT 1 ;";
          mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error()); 
          
          $TABLE_SQL_SQL='moteur_utilisateur';       
          historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');
        }

        echo '
        <script language="JavaScript">
        url=("./index.php");
        window.location=url;
        </script>
        ';
      }
      mysql_free_result($res_rq_user_info);
    }
  }
}
$rq_user_info="
SELECT `NOM`, `PRENOM` FROM `moteur_utilisateur` WHERE `LOGIN`='".$LOGIN."' LIMIT 1";
$res_rq_user_info = mysql_query($rq_user_info, $mysql_link) or die(mysql_error());
$tab_rq_user_info = mysql_fetch_assoc($res_rq_user_info);
$total_ligne_rq_user_info=mysql_num_rows($res_rq_user_info);
$NOM=$tab_rq_user_info['NOM'];
$PRENOM=$tab_rq_user_info['PRENOM'];
mysql_free_result($res_rq_user_info);
echo '
<!--Début page HTML -->  
<div align="center">

<form method="post" name="frm_utilisateur" id="frm_utilisateur" action="index.php?ITEM=Admin_Action_MDP">
<table class="table_inc">
        <tr align="center" class="titre">
                                <td colspan="2"><h2>&nbsp;[&nbsp;Modification du mot de passe de '.$PRENOM.' '.$NOM.'&nbsp;]&nbsp;</h2></td>
        </tr>';
        if($test=='KO'){
    	echo '
    	<tr align="center" class="titre">
          <td colspan="2">&nbsp;Merci de faire la Modification du mot de passe.&nbsp;</td>
        </tr>';
        }
  $j++;
  if ($j%2) { $class = "pair";}else{$class = "impair";} 
	echo '
	<tr class='.$class.'>
    <td align="left">&nbsp;Ancien Mot de passe&nbsp;</td>
    <td align="left"><input name="txt_MDP" type="password" value="" size="50"/></td>
	</tr>';
	$j++;
  if ($j%2) { $class = "pair";}else{$class = "impair";} 
  echo '
  <tr class='.$class.'>
    <td align="left">&nbsp;Nouveau Mot de passe&nbsp;</td>
    <td align="left"><input name="txt_MDP_NEW" type="password" value="" size="50"/></td>
  </tr>';
  $j++;
  if ($j%2) { $class = "pair";}else{$class = "impair";} 
  echo '
  <tr class='.$class.'>
    <td align="left">&nbsp;V&eacute;rification du Nouveau Mot de passe&nbsp;</td>
    <td align="left"><input name="txt_MDP_NEW_VERIF" type="password" value="" size="50"/></td>
  </tr>';

	if($STOP_INFO_MDP_NEW==1){
    echo '
    <tr class="titre">
      <td colspan="2" align="center"><h2>&nbsp;Merci de saisir deux nouveau mot de passe identique.&nbsp;</h2></td>
    </tr>';
	}
	if($STOP_INFO_MDP==1){
    echo '
    <tr class="titre">
      <td colspan="2" align="center"><h2>&nbsp;Merci mettre votre ancien mot de passe.&nbsp;</h2></td>
    </tr>';
	}
	if($STOP_INFO_MDP_NEW==2){
    echo '
    <tr class="titre">
      <td colspan="2" align="center"><h2>&nbsp;Merci de saisir un mot de passe de plus de 5 caract&egrave;res.&nbsp;</h2></td>
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
    if($action=='Modif'){
    	echo '<input name="btn" type="submit" id="btn" value="Modifier">';
    echo '<input name="btn" type="submit" id="btn" value="RAZ">';
    } 
    echo '
    <input type="hidden" name="ID" value="'.$ID.'">
    <input type="hidden" name="action" id="action" value="'.$action.'">
    </h2>
    </td>
	</tr>
	<tr class="titre">
    <td colspan="2" align="center"><h2>&nbsp;[&nbsp;<a href="./index.php">Retour</a>&nbsp;]&nbsp;</h2></td>
	</tr>
	</table>
</form>
<br/><br/>
</div>';
mysql_close(); 
?>