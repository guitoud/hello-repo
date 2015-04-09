<?PHP
/*******************************************************************
   Interface Gestion des indicateurs
   Version 1.0.0   
  22/02/2010 - VGU - Creation fichier
**************************************************Guibert Vincent***
*******************************************************************/
require_once("../cf/conf_outil_icdc.php"); 
require_once("../cf/fonctions.php");
if(isset($_GET['action'])){
	$action=$_GET['action'];
}else{
	$action='PROD';
}
if(isset($_GET['ANNEE'])){
	$ANNEE=$_GET['ANNEE'];
}else{
	$ANNEE=date("Y");
}
header("Content-Type: application/csv-tab-delimited-table"); 
header("Content-disposition: filename=indicateurs_Tableau_de_Bord_".$action."_".$ANNEE.".csv"); 
$j=0;
$ID='';
function Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$action,$type,$mysql_link)
{
	$ENV='I';
	$INFO_SQL="";
	if($action=='PROD'){
		$ENV='E';	
	}
	if($action=='VA'){
		$ENV='V';	
	}
	if($type==1){$INFO_SQL="";}
	if($type==2){$INFO_SQL="`ACTION`='L' AND";}
	if($type==3){$INFO_SQL="`ACTION`='T' AND";}
	if($type==4){$INFO_SQL="`ACTION`='M' AND";}
	if($type==5){$INFO_SQL="`ACTION`='D' AND";}
	if($type==6){$INFO_SQL="`ACTION`='I' AND";}
	$rq_info="
	SELECT COUNT(`REF`) AS `NB` 
	FROM `indicateur_calcul` 
	WHERE
	`STATUS`='V' AND
	`DATE_ANNEE`='".$ANNEE."' AND
	`DATE_MOIS`=".$MOIS." AND
	`ENV`='".$ENV."' AND
	".$INFO_SQL."
	`DATE_INDICATEUR`='".$DATE_RECHERCHE."'";
	$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
	$tab_rq_info = mysql_fetch_assoc($res_rq_info);
	$total_ligne_rq_info=mysql_num_rows($res_rq_info);
	$NB=$tab_rq_info['NB'];
	mysql_free_result($res_rq_info);
	return $NB;
}
function Return_DUREE($DATE_RECHERCHE,$ANNEE,$MOIS,$action,$type,$mysql_link)
{
	$ENV='I';
	$INFO_SQL="";
	if($action=='PROD'){
		$ENV='E';	
	}
	if($action=='VA'){
		$ENV='V';	
	}
	if($type==1){$INFO_SQL="";}
	if($type==2){$INFO_SQL="`ACTION`='L' AND";}
	if($type==3){$INFO_SQL="`ACTION`='T' AND";}
	if($type==4){$INFO_SQL="`ACTION`='M' AND";}
	if($type==5){$INFO_SQL="`ACTION`='D' AND";}
	if($type==6){$INFO_SQL="`ACTION`='I' AND";}
	$rq_info="
	SELECT SUM(`DUREE`) AS `DUREE` 
	FROM `indicateur_calcul` 
	WHERE
	`STATUS`='V' AND
	`DATE_ANNEE`='".$ANNEE."' AND
	`DATE_MOIS`=".$MOIS." AND
	`ENV`='".$ENV."' AND
	".$INFO_SQL."
	`DATE_INDICATEUR`='".$DATE_RECHERCHE."'";
	$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
	$tab_rq_info = mysql_fetch_assoc($res_rq_info);
	$total_ligne_rq_info=mysql_num_rows($res_rq_info);
	$DUREE=$tab_rq_info['DUREE'];
	if($DUREE==''){$DUREE=0;}
	mysql_free_result($res_rq_info);
	return $DUREE;
}
## Calcul des indicateurs
for($i=1;$i<=12;$i++){
  if($i<10){
	$DATE_RECHERCHE=$ANNEE."0".$i;
	$NB_INTER[$i][1]=Return_NB($DATE_RECHERCHE,$ANNEE,$i,$action,1,$mysql_link);
	$TEMPS_INTER[$i][1]=Return_DUREE($DATE_RECHERCHE,$ANNEE,$i,$action,1,$mysql_link);
	$NB_INTER[$i][2]=Return_NB($DATE_RECHERCHE,$ANNEE,$i,$action,2,$mysql_link);
	$TEMPS_INTER[$i][2]=Return_DUREE($DATE_RECHERCHE,$ANNEE,$i,$action,2,$mysql_link);
	$NB_INTER[$i][3]=Return_NB($DATE_RECHERCHE,$ANNEE,$i,$action,3,$mysql_link);
	$TEMPS_INTER[$i][3]=Return_DUREE($DATE_RECHERCHE,$ANNEE,$i,$action,3,$mysql_link);
	$NB_INTER[$i][4]=Return_NB($DATE_RECHERCHE,$ANNEE,$i,$action,4,$mysql_link);
	$TEMPS_INTER[$i][4]=Return_DUREE($DATE_RECHERCHE,$ANNEE,$i,$action,4,$mysql_link);
	$NB_INTER[$i][5]=Return_NB($DATE_RECHERCHE,$ANNEE,$i,$action,5,$mysql_link);
	$TEMPS_INTER[$i][5]=Return_DUREE($DATE_RECHERCHE,$ANNEE,$i,$action,5,$mysql_link);
	$NB_INTER[$i][6]=Return_NB($DATE_RECHERCHE,$ANNEE,$i,$action,6,$mysql_link);
	$TEMPS_INTER[$i][6]=Return_DUREE($DATE_RECHERCHE,$ANNEE,$i,$action,6,$mysql_link);
  }else{
    	$DATE_RECHERCHE=$ANNEE."".$i;  
	$NB_INTER[$i][1]=Return_NB($DATE_RECHERCHE,$ANNEE,$i,$action,1,$mysql_link);
	$TEMPS_INTER[$i][1]=Return_DUREE($DATE_RECHERCHE,$ANNEE,$i,$action,1,$mysql_link);
	$NB_INTER[$i][2]=Return_NB($DATE_RECHERCHE,$ANNEE,$i,$action,2,$mysql_link);
	$TEMPS_INTER[$i][2]=Return_DUREE($DATE_RECHERCHE,$ANNEE,$i,$action,2,$mysql_link);
	$NB_INTER[$i][3]=Return_NB($DATE_RECHERCHE,$ANNEE,$i,$action,3,$mysql_link);
	$TEMPS_INTER[$i][3]=Return_DUREE($DATE_RECHERCHE,$ANNEE,$i,$action,3,$mysql_link);
	$NB_INTER[$i][4]=Return_NB($DATE_RECHERCHE,$ANNEE,$i,$action,4,$mysql_link);
	$TEMPS_INTER[$i][4]=Return_DUREE($DATE_RECHERCHE,$ANNEE,$i,$action,4,$mysql_link);
	$NB_INTER[$i][5]=Return_NB($DATE_RECHERCHE,$ANNEE,$i,$action,5,$mysql_link);
	$TEMPS_INTER[$i][5]=Return_DUREE($DATE_RECHERCHE,$ANNEE,$i,$action,5,$mysql_link);
	$NB_INTER[$i][6]=Return_NB($DATE_RECHERCHE,$ANNEE,$i,$action,6,$mysql_link);
	$TEMPS_INTER[$i][6]=Return_DUREE($DATE_RECHERCHE,$ANNEE,$i,$action,6,$mysql_link);
    }            
}
$NB_INTER[13][1]=0;
$TEMPS_INTER[13][1]=0;
$NB_INTER[13][2]=0;
$TEMPS_INTER[13][2]=0;
$NB_INTER[13][3]=0;
$TEMPS_INTER[13][3]=0;
$NB_INTER[13][4]=0;
$TEMPS_INTER[13][4]=0;
$NB_INTER[13][5]=0;
$TEMPS_INTER[13][5]=0;
$NB_INTER[13][6]=0;
$TEMPS_INTER[13][6]=0;
for($i=1;$i<=12;$i++){
	$NB_INTER[13][1]=$NB_INTER[13][1]+$NB_INTER[$i][1];
	$TEMPS_INTER[13][1]=$TEMPS_INTER[13][1]+$TEMPS_INTER[$i][1];
	$NB_INTER[13][2]=$NB_INTER[13][2]+$NB_INTER[$i][2];
	$TEMPS_INTER[13][2]=$TEMPS_INTER[13][2]+$TEMPS_INTER[$i][2];
	$NB_INTER[13][3]=$NB_INTER[13][3]+$NB_INTER[$i][3];
	$TEMPS_INTER[13][3]=$TEMPS_INTER[13][3]+$TEMPS_INTER[$i][3];
	$NB_INTER[13][4]=$NB_INTER[13][4]+$NB_INTER[$i][4];
	$TEMPS_INTER[13][4]=$TEMPS_INTER[13][4]+$TEMPS_INTER[$i][4];
	$NB_INTER[13][5]=$NB_INTER[13][5]+$NB_INTER[$i][5];
	$TEMPS_INTER[13][5]=$TEMPS_INTER[13][5]+$TEMPS_INTER[$i][5];
	$NB_INTER[13][6]=$NB_INTER[13][6]+$NB_INTER[$i][6];
	$TEMPS_INTER[13][6]=$TEMPS_INTER[13][6]+$TEMPS_INTER[$i][6];
}



  echo 'Tableau de Bord ODTI de '.$action.' pour '.$ANNEE.';';
  echo "\n";
  echo ';Janvier;Fevrier;Mars;Avril;Mai;Juin;Juillet;Aout;Septembre;Octobre;Novembre;Decembre;'.$ANNEE.';';
  echo "\n";

