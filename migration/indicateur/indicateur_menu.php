<?PHP
//redirection si acces dirrect
if(substr_count($_SERVER['PHP_SELF'], 'index.php')==0){
  header("Location: ../"); 
  exit(); 
}
/*******************************************************************
   Interface Gestion des indicateurs
   Version 1.0.0   
  28/01/2010 - VGU - Creation fichier
**************************************************Guibert Vincent***
*******************************************************************/

require("./cf/conf_outil_icdc.php"); 
require_once("./cf/fonctions.php");
require_once("./cf/autre_fonctions.php");
$j=0;
$ID='';
  echo '
   <div align="center">
    <br/>
    <table class="table_inc" cellspacing="1" cellpading="0">
     <tr align="center" class="titre">
        <td align="center" colspan="2">
        <b>&nbsp;Rapport Exploitation Applicative Centre de production DDR&nbsp;</b>
        </td>
      </tr>';
//Rapport
     $rq_info_anne="
      SELECT DISTINCT(LEFT(`DATE_INDICATEUR`,4)) AS `ANNEE` 
      FROM `indicateur_calcul` 
      WHERE `DATE_INDICATEUR`>200900
      ORDER BY `ANNEE` DESC 
      ";
      $res_rq_info_anne = mysql_query($rq_info_anne, $mysql_link) or die(mysql_error());
      $tab_rq_info_anne = mysql_fetch_assoc($res_rq_info_anne);
      $total_ligne_rq_info_anne=mysql_num_rows($res_rq_info_anne); 
      if ($total_ligne_rq_info_anne==0){
        $j++;
        if ($j%2) { $class = "pair";}else{$class = "impair";} 
        echo '
        <tr align="center" class='.$class.'>
          <td align="left" colspan="2">&nbsp;Pas d\'information dans la base&nbsp;</a></td>
        </tr>';
      }else{
      
      do {
        $ANNEE=$tab_rq_info_anne['ANNEE'];
        $rq_info_mois="
	SELECT DISTINCT (RIGHT(`DATE_INDICATEUR`, 2 )) AS `MOIS` 
	FROM `indicateur_calcul` 
	WHERE `DATE_INDICATEUR` LIKE '".$ANNEE."%'
	ORDER BY `MOIS` DESC 
      ";
      $res_rq_info_mois = mysql_query($rq_info_mois, $mysql_link) or die(mysql_error());
      $tab_rq_info_mois = mysql_fetch_assoc($res_rq_info_mois);
      $total_ligne_rq_info_mois=mysql_num_rows($res_rq_info_mois); 
       do {
        $j++;
        if ($j%2) { $class = "pair";}else{$class = "impair";} 
        $MOIS=$tab_rq_info_mois['MOIS'];
        if($MOIS<10){$MOIS=substr($MOIS, -1);}
         echo '
        <tr align="center" class='.$class.'>
          <td align="center" colspan="2"><a class="LinkDef" href=./index.php?ITEM=indicateur_rapport_gec_ddr&ANNEE='.$ANNEE.'&MOIS='.$MOIS.'>&nbsp;Rapport de '.$Tab_des_Mois[$MOIS-1].' '.$ANNEE.'&nbsp;</a> - <a class="LinkDef" href=./index.php?ITEM=indicateur_rapport_gec_ddr_new&ANNEE='.$ANNEE.'&MOIS='.$MOIS.'>&nbsp;New Rapport de '.$Tab_des_Mois[$MOIS-1].' '.$ANNEE.'&nbsp;</a></td>
        </tr>';
         } while ($tab_rq_info_mois = mysql_fetch_assoc($res_rq_info_mois));
      $ligne= mysql_num_rows($res_rq_info_mois);
      if($ligne > 0) {
        mysql_data_seek($res_rq_info_mois, 0);
        $tab_rq_info_mois = mysql_fetch_assoc($res_rq_info_mois);
      }
        

      } while ($tab_rq_info_anne = mysql_fetch_assoc($res_rq_info_anne));
      $ligne= mysql_num_rows($res_rq_info_anne);
      if($ligne > 0) {
        mysql_data_seek($res_rq_info_anne, 0);
        $tab_rq_info_anne = mysql_fetch_assoc($res_rq_info_anne);
      }
    }
    mysql_free_result($res_rq_info_anne);
    
      echo '
           <tr align="center" class="titre">
        <td align="center" colspan="2">
        <b>&nbsp;Rapport Pond&eacute;r&eacute; ODTI/QC9.&nbsp;</b>
        </td>
      </tr>';
