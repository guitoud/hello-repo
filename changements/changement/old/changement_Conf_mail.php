<?PHP
$INCLUDE_DIR = "./lib/phpmailer/";
require_once($INCLUDE_DIR . "class.phpmailer.php");
require_once($INCLUDE_DIR . 'language/phpmailer.lang-en.php') ; 

////mail///
function sauvegarde_MailInfo( $corps, $CHANGEMENT_ID, $type,$UTILISATEUR_ID,$ENV,$mysql_link)
{
	$ID=$CHANGEMENT_ID;
	$var_date_svg=date("YmdHis");
	$DATE_MODIFICATION=date("d/m/Y H:i:s");
	$type=str_replace('&eacute;', 'e', $type);
	$fichier=$var_date_svg.'-'.$CHANGEMENT_ID.'-'.$type.'.html';
	$corps = str_replace('cid:','../Images/', $corps);
	$MAIL_A='';
	$NB_MAIL=0;
	$rq_info_mail="
	SELECT DISTINCT(`CHANGEMENT_MAIL_LIB`) AS `CHANGEMENT_MAIL_LIB`
	FROM `changement_mail`
	WHERE `ENABLE`='0' 
	AND `CHANGEMENT_STATUS_ID` IN (
	SELECT `CHANGEMENT_STATUS_ID` 
	FROM `changement_liste` 
	WHERE `ENABLE`='0' AND `CHANGEMENT_LISTE_ID`='".$ID."' 
	)
	AND `CHANGEMENT_DEMANDE_ID` IN (
	SELECT `CHANGEMENT_DEMANDE_ID` 
	FROM `changement_liste` 
	WHERE `ENABLE`='0' AND `CHANGEMENT_LISTE_ID`='".$ID."'
	);";
	$res_rq_info_mail = mysql_query($rq_info_mail, $mysql_link) or die(mysql_error());
	$tab_rq_info_mail = mysql_fetch_assoc($res_rq_info_mail);
	$total_ligne_rq_info_mail=mysql_num_rows($res_rq_info_mail);
	  if ( $ENV == "x" )
	  {	 	
		if($total_ligne_rq_info_mail!=0){
			do {
				if($NB_MAIL==0){
	if($tab_rq_info_mail['CHANGEMENT_MAIL_LIB']!='demandeur@caissedesdepots.fr'){
		$MAIL_A .= $tab_rq_info_mail['CHANGEMENT_MAIL_LIB']; 
		//$a_mail->AddAddress($tab_rq_info_mail['CHANGEMENT_MAIL_LIB']);
		$NB_MAIL=$NB_MAIL+1;
	}else{
		$rq_info_mail_demandeur="
		SELECT DISTINCT(`EMAIL_FULL`) AS `EMAIL_FULL` 
		FROM `moteur_utilisateur` 
		WHERE `UTILISATEUR_ID` IN (
		SELECT DISTINCT(`MOTEUR_TRACE_UTILISATEUR_ID`) 
		FROM `moteur_trace` 
		WHERE `MOTEUR_TRACE_CATEGORIE`='Changement' 
		AND `MOTEUR_TRACE_ETAT` IN(
		SELECT `CHANGEMENT_STATUS_ID` 
		FROM `changement_status` 
		WHERE `CHANGEMENT_STATUS` IN('Brouillon','Inscrit') 
		AND `ENABLE`=0
		) 
		AND `MOTEUR_TRACE_REF_ID`='".$ID."'
		) 
		AND UPPER(`EMAIL_FULL`) NOT IN(
		SELECT DISTINCT(UPPER(`CHANGEMENT_MAIL_LIB`)) AS `CHANGEMENT_MAIL_LIB`
		FROM `changement_mail`
		WHERE `ENABLE`='0' 
		AND `CHANGEMENT_STATUS_ID` IN (
		SELECT `CHANGEMENT_STATUS_ID` 
		FROM `changement_liste` 
		WHERE `ENABLE`='0' AND `CHANGEMENT_LISTE_ID`='".$ID."' 
		)
		AND `CHANGEMENT_DEMANDE_ID` IN (
		SELECT `CHANGEMENT_DEMANDE_ID` 
		FROM `changement_liste` 
		WHERE `ENABLE`='0' AND `CHANGEMENT_LISTE_ID`='".$ID."'
		)
		)";
		$res_rq_info_mail_demandeur = mysql_query($rq_info_mail_demandeur, $mysql_link) or die(mysql_error());
		$tab_rq_info_mail_demandeur = mysql_fetch_assoc($res_rq_info_mail_demandeur);
		$total_ligne_rq_info_mail_demandeur=mysql_num_rows($res_rq_info_mail_demandeur);
		if($total_ligne_rq_info_mail_demandeur!=0){
			do {
				if($NB_MAIL==0){
					$MAIL_A .= $tab_rq_info_mail_demandeur['EMAIL_FULL']; 
					//$a_mail->AddAddress($tab_rq_info_mail_demandeur['EMAIL_FULL']);
					$NB_MAIL=$NB_MAIL+1;
				}else{
					$MAIL_A .= ';'.$tab_rq_info_mail_demandeur['EMAIL_FULL']; 
					//$a_mail->AddAddress($tab_rq_info_mail_demandeur['EMAIL_FULL']);
					$NB_MAIL=$NB_MAIL+1;
				}
			
			} while ($tab_rq_info_mail_demandeur = mysql_fetch_assoc($res_rq_info_mail_demandeur));
			$ligne2= mysql_num_rows($res_rq_info_mail_demandeur);
			if($ligne2 > 0) {
				mysql_data_seek($res_rq_info_mail_demandeur, 0);
				$tab_rq_info_mail_demandeur = mysql_fetch_assoc($res_rq_info_mail_demandeur);
			}
		}
		mysql_free_result($res_rq_info_mail_demandeur);
	}
}else{
	if($tab_rq_info_mail['CHANGEMENT_MAIL_LIB']!='demandeur@caissedesdepots.fr'){
		$MAIL_A .= ';'.$tab_rq_info_mail['CHANGEMENT_MAIL_LIB']; 
		//$a_mail->AddAddress($tab_rq_info_mail['CHANGEMENT_MAIL_LIB']);
		$NB_MAIL=$NB_MAIL+1;
	}else{
		$rq_info_mail_demandeur="
		SELECT DISTINCT(`EMAIL_FULL`) AS `EMAIL_FULL` 
		FROM `moteur_utilisateur` 
		WHERE `UTILISATEUR_ID` IN (
		SELECT DISTINCT(`MOTEUR_TRACE_UTILISATEUR_ID`) 
		FROM `moteur_trace` 
		WHERE `MOTEUR_TRACE_CATEGORIE`='Changement' 
		AND `MOTEUR_TRACE_ETAT` IN(
		SELECT `CHANGEMENT_STATUS_ID` 
		FROM `changement_status` 
		WHERE `CHANGEMENT_STATUS` IN('Brouillon','Inscrit') 
		AND `ENABLE`=0
		) 
		AND `MOTEUR_TRACE_REF_ID`='".$ID."'
		) 
		AND UPPER(`EMAIL_FULL`) NOT IN(
		SELECT DISTINCT(UPPER(`CHANGEMENT_MAIL_LIB`)) AS `CHANGEMENT_MAIL_LIB`
		FROM `changement_mail`
		WHERE `ENABLE`='0' 
		AND `CHANGEMENT_STATUS_ID` IN (
		SELECT `CHANGEMENT_STATUS_ID` 
		FROM `changement_liste` 
		WHERE `ENABLE`='0' AND `CHANGEMENT_LISTE_ID`='".$ID."' 
		)
		AND `CHANGEMENT_DEMANDE_ID` IN (
		SELECT `CHANGEMENT_DEMANDE_ID` 
		FROM `changement_liste` 
		WHERE `ENABLE`='0' AND `CHANGEMENT_LISTE_ID`='".$ID."'
		)
		)";
		$res_rq_info_mail_demandeur = mysql_query($rq_info_mail_demandeur, $mysql_link) or die(mysql_error());
		$tab_rq_info_mail_demandeur = mysql_fetch_assoc($res_rq_info_mail_demandeur);
		$total_ligne_rq_info_mail_demandeur=mysql_num_rows($res_rq_info_mail_demandeur);
		if($total_ligne_rq_info_mail_demandeur!=0){
			do {
				if($NB_MAIL==0){
					$MAIL_A .= $tab_rq_info_mail_demandeur['EMAIL_FULL']; 
					//$a_mail->AddAddress($tab_rq_info_mail_demandeur['EMAIL_FULL']);
					$NB_MAIL=$NB_MAIL+1;
				}else{
					$MAIL_A .= ';'.$tab_rq_info_mail_demandeur['EMAIL_FULL']; 
					//$a_mail->AddAddress($tab_rq_info_mail_demandeur['EMAIL_FULL']);
					$NB_MAIL=$NB_MAIL+1;
				}
			
			} while ($tab_rq_info_mail_demandeur = mysql_fetch_assoc($res_rq_info_mail_demandeur));
			$ligne2= mysql_num_rows($res_rq_info_mail_demandeur);
			if($ligne2 > 0) {
				mysql_data_seek($res_rq_info_mail_demandeur, 0);
				$tab_rq_info_mail_demandeur = mysql_fetch_assoc($res_rq_info_mail_demandeur);
			}
		}
		mysql_free_result($res_rq_info_mail_demandeur);
	}
}
			
			 } while ($tab_rq_info_mail = mysql_fetch_assoc($res_rq_info_mail));
		        $ligne= mysql_num_rows($res_rq_info_mail);
		        if($ligne > 0) {
		          mysql_data_seek($res_rq_info_mail, 0);
		          $tab_rq_info_mail = mysql_fetch_assoc($res_rq_info_mail);
		        }
		}	    
	  }else{
	  	//on envoie toujours en dev a $Personne_email_FULL
	  	//$MAIL_A .= $Personne_email_FULL; 
		if($total_ligne_rq_info_mail!=0){
			do {
if($NB_MAIL==0){
	if($tab_rq_info_mail['CHANGEMENT_MAIL_LIB']!='demandeur@caissedesdepots.fr'){
		$MAIL_A .= $tab_rq_info_mail['CHANGEMENT_MAIL_LIB']; 
		//$a_mail->AddAddress($tab_rq_info_mail['CHANGEMENT_MAIL_LIB']);
		$NB_MAIL=$NB_MAIL+1;
	}else{
		$rq_info_mail_demandeur="
		SELECT DISTINCT(`EMAIL_FULL`) AS `EMAIL_FULL` 
		FROM `moteur_utilisateur` 
		WHERE `UTILISATEUR_ID` IN (
		SELECT DISTINCT(`MOTEUR_TRACE_UTILISATEUR_ID`) 
		FROM `moteur_trace` 
		WHERE `MOTEUR_TRACE_CATEGORIE`='Changement' 
		AND `MOTEUR_TRACE_ETAT` IN(
		SELECT `CHANGEMENT_STATUS_ID` 
		FROM `changement_status` 
		WHERE `CHANGEMENT_STATUS` IN('Brouillon','Inscrit') 
		AND `ENABLE`=0
		) 
		AND `MOTEUR_TRACE_REF_ID`='".$ID."'
		) 
		AND UPPER(`EMAIL_FULL`) NOT IN(
		SELECT DISTINCT(UPPER(`CHANGEMENT_MAIL_LIB`)) AS `CHANGEMENT_MAIL_LIB`
		FROM `changement_mail`
		WHERE `ENABLE`='0' 
		AND `CHANGEMENT_STATUS_ID` IN (
		SELECT `CHANGEMENT_STATUS_ID` 
		FROM `changement_liste` 
		WHERE `ENABLE`='0' AND `CHANGEMENT_LISTE_ID`='".$ID."' 
		)
		AND `CHANGEMENT_DEMANDE_ID` IN (
		SELECT `CHANGEMENT_DEMANDE_ID` 
		FROM `changement_liste` 
		WHERE `ENABLE`='0' AND `CHANGEMENT_LISTE_ID`='".$ID."'
		)
		)
		";
		$res_rq_info_mail_demandeur = mysql_query($rq_info_mail_demandeur, $mysql_link) or die(mysql_error());
		$tab_rq_info_mail_demandeur = mysql_fetch_assoc($res_rq_info_mail_demandeur);
		$total_ligne_rq_info_mail_demandeur=mysql_num_rows($res_rq_info_mail_demandeur);
		if($total_ligne_rq_info_mail_demandeur!=0){
			do {
				if($NB_MAIL==0){
					$MAIL_A .= $tab_rq_info_mail_demandeur['EMAIL_FULL']; 
					//$a_mail->AddAddress($tab_rq_info_mail_demandeur['EMAIL_FULL']);
					$NB_MAIL=$NB_MAIL+1;
				}else{
					$MAIL_A .= ';'.$tab_rq_info_mail_demandeur['EMAIL_FULL']; 
					//$a_mail->AddAddress($tab_rq_info_mail_demandeur['EMAIL_FULL']);
					$NB_MAIL=$NB_MAIL+1;
				}
			
			} while ($tab_rq_info_mail_demandeur = mysql_fetch_assoc($res_rq_info_mail_demandeur));
			$ligne2= mysql_num_rows($res_rq_info_mail_demandeur);
			if($ligne2 > 0) {
				mysql_data_seek($res_rq_info_mail_demandeur, 0);
				$tab_rq_info_mail_demandeur = mysql_fetch_assoc($res_rq_info_mail_demandeur);
			}
		}
		mysql_free_result($res_rq_info_mail_demandeur);
	}
}else{
	if($tab_rq_info_mail['CHANGEMENT_MAIL_LIB']!='demandeur@caissedesdepots.fr'){
		$MAIL_A .= ';'.$tab_rq_info_mail['CHANGEMENT_MAIL_LIB']; 
		//$a_mail->AddAddress($tab_rq_info_mail['CHANGEMENT_MAIL_LIB']);
		$NB_MAIL=$NB_MAIL+1;
	}else{
		$rq_info_mail_demandeur="
		SELECT DISTINCT(`EMAIL_FULL`) AS `EMAIL_FULL` 
		FROM `moteur_utilisateur` 
		WHERE `UTILISATEUR_ID` IN (
		SELECT DISTINCT(`MOTEUR_TRACE_UTILISATEUR_ID`) 
		FROM `moteur_trace` 
		WHERE `MOTEUR_TRACE_CATEGORIE`='Changement' 
		AND `MOTEUR_TRACE_ETAT` IN(
		SELECT `CHANGEMENT_STATUS_ID` 
		FROM `changement_status` 
		WHERE `CHANGEMENT_STATUS` IN('Brouillon','Inscrit') 
		AND `ENABLE`=0
		) 
		AND `MOTEUR_TRACE_REF_ID`='".$ID."'
		) 
		AND UPPER(`EMAIL_FULL`) NOT IN(
		SELECT DISTINCT(UPPER(`CHANGEMENT_MAIL_LIB`)) AS `CHANGEMENT_MAIL_LIB`
		FROM `changement_mail`
		WHERE `ENABLE`='0' 
		AND `CHANGEMENT_STATUS_ID` IN (
		SELECT `CHANGEMENT_STATUS_ID` 
		FROM `changement_liste` 
		WHERE `ENABLE`='0' AND `CHANGEMENT_LISTE_ID`='".$ID."' 
		)
		AND `CHANGEMENT_DEMANDE_ID` IN (
		SELECT `CHANGEMENT_DEMANDE_ID` 
		FROM `changement_liste` 
		WHERE `ENABLE`='0' AND `CHANGEMENT_LISTE_ID`='".$ID."'
		)
		)";
		$res_rq_info_mail_demandeur = mysql_query($rq_info_mail_demandeur, $mysql_link) or die(mysql_error());
		$tab_rq_info_mail_demandeur = mysql_fetch_assoc($res_rq_info_mail_demandeur);
		$total_ligne_rq_info_mail_demandeur=mysql_num_rows($res_rq_info_mail_demandeur);
		if($total_ligne_rq_info_mail_demandeur!=0){
			do {
				if($NB_MAIL==0){
					$MAIL_A .= $tab_rq_info_mail_demandeur['EMAIL_FULL']; 
					//$a_mail->AddAddress($tab_rq_info_mail_demandeur['EMAIL_FULL']);
					$NB_MAIL=$NB_MAIL+1;
				}else{
					$MAIL_A .= ';'.$tab_rq_info_mail_demandeur['EMAIL_FULL']; 
					//$a_mail->AddAddress($tab_rq_info_mail_demandeur['EMAIL_FULL']);
					$NB_MAIL=$NB_MAIL+1;
				}
			
			} while ($tab_rq_info_mail_demandeur = mysql_fetch_assoc($res_rq_info_mail_demandeur));
			$ligne2= mysql_num_rows($res_rq_info_mail_demandeur);
			if($ligne2 > 0) {
				mysql_data_seek($res_rq_info_mail_demandeur, 0);
				$tab_rq_info_mail_demandeur = mysql_fetch_assoc($res_rq_info_mail_demandeur);
			}
		}
		mysql_free_result($res_rq_info_mail_demandeur);
	}
}
			
			 } while ($tab_rq_info_mail = mysql_fetch_assoc($res_rq_info_mail));
		        $ligne= mysql_num_rows($res_rq_info_mail);
		        if($ligne > 0) {
		          mysql_data_seek($res_rq_info_mail, 0);
		          $tab_rq_info_mail = mysql_fetch_assoc($res_rq_info_mail);
		        }
		}
		
	  }
	  mysql_free_result($res_rq_info_mail);
	  if($NB_MAIL!=0){
		Export_to_fichier('./old_changement/',$fichier, $corps, 'w+');
	
	        $sql = "INSERT INTO `changement_mail_trace` (`CHANGEMENT_MAIL_TRACE_ID` ,`CHANGEMENT_MAIL_TRACE_UTILISATEUR_ID` ,`CHANGEMENT_MAIL_TRACE_DATE` ,`CHANGEMENT_MAIL_TRACE_TYPE` ,`CHANGEMENT_MAIL_TRACE_CHANGEMENT_ID` ,`CHANGEMENT_MAIL_TRACE_DEST` ,`CHANGEMENT_MAIL_TRACE_ARCHIVE` ) VALUES (NULL , '".$UTILISATEUR_ID."', '".$DATE_MODIFICATION."', '".$type."', '".$CHANGEMENT_ID."', '".$MAIL_A."', '".$fichier."');";
		mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
		$TABLE_SQL_SQL='changement_mail_trace';       
		historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');   
		
		$sql = "OPTIMIZE TABLE `changement_mail_trace`";
		mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
		$TABLE_SQL_SQL='changement_mail_trace';       
		historique_sql_new($sql,$TABLE_SQL_SQL,'OPTIMIZE'); 
	}
	
	

}

