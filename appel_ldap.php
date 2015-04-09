<?php

require_once("ldap_catech.php");

$ident = identificationLDAP("zgie\\ETA0874", "&Cher4f5");

echo "$ident <br>\n";
