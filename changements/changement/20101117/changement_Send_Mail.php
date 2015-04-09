<?php
//redirection si acces dirrect
if(substr_count($_SERVER['PHP_SELF'], 'index.php')==0){
  header("Location: ../");
  exit();
}
$j=0;
require("./cf/conf_outil_icdc.php"); 
require_once("./cf/fonctions.php");
require_once("./cf/autre_fonctions.php");
 
require_once('./changement/changement_Conf_Mail.php') ; 

$ENV =substr($_SERVER["SCRIPT_FILENAME"], 1, 1);
//$ENV='x';
?>
<script language="javascript" type="text/javascript" src="lib/tiny_mce/tiny_mce.js"></script>
<script language="javascript" type="text/javascript">
tinyMCE.init({
mode : "textareas",
theme : "simple"
});
</script>
<?PHP
if(isset($_GET['type'])){
  $type=$_GET['type'];
}else{
  $type='vide';
}
if(isset($_GET['action'])){
  $action=$_GET['action'];
}else{
  $action='Creation';
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
    $_GET['ID']=$ID;
  }
}
if(isset($tab_var['type'])){
    $type=$tab_var['type'];
    $_GET['type']=$type;
}
if(isset($tab_var['action'])){
    $action=$tab_var['action'];
    $_GET['action']=$action;
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

$MAIL_DEST='';
$MAIL_OBJET='';
$MAIL_COMMENTAIRE='';

list ($Personne_abrege,$Personne_complet,$Personne_email,$Personne_email_FULL)=Recherche_SQL_nom_FULL($UTILISATEUR_ID);

$rq_far_info="
SELECT `CHANGEMENT_STATUS_ID`
FROM `changement_liste`
WHERE 
`CHANGEMENT_LISTE_ID` ='".$ID."' AND
`changement_liste`.`ENABLE` = '0'
";
$res_rq_far_info = mysql_query($rq_far_info, $mysql_link) or die(mysql_error());
$tab_rq_far_info = mysql_fetch_assoc($res_rq_far_info);
$total_ligne_rq_far_info=mysql_num_rows($res_rq_far_info);
$TRACE_ETAT=$tab_rq_far_info['CHANGEMENT_STATUS_ID'];
mysql_free_result($res_rq_far_info);


/// Creation de l objet du mail
$objet='';
$FAR_info='non';
$Fiche_Bilan_info='non';
if ( $ENV != "x" )
{	
  $objet='[dev]';
}	

$SQL_CHANGEMENT_STATUS="SELECT `CHANGEMENT_STATUS_ID` FROM `changement_liste` WHERE `ENABLE`='0' AND `CHANGEMENT_LISTE_ID`='".$ID."'";

switch ($type)
{
  case "terminer": 
    $objet.='-=Gestion des changements=- La demande n '.$ID.' est terminee.';
    $suivi_info='oui';
    $changement_info='oui';
    $FAR_info='non';
    $Fiche_Bilan_info='non';
  break;
  case "cloture": 
    $objet.='-=Gestion des changements=- Cloture de la demande n '.$ID.'.';
    $suivi_info='oui';
    $changement_info='oui';
    $FAR_info='oui';
    $Fiche_Bilan_info='oui';
  break;
  case "inscription": 
    $objet.='-=Gestion des changements=- Inscription de la demande n '.$ID.'.';
    $suivi_info='non';
    $changement_info='oui';
    $FAR_info='non';
    $Fiche_Bilan_info='non';
  break;
  case "abandon": 
    $objet.='-=Gestion des changements=- Abandon de la demande n '.$ID.'.';
    $suivi_info='oui';
    $changement_info='oui';
    $FAR_info='non';
    $Fiche_Bilan_info='non';
  break;
  case "ReInscription": 
    $objet.='-=Gestion des changements=- ReInscription de la demande n '.$ID.'.';
    $suivi_info='oui';
    $changement_info='oui';
    $FAR_info='non';
    $Fiche_Bilan_info='non';
    $SQL_CHANGEMENT_STATUS="SELECT `CHANGEMENT_STATUS_ID` FROM `changement_status` WHERE `CHANGEMENT_STATUS` = 'ReInscription'";
  break;
  case "brouillon": 
    $objet.='-=Gestion des changements=- remise en Brouillon de la demande n '.$ID.'.';
    $suivi_info='oui';
    $changement_info='oui';
    $FAR_info='non';
    $Fiche_Bilan_info='non';
  break;
  case "validation": 
    $objet.='-=Gestion des changements=- Validation de la demande n '.$ID.'.';
    $suivi_info='oui';
    $changement_info='oui';
    $FAR_info='non';
    $Fiche_Bilan_info='non';
  break;
  default:
    $objet.='-=Gestion des changements=- Mail d information sur la demande n '.$ID.'.';
    $suivi_info='oui';
    $changement_info='oui';
    $FAR_info='non';
    $Fiche_Bilan_info='non';
  break;
}
$MAIL_OBJET=$objet;

/// Creation de la liste des destinataires

	$MAIL_A ='';
	$NB_MAIL=0;
	$rq_info_mail="
	SELECT DISTINCT(`CHANGEMENT_MAIL_LIB`) AS `CHANGEMENT_MAIL_LIB`
	FROM `changement_mail`
	WHERE `ENABLE`='0' 
	AND `CHANGEMENT_STATUS_ID` IN (".$SQL_CHANGEMENT_STATUS.")
	AND `CHANGEMENT_DEMANDE_ID` IN (
	SELECT `CHANGEMENT_DEMANDE_ID` 
	FROM `changement_liste` 
	WHERE `ENABLE`='0' AND `CHANGEMENT_LISTE_ID`='".$ID."'
	)
	;";
	$res_rq_info_mail = mysql_query($rq_info_mail, $mysql_link) or die(mysql_error());
	$tab_rq_info_mail = mysql_fetch_assoc($res_rq_info_mail);
	$total_ligne_rq_info_mail=mysql_num_rows($res_rq_info_mail);
	  if ( $ENV == "x" )
	  {	 	
		if($total_ligne_rq_info_mail!=0){
			do {

switch ($tab_rq_info_mail['CHANGEMENT_MAIL_LIB'])
{
  case "utilisateur@caissedesdepots.fr": 
    $rq_info_mail_demandeur="
		SELECT DISTINCT(`EMAIL_FULL`) AS `EMAIL_FULL` 
		FROM `moteur_utilisateur` 
		WHERE 
		UPPER(`EMAIL_FULL`)=UPPER('".$Personne_email_FULL."')
		;";
		$res_rq_info_mail_demandeur = mysql_query($rq_info_mail_demandeur, $mysql_link) or die(mysql_error());
		$tab_rq_info_mail_demandeur = mysql_fetch_assoc($res_rq_info_mail_demandeur);
		$total_ligne_rq_info_mail_demandeur=mysql_num_rows($res_rq_info_mail_demandeur);
		if($total_ligne_rq_info_mail_demandeur!=0){
			do {
        $temp_mail=strtoupper($tab_rq_info_mail_demandeur['EMAIL_FULL']);
        if(!isset($MAIL_CONFIG[$temp_mail])){$MAIL_CONFIG[$temp_mail]=1;}else{$MAIL_CONFIG[$temp_mail]=$MAIL_CONFIG[$temp_mail]+1;}
        if($MAIL_CONFIG[$temp_mail]==1){
          if($NB_MAIL==0){
            $MAIL_A .= $tab_rq_info_mail_demandeur['EMAIL_FULL']; 
            $NB_MAIL=$NB_MAIL+1;
          }else{
            $MAIL_A .= ';'.$tab_rq_info_mail_demandeur['EMAIL_FULL']; 
            $NB_MAIL=$NB_MAIL+1;
          }
				}
			
			} while ($tab_rq_info_mail_demandeur = mysql_fetch_assoc($res_rq_info_mail_demandeur));
			$ligne2= mysql_num_rows($res_rq_info_mail_demandeur);
			if($ligne2 > 0) {
				mysql_data_seek($res_rq_info_mail_demandeur, 0);
				$tab_rq_info_mail_demandeur = mysql_fetch_assoc($res_rq_info_mail_demandeur);
			}
		}
		mysql_free_result($res_rq_info_mail_demandeur);
  
  break;
  case "demandeur@caissedesdepots.fr": 
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
		WHERE `CHANGEMENT_STATUS` IN('Brouillon','Inscrit','ReInscrition') 
		AND `ENABLE`=0
		) 
		AND `MOTEUR_TRACE_REF_ID`='".$ID."'
		) 
		;";
		$res_rq_info_mail_demandeur = mysql_query($rq_info_mail_demandeur, $mysql_link) or die(mysql_error());
		$tab_rq_info_mail_demandeur = mysql_fetch_assoc($res_rq_info_mail_demandeur);
		$total_ligne_rq_info_mail_demandeur=mysql_num_rows($res_rq_info_mail_demandeur);
		if($total_ligne_rq_info_mail_demandeur!=0){
			do {
				$temp_mail=strtoupper($tab_rq_info_mail_demandeur['EMAIL_FULL']);
        if(!isset($MAIL_CONFIG[$temp_mail])){$MAIL_CONFIG[$temp_mail]=1;}else{$MAIL_CONFIG[$temp_mail]=$MAIL_CONFIG[$temp_mail]+1;}
        if($MAIL_CONFIG[$temp_mail]==1){
          if($NB_MAIL==0){
            $MAIL_A .= $tab_rq_info_mail_demandeur['EMAIL_FULL']; 
            $NB_MAIL=$NB_MAIL+1;
          }else{
            $MAIL_A .= ';'.$tab_rq_info_mail_demandeur['EMAIL_FULL']; 
            $NB_MAIL=$NB_MAIL+1;
          }
				}
			} while ($tab_rq_info_mail_demandeur = mysql_fetch_assoc($res_rq_info_mail_demandeur));
			$ligne2= mysql_num_rows($res_rq_info_mail_demandeur);
			if($ligne2 > 0) {
				mysql_data_seek($res_rq_info_mail_demandeur, 0);
				$tab_rq_info_mail_demandeur = mysql_fetch_assoc($res_rq_info_mail_demandeur);
			}
		}
		mysql_free_result($res_rq_info_mail_demandeur);
  
  break;
  default:
    $temp_mail=strtoupper($tab_rq_info_mail['CHANGEMENT_MAIL_LIB']);
    if(!isset($MAIL_CONFIG[$temp_mail])){$MAIL_CONFIG[$temp_mail]=1;}else{$MAIL_CONFIG[$temp_mail]=$MAIL_CONFIG[$temp_mail]+1;}
    if($MAIL_CONFIG[$temp_mail]==1){
      if($NB_MAIL==0){
        $MAIL_A .= $tab_rq_info_mail['CHANGEMENT_MAIL_LIB']; 
        $NB_MAIL=$NB_MAIL+1;
      }else{
        $MAIL_A .= ';'.$tab_rq_info_mail['CHANGEMENT_MAIL_LIB']; 
        $NB_MAIL=$NB_MAIL+1;
      }
    }
  break;
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
	  	/*
	  	$MAIL_A = $Personne_email_FULL; 
	  	$NB_MAIL=$NB_MAIL+1;
	  	*/
// pour les tests ////	  	
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if($total_ligne_rq_info_mail!=0){
			do {

switch ($tab_rq_info_mail['CHANGEMENT_MAIL_LIB'])
{
  case "utilisateur@caissedesdepots.fr": 
    $rq_info_mail_demandeur="
		SELECT DISTINCT(`EMAIL_FULL`) AS `EMAIL_FULL` 
		FROM `moteur_utilisateur` 
		WHERE 
		UPPER(`EMAIL_FULL`)=UPPER('".$Personne_email_FULL."')
		;";
		$res_rq_info_mail_demandeur = mysql_query($rq_info_mail_demandeur, $mysql_link) or die(mysql_error());
		$tab_rq_info_mail_demandeur = mysql_fetch_assoc($res_rq_info_mail_demandeur);
		$total_ligne_rq_info_mail_demandeur=mysql_num_rows($res_rq_info_mail_demandeur);
		if($total_ligne_rq_info_mail_demandeur!=0){
			do {
        $temp_mail=strtoupper($tab_rq_info_mail_demandeur['EMAIL_FULL']);
        if(!isset($MAIL_CONFIG[$temp_mail])){$MAIL_CONFIG[$temp_mail]=1;}else{$MAIL_CONFIG[$temp_mail]=$MAIL_CONFIG[$temp_mail]+1;}
        if($MAIL_CONFIG[$temp_mail]==1){
				if($NB_MAIL==0){
					$MAIL_A .= $tab_rq_info_mail_demandeur['EMAIL_FULL']; 
					$NB_MAIL=$NB_MAIL+1;
				}else{
					$MAIL_A .= ';'.$tab_rq_info_mail_demandeur['EMAIL_FULL']; 
					$NB_MAIL=$NB_MAIL+1;
				}
				}
			
			} while ($tab_rq_info_mail_demandeur = mysql_fetch_assoc($res_rq_info_mail_demandeur));
			$ligne2= mysql_num_rows($res_rq_info_mail_demandeur);
			if($ligne2 > 0) {
				mysql_data_seek($res_rq_info_mail_demandeur, 0);
				$tab_rq_info_mail_demandeur = mysql_fetch_assoc($res_rq_info_mail_demandeur);
			}
		}
		mysql_free_result($res_rq_info_mail_demandeur);
  
  break;
  case "demandeur@caissedesdepots.fr": 
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
		WHERE `CHANGEMENT_STATUS` IN('Brouillon','Inscrit','ReInscrition') 
		AND `ENABLE`=0
		) 
		AND `MOTEUR_TRACE_REF_ID`='".$ID."'
		) 
		;";
		$res_rq_info_mail_demandeur = mysql_query($rq_info_mail_demandeur, $mysql_link) or die(mysql_error());
		$tab_rq_info_mail_demandeur = mysql_fetch_assoc($res_rq_info_mail_demandeur);
		$total_ligne_rq_info_mail_demandeur=mysql_num_rows($res_rq_info_mail_demandeur);
		if($total_ligne_rq_info_mail_demandeur!=0){
			do {
				$temp_mail=strtoupper($tab_rq_info_mail_demandeur['EMAIL_FULL']);
        if(!isset($MAIL_CONFIG[$temp_mail])){$MAIL_CONFIG[$temp_mail]=1;}else{$MAIL_CONFIG[$temp_mail]=$MAIL_CONFIG[$temp_mail]+1;}
        if($MAIL_CONFIG[$temp_mail]==1){
				if($NB_MAIL==0){
					$MAIL_A .= $tab_rq_info_mail_demandeur['EMAIL_FULL']; 
					$NB_MAIL=$NB_MAIL+1;
				}else{
					$MAIL_A .= ';'.$tab_rq_info_mail_demandeur['EMAIL_FULL']; 
					$NB_MAIL=$NB_MAIL+1;
				}
				}
			} while ($tab_rq_info_mail_demandeur = mysql_fetch_assoc($res_rq_info_mail_demandeur));
			$ligne2= mysql_num_rows($res_rq_info_mail_demandeur);
			if($ligne2 > 0) {
				mysql_data_seek($res_rq_info_mail_demandeur, 0);
				$tab_rq_info_mail_demandeur = mysql_fetch_assoc($res_rq_info_mail_demandeur);
			}
		}
		mysql_free_result($res_rq_info_mail_demandeur);
  
  break;
  default:
    $temp_mail=strtoupper($tab_rq_info_mail['CHANGEMENT_MAIL_LIB']);
    if(!isset($MAIL_CONFIG[$temp_mail])){$MAIL_CONFIG[$temp_mail]=1;}else{$MAIL_CONFIG[$temp_mail]=$MAIL_CONFIG[$temp_mail]+1;}
      if($MAIL_CONFIG[$temp_mail]==1){
      if($NB_MAIL==0){
        $MAIL_A .= $tab_rq_info_mail['CHANGEMENT_MAIL_LIB']; 
        $NB_MAIL=$NB_MAIL+1;
      }else{
        $MAIL_A .= ';'.$tab_rq_info_mail['CHANGEMENT_MAIL_LIB']; 
        $NB_MAIL=$NB_MAIL+1;
      }
    }
  break;
}
			 } while ($tab_rq_info_mail = mysql_fetch_assoc($res_rq_info_mail));
		        $ligne= mysql_num_rows($res_rq_info_mail);
		        if($ligne > 0) {
		          mysql_data_seek($res_rq_info_mail, 0);
		          $tab_rq_info_mail = mysql_fetch_assoc($res_rq_info_mail);
		        }
		}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  	
	  }
	  mysql_free_result($res_rq_info_mail);

