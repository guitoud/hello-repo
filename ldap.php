<?PHP

function identificationLDAP($utilisateur,$pass)
{
// informations de connexions
$ldap_host = "158.156.35.20";  // votre serveur LDAP
$ldap_port = 3796;                 // votre port de serveur LDAP
$base_dn = "ou=people,o=lpfuserroot,dc=re,dc=cdc,dc=fr";
$user = $utilisateur ; // on traite les information recoltées
$password =$pass;
$connect = ldap_connect($ldap_host, $ldap_port) or exit(">>Connexion au serveur LDAP echoué<<"); // connexion en anonymous

$filter="(|(sn=".$user."*)(givenname=".$user."*))";
$justthese = array( "uid","ou", "sn", "givenname", "mail");

//Exécute la recherche d'un filtre indiqué sur l'annuaire
$read = ldap_search($connect,$base_dn, $filter, $justthese) or exit(">>erreur lors de la recherche<<");

//Renvoie une information complète de résultat dans une rangée
$info = ldap_get_entries($connect, $read) or exit(">>erreur lors de la recherche<<");

if(@$info[0]["dn"]){
	//BIND à l'annuaire de LDAP avec RDN indiqué et mot de passe. Retours VRAIS sur le succès, FAUX sur l'échec
	@$bind = ldap_bind($connect,$info[0]["dn"],$password);
	if ( $bind == FALSE ){ // si le BIND est FALSE, le mot de passe est erronée
		$ident='false';
		echo 'false1';
		ldap_close($connect);
		return $ident;
	}
	elseif ( $bind == TRUE ) // si le BIND est TRUE, le mot de passe est bon
	{
		// Renvoie la marque d'entrée pour la première entrée dans le résultat
		//$entry = ldap_first_entry($connect, $read);
		//la fonction est employée pour simplifier lire les attributs et les valeurs d'une entrée dans
		//la recherche résultent. La valeur de retour est un choix multidimensionnel d'attributs et de valeurs
		//$attrs = ldap_get_attributes($connect, $entry);
		ldap_close($connect);
		$ident='true';
		echo 'true';
		return $ident;
	}
}else {
	$ident='false';
	echo 'false2';
	return $ident;
}
}

identificationLDAP("dumas_g","xxxxx");
#
/*
$entry = ldap_first_entry($ds, $sr);
$attrs = ldap_get_attributes($ds, $entry);
echo $attrs["count"]." attributes held for this entry:<p>";
#
// afficher le nom des attributs
for ($i=0; $i<$attrs["count"]; $i++)
echo $attrs[$i]."<br>";
//afficher les valeurs des attributs
echo $attrs["owner"][0]."<br>";
echo $attrs["sn"][0]."<br>";
echo $attrs["cn"][0]."<br>";
*/
?>
#

?> 
