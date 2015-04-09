<?PHP
/*******************************************************************
   Interface Login
   Version 1.1.0  
**************************************************Guibert Vincent***
*******************************************************************/
//Appel du Fichier de configuration
require("./cf/conf_outil_icdc.php"); 
$j=0;
$UTILISATEUR='';
$UTILISATEUR_ID='';
$MDP='';
$INFO_LOGIN='';
$VALID='OK';
if(isset($_GET['action'])){
  $action=$_GET['action'];
}else{
  $action='Login';
}

$tab_var=$_POST;
if(empty($tab_var['btn'])){
}else{

  # Cas Login
  if($tab_var['btn']=="Login"){
    $UTILISATEUR=addslashes(trim($tab_var['txt_UTILISATEUR']));
    $MDP=md5(addslashes(trim($tab_var['txt_MDP'])));
    $rq_info="
    SELECT `UTILISATEUR_ID`, `LOGIN`, `NOM`,`PRENOM`,`ACCES`
    FROM `moteur_utilisateur` 
    WHERE 
    UPPER(`LOGIN`)=UPPER('".$UTILISATEUR."') AND
    `MDP_MD5`='".$MDP."' AND
    `ENABLE`='Y'
    ";
    $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
    $tab_rq_info = mysql_fetch_assoc($res_rq_info);
    $total_ligne_rq_info=mysql_num_rows($res_rq_info);
    //echo 'rq_info = '.$rq_info.'<br/>';
    if($total_ligne_rq_info==0){
      $UTILISATEUR=$UTILISATEUR;
      $MDP='';
      $action=='Login';
      $_SESSION['VALID']='KO';
      $INFO_LOGIN='KO';
      mysql_free_result($res_rq_info);
      $rq_info_HS="
      SELECT `LOGIN`
      FROM `moteur_utilisateur` 
      WHERE
      UPPER(`LOGIN`)=UPPER('".$UTILISATEUR."') AND
      `ENABLE`='N'
      ";
      $res_rq_info_HS = mysql_query($rq_info_HS, $mysql_link) or die(mysql_error());
      $tab_rq_info_HS = mysql_fetch_assoc($res_rq_info_HS);
      $total_ligne_rq_info_HS=mysql_num_rows($res_rq_info_HS);
      //echo 'rq_info_HS = '.$rq_info_HS.'<br/>';
      if($total_ligne_rq_info_HS!=0){
        $INFO_LOGIN='HS';
      }
      mysql_free_result($res_rq_info_HS);
    }else{
      $_SESSION['VALID']='OK';
      $_SESSION['ACCES']=$tab_rq_info['ACCES'];
      $_SESSION['LOGIN']=$tab_rq_info['LOGIN'];
      $_SESSION['NOM']=$tab_rq_info['NOM'];
      $_SESSION['PRENOM']=$tab_rq_info['PRENOM'];
      $_SESSION['id']=$tab_rq_info['UTILISATEUR_ID'];
      $INFO_LOGIN='OK';

      mysql_free_result($res_rq_info);

      $MDP=md5(addslashes(trim(strtoupper($tab_rq_info['NOM']))));
      $rq_info="
      SELECT `LOGIN`
      FROM `moteur_utilisateur`
      WHERE
      `LOGIN`='".$tab_rq_info['LOGIN']."' AND
      `MDP_MD5`='".$MDP."' AND
      `ENABLE`='Y'
      ";
      $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
      $tab_rq_info = mysql_fetch_assoc($res_rq_info);
      $total_ligne_rq_info=mysql_num_rows($res_rq_info);
      mysql_free_result($res_rq_info);
      if($total_ligne_rq_info==0){
      	if(isset($_SESSION["QUERY_STRING"])){
		if($_SESSION["QUERY_STRING"]==''){
			echo '
        		<script language="JavaScript">
		        url=("./");
		        window.location=url;
		        </script>
		        ';
		}else{
			echo '
        		<script language="JavaScript">
		        url=("./index.php?'.$_SESSION['QUERY_STRING'].'");
		        window.location=url;
		        </script>
		        ';
		}
	}else{
		echo '
	        <script language="JavaScript">
	        url=("./index.php?'.$_SESSION['QUERY_STRING'].'");
	        window.location=url;
	        </script>
	        ';
	}
        
      }else{
		echo '
		<script language="JavaScript">
		url=("./index.php?ITEM=Admin_Action_MDP&test=KO");
		window.location=url;
		</script>
		';
      }

    }
  }
}

