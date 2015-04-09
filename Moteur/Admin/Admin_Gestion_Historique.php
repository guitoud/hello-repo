<?PHP
//redirection si acces dirrect
if(substr_count($_SERVER['PHP_SELF'], 'index.php')==0){
  header("Location: ../"); 
  exit(); 
}
/*******************************************************************
   Interface Gestion des Modifications
   Version 1.0.0    
  24/12/2009 - VGU - Creation fichier
**************************************************Guibert Vincent***
*******************************************************************/

require("./cf/conf_outil_icdc.php"); 
require("./cf/fonctions.php");
$j=0;
if(isset($_GET['begin'])){
  $begin=$_GET['begin'];
}else{
  $begin=0;
}
echo '
 <div align="center">
	<br/>
  <table class="table_inc" cellspacing="0" cellpading="0">
    <tr align="center" class="titre">
      <td align="center" colspan="6"><b>&nbsp;Gestion des modifications&nbsp;</b></td>
    </tr>
    <tr align="center" class="titre">
      <td align="center"><b>&nbsp;Date&nbsp;</b></td>
      <td align="center"><b>&nbsp;Login&nbsp;</b></td>
      <td align="center"><b>&nbsp;Nom&nbsp;</b></td>
      <td align="center"><b>&nbsp;Pr&eacute;nom&nbsp;</b></td>
      <td align="center"><b>&nbsp;Type&nbsp;</b></td>
      <td align="center"><b>&nbsp;Table&nbsp;</b></td>
    </tr>';
    $rq_info="
    SELECT COUNT(`HISTORIQUE_ID`) AS `NB`
    FROM `moteur_historique`
     ";
    $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
    $tab_rq_info = mysql_fetch_assoc($res_rq_info);
    $total_ligne_rq_info=mysql_num_rows($res_rq_info); 
    $NB_ALL=$tab_rq_info['NB'];
    mysql_free_result($res_rq_info);
    $rq_info="
    SELECT 
    `moteur_historique`.`HISTORIQUE_ID`, 
    `moteur_historique`.`HISTORIQUE_DATE`, 
    `moteur_historique`.`HISTORIQUE_LOGIN`, 
    `moteur_utilisateur`.`NOM`, 
    `moteur_utilisateur`.`PRENOM`,
    `moteur_historique`.`HISTORIQUE_TABLE`, 
    `moteur_historique`.`HISTORIQUE_TYPE`,
    `moteur_historique`.`HISTORIQUE_SQL`
    FROM `moteur_historique`, `moteur_utilisateur` 
    WHERE `moteur_historique`.`HISTORIQUE_LOGIN`=`moteur_utilisateur`.`LOGIN`
    ORDER BY `moteur_historique`.`HISTORIQUE_DATE` DESC
    LIMIT ".$begin.",".$Var_max_resultat_page.";";
    $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
    $tab_rq_info = mysql_fetch_assoc($res_rq_info);
    $total_ligne_rq_info=mysql_num_rows($res_rq_info); 
    if($total_ligne_rq_info!=0){
      do {
        $ID=$tab_rq_info['HISTORIQUE_ID'];
        $HISTORIQUE_DATE=$tab_rq_info['HISTORIQUE_DATE'];
        $HISTORIQUE_LOGIN=$tab_rq_info['HISTORIQUE_LOGIN'];
        $NOM=$tab_rq_info['NOM'];
        $PRENOM=$tab_rq_info['PRENOM'];
        $HISTORIQUE_TABLE=$tab_rq_info['HISTORIQUE_TABLE'];
        $HISTORIQUE_TYPE=$tab_rq_info['HISTORIQUE_TYPE'];
        $HISTORIQUE_SQL=$tab_rq_info['HISTORIQUE_SQL'];
        $jour=substr($HISTORIQUE_DATE,6,2);
        $mois=substr($HISTORIQUE_DATE,4,2);
        $annee=substr($HISTORIQUE_DATE,0,4);
        $heure=substr($HISTORIQUE_DATE,8,2);
        $minutes=substr($HISTORIQUE_DATE,10,2);
        $seconde=substr($HISTORIQUE_DATE,12,2);

        $j++;
        if ($j%2) { $class = "pair";}else{$class = "impair";} 
        echo '
        <tr align="center" class='.$class.'>
          <td align="left"><a name="'.$ID.'"><a class="LinkDef" href=./index.php?ITEM=Admin_Gestion_Historique_SQL&ID='.$ID.'>&nbsp;'.$jour.'/'.$mois.'/'.$annee.' - '.$heure.' h '.$minutes.' mn '.$seconde.' s&nbsp;</a></td>
          <td align="left">&nbsp;'.stripslashes($HISTORIQUE_LOGIN).'&nbsp;</td>
          <td align="left">&nbsp;'.stripslashes($NOM).'&nbsp;</td>
          <td align="left">&nbsp;'.stripslashes($PRENOM).'&nbsp;</td>
          <td align="left">&nbsp;'.stripslashes($HISTORIQUE_TYPE).'&nbsp;</td>
          <td align="left">&nbsp;'.stripslashes($HISTORIQUE_TABLE).'&nbsp;</td>
        </tr>';

      } while ($tab_rq_info = mysql_fetch_assoc($res_rq_info));
      $ligne= mysql_num_rows($res_rq_info);
      if($ligne > 0) {
        mysql_data_seek($res_rq_info, 0);
        $tab_rq_info = mysql_fetch_assoc($res_rq_info);
      }
    }else{
    echo '
    <tr align="center" class="titre">
      <td align="center" colspan="6">&nbsp;Pas d\'information dans la base.&nbsp;</td>
    </tr>';
    }
    if($NB_ALL==0){
    echo '
    <tr align="center" class="titre">
      <td align="center" colspan="6">&nbsp;</td>
    </tr>';
    }else{
    echo '
    <tr align="center" class="titre">
      <td align="center" colspan="6">&nbsp;</td>
    </tr>
    <tr align="center" class="titre">
      <td align="center" colspan="6">&nbsp;';
      makeListLink($NB_ALL,$Var_max_resultat_page,"./index.php?ITEM=Admin_Gestion_Historique",1);
      echo '&nbsp;</td>
    </tr>
    <tr align="center" class="titre">
      <td align="center" colspan="6">&nbsp;</td>
    </tr>';
    }
    echo '
  </table>
</div>
';