<?PHP
/*******************************************************************
   Interface Gestion des indicateurs
   Version 1.0.0   
  22/02/2010 - VGU - Creation fichier
**************************************************Guibert Vincent***
*******************************************************************/

require_once("../cf/conf_outil_icdc.php"); 
require_once("../cf/fonctions.php");
require_once("../cf/autre_fonctions.php");

// initialisation des variables 
if(isset($_GET['ANNEE'])){
	$ANNEE=$_GET['ANNEE'];
}else{
	$ANNEE=date("Y");
}
header("Content-Type: application/csv-tab-delimited-table"); 
header("Content-disposition: filename=indicateurs_sogeti_".$ANNEE.".csv"); 
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
echo 'Contexte pour '.$ANNEE.';';
echo "\n";
echo ';Janvier;Fevrier;Mars;Avril;Mai;Juin;Juillet;Aout;Septembre;Octobre;Novembre;Decembre;'.$ANNEE.';';
echo "\n";

// Jours ouvres

      echo 'Jours ouvres;';
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
		echo $NB.';';	
        }
        echo $NB_ALL['ALL'].';';
        echo "\n";
// Effectif - Suivi de production IAB

      echo 'Effectif - Suivi de production IAB;';
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
		echo $NB.';';	
        }
        echo $NB_ALL['ALL'].';';
        echo "\n";
// Effectif - Transverse IAB
      echo 'Effectif - Transverse IAB;';
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
		echo $NB.';';	
        }
        echo $NB_ALL['ALL'].';';
        echo "\n";
// Intervention HNO (nb tranches de 4h)
      echo 'Intervention HNO (nb tranches de 4h);';
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
		echo $NB.';';	
        }
        echo $NB_ALL['ALL'].';';
        echo "\n";
// Nombre d'indidents BSP
      echo 'Nombre d\'indidents BSP;';
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
		echo $NB.';';	
        }
        echo $NB_ALL['ALL'].';';
        echo "\n";
        echo ';';
	echo "\n";
// Demandes ODTI
      echo 'Demandes ODTI pour '.$ANNEE.';';
      echo "\n";
      echo ';Janvier;Fevrier;Mars;Avril;Mai;Juin;Juillet;Aout;Septembre;Octobre;Novembre;Decembre;'.$ANNEE.';';
      echo "\n";
      echo 'Nb de demandes traitees en Production;';
      echo "\n";
// reinitialisation des variables
      for($i=1;$i<=13;$i++){
      	$NB_ALL[$i]=0;
      }
// Autres (OTDI non encore validees)
      echo 'Autres (OTDI non encore validees);';
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
		echo $NB.';';
        }
        echo $NB_ALL['ALL'].';';
        echo "\n";
// Livraison de composants
      echo 'Livraison de composants;';
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
		echo $NB.';';
        }
        echo $NB_ALL['ALL'].';';
        echo "\n";
// traitements exceptionnels
      echo 'traitements exceptionnels;';
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
		echo $NB.';';
        }
        echo $NB_ALL['ALL'].';';
        echo "\n";
// Modifications IAB (Action Ponstuelle)
      echo 'Modifications IAB (Action Ponstuelle);';
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
		echo $NB.';';
        }
        echo $NB_ALL['ALL'].';';
        echo "\n";
// Demandes MOA (Action Ponctuelle)
      echo 'Demandes MOA (Action Ponctuelle);';
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
		echo $NB.';';
        }
        echo $NB_ALL['ALL'].';';
        echo "\n";
// Totaux
      echo 'Totaux;';
        $NB_ALL['ALL']=0;
        for($i=1;$i<=12;$i++){

		$NB=$NB_ALL_ALL[$i]['AE']+$NB_ALL_ALL[$i]['LE']+$NB_ALL_ALL[$i]['TE']+$NB_ALL_ALL[$i]['ME']+$NB_ALL_ALL[$i]['DE'];
		$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
		echo $NB.';';
        }
        echo $NB_ALL['ALL'].';';
        echo "\n";
