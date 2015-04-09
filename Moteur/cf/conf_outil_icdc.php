<?PHP
// Serveur SQL
$host = 'localhost'; 
// Login SQL
$user = 'mathieu';
// Mot de passe SQL
$password = 'mathieu';
// Nom de la base de donnee
$database = 'portail_ope_dev';
// Redirection si erreur
$url_erreur='http://dpiddr-prod.re.cdc.fr/';
//connection à la base de donnée
$mysql_link = mysql_connect($host, $user, $password);
mysql_select_db($database,$mysql_link);
//faire aussi la modification de autre_fonctions.php (function ouverture_db())
?>

