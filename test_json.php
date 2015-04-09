<?php
require_once("../iab/cf/autre_fonctions.php");
require_once("../iab/cf/conf_outil_icdc.php");

$rq_info_extract="SELECT * FROM `indicateur_darwin_extract_brut`"; 
$res_rq_info_extract=mysql_query($rq_info_extract, $mysql_link) or die(mysql_error());
while ( $tab_rq_info_extract=mysql_fetch_assoc($res_rq_info_extract) ) {
	$total_ligne_rq_info_extract=mysql_num_rows($res_rq_info_extract); 
	//echo $total_ligne_rq_info_extract . "<br>\n";
	$toto=json_encode($tab_rq_info_extract  );
	echo $toto;	
}
?>