function mailMailInfo_php($CHANGEMENT_ID,$Personne_email_FULL, $redac_court, $objet, $corps, $type, $ENV,$mysql_link)
{	
	$ID=$CHANGEMENT_ID;
	$a_mail = new PHPMailer();
	$a_mail->SMTPDebug = 2; //pour débug
	$a_mail->Priority = 3;
	$a_mail->Encoding = "8bit";
	$a_mail->CharSet = "iso-8859-1";
	
	$a_mail->Sender = $Personne_email_FULL;
	$a_mail->IsMail();
	$a_mail->Host = "dpiddr.re.cdc.fr";
	$a_mail->From = $Personne_email_FULL;
	$a_mail->FromName = $Personne_email_FULL;
	$a_mail->SMTPAuth = true;
	$a_mail->Subject = $objet; 
	
	$NB_MAIL=0;
	$rq_info_mail="
	SELECT DISTINCT(`CHANGEMENT_MAIL_LIB`) AS `CHANGEMENT_MAIL_LIB`
	FROM `changement_mail`
	WHERE `ENABLE`='0' 
	AND `CHANGEMENT_STATUS_ID` IN (
	SELECT `CHANGEMENT_STATUS_ID` 
	FROM `changement_liste` 
	WHERE `ENABLE`='0' AND `CHANGEMENT_LISTE_ID`='".$ID."' 
	)
	AND `CHANGEMENT_DEMANDE_ID` IN (
	SELECT `CHANGEMENT_DEMANDE_ID` 
	FROM `changement_liste` 
	WHERE `ENABLE`='0' AND `CHANGEMENT_LISTE_ID`='".$ID."'
	);";
	$res_rq_info_mail = mysql_query($rq_info_mail, $mysql_link) or die(mysql_error());
	$tab_rq_info_mail = mysql_fetch_assoc($res_rq_info_mail);
	$total_ligne_rq_info_mail=mysql_num_rows($res_rq_info_mail);
	  if ( $ENV == "x" )
	  {	 	
		if($total_ligne_rq_info_mail!=0){
			do {
				if($NB_MAIL==0){
	if($tab_rq_info_mail['CHANGEMENT_MAIL_LIB']!='demandeur@caissedesdepots.fr'){
		//$MAIL_A .= $tab_rq_info_mail['CHANGEMENT_MAIL_LIB']; 
		$a_mail->AddAddress($tab_rq_info_mail['CHANGEMENT_MAIL_LIB']);
		$NB_MAIL=$NB_MAIL+1;
	}else{
		$rq_info_mail_demandeur="
		SELECT DISTINCT(`EMAIL_FULL`) AS `EMAIL_FULL` 
		FROM `moteur_utilisateur` 
		WHERE `UTILISATEUR_ID` IN (
		SELECT DISTINCT(`MOTEUR_TRACE_UTILISATEUR_ID`) 
		FROM `moteur_trace` 
		WHERE `MOTEUR_TRACE_CATEGORIE`='Changement' 
		AND `MOTEUR_TRACE_ETAT` IN(
		SELECT `CHANGEMENT_STATUS_ID` 
		FROM `changement_status` 
		WHERE `CHANGEMENT_STATUS` IN('Brouillon','Inscrit') 
		AND `ENABLE`=0
		) 
		AND `MOTEUR_TRACE_REF_ID`='".$ID."'
		) 
		AND UPPER(`EMAIL_FULL`) NOT IN(
		SELECT DISTINCT(UPPER(`CHANGEMENT_MAIL_LIB`)) AS `CHANGEMENT_MAIL_LIB`
		FROM `changement_mail`
		WHERE `ENABLE`='0' 
		AND `CHANGEMENT_STATUS_ID` IN (
		SELECT `CHANGEMENT_STATUS_ID` 
		FROM `changement_liste` 
		WHERE `ENABLE`='0' AND `CHANGEMENT_LISTE_ID`='".$ID."' 
		)
		AND `CHANGEMENT_DEMANDE_ID` IN (
		SELECT `CHANGEMENT_DEMANDE_ID` 
		FROM `changement_liste` 
		WHERE `ENABLE`='0' AND `CHANGEMENT_LISTE_ID`='".$ID."'
		)
		)";
		$res_rq_info_mail_demandeur = mysql_query($rq_info_mail_demandeur, $mysql_link) or die(mysql_error());
		$tab_rq_info_mail_demandeur = mysql_fetch_assoc($res_rq_info_mail_demandeur);
		$total_ligne_rq_info_mail_demandeur=mysql_num_rows($res_rq_info_mail_demandeur);
		if($total_ligne_rq_info_mail_demandeur!=0){
			do {
				if($NB_MAIL==0){
					//$MAIL_A .= $tab_rq_info_mail_demandeur['EMAIL_FULL']; 
					$a_mail->AddAddress($tab_rq_info_mail_demandeur['EMAIL_FULL']);
					$NB_MAIL=$NB_MAIL+1;
				}else{
					//$MAIL_A .= ';'.$tab_rq_info_mail_demandeur['EMAIL_FULL']; 
					$a_mail->AddAddress($tab_rq_info_mail_demandeur['EMAIL_FULL']);
					$NB_MAIL=$NB_MAIL+1;
				}
			
			} while ($tab_rq_info_mail_demandeur = mysql_fetch_assoc($res_rq_info_mail_demandeur));
			$ligne2= mysql_num_rows($res_rq_info_mail_demandeur);
			if($ligne2 > 0) {
				mysql_data_seek($res_rq_info_mail_demandeur, 0);
				$tab_rq_info_mail_demandeur = mysql_fetch_assoc($res_rq_info_mail_demandeur);
			}
		}
		mysql_free_result($res_rq_info_mail_demandeur);
	}
}else{
	if($tab_rq_info_mail['CHANGEMENT_MAIL_LIB']!='demandeur@caissedesdepots.fr'){
		//$MAIL_A .= ';'.$tab_rq_info_mail['CHANGEMENT_MAIL_LIB']; 
		$a_mail->AddAddress($tab_rq_info_mail['CHANGEMENT_MAIL_LIB']);
		$NB_MAIL=$NB_MAIL+1;
	}else{
		$rq_info_mail_demandeur="
		SELECT DISTINCT(`EMAIL_FULL`) AS `EMAIL_FULL` 
		FROM `moteur_utilisateur` 
		WHERE `UTILISATEUR_ID` IN (
		SELECT DISTINCT(`MOTEUR_TRACE_UTILISATEUR_ID`) 
		FROM `moteur_trace` 
		WHERE `MOTEUR_TRACE_CATEGORIE`='Changement' 
		AND `MOTEUR_TRACE_ETAT` IN(
		SELECT `CHANGEMENT_STATUS_ID` 
		FROM `changement_status` 
		WHERE `CHANGEMENT_STATUS` IN('Brouillon','Inscrit') 
		AND `ENABLE`=0
		) 
		AND `MOTEUR_TRACE_REF_ID`='".$ID."'
		) 
		AND UPPER(`EMAIL_FULL`) NOT IN(
		SELECT DISTINCT(UPPER(`CHANGEMENT_MAIL_LIB`)) AS `CHANGEMENT_MAIL_LIB`
		FROM `changement_mail`
		WHERE `ENABLE`='0' 
		AND `CHANGEMENT_STATUS_ID` IN (
		SELECT `CHANGEMENT_STATUS_ID` 
		FROM `changement_liste` 
		WHERE `ENABLE`='0' AND `CHANGEMENT_LISTE_ID`='".$ID."' 
		)
		AND `CHANGEMENT_DEMANDE_ID` IN (
		SELECT `CHANGEMENT_DEMANDE_ID` 
		FROM `changement_liste` 
		WHERE `ENABLE`='0' AND `CHANGEMENT_LISTE_ID`='".$ID."'
		)
		)";
		$res_rq_info_mail_demandeur = mysql_query($rq_info_mail_demandeur, $mysql_link) or die(mysql_error());
		$tab_rq_info_mail_demandeur = mysql_fetch_assoc($res_rq_info_mail_demandeur);
		$total_ligne_rq_info_mail_demandeur=mysql_num_rows($res_rq_info_mail_demandeur);
		if($total_ligne_rq_info_mail_demandeur!=0){
			do {
				if($NB_MAIL==0){
					//$MAIL_A .= $tab_rq_info_mail_demandeur['EMAIL_FULL']; 
					$a_mail->AddAddress($tab_rq_info_mail_demandeur['EMAIL_FULL']);
					$NB_MAIL=$NB_MAIL+1;
				}else{
					//$MAIL_A .= ';'.$tab_rq_info_mail_demandeur['EMAIL_FULL']; 
					$a_mail->AddAddress($tab_rq_info_mail_demandeur['EMAIL_FULL']);
					$NB_MAIL=$NB_MAIL+1;
				}
			
			} while ($tab_rq_info_mail_demandeur = mysql_fetch_assoc($res_rq_info_mail_demandeur));
			$ligne2= mysql_num_rows($res_rq_info_mail_demandeur);
			if($ligne2 > 0) {
				mysql_data_seek($res_rq_info_mail_demandeur, 0);
				$tab_rq_info_mail_demandeur = mysql_fetch_assoc($res_rq_info_mail_demandeur);
			}
		}
		mysql_free_result($res_rq_info_mail_demandeur);
	}
}
			
			 } while ($tab_rq_info_mail = mysql_fetch_assoc($res_rq_info_mail));
		        $ligne= mysql_num_rows($res_rq_info_mail);
		        if($ligne > 0) {
		          mysql_data_seek($res_rq_info_mail, 0);
		          $tab_rq_info_mail = mysql_fetch_assoc($res_rq_info_mail);
		        }
		}	    
	  }else{
	  	//on envoie toujours en dev a $Personne_email_FULL
	  	//$MAIL_A .= $Personne_email_FULL; 
	  	//$a_mail->AddAddress($Personne_email_FULL);
		if($total_ligne_rq_info_mail!=0){
			do {
if($NB_MAIL==0){
	if($tab_rq_info_mail['CHANGEMENT_MAIL_LIB']!='demandeur@caissedesdepots.fr'){
		//$MAIL_A .= $tab_rq_info_mail['CHANGEMENT_MAIL_LIB']; 
		$a_mail->AddAddress($tab_rq_info_mail['CHANGEMENT_MAIL_LIB']);
		$NB_MAIL=$NB_MAIL+1;
	}else{
		$rq_info_mail_demandeur="
		SELECT DISTINCT(`EMAIL_FULL`) AS `EMAIL_FULL` 
		FROM `moteur_utilisateur` 
		WHERE `UTILISATEUR_ID` IN (
		SELECT DISTINCT(`MOTEUR_TRACE_UTILISATEUR_ID`) 
		FROM `moteur_trace` 
		WHERE `MOTEUR_TRACE_CATEGORIE`='Changement' 
		AND `MOTEUR_TRACE_ETAT` IN(
		SELECT `CHANGEMENT_STATUS_ID` 
		FROM `changement_status` 
		WHERE `CHANGEMENT_STATUS` IN('Brouillon','Inscrit') 
		AND `ENABLE`=0
		) 
		AND `MOTEUR_TRACE_REF_ID`='".$ID."'
		) 
		AND UPPER(`EMAIL_FULL`) NOT IN(
		SELECT DISTINCT(UPPER(`CHANGEMENT_MAIL_LIB`)) AS `CHANGEMENT_MAIL_LIB`
		FROM `changement_mail`
		WHERE `ENABLE`='0' 
		AND `CHANGEMENT_STATUS_ID` IN (
		SELECT `CHANGEMENT_STATUS_ID` 
		FROM `changement_liste` 
		WHERE `ENABLE`='0' AND `CHANGEMENT_LISTE_ID`='".$ID."' 
		)
		AND `CHANGEMENT_DEMANDE_ID` IN (
		SELECT `CHANGEMENT_DEMANDE_ID` 
		FROM `changement_liste` 
		WHERE `ENABLE`='0' AND `CHANGEMENT_LISTE_ID`='".$ID."'
		)
		)";
		$res_rq_info_mail_demandeur = mysql_query($rq_info_mail_demandeur, $mysql_link) or die(mysql_error());
		$tab_rq_info_mail_demandeur = mysql_fetch_assoc($res_rq_info_mail_demandeur);
		$total_ligne_rq_info_mail_demandeur=mysql_num_rows($res_rq_info_mail_demandeur);
		if($total_ligne_rq_info_mail_demandeur!=0){
			do {
				if($NB_MAIL==0){
					//$MAIL_A .= $tab_rq_info_mail_demandeur['EMAIL_FULL']; 
					$a_mail->AddAddress($tab_rq_info_mail_demandeur['EMAIL_FULL']);
					$NB_MAIL=$NB_MAIL+1;
				}else{
					//$MAIL_A .= ';'.$tab_rq_info_mail_demandeur['EMAIL_FULL']; 
					$a_mail->AddAddress($tab_rq_info_mail_demandeur['EMAIL_FULL']);
					$NB_MAIL=$NB_MAIL+1;
				}
			
			} while ($tab_rq_info_mail_demandeur = mysql_fetch_assoc($res_rq_info_mail_demandeur));
			$ligne2= mysql_num_rows($res_rq_info_mail_demandeur);
			if($ligne2 > 0) {
				mysql_data_seek($res_rq_info_mail_demandeur, 0);
				$tab_rq_info_mail_demandeur = mysql_fetch_assoc($res_rq_info_mail_demandeur);
			}
		}
		mysql_free_result($res_rq_info_mail_demandeur);
	}
}else{
	if($tab_rq_info_mail['CHANGEMENT_MAIL_LIB']!='demandeur@caissedesdepots.fr'){
		//$MAIL_A .= ';'.$tab_rq_info_mail['CHANGEMENT_MAIL_LIB']; 
		$a_mail->AddAddress($tab_rq_info_mail['CHANGEMENT_MAIL_LIB']);
		$NB_MAIL=$NB_MAIL+1;
	}else{
		$rq_info_mail_demandeur="
		SELECT DISTINCT(`EMAIL_FULL`) AS `EMAIL_FULL` 
		FROM `moteur_utilisateur` 
		WHERE `UTILISATEUR_ID` IN (
		SELECT DISTINCT(`MOTEUR_TRACE_UTILISATEUR_ID`) 
		FROM `moteur_trace` 
		WHERE `MOTEUR_TRACE_CATEGORIE`='Changement' 
		AND `MOTEUR_TRACE_ETAT` IN(
		SELECT `CHANGEMENT_STATUS_ID` 
		FROM `changement_status` 
		WHERE `CHANGEMENT_STATUS` IN('Brouillon','Inscrit') 
		AND `ENABLE`=0
		) 
		AND `MOTEUR_TRACE_REF_ID`='".$ID."'
		) 
		AND UPPER(`EMAIL_FULL`) NOT IN(
		SELECT DISTINCT(UPPER(`CHANGEMENT_MAIL_LIB`)) AS `CHANGEMENT_MAIL_LIB`
		FROM `changement_mail`
		WHERE `ENABLE`='0' 
		AND `CHANGEMENT_STATUS_ID` IN (
		SELECT `CHANGEMENT_STATUS_ID` 
		FROM `changement_liste` 
		WHERE `ENABLE`='0' AND `CHANGEMENT_LISTE_ID`='".$ID."' 
		)
		AND `CHANGEMENT_DEMANDE_ID` IN (
		SELECT `CHANGEMENT_DEMANDE_ID` 
		FROM `changement_liste` 
		WHERE `ENABLE`='0' AND `CHANGEMENT_LISTE_ID`='".$ID."'
		)
		)";
		$res_rq_info_mail_demandeur = mysql_query($rq_info_mail_demandeur, $mysql_link) or die(mysql_error());
		$tab_rq_info_mail_demandeur = mysql_fetch_assoc($res_rq_info_mail_demandeur);
		$total_ligne_rq_info_mail_demandeur=mysql_num_rows($res_rq_info_mail_demandeur);
		if($total_ligne_rq_info_mail_demandeur!=0){
			do {
				if($NB_MAIL==0){
					//$MAIL_A .= $tab_rq_info_mail_demandeur['EMAIL_FULL']; 
					$a_mail->AddAddress($tab_rq_info_mail_demandeur['EMAIL_FULL']);
					$NB_MAIL=$NB_MAIL+1;
				}else{
					//$MAIL_A .= ';'.$tab_rq_info_mail_demandeur['EMAIL_FULL']; 
					$a_mail->AddAddress($tab_rq_info_mail_demandeur['EMAIL_FULL']);
					$NB_MAIL=$NB_MAIL+1;
				}
			
			} while ($tab_rq_info_mail_demandeur = mysql_fetch_assoc($res_rq_info_mail_demandeur));
			$ligne2= mysql_num_rows($res_rq_info_mail_demandeur);
			if($ligne2 > 0) {
				mysql_data_seek($res_rq_info_mail_demandeur, 0);
				$tab_rq_info_mail_demandeur = mysql_fetch_assoc($res_rq_info_mail_demandeur);
			}
		}
		mysql_free_result($res_rq_info_mail_demandeur);
	}
}
			
			 } while ($tab_rq_info_mail = mysql_fetch_assoc($res_rq_info_mail));
		        $ligne= mysql_num_rows($res_rq_info_mail);
		        if($ligne > 0) {
		          mysql_data_seek($res_rq_info_mail, 0);
		          $tab_rq_info_mail = mysql_fetch_assoc($res_rq_info_mail);
		        }
		}
		
	  }
	  mysql_free_result($res_rq_info_mail);

	$a_mail->Body = $corps; 
	$a_mail->IsHTML(true);
	$a_mail->AddEmbeddedImage('./Images/texture_blanc.gif', "texture_blanc.gif", 'texture_blanc.gif',"base64", 'image/jpeg');
	if($NB_MAIL!=0){
		$a_mail->Send();	
	}
	
}

