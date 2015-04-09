<?php
$baseDN = "ou=people,o=lpfuserroot";
$ldapServer = "158.156.35.20";
$ldapServerPort = "3796";
// $rdn="admin";
// $mdp="secret";
$rdn="test";
$mdp="test";
$dn = 'uid='.$rdn.','.$baseDN;

echo "Connexion au serveur <br />";
$conn=ldap_connect($ldapServer, $ldapServerPort) or die("Impossible de se connecter au serveur LDAP.");

//ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3);
// on teste : le serveur LDAP est-il trouve?
if($conn){
 echo 'Le resultat de connexion est '.$conn .'<br />';
 // Connexion authentifie
 echo 'Connexion authentifiee...<br/>';
 $bindServerLDAP=@ldap_bind($conn,$dn,$mdp); 
 //echo 'Liaison au serveur : '. ldap_error($conn).'<br/>';
 // en cas de succès de la liaison, renvoie Vrai
 if($bindServerLDAP){
  echo 'Le resultat de connexion est '.$bindServerLDAP.' <br/>';
  
  echo "Recherche suivant le filtre (sn=*) <br />";
  $justthese = array( "ou","cn", "sn", "givenname");
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
  }

 }else{
  echo 'Liaison impossible au serveur ldap ...<br/>';
 }
 echo 'Fermeture de la connexion<br/>';
 ldap_close($conn);

}else{
 echo 'connexion impossible au serveur LDAP<br/>';
}




?>
