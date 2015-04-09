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
echo '<div align="center">';
$numLigne=0;
if(!isset($_GET['m']) && !isset($_GET['y']))
{
	// Si on ne récupère rien dans l'url, on prend la date du 1er jour du mois actuel.
	$date_premier_j_mois = mktime(0, 0, 0, date('m'), 1, date('Y'));
}else{
// Sinon on récupère la date du 1er jour du mois donné.
$date_premier_j_mois = mktime(0, 0, 0, $_GET['m'], 1, $_GET['y']);
}

/* Si le mois et l'année de la variable $date_premier_j_mois correspondent au mois et à l'année d'aujourd'hui, on retient le jour actuel.
Sinon le jour actuel ne se situe pas dans le mois et on ne retient rien */
/*
if(date('m', $date_premier_j_mois) == date('m') && date('Y', $date_premier_j_mois) == date('Y'))
{
$coloreNum = date('d');
}
*/
$m = array("01" => "Janvier", "02" => "Février", "03" => "Mars", "04" => "Avril");
$m += array("05" => "Mai", "06" => "Juin", "07" => "Juillet", "08" => "Août");
$m += array("09" => "Septembre", "10" => "Octobre", "11" => "Novembre", "12" => "Décembre");

$JOURS_autre_mois = array('Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi');
// Souvenez-vous que les dates en PHP commencent par dimanche !

$numero_mois = date('m', $date_premier_j_mois);
$annee = date('Y', $date_premier_j_mois);
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
/*
if($numero_mois == 12)
{
	// Dans le cas du mois de décembre
	$annee_avant = $annee;
	$annee_apres = $annee + 1; // L'année d'après change
	$mois_avant = $numero_mois - 1;
	$mois_apres = 01; // Le mois d'après est janvier
}elseif($numero_mois == 01){
	// Dans le cas du mois de janvier
	$annee_avant = $annee - 1; // L'année d'avant change
	$annee_apres = $annee;
	$mois_avant = 12; // Le mois d'avant est décembre
	$mois_apres = $numero_mois + 1;
}else{
// Il ne s'agit ni de janvier ni de décembre
$annee_avant = $annee;
$annee_apres = $annee;
$mois_avant = $numero_mois - 1;
$mois_apres = $numero_mois + 1;
}
*/


// Lien pour aller au mois précédent
//echo '<a href="?m='.$mois_avant.'&amp;y='.$annee_avant.'"><<</a>';

// Affichage du mois et de l'année
//echo ' '.$m[$numero_mois].' '.$annee.' ';

// Lien pour aller au mois suivant
//echo '<a href="?m='.$mois_apres.'&amp;y='.$annee_apres.'">>></a>';

$numero_jour1er = date('w', $date_premier_j_mois); // 0 => Dimanche, 1 => Lundi, 2 = > Mardi...

// Changement du numéro du jour car l'array commence à l'indice 0.
if ($numero_jour1er == 0)
{
	//si c'est dimanche, on le place en 6e position (car on commencera notre boucle à 0)
	$numero_jour1er = 6;
}else{
// Sinon on met lundi à 0 ou mardi à 1 ou mercredi à 2...
$numero_jour1er--;
}

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

echo '
<table class="table_inc">
<tr align="center" class="titre">
<td align="center" colspan="8">
&nbsp;';
// Lien pour aller au mois précédent
echo '<a href="./index.php?ITEM=inter_va_Calendrier&amp;m='.$mois_avant.'&amp;y='.$annee_avant.'"><<</a>';

// Affichage du mois et de l'année
echo '<span style="color: #000000;"> '.$m[$numero_mois].' '.$annee.'</span> ';

