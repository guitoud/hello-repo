<?php
//redirection si acces dirrect
//if(substr_count($_SERVER['PHP_SELF'], 'index.php')==0){
//  header("Location: ../"); 
//  exit(); 
//}
$j=0;
$Mois_critere=date("m"); 
$Annee_critere=date("Y"); 
if($Mois_critere==1){
	$Mois_critere=12;
	$Annee_critere=$Annee_critere-1;
}else{
	$Mois_critere=$Mois_critere-1;
}

$DEMANDE="";
$CLE="";
$RESUME="";
$TYPE_DEMANDE="";
$ETAT="";
$MOTIF="";
$INTERVENANT="";
$MANDEUR="";
$CREATION1="";
$MAJ ="";
$RESOLUE="";
$COMPOSANT1="";
$OBSERVATEUR="";
$IMAGE="";
$ESTIM_O="";
$ESTIM_R="";
$TEMP_C="";
$RATIO="";
$SS_TACHE="";
$D_LIEE="";
$DESC="";
$N_SECU="";
$PROGRESSION="";
$SOM_PROGRES="";
$SOM_TEMP_C="";
$SOM_EST_R="";
$SOM_EST_O="";
$LIBELLE="";
$CODE_CONV="";
$CODE_APP="";
$COMPOSANT2="";
$DATE_SOUHAITEE="";
$ENT_METIER="";
$ENV_PHY="";
$ENVIRONNEMENT="";
$ID_APP="";
$FACTU="";
$ANNUL="";
$RESOL="";
$NIV_EXP="";
$NIV_SERV="";
$PERCENT_DONE="";
$REFACTUR="";
$REF_INC_CLI="";
$SITE="";
$TEMPS_JOURS="";
$TEMPS_HEURE="";
$TEMPS_MINUTES="";
$paramJourHVH = 4;

require_once("./cf/autre_fonctions.php");

// donne des information sur une date anne , mois , jour
function Return_info_date($date_en)
{
        if($date_en==''){
                $DATE='';
                $timestamp_DATE=0;
        }else{
                                $time_explode=explode(' ', $date_en);
                                $date_explode=explode('/', $time_explode[0]);
                                $mois="";

                $annee=substr($date_explode[2],0,2);
                                echo "annee : $annee\n";
                $mois_litteral=$date_explode[1];
                                echo "mois_litteral = $mois_litteral\n";
                                if ( !  is_numeric($mois_litteral))  {
                                        switch ($mois_litteral) {
                                                case "janv.":
                                                        $mois="01";
                                                        break;
                                                case "fev.":
                                                        $mois="02";
                                                        break;
                                                case "mar.":
                                                        $mois="03";
                                                        break;
                                                case "avr.":
                                                        $mois="04";
                                                        break;
                                                case "mai":
                                                        $mois="05";
                                                        break;
                                                case "juin":
                                                        $mois="06";
                                                        break;
                                                case "juil.":
                                                        $mois="07";
                                                        break;
                                                case "aout":
                                                        $mois="08";
                                                        break;
                                                case "sept.":
                                                        $mois="09";
                                                        break;
                                                case "oct.":
                                                        $mois="10";
                                                        break;
                                                case "nov.":
                                                        $mois="11";
                                                        break;
                                                case "dec.":
                                                        $mois="12";
                                                        break;
                                        }
                                }
                                else {
                                                $mois=$mois_litteral;
                                }
                                echo "mois = $mois\n";
								$jour=substr($date_explode[0],0,2);
                                echo "jour = $jour\n";
                                $hh_mm=explode(":", $time_explode[1]);
								$hh=$hh_mm[0];

                                $_heure=strlen($hh);
                                if ( $_heure=1 )  {
                                        $hh="0"."$hh";
                                }
								echo "heure = $hh\n";
                                $mm=$hh_mm[1];
                                echo "mm=$mm\n";
								$DATE='20'.$annee.''.$mois.''.$jour.''.$hh.''.$mm;
								$timestamp_DATE=mktime($hh, $mm, 00, $mois, $jour, $annee);
        }
        return array($DATE,$timestamp_DATE);
}