//Rapport pondéré
     $rq_info_anne="
      SELECT DISTINCT(LEFT(`DATE_INDICATEUR`,4)) AS `ANNEE` 
      FROM `indicateur_calcul` 
      WHERE `DATE_INDICATEUR`>200900
      ORDER BY `ANNEE` DESC 
      ";
      $res_rq_info_anne = mysql_query($rq_info_anne, $mysql_link) or die(mysql_error());
      $tab_rq_info_anne = mysql_fetch_assoc($res_rq_info_anne);
      $total_ligne_rq_info_anne=mysql_num_rows($res_rq_info_anne); 
      if ($total_ligne_rq_info_anne==0){
        $j++;
        if ($j%2) { $class = "pair";}else{$class = "impair";} 
        echo '
        <tr align="center" class='.$class.'>
          <td align="left" colspan="2">&nbsp;Pas d\'information dans la base&nbsp;</a></td>
        </tr>';
      }else{
      
      do {
        $ANNEE=$tab_rq_info_anne['ANNEE'];
        $j++;
        if ($j%2) { $class = "pair";}else{$class = "impair";} 
         echo '
        <tr align="center" class='.$class.'>
          <td align="center" colspan="2"><a class="LinkDef" href=./index.php?ITEM=indicateur_rapport_pondere&ANNEE='.$ANNEE.'>&nbsp;Rapport Pond&eacute;r&eacute; ODTI/QC9 pour '.$ANNEE.'&nbsp;</a></td>
        </tr>';

      } while ($tab_rq_info_anne = mysql_fetch_assoc($res_rq_info_anne));
      $ligne= mysql_num_rows($res_rq_info_anne);
      if($ligne > 0) {
        mysql_data_seek($res_rq_info_anne, 0);
        $tab_rq_info_anne = mysql_fetch_assoc($res_rq_info_anne);
      }
    }
    mysql_free_result($res_rq_info_anne);
    echo '
      <tr align="center" class="titre">
        <td align="center" colspan="2">&nbsp;</td>
      </tr>
      <tr align="center">
        <td align="center" colspan="2">&nbsp;</td>
      </tr>
      <tr align="center" class="titre">
        <td align="center" colspan="2">
        <b>&nbsp;Menu des indicateurs ODTI - IAB&nbsp;</b>
        </td>
      </tr>
      <tr align="center" class="titre">
        <td align="center">';
// tableau de gauche        
        echo '
        <table class="table_inc" cellspacing="1" cellpading="0">
      <tr align="center" class="titre">
        <td align="center" colspan="2"><b>&nbsp;Tableau de Bord ODTI de prod&nbsp;</b></td>
      </tr>';
//Tableau de Bord ODTI de prod
     $rq_info="
      SELECT DISTINCT(LEFT(`DATE_INDICATEUR`,4)) AS `ANNEE` 
      FROM `indicateur_calcul` 
      WHERE `DATE_INDICATEUR`>200900
      ORDER BY `ANNEE` DESC 
      ";
      $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
      $tab_rq_info = mysql_fetch_assoc($res_rq_info);
      $total_ligne_rq_info=mysql_num_rows($res_rq_info); 
      if ($total_ligne_rq_info==0){
        $j++;
        if ($j%2) { $class = "pair";}else{$class = "impair";} 
        echo '
        <tr align="center" class='.$class.'>
          <td align="left" colspan="2">&nbsp;Pas d\'information dans la base&nbsp;</a></td>
        </tr>';
      }else{
      
      do {
        $ANNEE=$tab_rq_info['ANNEE'];
        $j++;
        if ($j%2) { $class = "pair";}else{$class = "impair";} 
        echo '
        <tr align="center" class='.$class.'>
          <td align="left" colspan="2"><a class="LinkDef" href=./index.php?ITEM=indicateur_tdb&action=PROD&ANNEE='.$ANNEE.'>&nbsp;Tableau de Bord de prod pour '.$ANNEE.'&nbsp;</a></td>
        </tr>';
      } while ($tab_rq_info = mysql_fetch_assoc($res_rq_info));
      $ligne= mysql_num_rows($res_rq_info);
      if($ligne > 0) {
        mysql_data_seek($res_rq_info, 0);
        $tab_rq_info = mysql_fetch_assoc($res_rq_info);
      }
    }
    mysql_free_result($res_rq_info);
    echo '
    <tr align="center" class="titre">
        <td align="center" colspan="2"><b>&nbsp;Tableau de Bord ODTI de VA&nbsp;</b></td>
      </tr>';
//Tableau de Bord ODTI de VA
     $rq_info="
      SELECT DISTINCT(LEFT(`DATE_INDICATEUR`,4)) AS `ANNEE` 
      FROM `indicateur_calcul` 
      WHERE `DATE_INDICATEUR`>200900
      ORDER BY `ANNEE` DESC 
      ";
      $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
      $tab_rq_info = mysql_fetch_assoc($res_rq_info);
      $total_ligne_rq_info=mysql_num_rows($res_rq_info); 
      if ($total_ligne_rq_info==0){
        $j++;
        if ($j%2) { $class = "pair";}else{$class = "impair";} 
        echo '
        <tr align="center" class='.$class.'>
          <td align="left" colspan="2">&nbsp;Pas d\'information dans la base&nbsp;</a></td>
        </tr>';
      }else{
      
      do {
        $ANNEE=$tab_rq_info['ANNEE'];
        $j++;
        if ($j%2) { $class = "pair";}else{$class = "impair";} 
        echo '
        <tr align="center" class='.$class.'>
          <td align="left" colspan="2"><a class="LinkDef" href=./index.php?ITEM=indicateur_tdb&action=VA&ANNEE='.$ANNEE.'>&nbsp;Tableau de Bord de la VA pour '.$ANNEE.'&nbsp;</a></td>
        </tr>';
      } while ($tab_rq_info = mysql_fetch_assoc($res_rq_info));
      $ligne= mysql_num_rows($res_rq_info);
      if($ligne > 0) {
        mysql_data_seek($res_rq_info, 0);
        $tab_rq_info = mysql_fetch_assoc($res_rq_info);
      }
    }
    mysql_free_result($res_rq_info);
    echo '<tr align="center" class="titre">
        <td align="center" colspan="2"><b>&nbsp;Synth&egrave;se ODTI de prod&nbsp;</b></td>
      </tr>';