$MAIL_DEST=$MAIL_A;

/// Creation du contenu du message

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
	`changement_status`.`CHANGEMENT_STATUS_COULEUR_FOND`,
	`changement_status`.`CHANGEMENT_STATUS_COULEUR_TEXT`,
	`changement_liste`.`CHANGEMENT_DEMANDE_ID`, 
	`changement_demande`.`CHANGEMENT_DEMANDE_LIB`,
	`changement_demande`.`CHANGEMENT_DEMANDE_COULEUR_FOND`,
	`changement_demande`.`CHANGEMENT_DEMANDE_COULEUR_TEXTE`,
	`changement_liste`.`ENABLE` 
	FROM `changement_liste`,`changement_status`,`changement_demande`
	WHERE `changement_liste`.`CHANGEMENT_STATUS_ID`=`changement_status`.`CHANGEMENT_STATUS_ID`
	AND `changement_liste`.`CHANGEMENT_DEMANDE_ID`=`changement_demande`.`CHANGEMENT_DEMANDE_ID`
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
		$CHANGEMENT_DEMANDE_LIB=$tab_rq_info['CHANGEMENT_DEMANDE_LIB'];
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
		$CHANGEMENT_STATUS_COULEUR_FOND=$tab_rq_info['CHANGEMENT_STATUS_COULEUR_FOND'];
		$CHANGEMENT_STATUS_COULEUR_TEXT=$tab_rq_info['CHANGEMENT_STATUS_COULEUR_TEXT'];
		$CHANGEMENT_DEMANDE_COULEUR_FOND=$tab_rq_info['CHANGEMENT_DEMANDE_COULEUR_FOND'];
		$CHANGEMENT_DEMANDE_COULEUR_TEXTE=$tab_rq_info['CHANGEMENT_DEMANDE_COULEUR_TEXTE'];
	
	}
	mysql_free_result($res_rq_info);
	$message_debut  = '';
	$message_debut .= '<table>'."\n";
	$message_debut .= '<tr align="center" >'."\n";
	$message_debut .= '<td colspan="4" align="center"><b>&nbsp;Information sur le changement <a href="http://'.$_SERVER["HTTP_HOST"].''.$_SERVER["SCRIPT_NAME"].'?ITEM=changement_Info_Changement&amp;action=Info&amp;ID='.$ID.'" >'.$ID.'</a>&nbsp;</b></td>'."\n";
	$message_debut .= '</tr>'."\n";
	if($suivi_info=='oui'){
$message_debut .='<tr>'."\n";
$message_debut .='<td align="center" colspan="4">'."\n";
     $rq_info_liste_status="
     SELECT `CHANGEMENT_STATUS_ID`, `CHANGEMENT_STATUS` 
     FROM `changement_status`
     WHERE `ENABLE`='0' AND `CHANGEMENT_STATUS` NOT IN ('Abandonn&eacute;','ReInscription')
     ORDER BY `CHANGEMENT_STATUS_ORDRE`,`CHANGEMENT_STATUS`";
    $res_rq_info_liste_status = mysql_query($rq_info_liste_status, $mysql_link) or die(mysql_error());
    $tab_rq_info_liste_status = mysql_fetch_assoc($res_rq_info_liste_status);
    $total_ligne_rq_info_liste_status=mysql_num_rows($res_rq_info_liste_status);
    if($total_ligne_rq_info_liste_status!=0){
$message_debut .='<table>'."\n";
$message_debut .='<tr>'."\n";
    $LISTE_CHANGEMENT_STATUS_COULEUR_FOND='20EA01';
    $LISTE_CHANGEMENT_STATUS_COULEUR_TEXT='000000';
      $NB_LISTE_CHANGEMENT_STATUS_ID=0;
      $LISTE_CHANGEMENT_STATUS_ID_STOP=0;
      do {
      $LISTE_CHANGEMENT_STATUS_ID=$tab_rq_info_liste_status['CHANGEMENT_STATUS_ID'];
      $LISTE_CHANGEMENT_STATUS=$tab_rq_info_liste_status['CHANGEMENT_STATUS'];
      
      if($CHANGEMENT_STATUS=='Abandonn&eacute;'){
        $LISTE_CHANGEMENT_STATUS_COULEUR_FOND='FE0000';
        $LISTE_CHANGEMENT_STATUS_COULEUR_TEXT='FFFFFF';
      }else{
        if($LISTE_CHANGEMENT_STATUS_ID_STOP==1){
          $LISTE_CHANGEMENT_STATUS_COULEUR_FOND='F8F9F8';
          $LISTE_CHANGEMENT_STATUS_COULEUR_TEXT='000000';
        }else{
          $LISTE_CHANGEMENT_STATUS_COULEUR_FOND='20EA01';
          $LISTE_CHANGEMENT_STATUS_COULEUR_TEXT='000000';
        }
      }

      if($NB_LISTE_CHANGEMENT_STATUS_ID==0){
$message_debut .='<td bgcolor="#'.$LISTE_CHANGEMENT_STATUS_COULEUR_FOND.'"><FONT COLOR="#'.$LISTE_CHANGEMENT_STATUS_COULEUR_TEXT.'">&nbsp;'.$LISTE_CHANGEMENT_STATUS.'&nbsp;</FONT></td>'."\n";
      }else{
$message_debut .='<td bgcolor="#'.$LISTE_CHANGEMENT_STATUS_COULEUR_FOND.'"><FONT COLOR="#'.$LISTE_CHANGEMENT_STATUS_COULEUR_TEXT.'">&nbsp;->&nbsp;'.$LISTE_CHANGEMENT_STATUS.'&nbsp;</FONT></td>'."\n";
      }
      if($CHANGEMENT_STATUS==$LISTE_CHANGEMENT_STATUS){
        $LISTE_CHANGEMENT_STATUS_ID_STOP=1;
      }
      $NB_LISTE_CHANGEMENT_STATUS_ID=$NB_LISTE_CHANGEMENT_STATUS_ID+1;
       } while ($tab_rq_info_liste_status = mysql_fetch_assoc($res_rq_info_liste_status));
            $ligne= mysql_num_rows($res_rq_info_liste_status);
            if($ligne > 0) {
              mysql_data_seek($res_rq_info_liste_status, 0);
              $tab_rq_info_liste_status = mysql_fetch_assoc($res_rq_info_liste_status);
            }
$message_debut .='</tr>'."\n";
$message_debut .='</table>'."\n";
    }
    mysql_free_result($res_rq_info_liste_status);
$message_debut .='</td>'."\n";
$message_debut .='</tr>'."\n";
	}
	$message_debut .= '<tr>'."\n";
	$message_debut .= '<td align="left">&nbsp;Date de D&eacute;but&nbsp;</td>'."\n";
	$message_debut .= '<td align="left">&nbsp;'.$Date_Inter_Debut.'&nbsp;</td>'."\n";
	$message_debut .= '<td align="left">&nbsp;Heure de D&eacute;but&nbsp;</td>'."\n";
	$message_debut .= '<td align="left">&nbsp;'.$HEURE_PLANIF_DEBUT_H.'h'.$HEURE_PLANIF_DEBUT_M.'&nbsp;</td>'."\n";
	$message_debut .= '</tr>'."\n";
	$message_debut .= '<tr>'."\n";
	$message_debut .= '<td align="left">&nbsp;Date de fin&nbsp;</td>'."\n";
	$message_debut .= '<td align="left">&nbsp;'.$Date_Inter_Fin.'&nbsp;</td>'."\n";
	$message_debut .= '<td align="left">&nbsp;Heure de Fin&nbsp;</td>'."\n";
	$message_debut .= '<td align="left">&nbsp;'.$HEURE_PLANIF_FIN_H.'h'.$HEURE_PLANIF_FIN_M.'</td>'."\n";
	$message_debut .= '</tr>'."\n";
	$message_debut .= '<tr>'."\n";
	$message_debut .= '<td align="left">&nbsp;Titre du Changement&nbsp;</td>'."\n";
	$message_debut .= '<td align="left" colspan="3">&nbsp;'.stripslashes($lib).'&nbsp;</td>'."\n";
	$message_debut .= '</tr>'."\n";
	$message_debut .= '<tr>'."\n";
	$message_debut .= '<td align="left">&nbsp;Type de Changement&nbsp;</td>'."\n";
	$message_debut .= '<td align="left" colspan="3" bgcolor="#'.$CHANGEMENT_DEMANDE_COULEUR_FOND.'"><FONT COLOR="#'.$CHANGEMENT_DEMANDE_COULEUR_TEXTE.'">&nbsp;'.$CHANGEMENT_DEMANDE_LIB.'&nbsp;</FONT></td>'."\n";
	$message_debut .= '</tr>'."\n";
	if($changement_info=='oui'){
   $rq_info_config="
	 SELECT  
  `changement_liste_config`.`CHANGEMENT_LISTE_CONFIG_ID`,
  `changement_liste_config`.`CHANGEMENT_LISTE_CONFIG_LIB`, 
  `changement_liste_config`.`CHANGEMENT_LISTE_CONFIG_TYPE`, 
  `changement_liste_config`.`CHANGEMENT_LISTE_CONFIG_OBLIGATOIRE`,
  `changement_liste_config`.`CHANGEMENT_LISTE_CONFIG_TABLE` ,
  `changement_liste_info`.`CHANGEMENT_LISTE_INFO_AUTRE_ID`,
  `changement_liste_info`.`CHANGEMENT_LISTE_INFO_LIB`
	 FROM `changement_liste_config`,`changement_liste_info`
	 WHERE 
  `changement_liste_config`.`CHANGEMENT_LISTE_CONFIG_ID`=`changement_liste_info`.`CHANGEMENT_LISTE_CONFIG_ID`
  AND `changement_liste_info`.`CHANGEMENT_LISTE_ID`='".$ID."'
  GROUP BY `changement_liste_config`.`CHANGEMENT_LISTE_CONFIG_ID`
  ORDER BY `changement_liste_config`.`CHANGEMENT_LISTE_CONFIG_ORDRE`";
	$res_rq_info_config = mysql_query($rq_info_config, $mysql_link) or die(mysql_error());
	$tab_rq_info_config = mysql_fetch_assoc($res_rq_info_config);
	$total_ligne_rq_info_config=mysql_num_rows($res_rq_info_config);
	if($total_ligne_rq_info_config!=0){
		do {
		$CHANGEMENT_LISTE_CONFIG_ID=$tab_rq_info_config['CHANGEMENT_LISTE_CONFIG_ID'];
		$CHANGEMENT_LISTE_CONFIG_LIB=$tab_rq_info_config['CHANGEMENT_LISTE_CONFIG_LIB'];
		$CHANGEMENT_LISTE_CONFIG_TYPE=$tab_rq_info_config['CHANGEMENT_LISTE_CONFIG_TYPE'];
		$CHANGEMENT_LISTE_CONFIG_OBLIGATOIRE=$tab_rq_info_config['CHANGEMENT_LISTE_CONFIG_OBLIGATOIRE'];
		
		if(isset($tab_rq_info_config['CHANGEMENT_LISTE_INFO_AUTRE_ID'])){
      			$CHANGEMENT_LISTE_INFO_AUTRE_ID=$tab_rq_info_config['CHANGEMENT_LISTE_INFO_AUTRE_ID'];
		}else{
			$CHANGEMENT_LISTE_INFO_AUTRE_ID=0;
		}
		if(isset($tab_rq_info_config['CHANGEMENT_LISTE_INFO_LIB'])){
      			$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID]=$tab_rq_info_config['CHANGEMENT_LISTE_INFO_LIB'];
		}
		if(!isset($CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID])){$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID]='';}
	       $info_OBLIGATOIRE='';
	       if($CHANGEMENT_LISTE_CONFIG_OBLIGATOIRE=='oui'){$info_OBLIGATOIRE='*';}