function Timestamp2AAAAMMJJ($date_in) {
        $aaaa = strftime("%Y", $date_in);
        $mm=strftime("%m", $date_in);
        $jj=strftime("%d", $date_in);

        return array($date_in,$aaaa,$mm,$jj);
}

function Return_minutes($TEMPS_MINUTES) {
	$TEMPS_MINUTES_OK=$TEMPS_MINUTES;
	if($TEMPS_MINUTES==''){
		$TEMPS_MINUTES_OK=15;
	}
	if($TEMPS_MINUTES<=15){
		$TEMPS_MINUTES_OK=15;
	}
	return ($TEMPS_MINUTES_OK);	
}

function Return_heures($TEMPS_HEURE) {	
	$TEMPS_HEURE_OK=$TEMPS_HEURE;
	if ($TEMPS_HEURE=='') {
		$TEMPS_HEURE_OK=0;
	}
	$TEMPS_MINUTES_OK=$TEMPS_HEURE_OK * 60;
	return ($TEMPS_MINUTES_OK);
}

function Return_jours($TEMPS_JOUR) {
	$TEMP_JOURS_OK=$TEMPS_JOUR;
	if ($TEMPS_JOUR=='') {
		$TEMPS_JOURS_OK=0;
	}
	
	$TEMPS_MINUTES_OK=$TEMPS_JOUR * 1440 ;
	return ($TEMPS_MINUTES_OK);
}

// donne l'acteur et si sogeti
function Return_acteur($INTERVENANT,$mysql_link) {
	if($INTERVENANT=='') {
		$total_ligne_rq_info=0;
	} else {
	$liste = explode(' ', $INTERVENANT); // lecture du s&eacute;parateur ;
	$PRENOM=substr(strtoupper($liste[0]),0,1);
	$NOM=strtoupper($liste[1]);
	$liste = explode('-', $PRENOM); // lecture du s&eacute;parateur ;
	$PRENOM=strtoupper($liste[0]);
	$rq_info="
	SELECT LEFT(UPPER(`LOGIN`),3) AS `LOGIN` ,UPPER(`SOCIETE`) AS `SOCIETE`	
	FROM `moteur_utilisateur` 	
	WHERE UPPER(`NOM`) like '".$NOM."%' AND UPPER(`PRENOM`) LIKE '".$PRENOM."%'	LIMIT 1";
	$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
	$tab_rq_info = mysql_fetch_assoc($res_rq_info);
	$total_ligne_rq_info=mysql_num_rows($res_rq_info);
	mysql_free_result($res_rq_info);

		if($total_ligne_rq_info==0){
			$SOGETI="N";
		} else {
			if($tab_rq_info['SOCIETE']=='SOGETI') {
				$SOGETI="Y";
			} else {
				$SOGETI="N";
			}
		}
	return array($INTERVENANT,$SOGETI);
	}
}

