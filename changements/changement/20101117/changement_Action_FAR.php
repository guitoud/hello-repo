<?PHP
//redirection si acces dirrect
if(substr_count($_SERVER['PHP_SELF'], 'index.php')==0){
  header("Location: ../");
  exit(); 
}
/*******************************************************************
   Interface FAR action
   Version 1.0.0
  05/10/2010 - VGU - Creation fichier
**************************************************Guibert Vincent***
*******************************************************************/

require("./cf/conf_outil_icdc.php"); 
require_once("./cf/fonctions.php");
require_once("./cf/autre_fonctions.php");
 
require_once('./changement/changement_Conf_Mail.php') ; 

$ENV =substr($_SERVER["SCRIPT_FILENAME"], 1, 1);

$j=0;

$STOP=0;
$nb_modif=0;
$DATE_MODIFICATION=date("d/m/Y H:i:s");
$CHANGEMENT_FAR_INSCRIPTION="Non";
if(isset($_GET['action'])){
  $action=$_GET['action'];
}else{
  $action='Ajout';
}

$tab_var=$_POST;
if(isset($_GET['ID'])){
  if(is_numeric($_GET['ID'])){
    $ID=$_GET['ID'];
  }
}
//echo 'ID = '.$ID.'<br>';
if(isset($tab_var['ID'])){
  if(is_numeric($tab_var['ID'])){
    $ID=$tab_var['ID'];
  }
}

if(isset($tab_var['action'])){
  $action=$tab_var['action'];
}
if($action=='Info'){
	$readonly_var='readonly="readonly"';
	$disabled_var='DISABLED';
}else{
	$readonly_var='';
	$disabled_var='';
}
if(isset($_SESSION['LOGIN'])){
	$LOGIN=$_SESSION['LOGIN'];
	$rq_Selectionner_user ="
	SELECT `UTILISATEUR_ID`,`LOGIN`, `NOM`, `PRENOM` 
	FROM `moteur_utilisateur` 
	WHERE `LOGIN` = '".$LOGIN."'";
	$res_rq_Selectionner_user = mysql_query($rq_Selectionner_user, $mysql_link) or die(mysql_error());
	$tab_rq_Selectionner_user = mysql_fetch_assoc($res_rq_Selectionner_user);
	$total_ligne_Selectionner_user = mysql_num_rows($res_rq_Selectionner_user);
	if($total_ligne_Selectionner_user==0){
		$NOM='';
		$UTILISATEUR_ID=0;
		$PRENOM='';
		$LOGIN='';
	}else{
		$NOM=$tab_rq_Selectionner_user['NOM'];
		$UTILISATEUR_ID=$tab_rq_Selectionner_user['UTILISATEUR_ID'];
		$PRENOM=$tab_rq_Selectionner_user['PRENOM'];
		$LOGIN=$tab_rq_Selectionner_user['LOGIN'];
	}
	mysql_free_result($res_rq_Selectionner_user);
	$rq_info="
	SELECT `moteur_role_utilisateur`.`ROLE_UTILISATEUR_ACCES` 
	FROM `moteur_role_utilisateur`,`moteur_utilisateur`,`moteur_role`
	WHERE 
	`moteur_role_utilisateur`.`UTILISATEUR_ID`=`moteur_utilisateur`.`UTILISATEUR_ID` AND
	`moteur_role_utilisateur`.`ROLE_ID`=`moteur_role`.`ROLE_ID` AND
	`moteur_utilisateur`.`LOGIN`='".$LOGIN."' AND
	(`moteur_role`.`ROLE`='ROOT' OR `moteur_role`.`ROLE`='ADMIN-CHANGEMENT' ) AND
	`moteur_role_utilisateur`.`ROLE_UTILISATEUR_ACCES`=0
	LIMIT 1";
	$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
	$tab_rq_info = mysql_fetch_assoc($res_rq_info);
	$total_ligne_rq_info=mysql_num_rows($res_rq_info); 
	if($total_ligne_rq_info==0){
		$ROLE=1;
	}else{
		$ROLE=$tab_rq_info['ROLE_UTILISATEUR_ACCES'];
	}
}else{
	$NOM='';
	$UTILISATEUR_ID=0;
	$PRENOM='';
	$LOGIN='';
	$ROLE=1;
}