$message_debut .='<tr>'."\n";
$message_debut .='<td align="left">&nbsp;'.stripslashes($CHANGEMENT_LISTE_CONFIG_LIB).'&nbsp;'.$info_OBLIGATOIRE.'&nbsp;</td>'."\n";
$message_debut .='<td align="left" colspan="3">'."\n";
		      switch ($CHANGEMENT_LISTE_CONFIG_TYPE)
            {
            case "liste": 
              $CHANGEMENT_LISTE_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_LISTE_CONFIG_TABLE'];
              $CHANGEMENT_LISTE_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE);
              $rq_info_config_table="
              SELECT  *
              FROM `".$CHANGEMENT_LISTE_CONFIG_TABLE."`
              WHERE 
              `ENABLE`='0'
              AND `".$CHANGEMENT_LISTE_CONFIG_TABLE_TRI."`!='vide'
              ORDER BY `".$CHANGEMENT_LISTE_CONFIG_TABLE_TRI."`";
              $res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
              $tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
              $total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);
              $rq_info_config_table_info="
              SELECT  `CHANGEMENT_LISTE_INFO_AUTRE_ID`
              FROM `changement_liste_info`
              WHERE 
              `CHANGEMENT_LISTE_ID`='".$ID."'
              AND `CHANGEMENT_LISTE_CONFIG_ID`='".$CHANGEMENT_LISTE_CONFIG_ID."'
              AND `ENABLE`='0'
              LIMIT 1";
              //echo $rq_info_config_table_info;
              $res_rq_info_config_table_info = mysql_query($rq_info_config_table_info, $mysql_link) or die(mysql_error());
              $tab_rq_info_config_table_info = mysql_fetch_assoc($res_rq_info_config_table_info);
              $total_ligne_rq_info_config_table_info=mysql_num_rows($res_rq_info_config_table_info);
              if($total_ligne_rq_info_config_table_info!=0){
                $CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID]=$tab_rq_info_config_table_info['CHANGEMENT_LISTE_INFO_AUTRE_ID'];
              }else{
                $CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID]='';
              }
              
              mysql_free_result($res_rq_info_config_table_info);

        		if($total_ligne_rq_info_config_table!=0){
                	do {
				$ID_SQL=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE).'_ID';
				$ID_LIB=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE);
				$CHANGEMENT_LISTE_CONFIG_TABLE_ID=$tab_rq_info_config_table[$ID_SQL];
				$CHANGEMENT_LISTE_CONFIG_TABLE_LIB=$tab_rq_info_config_table[$ID_LIB];
                	if($CHANGEMENT_LISTE_CONFIG_TABLE_ID == $CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID] ){
$message_debut .=$CHANGEMENT_LISTE_CONFIG_TABLE_LIB."\n";
                		}	
                	} while ($tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table));
	                $ligne= mysql_num_rows($res_rq_info_config_table);
	                if($ligne > 0) {
	                  mysql_data_seek($res_rq_info_config_table, 0);
	                  $tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
	                }
                }

              mysql_free_result($res_rq_info_config_table);
              
            break;
            
            case "checkbox_horizontal": 
              $CHANGEMENT_LISTE_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_LISTE_CONFIG_TABLE'];
              $CHANGEMENT_LISTE_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE);
              $rq_info_config_table="
              SELECT  *
              FROM `".$CHANGEMENT_LISTE_CONFIG_TABLE."`
              WHERE 
              `ENABLE`='0'
              AND `".$CHANGEMENT_LISTE_CONFIG_TABLE_TRI."`!='vide'
              ORDER BY `".$CHANGEMENT_LISTE_CONFIG_TABLE_TRI."`";
              $res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
              $tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
              $total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);

              if($total_ligne_rq_info_config_table!=0){
                do {
                  $ID_SQL=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE).'_ID';
                  $ID_LIB=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE);
                  $COM_SQL=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE).'_COM';
                  $CHANGEMENT_LISTE_CONFIG_TABLE_ID=$tab_rq_info_config_table[$ID_SQL];
                  $CHANGEMENT_LISTE_CONFIG_TABLE_LIB=$tab_rq_info_config_table[$ID_LIB];
                  $CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE=$CHANGEMENT_LISTE_CONFIG_ID.'_'.$CHANGEMENT_LISTE_CONFIG_TABLE_ID;
                  	$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM=$CHANGEMENT_LISTE_CONFIG_ID.'_'.$CHANGEMENT_LISTE_CONFIG_TABLE_ID.'_COM';

                  	$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE]='off';
                  	$rq_info_config_liste_id="
                  	SELECT `CHANGEMENT_LISTE_INFO_AUTRE_ID` , `CHANGEMENT_LISTE_INFO_LIB` 
			FROM `changement_liste_info` 
			WHERE 
			`CHANGEMENT_LISTE_ID` = '".$ID."'
			AND `CHANGEMENT_LISTE_CONFIG_ID` = '".$CHANGEMENT_LISTE_CONFIG_ID."'
			AND `CHANGEMENT_LISTE_INFO_AUTRE_ID`='".$CHANGEMENT_LISTE_CONFIG_TABLE_ID."'
			AND `ENABLE` = '0'
			LIMIT 1";
			$res_rq_info_config_liste_id = mysql_query($rq_info_config_liste_id, $mysql_link) or die(mysql_error());
			$tab_rq_info_config_liste_id = mysql_fetch_assoc($res_rq_info_config_liste_id);
			$total_ligne_rq_info_config_liste_id=mysql_num_rows($res_rq_info_config_liste_id);
			
			if($total_ligne_rq_info_config_liste_id==0){
				$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE]='off';
				$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM]='';
			}else{
				$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE]='on';
				$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM]=$tab_rq_info_config_liste_id['CHANGEMENT_LISTE_INFO_LIB'];
			}
			mysql_free_result($res_rq_info_config_liste_id);

                  if(!isset($CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM])){
                  	$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM]='';
                  }
                  if(!isset($CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE])){
                  	$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE]='';
                  }

                  
                  if(isset($tab_rq_info_config_table[$COM_SQL])){
                  	$CHANGEMENT_LISTE_CONFIG_TABLE_COM=$tab_rq_info_config_table[$COM_SQL];	
                  }else{
                  	$CHANGEMENT_LISTE_CONFIG_TABLE_COM='non';
                  }