function Return_status($STATUS,$mysql_link)
{
	$rq_info="
	SELECT `INDICATEUR_STATUS_INFO` 
	FROM `indicateur_darwin_etat` 
	WHERE `INDICATEUR_STATUS_LIB`='".$STATUS."'
	LIMIT 1";
	$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
	$tab_rq_info = mysql_fetch_assoc($res_rq_info);
	$total_ligne_rq_info=mysql_num_rows($res_rq_info);
	mysql_free_result($res_rq_info);
	if($total_ligne_rq_info==0){
		$rq_info="
		SELECT `INDICATEUR_STATUS_INFO` 
		FROM `indicateur_status` 
		WHERE `INDICATEUR_STATUS_LIB`='ACTION INCONNUE'
		LIMIT 1";
		$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
		$tab_rq_info = mysql_fetch_assoc($res_rq_info);
		$total_ligne_rq_info=mysql_num_rows($res_rq_info);
		mysql_free_result($res_rq_info);
		$STATUS=$tab_rq_info['INDICATEUR_STATUS_INFO'];
	}else{
		$STATUS=$tab_rq_info['INDICATEUR_STATUS_INFO'];
	}
	return $STATUS;
}
// Donne l'action 
function Return_action($RESUME,$MOT_CLES,$mysql_link)
{
	$ACTION='';
	$liste = explode(' ', $RESUME); // lecture du s&eacute;parateur ;
	$ACTION_test=$liste[0];
	$rq_info="
	SELECT `INDICATEUR_ACTION_ID`,`INDICATEUR_ACTION_INFO`,`INDICATEUR_ACTION_INFO_NEW`
	FROM `indicateur_action` 
	WHERE `INDICATEUR_ACTION_LIB`='".$ACTION_test."'
	AND `INDICATEUR_ACTION_TYPE`='RESUME'
	LIMIT 1";
	$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
	$tab_rq_info = mysql_fetch_assoc($res_rq_info);
	$total_ligne_rq_info=mysql_num_rows($res_rq_info);
	mysql_free_result($res_rq_info);
	if($total_ligne_rq_info!=0){
		$ACTION=$tab_rq_info['INDICATEUR_ACTION_INFO'];
		$ACTION_NEW=$tab_rq_info['INDICATEUR_ACTION_INFO_NEW'];
		$ACTION_ID=$tab_rq_info['INDICATEUR_ACTION_ID'];
	}
	if($ACTION==''){
		$rq_info="
		SELECT `INDICATEUR_ACTION_ID`,`INDICATEUR_ACTION_LIB`, `INDICATEUR_ACTION_INFO`,`INDICATEUR_ACTION_INFO_NEW`
		FROM `indicateur_action`
		WHERE `INDICATEUR_ACTION_TYPE`='MOT_CLE'
		ORDER BY `INDICATEUR_ACTION_ORDRE` ASC"; 
	        $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
	        $tab_rq_info = mysql_fetch_assoc($res_rq_info);
	        $total_ligne_rq_info=mysql_num_rows($res_rq_info);
	        do {
	        	$LIB=$tab_rq_info['INDICATEUR_ACTION_LIB'];
	        	$ACTION_TEST=$tab_rq_info['INDICATEUR_ACTION_INFO'];
	        	$ACTION_NEW=$tab_rq_info['INDICATEUR_ACTION_INFO_NEW'];
	        	$ACTION_ID=$tab_rq_info['INDICATEUR_ACTION_ID'];
	        	$test=substr_count(strtoupper($RESUME), strtoupper($LIB))+substr_count(strtoupper($MOT_CLES), strtoupper($LIB));
	        	if($test>0){
	        		$ACTION=$ACTION_TEST;
	        		break;
	        	}
	        } while ($tab_rq_info = mysql_fetch_assoc($res_rq_info));
	        $ligne= mysql_num_rows($res_rq_info);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info, 0);
	          $tab_rq_info = mysql_fetch_assoc($res_rq_info);
	        }
	        mysql_free_result($res_rq_info);
	}
        if($ACTION==''){
		$rq_info="
			SELECT `INDICATEUR_ACTION_ID`,`INDICATEUR_ACTION_INFO` ,`INDICATEUR_ACTION_INFO_NEW`
			FROM `indicateur_darwin_action` 
			WHERE `INDICATEUR_ACTION_LIB`='ACTION INCONNUE' 
			AND `INDICATEUR_ACTION_TYPE`='RESUME'
			LIMIT 1";
		$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
		$tab_rq_info = mysql_fetch_assoc($res_rq_info);
		$total_ligne_rq_info=mysql_num_rows($res_rq_info);
		$ACTION=$tab_rq_info['INDICATEUR_ACTION_INFO'];
		$ACTION_NEW=$tab_rq_info['INDICATEUR_ACTION_INFO_NEW'];
		$ACTION_ID=$tab_rq_info['INDICATEUR_ACTION_ID'];
		mysql_free_result($res_rq_info);
	}
	return array($ACTION,$ACTION_NEW,$ACTION_ID);
}



