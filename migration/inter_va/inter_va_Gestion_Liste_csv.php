<?php

header("Content-Type: application/csv-tab-delimited-table"); 
header("Content-disposition: filename=suivi.csv"); 

# connexion base de donnees
require("../cf/conf_outil_icdc.php");
require_once("../cf/fonctions.php");

echo 'Identifiant;Date de Début;Date de Fin;Application;Détail de l\'intervention;';
echo "\n";

# récuperation date du jour
$date = date('Ymd');

$req_liste_intervention = "
select `VA_INTERVENTION_ID`,`VA_INTERVENTION_CODE_APPLI`,`VA_INTERVENTION_LIBELLE`,`VA_INTERVENTION_DATE_DEBUT`,`VA_INTERVENTION_DATE_FIN`
from `va_intervention`
where `ENABLE` = 0
and `VA_INTERVENTION_DATE_FIN` > '".$date."'
order by `VA_INTERVENTION_DATE_DEBUT` ASC ;";
$res_req_liste_intervention = mysql_query($req_liste_intervention, $mysql_link) or die(mysql_error());
$tab_req_liste_intervention = mysql_fetch_assoc($res_req_liste_intervention);
$total_ligne_req_liste_intervention = mysql_num_rows($res_req_liste_intervention);

if($total_ligne_req_liste_intervention!=0)
{
	do
	{
	$ID_INTER = $tab_req_liste_intervention['VA_INTERVENTION_ID'];
	$CODE_APPLI = $tab_req_liste_intervention['VA_INTERVENTION_CODE_APPLI'];
	$LIBELLE = $tab_req_liste_intervention['VA_INTERVENTION_LIBELLE'];
	$DATE_DEB = $tab_req_liste_intervention['VA_INTERVENTION_DATE_DEBUT'];
	$deb_jour = substr($DATE_DEB,6,2);
	$deb_mois = substr($DATE_DEB,4,2);
	$deb_year = substr($DATE_DEB,0,4);
	$date_debut_format = ($deb_jour.'/'.$deb_mois.'/'.$deb_year);
	$DATE_FIN = $tab_req_liste_intervention['VA_INTERVENTION_DATE_FIN'];
	$fin_jour = substr($DATE_FIN,6,2);
	$fin_mois = substr($DATE_FIN,4,2);
	$fin_year = substr($DATE_FIN,0,4);
	$date_fin_format = ($fin_jour.'/'.$fin_mois.'/'.$fin_year);
	
	echo $ID_INTER.';'.$date_debut_format.';'.$date_fin_format.';'.$CODE_APPLI.';'.str_replace("\r", " ",str_replace(";", " ", str_replace("\n", " ", html_entity_decode(stripslashes($LIBELLE))))).';';
	echo "\n";

	}while ($tab_req_liste_intervention = mysql_fetch_assoc($res_req_liste_intervention));
	$ligne= mysql_num_rows($res_req_liste_intervention);
	if($ligne > 0) {
	  mysql_data_seek($res_req_liste_intervention, 0);
	  $tab_req_liste_intervention = mysql_fetch_assoc($res_req_liste_intervention);
	}
}
else
{
echo 'Aucune intervention à venir;';
}

mysql_free_result($res_req_liste_intervention);
mysql_close($mysql_link);

?>