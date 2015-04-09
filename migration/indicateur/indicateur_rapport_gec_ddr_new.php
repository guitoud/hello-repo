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

if(isset($_GET['ANNEE'])){
	$ANNEE=$_GET['ANNEE'];
}else{
	$ANNEE=date("Y");
}
if(isset($_GET['MOIS'])){
	$MOIS=$_GET['MOIS'];
	if($MOIS<10){
		if(strlen($MOIS)==2){
			$MOIS=substr($MOIS,1,1);
		}
	}
}else{
	$MOIS=date("m");
}
$j=0;
$ID='';
if($MOIS<10){
	$DATE_RECHERCHE_BSP=$ANNEE."-0".$MOIS;
	$DATE_RECHERCHE=$ANNEE."0".$MOIS;
}else{
	$DATE_RECHERCHE_BSP=$ANNEE."-".$MOIS;
	$DATE_RECHERCHE=$ANNEE."".$MOIS;
}

## Patrimoine

$rq_info_patri="
SELECT COUNT(DISTINCT(`DATE_INDICATEUR`)) AS `NB` 
FROM `indicateur_comptages` 
WHERE `DATE_INDICATEUR` <= '".$DATE_RECHERCHE."' 
AND `TYPE` IN ('comptages_compilables','comptages_scripts')";
$res_rq_info_patri = mysql_query($rq_info_patri, $mysql_link) or die(mysql_error());
$tab_rq_info_patri = mysql_fetch_assoc($res_rq_info_patri);
$total_ligne_rq_info_patri=mysql_num_rows($res_rq_info_patri);
$NB=$tab_rq_info_patri['NB'];
mysql_free_result($res_rq_info_patri);
$LIMIT="";
$LIMIT_MIN="LIMIT 1";
if($NB > 12 ){
	$NB = $NB - 12 ;
	$LIMIT= "LIMIT ".$NB." , 12 ";
	$LIMIT_MIN= "LIMIT ".$NB." , 12 ";
}

$rq_info_patri_date="
SELECT DISTINCT(`DATE_INDICATEUR`)
FROM `indicateur_comptages` 
WHERE `DATE_INDICATEUR` <= '".$DATE_RECHERCHE."' 
AND `TYPE` IN ('comptages_compilables','comptages_scripts')
ORDER BY `DATE_INDICATEUR` ASC 
".$LIMIT."";
$res_rq_info_patri_date = mysql_query($rq_info_patri_date, $mysql_link) or die(mysql_error());
$tab_rq_info_patri_date = mysql_fetch_assoc($res_rq_info_patri_date);
$total_ligne_rq_info_patri_date=mysql_num_rows($res_rq_info_patri_date); 

## ODTI

$rq_info="
SELECT COUNT(DISTINCT(`DATE_INDICATEUR`)) AS `NB`
FROM `indicateur_calcul`
WHERE `DATE_INDICATEUR` <= '".$DATE_RECHERCHE."' ";
$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
$tab_rq_info = mysql_fetch_assoc($res_rq_info);
$total_ligne_rq_info=mysql_num_rows($res_rq_info);
$NB=$tab_rq_info['NB'];
mysql_free_result($res_rq_info);
$LIMIT="";
$LIMIT_MIN="LIMIT 1";
if($NB > 12 ){
	$NB = $NB - 12 ;
	$LIMIT= "LIMIT ".$NB." , 12 ";
	$LIMIT_MIN= "LIMIT ".$NB." , 12 ";
}

$rq_info_date="
SELECT DISTINCT (`DATE_INDICATEUR`)
FROM `indicateur_calcul` 
WHERE `DATE_INDICATEUR` <= '".$DATE_RECHERCHE."' 
ORDER BY `DATE_INDICATEUR` ASC 
".$LIMIT."";
$res_rq_info_date = mysql_query($rq_info_date, $mysql_link) or die(mysql_error());
$tab_rq_info_date = mysql_fetch_assoc($res_rq_info_date);
$total_ligne_rq_info_date=mysql_num_rows($res_rq_info_date); 

$rq_info_nature_ODTI_NON="
SELECT DISTINCT(`indicateur_regles`.`INDICATEUR_REGLE_INFO`) ,`indicateur_regles`.`INDICATEUR_REGLE_ID` 
FROM `indicateur_calcul`,`indicateur_regles`
WHERE `indicateur_calcul`.`NATURE`='P' AND
`indicateur_calcul`.`INDICATEUR_REGLE_ID`=`indicateur_regles`.`INDICATEUR_REGLE_ID`
ORDER BY `indicateur_regles`.`INDICATEUR_REGLE_INFO` ";
$res_rq_info_nature_ODTI_NON = mysql_query($rq_info_nature_ODTI_NON, $mysql_link) or die(mysql_error());
$tab_rq_info_nature_ODTI_NON = mysql_fetch_assoc($res_rq_info_nature_ODTI_NON);
$total_ligne_rq_info_nature_ODTI_NON=mysql_num_rows($res_rq_info_nature_ODTI_NON); 

## QC9

$rq_qc9_info="
SELECT COUNT(DISTINCT(`DATE_INDICATEUR`)) AS `NB`
FROM `indicateur_qc9_calcul`
WHERE `DATE_INDICATEUR` <= '".$DATE_RECHERCHE."' ";
$res_rq_qc9_info = mysql_query($rq_qc9_info, $mysql_link) or die(mysql_error());
$tab_rq_qc9_info = mysql_fetch_assoc($res_rq_qc9_info);
$total_ligne_rq_qc9_info=mysql_num_rows($res_rq_qc9_info);
$NB=$tab_rq_qc9_info['NB'];
mysql_free_result($res_rq_qc9_info);
$LIMIT="";
$LIMIT_MIN="LIMIT 1";
if($NB > 12 ){
	$NB = $NB - 12 ;
	$LIMIT= "LIMIT ".$NB." , 12 ";
	$LIMIT_MIN= "LIMIT ".$NB." , 12 ";
}

$rq_qc9_info_date="
SELECT DISTINCT (`DATE_INDICATEUR`)
FROM `indicateur_qc9_calcul` 
WHERE `DATE_INDICATEUR` <= '".$DATE_RECHERCHE."' 
ORDER BY `DATE_INDICATEUR` ASC 
".$LIMIT."";
$res_rq_qc9_info_date = mysql_query($rq_qc9_info_date, $mysql_link) or die(mysql_error());
$tab_rq_qc9_info_date = mysql_fetch_assoc($res_rq_qc9_info_date);
$total_ligne_rq_qc9_info_date=mysql_num_rows($res_rq_qc9_info_date); 

$rq_info_nature_qc9="
SELECT DISTINCT (`NATURE`)
FROM `indicateur_qc9_calcul` 
ORDER BY `NATURE` ASC ";
$res_rq_info_nature_qc9 = mysql_query($rq_info_nature_qc9, $mysql_link) or die(mysql_error());
$tab_rq_info_nature_qc9 = mysql_fetch_assoc($res_rq_info_nature_qc9);
$total_ligne_rq_info_nature_qc9=mysql_num_rows($res_rq_info_nature_qc9); 

##BSP
$rq_bsp_info="
SELECT COUNT(DISTINCT(LEFT( `date_incident` , 7 ))) AS `NB`
FROM `suivi_incidents` 
WHERE LEFT( `date_incident` , 7 ) <= '".$DATE_RECHERCHE_BSP."' 
AND `au_bsp`='Oui'";
$res_rq_bsp_info = mysql_query($rq_bsp_info, $mysql_link) or die(mysql_error());
$tab_rq_bsp_info = mysql_fetch_assoc($res_rq_bsp_info);
$total_ligne_rq_bsp_info=mysql_num_rows($res_rq_bsp_info);
$NB=$tab_rq_bsp_info['NB'];
mysql_free_result($res_rq_bsp_info);
$LIMIT="";
$LIMIT_MIN="LIMIT 1";
if($NB > 12 ){
	$NB = $NB - 12 ;
	$LIMIT= "LIMIT ".$NB." , 12 ";
	$LIMIT_MIN= "LIMIT ".$NB." , 12 ";
}

$rq_bsp_info_date="
SELECT DISTINCT (LEFT( `date_incident` , 7 )) AS `DATE_INDICATEUR`
FROM `suivi_incidents` 
WHERE LEFT( `date_incident` , 7 ) <= '".$DATE_RECHERCHE_BSP."' 
AND `au_bsp`='Oui'
ORDER BY `DATE_INDICATEUR` ASC 
".$LIMIT."";
$res_rq_bsp_info_date = mysql_query($rq_bsp_info_date, $mysql_link) or die(mysql_error());
$tab_rq_bsp_info_date = mysql_fetch_assoc($res_rq_bsp_info_date);
$total_ligne_rq_bsp_info_date=mysql_num_rows($res_rq_bsp_info_date); 

## fonction
function Return_NB_url($ANNEE,$MOIS,$mysql_link)
{
	if($MOIS<10){
		$DATE_RECHERCHE_SQL=$ANNEE."0".$MOIS;
	}else{
		$DATE_RECHERCHE_SQL=$ANNEE."".$MOIS;
	}
## Calcul de nombre de ref odti en fonction d'une date d indicateur
	$rq_info="
	SELECT COUNT(`REF`) AS `NB` 
	FROM `indicateur_calcul` 
	WHERE
	`DATE_INDICATEUR`='".$DATE_RECHERCHE_SQL."'
	AND `DATE_INDICATEUR`>200900";
	$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
	$tab_rq_info = mysql_fetch_assoc($res_rq_info);
	$total_ligne_rq_info=mysql_num_rows($res_rq_info);
	$NB=$tab_rq_info['NB'];
	mysql_free_result($res_rq_info);
	return $NB;
}



