#!/usr/bin/env php
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
if (empty($_POST)) {
} else {
    if (!empty($_POST['envoyer'])) {
        $dossier = './upload/'; //dossier ou l on met le csv
        //$fichier = basename($_FILES['data']['name']);
        $fichier='darwin.csv'; // le fichier aura toujours le meme nom
        $taille_maxi = 2097152;
        $taille = filesize($_FILES['data']['tmp_name']);
        $extensions = array('.csv');
        $extension = strrchr($_FILES['data']['name'], '.');
        //Début des vérifications de sécurité...
        if (!in_array($extension, $extensions)) {
            //Si l'extension n'est pas dans le tableau
             $erreur = 'Vous devez uploader un fichier de type csv ...';
        }
        if ($taille>$taille_maxi) {
             $erreur = 'Le fichier est trop gros...';
        }
        if (!isset($erreur)) {
            //S'il n'y a pas d'erreur, on upload
                 //On formate le nom du fichier ici...
                 /*
                 $fichier = strtr($fichier, 
'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ', 
'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
                 $fichier = preg_replace('/([^.a-z0-9]+)/i', '-', $fichier);
                 */

            if (move_uploaded_file($_FILES['data']['tmp_name'], $dossier . $fichier)) {
                //Si la fonction renvoie TRUE, c'est que ça a fonctionné...
                    $erreur = 'Upload effectué avec succès !';
                    // on vide la table indicateur_extract_brut
                $sql="TRUNCATE TABLE `indicateur_darwin_extract_brut`";
                mysql_query($sql, $mysql_link) or die('Erreur SQL !'.$sql.''.mysql_error());

                $fichier= $dossier.$fichier;
                if (file_exists($fichier)) {
                    //Action si l'extraction à réussie = si le fichier existe
                    $fp = fopen($fichier, 'r'); // ouverture en lecture
                    $date_fichier=date("Ymd", filemtime($fichier));

                    $numLigne=0;

                    while (!feof($fp)) {
                        // Boucle de Lecture
                        // $ligne = fgets($fp, 4096); // changement de ligne
                        $liste  = fgetcsv($fp, 4096, ";");

// $liste = explode(';', $ligne); // lecture du séparateur ;
// $champ0 = addslashes(trim(htmlentities($liste[0]))); 
                    // Pour convertir les caractères en équivalent html et supprimer les espaces inutiles

                        if (isset($liste[0])) {
                            $DEMANDE = str_replace("&quot;", "", addslashes(trim(htmlentities($liste[0]))));
                            if (($DEMANDE != 'Demande' )) {
                                // TODO gérer la dernière ligne du rapport.
                                $DEMANDE = str_replace("&quot;", "", addslashes(trim(htmlentities($liste[0]))));
                                $CLE = str_replace("&quot;", "", addslashes(trim(htmlentities($liste[1]))));
                                $RESUME = str_replace("&quot;", "", addslashes(trim(htmlentities($liste[2]))));
                                $TYPE_DEMANDE = str_replace("&quot;", "", addslashes(trim(htmlentities($liste[3]))));
                                $PRIORITE = str_replace("&quot;", "", addslashes(trim(htmlentities($liste[4]))));
                                $ETAT = str_replace("&quot;", "", addslashes(trim(htmlentities($liste[5]))));
                                $MOTIF = str_replace("&quot;", "", addslashes(trim(htmlentities($liste[6]))));
                                $INTERVENANT = str_replace("&quot;", "", addslashes(trim(htmlentities($liste[7]))));
                                $DEMANDEUR = str_replace("&quot;", "", addslashes(trim(htmlentities($liste[8]))));
                                $CREATION1 = str_replace("&quot;", "", addslashes(trim(htmlentities($liste[9]))));
                                $MAJ = str_replace("&quot;", "", addslashes(trim(htmlentities($liste[10]))));
                                $RESOLUE = str_replace("&quot;", "", addslashes(trim(htmlentities($liste[11]))));
                                $COMPOSANT1 = str_replace("&quot;", "", addslashes(trim(htmlentities($liste[12]))));
                                $OBSERVATEUR = str_replace("&quot;", "", addslashes(trim(htmlentities($liste[13]))));
                                $IMAGE = str_replace("&quot;", "", addslashes(trim(htmlentities($liste[14]))));
                                $ESTIM_O = str_replace("&quot;", "", addslashes(trim(htmlentities($liste[15]))));
                                $ESTIM_R = str_replace("&quot;", "", addslashes(trim(htmlentities($liste[16]))));
                                $TEMP_C = str_replace("&quot;", "", addslashes(trim(htmlentities($liste[17]))));
                                $RATIO = str_replace("&quot;", "", addslashes(trim(htmlentities($liste[18]))));
                                $SS_TACHE = str_replace("&quot;", "", addslashes(trim(htmlentities($liste[19]))));
                                $D_LIEE = str_replace("&quot;", "", addslashes(trim(htmlentities($liste[20]))));
                                $DESC = str_replace("&quot;", "", nl2br(addslashes(trim(htmlentities($liste[21])))));
                                $N_SECU = str_replace("&quot;", "", addslashes(trim(htmlentities($liste[22]))));
                                $PROGRESSION = str_replace("&quot;", "", addslashes(trim(htmlentities($liste[23]))));
                                $SOM_PROGRES = str_replace("&quot;", "", addslashes(trim(htmlentities($liste[24]))));
                                $SOM_TEMP_C = str_replace("&quot;", "", addslashes(trim(htmlentities($liste[25]))));
                                $SOM_EST_R = str_replace("&quot;", "", addslashes(trim(htmlentities($liste[26]))));
                                $SOM_EST_O = str_replace("&quot;", "", addslashes(trim(htmlentities($liste[27]))));
                                $LIBELLE = str_replace("&quot;", "", addslashes(trim(htmlentities($liste[28]))));
                                $CODE_CONV = str_replace("&quot;", "", addslashes(trim(htmlentities($liste[29]))));
                                $CODE_APP = str_replace("&quot;", "", addslashes(trim(htmlentities($liste[30]))));
                                $COMPOSANT2 = str_replace("&quot;", "", addslashes(trim(htmlentities($liste[31]))));
                                $DATE_SOUHAITEE = str_replace("&quot;", "", addslashes(trim(htmlentities($liste[32]))));
                                $ENT_METIER = str_replace("&quot;", "", addslashes(trim(htmlentities($liste[33]))));
                                $ENV_PHY = str_replace("&quot;", "", addslashes(trim(htmlentities($liste[34]))));
                                $ENVIRONNEMENT = str_replace("&quot;", "", addslashes(trim(htmlentities($liste[35]))));
                                $ID_APP = str_replace("&quot;", "", addslashes(trim(htmlentities($liste[36]))));
                                $FACTU = str_replace("&quot;", "", addslashes(trim(htmlentities($liste[37]))));
                                $ANNUL = str_replace("&quot;", "", addslashes(trim(htmlentities($liste[38]))));
                                $RESOL = str_replace(
                                    "&quot;",
                                    "",
                                    addslashes(
                                        trim(
                                            htmlentities(
                                                $liste[39]
                                            )
                                        )
                                    )
                                );
                                $NIV_EXP = str_replace("&quot;", "", addslashes(trim(htmlentities($liste[40]))));
                                $NIV_SERV = str_replace("&quot;", "", addslashes(trim(htmlentities($liste[41]))));
                                $REFACTUR= str_replace("&quot;", "", addslashes(trim(htmlentities($liste[42]))));
                                $REF_INC_CLI = str_replace("&quot;", "", addslashes(trim(htmlentities($liste[43]))));
                                $SITE = str_replace("&quot;", "", addslashes(trim(htmlentities($liste[44]))));
                                $TEMPS_JOURS = str_replace("&quot;", "", addslashes(trim(htmlentities($liste[45]))));
                                $TEMPS_HEURE = str_replace("&quot;", "", addslashes(trim(htmlentities($liste[46]))));
                                $TEMPS_MINUTES = str_replace(
                                    "&quot;",
                                    "",
                                    addslashes(
                                        trim(
                                            htmlentities(
                                                $liste[47]
                                            )
                                        )
                                    )
                                );
                            }
                        }
                        $resultat = count($liste);
                        echo "<html><body><table border=\"1\"><tr>";
                        foreach ($liste as $local_var) {
                            echo  "<td>$local_var</td>" ;
                        } ;
                        echo "</tr><br>\n";

                        echo "$resultat\n<BR>";
                        if (strlen($DEMANDE) > 0) {
                            if (($DEMANDE != 'Demande' ) || ($DEMANDE =! 'G&eacute;n&eacute;r&' )) {
                            ########## Bloc D'ajout dans la base ###########
                                    $sql="
                                    INSERT INTO `indicateur_darwin_extract_brut` (
                                    `ID`,
                                    `Demande`,
                                    `Cle`,
                                    `Resume`,
                                    `Type`,
                                    `Priorite`,
                                    `Etat`,
                                    `Motif`,
                                    `Intervenant`,
                                    `Demandeur`,
                                    `Creation`,
                                    `Mise a jour`,
                                    `Resolue`,
                                    `Composants`,
                                    `Observateurs`,
                                    `Images`,
                                    `Estimation originale`,
                                    `Estimation restante`,
                                    `Temps consacre`,
                                    `Ratio du travail reel compare a estimation`,
                                    `Sous-taches`,
                                    `Demandes liees`,
                                    `Descriptif`,
                                    `Niveau de securite`,
                                    `Progression`, 
                                    `S Progres`,
                                    `S Temps consacre`,
                                    `S Estimation restante`,
                                    `S Estimation originale`, 
                                    `libelle`, `Code Convention`,
                                    `Code application`,
                                    `Composant`,
                                    `Date souhaitee`,
                                    `Entite metier`,
                                    `Env. physique`,
                                    `Environnement`,
                                    `ID application`,
                                    `Facturation`,
                                    `Annulation`,
                                    `Resolution`,
                                    `Niveau d expertise`,
                                    `Niveau de service`,
                                    `Refacturation`,
                                    `RefIncidentclient`,
                                    `Site`,
                                    `TempsJours`,
                                    `TempsHeure`,
                                    `TempsMinutes`,
                                    `ENABLE`) VALUES ( NULL,
                                    '".$DEMANDE."',
                                    '".$CLE."',
                                    '".$RESUME."',
                                    '".$TYPE_DEMANDE."',
                                    '".$PRIORITE."',
                                    '".$ETAT."',
                                    '".$MOTIF."',
                                    '".$INTERVENANT."',
                                    '".$DEMANDEUR."',
                                    '".$CREATION1."',
                                    '".$MAJ."',
                                    '".$RESOLUE."',
                                    '".$COMPOSANT1."',
                                    '".$OBSERVATEUR."',
                                    '".$IMAGE."',
                                    '".$ESTIM_O."',
                                    '".$ESTIM_R."',
                                    '".$TEMP_C."',
                                    '".$RATIO."',
                                    '".$SS_TACHE."',
                                    '".$D_LIEE."',
                                    '".$DESC."',
                                    '".$N_SECU."',
                                    '".$PROGRESSION."',
                                    '".$SOM_PROGRES."',
                                    '".$SOM_TEMP_C."',
                                    '".$SOM_EST_R."',
                                    '".$SOM_EST_O."',
                                    '".$LIBELLE."',
                                    '".$CODE_CONV."',
                                    '".$CODE_APP."',
                                    '".$COMPOSANT2."',
                                    '".$DATE_SOUHAITEE."',
                                    '".$ENT_METIER."',
                                    '".$ENV_PHY."',
                                    '".$ENVIRONNEMENT."',
                                    '".$ID_APP."',
                                    '".$FACTU."',
                                    '".$ANNUL."',
                                    '".$RESOL."',
                                    '".$NIV_EXP."',
                                    '".$NIV_SERV."',
                                    '".$REFACTUR."',
                                    '".$REF_INC_CLI."',
                                    '".$SITE."',
                                    '".$TEMPS_JOURS."',
                                    '".$TEMPS_HEURE."',
                                    '".$TEMPS_MINUTES."',
                                    0  ); ";
                                    // echo "$sql<br>";

                                     mysql_query($sql, $mysql_link) or die('Erreur SQL !'.$sql.''.mysql_error());
                            }
                        }
                    }
                         fclose($fp); // fermeture du fichier
                         // $OptTable=mysql_query("OPTIMIZE TABLE `indicateur_darwin_extract_brut`",$mysql_link);
                } else {
                    // le fichier n'existe pas
                    $erreur = 'Fichier introuvable ! Lecture stopp&eacute;e.';
                }
            } else {
                //Sinon (la fonction renvoie FALSE).
                $erreur = 'Echec de l\'upload !';
            }
        }
    }
}
echo '<center>';
// formulaire pour l'upload

