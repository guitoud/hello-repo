<?PHP
/*******************************************************************
   Interface Gestion des indicateurs
   Version 1.0.0   
  22/02/2010 - VGU - Creation fichier
**************************************************Guibert Vincent***
## la partie par mois est en cours de dev
*******************************************************************/

require_once("../cf/conf_outil_icdc.php"); 
require_once("../cf/fonctions.php");
require_once("../cf/autre_fonctions.php");
// iniialisation des variables
if(isset($_GET['action'])){
	$action=$_GET['action'];
}else{
	$action='PROD';
}
if($action=='PROD'){
	$actionENV='E';	
}
if($action=='VA'){
	$actionENV='V';	
}
if(isset($_GET['ANNEE'])){
	$ANNEE=$_GET['ANNEE'];
}else{
	$ANNEE=date("Y");
}
if(empty($_GET['MOIS'])){
	$MOIS='';
	$info_mois=0;
	$colspan=14;
	$nb_colonne=12;
header("Content-Type: application/csv-tab-delimited-table"); 
header("Content-disposition: filename=indicateurs_synthese_".$action."_".$ANNEE.".csv"); 
}else{
	$MOIS=$_GET['MOIS'];
	$info_mois=1;
	if($MOIS<10){
		$DATE_RECHERCHE=$ANNEE."0".$MOIS;
	}else{
		$DATE_RECHERCHE=$ANNEE."".$MOIS;
	}
header("Content-Type: application/csv-tab-delimited-table"); 
header("Content-disposition: filename=indicateurs_synthese_".$action."_".$ANNEE."_".$MOIS.".csv"); 
	# Liste les semaines du mois
	$rq_info_semaine="
	SELECT DISTINCT(`DATE_SEMAINE`)
	FROM `indicateur_calcul` 
	WHERE `DATE_INDICATEUR` = '".$DATE_RECHERCHE."'
	AND `DATE_MOIS` ='".$MOIS."'
	AND `DATE_ANNEE` ='".$ANNEE."'
	ORDER BY `DATE_SEMAINE`"; 
	$res_rq_info_semaine = mysql_query($rq_info_semaine, $mysql_link) or die(mysql_error());
	$tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine);
	$total_ligne_rq_info_semaine=mysql_num_rows($res_rq_info_semaine);
	$colspan=1+$total_ligne_rq_info_semaine+1;
	$nb_colonne=$total_ligne_rq_info_semaine;
}
$j=0;
$ID='';
for($i=1;$i<=13;$i++){
	$NB_ALL_ALL[$i]['ALL']=0;
	$NB_ALL_ALL[$i]['AP']=0;
	$NB_ALL_ALL[$i]['LEV']=0;
	$NB_ALL_ALL[$i]['LHVP']=0;
	$NB_ALL_ALL[$i]['LHVJ']=0;
	$NB_ALL_ALL[$i]['LHVA']=0;
}


function Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link)
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
## Calcul de nombre de ref odti en fonction des criteres ci-dessus.
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
	`DATE_INDICATEUR`='".$DATE_RECHERCHE."'";
	$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
	$tab_rq_info = mysql_fetch_assoc($res_rq_info);
	$total_ligne_rq_info=mysql_num_rows($res_rq_info);
	$NB=$tab_rq_info['NB'];
	mysql_free_result($res_rq_info);
	return $NB;
}
    
    if($info_mois==0){
    	echo 'Synthese ODTI de '.$action.' pour '.$ANNEE.';';
    	echo "\n";
    	echo ';Janvier;Fevrier;Mars;Avril;Mai;Juin;Juillet;Aout;Septembre;Octobre;Novembre;Decembre;'.$ANNEE.';';
    	echo "\n";
	}else{
	echo 'Synthese ODTI de '.$action.' pour '.$Tab_des_Mois[$MOIS-1].' '.$ANNEE.';';
	echo "\n";
      	echo ';';
        do {
        	echo $tab_rq_info_semaine['DATE_SEMAINE'].';';

        } while ($tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine));
        $ligne= mysql_num_rows($res_rq_info_semaine);
        if($ligne > 0) {
          mysql_data_seek($res_rq_info_semaine, 0);
          $tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine);
        }
        echo $Tab_des_Mois[$MOIS-1].';';
        echo "\n";
	}
//Autres (ODTI non encore validees)
      echo 'Autres (ODTI non encore validees);';
      echo "\n";
      for($i=1;$i<=13;$i++){
      	$NB_ALL[$i]=0;
      } 
      echo 'Actions Ponctuelles;';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV=$actionENV;
	$STATUS="V";
	$ACTION="E";
	$NATURE="P";
	$EN_VERSION="";
	$EN_HVP="";
	$EN_HVH="";
        if($info_mois==0){
	        for($i=1;$i<=12;$i++){
	        	if($i<10){
				$DATE_RECHERCHE=$ANNEE."0".$i;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$i;
			}        	
			$MOIS=$i;
			$SEMAINE="";
			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL_ALL[$i]['AP']=$NB_ALL_ALL[$i]['AP']+$NB;
			echo $NB.';';	
			
	        }
	        echo $NB_ALL['ALL'].';';
	}else{
		do {
	        	if($MOIS<10){
				$DATE_RECHERCHE=$ANNEE."0".$MOIS;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$MOIS;
			}        	
			$SEMAINE=$tab_rq_info_semaine['DATE_SEMAINE'];
			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL_ALL[$SEMAINE]['AP']=$NB_ALL_ALL[$SEMAINE]['AP']+$NB;
			echo $NB.';';	
	
	        } while ($tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine));
	        $ligne= mysql_num_rows($res_rq_info_semaine);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_semaine, 0);
	          $tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine);
	        }
	        $SEMAINE='';
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
	        echo $NB.';';
	}
	
        echo "\n";
