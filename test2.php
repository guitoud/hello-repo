<?PHP
$ASSIGNE='lecorre_b-e';
$liste = explode('_', $ASSIGNE); // lecture du s&eacute;parateur ;
$PRENOM=strtoupper($liste[1]);
$NOM=strtoupper($liste[0]);

$liste = explode('-', $PRENOM); // lecture du s&eacute;parateur ;
$PRENOM=strtoupper($liste[0]);
echo 'NOM : '.$NOM.' - PRENOM : '.$PRENOM;
?>