$message_debut .='<INPUT TYPE="CHECKBOX" DISABLED NAME="CHANGEMENT_LISTE_CONFIG_ID_'.$CHANGEMENT_LISTE_CONFIG_ID.'_'.$CHANGEMENT_LISTE_CONFIG_TABLE_ID.'"'."\n";
                  if($CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE]=='on'){
$message_debut .=' checked'."\n";
			}
$message_debut .='>'.$CHANGEMENT_LISTE_CONFIG_TABLE_LIB.''."\n";
                  if($CHANGEMENT_LISTE_CONFIG_TABLE_COM=='oui'){
$message_debut .=stripslashes($CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM])."\n";
                }

                } while ($tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table));
                $ligne= mysql_num_rows($res_rq_info_config_table);
                if($ligne > 0) {
                  mysql_data_seek($res_rq_info_config_table, 0);
                  $tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
                }
              }
              mysql_free_result($res_rq_info_config_table);             
              
            break;
            case "checkbox": 
              $CHANGEMENT_LISTE_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_LISTE_CONFIG_TABLE'];
              $CHANGEMENT_LISTE_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE);
              $rq_info_config_table="
              SELECT  *
              FROM `".$CHANGEMENT_LISTE_CONFIG_TABLE."`
              WHERE 
              `ENABLE`='0'
              AND `".$CHANGEMENT_LISTE_CONFIG_TABLE_TRI."`!='vide'
              ORDER BY `".$CHANGEMENT_LISTE_CONFIG_TABLE_TRI."`";
              $res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
              $tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
              $total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);

              if($total_ligne_rq_info_config_table!=0){
                do {
                  $ID_SQL=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE).'_ID';
                  $ID_LIB=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE);
                  $COM_SQL=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE).'_COM';
                  $CHANGEMENT_LISTE_CONFIG_TABLE_ID=$tab_rq_info_config_table[$ID_SQL];
                  $CHANGEMENT_LISTE_CONFIG_TABLE_LIB=$tab_rq_info_config_table[$ID_LIB];
                  $CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE=$CHANGEMENT_LISTE_CONFIG_ID.'_'.$CHANGEMENT_LISTE_CONFIG_TABLE_ID;
                  	$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM=$CHANGEMENT_LISTE_CONFIG_ID.'_'.$CHANGEMENT_LISTE_CONFIG_TABLE_ID.'_COM';

                  	$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE]='off';
                  	$rq_info_config_liste_id="
                  	SELECT `CHANGEMENT_LISTE_INFO_AUTRE_ID` , `CHANGEMENT_LISTE_INFO_LIB` 
			FROM `changement_liste_info` 
			WHERE 
			`CHANGEMENT_LISTE_ID` = '".$ID."'
			AND `CHANGEMENT_LISTE_CONFIG_ID` = '".$CHANGEMENT_LISTE_CONFIG_ID."'
			AND `CHANGEMENT_LISTE_INFO_AUTRE_ID`='".$CHANGEMENT_LISTE_CONFIG_TABLE_ID."'
			AND `ENABLE` = '0'
			LIMIT 1";
			$res_rq_info_config_liste_id = mysql_query($rq_info_config_liste_id, $mysql_link) or die(mysql_error());
			$tab_rq_info_config_liste_id = mysql_fetch_assoc($res_rq_info_config_liste_id);
			$total_ligne_rq_info_config_liste_id=mysql_num_rows($res_rq_info_config_liste_id);
			
			if($total_ligne_rq_info_config_liste_id==0){
				$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE]='off';
				$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM]='';
			}else{
				$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE]='on';
				$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM]=$tab_rq_info_config_liste_id['CHANGEMENT_LISTE_INFO_LIB'];
			}
			mysql_free_result($res_rq_info_config_liste_id);

                  if(!isset($CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM])){
                  	$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM]='';
                  }
                  if(!isset($CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE])){
                  	$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE]='';
                  }

                  
                  if(isset($tab_rq_info_config_table[$COM_SQL])){
                  	$CHANGEMENT_LISTE_CONFIG_TABLE_COM=$tab_rq_info_config_table[$COM_SQL];	
                  }else{
                  	$CHANGEMENT_LISTE_CONFIG_TABLE_COM='non';
                  }

$message_debut .='<INPUT TYPE="CHECKBOX" DISABLED NAME="CHANGEMENT_LISTE_CONFIG_ID_'.$CHANGEMENT_LISTE_CONFIG_ID.'_'.$CHANGEMENT_LISTE_CONFIG_TABLE_ID.'"'."\n";
                  if($CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE]=='on'){
$message_debut .=' checked'."\n";
                  	}
$message_debut .='>'.$CHANGEMENT_LISTE_CONFIG_TABLE_LIB.''."\n";
                  if($CHANGEMENT_LISTE_CONFIG_TABLE_COM=='oui'){
$message_debut .=stripslashes($CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM])."\n";

                  }
$message_debut .='</BR>'."\n";
                
                } while ($tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table));
                $ligne= mysql_num_rows($res_rq_info_config_table);
                if($ligne > 0) {
                  mysql_data_seek($res_rq_info_config_table, 0);
                  $tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
                }
              }
              mysql_free_result($res_rq_info_config_table);             
              
            break;
            
            case "varchar": 
$message_debut .=stripslashes($CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID])."\n";
              break;
              
            case "text": 
$message_debut .=nl2br(stripslashes($CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID]))."\n";
            break;
            }
$message_debut .='</td>'."\n";
$message_debut .='</tr>'."\n";
	
		 } while ($tab_rq_info_config = mysql_fetch_assoc($res_rq_info_config));
	        $ligne= mysql_num_rows($res_rq_info_config);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_config, 0);
	          $tab_rq_info_config = mysql_fetch_assoc($res_rq_info_config);
	        }
	}
	mysql_free_result($res_rq_info_config);

        }
        $message_debut .='<tr>'."\n";
	$message_debut .='<td align="left" bgcolor="#'.$CHANGEMENT_STATUS_COULEUR_FOND.'"><FONT COLOR="#'.$CHANGEMENT_STATUS_COULEUR_TEXT.'">&nbsp;Status&nbsp;</FONT></td>'."\n";
	$message_debut .='<td align="left" colspan="3" bgcolor="#'.$CHANGEMENT_STATUS_COULEUR_FOND.'"><FONT COLOR="#'.$CHANGEMENT_STATUS_COULEUR_TEXT.'">&nbsp;'.$CHANGEMENT_STATUS.'&nbsp;</FONT></td>'."\n";
	$message_debut .='</tr>'."\n";
	/*$message_debut .= '<tr>'."\n";
	$message_debut .= '<td align="left">&nbsp;Status&nbsp;</td>'."\n";
	$message_debut .= '<td align="left" colspan="3">&nbsp;'.$CHANGEMENT_STATUS.'&nbsp;</td>'."\n";
	$message_debut .= '</tr>'."\n";*/
	
	
	
	$message_debut .= '</table>'."\n";
	$message_debut =stripslashes($message_debut);
	
	$message_signature  = '';
	$message_signature .='</BR>'."\n"; 
	$message_signature .='<div id="signature">'."\n";
	$message_signature .='  <b>'.$Personne_complet.'</b><br>'."\n";
	$message_signature .='  Gestion des changements<br>'."\n";
	$message_signature .='  Informatique CDC - Etablissement DPI<br>'."\n";
	$message_signature .='</div>   '."\n";	
	$message_signature .='</div>   '."\n";  
	$message_signature =stripslashes($message_signature);
	

