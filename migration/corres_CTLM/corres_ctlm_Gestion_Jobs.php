<?php
//redirection si acces dirrect
if(substr_count($_SERVER['PHP_SELF'], 'index.php')==0){
header("Location: ../");
exit();
}
//connexion base de donnees
require("./cf/conf_outil_icdc.php");
require_once("./cf/fonctions.php");

if(isset($_GET['begin'])){
  $begin=$_GET['begin'];
}else{
  $begin=0;
}

if(isset($_GET['AGENT'])){
  $AGENT=$_GET['AGENT'];
  $sql="and `EXTRACT_DATA_NODEID`='$AGENT'";
  
  $rq_info="
  SELECT COUNT(`EXTRACT_DATA_JOB_CTLM`) AS `NB`
  FROM `ctlm_extract_ctlm`
  where `ENABLE`=0
  ".$sql."
  ";
  $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
  $tab_rq_info = mysql_fetch_assoc($res_rq_info);
  $total_ligne_rq_info=mysql_num_rows($res_rq_info); 
  $NB_ALL=$tab_rq_info['NB'];
  mysql_free_result($res_rq_info);
}else{
  $sql='';
  $AGENT='TOUS';
  
  $rq_info="
  SELECT COUNT(`EXTRACT_DATA_JOB_CTLM`) AS `NB`
  FROM `ctlm_extract_ctlm`
  where `ENABLE`=0
  ";
  $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
  $tab_rq_info = mysql_fetch_assoc($res_rq_info);
  $total_ligne_rq_info=mysql_num_rows($res_rq_info); 
  $NB_ALL=$tab_rq_info['NB'];
  mysql_free_result($res_rq_info);
}

//requete liste des agents CONTROLM
$req_liste_jobs = "
select `EXTRACT_DATA_APPLICATION`,`EXTRACT_DATA_SHELL`,`EXTRACT_DATA_JOB_CTLM`,`EXTRACT_DATA_APPLICATIF`
from `ctlm_extract_ctlm`
where `ENABLE`=0
".$sql."
order by `EXTRACT_DATA_JOB_CTLM` ASC
LIMIT ".$begin.",".$Var_max_resultat_page_limit.";";
$res_req_liste_jobs = mysql_query($req_liste_jobs, $mysql_link) or die(mysql_error());
$tab_req_liste_jobs = mysql_fetch_assoc($res_req_liste_jobs);
$total_ligne_req_liste_jobs = mysql_num_rows($res_req_liste_jobs);

// Debut page HTML
//centrage dans la page
echo '<div align="center">';

//affichage du tableau
$numLigne=1;
$class = 0;

if($total_ligne_req_liste_jobs!=0)
{
  echo'
  <table class="table_inc">
  <tr align="center" class="titre">
  <td align="center" colspan="4"><h2>&nbsp;[&nbsp;<a href="./index.php?ITEM=corres_ctlm_Gestion_Agent">Liste des Agents CONTROLM</a>&nbsp;]</h2></td>
  </tr>
  ';
  echo'
  <tr align="center" class="titre">
  <td align="center">&nbsp;Application CTLM :&nbsp;</td>
  <td align="center">&nbsp;Shell executé :&nbsp;</td>
  <td align="center">&nbsp;Nom Job CTLM :&nbsp;</td>
  <td align="center">&nbsp;Commandes Applicatives :&nbsp;</td>
  </tr>
  ';
  do
  {
    // récupération valeur requete
    $APPLI = $tab_req_liste_jobs['EXTRACT_DATA_APPLICATION'];
    $SHELL = $tab_req_liste_jobs['EXTRACT_DATA_SHELL'];
    $JCTLM = $tab_req_liste_jobs['EXTRACT_DATA_JOB_CTLM'];
    $JAPPL = str_replace("~", "; ",$tab_req_liste_jobs['EXTRACT_DATA_APPLICATIF']);
    //test affichage couleur ligne dans tableau
    if ($numLigne%2) { $class = "pair";} else { $class = "impair"; }
    
    //affichage tableau avec resultat de la requete
    echo '
    <tr class="'.$class.'">
    <td align="center">&nbsp;'.$APPLI.'&nbsp;</td>
    <td align="center">&nbsp;'.$SHELL.'&nbsp;</td>
    <td align="center">&nbsp;'.$JCTLM.'&nbsp;</td>
    <td align="left">&nbsp;'.$JAPPL.'&nbsp;</td>
    </tr>';
    $numLigne++;
    }
    while ($tab_req_liste_jobs = mysql_fetch_assoc($res_req_liste_jobs));
  
  echo'
  <tr align="center" class="titre">
  <td align="center" colspan="4">&nbsp;';
    if ($AGENT=='TOUS'){
      $LIEN="./index.php?ITEM=corres_ctlm_Gestion_Jobs";
    }else{
      $LIEN="./index.php?ITEM=corres_ctlm_Gestion_Jobs&AGENT=".$AGENT;
    }
    if($NB_ALL>$Var_max_resultat_page_limit){
      makeListLink($NB_ALL,$Var_max_resultat_page_limit,$LIEN,1);
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
mysql_free_result($res_req_liste_jobs);
mysql_close($mysql_link);
?>