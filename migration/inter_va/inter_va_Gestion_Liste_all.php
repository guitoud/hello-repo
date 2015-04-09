<?php
# redirection si acces dirrect
if(substr_count($_SERVER['PHP_SELF'], 'index.php')==0){
	header("Location: ../");
	exit();
}
# connexion base de donnees
require("./cf/conf_outil_icdc.php");
require_once("./cf/fonctions.php");

# récuperation date du jour
$date = date('Ymd');

if(isset($_GET['begin'])){
  $begin=$_GET['begin'];
}else{
  $begin=0;
}
$compteur_deb=0;
$compteur_rev=0;
# Gestion pages classiques pour les Administrateurs
# Affichage de TOUTES les interventions...
	
	$rq_info="
	SELECT COUNT(`VA_INTERVENTION_ID`) AS `NB`
	FROM `va_intervention`
	";
	$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
	$tab_rq_info = mysql_fetch_assoc($res_rq_info);
	$total_ligne_rq_info=mysql_num_rows($res_rq_info); 
	$NB_ALL=$tab_rq_info['NB'];
	mysql_free_result($res_rq_info);
	
	$req_liste_intervention = "
	select `VA_INTERVENTION_ID`,`VA_INTERVENTION_CODE_APPLI`,`VA_INTERVENTION_LIBELLE`,`VA_INTERVENTION_DATE_DEBUT`,`VA_INTERVENTION_DATE_FIN`, `ENABLE`
	from `va_intervention`
	order by `VA_INTERVENTION_DATE_DEBUT` DESC
	LIMIT ".$begin.",".$Var_max_resultat_page_limit.";";

	$res_req_liste_intervention = mysql_query($req_liste_intervention, $mysql_link) or die(mysql_error());
	$tab_req_liste_intervention = mysql_fetch_assoc($res_req_liste_intervention);
	$total_ligne_req_liste_intervention = mysql_num_rows($res_req_liste_intervention);

	# Debut page HTML
	# Centrage dans la page
	echo '<div align="center">';

	$numLigne=1;
	$class = 0;

	if($total_ligne_req_liste_intervention!=0)
	{
		echo'
		<table class="table_inc">
		<tr align="center" class="titre">
		<td align="center" colspan="5"><h2>&nbsp;[&nbsp;<a href="./index.php?ITEM=inter_va_Calendrier">Calendrier des Interventions de VA</a>&nbsp;]</h2></td>
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
			$ENABLE = $tab_req_liste_intervention['ENABLE'];
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
			
			if ($ENABLE == 1){
				$action="Info";
			}else{
				$action="Modif";
			}
			
			$req_infos_inter = "
			select `MOTEUR_TRACE_DATE`,`MOTEUR_TRACE_ACTION`,`MOTEUR_TRACE_REF_ID`,`NOM`,`PRENOM`
			from `moteur_utilisateur`, `moteur_trace`
			where `moteur_utilisateur`.`UTILISATEUR_ID` = `moteur_trace`.`MOTEUR_TRACE_UTILISATEUR_ID`
			and `MOTEUR_TRACE_CATEGORIE`='va'
			and `MOTEUR_TRACE_REF_ID`='".$ID_INTER."';";

			$res_req_infos_inter = mysql_query($req_infos_inter, $mysql_link) or die(mysql_error());
			$tab_req_infos_inter = mysql_fetch_assoc($res_req_infos_inter);
			$total_ligne_req_infos_inter = mysql_num_rows($res_req_infos_inter);
			$AFF_SPAN='';
			if ($total_ligne_req_infos_inter!=0)
			{
			  do
			  {
      		  	    $DATE = $tab_req_infos_inter['MOTEUR_TRACE_DATE'];
      			    $PRENOM = $tab_req_infos_inter['PRENOM'];
      			    $NOM = $tab_req_infos_inter['NOM'];
      			    $ACTION = $tab_req_infos_inter['MOTEUR_TRACE_ACTION'];
      			    # Gestion du SPAN
      			    $AFF_SPAN.=' '.$DATE.' - '.$PRENOM.' '.$NOM.' - '.$ACTION;
			    $AFF_SPAN.='</BR>';
			    
      			  }while ($tab_req_infos_inter = mysql_fetch_assoc($res_req_infos_inter));
      			}
      			  if (($compteur_deb==0) && ($DATE_FIN >= $date)){
			    echo '
		  	    <tr align="center" class="titre">
		  	    <td align="center" colspan="5">Interventions A Venir / En Cours : </td>
		  	    </tr>
		  	  ';
		  	  $compteur_deb ++;
		  	  }
		  	   if (($compteur_rev==0) && ($DATE_FIN < $date)){
			    echo '
		  	    <tr align="center" class="titre">
		  	    <td align="center" colspan="5">Interventions Révolues : </td>
		  	    </tr>
		  	  ';
		  	  $compteur_rev ++;
		  	  }
				if ($numLigne%2) { $class = "pair";} else { $class = "impair"; }
				if ($ENABLE == 1){
				echo '
				<tr class="'.$class.'">
				<td align="center"><a href="./index.php?ITEM=inter_va_Info_Intervention&action=Info&INTER='.$ID_INTER.'" class="infobulledroite"><FONT COLOR="#666666">&nbsp;'.$ID_INTER.'&nbsp;</FONT>
				<span>'.$AFF_SPAN.'</span>
				</a></td>
				<td align="center"><strike>&nbsp;'.$date_debut_format.'&nbsp;<strike></td>
				<td align="center"><strike>&nbsp;'.$date_fin_format.'&nbsp;<strike></td>
				<td align="center"><strike>&nbsp;'.$CODE_APPLI.'&nbsp;<strike></td>
				<td align="left"><strike>&nbsp;'.$NEW_LIB.'&nbsp;<strike></td>
				</tr>';
				$numLigne++;
				}
				else{
				echo '
				<tr class="'.$class.'">
				<td align="center"><a href="./index.php?ITEM=inter_va_Info_Intervention&action=Info&INTER='.$ID_INTER.'" class="infobulledroite"><FONT COLOR="#666666">&nbsp;'.$ID_INTER.'&nbsp;</FONT>
				<span>'.$AFF_SPAN.'</span>
				</a></td>
				<td align="center">&nbsp;'.$date_debut_format.'&nbsp;</td>
				<td align="center">&nbsp;'.$date_fin_format.'&nbsp;</td>
				<td align="center">&nbsp;'.$CODE_APPLI.'&nbsp;</td>
				<td align="left">&nbsp;'.$NEW_LIB.'&nbsp;</td>
				</tr>';
				$numLigne++;
				}
			
		}while ($tab_req_liste_intervention = mysql_fetch_assoc($res_req_liste_intervention));
		
		echo'
		<tr align="center" class="titre">
     		<td align="center" colspan="5">&nbsp;';
     			if($NB_ALL>$Var_max_resultat_page_limit){
        		makeListLink($NB_ALL,$Var_max_resultat_page_limit,"./index.php?ITEM=inter_va_Gestion_Liste_all",1);
      			}
      			echo '&nbsp;
      		</td>
  		</tr> 
  		</table>
  		';
	}
	else
	{
		echo'
		<table class="table_inc">
		<tr align="center" class="titre">
		<td align="center"><h2>&nbsp;Il n\'y a aucun enregistrement à afficher&nbsp;</h2></td>
		</tr>
		</table>';
	}
	echo '</div>';
	mysql_free_result($res_req_liste_intervention);
	mysql_close($mysql_link);
?>