$message_suivi='';
$message_info='';
/// Creation de la FAR
$message_FAR='';
if($FAR_info=='oui'){
$CHANGEMENT_FAR_INSCRIPTION="Non";

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
$message_FAR='';
$message_FAR .='</BR>'."\n"; 
$message_FAR .='<div align="left">'."\n"; 
$message_FAR .='<table>'."\n"; 
$message_FAR .='<tr align="center">'."\n"; 
$message_FAR .='<td colspan="2"><b>&nbsp;Information de la FAR du changement n&deg; '.$ID.'&nbsp;</b></td>'."\n"; 
$message_FAR .='</tr>'."\n"; 

        $rq_info_far_lib="
        SELECT DISTINCT (`changement_far_config`.`CHANGEMENT_FAR_CONFIG_LIB`) AS `CHANGEMENT_FAR_CONFIG_LIB`
        FROM `changement_far_config` , `changement_far`
        WHERE `changement_far_config`.`CHANGEMENT_FAR_CONFIG_ID` = `changement_far`.`CHANGEMENT_FAR_CONFIG_ID`
        AND `changement_far`.`CHANGEMENT_LISTE_ID` = '".$ID."'
        AND `changement_far`.`ENABLE` = '0'
        ORDER BY `changement_far_config`.`CHANGEMENT_FAR_CONFIG_LIB`";
      $res_rq_info_far_lib = mysql_query($rq_info_far_lib, $mysql_link) or die(mysql_error());
      $tab_rq_info_far_lib = mysql_fetch_assoc($res_rq_info_far_lib);
      $total_ligne_rq_info_far_lib=mysql_num_rows($res_rq_info_far_lib);
      if($total_ligne_rq_info_far_lib!=0){
        do {
        	$CHANGEMENT_FAR_CONFIG_LIB=$tab_rq_info_far_lib['CHANGEMENT_FAR_CONFIG_LIB'];

$message_FAR .='<tr align="center">'."\n"; 
$message_FAR .='<td colspan="2">&nbsp;<b>'.stripslashes(substr($CHANGEMENT_FAR_CONFIG_LIB,strpos($CHANGEMENT_FAR_CONFIG_LIB,"-")+1)).'</b>&nbsp;</td>'."\n"; 
$message_FAR .='</tr>'."\n"; 
                  $rq_info_far="
                  SELECT 
                  `changement_far_config`.`CHANGEMENT_FAR_CONFIG_ID` , 
              	  `changement_far_config`.`CHANGEMENT_FAR_CONFIG_LIB` , 
              	  `changement_far_config`.`CHANGEMENT_FAR_CONFIG_CRITERE` , 
              	  `changement_far_config`.`CHANGEMENT_FAR_CONFIG_TYPE` , 
              	  `changement_far_config`.`CHANGEMENT_FAR_CONFIG_TABLE` , 
              	  `changement_far_config`.`CHANGEMENT_FAR_CONFIG_OBLIGATOIRE` , 
              	  `changement_far_config`.`CHANGEMENT_FAR_CONFIG_ORDRE` , 
              	  `changement_far_config`.`ENABLE` 
                  FROM `changement_far_config` , `changement_far`
                  WHERE `changement_far_config`.`CHANGEMENT_FAR_CONFIG_ID` = `changement_far`.`CHANGEMENT_FAR_CONFIG_ID`
                  AND `changement_far`.`CHANGEMENT_LISTE_ID` = '".$ID."'
                  AND `changement_far_config`.`CHANGEMENT_FAR_CONFIG_LIB`='".$CHANGEMENT_FAR_CONFIG_LIB."' 
                  AND `changement_far`.`ENABLE` = '0'
                  GROUP BY `changement_far_config`.`CHANGEMENT_FAR_CONFIG_ID`
                  ORDER BY `changement_far_config`.`CHANGEMENT_FAR_CONFIG_ORDRE`
                   ";
	      $res_rq_info_far = mysql_query($rq_info_far, $mysql_link) or die(mysql_error());
	      $tab_rq_info_far = mysql_fetch_assoc($res_rq_info_far);
	      $total_ligne_rq_info_far=mysql_num_rows($res_rq_info_far);
	      if($total_ligne_rq_info_far!=0){
	        do {
	        	$CHANGEMENT_FAR_CONFIG_ID=$tab_rq_info_far['CHANGEMENT_FAR_CONFIG_ID'];
	        	$CHANGEMENT_FAR_CONFIG_CRITERE=$tab_rq_info_far['CHANGEMENT_FAR_CONFIG_CRITERE'];
	        	$CHANGEMENT_FAR_CONFIG_TYPE=$tab_rq_info_far['CHANGEMENT_FAR_CONFIG_TYPE'];
	        	$CHANGEMENT_FAR_CONFIG_OBLIGATOIRE=$tab_rq_info_far['CHANGEMENT_FAR_CONFIG_OBLIGATOIRE'];
	        	if(isset($tab_rq_info_far['CHANGEMENT_FAR_INFO_AUTRE_ID'])){
	      			$CHANGEMENT_FAR_INFO_AUTRE_ID=$tab_rq_info_far['CHANGEMENT_FAR_INFO_AUTRE_ID'];
			}else{
				$CHANGEMENT_FAR_INFO_AUTRE_ID=0;
			}
			if(isset($tab_rq_info_far['CHANGEMENT_FAR_INFO_LIB'])){
	      			$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]=$tab_rq_info_far['CHANGEMENT_FAR_INFO_LIB'];
			}
			if(!isset($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID])){$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]='';}
		 	if(!isset($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID])){$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]='';}

$message_FAR .='<tr>'."\n"; 
$message_FAR .='<td align="left">&nbsp;'.stripslashes($CHANGEMENT_FAR_CONFIG_CRITERE).'&nbsp;</td>'."\n"; 
$message_FAR .='<td align="left">'."\n"; 
		      switch ($CHANGEMENT_FAR_CONFIG_TYPE)
            {
            case "oui-non": 
$message_FAR .='<INPUT type="radio" DISABLED name="CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID.'" value="Oui"'."\n";
              if($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]=='Oui'){$message_FAR .='CHECKED'."\n";} 
$message_FAR .='>&nbsp;Oui&nbsp;/&nbsp;Non&nbsp;'."\n";
$message_FAR .='<INPUT type="radio" DISABLED name="CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID.'" value="Non"'."\n";
              if($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]=='Non'){$message_FAR .='CHECKED'."\n";} 
$message_FAR .='>'."\n";
              
            break;
            case "risque": 
$message_FAR .='&nbsp;1&nbsp;<INPUT type="radio" DISABLED name="CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID.'" value="1"'."\n";
              if($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]=='1'){$message_FAR .='CHECKED'."\n";} 
$message_FAR .='>&nbsp;2&nbsp;<INPUT type="radio" DISABLED name="CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID.'" value="2"'."\n";
              if($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]=='2'){$message_FAR .='CHECKED'."\n";} 
$message_FAR .='>&nbsp;3&nbsp;<INPUT type="radio" DISABLED name="CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID.'" value="3"'."\n";
              if($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]=='3'){$message_FAR .='CHECKED'."\n";} 
$message_FAR .='>&nbsp;4&nbsp;<INPUT type="radio" DISABLED name="CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID.'" value="4"'."\n";
              if($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]=='4'){$message_FAR .='CHECKED'."\n";} 
$message_FAR .='>'."\n";
            break;
            case "liste": 
              $CHANGEMENT_FAR_CONFIG_TABLE=$tab_rq_info_far['CHANGEMENT_FAR_CONFIG_TABLE'];
              $CHANGEMENT_FAR_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE);
              $rq_info_config_table="
              SELECT  *
              FROM `".$CHANGEMENT_FAR_CONFIG_TABLE."`
              WHERE 
              `ENABLE`='0'
              AND `".$CHANGEMENT_FAR_CONFIG_TABLE_TRI."`!='vide'
              ORDER BY `".$CHANGEMENT_FAR_CONFIG_TABLE_TRI."`";
              $res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
              $tab_rq_info_far_table = mysql_fetch_assoc($res_rq_info_config_table);
              $total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);
              $rq_info_config_table_info="
              SELECT  `CHANGEMENT_FAR_INFO_AUTRE_ID`
              FROM `changement_far`
              WHERE 
              `CHANGEMENT_LISTE_ID`='".$ID."'
              AND `CHANGEMENT_FAR_CONFIG_ID`='".$CHANGEMENT_FAR_CONFIG_ID."'
              AND `ENABLE`='0'
              LIMIT 1";
              $res_rq_info_config_table_info = mysql_query($rq_info_config_table_info, $mysql_link) or die(mysql_error());
              $tab_rq_info_far_table_info = mysql_fetch_assoc($res_rq_info_config_table_info);
              $total_ligne_rq_info_config_table_info=mysql_num_rows($res_rq_info_config_table_info);
              if($total_ligne_rq_info_config_table_info!=0){
                $CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]=$tab_rq_info_far_table_info['CHANGEMENT_FAR_INFO_AUTRE_ID'];
              }else{
                $CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]='';
              }
              mysql_free_result($res_rq_info_config_table_info);

        		if($total_ligne_rq_info_config_table!=0){
                do {
                	$ID_SQL=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE).'_ID';
                  $ID_LIB=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE);
                  $CHANGEMENT_FAR_CONFIG_TABLE_ID=$tab_rq_info_far_table[$ID_SQL];
                  $CHANGEMENT_FAR_CONFIG_TABLE_LIB=$tab_rq_info_far_table[$ID_LIB];
                  if($CHANGEMENT_FAR_CONFIG_TABLE_ID == $CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID] ){$message_FAR .=$CHANGEMENT_FAR_CONFIG_TABLE_LIB.''."\n";}
                	
                	} while ($tab_rq_info_far_table = mysql_fetch_assoc($res_rq_info_config_table));
                $ligne= mysql_num_rows($res_rq_info_config_table);
                if($ligne > 0) {
                  mysql_data_seek($res_rq_info_config_table, 0);
                  $tab_rq_info_far_table = mysql_fetch_assoc($res_rq_info_config_table);
                }
                }
              mysql_free_result($res_rq_info_config_table);
              
            break;
            case "checkbox": 
              $CHANGEMENT_FAR_CONFIG_TABLE=$tab_rq_info_far['CHANGEMENT_FAR_CONFIG_TABLE'];
              $CHANGEMENT_FAR_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE);
              $rq_info_config_table="
              SELECT  *
              FROM `".$CHANGEMENT_FAR_CONFIG_TABLE."`
              WHERE 
              `ENABLE`='0'
              AND `".$CHANGEMENT_FAR_CONFIG_TABLE_TRI."`!='vide'
              ORDER BY `".$CHANGEMENT_FAR_CONFIG_TABLE_TRI."`";
              $res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
              $tab_rq_info_far_table = mysql_fetch_assoc($res_rq_info_config_table);
              $total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);

              if($total_ligne_rq_info_config_table!=0){
              	$NB_total_ligne_rq_info_config_table=0;
                do {
                  $ID_SQL=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE).'_ID';
                  $ID_LIB=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE);
                  $COM_SQL=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE).'_COM';
                  $CHANGEMENT_FAR_CONFIG_TABLE_ID=$tab_rq_info_far_table[$ID_SQL];
                  $CHANGEMENT_FAR_CONFIG_TABLE_LIB=$tab_rq_info_far_table[$ID_LIB];
                  $CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE=$CHANGEMENT_FAR_CONFIG_ID.'_'.$CHANGEMENT_FAR_CONFIG_TABLE_ID;
                  	$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE_COM=$CHANGEMENT_FAR_CONFIG_ID.'_'.$CHANGEMENT_FAR_CONFIG_TABLE_ID.'_COM';

                  	$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE]='off';
                  	$rq_info_config_liste_id="
                  	SELECT `CHANGEMENT_FAR_INFO_AUTRE_ID`,`CHANGEMENT_FAR_VALEUR`
			FROM `changement_far` 
			WHERE 
			`CHANGEMENT_LISTE_ID` = '".$ID."'
			AND `CHANGEMENT_FAR_CONFIG_ID` = '".$CHANGEMENT_FAR_CONFIG_ID."'
			AND `CHANGEMENT_FAR_INFO_AUTRE_ID`='".$CHANGEMENT_FAR_CONFIG_TABLE_ID."'
			AND `ENABLE` = '0'
			LIMIT 1";
			$res_rq_info_config_liste_id = mysql_query($rq_info_config_liste_id, $mysql_link) or die(mysql_error());
			$tab_rq_info_far_liste_id = mysql_fetch_assoc($res_rq_info_config_liste_id);
			$total_ligne_rq_info_config_liste_id=mysql_num_rows($res_rq_info_config_liste_id);
			
			if($total_ligne_rq_info_config_liste_id==0){
				$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE]='off';
				$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE_COM]='';
			}else{
				$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE]='on';
				$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE_COM]=$tab_rq_info_far_liste_id['CHANGEMENT_FAR_VALEUR'];
			}
			mysql_free_result($res_rq_info_config_liste_id);

                  if(!isset($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE_COM])){
                  	$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE_COM]='';
                  }
                  if(!isset($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE])){
                  	$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE]='';
                  }
                  if(isset($tab_rq_info_far_table[$COM_SQL])){
                  	$CHANGEMENT_FAR_CONFIG_TABLE_COM=$tab_rq_info_far_table[$COM_SQL];	
                  }else{
                  	$CHANGEMENT_FAR_CONFIG_TABLE_COM='non';
                  }
                  $NB_total_ligne_rq_info_config_table++;