$DATE_DU_JOUR=date("Ymd");
if(empty($tab_var['btn'])){
	
}else{

  # Cas Ajouter
  if($tab_var['btn']=="Ajouter"){
	$ID=$tab_var['ID'];
	$CHANGEMENT_ID=$ID;
	$rq_info_far="
	SELECT * 
	FROM `changement_far_config`
	WHERE `ENABLE` =0
	";
	$res_rq_info_far = mysql_query($rq_info_far, $mysql_link) or die(mysql_error());
	$tab_rq_info_far = mysql_fetch_assoc($res_rq_info_far);
	$total_ligne_rq_info_far=mysql_num_rows($res_rq_info_far);
	$rq_date_deb_info="
	SELECT `CHANGEMENT_LISTE_DATE_DEBUT`
	FROM `changement_liste`
	WHERE 
	`changement_liste`.`CHANGEMENT_LISTE_ID` ='".$ID."' AND
	`changement_liste`.`ENABLE` = '0'
	";
	$res_rq_date_deb_info = mysql_query($rq_date_deb_info, $mysql_link) or die(mysql_error());
	$tab_rq_date_deb_info = mysql_fetch_assoc($res_rq_date_deb_info);
	$total_ligne_rq_date_deb_info=mysql_num_rows($res_rq_date_deb_info);
	$Date_Inter_Debut_bdd=0;
	$Date_Inter_Debut_bdd=$tab_rq_date_deb_info['CHANGEMENT_LISTE_DATE_DEBUT'];
	mysql_free_result($res_rq_date_deb_info);
	if( $ENV == "x" ){	 	
		if($Date_Inter_Debut_bdd < $DATE_DU_JOUR){
			$STOP=1;
			$DATE_LISTE=2;
		}
	}
	$rq_info_config="
	SELECT  
	`CHANGEMENT_FAR_CONFIG_ID`,
	`CHANGEMENT_FAR_CONFIG_LIB`, 
	`CHANGEMENT_FAR_CONFIG_TYPE`,
	`CHANGEMENT_FAR_CONFIG_TABLE`,
	`CHANGEMENT_FAR_CONFIG_OBLIGATOIRE` 
	FROM `changement_far_config` 
	WHERE `ENABLE`='0'";
	$res_rq_info_config = mysql_query($rq_info_config, $mysql_link) or die(mysql_error());
	$tab_rq_info_config = mysql_fetch_assoc($res_rq_info_config);
	$total_ligne_rq_info_config=mysql_num_rows($res_rq_info_config);
	
	if($total_ligne_rq_info_config!=0){
	do {
		
	$CHANGEMENT_FAR_CONFIG_ID=$tab_rq_info_config['CHANGEMENT_FAR_CONFIG_ID'];
	$CHANGEMENT_FAR_CONFIG_LIB=$tab_rq_info_config['CHANGEMENT_FAR_CONFIG_LIB'];
	$CHANGEMENT_FAR_CONFIG_TYPE=$tab_rq_info_config['CHANGEMENT_FAR_CONFIG_TYPE'];
	$CHANGEMENT_FAR_CONFIG_OBLIGATOIRE=$tab_rq_info_config['CHANGEMENT_FAR_CONFIG_OBLIGATOIRE'];
	if(isset($tab_rq_info_config['CHANGEMENT_FAR_INFO_AUTRE_ID'])){
		$CHANGEMENT_FAR_INFO_AUTRE_ID=$tab_rq_info_config['CHANGEMENT_FAR_INFO_AUTRE_ID'];
	}
	if(isset($tab_rq_info_config['CHANGEMENT_FAR_INFO_LIB'])){
		$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]=$tab_rq_info_config['CHANGEMENT_FAR_INFO_LIB'];
	}
	if(!isset($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID])){$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]='';}
	$LISTE_CONFIG='CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID;
	
	switch ($CHANGEMENT_FAR_CONFIG_TYPE)
	{
	case "liste": 
		$CHANGEMENT_FAR_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_FAR_CONFIG_TABLE'];
		$CHANGEMENT_FAR_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE);
		$LISTE_CONFIG='CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID;
		$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		$CHANGEMENT_FAR_CONFIG_STOP[$CHANGEMENT_FAR_CONFIG_ID]=0;
		if($CHANGEMENT_FAR_CONFIG_OBLIGATOIRE=='oui'){
			if($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]=='0'){
				$CHANGEMENT_FAR_CONFIG_STOP[$CHANGEMENT_FAR_CONFIG_ID]=1;
				$STOP=1;
			}
		}
	break;
	case "checkbox": 
		$CHANGEMENT_FAR_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_FAR_CONFIG_TABLE'];
		$CHANGEMENT_FAR_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE);
		$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]='0';
		$rq_info_config_table="
		SELECT  *
		FROM `".$CHANGEMENT_FAR_CONFIG_TABLE."`
		WHERE 
		`ENABLE`='0'
		AND `".$CHANGEMENT_FAR_CONFIG_TABLE_TRI."`!='vide'
		ORDER BY `".$CHANGEMENT_FAR_CONFIG_TABLE_TRI."`";
		$res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
		$tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
		$total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);
		if($total_ligne_rq_info_config_table!=0){
		do {
			$ID_SQL=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE).'_ID';
			$ID_LIB=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE);
			$COM_SQL=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE).'_COM';
			$CHANGEMENT_FAR_CONFIG_TABLE_ID=$tab_rq_info_config_table[$ID_SQL];
			$CHANGEMENT_FAR_CONFIG_TABLE_LIB=$tab_rq_info_config_table[$ID_LIB];
			if(isset($tab_rq_info_config_table[$COM_SQL])){
				$CHANGEMENT_FAR_CONFIG_TABLE_COM=$tab_rq_info_config_table[$COM_SQL];	
			}else{
				$CHANGEMENT_FAR_CONFIG_TABLE_COM='non';
			}
			$LISTE_CONFIG='CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID.'_'.$CHANGEMENT_FAR_CONFIG_TABLE_ID;
			$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE=$CHANGEMENT_FAR_CONFIG_ID.'_'.$CHANGEMENT_FAR_CONFIG_TABLE_ID;
			if(!isset($NB_checkbox)){$NB_checkbox=0;}
			if(isset($tab_var[$LISTE_CONFIG])){
				$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
				$NB_checkbox=$NB_checkbox+1;
			}else{
				$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE]='off';
			}
			
			if($CHANGEMENT_FAR_CONFIG_TABLE_COM=='oui'){
				$LISTE_CONFIG='CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID.'_'.$CHANGEMENT_FAR_CONFIG_TABLE_ID.'_COM';
				$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE_COM=$CHANGEMENT_FAR_CONFIG_ID.'_'.$CHANGEMENT_FAR_CONFIG_TABLE_ID.'_COM';
				if(isset($tab_var[$LISTE_CONFIG])){
					$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE_COM]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
				}else{
					$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE_COM]='';
				}
				if($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE]=='on'){
					if($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE_COM]==''){
						$STOP=1;	
						$CHANGEMENT_FAR_CONFIG_STOP_COM[$CHANGEMENT_FAR_CONFIG_ID]=1;
					}
				}
			}
			
		
		} while ($tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table));
		$ligne= mysql_num_rows($res_rq_info_config_table);
		if($ligne > 0) {
			mysql_data_seek($res_rq_info_config_table, 0);
			$tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
		}
		$CHANGEMENT_FAR_CONFIG_STOP[$CHANGEMENT_FAR_CONFIG_ID]=0;
			if($CHANGEMENT_FAR_CONFIG_OBLIGATOIRE=='oui'){
				if($NB_checkbox==0){
					$CHANGEMENT_FAR_CONFIG_STOP[$CHANGEMENT_FAR_CONFIG_ID]=1;
					$STOP=1;
				}
			}
		}
		mysql_free_result($res_rq_info_config_table);
	break;
	
	case "checkbox_horizontal": 
		$CHANGEMENT_FAR_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_FAR_CONFIG_TABLE'];
		$CHANGEMENT_FAR_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE);
		$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]='0';
		$rq_info_config_table="
		SELECT  *
		FROM `".$CHANGEMENT_FAR_CONFIG_TABLE."`
		WHERE 
		`ENABLE`='0'
		AND `".$CHANGEMENT_FAR_CONFIG_TABLE_TRI."`!='vide'
		ORDER BY `".$CHANGEMENT_FAR_CONFIG_TABLE_TRI."`";
		$res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
		$tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
		$total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);
		if($total_ligne_rq_info_config_table!=0){
		do {
			$ID_SQL=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE).'_ID';
			$ID_LIB=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE);
			$COM_SQL=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE).'_COM';
			$CHANGEMENT_FAR_CONFIG_TABLE_ID=$tab_rq_info_config_table[$ID_SQL];
			$CHANGEMENT_FAR_CONFIG_TABLE_LIB=$tab_rq_info_config_table[$ID_LIB];
			if(isset($tab_rq_info_config_table[$COM_SQL])){
				$CHANGEMENT_FAR_CONFIG_TABLE_COM=$tab_rq_info_config_table[$COM_SQL];	
			}else{
				$CHANGEMENT_FAR_CONFIG_TABLE_COM='non';
			}
			$LISTE_CONFIG='CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID.'_'.$CHANGEMENT_FAR_CONFIG_TABLE_ID;
			$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE=$CHANGEMENT_FAR_CONFIG_ID.'_'.$CHANGEMENT_FAR_CONFIG_TABLE_ID;
			if(!isset($NB_checkbox)){$NB_checkbox=0;}
			if(isset($tab_var[$LISTE_CONFIG])){
				$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
				$NB_checkbox=$NB_checkbox+1;
			}else{
				$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE]='off';
			}
			
			if($CHANGEMENT_FAR_CONFIG_TABLE_COM=='oui'){
				$LISTE_CONFIG='CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID.'_'.$CHANGEMENT_FAR_CONFIG_TABLE_ID.'_COM';
				$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE_COM=$CHANGEMENT_FAR_CONFIG_ID.'_'.$CHANGEMENT_FAR_CONFIG_TABLE_ID.'_COM';
				if(isset($tab_var[$LISTE_CONFIG])){
					$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE_COM]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
				}else{
					$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE_COM]='';
				}
				if($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE]=='on'){
					if($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE_COM]==''){
						$STOP=1;	
						$CHANGEMENT_FAR_CONFIG_STOP_COM[$CHANGEMENT_FAR_CONFIG_ID]=1;
					}
				}
			}
			
		
		} while ($tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table));
		$ligne= mysql_num_rows($res_rq_info_config_table);
		if($ligne > 0) {
			mysql_data_seek($res_rq_info_config_table, 0);
			$tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
		}
		$CHANGEMENT_FAR_CONFIG_STOP[$CHANGEMENT_FAR_CONFIG_ID]=0;
			if($CHANGEMENT_FAR_CONFIG_OBLIGATOIRE=='oui'){
				if($NB_checkbox==0){
					$CHANGEMENT_FAR_CONFIG_STOP[$CHANGEMENT_FAR_CONFIG_ID]=1;
					$STOP=1;
				}
			}
		}
		mysql_free_result($res_rq_info_config_table);
	break;
	
	case "varchar": 
		$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		$CHANGEMENT_FAR_CONFIG_STOP[$CHANGEMENT_FAR_CONFIG_ID]=0;
		if($CHANGEMENT_FAR_CONFIG_OBLIGATOIRE=='oui'){
			if($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]==''){
				$CHANGEMENT_FAR_CONFIG_STOP[$CHANGEMENT_FAR_CONFIG_ID]=1;
				$STOP=1;
			}
		}
	break;
	
	case "text": 
		$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		$CHANGEMENT_FAR_CONFIG_STOP[$CHANGEMENT_FAR_CONFIG_ID]=0;
		if($CHANGEMENT_FAR_CONFIG_OBLIGATOIRE=='oui'){
			if($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]==''){
				$CHANGEMENT_FAR_CONFIG_STOP[$CHANGEMENT_FAR_CONFIG_ID]=1;
				$STOP=1;
			}
		}
	break;
	case "oui-non": 
		if(!isset($tab_var[$LISTE_CONFIG])){$tab_var[$LISTE_CONFIG]='';}
		$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		$CHANGEMENT_FAR_CONFIG_STOP[$CHANGEMENT_FAR_CONFIG_ID]=0;
		if($CHANGEMENT_FAR_CONFIG_OBLIGATOIRE=='oui'){
			if($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]==''){
				$CHANGEMENT_FAR_CONFIG_STOP[$CHANGEMENT_FAR_CONFIG_ID]=1;
				$STOP=1;
			}
		}
	break;
	case "risque": 
		if(!isset($tab_var[$LISTE_CONFIG])){$tab_var[$LISTE_CONFIG]='';}
		$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		$CHANGEMENT_FAR_CONFIG_STOP[$CHANGEMENT_FAR_CONFIG_ID]=0;
		if($CHANGEMENT_FAR_CONFIG_OBLIGATOIRE=='oui'){
			if($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]==''){
				$CHANGEMENT_FAR_CONFIG_STOP[$CHANGEMENT_FAR_CONFIG_ID]=1;
				$STOP=1;
			}
		}
	break;
	}
	
	
	} while ($tab_rq_info_config = mysql_fetch_assoc($res_rq_info_config));
	$ligne= mysql_num_rows($res_rq_info_config);
	if($ligne > 0) {
		mysql_data_seek($res_rq_info_config, 0);
		$tab_rq_info_config = mysql_fetch_assoc($res_rq_info_config);
	}
	}
	mysql_free_result($res_rq_info_config);   
	
	if($STOP==0){
	// ajout en base        
	$rq_info_config="
	SELECT  
	`CHANGEMENT_FAR_CONFIG_ID`,
	`CHANGEMENT_FAR_CONFIG_LIB`, 
	`CHANGEMENT_FAR_CONFIG_TYPE`, 
	`CHANGEMENT_FAR_CONFIG_OBLIGATOIRE`,
	`CHANGEMENT_FAR_CONFIG_TABLE`
	FROM `changement_far_config` 
	WHERE `ENABLE`='0'";
	$res_rq_info_config = mysql_query($rq_info_config, $mysql_link) or die(mysql_error());
	$tab_rq_info_config = mysql_fetch_assoc($res_rq_info_config);
	$total_ligne_rq_info_config=mysql_num_rows($res_rq_info_config);
	
	if($total_ligne_rq_info_config!=0){
	do {
	$CHANGEMENT_FAR_CONFIG_ID=$tab_rq_info_config['CHANGEMENT_FAR_CONFIG_ID'];
	$CHANGEMENT_FAR_CONFIG_LIB=$tab_rq_info_config['CHANGEMENT_FAR_CONFIG_LIB'];
	$CHANGEMENT_FAR_CONFIG_TYPE=$tab_rq_info_config['CHANGEMENT_FAR_CONFIG_TYPE'];
	$CHANGEMENT_FAR_CONFIG_OBLIGATOIRE=$tab_rq_info_config['CHANGEMENT_FAR_CONFIG_OBLIGATOIRE'];
	$CHANGEMENT_FAR_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_FAR_CONFIG_TABLE'];
	if(isset($tab_rq_info_config['CHANGEMENT_FAR_INFO_AUTRE_ID'])){
		$CHANGEMENT_FAR_INFO_AUTRE_ID=$tab_rq_info_config['CHANGEMENT_FAR_INFO_AUTRE_ID'];
	}
	if(isset($tab_rq_info_config['CHANGEMENT_FAR_INFO_LIB'])){
		$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]=$tab_rq_info_config['CHANGEMENT_FAR_INFO_LIB'];
	}
	if(!isset($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID])){$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]='';}
	$LISTE_CONFIG='CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID;
	
	switch ($CHANGEMENT_FAR_CONFIG_TYPE)
	{
	case "liste": 
	
		$CHANGEMENT_FAR_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_FAR_CONFIG_TABLE'];
		$CHANGEMENT_FAR_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE);
		$LISTE_CONFIG='CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID;
		$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		$CHANGEMENT_FAR_INFO_AUTRE_ID=$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID];

		$sql="INSERT INTO `changement_far` (`CHANGEMENT_FAR_ID` ,`CHANGEMENT_LISTE_ID` ,`CHANGEMENT_FAR_CONFIG_ID` ,`CHANGEMENT_FAR_INFO_AUTRE_ID` ,`CHANGEMENT_FAR_VALEUR`,`ENABLE` ) VALUES (NULL , '".$CHANGEMENT_ID."', '".$CHANGEMENT_FAR_CONFIG_ID."', '".$CHANGEMENT_FAR_INFO_AUTRE_ID."', '', '0');";
		mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
		$TABLE_SQL_SQL='changement_far';       
		historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');
	break;
	case "checkbox": 
		$CHANGEMENT_FAR_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_FAR_CONFIG_TABLE'];
		$CHANGEMENT_FAR_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE);
		$rq_info_config_table="
		SELECT  *
		FROM `".$CHANGEMENT_FAR_CONFIG_TABLE."`
		WHERE 
		`ENABLE`='0'
		AND `".$CHANGEMENT_FAR_CONFIG_TABLE_TRI."`!='vide'
		ORDER BY `".$CHANGEMENT_FAR_CONFIG_TABLE_TRI."`";
		$res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
		$tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
		$total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);
		if($total_ligne_rq_info_config_table!=0){
		do {
		$ID_SQL=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE).'_ID';
		$ID_LIB=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE);
		$COM_SQL=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE).'_COM';
		
		$CHANGEMENT_FAR_CONFIG_TABLE_ID=$tab_rq_info_config_table[$ID_SQL];
		$CHANGEMENT_FAR_CONFIG_TABLE_LIB=$tab_rq_info_config_table[$ID_LIB];
		$CHANGEMENT_FAR_INFO_AUTRE_ID=$CHANGEMENT_FAR_CONFIG_TABLE_ID;
		if(isset($tab_rq_info_config_table[$COM_SQL])){
		$CHANGEMENT_FAR_CONFIG_TABLE_COM=$tab_rq_info_config_table[$COM_SQL];	
		}else{
		$CHANGEMENT_FAR_CONFIG_TABLE_COM='non';
		}
		$LISTE_CONFIG='CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID.'_'.$CHANGEMENT_FAR_CONFIG_TABLE_ID;
		$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE=$CHANGEMENT_FAR_CONFIG_ID.'_'.$CHANGEMENT_FAR_CONFIG_TABLE_ID;
		if(!isset($NB_checkbox)){$NB_checkbox=0;}
		if(isset($tab_var[$LISTE_CONFIG])){
		$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
		$NB_checkbox=$NB_checkbox+1;
		}else{
		$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE]='off';
		}
		
		if($CHANGEMENT_FAR_CONFIG_TABLE_COM=='oui'){
		$LISTE_CONFIG='CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID.'_'.$CHANGEMENT_FAR_CONFIG_TABLE_ID.'_COM';
		$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE_COM=$CHANGEMENT_FAR_CONFIG_ID.'_'.$CHANGEMENT_FAR_CONFIG_TABLE_ID.'_COM';
		if(isset($tab_var[$LISTE_CONFIG])){
		$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE_COM]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
		
		}else{
		$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE_COM]='';
		}
		}
		if($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE]=='on'){
		$sql="INSERT INTO `changement_far` (`CHANGEMENT_FAR_ID` ,`CHANGEMENT_LISTE_ID` ,`CHANGEMENT_FAR_CONFIG_ID` ,`CHANGEMENT_FAR_INFO_AUTRE_ID` ,`CHANGEMENT_FAR_VALEUR`,`ENABLE` ) VALUES (NULL , '".$CHANGEMENT_ID."', '".$CHANGEMENT_FAR_CONFIG_ID."', '".$CHANGEMENT_FAR_INFO_AUTRE_ID."', '".$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE_COM]."', '0');";
		mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
		$TABLE_SQL_SQL='changement_far';       
		historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');
		}
		
		
		} while ($tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table));
		$ligne= mysql_num_rows($res_rq_info_config_table);
		if($ligne > 0) {
		mysql_data_seek($res_rq_info_config_table, 0);
		$tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
		}
		
		}
		mysql_free_result($res_rq_info_config_table);
		
		$rq_info_config_table="
		SELECT  *
		FROM `".$CHANGEMENT_FAR_CONFIG_TABLE."`
		WHERE 
		`ENABLE`='0'
		AND `".$CHANGEMENT_FAR_CONFIG_TABLE_TRI."`='vide'
		ORDER BY `".$CHANGEMENT_FAR_CONFIG_TABLE_TRI."`";
		$res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
		$tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
		$total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);
		if($total_ligne_rq_info_config_table!=0){
		do {
		$ID_SQL=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE).'_ID';
		$ID_LIB=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE);
		$COM_SQL=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE).'_COM';
		$CHANGEMENT_FAR_CONFIG_TABLE_ID=$tab_rq_info_config_table[$ID_SQL];
		$CHANGEMENT_FAR_CONFIG_TABLE_LIB=$tab_rq_info_config_table[$ID_LIB];
		$CHANGEMENT_FAR_INFO_AUTRE_ID=$CHANGEMENT_FAR_CONFIG_TABLE_ID;
		if(isset($tab_rq_info_config_table[$COM_SQL])){
		$CHANGEMENT_FAR_CONFIG_TABLE_COM=$tab_rq_info_config_table[$COM_SQL];	
		}else{
		$CHANGEMENT_FAR_CONFIG_TABLE_COM='non';
		}
		$LISTE_CONFIG='CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID.'_'.$CHANGEMENT_FAR_CONFIG_TABLE_ID;
		$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE=$CHANGEMENT_FAR_CONFIG_ID.'_'.$CHANGEMENT_FAR_CONFIG_TABLE_ID;
		if(!isset($NB_checkbox)){$NB_checkbox=0;}
		if(isset($tab_var[$LISTE_CONFIG])){
		$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
		$NB_checkbox=$NB_checkbox+1;
		}else{
		$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE]='off';
		}
		
		if($CHANGEMENT_FAR_CONFIG_TABLE_COM=='oui'){
		$LISTE_CONFIG='CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID.'_'.$CHANGEMENT_FAR_CONFIG_TABLE_ID.'_COM';
		$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE_COM=$CHANGEMENT_FAR_CONFIG_ID.'_'.$CHANGEMENT_FAR_CONFIG_TABLE_ID.'_COM';
		if(isset($tab_var[$LISTE_CONFIG])){
		$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE_COM]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
		
		}else{
		$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE_COM]='';
		}
		
		}
		if($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE]=='on'){
		$sql="INSERT INTO `changement_far` (`CHANGEMENT_FAR_ID` ,`CHANGEMENT_LISTE_ID` ,`CHANGEMENT_FAR_CONFIG_ID` ,`CHANGEMENT_FAR_INFO_AUTRE_ID` ,`CHANGEMENT_FAR_VALEUR`,`ENABLE` ) VALUES (NULL , '".$CHANGEMENT_ID."', '".$CHANGEMENT_FAR_CONFIG_ID."', '".$CHANGEMENT_FAR_INFO_AUTRE_ID."', '".$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE_COM]."', '0');";
		mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
		$TABLE_SQL_SQL='changement_far';       
		historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');
		}
		
		
		} while ($tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table));
		$ligne= mysql_num_rows($res_rq_info_config_table);
		if($ligne > 0) {
		mysql_data_seek($res_rq_info_config_table, 0);
		$tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
		}
		
		}
		mysql_free_result($res_rq_info_config_table);
	break;
	
	case "checkbox_horizontal": 
		$CHANGEMENT_FAR_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_FAR_CONFIG_TABLE'];
		$CHANGEMENT_FAR_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE);
		$rq_info_config_table="
		SELECT  *
		FROM `".$CHANGEMENT_FAR_CONFIG_TABLE."`
		WHERE 
		`ENABLE`='0'
		AND `".$CHANGEMENT_FAR_CONFIG_TABLE_TRI."`!='vide'
		ORDER BY `".$CHANGEMENT_FAR_CONFIG_TABLE_TRI."`";
		$res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
		$tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
		$total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);
		if($total_ligne_rq_info_config_table!=0){
		do {
		$ID_SQL=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE).'_ID';
		$ID_LIB=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE);
		$COM_SQL=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE).'_COM';
		
		$CHANGEMENT_FAR_CONFIG_TABLE_ID=$tab_rq_info_config_table[$ID_SQL];
		$CHANGEMENT_FAR_CONFIG_TABLE_LIB=$tab_rq_info_config_table[$ID_LIB];
		$CHANGEMENT_FAR_INFO_AUTRE_ID=$CHANGEMENT_FAR_CONFIG_TABLE_ID;
		if(isset($tab_rq_info_config_table[$COM_SQL])){
		$CHANGEMENT_FAR_CONFIG_TABLE_COM=$tab_rq_info_config_table[$COM_SQL];	
		}else{
		$CHANGEMENT_FAR_CONFIG_TABLE_COM='non';
		}
		$LISTE_CONFIG='CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID.'_'.$CHANGEMENT_FAR_CONFIG_TABLE_ID;
		$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE=$CHANGEMENT_FAR_CONFIG_ID.'_'.$CHANGEMENT_FAR_CONFIG_TABLE_ID;
		if(!isset($NB_checkbox)){$NB_checkbox=0;}
		if(isset($tab_var[$LISTE_CONFIG])){
		$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
		$NB_checkbox=$NB_checkbox+1;
		}else{
		$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE]='off';
		}
		
		if($CHANGEMENT_FAR_CONFIG_TABLE_COM=='oui'){
		$LISTE_CONFIG='CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID.'_'.$CHANGEMENT_FAR_CONFIG_TABLE_ID.'_COM';
		$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE_COM=$CHANGEMENT_FAR_CONFIG_ID.'_'.$CHANGEMENT_FAR_CONFIG_TABLE_ID.'_COM';
		if(isset($tab_var[$LISTE_CONFIG])){
		$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE_COM]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
		
		}else{
		$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE_COM]='';
		}
		}
		if($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE]=='on'){
		$sql="INSERT INTO `changement_far` (`CHANGEMENT_FAR_ID` ,`CHANGEMENT_LISTE_ID` ,`CHANGEMENT_FAR_CONFIG_ID` ,`CHANGEMENT_FAR_INFO_AUTRE_ID` ,`CHANGEMENT_FAR_VALEUR`,`ENABLE` ) VALUES (NULL , '".$CHANGEMENT_ID."', '".$CHANGEMENT_FAR_CONFIG_ID."', '".$CHANGEMENT_FAR_INFO_AUTRE_ID."', '".$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE_COM]."', '0');";
		mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
		$TABLE_SQL_SQL='changement_far';       
		historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');
		}
		
		
		} while ($tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table));
		$ligne= mysql_num_rows($res_rq_info_config_table);
		if($ligne > 0) {
		mysql_data_seek($res_rq_info_config_table, 0);
		$tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
		}
		
		}
		mysql_free_result($res_rq_info_config_table);
		
		$rq_info_config_table="
		SELECT  *
		FROM `".$CHANGEMENT_FAR_CONFIG_TABLE."`
		WHERE 
		`ENABLE`='0'
		AND `".$CHANGEMENT_FAR_CONFIG_TABLE_TRI."`='vide'
		ORDER BY `".$CHANGEMENT_FAR_CONFIG_TABLE_TRI."`";
		$res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
		$tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
		$total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);
		if($total_ligne_rq_info_config_table!=0){
		do {
		$ID_SQL=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE).'_ID';
		$ID_LIB=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE);
		$COM_SQL=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE).'_COM';
		$CHANGEMENT_FAR_CONFIG_TABLE_ID=$tab_rq_info_config_table[$ID_SQL];
		$CHANGEMENT_FAR_CONFIG_TABLE_LIB=$tab_rq_info_config_table[$ID_LIB];
		$CHANGEMENT_FAR_INFO_AUTRE_ID=$CHANGEMENT_FAR_CONFIG_TABLE_ID;
		if(isset($tab_rq_info_config_table[$COM_SQL])){
		$CHANGEMENT_FAR_CONFIG_TABLE_COM=$tab_rq_info_config_table[$COM_SQL];	
		}else{
		$CHANGEMENT_FAR_CONFIG_TABLE_COM='non';
		}
		$LISTE_CONFIG='CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID.'_'.$CHANGEMENT_FAR_CONFIG_TABLE_ID;
		$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE=$CHANGEMENT_FAR_CONFIG_ID.'_'.$CHANGEMENT_FAR_CONFIG_TABLE_ID;
		if(!isset($NB_checkbox)){$NB_checkbox=0;}
		if(isset($tab_var[$LISTE_CONFIG])){
		$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
		$NB_checkbox=$NB_checkbox+1;
		}else{
		$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE]='off';
		}
		
		if($CHANGEMENT_FAR_CONFIG_TABLE_COM=='oui'){
		$LISTE_CONFIG='CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID.'_'.$CHANGEMENT_FAR_CONFIG_TABLE_ID.'_COM';
		$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE_COM=$CHANGEMENT_FAR_CONFIG_ID.'_'.$CHANGEMENT_FAR_CONFIG_TABLE_ID.'_COM';
		if(isset($tab_var[$LISTE_CONFIG])){
		$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE_COM]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
		
		}else{
		$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE_COM]='';
		}
		
		}
		if($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE]=='on'){
		$sql="INSERT INTO `changement_far` (`CHANGEMENT_FAR_ID` ,`CHANGEMENT_LISTE_ID` ,`CHANGEMENT_FAR_CONFIG_ID` ,`CHANGEMENT_FAR_INFO_AUTRE_ID` ,`CHANGEMENT_FAR_VALEUR`,`ENABLE` ) VALUES (NULL , '".$CHANGEMENT_ID."', '".$CHANGEMENT_FAR_CONFIG_ID."', '".$CHANGEMENT_FAR_INFO_AUTRE_ID."', '".$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE_COM]."', '0');";
		mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
		$TABLE_SQL_SQL='changement_far';       
		historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');
		}
		
		
		} while ($tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table));
		$ligne= mysql_num_rows($res_rq_info_config_table);
		if($ligne > 0) {
		mysql_data_seek($res_rq_info_config_table, 0);
		$tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
		}
		
		}
		mysql_free_result($res_rq_info_config_table);
	break;
	
	case "varchar": 
		$CHANGEMENT_FAR_INFO_AUTRE_ID=0;
		$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		$sql="INSERT INTO `changement_far` (`CHANGEMENT_FAR_ID` ,`CHANGEMENT_LISTE_ID` ,`CHANGEMENT_FAR_CONFIG_ID` ,`CHANGEMENT_FAR_INFO_AUTRE_ID` ,`CHANGEMENT_FAR_VALEUR`,`ENABLE` ) VALUES (NULL , '".$CHANGEMENT_ID."', '".$CHANGEMENT_FAR_CONFIG_ID."', '".$CHANGEMENT_FAR_INFO_AUTRE_ID."', '".$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]."', '0');";
		mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
		$TABLE_SQL_SQL='changement_far';       
		historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');
	break;
	
	case "text": 
		$CHANGEMENT_FAR_INFO_AUTRE_ID=0;
		$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		$sql="INSERT INTO `changement_far` (`CHANGEMENT_FAR_ID` ,`CHANGEMENT_LISTE_ID` ,`CHANGEMENT_FAR_CONFIG_ID` ,`CHANGEMENT_FAR_INFO_AUTRE_ID` ,`CHANGEMENT_FAR_VALEUR`,`ENABLE` ) VALUES (NULL , '".$CHANGEMENT_ID."', '".$CHANGEMENT_FAR_CONFIG_ID."', '".$CHANGEMENT_FAR_INFO_AUTRE_ID."', '".$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]."', '0');";
		mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
		$TABLE_SQL_SQL='changement_far';       
		historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');
	break;
	case "risque": 
		$CHANGEMENT_FAR_INFO_AUTRE_ID=0;
		$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		$sql="INSERT INTO `changement_far` (`CHANGEMENT_FAR_ID` ,`CHANGEMENT_LISTE_ID` ,`CHANGEMENT_FAR_CONFIG_ID` ,`CHANGEMENT_FAR_INFO_AUTRE_ID` ,`CHANGEMENT_FAR_VALEUR`,`ENABLE` ) VALUES (NULL , '".$CHANGEMENT_ID."', '".$CHANGEMENT_FAR_CONFIG_ID."', '".$CHANGEMENT_FAR_INFO_AUTRE_ID."', '".$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]."', '0');";
		mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
		$TABLE_SQL_SQL='changement_far';       
		historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');
	break;
	case "oui-non": 
		$CHANGEMENT_FAR_INFO_AUTRE_ID=0;
		$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		$sql="INSERT INTO `changement_far` (`CHANGEMENT_FAR_ID` ,`CHANGEMENT_LISTE_ID` ,`CHANGEMENT_FAR_CONFIG_ID` ,`CHANGEMENT_FAR_INFO_AUTRE_ID` ,`CHANGEMENT_FAR_VALEUR`,`ENABLE` ) VALUES (NULL , '".$CHANGEMENT_ID."', '".$CHANGEMENT_FAR_CONFIG_ID."', '".$CHANGEMENT_FAR_INFO_AUTRE_ID."', '".$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]."', '0');";
		mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
		$TABLE_SQL_SQL='changement_far';       
		historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');
	break;
	}
	
	
	
	} while ($tab_rq_info_config = mysql_fetch_assoc($res_rq_info_config));
	$ligne= mysql_num_rows($res_rq_info_config);
	if($ligne > 0) {
		mysql_data_seek($res_rq_info_config, 0);
		$tab_rq_info_config = mysql_fetch_assoc($res_rq_info_config);
	}
	}
	mysql_free_result($res_rq_info_config);  
	
	$sql="OPTIMIZE TABLE `changement_far` ";
	mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
	
	$TABLE_SQL_SQL='changement_far';       
	historique_sql_new($sql,$TABLE_SQL_SQL,'OPTIMIZE');
	
	mysql_free_result($res_rq_info_far);
	if($STOP==0){
	$CHANGEMENT_FAR_INSCRIPTION=$tab_var['CHANGEMENT_FAR_INSCRIPTION'];
	if($CHANGEMENT_FAR_INSCRIPTION=='Oui'){
	
	$rq_info_status="
	SELECT `CHANGEMENT_STATUS_ID` 
	FROM `changement_status` 
	WHERE `CHANGEMENT_STATUS` = 'Inscrit'
	AND `ENABLE`='0'";
	$res_rq_info_status = mysql_query($rq_info_status, $mysql_link) or die(mysql_error());
	$tab_rq_info_status = mysql_fetch_assoc($res_rq_info_status);
	$total_ligne_rq_info_status=mysql_num_rows($res_rq_info_status);
	if($total_ligne_rq_info_status==0){
	$sql="INSERT INTO `changement_status` (`CHANGEMENT_STATUS_ID` ,`CHANGEMENT_STATUS`, `ENABLE` )VALUES (NULL , 'Inscrit', '0');";
	mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
	$TABLE_SQL_SQL='changement_status';       
	historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');
	$rq_info_status="
	SELECT `CHANGEMENT_STATUS_ID` 
	FROM `changement_status` 
	WHERE `CHANGEMENT_STATUS` = 'Inscrit'
	AND `ENABLE`='0'";
	$res_rq_info_status = mysql_query($rq_info_status, $mysql_link) or die(mysql_error());
	$tab_rq_info_status = mysql_fetch_assoc($res_rq_info_status);
	$total_ligne_rq_info_status=mysql_num_rows($res_rq_info_status);
	}
	$CHANGEMENT_STATUS_ID=$tab_rq_info_status['CHANGEMENT_STATUS_ID'];
	mysql_free_result($res_rq_info_status); 
	$sql="
	UPDATE `changement_liste` SET 
	`CHANGEMENT_STATUS_ID` = '".$CHANGEMENT_STATUS_ID."',
	`CHANGEMENT_LISTE_UTILISATEUR_ID`= '".$UTILISATEUR_ID."',
	`CHANGEMENT_LISTE_DATE_MODIFICATION`= '".$DATE_MODIFICATION."'
	WHERE `CHANGEMENT_LISTE_ID` ='".$ID."' LIMIT 1 ;";
	
	mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
	$TABLE_SQL_SQL='changement_liste';       
	historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');
	
	$TRACE_ETAT=$CHANGEMENT_STATUS_ID;
	$TRACE_CATEGORIE='Changement';
	$TRACE_TABLE='changement_far';
	$TRACE_REF_ID=$ID;
	$TRACE_ACTION='Ajout';
	moteur_trace($TRACE_CATEGORIE,$TRACE_TABLE,$TRACE_REF_ID,$TRACE_ACTION,$TRACE_ETAT);
	
	// mail d'inscription			
	/*
	$objet='';
	if ( $ENV != "x" )
	{	
		$objet='[dev]-';
	}		
	$objet.='-=Gestion des changements=- Inscription de la demande n '.$ID.'.';
	list ($Personne_abrege,$Personne_complet,$Personne_email,$Personne_email_FULL)=Recherche_SQL_nom_FULL($UTILISATEUR_ID);
	$Corps_mail = Constitue_corps_Message($CHANGEMENT_ID,$Personne_complet,$mysql_link);
	$Corps_mail_SVG = Constitue_corps_Message_SVG($CHANGEMENT_ID,$ENV,$Personne_email_FULL,$Personne_complet,$objet,$mysql_link);
				mailMailInfo_php($CHANGEMENT_ID,$Personne_email_FULL, $Personne_abrege, $objet, $Corps_mail, $TRACE_ETAT, $ENV,$mysql_link);
	sauvegarde_MailInfo( $Corps_mail_SVG, $CHANGEMENT_ID, $TRACE_ETAT,$UTILISATEUR_ID,$ENV,$mysql_link);
	*/
	echo '
      <script language="JavaScript">
      url=("./index.php?ITEM=changement_Send_Mail&type=inscription&ID='.$ID.'");
      window.location=url;
      </script>
      ';
	
	}else{
	$rq_info_CHANGEMENT_STATUS_ID="
	SELECT 
	`CHANGEMENT_STATUS_ID`
	FROM `changement_liste`
	WHERE 
	`CHANGEMENT_LISTE_ID` ='".$ID."' 
	AND `ENABLE` ='0'
	LIMIT 1";
	$res_rq_info_CHANGEMENT_STATUS_ID = mysql_query($rq_info_CHANGEMENT_STATUS_ID, $mysql_link) or die(mysql_error());
	$tab_rq_info_CHANGEMENT_STATUS_ID = mysql_fetch_assoc($res_rq_info_CHANGEMENT_STATUS_ID);
	$total_ligne_rq_info_CHANGEMENT_STATUS_ID=mysql_num_rows($res_rq_info_CHANGEMENT_STATUS_ID);
	$CHANGEMENT_STATUS_ID=$tab_rq_info_CHANGEMENT_STATUS_ID['CHANGEMENT_STATUS_ID'];
	mysql_free_result($res_rq_info_CHANGEMENT_STATUS_ID);
	$TRACE_ETAT=$CHANGEMENT_STATUS_ID;
	$TRACE_CATEGORIE='Changement';
	$TRACE_TABLE='changement_far';
	$TRACE_REF_ID=$ID;
	$TRACE_ACTION='Ajout';
	moteur_trace($TRACE_CATEGORIE,$TRACE_TABLE,$TRACE_REF_ID,$TRACE_ACTION,$TRACE_ETAT);
	}
	
	
	echo '
	<script language="JavaScript">
	url=("./index.php?ITEM=changement_Info_Changement&action=Info&ID='.$ID.'");
	window.location=url;
	</script>
	';
	}
	}
	
	$_GET['action']="Ajout";
  }
  

  
  # Cas Modifier
  if($tab_var['btn']=="Modifier"){
	$nb_modif=0;
  	$ID=$tab_var['ID'];
  	$CHANGEMENT_ID=$ID;

//verification
$rq_info_config="
	SELECT  
	`changement_far_config`.`CHANGEMENT_FAR_CONFIG_ID`,
	`changement_far_config`.`CHANGEMENT_FAR_CONFIG_LIB`, 
	`changement_far_config`.`CHANGEMENT_FAR_CONFIG_TYPE`,
	`changement_far_config`.`CHANGEMENT_FAR_CONFIG_TABLE`,
	`changement_far_config`.`CHANGEMENT_FAR_CONFIG_OBLIGATOIRE` 
	FROM `changement_far_config` 
	WHERE `changement_far_config`.`ENABLE`='0'
	AND `changement_far_config`.`CHANGEMENT_FAR_CONFIG_ID` IN(
	SELECT `CHANGEMENT_FAR_CONFIG_ID` FROM `changement_far` WHERE `CHANGEMENT_LISTE_ID`='".$ID."'
	)
	";
	$res_rq_info_config = mysql_query($rq_info_config, $mysql_link) or die(mysql_error());
	$tab_rq_info_config = mysql_fetch_assoc($res_rq_info_config);
	$total_ligne_rq_info_config=mysql_num_rows($res_rq_info_config);
	
	if($total_ligne_rq_info_config!=0){
	do {
		
	$CHANGEMENT_FAR_CONFIG_ID=$tab_rq_info_config['CHANGEMENT_FAR_CONFIG_ID'];
	$CHANGEMENT_FAR_CONFIG_LIB=$tab_rq_info_config['CHANGEMENT_FAR_CONFIG_LIB'];
	$CHANGEMENT_FAR_CONFIG_TYPE=$tab_rq_info_config['CHANGEMENT_FAR_CONFIG_TYPE'];
	$CHANGEMENT_FAR_CONFIG_OBLIGATOIRE=$tab_rq_info_config['CHANGEMENT_FAR_CONFIG_OBLIGATOIRE'];
	if(isset($tab_rq_info_config['CHANGEMENT_FAR_INFO_AUTRE_ID'])){
		$CHANGEMENT_FAR_INFO_AUTRE_ID=$tab_rq_info_config['CHANGEMENT_FAR_INFO_AUTRE_ID'];
	}
	if(isset($tab_rq_info_config['CHANGEMENT_FAR_INFO_LIB'])){
		$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]=$tab_rq_info_config['CHANGEMENT_FAR_INFO_LIB'];
	}
	if(!isset($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID])){$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]='';}
	$LISTE_CONFIG='CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID;
	
	switch ($CHANGEMENT_FAR_CONFIG_TYPE)
	{
	case "liste": 
		$CHANGEMENT_FAR_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_FAR_CONFIG_TABLE'];
		$CHANGEMENT_FAR_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE);
		$LISTE_CONFIG='CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID;
		$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		$CHANGEMENT_FAR_CONFIG_STOP[$CHANGEMENT_FAR_CONFIG_ID]=0;
		if($CHANGEMENT_FAR_CONFIG_OBLIGATOIRE=='oui'){
			if($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]=='0'){
				$CHANGEMENT_FAR_CONFIG_STOP[$CHANGEMENT_FAR_CONFIG_ID]=1;
				$STOP=1;
			}
		}
	break;
	case "checkbox": 
		$CHANGEMENT_FAR_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_FAR_CONFIG_TABLE'];
		$CHANGEMENT_FAR_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE);
		$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]='0';
		$rq_info_config_table="
		SELECT  *
		FROM `".$CHANGEMENT_FAR_CONFIG_TABLE."`
		WHERE 
		`ENABLE`='0'
		AND `".$CHANGEMENT_FAR_CONFIG_TABLE_TRI."`!='vide'
		ORDER BY `".$CHANGEMENT_FAR_CONFIG_TABLE_TRI."`";
		$res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
		$tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
		$total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);
		if($total_ligne_rq_info_config_table!=0){
		do {
			$ID_SQL=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE).'_ID';
			$ID_LIB=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE);
			$COM_SQL=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE).'_COM';
			$CHANGEMENT_FAR_CONFIG_TABLE_ID=$tab_rq_info_config_table[$ID_SQL];
			$CHANGEMENT_FAR_CONFIG_TABLE_LIB=$tab_rq_info_config_table[$ID_LIB];
			if(isset($tab_rq_info_config_table[$COM_SQL])){
				$CHANGEMENT_FAR_CONFIG_TABLE_COM=$tab_rq_info_config_table[$COM_SQL];	
			}else{
				$CHANGEMENT_FAR_CONFIG_TABLE_COM='non';
			}
			$LISTE_CONFIG='CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID.'_'.$CHANGEMENT_FAR_CONFIG_TABLE_ID;
			$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE=$CHANGEMENT_FAR_CONFIG_ID.'_'.$CHANGEMENT_FAR_CONFIG_TABLE_ID;
			if(!isset($NB_checkbox)){$NB_checkbox=0;}
			if(isset($tab_var[$LISTE_CONFIG])){
				$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
				$NB_checkbox=$NB_checkbox+1;
			}else{
				$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE]='off';
			}
			
			if($CHANGEMENT_FAR_CONFIG_TABLE_COM=='oui'){
				$LISTE_CONFIG='CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID.'_'.$CHANGEMENT_FAR_CONFIG_TABLE_ID.'_COM';
				$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE_COM=$CHANGEMENT_FAR_CONFIG_ID.'_'.$CHANGEMENT_FAR_CONFIG_TABLE_ID.'_COM';
				if(isset($tab_var[$LISTE_CONFIG])){
					$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE_COM]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
				}else{
					$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE_COM]='';
				}
				if($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE]=='on'){
					if($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE_COM]==''){
						$STOP=1;	
						$CHANGEMENT_FAR_CONFIG_STOP_COM[$CHANGEMENT_FAR_CONFIG_ID]=1;
					}
				}
			}
			
		
		} while ($tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table));
		$ligne= mysql_num_rows($res_rq_info_config_table);
		if($ligne > 0) {
			mysql_data_seek($res_rq_info_config_table, 0);
			$tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
		}
		$CHANGEMENT_FAR_CONFIG_STOP[$CHANGEMENT_FAR_CONFIG_ID]=0;
			if($CHANGEMENT_FAR_CONFIG_OBLIGATOIRE=='oui'){
				if($NB_checkbox==0){
					$CHANGEMENT_FAR_CONFIG_STOP[$CHANGEMENT_FAR_CONFIG_ID]=1;
					$STOP=1;
				}
			}
		}
		mysql_free_result($res_rq_info_config_table);
	break;
	
	case "checkbox_horizontal": 
		$CHANGEMENT_FAR_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_FAR_CONFIG_TABLE'];
		$CHANGEMENT_FAR_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE);
		$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]='0';
		$rq_info_config_table="
		SELECT  *
		FROM `".$CHANGEMENT_FAR_CONFIG_TABLE."`
		WHERE 
		`ENABLE`='0'
		AND `".$CHANGEMENT_FAR_CONFIG_TABLE_TRI."`!='vide'
		ORDER BY `".$CHANGEMENT_FAR_CONFIG_TABLE_TRI."`";
		$res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
		$tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
		$total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);
		if($total_ligne_rq_info_config_table!=0){
		do {
			$ID_SQL=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE).'_ID';
			$ID_LIB=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE);
			$COM_SQL=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE).'_COM';
			$CHANGEMENT_FAR_CONFIG_TABLE_ID=$tab_rq_info_config_table[$ID_SQL];
			$CHANGEMENT_FAR_CONFIG_TABLE_LIB=$tab_rq_info_config_table[$ID_LIB];
			if(isset($tab_rq_info_config_table[$COM_SQL])){
				$CHANGEMENT_FAR_CONFIG_TABLE_COM=$tab_rq_info_config_table[$COM_SQL];	
			}else{
				$CHANGEMENT_FAR_CONFIG_TABLE_COM='non';
			}
			$LISTE_CONFIG='CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID.'_'.$CHANGEMENT_FAR_CONFIG_TABLE_ID;
			$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE=$CHANGEMENT_FAR_CONFIG_ID.'_'.$CHANGEMENT_FAR_CONFIG_TABLE_ID;
			if(!isset($NB_checkbox)){$NB_checkbox=0;}
			if(isset($tab_var[$LISTE_CONFIG])){
				$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
				$NB_checkbox=$NB_checkbox+1;
			}else{
				$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE]='off';
			}
			
			if($CHANGEMENT_FAR_CONFIG_TABLE_COM=='oui'){
				$LISTE_CONFIG='CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID.'_'.$CHANGEMENT_FAR_CONFIG_TABLE_ID.'_COM';
				$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE_COM=$CHANGEMENT_FAR_CONFIG_ID.'_'.$CHANGEMENT_FAR_CONFIG_TABLE_ID.'_COM';
				if(isset($tab_var[$LISTE_CONFIG])){
					$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE_COM]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
				}else{
					$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE_COM]='';
				}
				if($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE]=='on'){
					if($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE_COM]==''){
						$STOP=1;	
						$CHANGEMENT_FAR_CONFIG_STOP_COM[$CHANGEMENT_FAR_CONFIG_ID]=1;
					}
				}
			}
			
		
		} while ($tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table));
		$ligne= mysql_num_rows($res_rq_info_config_table);
		if($ligne > 0) {
			mysql_data_seek($res_rq_info_config_table, 0);
			$tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
		}
		$CHANGEMENT_FAR_CONFIG_STOP[$CHANGEMENT_FAR_CONFIG_ID]=0;
			if($CHANGEMENT_FAR_CONFIG_OBLIGATOIRE=='oui'){
				if($NB_checkbox==0){
					$CHANGEMENT_FAR_CONFIG_STOP[$CHANGEMENT_FAR_CONFIG_ID]=1;
					$STOP=1;
				}
			}
		}
		mysql_free_result($res_rq_info_config_table);
	break;
	
	case "varchar": 
		$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		$CHANGEMENT_FAR_CONFIG_STOP[$CHANGEMENT_FAR_CONFIG_ID]=0;
		if($CHANGEMENT_FAR_CONFIG_OBLIGATOIRE=='oui'){
			if($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]==''){
				$CHANGEMENT_FAR_CONFIG_STOP[$CHANGEMENT_FAR_CONFIG_ID]=1;
				$STOP=1;
			}
		}
	break;
	
	case "text": 
		$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		$CHANGEMENT_FAR_CONFIG_STOP[$CHANGEMENT_FAR_CONFIG_ID]=0;
		if($CHANGEMENT_FAR_CONFIG_OBLIGATOIRE=='oui'){
			if($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]==''){
				$CHANGEMENT_FAR_CONFIG_STOP[$CHANGEMENT_FAR_CONFIG_ID]=1;
				$STOP=1;
			}
		}
	break;
	case "oui-non": 
		if(!isset($tab_var[$LISTE_CONFIG])){$tab_var[$LISTE_CONFIG]='';}
		$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		$CHANGEMENT_FAR_CONFIG_STOP[$CHANGEMENT_FAR_CONFIG_ID]=0;
		if($CHANGEMENT_FAR_CONFIG_OBLIGATOIRE=='oui'){
			if($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]==''){
				$CHANGEMENT_FAR_CONFIG_STOP[$CHANGEMENT_FAR_CONFIG_ID]=1;
				$STOP=1;
			}
		}
	break;
	case "risque": 
		if(!isset($tab_var[$LISTE_CONFIG])){$tab_var[$LISTE_CONFIG]='';}
		$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		$CHANGEMENT_FAR_CONFIG_STOP[$CHANGEMENT_FAR_CONFIG_ID]=0;
		if($CHANGEMENT_FAR_CONFIG_OBLIGATOIRE=='oui'){
			if($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]==''){
				$CHANGEMENT_FAR_CONFIG_STOP[$CHANGEMENT_FAR_CONFIG_ID]=1;
				$STOP=1;
			}
		}
	break;
	}
	
	
	} while ($tab_rq_info_config = mysql_fetch_assoc($res_rq_info_config));
	$ligne= mysql_num_rows($res_rq_info_config);
	if($ligne > 0) {
		mysql_data_seek($res_rq_info_config, 0);
		$tab_rq_info_config = mysql_fetch_assoc($res_rq_info_config);
	}
	}
