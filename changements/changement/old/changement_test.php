<?PHP
$annee=2011;
$mois=1;
$jour=1;
$CHANGEMENT_SEMAINE=date("W", mktime(12, 0, 0, $mois, $jour , $annee));
echo 'CHANGEMENT_SEMAINE : '.$CHANGEMENT_SEMAINE; 
 ?>
 