function header_mess()
{
	$mess_header  ='';
	$mess_header .='<head>'."\n";
	$mess_header .='  <meta content="text/html; charset=ISO-8859-1" http-equiv="content-type">'."\n";
	$mess_header .='  <title>Gestion des changements</title>'."\n";
	$mess_header .='  <style type="text/css" media="screen">'."\n";
	$mess_header .='    <!--'."\n";
	$mess_header .='    /* interet de la css : controler l ensemble de l affichage depuis cette petite zone de déclaration !!*/'."\n";
	$mess_header .='    /*redefinition affichage balise html*/'."\n";
	$mess_header .='    /*Pour changer la taille des charactères : changer la valeur font-size:*/'."\n";
	$mess_header .='    /*Pour changer la taille de la marge, changer la valeur margin-left */'."\n";
	$mess_header .='    /*definition des ID*/'."\n";
	$mess_header .='    #body{'."\n";
	$mess_header .='      font-family: arial;'."\n";
	$mess_header .='      font-size:10pt'."\n";
	$mess_header .='      }'."\n";
	$mess_header .='    #titre{'."\n";
	$mess_header .='      font-family: comics;'."\n";
	$mess_header .='      font-size:20pt ;'."\n";
	$mess_header .='      font: bold;'."\n";
	$mess_header .='      display:inline;'."\n";
	$mess_header .='      }'."\n";
	$mess_header .='    #ligne{'."\n";
	$mess_header .='      font-family: comics;'."\n";
	$mess_header .='      font-size:12pt ;'."\n";
	$mess_header .='      font: bold;'."\n";
	$mess_header .='      display:inline;'."\n";
	$mess_header .='      }'."\n";
	$mess_header .='    #log{'."\n";
	$mess_header .='      font-family: comics;'."\n";
	$mess_header .='      font-size: 10pt ;'."\n";
	$mess_header .='      color: blue;'."\n";
	$mess_header .='      display:inline;'."\n";
	$mess_header .='      }'."\n";
	$mess_header .='    .marge{margin-left :4em}'."\n";
	$mess_header .='    //-->'."\n";
	$mess_header .='  </style>'."\n";
	$mess_header .='</head>'."\n";
	
	return $mess_header;
}

