<?php
//redirection si acces dirrect
if(substr_count($_SERVER['PHP_SELF'], 'index.php')==0){
  header("Location: ../"); 
  exit(); 
}
$j=0;
$ENV =substr($_SERVER["SCRIPT_FILENAME"], 1, 1);
require_once("./cf/fonctions.php");
require("./cf/conf_outil_icdc.php"); 
if (empty($_POST)){
}else{
	if(!empty($_POST['envoyer'])){

$dossier = './upload/'; //dossier ou l on met le csv
//$fichier = basename($_FILES['data']['name']);
$fichier='extract_qc9.txt'; // le fichier aura toujours le meme nom
$taille_maxi = 2097152;
$taille = filesize($_FILES['data']['tmp_name']);
$extensions = array('.txt');
$extension = strrchr($_FILES['data']['name'], '.'); 
//Début des vérifications de sécurité...
if(!in_array($extension, $extensions)) //Si l'extension n'est pas dans le tableau
{
     $erreur = 'Vous devez uploader un fichier de type csv ...';
}
if($taille>$taille_maxi)
{
     $erreur = 'Le fichier est trop gros...';
}
if(!isset($erreur)) //S'il n'y a pas d'erreur, on upload
{
     //On formate le nom du fichier ici...
     /*$fichier = strtr($fichier, 
          'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ', 
          'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
     $fichier = preg_replace('/([^.a-z0-9]+)/i', '-', $fichier);
     */
     
     
     if(move_uploaded_file($_FILES['data']['tmp_name'], $dossier . $fichier)) //Si la fonction renvoie TRUE, c'est que ça a fonctionné...
     {
          $erreur = 'Upload effectué avec succès !';
          
          
	// on vide la table indicateur_extract_brut
	$sql="TRUNCATE TABLE `indicateur_qc9_extract_brut`";
	mysql_query($sql, $mysql_link) or die('Erreur SQL !'.$sql.''.mysql_error());
	
	$fichier= $dossier.$fichier;
	if(file_exists($fichier)) //Action si l'extraction à réussie = si le fichier existe
	{  
	
	$fp = fopen($fichier, 'r'); // ouverture en lecture
	$date_fichier=date("Ymd", filemtime($fichier));
	
	$numLigne=0;
	
	  while (!feof($fp)) // Boucle de Lecture
	  {
		$ligne = fgets($fp, 4096); // changement de ligne
		$liste = explode('|', $ligne); // lecture du séparateur ;
		//$champ0 = addslashes(trim(htmlentities($liste[0]))); // Pour convertir les caractères en équivalent html et supprimer les espaces inutiles   
			
		if(isset($liste[1])){
			$NUMERO = str_replace("&quot;", "",addslashes(trim(htmlentities($liste[1]))));	
			if($NUMERO != 'No de demande' ){
				$STATUS = str_replace("&quot;", "",addslashes(trim(htmlentities($liste[2]))));
				$AFFECTE = str_replace("&quot;", "",addslashes(trim(htmlentities($liste[3]))));
				
				$DATE_SOUHAITEE = str_replace("&quot;", "",addslashes(trim(htmlentities($liste[4]))));
				$APPLICATION_PROCESSUS = str_replace("&quot;", "",addslashes(trim(htmlentities($liste[5]))));
				$DATE_PREVUE = str_replace("&quot;", "",addslashes(trim(htmlentities($liste[6]))));
				$DATE_TRANSMISE = str_replace("&quot;", "",addslashes(trim(htmlentities($liste[7]))));
				$DEMANDEUR = str_replace("&quot;", "",addslashes(trim(htmlentities($liste[8]))));
				$ENVIRONNEMENT = str_replace("&quot;", "",addslashes(trim(htmlentities($liste[9]))));
				$NATURE = str_replace("&quot;", "",addslashes(trim(htmlentities($liste[10]))));
				$RECETTE = str_replace("&quot;", "",addslashes(trim(htmlentities($liste[11]))));
				$TEMPS_JOURS = str_replace("&quot;", "",addslashes(trim(htmlentities($liste[12]))));
				$TEMPS_MINUTES = str_replace("&quot;", "",addslashes(trim(htmlentities($liste[13]))));

			}
		}
       
	      if (strlen($NUMERO) > 0) {
		if($NUMERO != 'No de demande' ){
      			//echo $NUMERO.' - '.$STATUS.' - '.$AFFECTE.' - '.$AFFECTE.' - '.$TEMPS_MINUTES.'<br/>';
	
			########## Bloc D'ajout dans la base ###########
		        $sql="
		        INSERT INTO `indicateur_qc9_extract_brut` (
		        `ID` , `NUMERO` , `STATUS` , `AFFECTE` , `DATE_SOUHAITEE` , `APPLICATION_PROCESSUS` , `DATE_PREVUE` , `DATE_TRANSMISE` , `DEMANDEUR` , `ENVIRONNEMENT` , `NATURE` , `RECETTE` , `TEMPS_JOURS` , `TEMPS_MINUTES` ) 
		        VALUES ( NULL , '".$NUMERO."', '".$STATUS."', '".$AFFECTE."', '".$DATE_SOUHAITEE."', '".$APPLICATION_PROCESSUS."', '".$DATE_PREVUE."', '".$DATE_TRANSMISE."', '".$DEMANDEUR."', '".$ENVIRONNEMENT."', '".$NATURE."', '".$RECETTE."', '".$TEMPS_JOURS."', '".$TEMPS_MINUTES."');";
		        mysql_query($sql, $mysql_link) or die('Erreur SQL !'.$sql.''.mysql_error());
	        }
	     }
	  }
	  fclose($fp); // fermeture du fichier
	  $OptTable=mysql_query("OPTIMIZE TABLE `indicateur_qc9_extract_brut`",$mysql_link);
	  
	  
        echo '
        <script language="JavaScript">
        url=("./index.php?ITEM=indicateur_qc9_traitement");
        window.location=url;
        </script>
        ';
	
	}else{ // le fichier n'existe pas

	  $erreur = 'Fichier introuvable ! Lecture stopp&eacute;e.';
	}
     }
     else //Sinon (la fonction renvoie FALSE).
     {
          $erreur = 'Echec de l\'upload !';
     }
}
}
}
echo '
<center>';
// formulaire pour l'upload

