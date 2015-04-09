<?PHP
//redirection si acces dirrect
if(substr_count($_SERVER['PHP_SELF'], 'index.php')==0){
  header("Location: ../"); 
  exit(); 
}
/*******************************************************************
   Interface Gestion des indicateurs
   Version 1.0.0   
  01/02/2010 - VGU - Creation fichier
**************************************************Guibert Vincent***
*******************************************************************/

require("./cf/conf_outil_icdc.php"); 
require_once("./cf/fonctions.php");
require_once("./cf/autre_fonctions.php");
// initialisation des variables 
if(isset($_GET['ANNEE'])){
	$ANNEE=$_GET['ANNEE'];
}else{
	$ANNEE=date("Y");
}
for($i=1;$i<=13;$i++){
	$NB_ALL_ALL[$i]['ALL']=0;
	$NB_ALL_ALL[$i]['AE']=0;
	$NB_ALL_ALL[$i]['LE']=0;
	$NB_ALL_ALL[$i]['TE']=0;
	$NB_ALL_ALL[$i]['ME']=0;
	$NB_ALL_ALL[$i]['DE']=0;
	$NB_ALL_ALL[$i]['AV']=0;
	$NB_ALL_ALL[$i]['LV']=0;
	$NB_ALL_ALL[$i]['TV']=0;
	$NB_ALL_ALL[$i]['MV']=0;
	$NB_ALL_ALL[$i]['DV']=0;
	$NB_ALL_ALL[$i]['MEP']=0;
	$NB_ALL_ALL[$i]['MA']=0;
	$NB_ALL_ALL[$i]['MEP_U']=0;
	$NB_ALL_ALL[$i]['MEP_S']=0;
	$NB_ALL_ALL[$i]['MEP_M']=0;
	$NB_ALL_ALL[$i]['MEP_C']=0;
	$NB_ALL_ALL[$i]['AB']=0;
	$NB_ALL_ALL[$i]['AB_U']=0;
	$NB_ALL_ALL[$i]['AB_S']=0;
	$NB_ALL_ALL[$i]['AB_M']=0;
	$NB_ALL_ALL[$i]['AB_C']=0;
}
$MOIS='';
$colspan=14;
$nb_colonne=12;

$j=0;
$ID='';

function Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$NIVEAU,$SOGETI,$mysql_link)
{
	if($ENV==''){$ENV_SQL='';}else{$ENV_SQL="`ENV`='".$ENV."' AND";}
	if($STATUS==''){$STATUS_SQL='';}else{$STATUS_SQL="`STATUS`='".$STATUS."' AND";}
	if($ACTION==''){$ACTION_SQL='';}else{$ACTION_SQL="`ACTION`='".$ACTION."' AND";}
	if($NATURE==''){$NATURE_SQL='';}else{$NATURE_SQL="`NATURE`='".$NATURE."' AND";}
	if($EN_VERSION==''){$EN_VERSION_SQL='';}else{$EN_VERSION_SQL="`EN_VERSION`='".$EN_VERSION."' AND";}
	if($EN_HVP==''){$EN_HVP_SQL='';}else{$EN_HVP_SQL="`EN_HVP`='".$EN_HVP."' AND";}
	if($EN_HVH==''){$EN_HVH_SQL='';}else{$EN_HVH_SQL="`EN_HVH`='".$EN_HVH."' AND";}
	if($APP==''){$APP_SQL='';}else{$APP_SQL="`APPLI`='".$APP."' AND";}
	if($SEMAINE==''){$SEMAINE_SQL='';}else{$SEMAINE_SQL="`DATE_SEMAINE`='".$SEMAINE."' AND";}
	if($SOGETI==''){$SOGETI_SQL='';}else{$SOGETI_SQL="`SOGETI`='".$SOGETI."' AND";}
	if($NIVEAU==''){$NIVEAU_SQL='';}else{$NIVEAU_SQL="UPPER(`NIVEAU`)=UPPER('".$NIVEAU."') AND";}
	 
// Calcul de nombre de ref odti en fonction des crit&egrave;res ci-dessus.
	$rq_info="
	SELECT COUNT(`REF`) AS `NB` 
	FROM `indicateur_calcul` 
	WHERE
	".$STATUS_SQL."
	`DATE_ANNEE`='".$ANNEE."' AND
	`DATE_MOIS`=".$MOIS." AND
	".$SEMAINE_SQL." 
	".$ENV_SQL."
	".$ACTION_SQL." 
	".$NATURE_SQL." 
	".$EN_VERSION_SQL." 
	".$EN_HVP_SQL." 
	".$EN_HVH_SQL." 
	".$APP_SQL." 
	".$SOGETI_SQL." 
	".$NIVEAU_SQL." 
	`DATE_INDICATEUR`='".$DATE_RECHERCHE."'";
	$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
	$tab_rq_info = mysql_fetch_assoc($res_rq_info);
	$total_ligne_rq_info=mysql_num_rows($res_rq_info);
	$NB=$tab_rq_info['NB'];
	mysql_free_result($res_rq_info);
	return $NB;
}

// contexte 
  echo '
   <div align="center">
    <br/>
    <table class="table_inc" cellspacing="1" cellpading="0">
      <tr align="center" class="titre">
        <td align="center" colspan="'.$colspan.'">&nbsp;[&nbsp;<a href="./index.php?ITEM=indicateur_menu">Retour</a>&nbsp;]&nbsp;-&nbsp;<a href="./indicateur/indicateur_sogeti_csv.php?ANNEE='.$ANNEE.'"><img src="./img/logo_excel.png" border="0"/></a>&nbsp;</td>
      </tr>
      <tr align="center" class="titre">
        <td align="center" colspan="'.$colspan.'"><b>&nbsp;Contexte pour '.$ANNEE.'&nbsp;</b></td>
      </tr>
      <tr align="center" class="titre">
        <td align="center"><b>&nbsp;</b></td>
        <td align="center"><b>&nbsp;Janvier&nbsp;</b></td>
        <td align="center"><b>&nbsp;F&eacute;vrier&nbsp;</b></td>
        <td align="center"><b>&nbsp;Mars&nbsp;</b></td>
        <td align="center"><b>&nbsp;Avril&nbsp;</b></td>
        <td align="center"><b>&nbsp;Mai&nbsp;</b></td>
        <td align="center"><b>&nbsp;Juin&nbsp;</b></td>
        <td align="center"><b>&nbsp;Juillet&nbsp;</b></td>
        <td align="center"><b>&nbsp;Aout&nbsp;</b></td>
        <td align="center"><b>&nbsp;Septembre&nbsp;</b></td>
        <td align="center"><b>&nbsp;Octobre&nbsp;</b></td>
        <td align="center"><b>&nbsp;Novembre&nbsp;</b></td>
        <td align="center"><b>&nbsp;D&eacute;cembre&nbsp;</b></td>
        <td align="center"><b>&nbsp;'.$ANNEE.'&nbsp;</b></td>
      </tr>
      <tr align="center" class="titre">
        <td align="center" colspan="'.$colspan.'">&nbsp;</td>
      </tr>';
// Jours ouvr&eacute;s
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Jours ouvr&eacute;s&nbsp</b></td>';
        $NB_ALL['ALL']=0;
        for($i=1;$i<=12;$i++){
        	if($i<10){
			$DATE_RECHERCHE=$ANNEE."0".$i;
		}else{
			$DATE_RECHERCHE=$ANNEE."".$i;
		}      	
		$rq_info="
		SELECT SUM(`INDICATEUR_INFO_LIB`) AS `NB` 
		FROM `indicateur_info` 
		WHERE `DATE_INDICATEUR`='".$DATE_RECHERCHE."' AND 
		`INDICATEUR_INFO_TYPE`='JOURS'";
		$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
		$tab_rq_info = mysql_fetch_assoc($res_rq_info);
		$total_ligne_rq_info=mysql_num_rows($res_rq_info);
		if($total_ligne_rq_info==0){
			$NB=0;
		}else{
			$NB=$tab_rq_info['NB'];
			if($NB==''){$NB=0;}
		}
		
		mysql_free_result($res_rq_info);
		
		$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
		echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
        }
        echo '<td align="center"><b>&nbsp;'.$NB_ALL['ALL'].'&nbsp;</b></td>';
        echo ' 
      </tr>';
