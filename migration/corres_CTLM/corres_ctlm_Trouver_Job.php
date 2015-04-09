<?php
//redirection si acces dirrect
if(substr_count($_SERVER['PHP_SELF'], 'index.php')==0){
header("Location: ../");
exit();
}
//connexion base de donnees
require("./cf/conf_outil_icdc.php");
//require_once("./cf/fonctions.php");

// Debut page HTML
//centrage dans la page
echo '<div align="center">';

$Select_Table='';
$Select_Rep='';
$Select_Group='';
$Select_Jctlm='';
$Select_Shell='';
$Select_Jappli='';

//affichage du tableau
$numLigne=1;
$class = 0;

if(isset($_GET['appli'])){
  $Select_Appli=$_GET['appli'];
  if ($Select_Appli=='0'){
      	$Select_Appli="";
      }
}else{
  $Select_Appli="";
}

$tab_var=$_POST;

if(empty($tab_var['btn'])){	
}else{
    if(isset($tab_var['table'])){
    $Select_Table=$tab_var['table'];
      if ($Select_Table=='0'){
      	$Select_Table="";
      }
    }
    if(isset($tab_var['group'])){
    $Select_Group=$tab_var['group'];
      if ($Select_Group=='0'){
      	$Select_Group="";
      }
    }
    if(isset($tab_var['rep'])){
    $Select_Rep=$tab_var['rep'];
      if ($Select_Rep=='0'){
      	$Select_Rep="";
      }
    }
    if(isset($tab_var['jctlm'])){
    $Select_Jctlm=$tab_var['jctlm'];
      if ($Select_Jctlm=='0'){
      	$Select_Jctlm="";
      }
    }
    if(isset($tab_var['shell'])){
    $Select_Shell=$tab_var['shell'];
      if ($Select_Shell=='0'){
      	$Select_Shell="";
      }
    }
    if(isset($tab_var['jappli'])){
    $Select_Jappli=$tab_var['jappli'];
      if ($Select_Jappli=='0'){
      	$Select_Jappli="";
      }
    }
      
     $req_liste_jobs = "
     select *
     from `ctlm_extract_ctlm`
     where `EXTRACT_DATA_APPLICATION` like '%$Select_Appli%'
     and `EXTRACT_DATA_TABLE_CTLM` like '%$Select_Table%'
     and `EXTRACT_DATA_GROUP_CTLM` like '%$Select_Group%'
     and `EXTRACT_DATA_REPERTOIRE` like '%$Select_Rep%'
     and `EXTRACT_DATA_JOB_CTLM` like '%$Select_Jctlm%'
     and `EXTRACT_DATA_SHELL` like '%$Select_Shell%'
     and `EXTRACT_DATA_APPLICATIF` like '%$Select_Jappli%'
     and `ENABLE`=0
     order by `EXTRACT_DATA_JOB_CTLM` ASC ;";
     $res_req_liste_jobs = mysql_query($req_liste_jobs, $mysql_link) or die(mysql_error());
     $tab_req_liste_jobs = mysql_fetch_assoc($res_req_liste_jobs);
     $total_ligne_req_liste_jobs = mysql_num_rows($res_req_liste_jobs);
}