// Livraison En Version
      echo 'Livraison En Version;';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV=$actionENV;
	$STATUS="V";
	$ACTION="I";
	$NATURE="V";
	$EN_VERSION="V";
	$EN_HVP="F";
	$EN_HVH="F";
	if($info_mois==0){
	        for($i=1;$i<=12;$i++){
	        	if($i<10){
				$DATE_RECHERCHE=$ANNEE."0".$i;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$i;
			}
			$MOIS=$i;
			$SEMAINE="";
	
			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL[$i]=$NB_ALL[$i]+$NB;
			$NB_ALL_ALL[$i]['LEV']=$NB_ALL_ALL[$i]['LEV']+$NB;
			echo $NB.';';	
			
	        }
	        echo $NB_ALL['ALL'].';';
	}else{
		do {
	        	if($MOIS<10){
				$DATE_RECHERCHE=$ANNEE."0".$MOIS;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$MOIS;
			}        	
			$SEMAINE=$tab_rq_info_semaine['DATE_SEMAINE'];
			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL_ALL[$SEMAINE]['LEV']=$NB_ALL_ALL[$SEMAINE]['LEV']+$NB;
			echo $NB.';';	
	
	        } while ($tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine));
	        $ligne= mysql_num_rows($res_rq_info_semaine);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_semaine, 0);
	          $tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine);
	        }
	        $SEMAINE='';
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
	        echo $NB.';';
	}
        echo "\n";
// Livraison Hors Version Planifie
      echo 'Livraison Hors Version Planifie;';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV=$actionENV;
	$STATUS="V";
	$ACTION="I";
	$NATURE="H";
	$EN_VERSION="F";
	$EN_HVP="V";
	$EN_HVH="F";
        if($info_mois==0){
	        for($i=1;$i<=12;$i++){
	        	if($i<10){
				$DATE_RECHERCHE=$ANNEE."0".$i;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$i;
			}
			$MOIS=$i;
			$SEMAINE="";

			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL[$i]=$NB_ALL[$i]+$NB;
			$NB_ALL_ALL[$i]['LHVP']=$NB_ALL_ALL[$i]['LHVP']+$NB;
			echo $NB.';';	
			
	        }
	        echo $NB_ALL['ALL'].';';
	}else{
		do {
	        	if($MOIS<10){
				$DATE_RECHERCHE=$ANNEE."0".$MOIS;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$MOIS;
			}        	
			$SEMAINE=$tab_rq_info_semaine['DATE_SEMAINE'];
			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL_ALL[$SEMAINE]['LHVP']=$NB_ALL_ALL[$SEMAINE]['LHVP']+$NB;
			echo $NB.';';	
	
	        } while ($tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine));
	        $ligne= mysql_num_rows($res_rq_info_semaine);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_semaine, 0);
	          $tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine);
	        }
	        $SEMAINE='';
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
	        echo $NB.';';
	}
        echo "\n";
// Livraison Hors Version Jeudi
      echo 'Livraison Hors Version Jeudi;';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV=$actionENV;
	$STATUS="V";
	$ACTION="I";
	$NATURE="H";
	$EN_VERSION="F";
	$EN_HVP="F";
	$EN_HVH="V";
        if($info_mois==0){
	        for($i=1;$i<=12;$i++){
	        	if($i<10){
				$DATE_RECHERCHE=$ANNEE."0".$i;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$i;
			}
			$MOIS=$i;
			$SEMAINE="";
	
			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL[$i]=$NB_ALL[$i]+$NB;
			$NB_ALL_ALL[$i]['LHVJ']=$NB_ALL_ALL[$i]['LHVJ']+$NB;
			echo $NB.';';	
			
	        }
	        echo $NB_ALL['ALL'].';';
	}else{
		do {
	        	if($MOIS<10){
				$DATE_RECHERCHE=$ANNEE."0".$MOIS;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$MOIS;
			}        	
			$SEMAINE=$tab_rq_info_semaine['DATE_SEMAINE'];
			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL_ALL[$SEMAINE]['LHVJ']=$NB_ALL_ALL[$SEMAINE]['LHVJ']+$NB;
			echo $NB.';';	
	
	        } while ($tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine));
	        $ligne= mysql_num_rows($res_rq_info_semaine);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_semaine, 0);
	          $tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine);
	        }
	        $SEMAINE='';
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
	        echo $NB.';';
	}
        echo "\n";
// Livraison Hors Version autres jours
      echo 'Livraison Hors Version autres jours;';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV=$actionENV;
	$STATUS="V";
	$ACTION="I";
	$NATURE="H";
	$EN_VERSION="F";
	$EN_HVP="F";
	$EN_HVH="F";
        if($info_mois==0){
	        for($i=1;$i<=12;$i++){
	        	if($i<10){
				$DATE_RECHERCHE=$ANNEE."0".$i;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$i;
			}
			$MOIS=$i;
			$SEMAINE="";

			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL[$i]=$NB_ALL[$i]+$NB;
			$NB_ALL_ALL[$i]['LHVA']=$NB_ALL_ALL[$i]['LHVA']+$NB;
			echo $NB.';';	
			
	        }
	        echo $NB_ALL['ALL'].';';
	}else{
		do {
	        	if($MOIS<10){
				$DATE_RECHERCHE=$ANNEE."0".$MOIS;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$MOIS;
			}        	
			$SEMAINE=$tab_rq_info_semaine['DATE_SEMAINE'];
			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL_ALL[$SEMAINE]['LHVA']=$NB_ALL_ALL[$SEMAINE]['LHVA']+$NB;
			echo $NB.';';	
	
	        } while ($tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine));
	        $ligne= mysql_num_rows($res_rq_info_semaine);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_semaine, 0);
	          $tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine);
	        }
	        $SEMAINE='';
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
	        echo $NB.';';
	}
        echo "\n";
// Autres (ODTI non encore validees) - Total
      echo 'Autres (ODTI non encore validees) - Total;';
        
        for($i=1;$i<=$nb_colonne;$i++){
        	$NB_ALL[13]=$NB_ALL[13]+$NB_ALL[$i];
        	$NB_ALL_ALL[$i]['ALL']=$NB_ALL_ALL[$i]['ALL']+$NB_ALL[$i];
        	$NB_ALL_ALL[13]['ALL']=$NB_ALL_ALL[13]['ALL']+$NB_ALL[$i];
        	echo $NB_ALL[$i].';';	
        }
        echo $NB_ALL[13].';';
        echo "\n";
//Livraisons de composants
      echo 'Livraisons de composants;';
      echo "\n";
      for($i=1;$i<=13;$i++){
      	$NB_ALL[$i]=0;
      }
