<?php
 
$Nb_lignes_MAX_php_BSP=15;
$Nb_lignes_MAX_php_Ress=6;
date_default_timezone_set('Europe/Paris');
$TabDomaine_php = array("IAB1","IAB2","Pilotage","IIB_Oracle","IIB_Unix","IIB_Tux","IIB_Web","Wind","Reseau","Access_Master", "Editions","Editique","DEI1","DEI2");
$TabLibDomaine_php = array("Exploitation Applicative","Exploitation Applicative","Pilotage","Ingenierie Oracle","Ingenierie Unix","Ingenierie Tuxedo","Ingenierie Websphere","Windows - Citrix","Reseau","Access Master","Atelier Editions","Editique","DEI","DEI");
$Tab_des_Mois = array("Janvier","Fevrier","Mars","Avril","Mai","Juin","Juillet","Aout","Septembre","Octobre","Novembre","Decembre");


function alert ($message)
{
    echo "<script language=\"Javascript\">";
    echo "alert (\"$message\") </script>";
}

function Recup_date_php()
{
  $nomjour = date("l"); 
  $jour = date("d"); 
  $mois = date("m"); 
  $annee = date("Y");
  
  switch ($nomjour) 
  { 
     case "Monday": 
        $nomjour="Lundi"; 
     break; 
     case "Tuesday": 
        $nomjour="Mardi"; 
     break; 
     case "Wednesday": 
        $nomjour="Mercredi"; 
     break; 
     case "Thursday": 
        $nomjour="Jeudi"; 
     break; 
     case "Friday": 
        $nomjour="Vendredi"; 
     break; 
     case "Saturday": 
        $nomjour="Samedi"; 
     break; 
     case "Sunday": 
        $nomjour="Dimanche"; 
     break; 
  }
  $TabMois = array("janvier","février","mars","avril","mai","juin","juillet","aout","septembre","octobre","novembre","décembre");
  $messageDate_php = $nomjour." ".$jour." ".$TabMois[$mois-1]." ".$annee;
  
  $date_YYYYMMJJ = $annee.$mois.$jour;
  
  return array($messageDate_php,$date_YYYYMMJJ);
}

function Return_mois_courant()
{
  $mois = date("m"); 
  $TabMois = array("janvier","fevrier","mars","avril","mai","juin","juillet","aout","septembre","octobre","novembre","decembre");
  return $TabMois[$mois-1];	
}

function Import_From_Txt($file)
{
  $tableau = array("");
  $fp=fopen($file,'r');
  $i = 0;

  while (!feof($fp)) //on parcourt toutes les lignes
  {
    $i++;
    $tableau[$i] = rtrim(fgets($fp, 255)); // lecture du contenu de la ligne
  }
  fclose($fp);
  return $tableau;
}

function Export_to_fichier($location,$file,$texte,$mode)
{
  $locfile=$location.$file;
  $fp=fopen($locfile,$mode);
  fwrite($fp,$texte."\n");
  fclose($fp);
}


function affiche_tableau_dans_select($tableau)
{
  $ligne_a_ecrire = "";
  for ($i=1; $i<sizeof($tableau); $i++)
  {
    $ligne_a_ecrire = '<option value="';
    $ligne_a_ecrire .= $tableau[$i];
    $ligne_a_ecrire .= '">';
    $ligne_a_ecrire .= $tableau[$i];
    $ligne_a_ecrire .= '</option>';
    echo $ligne_a_ecrire;
  }
}

function AfficheListeEtatApplis($nom)
{
  echo '    <select name="Status_'.$nom.'" style= "width: 35px;" onchange="document.getElementById(\'Situation\').focus();">';
  echo '      <option selected value="Vert" STYLE="color:#FFFFFF;background-color:#99FF00"></option>';
  echo '      <OPTION VALUE="Orange" STYLE="color:#FFFFFF;background-color:#FF9900"></option>';
  echo '      <OPTION VALUE="Rouge" STYLE="color:#FFFFFF;background-color:#CC0000"></option>';
  echo '    </select>';
}


