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
	$fp = fopen($fichier, 'r'); 
	$date_fichier=date("Ymd", filemtime($fichier));
	$req="
		CREATE TEMPORARY table `indicateur_darwin_extract_xml_brut` (  
				id int(20) not null auto_increment ,
				primary key (id)
			) default charset=utf8;
	";
	
	//mysql_query($sql, $mysql_link) or die('Erreur SQL !'.$sql.''.mysql_error());
	echo $fichier;
	while (!feof($fp)) {
	
		$liste=fgets($fp, 4096); 
		$p = xml_parser_create();
		xml_parse_into_struct($p, $liste, $data, $index);
		print_r($data);
		xml_parser_free($p);
		//echo $fichier;
	}
	fclose($fp);
}

?>