// Actions Ponctuelles
      echo 'Actions Ponctuelles;';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV=$actionENV;
	$STATUS="V";
	$ACTION="L";
	$NATURE="P";
	$EN_VERSION="";
	$EN_HVP="";
	$EN_HVH="";
        if($info_mois==0){
	        for($i=1;$i<=12;$i++){
	        	if($i<10){
				$DATE_RECHERCHE=$ANNEE."0".$i;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$i;
			}
			$MOIS=$i;
			$SEMAINE="";

			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL[$i]=$NB_ALL[$i]+$NB;
			$NB_ALL_ALL[$i]['AP']=$NB_ALL_ALL[$i]['AP']+$NB;
			echo $NB.';';	
	        }
	        echo $NB_ALL['ALL'].';';
	}else{
		do {
	        	if($MOIS<10){
				$DATE_RECHERCHE=$ANNEE."0".$MOIS;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$MOIS;
			}        	
			$SEMAINE=$tab_rq_info_semaine['DATE_SEMAINE'];
			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL_ALL[$SEMAINE]['AP']=$NB_ALL_ALL[$SEMAINE]['AP']+$NB;
			echo $NB.';';	
	
	        } while ($tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine));
	        $ligne= mysql_num_rows($res_rq_info_semaine);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_semaine, 0);
	          $tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine);
	        }
	        $SEMAINE='';
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
	        echo $NB.';';
	}
        echo "\n";
// Livraison En Version (date = V)
      echo 'Livraison En Version (date = V);';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV=$actionENV;
	$STATUS="V";
	$ACTION="L";
	$NATURE="V";
	$EN_VERSION="V";
	$EN_HVP="F";
	$EN_HVH="F";
        if($info_mois==0){
	        for($i=1;$i<=12;$i++){
	        	if($i<10){
				$DATE_RECHERCHE=$ANNEE."0".$i;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$i;
			}        	
			$MOIS=$i;
			$SEMAINE="";

			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL[$i]=$NB_ALL[$i]+$NB;
			$NB_ALL_ALL[$i]['LEV']=$NB_ALL_ALL[$i]['LEV']+$NB;
			echo $NB.';';	
			
	        }
	        echo $NB_ALL['ALL'].';';
	}else{
		do {
	        	if($MOIS<10){
				$DATE_RECHERCHE=$ANNEE."0".$MOIS;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$MOIS;
			}        	
			$SEMAINE=$tab_rq_info_semaine['DATE_SEMAINE'];
			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL_ALL[$SEMAINE]['LEV']=$NB_ALL_ALL[$SEMAINE]['LEV']+$NB;
			echo $NB.';';	
	
	        } while ($tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine));
	        $ligne= mysql_num_rows($res_rq_info_semaine);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_semaine, 0);
	          $tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine);
	        }
	        $SEMAINE='';
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
	        echo $NB.';';
	}
        echo "\n";
// Livraison En Version (hors V, hors Jeudi)
      echo 'Livraison En Version (hors V, hors Jeudi);';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV=$actionENV;
	$STATUS="V";
	$ACTION="L";
	$NATURE="V";
	$EN_VERSION="F";
	$EN_HVP="F";
	$EN_HVH="F";
        if($info_mois==0){
	        for($i=1;$i<=12;$i++){
	        	if($i<10){
				$DATE_RECHERCHE=$ANNEE."0".$i;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$i;
			}        	
			$MOIS=$i;
			$SEMAINE="";

			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL[$i]=$NB_ALL[$i]+$NB;
			$NB_ALL_ALL[$i]['LEV']=$NB_ALL_ALL[$i]['LEV']+$NB;
			echo $NB.';';	
			
	        }
	        echo $NB_ALL['ALL'].';';
        }else{
		do {
	        	if($MOIS<10){
				$DATE_RECHERCHE=$ANNEE."0".$MOIS;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$MOIS;
			}        	
			$SEMAINE=$tab_rq_info_semaine['DATE_SEMAINE'];
			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL_ALL[$SEMAINE]['LEV']=$NB_ALL_ALL[$SEMAINE]['LEV']+$NB;
			echo $NB.';';	
	
	        } while ($tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine));
	        $ligne= mysql_num_rows($res_rq_info_semaine);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_semaine, 0);
	          $tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine);
	        }
	        $SEMAINE='';
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
	        echo $NB.';';
	}
        echo "\n";
// Livraison En Version (hors V, Jeudi)
      echo 'Livraison En Version (hors V, Jeudi);';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV=$actionENV;
	$STATUS="V";
	$ACTION="L";
	$NATURE="V";
	$EN_VERSION="F";
	$EN_HVP="F";
	$EN_HVH="V";
        if($info_mois==0){
	        for($i=1;$i<=12;$i++){
	        	if($i<10){
				$DATE_RECHERCHE=$ANNEE."0".$i;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$i;
			}        	
			$MOIS=$i;
			$SEMAINE="";

			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL[$i]=$NB_ALL[$i]+$NB;
			$NB_ALL_ALL[$i]['LEV']=$NB_ALL_ALL[$i]['LEV']+$NB;
			echo $NB.';';	
			
	        }
	        echo $NB_ALL['ALL'].';';
        }else{
		do {
	        	if($MOIS<10){
				$DATE_RECHERCHE=$ANNEE."0".$MOIS;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$MOIS;
			}        	
			$SEMAINE=$tab_rq_info_semaine['DATE_SEMAINE'];
			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL_ALL[$SEMAINE]['LEV']=$NB_ALL_ALL[$SEMAINE]['LEV']+$NB;
			echo $NB.';';	
	
	        } while ($tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine));
	        $ligne= mysql_num_rows($res_rq_info_semaine);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_semaine, 0);
	          $tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine);
	        }
	        $SEMAINE='';
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
	        echo $NB.';';
	}
        echo "\n";
