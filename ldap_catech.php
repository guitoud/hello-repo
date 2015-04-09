<?php
// défini une fonction indentificationLDAP(user(char), pass(char) 
// qui renvoi $ident ( true(bool), false(bool), ident(int 8) )

function identificationLDAP($utilisateur, $pass)
{
// informations de connexions
    $ldap_host = "SWMUZG0CDD61.xxxx.xxxxx";  // votre serveur LDAP
    $ldap_port = 389;                 // votre port de serveur LDAP
    $base_dn = "OU=Utilisateurs,OU=MOE,DC=zgie,DC=ztech";
    // $base_dn = "ou=people,o=lpfuserroot,dc=zgie,dc=zgie.ztech,dc=ztech";
    $user = $utilisateur ; // on traite les information recoltées
    $password =$pass;
    $connect = ldap_connect($ldap_host, $ldap_port)
        or exit(">>Connexion au serveur LDAP echoué<<"); // connexion en anonymous

    $filter="(&(objectCategory=user))";
    //$filter="(|(sn=".$user."*)(givenname=".$user."*))";
    
    $justthese = array( "uid","ou", "sn", "givenname", "mail");
   // $justthese = array( "objectSid","ou", "sn", "givenname", "mail");

//Exécute la recherche d'un filtre indiqué sur l'annuaire
    $read = ldap_search($connect, $base_dn, $filter, $justthese) or
        exit( ">>erreur lors de la recherche<< ".ldap_error($connect) ."<br>\n"  );

//Renvoie une information complète de résultat dans une rangée
    $info = ldap_get_entries($connect, $read) or exit(">>erreur lors de la recherche<<");

    if (@$info[0]["dn"]) {
        //BIND à l'annuaire de LDAP avec RDN indiqué et mot de passe. Retours VRAIS sur le succès, FAUX sur l'échec
        @$bind = ldap_bind($connect, $info[0]["dn"], $password);
        if ($bind == false) {
            // si le BIND est FALSE, le mot de passe est erronée
            $ident='false';
            echo 'false1';
            ldap_close($connect);
            return $ident;
        } elseif ($bind == true) {
            // si le BIND est TRUE, le mot de passe est bon
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
    } else {
        $ident='false';
        echo 'false2';
        return $ident;
    }
}

// identificationLDAP("zxxxx\\xxxxxxx", "prendmespoilscestgratuit");
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
