<?PHP
//redirection si acces dirrect
if(substr_count($_SERVER['PHP_SELF'], 'index.php')==0){
  header("Location: ../"); 
  exit(); 
}
/*******************************************************************
   Interface FAR action
   Version 1.0.0  
  05/10/2010 - VGU - Creation fichier
**************************************************Guibert Vincent***
*******************************************************************/

require("./cf/conf_outil_icdc.php"); 
require_once("./cf/fonctions.php");
require_once("./cf/autre_fonctions.php");
 
require_once('./changement/changement_Conf_mail.php') ; 

$ENV =substr($_SERVER["SCRIPT_FILENAME"], 1, 1);

$j=0;

$STOP=0;
$DATE_MODIFICATION=date("d/m/Y H:i:s");
$CHANGEMENT_FAR_INSCRIPTION="Non";
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
//echo 'ID = '.$ID.'<br>';
if(isset($tab_var['ID'])){
  if(is_numeric($tab_var['ID'])){
    $ID=$tab_var['ID'];
  }
}

if(isset($tab_var['action'])){
  $action=$tab_var['action'];
}
if(isset($_SESSION['LOGIN'])){
	$LOGIN=$_SESSION['LOGIN'];
	$rq_Selectionner_user ="
	SELECT `UTILISATEUR_ID`,`LOGIN`, `NOM`, `PRENOM` 
	FROM `moteur_utilisateur` 
	WHERE `LOGIN` = '".$LOGIN."'";
	$res_rq_Selectionner_user = mysql_query($rq_Selectionner_user, $mysql_link) or die(mysql_error());
	$tab_rq_Selectionner_user = mysql_fetch_assoc($res_rq_Selectionner_user);
	$total_ligne_Selectionner_user = mysql_num_rows($res_rq_Selectionner_user);
	if($total_ligne_Selectionner_user==0){
		$NOM='';
		$UTILISATEUR_ID=0;
		$PRENOM='';
		$LOGIN='';
	}else{
		$NOM=$tab_rq_Selectionner_user['NOM'];
		$UTILISATEUR_ID=$tab_rq_Selectionner_user['UTILISATEUR_ID'];
		$PRENOM=$tab_rq_Selectionner_user['PRENOM'];
		$LOGIN=$tab_rq_Selectionner_user['LOGIN'];
	}
	mysql_free_result($res_rq_Selectionner_user);
	$rq_info="
	SELECT `moteur_role_utilisateur`.`ROLE_UTILISATEUR_ACCES` 
	FROM `moteur_role_utilisateur`,`moteur_utilisateur`,`moteur_role`
	WHERE 
	`moteur_role_utilisateur`.`UTILISATEUR_ID`=`moteur_utilisateur`.`UTILISATEUR_ID` AND
	`moteur_role_utilisateur`.`ROLE_ID`=`moteur_role`.`ROLE_ID` AND
	`moteur_utilisateur`.`LOGIN`='".$LOGIN."' AND
	(`moteur_role`.`ROLE`='ROOT' OR `moteur_role`.`ROLE`='ADMIN-CHANGEMENT' ) AND
	`moteur_role_utilisateur`.`ROLE_UTILISATEUR_ACCES`=0
	LIMIT 1";
	$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
	$tab_rq_info = mysql_fetch_assoc($res_rq_info);
	$total_ligne_rq_info=mysql_num_rows($res_rq_info); 
	if($total_ligne_rq_info==0){
		$ROLE=1;
	}else{
		$ROLE=$tab_rq_info['ROLE_UTILISATEUR_ACCES'];
	}
}else{
	$NOM='';
	$UTILISATEUR_ID=0;
	$PRENOM='';
	$LOGIN='';
	$ROLE=1;
}

