<?PHP
// Serveur SQL
$host = 'localhost';
// Login SQL
$user = 'xxxxxxx';
// Mot de passe SQL
$password = 'xxxxxxx';
// Nom de la base de donnee
$database = 'portail_ope';
// Redirection si erreur
$url_erreur='http://dpiddr-prod.re.xxxxxx.fr/';
//connection � la base de donn�e
$mysql_link = mysql_connect($host, $user, $password);
mysql_select_db($database,$mysql_link);


?>