//Synth&egrave;se ODTI de prod
     $rq_info="
      SELECT DISTINCT(LEFT(`DATE_INDICATEUR`,4)) AS `ANNEE` 
      FROM `indicateur_calcul` 
      WHERE `DATE_INDICATEUR`>200900
      ORDER BY `ANNEE` DESC 
      ";
      $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
      $tab_rq_info = mysql_fetch_assoc($res_rq_info);
      $total_ligne_rq_info=mysql_num_rows($res_rq_info); 
      if ($total_ligne_rq_info==0){
        $j++;
        if ($j%2) { $class = "pair";}else{$class = "impair";} 
        echo '
        <tr align="center" class='.$class.'>
          <td align="left" colspan="2">&nbsp;Pas d\'information dans la base&nbsp;</a></td>
        </tr>';
      }else{
      
      do {
        $ANNEE=$tab_rq_info['ANNEE'];
        $j++;
        if ($j%2) { $class = "pair";}else{$class = "impair";} 
        echo '
        <tr align="center" class='.$class.'>
          <td align="left" colspan="2"><a class="LinkDef" href=./index.php?ITEM=indicateur_synthese&action=PROD&ANNEE='.$ANNEE.'>&nbsp;Synth&egrave;se de prod pour '.$ANNEE.'&nbsp;</a></td>
        </tr>';
      } while ($tab_rq_info = mysql_fetch_assoc($res_rq_info));
      $ligne= mysql_num_rows($res_rq_info);
      if($ligne > 0) {
        mysql_data_seek($res_rq_info, 0);
        $tab_rq_info = mysql_fetch_assoc($res_rq_info);
      }
    }
    mysql_free_result($res_rq_info);
    echo '
    <tr align="center" class="titre">
        <td align="center" colspan="2"><b>&nbsp;Synth&egrave;se ODTI de VA&nbsp;</b></td>
      </tr>';
//Synth&egrave;se ODTI de VA
     $rq_info="
      SELECT DISTINCT(LEFT(`DATE_INDICATEUR`,4)) AS `ANNEE` 
      FROM `indicateur_calcul` 
      WHERE `DATE_INDICATEUR`>200900
      ORDER BY `ANNEE` DESC 
      ";
      $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
      $tab_rq_info = mysql_fetch_assoc($res_rq_info);
      $total_ligne_rq_info=mysql_num_rows($res_rq_info); 
      if ($total_ligne_rq_info==0){
        $j++;
        if ($j%2) { $class = "pair";}else{$class = "impair";} 
        echo '
        <tr align="center" class='.$class.'>
          <td align="left" colspan="2">&nbsp;Pas d\'information dans la base&nbsp;</a></td>
        </tr>';
      }else{
      
      do {
        $ANNEE=$tab_rq_info['ANNEE'];
        $j++;
        if ($j%2) { $class = "pair";}else{$class = "impair";} 
        echo '
        <tr align="center" class='.$class.'>
          <td align="left" colspan="2"><a class="LinkDef" href=./index.php?ITEM=indicateur_synthese&action=VA&ANNEE='.$ANNEE.'>&nbsp;Synth&egrave;se de la VA pour '.$ANNEE.'&nbsp;</a></td>
        </tr>';
      } while ($tab_rq_info = mysql_fetch_assoc($res_rq_info));
      $ligne= mysql_num_rows($res_rq_info);
      if($ligne > 0) {
        mysql_data_seek($res_rq_info, 0);
        $tab_rq_info = mysql_fetch_assoc($res_rq_info);
      }
    }
    mysql_free_result($res_rq_info);
     echo '<tr align="center" class="titre">
        <td align="center" colspan="2"><b>&nbsp;Synth&egrave;se ODTI trait&eacute;es par ICDC&nbsp;</b></td>
      </tr>';
