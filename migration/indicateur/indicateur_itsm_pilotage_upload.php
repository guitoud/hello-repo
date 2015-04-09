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
$fichier = basename($_FILES['data']['name']);
$taille_maxi = 2097152;
$taille = filesize($_FILES['data']['tmp_name']);
$extensions = array('.csv');
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
     $fichier='extract_itsm_pilotage.csv'; // le fichier aura toujours le meme nom
     if(move_uploaded_file($_FILES['data']['tmp_name'], $dossier . $fichier)) //Si la fonction renvoie TRUE, c'est que ça a fonctionné...
     {
          $erreur = 'Upload effectué avec succès !';
          
	// on vide la table indicateur_extract_brut
	$sql="TRUNCATE TABLE `indicateur_pilotage_itsm_extract_brut`";
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
	      $liste = explode(';', $ligne); // lecture du séparateur ;
	      //$champ0 = addslashes(trim(htmlentities($liste[0]))); // Pour convertir les caractères en équivalent html et supprimer les espaces inutiles   
	      $REF = str_replace("&quot;", "",addslashes(trim(htmlentities($liste[0]))));
	      if(isset($liste[1])){
	      $RESUME = str_replace("&quot;", "",addslashes(trim(htmlentities($liste[1]))));
	      $STATUS = str_replace("&quot;", "",addslashes(trim(htmlentities($liste[2]))));
	      if($REF != 'Request Search Results' && $REF != 'R&eacute;f Intervention:' ){
	      if(substr_count($REF, 'Exported on:')==0){
		$rq_status_info="SELECT * FROM `indicateur_status` WHERE `INDICATEUR_STATUS_LIB`='".$STATUS."'";
		$res_rq_status_info = mysql_query($rq_status_info, $mysql_link) or die(mysql_error());
		$tab_rq_status_info = mysql_fetch_assoc($res_rq_status_info);
		$total_ligne_rq_status_info=mysql_num_rows($res_rq_status_info);
		mysql_free_result($res_rq_status_info);
		if($total_ligne_rq_status_info==0){
			$RESUME =  $RESUME.$STATUS;
			$STATUS = str_replace("&quot;", "",addslashes(trim(htmlentities($liste[3]))));
			$DATE_CREATION = str_replace("&quot;", "",addslashes(trim(htmlentities($liste[4]))));
			$ASSIGNE = str_replace("&quot;", "",addslashes(trim(htmlentities($liste[5]))));
			$DATE_FIN_RELLE = str_replace("&quot;", "",addslashes(trim(htmlentities($liste[6]))));
			$DATE_PREVUE = str_replace("&quot;", "",addslashes(trim(htmlentities($liste[7]))));
			$ENVIRONNEMENT = str_replace("&quot;", "",addslashes(trim(htmlentities($liste[8]))));
			$LAST_UPDATE = str_replace("&quot;", "",addslashes(trim(htmlentities($liste[9]))));
			$MOT_CLES = str_replace("&quot;", "",addslashes(trim(htmlentities($liste[10]))));
			$NATURE = str_replace("&quot;", "",addslashes(trim(htmlentities($liste[11]))));
			$NO_DEMANDE = str_replace("&quot;", "",addslashes(trim(htmlentities($liste[12]))));
			$TEMPS = str_replace("&quot;", "",addslashes(trim(htmlentities($liste[13]))));
		}else{
			$DATE_CREATION = str_replace("&quot;", "",addslashes(trim(htmlentities($liste[3]))));
			$ASSIGNE = str_replace("&quot;", "",addslashes(trim(htmlentities($liste[4]))));
			$DATE_FIN_RELLE = str_replace("&quot;", "",addslashes(trim(htmlentities($liste[5]))));
			$DATE_PREVUE = str_replace("&quot;", "",addslashes(trim(htmlentities($liste[6]))));
			$ENVIRONNEMENT = str_replace("&quot;", "",addslashes(trim(htmlentities($liste[7]))));
			$LAST_UPDATE = str_replace("&quot;", "",addslashes(trim(htmlentities($liste[8]))));
			$MOT_CLES = str_replace("&quot;", "",addslashes(trim(htmlentities($liste[9]))));
			$NATURE = str_replace("&quot;", "",addslashes(trim(htmlentities($liste[10]))));
			$NO_DEMANDE = str_replace("&quot;", "",addslashes(trim(htmlentities($liste[11]))));
			$TEMPS = str_replace("&quot;", "",addslashes(trim(htmlentities($liste[12]))));
		}
		}
		}
		

	      }
       
	      if (strlen($REF) > 0) {
		if($REF != 'Request Search Results' && $REF != 'R&eacute;f Intervention:' ){
	      		if(substr_count($REF, 'Exported on:')==0){
	      			//echo $REF.' - '.$RESUME.' - '.$TEMPS.'<br/>';
		
				########## Bloc D'ajout dans la base ###########
			        $sql="INSERT INTO `indicateur_extract_brut` (
			        `REF` ,`RESUME` ,`STATUS` ,`DATE_CREATION` ,`ASSIGNE` ,`DATE_FIN_RELLE` ,`DATE_PREVUE` ,`ENVIRONNEMENT` ,`LAST_UPDATE` ,`MOT_CLES` ,`NATURE` ,`NO_DEMANDE` ,`TEMPS` )
			        VALUES ( '".$REF."', '".$RESUME."', '".$STATUS."', '".$DATE_CREATION."', '".$ASSIGNE."', '".$DATE_FIN_RELLE."', '".$DATE_PREVUE."', '".$ENVIRONNEMENT."', '".$LAST_UPDATE."', '".$MOT_CLES."', '".$NATURE."', '".$NO_DEMANDE."', '".$TEMPS."');";
			        mysql_query($sql, $mysql_link) or die('Erreur SQL !'.$sql.''.mysql_error());
			}
	        }
	     }
	  }
	  fclose($fp); // fermeture du fichier
	  $OptTable=mysql_query("OPTIMIZE TABLE `indicateur_extract_brut`",$mysql_link);
	  
	  
        echo '
        <script language="JavaScript">
        url=("./index.php?ITEM=indicateur_traitement");
        window.location=url;
        </script>
        ';
	
	}else{ // le fichier n'existe pas
	  /*echo '<p>Fichier introuvable ! Lecture stopp&eacute;e.</p>';*/
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
if (file_exists('./upload/extract_odti.csv')) {
    $INFO_FILE='</BR>&nbsp;le fichier ./upload/extract_odti.csv sur le serveur date du '.date ("d/m/Y H:i:s", filemtime('./upload/extract_odti.csv')).'&nbsp';
}
echo '
<form method="POST" action="./index.php?ITEM=indicateur_upload" enctype="multipart/form-data">
<table class="table_inc" cellspacing="1" cellpading="0">
  <tr align="center" class="titre">
    <td align="center">&nbsp;</td>
  </tr>
  <tr align="center" class="titre">
    <td align="center">&nbsp;Upload de l\'extration ODTI pour la gestion applicative&nbsp;'.$INFO_FILE.'</td>
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