function Recherche_Fichiers_dans_rep($chemin)
{
  // retourne la liste des fichiers presents dans le repertoire
 
  // interdictions de chercher dans certains répertoires, définis par leur nom
  // (valable pour tout leur contenu, fichiers + sous-répertoires)
  $MasqDo= array("secret");

  $dp=opendir($chemin); 
  $ndoss=-1; 
  $nfich=-1;
  while ($file=readdir($dp))
  {
    // masquage des répertoires interdits (et leurs sous-répertoires)
    if (in_array($file,$MasqDo)) $file="?";
 
    if ($file!="." and $file!=".." and $file!="?")
    {
      if (is_file("$chemin/$file")) $Fich[++$nfich]=$file;
    }
  }
  closedir($dp);
	
  return array($Fich, $nfich);
}


function transcrit_date($date_incident)
{
  $ListeChampsDate = preg_split("/-/",$date_incident);
  if (!isset($ListeChampsDate[2])) {
    $ListeChampsDate[2]="";
  }
  if (!isset($ListeChampsDate[1])) {
    $ListeChampsDate[1]="";
  }
  $date_transcrite = $ListeChampsDate[2].'/'.$ListeChampsDate[1].'/'.$ListeChampsDate[0];
  
  return $date_transcrite;
}


function transcrit_inverse_date($date_incident)
{ 
  $ListeChampsDate[2]='';
  $ListeChampsDate[1]='';
  $ListeChampsDate[0]='';
  $ListeChampsDate = preg_split("'/'",$date_incident);
  if (!isset($ListeChampsDate[2])) {
    $ListeChampsDate[2]="";
  }
  if (!isset($ListeChampsDate[1])) {
    $ListeChampsDate[1]="";
  }
  $date_transcrite = $ListeChampsDate[2].'-'.$ListeChampsDate[1].'-'.$ListeChampsDate[0];
  
  return $date_transcrite;
}

function transcrit_heure($heure)
{
  // en entree on a une heure de la forme HH:MM:SS
  // en sortie on a HHhMM
  $ListeChampsHeure[0]='';
  $ListeChampsHeure[1]='';
  $ListeChampsHeure = preg_split("/:/",$heure);
  if (!isset($ListeChampsHeure[1])) {
    $ListeChampsHeure[1]="";
  }
  $heure_transcrite = $ListeChampsHeure[0].'h'.$ListeChampsHeure[1];
  
  return $heure_transcrite;
}


function transcrit_inverse_heure($heure)
{
  // en entree on a une heure de la forme HHhMM
  // en sortie on a HH:MM:SS
  $ListeChampsHeure[0]='';
  $ListeChampsHeure[1]='';
  $ListeChampsHeure = preg_split("/h/",$heure);
  if (!isset($ListeChampsHeure[1])) {
    $ListeChampsHeure[1]="";
  }
  if (( $ListeChampsHeure[1] == "" ) || ( $ListeChampsHeure[1] == "0" ))
  { 
    $ListeChampsHeure[1] = "00";
  }
  $heure_transcrite = $ListeChampsHeure[0].':'.$ListeChampsHeure[1].':00';
  
  return $heure_transcrite;
}

function testDate( $value )
{
  return preg_match( '`^((((0?[1-9]|[12]\d|3[01])[\/](0?[13578]|1[02])[\.\-\/]((1[6-9]|[2-9]\d)?\d{2}))|((0?[1-9]|[12]\d|30)[\/](0?[13456789]|1[012])[\/]((1[6-9]|[2-9]\d)?\d{2}))|((0?[1-9]|1\d|2[0-8])[\/]0?2[\/]((1[6-9]|[2-9]\d)?\d{2}))|(29[\/]0?2[\/]((1[6-9]|[2-9]\d)?(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00)|00)))|(((0[1-9]|[12]\d|3[01])(0[13578]|1[02])((1[6-9]|[2-9]\d)?\d{2}))|((0[1-9]|[12]\d|30)(0[13456789]|1[012])((1[6-9]|[2-9]\d)?\d{2}))|((0[1-9]|1\d|2[0-8])02((1[6-9]|[2-9]\d)?\d{2}))|(2902((1[6-9]|[2-9]\d)?(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00)|00))))$`' , $value );
}

