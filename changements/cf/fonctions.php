<?PHP
 
$Var_max_resultat_page=20;
$Var_max_resultat_page_limit=20;
date_default_timezone_set('Europe/Paris');
$Var_NOM_SITE='CHANGEMENTS DPI';
$Var_LIB_NOM_SITE='Bienvenue sur le Portail de la Gestion des Changements de DPI-DDR';

function historique_sql_new($sql,$TABLE_SQL_SQL,$TYPE_ORDRE){ 
  $date_sql_sql=date("YmdHis");  
  $LOGIN_sql_sql=$_SESSION['LOGIN'];
  if($TYPE_ORDRE==''){
  	$TYPE_SQL="?";
  }else{
  	$TYPE_SQL=$TYPE_ORDRE;
  }

  $sql_mod=addslashes($sql);
  $sql_sql="INSERT INTO `moteur_historique` (`HISTORIQUE_ID` ,`HISTORIQUE_DATE` ,`HISTORIQUE_LOGIN` ,`HISTORIQUE_TABLE` ,`HISTORIQUE_TYPE`,`HISTORIQUE_SQL`)
  VALUES (
  NULL , '".$date_sql_sql."', '".$LOGIN_sql_sql."', '".$TABLE_SQL_SQL."', '".$TYPE_SQL."', '".$sql_mod."'
  );
  ";
  mysql_query($sql_sql) or die('Erreur SQL !'.$sql_sql.''.mysql_error());
  $sql_sql="OPTIMIZE TABLE `moteur_historique`;";
  mysql_query($sql_sql) or die('Erreur SQL !'.$sql_sql.''.mysql_error());
}


function historique_sql($sql,$TABLE_SQL_SQL){ 
  $date_sql_sql=date("YmdHis");  
  $LOGIN_sql_sql=$_SESSION['LOGIN'];
  $TYPE_SQL="?";

  $sql_mod=addslashes($sql);
  $sql_sql="INSERT INTO `moteur_historique` (`HISTORIQUE_ID` ,`HISTORIQUE_DATE` ,`HISTORIQUE_LOGIN` ,`HISTORIQUE_TABLE` ,`HISTORIQUE_TYPE`,`HISTORIQUE_SQL`)
  VALUES (
  NULL , '".$date_sql_sql."', '".$LOGIN_sql_sql."', '".$TABLE_SQL_SQL."', '".$TYPE_SQL."', '".$sql_mod."'
  );
  ";
  mysql_query($sql_sql) or die('Erreur SQL !'.$sql_sql.''.mysql_error());
  $sql_sql="OPTIMIZE TABLE `moteur_historique`;";
  mysql_query($sql_sql) or die('Erreur SQL !'.$sql_sql.''.mysql_error());
}