// Livraison Hors Version Planifie
      echo 'Livraison Hors Version Planifie;';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV=$actionENV;
	$STATUS="V";
	$ACTION="L";
	$NATURE="H";
	$EN_VERSION="F";
	$EN_HVP="V";
	$EN_HVH="F";
        if($info_mois==0){
	        for($i=1;$i<=12;$i++){
	        	if($i<10){
				$DATE_RECHERCHE=$ANNEE."0".$i;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$i;
			}        	
			$MOIS=$i;
			$SEMAINE="";

			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL[$i]=$NB_ALL[$i]+$NB;
			echo $NB.';';	
			
	        }
	        echo $NB_ALL['ALL'].';';
        }else{
		do {
	        	if($MOIS<10){
				$DATE_RECHERCHE=$ANNEE."0".$MOIS;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$MOIS;
			}        	
			$SEMAINE=$tab_rq_info_semaine['DATE_SEMAINE'];
			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			//$NB_ALL_ALL[$SEMAINE]['AP']=$NB_ALL_ALL[$SEMAINE]['AP']+$NB;
			$NB_ALL[$SEMAINE]=$NB_ALL[$SEMAINE]+$NB;
			echo $NB.';';	
	
	        } while ($tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine));
	        $ligne= mysql_num_rows($res_rq_info_semaine);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_semaine, 0);
	          $tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine);
	        }
	        $SEMAINE='';
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
	        echo $NB.';';
	}
        echo "\n";
// Livraison Hors Version Jeudi
      echo 'Livraison Hors Version Jeudi;';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV=$actionENV;
	$STATUS="V";
	$ACTION="L";
	$NATURE="H";
	$EN_VERSION="F";
	$EN_HVP="F";
	$EN_HVH="V";
        if($info_mois==0){
	        for($i=1;$i<=12;$i++){
	        	if($i<10){
				$DATE_RECHERCHE=$ANNEE."0".$i;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$i;
			}        	
			$MOIS=$i;
			$SEMAINE="";

			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL[$i]=$NB_ALL[$i]+$NB;
			$NB_ALL_ALL[$i]['LHVJ']=$NB_ALL_ALL[$i]['LHVJ']+$NB;
			echo $NB.';';	
			
	        }
	        echo $NB_ALL['ALL'].';';
        }else{
		do {
	        	if($MOIS<10){
				$DATE_RECHERCHE=$ANNEE."0".$MOIS;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$MOIS;
			}        	
			$SEMAINE=$tab_rq_info_semaine['DATE_SEMAINE'];
			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL_ALL[$SEMAINE]['LHVJ']=$NB_ALL_ALL[$SEMAINE]['LHVJ']+$NB;
			echo $NB.';';	
	
	        } while ($tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine));
	        $ligne= mysql_num_rows($res_rq_info_semaine);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_semaine, 0);
	          $tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine);
	        }
	        $SEMAINE='';
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
	        echo $NB.';';
	}
        echo "\n";
// Livraison Hors Version autres jours
      echo 'Livraison Hors Version autres jours;';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV=$actionENV;
	$STATUS="V";
	$ACTION="L";
	$NATURE="H";
	$EN_VERSION="F";
	$EN_HVP="F";
	$EN_HVH="F";
        if($info_mois==0){
	        for($i=1;$i<=12;$i++){
	        	if($i<10){
				$DATE_RECHERCHE=$ANNEE."0".$i;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$i;
			}        	
			$MOIS=$i;
			$SEMAINE="";

			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL[$i]=$NB_ALL[$i]+$NB;
			$NB_ALL_ALL[$i]['LHVA']=$NB_ALL_ALL[$i]['LHVA']+$NB;
			echo $NB.';';	
			
	        }
	        echo $NB_ALL['ALL'].';';
        }else{
		do {
	        	if($MOIS<10){
				$DATE_RECHERCHE=$ANNEE."0".$MOIS;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$MOIS;
			}        	
			$SEMAINE=$tab_rq_info_semaine['DATE_SEMAINE'];
			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL_ALL[$SEMAINE]['LHVA']=$NB_ALL_ALL[$SEMAINE]['LHVA']+$NB;
			echo $NB.';';	
	
	        } while ($tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine));
	        $ligne= mysql_num_rows($res_rq_info_semaine);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_semaine, 0);
	          $tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine);
	        }
	        $SEMAINE='';
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
	        echo $NB.';';
	}
        echo "\n";
// Livraisons de composants - Total
      echo 'Livraisons de composants - Total;';
        for($i=1;$i<=$nb_colonne;$i++){
        	$NB_ALL[13]=$NB_ALL[13]+$NB_ALL[$i];
        	$NB_ALL_ALL[$i]['ALL']=$NB_ALL_ALL[$i]['ALL']+$NB_ALL[$i];
        	$NB_ALL_ALL[13]['ALL']=$NB_ALL_ALL[13]['ALL']+$NB_ALL[$i];
        	echo $NB_ALL[$i].';';	
        }
        echo $NB_ALL[13].';';	
        echo "\n";
//traitement exceptionnels
      echo 'traitements exceptionnels;';
      echo "\n";
      for($i=1;$i<=13;$i++){
      	$NB_ALL[$i]=0;
      }
// Actions Ponctuelles
      echo 'Actions Ponctuelles;';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV=$actionENV;
	$STATUS="V";
	$ACTION="T";
	$NATURE="P";
	$EN_VERSION="";
	$EN_HVP="";
	$EN_HVH="";
        if($info_mois==0){
	        for($i=1;$i<=12;$i++){
	        	if($i<10){
				$DATE_RECHERCHE=$ANNEE."0".$i;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$i;
			}        	
			$MOIS=$i;
			$SEMAINE="";

			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL[$i]=$NB_ALL[$i]+$NB;
			$NB_ALL_ALL[$i]['AP']=$NB_ALL_ALL[$i]['AP']+$NB;
			echo $NB.';';	
			
	        }
	        echo $NB_ALL['ALL'].';';
        }else{
		do {
	        	if($MOIS<10){
				$DATE_RECHERCHE=$ANNEE."0".$MOIS;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$MOIS;
			}        	
			$SEMAINE=$tab_rq_info_semaine['DATE_SEMAINE'];
			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL_ALL[$SEMAINE]['AP']=$NB_ALL_ALL[$SEMAINE]['AP']+$NB;
			echo $NB.';';	
	
	        } while ($tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine));
	        $ligne= mysql_num_rows($res_rq_info_semaine);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_semaine, 0);
	          $tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine);
	        }
	        $SEMAINE='';
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
	        echo $NB.';';
	}
        echo "\n";