//Synth&egrave;se ODTI trait&eacute;es par ICDC
     $rq_info="
      SELECT DISTINCT(LEFT(`DATE_INDICATEUR`,4)) AS `ANNEE` 
      FROM `indicateur_calcul` 
      WHERE `DATE_INDICATEUR`>200900
      ORDER BY `ANNEE` DESC 
      ";
      $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
      $tab_rq_info = mysql_fetch_assoc($res_rq_info);
      $total_ligne_rq_info=mysql_num_rows($res_rq_info); 
      if ($total_ligne_rq_info==0){
        $j++;
        if ($j%2) { $class = "pair";}else{$class = "impair";} 
        echo '
        <tr align="center" class='.$class.'>
          <td align="left" colspan="2">&nbsp;Pas d\'information dans la base&nbsp;</a></td>
        </tr>';
      }else{
      
      do {
        $ANNEE=$tab_rq_info['ANNEE'];
        $j++;
        if ($j%2) { $class = "pair";}else{$class = "impair";} 
        echo '
        <tr align="center" class='.$class.'>
          <td align="left" colspan="2"><a class="LinkDef" href=./index.php?ITEM=indicateur_synthese_icdc&ANNEE='.$ANNEE.'>&nbsp;Synth&egrave;se trait&eacute;es par ICDC pour '.$ANNEE.'&nbsp;</a></td>
        </tr>';
      } while ($tab_rq_info = mysql_fetch_assoc($res_rq_info));
      $ligne= mysql_num_rows($res_rq_info);
      if($ligne > 0) {
        mysql_data_seek($res_rq_info, 0);
        $tab_rq_info = mysql_fetch_assoc($res_rq_info);
      }
    }
    mysql_free_result($res_rq_info);
     echo '<tr align="center" class="titre">
        <td align="center" colspan="2"><b>&nbsp;Indicateurs Sogeti pour le forfait ICDC / IAB&nbsp;</b></td>
      </tr>';
//Synth&egrave;se ODTI trait&eacute;es par Sogeti
     $rq_info="
      SELECT DISTINCT(LEFT(`DATE_INDICATEUR`,4)) AS `ANNEE` 
      FROM `indicateur_calcul` 
      WHERE `DATE_INDICATEUR`>200900
      ORDER BY `ANNEE` DESC 
      ";
      $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
      $tab_rq_info = mysql_fetch_assoc($res_rq_info);
      $total_ligne_rq_info=mysql_num_rows($res_rq_info); 
      if ($total_ligne_rq_info==0){
        $j++;
        if ($j%2) { $class = "pair";}else{$class = "impair";} 
        echo '
        <tr align="center" class='.$class.'>
          <td align="left" colspan="2">&nbsp;Pas d\'information dans la base&nbsp;</a></td>
        </tr>';
      }else{
      
      do {
        $ANNEE=$tab_rq_info['ANNEE'];
        $j++;
        if ($j%2) { $class = "pair";}else{$class = "impair";} 
        echo '
        <tr align="center" class='.$class.'>
          <td align="left" colspan="2"><a class="LinkDef" href=./index.php?ITEM=indicateur_synthese_sogeti&ANNEE='.$ANNEE.'>&nbsp;Synth&egrave;se trait&eacute;es par Sogeti pour '.$ANNEE.'&nbsp;</a></td>
        </tr>';
      } while ($tab_rq_info = mysql_fetch_assoc($res_rq_info));
      $ligne= mysql_num_rows($res_rq_info);
      if($ligne > 0) {
        mysql_data_seek($res_rq_info, 0);
        $tab_rq_info = mysql_fetch_assoc($res_rq_info);
      }
    }
    mysql_free_result($res_rq_info);
     echo '<tr align="center" class="titre">
        <td align="center" colspan="2"><b>&nbsp;Indicateurs Sogeti pour le forfait ICDC / IAB&nbsp;</b></td>
      </tr>';
//Indicateurs Sogeti pour le forfait ICDC / IAB
     $rq_info="
      SELECT DISTINCT(LEFT(`DATE_INDICATEUR`,4)) AS `ANNEE` 
      FROM `indicateur_calcul` 
      WHERE `DATE_INDICATEUR`>200900
      ORDER BY `ANNEE` DESC 
      ";
      $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
      $tab_rq_info = mysql_fetch_assoc($res_rq_info);
      $total_ligne_rq_info=mysql_num_rows($res_rq_info); 
      if ($total_ligne_rq_info==0){
        $j++;
        if ($j%2) { $class = "pair";}else{$class = "impair";} 
        echo '
        <tr align="center" class='.$class.'>
          <td align="left" colspan="2">&nbsp;Pas d\'information dans la base&nbsp;</a></td>
        </tr>';
      }else{
      
      do {
        $ANNEE=$tab_rq_info['ANNEE'];
        $j++;
        if ($j%2) { $class = "pair";}else{$class = "impair";} 
        echo '
        <tr align="center" class='.$class.'>
          <td align="left" colspan="2"><a class="LinkDef" href=./index.php?ITEM=indicateur_sogeti&ANNEE='.$ANNEE.'>&nbsp;Indicateurs Sogeti pour '.$ANNEE.'&nbsp;</a></td>
        </tr>';
      } while ($tab_rq_info = mysql_fetch_assoc($res_rq_info));
      $ligne= mysql_num_rows($res_rq_info);
      if($ligne > 0) {
        mysql_data_seek($res_rq_info, 0);
        $tab_rq_info = mysql_fetch_assoc($res_rq_info);
      }
    }
    mysql_free_result($res_rq_info);
    //Indicateurs rapport
    echo '
      <tr align="center" class="titre">
        <td align="center" colspan="2"><b>&nbsp;Rapport ODTI&nbsp;</b></td>
      </tr>';

     $rq_info="
      SELECT DISTINCT(LEFT(`DATE_INDICATEUR`,4)) AS `ANNEE` 
      FROM `indicateur_calcul` 
      WHERE `DATE_INDICATEUR`>200900
      ORDER BY `ANNEE` DESC 
      ";
      $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
      $tab_rq_info = mysql_fetch_assoc($res_rq_info);
      $total_ligne_rq_info=mysql_num_rows($res_rq_info); 
      if ($total_ligne_rq_info==0){
        $j++;
        if ($j%2) { $class = "pair";}else{$class = "impair";} 
        echo '
        <tr align="center" class='.$class.'>
          <td align="left" colspan="2">&nbsp;Pas d\'information dans la base&nbsp;</a></td>
        </tr>';
      }else{
      
      do {
        $ANNEE=$tab_rq_info['ANNEE'];
        $j++;
        if ($j%2) { $class = "pair";}else{$class = "impair";} 
        echo '
        <tr align="center" class='.$class.'>
          <td align="left" colspan="2"><a class="LinkDef" href=./index.php?ITEM=indicateur_rapport&ANNEE='.$ANNEE.'>&nbsp;Rapport ODTI pour '.$ANNEE.'&nbsp;</a></td>
        </tr>';
      } while ($tab_rq_info = mysql_fetch_assoc($res_rq_info));
      $ligne= mysql_num_rows($res_rq_info);
      if($ligne > 0) {
        mysql_data_seek($res_rq_info, 0);
        $tab_rq_info = mysql_fetch_assoc($res_rq_info);
      }
    }
    mysql_free_result($res_rq_info);
