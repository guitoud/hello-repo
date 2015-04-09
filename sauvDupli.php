<?php session_start();

if (empty($_SESSION['utilisateur']) AND empty($_SESSION['droit'])) {
        header('Location: login.php');
}
$sourceNumChg=$_GET['src'];
$dest=$_GET['dest'];
$destination = explode(",",$dest);
print("NumChg source : ".$sourceNumChg."<br>");

// on se connecte àa base
        require_once("config.php");
        $res=mysql_connect($mysql_server,$mysql_user,$mysql_pass);
        mysql_select_db($mysql_db);

foreach ($destination as $valeur)
{
// on lance la requê (mysql_query) et on impose un message d'erreur si la requê ne se passe pas bien (or die)
        $sql="SELECT * FROM Serveur WHERE id_serveur='".$valeur."'";
        $req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
	while($ServeurAppli=mysql_fetch_array($req)){
		$sql2="INSERT INTO Changement(serveur,appli,titre,changement,date,technicien,zelosIncident,zelosChgt,glpi,type,typeNu) SELECT '".$ServeurAppli['serveur']."','".$ServeurAppli['appli']."',titre,changement,date,technicien,zelosIncident,zelosChgt,glpi,type,typeNu FROM Changement WHERE numChg='".$sourceNumChg."';";
        	$req2 = mysql_query($sql2) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
	}
}
?>
