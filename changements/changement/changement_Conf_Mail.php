<?PHP
$INCLUDE_DIR = "./lib/phpmailer/";
require_once($INCLUDE_DIR . "class.phpmailer.php");
require_once($INCLUDE_DIR . 'language/phpmailer.lang-en.php') ; 
 
////mail///
function sauvegarde_MailInfo( $MAIL_DEST, $corps, $CHANGEMENT_ID, $type_mail,$type,$UTILISATEUR_ID,$ENV,$mysql_link)
{
	$ID=$CHANGEMENT_ID;
	$CHANGEMENT_STATUS_ID=$type;
	$var_date_svg=date("YmdHis");
	$DATE_MODIFICATION=date("d/m/Y H:i:s");
	$type=str_replace('&eacute;', 'e', $type);
	$fichier=$var_date_svg.'-'.$CHANGEMENT_ID.'-'.$type.'.html';
	$corps = str_replace('cid:','../Images/', $corps);
	
	Export_to_fichier('./old_changement/',$fichier, $corps, 'w+');
	
	$sql = "INSERT INTO `changement_mail_trace` (`CHANGEMENT_MAIL_TRACE_ID` ,`CHANGEMENT_MAIL_TRACE_UTILISATEUR_ID` ,`CHANGEMENT_MAIL_TRACE_DATE` ,`CHANGEMENT_MAIL_TRACE_TYPE` ,`CHANGEMENT_MAIL_TRACE_CHANGEMENT_ID` ,`CHANGEMENT_MAIL_TRACE_DEST` ,`CHANGEMENT_MAIL_TRACE_ARCHIVE` ) VALUES (NULL , '".$UTILISATEUR_ID."', '".$DATE_MODIFICATION."', '".$type."', '".$CHANGEMENT_ID."', '".$MAIL_DEST."', '".$fichier."');";
	mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
	$TABLE_SQL_SQL='changement_mail_trace';       
	historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');   
	
	$sql = "OPTIMIZE TABLE `changement_mail_trace`";
	mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
	$TABLE_SQL_SQL='changement_mail_trace';       
	historique_sql_new($sql,$TABLE_SQL_SQL,'OPTIMIZE'); 
	
	$TRACE_ETAT=$CHANGEMENT_STATUS_ID;
	$TRACE_CATEGORIE='Changement';
	$TRACE_TABLE='changement_mail_trace';
	$TRACE_REF_ID=$ID;
	$TRACE_ACTION=$type_mail;
	moteur_trace($TRACE_CATEGORIE,$TRACE_TABLE,$TRACE_REF_ID,$TRACE_ACTION,$TRACE_ETAT);
}

function mailMailInfo_php($MAIL_DEST,$CHANGEMENT_ID,$Personne_email_FULL, $redac_court, $objet, $corps, $type, $ENV,$mysql_link)
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
	$MAIL = explode(";", $MAIL_DEST);
	$NB_TOTAL_MAIL=count($MAIL);
	for($NB_MAIL=0;$NB_MAIL < $NB_TOTAL_MAIL;$NB_MAIL++)
	{
		$a_mail->AddAddress($MAIL[$NB_MAIL]);
	}
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

function Constitue_corps_Message($MAIL_DEST,$MAIL_OBJET,$MAIL_COMMENTAIRE,$message_debut,$message_FAR,$message_RESSOUCES,$message_BILAN,$message_CR,$message_signature)
{
	$message  = '';
	$message .='<html>'."\n";
	$message .=header_mess();
	$message .= '<body background="cid:texture_blanc.gif">'."\n";
	$message .= '<div id="body">';
	$message .= '<br>'."\n";
	$message .=$message_debut;	
	$message .=$message_FAR;
	$message .=$message_RESSOUCES;
	$message .=$message_BILAN; 
	$message .=$message_CR; 
	$message .=$MAIL_COMMENTAIRE; 
	$message .=$message_signature; 
	$message .= '</body></html>' ."\n";
	$message =stripslashes($message);
	return $message;
}

function Constitue_corps_Message_SVG($Personne_complet,$MAIL_DEST,$MAIL_OBJET,$MAIL_COMMENTAIRE,$message_debut,$message_FAR,$message_RESSOUCES,$message_BILAN,$message_CR,$message_signature)
{
	$MAIL_A='';
	$MAIL = explode(";", $MAIL_DEST);
	$NB_TOTAL_MAIL=count($MAIL);
	for($NB_MAIL=0;$NB_MAIL < $NB_TOTAL_MAIL;$NB_MAIL++)
	{
		$MAIL_A.=$MAIL[$NB_MAIL].'<br>';
	}
	$message  = '';
	$message .='<html>'."\n";
	$message .=header_mess();
	$message .= '<body background="cid:texture_blanc.gif">'."\n";
	$message .= '<div id="body">';
	$message .= '<br>'."\n";
	$message .= 'De : '.$Personne_complet.'<br>'."\n";
	$message .= 'A : '.$MAIL_A.'<br>'."\n";
	$message .= 'Objet : '.$MAIL_OBJET.'<br>'."\n";
	$message .= '<br>'."\n";
	$message .=$message_debut;	
	$message .=$message_FAR;
	$message .=$message_RESSOUCES;
	$message .=$message_BILAN; 
	$message .=$message_CR; 
	$message .=$MAIL_COMMENTAIRE; 
	$message .=$message_signature; 
	$message .= '</body></html>' ."\n";
	$message =stripslashes($message);
	return $message;
}


////fin mail///
?>