// Effectif - Suivi de production IAB
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Effectif - Suivi de production IAB&nbsp</b></td>';
        $NB_ALL['ALL']=0;
        for($i=1;$i<=12;$i++){
        	if($i<10){
			$DATE_RECHERCHE=$ANNEE."0".$i;
		}else{
			$DATE_RECHERCHE=$ANNEE."".$i;
		}      	
		$rq_info="
		SELECT SUM(`INDICATEUR_INFO_LIB`) AS `NB` 
		FROM `indicateur_info` 
		WHERE `DATE_INDICATEUR`='".$DATE_RECHERCHE."' AND 
		`INDICATEUR_INFO_TYPE`='PROD_IAB'";
		$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
		$tab_rq_info = mysql_fetch_assoc($res_rq_info);
		$total_ligne_rq_info=mysql_num_rows($res_rq_info);
		if($total_ligne_rq_info==0){
			$NB=0;
		}else{
			$NB=$tab_rq_info['NB'];
			if($NB==''){$NB=0;}
		}
		mysql_free_result($res_rq_info);
		$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
		echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
        }
        echo '<td align="center"><b>&nbsp;'.$NB_ALL['ALL'].'&nbsp;</b></td>';
        echo ' 
      </tr>';
// Effectif - Transverse IAB
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Effectif - Transverse IAB&nbsp</b></td>';
        $NB_ALL['ALL']=0;
        for($i=1;$i<=12;$i++){
        	if($i<10){
			$DATE_RECHERCHE=$ANNEE."0".$i;
		}else{
			$DATE_RECHERCHE=$ANNEE."".$i;
		}      	
		$rq_info="
		SELECT SUM(`INDICATEUR_INFO_LIB`) AS `NB` 
		FROM `indicateur_info` 
		WHERE `DATE_INDICATEUR`='".$DATE_RECHERCHE."' AND 
		`INDICATEUR_INFO_TYPE`='TRANSVERSE_IAB'";
		$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
		$tab_rq_info = mysql_fetch_assoc($res_rq_info);
		$total_ligne_rq_info=mysql_num_rows($res_rq_info);
		if($total_ligne_rq_info==0){
			$NB=0;
		}else{
			$NB=$tab_rq_info['NB'];
			if($NB==''){$NB=0;}
		}
		mysql_free_result($res_rq_info);
		$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
		echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
        }
        echo '<td align="center"><b>&nbsp;'.$NB_ALL['ALL'].'&nbsp;</b></td>';
        echo ' 
      </tr>';
// Intervention HNO (nb tranches de 4h)
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Intervention HNO (nb tranches de 4h)&nbsp</b></td>';
        $NB_ALL['ALL']=0;
        for($i=1;$i<=12;$i++){
        	if($i<10){
			$DATE_RECHERCHE=$ANNEE."0".$i;
		}else{
			$DATE_RECHERCHE=$ANNEE."".$i;
		}      	
		$rq_info="
		SELECT SUM(`INDICATEUR_INFO_LIB`) AS `NB` 
		FROM `indicateur_info` 
		WHERE `DATE_INDICATEUR`='".$DATE_RECHERCHE."' AND 
		`INDICATEUR_INFO_TYPE`='HNO'";
		$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
		$tab_rq_info = mysql_fetch_assoc($res_rq_info);
		$total_ligne_rq_info=mysql_num_rows($res_rq_info);
		if($total_ligne_rq_info==0){
			$NB=0;
		}else{
			$NB=$tab_rq_info['NB'];
			if($NB==''){$NB=0;}
		}
		mysql_free_result($res_rq_info);
		$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
		echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
        }
        echo '<td align="center"><b>&nbsp;'.$NB_ALL['ALL'].'&nbsp;</b></td>';
        echo ' 
      </tr>';
// Nombre d'indidents BSP
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Nombre d\'indidents BSP&nbsp</b></td>';
        $NB_ALL['ALL']=0;
        for($i=1;$i<=12;$i++){
        	if($i<10){
			$DATE_RECHERCHE=$ANNEE."0".$i;
		}else{
			$DATE_RECHERCHE=$ANNEE."".$i;
		}      	
		$rq_info="
		SELECT SUM(`INDICATEUR_INFO_LIB`) AS `NB` 
		FROM `indicateur_info` 
		WHERE `DATE_INDICATEUR`='".$DATE_RECHERCHE."' AND 
		`INDICATEUR_INFO_TYPE`='NB_INCIDENTS_BSP'";
		$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
		$tab_rq_info = mysql_fetch_assoc($res_rq_info);
		$total_ligne_rq_info=mysql_num_rows($res_rq_info);
		if($total_ligne_rq_info==0){
			$NB=0;
		}else{
			$NB=$tab_rq_info['NB'];
			if($NB==''){$NB=0;}
		}
		mysql_free_result($res_rq_info);
		$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
		echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
        }
        echo '<td align="center"><b>&nbsp;'.$NB_ALL['ALL'].'&nbsp;</b></td>';
        echo ' 
      </tr>';
// Demandes ODTI
      echo '
      <tr align="center" class="titre">
        <td align="center" colspan="'.$colspan.'"><b>&nbsp;Demandes ODTI pour '.$ANNEE.'&nbsp;</b></td>
      </tr>
      <tr align="center" class="titre">
        <td align="center"><b>&nbsp;</b></td>
        <td align="center"><b>&nbsp;Janvier&nbsp;</b></td>
        <td align="center"><b>&nbsp;F&eacute;vrier&nbsp;</b></td>
        <td align="center"><b>&nbsp;Mars&nbsp;</b></td>
        <td align="center"><b>&nbsp;Avril&nbsp;</b></td>
        <td align="center"><b>&nbsp;Mai&nbsp;</b></td>
        <td align="center"><b>&nbsp;Juin&nbsp;</b></td>
        <td align="center"><b>&nbsp;Juillet&nbsp;</b></td>
        <td align="center"><b>&nbsp;Aout&nbsp;</b></td>
        <td align="center"><b>&nbsp;Septembre&nbsp;</b></td>
        <td align="center"><b>&nbsp;Octobre&nbsp;</b></td>
        <td align="center"><b>&nbsp;Novembre&nbsp;</b></td>
        <td align="center"><b>&nbsp;D&eacute;cembre&nbsp;</b></td>
        <td align="center"><b>&nbsp;'.$ANNEE.'&nbsp;</b></td>
      </tr>
      <tr align="center" class="titre">
        <td align="left" colspan="'.$colspan.'">&nbsp;Nb de demandes trait&eacute;es en Production&nbsp;</td>
      </tr>';
// reinitialisation des variables
      for($i=1;$i<=13;$i++){
      	$NB_ALL[$i]=0;
      }
// Autres (OTDI non encore valid&eacute;es)
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Autres (OTDI non encore valid&eacute;es)&nbsp</b></td>';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV="E";
	$STATUS="V";
	$ACTION="I";
	$NATURE="";
	$EN_VERSION="";
	$EN_HVP="";
	$EN_HVH="";
	$SOGETI="Y";
	$NIVEAU="";
        for($i=1;$i<=12;$i++){
        	if($i<10){
			$DATE_RECHERCHE=$ANNEE."0".$i;
		}else{
			$DATE_RECHERCHE=$ANNEE."".$i;
		}        	
		$MOIS=$i;
		$SEMAINE="";
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$NIVEAU,$SOGETI,$mysql_link);
		$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
		$NB_ALL_ALL[$i]['AE']=$NB_ALL_ALL[$i]['AE']+$NB;
		echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
		
        }
        echo '<td align="center"><b>&nbsp;'.$NB_ALL['ALL'].'&nbsp;</b></td>';
        echo ' 
      </tr>';
// Livraison de composants
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Livraison de composants&nbsp</b></td>';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV="E";
	$STATUS="V";
	$ACTION="L";
	$NATURE="";
	$EN_VERSION="";
	$EN_HVP="";
	$EN_HVH="";
	$SOGETI="Y";
	$NIVEAU="";
        for($i=1;$i<=12;$i++){
        	if($i<10){
			$DATE_RECHERCHE=$ANNEE."0".$i;
		}else{
			$DATE_RECHERCHE=$ANNEE."".$i;
		}        	
		$MOIS=$i;
		$SEMAINE="";
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$NIVEAU,$SOGETI,$mysql_link);
		$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
		$NB_ALL_ALL[$i]['LE']=$NB_ALL_ALL[$i]['LE']+$NB;
		echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
		
        }
        echo '<td align="center"><b>&nbsp;'.$NB_ALL['ALL'].'&nbsp;</b></td>';
        echo ' 
      </tr>';