function Return_PATRI_NB($DATE_RECHERCHE,$TYPE,$mysql_link)
{
	if($TYPE==''){
		$TYPE_SQL="";	
	}else{
		if($TYPE==1){
			$TYPE='';
			$TYPE_SQL="AND `TYPE` IN ('comptages_compilables','comptages_scripts')";
		}
		if($TYPE==2){
			$TYPE='comptages_compilables';
			$TYPE_SQL="AND `TYPE`='".$TYPE."'";
		}
		if($TYPE==3){
			$TYPE='comptages_scripts';
			$TYPE_SQL="AND `TYPE`='".$TYPE."'";
		}
		
	}
	$rq_info_app="
	SELECT SUM(`VALEUR`) AS `NB` 
	FROM `indicateur_comptages` 
	WHERE `DATE_INDICATEUR` ='".$DATE_RECHERCHE."'
	".$TYPE_SQL."
	";
	$res_rq_info_app = mysql_query($rq_info_app, $mysql_link) or die(mysql_error());
	$tab_rq_info_app = mysql_fetch_assoc($res_rq_info_app);
	$total_ligne_rq_info_app=mysql_num_rows($res_rq_info_app);
	$NB=$tab_rq_info_app['NB'];
	//echo $rq_info_app.'<BR>';
	mysql_free_result($res_rq_info_app);
	return $NB;
}

function Return_BSP_NB($DATE_RECHERCHE_BSP,$NATURE,$mysql_link)
{
	if($NATURE==''){
		$NATURE_SQL="";	
	}else{
		if($NATURE==1){$NATURE='Applicatif';}
		if($NATURE==2){$NATURE='Donnees';}
		if($NATURE==3){$NATURE='Indetermine';}
		if($NATURE==4){$NATURE='Technique';}
		$NATURE_SQL="AND `nature`='".$NATURE."'";
	}
	$rq_info_app="
	SELECT COUNT(`id_incident`) AS `NB` 
	FROM `suivi_incidents` 
	WHERE `date_incident` LIKE '".$DATE_RECHERCHE_BSP."%' 
	AND `au_bsp`='Oui'
	AND `nature`!=''
	".$NATURE_SQL."
	";
	$res_rq_info_app = mysql_query($rq_info_app, $mysql_link) or die(mysql_error());
	$tab_rq_info_app = mysql_fetch_assoc($res_rq_info_app);
	$total_ligne_rq_info_app=mysql_num_rows($res_rq_info_app);
	$NB=$tab_rq_info_app['NB'];
	//echo $rq_info_app.'<BR>';
	mysql_free_result($res_rq_info_app);
	return $NB;
}

function Return_QC9_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$ENVIRONNEMENT,$APPLICATION,$SOGETI,$NATURE_QC9,$mysql_link)
{
	if($APPLICATION==''){
		$APPLICATION_SQL="";	
	}else{
		$APPLICATION_SQL="`APPLICATION`='".$APPLICATION."' AND";
	}
	if($NATURE_QC9==''){
		$NATURE_SQL="";	
	}else{
		$NATURE_SQL="`NATURE`='".$NATURE_QC9."' AND";
	}
	if($SOGETI==''){
		$SOGETI_SQL="";	
	}else{
		if($SOGETI=='Y' || $SOGETI=='N'){
			$SOGETI_SQL="`SOGETI`='".$SOGETI."' AND";
		}else{
			$SOGETI_SQL="";	
		}
	}
	if($ENVIRONNEMENT==''){
		$ENVIRONNEMENT_SQL="";	
	}else{
		$ENVIRONNEMENT_SQL="`ENVIRONNEMENT`='".$ENVIRONNEMENT."' AND";	
	}
	$DATE_PREVUE=$DATE_RECHERCHE;
	$rq_info_app="
	SELECT COUNT(`ID`) AS `NB` 
	FROM `indicateur_qc9_calcul` 
	WHERE 
	`STATUS`='6- Termin&eacute;e' AND
	`DATE_PREVUE` LIKE '".$DATE_PREVUE."%' AND
	".$ENVIRONNEMENT_SQL."
	".$APPLICATION_SQL."
	".$NATURE_SQL."
	".$SOGETI_SQL."
	`DATE_INDICATEUR`='".$DATE_RECHERCHE."'";
	$res_rq_info_app = mysql_query($rq_info_app, $mysql_link) or die(mysql_error());
	$tab_rq_info_app = mysql_fetch_assoc($res_rq_info_app);
	$total_ligne_rq_info_app=mysql_num_rows($res_rq_info_app);
	$NB=$tab_rq_info_app['NB'];
	//echo $rq_info_app.'<BR>';
	mysql_free_result($res_rq_info_app);
	return $NB;
}

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

function Return_NB_NON_COR($DATE_RECHERCHE,$ANNEE,$MOIS,$APPLICATION,$INDICATEUR_REGLE_ID,$action,$mysql_link)
{
	if($INDICATEUR_REGLE_ID==''){
		$INDICATEUR_REGLE_ID_SQL="";	
	}else{
		$INDICATEUR_REGLE_ID_SQL="`INDICATEUR_REGLE_ID`='".$INDICATEUR_REGLE_ID."' AND";
	}
	if($APPLICATION==''){
		$APPLICATION_SQL="";	
	}else{
		$APPLICATION_SQL="`APPLI`='".$APPLICATION."' AND";
	}
	if($action==''){
		$ENV_SQL="";	
	}else{
		if($action=='PROD'){
			$ENV='E';	
		}
		if($action=='VA'){
			$ENV='V';	
		}
		$ENV_SQL="`ENV`='".$ENV."' AND";
	}
	
	
	$rq_info_app="
	SELECT COUNT(`REF`) AS `NB` 
	FROM `indicateur_calcul` 
	WHERE
	`DATE_ANNEE`='".$ANNEE."' AND
	`DATE_MOIS`=".$MOIS." AND
	".$APPLICATION_SQL."
	".$INDICATEUR_REGLE_ID_SQL."
	".$ENV_SQL."
	`NATURE`='P' AND
	`DATE_INDICATEUR`='".$DATE_RECHERCHE."'";
	$res_rq_info_app = mysql_query($rq_info_app, $mysql_link) or die(mysql_error());
	$tab_rq_info_app = mysql_fetch_assoc($res_rq_info_app);
	$total_ligne_rq_info_app=mysql_num_rows($res_rq_info_app);
	$NB=$tab_rq_info_app['NB'];
	//echo $rq_info_app.'<BR>';
	mysql_free_result($res_rq_info_app);
	return $NB;
}

$NB_INTER_PROD[1]=0;
$NB_INTER_VA[1]=0;
$NB_INTER_PROD_MOY=0;
$NB_INTER_MOY=0;
$NB_INTER_VA_MOY=0;

$NB_INTER_NON_Q_PROD[1]=0;
$NB_INTER_NON_Q_VA[1]=0;
$NB_INTER_NON_Q_PROD_MOY=0;
$NB_INTER_NON_Q_MOY=0;
$NB_INTER_NON_Q_VA_MOY=0;

$NB_QC9_INTER_PROD[1]=0;
$NB_QC9_INTER_VA[1]=0;
$NB_QC9_INTER_PROD_MOY=0;
$NB_QC9_INTER_MOY=0;
$NB_QC9_INTER_VA_MOY=0;

$NB_BSP_INTER_PROD=0;
$NB_BSP_INTER_PROD_MOY=0;

$NB_PATRI_PROD=0;
$NB_PATRI_PROD_MOY=0;

## Calcul des indicateurs
do {
	$DATE_RECHERCHE=$tab_rq_info_date['DATE_INDICATEUR'];
	$ANNEE_RECHERCHE=substr($DATE_RECHERCHE,0,4);
	$MOIS_RECHERCHE=substr($DATE_RECHERCHE,4,2);
	$APPLICATION='';
	$INDICATEUR_REGLE_ID='';
	$action='PROD';
	$NB_INTER_PROD[1]=$NB_INTER_PROD[1]+Return_NB($DATE_RECHERCHE,$ANNEE_RECHERCHE,$MOIS_RECHERCHE,$action,1,$mysql_link);
	$NB_INTER_NON_Q_PROD[1]=$NB_INTER_NON_Q_PROD[1]+Return_NB_NON_COR($DATE_RECHERCHE,$ANNEE_RECHERCHE,$MOIS_RECHERCHE,$APPLICATION,$INDICATEUR_REGLE_ID,$action,$mysql_link);
	$action='VA';
	$NB_INTER_VA[1]=$NB_INTER_VA[1]+Return_NB($DATE_RECHERCHE,$ANNEE_RECHERCHE,$MOIS_RECHERCHE,$action,1,$mysql_link);
	$NB_INTER_NON_Q_VA[1]=$NB_INTER_NON_Q_VA[1]+Return_NB_NON_COR($DATE_RECHERCHE,$ANNEE_RECHERCHE,$MOIS_RECHERCHE,$APPLICATION,$INDICATEUR_REGLE_ID,$action,$mysql_link);
	
} while ($tab_rq_info_date = mysql_fetch_assoc($res_rq_info_date));
$ligne= mysql_num_rows($res_rq_info_date);
if($ligne > 0) {
	mysql_data_seek($res_rq_info_date, 0);
	$tab_rq_info_date = mysql_fetch_assoc($res_rq_info_date);
}