//	mysql_free_result($res_rq_info_config);  

        if($STOP==0){
// ajout en base      
if($total_ligne_rq_info_config!=0){
	do {
	$CHANGEMENT_FAR_CONFIG_ID=$tab_rq_info_config['CHANGEMENT_FAR_CONFIG_ID'];
	$CHANGEMENT_FAR_CONFIG_LIB=$tab_rq_info_config['CHANGEMENT_FAR_CONFIG_LIB'];
	$CHANGEMENT_FAR_CONFIG_TYPE=$tab_rq_info_config['CHANGEMENT_FAR_CONFIG_TYPE'];
	$CHANGEMENT_FAR_CONFIG_OBLIGATOIRE=$tab_rq_info_config['CHANGEMENT_FAR_CONFIG_OBLIGATOIRE'];
	$CHANGEMENT_FAR_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_FAR_CONFIG_TABLE'];
	if(isset($tab_rq_info_config['CHANGEMENT_FAR_INFO_AUTRE_ID'])){
		$CHANGEMENT_FAR_INFO_AUTRE_ID=$tab_rq_info_config['CHANGEMENT_FAR_INFO_AUTRE_ID'];
	}
	if(isset($tab_rq_info_config['CHANGEMENT_FAR_INFO_LIB'])){
		$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]=$tab_rq_info_config['CHANGEMENT_FAR_INFO_LIB'];
	}
	if(!isset($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID])){$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]='';}
	$LISTE_CONFIG='CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID;
	
	switch ($CHANGEMENT_FAR_CONFIG_TYPE)
	{
	case "liste": 
	
		$CHANGEMENT_FAR_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_FAR_CONFIG_TABLE'];
		$CHANGEMENT_FAR_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE);
		$LISTE_CONFIG='CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID;
		$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		$CHANGEMENT_FAR_INFO_AUTRE_ID=$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID];
		$rq_info="
    SELECT 
    `CHANGEMENT_FAR_ID`
    FROM `changement_far`
    WHERE 
    `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."' 
    AND `CHANGEMENT_FAR_CONFIG_ID` ='".$CHANGEMENT_FAR_CONFIG_ID."' 
    AND `CHANGEMENT_FAR_INFO_AUTRE_ID` ='".$CHANGEMENT_FAR_INFO_AUTRE_ID."' 
    AND `CHANGEMENT_FAR_VALEUR` =''  
    AND `ENABLE` ='0'
    LIMIT 1";
    
    $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
    $tab_rq_info = mysql_fetch_assoc($res_rq_info);
    $total_ligne_rq_info=mysql_num_rows($res_rq_info);
    mysql_free_result($res_rq_info);
    if($total_ligne_rq_info==0){
      $nb_modif=$nb_modif+1;
      $sql="
      UPDATE `changement_far` SET 
      `CHANGEMENT_FAR_INFO_AUTRE_ID`= '".$CHANGEMENT_FAR_INFO_AUTRE_ID."'
      WHERE `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."'
      AND `CHANGEMENT_FAR_CONFIG_ID`='".$CHANGEMENT_FAR_CONFIG_ID."'
      AND `ENABLE`='0'
      LIMIT 1 ;";
      mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
      $TABLE_SQL_SQL='changement_far';       
      historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');
    }
		
	break;
	case "checkbox": 
		$CHANGEMENT_FAR_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_FAR_CONFIG_TABLE'];
		$CHANGEMENT_FAR_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE);
              $rq_info_config_table="
              SELECT  *
              FROM `".$CHANGEMENT_FAR_CONFIG_TABLE."`
              WHERE 
              `ENABLE`='0'
              ORDER BY `".$CHANGEMENT_FAR_CONFIG_TABLE_TRI."`";
              $res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
              $tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
              $total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);
              if($total_ligne_rq_info_config_table!=0){
                do {
                  $ID_SQL=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE).'_ID';
                  $ID_LIB=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE);
                  $COM_SQL=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE).'_COM';
                  
                  $CHANGEMENT_FAR_CONFIG_TABLE_ID=$tab_rq_info_config_table[$ID_SQL];
                  $CHANGEMENT_FAR_CONFIG_TABLE_LIB=$tab_rq_info_config_table[$ID_LIB];
                  $CHANGEMENT_FAR_INFO_AUTRE_ID=$CHANGEMENT_FAR_CONFIG_TABLE_ID;
                  if(isset($tab_rq_info_config_table[$COM_SQL])){
                  	$CHANGEMENT_FAR_CONFIG_TABLE_COM=$tab_rq_info_config_table[$COM_SQL];	
                  }else{
                  	$CHANGEMENT_FAR_CONFIG_TABLE_COM='non';
                  }
	              $LISTE_CONFIG='CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID.'_'.$CHANGEMENT_FAR_CONFIG_TABLE_ID;
	              $CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE=$CHANGEMENT_FAR_CONFIG_ID.'_'.$CHANGEMENT_FAR_CONFIG_TABLE_ID;
	              if(!isset($NB_checkbox)){$NB_checkbox=0;}
	              if(isset($tab_var[$LISTE_CONFIG])){
	              	$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
	              	$NB_checkbox=$NB_checkbox+1;
	              }else{
	              	$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE]='off';
	              }

	              if($CHANGEMENT_FAR_CONFIG_TABLE_COM=='oui'){
		              $LISTE_CONFIG='CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID.'_'.$CHANGEMENT_FAR_CONFIG_TABLE_ID.'_COM';
		              $CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE_COM=$CHANGEMENT_FAR_CONFIG_ID.'_'.$CHANGEMENT_FAR_CONFIG_TABLE_ID.'_COM';
		              if(isset($tab_var[$LISTE_CONFIG])){
		              	$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE_COM]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
		              	
		              }else{
		              	$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE_COM]='';
		              }
	              }

	              

	              if($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE]=='on'){
	              	      $rq_info="
		              SELECT 
		              `CHANGEMENT_FAR_VALEUR`
		              FROM `changement_far`
		              WHERE 
		              `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."' 
		              AND `CHANGEMENT_FAR_CONFIG_ID` ='".$CHANGEMENT_FAR_CONFIG_ID."' 
		              AND `CHANGEMENT_FAR_INFO_AUTRE_ID` ='".$CHANGEMENT_FAR_INFO_AUTRE_ID."' 
		              AND `ENABLE` ='0'
		              LIMIT 1";
		              $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
		              $tab_rq_info = mysql_fetch_assoc($res_rq_info);
		              $total_ligne_rq_info=mysql_num_rows($res_rq_info);
		              mysql_free_result($res_rq_info);
		              if($total_ligne_rq_info==0){
		              	$nb_modif=$nb_modif+1;
				$sql="INSERT INTO `changement_far` (
				`CHANGEMENT_FAR_ID` ,
				`CHANGEMENT_LISTE_ID` ,
				`CHANGEMENT_FAR_CONFIG_ID` ,
				`CHANGEMENT_FAR_INFO_AUTRE_ID` ,
				`CHANGEMENT_FAR_VALEUR`,
				`ENABLE` ) 
				VALUES (NULL ,
				 '".$CHANGEMENT_ID."', 
				 '".$CHANGEMENT_FAR_CONFIG_ID."', 
				 '".$CHANGEMENT_FAR_INFO_AUTRE_ID."', 
				 '".$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE_COM]."', 
				 '0');";
				mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
				$TABLE_SQL_SQL='changement_far';       
				historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');
		              }else{
		                $TEST_CHANGEMENT_FAR_INFO_LIB=$tab_rq_info['CHANGEMENT_FAR_VALEUR'];
		                if($TEST_CHANGEMENT_FAR_INFO_LIB!=$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE_COM]){
		                	$nb_modif=$nb_modif+1;
					$sql="UPDATE  `changement_far` SET
					`CHANGEMENT_FAR_VALEUR`='".$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE_COM]."'
					WHERE `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."' 
			              AND `CHANGEMENT_FAR_CONFIG_ID` ='".$CHANGEMENT_FAR_CONFIG_ID."' 
			              AND `CHANGEMENT_FAR_INFO_AUTRE_ID` ='".$CHANGEMENT_FAR_INFO_AUTRE_ID."';";
					mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
					$TABLE_SQL_SQL='changement_far';       
					historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');
		                }
		              }
	              }else{
	              	      $rq_info="
		              SELECT 
		              `CHANGEMENT_FAR_ID`
		              FROM `changement_far`
		              WHERE 
		              `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."' 
		              AND `CHANGEMENT_FAR_CONFIG_ID` ='".$CHANGEMENT_FAR_CONFIG_ID."' 
		              AND `CHANGEMENT_FAR_INFO_AUTRE_ID` ='".$CHANGEMENT_FAR_INFO_AUTRE_ID."'  
		              AND `ENABLE` ='0'
		              LIMIT 1";
		              $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
		              $tab_rq_info = mysql_fetch_assoc($res_rq_info);
		              $total_ligne_rq_info=mysql_num_rows($res_rq_info);
		              mysql_free_result($res_rq_info);
		              if($total_ligne_rq_info!=0){
		              	$nb_modif=$nb_modif+1;
		              	$sql="
		              	UPDATE `changement_far` SET `ENABLE` = '1' 
	              		WHERE `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."' 
				AND `CHANGEMENT_FAR_CONFIG_ID` ='".$CHANGEMENT_FAR_CONFIG_ID."'
				AND `CHANGEMENT_FAR_INFO_AUTRE_ID` ='".$CHANGEMENT_FAR_INFO_AUTRE_ID."'
				AND `ENABLE`='0'
				LIMIT 1 ;";
				mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
				$TABLE_SQL_SQL='changement_far';       
				historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');
		              }
	              }
	              
                
                } while ($tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table));
                $ligne= mysql_num_rows($res_rq_info_config_table);
                if($ligne > 0) {
                  mysql_data_seek($res_rq_info_config_table, 0);
                  $tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
                }

              }
              mysql_free_result($res_rq_info_config_table);
		
	break;
	
	case "checkbox_horizontal": 
		$CHANGEMENT_FAR_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_FAR_CONFIG_TABLE'];
		$CHANGEMENT_FAR_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE);
              $rq_info_config_table="
              SELECT  *
              FROM `".$CHANGEMENT_FAR_CONFIG_TABLE."`
              WHERE 
              `ENABLE`='0'
              ORDER BY `".$CHANGEMENT_FAR_CONFIG_TABLE_TRI."`";
              $res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
              $tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
              $total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);
              if($total_ligne_rq_info_config_table!=0){
                do {
                  $ID_SQL=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE).'_ID';
                  $ID_LIB=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE);
                  $COM_SQL=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE).'_COM';
                  
                  $CHANGEMENT_FAR_CONFIG_TABLE_ID=$tab_rq_info_config_table[$ID_SQL];
                  $CHANGEMENT_FAR_CONFIG_TABLE_LIB=$tab_rq_info_config_table[$ID_LIB];
                  $CHANGEMENT_FAR_INFO_AUTRE_ID=$CHANGEMENT_FAR_CONFIG_TABLE_ID;
                  if(isset($tab_rq_info_config_table[$COM_SQL])){
                  	$CHANGEMENT_FAR_CONFIG_TABLE_COM=$tab_rq_info_config_table[$COM_SQL];	
                  }else{
                  	$CHANGEMENT_FAR_CONFIG_TABLE_COM='non';
                  }
	              $LISTE_CONFIG='CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID.'_'.$CHANGEMENT_FAR_CONFIG_TABLE_ID;
	              $CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE=$CHANGEMENT_FAR_CONFIG_ID.'_'.$CHANGEMENT_FAR_CONFIG_TABLE_ID;
	              if(!isset($NB_checkbox)){$NB_checkbox=0;}
	              if(isset($tab_var[$LISTE_CONFIG])){
	              	$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
	              	$NB_checkbox=$NB_checkbox+1;
	              }else{
	              	$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE]='off';
	              }

	              if($CHANGEMENT_FAR_CONFIG_TABLE_COM=='oui'){
		              $LISTE_CONFIG='CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID.'_'.$CHANGEMENT_FAR_CONFIG_TABLE_ID.'_COM';
		              $CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE_COM=$CHANGEMENT_FAR_CONFIG_ID.'_'.$CHANGEMENT_FAR_CONFIG_TABLE_ID.'_COM';
		              if(isset($tab_var[$LISTE_CONFIG])){
		              	$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE_COM]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
		              	
		              }else{
		              	$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE_COM]='';
		              }
	              }

	              

	              if($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE]=='on'){
	              	      $rq_info="
		              SELECT 
		              `CHANGEMENT_FAR_VALEUR`
		              FROM `changement_far`
		              WHERE 
		              `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."' 
		              AND `CHANGEMENT_FAR_CONFIG_ID` ='".$CHANGEMENT_FAR_CONFIG_ID."' 
		              AND `CHANGEMENT_FAR_INFO_AUTRE_ID` ='".$CHANGEMENT_FAR_INFO_AUTRE_ID."' 
		              AND `ENABLE` ='0'
		              LIMIT 1";
		              $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
		              $tab_rq_info = mysql_fetch_assoc($res_rq_info);
		              $total_ligne_rq_info=mysql_num_rows($res_rq_info);
		              mysql_free_result($res_rq_info);
		              if($total_ligne_rq_info==0){
		              	$nb_modif=$nb_modif+1;
				$sql="INSERT INTO `changement_far` (
				`CHANGEMENT_FAR_ID` ,
				`CHANGEMENT_LISTE_ID` ,
				`CHANGEMENT_FAR_CONFIG_ID` ,
				`CHANGEMENT_FAR_INFO_AUTRE_ID` ,
				`CHANGEMENT_FAR_VALEUR`,
				`ENABLE` ) 
				VALUES (NULL ,
				 '".$CHANGEMENT_ID."', 
				 '".$CHANGEMENT_FAR_CONFIG_ID."', 
				 '".$CHANGEMENT_FAR_INFO_AUTRE_ID."', 
				 '".$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE_COM]."', 
				 '0');";
				mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
				$TABLE_SQL_SQL='changement_far';       
				historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');
		              }else{
		                $TEST_CHANGEMENT_FAR_INFO_LIB=$tab_rq_info['CHANGEMENT_FAR_VALEUR'];
		                if($TEST_CHANGEMENT_FAR_INFO_LIB!=$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE_COM]){
		                	$nb_modif=$nb_modif+1;
					$sql="UPDATE  `changement_far` SET
					`CHANGEMENT_FAR_VALEUR`='".$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE_COM]."'
					WHERE `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."' 
			              AND `CHANGEMENT_FAR_CONFIG_ID` ='".$CHANGEMENT_FAR_CONFIG_ID."' 
			              AND `CHANGEMENT_FAR_INFO_AUTRE_ID` ='".$CHANGEMENT_FAR_INFO_AUTRE_ID."';";
					mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
					$TABLE_SQL_SQL='changement_far';       
					historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');
		                }
		              }
	              }else{
	              	      $rq_info="
		              SELECT 
		              `CHANGEMENT_FAR_ID`
		              FROM `changement_far`
		              WHERE 
		              `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."' 
		              AND `CHANGEMENT_FAR_CONFIG_ID` ='".$CHANGEMENT_FAR_CONFIG_ID."' 
		              AND `CHANGEMENT_FAR_INFO_AUTRE_ID` ='".$CHANGEMENT_FAR_INFO_AUTRE_ID."'  
		              AND `ENABLE` ='0'
		              LIMIT 1";
		              $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
		              $tab_rq_info = mysql_fetch_assoc($res_rq_info);
		              $total_ligne_rq_info=mysql_num_rows($res_rq_info);
		              mysql_free_result($res_rq_info);
		              if($total_ligne_rq_info!=0){
		              	$nb_modif=$nb_modif+1;
		              	$sql="
		              	UPDATE `changement_far` SET `ENABLE` = '1' 
	              		WHERE `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."' 
				AND `CHANGEMENT_FAR_CONFIG_ID` ='".$CHANGEMENT_FAR_CONFIG_ID."'
				AND `CHANGEMENT_FAR_INFO_AUTRE_ID` ='".$CHANGEMENT_FAR_INFO_AUTRE_ID."'
				AND `ENABLE`='0'
				LIMIT 1 ;";
				mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
				$TABLE_SQL_SQL='changement_far';       
				historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');
		              }
	              }
	              
                
                } while ($tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table));
                $ligne= mysql_num_rows($res_rq_info_config_table);
                if($ligne > 0) {
                  mysql_data_seek($res_rq_info_config_table, 0);
                  $tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
                }

              }
              mysql_free_result($res_rq_info_config_table);
		
	break;
	
	case "varchar": 
		$CHANGEMENT_FAR_INFO_AUTRE_ID=0;
		$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		$rq_info="
    SELECT 
    `CHANGEMENT_FAR_ID`
    FROM `changement_far`
    WHERE 
    `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."' 
    AND `CHANGEMENT_FAR_CONFIG_ID` ='".$CHANGEMENT_FAR_CONFIG_ID."' 
    AND `CHANGEMENT_FAR_INFO_AUTRE_ID` ='".$CHANGEMENT_FAR_INFO_AUTRE_ID."' 
    AND `CHANGEMENT_FAR_VALEUR` ='".$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]."'  
    AND `ENABLE` ='0'
    LIMIT 1";
    
    $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
    $tab_rq_info = mysql_fetch_assoc($res_rq_info);
    $total_ligne_rq_info=mysql_num_rows($res_rq_info);
    mysql_free_result($res_rq_info);
    if($total_ligne_rq_info==0){
      $nb_modif=$nb_modif+1;
      $sql="
      UPDATE `changement_far` SET 
      `CHANGEMENT_FAR_VALEUR`= '".$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]."'
      WHERE `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."'
      AND `CHANGEMENT_FAR_CONFIG_ID`='".$CHANGEMENT_FAR_CONFIG_ID."'
      AND `CHANGEMENT_FAR_INFO_AUTRE_ID`= '".$CHANGEMENT_FAR_INFO_AUTRE_ID."'
      AND `ENABLE`='0'
      LIMIT 1 ;";
      mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
      $TABLE_SQL_SQL='changement_far';       
      historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');
    }
	break;
	
	case "text": 
		$CHANGEMENT_FAR_INFO_AUTRE_ID=0;
		$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		$rq_info="
    SELECT 
    `CHANGEMENT_FAR_ID`
    FROM `changement_far`
    WHERE 
    `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."' 
    AND `CHANGEMENT_FAR_CONFIG_ID` ='".$CHANGEMENT_FAR_CONFIG_ID."' 
    AND `CHANGEMENT_FAR_INFO_AUTRE_ID` ='".$CHANGEMENT_FAR_INFO_AUTRE_ID."' 
    AND `CHANGEMENT_FAR_VALEUR` ='".$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]."'  
    AND `ENABLE` ='0'
    LIMIT 1";
    
    $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
    $tab_rq_info = mysql_fetch_assoc($res_rq_info);
    $total_ligne_rq_info=mysql_num_rows($res_rq_info);
    mysql_free_result($res_rq_info);
    if($total_ligne_rq_info==0){
      $nb_modif=$nb_modif+1;
      $sql="
      UPDATE `changement_far` SET 
      `CHANGEMENT_FAR_VALEUR`= '".$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]."'
      WHERE `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."'
      AND `CHANGEMENT_FAR_CONFIG_ID`='".$CHANGEMENT_FAR_CONFIG_ID."'
      AND `CHANGEMENT_FAR_INFO_AUTRE_ID`= '".$CHANGEMENT_FAR_INFO_AUTRE_ID."'
      AND `ENABLE`='0'
      LIMIT 1 ;";
      mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
      $TABLE_SQL_SQL='changement_far';       
      historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');
    }
	break;
	case "risque": 
		$CHANGEMENT_FAR_INFO_AUTRE_ID=0;
		$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		$rq_info="
    SELECT 
    `CHANGEMENT_FAR_ID`
    FROM `changement_far`
    WHERE 
    `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."' 
    AND `CHANGEMENT_FAR_CONFIG_ID` ='".$CHANGEMENT_FAR_CONFIG_ID."' 
    AND `CHANGEMENT_FAR_INFO_AUTRE_ID` ='".$CHANGEMENT_FAR_INFO_AUTRE_ID."' 
    AND `CHANGEMENT_FAR_VALEUR` ='".$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]."'  
    AND `ENABLE` ='0'
    LIMIT 1";
    
    $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
    $tab_rq_info = mysql_fetch_assoc($res_rq_info);
    $total_ligne_rq_info=mysql_num_rows($res_rq_info);
    mysql_free_result($res_rq_info);
    if($total_ligne_rq_info==0){
      $nb_modif=$nb_modif+1;
      $sql="
      UPDATE `changement_far` SET 
      `CHANGEMENT_FAR_VALEUR`= '".$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]."'
      WHERE `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."'
      AND `CHANGEMENT_FAR_CONFIG_ID`='".$CHANGEMENT_FAR_CONFIG_ID."'
      AND `CHANGEMENT_FAR_INFO_AUTRE_ID`= '".$CHANGEMENT_FAR_INFO_AUTRE_ID."'
      AND `ENABLE`='0'
      LIMIT 1 ;";
      mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
      $TABLE_SQL_SQL='changement_far';       
      historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');
    }
	break;
	case "oui-non": 
		$CHANGEMENT_FAR_INFO_AUTRE_ID=0;
		$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		$rq_info="
    SELECT 
    `CHANGEMENT_FAR_ID`
    FROM `changement_far`
    WHERE 
    `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."' 
    AND `CHANGEMENT_FAR_CONFIG_ID` ='".$CHANGEMENT_FAR_CONFIG_ID."' 
    AND `CHANGEMENT_FAR_INFO_AUTRE_ID` ='".$CHANGEMENT_FAR_INFO_AUTRE_ID."' 
    AND `CHANGEMENT_FAR_VALEUR` ='".$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]."'  
    AND `ENABLE` ='0'
    LIMIT 1";
    
    $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
    $tab_rq_info = mysql_fetch_assoc($res_rq_info);
    $total_ligne_rq_info=mysql_num_rows($res_rq_info);
    mysql_free_result($res_rq_info);
    if($total_ligne_rq_info==0){
      $nb_modif=$nb_modif+1;
      $sql="
      UPDATE `changement_far` SET 
      `CHANGEMENT_FAR_VALEUR`= '".$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]."'
      WHERE `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."'
      AND `CHANGEMENT_FAR_CONFIG_ID`='".$CHANGEMENT_FAR_CONFIG_ID."'
      AND `CHANGEMENT_FAR_INFO_AUTRE_ID`= '".$CHANGEMENT_FAR_INFO_AUTRE_ID."'
      AND `ENABLE`='0'
      LIMIT 1 ;";
      mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
      $TABLE_SQL_SQL='changement_far';       
      historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');
    }
	break;
	}
	
	
	
	} while ($tab_rq_info_config = mysql_fetch_assoc($res_rq_info_config));
	$ligne= mysql_num_rows($res_rq_info_config);
	if($ligne > 0) {
		mysql_data_seek($res_rq_info_config, 0);
		$tab_rq_info_config = mysql_fetch_assoc($res_rq_info_config);
	}
	}
	$rq_info_status="
			SELECT `changement_status`.`CHANGEMENT_STATUS` ,`changement_liste`.`CHANGEMENT_STATUS_ID`
			FROM `changement_liste` , `changement_status` 
			WHERE `changement_liste`.`CHANGEMENT_STATUS_ID` = `changement_status`.`CHANGEMENT_STATUS_ID` 
			AND `changement_liste`.`CHANGEMENT_LISTE_ID` = '".$ID."'
			AND `changement_liste`.`ENABLE` = '0'";
			$res_rq_info_status = mysql_query($rq_info_status, $mysql_link) or die(mysql_error());
			$tab_rq_info_status = mysql_fetch_assoc($res_rq_info_status);
			$CHANGEMENT_STATUS=$tab_rq_info_status['CHANGEMENT_STATUS'];
			$CHANGEMENT_STATUS_ID=$tab_rq_info_status['CHANGEMENT_STATUS_ID'];
			
			$total_ligne_rq_info_status=mysql_num_rows($res_rq_info_status);
			mysql_free_result($res_rq_info_status); 
			
        if($nb_modif!=0){
        if($CHANGEMENT_STATUS=='Brouillon'){
		$sql="UPDATE `changement_liste` SET 
		`CHANGEMENT_LISTE_UTILISATEUR_ID`= '".$UTILISATEUR_ID."',
		`CHANGEMENT_LISTE_DATE_MODIFICATION`= '".$DATE_MODIFICATION."'
		WHERE `CHANGEMENT_LISTE_ID` ='".$ID."' LIMIT 1 ;";		
		mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
		$TABLE_SQL_SQL='changement_liste';       
		historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');
		}
        }
        
        if($STOP==0){
        	if(isset($tab_var['CHANGEMENT_FAR_INSCRIPTION'])){
        		$CHANGEMENT_FAR_INSCRIPTION=$tab_var['CHANGEMENT_FAR_INSCRIPTION'];
        	}else{
        		$CHANGEMENT_FAR_INSCRIPTION='';
		}	
		  
        	if($CHANGEMENT_FAR_INSCRIPTION=='Oui'){
        		

			if($CHANGEMENT_STATUS=='Brouillon'){
				$nb_modif=$nb_modif+1; 
		        	$rq_info_status="
				SELECT `CHANGEMENT_STATUS_ID` 
				FROM `changement_status` 
				WHERE `CHANGEMENT_STATUS` = 'Inscrit'
				AND `ENABLE`='0'";
				$res_rq_info_status = mysql_query($rq_info_status, $mysql_link) or die(mysql_error());
				$tab_rq_info_status = mysql_fetch_assoc($res_rq_info_status);
				$total_ligne_rq_info_status=mysql_num_rows($res_rq_info_status);
				if($total_ligne_rq_info_status==0){
					$sql="INSERT INTO `changement_status` (`CHANGEMENT_STATUS_ID` ,`CHANGEMENT_STATUS`, `ENABLE` )VALUES (NULL , 'Inscrit', '0');";
			        	mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
				        $TABLE_SQL_SQL='changement_status';       
				        historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');
				        $rq_info_status="
					SELECT `CHANGEMENT_STATUS_ID` 
					FROM `changement_status` 
					WHERE `CHANGEMENT_STATUS` = 'Inscrit'
					AND `ENABLE`='0'";
					$res_rq_info_status = mysql_query($rq_info_status, $mysql_link) or die(mysql_error());
					$tab_rq_info_status = mysql_fetch_assoc($res_rq_info_status);
					$total_ligne_rq_info_status=mysql_num_rows($res_rq_info_status);
				}
				$CHANGEMENT_STATUS_ID=$tab_rq_info_status['CHANGEMENT_STATUS_ID'];
				mysql_free_result($res_rq_info_status); 
				$sql="UPDATE `changement_liste` SET 
				`CHANGEMENT_STATUS_ID` = '".$CHANGEMENT_STATUS_ID."',
				`CHANGEMENT_LISTE_UTILISATEUR_ID`= '".$UTILISATEUR_ID."',
				`CHANGEMENT_LISTE_DATE_MODIFICATION`= '".$DATE_MODIFICATION."'
				WHERE `CHANGEMENT_LISTE_ID` ='".$ID."' LIMIT 1 ;";		
				mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
				$TABLE_SQL_SQL='changement_liste';       
				historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');
				
				$TRACE_ETAT=$CHANGEMENT_STATUS_ID;
				if($nb_modif!=0){
		        $sql="OPTIMIZE TABLE `changement_far` ";
		        mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
		
		        $TABLE_SQL_SQL='changement_far';       
		        historique_sql_new($sql,$TABLE_SQL_SQL,'OPTIMIZE');
		        
		        $TRACE_CATEGORIE='Changement';
		        $TRACE_TABLE='changement_far';
		        $TRACE_REF_ID=$ID;
		        $TRACE_ACTION='Modif';
		        moteur_trace($TRACE_CATEGORIE,$TRACE_TABLE,$TRACE_REF_ID,$TRACE_ACTION,$TRACE_ETAT);
		}
				
				// mail d'inscription	
				/*
				$objet='';
				if ( $ENV != "x" )
				{	
					$objet='[dev]-';
				}		
				$objet.='-=Gestion des changements=- Inscription de la demande n '.$ID.'.';
				list ($Personne_abrege,$Personne_complet,$Personne_email,$Personne_email_FULL)=Recherche_SQL_nom_FULL($UTILISATEUR_ID);
				$Corps_mail = Constitue_corps_Message($CHANGEMENT_ID,$Personne_complet,$mysql_link);
				$Corps_mail_SVG = Constitue_corps_Message_SVG($CHANGEMENT_ID,$ENV,$Personne_email_FULL,$Personne_complet,$objet,$mysql_link);
				mailMailInfo_php($CHANGEMENT_ID,$Personne_email_FULL, $Personne_abrege, $objet, $Corps_mail, $TRACE_ETAT, $ENV,$mysql_link);
				sauvegarde_MailInfo( $Corps_mail_SVG, $CHANGEMENT_ID, $TRACE_ETAT,$UTILISATEUR_ID,$ENV,$mysql_link);
				*/
				echo '
      <script language="JavaScript">
      url=("./index.php?ITEM=changement_Send_Mail&type=inscription&ID='.$ID.'");
      window.location=url;
      </script>
      ';
			}

		}else{
      $rq_info_CHANGEMENT_STATUS_ID="
      SELECT 
      `CHANGEMENT_STATUS_ID`
      FROM `changement_liste`
      WHERE 
      `CHANGEMENT_LISTE_ID` ='".$ID."' 
      AND `ENABLE` ='0'
      LIMIT 1";
      $res_rq_info_CHANGEMENT_STATUS_ID = mysql_query($rq_info_CHANGEMENT_STATUS_ID, $mysql_link) or die(mysql_error());
      $tab_rq_info_CHANGEMENT_STATUS_ID = mysql_fetch_assoc($res_rq_info_CHANGEMENT_STATUS_ID);
      $total_ligne_rq_info_CHANGEMENT_STATUS_ID=mysql_num_rows($res_rq_info_CHANGEMENT_STATUS_ID);
      $CHANGEMENT_STATUS_ID=$tab_rq_info_CHANGEMENT_STATUS_ID['CHANGEMENT_STATUS_ID'];
      mysql_free_result($res_rq_info_CHANGEMENT_STATUS_ID);
      $TRACE_ETAT=$CHANGEMENT_STATUS_ID;
      if($nb_modif!=0){
		        $sql="OPTIMIZE TABLE `changement_far` ";
		        mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
		
		        $TABLE_SQL_SQL='changement_far';       
		        historique_sql_new($sql,$TABLE_SQL_SQL,'OPTIMIZE');
		        
		        $TRACE_CATEGORIE='Changement';
		        $TRACE_TABLE='changement_far';
		        $TRACE_REF_ID=$ID;
		        $TRACE_ACTION='Modif';
		        moteur_trace($TRACE_CATEGORIE,$TRACE_TABLE,$TRACE_REF_ID,$TRACE_ACTION,$TRACE_ETAT);
		}
		}
		
		
        	
        echo '
        <script language="JavaScript">
        url=("./index.php?ITEM=changement_Info_Changement&action=Info&ID='.$ID.'");
        window.location=url;
        </script>
        ';
        }
	}
	//mysql_free_result($res_rq_info_far);
	$_GET['action']="Modif";
  	
  }
}
//# Cas action Info
if($action=="Info"){
	$rq_info_far="
	SELECT * 
	FROM `changement_far`
	WHERE `CHANGEMENT_LISTE_ID`='".$ID."'
	AND`ENABLE` = '0'
	";
	$res_rq_info_far = mysql_query($rq_info_far, $mysql_link) or die(mysql_error());
	$tab_rq_info_far = mysql_fetch_assoc($res_rq_info_far);
	$total_ligne_rq_info_far=mysql_num_rows($res_rq_info_far);
	
	if($total_ligne_rq_info_far!=0){
		do {
			$CHANGEMENT_FAR_CONFIG_ID=$tab_rq_info_far['CHANGEMENT_FAR_CONFIG_ID'];
			$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]=$tab_rq_info_far['CHANGEMENT_FAR_VALEUR'];         
		} while ($tab_rq_info_far = mysql_fetch_assoc($res_rq_info_far));
		$ligne= mysql_num_rows($res_rq_info_far);
		if($ligne > 0) {
		  mysql_data_seek($res_rq_info_far, 0);
		  $tab_rq_info_far = mysql_fetch_assoc($res_rq_info_far);
		}
	}
	mysql_free_result($res_rq_info_far);
        $rq_info_far="
	SELECT `changement_status`.`CHANGEMENT_STATUS` 
	FROM `changement_liste` , `changement_status` 
	WHERE `changement_liste`.`CHANGEMENT_LISTE_ID` = '".$ID."'
	AND `changement_liste`.`CHANGEMENT_STATUS_ID` = `changement_status`.`CHANGEMENT_STATUS_ID` 
	AND `changement_status`.`CHANGEMENT_STATUS` = 'Brouillon'
	AND `changement_liste`.`ENABLE` = '0'
	";
	$res_rq_info_far = mysql_query($rq_info_far, $mysql_link) or die(mysql_error());
	$tab_rq_info_far = mysql_fetch_assoc($res_rq_info_far);
	$total_ligne_rq_info_far=mysql_num_rows($res_rq_info_far);

	if($total_ligne_rq_info_far==0){
		$CHANGEMENT_FAR_INSCRIPTION="Oui";
	}else{
		$CHANGEMENT_FAR_INSCRIPTION="Non";
	}
	mysql_free_result($res_rq_info_far);
}
//# Cas action modifer
if($action=="Modif"){
	$rq_info_far="
	SELECT * 
	FROM `changement_far`
	WHERE `CHANGEMENT_LISTE_ID`='".$ID."'
	AND`ENABLE` = '0'
	";
	$res_rq_info_far = mysql_query($rq_info_far, $mysql_link) or die(mysql_error());
	$tab_rq_info_far = mysql_fetch_assoc($res_rq_info_far);
	$total_ligne_rq_info_far=mysql_num_rows($res_rq_info_far);
	
	if($total_ligne_rq_info_far!=0){
		do {
			$CHANGEMENT_FAR_CONFIG_ID=$tab_rq_info_far['CHANGEMENT_FAR_CONFIG_ID'];
			$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]=$tab_rq_info_far['CHANGEMENT_FAR_VALEUR'];         
		} while ($tab_rq_info_far = mysql_fetch_assoc($res_rq_info_far));
		$ligne= mysql_num_rows($res_rq_info_far);
		if($ligne > 0) {
		  mysql_data_seek($res_rq_info_far, 0);
		  $tab_rq_info_far = mysql_fetch_assoc($res_rq_info_far);
		}
	}
	mysql_free_result($res_rq_info_far);
        $rq_info_far="
	SELECT `changement_status`.`CHANGEMENT_STATUS` 
	FROM `changement_liste` , `changement_status` 
	WHERE `changement_liste`.`CHANGEMENT_LISTE_ID` = '".$ID."'
	AND `changement_liste`.`CHANGEMENT_STATUS_ID` = `changement_status`.`CHANGEMENT_STATUS_ID` 
	AND `changement_status`.`CHANGEMENT_STATUS` = 'Brouillon'
	AND `changement_liste`.`ENABLE` = '0'
	";
	$res_rq_info_far = mysql_query($rq_info_far, $mysql_link) or die(mysql_error());
	$tab_rq_info_far = mysql_fetch_assoc($res_rq_info_far);
	$total_ligne_rq_info_far=mysql_num_rows($res_rq_info_far);

	if($total_ligne_rq_info_far==0){
		$CHANGEMENT_FAR_INSCRIPTION="Oui";
	}else{
		$CHANGEMENT_FAR_INSCRIPTION="Non";
	}
	mysql_free_result($res_rq_info_far);
}
//# Cas action modifer
if($action=="Ajout"){
	$rq_info_far="
	SELECT `changement_status`.`CHANGEMENT_STATUS` 
	FROM `changement_liste` , `changement_status` 
	WHERE `changement_liste`.`CHANGEMENT_LISTE_ID` = '".$ID."'
	AND `changement_liste`.`CHANGEMENT_STATUS_ID` = `changement_status`.`CHANGEMENT_STATUS_ID` 
	AND `changement_status`.`CHANGEMENT_STATUS` = 'Brouillon'
	AND `changement_liste`.`ENABLE` = '0'
	";
	$res_rq_info_far = mysql_query($rq_info_far, $mysql_link) or die(mysql_error());
	$tab_rq_info_far = mysql_fetch_assoc($res_rq_info_far);
	$total_ligne_rq_info_far=mysql_num_rows($res_rq_info_far);

	if($total_ligne_rq_info_far==0){
		$CHANGEMENT_FAR_INSCRIPTION="Oui";
	}else{
		$CHANGEMENT_FAR_INSCRIPTION="Non";
	}
	mysql_free_result($res_rq_info_far);
}
//# Cas action Info
if($action=="Info"){
	$rq_info_far="
	SELECT `changement_status`.`CHANGEMENT_STATUS` 
	FROM `changement_liste` , `changement_status` 
	WHERE `changement_liste`.`CHANGEMENT_LISTE_ID` = '".$ID."'
	AND `changement_liste`.`CHANGEMENT_STATUS_ID` = `changement_status`.`CHANGEMENT_STATUS_ID` 
	AND `changement_liste`.`ENABLE` = '0'
	";
	$res_rq_info_far = mysql_query($rq_info_far, $mysql_link) or die(mysql_error());
	$tab_rq_info_far = mysql_fetch_assoc($res_rq_info_far);
	$total_ligne_rq_info_far=mysql_num_rows($res_rq_info_far);
	if($total_ligne_rq_info_far==0){
		$CHANGEMENT_STATUS='Brouillon';
		$CHANGEMENT_FAR_INSCRIPTION="Oui";
	}else{
		$CHANGEMENT_STATUS=$tab_rq_info_far['CHANGEMENT_STATUS'];;
		if($CHANGEMENT_STATUS=='Brouillon'){
			$CHANGEMENT_FAR_INSCRIPTION="Non";
		}else{
			$CHANGEMENT_FAR_INSCRIPTION="Oui";
		}
		
	}
	mysql_free_result($res_rq_info_far);
}
if($action=="Modif"){
	$rq_info_far="
	SELECT `changement_status`.`CHANGEMENT_STATUS` 
	FROM `changement_liste` , `changement_status` 
	WHERE `changement_liste`.`CHANGEMENT_LISTE_ID` = '".$ID."'
	AND `changement_liste`.`CHANGEMENT_STATUS_ID` = `changement_status`.`CHANGEMENT_STATUS_ID` 
	AND `changement_liste`.`ENABLE` = '0'
	";
	$res_rq_info_far = mysql_query($rq_info_far, $mysql_link) or die(mysql_error());
	$tab_rq_info_far = mysql_fetch_assoc($res_rq_info_far);
	$total_ligne_rq_info_far=mysql_num_rows($res_rq_info_far);
	if($total_ligne_rq_info_far==0){
		$CHANGEMENT_STATUS='Brouillon';
		$CHANGEMENT_FAR_INSCRIPTION="Oui";
	}else{
		$CHANGEMENT_STATUS=$tab_rq_info_far['CHANGEMENT_STATUS'];;
		if($CHANGEMENT_STATUS=='Brouillon'){
			$CHANGEMENT_FAR_INSCRIPTION="Non";
		}else{
			$CHANGEMENT_FAR_INSCRIPTION="Oui";
		}
		
	}
	mysql_free_result($res_rq_info_far);
}
echo '
<!--D&eacute;but page HTML -->  
<div align="center">
<form method="post" name="frm_inter" id="frm_inter" action="./index.php?ITEM='.$_GET['ITEM'].'">
  <table class="table_inc">';
  $rq_info_id="
	SELECT 
	`moteur_trace`.`MOTEUR_TRACE_UTILISATEUR_ID`, 
	`moteur_trace`.`MOTEUR_TRACE_DATE`, 
	`moteur_trace`.`MOTEUR_TRACE_CATEGORIE`, 
	`moteur_trace`.`MOTEUR_TRACE_TABLE`, 
	`moteur_trace`.`MOTEUR_TRACE_REF_ID`, 
	`moteur_trace`.`MOTEUR_TRACE_ACTION`, 
	`changement_status`.`CHANGEMENT_STATUS`,
	`moteur_utilisateur`.`LOGIN`, 
	`moteur_utilisateur`.`NOM`, 
	`moteur_utilisateur`.`PRENOM` 
	FROM `moteur_trace`,`moteur_utilisateur`,`changement_status`
	WHERE 
	`moteur_trace`.`MOTEUR_TRACE_CATEGORIE`='Changement'
	AND `moteur_trace`.`MOTEUR_TRACE_UTILISATEUR_ID`=`moteur_utilisateur`.`UTILISATEUR_ID` 
	AND `moteur_trace`.`MOTEUR_TRACE_ETAT`=`changement_status`.`CHANGEMENT_STATUS_ID` 
	AND `moteur_trace`.`MOTEUR_TRACE_REF_ID`='".$ID."'
	ORDER BY `moteur_trace`.`MOTEUR_TRACE_DATE` DESC";
	$res_rq_info_id = mysql_query($rq_info_id, $mysql_link) or die(mysql_error());
	$tab_rq_info_id = mysql_fetch_assoc($res_rq_info_id);
	$total_ligne_rq_info_id=mysql_num_rows($res_rq_info_id);
	$AFF_SPAN='';
	if($total_ligne_rq_info_id!=0){
		do {
		
		$MOTEUR_TRACE_DATE=$tab_rq_info_id['MOTEUR_TRACE_DATE'];
		$MOTEUR_TRACE_CATEGORIE=$tab_rq_info_id['MOTEUR_TRACE_CATEGORIE'];
		$MOTEUR_TRACE_TABLE=str_replace('changement_','',$tab_rq_info_id['MOTEUR_TRACE_TABLE']);
		$MOTEUR_TRACE_REF_ID=$tab_rq_info_id['MOTEUR_TRACE_REF_ID'];
		$MOTEUR_TRACE_ACTION=$tab_rq_info_id['MOTEUR_TRACE_ACTION'];
		$MOTEUR_TRACE_CHANGEMENT_STATUS=$tab_rq_info_id['CHANGEMENT_STATUS'];
		$MOTEUR_TRACE_NOM=$tab_rq_info_id['NOM'];
		$MOTEUR_TRACE_PRENOM=$tab_rq_info_id['PRENOM'];
		$AFF_SPAN.=$MOTEUR_TRACE_DATE.' - '.$MOTEUR_TRACE_PRENOM.' '.$MOTEUR_TRACE_NOM.' - '.$MOTEUR_TRACE_ACTION.' - '.$MOTEUR_TRACE_TABLE.' - '.$MOTEUR_TRACE_CHANGEMENT_STATUS.'</BR>';
		 } while ($tab_rq_info_id = mysql_fetch_assoc($res_rq_info_id));
	        $ligne= mysql_num_rows($res_rq_info_id);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_id, 0);
	          $tab_rq_info_id = mysql_fetch_assoc($res_rq_info_id);
	        }
	}
	mysql_free_result($res_rq_info_id);
	echo '<tr align="center" class="titre">
        <td colspan="2"><h2>&nbsp;[&nbsp;';
    if($action=="Ajout"){
      echo '
      Cr&eacute;ation de la FAR du changement n&deg; <a href="#" class="infobulledroite">'.$ID.'<span>'.$AFF_SPAN.'</span></a>';
    }
    if($action=="Modif"){
      if($CHANGEMENT_STATUS=='Brouillon'){
        echo 'Modification de la FAR du changement n&deg; <a href="#" class="infobulledroite">'.$ID.'<span>'.$AFF_SPAN.'</span></a>';
      }else{
        if($ROLE==0){
          echo 'Modification de la FAR du changement n&deg; <a href="#" class="infobulledroite">'.$ID.'<span>'.$AFF_SPAN.'</span></a>';
        }else{
          echo 'Information de la FAR du changement n&deg; <a href="#" class="infobulledroite">'.$ID.'<span>'.$AFF_SPAN.'</span></a>';
        }
      }
    }
    if($action=="Info"){
    	echo 'Information de la FAR du changement n&deg; <a href="#" class="infobulledroite">'.$ID.'<span>'.$AFF_SPAN.'</span></a>';
    }
    
    echo '&nbsp;]&nbsp;</h2></td>		
      </tr>';
	if($action=="Ajout"){
    $rq_info_far_lib="
      SELECT DISTINCT (`CHANGEMENT_FAR_CONFIG_LIB`) AS `CHANGEMENT_FAR_CONFIG_LIB`
      FROM `changement_far_config` 
      WHERE 
      `ENABLE` ='0'
      ORDER BY `CHANGEMENT_FAR_CONFIG_LIB` 
      ";
	}else{
        $rq_info_far_lib="
        SELECT DISTINCT (`changement_far_config`.`CHANGEMENT_FAR_CONFIG_LIB`) AS `CHANGEMENT_FAR_CONFIG_LIB`
        FROM `changement_far_config` , `changement_far`
        WHERE `changement_far_config`.`CHANGEMENT_FAR_CONFIG_ID` = `changement_far`.`CHANGEMENT_FAR_CONFIG_ID`
        AND `changement_far`.`CHANGEMENT_LISTE_ID` = '".$ID."'
        AND `changement_far`.`ENABLE` = '0'
        ORDER BY `changement_far_config`.`CHANGEMENT_FAR_CONFIG_LIB`";
      }
      $res_rq_info_far_lib = mysql_query($rq_info_far_lib, $mysql_link) or die(mysql_error());
      $tab_rq_info_far_lib = mysql_fetch_assoc($res_rq_info_far_lib);
      $total_ligne_rq_info_far_lib=mysql_num_rows($res_rq_info_far_lib);
      if($total_ligne_rq_info_far_lib!=0){
        do {
        	$CHANGEMENT_FAR_CONFIG_LIB=$tab_rq_info_far_lib['CHANGEMENT_FAR_CONFIG_LIB'];
      echo '
      <tr align="center" class="titre">
        <td colspan="2">&nbsp;'.stripslashes(substr($CHANGEMENT_FAR_CONFIG_LIB,strpos($CHANGEMENT_FAR_CONFIG_LIB,"-")+1)).'&nbsp;</td>		
      </tr>';
		if($action=="Ajout"){
              	$rq_info_far="
              	SELECT 
              	`changement_far_config`.`CHANGEMENT_FAR_CONFIG_ID` , 
              	`changement_far_config`.`CHANGEMENT_FAR_CONFIG_LIB` , 
              	`changement_far_config`.`CHANGEMENT_FAR_CONFIG_CRITERE` , 
              	`changement_far_config`.`CHANGEMENT_FAR_CONFIG_TYPE` , 
              	`changement_far_config`.`CHANGEMENT_FAR_CONFIG_TABLE` , 
              	`changement_far_config`.`CHANGEMENT_FAR_CONFIG_OBLIGATOIRE` , 
              	`changement_far_config`.`CHANGEMENT_FAR_CONFIG_ORDRE` , 
              	`changement_far_config`.`ENABLE` 
              	FROM `changement_far_config` 
              	WHERE `changement_far_config`.`CHANGEMENT_FAR_CONFIG_LIB`='".$CHANGEMENT_FAR_CONFIG_LIB."' AND 
              	`changement_far_config`.`ENABLE` = '0'
              	ORDER BY `changement_far_config`.`CHANGEMENT_FAR_CONFIG_ORDRE` 

              	";
        	}else{
                  $rq_info_far="
                  SELECT 
                  `changement_far_config`.`CHANGEMENT_FAR_CONFIG_ID` , 
              	  `changement_far_config`.`CHANGEMENT_FAR_CONFIG_LIB` , 
              	  `changement_far_config`.`CHANGEMENT_FAR_CONFIG_CRITERE` , 
              	  `changement_far_config`.`CHANGEMENT_FAR_CONFIG_TYPE` , 
              	  `changement_far_config`.`CHANGEMENT_FAR_CONFIG_TABLE` , 
              	  `changement_far_config`.`CHANGEMENT_FAR_CONFIG_OBLIGATOIRE` , 
              	  `changement_far_config`.`CHANGEMENT_FAR_CONFIG_ORDRE` , 
              	  `changement_far_config`.`ENABLE` 
                  FROM `changement_far_config` , `changement_far`
                  WHERE `changement_far_config`.`CHANGEMENT_FAR_CONFIG_ID` = `changement_far`.`CHANGEMENT_FAR_CONFIG_ID`
                  AND `changement_far`.`CHANGEMENT_LISTE_ID` = '".$ID."'
                  AND `changement_far_config`.`CHANGEMENT_FAR_CONFIG_LIB`='".$CHANGEMENT_FAR_CONFIG_LIB."' 
                  AND `changement_far`.`ENABLE` = '0'
                  GROUP BY `changement_far_config`.`CHANGEMENT_FAR_CONFIG_ID`
                  ORDER BY `changement_far_config`.`CHANGEMENT_FAR_CONFIG_ORDRE`
                   ";
              	}
              	
	      $res_rq_info_far = mysql_query($rq_info_far, $mysql_link) or die(mysql_error());
	      $tab_rq_info_far = mysql_fetch_assoc($res_rq_info_far);
	      $total_ligne_rq_info_far=mysql_num_rows($res_rq_info_far);
	      if($total_ligne_rq_info_far!=0){
	        do {
	        	$CHANGEMENT_FAR_CONFIG_ID=$tab_rq_info_far['CHANGEMENT_FAR_CONFIG_ID'];
	        	$CHANGEMENT_FAR_CONFIG_CRITERE=$tab_rq_info_far['CHANGEMENT_FAR_CONFIG_CRITERE'];
	        	$CHANGEMENT_FAR_CONFIG_TYPE=$tab_rq_info_far['CHANGEMENT_FAR_CONFIG_TYPE'];
	        	$CHANGEMENT_FAR_CONFIG_OBLIGATOIRE=$tab_rq_info_far['CHANGEMENT_FAR_CONFIG_OBLIGATOIRE'];
	        	if(isset($tab_rq_info_far['CHANGEMENT_FAR_INFO_AUTRE_ID'])){
	      			$CHANGEMENT_FAR_INFO_AUTRE_ID=$tab_rq_info_far['CHANGEMENT_FAR_INFO_AUTRE_ID'];
			}else{
				$CHANGEMENT_FAR_INFO_AUTRE_ID=0;
			}
			if(isset($tab_rq_info_far['CHANGEMENT_FAR_INFO_LIB'])){
	      			$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]=$tab_rq_info_far['CHANGEMENT_FAR_INFO_LIB'];
			}
			if(!isset($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID])){$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]='';}
		
	        	$info_OBLIGATOIRE='';
	       		if($CHANGEMENT_FAR_CONFIG_OBLIGATOIRE=='oui'){$info_OBLIGATOIRE='*';}
	        	if(!isset($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID])){$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]='';}
	       $j++;
		    if ($j%2) { $class = "pair";}else{$class = "impair";} 
		  echo '
		    <tr class="'.$class.'">
		     <td align="left">&nbsp;'.stripslashes($CHANGEMENT_FAR_CONFIG_CRITERE).'&nbsp;'.$info_OBLIGATOIRE.'&nbsp;</td>
		     <td align="left">';
		      switch ($CHANGEMENT_FAR_CONFIG_TYPE)
            {
            case "oui-non": 
              echo '
              <INPUT type="radio" '.$disabled_var.' name="CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID.'" value="Oui"';
              if($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]=='Oui'){echo 'CHECKED';} 
              echo '>
              &nbsp;Oui&nbsp;/&nbsp;Non&nbsp;
              <INPUT type="radio" '.$disabled_var.' name="CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID.'" value="Non"'; 
              if($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]=='Non'){echo 'CHECKED';} 
              echo '>';
              
            break;
            case "risque": 
            $AFF_SPAN_O_N='';
            $AFF_SPAN_O_N.='1 Risque Faible < > 4 Risque Fort';
              echo '
              &nbsp;1&nbsp;<INPUT type="radio" '.$disabled_var.' name="CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID.'" value="1"';
              if($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]=='1'){echo 'CHECKED';} 
              echo '>
              &nbsp;2&nbsp;<INPUT type="radio" '.$disabled_var.' name="CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID.'" value="2"'; 
              if($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]=='2'){echo 'CHECKED';} 
              echo '>
              &nbsp;3&nbsp;<INPUT type="radio" '.$disabled_var.' name="CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID.'" value="3"'; 
              if($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]=='3'){echo 'CHECKED';} 
              echo '>
              &nbsp;4&nbsp;<INPUT type="radio" '.$disabled_var.' name="CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID.'" value="4"'; 
              if($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]=='4'){echo 'CHECKED';} 
              echo '>';
              echo '&nbsp;<a href="#" class="infobullegauche"><font color=#000000>&nbsp;- Aide&nbsp;</font><span>'.$AFF_SPAN_O_N.'</span></a>';
            break;
            case "liste": 
              $CHANGEMENT_FAR_CONFIG_TABLE=$tab_rq_info_far['CHANGEMENT_FAR_CONFIG_TABLE'];
              $CHANGEMENT_FAR_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE);
              $rq_info_config_table="
              SELECT  *
              FROM `".$CHANGEMENT_FAR_CONFIG_TABLE."`
              WHERE 
              `ENABLE`='0'
              AND `".$CHANGEMENT_FAR_CONFIG_TABLE_TRI."`!='vide'
              ORDER BY `".$CHANGEMENT_FAR_CONFIG_TABLE_TRI."`";
              $res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
              $tab_rq_info_far_table = mysql_fetch_assoc($res_rq_info_config_table);
              $total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);
              if($action!='Ajout'){
              $rq_info_config_table_info="
              SELECT  `CHANGEMENT_FAR_INFO_AUTRE_ID`
              FROM `changement_far`
              WHERE 
              `CHANGEMENT_LISTE_ID`='".$ID."'
              AND `CHANGEMENT_FAR_CONFIG_ID`='".$CHANGEMENT_FAR_CONFIG_ID."'
              AND `ENABLE`='0'
              LIMIT 1";
              //echo $rq_info_config_table_info.'</BR>';
              $res_rq_info_config_table_info = mysql_query($rq_info_config_table_info, $mysql_link) or die(mysql_error());
              $tab_rq_info_far_table_info = mysql_fetch_assoc($res_rq_info_config_table_info);
              $total_ligne_rq_info_config_table_info=mysql_num_rows($res_rq_info_config_table_info);
              if($total_ligne_rq_info_config_table_info!=0){
                $CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]=$tab_rq_info_far_table_info['CHANGEMENT_FAR_INFO_AUTRE_ID'];
              }else{
                $CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]='';
              }
              mysql_free_result($res_rq_info_config_table_info);
        	}
        	if($_GET['action']=="Info"){
        		if($total_ligne_rq_info_config_table!=0){
                do {
                	$ID_SQL=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE).'_ID';
                  $ID_LIB=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE);
                  $CHANGEMENT_FAR_CONFIG_TABLE_ID=$tab_rq_info_far_table[$ID_SQL];
                  $CHANGEMENT_FAR_CONFIG_TABLE_LIB=$tab_rq_info_far_table[$ID_LIB];
                  if($CHANGEMENT_FAR_CONFIG_TABLE_ID == $CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID] ){echo $CHANGEMENT_FAR_CONFIG_TABLE_LIB;}
                	
                	} while ($tab_rq_info_far_table = mysql_fetch_assoc($res_rq_info_config_table));
                $ligne= mysql_num_rows($res_rq_info_config_table);
                if($ligne > 0) {
                  mysql_data_seek($res_rq_info_config_table, 0);
                  $tab_rq_info_far_table = mysql_fetch_assoc($res_rq_info_config_table);
                }
                }
        	}else{
              echo '
              <Select name="CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID.'" size="1" id="CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID.'" onChange="">
              <option value="0">&nbsp;</option>';
              if($total_ligne_rq_info_config_table!=0){
                do {
                  $ID_SQL=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE).'_ID';
                  $ID_LIB=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE);
                  $CHANGEMENT_FAR_CONFIG_TABLE_ID=$tab_rq_info_far_table[$ID_SQL];
                  $CHANGEMENT_FAR_CONFIG_TABLE_LIB=$tab_rq_info_far_table[$ID_LIB];
                  echo '<option value="'.$CHANGEMENT_FAR_CONFIG_TABLE_ID.'"';
                  if(!isset($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID])){
                    $CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]='';
                  }
                  if (empty($_POST)){}else{
                    if($_POST['btn']=="Ajouter"){
                      if($CHANGEMENT_FAR_CONFIG_TABLE_ID == $CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]){echo " SELECTED ";}
                    }
                    if ($_POST['btn']=="Modifier"){
                      if($CHANGEMENT_FAR_CONFIG_TABLE_ID == $CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID] ){echo " SELECTED ";}
                    }
                  }
                  if (empty($_GET)){}else{
                    if(isset($_GET['action'])){
                      if($_GET['action']=="Modif"){
                        if($CHANGEMENT_FAR_CONFIG_TABLE_ID == $CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID] ){echo " SELECTED ";}
                      }
                      if($_GET['action']=="Info"){
                        if($CHANGEMENT_FAR_CONFIG_TABLE_ID == $CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID] ){echo " SELECTED ";}
                      }
                    }
                  }
                  echo '>'.stripslashes($CHANGEMENT_FAR_CONFIG_TABLE_LIB).'</option>';
                
                } while ($tab_rq_info_far_table = mysql_fetch_assoc($res_rq_info_config_table));
                $ligne= mysql_num_rows($res_rq_info_config_table);
                if($ligne > 0) {
                  mysql_data_seek($res_rq_info_config_table, 0);
                  $tab_rq_info_far_table = mysql_fetch_assoc($res_rq_info_config_table);
                }
              }
              echo '
              </Select>';
        	}
              mysql_free_result($res_rq_info_config_table);
              
            break;
            case "checkbox": 
              $CHANGEMENT_FAR_CONFIG_TABLE=$tab_rq_info_far['CHANGEMENT_FAR_CONFIG_TABLE'];
              $CHANGEMENT_FAR_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE);
              $rq_info_config_table="
              SELECT  *
              FROM `".$CHANGEMENT_FAR_CONFIG_TABLE."`
              WHERE 
              `ENABLE`='0'
              AND `".$CHANGEMENT_FAR_CONFIG_TABLE_TRI."`!='vide'
              
              ORDER BY `".$CHANGEMENT_FAR_CONFIG_TABLE_TRI."`";
              $res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
              $tab_rq_info_far_table = mysql_fetch_assoc($res_rq_info_config_table);
              $total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);

              if($total_ligne_rq_info_config_table!=0){
              	$NB_total_ligne_rq_info_config_table=0;
                do {
                  $ID_SQL=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE).'_ID';
                  $ID_LIB=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE);
                  $COM_SQL=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE).'_COM';
                  $CHANGEMENT_FAR_CONFIG_TABLE_ID=$tab_rq_info_far_table[$ID_SQL];
                  $CHANGEMENT_FAR_CONFIG_TABLE_LIB=$tab_rq_info_far_table[$ID_LIB];
                  $CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE=$CHANGEMENT_FAR_CONFIG_ID.'_'.$CHANGEMENT_FAR_CONFIG_TABLE_ID;
                  	$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE_COM=$CHANGEMENT_FAR_CONFIG_ID.'_'.$CHANGEMENT_FAR_CONFIG_TABLE_ID.'_COM';
                  if($action!='Ajout'){

                  	$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE]='off';
                  	$rq_info_config_liste_id="
                  	SELECT `CHANGEMENT_FAR_INFO_AUTRE_ID`,`CHANGEMENT_FAR_VALEUR`
			FROM `changement_far` 
			WHERE 
			`CHANGEMENT_LISTE_ID` = '".$ID."'
			AND `CHANGEMENT_FAR_CONFIG_ID` = '".$CHANGEMENT_FAR_CONFIG_ID."'
			AND `CHANGEMENT_FAR_INFO_AUTRE_ID`='".$CHANGEMENT_FAR_CONFIG_TABLE_ID."'
			AND `ENABLE` = '0'
			LIMIT 1";
			$res_rq_info_config_liste_id = mysql_query($rq_info_config_liste_id, $mysql_link) or die(mysql_error());
			$tab_rq_info_far_liste_id = mysql_fetch_assoc($res_rq_info_config_liste_id);
			$total_ligne_rq_info_config_liste_id=mysql_num_rows($res_rq_info_config_liste_id);
			
			if($total_ligne_rq_info_config_liste_id==0){
				$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE]='off';
				$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE_COM]='';
			}else{
				$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE]='on';
				$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE_COM]=$tab_rq_info_far_liste_id['CHANGEMENT_FAR_VALEUR'];
			}
			mysql_free_result($res_rq_info_config_liste_id);
                  }
                  if(!isset($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE_COM])){
                  	$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE_COM]='';
                  }
                  if(!isset($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE])){
                  	$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE]='';
                  }
                  if(isset($tab_rq_info_far_table[$COM_SQL])){
                  	$CHANGEMENT_FAR_CONFIG_TABLE_COM=$tab_rq_info_far_table[$COM_SQL];	
                  }else{
                  	$CHANGEMENT_FAR_CONFIG_TABLE_COM='non';
                  }
                  
                  $NB_total_ligne_rq_info_config_table++;
                  echo '<INPUT TYPE="CHECKBOX" '.$disabled_var.' NAME="CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID.'_'.$CHANGEMENT_FAR_CONFIG_TABLE_ID.'"';
                  if($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE]=='on'){echo ' checked';}
                  echo '>'.$CHANGEMENT_FAR_CONFIG_TABLE_LIB.'';
                  if($CHANGEMENT_FAR_CONFIG_TABLE_COM=='oui'){
                  	if($action!='Info'){
                  	echo '&nbsp;&nbsp;<input '.$readonly_var.' id="CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID.'_'.$CHANGEMENT_FAR_CONFIG_TABLE_ID.'_COM" name="CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID.'_'.$CHANGEMENT_FAR_CONFIG_TABLE_ID.'_COM" type="text" value="'.stripslashes($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE_COM]).'" size="50" maxlength="100"/>&nbsp;*';
                	}else{
                	echo '&nbsp;:&nbsp;'.stripslashes($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE_COM]);
                	}
                  }
                  

                  	if($NB_total_ligne_rq_info_config_table < $total_ligne_rq_info_config_table){
                  		echo '</BR>';	
                  	}
    
                
                } while ($tab_rq_info_far_table = mysql_fetch_assoc($res_rq_info_config_table));
                $ligne= mysql_num_rows($res_rq_info_config_table);
                if($ligne > 0) {
                  mysql_data_seek($res_rq_info_config_table, 0);
                  $tab_rq_info_far_table = mysql_fetch_assoc($res_rq_info_config_table);
                }
              }
              mysql_free_result($res_rq_info_config_table);
              $rq_info_config_table="
              SELECT  *
              FROM `".$CHANGEMENT_FAR_CONFIG_TABLE."`
              WHERE 
              `ENABLE`='0'
              AND `".$CHANGEMENT_FAR_CONFIG_TABLE_TRI."`='vide'
              ORDER BY `".$CHANGEMENT_FAR_CONFIG_TABLE_TRI."`";
              $res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
              $tab_rq_info_far_table = mysql_fetch_assoc($res_rq_info_config_table);
              $total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);

              if($total_ligne_rq_info_config_table!=0){
                do {
                  $ID_SQL=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE).'_ID';
                  $ID_LIB=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE);
                  $CHANGEMENT_FAR_CONFIG_TABLE_ID=$tab_rq_info_far_table[$ID_SQL];
                  echo '<input type="hidden" name="CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID.'_'.$CHANGEMENT_FAR_CONFIG_TABLE_ID.'" value="on">';
                
                } while ($tab_rq_info_far_table = mysql_fetch_assoc($res_rq_info_config_table));
                $ligne= mysql_num_rows($res_rq_info_config_table);
                if($ligne > 0) {
                  mysql_data_seek($res_rq_info_config_table, 0);
                  $tab_rq_info_far_table = mysql_fetch_assoc($res_rq_info_config_table);
                }
              }
              mysql_free_result($res_rq_info_config_table);
              
              
            break;
            case "text": 
            if($action!='Info'){
              echo '<textarea '.$readonly_var.' id="CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID.'" name="CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID.'" cols="50" rows="2">'.stripslashes($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]).'</textarea>';
        	}else{
        	echo nl2br(stripslashes($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]));
        	}
              
            break;
            case "varchar": 
            if($action!='Info'){
              echo '<input '.$readonly_var.' id="CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID.'" name="CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID.'" type="text" value="'.stripslashes($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]).'" size="67" maxlength="100"/>';
        	}else{
        	echo stripslashes($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID]);
		}
            break;
            
            case "checkbox_horizontal": 

              $CHANGEMENT_FAR_CONFIG_TABLE=$tab_rq_info_far['CHANGEMENT_FAR_CONFIG_TABLE'];
              $CHANGEMENT_FAR_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE);
              $rq_info_config_table="
              SELECT  *
              FROM `".$CHANGEMENT_FAR_CONFIG_TABLE."`
              WHERE 
              `ENABLE`='0'
              AND `".$CHANGEMENT_FAR_CONFIG_TABLE_TRI."`!='vide'
              
              ORDER BY `".$CHANGEMENT_FAR_CONFIG_TABLE_TRI."`";
              $res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
              $tab_rq_info_far_table = mysql_fetch_assoc($res_rq_info_config_table);
              $total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);

              if($total_ligne_rq_info_config_table!=0){
                do {
                  $ID_SQL=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE).'_ID';
                  $ID_LIB=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE);
                  $COM_SQL=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE).'_COM';
                  $CHANGEMENT_FAR_CONFIG_TABLE_ID=$tab_rq_info_far_table[$ID_SQL];
                  $CHANGEMENT_FAR_CONFIG_TABLE_LIB=$tab_rq_info_far_table[$ID_LIB];
                  $CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE=$CHANGEMENT_FAR_CONFIG_ID.'_'.$CHANGEMENT_FAR_CONFIG_TABLE_ID;
                  	$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE_COM=$CHANGEMENT_FAR_CONFIG_ID.'_'.$CHANGEMENT_FAR_CONFIG_TABLE_ID.'_COM';
                  if($action!='Ajout'){

                  	$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE]='off';
                  	$rq_info_config_liste_id="
                  	SELECT `CHANGEMENT_FAR_INFO_AUTRE_ID`,`CHANGEMENT_FAR_VALEUR`
			FROM `changement_far` 
			WHERE 
			`CHANGEMENT_LISTE_ID` = '".$ID."'
			AND `CHANGEMENT_FAR_CONFIG_ID` = '".$CHANGEMENT_FAR_CONFIG_ID."'
			AND `CHANGEMENT_FAR_INFO_AUTRE_ID`='".$CHANGEMENT_FAR_CONFIG_TABLE_ID."'
			AND `ENABLE` = '0'
			LIMIT 1";
			$res_rq_info_config_liste_id = mysql_query($rq_info_config_liste_id, $mysql_link) or die(mysql_error());
			$tab_rq_info_far_liste_id = mysql_fetch_assoc($res_rq_info_config_liste_id);
			$total_ligne_rq_info_config_liste_id=mysql_num_rows($res_rq_info_config_liste_id);
			
			if($total_ligne_rq_info_config_liste_id==0){
				$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE]='off';
				$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE_COM]='';
			}else{
				$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE]='on';
				$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE_COM]=$tab_rq_info_far_liste_id['CHANGEMENT_FAR_VALEUR'];
			}
			mysql_free_result($res_rq_info_config_liste_id);
                  }
                  if(!isset($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE_COM])){
                  	$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE_COM]='';
                  }
                  if(!isset($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE])){
                  	$CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE]='';
                  }

                  
                  if(isset($tab_rq_info_far_table[$COM_SQL])){
                  	$CHANGEMENT_FAR_CONFIG_TABLE_COM=$tab_rq_info_far_table[$COM_SQL];	
                  }else{
                  	$CHANGEMENT_FAR_CONFIG_TABLE_COM='non';
                  }

                  echo '<INPUT TYPE="CHECKBOX" '.$disabled_var.' NAME="CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID.'_'.$CHANGEMENT_FAR_CONFIG_TABLE_ID.'"';
                  if($CHANGEMENT_FAR_CONFIG[$CHANGEMENT_FAR_CONFIG_ID_ID_AUTRE]=='on'){echo ' checked';}
                  echo '>'.$CHANGEMENT_FAR_CONFIG_TABLE_LIB.'&nbsp;&nbsp;';
                  if($CHANGEMENT_FAR_CONFIG_TABLE_COM=='oui'){
                  	echo '<input '.$readonly_var.' id="CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID.'_'.$CHANGEMENT_FAR_CONFIG_TABLE_ID.'_COM" name="CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID.'_'.$CHANGEMENT_FAR_CONFIG_TABLE_ID.'_COM" type="hidden" value="vide" size="50" maxlength="100"/>';
                  }
                  //echo '</BR>';
                
                } while ($tab_rq_info_far_table = mysql_fetch_assoc($res_rq_info_config_table));
                $ligne= mysql_num_rows($res_rq_info_config_table);
                if($ligne > 0) {
                  mysql_data_seek($res_rq_info_config_table, 0);
                  $tab_rq_info_far_table = mysql_fetch_assoc($res_rq_info_config_table);
                }
              }
              mysql_free_result($res_rq_info_config_table);
              $rq_info_config_table="
              SELECT  *
              FROM `".$CHANGEMENT_FAR_CONFIG_TABLE."`
              WHERE 
              `ENABLE`='0'
              AND `".$CHANGEMENT_FAR_CONFIG_TABLE_TRI."`='vide'
              ORDER BY `".$CHANGEMENT_FAR_CONFIG_TABLE_TRI."`";
              $res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
              $tab_rq_info_far_table = mysql_fetch_assoc($res_rq_info_config_table);
              $total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);

              if($total_ligne_rq_info_config_table!=0){
                do {
                  $ID_SQL=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE).'_ID';
                  $ID_LIB=strtoupper($CHANGEMENT_FAR_CONFIG_TABLE);
                  $CHANGEMENT_FAR_CONFIG_TABLE_ID=$tab_rq_info_far_table[$ID_SQL];
                  echo '<input type="hidden" name="CHANGEMENT_FAR_CONFIG_ID_'.$CHANGEMENT_FAR_CONFIG_ID.'_'.$CHANGEMENT_FAR_CONFIG_TABLE_ID.'" value="on">';
                
                } while ($tab_rq_info_far_table = mysql_fetch_assoc($res_rq_info_config_table));
                $ligne= mysql_num_rows($res_rq_info_config_table);
                if($ligne > 0) {
                  mysql_data_seek($res_rq_info_config_table, 0);
                  $tab_rq_info_far_table = mysql_fetch_assoc($res_rq_info_config_table);
                }
              }
              mysql_free_result($res_rq_info_config_table);
              
              
            break;
            
            }
		     echo '
		     &nbsp;</td>
		   </tr>';
	        	
	        } while ($tab_rq_info_far = mysql_fetch_assoc($res_rq_info_far));
	        $ligne= mysql_num_rows($res_rq_info_far);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_far, 0);
	          $tab_rq_info_far = mysql_fetch_assoc($res_rq_info_far);
	        }
	        mysql_free_result($res_rq_info_far);
	}

        	
        } while ($tab_rq_info_far_lib = mysql_fetch_assoc($res_rq_info_far_lib));
        $ligne= mysql_num_rows($res_rq_info_far_lib);
        if($ligne > 0) {
          mysql_data_seek($res_rq_info_far_lib, 0);
          $tab_rq_info_far_lib = mysql_fetch_assoc($res_rq_info_far_lib);
        }
        mysql_free_result($res_rq_info_far_lib);
      }  
      if($CHANGEMENT_FAR_INSCRIPTION=='Non'){
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
	  echo '
	    <tr class="'.$class.'">
	     <td align="left">&nbsp;<font color=#993333><b>Inscription</b></font>&nbsp;</td>
	     <td align="left">&nbsp;';
	     echo '
              <INPUT type="radio" '.$disabled_var.' name="CHANGEMENT_FAR_INSCRIPTION" value="Oui"';
              if($CHANGEMENT_FAR_INSCRIPTION=='Oui'){echo 'CHECKED';} 
              echo '>
              &nbsp;Oui&nbsp;/&nbsp;Non&nbsp;
              <INPUT type="radio" '.$disabled_var.' name="CHANGEMENT_FAR_INSCRIPTION" value="Non"'; 
              if($CHANGEMENT_FAR_INSCRIPTION=='Non'){echo 'CHECKED';} 
              echo '>';
              echo '</td></tr>';
      }
      

