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
}
if(isset($_GET['ANNEE'])){
	$ANNEE=$_GET['ANNEE'];
}else{
	$ANNEE=date("Y");
}
header("Content-Type: application/csv-tab-delimited-table"); 
header("Content-disposition: filename=indicateurs_synthese_sogeti_".$ANNEE.".csv"); 
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
	 
// Calcul de nombre de ref odti en fonction des criteres ci-dessus.
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
      
  echo 'Synthese ODTI traitees par Sogeti pour '.$ANNEE.';';
  echo "\n";
  echo ';Janvier;Fevrier;Mars;Avril;Mai;Juin;Juillet;Aout;Septembre;Octobre;Novembre;Decembre;'.$ANNEE.';';
  echo "\n";
  echo ';';
  echo "\n";
// Nb de demandes traitees en Production
      echo 'Nb de demandes traitees en Production;';
      echo "\n";
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
	echo ';';
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
        echo ';';
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
        echo ';';
        echo "\n";
// Typologie des demandes
      echo 'Typologie des demandes;';
      echo "\n";
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
		echo $NB.';';	
        }
        echo $NB_ALL['ALL'].';';
        echo "\n";
mysql_close($mysql_link);
?>