<?PHP
//redirection si acces dirrect
if(substr_count($_SERVER['PHP_SELF'], 'index.php')==0){
  header("Location: ../"); 
  exit(); 
}
/*******************************************************************
   Interface Gestion des indicateurs c5
   Version 1.0.0   
  26/02/2011 - VGU - Creation fichier
**************************************************Guibert Vincent***
*******************************************************************/

require("./cf/conf_outil_icdc.php"); 
require_once("./cf/fonctions.php");
require_once("./cf/autre_fonctions.php");
$j=0;
$ID='';

if(isset($_GET['ANNEE'])){
  $ANNEE=$_GET['ANNEE'];
}else{
  $ANNEE=date("Y");
}
if(isset($_GET['MOIS'])){
  $MOIS=$_GET['MOIS'];
}else{
  $MOIS=date("m");
}

if($MOIS<10){
	$DATE_INDICATEUR=$ANNEE."0".$MOIS;
}else{
	$DATE_INDICATEUR=$ANNEE."".$MOIS;
}
$livraison_lib='livraison';
echo '
 <div align="center">
  <table class="table_inc" cellspacing="1" cellpading="0">
    <tr align="center" class="titre">
      <td align="center" colspan="5">
      &nbsp;[&nbsp;<a href="./index.php?ITEM=indicateur_c5_tableau&ANNEE='.$ANNEE.'">Retour</a>&nbsp;]&nbsp;
      </td>
    </tr>
    <tr align="center" class="titre">
      <td align="center" colspan="5">
      <b>&nbsp;Liste de la Synthèse des Livraison pour '.$Tab_des_Mois[$MOIS-1].' '.$ANNEE.'&nbsp;</b>
      </td>
    </tr>
    <tr align="center" class="titre">
      <td align="center"><b>&nbsp;REF&nbsp;</b></td>
      <td align="center"><b>&nbsp;DATE&nbsp;</b></td>
      <td align="center"><b>&nbsp;ACTION&nbsp;</b></td>
      <td align="center"><b>&nbsp;APPLI&nbsp;</b></td>
      <td align="center"><b>&nbsp;RESUME&nbsp;</b></td>
    </tr>';

	$rq_info="
	SELECT 
	`indicateur_calcul`.`REF`,
	`indicateur_extract_archive`.`RESUME`,
	`indicateur_calcul`.`DATE`, 
	`indicateur_calcul`.`ACTION_NEW`, 
	 `indicateur_calcul`.`APPLI`
	FROM `indicateur_calcul`,`indicateur_extract_archive` 
	WHERE 
	`indicateur_calcul`.`REF`=`indicateur_extract_archive`.`REF` AND
	`indicateur_calcul`.`DATE` LIKE '".$DATE_INDICATEUR."%' AND 
	`indicateur_calcul`.`ENV` LIKE 'E' AND 
	`indicateur_calcul`.`DATE_INDICATEUR` LIKE '".$DATE_INDICATEUR."' AND
	`indicateur_calcul`.`ACTION_NEW` LIKE 'L%'
	GROUP BY `indicateur_calcul`.`REF`";
      $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
      $tab_rq_info = mysql_fetch_assoc($res_rq_info);
      $total_ligne_rq_info=mysql_num_rows($res_rq_info); 
      if ($total_ligne_rq_info==0){
      	
        $j++;
        if ($j%2) { $class = "pair";}else{$class = "impair";} 
        echo '
        <tr align="center" class='.$class.'>
          <td align="left" colspan="5">&nbsp;Pas de suivie dans la base.&nbsp;</td>
        </tr>';
      }else{
      
      do {
        $REF=$tab_rq_info['REF'];
        $DATE=$tab_rq_info['DATE'];
        $ACTION_NEW=$tab_rq_info['ACTION_NEW'];  
        $APPLI=$tab_rq_info['APPLI'];  
        $RESUME=$tab_rq_info['RESUME'];  
        $j++;
        if ($j%2) { $class = "pair";}else{$class = "impair";} 
        echo '
        <tr align="center" class='.$class.'>
          <td align="center">&nbsp;'.stripslashes($REF).'&nbsp;</td>';
          $jour=substr($DATE,6,2);
          $mois=substr($DATE,4,2);
          $annee=substr($DATE,0,4);
          echo '
          <td align="center">&nbsp;'.$jour.'/'.$mois.'/'.$annee.'&nbsp;</td>
          <td align="center">&nbsp;'.stripslashes($ACTION_NEW).'&nbsp;</td>
          <td align="center">&nbsp;'.stripslashes($APPLI).'&nbsp;</td>
          <td align="left">&nbsp;'.stripslashes($RESUME).'&nbsp;</td>
        </tr>';

      } while ($tab_rq_info = mysql_fetch_assoc($res_rq_info));
      $ligne= mysql_num_rows($res_rq_info);
      if($ligne > 0) {
        mysql_data_seek($res_rq_info, 0);
        $tab_rq_info = mysql_fetch_assoc($res_rq_info);
      }
    }
    if($total_ligne_rq_info > 1){
    	$livraison_lib='livraisons';
    }
    echo '
    <tr align="center" class="titre">
      <td align="center" colspan="5">&nbsp;</td>
    </tr>
    <tr align="center" class="titre">
      <td align="center" colspan="5">&nbsp;Il y a '.$total_ligne_rq_info.' '.$livraison_lib.'.&nbsp;</td>
    </tr>
    <tr align="center" class="titre">
      <td align="center" colspan="5">&nbsp;</td>
    </tr>
  </table>
</div>';

?>