// Nb de demandes traitees en VA
      echo 'Nb de demandes traitees en VA;';
      echo "\n";
      for($i=1;$i<=13;$i++){
      	$NB_ALL[$i]=0;
      }
// Autres (OTDI non encore validees)
      echo 'Autres (OTDI non encore validees);';
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
		echo $NB.';';
        }
        echo $NB_ALL['ALL'].';';
        echo "\n";
// Livraison de composants

      echo 'Livraison de composants;';
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
		echo $NB.';';
        }
        echo $NB_ALL['ALL'].';';
        echo "\n";
// traitements exceptionnels
	echo 'traitements exceptionnels;';
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
		echo $NB.';';
        }
        echo $NB_ALL['ALL'].';';
        echo "\n";
// Modifications IAB (Action Ponstuelle)
	echo 'Modifications IAB (Action Ponstuelle);';
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
		echo $NB.';';
        }
        echo $NB_ALL['ALL'].';';
        echo "\n";
// Demandes MOA (Action Ponctuelle)
	echo 'Demandes MOA (Action Ponctuelle);';
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
		echo $NB.';';
        }
        echo $NB_ALL['ALL'].';';
        echo "\n";
// Totaux
	echo 'Totaux;';
        $NB_ALL['ALL']=0;
        for($i=1;$i<=12;$i++){

		$NB=$NB_ALL_ALL[$i]['AV']+$NB_ALL_ALL[$i]['LV']+$NB_ALL_ALL[$i]['TV']+$NB_ALL_ALL[$i]['MV']+$NB_ALL_ALL[$i]['DV'];
		$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
		echo $NB.';';
        }
        echo $NB_ALL['ALL'].';';
        echo "\n";
// Typologie des demandes
      echo 'Typologie des demandes;';
      echo "\n";
// Production Simple
	echo 'Production Simple;';
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
		echo $NB.';';
        }
        echo $NB_ALL['ALL'].';';
        echo "\n";
// VA Simple
	echo 'VA Simple;';
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
		echo $NB.';';
        }
        echo $NB_ALL['ALL'].';';
        echo "\n";
// Production Moyenne
	echo 'Production Moyenne;';
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
		echo $NB.';';
        }
        echo $NB_ALL['ALL'].';';
        echo "\n";
// VA Moyenne
	echo 'VA Moyenne;';
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
		echo $NB.';';
        }
        echo $NB_ALL['ALL'].';';
        echo "\n";
// Production Complexe
	echo 'Production Complexe;';
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
		echo $NB.';';
        }
        echo $NB_ALL['ALL'].';';
        echo "\n"; 
// VA Complexe
	echo 'VA Complexe;';
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
		echo $NB.';';
        }
        echo $NB_ALL['ALL'].';';
        echo "\n";
// Typologie des demandes
      echo 'Typologie des demandes;';
      echo "\n"; ;
// MEP Simple
	echo 'MEP Simple;';
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
		echo $NB.';';
        }
        echo $NB_ALL['ALL'].';';
        echo "\n";
// MEP Moyenne
	echo 'MEP Moyenne;';
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
		echo $NB.';';
        }
        echo $NB_ALL['ALL'].';';
        echo "\n";
// MEP Complexe
	echo 'MEP Complexe;';
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
		echo $NB.';';
        }
        echo $NB_ALL['ALL'].';';
        echo "\n";
// AB Simple
	echo 'AB Simple;';
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
		echo $NB.';';
        }
        echo $NB_ALL['ALL'].';';
        echo "\n";
// AB Moyenne
	echo 'AB Moyenne;';
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
		echo $NB.';';
        }
        echo $NB_ALL['ALL'].';';
        echo "\n"; 
// AB Complexe
	echo 'AB Complexe;';
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
		echo $NB.';';
        }
        echo $NB_ALL['ALL'].';';
        echo "\n";
// Typologie des demandes
      echo 'Typologie des demandes;';
      echo "\n"; 
// MEP Poids
	echo 'MEP Poids;';
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
		echo $NB.';';	
        }
        echo $NB_ALL['ALL'].';';
        echo "\n";
