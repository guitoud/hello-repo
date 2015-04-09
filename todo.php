<?php
require_once("../iab/cf/fonctions.php");
require_once("../iab/cf/autre_fonctions.php");
require("../iab/cf/conf_outil_icdc.php");


// le but, a partir du champs 2 du résumé, déterminer la nature de l'action

$rq_info = "
CREATE TEMPORARY  TABLE IF NOT EXISTS INDICATEUR_DARWIN_TEMP_MATCH (
`CHAMP1` varchar(50) NOT NULL,
`CHAMP2` varchar(50) NOT NULL,
`ENABLE` int(1) NOT NULL default '0',
KEY `CHAMP1` (`CHAMP1`),
KEY CHAMP2 (`CHAMP2`)
) ENGINE=HEAP DEFAULT CHARSET=latin1;
";

$res_rq_info = mysql_query($rq_info, $mysql_link) or die (mysql_error()) ;
//$total_ligne_rq_info=mysql_num_rows($res_rq_info);
//mysql_free_result($res_rq_info);
//

$res_rq_info=mysql_query("TRUNCATE INDICATEUR_DARWIN_TEMP_MATCH;", $mysql_link);
//mysql_free_result($res_rq_info);
//

$req="SELECT `CLE`,`RESUME` from indicateur_darwin_extract_brut where resume like '%PROD-%'";
$res_rq_info = mysql_query($req, $mysql_link) or die(mysql_error());
$total_ligne = mysql_num_rows($res_rq_info);
if ($total_ligne > 0 ) {
	do { 
		//print_r($tab_rq_info);
		$cle=$tab_rq_info['CLE'];
		$resume=$tab_rq_info['RESUME'];
		$tomatch=preg_split("/[\s-]+/", $resume);
		//$action_match="$tomatch[0]"; //.$tomatch[1]";
		//if ($tomatch[1]) { echo "$tomatch[1]\n" ;}
		print_r($tomatch); #Dump de debug
		//	echo "$tomatch[2]\n";
		if ( defined($tomatch[1]) ) {
			// $req="insert into INDICATEUR_DARWIN_TEMP_MATCH values ($tomatch[1],$tomatch[2],NULL); ";
			// $res_rq_info=mysql_query($req, $mysql_link) or die (mysql_error());
			// mysql_free_result($res_rq_info);
				$req="
				select indicateur_action_info 
				from indicateur_darwin_action 
				where indicateur_action_lib like '%".$tomatch[2]."%' 
				order by INDICATEUR_ACTION_ORDRE ASC 
				limit 1 ;
				";
				$res_rq_info2 = mysql_query($req, $mysql_link) or die (mysql_error());
				$tab_rq_info2 = mysql_fetch_assoc($res_rq_info2);
				$total_ligne=mysql_num_rows($res_rq_info2);
				if ($total_ligne > 0 ) {
						do {
							$ACTION=$tab_rq_info2['indicateur_action_info'];
							echo $ACTION . "\n";
						} while ($tab_rq_info2=mysql_fetch_assoc($res_rq_info2));
				} else { 
					echo "coincoin\n";
				}
		}
		//array_push(&$global_match,$tomatch); # idée stoker dans une base temp
		
	} while ($tab_rq_info=mysql_fetch_assoc($res_rq_info));
	mysql_free_result($res_rq_info);
}
//
/* 
$req="
select CHAMP1, CHAMP2 from INDICATEUR_DARWIN_TEMP_MATCH;
";
$res_rq_info=mysql_query($req,$mysql_link) or die (mysql_error());

do {
	$tomatch[]=array("$tab_rq_info[CHAMP1]","$tab_rq_info[CHAMP2]");
	print_r($tomatch);
	if ($tomatch[1]) {
		$req="
		select indicateur_action_info 
		from indicateur_darwin_action 
		where indicateur_action_lib like '%".$tomatch[1]."%' 
		order by INDICATEUR_ACTION_ORDRE ASC 
		limit 1 ;
		";
		$res_rq_info2 = mysql_query($req, $mysql_link) or die (mysql_error());
		$tab_rq_info2 = mysql_fetch_assoc($res_rq_info2);
		$total_ligne=mysql_num_rows($res_rq_info2);
		
			if ($total_ligne > 0 ) { 
				do { 
					$ACTION=$tab_rq_info2[`indicateur_action_info`];
					echo $ACTION . "\n";
				} while ($tab_rq_info2=mysql_fetch_assoc($res_rq_info2));
			}
		mysql_free_result($res_rq_info2);
	}
	} while ($tab_rq_info=mysql_fetch_assoc($res_rq_info));

mysql_free_result($res_rq_info); */

//$req="drop table INDICATEUR_DARWIN_TEMP_MATCH ; ";
$res_rq_info=mysql_query("drop TEMPORARY table IF EXISTS INDICATEUR_DARWIN_TEMP_MATCH ;", $mysql_link) or die ( mysql_error() );

//mysql_free_result($res_rq_info);
?>
