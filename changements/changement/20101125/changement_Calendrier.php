<?PHP
//redirection si acces dirrect
if(substr_count($_SERVER['PHP_SELF'], 'index.php')==0){
  header("Location: ../");
  exit(); 
}
/*******************************************************************
   Interface calendrier
   Version 1.0.0  
  27/09/2010 - VGU - Creation fichier
**************************************************Guibert Vincent***
*******************************************************************/

require("./cf/conf_outil_icdc.php"); 

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
	(`moteur_role`.`ROLE`='ROOT' OR `moteur_role`.`ROLE`='ADMIN-CHANGEMENT') AND
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

function Return_info_semaine($ANNEE,$MOIS,$SEMAINE,$mysql_link)
{
	if(strlen($MOIS) == 1 ){
		$MOIS='0'.$MOIS;
	}
	
        $rq_info="
        SELECT `CHANGEMENT_SEMAINE` 
        FROM `changement_date` 
        WHERE `CHANGEMENT_DATE` LIKE '".$ANNEE."%' 
        AND `CHANGEMENT_SEMAINE` ='".$SEMAINE."' 
        AND `ENABLE`='0'
        GROUP BY `CHANGEMENT_SEMAINE`, `CHANGEMENT_ID`
        ";
        //echo $rq_info.'</BR>';
        $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
        $tab_rq_info = mysql_fetch_assoc($res_rq_info);
        $total_ligne_rq_info=mysql_num_rows($res_rq_info);
        mysql_free_result($res_rq_info);
        if($total_ligne_rq_info!=0){
        	$NB=$total_ligne_rq_info;
        }else{
        	$NB=0;
        }
       
        return $NB;
}
function Return_info_jour($ANNEE,$MOIS,$JOURS,$SEMAINE,$CHANGEMENT_ID,$mysql_link)
{
	if(strlen($JOURS) == 1 ){
		$JOURS='0'.$JOURS;
	}
	if(strlen($MOIS) == 1 ){
		$MOIS='0'.$MOIS;
	}
	if($CHANGEMENT_ID==''){
		$CHANGEMENT_ID_SQL='';
	}else{
		$CHANGEMENT_ID_SQL="AND `CHANGEMENT_ID` ='".$CHANGEMENT_ID."'";
	}
        $rq_info="
        SELECT *
        FROM `changement_date`
        WHERE 
        `CHANGEMENT_DATE` LIKE '".$ANNEE."".$MOIS."".$JOURS."'
        AND `CHANGEMENT_SEMAINE` ='".$SEMAINE."'
        ".$CHANGEMENT_ID_SQL."
        AND `ENABLE`='0'";
        //echo $rq_info.'</BR>';
        $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
        $tab_rq_info = mysql_fetch_assoc($res_rq_info);
        $total_ligne_rq_info=mysql_num_rows($res_rq_info);
        mysql_free_result($res_rq_info);
        if($total_ligne_rq_info!=0){
        	$NB=$total_ligne_rq_info;
        }else{
        	$NB="";
        }
       
        return $NB;
}

