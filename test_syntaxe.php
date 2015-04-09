<?php session_start();

// fonction 
function source_ref($reference_a_sourcer) { 
	$sql="
	SELECT INDICATEUR_CAS_PARTICULIER_TYPE, 
	INDICATEUR_CAS_PARTICULIER_VALEUR FROM INDICATEUR_CAS_PARTICULIER 
	WHERE INDICATEUR_CAS_PARTICULIER_REF=$reference_a_sourcer 
	AND ENABLE=0";
	$res = mysql_query($sql,$mysql_link) or die (mysql_error());
	
	$ArrayType = array ("ACTION_NEW", "DATE_FIN_RELLE", "ENV", "ENVIRONNEMENT", "NATURE", "RESUME");

	while ($reponseRef = mysql_fetch_array() ) {
		foreach ($ArrayType as $type ) {
			if ($reponseRef['type'] == $type ) {
					echo $type . "<BR>\n";
					echo $reponseRef['valeur'] ;
			}
		}
	}
	mysql_free_result($res);
}



$mysql_server="localhost";
$mysql_user = "mathieu";
$mysql_pass = "mathieu";
$mysql_db = "portail_ope";

$mysql_link=mysql_connect($mysql_server,$mysql_user,$mysql_pass) ;

mysql_select_db($mysql_db) ;

$sql="SELECT 
INDICATEUR_CAS_PARTICULIER_TYPE as type,
INDICATEUR_CAS_PARTICULIER_VALEUR as valeur
FROM indicateur_cas_particulier 
WHERE INDICATEUR_CAS_PARTICULIER_REF is not null
AND ENABLE=0";
$res = mysql_query($sql,$mysql_link) or die (mysql_error() );

$ArrayType = array ("ACTION_NEW", "DATE_FIN_RELLE", "ENV", "ENVIRONNEMENT", "NATURE", "RESUME");

// $_sql="SELECT DISTINCT(INDICATEUR_CAS_PARTICULIER_TYPE) from indicateur_cas_particulier";
// $req_type = mysql_query($_sql,$mysql_link) or die (mysql_error());

// $ArrayType=mysql_fetch_array($req_type);

while ($reponseRef = mysql_fetch_array($res) ) {
	// scalar($reponseRef[0]) == scalar($reponseRef[1]); // Dégueulasse, ne devrais pas fonctionner dans le monde reel.
	// inserer plutôt un cas de test pour chaque valeur possible de indicateur_cas_particulier_type et rappatrier la valeur indicateur_cas_particulier_valeur
	foreach ($ArrayType as $type ) {
		if ($reponseRef['type'] == $type ) {
			echo $type . "<BR>\n";
			echo $reponseRef['valeur'] ;
		}
	}
}

mysql_free_result($res);
?>
