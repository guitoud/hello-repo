<?PHP
//redirection si acces dirrect
if(substr_count($_SERVER['PHP_SELF'], 'index.php')==0){
  header("Location: ../");
  exit();
}
/*******************************************************************
   Interface Ajout utilisateur
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
$STOP_INFO_EMAIL=0;
$STOP_INFO_MDP=0;
$STOP_INFO_LOGIN=0;
$LOGIN='';
$NOM='';
$PRENOM='';
$EMAIL='';
$EMAIL_FULL='';
$COMPLEMENT='';
$SOCIETE='';
$MDP='';
$MDP_VERIF='';
$ROLE_ID='';
$ACCES='E';
$ENABLE='';
$action="Modif";
//$fichier_dest_cc='./Admin/destinataires_cc.txt';

function verifmail ($email_a_verif)
{
	$Syntaxe='#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,6}$#';
   	if(preg_match($Syntaxe,$email_a_verif)){
	     return 1;
	   }else{
	     return 0;
	}
}
//$var_resultats_test_email=verifmail($tab_var['txtemail']);
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
    $LOGIN='';
    $NOM='';
    $PRENOM='';
    $EMAIL='';
    $EMAIL_FULL='';
    $COMPLEMENT='';
    $SOCIETE='';
    $MDP='';
    $ACCES='E';
    $action='Ajout';
    $_GET['ITEM']='Admin_Ajout_Utilisateur ';
  }
  
  # Cas Ajouter
  if($tab_var['btn']=="Ajouter"){
    $LOGIN=addslashes(trim(htmlentities($tab_var['txt_LOGIN'])));
    $NOM=addslashes(trim(htmlentities($tab_var['txt_NOM'])));
    $PRENOM=addslashes(trim(htmlentities($tab_var['txt_PRENOM'])));
    $EMAIL=addslashes(trim(htmlentities($tab_var['txt_EMAIL'])));
    $EMAIL_FULL=addslashes(trim(htmlentities($tab_var['txt_EMAIL_FULL'])));
    $COMPLEMENT=addslashes(trim(htmlentities($tab_var['txt_COMPLEMENT'])));
    $MDP=addslashes(trim(htmlentities($tab_var['txt_MDP'])));
    $SOCIETE=addslashes(trim(htmlentities($tab_var['txt_SOCIETE'])));
    $ACCES=addslashes(trim(htmlentities($tab_var['txt_ACCES'])));


    $ENABLE='Y';
   
    if($LOGIN==''){
      $STOP=1;
    }
    if($NOM==''){
      $STOP=1;
    }
    if($PRENOM==''){
      $STOP=1;
    }
    if($EMAIL==''){
      $STOP=1;
    }
    if($EMAIL_FULL==''){
      $STOP=1;
    }else{
      $test_email=verifmail($EMAIL_FULL);
      if($test_email==0){
        $STOP=1;
        $STOP_INFO_EMAIL=1;
      }
    }
    if($MDP==''){
      $STOP=1;
    }else{
      if(strlen($MDP)<=3){
        $STOP=1;
        $STOP_INFO_MDP=1;
      }
    }


    if($STOP==0){
      $rq_user_info="
      SELECT `UTILISATEUR_ID` 
      FROM `moteur_utilisateur` 
      WHERE `LOGIN`='".$LOGIN."'";
      $res_rq_user_info = mysql_query($rq_user_info, $mysql_link) or die(mysql_error());
      $tab_rq_user_info = mysql_fetch_assoc($res_rq_user_info);
      $total_ligne_rq_user_info=mysql_num_rows($res_rq_user_info);
      if($total_ligne_rq_user_info!=0){
        $STOP_INFO_LOGIN=1;
      }else{
        //ajoute l utilisateur si non presente dans bdd
        $MDP_MD5=md5($MDP);
        $sql="
        INSERT INTO `moteur_utilisateur` ( `UTILISATEUR_ID` , `LOGIN` , `NOM` , `PRENOM` , `EMAIL` ,`EMAIL_FULL` ,`COMPLEMENT` , `SOCIETE` , `MDP_MD5` , `ACCES` ,`TYPE_LOGIN`,`ENABLE` )
        VALUES (
        NULL , '".$LOGIN."', '".$NOM."', '".$PRENOM."', '".$EMAIL."', '".$EMAIL_FULL."', '".$COMPLEMENT."','".$SOCIETE."', '".$MDP_MD5."', '".$ACCES."' ,'BDD' , 'Y'
        );";
        mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
        
        $TABLE_SQL_SQL='moteur_utilisateur';       
        historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');
        
        $rq_user_info_modif="
    SELECT `UTILISATEUR_ID` FROM `moteur_utilisateur` WHERE `LOGIN`='".$LOGIN."' AND `ENABLE`='Y' LIMIT 1";
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
	$ROLE='ROLE_';
	$ROLE .=$tab_rq_role_info_modif['ROLE'];
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
        
       /*
        //Gestion de la liste de diffusion CC
        $nb_cc=1;
        $pointeur_dest_cc = fopen($fichier_dest_cc, "r");
        if ($pointeur_dest_cc) 
        {
          //Boucle de lecture du fichier
          while (!feof($pointeur_dest_cc))
          {
            $ligne_dest_cc = fgets($pointeur_dest_cc, 4096);
            $cc_mail[$nb_cc]= $ligne_dest_cc;
            //echo $cc_mail[$nb_cc].'<br/>';
            $nb_cc++;
            //affichage du r&eacute;sultat
            $cc1 .= $ligne_dest_cc.',';
          }//while
          fclose($pointeur_dest_cc);
        }//if
        $destinataire=$EMAIL;
        $sujet = "[Outils] - creation de compte utilisateur"; // Titre du mail
        $from = "Admin Outils"; // c'est le nom du contact qui sera affich&eacute;
        $from_mail = "Vincent.Guibert-e@caissedesdepots.fr"; // Adresse de l'expediteur

        //G&eacute;n&eacute;ration des entetes complexes du mail
        $headers = "From: \"$from\"<$from_mail>\n";
        $headers .= "Reply-To: Vincent.Guibert-e@caissedesdepots.fr\n";  // Adresse de r&eacute;ponse
        //CC en du fonctionne comme ci-dessous
        //$headers .= "Cc: Vincent.Guibert-e@caissedesdepots.fr\n";
        for($j=1;$j<=$nb_cc-1;$j++){
          $mail_cc.=trim($cc_mail[$j]).', ';
        }
        $mail_cc.=trim($cc_mail[$nb_cc]).' ';
        //echo $mail_cc.'<br/>';
        $headers .= "Cc: ".$mail_cc."\n";
        $headers .= "MIME-Version: 1.0\n";
        $headers .= "Content-Type: text/html; charset='iso-8859-1'\n";
        $headers .= "Content-Transfer-Encoding: 8bit\n";
        
        $contenu .= '<html>';
        $contenu .= '<head>';
        $contenu .= '<title>.: Outils :.</title>';
        $contenu .= '</head>';
        $contenu .= '<body>';
        $contenu .= 'Bonjour,<br/>';
        $contenu .= '<br/>';
        $contenu .= 'Voici vos identifiants pour l\'utilisation des Outils.<br/>';
        $contenu .= '</br/>';
        $contenu .= 'Login : '.$LOGIN.'<br/>';
        $contenu .= 'Mot de passe : '.$MDP.'<br/>';
        $contenu .= '<br/>';
        $contenu .= 'Cordialement<br/>';
        $contenu .= 'le responsable des outils.';
        $contenu .= '</body>';
        $contenu .= '</html>';


        if(mail($destinataire,$sujet,$contenu,$headers)) 
        { 
            echo '<br>Le message a bien &eacute;t&eacute; envoy&eacute; avec les param&egrave;tres suivants :<br><br>';
            echo 'A  : '.$destinataire.'<br>';
            echo 'De : '.$from_mail.'<br>';
            echo 'Cc : '.$mail_cc.'<br>';
            //echo $headers ;
            
        }else{ 
          echo '<br>Le message n\'a pu &ecirc;tre envoy&eacute;'; 
        } 
        */

        
        echo '
        <script language="JavaScript">
        url=("./index.php?ITEM=Admin_Gestion_Utilisateurs");
        window.location=url;
        </script>
        ';
      }
      mysql_free_result($res_rq_user_info);
    }else{
      if($page_test!=3){
        $page_test=2;
      }
    }
  }
  
  
  # Cas Supprimer
  if($tab_var['btn']=="Supprimer"){
    $sql="
    UPDATE `moteur_utilisateur` SET 
    `ENABLE`='S'
    WHERE `UTILISATEUR_ID` ='".$ID."' LIMIT 1 ;";
    mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());      
        
    $TABLE_SQL_SQL='moteur_utilisateur';       
    historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');       
    $sql="
    UPDATE `moteur_role_utilisateur` SET 
    `ROLE_UTILISATEUR_ACCES` = '1'
    WHERE `UTILISATEUR_ID` ='".$ID."';";
    mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());      
    $TABLE_SQL_SQL='moteur_role_utilisateur';       
    historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');    
    
    $sql="OPTIMIZE TABLE `moteur_role_utilisateur` ";
    mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error()); 
    
    $TABLE_SQL_SQL='moteur_role_utilisateur';       
    historique_sql_new($sql,$TABLE_SQL_SQL,'OPTIMIZE');
    echo '
    <script language="JavaScript">
    url=("./index.php?ITEM=Admin_Gestion_Utilisateurs");
    window.location=url;
    </script>
    ';
  }
  
  # Cas Modifier
  if($tab_var['btn']=="Modifier"){
    $LOGIN=addslashes(trim(htmlentities($tab_var['txt_LOGIN'])));
    $NOM=addslashes(trim(htmlentities($tab_var['txt_NOM'])));
    $PRENOM=addslashes(trim(htmlentities($tab_var['txt_PRENOM'])));
    $EMAIL=addslashes(trim(htmlentities($tab_var['txt_EMAIL'])));
    $EMAIL_FULL=addslashes(trim(htmlentities($tab_var['txt_EMAIL_FULL'])));
    $COMPLEMENT=addslashes(trim(htmlentities($tab_var['txt_COMPLEMENT'])));
    $SOCIETE=addslashes(trim(htmlentities($tab_var['txt_SOCIETE'])));
    
    $ACCES=addslashes(trim(htmlentities($tab_var['txt_ACCES'])));
    $TYPE_LOGIN=$tab_var['TYPE_LOGIN'];
    if($TYPE_LOGIN=='LDAP'){
    	$MDP='';
    	$MDP_VERIF='';
    }else{
    	$MDP=addslashes(trim(htmlentities($tab_var['txt_MDP'])));
    	$MDP_VERIF=addslashes(trim(htmlentities($tab_var['txt_MDP_VERIF'])));
    }
    $ENABLE=$tab_var['ENABLE'];
   
    if($LOGIN==''){
      $STOP=1;
    }
    if($NOM==''){
      $STOP=1;
    }
    if($PRENOM==''){
      $STOP=1;
    }
    if($EMAIL==''){
      $STOP=1;
    }
    if($EMAIL_FULL==''){
      $STOP=1;
    }else{
      $test_email=verifmail($EMAIL_FULL);
      if($test_email==0){
        $STOP=1;
        $STOP_INFO_EMAIL=1;
      }
    }
    if($TYPE_LOGIN=='BDD'){
    if($MDP!=''){
      if(strlen($MDP)<=3){
        $STOP=1;
        $STOP_INFO_MDP=1;
      }
      if(strlen($MDP_VERIF)<=3){
        $STOP=1;
        $STOP_INFO_MDP=1;
      }
      if($MDP_VERIF!=$MDP){
        $STOP=1;
        $STOP_INFO_MDP=2;
        $page_test=3;
      }   
    }else{
      $STOP_INFO_MDP=1;
    }
    }


    if($STOP==0){
      $rq_user_info="
      SELECT `UTILISATEUR_ID` FROM `moteur_utilisateur` WHERE `LOGIN`='".$LOGIN."'";
      $res_rq_user_info = mysql_query($rq_user_info, $mysql_link) or die(mysql_error());
      $tab_rq_user_info = mysql_fetch_assoc($res_rq_user_info);
      $total_ligne_rq_user_info=mysql_num_rows($res_rq_user_info);
      if($total_ligne_rq_user_info!=0){     
        if($STOP_INFO_MDP!=0){
          $sql="
          UPDATE `moteur_utilisateur` SET 
          `LOGIN` = '".$LOGIN."',
          `NOM` = '".$NOM."',
          `PRENOM` = '".$PRENOM."',
          `EMAIL` = '".$EMAIL."',
          `EMAIL_FULL` = '".$EMAIL_FULL."',
          `COMPLEMENT` = '".$COMPLEMENT."',
          `SOCIETE` = '".$SOCIETE."',
          `ACCES` = '".$ACCES."',
          `ENABLE`='".$ENABLE."'
          WHERE `UTILISATEUR_ID` ='".$ID."' LIMIT 1 ;";
        }else{
          $MDP_MD5=md5($MDP);
          $sql="
          UPDATE `moteur_utilisateur` SET 
          `LOGIN` = '".$LOGIN."',
          `NOM` = '".$NOM."',
          `PRENOM` = '".$PRENOM."',
          `EMAIL` = '".$EMAIL."',
          `EMAIL_FULL` = '".$EMAIL_FULL."',
          `COMPLEMENT` = '".$COMPLEMENT."',
          `SOCIETE` = '".$SOCIETE."',
          `ACCES` = '".$ACCES."',
          `MDP_MD5` = '".$MDP_MD5."',
          `ENABLE`='".$ENABLE."'
          WHERE `UTILISATEUR_ID` ='".$ID."' LIMIT 1 ;";
          
        }

        //echo 'sql= '.$sql;
        mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());      
        
        $TABLE_SQL_SQL='moteur_utilisateur';       
        historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');       
                
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
	$rq_role_info_modif_actif="
	SELECT `ROLE_UTILISATEUR_ID`, `ROLE_UTILISATEUR_ACCES` 
	FROM `moteur_role_utilisateur` 
	WHERE `ROLE_ID`='".$ROLE_ID."' AND 
	`UTILISATEUR_ID`='".$ID."'
	LIMIT 1";
	$res_rq_role_info_modif_actif = mysql_query($rq_role_info_modif_actif, $mysql_link) or die(mysql_error());
	$tab_rq_role_info_modif_actif = mysql_fetch_assoc($res_rq_role_info_modif_actif);
	$total_ligne_rq_role_info_modif_actif=mysql_num_rows($res_rq_role_info_modif_actif);
	
	mysql_free_result($res_rq_role_info_modif_actif);
	if($total_ligne_rq_role_info_modif_actif=='0'){
	    //ajoute les roles
            $sql="INSERT INTO `moteur_role_utilisateur` 
            ( `ROLE_UTILISATEUR_ID`, `ROLE_ID`, `UTILISATEUR_ID`, `ROLE_UTILISATEUR_ACCES`  )
            VALUES ( NULL , '".$ROLE_ID."', '".$ID."', '".$ROLE_info."');";
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
	

    } while ($tab_rq_role_info_modif = mysql_fetch_assoc($res_rq_role_info_modif));
    $ligne= mysql_num_rows($res_rq_role_info_modif);
    if($ligne > 0) {
      mysql_data_seek($res_rq_role_info_modif, 0);
      $tab_rq_role_info_modif = mysql_fetch_assoc($res_rq_role_info_modif);
    }
	$sql="OPTIMIZE TABLE `moteur_role_utilisateur` ";
	mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error()); 
	
	$TABLE_SQL_SQL='moteur_role_utilisateur';       
	historique_sql_new($sql,$TABLE_SQL_SQL,'OPTIMIZE');
        echo '
        <script language="JavaScript">
        url=("./index.php?ITEM=Admin_Gestion_Utilisateurs");
        window.location=url;
        </script>
        ';
      }
      mysql_free_result($res_rq_user_info);
    }else{
      if($page_test!=3){
        $page_test=2;
      }
    }
  }
}
if($action=="Modif"){
  if(isset($_GET['ID'])){
    if(is_numeric($_GET['ID'])){
      $ID=$_GET['ID'];
      $rq_user_info="
      SELECT *
      FROM `moteur_utilisateur` 
      WHERE `UTILISATEUR_ID`='".$ID."'";
      $res_rq_user_info = mysql_query($rq_user_info, $mysql_link) or die(mysql_error());
      $tab_rq_user_info = mysql_fetch_assoc($res_rq_user_info);
      $total_ligne_rq_user_info=mysql_num_rows($res_rq_user_info);
      $LOGIN=$tab_rq_user_info['LOGIN'];
      $NOM=$tab_rq_user_info['NOM'];
      $PRENOM=$tab_rq_user_info['PRENOM'];
      $EMAIL=$tab_rq_user_info['EMAIL'];
      $EMAIL_FULL=$tab_rq_user_info['EMAIL_FULL'];
      $COMPLEMENT=$tab_rq_user_info['COMPLEMENT'];
      $SOCIETE=$tab_rq_user_info['SOCIETE'];
      $ACCES=$tab_rq_user_info['ACCES'];
      $TYPE_LOGIN=$tab_rq_user_info['TYPE_LOGIN'];
      $ENABLE=$tab_rq_user_info['ENABLE'];
    }
  }
  if(isset($_POST['ID'])){
    if(is_numeric($_POST['ID'])){
      $ID=$_POST['ID'];
      $rq_user_info="
      SELECT *
      FROM `moteur_utilisateur` 
      WHERE `UTILISATEUR_ID`='".$ID."'";
      $res_rq_user_info = mysql_query($rq_user_info, $mysql_link) or die(mysql_error());
      $tab_rq_user_info = mysql_fetch_assoc($res_rq_user_info);
      $total_ligne_rq_user_info=mysql_num_rows($res_rq_user_info);
      $LOGIN=$tab_rq_user_info['LOGIN'];
      $NOM=$tab_rq_user_info['NOM'];
      $PRENOM=$tab_rq_user_info['PRENOM'];
      $EMAIL=$tab_rq_user_info['EMAIL'];
      $EMAIL_FULL=$tab_rq_user_info['EMAIL_FULL'];
      $COMPLEMENT=$tab_rq_user_info['COMPLEMENT'];
      $SOCIETE=$tab_rq_user_info['SOCIETE'];
      $ACCES=$tab_rq_user_info['ACCES'];
      $TYPE_LOGIN=$tab_rq_user_info['TYPE_LOGIN'];
      $ENABLE=$tab_rq_user_info['ENABLE'];
    }
  }
}

