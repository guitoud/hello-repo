<?PHP
//redirection si acces dirrect
if(substr_count($_SERVER['PHP_SELF'], 'index.php')==0){
  header("Location: ../");
  exit();
}
/*******************************************************************
   Interface Upload FAR
   Version 1.0.0  
  25/03/2010 - VGU - Creation fichier
**************************************************Guibert Vincent***
*******************************************************************/
$j=0;

$ENV =substr($_SERVER["SCRIPT_FILENAME"], 1, 1);
require_once("./cf/fonctions.php");
require("./cf/conf_outil_icdc.php"); 
$ID=0;
if(isset($_GET['ID'])){
  if(is_numeric($_GET['ID'])){
    $ID=$_GET['ID'];
  }
}
if(isset($_POST['ID'])){
  if(is_numeric($_POST['ID'])){
    $ID=$_POST['ID'];
  }
}
if($ID==0){
	echo '
        <script language="JavaScript">
        url=("./index.php?ITEM=changement_Gestion_Liste");
        window.location=url;
        </script>
        ';
}
if (empty($_POST)){
}else{
	if(!empty($_POST['envoyer'])){
	
$dossier = './upload/FAR/'; //dossier ou l on met le csv
$fichier = basename($_FILES['data']['name']);
$taille_maxi = 2097152;
$taille = filesize($_FILES['data']['tmp_name']);
$extensions = array('.doc','.docx','.pdf','.xls','.xlsx','.ods','.odt');
$extension = strrchr($_FILES['data']['name'], '.'); 
//DÈbut des vÈrifications de sÈcuritÈ...
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
          '¿¡¬√ƒ≈«»… ÀÃÕŒœ“”‘’÷Ÿ⁄€‹›‡·‚„‰ÂÁËÈÍÎÏÌÓÔÚÛÙıˆ˘˙˚¸˝ˇ', 
          'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
     $fichier = preg_replace('/([^.a-z0-9]+)/i', '-', $fichier);
     */
     $date=date("YmdHis");  
     $fichier=$date.'_'.str_replace(' ','_',$fichier); // le fichier aura le nom du fichier
     if(move_uploaded_file($_FILES['data']['tmp_name'], $dossier . $fichier)) //Si la fonction renvoie TRUE, c'est que Áa a fonctionnÈ...
     {
          $erreur = 'Upload effectuÈ avec succËs !';

	$fichier= $dossier.$fichier;
	if(file_exists($fichier)) //Action si l'extraction ‡ rÈussie = si le fichier existe
	{  
		if(isset($_SESSION['LOGIN'])){
			$LOGIN=$_SESSION['LOGIN'];
		}else{
			$LOGIN='guest';
		}

		$rq_info="
		SELECT `UTILISATEUR_ID` 
		FROM `utilisateur` 
		WHERE `LOGIN`='".$LOGIN."'
		LIMIT 1";
		$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
		$tab_rq_info = mysql_fetch_assoc($res_rq_info);
		$total_ligne_rq_info=mysql_num_rows($res_rq_info);
		if($total_ligne_rq_info!=0){
			$CHANGEMENT_FAR_UTILISATEUR_ID=$tab_rq_info['UTILISATEUR_ID'];
			mysql_free_result($res_rq_info);
		}else{
			$LOGIN='guest';
			$rq_info="
			SELECT `UTILISATEUR_ID` 
			FROM `utilisateur` 
			WHERE `LOGIN`='".$LOGIN."'
			LIMIT 1";
			$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
			$tab_rq_info = mysql_fetch_assoc($res_rq_info);
			$total_ligne_rq_info=mysql_num_rows($res_rq_info);
			if($total_ligne_rq_info!=0){
				$CHANGEMENT_FAR_UTILISATEUR_ID=$tab_rq_info['UTILISATEUR_ID'];
			}else{
				$CHANGEMENT_FAR_UTILISATEUR_ID=0;
			}
			mysql_free_result($res_rq_info);
		}

		$sql="
	        INSERT INTO `changement_far`( `CHANGEMENT_FAR_ID` , `CHANGEMENT_LISTE_ID` , `CHANGEMENT_FAR` , `CHANGEMENT_FAR_UTILISATEUR_ID` ,`ENABLE`)
	        VALUES ( NULL , '".$ID."' , '".$fichier."', '".$CHANGEMENT_FAR_UTILISATEUR_ID."','0' );";
	        mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());

	        $TABLE_SQL_SQL='changement_far';       
	        historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');

	        $rq_info="
		SELECT `CHANGEMENT_FAR_ID` 
		FROM `changement_far` 
		WHERE 
		`CHANGEMENT_FAR`='".$fichier."' AND
		`CHANGEMENT_LISTE_ID`='".$ID."'
		LIMIT 1";
		$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
		$tab_rq_info = mysql_fetch_assoc($res_rq_info);
		$total_ligne_rq_info=mysql_num_rows($res_rq_info);
		if($total_ligne_rq_info!=0){
			$CHANGEMENT_FAR_ID=$tab_rq_info['CHANGEMENT_FAR_ID'];
		}else{
			$rq_info="
			SELECT `CHANGEMENT_FAR_ID` 
			FROM `changement_far` 
			WHERE 
			`CHANGEMENT_FAR`='non' AND
			`CHANGEMENT_LISTE_ID`='".$ID."' AND
			`ENABLE`=0
			LIMIT 1";
			$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
			$tab_rq_info = mysql_fetch_assoc($res_rq_info);
			$total_ligne_rq_info=mysql_num_rows($res_rq_info);
			if($total_ligne_rq_info!=0){
				$CHANGEMENT_FAR_ID=$tab_rq_info['CHANGEMENT_FAR_ID'];
				
			}else{
				$CHANGEMENT_FAR_ID=0;
			}

		}
		mysql_free_result($res_rq_info);
		$sql="UPDATE `changement_liste` SET `CHANGEMENT_FAR_ID` = '".$CHANGEMENT_FAR_ID."' WHERE `CHANGEMENT_LISTE_ID` ='".$ID."' LIMIT 1 ;";
	        mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
	        
	        $TABLE_SQL_SQL='changement_liste';       
	        historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');

	        echo '
	        <script language="JavaScript">
	        url=("./index.php?ITEM=changement_Gestion_Liste#'.$ID.'");
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

echo '
<form method="POST" action="./index.php?ITEM=changement_Upload_FAR" enctype="multipart/form-data">
<table class="table_inc" cellspacing="1" cellpading="0">
  <tr align="center" class="titre">
    <td align="center">&nbsp;</td>
  </tr>
  <tr align="center" class="titre">
    <td align="center">&nbsp;Upload de la FAR pour la REF '.$ID.'</td>
  </tr>
  <tr align="center" class="titre">
    <td align="center">&nbsp;     <!-- On limite le fichier ‡ 2048Ko --><input type="hidden" name="MAX_FILE_SIZE" value="2097152"></td>
  </tr>
  <tr align="center" class="impair">
    <td align="center">&nbsp;Fichier : <input type="file" name="data"></td>
  </tr>
  <tr align="center" class="impair">
    <td align="center">&nbsp;<input type="submit" name="envoyer" value="Envoyer le fichier">&nbsp;</td>
  </tr>';
  if(isset($erreur)){
  echo '
  <tr align="center" class="titre">
    <td align="center">&nbsp;'.$erreur.'&nbsp;</td>
  </tr>';
  }
  echo '
  <tr align="center" class="titre">
    <td align="center">&nbsp;<input type="hidden" name="ID" value="'.$ID.'">&nbsp;</td>
  </tr>
</table>     
</form>
</center>
';
mysql_close($mysql_link);
?>