// donne le poids en valeur
function Return_poids_valeur($REFACTURATION,$mysql_link) {
	if($REFACTURATION=='') {
		$total_ligne_rq_info=0;
	} else {
		$rq_info="
		SELECT `INDICATEUR_REGLE_POIDS` 
		FROM `indicateur_regles` 
		WHERE `INDICATEUR_REGLE_TYPE`='DARWIN' AND 
		UPPER(`INDICATEUR_REGLE_INFO`)=UPPER('".$REFACTURATION."') AND 
		`ENABLE`=0
		LIMIT 1";
		$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
		$tab_rq_info = mysql_fetch_assoc($res_rq_info);
		$total_ligne_rq_info=mysql_num_rows($res_rq_info);
		mysql_free_result($res_rq_info);
	}
	if($total_ligne_rq_info==0) {
		$VALEUR=0;
	} else {
		$VALEUR=$tab_rq_info['INDICATEUR_REGLE_POIDS'];
	}
	return $VALEUR;
}
// donne le poids et le niveau
function Return_NIVEAU_POIDS($DUREE,$mysql_link) {
	if($DUREE==0) {
		$NIVEAU='VIDE';
		$POIDS=0;
	} else {
		$rq_complex_info="SELECT INDICATEUR_DUREE_ID, 
		INDICATEUR_DUREE_LIB, 
		INDICATEUR_DUREE_POIDS,
		INDICATEUR_DUREE_TEMPS_MINI, 
		INDICATEUR_DUREE_TEMPS_MAX, 
		ENABLE FROM `indicateur_duree` WHERE '".$DUREE."' >= `INDICATEUR_DUREE_TEMPS_MINI` AND '".$DUREE."' < `INDICATEUR_DUREE_TEMPS_MAX` AND `ENABLE` =0 LIMIT 1 
		";
		$res_rq_complex_info = mysql_query($rq_complex_info, $mysql_link) or die(mysql_error());
		$tab_rq_complex_info = mysql_fetch_assoc($res_rq_complex_info);
		$total_ligne_rq_complex_info=mysql_num_rows($res_rq_complex_info);
		if($total_ligne_rq_complex_info==0) {
			$NIVEAU='VIDE';
			$POIDS=0;
		} else {
			$NIVEAU=$tab_rq_complex_info['INDICATEUR_DUREE_LIB'];
			$POIDS=$tab_rq_complex_info['INDICATEUR_DUREE_POIDS'];
		}
		mysql_free_result($res_rq_complex_info);
	}
	return array($NIVEAU,$POIDS);
}

function Return_application($RESUME, $mysql_link) { 
		
	//$RESUME="PROD-199 TRAIT.EXCEPT.LUP Z CL9 CARRIERES LONGUES";
	preg_match('/ ([A-Z0-9]{3}) /', $RESUME, $APPLICATION);
	foreach ($APPLICATION as $app ) { 
		$appt=trim($app);
		$sql="select distinct(`INDICATEUR_APPLICATION_REF`) as APPLICATION from indicateur_application  where `INDICATEUR_APPLICATION_REF`='".$appt."' ;" ;
		echo "$sql\n";
		$res_rq_info=mysql_query($sql, $mysql_link) or die('Erreur SQL !'.$sql.''.mysql_error());
		$tab_rq_info = mysql_fetch_assoc($res_rq_info);
		$total_ligne_rq_info=mysql_num_rows($res_rq_info);
		print "$total_ligne_rq_info\n";
		mysql_free_result($res_rq_info);
		//print_r($tab_rq_info);
	}

	$_appR=$tab_rq_info['APPLICATION'];

	if ($total_ligne_rq_info < 1 ) {
			$_application="";
	}
	return array($RESUME,$_appR);
}

// donne l'appli et le processus
function Return_app_proc($APPLICATION_PROCESSUS,$mysql_link) {
	if($APPLICATION_PROCESSUS=='') {
		$APPLICATION='';
		$PROCESSUS='';
	} else {
		if(substr_count($APPLICATION_PROCESSUS, '-')==0) {
			$APPLICATION=$APPLICATION_PROCESSUS;
			$PROCESSUS='';
		} else {
			$liste = explode('-', $APPLICATION_PROCESSUS); // lecture du s&eacute;parateur ;
			$APPLICATION=strtoupper($liste[0]);
			$PROCESSUS=strtoupper($liste[1]);
		}
	}
	return array($APPLICATION,$PROCESSUS);
}