echo '
<!--D&eacute;but page HTML -->  
<div align="center">

<form method="post" name="frm_utilisateur" id="frm_utilisateur" action="index.php?ITEM='.$_GET['ITEM'].'">
<table class="table_inc">
	<tr align="center" class="titre">';
    if($action=="Ajout"){
      echo '
				<td colspan="2"><h2>&nbsp;[&nbsp;Ajout d\'un Utilisateur&nbsp;]&nbsp;</h2></td>';
    }
    if($action=="Modif"){
      echo '
				<td colspan="2"><h2>&nbsp;[&nbsp;Modification de l\' Utilisateur '.$PRENOM.' '.$NOM.'&nbsp;]&nbsp;</h2>';
				if(substr_count($_SERVER["SCRIPT_FILENAME"], '/php/iab')==1){
				echo '</BR><font color=red><b>&nbsp;Attention ne pas faire la supression d\'un utilisateur s\'il a fait des ODTI le mois n-1&nbsp;</BR>&nbsp;</b></font>';
				}
				echo '
				</td>';
    }
echo'
	</tr>
	';
  $j++;
  if ($j%2) { $class = "pair";}else{$class = "impair";} 
	echo '
	<tr class='.$class.'>
    <td align="left">&nbsp;Login&nbsp;</td>
    <td align="left"><input name="txt_LOGIN" type="text" value="'.stripslashes($LOGIN).'" size="50"/></td>
	</tr>
	';
  $j++;
  if ($j%2) { $class = "pair";}else{$class = "impair";} 
	echo '
	<tr class='.$class.'>
    <td align="left">&nbsp;Nom&nbsp;</td>
    <td align="left"><input name="txt_NOM" type="text" value="'.stripslashes($NOM).'" size="50"/></td>
	</tr>
	';
  $j++;
  if ($j%2) { $class = "pair";}else{$class = "impair";} 
	echo '
	<tr class='.$class.'>
    <td align="left">&nbsp;Pr&eacute;nom&nbsp;</td>
    <td align="left"><input name="txt_PRENOM" type="text" value="'.stripslashes($PRENOM).'" size="50"/></td>
	</tr>
	';
  $j++;
  if ($j%2) { $class = "pair";}else{$class = "impair";} 
	echo '
	<tr class='.$class.'>
    <td align="left">&nbsp;Email&nbsp;</td>
    <td align="left"><input name="txt_EMAIL" type="text" value="'.stripslashes($EMAIL).'" size="50"/></td>
	</tr>';
  $j++;
  if ($j%2) { $class = "pair";}else{$class = "impair";} 
	echo '
	<tr class='.$class.'>
    <td align="left">&nbsp;Email (full)&nbsp;</td>
    <td align="left"><input name="txt_EMAIL_FULL" type="text" value="'.stripslashes($EMAIL_FULL).'" size="50"/></td>
	</tr>';
  $j++;
  if ($j%2) { $class = "pair";}else{$class = "impair";} 
	echo '
	<tr class='.$class.'>
    <td align="left">&nbsp;Soci&eacute;t&eacute;e&nbsp;</td>
    <td align="left"><input name="txt_SOCIETE" type="text" value="'.stripslashes($SOCIETE).'" size="50"/></td>
	</tr>';
  $j++;
  if ($j%2) { $class = "pair";}else{$class = "impair";} 
	echo '
	<tr class='.$class.'>
    <td align="left">&nbsp;Compl&eacute;ment&nbsp;</td>
    <td align="left"><input name="txt_COMPLEMENT" type="text" value="'.stripslashes($COMPLEMENT).'" size="50"/></td>
	</tr>';
	$j++;
    if ($j%2) { $class = "pair";}else{$class = "impair";} 
    echo '
    <tr class='.$class.'>
      <td align="left">&nbsp;Type d\'acc&eacute;s&nbsp;</td>
      <td align="left">&nbsp;Lecture&nbsp;<INPUT type=radio name="txt_ACCES" value="L"';if($ACCES=='L'){echo 'CHECKED';} echo '>&nbsp; /&nbsp;Lecture/&eacute;criture&nbsp;<INPUT type=radio name="txt_ACCES" value="E"'; if($ACCES=='E'){echo 'CHECKED';} echo '></td>
    </tr>';
    if($action=='Ajout'){
  $j++;
  if ($j%2) { $class = "pair";}else{$class = "impair";} 
	echo '
	<tr class='.$class.'>
    <td align="left">&nbsp;Mot de passe&nbsp;</td>
    <td align="left"><input name="txt_MDP" type="password" value="'.stripslashes($MDP).'" size="50"/></td>
	</tr>';
	}
	if($action=='Modif'){
	if($TYPE_LOGIN=='BDD'){
      $j++;
  if ($j%2) { $class = "pair";}else{$class = "impair";} 
	echo '
	<tr class='.$class.'>
    <td align="left">&nbsp;Mot de passe&nbsp;</td>
    <td align="left"><input name="txt_MDP" type="password" value="'.stripslashes($MDP).'" size="50"/></td>
	</tr>';
    $j++;
    if ($j%2) { $class = "pair";}else{$class = "impair";} 
    echo '
    <tr class='.$class.'>
      <td align="left">&nbsp;V&eacute;rification du Mot de passe&nbsp;</td>
      <td align="left"><input name="txt_MDP_VERIF" type="password" value="'.stripslashes($MDP_VERIF).'" size="50"/></td>
    </tr>';
	}
    $rq_info="
    SELECT `ROLE_ID`,`ROLE` 
    FROM `moteur_role`
    ORDER BY `ROLE`
    ";
    $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
    $tab_rq_info = mysql_fetch_assoc($res_rq_info);
    $total_ligne_rq_info=mysql_num_rows($res_rq_info); 
    do {
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      $ROLE_BDD=$tab_rq_info['ROLE'];
      $ROLE_ID_BDD=$tab_rq_info['ROLE_ID'];
	$DROIT='1';
	$rq_droit_role_info="
	SELECT `ROLE_UTILISATEUR_ACCES` FROM `moteur_role_utilisateur` WHERE `UTILISATEUR_ID`='".$ID."' AND `ROLE_ID`='".$ROLE_ID_BDD."'";
	$res_rq_droit_role_info = mysql_query($rq_droit_role_info, $mysql_link) or die(mysql_error());
	$tab_rq_droit_role_info = mysql_fetch_assoc($res_rq_droit_role_info);
	$total_ligne_rq_droit_role_info=mysql_num_rows($res_rq_droit_role_info);
	if($total_ligne_rq_droit_role_info!=0){
	$DROIT=$tab_rq_droit_role_info['ROLE_UTILISATEUR_ACCES'];
	}
	mysql_free_result($res_rq_droit_role_info);
          echo '
    <tr class='.$class.'>
      <td align="left">&nbsp;'.$ROLE_BDD.'&nbsp;</td>
      <td align="left">&nbsp;oui&nbsp;<INPUT type=radio name="ROLE_'.$ROLE_BDD.'" value="0"';if($DROIT=='0'){echo 'CHECKED';} echo '>&nbsp; /&nbsp;non&nbsp;<INPUT type=radio name="ROLE_'.$ROLE_BDD.'" value="1"'; if($DROIT=='1'){echo 'CHECKED';} echo '></td>
    </tr>';
    } while ($tab_rq_info = mysql_fetch_assoc($res_rq_info));
    $ligne= mysql_num_rows($res_rq_info);
    if($ligne > 0) {
      mysql_data_seek($res_rq_info, 0);
      $tab_rq_info = mysql_fetch_assoc($res_rq_info);
    }
    $j++;
    if ($j%2) { $class = "pair";}else{$class = "impair";} 
    echo '
    <tr class='.$class.'>
      <td align="left">&nbsp;Actif&nbsp;</td>
      <td align="left">&nbsp;oui&nbsp;<INPUT type=radio name="ENABLE" value="Y"';if($ENABLE=='Y'){echo 'CHECKED';} echo '>&nbsp; /&nbsp;non&nbsp;<INPUT type=radio name="ENABLE" value="N"'; if($ENABLE=='N'){echo 'CHECKED';} echo '></td>
    </tr>';
  
	}

	if($STOP_INFO_EMAIL==1){
    echo '
    <tr class="titre">
      <td colspan="2" align="center"><h2>&nbsp;Merci de saisir un email.&nbsp;</h2></td>
    </tr>';
	}
	if($STOP_INFO_MDP==1){
    echo '
    <tr class="titre">
      <td colspan="2" align="center"><h2>&nbsp;Merci de saisir mot de passe de plus de 5 caract&egrave;res.&nbsp;</h2></td>
    </tr>';
	}
	if($STOP_INFO_MDP==2){
    echo '
    <tr class="titre">
      <td colspan="2" align="center"><h2>&nbsp;Merci mettre des mots de passe identique.&nbsp;</h2></td>
    </tr>';
	}
	if($STOP_INFO_LOGIN==1){
    echo '
    <tr class="titre">
      <td colspan="2" align="center"><h2>&nbsp;Le Login '.$LOGIN.' existe d&eacute;j&agrave;, merci de choisir un autre Login.&nbsp;</h2></td>
    </tr>';
	}
	$STOP_INFO_LOGIN=0;
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
    	if(acces_sql()!="L"){
   	echo '<input name="btn" type="submit" id="btn" value="Modifier">';
   	echo '<input name="btn" type="submit" id="btn" value="Supprimer">';
    }
    echo '<input name="btn" type="submit" id="btn" value="RAZ">';
    echo '<input type="hidden" name="TYPE_LOGIN" value="'.$TYPE_LOGIN.'">';
    
    } 
    echo '
    <input type="hidden" name="ID" value="'.$ID.'">
    <input type="hidden" name="action" id="action" value="'.$action.'">
    </h2>
    </td>
	</tr>
	<tr class="titre">
    <td colspan="2" align="center"><h2>&nbsp;[&nbsp;<a href="./index.php?ITEM=Admin_Gestion_Utilisateurs">Retour - Liste des Utilisateurs</a>&nbsp;]&nbsp;</h2></td>
	</tr>
	</table>
</form>
<br/><br/>
</div>';

mysql_close($mysql_link); 
?>