if($action=='UnLogin'){
	unset($_SESSION['VALID']);
	unset($_SESSION['LOGIN']);
	unset($_SESSION['NOM']);
	unset($_SESSION['PRENOM']);
	unset($_SESSION['ACCES']);
	unset($_SESSION['id']);
	if(isset($_SESSION["sa_Appli"])){
		unset($_SESSION['sa_Appli']);	
	}
	if(isset($_SESSION["sa_DateInc"])){
		unset($_SESSION['sa_DateInc']);	
	}
	if(isset($_SESSION["sa_Type"])){
		unset($_SESSION['sa_Type']);	
	}
	if(isset($_SESSION["ch_NumChron"])){
		unset($_SESSION['ch_NumChron']);	
	}
	if(isset($_SESSION["ch_Date_critere"])){
		unset($_SESSION['ch_Date_critere']);	
	}
	if(isset($_SESSION["a_Appli"])){
		unset($_SESSION['a_Appli']);	
	}
	if(isset($_SESSION["a_DateInc"])){
		unset($_SESSION['a_DateInc']);	
	}
	if(isset($_SESSION["d_MoisTaux"])){
		unset($_SESSION['d_MoisTaux']);	
	}
	if(isset($_SESSION["d_AnneeTaux"])){
		unset($_SESSION['d_AnneeTaux']);	
	}
	if(isset($_SESSION["d_DateIndispo"])){
		unset($_SESSION['d_DateIndispo']);	
	}
	if(isset($_SESSION["d_choix_recherche"])){
		unset($_SESSION['d_choix_recherche']);	
	}
	if(isset($_SESSION["d_Appli"])){
		unset($_SESSION['d_Appli']);	
	}
	if(isset($_SESSION["d_Infra"])){
		unset($_SESSION['d_Infra']);	
	}
	if(isset($_SESSION["i_Appli"])){
		unset($_SESSION['i_Appli']);	
	}
	if(isset($_SESSION["i_Date"])){
		unset($_SESSION['i_Date']);	
	}
	if(isset($_SESSION["i_Nature"])){
		unset($_SESSION['i_Nature']);	
	}
	if(isset($_SESSION["i_Etat"])){
		unset($_SESSION['i_Etat']);	
	}
	if(isset($_SESSION["i_Degre"])){
		unset($_SESSION['i_Degre']);	
	}
	if(isset($_SESSION["i_Au_BSP"])){
		unset($_SESSION['i_Au_BSP']);	
	}
	if(isset($_SESSION["QUERY_STRING"])){
	  	unset($_SESSION['QUERY_STRING']);	
	}

  $MDP='';
  $action='Login';
  $_SESSION['VALID']='KO';
}
?>

<script type="text/javascript">
// <![CDATA[
function PMA_focusInput()
{
    document.getElementById('txt_UTILISATEUR').focus();
}
window.setTimeout('PMA_focusInput()', 500);
// ]]>

</script>
<?PHP
echo '
<!--Début page HTML --> 
<div align="center">

<form method="post" name="frm_login" id="frm_login" action="index.php?ITEM=login">
	<table class="table_inc">
	<tr class="titre">
	<td colspan="2" align="center">
		<h2>Identification d\'un utilisateur</h2>
	</td>
	</tr>
	<tr class="impair">
	<td align="left">Utilisateur</td>
	<td align="left">
	<input name="txt_UTILISATEUR" id="txt_UTILISATEUR"  type="text" value="'.$UTILISATEUR.'" size="50" />
  </td>
	</tr>
	<tr class="pair">
	<td align="left">Mot de passe</td>
	<td align="left">
    <input name="txt_MDP" type="password" value="'.$MDP.'" size="50"/>
	</td>
	</tr>';
	if($INFO_LOGIN=='KO'){
    echo '
    <tr class="titre">
    <td colspan="2" align="center">
    <h2>Erreur de login ou de mot de passe.</h2>
    </td>
    </tr>';
	}
	if($INFO_LOGIN=='HS'){
    echo '
    <tr class="titre">
    <td colspan="2" align="center">
    <h2>Utilisateur non actif.</h2>
    </td>
    </tr>';
	}
	echo '
	<tr class="titre">
	<td colspan="2" align="center">
	<h2>
	<input name="btn" type="submit" id="btn" value="Login">
	<!--<input name="btn" type="submit" id="btn" value="RAZ">-->
  </h2>
	</td>
	</tr>
	<!--<tr class="titre">
	<td colspan="2" align="center">
		<h2>[&nbsp;<a href="./">Retour</a>&nbsp;]</h2>
	</td>
	</tr>-->
	</table>
	
</form>
<br/><br/>
</div>';
mysql_close(); 
?>

