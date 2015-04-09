<?php
//redirection si acces dirrect
if(substr_count($_SERVER['PHP_SELF'], 'index.php')==0){
header("Location: ../");
exit();
}
//connexion base de donnees
require("./cf/conf_outil_icdc.php");
//require_once("./cf/fonctions.php");

// compter tous les enregistrements
$rq_info="
SELECT COUNT(`EXTRACT_DATA_JOB_CTLM`) AS `NB`
FROM `ctlm_extract_ctlm`
";
$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
$tab_rq_info = mysql_fetch_assoc($res_rq_info);
$total_ligne_rq_info=mysql_num_rows($res_rq_info); 
$NB_ALL=$tab_rq_info['NB'];
mysql_free_result($res_rq_info);

//requete liste des agents CONTROLM
$req_liste_agents = "
select count(`EXTRACT_DATA_NODEID`) as nbre, `EXTRACT_DATA_NODEID`
from `ctlm_extract_ctlm`
where `ENABLE` = 0
group by `EXTRACT_DATA_NODEID`
order by `EXTRACT_DATA_NODEID` ASC;";
$res_req_liste_agents = mysql_query($req_liste_agents, $mysql_link) or die(mysql_error());
$tab_req_liste_agents = mysql_fetch_assoc($res_req_liste_agents);
$total_ligne_req_liste_agents = mysql_num_rows($res_req_liste_agents);

// Debut page HTML
//centrage dans la page
echo '<div align="center">';

//affichage du tableau
$numLigne=1;
$class = 0;

if($total_ligne_req_liste_agents!=0)
{
  echo'
  <table class="table_inc">
  <tr align="center" class="titre">
  <td align="center" colspan="3"><h2>&nbsp;[&nbsp;<a href="./index.php?ITEM=corres_ctlm_Gestion_Jobs">Liste des JOBS CONTROLM</a>&nbsp;]&nbsp;-&nbsp;[&nbsp;<a href="#Bas_de_page">Fin</a>&nbsp;]</h2></td>
  </tr>
  ';
  echo'
  <tr align="center" class="titre">
  <td align="center">&nbsp;Agent CTLM :&nbsp;</a></td>
  <td align="center">&nbsp;Nbre de jobs ordonnancés :&nbsp;</td>
  <td align="center">&nbsp;Dernière MAJ :&nbsp;</td>
  </tr>
  ';
  do
  {
    // récupération valeur requete
    $AGENT = $tab_req_liste_agents['EXTRACT_DATA_NODEID'];
    $NBRE = $tab_req_liste_agents['nbre'];
    
    $req_date_maj = "
    select MAX(`MOTEUR_TRACE_DATE_TRI`) as `date`
    from `moteur_trace`
    where `MOTEUR_TRACE_ETAT` in(
    select `EXTRACT_NOM_FIC_IMPORT` 
    from `ctlm_extract_ctlm`
    where `EXTRACT_DATA_NODEID`='".$AGENT."')
    ";
    $res_req_date_maj = mysql_query($req_date_maj, $mysql_link) or die(mysql_error());
    $tab_req_date_maj = mysql_fetch_assoc($res_req_date_maj);
    $DATE = $tab_req_date_maj['date'];
    //echo $req_date_maj.'</BR>';
    if($DATE=='0'){$DATE_format = "---";}
    else {
    	if($DATE==''){$DATE_format = "---";}else{
	    	$date_a = substr($DATE,0,4);
	        $date_m = substr($DATE,4,2);
	        $date_d = substr($DATE,6,2);
	        $date_H = substr($DATE,8,2);
	        $date_M = substr($DATE,10,2);
	        $date_S = substr($DATE,12,2);
	        $DATE_format = ($date_d.'/'.$date_m.'/'.$date_a.' '.$date_H.':'.$date_M.':'.$date_S);
	}
    }
        
    //test affichage couleur ligne dans tableau
    if ($numLigne%2) { $class = "pair";} else { $class = "impair"; }
    
    //affichage tableau avec resultat de la requete
    echo '
    <tr class="'.$class.'">
    <td align="center"><a class="LinkDef" href="./index.php?ITEM=corres_ctlm_Gestion_Jobs&AGENT='.$AGENT.'">&nbsp;'.$AGENT.'&nbsp;</td>
    <td align="center">&nbsp;'.$NBRE.'&nbsp;</td>
    <td align="center">&nbsp;'.$DATE_format.'&nbsp;</td>
    </tr>';
    $numLigne++;
    }
    while ($tab_req_liste_agents = mysql_fetch_assoc($res_req_liste_agents));
  
  //$numLigne++;
  if ($numLigne%2) { $class = "pair";} else { $class = "impair"; }
  echo'
  <tr class="'.$class.'">
  <td align="center"></td>
  <td align="center">&nbsp;TOTAL: '.$NB_ALL.'&nbsp;</td>
  <td align="center"></td>
  </tr>
  <tr align="center" class="titre">
  <td align="center" colspan="3"><h2>[&nbsp;<a href="#Haut_de_page">Début</a>&nbsp;]&nbsp;</h2></td>
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
mysql_free_result($res_req_liste_agents);
mysql_close($mysql_link);
?>