$message_FAR .='<INPUT TYPE="CHECKBOX" DISABLED NAME="CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID.'_'.$CHANGEMENT_FAR_CONFIG_TABLE_ID.'"'."\n";
                  if($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE]=='on'){$message_FAR .='checked'."\n";}
$message_FAR .='>'.$CHANGEMENT_FAR_CONFIG_TABLE_LIB.''."\n";
                  if($CHANGEMENT_FAR_CONFIG_TABLE_COM=='oui'){
$message_FAR .='&nbsp;:&nbsp;'.stripslashes($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE_COM]).''."\n";
                  }

		if($NB_total_ligne_rq_info_config_table < $total_ligne_rq_info_config_table){
$message_FAR .='</BR>'."\n";	
                }
                
                } while ($tab_rq_info_far_table = mysql_fetch_assoc($res_rq_info_config_table));
                $ligne= mysql_num_rows($res_rq_info_config_table);
                if($ligne > 0) {
                  mysql_data_seek($res_rq_info_config_table, 0);
                  $tab_rq_info_far_table = mysql_fetch_assoc($res_rq_info_config_table);
                }
              }
              mysql_free_result($res_rq_info_config_table);            
            break;
            case "text": 
$message_FAR .=nl2br(stripslashes($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID])).''."\n";
            break;
            case "varchar": 
$message_FAR .=stripslashes($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]).''."\n";
            break;
            case "checkbox_horizontal": 
              $CHANGEMENT_FAR_CONFIG_TABLE=$tab_rq_info_far['CHANGEMENT_FAR_CONFIG_TABLE'];
              $CHANGEMENT_FAR_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE);
              $rq_info_config_table="
              SELECT  *
              FROM `".$CHANGEMENT_FAR_CONFIG_TABLE."`
              WHERE 
              `ENABLE`='0'
              AND `".$CHANGEMENT_FAR_CONFIG_TABLE_TRI."`!='vide'
              ORDER BY `".$CHANGEMENT_FAR_CONFIG_TABLE_TRI."`";
              $res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
              $tab_rq_info_far_table = mysql_fetch_assoc($res_rq_info_config_table);
              $total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);

              if($total_ligne_rq_info_config_table!=0){
                do {
                  $ID_SQL=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE).'_ID';
                  $ID_LIB=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE);
                  $COM_SQL=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE).'_COM';
                  $CHANGEMENT_FAR_CONFIG_TABLE_ID=$tab_rq_info_far_table[$ID_SQL];
                  $CHANGEMENT_FAR_CONFIG_TABLE_LIB=$tab_rq_info_far_table[$ID_LIB];
                  $CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE=$CHANGEMENT_FAR_CONFIG_ID.'_'.$CHANGEMENT_FAR_CONFIG_TABLE_ID;
                  	$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE_COM=$CHANGEMENT_FAR_CONFIG_ID.'_'.$CHANGEMENT_FAR_CONFIG_TABLE_ID.'_COM';

                  	$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE]='off';
                  	$rq_info_config_liste_id="
                  	SELECT `CHANGEMENT_FAR_INFO_AUTRE_ID`,`CHANGEMENT_FAR_VALEUR`
			FROM `changement_far` 
			WHERE 
			`CHANGEMENT_LISTE_ID` = '".$ID."'
			AND `CHANGEMENT_FAR_CONFIG_ID` = '".$CHANGEMENT_FAR_CONFIG_ID."'
			AND `CHANGEMENT_FAR_INFO_AUTRE_ID`='".$CHANGEMENT_FAR_CONFIG_TABLE_ID."'
			AND `ENABLE` = '0'
			LIMIT 1";
			$res_rq_info_config_liste_id = mysql_query($rq_info_config_liste_id, $mysql_link) or die(mysql_error());
			$tab_rq_info_far_liste_id = mysql_fetch_assoc($res_rq_info_config_liste_id);
			$total_ligne_rq_info_config_liste_id=mysql_num_rows($res_rq_info_config_liste_id);
			
			if($total_ligne_rq_info_config_liste_id==0){
				$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE]='off';
				$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE_COM]='';
			}else{
				$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE]='on';
				$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE_COM]=$tab_rq_info_far_liste_id['CHANGEMENT_FAR_VALEUR'];
			}
			mysql_free_result($res_rq_info_config_liste_id);
                  if(!isset($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE_COM])){
                  	$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE_COM]='';
                  }
                  if(!isset($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE])){
                  	$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE]='';
                  } 
                  if(isset($tab_rq_info_far_table[$COM_SQL])){
                  	$CHANGEMENT_FAR_CONFIG_TABLE_COM=$tab_rq_info_far_table[$COM_SQL];	
                  }else{
                  	$CHANGEMENT_FAR_CONFIG_TABLE_COM='non';
                  }
$message_FAR .='<INPUT TYPE="CHECKBOX" DISABLED NAME="CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID.'_'.$CHANGEMENT_FAR_CONFIG_TABLE_ID.'"'."\n";
                  if($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE]=='on'){$message_FAR .=' checked'."\n";}
$message_FAR .='>'.$CHANGEMENT_FAR_CONFIG_TABLE_LIB.'&nbsp;&nbsp;'."\n";
                  if($CHANGEMENT_FAR_CONFIG_TABLE_COM=='oui'){
$message_FAR .='<input readonly="readonly" id="CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID.'_'.$CHANGEMENT_FAR_CONFIG_TABLE_ID.'_COM" name="CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID.'_'.$CHANGEMENT_FAR_CONFIG_TABLE_ID.'_COM" type="hidden" value="vide" size="50" maxlength="100"/>'."\n";
                  }
                } while ($tab_rq_info_far_table = mysql_fetch_assoc($res_rq_info_config_table));
                $ligne= mysql_num_rows($res_rq_info_config_table);
                if($ligne > 0) {
                  mysql_data_seek($res_rq_info_config_table, 0);
                  $tab_rq_info_far_table = mysql_fetch_assoc($res_rq_info_config_table);
                }
              }
              mysql_free_result($res_rq_info_config_table);
            break;
            
            }
$message_FAR .='&nbsp;</td>'."\n";
$message_FAR .='</tr>'."\n";
	        	
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
      
$message_FAR .='</table>'."\n";
$message_FAR .='</div>'."\n";
$message_FAR .='</BR>'."\n"; 
$message_FAR =stripslashes($message_FAR);
}

