<?PHP
//redirection si acces dirrect
if(substr_count($_SERVER['PHP_SELF'], 'index.php')==0){
  header("Location: ../");
  exit();
}
/*******************************************************************
   Interface Historique des changements
   Version 1.0.0  
  30/09/2010 - VGU - Creation fichier
**************************************************Guibert Vincent***
*******************************************************************/

require("./cf/conf_outil_icdc.php"); 

$numLigne=0;
$date_lib='';
$date_lib_old='';

$rq_lastdate_info="
SELECT DISTINCT(LEFT(`CHANGEMENT_DATE`,6)) AS `CHANGEMENT_DATE` 
FROM `changement_date` 
WHERE `ENABLE` =0
ORDER BY `CHANGEMENT_DATE` DESC 
";
$res_rq_lastdate_info = mysql_query($rq_lastdate_info, $mysql_link) or die(mysql_error());
$tab_rq_lastdate_info = mysql_fetch_assoc($res_rq_lastdate_info);
$total_ligne_rq_lastdate_info=mysql_num_rows($res_rq_lastdate_info); 
echo '
  <div align="center">
    <table class="table_inc">
      <tr align="center" class="titre">
	<td align="center"><h2>&nbsp;[&nbsp;Historique des changements&nbsp;]&nbsp;-&nbsp;[&nbsp;<a href="#Bas_de_page">Fin</a>&nbsp;]&nbsp;</h2></td>
      </tr>';
       
        if($total_ligne_rq_lastdate_info==0){
          $numLigne = $numLigne + 1;
          // traITEMent des couleurs une ligne sur 2
          if ($numLigne%2) { $class = "pair";}else {$class = "impair";}
          echo '
          <tr align="center" class="'.$class.'">
            <td>
            Pas d\'historique
            </td>
          </tr>';
        }else{
          do {
            $date=$tab_rq_lastdate_info['CHANGEMENT_DATE'];
            $mois=substr($date,4,2);
            $annee=substr($date,0,4);
            $NomMois=date('M',mktime(12, 0, 0, $mois,01, $annee));
            switch ($NomMois)
            {
            case "Jan": $NomMois = "Janvier"; break;
            case "Feb": $NomMois = "F&eacute;vrier"; break;
            case "Mar": $NomMois = "Mars"; break;
            case "Apr": $NomMois = "Avril"; break;
            case "May": $NomMois = "Mai"; break;
            case "Jun": $NomMois = "Juin"; break;
            case "Jul": $NomMois = "Juillet"; break;
            case "Aug": $NomMois = "Ao&ucirc;t"; break;
            case "Sep": $NomMois = "Septembre"; break;
            case "Oct": $NomMois = "Octobre"; break;
            case "Nov": $NomMois = "Novembre"; break;
            case "Dec": $NomMois = "D&eacute;cembre"; break;
            }
            
              $numLigne = $numLigne + 1;
                // traITEMent des couleurs une ligne sur 2
              if ($numLigne%2) { $class = "pair";}else {$class = "impair";}
              echo '
              <tr align="center" class="'.$class.'">
                <td>
                  <a class="LinkModif" href=./index.php?ITEM=changement_Gestion_Liste&annee='.$annee.'&mois='.$mois.'&histo=histo>Changements de '.$NomMois.' '.$annee.'</a>
                </td>
              </tr>';
              $date_lib_old=$date_lib;
          } while ($tab_rq_lastdate_info = mysql_fetch_assoc($res_rq_lastdate_info));
          $ligne= mysql_num_rows($res_rq_lastdate_info);
          if($ligne > 0) {
            mysql_data_seek($res_rq_lastdate_info, 0);
            $tab_rq_lastdate_info = mysql_fetch_assoc($res_rq_lastdate_info);
          }
        }
        mysql_free_result($res_rq_lastdate_info);
       echo '
        <tr align="center" class="titre">
          <td align="center">
          <h2>&nbsp;[&nbsp;<a href="#Haut_de_page">D&eacute;but</a>&nbsp;]&nbsp;</h2>
          </td>
        </tr>
      </table>
		<br/>
    </div>';

mysql_close($mysql_link); 
?>