if (empty($_POST)) {
} else {
	if(!empty($_POST['execution'])) {
		$ANNEE=$_POST['Annee'];
		$MOIS=$_POST['Mois'];
		$Mois_critere=$MOIS; 
		$Annee_critere=$ANNEE; 
		$AMMEE_MIN=$ANNEE;
		$AMMEE_MAX=$ANNEE;
		$MOIS_MIN=$MOIS-1;
		$MOIS_MAX=$MOIS+1;
		if($MOIS==1) {
			$MOIS_MIN=12;
			$AMMEE_MIN=$ANNEE-1;
		}
		if($MOIS==12) {
			$MOIS_MAX=1;
			$AMMEE_MAX=$ANNEE+1;
		}
		if($MOIS<10){
			$MOIS='0'.$MOIS;		
		}
		if($MOIS_MIN<10){
			$MOIS_MIN='0'.$MOIS_MIN;		
		}
		if($MOIS_MAX<10){
			$MOIS_MAX='0'.$MOIS_MAX;		
		}
		$DATE_INDICATEUR=$ANNEE.$MOIS;
		$DATE_MIN=$AMMEE_MIN.$MOIS_MIN;
		$DATE_MAX=$AMMEE_MAX.$MOIS_MAX;
		
		// on supprimme les donn&eacute;es si l'on a deja fait un calcul		
		$sql="DELETE FROM `indicateur_darwin_extract_archive` WHERE `DATE_INDICATEUR` ='".$DATE_INDICATEUR."'";
		mysql_query($sql, $mysql_link) or die('Erreur SQL !'.$sql.''.mysql_error());
		$OptTable=mysql_query("OPTIMIZE TABLE `indicateur_darwin_extract_archive `",$mysql_link);
		
		$sql="DELETE FROM `indicateur_darwin_calcul` WHERE `DATE_INDICATEUR` ='".$DATE_INDICATEUR."'";
		mysql_query($sql, $mysql_link) or die('Erreur SQL !'.$sql.''.mysql_error());
		$OptTable=mysql_query("OPTIMIZE TABLE `indicateur_darwin_calcul`",$mysql_link);
		
		// on traite l import
		$rq_info_extract="SELECT * FROM `indicateur_darwin_extract_brut`"; 
		$res_rq_info_extract = mysql_query($rq_info_extract, $mysql_link) or die(mysql_error());
        $tab_rq_info_extract = mysql_fetch_assoc($res_rq_info_extract);
        $total_ligne_rq_info_extract=mysql_num_rows($res_rq_info_extract); 
          if ($total_ligne_rq_info_extract==0){
            $erreur="Pas d\'information dans la base.";
          }else{
          do {
				/*							
				COINCOIN		 
				*/ 

			$ID=addslashes($tab_rq_info_extract['ID']);
			$DEMANDE=addslashes($tab_rq_info_extract['Demande']);
            $CLE=addslashes($tab_rq_info_extract['Cle']);
            $RESUME=addslashes($tab_rq_info_extract['Resume']);
            $TYPE=addslashes($tab_rq_info_extract['Type']);
			$PRIORITE=addslashes($tab_rq_info_extract['Priorite'];
            $ETAT=addslashes($tab_rq_info_extract['Etat']);
            $MOTIF=addslashes($tab_rq_info_extract['Motif']);
            $INTERVENANT=addslashes($tab_rq_info_extract['Intervenant']);
            $DEMANDEUR=addslashes($tab_rq_info_extract['Demandeur']);
            $CREATION1=addslashes($tab_rq_info_extract['Creation']);
            $MAJ=addslashes($tab_rq_info_extract['Mise a jour']); 
			$RESOLUE=addslashes($tab_rq_info_extract['Resolue']);
			$COMPOSANT1=addslashes($tab_rq_info_extract['Composant']);
			$OBSERVATEUR=addslashes($tab_rq_info_extract['Observateurs']);
			$IMAGE=addslashes($tab_rq_info_extract['Images']);
			$ESTIM_O=addslashes($tab_rq_info_extract['Estimation originale']);
			$ESTIM_R=addslashes($tab_rq_info_extract['Estimation restante']);
			$TEMP_C=addslashes($tab_rq_info_extract['Temps consacre']);
			$RATIO=addslashes($tab_rq_info_extract['Ratio du travail reel compare a estimation']);
			$SS_TACHE=addslashes($tab_rq_info_extract['Sous-taches']);
			$D_LIEE=addslashes($tab_rq_info_extract['Demandes liees']);
			$DESC=addslashes($tab_rq_info_extract['Descriptif']);
			$N_SECU=addslashes($tab_rq_info_extract['Niveau de securite']);
			$PROGRESSION=addslashes($tab_rq_info_extract['Progression']);
			$SOM_PROGRES=addslashes($tab_rq_info_extract['S Temps consacre']);
			$SOM_TEMP_C=addslashes($tab_rq_info_extract['S Temps consacre']);
			$SOM_EST_R=addslashes($tab_rq_info_extract['S Estimation restante']);
			$SOM_EST_O=addslashes($tab_rq_info_extract['S Estimation originale']);
			$LIBELLE=addslashes($tab_rq_info_extract['Libelle']);
			$CODE_CONV=addslashes($tab_rq_info_extract['Code Convention']);
			$CODE_APP=addslashes($tab_rq_info_extract['Code application']);
			$COMPOSANT2=addslashes($tab_rq_info_extract['Composant']);
			$DATE_SOUHAITEE=addslashes($tab_rq_info_extract['Date souhaitee']);
			$ENV_METIER=addslashes($tab_rq_info_extract['Entite metier']);
			$ENV_PHY=addslashes($tab_rq_info_extract['Env. physique']);
			$ENVIRONNEMENT=addslashes($tab_rq_info_extract['Environnement']);
			$ID_APP=addslashes($tab_rq_info_extract['ID application']);
			$FACTU=addslashes($tab_rq_info_extract['Facturation']);
			$ANNUL=addslashes($tab_rq_info_extract['Annulation']);
			$RESOL=addslashes($tab_rq_info_extract['Resolution']);
			$NIV_EXP=addslashes($tab_rq_info_extract['Niveau d expertise']);
			$NIV_SERV=addslashes($tab_rq_info_extract['Niveau de service']);
			$REFACTUR=addslashes($tab_rq_info_extract['Refacturation']);
			$REF_INC_CLI=addslashes($tab_rq_info_extract['RefIncidentclient']);
			$SITE=addslashes($tab_rq_info_extract['Site']);
			$TEMPS_JOURS=addslashes($tab_rq_info_extract['TempsJours']);
			$TEMPS_HEURE=addslashes($tab_rq_info_extract['TempsHeure']);
			$TEMPS_MINUTES=addslashes($tab_rq_info_extract['TempsMinutes']);
			$ENABLE=addslashes($tab_rq_info_extract['ENABLE']);
			
			
            ########## Bloc De sauvegarde dans la base ########### 

            list($CREATION1,$TS_CREATION) = Return_info_date($CREATION1);
			list($MAJ,$TS_MAJ) = Return_info_date($MAJ);
				list($RESOLUE,$TS_RESOLUE) = Return_info_date($RESOLUE);
				list($DATE_SOUHAITEE,$TS_SOUHAITEE) = Return_info_date($DATE_SOUHAITEE);
			
				list($TS_CREATION,$DATE_A_CREATION,$DATE_M_CREATION,$DATE_J_CREATION)=Timestamp2AAAAMMJJ($TS_CREATION);
				list($TS_CREATION,$DATE_A_MAJ,$DATE_M_MAJ,$DATE_J_MAJ)=Timestamp2AAAAMMJJ($TS_MAJ);
				list($TS_CREATION,$DATE_A_RESOLUE,$DATE_M_RESOLUE,$DATE_J_RESOLUE)=Timestamp2AAAAMMJJ($TS_RESOLUE);
				list($TS_CREATION,$DATE_A_SOUHAITEE,$DATE_M_SOUHAITEE,$DATE_J_SOUHAITEE)=Timestamp2AAAAMMJJ($TS_SOUHAITEE);
			
				//list($APPLICATION,$PROCESSUS) = Return_app_proc($APPLICATION_PROCESSUS,$mysql_link);// J'en suis la 
				$APPLICATION=$CODE_APP;
				if ( $APPLICATION="" ) {
					list($RESUME,$APPLICATION)=Return_application($RESUME,$mysql_link);
				}

				// calcul 
				$TEMPS_MINUTE_OK = Return_jours($TEMPS_JOURS) + Return_heures($TEMPS_HEURE) +  $TEMPS_MINUTES;
				$TEMPS_MINUTE_OK = Return_minutes($TEMPS_MINUTES_OK);
			
				$DATE_TEST=$DATE_A_SOUHAITEE.$DATE_M_SOUHAITEE;
			
				if($DATE_A_SOUHAITEE==''){
					$DATE_TEST=$DATE_A_CREATION.$DATE_M_CREATION;
				}
				if($DATE_TEST >= $DATE_MIN){
					if($DATE_TEST <= $DATE_MAX){
						$sql="
						INSERT INTO `indicateur_darwin_extract_archive` (	
							`ID`, 		`Demande`, 		`Cle`, 		`Resume`, 			`Type`, 	`Priorite`,		`Etat`, 		`Motif`, 		`Intervenant`,			 `Demandeur`,		 `Creation`,			 `Mise a jour`, `Resolue`, 	`Composants`, 			`Observateurs`, 				`Images`, 		`Estimation originale`, `Estimation restante`, `Temps consacre`, `Ratio du travail reel compare a estimation`, `Sous-taches`, `Demandes liees`, `Descriptif`, `Niveau de securite`, `Progression`, `S Progres`, `S Temps consacre`, `S Estimation restante`, `S Estimation originale`, `libelle`, `Code Convention`, `Code application`, `Composant`, `Date souhaitee`, `Entite metier`, `Env. physique`, `Environnement`, `ID application`, `Facturation`, `Annulation`, `Resolution`, `Niveau d expertise`, `Niveau de service`, `Refacturation`, `RefIncidentclient`, `Site`, `TempsJours`, `TempsHeure`, `TempsMinutes`, `date_indicateur`, `ENABLE` ) 
						VALUES 
							( NULL, '".$DEMANDE."', '".$CLE."', '".$RESUME."', '".$TYPE_DEMANDE."', '".$PRIORITE."', '".$ETAT."', '".$MOTIF."', '".$INTERVENANT."', '".$DEMANDEUR."', '".$CREATION1."', '".$MAJ."', '".$RESOLUE."', '".$COMPOSANT1."', '".$OBSERVATEUR."', '".$IMAGE."', '".$ESTIM_O."', '".$ESTIM_R."', '".$TEMP_C."', '".$RATIO."', '".$SS_TACHE."', '".$D_LIEE."', '".$DESC."', '".$N_SECU."', '".$PROGRESSION."', '".$SOM_PROGRES."', '".$SOM_TEMP_C."', '".$SOM_EST_R."', '".$SOM_EST_O."', '".$LIBELLE."', '".$CODE_CONV."', '".$CODE_APP."', '".$COMPOSANT2."', '".$DATE_SOUHAITEE."', '".$ENT_METIER."', '".$ENV_PHY."', '".$ENVIRONNEMENT."', '".$ID_APP."', '".$FACTU."', '".$ANNUL."', '".$RESOL."', '".$NIV_EXP."', '".$NIV_SERV."', '".$REFACTUR."', '".$REF_INC_CLI."', '".$SITE."', '".$TEMPS_JOURS."', '".$TEMPS_HEURE."', '".$TEMPS_MINUTES."', '".$DATE_INDICATEUR."', 0 );
						";
						mysql_query($sql, $mysql_link) or die('Erreur SQL !'.$sql.''.mysql_error());

						##########  Bloc De traitement des informations  ###########			   
						// on traite l intervenant
						list($INTERVENANT,$SOGETI)=Return_acteur($INTERVENANT,$mysql_link);
						// J'en suis la
						$ACTION=Return_action($ACTION,$mysql_link);
						
						// on traite la valeur
						$VALEUR=Return_poids_valeur($REFACTURATION,$mysql_link);
						// on traite le poids et le niveau
							list($NIVEAU,$POIDS)=Return_NIVEAU_POIDS($TEMPS_MINUTES_OK,$mysql_link);
						//  	
						 //echo 'NUMERO : '.$NUMERO.' - ACTEUR : '.$ACTEUR.' - SOGETI : '.$SOGETI.' - DATE_PREVUE : '.$DATE_PREVUE.'</BR>';

							  // echo '</BR>';
							   $sql="INSERT INTO `indicateur_darwin_calcul` 
							   (`ID` ,`NUMERO` ,`STATUS` ,`AFFECTE` ,`SOGETI` ,`DATE_SOUHAITEE` ,`APPLICATION` ,`PROCESSUS` ,`DATE_PREVUE` ,`DATE_TRANSMISE` ,`DEMANDEUR` ,`ENVIRONNEMENT` ,`NATURE` ,`RECETTE` ,`TEMPS_JOURS` ,`TEMPS_MINUTES` ,`TEMPS_MINUTES_OK` ,`VALEUR`,`POIDS`,`NIVEAU` ,`DATE_INDICATEUR` ) 
							   VALUES 
							   (NULL , '".$NUMERO."', '".$STATUS."', '".$ACTEUR."', '".$SOGETI."', '".$DATE_SOUHAITEE."', '".$APPLICATION."', '".$PROCESSUS."', '".$DATE_PREVUE."', '".$DATE_TRANSMISE."', '".$DEMANDEUR."', '".$ENVIRONNEMENT."', '".$NATURE."', '".$RECETTE."', '".$TEMPS_JOURS."', '".$TEMPS_MINUTES."', '".$TEMPS_MINUTES_OK."','".$VALEUR."','".$POIDS."','".$NIVEAU."' , '".$DATE_INDICATEUR."');
							   ";
							   //echo $sql.'<BR>';
							   mysql_query($sql, $mysql_link) or die('Erreur SQL !'.$sql.''.mysql_error());
							}
				}
			} while ($tab_rq_info_extract = mysql_fetch_assoc($res_rq_info_extract));
				$ligne= mysql_num_rows($res_rq_info_extract);
				if($ligne > 0) {
					mysql_data_seek($res_rq_info_extract, 0);
					$tab_rq_info_extract = mysql_fetch_assoc($res_rq_info_extract);
				}
			}
        mysql_free_result($res_rq_info_extract);
        $OptTable=mysql_query("OPTIMIZE TABLE `indicateur_darwin_calcul`",$mysql_link);
        
        echo '
        <script language="JavaScript">
        url=("./test_darwin_menu.php?ITEM=indicateur_menu");
        window.location=url;
        </script>
        ';
	}
}
echo '
<center>
<form method="POST" action="./index.php?ITEM=indicateur_darwin_traitement">
<table class="table_inc" cellspacing="0" cellpading="0">
  <tr align="center" class="titre">
    <td align="center" colspan="2">&nbsp;</td>
  </tr>
  <tr align="center" class="titre">
    <td align="center" colspan="2">&nbsp;traitement des indicateurs DARWIN&nbsp;</td>
  </tr>
  <tr align="center" class="titre">
    <td align="center" colspan="2">&nbsp;</td>
  </tr>
  <tr align="center" class="impair">
    <td align="center">
       <u>Ann&eacute;e</u> : 
       <select name="Annee" id="Annee" value="">';
       $Annee_temp=$Annee_critere-4;
       for ($k=0;$k<=4; $k++)
       {
	       echo '<option '; if($Annee_critere == $Annee_temp){echo "selected ";} echo 'value="'.$Annee_temp.'">'.$Annee_temp.'</option>';
	       $Annee_temp=$Annee_temp+1;	
       }
         echo '
       </select>
    </td>
    <td align="center"><u>Mois</u> : 
       <select name="Mois" id="Mois" value="">
        ';
           // gestion de l'affichage par defaut dans la liste deroulante du mois en cours	
           
           for ($k=0; $k<sizeof($Tab_des_Mois); $k++)
           {
             $m=$k+1;
               echo '<option '; if($Mois_critere == $m){echo "selected ";} echo 'value="'.$m.'">'.$Tab_des_Mois[$k].'</option>'."\n";
           }
         echo '
       </select>
     </td>
  </tr>
  <tr align="center" class="titre">
    <td align="center" colspan="2">&nbsp;</td>
  </tr>
  <tr align="center" class="titre">
    <td align="center" colspan="2">&nbsp;<input type="submit" name="execution" value="Execution du traitement">&nbsp;</td>
  </tr>
  <tr align="center" class="titre">
    <td align="center" colspan="2">&nbsp;</td>
  </tr>
</table>     
</form>
</center>
';
mysql_close($mysql_link);
?>