/// Creation de la fiche Bilan
$message_BILAN='';
if($Fiche_Bilan_info=='oui'){

	$rq_info_bilan="
	SELECT `changement_status`.`CHANGEMENT_STATUS` 
	FROM `changement_liste` , `changement_status` 
	WHERE `changement_liste`.`CHANGEMENT_LISTE_ID` = '".$ID."'
	AND `changement_liste`.`CHANGEMENT_STATUS_ID` = `changement_status`.`CHANGEMENT_STATUS_ID` 
	AND `changement_liste`.`ENABLE` = '0'
	";
	$res_rq_info_bilan = mysql_query($rq_info_bilan, $mysql_link) or die(mysql_error());
	$tab_rq_info_bilan = mysql_fetch_assoc($res_rq_info_bilan);
	$total_ligne_rq_info_bilan=mysql_num_rows($res_rq_info_bilan);
	if($total_ligne_rq_info_bilan==0){
		$CHANGEMENT_STATUS='Brouillon';
	}else{
		$CHANGEMENT_STATUS=$tab_rq_info_bilan['CHANGEMENT_STATUS'];;		
	}
	mysql_free_result($res_rq_info_bilan);
	$rq_info_bilan="
	SELECT *
	FROM `changement_bilan`
        WHERE `CHANGEMENT_LISTE_ID` = '".$ID."'
        AND ENABLE ='0'
	";
	$res_rq_info_bilan = mysql_query($rq_info_bilan, $mysql_link) or die(mysql_error());
	$tab_rq_info_bilan = mysql_fetch_assoc($res_rq_info_bilan);
	$total_ligne_rq_info_bilan=mysql_num_rows($res_rq_info_bilan);
	if($total_ligne_rq_info_bilan!=0){
          do {
            $CHANGEMENT_BILAN_ID=$tab_rq_info_bilan['CHANGEMENT_BILAN_ID'];
            $CHANGEMENT_LISTE_ID=$tab_rq_info_bilan['CHANGEMENT_LISTE_ID'];
            $CHANGEMENT_BILAN_CONFIG_ID=$tab_rq_info_bilan['CHANGEMENT_BILAN_CONFIG_ID'];
            $CHANGEMENT_BILAN_UTILISATEUR_ID=$tab_rq_info_bilan['CHANGEMENT_BILAN_UTILISATEUR_ID'];
            $CHANGEMENT_BILAN_AUTRE_ID=$tab_rq_info_bilan['CHANGEMENT_BILAN_AUTRE_ID'];
            $CHANGEMENT_BILAN_VALEUR=$tab_rq_info_bilan['CHANGEMENT_BILAN_VALEUR'];
            $CHANGEMENT_BILAN_COM=$tab_rq_info_bilan['CHANGEMENT_BILAN_COM'];
            $CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_ID]=$CHANGEMENT_BILAN_VALEUR;
            $CHANGEMENT_BILAN_CONFIG_COM_COM[$CHANGEMENT_BILAN_CONFIG_ID]=$CHANGEMENT_BILAN_COM;
            $CHANGEMENT_BILAN_CONFIG_NOM=$CHANGEMENT_BILAN_CONFIG_ID.'_'.$CHANGEMENT_BILAN_AUTRE_ID;
            $CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_NOM]=$CHANGEMENT_BILAN_VALEUR;

          } while ($tab_rq_info_bilan = mysql_fetch_assoc($res_rq_info_bilan));
          $ligne= mysql_num_rows($res_rq_info_bilan);
          if($ligne > 0) {
          mysql_data_seek($res_rq_info_bilan, 0);
          $tab_rq_info_bilan = mysql_fetch_assoc($res_rq_info_bilan);
          }
          }
	mysql_free_result($res_rq_info_bilan);

$message_BILAN='';
$message_BILAN .='</BR>'."\n"; 
$message_BILAN .='<div align="left">'."\n";
$message_BILAN .='<table>'."\n";

        $rq_info_bilan_lib="       
        SELECT DISTINCT (`changement_bilan_config`.`CHANGEMENT_BILAN_CONFIG_LIB`) AS `CHANGEMENT_BILAN_CONFIG_LIB`
        FROM `changement_bilan_config` , `changement_bilan`
        WHERE `changement_bilan_config`.`CHANGEMENT_BILAN_CONFIG_ID` = `changement_bilan`.`CHANGEMENT_BILAN_CONFIG_ID`
        AND `changement_bilan`.`CHANGEMENT_LISTE_ID` = '".$ID."'
        AND `changement_bilan`.`ENABLE` = '0'
        ORDER BY `changement_bilan_config`.`CHANGEMENT_BILAN_CONFIG_LIB`";
      $res_rq_info_bilan_lib = mysql_query($rq_info_bilan_lib, $mysql_link) or die(mysql_error());
      $tab_rq_info_bilan_lib = mysql_fetch_assoc($res_rq_info_bilan_lib);
      $total_ligne_rq_info_bilan_lib=mysql_num_rows($res_rq_info_bilan_lib);
      if($total_ligne_rq_info_bilan_lib!=0){
$message_BILAN .='<tr align="center">'."\n"; 
$message_BILAN .='<td colspan="2"><b>&nbsp;Information de la fiche Bilan du changement n&deg; '.$ID.'&nbsp;</b></td>'."\n"; 
$message_BILAN .='</tr>'."\n"; 

        do {
        	$CHANGEMENT_BILAN_CONFIG_LIB=$tab_rq_info_bilan_lib['CHANGEMENT_BILAN_CONFIG_LIB'];
$message_BILAN .='<tr align="center">'."\n";
$message_BILAN .='<td colspan="2">&nbsp;<b>'.stripslashes(substr($CHANGEMENT_BILAN_CONFIG_LIB,strpos($CHANGEMENT_BILAN_CONFIG_LIB,"-")+1)).'</b>&nbsp;</td>'."\n";
$message_BILAN .='</tr>'."\n";
                  $rq_info_bilan="
                  SELECT 
                  `changement_bilan_config`.`CHANGEMENT_BILAN_CONFIG_ID`, 
                  `changement_bilan_config`.`CHANGEMENT_BILAN_CONFIG_CRITERE`, 
                  `changement_bilan_config`.`CHANGEMENT_BILAN_CONFIG_TYPE`,
                  `changement_bilan_config`.`CHANGEMENT_BILAN_CONFIG_COM`
                  FROM `changement_bilan_config` , `changement_bilan`
                  WHERE `changement_bilan_config`.`CHANGEMENT_BILAN_CONFIG_ID` = `changement_bilan`.`CHANGEMENT_BILAN_CONFIG_ID`
                  AND `changement_bilan`.`CHANGEMENT_LISTE_ID` = '".$ID."'
                  AND `changement_bilan_config`.`CHANGEMENT_BILAN_CONFIG_LIB`='".$CHANGEMENT_BILAN_CONFIG_LIB."' 
                  AND `changement_bilan`.`ENABLE` = '0'
                  GROUP BY `changement_bilan_config`.`CHANGEMENT_BILAN_CONFIG_CRITERE`
                  ORDER BY `changement_bilan_config`.`CHANGEMENT_BILAN_CONFIG_ORDRE`
                  ";

	      $res_rq_info_bilan = mysql_query($rq_info_bilan, $mysql_link) or die(mysql_error());
	      $tab_rq_info_bilan = mysql_fetch_assoc($res_rq_info_bilan);
	      $total_ligne_rq_info_bilan=mysql_num_rows($res_rq_info_bilan);
	      if($total_ligne_rq_info_bilan!=0){
	        do {
	        	$CHANGEMENT_BILAN_CONFIG_ID=$tab_rq_info_bilan['CHANGEMENT_BILAN_CONFIG_ID'];
	        	$CHANGEMENT_BILAN_CONFIG_CRITERE=$tab_rq_info_bilan['CHANGEMENT_BILAN_CONFIG_CRITERE'];
	        	$CHANGEMENT_BILAN_CONFIG_TYPE=$tab_rq_info_bilan['CHANGEMENT_BILAN_CONFIG_TYPE'];
	        	$CHANGEMENT_BILAN_CONFIG_COM=$tab_rq_info_bilan['CHANGEMENT_BILAN_CONFIG_COM'];
	        	if(!isset($CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_ID])){$CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_ID]='';}
	        	if(!isset($CHANGEMENT_BILAN_CONFIG_COM_COM[$CHANGEMENT_BILAN_CONFIG_ID])){$CHANGEMENT_BILAN_CONFIG_COM_COM[$CHANGEMENT_BILAN_CONFIG_ID]='';}

$message_BILAN .='<tr>'."\n";
$message_BILAN .='<td align="left">&nbsp;'.stripslashes($CHANGEMENT_BILAN_CONFIG_CRITERE).'&nbsp;</td>'."\n";
$message_BILAN .='<td align="left">&nbsp;'."\n";
		     
		      switch ($CHANGEMENT_BILAN_CONFIG_TYPE)
            {
            case "oui-non": 
$message_BILAN .='<INPUT type="radio" DISABLED name="CHANGEMENT_BILAN_CONFIG_ID_'.$CHANGEMENT_BILAN_CONFIG_ID.'" value="Oui"'."\n";
              if($CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_ID]=='Oui'){$message_BILAN .='CHECKED'."\n";} 
$message_BILAN .='>&nbsp;Oui&nbsp;/&nbsp;Non&nbsp;'."\n";
$message_BILAN .='<INPUT type="radio" DISABLED name="CHANGEMENT_BILAN_CONFIG_ID_'.$CHANGEMENT_BILAN_CONFIG_ID.'" value="Non"'."\n";
              if($CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_ID]=='Non'){$message_BILAN .='CHECKED'."\n";} 
$message_BILAN .='>'."\n";
              if($CHANGEMENT_BILAN_CONFIG_COM=='oui'){
$message_BILAN .='&nbsp;-&nbsp;'.stripslashes($CHANGEMENT_BILAN_CONFIG_COM_COM[$CHANGEMENT_BILAN_CONFIG_ID]).''."\n";
              }
            break;
            
            case "liste_changement_bilan_personne": 
		$rq_info_personne="
		SELECT `CHANGEMENT_BILAN_PERSONNE_ID` , `CHANGEMENT_BILAN_PERSONNE_LIB` 
		FROM `changement_bilan_personne` 
		WHERE `ENABLE` = '0'
		AND `CHANGEMENT_BILAN_PERSONNE_LIB` != 'Total'
		ORDER BY `CHANGEMENT_BILAN_PERSONNE_LIB`";
		$res_rq_info_personne = mysql_query($rq_info_personne, $mysql_link) or die(mysql_error());
		$tab_rq_info_personne = mysql_fetch_assoc($res_rq_info_personne);
		$total_ligne_rq_info_personne=mysql_num_rows($res_rq_info_personne);
		if($total_ligne_rq_info_personne!=0){
		do {
			$CHANGEMENT_BILAN_PERSONNE_ID=$tab_rq_info_personne['CHANGEMENT_BILAN_PERSONNE_ID'];
			$CHANGEMENT_BILAN_PERSONNE_LIB=$tab_rq_info_personne['CHANGEMENT_BILAN_PERSONNE_LIB'];
			$CHANGEMENT_BILAN_CONFIG_NOM=$CHANGEMENT_BILAN_CONFIG_ID.'_'.$CHANGEMENT_BILAN_PERSONNE_ID;
			if(!isset($CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_NOM])){
	        		$CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_NOM]='0';
	        	}
$message_BILAN .='&nbsp;'.$CHANGEMENT_BILAN_PERSONNE_LIB.': '.stripslashes($CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_NOM]).'&nbsp;'."\n";
		
		} while ($tab_rq_info_personne = mysql_fetch_assoc($res_rq_info_personne));
		$ligne= mysql_num_rows($res_rq_info_personne);
		if($ligne > 0) {
		mysql_data_seek($res_rq_info_personne, 0);
		$tab_rq_info_personne = mysql_fetch_assoc($res_rq_info_personne);
		}
		}
		mysql_free_result($res_rq_info_personne);
		$rq_info_personne="
		SELECT `CHANGEMENT_BILAN_PERSONNE_ID` , `CHANGEMENT_BILAN_PERSONNE_LIB` 
		FROM `changement_bilan_personne` 
		WHERE `ENABLE` = '0'
		AND `CHANGEMENT_BILAN_PERSONNE_LIB` = 'Total'
		ORDER BY `CHANGEMENT_BILAN_PERSONNE_LIB`";
		$res_rq_info_personne = mysql_query($rq_info_personne, $mysql_link) or die(mysql_error());
		$tab_rq_info_personne = mysql_fetch_assoc($res_rq_info_personne);
		$total_ligne_rq_info_personne=mysql_num_rows($res_rq_info_personne);
		if($total_ligne_rq_info_personne!=0){
		do {
			$CHANGEMENT_BILAN_PERSONNE_ID=$tab_rq_info_personne['CHANGEMENT_BILAN_PERSONNE_ID'];
			$CHANGEMENT_BILAN_PERSONNE_LIB=$tab_rq_info_personne['CHANGEMENT_BILAN_PERSONNE_LIB'];
			$CHANGEMENT_BILAN_CONFIG_NOM=$CHANGEMENT_BILAN_CONFIG_ID.'_'.$CHANGEMENT_BILAN_PERSONNE_ID;
			if(!isset($CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_NOM])){
	        		$CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_NOM]='0';
	        	}