function testHeure( $value )
{
  return preg_match( '`^([0-1]?[0-9]|2[0-4]):([0-5][0-9]):([0-5][0-9])`' , $value );
} 


function transforme($time)
{
  /* 86400 = 3600*24 c'est à dire le nombre de secondes dans un seul jour ! donc là on vérifie si le nombre de secondes donné contient des jours ou pas */
  if ($time>=86400)
  {
    // Si c'est le cas on commence nos calculs en incluant les jours
    // on divise le nombre de seconde par 86400 (=3600*24)
    // puis on utilise la fonction floor() pour arrondir au plus petit
    
    $jour = floor($time/86400); 
    // On extrait le nombre de jours
    $reste = $time%86400;
    
    $heure = floor($reste/3600); 
    // puis le nombre d'heures
    $reste = $reste%3600;
    
    $minute = floor($reste/60); 
    // puis les minutes
    
    $seconde = $reste%60; 
    // et le reste en secondes
    
    // on n'affiche pas les secondes
    // on rassemble les résultats en forme de date
    // on n'affiche pas les secondes
    //$result = $jour.'j '.$heure.'h '.$minute.'min '.$seconde.'s';
    $result = $jour.'j '.$heure.'h '.$minute.'min ';
  }
  elseif ($time < 86400 AND $time>=3600)
  {
    // si le nombre de secondes ne contient pas de jours mais contient des heures
    // on refait la même opération sans calculer les jours
    $heure = floor($time/3600);
    $reste = $time%3600;
    
    $minute = floor($reste/60);
    
    $seconde = $reste%60;
    // on n'affiche pas les secondes
    //$result = $heure.'h '.$minute.'min '.$seconde.' s';
    $result = $heure.'h '.$minute.'min ';
  }
  elseif ($time<3600 AND $time>=60)
  {
    // si le nombre de secondes ne contient pas d'heures mais contient des minutes
    $minute = floor($time/60);
    $seconde = $time%60;
    // on n'affiche pas les secondes
    //$result = $minute.'min '.$seconde.'s';
    $result = $minute.'min ';
  }
  elseif ($time < 60)
  {
    // si le nombre de secondes ne contient aucune minutes
    // on n'affiche pas les secondes
    //$result = $time.'s';
    $result = '0';
  }
  return $result;
}

//test si année bissextile
function bissextile($annee) {
return ($annee%4==0 && $annee %100!=0 || $annee%400==0);
}


function NbJours($date_debutCP, $date_finCP)
{
  $tDeb = explode("/", $date_debutCP);
  $tFin = explode("/", $date_finCP);
  
  //mktime retourne le nombre de secondes ecoulees depuis le 01/01/1970
  
  $diff = mktime(0, 0, 0, $tFin[1], $tFin[0], $tFin[2]) -
          mktime(0, 0, 0, $tDeb[1], $tDeb[0], $tDeb[2]);
  return(round(($diff / 86400)+1));
}


//retourne le nombre de minutes entre deux heures exprimees en HH:MM:SS
function difheure($heuredeb,$heurefin)
{
   $hd=explode(":",$heuredeb);
   $hf=explode(":",$heurefin);
   $hd[0]=(int)($hd[0]);$hd[1]=(int)($hd[1]);$hd[2]=(int)($hd[2]);
   $hf[0]=(int)($hf[0]);$hf[1]=(int)($hf[1]);$hf[2]=(int)($hf[2]);
   if($hf[2]<$hd[2]){$hf[1]=$hf[1]-1;$hf[2]=$hf[2]+60;}
   if($hf[1]<$hd[1]){$hf[0]=$hf[0]-1;$hf[1]=$hf[1]+60;}
   if($hf[0]<$hd[0]){$hf[0]=$hf[0]+24;}
   
   $nb_minutes=($hf[0]-$hd[0])*60 + ($hf[1]-$hd[1]);
   
   return ($nb_minutes);
}