// traitements exceptionnels
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;traitements exceptionnels&nbsp</b></td>';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV="E";
	$STATUS="V";
	$ACTION="T";
	$NATURE="";
	$EN_VERSION="";
	$EN_HVP="";
	$EN_HVH="";
	$SOGETI="Y";
	$NIVEAU="";
        for($i=1;$i<=12;$i++){
        	if($i<10){
			$DATE_RECHERCHE=$ANNEE."0".$i;
		}else{
			$DATE_RECHERCHE=$ANNEE."".$i;
		}        	
		$MOIS=$i;
		$SEMAINE="";
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$NIVEAU,$SOGETI,$mysql_link);
		$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
		$NB_ALL_ALL[$i]['TE']=$NB_ALL_ALL[$i]['TE']+$NB;
		echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
		
        }
        echo '<td align="center"><b>&nbsp;'.$NB_ALL['ALL'].'&nbsp;</b></td>';
        echo ' 
      </tr>';
// Modifications IAB (Action Ponstuelle)
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Modifications IAB (Action Ponstuelle)&nbsp</b></td>';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV="E";
	$STATUS="V";
	$ACTION="M";
	$NATURE="";
	$EN_VERSION="";
	$EN_HVP="";
	$EN_HVH="";
	$SOGETI="Y";
	$NIVEAU="";
        for($i=1;$i<=12;$i++){
        	if($i<10){
			$DATE_RECHERCHE=$ANNEE."0".$i;
		}else{
			$DATE_RECHERCHE=$ANNEE."".$i;
		}        	
		$MOIS=$i;
		$SEMAINE="";
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$NIVEAU,$SOGETI,$mysql_link);
		$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
		$NB_ALL_ALL[$i]['ME']=$NB_ALL_ALL[$i]['ME']+$NB;
		echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
		
        }
        echo '<td align="center"><b>&nbsp;'.$NB_ALL['ALL'].'&nbsp;</b></td>';
        echo ' 
      </tr>';
// Demandes MOA (Action Ponctuelle)
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Demandes MOA (Action Ponctuelle)&nbsp</b></td>';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV="E";
	$STATUS="V";
	$ACTION="D";
	$NATURE="";
	$EN_VERSION="";
	$EN_HVP="";
	$EN_HVH="";
	$SOGETI="Y";
	$NIVEAU="";
        for($i=1;$i<=12;$i++){
        	if($i<10){
			$DATE_RECHERCHE=$ANNEE."0".$i;
		}else{
			$DATE_RECHERCHE=$ANNEE."".$i;
		}        	
		$MOIS=$i;
		$SEMAINE="";
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$NIVEAU,$SOGETI,$mysql_link);
		$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
		$NB_ALL_ALL[$i]['DE']=$NB_ALL_ALL[$i]['DE']+$NB;
		echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
		
        }
        echo '<td align="center"><b>&nbsp;'.$NB_ALL['ALL'].'&nbsp;</b></td>';
        echo ' 
      </tr>';
// Totaux
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Totaux&nbsp</b></td>';
        $NB_ALL['ALL']=0;
        for($i=1;$i<=12;$i++){

		$NB=$NB_ALL_ALL[$i]['AE']+$NB_ALL_ALL[$i]['LE']+$NB_ALL_ALL[$i]['TE']+$NB_ALL_ALL[$i]['ME']+$NB_ALL_ALL[$i]['DE'];
		$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
		echo '<td align="center"><b>&nbsp;'.$NB.'&nbsp;</b></td>';	
		
        }
        echo '<td align="center"><b><b>&nbsp;'.$NB_ALL['ALL'].'&nbsp;</b></b></td>';
        echo ' 
      </tr>';
// Nb de demandes trait&eacute;es en VA
      echo '
      <tr align="center" class="titre">
        <td align="left" colspan="'.$colspan.'">&nbsp;Nb de demandes trait&eacute;es en VA&nbsp;</td>
      </tr>';
      for($i=1;$i<=13;$i++){
      	$NB_ALL[$i]=0;
      }
// Autres (OTDI non encore valid&eacute;es)
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Autres (OTDI non encore valid&eacute;es)&nbsp</b></td>';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV="V";
	$STATUS="V";
	$ACTION="I";
	$NATURE="";
	$EN_VERSION="";
	$EN_HVP="";
	$EN_HVH="";
	$SOGETI="Y";
	$NIVEAU="";
        for($i=1;$i<=12;$i++){
        	if($i<10){
			$DATE_RECHERCHE=$ANNEE."0".$i;
		}else{
			$DATE_RECHERCHE=$ANNEE."".$i;
		}        	
		$MOIS=$i;
		$SEMAINE="";
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$NIVEAU,$SOGETI,$mysql_link);
		$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
		$NB_ALL_ALL[$i]['AV']=$NB_ALL_ALL[$i]['AV']+$NB;
		echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
		
        }
        echo '<td align="center"><b>&nbsp;'.$NB_ALL['ALL'].'&nbsp;</b></td>';
        echo ' 
      </tr>';
// Livraison de composants
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Livraison de composants&nbsp</b></td>';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV="V";
	$STATUS="V";
	$ACTION="L";
	$NATURE="";
	$EN_VERSION="";
	$EN_HVP="";
	$EN_HVH="";
	$SOGETI="Y";
	$NIVEAU="";
        for($i=1;$i<=12;$i++){
        	if($i<10){
			$DATE_RECHERCHE=$ANNEE."0".$i;
		}else{
			$DATE_RECHERCHE=$ANNEE."".$i;
		}        	
		$MOIS=$i;
		$SEMAINE="";
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$NIVEAU,$SOGETI,$mysql_link);
		$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
		$NB_ALL_ALL[$i]['LV']=$NB_ALL_ALL[$i]['LV']+$NB;
		echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
		
        }
        echo '<td align="center"><b>&nbsp;'.$NB_ALL['ALL'].'&nbsp;</b></td>';
        echo ' 
      </tr>';
// traitements exceptionnels
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;traitements exceptionnels&nbsp</b></td>';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV="V";
	$STATUS="V";
	$ACTION="T";
	$NATURE="";
	$EN_VERSION="";
	$EN_HVP="";
	$EN_HVH="";
	$SOGETI="Y";
	$NIVEAU="";
        for($i=1;$i<=12;$i++){
        	if($i<10){
			$DATE_RECHERCHE=$ANNEE."0".$i;
		}else{
			$DATE_RECHERCHE=$ANNEE."".$i;
		}        	
		$MOIS=$i;
		$SEMAINE="";
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$NIVEAU,$SOGETI,$mysql_link);
		$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
		$NB_ALL_ALL[$i]['TV']=$NB_ALL_ALL[$i]['TV']+$NB;
		echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
		
        }
        echo '<td align="center"><b>&nbsp;'.$NB_ALL['ALL'].'&nbsp;</b></td>';
        echo ' 
      </tr>';
// Modifications IAB (Action Ponstuelle)
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Modifications IAB (Action Ponstuelle)&nbsp</b></td>';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV="V";
	$STATUS="V";
	$ACTION="M";
	$NATURE="";
	$EN_VERSION="";
	$EN_HVP="";
	$EN_HVH="";
	$SOGETI="Y";
	$NIVEAU="";
        for($i=1;$i<=12;$i++){
        	if($i<10){
			$DATE_RECHERCHE=$ANNEE."0".$i;
		}else{
			$DATE_RECHERCHE=$ANNEE."".$i;
		}        	
		$MOIS=$i;
		$SEMAINE="";
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$NIVEAU,$SOGETI,$mysql_link);
		$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
		$NB_ALL_ALL[$i]['MV']=$NB_ALL_ALL[$i]['MV']+$NB;
		echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
		
        }
        echo '<td align="center"><b>&nbsp;'.$NB_ALL['ALL'].'&nbsp;</b></td>';
        echo ' 
      </tr>';