$INFO_FILE='';
if (file_exists('./upload/extract_darwin.txt')) {
    $INFO_FILE='</BR>&nbsp;le fichier ./upload/extract_darwin.txt sur le serveur date du '.date(
        "d/m/Y H:i:s",
        filemtime(
            './upload/extract_darwin.txt'
        )
    ).'&nbsp';
}
    echo '
    <form method="POST" action="./test_darwin_upload.php?ITEM=indicateur_darwin_upload" enctype="multipart/form-data">
    <table class="table_inc" cellspacing="1" cellpading="0">
      <tr align="center" class="titre">
        <td align="center">&nbsp;</td>
      </tr>
      <tr align="center" class="titre">
        <td align="center">&nbsp;Upload de l\'extration DARWIN pour la gestion applicative&nbsp;'.$INFO_FILE.'</td>
      </tr>
      <tr align="center" class="titre">
        <td align="center">&nbsp;
        <!-- On limite le fichier à 2048Ko -->
        <input type="hidden" name="MAX_FILE_SIZE" value="2097152"></td>
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
if (isset($erreur)) {
      echo '
      <tr align="center" class="titre">
        <td align="center">&nbsp;'.$erreur.'&nbsp;</td>
      </tr>';
} echo '
  <tr align="center" class="titre">
    <td align="center">&nbsp;</td>
  </tr>
  <tr align="center" class="titre">
    <td align="center">&nbsp;[&nbsp;
    <a href="./index.php?ITEM=test_darwin_upload">Retour</a>&nbsp;]&nbsp;
    </td>
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