$DATE_DU_JOUR=date("Ymd");
if(empty($tab_var['btn'])){
}else{

  # Cas Ajouter
  if($tab_var['btn']=="Ajouter"){
  	$ID=$tab_var['ID'];
  	$CHANGEMENT_ID=$ID;
  	$rq_info_far="
      	SELECT * 
      	FROM `changement_far_config`
      	WHERE `ENABLE` =0
      	";
      $res_rq_info_far = mysql_query($rq_info_far, $mysql_link) or die(mysql_error());
      $tab_rq_info_far = mysql_fetch_assoc($res_rq_info_far);
      $total_ligne_rq_info_far=mysql_num_rows($res_rq_info_far);
	$rq_date_deb_info="
	SELECT `CHANGEMENT_LISTE_DATE_DEBUT`
	FROM `changement_liste`
	WHERE 
	`changement_liste`.`CHANGEMENT_LISTE_ID` ='".$ID."' AND
	`changement_liste`.`ENABLE` = '0'
	";
	$res_rq_date_deb_info = mysql_query($rq_date_deb_info, $mysql_link) or die(mysql_error());
	$tab_rq_date_deb_info = mysql_fetch_assoc($res_rq_date_deb_info);
	$total_ligne_rq_date_deb_info=mysql_num_rows($res_rq_date_deb_info);
	$Date_Inter_Debut_bdd=0;
	$Date_Inter_Debut_bdd=$tab_rq_date_deb_info['CHANGEMENT_LISTE_DATE_DEBUT'];
	mysql_free_result($res_rq_date_deb_info);
	    if ( $ENV == "x" ){	 	
      if($Date_Inter_Debut_bdd < $DATE_DU_JOUR){
        $STOP=1;
        $DATE_LISTE=2;
      }
	  }

      if($total_ligne_rq_info_far!=0){
//verification
        do {
        	$CHANGEMENT_FAR_CONFIG_ID=$tab_rq_info_far['CHANGEMENT_FAR_CONFIG_ID'];
        	$CHANGEMENT_FAR_CONFIG_NOM='CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID;
        	
          if (!isset($tab_var[$CHANGEMENT_FAR_CONFIG_NOM])){
            $STOP=1;
          }else{
            $CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$CHANGEMENT_FAR_CONFIG_NOM])));
          }
      
        	
        } while ($tab_rq_info_far = mysql_fetch_assoc($res_rq_info_far));
        $ligne= mysql_num_rows($res_rq_info_far);
        if($ligne > 0) {
          mysql_data_seek($res_rq_info_far, 0);
          $tab_rq_info_far = mysql_fetch_assoc($res_rq_info_far);
        }
        if($STOP==0){
// ajout en base        
        	do {
        	$CHANGEMENT_FAR_CONFIG_ID=$tab_rq_info_far['CHANGEMENT_FAR_CONFIG_ID'];
        	$CHANGEMENT_FAR_CONFIG_NOM='CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID;
        	
          if (!isset($tab_var[$CHANGEMENT_FAR_CONFIG_NOM])){
            $STOP=1;
          }else{
            $CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$CHANGEMENT_FAR_CONFIG_NOM])));
            $sql="INSERT INTO `changement_far` (`CHANGEMENT_FAR_ID` ,`CHANGEMENT_LISTE_ID` ,`CHANGEMENT_FAR_CONFIG_ID`,`CHANGEMENT_FAR_UTILISATEUR_ID` ,`CHANGEMENT_FAR_VALEUR` ,`ENABLE`) VALUES (NULL , '".$ID."', '".$CHANGEMENT_FAR_CONFIG_ID."', '".$UTILISATEUR_ID."', '".$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]."', '0');";
            
            mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
            $TABLE_SQL_SQL='changement_far';       
            historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');           
          }
      
        	
        } while ($tab_rq_info_far = mysql_fetch_assoc($res_rq_info_far));
        $ligne= mysql_num_rows($res_rq_info_far);
        if($ligne > 0) {
          mysql_data_seek($res_rq_info_far, 0);
          $tab_rq_info_far = mysql_fetch_assoc($res_rq_info_far);
        }
        $sql="OPTIMIZE TABLE `changement_far` ";
        mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());

        $TABLE_SQL_SQL='changement_far';       
        historique_sql_new($sql,$TABLE_SQL_SQL,'OPTIMIZE');

        }

        mysql_free_result($res_rq_info_far);
        if($STOP==0){
        	$CHANGEMENT_FAR_INSCRIPTION=$tab_var['CHANGEMENT_FAR_INSCRIPTION'];
        	if($CHANGEMENT_FAR_INSCRIPTION=='Oui'){

	        	$rq_info_status="
			SELECT `CHANGEMENT_STATUS_ID` 
			FROM `changement_status` 
			WHERE `CHANGEMENT_STATUS` = 'Inscrit'
			AND `ENABLE`='0'";
			$res_rq_info_status = mysql_query($rq_info_status, $mysql_link) or die(mysql_error());
			$tab_rq_info_status = mysql_fetch_assoc($res_rq_info_status);
			$total_ligne_rq_info_status=mysql_num_rows($res_rq_info_status);
			if($total_ligne_rq_info_status==0){
				$sql="INSERT INTO `changement_status` (`CHANGEMENT_STATUS_ID` ,`CHANGEMENT_STATUS`, `ENABLE` )VALUES (NULL , 'Inscrit', '0');";
		        	mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
			        $TABLE_SQL_SQL='changement_status';       
			        historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');
			        $rq_info_status="
				SELECT `CHANGEMENT_STATUS_ID` 
				FROM `changement_status` 
				WHERE `CHANGEMENT_STATUS` = 'Inscrit'
				AND `ENABLE`='0'";
				$res_rq_info_status = mysql_query($rq_info_status, $mysql_link) or die(mysql_error());
				$tab_rq_info_status = mysql_fetch_assoc($res_rq_info_status);
				$total_ligne_rq_info_status=mysql_num_rows($res_rq_info_status);
			}
			$CHANGEMENT_STATUS_ID=$tab_rq_info_status['CHANGEMENT_STATUS_ID'];
			mysql_free_result($res_rq_info_status); 
			$sql="
			UPDATE `changement_liste` SET 
			`CHANGEMENT_STATUS_ID` = '".$CHANGEMENT_STATUS_ID."',
			`CHANGEMENT_LISTE_UTILISATEUR_ID`= '".$UTILISATEUR_ID."',
			`CHANGEMENT_LISTE_DATE_MODIFICATION`= '".$DATE_MODIFICATION."'
			WHERE `CHANGEMENT_LISTE_ID` ='".$ID."' LIMIT 1 ;";
	
			mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
			$TABLE_SQL_SQL='changement_liste';       
			historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');
			
			$TRACE_ETAT=$CHANGEMENT_STATUS_ID;
			
// mail d'inscription			
			$objet='-=Gestion des changements=- Inscription de la demande n '.$ID.'.';
			list ($Personne_abrege,$Personne_complet,$Personne_email,$Personne_email_FULL)=Recherche_SQL_nom_FULL($UTILISATEUR_ID);
			$Corps_mail = Constitue_corps_Message($CHANGEMENT_ID,$Personne_complet,$mysql_link);
			$Corps_mail_SVG = Constitue_corps_Message_SVG($CHANGEMENT_ID,$ENV,$Personne_email_FULL,$Personne_complet,$objet,$mysql_link);
//			mailMailInfo_php($CHANGEMENT_ID,$Personne_email_FULL, $Personne_abrege, $objet, $Corps_mail, $TRACE_ETAT, $ENV,$mysql_link);
			sauvegarde_MailInfo( $Corps_mail_SVG, $CHANGEMENT_ID, $TRACE_ETAT,$UTILISATEUR_ID,$ENV,$mysql_link);

		}else{
      $rq_info_CHANGEMENT_STATUS_ID="
      SELECT 
      `CHANGEMENT_STATUS_ID`
      FROM `changement_liste`
      WHERE 
      `CHANGEMENT_LISTE_ID` ='".$ID."' 
      AND `ENABLE` ='0'
      LIMIT 1";
      $res_rq_info_CHANGEMENT_STATUS_ID = mysql_query($rq_info_CHANGEMENT_STATUS_ID, $mysql_link) or die(mysql_error());
      $tab_rq_info_CHANGEMENT_STATUS_ID = mysql_fetch_assoc($res_rq_info_CHANGEMENT_STATUS_ID);
      $total_ligne_rq_info_CHANGEMENT_STATUS_ID=mysql_num_rows($res_rq_info_CHANGEMENT_STATUS_ID);
      $CHANGEMENT_STATUS_ID=$tab_rq_info_CHANGEMENT_STATUS_ID['CHANGEMENT_STATUS_ID'];
      mysql_free_result($res_rq_info_CHANGEMENT_STATUS_ID);
			$TRACE_ETAT=$CHANGEMENT_STATUS_ID;
		}
		$TRACE_CATEGORIE='Changement';
	        $TRACE_TABLE='changement_far';
	        $TRACE_REF_ID=$ID;
	        $TRACE_ACTION='Ajout';
	        moteur_trace($TRACE_CATEGORIE,$TRACE_TABLE,$TRACE_REF_ID,$TRACE_ACTION,$TRACE_ETAT);
        	
        echo '
        <script language="JavaScript">
        url=("./index.php?ITEM=changement_Calendrier");
        window.location=url;
        </script>
        ';
        }
	}

    $_GET['action']="Ajout";
  }
  

  
  # Cas Modifier
  if($tab_var['btn']=="Modifier"){
	$nb_modif=0;
  	$ID=$tab_var['ID'];
  	$CHANGEMENT_ID=$ID;
  	$rq_info_far="
      	SELECT * 
      	FROM `changement_far_config`
      	WHERE `ENABLE` =0
      	";
      $res_rq_info_far = mysql_query($rq_info_far, $mysql_link) or die(mysql_error());
      $tab_rq_info_far = mysql_fetch_assoc($res_rq_info_far);
      $total_ligne_rq_info_far=mysql_num_rows($res_rq_info_far);


      if($total_ligne_rq_info_far!=0){
//verification
        do {
        	$CHANGEMENT_FAR_CONFIG_ID=$tab_rq_info_far['CHANGEMENT_FAR_CONFIG_ID'];
        	$CHANGEMENT_FAR_CONFIG_NOM='CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID;
        	
          if (!isset($tab_var[$CHANGEMENT_FAR_CONFIG_NOM])){
            $STOP=1;
          }else{
            $CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$CHANGEMENT_FAR_CONFIG_NOM])));
          }
      
        	
        } while ($tab_rq_info_far = mysql_fetch_assoc($res_rq_info_far));
        $ligne= mysql_num_rows($res_rq_info_far);
        if($ligne > 0) {
          mysql_data_seek($res_rq_info_far, 0);
          $tab_rq_info_far = mysql_fetch_assoc($res_rq_info_far);
        }
        if($STOP==0){
// ajout en base        
        	do {
        	$CHANGEMENT_FAR_CONFIG_ID=$tab_rq_info_far['CHANGEMENT_FAR_CONFIG_ID'];
        	$CHANGEMENT_FAR_CONFIG_NOM='CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID;
        	
          if (!isset($tab_var[$CHANGEMENT_FAR_CONFIG_NOM])){
            $STOP=1;
          }else{
            $CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$CHANGEMENT_FAR_CONFIG_NOM])));
            
            $rq_info_far_id="
            SELECT `CHANGEMENT_FAR_ID` 
            FROM `changement_far`
            WHERE
            `CHANGEMENT_LISTE_ID`='".$ID."' AND
            `CHANGEMENT_FAR_CONFIG_ID`='".$CHANGEMENT_FAR_CONFIG_ID."' AND
            `ENABLE`='0'";
		$res_rq_info_far_id = mysql_query($rq_info_far_id, $mysql_link) or die(mysql_error());
		$tab_rq_info_far_id = mysql_fetch_assoc($res_rq_info_far_id);
		$total_ligne_rq_info_far_id=mysql_num_rows($res_rq_info_far_id);
		mysql_free_result($res_rq_info_far_id); 
            if($total_ligne_rq_info_far_id==0){
	            $sql="INSERT INTO `changement_far` (`CHANGEMENT_FAR_ID` ,`CHANGEMENT_LISTE_ID` ,`CHANGEMENT_FAR_CONFIG_ID`,`CHANGEMENT_FAR_UTILISATEUR_ID` ,`CHANGEMENT_FAR_VALEUR` ,`ENABLE`) VALUES (NULL , '".$ID."', '".$CHANGEMENT_FAR_CONFIG_ID."', '".$UTILISATEUR_ID."', '".$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]."', '0');";
	            mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
	            $TABLE_SQL_SQL='changement_far';       
	            historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');    
	            $nb_modif=$nb_modif+1;       
            }else{
		$rq_info_far_id="
		SELECT `CHANGEMENT_FAR_ID` 
		FROM `changement_far`
		WHERE
		`CHANGEMENT_LISTE_ID`='".$ID."' AND
		`CHANGEMENT_FAR_CONFIG_ID`='".$CHANGEMENT_FAR_CONFIG_ID."' AND
		`CHANGEMENT_FAR_VALEUR`='".$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]."' AND
		`ENABLE`='0'";
		$res_rq_info_far_id = mysql_query($rq_info_far_id, $mysql_link) or die(mysql_error());
		$tab_rq_info_far_id = mysql_fetch_assoc($res_rq_info_far_id);
		$total_ligne_rq_info_far_id=mysql_num_rows($res_rq_info_far_id);
		mysql_free_result($res_rq_info_far_id); 
		if($total_ligne_rq_info_far_id==0){
            	    $sql="
            	    UPDATE `changement_far` SET 
		    `CHANGEMENT_FAR_UTILISATEUR_ID`= '".$UTILISATEUR_ID."',
		    `CHANGEMENT_FAR_VALEUR`= '".$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]."'
		    WHERE 
		    `CHANGEMENT_LISTE_ID` ='".$ID."' AND 
		    `CHANGEMENT_FAR_CONFIG_ID`='".$CHANGEMENT_FAR_CONFIG_ID."'
		    LIMIT 1 ;";
	            mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
	            $TABLE_SQL_SQL='changement_far';       
	            historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');   
	            $nb_modif=$nb_modif+1;  
	        }
            }
          }
      
        	
        } while ($tab_rq_info_far = mysql_fetch_assoc($res_rq_info_far));
        $ligne= mysql_num_rows($res_rq_info_far);
        if($ligne > 0) {
          mysql_data_seek($res_rq_info_far, 0);
          $tab_rq_info_far = mysql_fetch_assoc($res_rq_info_far);
        }
        
        }
        if($nb_modif!=0){
		$sql="UPDATE `changement_liste` SET 
		`CHANGEMENT_LISTE_UTILISATEUR_ID`= '".$UTILISATEUR_ID."',
		`CHANGEMENT_LISTE_DATE_MODIFICATION`= '".$DATE_MODIFICATION."'
		WHERE `CHANGEMENT_LISTE_ID` ='".$ID."' LIMIT 1 ;";		
		mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
		$TABLE_SQL_SQL='changement_liste';       
		historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');
        }
        
        if($STOP==0){
        	if(isset($tab_var['CHANGEMENT_FAR_INSCRIPTION'])){
        		$CHANGEMENT_FAR_INSCRIPTION=$tab_var['CHANGEMENT_FAR_INSCRIPTION'];
        	}else{
        		$CHANGEMENT_FAR_INSCRIPTION='';
		}	
		  $rq_info_status="
			SELECT `changement_status`.`CHANGEMENT_STATUS` ,`changement_liste`.`CHANGEMENT_STATUS_ID`
			FROM `changement_liste` , `changement_status` 
			WHERE `changement_liste`.`CHANGEMENT_STATUS_ID` = `changement_status`.`CHANGEMENT_STATUS_ID` 
			AND `changement_liste`.`CHANGEMENT_LISTE_ID` = '".$ID."'
			AND `changement_liste`.`ENABLE` = '0'";
			$res_rq_info_status = mysql_query($rq_info_status, $mysql_link) or die(mysql_error());
			$tab_rq_info_status = mysql_fetch_assoc($res_rq_info_status);
			$CHANGEMENT_STATUS=$tab_rq_info_status['CHANGEMENT_STATUS'];
			$CHANGEMENT_STATUS_ID=$tab_rq_info_status['CHANGEMENT_STATUS_ID'];
			
			$total_ligne_rq_info_status=mysql_num_rows($res_rq_info_status);
			mysql_free_result($res_rq_info_status); 
        	if($CHANGEMENT_FAR_INSCRIPTION=='Oui'){
        		

			if($CHANGEMENT_STATUS=='Brouillon'){
				$nb_modif=$nb_modif+1; 
		        	$rq_info_status="
				SELECT `CHANGEMENT_STATUS_ID` 
				FROM `changement_status` 
				WHERE `CHANGEMENT_STATUS` = 'Inscrit'
				AND `ENABLE`='0'";
				$res_rq_info_status = mysql_query($rq_info_status, $mysql_link) or die(mysql_error());
				$tab_rq_info_status = mysql_fetch_assoc($res_rq_info_status);
				$total_ligne_rq_info_status=mysql_num_rows($res_rq_info_status);
				if($total_ligne_rq_info_status==0){
					$sql="INSERT INTO `changement_status` (`CHANGEMENT_STATUS_ID` ,`CHANGEMENT_STATUS`, `ENABLE` )VALUES (NULL , 'Inscrit', '0');";
			        	mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
				        $TABLE_SQL_SQL='changement_status';       
				        historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');
				        $rq_info_status="
					SELECT `CHANGEMENT_STATUS_ID` 
					FROM `changement_status` 
					WHERE `CHANGEMENT_STATUS` = 'Inscrit'
					AND `ENABLE`='0'";
					$res_rq_info_status = mysql_query($rq_info_status, $mysql_link) or die(mysql_error());
					$tab_rq_info_status = mysql_fetch_assoc($res_rq_info_status);
					$total_ligne_rq_info_status=mysql_num_rows($res_rq_info_status);
				}
				$CHANGEMENT_STATUS_ID=$tab_rq_info_status['CHANGEMENT_STATUS_ID'];
				mysql_free_result($res_rq_info_status); 
				$sql="UPDATE `changement_liste` SET 
				`CHANGEMENT_STATUS_ID` = '".$CHANGEMENT_STATUS_ID."',
				`CHANGEMENT_LISTE_UTILISATEUR_ID`= '".$UTILISATEUR_ID."',
				`CHANGEMENT_LISTE_DATE_MODIFICATION`= '".$DATE_MODIFICATION."'
				WHERE `CHANGEMENT_LISTE_ID` ='".$ID."' LIMIT 1 ;";		
				mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
				$TABLE_SQL_SQL='changement_liste';       
				historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');
				
				$TRACE_ETAT=$CHANGEMENT_STATUS_ID;
				
// mail d'inscription		
				$objet='-=Gestion des changements=- Inscription de la demande n '.$ID.'.';
				list ($Personne_abrege,$Personne_complet,$Personne_email,$Personne_email_FULL)=Recherche_SQL_nom_FULL($UTILISATEUR_ID);
				$Corps_mail = Constitue_corps_Message($CHANGEMENT_ID,$Personne_complet,$mysql_link);
				$Corps_mail_SVG = Constitue_corps_Message_SVG($CHANGEMENT_ID,$ENV,$Personne_email_FULL,$Personne_complet,$objet,$mysql_link);
//				mailMailInfo_php($CHANGEMENT_ID,$Personne_email_FULL, $Personne_abrege, $objet, $Corps_mail, $TRACE_ETAT, $ENV,$mysql_link);
				sauvegarde_MailInfo( $Corps_mail_SVG, $CHANGEMENT_ID, $TRACE_ETAT,$UTILISATEUR_ID,$ENV,$mysql_link);
			}

		}else{
      $rq_info_CHANGEMENT_STATUS_ID="
      SELECT 
      `CHANGEMENT_STATUS_ID`
      FROM `changement_liste`
      WHERE 
      `CHANGEMENT_LISTE_ID` ='".$ID."' 
      AND `ENABLE` ='0'
      LIMIT 1";
      $res_rq_info_CHANGEMENT_STATUS_ID = mysql_query($rq_info_CHANGEMENT_STATUS_ID, $mysql_link) or die(mysql_error());
      $tab_rq_info_CHANGEMENT_STATUS_ID = mysql_fetch_assoc($res_rq_info_CHANGEMENT_STATUS_ID);
      $total_ligne_rq_info_CHANGEMENT_STATUS_ID=mysql_num_rows($res_rq_info_CHANGEMENT_STATUS_ID);
      $CHANGEMENT_STATUS_ID=$tab_rq_info_CHANGEMENT_STATUS_ID['CHANGEMENT_STATUS_ID'];
      mysql_free_result($res_rq_info_CHANGEMENT_STATUS_ID);
      $TRACE_ETAT=$CHANGEMENT_STATUS_ID;
		}
		
		if($nb_modif!=0){
		        $sql="OPTIMIZE TABLE `changement_far` ";
		        mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
		
		        $TABLE_SQL_SQL='changement_far';       
		        historique_sql_new($sql,$TABLE_SQL_SQL,'OPTIMIZE');
		        
		        $TRACE_CATEGORIE='Changement';
		        $TRACE_TABLE='changement_far';
		        $TRACE_REF_ID=$ID;
		        $TRACE_ACTION='Modif';
		        moteur_trace($TRACE_CATEGORIE,$TRACE_TABLE,$TRACE_REF_ID,$TRACE_ACTION,$TRACE_ETAT);
		}
		
        	
        echo '
        <script language="JavaScript">
        url=("./index.php?ITEM=changement_Calendrier");
        window.location=url;
        </script>
        ';
        }
	}
	mysql_free_result($res_rq_info_far);
	$_GET['action']="Modif";
  	
  }
}
//# Cas action Info
if($action=="Info"){
	$rq_info_far="
	SELECT * 
	FROM `changement_far`
	WHERE `CHANGEMENT_LISTE_ID`='".$ID."'
	AND`ENABLE` = '0'
	";
	$res_rq_info_far = mysql_query($rq_info_far, $mysql_link) or die(mysql_error());
	$tab_rq_info_far = mysql_fetch_assoc($res_rq_info_far);
	$total_ligne_rq_info_far=mysql_num_rows($res_rq_info_far);
	
	if($total_ligne_rq_info_far!=0){
		do {
			$CHANGEMENT_FAR_CONFIG_ID=$tab_rq_info_far['CHANGEMENT_FAR_CONFIG_ID'];
			$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]=$tab_rq_info_far['CHANGEMENT_FAR_VALEUR'];         
		} while ($tab_rq_info_far = mysql_fetch_assoc($res_rq_info_far));
		$ligne= mysql_num_rows($res_rq_info_far);
		if($ligne > 0) {
		  mysql_data_seek($res_rq_info_far, 0);
		  $tab_rq_info_far = mysql_fetch_assoc($res_rq_info_far);
		}
	}
	mysql_free_result($res_rq_info_far);
        $rq_info_far="
	SELECT `changement_status`.`CHANGEMENT_STATUS` 
	FROM `changement_liste` , `changement_status` 
	WHERE `changement_liste`.`CHANGEMENT_LISTE_ID` = '".$ID."'
	AND `changement_liste`.`CHANGEMENT_STATUS_ID` = `changement_status`.`CHANGEMENT_STATUS_ID` 
	AND `changement_status`.`CHANGEMENT_STATUS` = 'Brouillon'
	AND `changement_liste`.`ENABLE` = '0'
	";
	$res_rq_info_far = mysql_query($rq_info_far, $mysql_link) or die(mysql_error());
	$tab_rq_info_far = mysql_fetch_assoc($res_rq_info_far);
	$total_ligne_rq_info_far=mysql_num_rows($res_rq_info_far);

	if($total_ligne_rq_info_far==0){
		$CHANGEMENT_FAR_INSCRIPTION="Oui";
	}else{
		$CHANGEMENT_FAR_INSCRIPTION="Non";
	}
	mysql_free_result($res_rq_info_far);
}
//# Cas action modifer
if($action=="Modif"){
	$rq_info_far="
	SELECT * 
	FROM `changement_far`
	WHERE `CHANGEMENT_LISTE_ID`='".$ID."'
	AND`ENABLE` = '0'
	";
	$res_rq_info_far = mysql_query($rq_info_far, $mysql_link) or die(mysql_error());
	$tab_rq_info_far = mysql_fetch_assoc($res_rq_info_far);
	$total_ligne_rq_info_far=mysql_num_rows($res_rq_info_far);
	
	if($total_ligne_rq_info_far!=0){
		do {
			$CHANGEMENT_FAR_CONFIG_ID=$tab_rq_info_far['CHANGEMENT_FAR_CONFIG_ID'];
			$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]=$tab_rq_info_far['CHANGEMENT_FAR_VALEUR'];         
		} while ($tab_rq_info_far = mysql_fetch_assoc($res_rq_info_far));
		$ligne= mysql_num_rows($res_rq_info_far);
		if($ligne > 0) {
		  mysql_data_seek($res_rq_info_far, 0);
		  $tab_rq_info_far = mysql_fetch_assoc($res_rq_info_far);
		}
	}
	mysql_free_result($res_rq_info_far);
        $rq_info_far="
	SELECT `changement_status`.`CHANGEMENT_STATUS` 
	FROM `changement_liste` , `changement_status` 
	WHERE `changement_liste`.`CHANGEMENT_LISTE_ID` = '".$ID."'
	AND `changement_liste`.`CHANGEMENT_STATUS_ID` = `changement_status`.`CHANGEMENT_STATUS_ID` 
	AND `changement_status`.`CHANGEMENT_STATUS` = 'Brouillon'
	AND `changement_liste`.`ENABLE` = '0'
	";
	$res_rq_info_far = mysql_query($rq_info_far, $mysql_link) or die(mysql_error());
	$tab_rq_info_far = mysql_fetch_assoc($res_rq_info_far);
	$total_ligne_rq_info_far=mysql_num_rows($res_rq_info_far);

	if($total_ligne_rq_info_far==0){
		$CHANGEMENT_FAR_INSCRIPTION="Oui";
	}else{
		$CHANGEMENT_FAR_INSCRIPTION="Non";
	}
	mysql_free_result($res_rq_info_far);
}
//# Cas action modifer
if($action=="Ajout"){
	$rq_info_far="
	SELECT `changement_status`.`CHANGEMENT_STATUS` 
	FROM `changement_liste` , `changement_status` 
	WHERE `changement_liste`.`CHANGEMENT_LISTE_ID` = '".$ID."'
	AND `changement_liste`.`CHANGEMENT_STATUS_ID` = `changement_status`.`CHANGEMENT_STATUS_ID` 
	AND `changement_status`.`CHANGEMENT_STATUS` = 'Brouillon'
	AND `changement_liste`.`ENABLE` = '0'
	";
	$res_rq_info_far = mysql_query($rq_info_far, $mysql_link) or die(mysql_error());
	$tab_rq_info_far = mysql_fetch_assoc($res_rq_info_far);
	$total_ligne_rq_info_far=mysql_num_rows($res_rq_info_far);

	if($total_ligne_rq_info_far==0){
		$CHANGEMENT_FAR_INSCRIPTION="Oui";
	}else{
		$CHANGEMENT_FAR_INSCRIPTION="Non";
	}
	mysql_free_result($res_rq_info_far);
}
//# Cas action Info
if($action=="Info"){
	$rq_info_far="
	SELECT `changement_status`.`CHANGEMENT_STATUS` 
	FROM `changement_liste` , `changement_status` 
	WHERE `changement_liste`.`CHANGEMENT_LISTE_ID` = '".$ID."'
	AND `changement_liste`.`CHANGEMENT_STATUS_ID` = `changement_status`.`CHANGEMENT_STATUS_ID` 
	AND `changement_liste`.`ENABLE` = '0'
	";
	$res_rq_info_far = mysql_query($rq_info_far, $mysql_link) or die(mysql_error());
	$tab_rq_info_far = mysql_fetch_assoc($res_rq_info_far);
	$total_ligne_rq_info_far=mysql_num_rows($res_rq_info_far);
	if($total_ligne_rq_info_far==0){
		$CHANGEMENT_STATUS='Brouillon';
		$CHANGEMENT_FAR_INSCRIPTION="Oui";
	}else{
		$CHANGEMENT_STATUS=$tab_rq_info_far['CHANGEMENT_STATUS'];;
		if($CHANGEMENT_STATUS=='Brouillon'){
			$CHANGEMENT_FAR_INSCRIPTION="Non";
		}else{
			$CHANGEMENT_FAR_INSCRIPTION="Oui";
		}
		
	}
	mysql_free_result($res_rq_info_far);
}
if($action=="Modif"){
	$rq_info_far="
	SELECT `changement_status`.`CHANGEMENT_STATUS` 
	FROM `changement_liste` , `changement_status` 
	WHERE `changement_liste`.`CHANGEMENT_LISTE_ID` = '".$ID."'
	AND `changement_liste`.`CHANGEMENT_STATUS_ID` = `changement_status`.`CHANGEMENT_STATUS_ID` 
	AND `changement_liste`.`ENABLE` = '0'
	";
	$res_rq_info_far = mysql_query($rq_info_far, $mysql_link) or die(mysql_error());
	$tab_rq_info_far = mysql_fetch_assoc($res_rq_info_far);
	$total_ligne_rq_info_far=mysql_num_rows($res_rq_info_far);
	if($total_ligne_rq_info_far==0){
		$CHANGEMENT_STATUS='Brouillon';
		$CHANGEMENT_FAR_INSCRIPTION="Oui";
	}else{
		$CHANGEMENT_STATUS=$tab_rq_info_far['CHANGEMENT_STATUS'];;
		if($CHANGEMENT_STATUS=='Brouillon'){
			$CHANGEMENT_FAR_INSCRIPTION="Non";
		}else{
			$CHANGEMENT_FAR_INSCRIPTION="Oui";
		}
		
	}
	mysql_free_result($res_rq_info_far);
}
echo '
<!--D&eacute;but page HTML -->  
<div align="center">
<form method="post" name="frm_inter" id="frm_inter" action="./index.php?ITEM='.$_GET['ITEM'].'">
  <table class="table_inc">';
  $rq_info_id="
	SELECT 
	`moteur_trace`.`MOTEUR_TRACE_UTILISATEUR_ID`, 
	`moteur_trace`.`MOTEUR_TRACE_DATE`, 
	`moteur_trace`.`MOTEUR_TRACE_CATEGORIE`, 
	`moteur_trace`.`MOTEUR_TRACE_TABLE`, 
	`moteur_trace`.`MOTEUR_TRACE_REF_ID`, 
	`moteur_trace`.`MOTEUR_TRACE_ACTION`, 
	`changement_status`.`CHANGEMENT_STATUS`,
	`moteur_utilisateur`.`LOGIN`, 
	`moteur_utilisateur`.`NOM`, 
	`moteur_utilisateur`.`PRENOM` 
	FROM `moteur_trace`,`moteur_utilisateur`,`changement_status`
	WHERE 
	`moteur_trace`.`MOTEUR_TRACE_CATEGORIE`='Changement'
	AND `moteur_trace`.`MOTEUR_TRACE_UTILISATEUR_ID`=`moteur_utilisateur`.`UTILISATEUR_ID` 
	AND `moteur_trace`.`MOTEUR_TRACE_ETAT`=`changement_status`.`CHANGEMENT_STATUS_ID` 
	AND `moteur_trace`.`MOTEUR_TRACE_REF_ID`='".$ID."'
	ORDER BY `moteur_trace`.`MOTEUR_TRACE_DATE` DESC";
	$res_rq_info_id = mysql_query($rq_info_id, $mysql_link) or die(mysql_error());
	$tab_rq_info_id = mysql_fetch_assoc($res_rq_info_id);
	$total_ligne_rq_info_id=mysql_num_rows($res_rq_info_id);
	$AFF_SPAN='';
	if($total_ligne_rq_info_id!=0){
		do {
		
		$MOTEUR_TRACE_DATE=$tab_rq_info_id['MOTEUR_TRACE_DATE'];
		$MOTEUR_TRACE_CATEGORIE=$tab_rq_info_id['MOTEUR_TRACE_CATEGORIE'];
		$MOTEUR_TRACE_TABLE=str_replace('changement_','',$tab_rq_info_id['MOTEUR_TRACE_TABLE']);
		$MOTEUR_TRACE_REF_ID=$tab_rq_info_id['MOTEUR_TRACE_REF_ID'];
		$MOTEUR_TRACE_ACTION=$tab_rq_info_id['MOTEUR_TRACE_ACTION'];
		$MOTEUR_TRACE_CHANGEMENT_STATUS=$tab_rq_info_id['CHANGEMENT_STATUS'];
		$MOTEUR_TRACE_NOM=$tab_rq_info_id['NOM'];
		$MOTEUR_TRACE_PRENOM=$tab_rq_info_id['PRENOM'];
		$AFF_SPAN.=$MOTEUR_TRACE_DATE.' - '.$MOTEUR_TRACE_PRENOM.' '.$MOTEUR_TRACE_NOM.' - '.$MOTEUR_TRACE_ACTION.' - '.$MOTEUR_TRACE_TABLE.' - '.$MOTEUR_TRACE_CHANGEMENT_STATUS.'</BR>';
		 } while ($tab_rq_info_id = mysql_fetch_assoc($res_rq_info_id));
	        $ligne= mysql_num_rows($res_rq_info_id);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_id, 0);
	          $tab_rq_info_id = mysql_fetch_assoc($res_rq_info_id);
	        }
	}
	mysql_free_result($res_rq_info_id);
	echo '<tr align="center" class="titre">
        <td colspan="2"><h2>&nbsp;[&nbsp;';
    if($action=="Ajout"){
      echo '
      Cr&eacute;ation de la FAR du changement n&deg; <a href="#" class="infobulledroite">'.$ID.'<span>'.$AFF_SPAN.'</span></a>';
    }
    if($action=="Modif"){
      if($CHANGEMENT_STATUS=='Brouillon'){
        echo 'Modification de la FAR du changement n&deg; <a href="#" class="infobulledroite">'.$ID.'<span>'.$AFF_SPAN.'</span></a>';
      }else{
        if($ROLE==0){
          echo 'Modification de la FAR du changement n&deg; <a href="#" class="infobulledroite">'.$ID.'<span>'.$AFF_SPAN.'</span></a>';
        }else{
          echo 'Information de la FAR du changement n&deg; <a href="#" class="infobulledroite">'.$ID.'<span>'.$AFF_SPAN.'</span></a>';
        }
      }
    }
    if($action=="Info"){
    	echo 'Information de la FAR du changement n&deg; <a href="#" class="infobulledroite">'.$ID.'<span>'.$AFF_SPAN.'</span></a>';
    }
    
    echo '&nbsp;]&nbsp;</h2></td>		
      </tr>';

    $rq_info_far_lib="
      SELECT DISTINCT (`CHANGEMENT_FAR_CONFIG_LIB`) AS `CHANGEMENT_FAR_CONFIG_LIB`
      FROM `changement_far_config` 
      WHERE 
      `ENABLE` ='0'
      ORDER BY `CHANGEMENT_FAR_CONFIG_LIB` 
      ";
      if($action=="Modif"){
        $rq_info_far_lib="
        SELECT DISTINCT (`changement_far_config`.`CHANGEMENT_FAR_CONFIG_LIB`) AS `CHANGEMENT_FAR_CONFIG_LIB`
        FROM `changement_far_config` , `changement_far`
        WHERE `changement_far_config`.`CHANGEMENT_FAR_CONFIG_ID` = `changement_far`.`CHANGEMENT_FAR_CONFIG_ID`
        AND `changement_far`.`CHANGEMENT_LISTE_ID` = '".$ID."'
        AND `changement_far`.`ENABLE` = '0'
        ORDER BY `changement_far_config`.`CHANGEMENT_FAR_CONFIG_LIB`";
      }
      $res_rq_info_far_lib = mysql_query($rq_info_far_lib, $mysql_link) or die(mysql_error());
      $tab_rq_info_far_lib = mysql_fetch_assoc($res_rq_info_far_lib);
      $total_ligne_rq_info_far_lib=mysql_num_rows($res_rq_info_far_lib);
      if($total_ligne_rq_info_far_lib!=0){
        do {
        	$CHANGEMENT_FAR_CONFIG_LIB=$tab_rq_info_far_lib['CHANGEMENT_FAR_CONFIG_LIB'];
      echo '
      <tr align="center" class="titre">
        <td colspan="2">&nbsp;'.stripslashes(substr($CHANGEMENT_FAR_CONFIG_LIB,strpos($CHANGEMENT_FAR_CONFIG_LIB,"-")+1)).'&nbsp;</td>		
      </tr>';
              	$rq_info_far="
              	SELECT * 
              	FROM `changement_far_config`
              	WHERE `CHANGEMENT_FAR_CONFIG_LIB`='".$CHANGEMENT_FAR_CONFIG_LIB."' AND 
              	`ENABLE` ='0'
              	";
              	if($action=="Modif"){
                  $rq_info_far="
                  SELECT 
                  `changement_far_config`.`CHANGEMENT_FAR_CONFIG_ID`, 
                  `changement_far_config`.`CHANGEMENT_FAR_CONFIG_CRITERE`, 
                  `changement_far_config`.`CHANGEMENT_FAR_CONFIG_TYPE`
                  FROM `changement_far_config` , `changement_far`
                  WHERE `changement_far_config`.`CHANGEMENT_FAR_CONFIG_ID` = `changement_far`.`CHANGEMENT_FAR_CONFIG_ID`
                  AND `changement_far`.`CHANGEMENT_LISTE_ID` = '".$ID."'
                  AND `changement_far_config`.`CHANGEMENT_FAR_CONFIG_LIB`='".$CHANGEMENT_FAR_CONFIG_LIB."' 
                  AND `changement_far`.`ENABLE` = '0'
                  ORDER BY `changement_far_config`.`CHANGEMENT_FAR_CONFIG_LIB`";
              	}
	      $res_rq_info_far = mysql_query($rq_info_far, $mysql_link) or die(mysql_error());
	      $tab_rq_info_far = mysql_fetch_assoc($res_rq_info_far);
	      $total_ligne_rq_info_far=mysql_num_rows($res_rq_info_far);
	      if($total_ligne_rq_info_far!=0){
	        do {
	        	$CHANGEMENT_FAR_CONFIG_ID=$tab_rq_info_far['CHANGEMENT_FAR_CONFIG_ID'];
	        	$CHANGEMENT_FAR_CONFIG_CRITERE=$tab_rq_info_far['CHANGEMENT_FAR_CONFIG_CRITERE'];
	        	$CHANGEMENT_FAR_CONFIG_TYPE=$tab_rq_info_far['CHANGEMENT_FAR_CONFIG_TYPE'];
	        	if(!isset($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID])){$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]='';}
	       $j++;
		    if ($j%2) { $class = "pair";}else{$class = "impair";} 
		  echo '
		    <tr class="'.$class.'">
		     <td align="left">&nbsp;'.stripslashes($CHANGEMENT_FAR_CONFIG_CRITERE).'&nbsp;</td>
		     <td align="left">&nbsp;';
		      switch ($CHANGEMENT_FAR_CONFIG_TYPE)
            {
            case "oui-non": 
              echo '
              <INPUT type=radio name="CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID.'" value="Oui"';
              if($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]=='Oui'){echo 'CHECKED';} 
              echo '>
              &nbsp;Oui&nbsp;/&nbsp;Non&nbsp;
              <INPUT type=radio name="CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID.'" value="Non"'; 
              if($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]=='Non'){echo 'CHECKED';} 
              echo '>';
              
            break;
            case "risque": 
            $AFF_SPAN_O_N='';
            $AFF_SPAN_O_N.='1 Risque Faible < > 4 Risque Fort';
              echo '
              &nbsp;1&nbsp;<INPUT type=radio name="CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID.'" value="1"';
              if($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]=='1'){echo 'CHECKED';} 
              echo '>
              &nbsp;2&nbsp;<INPUT type=radio name="CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID.'" value="2"'; 
              if($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]=='2'){echo 'CHECKED';} 
              echo '>
              &nbsp;3&nbsp;<INPUT type=radio name="CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID.'" value="3"'; 
              if($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]=='3'){echo 'CHECKED';} 
              echo '>
              &nbsp;4&nbsp;<INPUT type=radio name="CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID.'" value="4"'; 
              if($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]=='4'){echo 'CHECKED';} 
              echo '>';
              echo '&nbsp;<a href="#" class="infobullegauche"><font color=#000000>&nbsp;- Aide&nbsp;</font><span>'.$AFF_SPAN_O_N.'</span></a>';
            break;
            case "text": 
              echo '<textarea id="CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID.'" name="CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID.'" cols="50" rows="2">'.stripslashes($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]).'</textarea>';
            break;
            }
		     echo '
		     &nbsp;</td>
		   </tr>';
	        	
	        } while ($tab_rq_info_far = mysql_fetch_assoc($res_rq_info_far));
	        $ligne= mysql_num_rows($res_rq_info_far);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_far, 0);
	          $tab_rq_info_far = mysql_fetch_assoc($res_rq_info_far);
	        }
	        mysql_free_result($res_rq_info_far);
	}

        	
        } while ($tab_rq_info_far_lib = mysql_fetch_assoc($res_rq_info_far_lib));
        $ligne= mysql_num_rows($res_rq_info_far_lib);
        if($ligne > 0) {
          mysql_data_seek($res_rq_info_far_lib, 0);
          $tab_rq_info_far_lib = mysql_fetch_assoc($res_rq_info_far_lib);
        }
        mysql_free_result($res_rq_info_far_lib);
      }  
      if($CHANGEMENT_FAR_INSCRIPTION=='Non'){
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
	  echo '
	    <tr class="'.$class.'">
	     <td align="left">&nbsp;<font color=#993333><b>Incription</b></font>&nbsp;</td>
	     <td align="left">&nbsp;';
	     echo '
              <INPUT type=radio name="CHANGEMENT_FAR_INSCRIPTION" value="Oui"';
              if($CHANGEMENT_FAR_INSCRIPTION=='Oui'){echo 'CHECKED';} 
              echo '>
              &nbsp;Oui&nbsp;/&nbsp;Non&nbsp;
              <INPUT type=radio name="CHANGEMENT_FAR_INSCRIPTION" value="Non"'; 
              if($CHANGEMENT_FAR_INSCRIPTION=='Non'){echo 'CHECKED';} 
              echo '>';
              echo '</td></tr>';
      }
      $j++;