################### Nombre d'interventions IAB 
      echo 'Nombre d\'interventions IAB;';
        for($i=1;$i<=13;$i++){
        	echo $NB_INTER[$i][1].';';
        }
        echo "\n";

      echo 'Livraisons de composants;';
        for($i=1;$i<=13;$i++){
        	echo $NB_INTER[$i][2].';';	
        }
        echo "\n";

      echo 'traitements exceptionnels;';
        for($i=1;$i<=13;$i++){
        	echo $NB_INTER[$i][3].';';	
        }
        echo "\n";

      echo 'Modifications IAB;';
        for($i=1;$i<=13;$i++){
        	echo $NB_INTER[$i][4].';';	
        }
        echo "\n";

      echo 'Demandes MOA;';
        for($i=1;$i<=13;$i++){
        	echo $NB_INTER[$i][5].';';	
        }
        echo "\n";

      echo 'Demandes non qualifiees;';
        for($i=1;$i<=13;$i++){
        	echo $NB_INTER[$i][6].';';	
        }
        echo "\n";
        echo ';';
        echo "\n";

################### Duree des interventions IAB (minutes)
      echo 'Duree des interventions IAB (minutes);';
        for($i=1;$i<=13;$i++){
        	echo $TEMPS_INTER[$i][1].';';
        }
        echo "\n";

      echo 'Livraisons de composants;';
        for($i=1;$i<=13;$i++){
        	echo $TEMPS_INTER[$i][2].';';	
        }
        echo "\n";

      echo 'traitements exceptionnels;';
        for($i=1;$i<=13;$i++){
        	echo $TEMPS_INTER[$i][3].';';	
        }
        echo "\n";

      echo 'Modifications IAB;';
        for($i=1;$i<=13;$i++){
        	echo $TEMPS_INTER[$i][4].';';	
        }
        echo "\n";

      echo 'Demandes MOA;';
        for($i=1;$i<=13;$i++){
        	echo $TEMPS_INTER[$i][5].';';	
        }
        echo "\n";

      echo 'Demandes non qualifiees;';
        for($i=1;$i<=13;$i++){
        	echo $TEMPS_INTER[$i][6].';';	
        }
        echo "\n";
        echo ";";
        echo "\n";

