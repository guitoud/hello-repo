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
    
    $rq_info_type_acces="
    SELECT `UTILISATEUR_ID`
    FROM `moteur_utilisateur` 
    WHERE 
    UPPER(`LOGIN`)=UPPER('".$UTILISATEUR."') 
    AND `TYPE_LOGIN`='BDD'
    AND `ENABLE`='Y'
    ";
    $res_rq_info_type_acces = mysql_query($rq_info_type_acces, $mysql_link) or die(mysql_error());
    $tab_rq_info_type_acces = mysql_fetch_assoc($res_rq_info_type_acces);
    $total_ligne_rq_info_type_acces=mysql_num_rows($res_rq_info_type_acces);
    mysql_free_result($res_rq_info_type_acces);
    if($total_ligne_rq_info_type_acces!=0){
    $MDP=md5(addslashes(trim($tab_var['txt_MDP'])));
    $rq_info="
    SELECT `UTILISATEUR_ID`, `LOGIN`, `NOM`,`PRENOM`,`ACCES`,`TYPE_LOGIN`
    FROM `moteur_utilisateur` 
    WHERE 
    UPPER(`LOGIN`)=UPPER('".$UTILISATEUR."') AND
    `MDP_MD5`='".$MDP."' AND
    `TYPE_LOGIN`='BDD' AND
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
      `ENABLE`='N' AND `TYPE_LOGIN`='BDD'
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
      $_SESSION['TYPE_LOGIN']=$tab_rq_info['TYPE_LOGIN'];
      $INFO_LOGIN='OK';

      mysql_free_result($res_rq_info);
      
      $rq_info_guest="
      SELECT `UTILISATEUR_ID`
      FROM `moteur_utilisateur` 
      WHERE 
      UPPER(`LOGIN`)=UPPER('".$_SESSION['LOGIN']."')
      AND `ENABLE`='Y' AND `TYPE_LOGIN`='BDD'
      ";
      $res_rq_info_guest = mysql_query($rq_info_guest, $mysql_link) or die(mysql_error());
      $tab_rq_info_guest = mysql_fetch_assoc($res_rq_info_guest);
      $total_ligne_rq_info_guest=mysql_num_rows($res_rq_info_guest);
      mysql_free_result($res_rq_info_guest);
      $TRACE_CATEGORIE='Login';
      $TRACE_TABLE='moteur_utilisateur';
      $TRACE_REF_ID=$tab_rq_info_guest['UTILISATEUR_ID'];
      $TRACE_ACTION='Login';
      $TRACE_ETAT='BDD_'.session_id();
      moteur_trace($TRACE_CATEGORIE,$TRACE_TABLE,$TRACE_REF_ID,$TRACE_ACTION,$TRACE_ETAT);

      $MDP=md5(addslashes(trim(strtoupper($tab_rq_info['NOM']))));
      $rq_info="
      SELECT `LOGIN`
      FROM `moteur_utilisateur`
      WHERE
      `LOGIN`='".$tab_rq_info['LOGIN']."' AND
      `MDP_MD5`='".$MDP."' AND
      `ENABLE`='Y' AND `TYPE_LOGIN`='BDD'
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
	        url=("./index.php");
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
    }else{
	$baseDN = "ou=people,o=lpfuserroot";
	$ldapServer = "158.156.35.20";
	$ldapServerPort = "3796";
	$UTILISATEUR=trim($tab_var['txt_UTILISATEUR']);
	$MDP=trim($tab_var['txt_MDP']);
	if($MDP==''){$MDP='motdepassevide';}
	$rdn=$UTILISATEUR;
	$mdp=$MDP;
	$dn = 'uid='.$rdn.','.$baseDN;
	
	//echo "Connexion au serveur <br />";
	$conn=ldap_connect($ldapServer, $ldapServerPort);
	
	// on teste : le serveur LDAP est-il trouve?
	if($conn){
	 //echo 'Le resultat de connexion est '.$conn .'<br />';
	 // Connexion authentifie
	 //echo 'Connexion authentifiee...<br/>';
	 $bindServerLDAP=@ldap_bind($conn,$dn,$mdp); 
	 //echo 'Liaison au serveur : '. ldap_error($conn).'<br/>';
	 // en cas de succ�s de la liaison, renvoie Vrai
	 if($bindServerLDAP){
	  //echo 'Le resultat de connexion est '.$bindServerLDAP.' <br/>';
	    $rq_info="
	    SELECT `UTILISATEUR_ID`, `LOGIN`, `NOM`,`PRENOM`
	    FROM `moteur_utilisateur` 
	    WHERE 
	    UPPER(`LOGIN`)=UPPER('".$UTILISATEUR."') AND
	    `TYPE_LOGIN`='LDAP' AND
	    `ENABLE`='Y'
	    ";
	    $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
	    $tab_rq_info = mysql_fetch_assoc($res_rq_info);
	    $total_ligne_rq_info=mysql_num_rows($res_rq_info);
	    if($total_ligne_rq_info==0){
		//echo "Recherche suivant le filtre (sn=*) <br />";
		$justthese = array( "ou","cn", "sn", "givenname");
		$query = "sn=*";
		$result=ldap_read($conn, $dn, $query,$justthese);
		//echo 'Le resultat de la recherche est '.$result.' <br />';
		
		//echo 'Le nombre d\'entrees retournees est '.ldap_count_entries($conn,$result).'<p />';
		//echo 'Lecture de ces entrees ....<p />';
		$info = ldap_get_entries($conn, $result);
		//echo 'Donnees pour '.$info['count'].' entrees:<p />';
		
		for ($i=0; $i < $info['count']; $i++) {
			//echo 'cn est : '. $info[$i]['cn'][0] .'<br />';
			//echo 'sn est : '. $info[$i]['sn'][0] .'<br />';
			//echo 'premiere entree cn : '. $info[$i]['givenname'][0] .'<br />';
			$NOM_ldap=$info[$i]['sn'][0];
			$PRENOM=$info[$i]['givenname'][0];
			if(substr_count($NOM_ldap, '(')==0){
				$COMPLEMENT='';
			}else{
				$COMPLEMENT=substr($NOM_ldap,strpos($NOM_ldap,"("));
			}

		}
		$LOGIN=strtoupper($UTILISATEUR);
		$EMAIL='';
		$EMAIL_FULL='';
		$NOM=trim(str_replace($COMPLEMENT,'',$NOM_ldap));
		if(substr_count($NOM_ldap, '(')==0){
			$SOCIETE='';
		}else{
			$SOCIETE=trim(str_replace('(','',str_replace(')','',str_replace($NOM,'',$NOM_ldap))));
		}
		
		$MDP_MD5='';
		$ACCES='E';
		$sql="
		INSERT INTO `moteur_utilisateur` 
		( `UTILISATEUR_ID` , `LOGIN` , `NOM` , `PRENOM` , `EMAIL` ,`EMAIL_FULL` ,`COMPLEMENT` , `SOCIETE` , `MDP_MD5` , `ACCES`,`TYPE_LOGIN` ,`ENABLE` )
        	VALUES (
        	NULL , '".$LOGIN."', '".ucwords(strtolower($NOM))."', '".ucwords(strtolower($PRENOM))."', '".$EMAIL."', '".$EMAIL_FULL."', '".$COMPLEMENT."','".$SOCIETE."', '".$MDP_MD5."', '".$ACCES."' ,'LDAP', 'Y'
        	);";
        	//echo $sql;
        	mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
        	
        	$rq_info="
		SELECT `UTILISATEUR_ID`, `LOGIN`, `NOM`,`PRENOM`,`ACCES`,`TYPE_LOGIN`
		FROM `moteur_utilisateur` 
		WHERE 
		UPPER(`LOGIN`)=UPPER('".$UTILISATEUR."') AND
		`TYPE_LOGIN`='LDAP' AND
		`ENABLE`='Y'
		";
		$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
		$tab_rq_info = mysql_fetch_assoc($res_rq_info);
		$total_ligne_rq_info=mysql_num_rows($res_rq_info);
        
		$_SESSION['VALID']='OK';
		$_SESSION['ACCES']=$tab_rq_info['ACCES'];
		$_SESSION['LOGIN']=$tab_rq_info['LOGIN'];
		$_SESSION['NOM']=$tab_rq_info['NOM'];
		$_SESSION['PRENOM']=$tab_rq_info['PRENOM'];
		$_SESSION['id']=$tab_rq_info['UTILISATEUR_ID'];
		$_SESSION['TYPE_LOGIN']=$tab_rq_info['TYPE_LOGIN'];
		$INFO_LOGIN='OK';
        
		$TABLE_SQL_SQL='moteur_utilisateur';       
		historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');
		
		$rq_user_info_modif="
		SELECT `UTILISATEUR_ID` FROM `moteur_utilisateur` WHERE `LOGIN`='".$LOGIN."' AND `TYPE_LOGIN`='LDAP' AND `ENABLE`='Y' LIMIT 1";
		$res_rq_user_info_modif = mysql_query($rq_user_info_modif, $mysql_link) or die(mysql_error());
		$tab_rq_user_info_modif = mysql_fetch_assoc($res_rq_user_info_modif);
		$total_ligne_rq_user_info_modif=mysql_num_rows($res_rq_user_info_modif);
		$UTILISATEUR_ID=$tab_rq_user_info_modif['UTILISATEUR_ID'];
		mysql_free_result($res_rq_user_info_modif);
		
		$rq_role_info_modif="
		SELECT `ROLE_ID` ,`ROLE` FROM `moteur_role` WHERE `ROLE`='GUEST' LIMIT 1";
		$res_rq_role_info_modif = mysql_query($rq_role_info_modif, $mysql_link) or die(mysql_error());
		$tab_rq_role_info_modif = mysql_fetch_assoc($res_rq_role_info_modif);
		$total_ligne_rq_role_info_modif=mysql_num_rows($res_rq_role_info_modif);
		$ROLE_ID=$tab_rq_role_info_modif['ROLE_ID'];
		$ROLE_DBB=$tab_rq_role_info_modif['ROLE'];
		$ROLE_info=0;
		$rq_role_info_modif_actif="
		SELECT `ROLE_UTILISATEUR_ID`, `ROLE_UTILISATEUR_ACCES` 
		FROM `moteur_role_utilisateur` 
		WHERE `ROLE_ID`='".$ROLE_ID."' AND 
		`UTILISATEUR_ID`='".$UTILISATEUR_ID."'
		LIMIT 1";
		$res_rq_role_info_modif_actif = mysql_query($rq_role_info_modif_actif, $mysql_link) or die(mysql_error());
		$tab_rq_role_info_modif_actif = mysql_fetch_assoc($res_rq_role_info_modif_actif);
		$total_ligne_rq_role_info_modif_actif=mysql_num_rows($res_rq_role_info_modif_actif);
		
		mysql_free_result($res_rq_role_info_modif_actif);
		if($total_ligne_rq_role_info_modif_actif=='0'){
			//ajoute les roles
			$sql="INSERT INTO `moteur_role_utilisateur` 
			( `ROLE_UTILISATEUR_ID`, `ROLE_ID`, `UTILISATEUR_ID`, `ROLE_UTILISATEUR_ACCES`  )
			VALUES ( NULL , '".$ROLE_ID."', '".$UTILISATEUR_ID."', '".$ROLE_info."');";
			mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
			
			$TABLE_SQL_SQL='moteur_role_utilisateur';       
			historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');	
		}else{
			$ROLE_UTILISATEUR_ID=$tab_rq_role_info_modif_actif['ROLE_UTILISATEUR_ID'];
			$sql="
			UPDATE `moteur_role_utilisateur` SET 
			`ROLE_UTILISATEUR_ACCES` = '".$ROLE_info."'
			WHERE `ROLE_UTILISATEUR_ID` ='".$ROLE_UTILISATEUR_ID."' LIMIT 1 ;";
			mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());      
			
			$TABLE_SQL_SQL='moteur_role_utilisateur';       
			historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');    
		}
		
	    }else{
	    	$rq_info="
		SELECT `UTILISATEUR_ID`, `LOGIN`, `NOM`,`PRENOM`,`ACCES`,`TYPE_LOGIN`
		FROM `moteur_utilisateur` 
		WHERE 
		UPPER(`LOGIN`)=UPPER('".$UTILISATEUR."') AND
		`TYPE_LOGIN`='LDAP' AND
		`ENABLE`='Y'
		";
		$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
		$tab_rq_info = mysql_fetch_assoc($res_rq_info);
		$total_ligne_rq_info=mysql_num_rows($res_rq_info);
        
		$_SESSION['VALID']='OK';
		$_SESSION['ACCES']=$tab_rq_info['ACCES'];
		$_SESSION['LOGIN']=$tab_rq_info['LOGIN'];
		$_SESSION['NOM']=$tab_rq_info['NOM'];
		$_SESSION['PRENOM']=$tab_rq_info['PRENOM'];
		$_SESSION['id']=$tab_rq_info['UTILISATEUR_ID'];
		$_SESSION['TYPE_LOGIN']=$tab_rq_info['TYPE_LOGIN'];
		$INFO_LOGIN='OK';
	    }
	    mysql_free_result($res_rq_info);
		$rq_info_guest="
		SELECT `UTILISATEUR_ID`
		FROM `moteur_utilisateur` 
		WHERE 
		UPPER(`LOGIN`)=UPPER('".$_SESSION['LOGIN']."')
		AND `ENABLE`='Y'
		";
		$res_rq_info_guest = mysql_query($rq_info_guest, $mysql_link) or die(mysql_error());
		$tab_rq_info_guest = mysql_fetch_assoc($res_rq_info_guest);
		$total_ligne_rq_info_guest=mysql_num_rows($res_rq_info_guest);
		mysql_free_result($res_rq_info_guest);
		$TRACE_CATEGORIE='Login';
		$TRACE_TABLE='moteur_utilisateur';
		$TRACE_REF_ID=$tab_rq_info_guest['UTILISATEUR_ID'];
		$TRACE_ACTION='Login';
		$TRACE_ETAT='LDAP_'.session_id();
		moteur_trace($TRACE_CATEGORIE,$TRACE_TABLE,$TRACE_REF_ID,$TRACE_ACTION,$TRACE_ETAT);
		
		
		$rq_info="
		SELECT `LOGIN` 
		FROM `moteur_utilisateur` 
		WHERE `LOGIN` = '".$_SESSION['LOGIN']."'
		AND `EMAIL_FULL` NOT LIKE '%@%'
		AND `ENABLE` = 'Y'
		AND `TYPE_LOGIN` = 'LDAP'
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
		        url=("./index.php");
		        window.location=url;
		        </script>
		        ';
		}
	        
	      }else{
	      //mettre l email � jour Admin_Action_Email 
			echo '
			<script language="JavaScript">
			url=("./index.php?ITEM=Admin_Action_Email");
			window.location=url;
			</script>
			';
	      }

	 }else{
	  //echo 'Liaison impossible au serveur ldap ...';
	  $INFO_LOGIN='KO';
	 }
	 //echo 'Fermeture de la connexion';
	 ldap_close($conn);
	
	}else{
	 //echo 'connexion impossible au serveur LDAP';
	 $INFO_LOGIN='LDAP_KO';
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
	unset($_SESSION['TYPE_LOGIN']);
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
<!--D�but page HTML --> 
<div align="center">

<form method="post" name="frm_login" id="frm_login" action="index.php?ITEM=login">
	<table class="table_inc">
	<tr class="titre">
	<td colspan="2" align="center">
		<h2>Identification d\'un utilisateur - AccessMaster</h2>
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
	if($INFO_LOGIN=='LDAP_KO'){
    echo '
    <tr class="titre">
    <td colspan="2" align="center">
    <h2>connexion impossible au serveur LDAP.</h2>
    </td>
    </tr>';
	}
	echo '
	<tr class="titre">
	<td colspan="2" align="center">
	<h2>
	<input name="btn" type="submit" id="btn" value="Login">
  </h2>
	</td>
	</tr>
</form>
</div>';
mysql_close(); 
?>