// Livraison En Version
      echo 'Livraison En Version;';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV=$actionENV;
	$STATUS="V";
	$ACTION="T";
	$NATURE="V";
	$EN_VERSION="V";
	$EN_HVP="F";
	$EN_HVH="F";
        if($info_mois==0){
	        for($i=1;$i<=12;$i++){
	        	if($i<10){
				$DATE_RECHERCHE=$ANNEE."0".$i;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$i;
			}        	
			$MOIS=$i;
			$SEMAINE="";

			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL[$i]=$NB_ALL[$i]+$NB;
			$NB_ALL_ALL[$i]['LEV']=$NB_ALL_ALL[$i]['LEV']+$NB;
			echo $NB.';';	
			
	        }
	        echo $NB_ALL['ALL'].';';
        }else{
		do {
	        	if($MOIS<10){
				$DATE_RECHERCHE=$ANNEE."0".$MOIS;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$MOIS;
			}        	
			$SEMAINE=$tab_rq_info_semaine['DATE_SEMAINE'];
			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL_ALL[$SEMAINE]['LEV']=$NB_ALL_ALL[$SEMAINE]['LEV']+$NB;
			echo $NB.';';	
	
	        } while ($tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine));
	        $ligne= mysql_num_rows($res_rq_info_semaine);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_semaine, 0);
	          $tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine);
	        }
	        $SEMAINE='';
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
	        echo $NB.';';
	}
        echo "\n";
// Livraison Hors Version Planifie
      echo 'Livraison Hors Version Planifie;';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV=$actionENV;
	$STATUS="V";
	$ACTION="T";
	$NATURE="H";
	$EN_VERSION="F";
	$EN_HVP="V";
	$EN_HVH="F";
        if($info_mois==0){
	        for($i=1;$i<=12;$i++){
	        	if($i<10){
				$DATE_RECHERCHE=$ANNEE."0".$i;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$i;
			}        	
			$MOIS=$i;
			$SEMAINE="";

			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL[$i]=$NB_ALL[$i]+$NB;
			$NB_ALL_ALL[$i]['LHVP']=$NB_ALL_ALL[$i]['LHVP']+$NB;
			echo $NB.';';	
			
	        }
	        echo $NB_ALL['ALL'].';';
        }else{
		do {
	        	if($MOIS<10){
				$DATE_RECHERCHE=$ANNEE."0".$MOIS;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$MOIS;
			}        	
			$SEMAINE=$tab_rq_info_semaine['DATE_SEMAINE'];
			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL_ALL[$SEMAINE]['LHVP']=$NB_ALL_ALL[$SEMAINE]['LHVP']+$NB;
			echo $NB.';';	
	
	        } while ($tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine));
	        $ligne= mysql_num_rows($res_rq_info_semaine);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_semaine, 0);
	          $tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine);
	        }
	        $SEMAINE='';
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
	        echo $NB.';';
	}
        echo "\n";
// Livraison Hors Version Jeudi
      echo 'Livraison Hors Version Jeudi;';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV=$actionENV;
	$STATUS="V";
	$ACTION="T";
	$NATURE="H";
	$EN_VERSION="F";
	$EN_HVP="F";
	$EN_HVH="V";
        if($info_mois==0){
	         for($i=1;$i<=12;$i++){
	        	if($i<10){
				$DATE_RECHERCHE=$ANNEE."0".$i;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$i;
			}        	
			$MOIS=$i;
			$SEMAINE="";

			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL[$i]=$NB_ALL[$i]+$NB;
			$NB_ALL_ALL[$i]['LHVJ']=$NB_ALL_ALL[$i]['LHVJ']+$NB;
			echo $NB.';';	
			
	        }
	        echo $NB_ALL['ALL'].';';
        }else{
		do {
	        	if($MOIS<10){
				$DATE_RECHERCHE=$ANNEE."0".$MOIS;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$MOIS;
			}        	
			$SEMAINE=$tab_rq_info_semaine['DATE_SEMAINE'];
			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL_ALL[$SEMAINE]['LHVJ']=$NB_ALL_ALL[$SEMAINE]['LHVJ']+$NB;
			echo $NB.';';	
	
	        } while ($tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine));
	        $ligne= mysql_num_rows($res_rq_info_semaine);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_semaine, 0);
	          $tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine);
	        }
	        $SEMAINE='';
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
	        echo $NB.';';
	}
        echo "\n";
// Livraison Hors Version autres jours
      echo 'Livraison Hors Version autres jours;';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV=$actionENV;
	$STATUS="V";
	$ACTION="T";
	$NATURE="H";
	$EN_VERSION="F";
	$EN_HVP="F";
	$EN_HVH="F";
        if($info_mois==0){
		for($i=1;$i<=12;$i++){
			if($i<10){
				$DATE_RECHERCHE=$ANNEE."0".$i;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$i;
			}        	
			$MOIS=$i;
			$SEMAINE="";

			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL[$i]=$NB_ALL[$i]+$NB;
			$NB_ALL_ALL[$i]['LHVA']=$NB_ALL_ALL[$i]['LHVA']+$NB;
			echo $NB.';';	
			
		}
		echo $NB_ALL['ALL'].';';
	}else{
		do {
	        	if($MOIS<10){
				$DATE_RECHERCHE=$ANNEE."0".$MOIS;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$MOIS;
			}        	
			$SEMAINE=$tab_rq_info_semaine['DATE_SEMAINE'];
			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL_ALL[$SEMAINE]['LHVA']=$NB_ALL_ALL[$SEMAINE]['LHVA']+$NB;
			echo $NB.';';	
	
	        } while ($tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine));
	        $ligne= mysql_num_rows($res_rq_info_semaine);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_semaine, 0);
	          $tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine);
	        }
	        $SEMAINE='';
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
	        echo $NB.';';
	}
        echo "\n";
// traitements exceptionnels - Total
      echo 'traitements exceptionnels - Total;';
        for($i=1;$i<=$nb_colonne;$i++){
        	$NB_ALL[13]=$NB_ALL[13]+$NB_ALL[$i];
        	$NB_ALL_ALL[$i]['ALL']=$NB_ALL_ALL[$i]['ALL']+$NB_ALL[$i];
        	$NB_ALL_ALL[13]['ALL']=$NB_ALL_ALL[13]['ALL']+$NB_ALL[$i];
        	echo $NB_ALL[$i].';';	
        }
        echo $NB_ALL[13].';';
        echo "\n";
//Modification IAB (Action Ponctuelle)
      echo 'Modification IAB (Action Ponctuelle);';
      echo "\n";
      for($i=1;$i<=13;$i++){
      	$NB_ALL[$i]=0;
      }