// Lien pour aller au mois suivant
echo '<a href="./index.php?ITEM=inter_va_Calendrier&amp;m='.$mois_apres.'&amp;y='.$annee_apres.'">>></a>';
echo'
&nbsp;
</td>
</tr>
<tr align="center" class="titre">
<td>&nbsp;</td>
<td>&nbsp; Lundi  &nbsp;</td>
<td>&nbsp; Mardi  &nbsp;</td>
<td>&nbsp;Mercredi&nbsp;</td>
<td>&nbsp; Jeudi  &nbsp;</td>
<td>&nbsp;Vendredi&nbsp;</td>
<td>&nbsp; Samedi &nbsp;</td>
<td>&nbsp;Dimanche&nbsp;</td>
</tr>
';
function Return_info_semaine($ANNEE,$MOIS,$SEMAINE,$mysql_link)
{
	if(strlen($MOIS) == 1 ){
		$MOIS='0'.$MOIS;
	}

	$rq_info="
	SELECT `VA_DATE_SEMAINE`
	FROM `va_date`
	WHERE `VA_DATE` LIKE '".$ANNEE."%'
	AND `VA_DATE_SEMAINE` ='".$SEMAINE."'
	AND `ENABLE`='0'
	GROUP BY `VA_DATE_SEMAINE`, `VA_INTERVENTION_ID`
	";
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
function Return_info_jour($ANNEE,$MOIS,$JOURS,$SEMAINE,$INTERVENTION_ID,$mysql_link)
{
	if(strlen($JOURS) == 1 ){
		$JOURS='0'.$JOURS;
	}
	if(strlen($MOIS) == 1 ){
		$MOIS='0'.$MOIS;
	}
	if($INTERVENTION_ID==''){
		$INTERVENTION_ID_SQL='';
	}else{
	$INTERVENTION_ID_SQL="AND `VA_INTERVENTION_ID` ='".$INTERVENTION_ID."'";
	}
	$rq_info="
	SELECT *
	FROM `va_date`
	WHERE
	`VA_DATE` LIKE '".$ANNEE."".$MOIS."".$JOURS."'
	AND `VA_DATE_SEMAINE` ='".$SEMAINE."'
	".$INTERVENTION_ID_SQL."
	AND `ENABLE`='0'";
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
$JOURS_autre_mois=date('t',mktime(0, 0, 0, $mois_avant, 1, $annee_avant))-$numero_jour1er+1;
$numLigne = $numLigne + 1;
if ($numLigne%2) { $class = "pair";}else{$class = "impair";}
echo '<tr class="'.$class.'">'; // Nouvelle ligne du tableau (celle de la 1ère semaine, donc)
// Écriture de colonnes vides tant que le mois ne démarre pas.
$SEMAINE=date('W',mktime(0, 0, 0, $numero_mois, 1, $annee));
$DATE_DU_JOUR=date("Ymd");
echo '<td bgcolor="#8A9999"><FONT COLOR="#FFFFFF"><b>&nbsp;S'.$SEMAINE.'&nbsp;</b></FONT></td>';
/*
for($jour_du_mois = 0 ; $jour_du_mois < $numero_jour1er ; $jour_du_mois++)
{

	echo '<td>&nbsp;<a class="LinkDef" href="./index.php?ITEM=inter_va_Ajout_Intervention&action=Ajout&ANNEE='.$annee_avant.'&MOIS='.$mois_avant.'&JOUR='.$JOURS_autre_mois.'">'.$JOURS_autre_mois.'</a>&nbsp;</td>';
	$JOURS_autre_mois = $JOURS_autre_mois + 1 ;
}

for($jour_du_mois = 1 ; $jour_du_mois <= 7 - $numero_jour1er; $jour_du_mois++)
{
	echo '<td>&nbsp;<a class="LinkDef" href="./index.php?ITEM=inter_va_Ajout_Intervention&action=Ajout&ANNEE='.$annee.'&MOIS='.$numero_mois.'&JOUR='.$jour_du_mois.'">'.$jour_du_mois.'</a>&nbsp;</td>';
}
*/
for($jour_du_mois = 0 ; $jour_du_mois < $numero_jour1er ; $jour_du_mois++)
{
	if(strlen($mois_avant) == 1 ){
		$mois_avant='0'.$mois_avant;
	}
	if(strlen($JOURS_autre_mois) == 1 ){
		$JOURS_autre_mois='0'.$JOURS_autre_mois;
	}
	$DATE_URL=$annee_avant.''.$mois_avant.''.$JOURS_autre_mois;
	//if($DATE_URL>$DATE_DU_JOUR){
	echo '<td>&nbsp;<a class="LinkDef" href="./index.php?ITEM=inter_va_Ajout_Intervention&action=Ajout&ANNEE='.$annee_avant.'&MOIS='.$mois_avant.'&JOUR='.$JOURS_autre_mois.'">'.$JOURS_autre_mois.'</a>&nbsp;</td>';
	//}else{
	//echo '<td>&nbsp;'.$JOURS_autre_mois.'&nbsp;</td>';
	//}
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
	//if($DATE_URL>$DATE_DU_JOUR){
    echo '<td>&nbsp;<a class="LinkDef" href="./index.php?ITEM=inter_va_Ajout_Intervention&action=Ajout&ANNEE='.$annee.'&MOIS='.$numero_mois.'&JOUR='.$jour_du_mois.'">'.$jour_du_mois.'</a>&nbsp;</td>';
  // }else{
   // echo '<td>&nbsp;'.$jour_du_mois.'&nbsp;</td>';
   // }
}
echo '</tr>';
$NB_inter=Return_info_semaine($annee,$numero_mois,date('W',mktime(0, 0, 0, $numero_mois, 1, $annee)),$mysql_link);
$SEMAINE=date('W',mktime(0, 0, 0, $numero_mois, 1, $annee));
if($NB_inter>=1){
	$JOURS_autre_mois=date('t',mktime(0, 0, 0, $mois_avant, 1, $annee_avant))-$numero_jour1er+1;
	$rq_intervention_info="
	SELECT `va_date`.`VA_INTERVENTION_ID`,`va_intervention`.`VA_INTERVENTION_CODE_APPLI`
	FROM `va_date`, `va_intervention`
	WHERE `va_date`.`VA_INTERVENTION_ID` = `va_intervention`.`VA_INTERVENTION_ID`
	AND (`va_date`.`VA_DATE` LIKE '".$annee_avant."".$mois_avant."%' OR `va_date`.`VA_DATE` LIKE '".$annee."".$numero_mois."%')
        AND `va_date`.`VA_DATE_SEMAINE` ='".$SEMAINE."' 
	AND `va_date`.`ENABLE`='0'
	AND `va_intervention`.`ENABLE`='0'
	GROUP BY `va_date`.`VA_DATE_SEMAINE`, `va_date`.`VA_INTERVENTION_ID` ";
	$res_rq_intervention_info = mysql_query($rq_intervention_info, $mysql_link) or die(mysql_error());
	$tab_rq_intervention_info = mysql_fetch_assoc($res_rq_intervention_info);
	$total_ligne_rq_intervention_info=mysql_num_rows($res_rq_intervention_info);
	if($total_ligne_rq_intervention_info!=0){
		do {
			$INTERVENTION_ID=$tab_rq_intervention_info['VA_INTERVENTION_ID'];
			$INTERVENTION_CODE_APPLI=$tab_rq_intervention_info['VA_INTERVENTION_CODE_APPLI'];
			echo '<tr class="'.$class.'">'; // Nouvelle ligne du tableau (celle de la 1ère semaine, donc)
			// Écriture de colonnes du mois - 1  tant que le mois ne démarre pas.
			echo '<td bgcolor="#8A9999"><FONT COLOR="#FFFFFF">&nbsp;</FONT></td>';
			$JOURS_autre_mois=date('t',mktime(0, 0, 0, $mois_avant, 1, $annee_avant))-$numero_jour1er+1;
			for($jour_du_mois = 0 ; $jour_du_mois < $numero_jour1er ; $jour_du_mois++)
			{
				$NB=Return_info_jour($annee_avant,$mois_avant,$JOURS_autre_mois,$SEMAINE,$INTERVENTION_ID,$mysql_link);
				$AFF='';
				$AFF_TEST='';
				if($NB > 0 ){
					$AFF=$INTERVENTION_CODE_APPLI;
					$AFF_TEST=$INTERVENTION_ID%$NB_COULEUR;
					$AFF_SPAN='Détail de l\'intervention: ';
					$req_detail_intervention = "
					select *
					from `va_intervention`, `moteur_utilisateur`
					where `va_intervention`.`VA_INTERVENTION_UTILISATEUR_ID`=`moteur_utilisateur`.`UTILISATEUR_ID`
					and `va_intervention`.`ENABLE` = 0
					and `va_intervention`.`VA_INTERVENTION_ID` = '".$INTERVENTION_ID."'
					LIMIT 1";
					$res_req_detail_intervention = mysql_query($req_detail_intervention, $mysql_link) or die(mysql_error());
					$tab_req_detail_intervention = mysql_fetch_assoc($res_req_detail_intervention);
					$total_ligne_req_detail_intervention = mysql_num_rows($res_req_detail_intervention);

					if($total_ligne_req_detail_intervention==0){
						$AFF_SPAN='Info : '.$AFF;
					}else{
					$I_CODE_APPLI=$tab_req_detail_intervention['VA_INTERVENTION_CODE_APPLI'];
					$I_DATE_DEB = $tab_req_detail_intervention['VA_INTERVENTION_DATE_DEBUT'];
					$deb_jour = substr($I_DATE_DEB,6,2);
					$deb_mois = substr($I_DATE_DEB,4,2);
					$deb_year = substr($I_DATE_DEB,0,4);
					$date_debut_format = ($deb_jour.'/'.$deb_mois.'/'.$deb_year);
					$I_DATE_FIN = $tab_req_detail_intervention['VA_INTERVENTION_DATE_FIN'];
					$fin_jour = substr($I_DATE_FIN,6,2);
					$fin_mois = substr($I_DATE_FIN,4,2);
					$fin_year = substr($I_DATE_FIN,0,4);
					$date_fin_format = ($fin_jour.'/'.$fin_mois.'/'.$fin_year);
					
					$I_LIBELLE=$tab_req_detail_intervention['VA_INTERVENTION_LIBELLE'];
					$I_LIBELLE = html_entity_decode($I_LIBELLE);
					$nbre_car = strlen ($I_LIBELLE);
					if ($nbre_car < 50){
						$NEW_LIB = $I_LIBELLE;
					}
					else
					{
						$NEW_LIB = substr($I_LIBELLE,0,50);
						$NEW_LIB = $NEW_LIB.'...';
					}
					$NEW_LIB = htmlentities($NEW_LIB);
					
					$I_DATE_CRE=$tab_req_detail_intervention['VA_INTERVENTION_DATE_CREATION'];
					$I_USER=$tab_req_detail_intervention['LOGIN'];

					$AFF_SPAN='Application : '.$I_CODE_APPLI;
					$AFF_SPAN.='</BR>Date de d&eacute;but = '.$date_debut_format;
					$AFF_SPAN.='</BR>Date de fin = '.$date_fin_format;
					$AFF_SPAN.='</BR>Détail = '.$NEW_LIB;
					$AFF_SPAN.='</BR>Date de cr&eacute;ation = '.$I_DATE_CRE;
					$AFF_SPAN.='</BR>User = '.$I_USER;
				}
				if(isset($NBAFF[$INTERVENTION_ID])){
					$NBAFF[$INTERVENTION_ID]=$NBAFF[$INTERVENTION_ID]+1;
					$AFF='&nbsp;';
				}else{
				$NBAFF[$INTERVENTION_ID]=1;
			}
			echo '
			<td bgcolor="#'.$COULEUR_FOND[$AFF_TEST].'">
			<a href="./index.php?ITEM=inter_va_Modif_Intervention&action=Modif&INTER='.$INTERVENTION_ID.'" class="infobulledroite"><FONT COLOR="#'.$COULEUR_TEXTE[$AFF_TEST].'">'.$AFF.'</FONT>
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
	$NB=Return_info_jour($annee,$numero_mois,$jour_du_mois,$SEMAINE,$INTERVENTION_ID,$mysql_link);
	$AFF='';
	$AFF_TEST='';
	if($NB > 0 ){
		$AFF=$INTERVENTION_CODE_APPLI;
		$AFF_TEST=$INTERVENTION_ID%$NB_COULEUR;
		$AFF_SPAN='Détail de l\'intervention: ';
		$req_detail_intervention = "
		select *
		from `va_intervention`, `moteur_utilisateur`
		where `va_intervention`.`VA_INTERVENTION_UTILISATEUR_ID`=`moteur_utilisateur`.`UTILISATEUR_ID`
		and `va_intervention`.`ENABLE` = 0
		and `va_intervention`.`VA_INTERVENTION_ID` = '".$INTERVENTION_ID."'
		LIMIT 1";
		$res_req_detail_intervention = mysql_query($req_detail_intervention, $mysql_link) or die(mysql_error());
		$tab_req_detail_intervention = mysql_fetch_assoc($res_req_detail_intervention);
		$total_ligne_req_detail_intervention = mysql_num_rows($res_req_detail_intervention);

		if($total_ligne_req_detail_intervention==0){
			$AFF_SPAN='Info : '.$AFF;
		}else{
		$I_CODE_APPLI=$tab_req_detail_intervention['VA_INTERVENTION_CODE_APPLI'];
		$I_DATE_DEB = $tab_req_detail_intervention['VA_INTERVENTION_DATE_DEBUT'];
		$deb_jour = substr($I_DATE_DEB,6,2);
		$deb_mois = substr($I_DATE_DEB,4,2);
		$deb_year = substr($I_DATE_DEB,0,4);
		$date_debut_format = ($deb_jour.'/'.$deb_mois.'/'.$deb_year);
		$I_DATE_FIN = $tab_req_detail_intervention['VA_INTERVENTION_DATE_FIN'];
		$fin_jour = substr($I_DATE_FIN,6,2);
		$fin_mois = substr($I_DATE_FIN,4,2);
		$fin_year = substr($I_DATE_FIN,0,4);
		$date_fin_format = ($fin_jour.'/'.$fin_mois.'/'.$fin_year);
		
		$I_LIBELLE=$tab_req_detail_intervention['VA_INTERVENTION_LIBELLE'];
		$I_LIBELLE = html_entity_decode($I_LIBELLE);
		$nbre_car = strlen ($I_LIBELLE);
		if ($nbre_car < 50){
			$NEW_LIB = $I_LIBELLE;
		}
		else
		{
			$NEW_LIB = substr($I_LIBELLE,0,50);
			$NEW_LIB = $NEW_LIB.'...';
		}
		$NEW_LIB = htmlentities($NEW_LIB);
		
		$I_DATE_CRE=$tab_req_detail_intervention['VA_INTERVENTION_DATE_CREATION'];
		$I_USER=$tab_req_detail_intervention['LOGIN'];

		$AFF_SPAN='Application : '.$I_CODE_APPLI;
		$AFF_SPAN.='</BR>Date de d&eacute;but = '.$date_debut_format;
		$AFF_SPAN.='</BR>Date de fin = '.$date_fin_format;
		$AFF_SPAN.='</BR>Détail = '.$NEW_LIB;
		$AFF_SPAN.='</BR>Date de cr&eacute;ation = '.$I_DATE_CRE;
		$AFF_SPAN.='</BR>User = '.$I_USER;
	}
	if(isset($NBAFF[$INTERVENTION_ID])){
		$NBAFF[$INTERVENTION_ID]=$NBAFF[$INTERVENTION_ID]+1;
		$AFF='&nbsp;';
	}else{
	$NBAFF[$INTERVENTION_ID]=1;
}

echo '
<td bgcolor="#'.$COULEUR_FOND[$AFF_TEST].'">
<a href="./index.php?ITEM=inter_va_Modif_Intervention&action=Modif&INTER='.$INTERVENTION_ID.'" class="infobulledroite"><FONT COLOR="#'.$COULEUR_TEXTE[$AFF_TEST].'">'.$AFF.'</FONT>
<span>'.$AFF_SPAN.'</span>
</a>
</td>
';
}else{
echo '<td>'.$AFF.'</td>';
}
}
echo '</tr>';
} while ($tab_rq_intervention_info = mysql_fetch_assoc($res_rq_intervention_info));
$ligne= mysql_num_rows($res_rq_intervention_info);
if($ligne > 0) {
	mysql_data_seek($res_rq_intervention_info, 0);
	$tab_rq_intervention_info = mysql_fetch_assoc($res_rq_intervention_info);
}
}
mysql_free_result($res_rq_intervention_info);

}else{
echo '
<tr class="'.$class.'">
<td bgcolor="#8A9999" colspan="1"><FONT COLOR="#FFFFFF">&nbsp;</FONT></td>
<td align="center" colspan="7">&nbsp;</td>
</tr>';
}
$NB_jours_ok=$jour_du_mois;
$nbLignes = ceil((date('t', $date_premier_j_mois) - ($jour_du_mois-1)) / 7);
for($ligne = 0 ; $ligne < $nbLignes ; $ligne++)
{
	$SEMAINE=date('W',mktime(0, 0, 0, $numero_mois, $jour_du_mois, $annee));
	$NB_inter=Return_info_semaine($annee,$numero_mois,$SEMAINE,$mysql_link);
	
	$numLigne = $numLigne + 1;
	if ($numLigne%2) { $class = "pair";}else{$class = "impair";}
	echo '<tr class="'.$class.'">'; // Nouvelle ligne du tableau (celle de la nouvelle semaine)
	echo '<td bgcolor="#8A9999"><FONT COLOR="#FFFFFF"><b>&nbsp;S'.$SEMAINE.'&nbsp;</b></FONT></td>';
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
		      	//if($DATE_URL>$DATE_DU_JOUR){
		        	echo '<td>&nbsp;<a class="LinkDef" href="./index.php?ITEM=inter_va_Ajout_Intervention&action=Ajout&ANNEE='.$annee.'&MOIS='.$numero_mois.'&JOUR='.$jour_du_mois.'">'.$jour_du_mois.'</a>&nbsp;</td>';
		      	//}else{
		        //	echo '<td>&nbsp;'.$jour_du_mois.'&nbsp;</td>';
		      	//}
	//		echo '<td>&nbsp;<a class="LinkDef" href="./index.php?ITEM=inter_va_Ajout_Intervention&action=Ajout&ANNEE='.$annee.'&MOIS='.$numero_mois.'&JOUR='.$jour_du_mois.'">'.$jour_du_mois.'</a>&nbsp;</td>';
		}else{
			if($JOURS_autre_mois==0){
				$JOURS_autre_mois=1;
			}
			// On a fini d'écrire le mois on termine la tableau par des cellules du mois + 1
			if(strlen($mois_apres) == 1 ){
				$mois_apres='0'.$mois_apres;
			}
			if(strlen($JOURS_autre_mois) == 1 ){
				$JOURS_autre_mois='0'.$JOURS_autre_mois;
			}
			$DATE_URL=$annee_apres.''.$mois_apres.''.$JOURS_autre_mois;
			//if($DATE_URL>$DATE_DU_JOUR){
				echo '<td>&nbsp;<a class="LinkDef" href="./index.php?ITEM=inter_va_Ajout_Intervention&action=Ajout&ANNEE='.$annee_apres.'&MOIS='.$mois_apres.'&JOUR='.$JOURS_autre_mois.'">'.$JOURS_autre_mois.'</a>&nbsp;</td>';
			//}else{
			//	echo '<td>&nbsp;'.$JOURS_autre_mois.'&nbsp;</td>';
			//}
			//echo '<td>&nbsp;<a class="LinkDef" href="./index.php?ITEM=inter_va_Ajout_Intervention&action=Ajout&ANNEE='.$annee_apres.'&MOIS='.$mois_apres.'&JOUR='.$JOURS_autre_mois.'">'.$JOURS_autre_mois.'</a>&nbsp;</td>';
			$JOURS_autre_mois= $JOURS_autre_mois +1;
	}

	$jour_du_mois = $jour_du_mois +1;
}
echo '</tr>';
if($NB_inter >=1 ){
	$rq_intervention_info="
	SELECT `va_date`.`VA_INTERVENTION_ID`,`va_intervention`.`VA_INTERVENTION_CODE_APPLI`
	FROM `va_date`, `va_intervention`
	WHERE `va_date`.`VA_INTERVENTION_ID` = `va_intervention`.`VA_INTERVENTION_ID`
	AND (`va_date`.`VA_DATE` LIKE '".$annee."".$numero_mois."%' OR `va_date`.`VA_DATE` LIKE '".$annee_apres."".$mois_apres."%')
	AND `va_date`.`VA_DATE_SEMAINE` ='".$SEMAINE."'
	AND `va_date`.`ENABLE`='0'
	AND `va_intervention`.`ENABLE`='0'
	GROUP BY `va_date`.`VA_DATE_SEMAINE`, `va_date`.`VA_INTERVENTION_ID` ";
	$res_rq_intervention_info = mysql_query($rq_intervention_info, $mysql_link) or die(mysql_error());
	$tab_rq_intervention_info = mysql_fetch_assoc($res_rq_intervention_info);
	$total_ligne_rq_intervention_info=mysql_num_rows($res_rq_intervention_info);

	if($total_ligne_rq_intervention_info!=0){
		do {
			$INTERVENTION_ID=$tab_rq_intervention_info['VA_INTERVENTION_ID'];
			$INTERVENTION_CODE_APPLI=$tab_rq_intervention_info['VA_INTERVENTION_CODE_APPLI'];
			if(isset($NBAFF_S[$INTERVENTION_ID])){
				$AFF='&nbsp;';
				$NBAFF_S[$INTERVENTION_ID]=0;
			}else{
				$NBAFF_S[$INTERVENTION_ID]=0;
			}
			echo '<tr class="'.$class.'">'; // Nouvelle ligne du tableau (celle de la nouvelle semaine)
			echo '<td bgcolor="#8A9999"><FONT COLOR="#FFFFFF">&nbsp;</FONT></td>';
			$JOURS_autre_mois=0;
			$jour_du_mois=$NB_jours_ok;
			for($colone = 0 ; $colone < 7 ; $colone++)
			{
				if($jour_du_mois <= date('t', $date_premier_j_mois))
				{
					$NB=Return_info_jour($annee,$numero_mois,$jour_du_mois,$SEMAINE,$INTERVENTION_ID,$mysql_link);
					$AFF='';
					$AFF_TEST='';
					if($NB > 0 ){
						$AFF=$INTERVENTION_CODE_APPLI;
						$AFF_TEST=$INTERVENTION_ID%$NB_COULEUR;
						$AFF_SPAN='Détail de l\'intervention: ';
						$req_detail_intervention = "
						select *
						from `va_intervention`, `moteur_utilisateur`
						where `va_intervention`.`VA_INTERVENTION_UTILISATEUR_ID`=`moteur_utilisateur`.`UTILISATEUR_ID`
						and `va_intervention`.`ENABLE` = 0
						and `va_intervention`.`VA_INTERVENTION_ID` = '".$INTERVENTION_ID."'
						LIMIT 1";
						$res_req_detail_intervention = mysql_query($req_detail_intervention, $mysql_link) or die(mysql_error());
						$tab_req_detail_intervention = mysql_fetch_assoc($res_req_detail_intervention);
						$total_ligne_req_detail_intervention = mysql_num_rows($res_req_detail_intervention);

						if($total_ligne_req_detail_intervention==0){
							$AFF_SPAN='Info : '.$AFF;
						}else{
						$I_CODE_APPLI=$tab_req_detail_intervention['VA_INTERVENTION_CODE_APPLI'];
						$I_DATE_DEB = $tab_req_detail_intervention['VA_INTERVENTION_DATE_DEBUT'];
						$deb_jour = substr($I_DATE_DEB,6,2);
						$deb_mois = substr($I_DATE_DEB,4,2);
						$deb_year = substr($I_DATE_DEB,0,4);
						$date_debut_format = ($deb_jour.'/'.$deb_mois.'/'.$deb_year);
						$I_DATE_FIN = $tab_req_detail_intervention['VA_INTERVENTION_DATE_FIN'];
						$fin_jour = substr($I_DATE_FIN,6,2);
						$fin_mois = substr($I_DATE_FIN,4,2);
						$fin_year = substr($I_DATE_FIN,0,4);
						$date_fin_format = ($fin_jour.'/'.$fin_mois.'/'.$fin_year);
						
						$I_LIBELLE=$tab_req_detail_intervention['VA_INTERVENTION_LIBELLE'];
						$I_LIBELLE = html_entity_decode($I_LIBELLE);
						$nbre_car = strlen ($I_LIBELLE);
						if ($nbre_car < 50){
							$NEW_LIB = $I_LIBELLE;
						}
						else
						{
							$NEW_LIB = substr($I_LIBELLE,0,50);
							$NEW_LIB = $NEW_LIB.'...';
						}
						$NEW_LIB = htmlentities($NEW_LIB);

						$I_DATE_CRE=$tab_req_detail_intervention['VA_INTERVENTION_DATE_CREATION'];
						$I_USER=$tab_req_detail_intervention['LOGIN'];

						$AFF_SPAN='Application : '.$I_CODE_APPLI;
						$AFF_SPAN.='</BR>Date de d&eacute;but =	'.$date_debut_format;
						$AFF_SPAN.='</BR>Date de fin = '.$date_fin_format;
						$AFF_SPAN.='</BR>Détail	= '.$NEW_LIB;
						$AFF_SPAN.='</BR>Date de cr&eacute;ation = '.$I_DATE_CRE;
						$AFF_SPAN.='</BR>User =	'.$I_USER;
					}
					if(isset($NBAFF[$INTERVENTION_ID])){
						$NBAFF[$INTERVENTION_ID]=$NBAFF[$INTERVENTION_ID]+1;
						$AFF='&nbsp;';
					}else{
						$NBAFF[$INTERVENTION_ID]=1;
					}
					if(isset($NBAFF_S[$INTERVENTION_ID])){
						$NBAFF_S[$INTERVENTION_ID]=$NBAFF_S[$INTERVENTION_ID]+1;
						$AFF='&nbsp;';
					}else{
						$NBAFF_S[$INTERVENTION_ID]=1;
					}
					if($NBAFF_S[$INTERVENTION_ID]==1){$AFF=$INTERVENTION_CODE_APPLI;}
				echo '
				<td bgcolor="#'.$COULEUR_FOND[$AFF_TEST].'">
				<a href="./index.php?ITEM=inter_va_Modif_Intervention&action=Modif&INTER='.$INTERVENTION_ID.'" class="infobulledroite"><FONT COLOR="#'.$COULEUR_TEXTE[$AFF_TEST].'">'.$AFF.'</FONT>
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
	// On a fini d'écrire le mois on termine la tableau par des cellules du mois +1
	$NB=Return_info_jour($annee_apres,$mois_apres,$JOURS_autre_mois,$SEMAINE,$INTERVENTION_ID,$mysql_link);
	$AFF='';
	$AFF_TEST='';
	if($NB > 0 ){
		$AFF=$INTERVENTION_CODE_APPLI;
		$AFF_TEST=$INTERVENTION_ID%$NB_COULEUR;
		$AFF_SPAN='Détail de l\'intervention: ';
		$req_detail_intervention = "
		select *
		from `va_intervention`, `moteur_utilisateur`
		where `va_intervention`.`VA_INTERVENTION_UTILISATEUR_ID`=`moteur_utilisateur`.`UTILISATEUR_ID`
		and `va_intervention`.`ENABLE` = 0
		and `va_intervention`.`VA_INTERVENTION_ID` = '".$INTERVENTION_ID."'
		LIMIT 1";
		$res_req_detail_intervention = mysql_query($req_detail_intervention, $mysql_link) or die(mysql_error());
		$tab_req_detail_intervention = mysql_fetch_assoc($res_req_detail_intervention);
		$total_ligne_req_detail_intervention = mysql_num_rows($res_req_detail_intervention);

		if($total_ligne_req_detail_intervention==0){
			$AFF_SPAN='Info : '.$AFF;
		}else{
		$I_CODE_APPLI=$tab_req_detail_intervention['VA_INTERVENTION_CODE_APPLI'];
		$I_DATE_DEB = $tab_req_detail_intervention['VA_INTERVENTION_DATE_DEBUT'];
		$deb_jour = substr($I_DATE_DEB,6,2);
		$deb_mois = substr($I_DATE_DEB,4,2);
		$deb_year = substr($I_DATE_DEB,0,4);
		$date_debut_format = ($deb_jour.'/'.$deb_mois.'/'.$deb_year);
		$I_DATE_FIN = $tab_req_detail_intervention['VA_INTERVENTION_DATE_FIN'];
		$fin_jour = substr($I_DATE_FIN,6,2);
		$fin_mois = substr($I_DATE_FIN,4,2);
		$fin_year = substr($I_DATE_FIN,0,4);
		$date_fin_format = ($fin_jour.'/'.$fin_mois.'/'.$fin_year);

		$I_LIBELLE=$tab_req_detail_intervention['VA_INTERVENTION_LIBELLE'];
		$I_LIBELLE = html_entity_decode($I_LIBELLE);
		$nbre_car = strlen ($I_LIBELLE);
		if ($nbre_car < 50){
			$NEW_LIB = $I_LIBELLE;
		}
		else
		{
			$NEW_LIB = substr($I_LIBELLE,0,50);
			$NEW_LIB = $NEW_LIB.'...';
		}
		$NEW_LIB = htmlentities($NEW_LIB);
		
		$I_DATE_CRE=$tab_req_detail_intervention['VA_INTERVENTION_DATE_CREATION'];
		$I_USER=$tab_req_detail_intervention['LOGIN'];

		$AFF_SPAN='Application : '.$I_CODE_APPLI;
		$AFF_SPAN.='</BR>Date de d&eacute;but = '.$date_debut_format;
		$AFF_SPAN.='</BR>Date de fin = '.$date_fin_format;
		$AFF_SPAN.='</BR>Détail = '.$NEW_LIB;
		$AFF_SPAN.='</BR>Date de cr&eacute;ation = '.$I_DATE_CRE;
		$AFF_SPAN.='</BR>User = '.$I_USER;
	}
	if(isset($NBAFF[$INTERVENTION_ID])){
		$NBAFF[$INTERVENTION_ID]=$NBAFF[$INTERVENTION_ID]+1;
		$AFF='&nbsp;';
	}else{
	$NBAFF[$INTERVENTION_ID]=1;
}
echo '
<td bgcolor="#'.$COULEUR_FOND[$AFF_TEST].'">
<a href="./index.php?ITEM=inter_va_Modif_Intervention&action=Modif&INTER='.$INTERVENTION_ID.'" class="infobulledroite"><FONT COLOR="#'.$COULEUR_TEXTE[$AFF_TEST].'">'.$AFF.'</FONT>
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
} while ($tab_rq_intervention_info = mysql_fetch_assoc($res_rq_intervention_info));
$ligne_rq= mysql_num_rows($res_rq_intervention_info);
if($ligne_rq > 0) {
	mysql_data_seek($res_rq_intervention_info, 0);
	$tab_rq_intervention_info = mysql_fetch_assoc($res_rq_intervention_info);
}
}
mysql_free_result($res_rq_intervention_info);
}else{
echo '
<tr class="'.$class.'">
<td bgcolor="#8A9999" colspan="1"><FONT COLOR="#FFFFFF">&nbsp;</FONT></td>
<td align="center" colspan="7">&nbsp;</td>
</tr>';
}

}
echo '
<tr align="center" class="titre">
<td align="center" colspan="8">&nbsp;</td>
</tr>
</table>
</div>';
mysql_close($mysql_link);
?>