function calendrier($ROLE,$MOIS_CAL,$ANNEE_CAL,$date_premier_j_mois,$mois_avant_url,$annee_avant_url,$mois_apres_url,$annee_apres_url,$mysql_link)
{
$numero_mois = $MOIS_CAL;
$annee = $ANNEE_CAL;
$numLigne=0;
// preparation de la table des couleurs
$rq_info="
SELECT COUNT(`CALENDRIER_COULEUR_ID`) AS `NB` 
FROM `calendrier_couleur`
WHERE `ENABLE`='0'
";
$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
$tab_rq_info = mysql_fetch_assoc($res_rq_info);
$total_ligne_rq_info=mysql_num_rows($res_rq_info);
$NB_COULEUR=$tab_rq_info['NB'];
mysql_free_result($res_rq_info);

$COULEUR_FOND = array();
$COULEUR_TEXTE = array();
$rq_info="
SELECT `CALENDRIER_COULEUR_FOND` , `CALENDRIER_COULEUR_TEXTE` 
FROM `calendrier_couleur` 
WHERE `ENABLE` = '0'
";
$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
$tab_rq_info = mysql_fetch_assoc($res_rq_info);
$total_ligne_rq_info=mysql_num_rows($res_rq_info);

do {
	$COULEUR_FOND[]=$tab_rq_info['CALENDRIER_COULEUR_FOND'];
	$COULEUR_TEXTE[]=$tab_rq_info['CALENDRIER_COULEUR_TEXTE'];
} while ($tab_rq_info = mysql_fetch_assoc($res_rq_info));
$ligne= mysql_num_rows($res_rq_info);
if($ligne > 0) {
	mysql_data_seek($res_rq_info, 0);
	$tab_rq_info = mysql_fetch_assoc($res_rq_info);
}
mysql_free_result($res_rq_info);

$m = array("01" => "Janvier", "02" => "F&eacute;vrier", "03" => "Mars", "04" => "Avril");
$m += array("05" => "Mai", "06" => "Juin", "07" => "Juillet", "08" => "Août");
$m += array("09" => "Septembre", "10" => "Octobre", "11" => "Novembre", "12" => "D&eacute;cembre");

$JOURS_autre_mois = array('Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi');
// Souvenez-vous que les dates en PHP commencent par dimanche !


if($numero_mois == 12)
{
	// Dans le cas du mois de d&eacute;cembre
	$annee_avant = $annee;
	$annee_apres = $annee + 1; // L'ann&eacute;e d'après change
	$mois_avant = $numero_mois - 1;
	$mois_apres = 01; // Le mois d'après est janvier
	if(strlen($mois_avant) == 1 ){
		$mois_avant='0'.$mois_avant;
	}
	if(strlen($mois_apres) == 1 ){
		$mois_apres='0'.$mois_apres;
	}
}elseif($numero_mois == 01){
	// Dans le cas du mois de janvier
	$annee_avant = $annee - 1; // L'ann&eacute;e d'avant change
	$annee_apres = $annee;
	$mois_avant = 12; // Le mois d'avant est d&eacute;cembre
	$mois_apres = $numero_mois + 1;
	if(strlen($mois_avant) == 1 ){
		$mois_avant='0'.$mois_avant;
	}
	if(strlen($mois_apres) == 1 ){
		$mois_apres='0'.$mois_apres;
	}
}else{
	// Il ne s'agit ni de janvier ni de d&eacute;cembre
	$annee_avant = $annee;
	$annee_apres = $annee;
	$mois_avant = $numero_mois - 1;
	$mois_apres = $numero_mois + 1;
	if(strlen($mois_avant) == 1 ){
		$mois_avant='0'.$mois_avant;
	}
	if(strlen($mois_apres) == 1 ){
		$mois_apres='0'.$mois_apres;
	}
}

$numero_jour1er = date('w', $date_premier_j_mois); // 0 => Dimanche, 1 => Lundi, 2 = > Mardi...

// Changement du num&eacute;ro du jour car l'array commence &agrave; l'indice 0.
if ($numero_jour1er == 0)
{
	//si c'est dimanche, on le place en 6e position (car on commencera notre boucle &agrave; 0)
	$numero_jour1er = 6;
}else{
	// Sinon on met lundi &agrave; 0 ou mardi &agrave; 1 ou mercredi &agrave; 2...
	$numero_jour1er--;
}
echo '
<table class="table_inc" >
  <tr align="center" class="titre">
    <td align="center" colspan="8">
    &nbsp;';
	// Lien pour aller au mois pr&eacute;c&eacute;dent
	echo '<a href="./index.php?ITEM=changement_Calendrier&amp;m='.$mois_avant_url.'&amp;y='.$annee_avant_url.'"><<</a>';
	
	// Affichage du mois et de l'ann&eacute;e
	echo '<span style="color: #000000;"> '.$m[$numero_mois].' '.$annee.'</span> ';
	
	// Lien pour aller au mois suivant
	echo '<a href="./index.php?ITEM=changement_Calendrier&amp;m='.$mois_apres_url.'&amp;y='.$annee_apres_url.'">>></a>';
    echo'
    &nbsp;
    </td>
  </tr>
<tr align="center" class="titre">
  <td>&nbsp;</td>
  <td>&nbsp;Lu&nbsp;</td>
  <td>&nbsp;Ma&nbsp;</td>
  <td>&nbsp;Me&nbsp;</td>
  <td>&nbsp;Je&nbsp;</td>
  <td>&nbsp;Ve&nbsp;</td>
  <td>&nbsp;Sa&nbsp;</td>
  <td>&nbsp;Di&nbsp;</td>
</tr>
';

$JOURS_autre_mois=date('t',mktime(0, 0, 0, $mois_avant, 1, $annee_avant))-$numero_jour1er+1;
$numLigne = $numLigne + 1;
if ($numLigne%2) { $class = "pair";}else{$class = "impair";} 
echo '<tr class="'.$class.'">'; // Nouvelle ligne du tableau (celle de la 1ère semaine, donc)
// &eacute;criture de colonnes vides tant que le mois ne d&eacute;marre pas.
$SEMAINE=date('W',mktime(0, 0, 0, $numero_mois, 1, $annee));
echo '<td><b>&nbsp;S'.$SEMAINE.'&nbsp;</b></td>';
$DATE_DU_JOUR=date("Ymd");
for($jour_du_mois = 0 ; $jour_du_mois < $numero_jour1er ; $jour_du_mois++)
{
	
	if(strlen($mois_avant) == 1 ){
		$mois_avant='0'.$mois_avant;
	}
	if(strlen($JOURS_autre_mois) == 1 ){
		$JOURS_autre_mois='0'.$JOURS_autre_mois;
	}
	$DATE_URL=$annee_avant.''.$mois_avant.''.$JOURS_autre_mois;
	if($DATE_URL > $DATE_DU_JOUR){
		echo '<td>&nbsp;<a class="LinkDef" href="./index.php?ITEM=changement_Ajout_Changement&action=Ajout&ANNEE='.$annee_avant.'&MOIS='.$mois_avant.'&JOUR='.$JOURS_autre_mois.'">'.$JOURS_autre_mois.'</a>&nbsp;</td>';
	}else{
		if($DATE_URL == $DATE_DU_JOUR ){
			echo '<td bgcolor="#FF0000"><FONT COLOR="#FFFFFF">&nbsp;'.$JOURS_autre_mois.'&nbsp;</FONT></td>';
		}else{
			echo '<td>&nbsp;'.$JOURS_autre_mois.'&nbsp;</td>';
		}
	}
	$JOURS_autre_mois = $JOURS_autre_mois + 1 ;
}

for($jour_du_mois = 1 ; $jour_du_mois <= 7 - $numero_jour1er; $jour_du_mois++)
{
  if(strlen($numero_mois) == 1 ){
		$numero_mois='0'.$numero_mois;
	}
	if(strlen($jour_du_mois) == 1 ){
		$jour_du_mois='0'.$jour_du_mois;
	}
	$DATE_URL=$annee.''.$numero_mois.''.$jour_du_mois;
	if($DATE_URL > $DATE_DU_JOUR){
    echo '<td>&nbsp;<a class="LinkDef" href="./index.php?ITEM=changement_Ajout_Changement&action=Ajout&ANNEE='.$annee.'&MOIS='.$numero_mois.'&JOUR='.$jour_du_mois.'">'.$jour_du_mois.'</a>&nbsp;</td>';
   }else{
    	if($DATE_URL == $DATE_DU_JOUR ){
		echo '<td bgcolor="#FF0000"><FONT COLOR="#FFFFFF">&nbsp;'.$jour_du_mois.'&nbsp;</FONT></td>';
	}else{
		echo '<td>&nbsp;'.$jour_du_mois.'&nbsp;</td>';
	}
    }
}
echo '</tr>';
$SEMAINE=date('W',mktime(0, 0, 0, $numero_mois, 1, $annee));
$NB_inter=Return_info_semaine($annee_avant,$mois_avant,$SEMAINE,$mysql_link);
/*if($annee_avant!=$annee){
  $NB_inter=$NB_inter+Return_info_semaine($annee,$numero_mois,$SEMAINE,$mysql_link);
}
if($annee_apres!=$annee){
  $NB_inter=$NB_inter+Return_info_semaine($annee_apres,$mois_apres,$SEMAINE,$mysql_link);
}
*/
if($NB_inter>=1){
	$JOURS_autre_mois=date('t',mktime(0, 0, 0, $mois_avant, 1, $annee_avant))-$numero_jour1er+1;
	$rq_changement_info="
      	SELECT DISTINCT(`CHANGEMENT_ID`) AS `CHANGEMENT_ID`
      	FROM `changement_date` 
      	WHERE 
      	(`CHANGEMENT_DATE` LIKE '".$annee_avant."".$mois_avant."%' OR `CHANGEMENT_DATE` LIKE '".$annee."".$numero_mois."%')
        AND `CHANGEMENT_SEMAINE` ='".$SEMAINE."'  
      	AND `ENABLE`='0' 
      	GROUP BY `CHANGEMENT_SEMAINE`, `CHANGEMENT_ID` ";
	$res_rq_changement_info = mysql_query($rq_changement_info, $mysql_link) or die(mysql_error());
	$tab_rq_changement_info = mysql_fetch_assoc($res_rq_changement_info);
	$total_ligne_rq_changement_info=mysql_num_rows($res_rq_changement_info); 
	if($total_ligne_rq_changement_info!=0){
		do {
			$CHANGEMENT_ID=$tab_rq_changement_info['CHANGEMENT_ID'];
			echo '<tr class="'.$class.'">'; // Nouvelle ligne du tableau (celle de la 1ère semaine, donc)
			// &eacute;criture de colonnes du mois - 1  tant que le mois ne d&eacute;marre pas.
			echo '<td>&nbsp;</td>';
			$JOURS_autre_mois=date('t',mktime(0, 0, 0, $mois_avant, 1, $annee_avant))-$numero_jour1er+1;
			for($jour_du_mois = 0 ; $jour_du_mois < $numero_jour1er ; $jour_du_mois++)
			{
				$NB=Return_info_jour($annee_avant,$mois_avant,$JOURS_autre_mois,$SEMAINE,$CHANGEMENT_ID,$mysql_link);
				$AFF='';
				$AFF_TEST='';
				if($NB > 0 ){
					$AFF=$CHANGEMENT_ID;
					$AFF_TEST=$CHANGEMENT_ID%$NB_COULEUR; 
          $rq_info_CHANGEMENT_ID="
          SELECT 
          `moteur_utilisateur`.`UTILISATEUR_ID` ,  
          `moteur_utilisateur`.`LOGIN` ,  
          `moteur_utilisateur`.`NOM` ,  
          `moteur_utilisateur`.`PRENOM` ,  
          `changement_liste`.`CHANGEMENT_LISTE_ID` ,  
          `changement_liste`.`CHANGEMENT_LISTE_UTILISATEUR_ID` , 
          `changement_liste`.`CHANGEMENT_LISTE_DATE_DEBUT` ,  
          `changement_liste`.`CHANGEMENT_LISTE_DATE_FIN` ,  
          `changement_liste`.`CHANGEMENT_LISTE_HEURE_DEBUT` ,  
          `changement_liste`.`CHANGEMENT_LISTE_HEURE_FIN` ,  
          `changement_liste`.`CHANGEMENT_LISTE_DATE_MODIFICATION` ,  
          `changement_liste`.`CHANGEMENT_LISTE_LIB`,
          `changement_status`.`CHANGEMENT_STATUS`,
          `changement_demande`.`CHANGEMENT_DEMANDE_LIB`,
          `changement_demande`.`CHANGEMENT_DEMANDE_COULEUR_FOND`,
          `changement_demande`.`CHANGEMENT_DEMANDE_COULEUR_TEXTE`
          FROM `changement_liste` , `moteur_utilisateur`,`changement_status`,`changement_demande`
          WHERE `changement_liste`.`CHANGEMENT_LISTE_ID` = '".$CHANGEMENT_ID."' 
          AND `changement_liste`.`CHANGEMENT_STATUS_ID`=`changement_status`.`CHANGEMENT_STATUS_ID`
          AND `changement_liste`.`CHANGEMENT_DEMANDE_ID`=`changement_demande`.`CHANGEMENT_DEMANDE_ID`
          AND `moteur_utilisateur`.`UTILISATEUR_ID` = `changement_liste`.`CHANGEMENT_LISTE_UTILISATEUR_ID`
          AND `changement_liste`.`ENABLE` = '0'
          LIMIT 1
          ";
					$res_rq_info_CHANGEMENT_ID = mysql_query($rq_info_CHANGEMENT_ID, $mysql_link) or die(mysql_error());
					$tab_rq_info_CHANGEMENT_ID = mysql_fetch_assoc($res_rq_info_CHANGEMENT_ID);
					$total_ligne_rq_info_CHANGEMENT_ID=mysql_num_rows($res_rq_info_CHANGEMENT_ID);
					mysql_free_result($res_rq_info_CHANGEMENT_ID);
					if($total_ligne_rq_info_CHANGEMENT_ID==0){
						$AFF_SPAN='Info : '.$AFF;
					}else{
						$LOGIN=$tab_rq_info_CHANGEMENT_ID['LOGIN'];
						$NOM=$tab_rq_info_CHANGEMENT_ID['NOM'];
						$PRENOM=$tab_rq_info_CHANGEMENT_ID['PRENOM'];
						$DATE_DEBUT=$tab_rq_info_CHANGEMENT_ID['CHANGEMENT_LISTE_DATE_DEBUT'];
						$DATE_DEBUT_jour=substr($DATE_DEBUT,6,2);
						$DATE_DEBUT_mois=substr($DATE_DEBUT,4,2);
						$DATE_DEBUT_annee=substr($DATE_DEBUT,0,4); 
						$DATE_DEBUT=$DATE_DEBUT_jour.'/'.$DATE_DEBUT_mois.'/'.$DATE_DEBUT_annee;
						$DATE_FIN=$tab_rq_info_CHANGEMENT_ID['CHANGEMENT_LISTE_DATE_FIN'];
						$DATE_FIN_jour=substr($DATE_FIN,6,2);
						$DATE_FIN_mois=substr($DATE_FIN,4,2);
						$DATE_FIN_annee=substr($DATE_FIN,0,4); 
						$DATE_FIN=$DATE_FIN_jour.'/'.$DATE_FIN_mois.'/'.$DATE_FIN_annee;
						$HEURE_DEBUT=$tab_rq_info_CHANGEMENT_ID['CHANGEMENT_LISTE_HEURE_DEBUT'];
						$HEURE_FIN=$tab_rq_info_CHANGEMENT_ID['CHANGEMENT_LISTE_HEURE_FIN'];
						$DATE_MODIFICATION=$tab_rq_info_CHANGEMENT_ID['CHANGEMENT_LISTE_DATE_MODIFICATION'];
						$LIB=$tab_rq_info_CHANGEMENT_ID['CHANGEMENT_LISTE_LIB'];
						$CHANGEMENT_DEMANDE_LIB=$tab_rq_info_CHANGEMENT_ID['CHANGEMENT_DEMANDE_LIB'];
						$CHANGEMENT_STATUS=$tab_rq_info_CHANGEMENT_ID['CHANGEMENT_STATUS'];
						$CHANGEMENT_DEMANDE_COULEUR_FOND=$tab_rq_info_CHANGEMENT_ID['CHANGEMENT_DEMANDE_COULEUR_FOND'];
						$CHANGEMENT_DEMANDE_COULEUR_TEXTE=$tab_rq_info_CHANGEMENT_ID['CHANGEMENT_DEMANDE_COULEUR_TEXTE'];
						$LIB=html_entity_decode($LIB);
								
						$nbre_car = strlen($LIB);
						if ($nbre_car < 30){
							$LIB = $LIB;
						}else{
							$LIB = substr($LIB,0,30);
							$LIB = $LIB.'...';
						}
						$LIB=htmlentities($LIB);
				
						$AFF_SPAN='Num&eacute;ro : '.$AFF;
						$AFF_SPAN.='</BR>Type = '.$CHANGEMENT_DEMANDE_LIB;
						$AFF_SPAN.='</BR>Date de d&eacute;but = '.$DATE_DEBUT.' - '.$HEURE_DEBUT;
						$AFF_SPAN.='</BR>Date de fin = '.$DATE_FIN.' - '.$HEURE_FIN;
						$AFF_SPAN.='</BR>Titre du changement = '.$LIB;
						$AFF_SPAN.='</BR>Status = '.$CHANGEMENT_STATUS;
						$AFF_SPAN.='</BR>Derni&egrave;re Modification = '.$DATE_MODIFICATION;
						$AFF_SPAN.='</BR>Demandeur = '.$PRENOM.' '.$NOM;
						
					}
					
					if(isset($NBAFF[$CHANGEMENT_ID])){
						$NBAFF[$CHANGEMENT_ID]=$NBAFF[$CHANGEMENT_ID]+1;
						$AFF='&nbsp;';
					}else{
						$NBAFF[$CHANGEMENT_ID]=1;
					}
					if($ROLE==0){
			              	switch ($CHANGEMENT_STATUS)
					{
					  case "Brouillon": 
					    $action_url='Modif';
					  break;
					  case "Inscrit": 
					    $action_url='Modif';
					  break;
					  case "Abandonn&eacute;": 
					    $action_url='Info';
					  break;
					  case "Valid&eacute;": 
					    $action_url='Modif';
					  break;
					  case "Termin&eacute;": 
					    $action_url='Modif';
					  break;
					  case "Clotur&eacute;": 
					    $action_url='Info';
					  break;
					  case "ReInscription": 
					    $action_url='Modif';
					  break;
					  default:
					    $action_url='Info';
					  break;
					}
			              }else{
			              	switch ($CHANGEMENT_STATUS)
					{
					  case "Brouillon": 
					    $action_url='Modif';
					  break;
					  case "Inscrit": 
					    $action_url='Info';
					  break;
					  case "Abandonn&eacute;": 
					    $action_url='Info';
					  break;
					  case "Valid&eacute;": 
					    $action_url='Modif';
					  break;
					  case "Termin&eacute;": 
					    $action_url='Info';
					  break;
					  case "Clotur&eacute;": 
					    $action_url='Info';
					  break;
					  case "ReInscription": 
					    $action_url='Modif';
					  break;
					  default:
					    $action_url='Info';
					  break;
					}
			              }

					echo '
					<td bgcolor="#'.$CHANGEMENT_DEMANDE_COULEUR_FOND.'">
					<a href="./index.php?ITEM=changement_'.$action_url.'_Changement&action='.$action_url.'&ID='.$CHANGEMENT_ID.'" class="infobulledroite"><FONT COLOR="#'.$CHANGEMENT_DEMANDE_COULEUR_TEXTE.'">'.$AFF.'</FONT>
					<span>'.$AFF_SPAN.'</span>
					</a>
					</td>';
				}else{
					echo '<td>'.$AFF.'</td>';
				}
				$JOURS_autre_mois = $JOURS_autre_mois + 1 ;
			}
			
			for($jour_du_mois = 1 ; $jour_du_mois <= 7 - $numero_jour1er; $jour_du_mois++)
			{
				$NB=Return_info_jour($annee,$numero_mois,$jour_du_mois,$SEMAINE,$CHANGEMENT_ID,$mysql_link);
				$AFF='';
				$AFF_TEST='';
				if($NB > 0 ){
					$AFF=$CHANGEMENT_ID;
					$AFF_TEST=$CHANGEMENT_ID%$NB_COULEUR; 
					$rq_info_CHANGEMENT_ID="
					SELECT 
					`moteur_utilisateur`.`UTILISATEUR_ID` ,  
					`moteur_utilisateur`.`LOGIN` ,  
					`moteur_utilisateur`.`NOM` ,  
					`moteur_utilisateur`.`PRENOM` ,  
					`changement_liste`.`CHANGEMENT_LISTE_ID` ,  
					`changement_liste`.`CHANGEMENT_LISTE_UTILISATEUR_ID` , 
					`changement_liste`.`CHANGEMENT_LISTE_DATE_DEBUT` ,  
					`changement_liste`.`CHANGEMENT_LISTE_DATE_FIN` ,  
					`changement_liste`.`CHANGEMENT_LISTE_HEURE_DEBUT` ,  
					`changement_liste`.`CHANGEMENT_LISTE_HEURE_FIN` ,  
					`changement_liste`.`CHANGEMENT_LISTE_DATE_MODIFICATION` ,  
					`changement_liste`.`CHANGEMENT_LISTE_LIB`,
					`changement_status`.`CHANGEMENT_STATUS`,
					`changement_demande`.`CHANGEMENT_DEMANDE_LIB`,
					`changement_demande`.`CHANGEMENT_DEMANDE_COULEUR_FOND`,
					`changement_demande`.`CHANGEMENT_DEMANDE_COULEUR_TEXTE`
					FROM `changement_liste` , `moteur_utilisateur`,`changement_status`,`changement_demande`
					WHERE `changement_liste`.`CHANGEMENT_LISTE_ID` = '".$CHANGEMENT_ID."' 
					AND `changement_liste`.`CHANGEMENT_STATUS_ID`=`changement_status`.`CHANGEMENT_STATUS_ID`
					AND `changement_liste`.`CHANGEMENT_DEMANDE_ID`=`changement_demande`.`CHANGEMENT_DEMANDE_ID`
					AND `moteur_utilisateur`.`UTILISATEUR_ID` = `changement_liste`.`CHANGEMENT_LISTE_UTILISATEUR_ID`
					AND `changement_liste`.`ENABLE` = '0'
					LIMIT 1
					";
					
					$res_rq_info_CHANGEMENT_ID = mysql_query($rq_info_CHANGEMENT_ID, $mysql_link) or die(mysql_error());
					$tab_rq_info_CHANGEMENT_ID = mysql_fetch_assoc($res_rq_info_CHANGEMENT_ID);
					$total_ligne_rq_info_CHANGEMENT_ID=mysql_num_rows($res_rq_info_CHANGEMENT_ID);
					mysql_free_result($res_rq_info_CHANGEMENT_ID);
					if($total_ligne_rq_info_CHANGEMENT_ID==0){
						$AFF_SPAN='Info : '.$AFF;
					}else{
						$LOGIN=$tab_rq_info_CHANGEMENT_ID['LOGIN'];
						$NOM=$tab_rq_info_CHANGEMENT_ID['NOM'];
						$PRENOM=$tab_rq_info_CHANGEMENT_ID['PRENOM'];
						$DATE_DEBUT=$tab_rq_info_CHANGEMENT_ID['CHANGEMENT_LISTE_DATE_DEBUT'];
						$DATE_DEBUT_jour=substr($DATE_DEBUT,6,2);
						$DATE_DEBUT_mois=substr($DATE_DEBUT,4,2);
						$DATE_DEBUT_annee=substr($DATE_DEBUT,0,4); 
						$DATE_DEBUT=$DATE_DEBUT_jour.'/'.$DATE_DEBUT_mois.'/'.$DATE_DEBUT_annee;
						$DATE_FIN=$tab_rq_info_CHANGEMENT_ID['CHANGEMENT_LISTE_DATE_FIN'];
						$DATE_FIN_jour=substr($DATE_FIN,6,2);
						$DATE_FIN_mois=substr($DATE_FIN,4,2);
						$DATE_FIN_annee=substr($DATE_FIN,0,4); 
						$DATE_FIN=$DATE_FIN_jour.'/'.$DATE_FIN_mois.'/'.$DATE_FIN_annee;
						$HEURE_DEBUT=$tab_rq_info_CHANGEMENT_ID['CHANGEMENT_LISTE_HEURE_DEBUT'];
						$HEURE_FIN=$tab_rq_info_CHANGEMENT_ID['CHANGEMENT_LISTE_HEURE_FIN'];
						$DATE_MODIFICATION=$tab_rq_info_CHANGEMENT_ID['CHANGEMENT_LISTE_DATE_MODIFICATION'];
						$LIB=$tab_rq_info_CHANGEMENT_ID['CHANGEMENT_LISTE_LIB'];
						$CHANGEMENT_DEMANDE_LIB=$tab_rq_info_CHANGEMENT_ID['CHANGEMENT_DEMANDE_LIB'];
						$CHANGEMENT_STATUS=$tab_rq_info_CHANGEMENT_ID['CHANGEMENT_STATUS'];
						$CHANGEMENT_DEMANDE_COULEUR_FOND=$tab_rq_info_CHANGEMENT_ID['CHANGEMENT_DEMANDE_COULEUR_FOND'];
						$CHANGEMENT_DEMANDE_COULEUR_TEXTE=$tab_rq_info_CHANGEMENT_ID['CHANGEMENT_DEMANDE_COULEUR_TEXTE'];
						$LIB=html_entity_decode($LIB);
								
						$nbre_car = strlen($LIB);
						if ($nbre_car < 30){
							$LIB = $LIB;
						}else{
							$LIB = substr($LIB,0,30);
							$LIB = $LIB.'...';
						}
						$LIB=htmlentities($LIB);
						
						$AFF_SPAN='Num&eacute;ro : '.$AFF;
						$AFF_SPAN.='</BR>Type = '.$CHANGEMENT_DEMANDE_LIB;
						$AFF_SPAN.='</BR>Date de d&eacute;but = '.$DATE_DEBUT.' - '.$HEURE_DEBUT;
						$AFF_SPAN.='</BR>Date de fin = '.$DATE_FIN.' - '.$HEURE_FIN;
						$AFF_SPAN.='</BR>Titre du changement = '.$LIB;
						$AFF_SPAN.='</BR>Status = '.$CHANGEMENT_STATUS;
						$AFF_SPAN.='</BR>Derni&egrave;re Modification = '.$DATE_MODIFICATION;
						$AFF_SPAN.='</BR>Demandeur = '.$PRENOM.' '.$NOM;
						
					}
					if(isset($NBAFF[$CHANGEMENT_ID])){
						$NBAFF[$CHANGEMENT_ID]=$NBAFF[$CHANGEMENT_ID]+1;
						$AFF='&nbsp;';
					}else{
						$NBAFF[$CHANGEMENT_ID]=1;
					}
					if($ROLE==0){
			              	switch ($CHANGEMENT_STATUS)
					{
					  case "Brouillon": 
					    $action_url='Modif';
					  break;
					  case "Inscrit": 
					    $action_url='Modif';
					  break;
					  case "Abandonn&eacute;": 
					    $action_url='Info';
					  break;
					  case "Valid&eacute;": 
					    $action_url='Modif';
					  break;
					  case "Termin&eacute;": 
					    $action_url='Modif';
					  break;
					  case "Clotur&eacute;": 
					    $action_url='Info';
					  break;
					  case "ReInscription": 
					    $action_url='Modif';
					  break;
					  default:
					    $action_url='Info';
					  break;
					}
			              }else{
			              	switch ($CHANGEMENT_STATUS)
					{
					  case "Brouillon": 
					    $action_url='Modif';
					  break;
					  case "Inscrit": 
					    $action_url='Info';
					  break;
					  case "Abandonn&eacute;": 
					    $action_url='Info';
					  break;
					  case "Valid&eacute;": 
					    $action_url='Modif';
					  break;
					  case "Termin&eacute;": 
					    $action_url='Info';
					  break;
					  case "Clotur&eacute;": 
					    $action_url='Info';
					  break;
					  case "ReInscription": 
					    $action_url='Modif';
					  break;
					  default:
					    $action_url='Info';
					  break;
					}
			              }

					echo '
					<td bgcolor="#'.$CHANGEMENT_DEMANDE_COULEUR_FOND.'">
						<a href="./index.php?ITEM=changement_'.$action_url.'_Changement&action='.$action_url.'&ID='.$CHANGEMENT_ID.'" class="infobulledroite"><FONT COLOR="#'.$CHANGEMENT_DEMANDE_COULEUR_TEXTE.'">'.$AFF.'</FONT>
						<span>'.$AFF_SPAN.'</span>
						</a>
					</td>
					';
				}else{
					echo '<td>'.$AFF.'</td>';
				}
			}
			echo '</tr>';
		} while ($tab_rq_changement_info = mysql_fetch_assoc($res_rq_changement_info));
	        $ligne= mysql_num_rows($res_rq_changement_info);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_changement_info, 0);
	          $tab_rq_changement_info = mysql_fetch_assoc($res_rq_changement_info);
	        }
	}
        mysql_free_result($res_rq_changement_info);
	
}else{
	echo '
	<tr class="'.$class.'">
	  <td align="center" colspan="8">&nbsp;</td>
	</tr>';
}
$NB_jours_ok=$jour_du_mois;
$DATE_DU_JOUR=date("Ymd");
$nbLignes = ceil((date('t', $date_premier_j_mois) - ($jour_du_mois-1)) / 7);
for($ligne = 0 ; $ligne < $nbLignes ; $ligne++)
{
  $SEMAINE=date('W',mktime(0, 0, 0, $numero_mois, $jour_du_mois, $annee));
	$NB_inter=Return_info_semaine($annee,$numero_mois,$SEMAINE,$mysql_link);
  /*  if($annee_apres!=$annee){
    $NB_inter=$NB_inter+Return_info_semaine($annee_apres,$mois_apres,$SEMAINE,$mysql_link);
  }*/
	$numLigne = $numLigne + 1;
	echo '<tr>';
	echo '<td colspan="8">&nbsp;</td>';
	echo '</tr>';
	if ($numLigne%2) { $class = "pair";}else{$class = "impair";} 
	echo '<tr class="'.$class.'">'; // Nouvelle ligne du tableau (celle de la nouvelle semaine)
	echo '<td><b>&nbsp;S'.$SEMAINE.'&nbsp;</b></td>';
	$JOURS_autre_mois=0;
	$NB_jours_ok=$jour_du_mois;
	for($colone = 0 ; $colone < 7 ; $colone++)
	{
		if($jour_du_mois <= date('t', $date_premier_j_mois))
		{
			if(strlen($numero_mois) == 1 ){
        $numero_mois='0'.$numero_mois;
      }
      if(strlen($jour_du_mois) == 1 ){
        $jour_du_mois='0'.$jour_du_mois;
      }
      $DATE_URL=$annee.''.$numero_mois.''.$jour_du_mois;
      if($DATE_URL > $DATE_DU_JOUR){
        echo '<td>&nbsp;<a class="LinkDef" href="./index.php?ITEM=changement_Ajout_Changement&action=Ajout&ANNEE='.$annee.'&MOIS='.$numero_mois.'&JOUR='.$jour_du_mois.'">'.$jour_du_mois.'</a>&nbsp;</td>';
      }else{
        if($DATE_URL == $DATE_DU_JOUR ){
		echo '<td bgcolor="#FF0000"><FONT COLOR="#FFFFFF">&nbsp;'.$jour_du_mois.'&nbsp;</FONT></td>';
	}else{
		echo '<td>&nbsp;'.$jour_du_mois.'&nbsp;</td>';
	}
      }
			
		}else{
			if($JOURS_autre_mois==0){
				$JOURS_autre_mois=1;		
			}
			// On a fini d'&eacute;crire le mois on termine la tableau par des cellules du mois + 1 
			if(strlen($mois_apres) == 1 ){
        $mois_apres='0'.$mois_apres;
      }
      if(strlen($JOURS_autre_mois) == 1 ){
        $JOURS_autre_mois='0'.$JOURS_autre_mois;
      }
      $DATE_URL=$annee_apres.''.$mois_apres.''.$JOURS_autre_mois;
      if($DATE_URL > $DATE_DU_JOUR){
			echo '<td>&nbsp;<a class="LinkDef" href="./index.php?ITEM=changement_Ajout_Changement&action=Ajout&ANNEE='.$annee_apres.'&MOIS='.$mois_apres.'&JOUR='.$JOURS_autre_mois.'">'.$JOURS_autre_mois.'</a>&nbsp;</td>';
			}else{
			if($DATE_URL == $DATE_DU_JOUR ){
				echo '<td bgcolor="#FF0000"><FONT COLOR="#FFFFFF">&nbsp;'.$JOURS_autre_mois.'&nbsp;</FONT></td>';
			}else{
				echo '<td>&nbsp;'.$JOURS_autre_mois.'&nbsp;</td>';
			}
			}
			$JOURS_autre_mois= $JOURS_autre_mois +1;
		}
	
		$jour_du_mois = $jour_du_mois +1;
	}
	echo '</tr>';
	if($NB_inter >=1 ){
		$rq_changement_info="
	      	SELECT `CHANGEMENT_ID` 
	      	FROM `changement_date` 
	      	WHERE 
	      	(`CHANGEMENT_DATE` LIKE '".$annee."".$numero_mois."%' OR `CHANGEMENT_DATE` LIKE '".$annee_apres."".$mois_apres."%')
	        AND `CHANGEMENT_SEMAINE` ='".$SEMAINE."'  
	      	AND `ENABLE`='0' 
	      	GROUP BY `CHANGEMENT_SEMAINE`, `CHANGEMENT_ID` ";
		$res_rq_changement_info = mysql_query($rq_changement_info, $mysql_link) or die(mysql_error());
		$tab_rq_changement_info = mysql_fetch_assoc($res_rq_changement_info);
		$total_ligne_rq_changement_info=mysql_num_rows($res_rq_changement_info); 
		
		if($total_ligne_rq_changement_info!=0){
			do {
				$CHANGEMENT_ID=$tab_rq_changement_info['CHANGEMENT_ID'];
				echo '<tr class="'.$class.'">'; // Nouvelle ligne du tableau (celle de la nouvelle semaine)
				if(isset($NBAFF_S[$CHANGEMENT_ID])){
						$AFF='&nbsp;';
						$NBAFF_S[$CHANGEMENT_ID]=0;
					}else{
						$NBAFF_S[$CHANGEMENT_ID]=0;
					}
				echo '<td>&nbsp;</td>';

				$JOURS_autre_mois=0;
				$jour_du_mois=$NB_jours_ok;
				for($colone = 0 ; $colone < 7 ; $colone++)
				{
					if($jour_du_mois <= date('t', $date_premier_j_mois))
					{
						$NB=Return_info_jour($annee,$numero_mois,$jour_du_mois,$SEMAINE,$CHANGEMENT_ID,$mysql_link);
						$AFF='';
						$AFF_TEST='';
						if($NB > 0 ){
							$AFF=$CHANGEMENT_ID;
							$AFF_TEST=$CHANGEMENT_ID%$NB_COULEUR; 
							$rq_info_CHANGEMENT_ID="
					              SELECT 
					              `moteur_utilisateur`.`UTILISATEUR_ID` ,  
					              `moteur_utilisateur`.`LOGIN` ,  
					              `moteur_utilisateur`.`NOM` ,  
					              `moteur_utilisateur`.`PRENOM` ,  
					              `changement_liste`.`CHANGEMENT_LISTE_ID` ,  
					              `changement_liste`.`CHANGEMENT_LISTE_UTILISATEUR_ID` , 
					              `changement_liste`.`CHANGEMENT_LISTE_DATE_DEBUT` ,  
					              `changement_liste`.`CHANGEMENT_LISTE_DATE_FIN` ,  
					              `changement_liste`.`CHANGEMENT_LISTE_HEURE_DEBUT` ,  
					              `changement_liste`.`CHANGEMENT_LISTE_HEURE_FIN` ,  
					              `changement_liste`.`CHANGEMENT_LISTE_DATE_MODIFICATION` , 
					              `changement_liste`.`CHANGEMENT_LISTE_LIB`,
					              `changement_status`.`CHANGEMENT_STATUS`,
					              `changement_demande`.`CHANGEMENT_DEMANDE_LIB`,
					              `changement_demande`.`CHANGEMENT_DEMANDE_COULEUR_FOND`,
					              `changement_demande`.`CHANGEMENT_DEMANDE_COULEUR_TEXTE`
					              FROM `changement_liste` , `moteur_utilisateur`,`changement_status`,`changement_demande`
					              WHERE `changement_liste`.`CHANGEMENT_LISTE_ID` = '".$CHANGEMENT_ID."' 
					              AND `changement_liste`.`CHANGEMENT_STATUS_ID`=`changement_status`.`CHANGEMENT_STATUS_ID`
					              AND `changement_liste`.`CHANGEMENT_DEMANDE_ID`=`changement_demande`.`CHANGEMENT_DEMANDE_ID`
					              AND `moteur_utilisateur`.`UTILISATEUR_ID` = `changement_liste`.`CHANGEMENT_LISTE_UTILISATEUR_ID`
					              AND `changement_liste`.`ENABLE` = '0'
					              LIMIT 1
					              ";
							$res_rq_info_CHANGEMENT_ID = mysql_query($rq_info_CHANGEMENT_ID, $mysql_link) or die(mysql_error());
							$tab_rq_info_CHANGEMENT_ID = mysql_fetch_assoc($res_rq_info_CHANGEMENT_ID);
							$total_ligne_rq_info_CHANGEMENT_ID=mysql_num_rows($res_rq_info_CHANGEMENT_ID);
							mysql_free_result($res_rq_info_CHANGEMENT_ID);
							if($total_ligne_rq_info_CHANGEMENT_ID==0){
								$AFF_SPAN='Info : '.$AFF;
							}else{
								$LOGIN=$tab_rq_info_CHANGEMENT_ID['LOGIN'];
						                $NOM=$tab_rq_info_CHANGEMENT_ID['NOM'];
						                $PRENOM=$tab_rq_info_CHANGEMENT_ID['PRENOM'];
						                $DATE_DEBUT=$tab_rq_info_CHANGEMENT_ID['CHANGEMENT_LISTE_DATE_DEBUT'];
						                $DATE_DEBUT_jour=substr($DATE_DEBUT,6,2);
						                $DATE_DEBUT_mois=substr($DATE_DEBUT,4,2);
						                $DATE_DEBUT_annee=substr($DATE_DEBUT,0,4); 
						                $DATE_DEBUT=$DATE_DEBUT_jour.'/'.$DATE_DEBUT_mois.'/'.$DATE_DEBUT_annee;
						                $DATE_FIN=$tab_rq_info_CHANGEMENT_ID['CHANGEMENT_LISTE_DATE_FIN'];
						                $DATE_FIN_jour=substr($DATE_FIN,6,2);
						                $DATE_FIN_mois=substr($DATE_FIN,4,2);
						                $DATE_FIN_annee=substr($DATE_FIN,0,4); 
						                $DATE_FIN=$DATE_FIN_jour.'/'.$DATE_FIN_mois.'/'.$DATE_FIN_annee;
						                $HEURE_DEBUT=$tab_rq_info_CHANGEMENT_ID['CHANGEMENT_LISTE_HEURE_DEBUT'];
						                $HEURE_FIN=$tab_rq_info_CHANGEMENT_ID['CHANGEMENT_LISTE_HEURE_FIN'];
						                $DATE_MODIFICATION=$tab_rq_info_CHANGEMENT_ID['CHANGEMENT_LISTE_DATE_MODIFICATION'];
						                $LIB=$tab_rq_info_CHANGEMENT_ID['CHANGEMENT_LISTE_LIB'];
						                $CHANGEMENT_DEMANDE_LIB=$tab_rq_info_CHANGEMENT_ID['CHANGEMENT_DEMANDE_LIB'];
						                $CHANGEMENT_STATUS=$tab_rq_info_CHANGEMENT_ID['CHANGEMENT_STATUS'];
						                $CHANGEMENT_DEMANDE_COULEUR_FOND=$tab_rq_info_CHANGEMENT_ID['CHANGEMENT_DEMANDE_COULEUR_FOND'];
						                $CHANGEMENT_DEMANDE_COULEUR_TEXTE=$tab_rq_info_CHANGEMENT_ID['CHANGEMENT_DEMANDE_COULEUR_TEXTE'];
						                $LIB=html_entity_decode($LIB);
								
								$nbre_car = strlen($LIB);
								if ($nbre_car < 30){
									$LIB = $LIB;
								}else{
									$LIB = substr($LIB,0,30);
									$LIB = $LIB.'...';
								}
								$LIB=htmlentities($LIB);
                    
								$AFF_SPAN='Num&eacute;ro : '.$AFF;
								$AFF_SPAN.='</BR>Type = '.$CHANGEMENT_DEMANDE_LIB;
								$AFF_SPAN.='</BR>Date de d&eacute;but = '.$DATE_DEBUT.' - '.$HEURE_DEBUT;
								$AFF_SPAN.='</BR>Date de fin = '.$DATE_FIN.' - '.$HEURE_FIN;
								$AFF_SPAN.='</BR>Titre du changement = '.$LIB;
								$AFF_SPAN.='</BR>Status = '.$CHANGEMENT_STATUS;
								$AFF_SPAN.='</BR>Derni&egrave;re Modification = '.$DATE_MODIFICATION;
								$AFF_SPAN.='</BR>Demandeur = '.$PRENOM.' '.$NOM;
								
							}
							if(isset($NBAFF[$CHANGEMENT_ID])){
								$NBAFF[$CHANGEMENT_ID]=$NBAFF[$CHANGEMENT_ID]+1;
								$AFF='&nbsp;';
							}else{
								$NBAFF[$CHANGEMENT_ID]=1;
							}
							if(isset($NBAFF_S[$CHANGEMENT_ID])){
                $NBAFF_S[$CHANGEMENT_ID]=$NBAFF_S[$CHANGEMENT_ID]+1;
                $AFF='&nbsp;';
              }else{
                $NBAFF_S[$CHANGEMENT_ID]=1;
              }
              if($NBAFF_S[$CHANGEMENT_ID]==1){$AFF=$CHANGEMENT_ID;}
              if($ROLE==0){
              	switch ($CHANGEMENT_STATUS)
		{
		  case "Brouillon": 
		    $action_url='Modif';
		  break;
		  case "Inscrit": 
		    $action_url='Modif';
		  break;
		  case "Abandonn&eacute;": 
		    $action_url='Info';
		  break;
		  case "Valid&eacute;": 
		    $action_url='Modif';
		  break;
		  case "Termin&eacute;": 
		    $action_url='Modif';
		  break;
		  case "Clotur&eacute;": 
		    $action_url='Info';
		  break;
		  case "ReInscription": 
		    $action_url='Modif';
		  break;
		  default:
		    $action_url='Info';
		  break;
		}
              }else{
              	switch ($CHANGEMENT_STATUS)
		{
		  case "Brouillon": 
		    $action_url='Modif';
		  break;
		  case "Inscrit": 
		    $action_url='Info';
		  break;
		  case "Abandonn&eacute;": 
		    $action_url='Info';
		  break;
		  case "Valid&eacute;": 
		    $action_url='Modif';
		  break;
		  case "Termin&eacute;": 
		    $action_url='Info';
		  break;
		  case "Clotur&eacute;": 
		    $action_url='Info';
		  break;
		  case "ReInscription": 
		    $action_url='Modif';
		  break;
		  default:
		    $action_url='Info';
		  break;
		}
              }
							echo '
							<td bgcolor="#'.$CHANGEMENT_DEMANDE_COULEUR_FOND.'">
							<a href="./index.php?ITEM=changement_'.$action_url.'_Changement&action='.$action_url.'&ID='.$CHANGEMENT_ID.'" class="infobulledroite"><FONT COLOR="#'.$CHANGEMENT_DEMANDE_COULEUR_TEXTE.'">'.$AFF.'</FONT>
							<span>'.$AFF_SPAN.'</span>
							</a>
							</td>';
						}else{
							echo '<td>'.$AFF.'</td>';
						}
						
					}else{
						if($JOURS_autre_mois==0){
							$JOURS_autre_mois=1;		
						}
						// On a fini d'&eacute;crire le mois on termine la tableau par des cellules du mois +1
						$NB=Return_info_jour($annee_apres,$mois_apres,$JOURS_autre_mois,$SEMAINE,$CHANGEMENT_ID,$mysql_link);
						$AFF='';
						$AFF_TEST='';
						if($NB > 0 ){
							$AFF=$CHANGEMENT_ID;
							$AFF_TEST=$CHANGEMENT_ID%$NB_COULEUR; 
							$rq_info_CHANGEMENT_ID="
              SELECT 
              `moteur_utilisateur`.`UTILISATEUR_ID` ,  
              `moteur_utilisateur`.`LOGIN` ,  
              `moteur_utilisateur`.`NOM` ,  
              `moteur_utilisateur`.`PRENOM` ,  
              `changement_liste`.`CHANGEMENT_LISTE_ID` ,  
              `changement_liste`.`CHANGEMENT_LISTE_UTILISATEUR_ID` , 
              `changement_liste`.`CHANGEMENT_LISTE_DATE_DEBUT` ,  
              `changement_liste`.`CHANGEMENT_LISTE_DATE_FIN` ,  
              `changement_liste`.`CHANGEMENT_LISTE_HEURE_DEBUT` ,  
              `changement_liste`.`CHANGEMENT_LISTE_HEURE_FIN` ,  
              `changement_liste`.`CHANGEMENT_LISTE_DATE_MODIFICATION` ,   
              `changement_liste`.`CHANGEMENT_LISTE_LIB`,
              `changement_status`.`CHANGEMENT_STATUS`,
              `changement_demande`.`CHANGEMENT_DEMANDE_LIB`,
              `changement_demande`.`CHANGEMENT_DEMANDE_COULEUR_FOND`,
              `changement_demande`.`CHANGEMENT_DEMANDE_COULEUR_TEXTE`
              FROM `changement_liste` , `moteur_utilisateur`,`changement_status`,`changement_demande`
              WHERE `changement_liste`.`CHANGEMENT_LISTE_ID` = '".$CHANGEMENT_ID."' 
              AND `changement_liste`.`CHANGEMENT_STATUS_ID`=`changement_status`.`CHANGEMENT_STATUS_ID`
              AND `changement_liste`.`CHANGEMENT_DEMANDE_ID`=`changement_demande`.`CHANGEMENT_DEMANDE_ID`
              AND `moteur_utilisateur`.`UTILISATEUR_ID` = `changement_liste`.`CHANGEMENT_LISTE_UTILISATEUR_ID`
              AND `changement_liste`.`ENABLE` = '0'
              LIMIT 1
              ";
							$res_rq_info_CHANGEMENT_ID = mysql_query($rq_info_CHANGEMENT_ID, $mysql_link) or die(mysql_error());
							$tab_rq_info_CHANGEMENT_ID = mysql_fetch_assoc($res_rq_info_CHANGEMENT_ID);
							$total_ligne_rq_info_CHANGEMENT_ID=mysql_num_rows($res_rq_info_CHANGEMENT_ID);
							mysql_free_result($res_rq_info_CHANGEMENT_ID);
							if($total_ligne_rq_info_CHANGEMENT_ID==0){
								$AFF_SPAN='Info : '.$AFF;
							}else{
						                $LOGIN=$tab_rq_info_CHANGEMENT_ID['LOGIN'];
						                $NOM=$tab_rq_info_CHANGEMENT_ID['NOM'];
						                $PRENOM=$tab_rq_info_CHANGEMENT_ID['PRENOM'];
						                $DATE_DEBUT=$tab_rq_info_CHANGEMENT_ID['CHANGEMENT_LISTE_DATE_DEBUT'];
						                $DATE_DEBUT_jour=substr($DATE_DEBUT,6,2);
						                $DATE_DEBUT_mois=substr($DATE_DEBUT,4,2);
						                $DATE_DEBUT_annee=substr($DATE_DEBUT,0,4); 
						                $DATE_DEBUT=$DATE_DEBUT_jour.'/'.$DATE_DEBUT_mois.'/'.$DATE_DEBUT_annee;
						                $DATE_FIN=$tab_rq_info_CHANGEMENT_ID['CHANGEMENT_LISTE_DATE_FIN'];
						                $DATE_FIN_jour=substr($DATE_FIN,6,2);
						                $DATE_FIN_mois=substr($DATE_FIN,4,2);
						                $DATE_FIN_annee=substr($DATE_FIN,0,4); 
						                $DATE_FIN=$DATE_FIN_jour.'/'.$DATE_FIN_mois.'/'.$DATE_FIN_annee;
						                $HEURE_DEBUT=$tab_rq_info_CHANGEMENT_ID['CHANGEMENT_LISTE_HEURE_DEBUT'];
						                $HEURE_FIN=$tab_rq_info_CHANGEMENT_ID['CHANGEMENT_LISTE_HEURE_FIN'];
						                $DATE_MODIFICATION=$tab_rq_info_CHANGEMENT_ID['CHANGEMENT_LISTE_DATE_MODIFICATION'];
						                $LIB=$tab_rq_info_CHANGEMENT_ID['CHANGEMENT_LISTE_LIB'];
						                $CHANGEMENT_DEMANDE_LIB=$tab_rq_info_CHANGEMENT_ID['CHANGEMENT_DEMANDE_LIB'];
						                $CHANGEMENT_STATUS=$tab_rq_info_CHANGEMENT_ID['CHANGEMENT_STATUS'];
						                $CHANGEMENT_DEMANDE_COULEUR_FOND=$tab_rq_info_CHANGEMENT_ID['CHANGEMENT_DEMANDE_COULEUR_FOND'];
						                $CHANGEMENT_DEMANDE_COULEUR_TEXTE=$tab_rq_info_CHANGEMENT_ID['CHANGEMENT_DEMANDE_COULEUR_TEXTE'];
								$LIB=html_entity_decode($LIB);
								
								$nbre_car = strlen($LIB);
								if ($nbre_car < 30){
									$LIB = $LIB;
								}else{
									$LIB = substr($LIB,0,30);
									$LIB = $LIB.'...';
								}
								$LIB=htmlentities($LIB);
								
								$AFF_SPAN='Num&eacute;ro : '.$AFF;
								$AFF_SPAN.='</BR>Type = '.$CHANGEMENT_DEMANDE_LIB;
								$AFF_SPAN.='</BR>Date de d&eacute;but = '.$DATE_DEBUT.' - '.$HEURE_DEBUT;
								$AFF_SPAN.='</BR>Date de fin = '.$DATE_FIN.' - '.$HEURE_FIN;
								$AFF_SPAN.='</BR>Titre du changement = '.$LIB;
								$AFF_SPAN.='</BR>Status = '.$CHANGEMENT_STATUS;
								$AFF_SPAN.='</BR>Derni&egrave;re Modification = '.$DATE_MODIFICATION;
								$AFF_SPAN.='</BR>Demandeur = '.$PRENOM.' '.$NOM;
								
							}
							if(isset($NBAFF[$CHANGEMENT_ID])){
								$NBAFF[$CHANGEMENT_ID]=$NBAFF[$CHANGEMENT_ID]+1;
								$AFF='&nbsp;';
							}else{
								$NBAFF[$CHANGEMENT_ID]=1;
							}
							if($ROLE==0){
					              	switch ($CHANGEMENT_STATUS)
							{
							  case "Brouillon": 
							    $action_url='Modif';
							  break;
							  case "Inscrit": 
							    $action_url='Modif';
							  break;
							  case "Abandonn&eacute;": 
							    $action_url='Info';
							  break;
							  case "Valid&eacute;": 
							    $action_url='Modif';
							  break;
							  case "Termin&eacute;": 
							    $action_url='Modif';
							  break;
							  case "Clotur&eacute;": 
							    $action_url='Info';
							  break;
							  case "ReInscription": 
							    $action_url='Modif';
							  break;
							  default:
							    $action_url='Info';
							  break;
							}
					              }else{
					              	switch ($CHANGEMENT_STATUS)
							{
							  case "Brouillon": 
							    $action_url='Modif';
							  break;
							  case "Inscrit": 
							    $action_url='Info';
							  break;
							  case "Abandonn&eacute;": 
							    $action_url='Info';
							  break;
							  case "Valid&eacute;": 
							    $action_url='Modif';
							  break;
							  case "Termin&eacute;": 
							    $action_url='Info';
							  break;
							  case "Clotur&eacute;": 
							    $action_url='Info';
							  break;
							  case "ReInscription": 
							    $action_url='Modif';
							  break;
							  default:
							    $action_url='Info';
							  break;
							}
					              }
							echo '
							<td bgcolor="#'.$CHANGEMENT_DEMANDE_COULEUR_FOND.'">
							<a href="./index.php?ITEM=changement_'.$action_url.'_Changement&action='.$action_url.'&ID='.$CHANGEMENT_ID.'" class="infobulledroite"><FONT COLOR="#'.$CHANGEMENT_DEMANDE_COULEUR_TEXTE.'">'.$AFF.'</FONT>
							<span>'.$AFF_SPAN.'</span>
							</a>
							</td>';
						}else{
							echo '<td>'.$AFF.'</td>';
						}
						$JOURS_autre_mois= $JOURS_autre_mois +1;
					}
					$jour_du_mois = $jour_du_mois +1;
				}
				echo '</tr>';
			} while ($tab_rq_changement_info = mysql_fetch_assoc($res_rq_changement_info));
		        $ligne_rq= mysql_num_rows($res_rq_changement_info);
		        if($ligne_rq > 0) {
		          mysql_data_seek($res_rq_changement_info, 0);
		          $tab_rq_changement_info = mysql_fetch_assoc($res_rq_changement_info);
		        }
		}
	        mysql_free_result($res_rq_changement_info);
	}else{
		echo '
		<tr class="'.$class.'">
		  <td align="center" colspan="8">&nbsp;</td>
		</tr>';
	}

}
echo '
</table>';
}