################### Duree des interventions IAB (heures)
      echo 'Duree des interventions IAB (heures);';
        for($i=1;$i<=13;$i++){
        	$TEMPS=ceil($TEMPS_INTER[$i][1]/60); 
        	echo $TEMPS.';';
        }
        echo "\n";

      echo 'Livraisons de composants;';
        for($i=1;$i<=13;$i++){
        	$TEMPS=ceil($TEMPS_INTER[$i][2]/60); 
        	echo $TEMPS.';';	
        }
        echo "\n";

      echo 'traitements exceptionnels;';
        for($i=1;$i<=13;$i++){
        	$TEMPS=ceil($TEMPS_INTER[$i][3]/60); 
        	echo $TEMPS.';';		
        }
        echo "\n";

      echo 'Modifications IAB;';
        for($i=1;$i<=13;$i++){
        	$TEMPS=ceil($TEMPS_INTER[$i][4]/60); 
        	echo $TEMPS.';';	
        }
        echo "\n";

      echo 'Demandes MOA;';
        for($i=1;$i<=13;$i++){
        	$TEMPS=ceil($TEMPS_INTER[$i][5]/60); 
        	echo $TEMPS.';';	
        }
        echo "\n";

      echo 'Demandes non qualifiees;';
        for($i=1;$i<=13;$i++){
        	$TEMPS=ceil($TEMPS_INTER[$i][6]/60); 
        	echo $TEMPS.';';		
        }
        echo "\n";
        echo ';';
        echo "\n";

################### Duree des interventions IAB (jours)
      echo 'Duree des interventions IAB (jours);';
        for($i=1;$i<=13;$i++){
        	$TEMPS=ceil($TEMPS_INTER[$i][1]/60/8); 
        	echo $TEMPS.';';
        }
        echo "\n";
################### Livraisons de composants

      echo 'Livraisons de composants;';
        for($i=1;$i<=13;$i++){
        	$TEMPS=ceil($TEMPS_INTER[$i][2]/60/8); 
        	echo $TEMPS.';';		
        }
        echo "\n";
################### traitements exceptionnels

      echo 'traitements exceptionnels;';
        for($i=1;$i<=13;$i++){
        	$TEMPS=ceil($TEMPS_INTER[$i][3]/60/8); 
        	echo $TEMPS.';';		
        }
        echo "\n";
################### Modifications IAB

      echo 'Modifications IAB;';
        for($i=1;$i<=13;$i++){
        	$TEMPS=ceil($TEMPS_INTER[$i][4]/60/8); 
        	echo $TEMPS.';';	
        }
        echo "\n";
################### Demandes MOA

      echo 'Demandes MOA;';
        for($i=1;$i<=13;$i++){
        	$TEMPS=ceil($TEMPS_INTER[$i][5]/60/8); 
        	echo $TEMPS.';';		
        }
        echo "\n";
################### Demandes non qualifiees

      echo 'Demandes non qualifiees;';
        for($i=1;$i<=13;$i++){
        	$TEMPS=ceil($TEMPS_INTER[$i][6]/60/8); 
        	echo $TEMPS.';';	
        }
        echo "\n";
mysql_close($mysql_link);
?>