// Liste des donn&eacute;es
    echo '
      <tr align="center" class="titre">
        <td align="center" colspan="2"><b>&nbsp;Liste des donn&eacute;es&nbsp;</b></td>
      </tr>';
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="left" colspan="2"><a class="LinkDef" href=./index.php?ITEM=indicateur_liste_donnee>&nbsp;Liste des donn&eacute;es&nbsp;</a></td>
      </tr>';
    echo '
        </table>
      </td>
      <td>';
// tableau de droite
      echo '
    <table class="table_inc" cellspacing="1" cellpading="0">
    <tr align="center" class="titre">
        <td align="center" colspan="2"><b>&nbsp;Synth&egrave;se des MEP&nbsp;</b></td>
      </tr>';
//Synth&egrave;se des MEP
     $rq_info_anne="
      SELECT DISTINCT(LEFT(`DATE_INDICATEUR`,4)) AS `ANNEE` 
      FROM `indicateur_calcul` 
      WHERE `DATE_INDICATEUR`>200900
      ORDER BY `ANNEE` DESC 
      ";
      $res_rq_info_anne = mysql_query($rq_info_anne, $mysql_link) or die(mysql_error());
      $tab_rq_info_anne = mysql_fetch_assoc($res_rq_info_anne);
      $total_ligne_rq_info_anne=mysql_num_rows($res_rq_info_anne); 
      if ($total_ligne_rq_info_anne==0){
        $j++;
        if ($j%2) { $class = "pair";}else{$class = "impair";} 
        echo '
        <tr align="center" class='.$class.'>
          <td align="left" colspan="2">&nbsp;Pas d\'information dans la base&nbsp;</a></td>
        </tr>';
      }else{
      
      do {
        $ANNEE=$tab_rq_info_anne['ANNEE'];
        $rq_info_mois="
	SELECT DISTINCT (RIGHT(`DATE_INDICATEUR`, 2 )) AS `MOIS` 
	FROM `indicateur_calcul` 
	WHERE `DATE_INDICATEUR` LIKE '".$ANNEE."%'
	ORDER BY `MOIS` DESC 
      ";
      $res_rq_info_mois = mysql_query($rq_info_mois, $mysql_link) or die(mysql_error());
      $tab_rq_info_mois = mysql_fetch_assoc($res_rq_info_mois);
      $total_ligne_rq_info_mois=mysql_num_rows($res_rq_info_mois); 
       do {
        $j++;
        if ($j%2) { $class = "pair";}else{$class = "impair";} 
        $MOIS=$tab_rq_info_mois['MOIS'];
        if($MOIS<10){$MOIS=substr($MOIS, -1);}
         echo '
        <tr align="center" class='.$class.'>
          <td align="left" colspan="2"><a class="LinkDef" href=./index.php?ITEM=indicateur_mep&ANNEE='.$ANNEE.'&MOIS='.$MOIS.'>&nbsp;Synth&egrave;se des MEP de '.$Tab_des_Mois[$MOIS-1].' '.$ANNEE.'&nbsp;</a></td>
        </tr>';
         } while ($tab_rq_info_mois = mysql_fetch_assoc($res_rq_info_mois));
      $ligne= mysql_num_rows($res_rq_info_mois);
      if($ligne > 0) {
        mysql_data_seek($res_rq_info_mois, 0);
        $tab_rq_info_mois = mysql_fetch_assoc($res_rq_info_mois);
      }
        

      } while ($tab_rq_info_anne = mysql_fetch_assoc($res_rq_info_anne));
      $ligne= mysql_num_rows($res_rq_info_anne);
      if($ligne > 0) {
        mysql_data_seek($res_rq_info_anne, 0);
        $tab_rq_info_anne = mysql_fetch_assoc($res_rq_info_anne);
      }
    }
    mysql_free_result($res_rq_info_anne);
    echo '
        </table>
      </td>
    </tr>
    <tr align="center">
      <td align="center" colspan="2">&nbsp;</td>
    </tr>
    <tr align="center" class="titre">
        <td align="center" colspan="2">
        <b>&nbsp;Menu des indicateurs QC9 - IAB&nbsp;</b>
        </td>
      </tr>
      <tr align="center" class="titre">
        <td align="center">';