// Demandes MOA (Action Ponctuelle)
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Demandes MOA (Action Ponctuelle)&nbsp</b></td>';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV="V";
	$STATUS="V";
	$ACTION="D";
	$NATURE="";
	$EN_VERSION="";
	$EN_HVP="";
	$EN_HVH="";
	$SOGETI="Y";
	$NIVEAU="";
        for($i=1;$i<=12;$i++){
        	if($i<10){
			$DATE_RECHERCHE=$ANNEE."0".$i;
		}else{
			$DATE_RECHERCHE=$ANNEE."".$i;
		}        	
		$MOIS=$i;
		$SEMAINE="";
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$NIVEAU,$SOGETI,$mysql_link);
		$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
		$NB_ALL_ALL[$i]['DV']=$NB_ALL_ALL[$i]['DV']+$NB;
		echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
		
        }
        echo '<td align="center"><b>&nbsp;'.$NB_ALL['ALL'].'&nbsp;</b></td>';
        echo ' 
      </tr>';
// Totaux
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Totaux&nbsp</b></td>';
        $NB_ALL['ALL']=0;
        for($i=1;$i<=12;$i++){

		$NB=$NB_ALL_ALL[$i]['AV']+$NB_ALL_ALL[$i]['LV']+$NB_ALL_ALL[$i]['TV']+$NB_ALL_ALL[$i]['MV']+$NB_ALL_ALL[$i]['DV'];
		$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
		echo '<td align="center"><b>&nbsp;'.$NB.'&nbsp;</b></td>';	
		
        }
        echo '<td align="center"><b><b>&nbsp;'.$NB_ALL['ALL'].'&nbsp;</b></b></td>';
        echo ' 
      </tr>';
// Typologie des demandes
      echo '
      <tr align="center" class="titre">
        <td align="left" colspan="'.$colspan.'">&nbsp;Typologie des demandes&nbsp;</td>
      </tr>';
// Production Simple
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Production Simple&nbsp</b></td>';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV="E";
	$STATUS="V";
	$ACTION="";
	$NATURE="";
	$EN_VERSION="";
	$EN_HVP="";
	$EN_HVH="";
	$SOGETI="Y";
	$NIVEAU="Simple";
        for($i=1;$i<=12;$i++){
        	if($i<10){
			$DATE_RECHERCHE=$ANNEE."0".$i;
		}else{
			$DATE_RECHERCHE=$ANNEE."".$i;
		}        	
		$MOIS=$i;
		$SEMAINE="";
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$NIVEAU,$SOGETI,$mysql_link);
		$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
		echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
		
        }
        echo '<td align="center"><b>&nbsp;'.$NB_ALL['ALL'].'&nbsp;</b></td>';
        echo ' 
      </tr>';
// VA Simple
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;VA Simple&nbsp</b></td>';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV="V";
	$STATUS="V";
	$ACTION="";
	$NATURE="";
	$EN_VERSION="";
	$EN_HVP="";
	$EN_HVH="";
	$SOGETI="Y";
	$NIVEAU="Simple";
        for($i=1;$i<=12;$i++){
        	if($i<10){
			$DATE_RECHERCHE=$ANNEE."0".$i;
		}else{
			$DATE_RECHERCHE=$ANNEE."".$i;
		}        	
		$MOIS=$i;
		$SEMAINE="";
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$NIVEAU,$SOGETI,$mysql_link);
		$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
		echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
		
        }
        echo '<td align="center"><b>&nbsp;'.$NB_ALL['ALL'].'&nbsp;</b></td>';
        echo ' 
      </tr>';
// Production Moyenne
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Production Moyenne&nbsp</b></td>';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV="E";
	$STATUS="V";
	$ACTION="";
	$NATURE="";
	$EN_VERSION="";
	$EN_HVP="";
	$EN_HVH="";
	$SOGETI="Y";
	$NIVEAU="Moyenne";
        for($i=1;$i<=12;$i++){
        	if($i<10){
			$DATE_RECHERCHE=$ANNEE."0".$i;
		}else{
			$DATE_RECHERCHE=$ANNEE."".$i;
		}        	
		$MOIS=$i;
		$SEMAINE="";
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$NIVEAU,$SOGETI,$mysql_link);
		$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
		echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
		
        }
        echo '<td align="center"><b>&nbsp;'.$NB_ALL['ALL'].'&nbsp;</b></td>';
        echo ' 
      </tr>';
// VA Moyenne
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;VA Moyenne&nbsp</b></td>';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV="V";
	$STATUS="V";
	$ACTION="";
	$NATURE="";
	$EN_VERSION="";
	$EN_HVP="";
	$EN_HVH="";
	$SOGETI="Y";
	$NIVEAU="Moyenne";
        for($i=1;$i<=12;$i++){
        	if($i<10){
			$DATE_RECHERCHE=$ANNEE."0".$i;
		}else{
			$DATE_RECHERCHE=$ANNEE."".$i;
		}        	
		$MOIS=$i;
		$SEMAINE="";
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$NIVEAU,$SOGETI,$mysql_link);
		$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
		echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
		
        }
        echo '<td align="center"><b>&nbsp;'.$NB_ALL['ALL'].'&nbsp;</b></td>';
        echo ' 
      </tr>';
// Production Complexe
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Production Complexe&nbsp</b></td>';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV="E";
	$STATUS="V";
	$ACTION="";
	$NATURE="";
	$EN_VERSION="";
	$EN_HVP="";
	$EN_HVH="";
	$SOGETI="Y";
	$NIVEAU="Complexe";
        for($i=1;$i<=12;$i++){
        	if($i<10){
			$DATE_RECHERCHE=$ANNEE."0".$i;
		}else{
			$DATE_RECHERCHE=$ANNEE."".$i;
		}        	
		$MOIS=$i;
		$SEMAINE="";
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$NIVEAU,$SOGETI,$mysql_link);
		$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
		echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
		
        }
        echo '<td align="center"><b>&nbsp;'.$NB_ALL['ALL'].'&nbsp;</b></td>';
        echo ' 
      </tr>'; 
// VA Complexe
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;VA Complexe&nbsp</b></td>';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV="V";
	$STATUS="V";
	$ACTION="";
	$NATURE="";
	$EN_VERSION="";
	$EN_HVP="";
	$EN_HVH="";
	$SOGETI="Y";
	$NIVEAU="Complexe";
        for($i=1;$i<=12;$i++){
        	if($i<10){
			$DATE_RECHERCHE=$ANNEE."0".$i;
		}else{
			$DATE_RECHERCHE=$ANNEE."".$i;
		}        	
		$MOIS=$i;
		$SEMAINE="";
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$NIVEAU,$SOGETI,$mysql_link);
		$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
		echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
		
        }
        echo '<td align="center"><b>&nbsp;'.$NB_ALL['ALL'].'&nbsp;</b></td>';
        echo ' 
      </tr>';
// Typologie des demandes
      echo '
      <tr align="center" class="titre">
        <td align="left" colspan="'.$colspan.'">&nbsp;Typologie des demandes&nbsp;</td>
      </tr>';
// MEP Simple
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;MEP Simple&nbsp</b></td>';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV="";
	$STATUS="V";
	$NATURE="";
	$EN_VERSION="";
	$EN_HVP="";
	$EN_HVH="";
	$SOGETI="Y";
	$NIVEAU="Simple";
        for($i=1;$i<=12;$i++){
        	if($i<10){
			$DATE_RECHERCHE=$ANNEE."0".$i;
		}else{
			$DATE_RECHERCHE=$ANNEE."".$i;
		}        	
		$MOIS=$i;
		$SEMAINE="";
		$ACTION="L";
		$NB1=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$NIVEAU,$SOGETI,$mysql_link);
		$ACTION="M";
		$NB2=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$NIVEAU,$SOGETI,$mysql_link);
		$NB=$NB1+$NB2;
		$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
		$NB_ALL_ALL[$i]['MEP_S']=$NB;
		$NB_ALL_ALL[13]['MEP_S']=$NB_ALL['ALL'];
		echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
		
        }
        echo '<td align="center"><b>&nbsp;'.$NB_ALL['ALL'].'&nbsp;</b></td>';
        echo ' 
      </tr>';