$INFO_FILE='';
if (file_exists('./upload/extract_qc9.txt')) {
    $INFO_FILE='</BR>&nbsp;le fichier ./upload/extract_qc9.txt sur le serveur date du '.date ("d/m/Y H:i:s", filemtime('./upload/extract_qc9.txt')).'&nbsp';
}
echo '
<form method="POST" action="./index.php?ITEM=indicateur_qc9_upload" enctype="multipart/form-data">
<table class="table_inc" cellspacing="1" cellpading="0">
  <tr align="center" class="titre">
    <td align="center">&nbsp;</td>
  </tr>
  <tr align="center" class="titre">
    <td align="center">&nbsp;Upload de l\'extration QC9 pour la gestion applicative&nbsp;'.$INFO_FILE.'</td>
  </tr>
  <tr align="center" class="titre">
    <td align="center">&nbsp;     <!-- On limite le fichier à 2048Ko --><input type="hidden" name="MAX_FILE_SIZE" value="2097152"></td>
  </tr>
  <tr align="center" class="impair">
    <td align="center">&nbsp;Fichier : <input type="file" name="data"></td>
  </tr>
  <tr align="center" class="impair">
    <td align="center">&nbsp;';
    if(acces_sql()!="L"){
    	echo '<input type="submit" name="envoyer" value="Envoyer le fichier">';
    }
    echo '&nbsp;</td>
  </tr>';
  if(isset($erreur)){
  echo '
  <tr align="center" class="titre">
    <td align="center">&nbsp;'.$erreur.'&nbsp;</td>
  </tr>';
  }
  echo '
  <tr align="center" class="titre">
    <td align="center">&nbsp;</td>
  </tr>
  <tr align="center" class="titre">
    <td align="center">&nbsp;[&nbsp;<a href="./index.php?ITEM=indicateur_menu_creation">Retour</a>&nbsp;]&nbsp;</td>
  </tr>
  <tr align="center" class="titre">
    <td align="center">&nbsp;</td>
  </tr>
</table>     
</form>
</center>
';
mysql_close($mysql_link);
?>