// tableau de gauche        
        echo '
        <table class="table_inc" cellspacing="1" cellpading="0">
        <tr align="center" class="titre">
        <td align="center" colspan="2"><b>&nbsp;Tableau de Bord QC9&nbsp;</b></td>
      </tr>';
//Tableau de Bord QC9
     $rq_info="
      SELECT DISTINCT(LEFT(`DATE_INDICATEUR`,4)) AS `ANNEE` 
      FROM `indicateur_qc9_calcul` 
      WHERE `DATE_INDICATEUR`>200900
      ORDER BY `ANNEE` DESC 
      ";
      $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
      $tab_rq_info = mysql_fetch_assoc($res_rq_info);
      $total_ligne_rq_info=mysql_num_rows($res_rq_info); 
      if ($total_ligne_rq_info==0){
        $j++;
        if ($j%2) { $class = "pair";}else{$class = "impair";} 
        echo '
        <tr align="center" class='.$class.'>
          <td align="left" colspan="2">&nbsp;Pas d\'information dans la base&nbsp;</a></td>
        </tr>';
      }else{
      
      do {
        $ANNEE=$tab_rq_info['ANNEE'];
        $j++;
        if ($j%2) { $class = "pair";}else{$class = "impair";} 
        echo '
        <tr align="center" class='.$class.'>
          <td align="left" colspan="2"><a class="LinkDef" href=./index.php?ITEM=indicateur_qc9_tdb&ANNEE='.$ANNEE.'>&nbsp;Tableau de Bord pour '.$ANNEE.'&nbsp;</a></td>
        </tr>';
      } while ($tab_rq_info = mysql_fetch_assoc($res_rq_info));
      $ligne= mysql_num_rows($res_rq_info);
      if($ligne > 0) {
        mysql_data_seek($res_rq_info, 0);
        $tab_rq_info = mysql_fetch_assoc($res_rq_info);
      }
    }
    mysql_free_result($res_rq_info);
    echo '
      <tr align="center" class="titre">
        <td align="center" colspan="2"><b>&nbsp;Tableau de Bord QC9 de prod&nbsp;</b></td>
      </tr>';
//Tableau de Bord QC9 de prod
     $rq_info="
      SELECT DISTINCT(LEFT(`DATE_INDICATEUR`,4)) AS `ANNEE` 
      FROM `indicateur_qc9_calcul` 
      WHERE `DATE_INDICATEUR`>200900
      ORDER BY `ANNEE` DESC 
      ";
      $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
      $tab_rq_info = mysql_fetch_assoc($res_rq_info);
      $total_ligne_rq_info=mysql_num_rows($res_rq_info); 
      if ($total_ligne_rq_info==0){
        $j++;
        if ($j%2) { $class = "pair";}else{$class = "impair";} 
        echo '
        <tr align="center" class='.$class.'>
          <td align="left" colspan="2">&nbsp;Pas d\'information dans la base&nbsp;</a></td>
        </tr>';
      }else{
      
      do {
        $ANNEE=$tab_rq_info['ANNEE'];
        $j++;
        if ($j%2) { $class = "pair";}else{$class = "impair";} 
        echo '
        <tr align="center" class='.$class.'>
          <td align="left" colspan="2"><a class="LinkDef" href=./index.php?ITEM=indicateur_qc9_tdb&ENVIRONNEMENT=PROD&ANNEE='.$ANNEE.'>&nbsp;Tableau de Bord de prod pour '.$ANNEE.'&nbsp;</a></td>
        </tr>';
      } while ($tab_rq_info = mysql_fetch_assoc($res_rq_info));
      $ligne= mysql_num_rows($res_rq_info);
      if($ligne > 0) {
        mysql_data_seek($res_rq_info, 0);
        $tab_rq_info = mysql_fetch_assoc($res_rq_info);
      }
    }
    mysql_free_result($res_rq_info);
    echo '
    <tr align="center" class="titre">
        <td align="center" colspan="2"><b>&nbsp;Tableau de Bord QC9 de VA&nbsp;</b></td>
      </tr>';
