<?php
	//redirection si acces dirrect
	// if(substr_count($_SERVER['PHP_SELF'], 'index.php')==0){
	  // header("Location: ../"); 
	  // exit(); 
	// }
	$j=0;
	$ENV=substr($_SERVER["SCRIPT_FILENAME"], 1, 1);
	require_once("../iab/cf/fonctions.php");
	require_once("../iab/cf/autre_fonctions.php");
	require("../iab/cf/conf_outil_icdc.php"); 
	if (empty($_POST)){
	} else {
		if(!empty($_POST['envoyer'])) {

			$dossier = './upload/'; //dossier ou l on met le csv
			//$fichier = basename($_FILES['data']['name']);
			$fichier='darwin.xml'; // le fichier aura toujours le meme nom
			$taille_maxi = 20971520;
			$taille = filesize($_FILES['data']['tmp_name']);
			$extensions = array('.xml');
			$extension = strrchr($_FILES['data']['name'], '.'); 
			//Début des vérifications de sécurité...
			if(!in_array($extension, $extensions)) { //Si l'extension n'est pas dans le tableau
				 $erreur = 'Vous devez uploader un fichier de type xml ...';
			}
			if($taille>$taille_maxi) {
				 $erreur = 'Le fichier est trop gros...';
			}
			if(!isset($erreur)) { //S'il n'y a pas d'erreur, on upload

				 //On formate le nom du fichier ici...
				 /*
				 $fichier = strtr($fichier, 
					  'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜİàáâãäåçèéêëìíîïğòóôõöùúûüıÿ', 
					  'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
				 $fichier = preg_replace('/([^.a-z0-9]+)/i', '-', $fichier);
				 */
					  
				 if(move_uploaded_file($_FILES['data']['tmp_name'], $dossier . $fichier)) { //Si la fonction renvoie TRUE, c'est que ça a fonctionné...
						$erreur = 'Upload effectué avec succès !';
						// on droppe la table indicateur_xml_extract_brut
					$sql="DROP TABLE `indicateur_darwin_extract_xml_brut`";
					mysql_query($sql, $mysql_link) or die('Erreur SQL !'.$sql.''.mysql_error());
					
					$fichier= $dossier.$fichier;
					if(file_exists($fichier)) { //Action si l'extraction à réussie = si le fichier existe
						$fp = fopen($fichier, 'r'); // ouverture en lecture
						$date_fichier=date("Ymd", filemtime($fichier));
				
						$numLigne=0;
						$req="
							CREATE TEMPORARY table `indicateur_darwin_extract_xml_brut` (  
    								id int(20) not null auto_increment ,
    								primary key (id)
    							) default charset=utf8;
						";
						mysql_query($sql, $mysql_link) or die('Erreur SQL !'.$sql.''.mysql_error());
						while (!feof($fp)) { // Boucle de Lecture
							// La je source  2 structures avec struct[0] index 
							// pour creation table temp
							// stuct[1] data des tables pour insert.	
							$liste=fget($fp, 4096); // On charge la ligne
							$p = xml_parser_create();
							xml_parse_into_struct($p, $ligne, $data, $index);
							print_r($index);
							xml_parser_free($p);
						
							
						}else{ // le fichier n'existe pas
							$erreur = 'Fichier introuvable ! Lecture stopp&eacute;e.';
						}
				 }
				else { //Sinon (la fonction renvoie FALSE).
						$erreur = 'Echec de l\'upload !';
				}
			}
		}
	}
	echo '
	<center>';
	// formulaire pour l'upload

	$INFO_FILE='';
	if (file_exists('./upload/extract_darwin_xml.txt')) {
		$INFO_FILE='</BR>&nbsp;le fichier ./upload/extract_darwin.txt sur le serveur date du '.date ("d/m/Y H:i:s", filemtime('./upload/extract_darwin.txt')).'&nbsp';
	}
		echo '
		<form method="POST" action="./test_darwin_xml_upload.php?ITEM=indicateur_darwin_xml_upload" enctype="application/x-www-form-urlencoded">
		<table class="table_inc" cellspacing="1" cellpading="0">
		  <tr align="center" class="titre">
			<td align="center">&nbsp;</td>
		  </tr>
		  <tr align="center" class="titre">
			<td align="center">&nbsp;Upload de l\'extration DARWIN pour la gestion applicative&nbsp;'.$INFO_FILE.'</td>
		  </tr>
		  <tr align="center" class="titre">
			<td align="center">&nbsp;     <!-- On limite le fichier à 2048KOo --><input type="hidden" name="MAX_FILE_SIZE" value="2097152"></td>
		  </tr>
		  <tr align="center" class="impair">
			<td align="center">&nbsp;Fichier : <input type="file" name="data"></td>
		  </tr>
		  <tr align="center" class="impair">
			<td align="center">&nbsp;';
		//if(acces_sql()!="L"){
			echo '<input type="submit" name="envoyer" value="Envoyer le fichier">';
		// }
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
	echo "</body></html>";
?>
