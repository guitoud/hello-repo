<?php
require_once("../lib/Artichow-php4+5/BarPlot.class.php");
require_once("../cf/autre_fonctions.php");

if(isset($_GET['ENVIRONNEMENT'])){
	$ENVIRONNEMENT=$_GET['ENVIRONNEMENT'];
	$ENVIRONNEMENT_LIB=' de '.$ENVIRONNEMENT;
}else{
	$ENVIRONNEMENT='';
	$ENVIRONNEMENT_LIB='';
}

if($ENVIRONNEMENT==''){
	$ENVIRONNEMENT_SQL="";	
}else{
	$ENVIRONNEMENT_SQL="`ENVIRONNEMENT`='".$ENVIRONNEMENT."' AND";	
}

if(isset($_GET['SOGETI'])){
	$SOGETI=$_GET['SOGETI'];
	if($SOGETI=='Y'){
		$SOGETI_LIB=' pour SOGETI';
	}else{
		$SOGETI_LIB='';
	}
}else{
	$SOGETI='';
	$SOGETI_LIB='';
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
ouverture_db();


function Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$ENVIRONNEMENT,$APPLICATION,$SOGETI)
{
	if($APPLICATION==''){
		$APPLICATION_SQL="";	
	}else{
		$APPLICATION_SQL="`APPLICATION`='".$APPLICATION."' AND";
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
	$rq_info_nb="
	SELECT COUNT(`ID`) AS `NB` 
	FROM `indicateur_qc9_calcul` 
	WHERE 
	`STATUS`='6- Termin&eacute;e' AND
	`DATE_PREVUE` LIKE '".$DATE_PREVUE."%' AND
	".$ENVIRONNEMENT_SQL."
	".$APPLICATION_SQL."
	".$SOGETI_SQL."
	`DATE_INDICATEUR`='".$DATE_RECHERCHE."'";
	$res_rq_info_nb = mysql_query($rq_info_nb) or die(mysql_error());
	$tab_rq_info_nb = mysql_fetch_assoc($res_rq_info_nb);
	$total_ligne_rq_info_nb=mysql_num_rows($res_rq_info_nb);
	$NB=$tab_rq_info_nb['NB'];
	//echo $rq_info_nb.'<BR>';
	mysql_free_result($res_rq_info_nb);
	return $NB;
}

function Return_DUREE($DATE_RECHERCHE,$ANNEE,$MOIS,$ENVIRONNEMENT,$APPLICATION,$SOGETI)
{
	if($APPLICATION==''){
		$APPLICATION_SQL="";	
	}else{
		$APPLICATION_SQL="`APPLICATION`='".$APPLICATION."' AND";
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
	$rq_info_duree="
	SELECT SUM(`TEMPS_MINUTES_OK`) AS `DUREE` 
	FROM `indicateur_qc9_calcul` 
	WHERE 
	`STATUS`='6- Termin&eacute;e' AND
	`DATE_PREVUE` LIKE '".$DATE_PREVUE."%' AND
	".$ENVIRONNEMENT_SQL."
	".$APPLICATION_SQL."
	".$SOGETI_SQL."
	`DATE_INDICATEUR`='".$DATE_RECHERCHE."'";
	$res_rq_info_duree = mysql_query($rq_info_duree) or die(mysql_error());
	$tab_rq_info_duree = mysql_fetch_assoc($res_rq_info_duree);
	$total_ligne_rq_info_duree=mysql_num_rows($res_rq_info_duree);
	$DUREE=$tab_rq_info_duree['DUREE'];
	//echo $rq_info_duree.'<BR>';
	if($DUREE==''){$DUREE=0;}
	mysql_free_result($res_rq_info_duree);
	return $DUREE;
}

for($i=1;$i<=14;$i++){
	$NB_INTER_ALL[$i]=0;
	$DUREE_INTER_ALL[$i]=0;
}
## Calcul des indicateurs

$rq_info="
SELECT COUNT(DISTINCT(`DATE_INDICATEUR`)) AS `NB`
FROM `indicateur_qc9_calcul` ";
$res_rq_info = mysql_query($rq_info) or die(mysql_error());
$tab_rq_info = mysql_fetch_assoc($res_rq_info);
$total_ligne_rq_info=mysql_num_rows($res_rq_info);
$NB=$tab_rq_info['NB'];
mysql_free_result($res_rq_info);

$LIMIT="";
$LIMIT_MIN="LIMIT  1";
if($NB > 14 ){
	$NB = $NB - 14 ;
	$LIMIT= "LIMIT ".$NB." , 14 ";
	$LIMIT_MIN= "LIMIT ".$NB." , 14 ";
}
$rq_info="
SELECT DISTINCT (
`DATE_INDICATEUR` 
)
FROM `indicateur_qc9_calcul` 
ORDER BY `DATE_INDICATEUR` ASC 
".$LIMIT_MIN."";
$res_rq_info = mysql_query($rq_info) or die(mysql_error());
$tab_rq_info = mysql_fetch_assoc($res_rq_info);
$total_ligne_rq_info=mysql_num_rows($res_rq_info);
$DATE_INDICATEUR_MIN=$tab_rq_info['DATE_INDICATEUR'];
mysql_free_result($res_rq_info);

$rq_info_date="
SELECT DISTINCT (
`DATE_INDICATEUR` 
)
FROM `indicateur_qc9_calcul` 
ORDER BY `DATE_INDICATEUR` ASC 
".$LIMIT."";
$res_rq_info_date = mysql_query($rq_info_date) or die(mysql_error());
$tab_rq_info_date = mysql_fetch_assoc($res_rq_info_date);
$total_ligne_rq_info_date=mysql_num_rows($res_rq_info_date); 

do {
	$APPLICATION='';
	$DATE_RECHERCHE=$tab_rq_info_date['DATE_INDICATEUR'];
	$ANNEE=substr($DATE_RECHERCHE,0,4);
	$MOIS=substr($DATE_RECHERCHE,4,2);
	if($MOIS<10){$MOIS=substr($MOIS,1,1);}
	if(!isset($NB_INTER_ALL[$DATE_RECHERCHE])){$NB_INTER_ALL[$DATE_RECHERCHE]=0;}
	$NB_INTER_ALL[$DATE_RECHERCHE]=$NB_INTER_ALL[$DATE_RECHERCHE]+Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$ENVIRONNEMENT,$APPLICATION,$SOGETI);
	
	if(!isset($DUREE_INTER_ALL[$DATE_RECHERCHE])){$DUREE_INTER_ALL[$DATE_RECHERCHE]=0;}
	$DUREE_INTER_ALL[$DATE_RECHERCHE]=$DUREE_INTER_ALL[$DATE_RECHERCHE]+Return_DUREE($DATE_RECHERCHE,$ANNEE,$MOIS,$ENVIRONNEMENT,$APPLICATION,$SOGETI);

} while ($tab_rq_info_date = mysql_fetch_assoc($res_rq_info_date));
$ligne= mysql_num_rows($res_rq_info_date);
if($ligne > 0) {
	mysql_data_seek($res_rq_info_date, 0);
	$tab_rq_info_date = mysql_fetch_assoc($res_rq_info_date);
}



$x = array();
$y = array();

do {
	$ANNEE=substr($tab_rq_info_date['DATE_INDICATEUR'],0,4);
	$MOIS=substr($tab_rq_info_date['DATE_INDICATEUR'],4,2);
	$DATE_RECHERCHE=$tab_rq_info_date['DATE_INDICATEUR'];
	if($NB_INTER_ALL[$DATE_RECHERCHE]==0){
		$NB=0;
	}else{
		$NB=round($DUREE_INTER_ALL[$DATE_RECHERCHE] / $NB_INTER_ALL[$DATE_RECHERCHE],2);
	}
	$y[]=$MOIS.'/'.$ANNEE;
	$x[]=$NB;

} while ($tab_rq_info_date = mysql_fetch_assoc($res_rq_info_date));
$ligne= mysql_num_rows($res_rq_info_date);
if($ligne > 0) {
	mysql_data_seek($res_rq_info_date, 0);
	$tab_rq_info_date = mysql_fetch_assoc($res_rq_info_date);
}
close_db();
$graph = new Graph(800, 300);

$graph->setAntiAliasing(TRUE);

$plot = new BarPlot($x);

$plot->setSpace(4, 4, 10, 0);
$plot->setPadding(40, 15, 10, 40);

$plot->title->set(html_entity_decode("Moyenne des temps de traitement"));
$plot->title->setFont(new TuffyBold(11));
$plot->title->border->show();
$plot->title->setBackgroundColor(new Color(255, 255, 255, 25));
$plot->title->setPadding(4, 4, 4, 4);
$plot->title->move(-20, 25);

$plot->yAxis->title->set("Durée en minutes");
$plot->yAxis->title->setFont(new TuffyBold(10));
$plot->yAxis->title->move(-4, 0);
$plot->yAxis->setTitleAlignment(LABEL_TOP);

$plot->xAxis->title->set("Date");
$plot->xAxis->title->setFont(new TuffyBold(10));
$plot->xAxis->setTitleAlignment(LABEL_RIGHT);

$plot->setBackgroundGradient(
	new LinearGradient(
		new Color(230, 230, 230),
		new Color(255, 255, 255),
		0
	)
);

$plot->barBorder->setColor(new Color(0, 0, 150, 20));

$plot->setBarGradient(
	new LinearGradient(
		new Color(150, 150, 210, 0),
		new Color(230, 230, 255, 30),
		0
	)
);

$plot->xAxis->setLabelText($y);
$plot->xAxis->label->setFont(new TuffyBold(7));

$graph->shadow->setSize(4);
$graph->shadow->setPosition(SHADOW_LEFT_TOP);
$graph->shadow->smooth(TRUE);
$graph->shadow->setColor(new Color(160, 160, 160));

$graph->add($plot);
$graph->draw();

?>