// MEP Moyenne
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;MEP Moyenne&nbsp</b></td>';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV="";
	$STATUS="V";
	$NATURE="";
	$EN_VERSION="";
	$EN_HVP="";
	$EN_HVH="";
	$SOGETI="Y";
	$NIVEAU="Moyenne";
        for($i=1;$i<=12;$i++){
        	if($i<10){
			$DATE_RECHERCHE=$ANNEE."0".$i;
		}else{
			$DATE_RECHERCHE=$ANNEE."".$i;
		}        	
		$MOIS=$i;
		$SEMAINE="";
		$ACTION="L";
		$NB1=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$NIVEAU,$SOGETI,$mysql_link);
		$ACTION="M";
		$NB2=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$NIVEAU,$SOGETI,$mysql_link);
		$NB=$NB1+$NB2;
		$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
		$NB_ALL_ALL[$i]['MEP_M']=$NB;
		$NB_ALL_ALL[13]['MEP_M']=$NB_ALL['ALL'];
		echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
		
        }
        echo '<td align="center"><b>&nbsp;'.$NB_ALL['ALL'].'&nbsp;</b></td>';
        echo ' 
      </tr>';
// MEP Complexe
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;MEP Complexe&nbsp</b></td>';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV="";
	$STATUS="V";
	$NATURE="";
	$EN_VERSION="";
	$EN_HVP="";
	$EN_HVH="";
	$SOGETI="Y";
	$NIVEAU="Complexe";
        for($i=1;$i<=12;$i++){
        	if($i<10){
			$DATE_RECHERCHE=$ANNEE."0".$i;
		}else{
			$DATE_RECHERCHE=$ANNEE."".$i;
		}        	
		$MOIS=$i;
		$SEMAINE="";
		$ACTION="L";
		$NB1=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$NIVEAU,$SOGETI,$mysql_link);
		$ACTION="M";
		$NB2=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$NIVEAU,$SOGETI,$mysql_link);
		$NB=$NB1+$NB2;
		$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
		$NB_ALL_ALL[$i]['MEP_C']=$NB;
		$NB_ALL_ALL[13]['MEP_C']=$NB_ALL['ALL'];
		echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
		
        }
        echo '<td align="center"><b>&nbsp;'.$NB_ALL['ALL'].'&nbsp;</b></td>';
        echo ' 
      </tr>';
// AB Simple
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;AB Simple&nbsp</b></td>';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV="";
	$STATUS="V";
	$NATURE="";
	$EN_VERSION="";
	$EN_HVP="";
	$EN_HVH="";
	$SOGETI="Y";
	$NIVEAU="Simple";
        for($i=1;$i<=12;$i++){
        	if($i<10){
			$DATE_RECHERCHE=$ANNEE."0".$i;
		}else{
			$DATE_RECHERCHE=$ANNEE."".$i;
		}        	
		$MOIS=$i;
		$SEMAINE="";
		$ACTION="T";
		$NB1=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$NIVEAU,$SOGETI,$mysql_link);
		$ACTION="D";
		$NB2=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$NIVEAU,$SOGETI,$mysql_link);
		$NB=$NB1+$NB2;
		$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
		$NB_ALL_ALL[$i]['AB_S']=$NB;
		$NB_ALL_ALL[13]['AB_S']=$NB_ALL['ALL'];
		echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
		
        }
        echo '<td align="center"><b>&nbsp;'.$NB_ALL['ALL'].'&nbsp;</b></td>';
        echo ' 
      </tr>';
// AB Moyenne
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;AB Moyenne&nbsp</b></td>';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV="";
	$STATUS="V";
	$NATURE="";
	$EN_VERSION="";
	$EN_HVP="";
	$EN_HVH="";
	$SOGETI="Y";
	$NIVEAU="Moyenne";
        for($i=1;$i<=12;$i++){
        	if($i<10){
			$DATE_RECHERCHE=$ANNEE."0".$i;
		}else{
			$DATE_RECHERCHE=$ANNEE."".$i;
		}        	
		$MOIS=$i;
		$SEMAINE="";
		$ACTION="T";
		$NB1=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$NIVEAU,$SOGETI,$mysql_link);
		$ACTION="D";
		$NB2=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$NIVEAU,$SOGETI,$mysql_link);
		$NB=$NB1+$NB2;
		$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
		$NB_ALL_ALL[$i]['AB_M']=$NB;
		$NB_ALL_ALL[13]['AB_M']=$NB_ALL['ALL'];
		echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
		
        }
        echo '<td align="center"><b>&nbsp;'.$NB_ALL['ALL'].'&nbsp;</b></td>';
        echo ' 
      </tr>'; 
// AB Complexe
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;AB Complexe&nbsp</b></td>';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV="";
	$STATUS="V";
	$NATURE="";
	$EN_VERSION="";
	$EN_HVP="";
	$EN_HVH="";
	$SOGETI="Y";
	$NIVEAU="Complexe";
        for($i=1;$i<=12;$i++){
        	if($i<10){
			$DATE_RECHERCHE=$ANNEE."0".$i;
		}else{
			$DATE_RECHERCHE=$ANNEE."".$i;
		}        	
		$MOIS=$i;
		$SEMAINE="";
		$ACTION="T";
		$NB1=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$NIVEAU,$SOGETI,$mysql_link);
		$ACTION="D";
		$NB2=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$NIVEAU,$SOGETI,$mysql_link);
		$NB=$NB1+$NB2;
		$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
		$NB_ALL_ALL[$i]['AB_C']=$NB;
		$NB_ALL_ALL[13]['AB_C']=$NB_ALL['ALL'];
		echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
		
        }
        echo '<td align="center"><b>&nbsp;'.$NB_ALL['ALL'].'&nbsp;</b></td>';
        echo ' 
      </tr>';
// Typologie des demandes
      echo '
      <tr align="center" class="titre">
        <td align="left" colspan="'.$colspan.'">&nbsp;Typologie des demandes&nbsp;</td>
      </tr>';
// MEP Poids
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;MEP Poids&nbsp</b></td>';
        $NB_ALL['ALL']=0;
        for($i=1;$i<=12;$i++){
        	if($i<10){
			$DATE_RECHERCHE=$ANNEE."0".$i;
		}else{
			$DATE_RECHERCHE=$ANNEE."".$i;
		}  
		$rq_info="
		SELECT * FROM `indicateur_config` WHERE 
		`INDICATEUR_CONFIG_DATE`<'".$DATE_RECHERCHE."' AND 
		`INDICATEUR_CONFIG_TYPE`='PERIMETRE' AND
		UPPER(`INDICATEUR_CONFIG_LIB`)=UPPER('Simple')
		ORDER BY `INDICATEUR_CONFIG_DATE` DESC LIMIT 1";
		$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
		$tab_rq_info = mysql_fetch_assoc($res_rq_info);
		$total_ligne_rq_info=mysql_num_rows($res_rq_info);
		if($total_ligne_rq_info==0){
			$PS=1;
		}else{
			$PS=$tab_rq_info['INDICATEUR_CONFIG_COEF'];
		}
		mysql_free_result($res_rq_info);
		$rq_info="
		SELECT * FROM `indicateur_config` WHERE 
		`INDICATEUR_CONFIG_DATE`<'".$DATE_RECHERCHE."' AND 
		`INDICATEUR_CONFIG_TYPE`='PERIMETRE' AND
		UPPER(`INDICATEUR_CONFIG_LIB`)=UPPER('Moyen')
		ORDER BY `INDICATEUR_CONFIG_DATE` DESC LIMIT 1";
		$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
		$tab_rq_info = mysql_fetch_assoc($res_rq_info);
		$total_ligne_rq_info=mysql_num_rows($res_rq_info);
		if($total_ligne_rq_info==0){
			$PM=2;
		}else{
			$PM=$tab_rq_info['INDICATEUR_CONFIG_COEF'];
		}
		mysql_free_result($res_rq_info);
		$rq_info="
		SELECT * FROM `indicateur_config` WHERE 
		`INDICATEUR_CONFIG_DATE`<'".$DATE_RECHERCHE."' AND 
		`INDICATEUR_CONFIG_TYPE`='PERIMETRE' AND
		UPPER(`INDICATEUR_CONFIG_LIB`)=UPPER('Complexe')
		ORDER BY `INDICATEUR_CONFIG_DATE` DESC LIMIT 1";
		$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
		$tab_rq_info = mysql_fetch_assoc($res_rq_info);
		$total_ligne_rq_info=mysql_num_rows($res_rq_info);
		if($total_ligne_rq_info==0){
			$PC=4;
		}else{
			$PC=$tab_rq_info['INDICATEUR_CONFIG_COEF'];
		}
		mysql_free_result($res_rq_info);

        	$NB=$NB_ALL_ALL[$i]['MEP_S']*$PS+$NB_ALL_ALL[$i]['MEP_M']*$PM+$NB_ALL_ALL[$i]['MEP_C']*$PC;
		$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
		$NB_ALL_ALL[$i]['MEP']+$NB;
		$NB_ALL_ALL[13]['MEP']=$NB_ALL['ALL'];
		echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
        }
        echo '<td align="center"><b>&nbsp;'.$NB_ALL['ALL'].'&nbsp;</b></td>';
        echo ' 
      </tr>';
