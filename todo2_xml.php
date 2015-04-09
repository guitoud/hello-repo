<?php
require_once("../iab/cf/fonctions.php");
require_once("../iab/cf/autre_fonctions.php");
require("../iab/cf/conf_outil_icdc.php"); 

$dossier = '../iab/upload/';
$fichier='darwin_sept.xml'; 
$taille_maxi = 20971520;

$extensions = array('.xml');

$sql="DROP TABLE IF EXISTS `indicateur_darwin_extract_xml_brut`" ;
mysql_query($sql, $mysql_link) or die('Erreur SQL !'.$sql.''.mysql_error()) ;

$fichier= $dossier.$fichier;

if(file_exists($fichier)) {
	if(!$xml=simplexml_load_file('syntest.xml')){
		trigger_error('Error reading XML file',E_USER_ERROR);
	}

	foreach ($xml as $syn) {
		$w1 = $syn->w1;  
		$w2 = $syn->w2;     
	}

	$date_fichier=date("Ymd", filemtime($fichier));
	$req="
		CREATE TEMPORARY table `indicateur_darwin_extract_xml_brut` (  
				id int(20) not null auto_increment ,
				primary key (id)
			) default charset=utf8;
	";
	
	//mysql_query($sql, $mysql_link) or die('Erreur SQL !'.$sql.''.mysql_error());
	echo $w1 . "\n";
	echo $w2 . "\n";
}

?>