//Tableau de Bord QC9 de VA
     $rq_info="
      SELECT DISTINCT(LEFT(`DATE_INDICATEUR`,4)) AS `ANNEE` 
      FROM `indicateur_qc9_calcul` 
      WHERE `DATE_INDICATEUR`>200900
      ORDER BY `ANNEE` DESC 
      ";
      $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
      $tab_rq_info = mysql_fetch_assoc($res_rq_info);
      $total_ligne_rq_info=mysql_num_rows($res_rq_info); 
      if ($total_ligne_rq_info==0){
        $j++;
        if ($j%2) { $class = "pair";}else{$class = "impair";} 
        echo '
        <tr align="center" class='.$class.'>
          <td align="left" colspan="2">&nbsp;Pas d\'information dans la base&nbsp;</a></td>
        </tr>';
      }else{
      
      do {
        $ANNEE=$tab_rq_info['ANNEE'];
        $j++;
        if ($j%2) { $class = "pair";}else{$class = "impair";} 
        echo '
        <tr align="center" class='.$class.'>
          <td align="left" colspan="2"><a class="LinkDef" href=./index.php?ITEM=indicateur_qc9_tdb&ENVIRONNEMENT=VA&ANNEE='.$ANNEE.'>&nbsp;Tableau de Bord de la VA pour '.$ANNEE.'&nbsp;</a></td>
        </tr>';
      } while ($tab_rq_info = mysql_fetch_assoc($res_rq_info));
      $ligne= mysql_num_rows($res_rq_info);
      if($ligne > 0) {
        mysql_data_seek($res_rq_info, 0);
        $tab_rq_info = mysql_fetch_assoc($res_rq_info);
      }
    }
    mysql_free_result($res_rq_info);
    echo '
        </table>
      </td>
      <td>';
// tableau de droite
      echo '
    <table class="table_inc" cellspacing="1" cellpading="0">';
        //Indicateurs rapport
    echo '
      <tr align="center" class="titre">
        <td align="center" colspan="2"><b>&nbsp;Rapport QC9&nbsp;</b></td>
      </tr>';

     $rq_info="
      SELECT DISTINCT(LEFT(`DATE_INDICATEUR`,4)) AS `ANNEE` 
      FROM `indicateur_qc9_calcul` 
      WHERE `DATE_INDICATEUR`>200900
      ORDER BY `ANNEE` DESC 
      ";
      $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
      $tab_rq_info = mysql_fetch_assoc($res_rq_info);
      $total_ligne_rq_info=mysql_num_rows($res_rq_info); 
      if ($total_ligne_rq_info==0){
        $j++;
        if ($j%2) { $class = "pair";}else{$class = "impair";} 
        echo '
        <tr align="center" class='.$class.'>
          <td align="left" colspan="2">&nbsp;Pas d\'information dans la base&nbsp;</a></td>
        </tr>';
      }else{
      
      do {
        $ANNEE=$tab_rq_info['ANNEE'];
        $j++;
        if ($j%2) { $class = "pair";}else{$class = "impair";} 
        echo '
        <tr align="center" class='.$class.'>
          <td align="left" colspan="2"><a class="LinkDef" href=./index.php?ITEM=indicateur_qc9_rapport&ANNEE='.$ANNEE.'>&nbsp;Rapport QC9 pour '.$ANNEE.'&nbsp;</a></td>
        </tr>';
      } while ($tab_rq_info = mysql_fetch_assoc($res_rq_info));
      $ligne= mysql_num_rows($res_rq_info);
      if($ligne > 0) {
        mysql_data_seek($res_rq_info, 0);
        $tab_rq_info = mysql_fetch_assoc($res_rq_info);
      }
    }
    mysql_free_result($res_rq_info);
    echo '
      <tr align="center" class="titre">
        <td align="center" colspan="2"><b>&nbsp;D&eacute;tail du Tableau de Bord QC9&nbsp;</b></td>
      </tr>';
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="left" colspan="2"><a class="LinkDef" href=./index.php?ITEM=indicateur_qc9_tdb_detail_glissant>&nbsp;D&eacute;tail du Tableau de Bord QC9&nbsp;</a></td>
      </tr>
      <tr align="center" class="titre">
        <td align="center" colspan="2"><b>&nbsp;Tableau de Bord SOGETI QC9&nbsp;</b></td>
      </tr>';
//Tableau de Bord SOGETI QC9
     $rq_info="
      SELECT DISTINCT(LEFT(`DATE_INDICATEUR`,4)) AS `ANNEE` 
      FROM `indicateur_qc9_calcul` 
      WHERE `DATE_INDICATEUR`>200900
      ORDER BY `ANNEE` DESC 
      ";
      $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
      $tab_rq_info = mysql_fetch_assoc($res_rq_info);
      $total_ligne_rq_info=mysql_num_rows($res_rq_info); 
      if ($total_ligne_rq_info==0){
        $j++;
        if ($j%2) { $class = "pair";}else{$class = "impair";} 
        echo '
        <tr align="center" class='.$class.'>
          <td align="left" colspan="2">&nbsp;Pas d\'information dans la base&nbsp;</a></td>
        </tr>';
      }else{
      
      do {
        $ANNEE=$tab_rq_info['ANNEE'];
        $j++;
        if ($j%2) { $class = "pair";}else{$class = "impair";} 
        echo '
        <tr align="center" class='.$class.'>
          <td align="left" colspan="2"><a class="LinkDef" href=./index.php?ITEM=indicateur_qc9_tdb&SOGETI=Y&ANNEE='.$ANNEE.'>&nbsp;Tableau de Bord pour '.$ANNEE.'&nbsp;</a></td>
        </tr>';
      } while ($tab_rq_info = mysql_fetch_assoc($res_rq_info));
      $ligne= mysql_num_rows($res_rq_info);
      if($ligne > 0) {
        mysql_data_seek($res_rq_info, 0);
        $tab_rq_info = mysql_fetch_assoc($res_rq_info);
      }
    }
    mysql_free_result($res_rq_info);
    echo '
        </table>
      </td>
    </tr>';
     echo '
     <tr align="center">
        <td align="center" colspan="2">&nbsp;</td>
      </tr>
     <tr align="center" class="titre">
        <td align="center" colspan="2">
        <b>&nbsp;Rapport B22.&nbsp;</b>
        </td>
      </tr>';