// MEP Unit&eacute;s d'oeuvres
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;MEP Unit&eacute;s d\'oeuvres&nbsp</b></td>';
        $NB_ALL['ALL']=0;
        for($i=1;$i<=12;$i++){
        	if($i<10){
			$DATE_RECHERCHE=$ANNEE."0".$i;
		}else{
			$DATE_RECHERCHE=$ANNEE."".$i;
		}  
		$rq_info="
		SELECT * FROM `indicateur_config` WHERE 
		`INDICATEUR_CONFIG_DATE`<'".$DATE_RECHERCHE."' AND 
		`INDICATEUR_CONFIG_TYPE`='PERIMETRE'";
		$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
		$tab_rq_info = mysql_fetch_assoc($res_rq_info);
		$total_ligne_rq_info=mysql_num_rows($res_rq_info); 
		if($total_ligne_rq_info==0){
			$PT=162;
		}else{
		      	$PT=0;
			do {
				$PT=$PT+$tab_rq_info['INDICATEUR_CONFIG_COEF']*$tab_rq_info['INDICATEUR_CONFIG_NB_APPLI'];
			} while ($tab_rq_info = mysql_fetch_assoc($res_rq_info));
			$ligne= mysql_num_rows($res_rq_info);
			if($ligne > 0) {
				mysql_data_seek($res_rq_info, 0);
				$tab_rq_info = mysql_fetch_assoc($res_rq_info);
			}
		}
		mysql_free_result($res_rq_info);

        	$NB=$PT*($NB_ALL_ALL[$i]['MEP_S']*$PS+$NB_ALL_ALL[$i]['MEP_M']*$PM+$NB_ALL_ALL[$i]['MEP_C']*$PC);
		$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
		$NB_ALL_ALL[$i]['MEP_U']=$NB;
		$NB_ALL_ALL[13]['MEP_U']=$NB_ALL['ALL'];
		echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
        }
        echo '<td align="center"><b>&nbsp;'.$NB_ALL['ALL'].'&nbsp;</b></td>';
        echo ' 
      </tr>';
// AB Poids
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;AB Poids&nbsp</b></td>';
        $NB_ALL['ALL']=0;
        for($i=1;$i<=12;$i++){
        	if($i<10){
			$DATE_RECHERCHE=$ANNEE."0".$i;
		}else{
			$DATE_RECHERCHE=$ANNEE."".$i;
		}  
		$rq_info="
		SELECT * FROM `indicateur_config` WHERE 
		`INDICATEUR_CONFIG_DATE`<'".$DATE_RECHERCHE."' AND 
		`INDICATEUR_CONFIG_TYPE`='PERIMETRE' AND
		UPPER(`INDICATEUR_CONFIG_LIB`)=UPPER('Simple')
		ORDER BY `INDICATEUR_CONFIG_DATE` DESC LIMIT 1";
		$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
		$tab_rq_info = mysql_fetch_assoc($res_rq_info);
		$total_ligne_rq_info=mysql_num_rows($res_rq_info);
		if($total_ligne_rq_info==0){
			$PS=1;
		}else{
			$PS=$tab_rq_info['INDICATEUR_CONFIG_COEF'];
		}
		mysql_free_result($res_rq_info);
		$rq_info="
		SELECT * FROM `indicateur_config` WHERE 
		`INDICATEUR_CONFIG_DATE`<'".$DATE_RECHERCHE."' AND 
		`INDICATEUR_CONFIG_TYPE`='PERIMETRE' AND
		UPPER(`INDICATEUR_CONFIG_LIB`)=UPPER('Moyen')
		ORDER BY `INDICATEUR_CONFIG_DATE` DESC LIMIT 1";
		$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
		$tab_rq_info = mysql_fetch_assoc($res_rq_info);
		$total_ligne_rq_info=mysql_num_rows($res_rq_info);
		if($total_ligne_rq_info==0){
			$PM=2;
		}else{
			$PM=$tab_rq_info['INDICATEUR_CONFIG_COEF'];
		}
		mysql_free_result($res_rq_info);
		$rq_info="
		SELECT * FROM `indicateur_config` WHERE 
		`INDICATEUR_CONFIG_DATE`<'".$DATE_RECHERCHE."' AND 
		`INDICATEUR_CONFIG_TYPE`='PERIMETRE' AND
		UPPER(`INDICATEUR_CONFIG_LIB`)=UPPER('Complexe')
		ORDER BY `INDICATEUR_CONFIG_DATE` DESC LIMIT 1";
		$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
		$tab_rq_info = mysql_fetch_assoc($res_rq_info);
		$total_ligne_rq_info=mysql_num_rows($res_rq_info);
		if($total_ligne_rq_info==0){
			$PC=4;
		}else{
			$PC=$tab_rq_info['INDICATEUR_CONFIG_COEF'];
		}
		mysql_free_result($res_rq_info);

        	$NB=$NB_ALL_ALL[$i]['AB_S']*$PS+$NB_ALL_ALL[$i]['AB_M']*$PM+$NB_ALL_ALL[$i]['AB_C']*$PC;
		$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
		$NB_ALL_ALL[$i]['AB']+$NB;
		$NB_ALL_ALL[13]['AB']=$NB_ALL['ALL'];
		echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
        }
        echo '<td align="center"><b>&nbsp;'.$NB_ALL['ALL'].'&nbsp;</b></td>';
        echo ' 
      </tr>';
// AB Unit&eacute;s d'oeuvres
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;AB Unit&eacute;s d\'oeuvres&nbsp</b></td>';
        $NB_ALL['ALL']=0;
        for($i=1;$i<=12;$i++){
        	if($i<10){
			$DATE_RECHERCHE=$ANNEE."0".$i;
		}else{
			$DATE_RECHERCHE=$ANNEE."".$i;
		}  
		$rq_info="
		SELECT * FROM `indicateur_config` WHERE 
		`INDICATEUR_CONFIG_DATE`<'".$DATE_RECHERCHE."' AND 
		`INDICATEUR_CONFIG_TYPE`='PERIMETRE'";
		$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
		$tab_rq_info = mysql_fetch_assoc($res_rq_info);
		$total_ligne_rq_info=mysql_num_rows($res_rq_info); 
		if($total_ligne_rq_info==0){
			$PT=162;
		}else{
		      	$PT=0;
			do {
				$PT=$PT+$tab_rq_info['INDICATEUR_CONFIG_COEF']*$tab_rq_info['INDICATEUR_CONFIG_NB_APPLI'];
			} while ($tab_rq_info = mysql_fetch_assoc($res_rq_info));
			$ligne= mysql_num_rows($res_rq_info);
			if($ligne > 0) {
				mysql_data_seek($res_rq_info, 0);
				$tab_rq_info = mysql_fetch_assoc($res_rq_info);
			}
		}
		mysql_free_result($res_rq_info);

        	$NB=$PT*($NB_ALL_ALL[$i]['AB_S']*$PS+$NB_ALL_ALL[$i]['AB_M']*$PM+$NB_ALL_ALL[$i]['AB_C']*$PC);
        	$NB_ALL_ALL[$i]['AB_U']=$NB;
        	$NB_ALL_ALL[13]['AB_U']=$NB_ALL['ALL'];
		$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
		echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
        }
        echo '<td align="center"><b>&nbsp;'.$NB_ALL['ALL'].'&nbsp;</b></td>';
        echo ' 
      </tr>';