if($STOP==1){
   	$j++;
	   if ($j%2) { $class = "pair";}else{$class = "impair";} 
	   echo '
	   <tr class="'.$class.'">
	    <td align="center" colspan="4"><font color=#993333><b>Merci de saisir l\'ensemble des champs.</b></font>
	    </td>
	   </tr>';
   }
   echo '
   <tr class="titre">
	<td colspan="2" align="center">
	<h2>';
	if($action=='Ajout'){
	echo '
	<input name="btn" type="submit" id="btn" value="Ajouter">
	<input type="hidden" name="ID" value="'.$ID.'">
	<input type="hidden" name="action" value="Ajout">
	';
	}
	if($action=='Modif'){
		echo '
		<input type="hidden" name="ID" value="'.$ID.'">
		<input type="hidden" name="action" value="'.$action.'">';
		if($CHANGEMENT_STATUS=='Brouillon'){
			echo '
			<input name="btn" type="submit" id="btn" value="Modifier">
			';
		}else{
			if($ROLE==0){
				echo '
				<input name="btn" type="submit" id="btn" value="Modifier">
				';
			}
		}
		
		 
	}
	if($action=='Info'){
		echo '
		<input type="hidden" name="ID" value="'.$ID.'">
		<input type="hidden" name="action" value="'.$action.'">';
	}
	echo '
  </h2>
	</td>
	</tr>
	<tr class="titre">
	<td colspan="4" align="center">';
	if($action=="Info"){
		echo '<h2>&nbsp;[&nbsp;<a href="./index.php?ITEM=changement_Info_Changement&action=Info&ID='.$ID.'">Retour</a>&nbsp;]&nbsp;</h2>';
        }else{
        	echo '<h2>&nbsp;[&nbsp;<a href="./index.php?ITEM=changement_Modif_Changement&action=Modif&ID='.$ID.'">Retour</a>&nbsp;]&nbsp;</h2>';
	}
	echo '
	</td>
	</tr>';
echo '
</table>
</form>
</div>
';

mysql_close($mysql_link); 
?>