// MEP Unites d'oeuvres
	echo 'MEP Unites d\'oeuvres;';
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
		echo $NB.';';	
        }
        echo $NB_ALL['ALL'].';';
        echo "\n";
// AB Poids
	echo 'AB Poids;';
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
		echo $NB.';';	
        }
        echo $NB_ALL['ALL'].';';
        echo "\n";
// AB Unites d'oeuvres
	echo 'AB Unites d\'oeuvres;';
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
		echo $NB.';';	
        }
        echo $NB_ALL['ALL'].';';
        echo "\n";
// Total Poids
	echo 'Total Poids;';
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
		echo $NB.';';	
        }
        echo $NB_ALL['ALL'].';';
        echo "\n";
// Total Unites d'oeuvres
	echo 'Total Unites d\'oeuvres;';
        $NB_ALL['ALL']=0;
        for($i=1;$i<=12;$i++){
        	$NB=$NB_ALL_ALL[$i]['MEP_U']+$NB_ALL_ALL[$i]['AB_U'];
		$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
		echo $NB.';';	
        }
        echo $NB_ALL['ALL'].';';
        echo "\n";
// Mesure de l\'activite
	echo ';';
	echo "\n";
      echo ';Janvier;Fevrier;Mars;Avril;Mai;Juin;Juillet;Aout;Septembre;Octobre;Novembre;Decembre;'.$ANNEE.';';
      echo "\n"; 
      echo'Mesure de l\'activite;';
      echo "\n"; 
// Intervention HNO
	echo 'Intervention HNO;';
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
		echo $NB.';';	
        }
        echo $NB_ALL['ALL'].';';
        echo "\n";
// MEP
	echo 'MEP;';
        $NB_ALL['ALL']=0;
        for($i=1;$i<=12;$i++){
        	$NB=$NB_ALL_ALL[$i]['MEP_U'];
		$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
		$NB_ALL_ALL[$i]['MA']=$NB_ALL_ALL[$i]['MA']+$NB;
		echo $NB.';';	
        }
        echo $NB_ALL['ALL'].';';
        echo "\n";
// AB
	echo 'AB;';
        $NB_ALL['ALL']=0;
        for($i=1;$i<=12;$i++){
        	$NB=$NB_ALL_ALL[$i]['AB_U'];
		$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
		$NB_ALL_ALL[$i]['MA']=$NB_ALL_ALL[$i]['MA']+$NB;
		echo $NB.';';	
        }
        echo $NB_ALL['ALL'].';';
        echo "\n";
// Total
	echo 'Total;';
        $NB_ALL['ALL']=0;
        for($i=1;$i<=12;$i++){
        	$NB=$NB_ALL_ALL[$i]['MA'];
		$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
		echo $NB.';';	
        }
        echo $NB_ALL['ALL'].';';
        echo "\n";
// Livrables
	echo ';';
	echo "\n";
      echo ';Janvier;Fevrier;Mars;Avril;Mai;Juin;Juillet;Aout;Septembre;Octobre;Novembre;Decembre;'.$ANNEE.';';
      echo "\n"; 
      echo 'Livrables;';
      echo "\n"; 
// MEP - Rapport mensuel
	echo 'MEP - Rapport mensuel;';
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
		echo $NB.';';	
        }
        echo $NB_ALL['ALL'].';';
        echo "\n";
// SMQ - Saisie des performances applicatives
	echo 'SMQ - Saisie des performances applicatives;';
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
		echo $NB.';';	
        }
        echo $NB_ALL['ALL'].';';
        echo "\n";
// BSP - Redaction a 8h30
	echo 'BSP - Redaction a 8h30;';
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
		echo $NB.';';	
        }
        echo $NB_ALL['ALL'].';';
        echo "\n";
// BSP - Redaction a 14h30
	echo 'BSP - Redaction a 14h30;';
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
		echo $NB.';';	
        }
        echo $NB_ALL['ALL'].';';
        echo "\n";
// Fin de la page

mysql_close($mysql_link);
?>