// Actions Ponctuelles
      echo 'Actions Ponctuelles;';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV=$actionENV;
	$STATUS="V";
	$ACTION="M";
	$NATURE="P";
	$EN_VERSION="";
	$EN_HVP="";
	$EN_HVH="";
        if($info_mois==0){
	        for($i=1;$i<=12;$i++){
	        	if($i<10){
				$DATE_RECHERCHE=$ANNEE."0".$i;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$i;
			}        	
			$MOIS=$i;
			$SEMAINE="";

			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL[$i]=$NB_ALL[$i]+$NB;
			$NB_ALL_ALL[$i]['AP']=$NB_ALL_ALL[$i]['AP']+$NB;
			echo $NB.';';	
			
	        }
	        echo $NB_ALL['ALL'].';';
        }else{
		do {
	        	if($MOIS<10){
				$DATE_RECHERCHE=$ANNEE."0".$MOIS;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$MOIS;
			}        	
			$SEMAINE=$tab_rq_info_semaine['DATE_SEMAINE'];
			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL_ALL[$SEMAINE]['AP']=$NB_ALL_ALL[$SEMAINE]['AP']+$NB;
			echo $NB.';';	
	
	        } while ($tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine));
	        $ligne= mysql_num_rows($res_rq_info_semaine);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_semaine, 0);
	          $tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine);
	        }
	        $SEMAINE='';
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
	        echo $NB.';';
	}
        echo "\n";
// Livraison En Version
      echo 'Livraison En Version;';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV=$actionENV;
	$STATUS="V";
	$ACTION="M";
	$NATURE="V";
	$EN_VERSION="V";
	$EN_HVP="F";
	$EN_HVH="F";
        if($info_mois==0){
	        for($i=1;$i<=12;$i++){
	        	if($i<10){
				$DATE_RECHERCHE=$ANNEE."0".$i;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$i;
			}        	
			$MOIS=$i;
			$SEMAINE="";

			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL[$i]=$NB_ALL[$i]+$NB;
			$NB_ALL_ALL[$i]['LEV']=$NB_ALL_ALL[$i]['LEV']+$NB;
			echo $NB.';';	
			
	        }
	        echo $NB_ALL['ALL'].';';
	}else{
		do {
	        	if($MOIS<10){
				$DATE_RECHERCHE=$ANNEE."0".$MOIS;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$MOIS;
			}        	
			$SEMAINE=$tab_rq_info_semaine['DATE_SEMAINE'];
			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL_ALL[$SEMAINE]['LEV']=$NB_ALL_ALL[$SEMAINE]['LEV']+$NB;
			echo $NB.';';	
	
	        } while ($tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine));
	        $ligne= mysql_num_rows($res_rq_info_semaine);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_semaine, 0);
	          $tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine);
	        }
	        $SEMAINE='';
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
	        echo $NB.';';
	}
        echo "\n";
// Livraison Hors Version Planifie
      echo 'Livraison Hors Version Planifie;';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV=$actionENV;
	$STATUS="V";
	$ACTION="M";
	$NATURE="H";
	$EN_VERSION="F";
	$EN_HVP="V";
	$EN_HVH="F";
         if($info_mois==0){
	         for($i=1;$i<=12;$i++){
	        	if($i<10){
				$DATE_RECHERCHE=$ANNEE."0".$i;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$i;
			}        	
			$MOIS=$i;
			$SEMAINE="";

			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL[$i]=$NB_ALL[$i]+$NB;
			$NB_ALL_ALL[$i]['LHVP']=$NB_ALL_ALL[$i]['LHVP']+$NB;
			echo $NB.';';	
			
	        }
	        echo $NB_ALL['ALL'].';';
        }else{
		do {
	        	if($MOIS<10){
				$DATE_RECHERCHE=$ANNEE."0".$MOIS;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$MOIS;
			}        	
			$SEMAINE=$tab_rq_info_semaine['DATE_SEMAINE'];
			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL_ALL[$SEMAINE]['LHVP']=$NB_ALL_ALL[$SEMAINE]['LHVP']+$NB;
			echo $NB.';';	
	
	        } while ($tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine));
	        $ligne= mysql_num_rows($res_rq_info_semaine);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_semaine, 0);
	          $tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine);
	        }
	        $SEMAINE='';
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
	        echo $NB.';';
	}
        echo "\n";
// Livraison Hors Version Jeudi
      echo 'Livraison Hors Version Jeudi;';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV=$actionENV;
	$STATUS="V";
	$ACTION="M";
	$NATURE="H";
	$EN_VERSION="F";
	$EN_HVP="F";
	$EN_HVH="V";
        if($info_mois==0){
	        for($i=1;$i<=12;$i++){
	        	if($i<10){
				$DATE_RECHERCHE=$ANNEE."0".$i;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$i;
			}        	
			$MOIS=$i;
			$SEMAINE="";

			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL[$i]=$NB_ALL[$i]+$NB;
			$NB_ALL_ALL[$i]['LHVJ']=$NB_ALL_ALL[$i]['LHVJ']+$NB;
			echo $NB.';';	
			
	        }
	        echo $NB_ALL['ALL'].';';
	}else{
		do {
	        	if($MOIS<10){
				$DATE_RECHERCHE=$ANNEE."0".$MOIS;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$MOIS;
			}        	
			$SEMAINE=$tab_rq_info_semaine['DATE_SEMAINE'];
			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL_ALL[$SEMAINE]['LHVJ']=$NB_ALL_ALL[$SEMAINE]['LHVJ']+$NB;
			echo $NB.';';	
	
	        } while ($tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine));
	        $ligne= mysql_num_rows($res_rq_info_semaine);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_semaine, 0);
	          $tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine);
	        }
	        $SEMAINE='';
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
	        echo $NB.';';
	}
        echo "\n";
