<?PHP
//redirection si acces dirrect
if(substr_count($_SERVER['PHP_SELF'], 'index.php')==0){
  header("Location: ../"); 
  exit(); 
}
/*******************************************************************
   Interface Gestion des applications pour les indicateurs
   Version 1.0.0   
  31/08/2010 - VGU - Creation fichier
**************************************************Guibert Vincent***
*******************************************************************/
require("./cf/conf_outil_icdc.php"); 
require_once("./cf/fonctions.php");
$j=0;
echo '
 <div align="center">
 <br/>
  <table class="table_inc" cellspacing="0" cellpading="0">
    <tr align="center" class="titre">
      <td align="center" colspan="2">&nbsp;[&nbsp;<a href="./index.php?ITEM=indicateur_menu_creation">Retour</a>&nbsp;]&nbsp;</td>
    </tr>
    <tr align="center" class="titre">
      <td align="center"><b>&nbsp;Application&nbsp;</b></td>
      <td align="center"><b>&nbsp;Application Autre&nbsp;</b></td>
    </tr>';
    $rq_appli="SELECT UPPER(`id_appli`) AS `id_appli` FROM `referentiel_appli` WHERE `id_appli`!='TOUT' ORDER BY `id_appli`";
    $res_rq_appli = mysql_query($rq_appli, $mysql_link) or die(mysql_error());
    $tab_rq_appli = mysql_fetch_assoc($res_rq_appli);
    $total_ligne_rq_appli=mysql_num_rows($res_rq_appli);
    if($total_ligne_rq_appli==0){
          echo '
          <tr align="center" class='.$class.'>
            <td align="left" colspan="2">&nbsp;Pas d\'application dans la base&nbsp;</td>
          </tr>';
    }else{
    do {
      $id_appli=$tab_rq_appli['id_appli'];
          
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="center"><b>'.stripslashes($id_appli).'</b></td>
        <td align="center">&nbsp;</td>
      </tr>';
      $rq_appli_info="
      SELECT UPPER(`INDICATEUR_APPLICATION_AUTRE`) AS `INDICATEUR_APPLICATION_AUTRE`
      FROM `indicateur_application` 
      WHERE `INDICATEUR_APPLICATION_REF`='".$id_appli."' AND `ENABLE` =0";
      $res_rq_appli_info = mysql_query($rq_appli_info, $mysql_link) or die(mysql_error());
      $tab_rq_appli_info = mysql_fetch_assoc($res_rq_appli_info);
      $total_ligne_rq_appli_info=mysql_num_rows($res_rq_appli_info);
      if($total_ligne_rq_appli_info==0){
      	echo '
	      <tr align="center" class='.$class.'>
	        <td align="center" colspan="2">&nbsp;<b>ATTENTION :</b>&nbsp;</BR>&nbsp;L\'application <a class="LinkDef" href="./index.php?ITEM=indicateur_Action_Application&action=REF&APP='.$id_appli.'">'.$id_appli.'</a> ne sera pas dans&nbsp;</BR>&nbsp;l\'indicateur "Rapport pondéré ODTI/QC9"&nbsp;</td>
	      </tr>';
      }else{
	      do {
	      	 echo '
	      <tr align="center" class='.$class.'>
	        <td align="center">&nbsp;</td>
	        <td align="center">&nbsp;'.$tab_rq_appli_info['INDICATEUR_APPLICATION_AUTRE'].'&nbsp;</td>
	      </tr>';
	      
	      } while ($tab_rq_appli_info = mysql_fetch_assoc($res_rq_appli_info));
	      $ligne= mysql_num_rows($res_rq_appli_info);
	      if($ligne > 0) {
	      	mysql_data_seek($res_rq_appli_info, 0);
	      	$tab_rq_appli_info = mysql_fetch_assoc($res_rq_appli_info);
	      }
      }
      mysql_free_result($res_rq_appli_info);
    
    } while ($tab_rq_appli = mysql_fetch_assoc($res_rq_appli));
    $ligne= mysql_num_rows($res_rq_appli);
    if($ligne > 0) {
      mysql_data_seek($res_rq_appli, 0);
      $tab_rq_appli = mysql_fetch_assoc($res_rq_appli);
    }
    mysql_free_result($res_rq_appli);
}
    echo '
    <tr align="center" class="titre">
      <td align="center" colspan="2">&nbsp;</td>
    </tr>
  </table>';

    $rq_appli="SELECT DISTINCT (`APPLI`)
    FROM `indicateur_calcul` 
    WHERE UPPER(`APPLI`) NOT IN (
	    SELECT UPPER(`INDICATEUR_APPLICATION_AUTRE`) 
	    FROM `indicateur_application` 
	    WHERE `ENABLE` =0
    ) AND `APPLI` != ''";
    $res_rq_appli = mysql_query($rq_appli, $mysql_link) or die(mysql_error());
    $tab_rq_appli = mysql_fetch_assoc($res_rq_appli);
    $total_ligne_rq_appli=mysql_num_rows($res_rq_appli);
    if($total_ligne_rq_appli!=0){
    	echo '
      <table class="table_inc" cellspacing="0" cellpading="0">
    <tr align="center" class="titre">
      <td align="center" colspan="2">&nbsp;</td>
    </tr>
    <tr align="center" class="titre">
      <td align="center"><b>&nbsp;Application ODTI&nbsp;</BR>&nbsp;non présente dans les indicateurs&nbsp;</b></td>
    </tr>';
    do {
      $APPLI=$tab_rq_appli['APPLI'];
          
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="center">&nbsp;<a class="LinkDef" href="./index.php?ITEM=indicateur_Action_Application&action=AUTRE&APP='.$APPLI.'">'.$APPLI.'</a>&nbsp;</td>
      </tr>';

    } while ($tab_rq_appli = mysql_fetch_assoc($res_rq_appli));
    $ligne= mysql_num_rows($res_rq_appli);
    if($ligne > 0) {
      mysql_data_seek($res_rq_appli, 0);
      $tab_rq_appli = mysql_fetch_assoc($res_rq_appli);
    }
    mysql_free_result($res_rq_appli);
    echo '
    <tr align="center" class="titre">
      <td align="center" colspan="2">&nbsp;</td>
    </tr>
  </table>';
}
  $rq_appli="
  SELECT DISTINCT(`APPLICATION`) 
  FROM `indicateur_qc9_calcul` 
  WHERE UPPER(`APPLICATION`) NOT IN(
  SELECT UPPER(`INDICATEUR_APPLICATION_AUTRE`)
  FROM `indicateur_application`
  WHERE `ENABLE` =0
  ) AND `APPLICATION`!=''";
    $res_rq_appli = mysql_query($rq_appli, $mysql_link) or die(mysql_error());
    $tab_rq_appli = mysql_fetch_assoc($res_rq_appli);
    $total_ligne_rq_appli=mysql_num_rows($res_rq_appli);
    if($total_ligne_rq_appli!=0){
    	echo '
      <table class="table_inc" cellspacing="0" cellpading="0">
    <tr align="center" class="titre">
      <td align="center" colspan="2">&nbsp;</td>
    </tr>
    <tr align="center" class="titre">
      <td align="center"><b>&nbsp;Application QC9&nbsp;</BR>&nbsp;non présente dans les indicateurs&nbsp;</b></td>
    </tr>';
    do {
      $APPLI=$tab_rq_appli['APPLICATION'];
          
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="center">&nbsp;<a class="LinkDef" href="./index.php?ITEM=indicateur_Action_Application&action=AUTRE&APP='.$APPLI.'">'.$APPLI.'</a>&nbsp;</td>
      </tr>';

    } while ($tab_rq_appli = mysql_fetch_assoc($res_rq_appli));
    $ligne= mysql_num_rows($res_rq_appli);
    if($ligne > 0) {
      mysql_data_seek($res_rq_appli, 0);
      $tab_rq_appli = mysql_fetch_assoc($res_rq_appli);
    }
    mysql_free_result($res_rq_appli);
    echo '
    <tr align="center" class="titre">
      <td align="center" colspan="2">&nbsp;</td>
    </tr>
  </table>';
}
echo'
</div>';

mysql_close(); 
?>