do {
	$DATE_RECHERCHE=$tab_rq_info_patri_date['DATE_INDICATEUR'];
	$TYPE=1;
	$NB_PATRI_PROD=$NB_PATRI_PROD+Return_PATRI_NB($DATE_RECHERCHE,$TYPE,$mysql_link);

} while ($tab_rq_info_patri_date = mysql_fetch_assoc($res_rq_info_patri_date));
$ligne= mysql_num_rows($res_rq_info_patri_date);
if($ligne > 0) {
	mysql_data_seek($res_rq_info_patri_date, 0);
	$tab_rq_info_patri_date = mysql_fetch_assoc($res_rq_info_patri_date);
}

do {
	$DATE_RECHERCHE=$tab_rq_qc9_info_date['DATE_INDICATEUR'];
	$ANNEE_RECHERCHE=substr($DATE_RECHERCHE,0,4);
	$MOIS_RECHERCHE=substr($DATE_RECHERCHE,4,2);
	$ENVIRONNEMENT='PROD';
	$APPLICATION='';
	$NATURE_QC9='';
	$SOGETI='';
	$NB_QC9_INTER_PROD[1]=$NB_QC9_INTER_PROD[1]+Return_QC9_NB($DATE_RECHERCHE,$ANNEE_RECHERCHE,$MOIS_RECHERCHE,$ENVIRONNEMENT,$APPLICATION,$SOGETI,$NATURE_QC9,$mysql_link);
	$ENVIRONNEMENT='VA';
	$NB_QC9_INTER_VA[1]=$NB_QC9_INTER_VA[1]+Return_QC9_NB($DATE_RECHERCHE,$ANNEE_RECHERCHE,$MOIS_RECHERCHE,$ENVIRONNEMENT,$APPLICATION,$SOGETI,$NATURE_QC9,$mysql_link);
	
} while ($tab_rq_qc9_info_date = mysql_fetch_assoc($res_rq_qc9_info_date));
$ligne= mysql_num_rows($res_rq_qc9_info_date);
if($ligne > 0) {
	mysql_data_seek($res_rq_qc9_info_date, 0);
	$tab_rq_qc9_info_date = mysql_fetch_assoc($res_rq_qc9_info_date);
}

do {
	$DATE_RECHERCHE_BSP=$tab_rq_bsp_info_date['DATE_INDICATEUR'];
	$NATURE='';
	$NB_BSP_INTER_PROD=$NB_BSP_INTER_PROD+Return_BSP_NB($DATE_RECHERCHE_BSP,$NATURE,$mysql_link);

} while ($tab_rq_bsp_info_date = mysql_fetch_assoc($res_rq_bsp_info_date));
$ligne= mysql_num_rows($res_rq_bsp_info_date);
if($ligne > 0) {
	mysql_data_seek($res_rq_bsp_info_date, 0);
	$tab_rq_bsp_info_date = mysql_fetch_assoc($res_rq_bsp_info_date);
}

if($MOIS<10){
	$DATE_RECHERCHE=$ANNEE."0".$MOIS;
	$DATE_RECHERCHE_BSP=$ANNEE."-0".$MOIS;
}else{
	$DATE_RECHERCHE=$ANNEE."".$MOIS;
	$DATE_RECHERCHE_BSP=$ANNEE."-".$MOIS;
}
$NB_INTER_PROD_MOY=round($NB_INTER_PROD[1]/12,2);
$NB_INTER_VA_MOY=round($NB_INTER_VA[1]/12,2);
$NB_INTER_MOY=round(($NB_INTER_PROD[1]+$NB_INTER_VA[1])/12,2);

$NB_INTER_NON_Q_PROD_MOY=round($NB_INTER_NON_Q_PROD[1]/12,2);
$NB_INTER_NON_Q_VA_MOY=round($NB_INTER_NON_Q_VA[1]/12,2);
$NB_INTER_NON_Q_MOY=round(($NB_INTER_NON_Q_PROD[1]+$NB_INTER_NON_Q_VA[1])/12,2);

$NB_QC9_INTER_PROD_MOY=round($NB_QC9_INTER_PROD[1]/12,2);
$NB_QC9_INTER_VA_MOY=round($NB_QC9_INTER_VA[1]/12,2);
$NB_QC9_INTER_MOY=round(($NB_QC9_INTER_PROD[1]+$NB_QC9_INTER_VA[1])/12,2);

$NB_BSP_INTER_PROD_MOY=round($NB_BSP_INTER_PROD/12,2);
$APPLICATION='';
$INDICATEUR_REGLE_ID='';
$action='PROD';
$NB_INTER_PROD_DATE[1]=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$action,1,$mysql_link);
$NB_INTER_PROD_DATE[2]=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$action,2,$mysql_link);
$NB_INTER_PROD_DATE[3]=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$action,3,$mysql_link);
$NB_INTER_PROD_DATE[4]=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$action,4,$mysql_link);
$NB_INTER_PROD_DATE[5]=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$action,5,$mysql_link);
$NB_INTER_PROD_DATE[6]=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$action,6,$mysql_link);

$NB_INTER_NON_Q_PROD_DATE[1]=Return_NB_NON_COR($DATE_RECHERCHE,$ANNEE,$MOIS,$APPLICATION,$INDICATEUR_REGLE_ID,$action,$mysql_link);

$action='VA';
$NB_INTER_VA_DATE[1]=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$action,1,$mysql_link);
$NB_INTER_VA_DATE[2]=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$action,2,$mysql_link);
$NB_INTER_VA_DATE[3]=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$action,3,$mysql_link);
$NB_INTER_VA_DATE[4]=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$action,4,$mysql_link);
$NB_INTER_VA_DATE[5]=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$action,5,$mysql_link);
$NB_INTER_VA_DATE[6]=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$action,6,$mysql_link);

$NB_INTER_NON_Q_VA_DATE[1]=Return_NB_NON_COR($DATE_RECHERCHE,$ANNEE,$MOIS,$APPLICATION,$INDICATEUR_REGLE_ID,$action,$mysql_link);

$NB_INTER_DATE=$NB_INTER_PROD_DATE[1]+$NB_INTER_VA_DATE[1];

$NB_INTER_NON_Q_DATE=$NB_INTER_NON_Q_PROD_DATE[1]+$NB_INTER_NON_Q_VA_DATE[1];
$NB_INTER_NON_Q_ALL_DATE[1]=$NB_INTER_NON_Q_PROD_DATE[1]+$NB_INTER_NON_Q_VA_DATE[1];

$NB_INTER_ALL_DATE[1]=$NB_INTER_PROD_DATE[1]+$NB_INTER_VA_DATE[1];
$NB_INTER_ALL_DATE[2]=$NB_INTER_PROD_DATE[2]+$NB_INTER_VA_DATE[2];
$NB_INTER_ALL_DATE[3]=$NB_INTER_PROD_DATE[3]+$NB_INTER_VA_DATE[3];
$NB_INTER_ALL_DATE[4]=$NB_INTER_PROD_DATE[4]+$NB_INTER_VA_DATE[4];
$NB_INTER_ALL_DATE[5]=$NB_INTER_PROD_DATE[5]+$NB_INTER_VA_DATE[5];
$NB_INTER_ALL_DATE[6]=$NB_INTER_PROD_DATE[6]+$NB_INTER_VA_DATE[6];

$ENVIRONNEMENT='PROD';
$APPLICATION='';
$SOGETI='';
$NATURE_QC9='';
$NB_QC9_INTER_PROD_DATE=Return_QC9_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$ENVIRONNEMENT,$APPLICATION,$SOGETI,$NATURE_QC9,$mysql_link);
$ENVIRONNEMENT='VA';
$NB_QC9_INTER_VA_DATE=Return_QC9_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$ENVIRONNEMENT,$APPLICATION,$SOGETI,$NATURE_QC9,$mysql_link);

$NB_QC9_INTER_DATE=$NB_QC9_INTER_PROD_DATE+$NB_QC9_INTER_VA_DATE;

do {
	$NATURE_QC9=$tab_rq_info_nature_qc9['NATURE'];
	$ENVIRONNEMENT='PROD';
	$APPLICATION='';
	$SOGETI='';
	$NB_QC9_INTER_PROD_DATE_DETAIL[$NATURE_QC9]=Return_QC9_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$ENVIRONNEMENT,$APPLICATION,$SOGETI,$NATURE_QC9,$mysql_link);
	$ENVIRONNEMENT='VA';
	$NB_QC9_INTER_VA_DATE_DETAIL[$NATURE_QC9]=Return_QC9_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$ENVIRONNEMENT,$APPLICATION,$SOGETI,$NATURE_QC9,$mysql_link);
	$NB_QC9_INTER_DATE_DETAIL[$NATURE_QC9]=$NB_QC9_INTER_PROD_DATE_DETAIL[$NATURE_QC9]+$NB_QC9_INTER_VA_DATE_DETAIL[$NATURE_QC9];
	
} while ($tab_rq_info_nature_qc9 = mysql_fetch_assoc($res_rq_info_nature_qc9));
$ligne= mysql_num_rows($res_rq_info_nature_qc9);
if($ligne > 0) {
	mysql_data_seek($res_rq_info_nature_qc9, 0);
	$tab_rq_info_nature_qc9 = mysql_fetch_assoc($res_rq_info_nature_qc9);
}