function Constitue_corps_Message($CHANGEMENT_ID,$Personne_complet,$mysql_link)
{
	$ID=$CHANGEMENT_ID;
	$message  = '';
	$message .='<html>'."\n";
	$message .=header_mess();
	$message .= '<body background="cid:texture_blanc.gif">'."\n";
	$message .= '<div id="body">';
	$message .= '<br>'."\n";
	$rq_info="
	SELECT 
	`changement_liste`.`CHANGEMENT_LISTE_ID`, 
	`changement_liste`.`CHANGEMENT_LISTE_UTILISATEUR_ID`, 
	`changement_liste`.`CHANGEMENT_LISTE_DATE_DEBUT`, 
	`changement_liste`.`CHANGEMENT_LISTE_DATE_FIN`, 
	`changement_liste`.`CHANGEMENT_LISTE_HEURE_DEBUT`, 
	`changement_liste`.`CHANGEMENT_LISTE_HEURE_FIN`, 
	`changement_liste`.`CHANGEMENT_LISTE_DATE_MODIFICATION`, 
	`changement_liste`.`CHANGEMENT_LISTE_LIB`, 
	`changement_liste`.`CHANGEMENT_STATUS_ID`, 
	`changement_status`.`CHANGEMENT_STATUS`,
	`changement_liste`.`ENABLE` 
	FROM `changement_liste`,`changement_status` 
	WHERE `changement_liste`.`CHANGEMENT_STATUS_ID`=`changement_status`.`CHANGEMENT_STATUS_ID`
	AND `changement_liste`.`CHANGEMENT_LISTE_ID` ='".$ID."'
	AND `changement_liste`.`ENABLE` ='0'
	LIMIT 1";
	$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
	$tab_rq_info = mysql_fetch_assoc($res_rq_info);
	$total_ligne_rq_info=mysql_num_rows($res_rq_info);
	if($total_ligne_rq_info!=0){
		$CHANGEMENT_ID=$tab_rq_info['CHANGEMENT_LISTE_ID'];
		$ID=$tab_rq_info['CHANGEMENT_LISTE_ID'];
		$CHANGEMENT_STATUS=$tab_rq_info['CHANGEMENT_STATUS'];
		$CHANGEMENT_LISTE_DATE_DEBUT=$tab_rq_info['CHANGEMENT_LISTE_DATE_DEBUT'];
		$JOUR_Inter_Debut=substr($CHANGEMENT_LISTE_DATE_DEBUT,6,2);
		$MOIS_Inter_Debut=substr($CHANGEMENT_LISTE_DATE_DEBUT,4,2);
		$ANNEE_Inter_Debut=substr($CHANGEMENT_LISTE_DATE_DEBUT,0,4);
		$Date_Inter_Debut=$JOUR_Inter_Debut.'/'.$MOIS_Inter_Debut.'/'.$ANNEE_Inter_Debut;
		
		$CHANGEMENT_LISTE_DATE_FIN=$tab_rq_info['CHANGEMENT_LISTE_DATE_FIN'];
		$JOUR_Inter_Fin=substr($CHANGEMENT_LISTE_DATE_FIN,6,2);
		$MOIS_Inter_Fin=substr($CHANGEMENT_LISTE_DATE_FIN,4,2);
		$ANNEE_Inter_Fin=substr($CHANGEMENT_LISTE_DATE_FIN,0,4);
		$Date_Inter_Fin=$JOUR_Inter_Fin.'/'.$MOIS_Inter_Fin.'/'.$ANNEE_Inter_Fin;
		
		$CHANGEMENT_LISTE_HEURE_DEBUT=$tab_rq_info['CHANGEMENT_LISTE_HEURE_DEBUT'];
		$HEURE_PLANIF_DEBUT_H=substr($CHANGEMENT_LISTE_HEURE_DEBUT,0,2);
		$HEURE_PLANIF_DEBUT_M=substr($CHANGEMENT_LISTE_HEURE_DEBUT,3,2);
		
		$CHANGEMENT_LISTE_HEURE_FIN=$tab_rq_info['CHANGEMENT_LISTE_HEURE_FIN'];
		$HEURE_PLANIF_FIN_H=substr($CHANGEMENT_LISTE_HEURE_FIN,0,2);
		$HEURE_PLANIF_FIN_M=substr($CHANGEMENT_LISTE_HEURE_FIN,3,2);
		
		$lib=$tab_rq_info['CHANGEMENT_LISTE_LIB'];
	
	}
	mysql_free_result($res_rq_info);

	$message .= '<table>'."\n";
	$message .= '<tr align="center" >'."\n";
	$message .= '<td colspan="4"><b>&nbsp;Information sur le changement <a href="http://'.$_SERVER["HTTP_HOST"].''.$_SERVER["SCRIPT_NAME"].'?ITEM=changement_Info_Changement&amp;action=Info&amp;ID='.$ID.'" >'.$ID.'</a>&nbsp;</b></td>'."\n";
	$message .= '</tr>'."\n";
	$message .= '<tr>'."\n";
	$message .= '<td align="left">&nbsp;Date de D&eacute;but&nbsp;</td>'."\n";
	$message .= '<td align="left">&nbsp;'.$Date_Inter_Debut.'&nbsp;</td>'."\n";
	$message .= '<td align="left">&nbsp;Heure de D&eacute;but&nbsp;</td>'."\n";
	$message .= '<td align="left">&nbsp;'.$HEURE_PLANIF_DEBUT_H.'h'.$HEURE_PLANIF_DEBUT_M.'&nbsp;</td>'."\n";
	$message .= '</tr>'."\n";
	$message .= '<tr>'."\n";
	$message .= '<td align="left">&nbsp;Date de fin&nbsp;</td>'."\n";
	$message .= '<td align="left">&nbsp;'.$Date_Inter_Fin.'&nbsp;</td>'."\n";
	$message .= '<td align="left">&nbsp;Heure de Fin&nbsp;</td>'."\n";
	$message .= '<td align="left">&nbsp;'.$HEURE_PLANIF_FIN_H.'h'.$HEURE_PLANIF_FIN_M.'</td>'."\n";
	$message .= '</tr>'."\n";
	$message .= '<tr>'."\n";
	$message .= '<td align="left">&nbsp;Titre du Changement&nbsp;</td>'."\n";
	$message .= '<td align="left" colspan="3">&nbsp;'.stripslashes($lib).'&nbsp;</td>'."\n";
	$message .= '</tr>'."\n";
	$message .= '<tr>'."\n";
	$message .= '<td align="left">&nbsp;Status&nbsp;</td>'."\n";
	$message .= '<td align="left" colspan="3">&nbsp;'.$CHANGEMENT_STATUS.'&nbsp;</td>'."\n";
	$message .= '</tr>'."\n";
	$message .= '</table>'."\n";
	$message .= '<br><br>'."\n";
	$message .='<div id="signature">'."\n";
	$message .='  <b>'.$Personne_complet.'</b><br>'."\n";
	$message .='  Gestion des changements<br>'."\n";
	$message .='  Informatique CDC - Etablissement DPI<br>'."\n";
	$message .='</div>   '."\n";	
	$message .='</div>   '."\n";  
	$message .= '</body></html>' ."\n";
	$message =stripslashes($message);
	return $message;
}

