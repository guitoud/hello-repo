<?PHP
$DATE_CREATION='31/01/11 17:34';
$jour=substr($DATE_CREATION,0,2);
$mois=substr($DATE_CREATION,3,2);
$annee=substr($DATE_CREATION,6,2);
$hh=substr($DATE_CREATION,9,2);
$mm=substr($DATE_CREATION,12,2);
echo 'DATE_CREATION : '.$DATE_CREATION.'</BR>';
echo 'annee : '.$annee.'</BR>';
echo 'mois : '.$mois.'</BR>';
echo 'jour : '.$jour.'</BR>';
echo 'hh : '.$hh.'</BR>';
echo 'mm : '.$mm.'</BR>';
?>