function moteur_trace($TRACE_CATEGORIE,$TRACE_TABLE,$TRACE_REF_ID,$TRACE_ACTION,$TRACE_ETAT){ 
	$DATE_TRACE=date("d/m/Y H:i:s");
	$DATE_TRACE_TRI=date("YmdHis");
	if(isset($_SESSION['LOGIN'])){
		$LOGIN=$_SESSION['LOGIN'];
		$rq_Selectionner_user ="SELECT `UTILISATEUR_ID`
		FROM `moteur_utilisateur` 
		WHERE `LOGIN` = '".$LOGIN."'";
		$res_rq_Selectionner_user = mysql_query($rq_Selectionner_user) or die(mysql_error());
		$tab_rq_Selectionner_user = mysql_fetch_assoc($res_rq_Selectionner_user);
		$total_ligne_Selectionner_user = mysql_num_rows($res_rq_Selectionner_user);
		if($total_ligne_Selectionner_user==0){
			$UTILISATEUR_ID=0;
		}else{
			$UTILISATEUR_ID=$tab_rq_Selectionner_user['UTILISATEUR_ID'];
		}
		mysql_free_result($res_rq_Selectionner_user);
	}else{
		$UTILISATEUR_ID=0;
	}
	if($TRACE_CATEGORIE==''){
		$TRACE_CATEGORIE='vide';	
	}
	if($TRACE_TABLE==''){
		$TRACE_TABLE='vide';	
	}
	if($TRACE_REF_ID==''){
		$TRACE_REF_ID='0';	
	}
	if($TRACE_ACTION==''){
		$TRACE_ACTION='vide';	
	}
	if($TRACE_ETAT==''){
		$TRACE_ETAT='';	
	}
	$sql="
	INSERT INTO `moteur_trace` (
	`MOTEUR_TRACE_ID`, 
	`MOTEUR_TRACE_UTILISATEUR_ID`, 
	`MOTEUR_TRACE_DATE`, 
	`MOTEUR_TRACE_DATE_TRI`,
	`MOTEUR_TRACE_CATEGORIE`,
	`MOTEUR_TRACE_TABLE`, 
	`MOTEUR_TRACE_REF_ID`, 
	`MOTEUR_TRACE_ACTION`,
	`MOTEUR_TRACE_ETAT`
	) VALUES (
	NULL, '".$UTILISATEUR_ID."', '".$DATE_TRACE."','".$DATE_TRACE_TRI."','".$TRACE_CATEGORIE."','".$TRACE_TABLE."', '".$TRACE_REF_ID."', '".$TRACE_ACTION."', '".$TRACE_ETAT."');";
	mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
	$TABLE_SQL_SQL='moteur_trace';       
		historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');
	$sql_sql="OPTIMIZE TABLE `moteur_trace`;";
        mysql_query($sql_sql) or die('Erreur SQL !'.$sql_sql.''.mysql_error());
}
function droit_page($ITEM,$LOGIN){ 
  $rq_info="
  SELECT  `moteur_role`.`ROLE`,`moteur_droit`.`DROIT`, `moteur_pages`.`ITEM`
  FROM `moteur_droit`, `moteur_pages`,`moteur_role`
  WHERE `moteur_pages`.`PAGES_ID`=`moteur_droit`.`PAGES_ID` AND
  `moteur_role`.`ROLE_ID`=`moteur_droit`.`ROLE_ID` AND
  `moteur_pages`.`ITEM`='".$ITEM."' AND
  `moteur_droit`.`ROLE_ID` IN(
  SELECT `moteur_role_utilisateur`.`ROLE_ID` 
  FROM `moteur_role_utilisateur`,`moteur_utilisateur` 
  WHERE `moteur_utilisateur`.`UTILISATEUR_ID`=`moteur_role_utilisateur`.`UTILISATEUR_ID` AND
  `moteur_utilisateur`.`LOGIN`='".$LOGIN."' AND 
  `moteur_role_utilisateur`.`ROLE_UTILISATEUR_ACCES`='0'
  ) AND
  `moteur_droit`.`DROIT`='OK' AND 
  `moteur_pages`.`ENABLE`='0'
  ";
  $res_rq_info = mysql_query($rq_info) or die(mysql_error());
  $tab_rq_info = mysql_fetch_assoc($res_rq_info);
  $total_ligne_rq_info=mysql_num_rows($res_rq_info);
  if($total_ligne_rq_info!=0){
    return 0;
  }else{
    return 1;
  }
}
function acces_sql(){ 
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

function datebdd_nomjour($Date){ // retourne le nom du jour
	$jour_test=substr($Date,0,2);
	$mois_test=substr($Date,3,2);
	$annee_test=substr($Date,6,4);
	$nomjour_test=date("l", mktime(0, 0, 0, $mois_test, $jour_test, $annee_test));
	switch ($nomjour_test)
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
  return $nomjour;
  // donne le nom du jour d une date
}

function paquesDate($annee){ // retourne le jour de paque
  $a= $annee % 4;
  $b= $annee % 7;
  $c= $annee % 19;
  $m = 24;
  $n = 5;
  $d =(19*$c + $m ) % 30;
  $e =(2*$a + 4*$b + 6*$d + $n) % 7;
  $datepaques = 22+$d+$e;
  if($datepaques>31){
    $jour = $d + $e - 9;
    $mois = 4;
    }else{
    $jour = 22 + $d + $e;
    $mois = 3;
    }
  if($d==29 && $e==6){
    $jour = 10;
    $mois = 04;
    }
  if($d==28 && $e==6){
    $jour = 18;
    $mois = 04;
    }
  return date("Ymd",mktime(0,0,0,$mois,$jour,$annee));
  // donne le jour de paque
}

function test_date_Ymd($date_rapport){// retourne 0 si jour normal 1 si jour feriee

  $jour=substr($date_rapport,6,2);
  $mois=substr($date_rapport,4,2);
  $annee=substr($date_rapport,0,4);
  $test_date=0;

  // Définition des dates fériées fixes
  if($jour == 01 && $mois == 01){$test_date=1;} // 1er janvier
  if($jour == 01 && $mois == 05){$test_date=1;} // 1er mai
  if($jour == 08 && $mois == 05){$test_date=1;} // 8 mai
  if($jour == 14 && $mois == 07){$test_date=1;} // 14 juillet
  if($jour == 15 && $mois == 08){$test_date=1;} // 15 aout
  if($jour == 01 && $mois == 11){$test_date=1;} // 1 novembre
  if($jour == 11 && $mois == 11){$test_date=1;} // 11 novembre
  if($jour == 25 && $mois == 12){$test_date=1;} // 25 décembre

  $date_paque=paquesDate($annee);
  $jour_paque=substr($date_paque,6,2);
  $mois_paque=substr($date_paque,4,2);
  $annee_paque=substr($date_paque,0,4);
  if($jour == $jour_paque && $mois == $mois_paque){$test_date=1;} // jour de paques

  $date_lundi_paque=date("Ymd",mktime(0,0,0,$mois_paque,$jour_paque+1,$annee_paque));
  $jour_lundi_paque=substr($date_lundi_paque,6,2);
  $mois_lundi_paque=substr($date_lundi_paque,4,2);
  $annee_lundi_paque=substr($date_lundi_paque,0,4);
  if($jour == $jour_lundi_paque && $mois == $mois_lundi_paque){$test_date=1;} // Lundi de Paques

  $date_Ascension=date("Ymd",mktime(0,0,0,$mois_paque,$jour_paque+39,$annee_paque));
  $jour_Ascension=substr($date_Ascension,6,2);
  $mois_Ascension=substr($date_Ascension,4,2);
  $annee_Ascension=substr($date_Ascension,0,4);
  if($jour == $jour_Ascension && $mois == $mois_Ascension){$test_date=1;} // Ascension

  $date_Pentecote=date("Ymd",mktime(0,0,0,$mois_paque,$jour_paque+49,$annee_paque));
  $jour_Pentecote=substr($date_Pentecote,6,2);
  $mois_Pentecote=substr($date_Pentecote,4,2);
  $annee_Pentecote=substr($date_Pentecote,0,4);
  if($jour == $jour_Pentecote && $mois == $mois_Pentecote){$test_date=1;} // Pentecôte

  //Ascension=jour de paque+39
  //Pentecôte=jour de paque+49
  if($test_date==1){
    $NomJour=date('D',mktime(12, 0, 0, $mois, $jour-1, $annee));
    switch ($NomJour)
    {
      case "Mon": return date("Ymd",mktime(0,0,0,$mois,$jour-1,$annee)); break;
      case "Tue": return date("Ymd",mktime(0,0,0,$mois,$jour-1,$annee)); break;
      case "Wed": return date("Ymd",mktime(0,0,0,$mois,$jour-1,$annee)); break;
      case "Thu": return date("Ymd",mktime(0,0,0,$mois,$jour-1,$annee)); break;
      case "Fri": return date("Ymd",mktime(0,0,0,$mois,$jour-1,$annee)); break;
      case "Sat": return date("Ymd",mktime(0,0,0,$mois,$jour-2,$annee)); break;
      case "Sun": return date("Ymd",mktime(0,0,0,$mois,$jour-3,$annee)); break;
    }
  }else{
    $NomJour=date('D',mktime(12, 0, 0, $mois, $jour, $annee));
    switch ($NomJour)
    {
      case "Mon": return date("Ymd",mktime(0,0,0,$mois,$jour,$annee)); break;
      case "Tue": return date("Ymd",mktime(0,0,0,$mois,$jour,$annee)); break;
      case "Wed": return date("Ymd",mktime(0,0,0,$mois,$jour,$annee)); break;
      case "Thu": return date("Ymd",mktime(0,0,0,$mois,$jour,$annee)); break;
      case "Fri": return date("Ymd",mktime(0,0,0,$mois,$jour,$annee)); break;
      case "Sat": return date("Ymd",mktime(0,0,0,$mois,$jour-1,$annee)); break;
      case "Sun": return date("Ymd",mktime(0,0,0,$mois,$jour-2,$annee)); break;
    }
  }

  
}

function test_date_dmY($date_rapport){// retourne 0 si jour normal 1 si jour feriee

  $jour=substr($date_rapport,0,2);
  $mois=substr($date_rapport,2,2);
  $annee=substr($date_rapport,4,4);
  $test_date=0;

  // Définition des dates fériées fixes
  if($jour == 01 && $mois == 01){$test_date=1;} // 1er janvier
  if($jour == 01 && $mois == 05){$test_date=1;} // 1er mai
  if($jour == 08 && $mois == 05){$test_date=1;} // 8 mai
  if($jour == 14 && $mois == 07){$test_date=1;} // 14 juillet
  if($jour == 15 && $mois == 08){$test_date=1;} // 15 aout
  if($jour == 01 && $mois == 11){$test_date=1;} // 1 novembre
  if($jour == 11 && $mois == 11){$test_date=1;} // 11 novembre
  if($jour == 25 && $mois == 12){$test_date=1;} // 25 décembre

  $date_paque=paquesDate($annee);
  $jour_paque=substr($date_paque,6,2);
  $mois_paque=substr($date_paque,4,2);
  $annee_paque=substr($date_paque,0,4);
  if($jour == $jour_paque && $mois == $mois_paque){$test_date=1;} // jour de paques

  $date_lundi_paque=date("Ymd",mktime(0,0,0,$mois_paque,$jour_paque+1,$annee_paque));
  $jour_lundi_paque=substr($date_lundi_paque,6,2);
  $mois_lundi_paque=substr($date_lundi_paque,4,2);
  $annee_lundi_paque=substr($date_lundi_paque,0,4);
  if($jour == $jour_lundi_paque && $mois == $mois_lundi_paque){$test_date=1;} // Lundi de Paques

  $date_Ascension=date("Ymd",mktime(0,0,0,$mois_paque,$jour_paque+39,$annee_paque));
  $jour_Ascension=substr($date_Ascension,6,2);
  $mois_Ascension=substr($date_Ascension,4,2);
  $annee_Ascension=substr($date_Ascension,0,4);
  if($jour == $jour_Ascension && $mois == $mois_Ascension){$test_date=1;} // Ascension

  $date_Pentecote=date("Ymd",mktime(0,0,0,$mois_paque,$jour_paque+49,$annee_paque));
  $jour_Pentecote=substr($date_Pentecote,6,2);
  $mois_Pentecote=substr($date_Pentecote,4,2);
  $annee_Pentecote=substr($date_Pentecote,0,4);
  if($jour == $jour_Pentecote && $mois == $mois_Pentecote){$test_date=1;} // Pentecôte

  //Ascension=jour de paque+39
  //Pentecôte=jour de paque+49
  if($test_date==1){
    $NomJour=date('D',mktime(12, 0, 0, $mois, $jour-1, $annee));
    switch ($NomJour)
    {
      case "Mon": return date("dmY",mktime(0,0,0,$mois,$jour-1,$annee)); break;
      case "Tue": return date("dmY",mktime(0,0,0,$mois,$jour-1,$annee)); break;
      case "Wed": return date("dmY",mktime(0,0,0,$mois,$jour-1,$annee)); break;
      case "Thu": return date("dmY",mktime(0,0,0,$mois,$jour-1,$annee)); break;
      case "Fri": return date("dmY",mktime(0,0,0,$mois,$jour-1,$annee)); break;
      case "Sat": return date("dmY",mktime(0,0,0,$mois,$jour-2,$annee)); break;
      case "Sun": return date("dmY",mktime(0,0,0,$mois,$jour-3,$annee)); break;
    }
  }else{
    $NomJour=date('D',mktime(12, 0, 0, $mois, $jour, $annee));
    switch ($NomJour)
    {
      case "Mon": return date("dmY",mktime(0,0,0,$mois,$jour,$annee)); break;
      case "Tue": return date("dmY",mktime(0,0,0,$mois,$jour,$annee)); break;
      case "Wed": return date("dmY",mktime(0,0,0,$mois,$jour,$annee)); break;
      case "Thu": return date("dmY",mktime(0,0,0,$mois,$jour,$annee)); break;
      case "Fri": return date("dmY",mktime(0,0,0,$mois,$jour,$annee)); break;
      case "Sat": return date("dmY",mktime(0,0,0,$mois,$jour-1,$annee)); break;
      case "Sun": return date("dmY",mktime(0,0,0,$mois,$jour-2,$annee)); break;
    }
  } 
}

function convert_datetime_to_date($datetime){// change le format datetime sql en une date de type : JJ/MM/AAAA XX:XX:XX

  $jour=substr($datetime,8,2);
  $mois=substr($datetime,5,2);
  $annee=substr($datetime,0,4);
  $heure=substr($datetime,11,8);
  $date=$jour.'/'.$mois.'/'.$annee.' '.$heure;
  return $date;
}

# -= Fonction fait la Pagination du résultat d'une requete. (si l'on a des variables en get) =-

function makeListLink($nombre_total,$Var_max_resultat_page,$link,$border){
	/*	$nombre_total         => Nombre total d'éléments à afficher sur toutes les pages
			Exemple : 150 éléments
		$Var_max_resultat_page     => Nombre d'éléments par page
			Exemple : 10 éléments par page (=> 15 pages)
		$link                 => Adresse de la page qui liste
			Exemple : liste.php?mavariable=mavaleur
		$border                => Nombre de liens autour du lien de la page actuelle
			Exemple : 3 (=> 1 ... 5 6 7 [8] 9 10 11 ... 25)
			Exemple : 2 (=> 1 ... 6 7 [8] 9 10 ... 25)
			Exemple : 1 (=> 1 ... 7 [8] 9 ... 25)
			 
		 Synthaxe de la requete à mettre sur la page qui liste :
		 =======================================================
		 
		 $Var_max_resultat_page = <Valeur mise pour $Var_max_resultat_page dans la fonction>;
		 if((!isset($_GET['begin'])) || (!is_numeric($_GET['begin']))) {
			// Si la variable begin a une valeur étrange on commence au début de la liste
			 $requete = "SELECT mes_champs FROM ma_table ORDER BY mon_champ ASC LIMIT ".$Var_max_resultat_page."";
		 } else {
			 $requete = "SELECT mes_champs FROM ma_table ORDER BY mon_champ ASC LIMIT ".$_GET['begin'].",".$Var_max_resultat_page."";
		 }
	 */
	
	// Calcul du nombre de pages necessaires
	// Ceil = fonction arrondir au nombre supérieur
	// Ex : 4,6 pages necessaire => 5 pages affichées
	$nombre_page_necessaire = ceil($nombre_total / $Var_max_resultat_page);
	// Calcul de la page actuelle
	if (isset($_GET['begin'])){}else{
		$_GET['begin']=0;
	}
	$page_actuelle = ($_GET['begin'] / $Var_max_resultat_page) +1;
	$first_link = 1;
	echo "<strong>Pages : </strong> ";
	if($page_actuelle!=1){
		// Si la page actuelle n'est pas la première, on peut afficher la premiere page'
		// Première page
		echo "<a href='".$link."&begin=0'>1</a>";
		$first_link = 0;
	}    
	if($page_actuelle > $border +2 ) {
		// Si la page actuelle est plus loin que la bordure et la première page, on met des ...
		echo "...";
	}
	for($y=0;$y<=($border-1);$y++) {
		$z = $border - $y;
		if($page_actuelle > ($z + 1)){
			// Affichage des pages précédentes selon le $border défini
			if($first_link != 1) { echo " "; }
			$page_precedente = $page_actuelle - $z;
			$page_begin = ($page_precedente - 1) * $Var_max_resultat_page;
			echo "<a href='".$link."&begin=".$page_begin."'>".$page_precedente."</a>";
			$first_link = 0;
		}
	}
	 
	// Affichage page actuelle
	if($first_link != 1) { echo " "; }
	$page_begin = ($page_actuelle - 1) * $Var_max_resultat_page;
	echo "<strong>[".$page_actuelle."]</strong>";
	$first_link = 0;
	 
	for($y=1;$y<=$border;$y++) {
		$z = $border - $y;
		if($page_actuelle < ($nombre_page_necessaire-$y)) {
			// Affichage des pages suivantes selon le $border défini
			if($first_link != 1) { echo " "; }
			$page_suivante = $page_actuelle + $y;
			$page_begin = ($page_suivante - 1) * $Var_max_resultat_page;
			echo "<a href='".$link."&begin=".$page_begin."'>".$page_suivante."</a>";
			$first_link = 0;
		}
	}
	 
	if($page_actuelle < ($nombre_page_necessaire - ($border +1))) {
		// Si la page actuelle et sa bordure n'atteignent pas la dernière page, on met des ...
		echo "...";
	}
	if($page_actuelle != $nombre_page_necessaire ) {
		// La page actuelle est avant la derniere page, donc on affiche la derniere page
		// Dernière page
		if($first_link != 1) { echo " "; }
		$page_begin = ($nombre_page_necessaire - 1) * $Var_max_resultat_page;
		echo "<a href='".$link."&begin=".$page_begin."'>".$nombre_page_necessaire."</a>";
	}
}

# -= Fonction fait la Pagination du résultat d'une requete. (si l'on n'a pas de variables en get) =-

function makeListLinkno($nombre_total,$Var_max_resultat_page,$link,$border){
	/*	$nombre_total         => Nombre total d'éléments à afficher sur toutes les pages
			Exemple : 150 éléments
		$Var_max_resultat_page     => Nombre d'éléments par page
			Exemple : 10 éléments par page (=> 15 pages)
		$link                 => Adresse de la page qui liste
			Exemple : liste.php?mavariable=mavaleur
		$border                => Nombre de liens autour du lien de la page actuelle
			Exemple : 3 (=> 1 ... 5 6 7 [8] 9 10 11 ... 25)
			Exemple : 2 (=> 1 ... 6 7 [8] 9 10 ... 25)
			Exemple : 1 (=> 1 ... 7 [8] 9 ... 25)
			 
		 Synthaxe de la requete à mettre sur la page qui liste :
		 =======================================================
		 
		 $Var_max_resultat_page = <Valeur mise pour $Var_max_resultat_page dans la fonction>;
		 if((!isset($_GET['begin'])) || (!is_numeric($_GET['begin']))) {
			// Si la variable begin a une valeur étrange on commence au début de la liste
			 $requete = "SELECT mes_champs FROM ma_table ORDER BY mon_champ ASC LIMIT ".$Var_max_resultat_page."";
		 } else {
			 $requete = "SELECT mes_champs FROM ma_table ORDER BY mon_champ ASC LIMIT ".$_GET['begin'].",".$Var_max_resultat_page."";
		 }
	 */
	
	// Calcul du nombre de pages necessaires
	// Ceil = fonction arrondir au nombre supérieur
	// Ex : 4,6 pages necessaire => 5 pages affichées
	$nombre_page_necessaire = ceil($nombre_total / $Var_max_resultat_page);
	// Calcul de la page actuelle
	if (isset($_GET['begin'])){}else{
		$_GET['begin']=0;
	}
	$page_actuelle = ($_GET['begin'] / $Var_max_resultat_page) +1;
	$first_link = 1;
	echo "<strong>Pages : </strong> ";
	if($page_actuelle!=1){
		// Si la page actuelle n'est pas la première, on peut afficher la premiere page'
		// Première page
		echo "<a href='".$link."?begin=0'>1</a>";
		$first_link = 0;
	}    
	if($page_actuelle > $border +2 ) {
		// Si la page actuelle est plus loin que la bordure et la première page, on met des ...
		echo "...";
	}
	for($y=0;$y<=($border-1);$y++) {
		$z = $border - $y;
		if($page_actuelle > ($z + 1)){
			// Affichage des pages précédentes selon le $border défini
			if($first_link != 1) { echo " "; }
			$page_precedente = $page_actuelle - $z;
			$page_begin = ($page_precedente - 1) * $Var_max_resultat_page;
			echo "<a href='".$link."?begin=".$page_begin."'>".$page_precedente."</a>";
			$first_link = 0;
		}
	}
	 
	// Affichage page actuelle
	if($first_link != 1) { echo " "; }
	$page_begin = ($page_actuelle - 1) * $Var_max_resultat_page;
	echo "<strong>[".$page_actuelle."]</strong>";
	$first_link = 0;
	 
	for($y=1;$y<=$border;$y++) {
		$z = $border - $y;
		if($page_actuelle < ($nombre_page_necessaire-$y)) {
			// Affichage des pages suivantes selon le $border défini
			if($first_link != 1) { echo " "; }
			$page_suivante = $page_actuelle + $y;
			$page_begin = ($page_suivante - 1) * $Var_max_resultat_page;
			echo "<a href='".$link."?begin=".$page_begin."'>".$page_suivante."</a>";
			$first_link = 0;
		}
	}
	 
	if($page_actuelle < ($nombre_page_necessaire - ($border +1))) {
		// Si la page actuelle et sa bordure n'atteignent pas la dernière page, on met des ...
		echo "...";
	}
	if($page_actuelle != $nombre_page_necessaire ) {
		// La page actuelle est avant la derniere page, donc on affiche la derniere page
		// Dernière page
		if($first_link != 1) { echo " "; }
		$page_begin = ($nombre_page_necessaire - 1) * $Var_max_resultat_page;
		echo "<a href='".$link."?begin=".$page_begin."'>".$nombre_page_necessaire."</a>";
	}
}

?>