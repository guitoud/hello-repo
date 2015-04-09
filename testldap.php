<?php
$baseDN = "ou=people,o=lpfuserroot";
$ldapServer = "158.156.35.20";
$ldapServerPort = "3796";
// $rdn="admin";
// $mdp="secret";
$rdn="";
$mdp="";
$dn = 'uid='.$rdn.',ou=people,o=lpfuserroot';

echo "Connexion au serveur <br />";
$conn=ldap_connect($ldapServer, $ldapServerPort);

// on teste : le serveur LDAP est-il trouve?
if($conn){
 echo "Le resultat de connexion est ".$conn ."<br />";
}else
 die("connexion impossible au serveur LDAP");



/* 2etape : on effectue une liaison au serveur, ici de type "anonyme"
 * pour une recherche permise par un accès en lecture seule
 */

// On dit qu'on utilise LDAP V3, sinon la V2 par défaut est utiliisee
// et le bind ne passe pas.
//if (ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3)) {
//    print ("Utilisation de LDAPv3...<br/>");
// } else {
//    echo "Impossible d'utiliser LDAP V3\n";
//    exit;
// }


// Instruction de liaison.
// Decommenter la ligne pour une connexion authentihentifie
// ou pour une connexion anonyme.
// Connexion authentifie
print ("Connexion authentifiee...<br/>");
$bindServerLDAP=ldap_bind($conn,$dn,$mdp);
// print ("Connexion anonyme...<br/>");
//$bindServerLDAP=ldap_bind($conn);

print ("Liaison au serveur : ". ldap_error($conn)."<br/>");
// en cas de succès de la liaison, renvoie Vrai
if ($bindServerLDAP)
  print("Le resultat de connexion est $bindServerLDAP <br/>");
else
  die("Liaison impossible au serveur ldap ...");

/* 3 etape : on effectue une recherche anonyme, avec le dn de base,
 * par exemple, sur tous les noms commençant par B
 */
echo "Recherche suivant le filtre (sn=*) <br />";
$justthese = array( "ou","cn", "sn", "givenname", "mail");
$query = "sn=*";
$result=ldap_read($conn, $dn, $query,$justthese);
echo "Le resultat de la recherche est $result <br />";

echo "Le nombre d'entrees retournees est ".ldap_count_entries($conn,$result)."<p />";
echo "Lecture de ces entrees ....<p />";
$info = ldap_get_entries($conn, $result);
echo "Donnees pour ".$info["count"]." entrees:<p />";

for ($i=0; $i < $info["count"]; $i++) {
        echo "cn est : ". $info[$i]["cn"][0] ."<br />";
        echo "sn est : ". $info[$i]["sn"][0] ."<br />";
        echo "premiere entree cn : ". $info[$i]["givenname"][0] ."<br />";
        echo "premier email : ". $info[$i]["mail"][0] ."<p />";
}
/* 4 ettape : cloture de la session  */
echo "Fermeture de la connexion";
ldap_close($conn);

?>