// Livraison Hors Version autres jours
      echo 'Livraison Hors Version autres jours;';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV=$actionENV;
	$STATUS="V";
	$ACTION="M";
	$NATURE="H";
	$EN_VERSION="F";
	$EN_HVP="F";
	$EN_HVH="F";
        if($info_mois==0){
	        for($i=1;$i<=12;$i++){
	        	if($i<10){
				$DATE_RECHERCHE=$ANNEE."0".$i;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$i;
			}        	
			$MOIS=$i;
			$SEMAINE="";

			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL[$i]=$NB_ALL[$i]+$NB;
			$NB_ALL_ALL[$i]['LHVA']=$NB_ALL_ALL[$i]['LHVA']+$NB;
			echo $NB.';';	
			
	        }
	        echo $NB_ALL['ALL'].';';
	}else{
		do {
	        	if($MOIS<10){
				$DATE_RECHERCHE=$ANNEE."0".$MOIS;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$MOIS;
			}        	
			$SEMAINE=$tab_rq_info_semaine['DATE_SEMAINE'];
			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL_ALL[$SEMAINE]['LHVA']=$NB_ALL_ALL[$SEMAINE]['LHVA']+$NB;
			echo $NB.';';	
	
	        } while ($tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine));
	        $ligne= mysql_num_rows($res_rq_info_semaine);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_semaine, 0);
	          $tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine);
	        }
	        $SEMAINE='';
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
	        echo $NB.';';
	}
        echo "\n";
// Modification IAB (Action Ponctuelle) - Total
      echo 'Modification IAB (Action Ponctuelle) - Total;';
        for($i=1;$i<=$nb_colonne;$i++){
        	$NB_ALL[13]=$NB_ALL[13]+$NB_ALL[$i];
        	$NB_ALL_ALL[$i]['ALL']=$NB_ALL_ALL[$i]['ALL']+$NB_ALL[$i];
        	$NB_ALL_ALL[13]['ALL']=$NB_ALL_ALL[13]['ALL']+$NB_ALL[$i];
        	echo $NB_ALL[$i].';';	
        }
        echo $NB_ALL[13].';';
        echo "\n";
//Demandes MOA (Action Ponctuelle)
      echo 'Demandes MOA (Action Ponctuelle);';
      echo "\n";
      for($i=1;$i<=13;$i++){
      	$NB_ALL[$i]=0;
      }
// Actions Ponctuelles
      echo 'Actions Ponctuelles;';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV=$actionENV;
	$STATUS="V";
	$ACTION="D";
	$NATURE="P";
	$EN_VERSION="";
	$EN_HVP="";
	$EN_HVH="";
        if($info_mois==0){
	        for($i=1;$i<=12;$i++){
	        	if($i<10){
				$DATE_RECHERCHE=$ANNEE."0".$i;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$i;
			}        	
			$MOIS=$i;
			$SEMAINE="";

			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL[$i]=$NB_ALL[$i]+$NB;
			$NB_ALL_ALL[$i]['AP']=$NB_ALL_ALL[$i]['AP']+$NB;
			echo $NB.';';	
			
	        }
	        echo $NB_ALL['ALL'].';';
        }else{
		do {
	        	if($MOIS<10){
				$DATE_RECHERCHE=$ANNEE."0".$MOIS;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$MOIS;
			}        	
			$SEMAINE=$tab_rq_info_semaine['DATE_SEMAINE'];
			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL_ALL[$SEMAINE]['AP']=$NB_ALL_ALL[$SEMAINE]['AP']+$NB;
			echo $NB.';';	
	
	        } while ($tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine));
	        $ligne= mysql_num_rows($res_rq_info_semaine);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_semaine, 0);
	          $tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine);
	        }
	        $SEMAINE='';
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
	        echo $NB.';';
	}
        echo "\n";
// Livraison En Version
      echo 'Livraison En Version;';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV=$actionENV;
	$STATUS="V";
	$ACTION="D";
	$NATURE="V";
	$EN_VERSION="V";
	$EN_HVP="F";
	$EN_HVH="F";
        if($info_mois==0){
	        for($i=1;$i<=12;$i++){
	        	if($i<10){
				$DATE_RECHERCHE=$ANNEE."0".$i;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$i;
			}        	
			$MOIS=$i;
			$SEMAINE="";

			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL[$i]=$NB_ALL[$i]+$NB;
			$NB_ALL_ALL[$i]['LEV']=$NB_ALL_ALL[$i]['LEV']+$NB;
			echo $NB.';';	
			
	        }
	        echo $NB_ALL['ALL'].';';
        }else{
		do {
	        	if($MOIS<10){
				$DATE_RECHERCHE=$ANNEE."0".$MOIS;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$MOIS;
			}        	
			$SEMAINE=$tab_rq_info_semaine['DATE_SEMAINE'];
			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL_ALL[$SEMAINE]['LEV']=$NB_ALL_ALL[$SEMAINE]['LEV']+$NB;
			echo $NB.';';	
	
	        } while ($tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine));
	        $ligne= mysql_num_rows($res_rq_info_semaine);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_semaine, 0);
	          $tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine);
	        }
	        $SEMAINE='';
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
	        echo $NB.';';
	}
        echo "\n";
// Livraison Hors Version Planifie
      echo 'Livraison Hors Version Planifie;';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV=$actionENV;
	$STATUS="V";
	$ACTION="D";
	$NATURE="H";
	$EN_VERSION="F";
	$EN_HVP="F";
	$EN_HVH="V";
        if($info_mois==0){
	        for($i=1;$i<=12;$i++){
	        	if($i<10){
				$DATE_RECHERCHE=$ANNEE."0".$i;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$i;
			}        	
			$MOIS=$i;
			$SEMAINE="";

			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL[$i]=$NB_ALL[$i]+$NB;
			$NB_ALL_ALL[$i]['LHVP']=$NB_ALL_ALL[$i]['LHVP']+$NB;
			echo $NB.';';	
			
	        }
	        echo $NB_ALL['ALL'].';';
        }else{
		do {
	        	if($MOIS<10){
				$DATE_RECHERCHE=$ANNEE."0".$MOIS;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$MOIS;
			}        	
			$SEMAINE=$tab_rq_info_semaine['DATE_SEMAINE'];
			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL_ALL[$SEMAINE]['LHVP']=$NB_ALL_ALL[$SEMAINE]['LHVP']+$NB;
			echo $NB.';';	
	
	        } while ($tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine));
	        $ligne= mysql_num_rows($res_rq_info_semaine);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_semaine, 0);
	          $tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine);
	        }
	        $SEMAINE='';
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
	        echo $NB.';';
	}
        echo "\n";
