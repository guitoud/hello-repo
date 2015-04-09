<?php
# redirection si acces dirrect
if(substr_count($_SERVER['PHP_SELF'], 'index.php')==0){
	header("Location: ../");
	exit();
}
# connexion base de donnees
require("./cf/conf_outil_icdc.php");

# récuperation date du jour
$date = date('Ymd');

# gestion pages historiques
if(isset($_GET['histo']))
{
	$histo=$_GET['histo'];
	if(isset($_GET['mois']))
	{
		$mois = $_GET['mois'];
	}
	if(isset($_GET['annee']))
	{
		$year = $_GET['annee'];
	}	
	
	$req_histo_intervention = "
	select `VA_INTERVENTION_ID`,`VA_INTERVENTION_CODE_APPLI`,`VA_INTERVENTION_LIBELLE`,`VA_INTERVENTION_DATE_DEBUT`,`VA_INTERVENTION_DATE_FIN`
	from `va_intervention`
	where `ENABLE` = 0
	and `VA_INTERVENTION_ID` in 
		(select `VA_INTERVENTION_ID`
		 from `va_date`
		 where `VA_DATE` like '%".$year."".$mois."%')
	order by `VA_INTERVENTION_DATE_DEBUT` ASC ;";
	$res_req_histo_intervention = mysql_query($req_histo_intervention, $mysql_link) or die(mysql_error());
	$tab_req_histo_intervention = mysql_fetch_assoc($res_req_histo_intervention);
	$total_ligne_req_histo_intervention = mysql_num_rows($res_req_histo_intervention);

	# Debut page HTML
	# centrage dans la page
	echo '<div align="center">';

	# affichage du tableau des historiques
	$numLigne=1;
	$class = 0;

	if($total_ligne_req_histo_intervention!=0)
	{
		echo'
		<table class="table_inc">
		<tr align="center" class="titre">
		<td align="center" colspan="5"><h2>&nbsp;[&nbsp;<a href="./index.php?ITEM=inter_va_Calendrier&y='.$year.'&m='.$mois.'">Calendrier des Interventions de VA</a>&nbsp;]&nbsp;-&nbsp;[&nbsp;<a href="#Bas_de_page">Fin</a>&nbsp;]</h2></td>
		</tr>
		<tr align="center" class="titre">
		<td align="center">&nbsp;Identifiant&nbsp;</td>
		<td align="center">&nbsp;Date de début&nbsp;</td>
		<td align="center">&nbsp;Date de fin&nbsp;</td>
		<td align="center">&nbsp;Application&nbsp;</td>
		<td align="center">&nbsp;Détail de l\'intervention&nbsp;</td>
		</tr>
		';
		do
		{
			$ID_INTER = $tab_req_histo_intervention['VA_INTERVENTION_ID'];
			$CODE_APPLI = $tab_req_histo_intervention['VA_INTERVENTION_CODE_APPLI'];
			
			$LIBELLE = $tab_req_histo_intervention['VA_INTERVENTION_LIBELLE'];
			$LIBELLE = html_entity_decode($LIBELLE);
				$nbre_car = strlen ($LIBELLE);
				if ($nbre_car < 70){
					$NEW_LIB = $LIBELLE;
				}
				else
				{
					$NEW_LIB = substr($LIBELLE,0,67);
					$NEW_LIB = $NEW_LIB.'...';
				}
			$NEW_LIB = htmlentities($NEW_LIB);
				
			$DATE_DEB = $tab_req_histo_intervention['VA_INTERVENTION_DATE_DEBUT'];
			$deb_jour = substr($DATE_DEB,6,2);
			$deb_mois = substr($DATE_DEB,4,2);
			$deb_year = substr($DATE_DEB,0,4);
			$date_debut_format = ($deb_jour.'/'.$deb_mois.'/'.$deb_year);

			$DATE_FIN = $tab_req_histo_intervention['VA_INTERVENTION_DATE_FIN'];
			$fin_jour = substr($DATE_FIN,6,2);
			$fin_mois = substr($DATE_FIN,4,2);
			$fin_year = substr($DATE_FIN,0,4);
			$date_fin_format = ($fin_jour.'/'.$fin_mois.'/'.$fin_year);

			if ($numLigne%2) { $class = "pair";} else { $class = "impair"; }

			echo '
			<tr class="'.$class.'">
			<td align="center"><a class="LinkDef" href="./index.php?ITEM=inter_va_Modif_Intervention&action=Modif&INTER='.$ID_INTER.'">&nbsp;'.$ID_INTER.'&nbsp;</a></td>
			<td align="center">&nbsp;'.$date_debut_format.'&nbsp;</td>
			<td align="center">&nbsp;'.$date_fin_format.'&nbsp;</td>
			<td align="center">&nbsp;'.$CODE_APPLI.'&nbsp;</td>
			<td align="left">&nbsp;'.$NEW_LIB.'&nbsp;</td>
			</tr>';
			$numLigne++;
		}
		while ($tab_req_histo_intervention = mysql_fetch_assoc($res_req_histo_intervention));
		
		echo'
		<tr align="center" class="titre">
		  <td align="center" colspan="5"><h2>[&nbsp;<a href="#Haut_de_page">Début</a>&nbsp;]&nbsp;</h2></td>
		</tr>
		';
	}
	else
	{
		echo'
		<table class="table_inc">
		<tr align="center" class="titre">
		<td align="center"><h2>&nbsp;Il n\'y a aucun enregistrement à afficher&nbsp;</h2></td>
		</tr>';
	}
	mysql_free_result($res_req_histo_intervention);
	mysql_close($mysql_link);
}
# gestion pages classiques
else
{

	$req_liste_intervention = "
	select `VA_INTERVENTION_ID`,`VA_INTERVENTION_CODE_APPLI`,`VA_INTERVENTION_LIBELLE`,`VA_INTERVENTION_DATE_DEBUT`,`VA_INTERVENTION_DATE_FIN`
	from `va_intervention`
	where `ENABLE` = 0
	and `VA_INTERVENTION_DATE_FIN` > '".$date."'
	order by `VA_INTERVENTION_DATE_DEBUT` ASC ;";

	$res_req_liste_intervention = mysql_query($req_liste_intervention, $mysql_link) or die(mysql_error());
	$tab_req_liste_intervention = mysql_fetch_assoc($res_req_liste_intervention);
	$total_ligne_req_liste_intervention = mysql_num_rows($res_req_liste_intervention);

	# Debut page HTML
	# centrage dans la page
	echo '<div align="center">';

	$numLigne=1;
	$class = 0;

	if($total_ligne_req_liste_intervention!=0)
	{
		echo'
		<table class="table_inc">
		<tr align="center" class="titre">
		<td align="center" colspan="1"><h2><a href="./inter_va/inter_va_Gestion_Liste_csv.php"><img src="./img/logo_excel.png" border="0"/></a></td>
		<td align="center" colspan="4"><h2>&nbsp;[&nbsp;<a href="./index.php?ITEM=inter_va_Calendrier">Calendrier des Interventions de VA</a>&nbsp;]&nbsp;-&nbsp;[&nbsp;<a href="#Bas_de_page">Fin</a>&nbsp;]</h2></td>
		</tr>
		';
		echo'
		<tr align="center" class="titre">
		<td align="center">&nbsp;Identifiant&nbsp;</td>
		<td align="center">&nbsp;Date de début&nbsp;</td>
		<td align="center">&nbsp;Date de fin&nbsp;</td>
		<td align="center">&nbsp;Application&nbsp;</td>
		<td align="center">&nbsp;Détail de l\'intervention&nbsp;</td>
		</tr>
		';
		do
		{
			$ID_INTER = $tab_req_liste_intervention['VA_INTERVENTION_ID'];
			$CODE_APPLI = $tab_req_liste_intervention['VA_INTERVENTION_CODE_APPLI'];
			
			$LIBELLE = $tab_req_liste_intervention['VA_INTERVENTION_LIBELLE'];
			$LIBELLE = html_entity_decode($LIBELLE);
				$nbre_car = strlen ($LIBELLE);
				if ($nbre_car < 70){
					$NEW_LIB = $LIBELLE;
				}
				else
				{
					$NEW_LIB = substr($LIBELLE,0,67);
					$NEW_LIB = $NEW_LIB.'...';
				}
			$NEW_LIB = htmlentities($NEW_LIB);
				
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

			if ($numLigne%2) { $class = "pair";} else { $class = "impair"; }

			echo '
			<tr class="'.$class.'">
			<td align="center"><a class="LinkDef" href="./index.php?ITEM=inter_va_Modif_Intervention&action=Modif&INTER='.$ID_INTER.'">&nbsp;'.$ID_INTER.'&nbsp;</a></td>
			<td align="center">&nbsp;'.$date_debut_format.'&nbsp;</td>
			<td align="center">&nbsp;'.$date_fin_format.'&nbsp;</td>
			<td align="center">&nbsp;'.$CODE_APPLI.'&nbsp;</td>
			<td align="left">&nbsp;'.$NEW_LIB.'&nbsp;</td>
			</tr>';
			$numLigne++;
		}
		while ($tab_req_liste_intervention = mysql_fetch_assoc($res_req_liste_intervention));
		
		echo'
		<tr align="center" class="titre">
		  <td align="center" colspan="5"><h2>[&nbsp;<a href="#Haut_de_page">Début</a>&nbsp;]&nbsp;</h2></td>
		</tr>
		</table>
		';
	}else{
		echo'
		<table class="table_inc">
		<tr align="center" class="titre">
		<td align="center"><h2>&nbsp;Il n\'y a aucun enregistrement à afficher&nbsp;</h2></td>
		</tr>
		</table>';
	}
	mysql_free_result($res_req_liste_intervention);
	mysql_close($mysql_link);
}
echo '</div>';
?>