// Total Poids
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Total Poids&nbsp</b></td>';
        $NB_ALL['ALL']=0;
        for($i=1;$i<=12;$i++){
        	if($i<10){
			$DATE_RECHERCHE=$ANNEE."0".$i;
		}else{
			$DATE_RECHERCHE=$ANNEE."".$i;
		}  
		$rq_info="
		SELECT * FROM `indicateur_config` WHERE 
		`INDICATEUR_CONFIG_DATE`<'".$DATE_RECHERCHE."' AND 
		`INDICATEUR_CONFIG_TYPE`='PERIMETRE' AND
		UPPER(`INDICATEUR_CONFIG_LIB`)=UPPER('Simple')
		ORDER BY `INDICATEUR_CONFIG_DATE` DESC LIMIT 1";
		$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
		$tab_rq_info = mysql_fetch_assoc($res_rq_info);
		$total_ligne_rq_info=mysql_num_rows($res_rq_info);
		if($total_ligne_rq_info==0){
			$PS=1;
		}else{
			$PS=$tab_rq_info['INDICATEUR_CONFIG_COEF'];
		}
		mysql_free_result($res_rq_info);
		$rq_info="
		SELECT * FROM `indicateur_config` WHERE 
		`INDICATEUR_CONFIG_DATE`<'".$DATE_RECHERCHE."' AND 
		`INDICATEUR_CONFIG_TYPE`='PERIMETRE' AND
		UPPER(`INDICATEUR_CONFIG_LIB`)=UPPER('Moyen')
		ORDER BY `INDICATEUR_CONFIG_DATE` DESC LIMIT 1";
		$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
		$tab_rq_info = mysql_fetch_assoc($res_rq_info);
		$total_ligne_rq_info=mysql_num_rows($res_rq_info);
		if($total_ligne_rq_info==0){
			$PM=2;
		}else{
			$PM=$tab_rq_info['INDICATEUR_CONFIG_COEF'];
		}
		mysql_free_result($res_rq_info);
		$rq_info="
		SELECT * FROM `indicateur_config` WHERE 
		`INDICATEUR_CONFIG_DATE`<'".$DATE_RECHERCHE."' AND 
		`INDICATEUR_CONFIG_TYPE`='PERIMETRE' AND
		UPPER(`INDICATEUR_CONFIG_LIB`)=UPPER('Complexe')
		ORDER BY `INDICATEUR_CONFIG_DATE` DESC LIMIT 1";
		$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
		$tab_rq_info = mysql_fetch_assoc($res_rq_info);
		$total_ligne_rq_info=mysql_num_rows($res_rq_info);
		if($total_ligne_rq_info==0){
			$PC=4;
		}else{
			$PC=$tab_rq_info['INDICATEUR_CONFIG_COEF'];
		}
		mysql_free_result($res_rq_info);

        	$NB=$NB_ALL_ALL[$i]['AB_S']*$PS+$NB_ALL_ALL[$i]['AB_M']*$PM+$NB_ALL_ALL[$i]['AB_C']*$PC+$NB_ALL_ALL[$i]['MEP_S']*$PS+$NB_ALL_ALL[$i]['MEP_M']*$PM+$NB_ALL_ALL[$i]['MEP_C']*$PC;
		$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
		$NB_ALL_ALL[$i]['AB']+$NB;
		$NB_ALL_ALL[13]['AB']=$NB_ALL['ALL'];
		echo '<td align="center"><b>&nbsp;'.$NB.'&nbsp;</b></td>';	
        }
        echo '<td align="center"><b>&nbsp;'.$NB_ALL['ALL'].'&nbsp;</b></td>';
        echo ' 
      </tr>';
// Total Unit&eacute;s d'oeuvres
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Total Unit&eacute;s d\'oeuvres&nbsp</b></td>';
        $NB_ALL['ALL']=0;
        for($i=1;$i<=12;$i++){
        	$NB=$NB_ALL_ALL[$i]['MEP_U']+$NB_ALL_ALL[$i]['AB_U'];
		$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
		echo '<td align="center"><b>&nbsp;'.$NB.'&nbsp;</b></td>';	
        }
        echo '<td align="center"><b>&nbsp;'.$NB_ALL['ALL'].'&nbsp;</b></td>';
        echo ' 
      </tr>';
// Mesure de l\'activit&eacute;
      echo '
      <tr align="center" class="titre">
        <td align="center" colspan="'.$colspan.'">&nbsp;</td>
      </tr>
      <tr align="center" class="titre">
        <td align="center"><b>&nbsp;</b></td>
        <td align="center"><b>&nbsp;Janvier&nbsp;</b></td>
        <td align="center"><b>&nbsp;F&eacute;vrier&nbsp;</b></td>
        <td align="center"><b>&nbsp;Mars&nbsp;</b></td>
        <td align="center"><b>&nbsp;Avril&nbsp;</b></td>
        <td align="center"><b>&nbsp;Mai&nbsp;</b></td>
        <td align="center"><b>&nbsp;Juin&nbsp;</b></td>
        <td align="center"><b>&nbsp;Juillet&nbsp;</b></td>
        <td align="center"><b>&nbsp;Aout&nbsp;</b></td>
        <td align="center"><b>&nbsp;Septembre&nbsp;</b></td>
        <td align="center"><b>&nbsp;Octobre&nbsp;</b></td>
        <td align="center"><b>&nbsp;Novembre&nbsp;</b></td>
        <td align="center"><b>&nbsp;D&eacute;cembre&nbsp;</b></td>
        <td align="center"><b>&nbsp;'.$ANNEE.'&nbsp;</b></td>
      </tr>
      <tr align="center" class="titre">
        <td align="left" colspan="'.$colspan.'">&nbsp;Mesure de l\'activit&eacute;&nbsp;</td>
      </tr>';
// Intervention HNO
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Intervention HNO&nbsp</b></td>';
        $NB_ALL['ALL']=0;
        for($i=1;$i<=12;$i++){
        	if($i<10){
			$DATE_RECHERCHE=$ANNEE."0".$i;
		}else{
			$DATE_RECHERCHE=$ANNEE."".$i;
		}  
		$rq_info="
		SELECT * FROM `indicateur_config` WHERE 
		`INDICATEUR_CONFIG_DATE`<'".$DATE_RECHERCHE."' AND 
		`INDICATEUR_CONFIG_TYPE`='HNO' AND
		UPPER(`INDICATEUR_CONFIG_LIB`)=UPPER('Poids par tranche de 4h')
		ORDER BY `INDICATEUR_CONFIG_DATE` DESC LIMIT 1";
		$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
		$tab_rq_info = mysql_fetch_assoc($res_rq_info);
		$total_ligne_rq_info=mysql_num_rows($res_rq_info);
		if($total_ligne_rq_info==0){
			$HNO=1;
		}else{
			$HNO=$tab_rq_info['INDICATEUR_CONFIG_COEF'];
		}
		mysql_free_result($res_rq_info);
		$rq_info="
		SELECT SUM(`INDICATEUR_INFO_LIB`) AS `NB` 
		FROM `indicateur_info` 
		WHERE `DATE_INDICATEUR`='".$DATE_RECHERCHE."' AND 
		`INDICATEUR_INFO_TYPE`='HNO'";
		$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
		$tab_rq_info = mysql_fetch_assoc($res_rq_info);
		$total_ligne_rq_info=mysql_num_rows($res_rq_info);
		if($total_ligne_rq_info==0){
			$NB=1;
		}else{
			$NB=str_replace(",","",$tab_rq_info['NB']);
		}
		
		mysql_free_result($res_rq_info);
		$NB=$HNO*$NB;
		$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
		$NB_ALL_ALL[$i]['MA']=$NB_ALL_ALL[$i]['MA']+$NB;
		echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
        }
        echo '<td align="center"><b>&nbsp;'.$NB_ALL['ALL'].'&nbsp;</b></td>';
        echo ' 
      </tr>';
// MEP
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;MEP&nbsp</b></td>';
        $NB_ALL['ALL']=0;
        for($i=1;$i<=12;$i++){
        	$NB=$NB_ALL_ALL[$i]['MEP_U'];
		$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
		$NB_ALL_ALL[$i]['MA']=$NB_ALL_ALL[$i]['MA']+$NB;
		echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
        }
        echo '<td align="center"><b>&nbsp;'.$NB_ALL['ALL'].'&nbsp;</b></td>';
        echo ' 
      </tr>';