// Livraison Hors Version Jeudi
      echo 'Livraison Hors Version Jeudi;';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV=$actionENV;
	$STATUS="V";
	$ACTION="D";
	$NATURE="H";
	$EN_VERSION="F";
	$EN_HVP="F";
	$EN_HVH="V";
        if($info_mois==0){
	        for($i=1;$i<=12;$i++){
	        	if($i<10){
				$DATE_RECHERCHE=$ANNEE."0".$i;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$i;
			}        	
			$MOIS=$i;
			$SEMAINE="";

			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL[$i]=$NB_ALL[$i]+$NB;
			$NB_ALL_ALL[$i]['LHVJ']=$NB_ALL_ALL[$i]['LHVJ']+$NB;
			echo $NB.';';	
			
	        }
	        echo $NB_ALL['ALL'].';';
        }else{
		do {
	        	if($MOIS<10){
				$DATE_RECHERCHE=$ANNEE."0".$MOIS;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$MOIS;
			}        	
			$SEMAINE=$tab_rq_info_semaine['DATE_SEMAINE'];
			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL_ALL[$SEMAINE]['LHVJ']=$NB_ALL_ALL[$SEMAINE]['LHVJ']+$NB;
			echo $NB.';';	
	
	        } while ($tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine));
	        $ligne= mysql_num_rows($res_rq_info_semaine);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_semaine, 0);
	          $tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine);
	        }
	        $SEMAINE='';
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
	        echo $NB.';';
	}
        echo "\n";
// Livraison Hors Version autres jours
      echo 'Livraison Hors Version autres jours;';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV=$actionENV;
	$STATUS="V";
	$ACTION="D";
	$NATURE="H";
	$EN_VERSION="F";
	$EN_HVP="F";
	$EN_HVH="F";
        if($info_mois==0){
	        for($i=1;$i<=12;$i++){
	        	if($i<10){
				$DATE_RECHERCHE=$ANNEE."0".$i;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$i;
			}        	
			$MOIS=$i;
			$SEMAINE="";

			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL[$i]=$NB_ALL[$i]+$NB;
			$NB_ALL_ALL[$i]['LHVA']=$NB_ALL_ALL[$i]['LHVA']+$NB;
			echo $NB.';';	
			
	        }
	        echo $NB_ALL['ALL'].';';
        }else{
		do {
	        	if($MOIS<10){
				$DATE_RECHERCHE=$ANNEE."0".$MOIS;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$MOIS;
			}        	
			$SEMAINE=$tab_rq_info_semaine['DATE_SEMAINE'];
			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL_ALL[$SEMAINE]['LHVA']=$NB_ALL_ALL[$SEMAINE]['LHVA']+$NB;
			echo $NB.';';	
	
	        } while ($tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine));
	        $ligne= mysql_num_rows($res_rq_info_semaine);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_semaine, 0);
	          $tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine);
	        }
	        $SEMAINE='';
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
	        echo $NB.';';
	}
        echo "\n";
// Demandes MOA (Action Ponctuelle) - Total
      echo 'Demandes MOA (Action Ponctuelle) - Total;';
        for($i=1;$i<=$nb_colonne;$i++){
        	$NB_ALL[13]=$NB_ALL[13]+$NB_ALL[$i];
        	$NB_ALL_ALL[$i]['ALL']=$NB_ALL_ALL[$i]['ALL']+$NB_ALL[$i];
        	$NB_ALL_ALL[13]['ALL']=$NB_ALL_ALL[13]['ALL']+$NB_ALL[$i];
        	echo $NB_ALL[$i].';';	
        }
        echo $NB_ALL[13].';';
        echo "\n";
//Totaux
      echo 'Totaux;';
      echo "\n";
// Actions Ponctuelles
      echo 'Actions Ponctuelles;';
        for($i=1;$i<=$nb_colonne;$i++){
        	$NB_ALL_ALL[13]['AP']=$NB_ALL_ALL[13]['AP']+$NB_ALL_ALL[$i]['AP'];
        	echo $NB_ALL_ALL[$i]['AP'].';';	
        }
        echo $NB_ALL_ALL[13]['AP'].';';
        echo "\n";
// Livraison En Version
      echo 'Livraison En Version;';
        for($i=1;$i<=$nb_colonne;$i++){
        	$NB_ALL_ALL[13]['LEV']=$NB_ALL_ALL[13]['LEV']+$NB_ALL_ALL[$i]['LEV'];
        	echo $NB_ALL_ALL[$i]['LEV'].';';	
        }
        echo $NB_ALL_ALL[13]['LEV'].';';
        echo "\n";
// Livraison Hors Version Planifie
      echo 'Livraison Hors Version Planifie;';
        for($i=1;$i<=$nb_colonne;$i++){
        	$NB_ALL_ALL[13]['LHVP']=$NB_ALL_ALL[13]['LHVP']+$NB_ALL_ALL[$i]['LHVP'];
        	echo $NB_ALL_ALL[$i]['LHVP'].';';	
        }
        echo $NB_ALL_ALL[13]['LHVP'].';';
        echo "\n";
// Livraison Hors Version Jeudi
      echo 'Livraison Hors Version Jeudi;';
        for($i=1;$i<=$nb_colonne;$i++){
        	$NB_ALL_ALL[13]['LHVJ']=$NB_ALL_ALL[13]['LHVJ']+$NB_ALL_ALL[$i]['LHVJ'];
        	echo $NB_ALL_ALL[$i]['LHVJ'].';';	
        }
        echo $NB_ALL_ALL[13]['LHVJ'].';';
        echo "\n";
// Livraison Hors Version autres jours
      echo 'Livraison Hors Version autres jours;';
        for($i=1;$i<=$nb_colonne;$i++){
        	$NB_ALL_ALL[13]['LHVA']=$NB_ALL_ALL[13]['LHVA']+$NB_ALL_ALL[$i]['LHVA'];
        	echo $NB_ALL_ALL[$i]['LHVA'].';';	
        }
        echo $NB_ALL_ALL[13]['LHVA'].';';
        echo "\n";
// Total
      echo 'Total;';
        for($i=1;$i<=$nb_colonne;$i++){
        	echo $NB_ALL_ALL[$i]['ALL'].';';	
        }
        echo $NB_ALL_ALL[13]['ALL'].';';
        echo "\n";

mysql_close($mysql_link);
?>