echo '<div align="center">';

if(isset($_GET['m'])){
	if($_GET['m']==''){
		unset($_GET['m']);
	}
}
if(isset($_GET['y'])){
	if($_GET['y']==''){
		unset($_GET['y']);
	}
}
if(!isset($_GET['m']) && !isset($_GET['y']))
{
	// Si on ne r&eacute;cupère rien dans l'url, on prend la date du 1er jour du mois actuel.
	$date_premier_j_mois = mktime(0, 0, 0, date('m'), 1, date('Y'));
}else{
	// Sinon on r&eacute;cupère la date du 1er jour du mois donn&eacute;.
	$date_premier_j_mois = mktime(0, 0, 0, $_GET['m'], 1, $_GET['y']);
}
$numero_mois = date('m', $date_premier_j_mois);
$annee = date('Y', $date_premier_j_mois);

$mois_avant_url=date('m', mktime(0, 0, 0, $numero_mois - 1, 1, $annee));
$annee_avant_url=date('Y', mktime(0, 0, 0, $numero_mois - 1, 1, $annee));
$mois_apres_url=date('m', mktime(0, 0, 0, $numero_mois + 1, 1, $annee));
$annee_apres_url=date('Y', mktime(0, 0, 0, $numero_mois + 1, 1, $annee));
$m = array("01" => "Janvier", "02" => "F&eacute;vrier", "03" => "Mars", "04" => "Avril");
$m += array("05" => "Mai", "06" => "Juin", "07" => "Juillet", "08" => "Août");
$m += array("09" => "Septembre", "10" => "Octobre", "11" => "Novembre", "12" => "D&eacute;cembre");
$mois_en_cours=date('m');//date("d/m/Y H:i:s")
echo '
<table class="table_inc">
  <tr>
    <td align="center" colspan="3">
    Date du jour : '.datebdd_nomjour(date("d/m/Y")).' '.date("d").' '.$m[$mois_en_cours].' '.date("Y").'
    </td>
  </tr>
  <tr align="center" class="titre">
  
    <td align="center">';
    calendrier($ROLE,$numero_mois,$annee,$date_premier_j_mois,$mois_avant_url,$annee_avant_url,$mois_apres_url,$annee_apres_url,$mysql_link);
    echo '</td>';
    echo '<td align="center">';
	if(!isset($_GET['m']) && !isset($_GET['y']))
	{
	// Si on ne r&eacute;cupère rien dans l'url, on prend la date du 1er jour du mois actuel.
	$date_premier_j_mois = mktime(0, 0, 0, date('m') + 1, 1, date('Y'));
	}else{
	// Sinon on r&eacute;cupère la date du 1er jour du mois donn&eacute;.
	$date_premier_j_mois = mktime(0, 0, 0, $_GET['m'] + 1 , 1, $_GET['y']);
	}
	$numero_mois = date('m', $date_premier_j_mois);
	$annee = date('Y', $date_premier_j_mois);
	calendrier($ROLE,$numero_mois,$annee,$date_premier_j_mois,$mois_avant_url,$annee_avant_url,$mois_apres_url,$annee_apres_url,$mysql_link);
    echo '</td>';
    echo '<td align="center">';
	if(!isset($_GET['m']) && !isset($_GET['y']))
	{
	// Si on ne r&eacute;cupère rien dans l'url, on prend la date du 1er jour du mois actuel.
	$date_premier_j_mois = mktime(0, 0, 0, date('m') + 2, 1, date('Y'));
	}else{
	// Sinon on r&eacute;cupère la date du 1er jour du mois donn&eacute;.
	$date_premier_j_mois = mktime(0, 0, 0, $_GET['m'] + 2 , 1, $_GET['y']);
	}
	$numero_mois = date('m', $date_premier_j_mois);
	$annee = date('Y', $date_premier_j_mois);
	calendrier($ROLE,$numero_mois,$annee,$date_premier_j_mois,$mois_avant_url,$annee_avant_url,$mois_apres_url,$annee_apres_url,$mysql_link);
	
    echo '</td>
    </tr>
    <tr>
    <td align="center" colspan="3">';
    $rq_info_type_demande="
	SELECT 
	`CHANGEMENT_DEMANDE_LIB`, 
	`CHANGEMENT_DEMANDE_EXEMPLE`, 
	`CHANGEMENT_DEMANDE_COULEUR_FOND`, 
	`CHANGEMENT_DEMANDE_COULEUR_TEXTE` 
	FROM `changement_demande`
	WHERE `ENABLE`='0'
	ORDER BY `CHANGEMENT_DEMANDE_LIB`";
	$res_rq_info_type_demande = mysql_query($rq_info_type_demande, $mysql_link) or die(mysql_error());
	$tab_rq_info_type_demande = mysql_fetch_assoc($res_rq_info_type_demande);
	$total_ligne_rq_info_type_demande=mysql_num_rows($res_rq_info_type_demande);
	
	if($total_ligne_rq_info_type_demande!=0){
	          echo '<table class="table_inc">';
		do {
	
		$CHANGEMENT_DEMANDE_LIB=$tab_rq_info_type_demande['CHANGEMENT_DEMANDE_LIB'];
		$CHANGEMENT_DEMANDE_EXEMPLE=$tab_rq_info_type_demande['CHANGEMENT_DEMANDE_EXEMPLE'];
		$CHANGEMENT_DEMANDE_COULEUR_FOND=$tab_rq_info_type_demande['CHANGEMENT_DEMANDE_COULEUR_FOND'];
		$CHANGEMENT_DEMANDE_COULEUR_TEXTE=$tab_rq_info_type_demande['CHANGEMENT_DEMANDE_COULEUR_TEXTE'];
		echo '
		<tr>
		<td bgcolor="#'.$CHANGEMENT_DEMANDE_COULEUR_FOND.'"  align="center">
		<FONT COLOR="#'.$CHANGEMENT_DEMANDE_COULEUR_TEXTE.'">'.$CHANGEMENT_DEMANDE_LIB.'</FONT>
		</td>
		</tr>';
		
		 } while ($tab_rq_info_type_demande = mysql_fetch_assoc($res_rq_info_type_demande));
	        $ligne= mysql_num_rows($res_rq_info_type_demande);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_type_demande, 0);
	          $tab_rq_info_type_demande = mysql_fetch_assoc($res_rq_info_type_demande);
	        }
	        echo '</table>';
	}
	mysql_free_result($res_rq_info_type_demande);
    echo '
    </td>
    </tr>
</table>
</div>';
mysql_close($mysql_link);
?>