function format_taux_dispo($taux)
{
  // formatage d'un taux recupere en table Mysql
  if ( $taux == 100 )
  {
    return "100";
  }
  else
  {
    // on formate l'arrondi avec une virgule en séparateur
    return number_format(round($taux,2),2,',',' ');
  }

}




// ================= fonctions SQL ========================



function ouverture_db()
{  
  @mysql_connect("localhost", "mathieu", "mathieu") or die("impossible de se connecter à la base de donnée");
  mysql_select_db("portail_ope_copie_prod");
}    

function close_db()
{
  mysql_close();	
}

function Archive_Mail($UTILISATEUR_ID,$TYPE_MAIL,$DOSSIER,$FICHIER)
{
	$query = "INSERT INTO `archives_mail` (`ID` ,`DATE` ,`UTILISATEUR_ID` ,`TYPE_MAIL`,`DOSSIER`,`FICHIER` )";
	$query .= "VALUES (NULL , '".date("YmdHis")."', '".$UTILISATEUR_ID."', '".$TYPE_MAIL."', '".$DOSSIER."', '".$FICHIER."'); ";
	@mysql_query($query) or die("probleme pendant l'insertion en Base sur la table archives_mail");
	@mysql_query("OPTIMIZE TABLE `archives_mail`") or die("probleme pendant l'insertion en Base sur la table archives_mail");
}

function Recherche_SQL_nom($id)
{
  $Resultat = mysql_query("SELECT `PRENOM`, `NOM`, `EMAIL`,`COMPLEMENT` FROM `moteur_utilisateur` where `UTILISATEUR_ID`=".$id);
  $Personne_abrege = mysql_result($Resultat, 0, "PRENOM").".".mysql_result($Resultat, 0, "NOM");
  $Personne_complet = mysql_result($Resultat, 0, "PRENOM")." ".mysql_result($Resultat, 0, "NOM")." ".mysql_result($Resultat, 0, "COMPLEMENT");
  $Personne_email = mysql_result($Resultat, 0, "EMAIL");
  
  return array($Personne_abrege,$Personne_complet,$Personne_email);
}

function Recherche_SQL_nom_FULL($id)
{
  $Resultat = mysql_query("SELECT `PRENOM`, `NOM`, `EMAIL`,`EMAIL_FULL`, `COMPLEMENT` FROM `moteur_utilisateur` where `UTILISATEUR_ID`=".$id);
  $Personne_abrege = mysql_result($Resultat, 0, "PRENOM").".".mysql_result($Resultat, 0, "NOM");
  $Personne_complet = mysql_result($Resultat, 0, "PRENOM")." ".mysql_result($Resultat, 0, "NOM")." ".mysql_result($Resultat, 0, "COMPLEMENT");
  $Personne_email = mysql_result($Resultat, 0, "EMAIL");
  $Personne_email_FULL = mysql_result($Resultat, 0, "EMAIL_FULL");
  
  return array($Personne_abrege,$Personne_complet,$Personne_email,$Personne_email_FULL);
}