//Rapport B22
     $rq_info_anne="
      SELECT DISTINCT(LEFT(`INDICATEUR_B22_TABLEAU_DATE_INDICATEUR`,4)) AS `ANNEE` 
      FROM `indicateur_b22_tableau`
      WHERE `INDICATEUR_B22_TABLEAU_DATE_INDICATEUR`>200900
      ORDER BY `ANNEE` DESC 
      ";
      $res_rq_info_anne = mysql_query($rq_info_anne, $mysql_link) or die(mysql_error());
      $tab_rq_info_anne = mysql_fetch_assoc($res_rq_info_anne);
      $total_ligne_rq_info_anne=mysql_num_rows($res_rq_info_anne); 
      if ($total_ligne_rq_info_anne==0){
        $j++;
        if ($j%2) { $class = "pair";}else{$class = "impair";} 
        echo '
        <tr align="center" class='.$class.'>
          <td align="left" colspan="2">&nbsp;Pas d\'information dans la base&nbsp;</a></td>
        </tr>';
      }else{
      
      do {
        $ANNEE=$tab_rq_info_anne['ANNEE'];
        $j++;
        if ($j%2) { $class = "pair";}else{$class = "impair";}
         echo '
        <tr align="center" class='.$class.'>
          <td align="center" colspan="2"><a class="LinkDef" href=./index.php?ITEM=indicateur_b22_tableau&ANNEE='.$ANNEE.'>&nbsp;Rapport B22 pour '.$ANNEE.'&nbsp;</a></td>
        </tr>';

      } while ($tab_rq_info_anne = mysql_fetch_assoc($res_rq_info_anne));
      $ligne= mysql_num_rows($res_rq_info_anne);
      if($ligne > 0) {
        mysql_data_seek($res_rq_info_anne, 0);
        $tab_rq_info_anne = mysql_fetch_assoc($res_rq_info_anne);
      }
    }
    mysql_free_result($res_rq_info_anne);
    
         echo '
     <tr align="center">
        <td align="center" colspan="2">&nbsp;</td>
      </tr>
     <tr align="center" class="titre">
        <td align="center" colspan="2">
        <b>&nbsp;Rapport C5.&nbsp;</b>
        </td>
      </tr>';
//Rapport c5
     $rq_info_anne="
      SELECT DISTINCT(LEFT(`DATE_INDICATEUR`,4)) AS `ANNEE` 
      FROM `indicateur_calcul` 
      WHERE `DATE_INDICATEUR`>201100
      ORDER BY `ANNEE` DESC 
      ";
      $res_rq_info_anne = mysql_query($rq_info_anne, $mysql_link) or die(mysql_error());
      $tab_rq_info_anne = mysql_fetch_assoc($res_rq_info_anne);
      $total_ligne_rq_info_anne=mysql_num_rows($res_rq_info_anne); 
      if ($total_ligne_rq_info_anne==0){
        $j++;
        if ($j%2) { $class = "pair";}else{$class = "impair";} 
        echo '
        <tr align="center" class='.$class.'>
          <td align="left" colspan="2">&nbsp;Pas d\'information dans la base&nbsp;</a></td>
        </tr>';
      }else{
      
      do {
        $ANNEE=$tab_rq_info_anne['ANNEE'];
        $j++;
        if ($j%2) { $class = "pair";}else{$class = "impair";}
         echo '
        <tr align="center" class='.$class.'>
          <td align="center" colspan="2"><a class="LinkDef" href=./index.php?ITEM=indicateur_c5_tableau&ANNEE='.$ANNEE.'>&nbsp;Rapport C5 pour '.$ANNEE.'&nbsp;</a></td>
        </tr>';

      } while ($tab_rq_info_anne = mysql_fetch_assoc($res_rq_info_anne));
      $ligne= mysql_num_rows($res_rq_info_anne);
      if($ligne > 0) {
        mysql_data_seek($res_rq_info_anne, 0);
        $tab_rq_info_anne = mysql_fetch_assoc($res_rq_info_anne);
      }
    }
    mysql_free_result($res_rq_info_anne);
    
    echo '
    <tr align="center" class="titre">
      <td align="center" colspan="2">&nbsp;</td>
    </tr>
    
    
  </table>
</div>';
mysql_close($mysql_link);
?>