if($total_ligne_req_liste_jobs==0){
 echo '
 <table class="table_inc" cellspacing="0" cellpading="0">
 <tr align="center" class="impair">
  <td></td>
 </tr>
 <tr align="center" class="impair">
  <td align="center" colspan="2" class="hostname_histo">La requete ne remonte aucun enregistrement</td>
 </tr>
 <tr align="center" class="impair">
   <td></td>
 </tr>
 <tr align="center" class="titre">
   <td colspan="2" align="center">
    <h2>&nbsp;[&nbsp;<a href="./index.php?ITEM=corres_ctlm_Chercher_Job&a='.$Select_Appli.'&t='.$Select_Table.'&g='.$Select_Group.'&r='.$Select_Rep.'&jc='.$Select_Jctlm.'&s='.$Select_Shell.'&ja='.$Select_Jappli.'">Retour à la Requete</a>&nbsp;]&nbsp;-&nbsp;&nbsp;[&nbsp;<a href="./index.php?ITEM=corres_ctlm_Chercher_Job">Retour - Chercher un Job</a>&nbsp;]&nbsp;</h2>
   </td>
 </tr>
 </table>';
}else{
  if (($Select_Appli=="")&&($Select_Table=="")&&($Select_Rep=="")&&($Select_Shell=="")&&($Select_Jctlm=="")&&($Select_Jappli=="")){
  echo '
    <table class="table_inc" cellspacing="0" cellpading="0">
      <tr align="center" class="impair">
      	<td></td>
      </tr>
      <tr align="center" class="impair">
        <td align="center" colspan="2" class="hostname_histo">Aucun critère de recherche n\'a été spécifié</td>
      </tr>
      <tr align="center" class="impair">
      	<td></td>
      </tr>
      <tr align="center" class="titre">
      <td colspan="2" align="center">
  	<h2>&nbsp;[&nbsp;<a href="./index.php?ITEM=corres_ctlm_Chercher_Job">Retour - Chercher un Job</a>&nbsp;]&nbsp;</h2>
      </td>
      </tr>
    </table>
    ';
  }else{
    echo '
    <table class="table_inc">
      <tr align="center" class="titre">
      <td align="center" colspan="11"><h2>&nbsp;[&nbsp;<a href="./index.php?ITEM=corres_ctlm_Chercher_Job&a='.$Select_Appli.'&t='.$Select_Table.'&g='.$Select_Group.'&r='.$Select_Rep.'&jc='.$Select_Jctlm.'&s='.$Select_Shell.'&ja='.$Select_Jappli.'">Retour à la Requete</a>&nbsp;]&nbsp;-&nbsp;[&nbsp;<a href="./index.php?ITEM=corres_ctlm_Chercher_Job">Nouvelle Recherche</a>&nbsp;]&nbsp;-&nbsp;[&nbsp;<a href="#Bas_de_page">Fin</a>&nbsp;]</h2></td>
      </tr>
      <tr align="center" class="titre">
      <td align="center">&nbsp;Application CTLM:&nbsp;</a></td>
      <td align="center">&nbsp;Table CTLM:&nbsp;</a></td>
      <td align="center">&nbsp;Groupe CTLM:&nbsp;</a></td>
      <td align="center">&nbsp;Répertoire Physique:&nbsp;</a></td>
      <td align="center">&nbsp;Shell UNIX:&nbsp;</td>
      <td align="center">&nbsp;Job CTLM:&nbsp;</td>
      <td align="center">&nbsp;NodeId:&nbsp;</a></td>
      <td align="center">&nbsp;Calendrier CTLM:&nbsp;</a></td>
      <td align="center">&nbsp;WCalendrier CTLM:&nbsp;</a></td>
      <td align="center">&nbsp;Scheduling CTLM:&nbsp;</a></td>
      <td align="center">&nbsp;Job Applicatif:&nbsp;</td>
      </tr>
    ';
    do
    {
      // récupération valeur requete
      $APPLI = $tab_req_liste_jobs['EXTRACT_DATA_APPLICATION'];
      $TABLE = $tab_req_liste_jobs['EXTRACT_DATA_TABLE_CTLM'];
      $GROUP = $tab_req_liste_jobs['EXTRACT_DATA_GROUP_CTLM'];
      $REP = $tab_req_liste_jobs['EXTRACT_DATA_REPERTOIRE'];
      $SHELL = $tab_req_liste_jobs['EXTRACT_DATA_SHELL'];
      $JCTLM = $tab_req_liste_jobs['EXTRACT_DATA_JOB_CTLM'];
      $NODEID = $tab_req_liste_jobs['EXTRACT_DATA_NODEID'];
      $CAL = $tab_req_liste_jobs['EXTRACT_DATA_CAL'];
      $WCAL = $tab_req_liste_jobs['EXTRACT_DATA_WCAL'];
      $SCHED = $tab_req_liste_jobs['EXTRACT_DATA_SCHEDULING'];
      $JAPPL = str_replace("~", "; ",$tab_req_liste_jobs['EXTRACT_DATA_APPLICATIF']);
      
      //test affichage couleur ligne dans tableau
      if ($numLigne%2) { $class = "pair";} else { $class = "impair"; }
      
      //affichage tableau avec resultat de la requete
      echo '
      <tr class="'.$class.'">
       <td align="center">&nbsp;'.$APPLI.'&nbsp;</td>
       <td align="center">&nbsp;'.$TABLE.'&nbsp;</td>
       <td align="center">&nbsp;'.$GROUP.'&nbsp;</td>
       <td align="center">&nbsp;'.$REP.'&nbsp;</td>
       <td align="center">&nbsp;'.$SHELL.'&nbsp;</td>
       <td align="center">&nbsp;'.$JCTLM.'&nbsp;</td>
       <td align="center">&nbsp;'.$NODEID.'&nbsp;</td>
       <td align="center">&nbsp;'.$CAL.'&nbsp;</td>
       <td align="center">&nbsp;'.$WCAL.'&nbsp;</td>
       <td align="center">&nbsp;'.$SCHED.'&nbsp;</td>
       <td align="left">&nbsp;'.$JAPPL.'&nbsp;</td>
      </tr>';
      $numLigne++;
      }
      while ($tab_req_liste_jobs = mysql_fetch_assoc($res_req_liste_jobs));
    
    //$numLigne++;
    if ($numLigne%2) { $class = "pair";} else { $class = "impair"; }
    echo'
    <tr align="center" class="titre">
      <td align="center" colspan="11"><h2>[&nbsp;<a href="#Haut_de_page">Début</a>&nbsp;]&nbsp;</h2></td>
    </tr>
    </table>
    ';
      
  }
}
echo '</div>';
mysql_close();
?>