function autre_acces_sql(){ 
  $ACCES_TYPE="L";
  $LOGIN=$_SESSION['LOGIN'];
  $rq_acces_info="SELECT `ACCES` FROM `moteur_utilisateur` WHERE `LOGIN`='".$LOGIN."' LIMIT 1";
  $res_rq_acces_info = mysql_query($rq_acces_info) or die(mysql_error());
  $tab_rq_acces_info = mysql_fetch_assoc($res_rq_acces_info);
  $total_ligne_rq_acces_info=mysql_num_rows($res_rq_acces_info);
  if($total_ligne_rq_acces_info==1){
  	if($tab_rq_acces_info['ACCES']=='L'){
  		$ACCES_TYPE="L";
  	}else{
  		if($tab_rq_acces_info['ACCES']=='E'){
	  		$ACCES_TYPE="E";
	  	}else{
	  		$ACCES_TYPE="L";
		}
	}
  }else{
  	$ACCES_TYPE="L";
  }
  mysql_free_result($res_rq_acces_info);
  return $ACCES_TYPE;
}

function jour_ferie($date_debutCP, $date_finCP)
{
    $tDeb = explode("/", $date_debutCP);
    $tFin = explode("/", $date_finCP);
    
    $timestampEnd = mktime(0, 0, 0, $tFin[1], $tFin[0], $tFin[2]);
    $timestampStart = mktime(0, 0, 0, $tDeb[1], $tDeb[0], $tDeb[2]);

    // Initialisation de la date de début
    $jour = date("d", $timestampStart);
    $mois = date("m", $timestampStart);
    $annee = date("Y", $timestampStart);
    
    $nbFerie = 0;
    $nbFerie2 = 0;
    while ($timestampStart <= $timestampEnd)
    {
	// Calul des samedis et dimanches
	
	$jour_position = unixtojd($timestampStart);
	$jour_semaine = jddayofweek($jour_position, 0);
	
	//Samedi (6) et dimanche (0)
	if($jour_semaine == 0 || $jour_semaine == 6)
	{
	   $nbFerie++;
	}
	
	if($jour_semaine == 1||$jour_semaine == 2||$jour_semaine == 3||$jour_semaine == 4||$jour_semaine == 5)
	{
          $jour_deja_trouve = "ko";
          // Définition des dates fériées fixes
          if($jour == 1 && $mois == 1) {$nbFerie2++; $jour_deja_trouve="ok";}  // 1er janvier
          if($jour == 1 && $mois == 5) {$nbFerie2++; $jour_deja_trouve="ok";} // 1er mai
          if($jour == 8 && $mois == 5) {$nbFerie2++; $jour_deja_trouve="ok";} // 8 mai
          if($jour == 14 && $mois == 7) {$nbFerie2++; $jour_deja_trouve="ok";} // 14 juillet
          if($jour == 15 && $mois == 8) {$nbFerie2++; $jour_deja_trouve="ok";} // 15 aout
          if($jour == 1 && $mois == 11) {$nbFerie2++; $jour_deja_trouve="ok";} // 1 novembre
          if($jour == 11 && $mois == 11) {$nbFerie2++; $jour_deja_trouve="ok";} // 11 novembre
          if($jour == 25 && $mois == 12) {$nbFerie2++; $jour_deja_trouve="ok";} // 25 décembre

          // autres jours feries ou fermé CDC ou non ouvrés declares en table
          // uniquement si on n'est pas déjà tombé dessus (pour pas compter deux fois)
          if ( $jour_deja_trouve == "ko" )
          {
            $Resultat = mysql_query("SELECT * FROM `jour_non_ouvre`;");
            
            for ($m=0 ;$m < mysql_numrows($Resultat) ;$m++)
            {
              $jour_table=mysql_result($Resultat, $m, "jour");
              $mois_table=mysql_result($Resultat, $m, "mois");
              $annee_table=mysql_result($Resultat, $m, "annee");
              
              if ( ($jour == $jour_table) && ( $mois == $mois_table ) && ($annee == $annee_table))
              {
              	$nbFerie2++;
              }
            }
          }
	}
	
	// Incrémentation du nombre de jour ( on avance dans la boucle)
	$jour++;
	$timestampStart=mktime(0,0,0,$mois,$jour,$annee);
	
    }
     return $nbFerie+$nbFerie2;
}

?>