$NATURE='';
$NB_BSP_INTER_PROD_DATE[0]=Return_BSP_NB($DATE_RECHERCHE_BSP,$NATURE,$mysql_link);
$NATURE=1;
$NB_BSP_INTER_PROD_DATE[1]=Return_BSP_NB($DATE_RECHERCHE_BSP,$NATURE,$mysql_link);
$NATURE=2;
$NB_BSP_INTER_PROD_DATE[2]=Return_BSP_NB($DATE_RECHERCHE_BSP,$NATURE,$mysql_link);
$NATURE=3;
$NB_BSP_INTER_PROD_DATE[3]=Return_BSP_NB($DATE_RECHERCHE_BSP,$NATURE,$mysql_link);
$NATURE=4;
$NB_BSP_INTER_PROD_DATE[4]=Return_BSP_NB($DATE_RECHERCHE_BSP,$NATURE,$mysql_link);


$NB_PATRI_PROD_MOY=round($NB_PATRI_PROD/12,2);
$TYPE=1;
$NB_PATRI_PROD_DATE[1]=Return_PATRI_NB($DATE_RECHERCHE,$TYPE,$mysql_link);
$TYPE=2;
$NB_PATRI_PROD_DATE[2]=Return_PATRI_NB($DATE_RECHERCHE,$TYPE,$mysql_link);
$TYPE=3;
$NB_PATRI_PROD_DATE[3]=Return_PATRI_NB($DATE_RECHERCHE,$TYPE,$mysql_link);

  echo '
   <div align="center">';
   echo '
    <table class="table_inc" cellspacing="1" cellpading="0">
      <tr align="center" class="titre">
        <td align="center" colspan="10">';
        if($MOIS>1){
		$MOIS_url=$MOIS-1;
		$ANNEE_url=$ANNEE;
		if (Return_NB_url($ANNEE_url,$MOIS_url,$mysql_link)>0){
			echo '&nbsp;[&nbsp;<a href="./index.php?ITEM=indicateur_rapport_gec_ddr_new&ANNEE='.$ANNEE_url.'&MOIS='.$MOIS_url.'">'.$Tab_des_Mois[$MOIS_url-1].'</a>&nbsp;]&nbsp;-';
		}
	}
	if($MOIS==1){
		$MOIS_url=12;
		$ANNEE_url=$ANNEE-1;
		if (Return_NB_url($ANNEE_url,$MOIS_url,$mysql_link)>0){
			echo '&nbsp;[&nbsp;<a href="./index.php?ITEM=indicateur_rapport_gec_ddr_new&ANNEE='.$ANNEE_url.'&MOIS='.$MOIS_url.'">'.$Tab_des_Mois[$MOIS_url-1].' '.$ANNEE_url.'</a>&nbsp;]&nbsp;-';
		}
	}
	echo '&nbsp;[&nbsp;<a href="./index.php?ITEM=indicateur_menu">Retour</a>&nbsp;]&nbsp;';
	if($MOIS<12){
		$MOIS_url=$MOIS+1;
		$ANNEE_url=$ANNEE;
		if (Return_NB_url($ANNEE_url,$MOIS_url,$mysql_link)>0){
			echo '-&nbsp;[&nbsp;<a href="./index.php?ITEM=indicateur_rapport_gec_ddr_new&ANNEE='.$ANNEE_url.'&MOIS='.$MOIS_url.'">'.$Tab_des_Mois[$MOIS_url-1].'</a>&nbsp;]&nbsp;';
		}
	}
	if($MOIS==12){
		$MOIS_url=1;
		$ANNEE_url=$ANNEE+1;
		if (Return_NB_url($ANNEE_url,$MOIS_url,$mysql_link)>0){
			echo '-&nbsp;[&nbsp;<a href="./index.php?ITEM=indicateur_rapport_gec_ddr_new&ANNEE='.$ANNEE_url.'&MOIS='.$MOIS_url.'">'.$Tab_des_Mois[$MOIS_url-1].' '.$ANNEE_url.'</a>&nbsp;]&nbsp;';
		}
	}
        echo '<!---&nbsp;<a href="./indicateur/indicateur_rapport_gec_ddr_new.php?ANNEE='.$ANNEE.'&MOIS='.$MOIS.'"><img src="./img/logo_excel.png" border="0"/></a>&nbsp;--></td>
      </tr>
      <tr align="center" class="titre">
        <td align="center" colspan="10"><b>&nbsp;Exploitation Applicative Centre de production DDR '.$Tab_des_Mois[$MOIS-1].' '.$ANNEE.'&nbsp;</b></td>
      </tr>
      <tr align="center" class="titre">
        <td align="center">&nbsp;</td>
        <td align="center" colspan="3"><b>&nbsp;Prod&nbsp;</b></td>
        <td align="center" colspan="3"><b>&nbsp;VA&nbsp;</b></td>
        <td align="center" colspan="3"><b>&nbsp;Total&nbsp;</b></td>
      </tr>
      <tr align="center" class="titre">
        <td align="center">&nbsp;</td>
        <td align="center"><b>&nbsp;Valeur&nbsp;</BR>&nbsp;Mensuelle&nbsp;</b></td>
        <td align="center"><b>&nbsp;%&nbsp;</b></td>
        <td align="center"><b>&nbsp;Graph&nbsp;</b></td>
        <td align="center"><b>&nbsp;Valeur&nbsp;</BR>&nbsp;Mensuelle&nbsp;</b></td>
        <td align="center"><b>&nbsp;%&nbsp;</b></td>
        <td align="center"><b>&nbsp;Graph&nbsp;</b></td>
        <td align="center"><b>&nbsp;Valeur&nbsp;</BR>&nbsp;Mensuelle&nbsp;</b></td>
        <td align="center"><b>&nbsp;%&nbsp;</b></td>
        <td align="center"><b>&nbsp;Graph&nbsp;</b></td>
      </tr>
      <tr align="center" class="titre">
        <td align="center" colspan="10"><b>&nbsp;Gestion des demandes coordonn&eacute;es&nbsp;</b></td>
      </tr>
      <tr align="center" class="titre">
        <td align="center" colspan="10"><b>&nbsp;via ODTI&nbsp;</b></td>
      </tr>';
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      if($NB_INTER_PROD_DATE[1]==0){
      	$RATIO_NB_INTER_PROD=0;
      }else{
       	$RATIO_NB_INTER_PROD=Round(($NB_INTER_PROD_DATE[2]/$NB_INTER_PROD_DATE[1])*100,2);
      }
      $pourcent_NB=$RATIO_NB_INTER_PROD;
      $pourcent_NB_100=100-$pourcent_NB;
      if($pourcent_NB>100){$pourcent_NB=100;}
      echo '
      <tr align="center" class='.$class.'>
        <td align="right">&nbsp;Livraisons de composants&nbsp</td>
        <td align="center">&nbsp;'.$NB_INTER_PROD_DATE[2].'&nbsp;</td>
        <td align="center">&nbsp;'.$RATIO_NB_INTER_PROD.' %&nbsp;</td>
        <td align="center">';
        if ($pourcent_NB!=0){echo '<img src="./img/FS_vide.png" width="'.$pourcent_NB.'" height="15">';}
        echo '<img src="./img/FS_OK.png" width="'.$pourcent_NB_100.'" height="15">
        </td>';
      if($NB_INTER_VA_DATE[1]==0){
      	$RATIO_NB_INTER_VA=0;
      }else{
       	$RATIO_NB_INTER_VA=Round(($NB_INTER_VA_DATE[2]/$NB_INTER_VA_DATE[1])*100,2);
      }
      $pourcent_NB=$RATIO_NB_INTER_VA;
      $pourcent_NB_100=100-$pourcent_NB;
      if($pourcent_NB>100){$pourcent_NB=100;}
      echo '
        <td align="center">&nbsp;'.$NB_INTER_VA_DATE[2].'&nbsp;</td>
        <td align="center">&nbsp;'.$RATIO_NB_INTER_VA.' %&nbsp;</td>
        <td align="center">';
        if ($pourcent_NB!=0){echo '<img src="./img/FS_vide.png" width="'.$pourcent_NB.'" height="15">';}
        echo '<img src="./img/FS_OK.png" width="'.$pourcent_NB_100.'" height="15">
        </td>
        <td align="center">&nbsp;'.$NB_INTER_ALL_DATE[2].'&nbsp;</td>';
        if($NB_INTER_ALL_DATE[2]==0){
      	$RATIO_NB_INTER_ALL=0;
      }else{
       	$RATIO_NB_INTER_ALL=Round(($NB_INTER_ALL_DATE[2]/$NB_INTER_ALL_DATE[1])*100,2);
      }
      $pourcent_NB=$RATIO_NB_INTER_ALL;
      $pourcent_NB_100=100-$pourcent_NB;
      if($pourcent_NB>100){$pourcent_NB=100;}
      echo '
        <td align="center">&nbsp;'.$RATIO_NB_INTER_ALL.' %&nbsp;</td>
        <td align="center">';
        if ($pourcent_NB!=0){echo '<img src="./img/FS_vide.png" width="'.$pourcent_NB.'" height="15">';}
        echo '<img src="./img/FS_OK.png" width="'.$pourcent_NB_100.'" height="15">
        </td>
      </tr>';
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      if($NB_INTER_PROD_DATE[1]==0){
      	$RATIO_NB_INTER_PROD=0;
      }else{
       	$RATIO_NB_INTER_PROD=Round(($NB_INTER_PROD_DATE[3]/$NB_INTER_PROD_DATE[1])*100,2);
      }
      $pourcent_NB=$RATIO_NB_INTER_PROD;
      $pourcent_NB_100=100-$pourcent_NB;
      if($pourcent_NB>100){$pourcent_NB=100;}

      echo '
      <tr align="center" class='.$class.'>
        <td align="right">&nbsp;traitements exceptionnels&nbsp</td>
        <td align="center">&nbsp;'.$NB_INTER_PROD_DATE[3].'&nbsp;</td>
        <td align="center">&nbsp;'.$RATIO_NB_INTER_PROD.' %&nbsp;</td>
        <td align="center">';
        if ($pourcent_NB!=0){echo '<img src="./img/FS_vide.png" width="'.$pourcent_NB.'" height="15">';}
        echo '<img src="./img/FS_OK.png" width="'.$pourcent_NB_100.'" height="15">
        </td>';
              if($NB_INTER_VA_DATE[1]==0){
      	$RATIO_NB_INTER_VA=0;
      }else{
       	$RATIO_NB_INTER_VA=Round(($NB_INTER_VA_DATE[3]/$NB_INTER_VA_DATE[1])*100,2);
      }
      $pourcent_NB=$RATIO_NB_INTER_VA;
      $pourcent_NB_100=100-$pourcent_NB;
      if($pourcent_NB>100){$pourcent_NB=100;}
      echo '
        <td align="center">&nbsp;'.$NB_INTER_VA_DATE[3].'&nbsp;</td>
        <td align="center">&nbsp;'.$RATIO_NB_INTER_VA.' %&nbsp;</td>
        <td align="center">';
        if ($pourcent_NB!=0){echo '<img src="./img/FS_vide.png" width="'.$pourcent_NB.'" height="15">';}
        echo '<img src="./img/FS_OK.png" width="'.$pourcent_NB_100.'" height="15">
        </td>
        <td align="center">&nbsp;'.$NB_INTER_ALL_DATE[3].'&nbsp;</td>';
        if($NB_INTER_ALL_DATE[2]==0){
      	$RATIO_NB_INTER_ALL=0;
      }else{
       	$RATIO_NB_INTER_ALL=Round(($NB_INTER_ALL_DATE[3]/$NB_INTER_ALL_DATE[1])*100,2);
      }
      $pourcent_NB=$RATIO_NB_INTER_ALL;
      $pourcent_NB_100=100-$pourcent_NB;
      if($pourcent_NB>100){$pourcent_NB=100;}
      echo '
        <td align="center">&nbsp;'.$RATIO_NB_INTER_ALL.' %&nbsp;</td>
        <td align="center">';
        if ($pourcent_NB!=0){echo '<img src="./img/FS_vide.png" width="'.$pourcent_NB.'" height="15">';}
        echo '<img src="./img/FS_OK.png" width="'.$pourcent_NB_100.'" height="15">
        </td>
      </tr>';
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      if($NB_INTER_PROD_DATE[1]==0){
      	$RATIO_NB_INTER_PROD=0;
      }else{
       	$RATIO_NB_INTER_PROD=Round(($NB_INTER_PROD_DATE[4]/$NB_INTER_PROD_DATE[1])*100,2);
      }

      $pourcent_NB=$RATIO_NB_INTER_PROD;
      $pourcent_NB_100=100-$pourcent_NB;
      if($pourcent_NB>100){$pourcent_NB=100;}

      echo '
      <tr align="center" class='.$class.'>
        <td align="right">&nbsp;Modifications IAB&nbsp</td>
        <td align="center">&nbsp;'.$NB_INTER_PROD_DATE[4].'&nbsp;</td>
        <td align="center">&nbsp;'.$RATIO_NB_INTER_PROD.' %&nbsp;</td>
        <td align="center">';
        if ($pourcent_NB!=0){echo '<img src="./img/FS_vide.png" width="'.$pourcent_NB.'" height="15">';}
        echo '<img src="./img/FS_OK.png" width="'.$pourcent_NB_100.'" height="15">
        </td>';
      if($NB_INTER_VA_DATE[1]==0){
      	$RATIO_NB_INTER_VA=0;
      }else{
       	$RATIO_NB_INTER_VA=Round(($NB_INTER_VA_DATE[4]/$NB_INTER_VA_DATE[1])*100,2);
      }
      $pourcent_NB=$RATIO_NB_INTER_VA;
      $pourcent_NB_100=100-$pourcent_NB;
      if($pourcent_NB>100){$pourcent_NB=100;}
      echo '
        <td align="center">&nbsp;'.$NB_INTER_VA_DATE[4].'&nbsp;</td>
        <td align="center">&nbsp;'.$RATIO_NB_INTER_VA.' %&nbsp;</td>
        <td align="center">';
        if ($pourcent_NB!=0){echo '<img src="./img/FS_vide.png" width="'.$pourcent_NB.'" height="15">';}
        echo '<img src="./img/FS_OK.png" width="'.$pourcent_NB_100.'" height="15">
        </td>
        <td align="center">&nbsp;'.$NB_INTER_ALL_DATE[4].'&nbsp;</td>';
        if($NB_INTER_ALL_DATE[2]==0){
      	$RATIO_NB_INTER_ALL=0;
      }else{
       	$RATIO_NB_INTER_ALL=Round(($NB_INTER_ALL_DATE[4]/$NB_INTER_ALL_DATE[1])*100,2);
      }
      $pourcent_NB=$RATIO_NB_INTER_ALL;
      $pourcent_NB_100=100-$pourcent_NB;
      if($pourcent_NB>100){$pourcent_NB=100;}
      echo '
        <td align="center">&nbsp;'.$RATIO_NB_INTER_ALL.' %&nbsp;</td>
        <td align="center">';
        if ($pourcent_NB!=0){echo '<img src="./img/FS_vide.png" width="'.$pourcent_NB.'" height="15">';}
        echo '<img src="./img/FS_OK.png" width="'.$pourcent_NB_100.'" height="15">
        </td>
      </tr>';
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      if($NB_INTER_PROD_DATE[1]==0){
      	$RATIO_NB_INTER_PROD=0;
      }else{
       	$RATIO_NB_INTER_PROD=Round(($NB_INTER_PROD_DATE[5]/$NB_INTER_PROD_DATE[1])*100,2);
      }

      $pourcent_NB=$RATIO_NB_INTER_PROD;
      $pourcent_NB_100=100-$pourcent_NB;
      if($pourcent_NB>100){$pourcent_NB=100;}

      echo '
      <tr align="center" class='.$class.'>
        <td align="right">&nbsp;Demandes MOA&nbsp</td>
        <td align="center">&nbsp;'.$NB_INTER_PROD_DATE[5].'&nbsp;</td>
        <td align="center">&nbsp;'.$RATIO_NB_INTER_PROD.' %&nbsp;</td>
        <td align="center">';
        if ($pourcent_NB!=0){echo '<img src="./img/FS_vide.png" width="'.$pourcent_NB.'" height="15">';}
        echo '<img src="./img/FS_OK.png" width="'.$pourcent_NB_100.'" height="15">
        </td>';
      if($NB_INTER_VA_DATE[1]==0){
      	$RATIO_NB_INTER_VA=0;
      }else{
       	$RATIO_NB_INTER_VA=Round(($NB_INTER_VA_DATE[5]/$NB_INTER_VA_DATE[1])*100,2);
      }
      $pourcent_NB=$RATIO_NB_INTER_VA;
      $pourcent_NB_100=100-$pourcent_NB;
      if($pourcent_NB>100){$pourcent_NB=100;}
      echo '
        <td align="center">&nbsp;'.$NB_INTER_VA_DATE[5].'&nbsp;</td>
        <td align="center">&nbsp;'.$RATIO_NB_INTER_VA.' %&nbsp;</td>
        <td align="center">';
        if ($pourcent_NB!=0){echo '<img src="./img/FS_vide.png" width="'.$pourcent_NB.'" height="15">';}
        echo '<img src="./img/FS_OK.png" width="'.$pourcent_NB_100.'" height="15">
        </td>
        <td align="center">&nbsp;'.$NB_INTER_ALL_DATE[5].'&nbsp;</td>';
        if($NB_INTER_ALL_DATE[2]==0){
      	$RATIO_NB_INTER_ALL=0;
      }else{
       	$RATIO_NB_INTER_ALL=Round(($NB_INTER_ALL_DATE[5]/$NB_INTER_ALL_DATE[1])*100,2);
      }
      $pourcent_NB=$RATIO_NB_INTER_ALL;
      $pourcent_NB_100=100-$pourcent_NB;
      if($pourcent_NB>100){$pourcent_NB=100;}
      echo '
        <td align="center">&nbsp;'.$RATIO_NB_INTER_ALL.' %&nbsp;</td>
        <td align="center">';
        if ($pourcent_NB!=0){echo '<img src="./img/FS_vide.png" width="'.$pourcent_NB.'" height="15">';}
        echo '<img src="./img/FS_OK.png" width="'.$pourcent_NB_100.'" height="15">
        </td>
      </tr>';

      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      if($NB_INTER_PROD_DATE[1]==0){
      	$RATIO_NB_INTER_PROD=0;
      }else{
       	$RATIO_NB_INTER_PROD=Round(($NB_INTER_PROD_DATE[6]/$NB_INTER_PROD_DATE[1])*100,2);
      }

      $pourcent_NB=$RATIO_NB_INTER_PROD;
      $pourcent_NB_100=100-$pourcent_NB;
      if($pourcent_NB>100){$pourcent_NB=100;}

      echo '
      <tr align="center" class='.$class.'>
        <td align="right">&nbsp;Demandes non qualifi&eacute;es&nbsp</td>
        <td align="center">&nbsp;'.$NB_INTER_PROD_DATE[6].'&nbsp;</td>
        <td align="center">&nbsp;'.$RATIO_NB_INTER_PROD.' %&nbsp;</td>
        <td align="center">';
        if ($pourcent_NB!=0){echo '<img src="./img/FS_vide.png" width="'.$pourcent_NB.'" height="15">';}
        echo '<img src="./img/FS_OK.png" width="'.$pourcent_NB_100.'" height="15">
        </td>';
      if($NB_INTER_VA_DATE[1]==0){
      	$RATIO_NB_INTER_VA=0;
      }else{
       	$RATIO_NB_INTER_VA=Round(($NB_INTER_VA_DATE[6]/$NB_INTER_VA_DATE[1])*100,2);
      }
      $pourcent_NB=$RATIO_NB_INTER_VA;
      $pourcent_NB_100=100-$pourcent_NB;
      if($pourcent_NB>100){$pourcent_NB=100;}
      echo '
        <td align="center">&nbsp;'.$NB_INTER_VA_DATE[6].'&nbsp;</td>
        <td align="center">&nbsp;'.$RATIO_NB_INTER_VA.' %&nbsp;</td>
        <td align="center">';
        if ($pourcent_NB!=0){echo '<img src="./img/FS_vide.png" width="'.$pourcent_NB.'" height="15">';}
        echo '<img src="./img/FS_OK.png" width="'.$pourcent_NB_100.'" height="15">
        </td>
        <td align="center">&nbsp;'.$NB_INTER_ALL_DATE[6].'&nbsp;</td>';
        if($NB_INTER_ALL_DATE[2]==0){
      	$RATIO_NB_INTER_ALL=0;
      }else{
       	$RATIO_NB_INTER_ALL=Round(($NB_INTER_ALL_DATE[6]/$NB_INTER_ALL_DATE[1])*100,2);
      }
      $pourcent_NB=$RATIO_NB_INTER_ALL;
      $pourcent_NB_100=100-$pourcent_NB;
      if($pourcent_NB>100){$pourcent_NB=100;}
      echo '
        <td align="center">&nbsp;'.$RATIO_NB_INTER_ALL.' %&nbsp;</td>
        <td align="center">';
        if ($pourcent_NB!=0){echo '<img src="./img/FS_vide.png" width="'.$pourcent_NB.'" height="15">';}
        echo '<img src="./img/FS_OK.png" width="'.$pourcent_NB_100.'" height="15">
        </td>
      </tr>';
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Valeur du mois&nbsp</b></td>
        <td align="center"><b>&nbsp;'.$NB_INTER_PROD_DATE[1].'&nbsp;</b></td>';
        if($NB_INTER_ALL_DATE[1]==0){
      	$NB=0;
      }else{
       	$NB=Round(($NB_INTER_PROD_DATE[1]/$NB_INTER_ALL_DATE[1])*100,2);
      }
      $pourcent_NB=$NB;
      $pourcent_NB_100=100-$pourcent_NB;
      if($pourcent_NB>100){$pourcent_NB=100;}
      echo '
        <td align="center">&nbsp;'.$NB.' %&nbsp;</td>
        <td align="center">';
        if ($pourcent_NB!=0){echo '<img src="./img/FS_vide.png" width="'.$pourcent_NB.'" height="15">';}
        echo '<img src="./img/FS_OK.png" width="'.$pourcent_NB_100.'" height="15">
        </td>
        <td align="center"><b>&nbsp;'.$NB_INTER_VA_DATE[1].'&nbsp;</b></td>';
        if($NB_INTER_ALL_DATE[1]==0){
      	$NB=0;
      }else{
       	$NB=Round(($NB_INTER_VA_DATE[1]/$NB_INTER_ALL_DATE[1])*100,2);
      }
      $pourcent_NB=$NB;
      $pourcent_NB_100=100-$pourcent_NB;
      if($pourcent_NB>100){$pourcent_NB=100;}
      echo '
        <td align="center">&nbsp;'.$NB.' %&nbsp;</td>
        <td align="center">';
        if ($pourcent_NB!=0){echo '<img src="./img/FS_vide.png" width="'.$pourcent_NB.'" height="15">';}
        echo '<img src="./img/FS_OK.png" width="'.$pourcent_NB_100.'" height="15">
        </td>
        <td align="center"><b>&nbsp;'.$NB_INTER_ALL_DATE[1].'&nbsp;</b></td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>

      </tr>';
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Moyenne sur 12 mois&nbsp</b></td>
        <td align="center"><b>&nbsp;'.$NB_INTER_PROD_MOY.'&nbsp;</b></td>';
        if($NB_INTER_MOY==0){
      	$NB=0;
      }else{
       	$NB=Round(($NB_INTER_PROD_MOY/$NB_INTER_MOY)*100,2);
      }
      $pourcent_NB=$NB;
      $pourcent_NB_100=100-$pourcent_NB;
      if($pourcent_NB>100){$pourcent_NB=100;}
      echo '
        <td align="center">&nbsp;'.$NB.' %&nbsp;</td>
        <td align="center">';
        if ($pourcent_NB!=0){echo '<img src="./img/FS_vide.png" width="'.$pourcent_NB.'" height="15">';}
        echo '<img src="./img/FS_OK.png" width="'.$pourcent_NB_100.'" height="15">
        </td>
        <td align="center"><b>&nbsp;'.$NB_INTER_VA_MOY.'&nbsp;</b></td>';
        if($NB_INTER_MOY==0){
      	$NB=0;
      }else{
       	$NB=Round(($NB_INTER_VA_MOY/$NB_INTER_MOY)*100,2);
      }
      $pourcent_NB=$NB;
      $pourcent_NB_100=100-$pourcent_NB;
      if($pourcent_NB>100){$pourcent_NB=100;}
      echo '
        <td align="center">&nbsp;'.$NB.' %&nbsp;</td>
        <td align="center">';
        if ($pourcent_NB!=0){echo '<img src="./img/FS_vide.png" width="'.$pourcent_NB.'" height="15">';}
        echo '<img src="./img/FS_OK.png" width="'.$pourcent_NB_100.'" height="15">
        </td>
        <td align="center"><b>&nbsp;'.$NB_INTER_MOY.'&nbsp;</b></td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
      </tr>';
      echo '
      <tr align="center" class="titre">
        <td align="center" colspan="10"><b>&nbsp;Gestion des demandes non coordonn&eacute;es&nbsp;</b></td>
      </tr>
      <tr align="center" class="titre">
        <td align="center" colspan="10">&nbsp;<a href="./index.php?ITEM=indicateur_rapport_gec_ddr_new_detail&ANNEE='.$ANNEE.'&MOIS='.$MOIS.'">via ODTI</a>&nbsp;</td>
      </tr>';
      /*do {
      	$NATURE_ODTI=$tab_rq_info_nature_ODTI_NON['INDICATEUR_REGLE_INFO'];
      	$j++;
	if ($j%2) { $class = "pair";}else{$class = "impair";} 
	echo '
      <tr align="center" class='.$class.'>
        <td align="right">&nbsp;'.$NATURE_ODTI.'&nbsp</td>
        <td align="center">&nbsp;&nbsp;</td>
        <td align="center">&nbsp;&nbsp;</td>
        <td align="center">&nbsp;&nbsp;</td>
        <td align="center">&nbsp;&nbsp;</td>
        <td align="center">&nbsp;&nbsp;</td>
        <td align="center">&nbsp;&nbsp;</td>
        <td align="center">&nbsp;&nbsp;</td>
        <td align="center">&nbsp;&nbsp;</td>
        <td align="center">&nbsp;&nbsp;</td>
      </tr>';
        
      	} while ($tab_rq_info_nature_ODTI_NON = mysql_fetch_assoc($res_rq_info_nature_ODTI_NON));
	$ligne= mysql_num_rows($res_rq_info_nature_ODTI_NON);
	if($ligne > 0) {
		mysql_data_seek($res_rq_info_nature_ODTI_NON, 0);
		$tab_rq_info_nature_ODTI_NON = mysql_fetch_assoc($res_rq_info_nature_ODTI_NON);
	}*/
	$j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Valeur du mois&nbsp</b></td>
        <td align="center"><b>&nbsp;'.$NB_INTER_NON_Q_PROD_DATE[1].'&nbsp;</b></td>';
        if($NB_INTER_NON_Q_ALL_DATE[1]==0){
      	$NB=0;
      }else{
       	$NB=Round(($NB_INTER_NON_Q_PROD_DATE[1]/$NB_INTER_NON_Q_ALL_DATE[1])*100,2);
      }
      $pourcent_NB=$NB;
      $pourcent_NB_100=100-$pourcent_NB;
      if($pourcent_NB>100){$pourcent_NB=100;}
      echo '
        <td align="center">&nbsp;'.$NB.' %&nbsp;</td>
        <td align="center">';
        if ($pourcent_NB!=0){echo '<img src="./img/FS_vide.png" width="'.$pourcent_NB.'" height="15">';}
        echo '<img src="./img/FS_OK.png" width="'.$pourcent_NB_100.'" height="15">
        </td>
        <td align="center"><b>&nbsp;'.$NB_INTER_NON_Q_VA_DATE[1].'&nbsp;</b></td>';
        if($NB_INTER_NON_Q_ALL_DATE[1]==0){
      	$NB=0;
      }else{
       	$NB=Round(($NB_INTER_NON_Q_VA_DATE[1]/$NB_INTER_NON_Q_ALL_DATE[1])*100,2);
      }
      $pourcent_NB=$NB;
      $pourcent_NB_100=100-$pourcent_NB;
      if($pourcent_NB>100){$pourcent_NB=100;}
      echo '
        <td align="center">&nbsp;'.$NB.' %&nbsp;</td>
        <td align="center">';
        if ($pourcent_NB!=0){echo '<img src="./img/FS_vide.png" width="'.$pourcent_NB.'" height="15">';}
        echo '<img src="./img/FS_OK.png" width="'.$pourcent_NB_100.'" height="15">
        </td>
        <td align="center"><b>&nbsp;'.$NB_INTER_NON_Q_ALL_DATE[1].'&nbsp;</b></td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>

      </tr>';
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Moyenne sur 12 mois&nbsp</b></td>
        <td align="center"><b>&nbsp;'.$NB_INTER_NON_Q_PROD_MOY.'&nbsp;</b></td>';
        if($NB_INTER_NON_Q_MOY==0){
      	$NB=0;
      }else{
       	$NB=Round(($NB_INTER_NON_Q_PROD_MOY/$NB_INTER_NON_Q_MOY)*100,2);
      }
      $pourcent_NB=$NB;
      $pourcent_NB_100=100-$pourcent_NB;
      if($pourcent_NB>100){$pourcent_NB=100;}
      echo '
        <td align="center">&nbsp;'.$NB.' %&nbsp;</td>
        <td align="center">';
        if ($pourcent_NB!=0){echo '<img src="./img/FS_vide.png" width="'.$pourcent_NB.'" height="15">';}
        echo '<img src="./img/FS_OK.png" width="'.$pourcent_NB_100.'" height="15">
        </td>
        <td align="center"><b>&nbsp;'.$NB_INTER_NON_Q_VA_MOY.'&nbsp;</b></td>';
        if($NB_INTER_NON_Q_MOY==0){
      	$NB=0;
      }else{
       	$NB=Round(($NB_INTER_NON_Q_VA_MOY/$NB_INTER_NON_Q_MOY)*100,2);
      }
      $pourcent_NB=$NB;
      $pourcent_NB_100=100-$pourcent_NB;
      if($pourcent_NB>100){$pourcent_NB=100;}
      echo '
        <td align="center">&nbsp;'.$NB.' %&nbsp;</td>
        <td align="center">';
        if ($pourcent_NB!=0){echo '<img src="./img/FS_vide.png" width="'.$pourcent_NB.'" height="15">';}
        echo '<img src="./img/FS_OK.png" width="'.$pourcent_NB_100.'" height="15">
        </td>
        <td align="center"><b>&nbsp;'.$NB_INTER_NON_Q_MOY.'&nbsp;</b></td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
      </tr>';
      	
      echo '
      <tr align="center" class="titre">
        <td align="center" colspan="10">&nbsp;<a href="./index.php?ITEM=indicateur_rapport_gec_ddr_new_detail&ANNEE='.$ANNEE.'&MOIS='.$MOIS.'">via QC9</a>&nbsp;</td>
      </tr>';