if($STOP==1){
   	$j++;
	   if ($j%2) { $class = "pair";}else{$class = "impair";} 
	   echo '
	   <tr class="'.$class.'">
	    <td align="center" colspan="4"><font color=#993333><b>Merci de saisir l\'ensemble des champs.</b></font>
	    </td>
	   </tr>';
   }
   echo '
   <tr class="titre">
	<td colspan="2" align="center">
	<h2>';
	if($action=='Ajout'){
	echo '
	<input name="btn" type="submit" id="btn" value="Ajouter">
	<input type="hidden" name="ID" value="'.$ID.'">
	<input type="hidden" name="action" value="Ajout">
	';
	}
	if($action=='Modif'){
		echo '
		<input type="hidden" name="ID" value="'.$ID.'">
		<input type="hidden" name="action" value="'.$action.'">';
		if($CHANGEMENT_STATUS=='Brouillon'){
			echo '
			<input name="btn" type="submit" id="btn" value="Modifier">
			';
		}else{
			if($ROLE==0){
				echo '
				<input name="btn" type="submit" id="btn" value="Modifier">
				';
			}
		}
		
		 
	}
	if($action=='Info'){
		echo '
		<input type="hidden" name="ID" value="'.$ID.'">
		<input type="hidden" name="action" value="'.$action.'">';
	}
	echo '
  </h2>
	</td>
	</tr>
	<tr class="titre">
	<td colspan="4" align="center">
		<h2>&nbsp;[&nbsp;<a href="./index.php?ITEM=changement_Modif_Changement&action=Modif&ID='.$ID.'">Retour</a>&nbsp;]&nbsp;</h2>
	</td>
	</tr>';


echo '
</table>
</form>
</div>
';

mysql_close($mysql_link); 
?>