// AB
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;AB&nbsp</b></td>';
        $NB_ALL['ALL']=0;
        for($i=1;$i<=12;$i++){
        	$NB=$NB_ALL_ALL[$i]['AB_U'];
		$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
		$NB_ALL_ALL[$i]['MA']=$NB_ALL_ALL[$i]['MA']+$NB;
		echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
        }
        echo '<td align="center"><b>&nbsp;'.$NB_ALL['ALL'].'&nbsp;</b></td>';
        echo ' 
      </tr>';
// Total
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Total&nbsp</b></td>';
        $NB_ALL['ALL']=0;
        for($i=1;$i<=12;$i++){
        	$NB=$NB_ALL_ALL[$i]['MA'];
		$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
		echo '<td align="center"><b>&nbsp;'.$NB.'&nbsp;</b></td>';	
        }
        echo '<td align="center"><b>&nbsp;'.$NB_ALL['ALL'].'&nbsp;</b></td>';
        echo ' 
      </tr>';
// Livrables
      echo '
      <tr align="center" class="titre">
        <td align="center" colspan="'.$colspan.'">&nbsp;</td>
      </tr>
      <tr align="center" class="titre">
        <td align="center"><b>&nbsp;</b></td>
        <td align="center"><b>&nbsp;Janvier&nbsp;</b></td>
        <td align="center"><b>&nbsp;F&eacute;vrier&nbsp;</b></td>
        <td align="center"><b>&nbsp;Mars&nbsp;</b></td>
        <td align="center"><b>&nbsp;Avril&nbsp;</b></td>
        <td align="center"><b>&nbsp;Mai&nbsp;</b></td>
        <td align="center"><b>&nbsp;Juin&nbsp;</b></td>
        <td align="center"><b>&nbsp;Juillet&nbsp;</b></td>
        <td align="center"><b>&nbsp;Aout&nbsp;</b></td>
        <td align="center"><b>&nbsp;Septembre&nbsp;</b></td>
        <td align="center"><b>&nbsp;Octobre&nbsp;</b></td>
        <td align="center"><b>&nbsp;Novembre&nbsp;</b></td>
        <td align="center"><b>&nbsp;D&eacute;cembre&nbsp;</b></td>
        <td align="center"><b>&nbsp;'.$ANNEE.'&nbsp;</b></td>
      </tr>
      <tr align="center" class="titre">
        <td align="left" colspan="'.$colspan.'">&nbsp;Livrables&nbsp;</td>
      </tr>';
// MEP - Rapport mensuel
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;MEP - Rapport mensuel&nbsp</b></td>';
        $NB_ALL['ALL']=0;
        for($i=1;$i<=12;$i++){
        	if($i<10){
			$DATE_RECHERCHE=$ANNEE."0".$i;
		}else{
			$DATE_RECHERCHE=$ANNEE."".$i;
		}      	
		$rq_info="
		SELECT SUM(`INDICATEUR_INFO_LIB`) AS `NB` 
		FROM `indicateur_info` 
		WHERE `DATE_INDICATEUR`='".$DATE_RECHERCHE."' AND 
		`INDICATEUR_INFO_TYPE`='RAPPORT_MENSUEL'";
		$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
		$tab_rq_info = mysql_fetch_assoc($res_rq_info);
		$total_ligne_rq_info=mysql_num_rows($res_rq_info);
		if($total_ligne_rq_info==0){
			$NB=0;
		}else{
			$NB=$tab_rq_info['NB'];
			if($NB==''){$NB=0;}
		}
		mysql_free_result($res_rq_info);
		$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
		echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
        }
        echo '<td align="center"><b>&nbsp;'.$NB_ALL['ALL'].'&nbsp;</b></td>';
        echo ' 
      </tr>';
// SMQ - Saisie des performances applicatives
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;SMQ - Saisie des performances applicatives&nbsp</b></td>';
        $NB_ALL['ALL']=0;
        for($i=1;$i<=12;$i++){
        	if($i<10){
			$DATE_RECHERCHE=$ANNEE."0".$i;
		}else{
			$DATE_RECHERCHE=$ANNEE."".$i;
		}      	
		$rq_info="
		SELECT SUM(`INDICATEUR_INFO_LIB`) AS `NB` 
		FROM `indicateur_info` 
		WHERE `DATE_INDICATEUR`='".$DATE_RECHERCHE."' AND 
		`INDICATEUR_INFO_TYPE`='SAISIE_PERFORMANCES'";
		$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
		$tab_rq_info = mysql_fetch_assoc($res_rq_info);
		$total_ligne_rq_info=mysql_num_rows($res_rq_info);
		if($total_ligne_rq_info==0){
			$NB=0;
		}else{
			$NB=$tab_rq_info['NB'];
			if($NB==''){$NB=0;}
		}
		mysql_free_result($res_rq_info);
		$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
		echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
        }
        echo '<td align="center"><b>&nbsp;'.$NB_ALL['ALL'].'&nbsp;</b></td>';
        echo ' 
      </tr>';
// BSP - R&eacute;daction &agrave; 8h30
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;BSP - R&eacute;daction &agrave; 8h30&nbsp</b></td>';
        $NB_ALL['ALL']=0;
        for($i=1;$i<=12;$i++){
        	if($i<10){
			$DATE_RECHERCHE=$ANNEE."0".$i;
		}else{
			$DATE_RECHERCHE=$ANNEE."".$i;
		}      	
		$rq_info="
		SELECT SUM(`INDICATEUR_INFO_LIB`) AS `NB` 
		FROM `indicateur_info` 
		WHERE `DATE_INDICATEUR`='".$DATE_RECHERCHE."' AND 
		`INDICATEUR_INFO_TYPE`='BSP_MATIN_SOGETI'";
		$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
		$tab_rq_info = mysql_fetch_assoc($res_rq_info);
		$total_ligne_rq_info=mysql_num_rows($res_rq_info);
		if($total_ligne_rq_info==0){
			$NB=0;
		}else{
			$NB=$tab_rq_info['NB'];
			if($NB==''){$NB=0;}
		}
		mysql_free_result($res_rq_info);
		$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
		echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
        }
        echo '<td align="center"><b>&nbsp;'.$NB_ALL['ALL'].'&nbsp;</b></td>';
        echo ' 
      </tr>';
// BSP - R&eacute;daction &agrave; 14h30
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;BSP - R&eacute;daction &agrave; 14h30&nbsp</b></td>';
        $NB_ALL['ALL']=0;
        for($i=1;$i<=12;$i++){
        	if($i<10){
			$DATE_RECHERCHE=$ANNEE."0".$i;
		}else{
			$DATE_RECHERCHE=$ANNEE."".$i;
		}      	
		$rq_info="
		SELECT SUM(`INDICATEUR_INFO_LIB`) AS `NB` 
		FROM `indicateur_info` 
		WHERE `DATE_INDICATEUR`='".$DATE_RECHERCHE."' AND 
		`INDICATEUR_INFO_TYPE`='BSP_APREM_SOGETI'";
		$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
		$tab_rq_info = mysql_fetch_assoc($res_rq_info);
		$total_ligne_rq_info=mysql_num_rows($res_rq_info);
		if($total_ligne_rq_info==0){
			$NB=0;
		}else{
			$NB=$tab_rq_info['NB'];
			if($NB==''){$NB=0;}
		}
		mysql_free_result($res_rq_info);
		$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
		echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
        }
        echo '<td align="center"><b>&nbsp;'.$NB_ALL['ALL'].'&nbsp;</b></td>';
        echo ' 
      </tr>';
// Fin de la page
  echo '
      <tr align="center" class="titre">
        <td align="center" colspan="'.$colspan.'">&nbsp;</td>
      </tr>
      <tr align="center" class="titre">
        <td align="center" colspan="'.$colspan.'">&nbsp;[&nbsp;<a href="./index.php?ITEM=indicateur_menu">Retour</a>&nbsp;]&nbsp;</td>
      </tr>
      <tr align="center" class="titre">
        <td align="center" colspan="'.$colspan.'">&nbsp;</td>
      </tr>
    </table>
  </div>
  ';
mysql_close($mysql_link);
?>