/*
      do {
	$NATURE_QC9=$tab_rq_info_nature_qc9['NATURE'];
	$j++;
	if ($j%2) { $class = "pair";}else{$class = "impair";} 
	echo '
      <tr align="center" class='.$class.'>
        <td align="right">&nbsp;'.$NATURE_QC9.'&nbsp</td>
        <td align="center">&nbsp;'.$NB_QC9_INTER_PROD_DATE_DETAIL[$NATURE_QC9].'&nbsp;</td>';
        if($NB_QC9_INTER_PROD_DATE==0){
      	$NB=0;
      }else{
       	$NB=Round(($NB_QC9_INTER_PROD_DATE_DETAIL[$NATURE_QC9]/$NB_QC9_INTER_PROD_DATE)*100,2);
      }
      $pourcent_NB=$NB;
      $pourcent_NB_100=100-$pourcent_NB;
      if($pourcent_NB>100){$pourcent_NB=100;}
      echo'
        <td align="center">&nbsp;'.$NB.' %&nbsp;</td>
        <td align="center">';
        if ($pourcent_NB!=0){echo '<img src="./img/FS_vide.png" width="'.$pourcent_NB.'" height="15">';}
        echo '<img src="./img/FS_OK.png" width="'.$pourcent_NB_100.'" height="15">
        </td>
        <td align="center">&nbsp;'.$NB_QC9_INTER_VA_DATE_DETAIL[$NATURE_QC9].'&nbsp;</td>';
        if($NB_QC9_INTER_VA_DATE==0){
      	$NB=0;
      }else{
       	$NB=Round(($NB_QC9_INTER_VA_DATE_DETAIL[$NATURE_QC9]/$NB_QC9_INTER_VA_DATE)*100,2);
      }
       $pourcent_NB=$NB;
      $pourcent_NB_100=100-$pourcent_NB;
      if($pourcent_NB>100){$pourcent_NB=100;}
      echo'
        <td align="center">&nbsp;'.$NB.' %&nbsp;</td>
        <td align="center">';
        if ($pourcent_NB!=0){echo '<img src="./img/FS_vide.png" width="'.$pourcent_NB.'" height="15">';}
        echo '<img src="./img/FS_OK.png" width="'.$pourcent_NB_100.'" height="15">
        </td>
        <td align="center">&nbsp;'.$NB_QC9_INTER_DATE_DETAIL[$NATURE_QC9].'&nbsp;</td>';
        if($NB_QC9_INTER_DATE==0){
      	$NB=0;
      }else{
       	$NB=Round(($NB_QC9_INTER_DATE_DETAIL[$NATURE_QC9]/$NB_QC9_INTER_DATE)*100,2);
      }
       $pourcent_NB=$NB;
      $pourcent_NB_100=100-$pourcent_NB;
      if($pourcent_NB>100){$pourcent_NB=100;}
      echo'
        <td align="center">&nbsp;'.$NB.' %&nbsp;</td>
        <td align="center">';
        if ($pourcent_NB!=0){echo '<img src="./img/FS_vide.png" width="'.$pourcent_NB.'" height="15">';}
        echo '<img src="./img/FS_OK.png" width="'.$pourcent_NB_100.'" height="15">
        </td>
      </tr>';
	} while ($tab_rq_info_nature_qc9 = mysql_fetch_assoc($res_rq_info_nature_qc9));
	$ligne= mysql_num_rows($res_rq_info_nature_qc9);
	if($ligne > 0) {
		mysql_data_seek($res_rq_info_nature_qc9, 0);
		$tab_rq_info_nature_qc9 = mysql_fetch_assoc($res_rq_info_nature_qc9);
	}*/
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      if($NB_QC9_INTER_DATE==0){
      	$RATIO_QC9_NB_INTER_PROD=0;
      }else{
       	$RATIO_QC9_NB_INTER_PROD=Round(($NB_QC9_INTER_PROD_DATE/$NB_QC9_INTER_DATE)*100,2);
      }
      $pourcent_NB=$RATIO_QC9_NB_INTER_PROD;
      $pourcent_NB_100=100-$pourcent_NB;
      if($pourcent_NB>100){$pourcent_NB=100;}

      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Valeur du mois&nbsp</b></td>
        <td align="center"><b>&nbsp;'.$NB_QC9_INTER_PROD_DATE.'&nbsp;</b></td>
        <td align="center">&nbsp;'.$RATIO_QC9_NB_INTER_PROD.' %&nbsp;</td>
        <td align="center">';
        if ($pourcent_NB!=0){echo '<img src="./img/FS_vide.png" width="'.$pourcent_NB.'" height="15">';}
        echo '<img src="./img/FS_OK.png" width="'.$pourcent_NB_100.'" height="15">
        </td>';
        if($NB_QC9_INTER_DATE==0){
      	$RATIO_QC9_NB_INTER_VA=0;
      }else{
       	$RATIO_QC9_NB_INTER_VA=Round(($NB_QC9_INTER_VA_DATE/$NB_QC9_INTER_DATE)*100,2);
      }

      $pourcent_NB=$RATIO_QC9_NB_INTER_VA;
      $pourcent_NB_100=100-$pourcent_NB;
      if($pourcent_NB>100){$pourcent_NB=100;}

      echo '<td align="center"><b>&nbsp;'.$NB_QC9_INTER_VA_DATE.'&nbsp;</b></td>
        <td align="center">&nbsp;'.$RATIO_QC9_NB_INTER_VA.' %&nbsp;</td>
        <td align="center">';
        if ($pourcent_NB!=0){echo '<img src="./img/FS_vide.png" width="'.$pourcent_NB.'" height="15">';}
        echo '<img src="./img/FS_OK.png" width="'.$pourcent_NB_100.'" height="15">
        </td>
        <td align="center"><b>&nbsp;'.$NB_QC9_INTER_DATE.'&nbsp;</b></td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
      </tr>';
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Moyenne sur 12 mois&nbsp</b></td>
        <td align="center"><b>&nbsp;'.$NB_QC9_INTER_PROD_MOY.'&nbsp;</b></td>';
        if($NB_QC9_INTER_MOY==0){
      	$NB=0;
      }else{
       	$NB=Round(($NB_QC9_INTER_PROD_MOY/$NB_INTER_NON_Q_MOY)*100,2);
      }
      $pourcent_NB=$NB;
      $pourcent_NB_100=100-$pourcent_NB;
      if($pourcent_NB>100){$pourcent_NB=100;}
      echo '
        <td align="center">&nbsp;'.$NB.' %&nbsp;</td>
        <td align="center">';
        if ($pourcent_NB!=0){echo '<img src="./img/FS_vide.png" width="'.$pourcent_NB.'" height="15">';}
        echo '<img src="./img/FS_OK.png" width="'.$pourcent_NB_100.'" height="15">
        </td>
        <td align="center"><b>&nbsp;'.$NB_QC9_INTER_VA_MOY.'&nbsp;</b></td>';
        if($NB_QC9_INTER_MOY==0){
      	$NB=0;
      }else{
       	$NB=Round(($NB_QC9_INTER_VA_MOY/$NB_INTER_NON_Q_MOY)*100,2);
      }
      $pourcent_NB=$NB;
      $pourcent_NB_100=100-$pourcent_NB;
      if($pourcent_NB>100){$pourcent_NB=100;}
      echo '
        <td align="center">&nbsp;'.$NB.' %&nbsp;</td>
        <td align="center">';
        if ($pourcent_NB!=0){echo '<img src="./img/FS_vide.png" width="'.$pourcent_NB.'" height="15">';}
        echo '<img src="./img/FS_OK.png" width="'.$pourcent_NB_100.'" height="15">
        </td>
        <td align="center"><b>&nbsp;'.$NB_QC9_INTER_MOY.'&nbsp;</b></td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
      </tr>';

      echo '
      <tr align="center" class="titre">
        <td align="left" colspan="10"><b>&nbsp;Suivi de Production - Nombre d\'incidents au BSP&nbsp;</b></td>
      </tr>';
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      if($NB_BSP_INTER_PROD_DATE[0]==0){
      	$RATIO_BSP_NB_INTER_PROD=0;
      }else{
       	$RATIO_BSP_NB_INTER_PROD=Round(($NB_BSP_INTER_PROD_DATE[1]/$NB_BSP_INTER_PROD_DATE[0])*100,2);
      }

      $pourcent_NB=$RATIO_BSP_NB_INTER_PROD;
      $pourcent_NB_100=100-$pourcent_NB;
      if($pourcent_NB>100){$pourcent_NB=100;}

      echo '
      <tr align="center" class='.$class.'>
        <td align="right">&nbsp;Applicatif&nbsp</td>
        <td align="center">&nbsp;'.$NB_BSP_INTER_PROD_DATE[1].'&nbsp;</td>
        <td align="center">&nbsp;'.$RATIO_BSP_NB_INTER_PROD.' %&nbsp;</td>
        <td align="center">';
        if ($pourcent_NB!=0){echo '<img src="./img/FS_vide.png" width="'.$pourcent_NB.'" height="15">';}
        echo '<img src="./img/FS_OK.png" width="'.$pourcent_NB_100.'" height="15">
        </td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
      </tr>';
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      if($NB_BSP_INTER_PROD_DATE[0]==0){
      	$RATIO_BSP_NB_INTER_PROD=0;
      }else{
       	$RATIO_BSP_NB_INTER_PROD=Round(($NB_BSP_INTER_PROD_DATE[2]/$NB_BSP_INTER_PROD_DATE[0])*100,2);
      }

      $pourcent_NB=$RATIO_BSP_NB_INTER_PROD;
      $pourcent_NB_100=100-$pourcent_NB;
      if($pourcent_NB>100){$pourcent_NB=100;}

      echo '
      <tr align="center" class='.$class.'>
        <td align="right">&nbsp;Données&nbsp</td>
        <td align="center">&nbsp;'.$NB_BSP_INTER_PROD_DATE[2].'&nbsp;</td>
        <td align="center">&nbsp;'.$RATIO_BSP_NB_INTER_PROD.' %&nbsp;</td>
        <td align="center">';
        if ($pourcent_NB!=0){echo '<img src="./img/FS_vide.png" width="'.$pourcent_NB.'" height="15">';}
        echo '<img src="./img/FS_OK.png" width="'.$pourcent_NB_100.'" height="15">
        </td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
      </tr>';
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      if($NB_BSP_INTER_PROD_DATE[0]==0){
      	$RATIO_BSP_NB_INTER_PROD=0;
      }else{
       	$RATIO_BSP_NB_INTER_PROD=Round(($NB_BSP_INTER_PROD_DATE[3]/$NB_BSP_INTER_PROD_DATE[0])*100,2);
      }

      $pourcent_NB=$RATIO_BSP_NB_INTER_PROD;
      $pourcent_NB_100=100-$pourcent_NB;
      if($pourcent_NB>100){$pourcent_NB=100;}

      echo '
      <tr align="center" class='.$class.'>
        <td align="right">&nbsp;Indéterminé&nbsp</td>
        <td align="center">&nbsp;'.$NB_BSP_INTER_PROD_DATE[3].'&nbsp;</td>
        <td align="center">&nbsp;'.$RATIO_BSP_NB_INTER_PROD.' %&nbsp;</td>
        <td align="center">';
        if ($pourcent_NB!=0){echo '<img src="./img/FS_vide.png" width="'.$pourcent_NB.'" height="15">';}
        echo '<img src="./img/FS_OK.png" width="'.$pourcent_NB_100.'" height="15">
        </td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
      </tr>';
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      if($NB_BSP_INTER_PROD_DATE[0]==0){
      	$RATIO_BSP_NB_INTER_PROD=0;
      }else{
       	$RATIO_BSP_NB_INTER_PROD=Round(($NB_BSP_INTER_PROD_DATE[4]/$NB_BSP_INTER_PROD_DATE[0])*100,2);
      }

      $pourcent_NB=$RATIO_BSP_NB_INTER_PROD;
      $pourcent_NB_100=100-$pourcent_NB;
      if($pourcent_NB>100){$pourcent_NB=100;}

      echo '
      <tr align="center" class='.$class.'>
        <td align="right">&nbsp;Technique&nbsp</td>
        <td align="center">&nbsp;'.$NB_BSP_INTER_PROD_DATE[4].'&nbsp;</td>
        <td align="center">&nbsp;'.$RATIO_BSP_NB_INTER_PROD.' %&nbsp;</td>
        <td align="center">';
        if ($pourcent_NB!=0){echo '<img src="./img/FS_vide.png" width="'.$pourcent_NB.'" height="15">';}
        echo '<img src="./img/FS_OK.png" width="'.$pourcent_NB_100.'" height="15">
        </td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
      </tr>';
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Total : Valeur du mois&nbsp</b></td>
        <td align="center"><b>&nbsp;'.$NB_BSP_INTER_PROD_DATE[0].'&nbsp;</b></td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
      </tr>';
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Total : Moyenne sur 12 mois&nbsp</b></td>
        <td align="center"><b>&nbsp;'.$NB_BSP_INTER_PROD_MOY.'&nbsp;</b></td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
      </tr>
      <tr align="center" class="titre">
        <td align="left" colspan="10"><b>&nbsp;Patrimoine en Production - Nombre de composants&nbsp;</b></td>
      </tr>';
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      if($NB_PATRI_PROD_DATE[1]==0){
      	$RATIO_PATRI_NB_PROD=0;
      }else{
       	$RATIO_PATRI_NB_PROD=Round(($NB_PATRI_PROD_DATE[2]/$NB_PATRI_PROD_DATE[1])*100,2);
      }

      $pourcent_NB=$RATIO_PATRI_NB_PROD;
      $pourcent_NB_100=100-$pourcent_NB;
      if($pourcent_NB>100){$pourcent_NB=100;}

      echo '
      <tr align="center" class='.$class.'>
        <td align="right">&nbsp;Compilables&nbsp</td>
        <td align="center">&nbsp;'.$NB_PATRI_PROD_DATE[2].'&nbsp;</td>
        <td align="center">&nbsp;'.$RATIO_PATRI_NB_PROD.' %&nbsp;</td>
        <td align="center">';
        if ($pourcent_NB!=0){echo '<img src="./img/FS_vide.png" width="'.$pourcent_NB.'" height="15">';}
        echo '<img src="./img/FS_OK.png" width="'.$pourcent_NB_100.'" height="15">
        </td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
      </tr>';
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      if($NB_PATRI_PROD_DATE[1]==0){
      	$RATIO_PATRI_NB_PROD=0;
      }else{
       	$RATIO_PATRI_NB_PROD=Round(($NB_PATRI_PROD_DATE[3]/$NB_PATRI_PROD_DATE[1])*100,2);
      }

      $pourcent_NB=$RATIO_PATRI_NB_PROD;
      $pourcent_NB_100=100-$pourcent_NB;
      if($pourcent_NB>100){$pourcent_NB=100;}

      echo '
      <tr align="center" class='.$class.'>
        <td align="right">&nbsp;Scripts&nbsp</td>
        <td align="center">&nbsp;'.$NB_PATRI_PROD_DATE[3].'&nbsp;</td>
        <td align="center">&nbsp;'.$RATIO_PATRI_NB_PROD.' %&nbsp;</td>
        <td align="center">';
        if ($pourcent_NB!=0){echo '<img src="./img/FS_vide.png" width="'.$pourcent_NB.'" height="15">';}
        echo '<img src="./img/FS_OK.png" width="'.$pourcent_NB_100.'" height="15">
        </td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
      </tr>';
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Total : Valeur du mois&nbsp</b></td>
        <td align="center"><b>&nbsp;'.$NB_PATRI_PROD_DATE[1].'&nbsp;</b></td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
      </tr>';
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Total : Moyenne sur 12 mois&nbsp</b></td>
        <td align="center"><b>&nbsp;'.$NB_PATRI_PROD_MOY.'&nbsp;</b></td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
      </tr>
      <tr align="center" class="titre">
        <td align="center" colspan="10">&nbsp;[&nbsp;<a href="./index.php?ITEM=indicateur_menu">Retour</a>&nbsp;]&nbsp;</td>
      </tr>
      <tr align="center" class="titre">
        <td align="center" colspan="10">&nbsp;</td>
      </tr>
      </table>
  </div>
  ';
mysql_free_result($res_rq_info_patri_date);
mysql_free_result($res_rq_bsp_info_date);
mysql_free_result($res_rq_qc9_info_date);
mysql_free_result($res_rq_info_nature_qc9);
mysql_free_result($res_rq_info_date);
mysql_close($mysql_link);
?>