function Constitue_corps_Message_SVG($CHANGEMENT_ID,$ENV,$Personne_email_FULL,$Personne_complet,$objet,$mysql_link)
{
	$ID=$CHANGEMENT_ID;
	$MAIL_A='';
$NB_MAIL=0;
	$rq_info_mail="
	SELECT DISTINCT(`CHANGEMENT_MAIL_LIB`) AS `CHANGEMENT_MAIL_LIB`
	FROM `changement_mail`
	WHERE `ENABLE`='0' 
	AND `CHANGEMENT_STATUS_ID` IN (
	SELECT `CHANGEMENT_STATUS_ID` 
	FROM `changement_liste` 
	WHERE `ENABLE`='0' AND `CHANGEMENT_LISTE_ID`='".$ID."' 
	)
	AND `CHANGEMENT_DEMANDE_ID` IN (
	SELECT `CHANGEMENT_DEMANDE_ID` 
	FROM `changement_liste` 
	WHERE `ENABLE`='0' AND `CHANGEMENT_LISTE_ID`='".$ID."'
	);";
	$res_rq_info_mail = mysql_query($rq_info_mail, $mysql_link) or die(mysql_error());
	$tab_rq_info_mail = mysql_fetch_assoc($res_rq_info_mail);
	$total_ligne_rq_info_mail=mysql_num_rows($res_rq_info_mail);
	  if ( $ENV == "x" )
	  {	 	
		if($total_ligne_rq_info_mail!=0){
			do {
				if($NB_MAIL==0){
	if($tab_rq_info_mail['CHANGEMENT_MAIL_LIB']!='demandeur@caissedesdepots.fr'){
		$MAIL_A .= $tab_rq_info_mail['CHANGEMENT_MAIL_LIB']; 
		//$a_mail->AddAddress($tab_rq_info_mail['CHANGEMENT_MAIL_LIB']);
		$NB_MAIL=$NB_MAIL+1;
	}else{
		$rq_info_mail_demandeur="
		SELECT DISTINCT(`EMAIL_FULL`) AS `EMAIL_FULL` 
		FROM `moteur_utilisateur` 
		WHERE `UTILISATEUR_ID` IN (
		SELECT DISTINCT(`MOTEUR_TRACE_UTILISATEUR_ID`) 
		FROM `moteur_trace` 
		WHERE `MOTEUR_TRACE_CATEGORIE`='Changement' 
		AND `MOTEUR_TRACE_ETAT` IN(
		SELECT `CHANGEMENT_STATUS_ID` 
		FROM `changement_status` 
		WHERE `CHANGEMENT_STATUS` IN('Brouillon','Inscrit') 
		AND `ENABLE`=0
		) 
		AND `MOTEUR_TRACE_REF_ID`='".$ID."'
		) 
		AND UPPER(`EMAIL_FULL`) NOT IN(
		SELECT DISTINCT(UPPER(`CHANGEMENT_MAIL_LIB`)) AS `CHANGEMENT_MAIL_LIB`
		FROM `changement_mail`
		WHERE `ENABLE`='0' 
		AND `CHANGEMENT_STATUS_ID` IN (
		SELECT `CHANGEMENT_STATUS_ID` 
		FROM `changement_liste` 
		WHERE `ENABLE`='0' AND `CHANGEMENT_LISTE_ID`='".$ID."' 
		)
		AND `CHANGEMENT_DEMANDE_ID` IN (
		SELECT `CHANGEMENT_DEMANDE_ID` 
		FROM `changement_liste` 
		WHERE `ENABLE`='0' AND `CHANGEMENT_LISTE_ID`='".$ID."'
		)
		)";
		$res_rq_info_mail_demandeur = mysql_query($rq_info_mail_demandeur, $mysql_link) or die(mysql_error());
		$tab_rq_info_mail_demandeur = mysql_fetch_assoc($res_rq_info_mail_demandeur);
		$total_ligne_rq_info_mail_demandeur=mysql_num_rows($res_rq_info_mail_demandeur);
		if($total_ligne_rq_info_mail_demandeur!=0){
			do {
				if($NB_MAIL==0){
					$MAIL_A .= $tab_rq_info_mail_demandeur['EMAIL_FULL']; 
					//$a_mail->AddAddress($tab_rq_info_mail_demandeur['EMAIL_FULL']);
					$NB_MAIL=$NB_MAIL+1;
				}else{
					$MAIL_A .= ';'.$tab_rq_info_mail_demandeur['EMAIL_FULL']; 
					//$a_mail->AddAddress($tab_rq_info_mail_demandeur['EMAIL_FULL']);
					$NB_MAIL=$NB_MAIL+1;
				}
			
			} while ($tab_rq_info_mail_demandeur = mysql_fetch_assoc($res_rq_info_mail_demandeur));
			$ligne2= mysql_num_rows($res_rq_info_mail_demandeur);
			if($ligne2 > 0) {
				mysql_data_seek($res_rq_info_mail_demandeur, 0);
				$tab_rq_info_mail_demandeur = mysql_fetch_assoc($res_rq_info_mail_demandeur);
			}
		}
		mysql_free_result($res_rq_info_mail_demandeur);
	}
}else{
	if($tab_rq_info_mail['CHANGEMENT_MAIL_LIB']!='demandeur@caissedesdepots.fr'){
		$MAIL_A .= ';'.$tab_rq_info_mail['CHANGEMENT_MAIL_LIB']; 
		//$a_mail->AddAddress($tab_rq_info_mail['CHANGEMENT_MAIL_LIB']);
		$NB_MAIL=$NB_MAIL+1;
	}else{
		$rq_info_mail_demandeur="
		SELECT DISTINCT(`EMAIL_FULL`) AS `EMAIL_FULL` 
		FROM `moteur_utilisateur` 
		WHERE `UTILISATEUR_ID` IN (
		SELECT DISTINCT(`MOTEUR_TRACE_UTILISATEUR_ID`) 
		FROM `moteur_trace` 
		WHERE `MOTEUR_TRACE_CATEGORIE`='Changement' 
		AND `MOTEUR_TRACE_ETAT` IN(
		SELECT `CHANGEMENT_STATUS_ID` 
		FROM `changement_status` 
		WHERE `CHANGEMENT_STATUS` IN('Brouillon','Inscrit') 
		AND `ENABLE`=0
		) 
		AND `MOTEUR_TRACE_REF_ID`='".$ID."'
		) 
		AND UPPER(`EMAIL_FULL`) NOT IN(
		SELECT DISTINCT(UPPER(`CHANGEMENT_MAIL_LIB`)) AS `CHANGEMENT_MAIL_LIB`
		FROM `changement_mail`
		WHERE `ENABLE`='0' 
		AND `CHANGEMENT_STATUS_ID` IN (
		SELECT `CHANGEMENT_STATUS_ID` 
		FROM `changement_liste` 
		WHERE `ENABLE`='0' AND `CHANGEMENT_LISTE_ID`='".$ID."' 
		)
		AND `CHANGEMENT_DEMANDE_ID` IN (
		SELECT `CHANGEMENT_DEMANDE_ID` 
		FROM `changement_liste` 
		WHERE `ENABLE`='0' AND `CHANGEMENT_LISTE_ID`='".$ID."'
		)
		)";
		$res_rq_info_mail_demandeur = mysql_query($rq_info_mail_demandeur, $mysql_link) or die(mysql_error());
		$tab_rq_info_mail_demandeur = mysql_fetch_assoc($res_rq_info_mail_demandeur);
		$total_ligne_rq_info_mail_demandeur=mysql_num_rows($res_rq_info_mail_demandeur);
		if($total_ligne_rq_info_mail_demandeur!=0){
			do {
				if($NB_MAIL==0){
					$MAIL_A .= $tab_rq_info_mail_demandeur['EMAIL_FULL']; 
					//$a_mail->AddAddress($tab_rq_info_mail_demandeur['EMAIL_FULL']);
					$NB_MAIL=$NB_MAIL+1;
				}else{
					$MAIL_A .= ';'.$tab_rq_info_mail_demandeur['EMAIL_FULL']; 
					//$a_mail->AddAddress($tab_rq_info_mail_demandeur['EMAIL_FULL']);
					$NB_MAIL=$NB_MAIL+1;
				}
			
			} while ($tab_rq_info_mail_demandeur = mysql_fetch_assoc($res_rq_info_mail_demandeur));
			$ligne2= mysql_num_rows($res_rq_info_mail_demandeur);
			if($ligne2 > 0) {
				mysql_data_seek($res_rq_info_mail_demandeur, 0);
				$tab_rq_info_mail_demandeur = mysql_fetch_assoc($res_rq_info_mail_demandeur);
			}
		}
		mysql_free_result($res_rq_info_mail_demandeur);
	}
}
			
			 } while ($tab_rq_info_mail = mysql_fetch_assoc($res_rq_info_mail));
		        $ligne= mysql_num_rows($res_rq_info_mail);
		        if($ligne > 0) {
		          mysql_data_seek($res_rq_info_mail, 0);
		          $tab_rq_info_mail = mysql_fetch_assoc($res_rq_info_mail);
		        }
		}	    
	  }else{
	  	//on envoie toujours en dev a $Personne_email_FULL
	  	//$MAIL_A .= $Personne_email_FULL; 
		if($total_ligne_rq_info_mail!=0){
			do {
if($NB_MAIL==0){
	if($tab_rq_info_mail['CHANGEMENT_MAIL_LIB']!='demandeur@caissedesdepots.fr'){
		$MAIL_A .= $tab_rq_info_mail['CHANGEMENT_MAIL_LIB']; 
		//$a_mail->AddAddress($tab_rq_info_mail['CHANGEMENT_MAIL_LIB']);
		$NB_MAIL=$NB_MAIL+1;
	}else{
		$rq_info_mail_demandeur="
		SELECT DISTINCT(`EMAIL_FULL`) AS `EMAIL_FULL` 
		FROM `moteur_utilisateur` 
		WHERE `UTILISATEUR_ID` IN (
		SELECT DISTINCT(`MOTEUR_TRACE_UTILISATEUR_ID`) 
		FROM `moteur_trace` 
		WHERE `MOTEUR_TRACE_CATEGORIE`='Changement' 
		AND `MOTEUR_TRACE_ETAT` IN(
		SELECT `CHANGEMENT_STATUS_ID` 
		FROM `changement_status` 
		WHERE `CHANGEMENT_STATUS` IN('Brouillon','Inscrit') 
		AND `ENABLE`=0
		) 
		AND `MOTEUR_TRACE_REF_ID`='".$ID."'
		) 
		AND UPPER(`EMAIL_FULL`) NOT IN(
		SELECT DISTINCT(UPPER(`CHANGEMENT_MAIL_LIB`)) AS `CHANGEMENT_MAIL_LIB`
		FROM `changement_mail`
		WHERE `ENABLE`='0' 
		AND `CHANGEMENT_STATUS_ID` IN (
		SELECT `CHANGEMENT_STATUS_ID` 
		FROM `changement_liste` 
		WHERE `ENABLE`='0' AND `CHANGEMENT_LISTE_ID`='".$ID."' 
		)
		AND `CHANGEMENT_DEMANDE_ID` IN (
		SELECT `CHANGEMENT_DEMANDE_ID` 
		FROM `changement_liste` 
		WHERE `ENABLE`='0' AND `CHANGEMENT_LISTE_ID`='".$ID."'
		)
		)";
		$res_rq_info_mail_demandeur = mysql_query($rq_info_mail_demandeur, $mysql_link) or die(mysql_error());
		$tab_rq_info_mail_demandeur = mysql_fetch_assoc($res_rq_info_mail_demandeur);
		$total_ligne_rq_info_mail_demandeur=mysql_num_rows($res_rq_info_mail_demandeur);
		if($total_ligne_rq_info_mail_demandeur!=0){
			do {
				if($NB_MAIL==0){
					$MAIL_A .= $tab_rq_info_mail_demandeur['EMAIL_FULL']; 
					//$a_mail->AddAddress($tab_rq_info_mail_demandeur['EMAIL_FULL']);
					$NB_MAIL=$NB_MAIL+1;
				}else{
					$MAIL_A .= ';'.$tab_rq_info_mail_demandeur['EMAIL_FULL']; 
					//$a_mail->AddAddress($tab_rq_info_mail_demandeur['EMAIL_FULL']);
					$NB_MAIL=$NB_MAIL+1;
				}
			
			} while ($tab_rq_info_mail_demandeur = mysql_fetch_assoc($res_rq_info_mail_demandeur));
			$ligne2= mysql_num_rows($res_rq_info_mail_demandeur);
			if($ligne2 > 0) {
				mysql_data_seek($res_rq_info_mail_demandeur, 0);
				$tab_rq_info_mail_demandeur = mysql_fetch_assoc($res_rq_info_mail_demandeur);
			}
		}
		mysql_free_result($res_rq_info_mail_demandeur);
	}
}else{
	if($tab_rq_info_mail['CHANGEMENT_MAIL_LIB']!='demandeur@caissedesdepots.fr'){
		$MAIL_A .= ';'.$tab_rq_info_mail['CHANGEMENT_MAIL_LIB']; 
		//$a_mail->AddAddress($tab_rq_info_mail['CHANGEMENT_MAIL_LIB']);
		$NB_MAIL=$NB_MAIL+1;
	}else{
		$rq_info_mail_demandeur="
		SELECT DISTINCT(`EMAIL_FULL`) AS `EMAIL_FULL` 
		FROM `moteur_utilisateur` 
		WHERE `UTILISATEUR_ID` IN (
		SELECT DISTINCT(`MOTEUR_TRACE_UTILISATEUR_ID`) 
		FROM `moteur_trace` 
		WHERE `MOTEUR_TRACE_CATEGORIE`='Changement' 
		AND `MOTEUR_TRACE_ETAT` IN(
		SELECT `CHANGEMENT_STATUS_ID` 
		FROM `changement_status` 
		WHERE `CHANGEMENT_STATUS` IN('Brouillon','Inscrit') 
		AND `ENABLE`=0
		) 
		AND `MOTEUR_TRACE_REF_ID`='".$ID."'
		) 
		AND UPPER(`EMAIL_FULL`) NOT IN(
		SELECT DISTINCT(UPPER(`CHANGEMENT_MAIL_LIB`)) AS `CHANGEMENT_MAIL_LIB`
		FROM `changement_mail`
		WHERE `ENABLE`='0' 
		AND `CHANGEMENT_STATUS_ID` IN (
		SELECT `CHANGEMENT_STATUS_ID` 
		FROM `changement_liste` 
		WHERE `ENABLE`='0' AND `CHANGEMENT_LISTE_ID`='".$ID."' 
		)
		AND `CHANGEMENT_DEMANDE_ID` IN (
		SELECT `CHANGEMENT_DEMANDE_ID` 
		FROM `changement_liste` 
		WHERE `ENABLE`='0' AND `CHANGEMENT_LISTE_ID`='".$ID."'
		)
		)";
		$res_rq_info_mail_demandeur = mysql_query($rq_info_mail_demandeur, $mysql_link) or die(mysql_error());
		$tab_rq_info_mail_demandeur = mysql_fetch_assoc($res_rq_info_mail_demandeur);
		$total_ligne_rq_info_mail_demandeur=mysql_num_rows($res_rq_info_mail_demandeur);
		if($total_ligne_rq_info_mail_demandeur!=0){
			do {
				if($NB_MAIL==0){
					$MAIL_A .= $tab_rq_info_mail_demandeur['EMAIL_FULL']; 
					//$a_mail->AddAddress($tab_rq_info_mail_demandeur['EMAIL_FULL']);
					$NB_MAIL=$NB_MAIL+1;
				}else{
					$MAIL_A .= ';'.$tab_rq_info_mail_demandeur['EMAIL_FULL']; 
					//$a_mail->AddAddress($tab_rq_info_mail_demandeur['EMAIL_FULL']);
					$NB_MAIL=$NB_MAIL+1;
				}
			
			} while ($tab_rq_info_mail_demandeur = mysql_fetch_assoc($res_rq_info_mail_demandeur));
			$ligne2= mysql_num_rows($res_rq_info_mail_demandeur);
			if($ligne2 > 0) {
				mysql_data_seek($res_rq_info_mail_demandeur, 0);
				$tab_rq_info_mail_demandeur = mysql_fetch_assoc($res_rq_info_mail_demandeur);
			}
		}
		mysql_free_result($res_rq_info_mail_demandeur);
	}
}
			
			 } while ($tab_rq_info_mail = mysql_fetch_assoc($res_rq_info_mail));
		        $ligne= mysql_num_rows($res_rq_info_mail);
		        if($ligne > 0) {
		          mysql_data_seek($res_rq_info_mail, 0);
		          $tab_rq_info_mail = mysql_fetch_assoc($res_rq_info_mail);
		        }
		}
		
	  }
	  mysql_free_result($res_rq_info_mail);



	 

	$message  = '';
	$message .='<html>'."\n";
	$message .=header_mess();
	$message .= '<body background="cid:texture_blanc.gif">'."\n";
	$message .= '<div id="body">';
	$message .= '<br>'."\n";
	$message .= 'De : '.$Personne_complet.'<br>'."\n";
	$message .= 'A : '.$MAIL_A.'<br>'."\n";
	$message .= 'Objet : '.$objet.'<br>'."\n";
	$message .= '<br>'."\n";
	$rq_info="
	SELECT 
	`changement_liste`.`CHANGEMENT_LISTE_ID`, 
	`changement_liste`.`CHANGEMENT_LISTE_UTILISATEUR_ID`, 
	`changement_liste`.`CHANGEMENT_LISTE_DATE_DEBUT`, 
	`changement_liste`.`CHANGEMENT_LISTE_DATE_FIN`, 
	`changement_liste`.`CHANGEMENT_LISTE_HEURE_DEBUT`, 
	`changement_liste`.`CHANGEMENT_LISTE_HEURE_FIN`, 
	`changement_liste`.`CHANGEMENT_LISTE_DATE_MODIFICATION`, 
	`changement_liste`.`CHANGEMENT_LISTE_LIB`, 
	`changement_liste`.`CHANGEMENT_STATUS_ID`, 
	`changement_status`.`CHANGEMENT_STATUS`,
	`changement_liste`.`ENABLE` 
	FROM `changement_liste`,`changement_status` 
	WHERE `changement_liste`.`CHANGEMENT_STATUS_ID`=`changement_status`.`CHANGEMENT_STATUS_ID`
	AND `changement_liste`.`CHANGEMENT_LISTE_ID` ='".$ID."'
	AND `changement_liste`.`ENABLE` ='0'
	LIMIT 1";
	$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
	$tab_rq_info = mysql_fetch_assoc($res_rq_info);
	$total_ligne_rq_info=mysql_num_rows($res_rq_info);
	if($total_ligne_rq_info!=0){
		$CHANGEMENT_ID=$tab_rq_info['CHANGEMENT_LISTE_ID'];
		$ID=$tab_rq_info['CHANGEMENT_LISTE_ID'];
		$CHANGEMENT_STATUS=$tab_rq_info['CHANGEMENT_STATUS'];
		$CHANGEMENT_LISTE_DATE_DEBUT=$tab_rq_info['CHANGEMENT_LISTE_DATE_DEBUT'];
		$JOUR_Inter_Debut=substr($CHANGEMENT_LISTE_DATE_DEBUT,6,2);
		$MOIS_Inter_Debut=substr($CHANGEMENT_LISTE_DATE_DEBUT,4,2);
		$ANNEE_Inter_Debut=substr($CHANGEMENT_LISTE_DATE_DEBUT,0,4);
		$Date_Inter_Debut=$JOUR_Inter_Debut.'/'.$MOIS_Inter_Debut.'/'.$ANNEE_Inter_Debut;
		
		$CHANGEMENT_LISTE_DATE_FIN=$tab_rq_info['CHANGEMENT_LISTE_DATE_FIN'];
		$JOUR_Inter_Fin=substr($CHANGEMENT_LISTE_DATE_FIN,6,2);
		$MOIS_Inter_Fin=substr($CHANGEMENT_LISTE_DATE_FIN,4,2);
		$ANNEE_Inter_Fin=substr($CHANGEMENT_LISTE_DATE_FIN,0,4);
		$Date_Inter_Fin=$JOUR_Inter_Fin.'/'.$MOIS_Inter_Fin.'/'.$ANNEE_Inter_Fin;
		
		$CHANGEMENT_LISTE_HEURE_DEBUT=$tab_rq_info['CHANGEMENT_LISTE_HEURE_DEBUT'];
		$HEURE_PLANIF_DEBUT_H=substr($CHANGEMENT_LISTE_HEURE_DEBUT,0,2);
		$HEURE_PLANIF_DEBUT_M=substr($CHANGEMENT_LISTE_HEURE_DEBUT,3,2);
		
		$CHANGEMENT_LISTE_HEURE_FIN=$tab_rq_info['CHANGEMENT_LISTE_HEURE_FIN'];
		$HEURE_PLANIF_FIN_H=substr($CHANGEMENT_LISTE_HEURE_FIN,0,2);
		$HEURE_PLANIF_FIN_M=substr($CHANGEMENT_LISTE_HEURE_FIN,3,2);
		
		$lib=$tab_rq_info['CHANGEMENT_LISTE_LIB'];
	
	}
	mysql_free_result($res_rq_info);

	$message .= '<table>'."\n";
	$message .= '<tr align="center" >'."\n";
	$message .= '<td colspan="4"><b>&nbsp;Information sur le changement <a href="http://'.$_SERVER["HTTP_HOST"].''.$_SERVER["SCRIPT_NAME"].'?ITEM=changement_Info_Changement&amp;action=Info&amp;ID='.$ID.'" >'.$ID.'</a>&nbsp;</b></td>'."\n";
	$message .= '</tr>'."\n";
	$message .= '<tr>'."\n";
	$message .= '<td align="left">&nbsp;Date de D&eacute;but&nbsp;</td>'."\n";
	$message .= '<td align="left">&nbsp;'.$Date_Inter_Debut.'&nbsp;</td>'."\n";
	$message .= '<td align="left">&nbsp;Heure de D&eacute;but&nbsp;</td>'."\n";
	$message .= '<td align="left">&nbsp;'.$HEURE_PLANIF_DEBUT_H.'h'.$HEURE_PLANIF_DEBUT_M.'&nbsp;</td>'."\n";
	$message .= '</tr>'."\n";
	$message .= '<tr>'."\n";
	$message .= '<td align="left">&nbsp;Date de fin&nbsp;</td>'."\n";
	$message .= '<td align="left">&nbsp;'.$Date_Inter_Fin.'&nbsp;</td>'."\n";
	$message .= '<td align="left">&nbsp;Heure de Fin&nbsp;</td>'."\n";
	$message .= '<td align="left">&nbsp;'.$HEURE_PLANIF_FIN_H.'h'.$HEURE_PLANIF_FIN_M.'</td>'."\n";
	$message .= '</tr>'."\n";
	$message .= '<tr>'."\n";
	$message .= '<td align="left">&nbsp;Titre du Changement&nbsp;</td>'."\n";
	$message .= '<td align="left" colspan="3">&nbsp;'.stripslashes($lib).'&nbsp;</td>'."\n";
	$message .= '</tr>'."\n";
	$message .= '<tr>'."\n";
	$message .= '<td align="left">&nbsp;Status&nbsp;</td>'."\n";
	$message .= '<td align="left" colspan="3">&nbsp;'.$CHANGEMENT_STATUS.'&nbsp;</td>'."\n";
	$message .= '</tr>'."\n";
	$message .= '</table>'."\n";
	$message .= '<br><br>'."\n";
	$message .='<div id="signature">'."\n";
	$message .='  <b>'.$Personne_complet.'</b><br>'."\n";
	$message .='  Gestion des changements<br>'."\n";
	$message .='  Informatique CDC - Etablissement DPI<br>'."\n";
	$message .='</div>   '."\n";	
	$message .='</div>   '."\n";  
	$message .= '</body></html>' ."\n";
	$message =stripslashes($message);
	return $message;
}


////fin mail///
?>