$message_BILAN .='&nbsp;'.$CHANGEMENT_BILAN_PERSONNE_LIB.': '.stripslashes($CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_NOM]).'&nbsp;'."\n";

		} while ($tab_rq_info_personne = mysql_fetch_assoc($res_rq_info_personne));
		$ligne= mysql_num_rows($res_rq_info_personne);
		if($ligne > 0) {
		mysql_data_seek($res_rq_info_personne, 0);
		$tab_rq_info_personne = mysql_fetch_assoc($res_rq_info_personne);
		}
		}
		mysql_free_result($res_rq_info_personne);
            break;
            case "text": 
$message_BILAN .=nl2br(stripslashes($CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_ID]))."\n";
            break;
            }
            
$message_BILAN .='&nbsp;</td>'."\n";
$message_BILAN .='</tr>'."\n";
	        	
	        } while ($tab_rq_info_bilan = mysql_fetch_assoc($res_rq_info_bilan));
	        $ligne= mysql_num_rows($res_rq_info_bilan);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_bilan, 0);
	          $tab_rq_info_bilan = mysql_fetch_assoc($res_rq_info_bilan);
	        }
	        mysql_free_result($res_rq_info_bilan);
	}

        	
        } while ($tab_rq_info_bilan_lib = mysql_fetch_assoc($res_rq_info_bilan_lib));
        $ligne= mysql_num_rows($res_rq_info_bilan_lib);
        if($ligne > 0) {
          mysql_data_seek($res_rq_info_bilan_lib, 0);
          $tab_rq_info_bilan_lib = mysql_fetch_assoc($res_rq_info_bilan_lib);
        }
        mysql_free_result($res_rq_info_bilan_lib);
        
      }
$message_BILAN .='</table>'."\n";
$message_BILAN .='</div>'."\n";
$message_BILAN .='</BR>'."\n"; 
$message_BILAN =stripslashes($message_BILAN);
}

if (empty($tab_var)){

}else{
if(empty($tab_var['btn'])){
}else{
# Cas Verification
	if ($tab_var['btn']=="Verification"){
		$MAIL_DEST=$tab_var['MAIL_DEST'];
		$MAIL_OBJET=$tab_var['MAIL_OBJET'];
		$MAIL_COMMENTAIRE=$tab_var['MAIL_COMMENTAIRE'];
		$action='Verification';
	}
# Cas Apercu
	if ($tab_var['btn']=="Apercu"){
		$MAIL_DEST=$tab_var['MAIL_DEST'];
		$MAIL_OBJET=$tab_var['MAIL_OBJET'];
		$MAIL_COMMENTAIRE=$tab_var['MAIL_COMMENTAIRE'];
		$action='Apercu';
	}
# Cas Annulation
	if ($tab_var['btn']=="Annulation"){
    echo '
    <script language="JavaScript">
    url=("./index.php?ITEM=changement_Info_Changement&action=Info&ID='.$ID.'");
    window.location=url;
    </script>
    ';
	}
# Cas Envoi
	if ($tab_var['btn']=="Envoi"){
		$MAIL_DEST=$tab_var['MAIL_DEST'];
		$MAIL_OBJET=$tab_var['MAIL_OBJET'];
		$MAIL_COMMENTAIRE=$tab_var['MAIL_COMMENTAIRE'];
		$action='Apercu';
		$Corps_mail = Constitue_corps_Message($MAIL_DEST,$MAIL_OBJET,$MAIL_COMMENTAIRE,$message_debut,$message_FAR,$message_BILAN,$message_signature);
		$Corps_mail_SVG = Constitue_corps_Message_SVG($Personne_complet,$MAIL_DEST,$MAIL_OBJET,$MAIL_COMMENTAIRE,$message_debut,$message_FAR,$message_BILAN,$message_signature);
		mailMailInfo_php($MAIL_DEST,$CHANGEMENT_ID,$Personne_email_FULL, $Personne_abrege, $objet, $Corps_mail, $TRACE_ETAT, $ENV,$mysql_link);
		sauvegarde_MailInfo( $MAIL_DEST, $Corps_mail_SVG, $CHANGEMENT_ID,$type, $TRACE_ETAT,$UTILISATEUR_ID,$ENV,$mysql_link);
		echo '
    <script language="JavaScript">
    url=("./index.php?ITEM=changement_Info_Changement&action=Info&ID='.$ID.'");
    window.location=url;
    </script>
    ';
	}	

}
}	

if($NB_MAIL==0){
  echo '
  <script language="JavaScript">
  url=("./index.php?ITEM=changement_Info_Changement&action=Info&ID='.$ID.'");
  window.location=url;
  </script>
  ';
}

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
echo '
<form name="changement_Send_Mail" method="post" action="./index.php?ITEM=changement_Send_Mail">
<center>';
if($action=='Apercu'){
echo '<table>';
}else{
echo '<table class="table_inc" cellspacing="1" cellpading="0">';
}
echo '
  <tr align="center" class="titre" class="impair">
  	<tr align="center" class="titre">
		<td align=center colspan=2 ><h2>&nbsp;[&nbsp;&nbsp;';
		echo 'Envoi du mail pour le changement n&deg; <a href="#" class="infobulledroite">'.$ID.'<span>'.$AFF_SPAN.'</span></a>';
		echo '&nbsp;]&nbsp;</h2>&nbsp;</td>
	</tr>';
	$j++;
	if ($j%2) { $class = "pair";}else{$class = "impair";} 
	echo '
	<tr class="'.$class.'">
    <td align="left">
    	Destinataire : 
    </td>
    <td align="left">';
    if($action=='Apercu'){
      $MAIL_LIB='';
          $MAIL = explode(";", $MAIL_DEST);
          $NB_TOTAL_MAIL=count($MAIL);
            for($NB_MAIL=0;$NB_MAIL < $NB_TOTAL_MAIL;$NB_MAIL++)
            {
              $MAIL_LIB.=$MAIL[$NB_MAIL].'</BR>';
            }
            echo $MAIL_LIB;
            echo '<input type="hidden" name="MAIL_DEST" size=100 value="'.$MAIL_DEST.'">';
    }else{
      echo '<input type="text" name="MAIL_DEST" size=100 value="'.$MAIL_DEST.'">';
    }
       echo '
    </td>
  </tr>';
  if($action!='Apercu'){
	$j++;
	if ($j%2) { $class = "pair";}else{$class = "impair";} 
	}
	echo '
	<tr class="'.$class.'">
    <td align="left">
    	<div id="ligne">Objet du mail : </div>
    </td>
    <td align="left">';
    if($action=='Apercu'){
      echo $MAIL_OBJET;
      echo '<input type="hidden" name="MAIL_OBJET" size=100 value="'.$MAIL_OBJET.'">';
    }else{
      echo '<input type="text" name="MAIL_OBJET" size=100 value="'.$MAIL_OBJET.'">';
    }
    echo '
    </td>
  </tr>
  ';
  if($action!='Apercu'){
	$j++;
	if ($j%2) { $class = "pair";}else{$class = "impair";} 
	}
	echo '
	<tr class="'.$class.'">
  <td colspan=2 align="left"> 
  '.$message_debut.'
  '.$message_FAR.'
  '.$message_BILAN;
  if($action=='Apercu'){
  echo nl2br(stripslashes($MAIL_COMMENTAIRE));
  echo '<input type="hidden" name="MAIL_COMMENTAIRE" size=100 value="'.stripslashes($MAIL_COMMENTAIRE).'">';
  }else{
  echo '<center><textarea name="MAIL_COMMENTAIRE" cols="70" rows="10" id="MAIL_COMMENTAIRE">'.stripslashes($MAIL_COMMENTAIRE).'</textarea></center>';
  }
  echo $message_signature.'
  </td>
  </tr>
  <tr class="titre">
  <td colspan=2 align="center">
  <input type="hidden" name="ID" value="'.$ID.'">
  <input type="hidden" name="type" value="'.$type.'">
  <input type="hidden" name="action" value="'.$action.'">
  <input type="hidden" name="TRACE_ETAT" value="'.$TRACE_ETAT.'">';
  switch ($action){
		case "Creation": 					
			echo '<input name="btn" type="submit" id="btn" value="Verification">';
			echo '<input name="btn" type="submit" id="btn" value="Annulation">';
		break;
		case "Verification": 
			echo '<input name="btn" type="submit" id="btn" value="Apercu">';
			echo '<input name="btn" type="submit" id="btn" value="Envoi">';
			echo '<input name="btn" type="submit" id="btn" value="Annulation">';
		break;
		case "Apercu": 
      echo '<input name="btn" type="submit" id="btn" value="Verification">';
      echo '<input name="btn" type="submit" id="btn" value="Envoi">';
      echo '<input name="btn" type="submit" id="btn" value="Annulation">';
		break;	
	}	
  echo '  
  </td>
  </tr>
<tr align="center" class="titre">
	<td align=center colspan=2 >&nbsp;</td>
</tr>
</table>
</center>
</form>';

mysql_close($mysql_link); 
?>