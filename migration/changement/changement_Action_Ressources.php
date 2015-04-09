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
require_once('./changement/changement_Conf_Mail.php');

$ENV =substr($_SERVER["SCRIPT_FILENAME"], 1, 1);

$j=0;
$STOP=0;
$nb_modif=0;
$DATE_MODIFICATION=date("d/m/Y H:i:s");
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

$rq_info_status="
SELECT `changement_status`.`CHANGEMENT_STATUS` 
FROM `changement_liste` , `changement_status` 
WHERE `changement_liste`.`CHANGEMENT_LISTE_ID` = '".$ID."'
AND `changement_liste`.`CHANGEMENT_STATUS_ID` = `changement_status`.`CHANGEMENT_STATUS_ID` 
AND `changement_liste`.`ENABLE` = '0'
";
$res_rq_info_status = mysql_query($rq_info_status, $mysql_link) or die(mysql_error());
$tab_rq_info_status = mysql_fetch_assoc($res_rq_info_status);
$total_ligne_rq_info_status=mysql_num_rows($res_rq_info_status);
if($total_ligne_rq_info_status==0){
	$CHANGEMENT_STATUS='Brouillon';
}else{
	$CHANGEMENT_STATUS=$tab_rq_info_status['CHANGEMENT_STATUS'];;		
}
mysql_free_result($res_rq_info_status);

$DATE_DU_JOUR=date("Ymd");
if(empty($tab_var['btn'])){
	
}else{

  # Cas Enregistrer
  if($tab_var['btn']=="Enregistrer"){
  	if($action=="Ajout"){
  	
	$ID=$tab_var['ID'];
	$CHANGEMENT_ID=$ID;
	$rq_info_ressources="
	SELECT * 
	FROM `changement_ressources_config`
	WHERE `ENABLE` =0
	";
	$res_rq_info_ressources = mysql_query($rq_info_ressources, $mysql_link) or die(mysql_error());
	$tab_rq_info_ressources = mysql_fetch_assoc($res_rq_info_ressources);
	$total_ligne_rq_info_ressources=mysql_num_rows($res_rq_info_ressources);
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
	`CHANGEMENT_RESSOURCES_CONFIG_ID`,
	`CHANGEMENT_RESSOURCES_CONFIG_LIB`, 
	`CHANGEMENT_RESSOURCES_CONFIG_TYPE`,
	`CHANGEMENT_RESSOURCES_CONFIG_TABLE`,
	`CHANGEMENT_RESSOURCES_CONFIG_OBLIGATOIRE` 
	FROM `changement_ressources_config` 
	WHERE `ENABLE`='0'";
	$res_rq_info_config = mysql_query($rq_info_config, $mysql_link) or die(mysql_error());
	$tab_rq_info_config = mysql_fetch_assoc($res_rq_info_config);
	$total_ligne_rq_info_config=mysql_num_rows($res_rq_info_config);
	
	if($total_ligne_rq_info_config!=0){
	do {
		
	$CHANGEMENT_RESSOURCES_CONFIG_ID=$tab_rq_info_config['CHANGEMENT_RESSOURCES_CONFIG_ID'];
	$CHANGEMENT_RESSOURCES_CONFIG_LIB=$tab_rq_info_config['CHANGEMENT_RESSOURCES_CONFIG_LIB'];
	$CHANGEMENT_RESSOURCES_CONFIG_TYPE=$tab_rq_info_config['CHANGEMENT_RESSOURCES_CONFIG_TYPE'];
	$CHANGEMENT_RESSOURCES_CONFIG_OBLIGATOIRE=$tab_rq_info_config['CHANGEMENT_RESSOURCES_CONFIG_OBLIGATOIRE'];
	if(isset($tab_rq_info_config['CHANGEMENT_RESSOURCES_INFO_AUTRE_ID'])){
		$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID=$tab_rq_info_config['CHANGEMENT_RESSOURCES_INFO_AUTRE_ID'];
	}
	if(isset($tab_rq_info_config['CHANGEMENT_RESSOURCES_INFO_LIB'])){
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]=$tab_rq_info_config['CHANGEMENT_RESSOURCES_INFO_LIB'];
	}
	if(!isset($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID])){$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]='';}
	$LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID;
	
	switch ($CHANGEMENT_RESSOURCES_CONFIG_TYPE)
	{
	case "liste": 
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_RESSOURCES_CONFIG_TABLE'];
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
		$LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID;
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		$CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=0;
//		if($CHANGEMENT_RESSOURCES_CONFIG_OBLIGATOIRE=='oui'){
//			if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]=='0'){
//				$CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=1;
//				$STOP=1;
//			}
//		}
	break;
	case "liste_acteur": 
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_RESSOURCES_CONFIG_TABLE'];
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
		$rq_info_config_table="
		SELECT  *
		FROM `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE."`
		WHERE 
		`ENABLE`='0'
		AND `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI."`!='vide'
		ORDER BY `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI."`";
		$res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
		$tab_rq_info_ressources_table = mysql_fetch_assoc($res_rq_info_config_table);
		$total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);
		
		if($total_ligne_rq_info_config_table!=0){
		do {
		  $ID_SQL=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE).'_ID';
		  $ID_LIB=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
		  $CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=0;
		  $CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID=$tab_rq_info_ressources_table[$ID_SQL];
		  $CHANGEMENT_RESSOURCES_CONFIG_TABLE_LIB=$tab_rq_info_ressources_table[$ID_LIB];
		  $CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID;
		  $LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE;
		  $CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		  
		  $CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_COM';
		  $LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM;
		  $CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		  
		  $CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_INFO=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_INFO';
		  $LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_INFO;
		  $CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_INFO]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		  
		  $CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM_INFO=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_COM_INFO';
		  $LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM_INFO;
		  $CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM_INFO]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		  if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]=='0'){
        $CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=1;
      }
      if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]=='0'){
        $CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=1;
      }
      if(!is_numeric($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE])){
				$CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=1;
				$STOP=2;
			}
			if(!is_numeric($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM])){
				$CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=1;
				$STOP=2;
			}
      } while ($tab_rq_info_ressources_table = mysql_fetch_assoc($res_rq_info_config_table));
		$ligne= mysql_num_rows($res_rq_info_config_table);
		if($ligne > 0) {
		  mysql_data_seek($res_rq_info_config_table, 0);
		  $tab_rq_info_ressources_table = mysql_fetch_assoc($res_rq_info_config_table);
		}
		}
      $CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=0;
//		if($CHANGEMENT_RESSOURCES_CONFIG_OBLIGATOIRE=='oui'){
//			if($CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=='1'){
//				$CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=1;
//				$STOP=1;
//			}
//		}

        break;
	case "checkbox": 
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_RESSOURCES_CONFIG_TABLE'];
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]='0';
		$rq_info_config_table="
		SELECT  *
		FROM `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE."`
		WHERE 
		`ENABLE`='0'
		AND `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI."`!='vide'
		ORDER BY `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI."`";
		$res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
		$tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
		$total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);
		if($total_ligne_rq_info_config_table!=0){
		do {
			$ID_SQL=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE).'_ID';
			$ID_LIB=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
			$COM_SQL=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE).'_COM';
			$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID=$tab_rq_info_config_table[$ID_SQL];
			$CHANGEMENT_RESSOURCES_CONFIG_TABLE_LIB=$tab_rq_info_config_table[$ID_LIB];
			if(isset($tab_rq_info_config_table[$COM_SQL])){
				$CHANGEMENT_RESSOURCES_CONFIG_TABLE_COM=$tab_rq_info_config_table[$COM_SQL];	
			}else{
				$CHANGEMENT_RESSOURCES_CONFIG_TABLE_COM='non';
			}
			$LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID;
			$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID;
			if(!isset($NB_checkbox)){$NB_checkbox=0;}
			if(isset($tab_var[$LISTE_CONFIG])){
				$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
				$NB_checkbox=$NB_checkbox+1;
			}else{
				$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]='off';
			}
			
			if($CHANGEMENT_RESSOURCES_CONFIG_TABLE_COM=='oui'){
				$LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_COM';
				$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_COM';
				if(isset($tab_var[$LISTE_CONFIG])){
					$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
				}else{
					$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]='';
				}
				if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]=='on'){
					if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]==''){
						$STOP=1;	
						$CHANGEMENT_RESSOURCES_CONFIG_STOP_COM[$CHANGEMENT_RESSOURCES_CONFIG_ID]=1;
					}
				}
			}
			
		
		} while ($tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table));
		$ligne= mysql_num_rows($res_rq_info_config_table);
		if($ligne > 0) {
			mysql_data_seek($res_rq_info_config_table, 0);
			$tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
		}
		$CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=0;
//			if($CHANGEMENT_RESSOURCES_CONFIG_OBLIGATOIRE=='oui'){
//				if($NB_checkbox==0){
//					$CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=1;
//					$STOP=1;
//				}
//			}
		}
		mysql_free_result($res_rq_info_config_table);
	break;
	
	case "checkbox_horizontal": 
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_RESSOURCES_CONFIG_TABLE'];
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]='0';
		$rq_info_config_table="
		SELECT  *
		FROM `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE."`
		WHERE 
		`ENABLE`='0'
		AND `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI."`!='vide'
		ORDER BY `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI."`";
		$res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
		$tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
		$total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);
		if($total_ligne_rq_info_config_table!=0){
		do {
			$ID_SQL=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE).'_ID';
			$ID_LIB=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
			$COM_SQL=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE).'_COM';
			$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID=$tab_rq_info_config_table[$ID_SQL];
			$CHANGEMENT_RESSOURCES_CONFIG_TABLE_LIB=$tab_rq_info_config_table[$ID_LIB];
			if(isset($tab_rq_info_config_table[$COM_SQL])){
				$CHANGEMENT_RESSOURCES_CONFIG_TABLE_COM=$tab_rq_info_config_table[$COM_SQL];	
			}else{
				$CHANGEMENT_RESSOURCES_CONFIG_TABLE_COM='non';
			}
			$LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID;
			$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID;
			if(!isset($NB_checkbox)){$NB_checkbox=0;}
			if(isset($tab_var[$LISTE_CONFIG])){
				$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
				$NB_checkbox=$NB_checkbox+1;
			}else{
				$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]='off';
			}
			
			if($CHANGEMENT_RESSOURCES_CONFIG_TABLE_COM=='oui'){
				$LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_COM';
				$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_COM';
				if(isset($tab_var[$LISTE_CONFIG])){
					$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
				}else{
					$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]='';
				}
				if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]=='on'){
					if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]==''){
						$STOP=1;	
						$CHANGEMENT_RESSOURCES_CONFIG_STOP_COM[$CHANGEMENT_RESSOURCES_CONFIG_ID]=1;
					}
				}
			}
			
		
		} while ($tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table));
		$ligne= mysql_num_rows($res_rq_info_config_table);
		if($ligne > 0) {
			mysql_data_seek($res_rq_info_config_table, 0);
			$tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
		}
		$CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=0;
//			if($CHANGEMENT_RESSOURCES_CONFIG_OBLIGATOIRE=='oui'){
//				if($NB_checkbox==0){
//					$CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=1;
//					$STOP=1;
//				}
//			}
		}
		mysql_free_result($res_rq_info_config_table);
	break;
	
	case "varchar": 
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		$CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=0;
//		if($CHANGEMENT_RESSOURCES_CONFIG_OBLIGATOIRE=='oui'){
//			if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]==''){
//				$CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=1;
//				$STOP=1;
//			}
//		}
	break;
	
	case "text": 
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		$CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=0;
//		if($CHANGEMENT_RESSOURCES_CONFIG_OBLIGATOIRE=='oui'){
//			if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]==''){
//				$CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=1;
//				$STOP=1;
//			}
//		}
	break;
	case "oui-non": 
		if(!isset($tab_var[$LISTE_CONFIG])){$tab_var[$LISTE_CONFIG]='';}
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		$LISTE_CONFIG_COMMENTAIRE=$LISTE_CONFIG.'_COMMENTAIRE';
		$CHANGEMENT_RESSOURCES_CONFIG_ID_COMMENTAIRE=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_COMMENTAIRE';
		if(!isset($tab_var[$LISTE_CONFIG_COMMENTAIRE])){$tab_var[$LISTE_CONFIG_COMMENTAIRE]='';}
		$CHANGEMENT_RESSOURCES_CONFIG[$LISTE_CONFIG_COMMENTAIRE]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG_COMMENTAIRE])));
		$CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=0;
//		if($CHANGEMENT_RESSOURCES_CONFIG_OBLIGATOIRE=='oui'){
//			if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]==''){
//				$CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=1;
//				$STOP=1;
//			}
//		}
	break;
	case "risque": 
		if(!isset($tab_var[$LISTE_CONFIG])){$tab_var[$LISTE_CONFIG]='';}
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		$LISTE_CONFIG_COMMENTAIRE=$LISTE_CONFIG.'_COMMENTAIRE';
		$CHANGEMENT_RESSOURCES_CONFIG_ID_COMMENTAIRE=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_COMMENTAIRE';
		if(!isset($tab_var[$LISTE_CONFIG_COMMENTAIRE])){$tab_var[$LISTE_CONFIG_COMMENTAIRE]='';}
		$CHANGEMENT_RESSOURCES_CONFIG[$LISTE_CONFIG_COMMENTAIRE]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG_COMMENTAIRE])));
		$CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=0;
//		if($CHANGEMENT_RESSOURCES_CONFIG_OBLIGATOIRE=='oui'){
//			if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]==''){
//				$CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=1;
//				$STOP=1;
//			}
//		}
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
	`CHANGEMENT_RESSOURCES_CONFIG_ID`,
	`CHANGEMENT_RESSOURCES_CONFIG_LIB`, 
	`CHANGEMENT_RESSOURCES_CONFIG_TYPE`, 
	`CHANGEMENT_RESSOURCES_CONFIG_OBLIGATOIRE`,
	`CHANGEMENT_RESSOURCES_CONFIG_TABLE`
	FROM `changement_ressources_config` 
	WHERE `ENABLE`='0'";
	$res_rq_info_config = mysql_query($rq_info_config, $mysql_link) or die(mysql_error());
	$tab_rq_info_config = mysql_fetch_assoc($res_rq_info_config);
	$total_ligne_rq_info_config=mysql_num_rows($res_rq_info_config);
	
	if($total_ligne_rq_info_config!=0){
	do {
	$CHANGEMENT_RESSOURCES_CONFIG_ID=$tab_rq_info_config['CHANGEMENT_RESSOURCES_CONFIG_ID'];
	$CHANGEMENT_RESSOURCES_CONFIG_LIB=$tab_rq_info_config['CHANGEMENT_RESSOURCES_CONFIG_LIB'];
	$CHANGEMENT_RESSOURCES_CONFIG_TYPE=$tab_rq_info_config['CHANGEMENT_RESSOURCES_CONFIG_TYPE'];
	$CHANGEMENT_RESSOURCES_CONFIG_OBLIGATOIRE=$tab_rq_info_config['CHANGEMENT_RESSOURCES_CONFIG_OBLIGATOIRE'];
	$CHANGEMENT_RESSOURCES_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_RESSOURCES_CONFIG_TABLE'];
	if(isset($tab_rq_info_config['CHANGEMENT_RESSOURCES_INFO_AUTRE_ID'])){
		$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID=$tab_rq_info_config['CHANGEMENT_RESSOURCES_INFO_AUTRE_ID'];
	}
	if(isset($tab_rq_info_config['CHANGEMENT_RESSOURCES_INFO_LIB'])){
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]=$tab_rq_info_config['CHANGEMENT_RESSOURCES_INFO_LIB'];
	}
	if(!isset($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID])){$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]='';}
	$LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID;
	
	switch ($CHANGEMENT_RESSOURCES_CONFIG_TYPE)
	{
	case "liste": 
	
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_RESSOURCES_CONFIG_TABLE'];
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
		$LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID;
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID=$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID];

		$sql="INSERT INTO `changement_ressources` (`CHANGEMENT_RESSOURCES_ID` ,`CHANGEMENT_LISTE_ID` ,`CHANGEMENT_RESSOURCES_CONFIG_ID` ,`CHANGEMENT_RESSOURCES_INFO_AUTRE_ID` ,`CHANGEMENT_RESSOURCES_VALEUR`,`ENABLE` ) VALUES (NULL , '".$CHANGEMENT_ID."', '".$CHANGEMENT_RESSOURCES_CONFIG_ID."', '".$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID."', '', '0');";
		mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
		$TABLE_SQL_SQL='changement_ressources';       
		historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');
	break;
	case "liste_acteur": 
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_RESSOURCES_CONFIG_TABLE'];
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
		$rq_info_config_table="
		SELECT  *
		FROM `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE."`
		WHERE 
		`ENABLE`='0'
		AND `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI."`!='vide'
		ORDER BY `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI."`";
		$res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
		$tab_rq_info_ressources_table = mysql_fetch_assoc($res_rq_info_config_table);
		$total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);
		
		if($total_ligne_rq_info_config_table!=0){
		do {
		  $ID_SQL=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE).'_ID';
		  $ID_LIB=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
		  $CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=0;
		  $CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID=$tab_rq_info_ressources_table[$ID_SQL];
		  $CHANGEMENT_RESSOURCES_CONFIG_TABLE_LIB=$tab_rq_info_ressources_table[$ID_LIB];
		  $CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID;
		  $LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE;
		  $CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		  
		  $CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_COM';
		  $LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM;
		  $CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		  
		  $CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_INFO=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_INFO';
		  $LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_INFO;
		  $CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_INFO]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		  
		  $CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM_INFO=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_COM_INFO';
		  $LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM_INFO;
		  $CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM_INFO]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		  
		  $sql="INSERT INTO `changement_ressources` (`CHANGEMENT_RESSOURCES_ID` ,`CHANGEMENT_LISTE_ID` ,`CHANGEMENT_RESSOURCES_CONFIG_ID` ,`CHANGEMENT_RESSOURCES_INFO_AUTRE_ID` ,`CHANGEMENT_RESSOURCES_VALEUR`,`CHANGEMENT_RESSOURCES_COMMENTAIRE`,`ENABLE` ) VALUES (NULL , '".$CHANGEMENT_ID."', '".$CHANGEMENT_RESSOURCES_CONFIG_ID."', '".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID."','".$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]."|".$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_INFO]."|', '".$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]."|".$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM_INFO]."|', '0');";
		mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
		$TABLE_SQL_SQL='changement_ressources';       
		historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');

      } while ($tab_rq_info_ressources_table = mysql_fetch_assoc($res_rq_info_config_table));
		$ligne= mysql_num_rows($res_rq_info_config_table);
		if($ligne > 0) {
		  mysql_data_seek($res_rq_info_config_table, 0);
		  $tab_rq_info_ressources_table = mysql_fetch_assoc($res_rq_info_config_table);
		}
		}

            break;
	case "checkbox": 
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_RESSOURCES_CONFIG_TABLE'];
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
		$rq_info_config_table="
		SELECT  *
		FROM `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE."`
		WHERE 
		`ENABLE`='0'
		AND `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI."`!='vide'
		ORDER BY `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI."`";
		$res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
		$tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
		$total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);
		if($total_ligne_rq_info_config_table!=0){
		do {
		$ID_SQL=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE).'_ID';
		$ID_LIB=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
		$COM_SQL=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE).'_COM';
		
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID=$tab_rq_info_config_table[$ID_SQL];
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE_LIB=$tab_rq_info_config_table[$ID_LIB];
		$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID=$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID;
		if(isset($tab_rq_info_config_table[$COM_SQL])){
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE_COM=$tab_rq_info_config_table[$COM_SQL];	
		}else{
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE_COM='non';
		}
		$LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID;
		$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID;
		if(!isset($NB_checkbox)){$NB_checkbox=0;}
		if(isset($tab_var[$LISTE_CONFIG])){
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
		$NB_checkbox=$NB_checkbox+1;
		}else{
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]='off';
		}
		
		if($CHANGEMENT_RESSOURCES_CONFIG_TABLE_COM=='oui'){
		$LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_COM';
		$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_COM';
		if(isset($tab_var[$LISTE_CONFIG])){
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
		
		}else{
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]='';
		}
		}
		if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]=='on'){
		$sql="INSERT INTO `changement_ressources` (`CHANGEMENT_RESSOURCES_ID` ,`CHANGEMENT_LISTE_ID` ,`CHANGEMENT_RESSOURCES_CONFIG_ID` ,`CHANGEMENT_RESSOURCES_INFO_AUTRE_ID` ,`CHANGEMENT_RESSOURCES_VALEUR`,`ENABLE` ) VALUES (NULL , '".$CHANGEMENT_ID."', '".$CHANGEMENT_RESSOURCES_CONFIG_ID."', '".$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID."', '".$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]."', '0');";
		mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
		$TABLE_SQL_SQL='changement_ressources';       
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
		FROM `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE."`
		WHERE 
		`ENABLE`='0'
		AND `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI."`='vide'
		ORDER BY `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI."`";
		$res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
		$tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
		$total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);
		if($total_ligne_rq_info_config_table!=0){
		do {
		$ID_SQL=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE).'_ID';
		$ID_LIB=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
		$COM_SQL=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE).'_COM';
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID=$tab_rq_info_config_table[$ID_SQL];
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE_LIB=$tab_rq_info_config_table[$ID_LIB];
		$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID=$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID;
		if(isset($tab_rq_info_config_table[$COM_SQL])){
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE_COM=$tab_rq_info_config_table[$COM_SQL];	
		}else{
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE_COM='non';
		}
		$LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID;
		$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID;
		if(!isset($NB_checkbox)){$NB_checkbox=0;}
		if(isset($tab_var[$LISTE_CONFIG])){
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
		$NB_checkbox=$NB_checkbox+1;
		}else{
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]='off';
		}
		
		if($CHANGEMENT_RESSOURCES_CONFIG_TABLE_COM=='oui'){
		$LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_COM';
		$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_COM';
		if(isset($tab_var[$LISTE_CONFIG])){
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
		
		}else{
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]='';
		}
		
		}
		if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]=='on'){
		$sql="INSERT INTO `changement_ressources` (`CHANGEMENT_RESSOURCES_ID` ,`CHANGEMENT_LISTE_ID` ,`CHANGEMENT_RESSOURCES_CONFIG_ID` ,`CHANGEMENT_RESSOURCES_INFO_AUTRE_ID` ,`CHANGEMENT_RESSOURCES_VALEUR`,`ENABLE` ) VALUES (NULL , '".$CHANGEMENT_ID."', '".$CHANGEMENT_RESSOURCES_CONFIG_ID."', '".$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID."', '".$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]."', '0');";
		mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
		$TABLE_SQL_SQL='changement_ressources';       
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
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_RESSOURCES_CONFIG_TABLE'];
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
		$rq_info_config_table="
		SELECT  *
		FROM `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE."`
		WHERE 
		`ENABLE`='0'
		AND `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI."`!='vide'
		ORDER BY `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI."`";
		$res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
		$tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
		$total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);
		if($total_ligne_rq_info_config_table!=0){
		do {
		$ID_SQL=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE).'_ID';
		$ID_LIB=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
		$COM_SQL=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE).'_COM';
		
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID=$tab_rq_info_config_table[$ID_SQL];
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE_LIB=$tab_rq_info_config_table[$ID_LIB];
		$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID=$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID;
		if(isset($tab_rq_info_config_table[$COM_SQL])){
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE_COM=$tab_rq_info_config_table[$COM_SQL];	
		}else{
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE_COM='non';
		}
		$LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID;
		$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID;
		if(!isset($NB_checkbox)){$NB_checkbox=0;}
		if(isset($tab_var[$LISTE_CONFIG])){
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
		$NB_checkbox=$NB_checkbox+1;
		}else{
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]='off';
		}
		
		if($CHANGEMENT_RESSOURCES_CONFIG_TABLE_COM=='oui'){
		$LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_COM';
		$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_COM';
		if(isset($tab_var[$LISTE_CONFIG])){
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
		
		}else{
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]='';
		}
		}
		if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]=='on'){
		$sql="INSERT INTO `changement_ressources` (`CHANGEMENT_RESSOURCES_ID` ,`CHANGEMENT_LISTE_ID` ,`CHANGEMENT_RESSOURCES_CONFIG_ID` ,`CHANGEMENT_RESSOURCES_INFO_AUTRE_ID` ,`CHANGEMENT_RESSOURCES_VALEUR`,`ENABLE` ) VALUES (NULL , '".$CHANGEMENT_ID."', '".$CHANGEMENT_RESSOURCES_CONFIG_ID."', '".$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID."', '".$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]."', '0');";
		mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
		$TABLE_SQL_SQL='changement_ressources';       
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
		FROM `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE."`
		WHERE 
		`ENABLE`='0'
		AND `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI."`='vide'
		ORDER BY `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI."`";
		$res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
		$tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
		$total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);
		if($total_ligne_rq_info_config_table!=0){
		do {
		$ID_SQL=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE).'_ID';
		$ID_LIB=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
		$COM_SQL=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE).'_COM';
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID=$tab_rq_info_config_table[$ID_SQL];
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE_LIB=$tab_rq_info_config_table[$ID_LIB];
		$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID=$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID;
		if(isset($tab_rq_info_config_table[$COM_SQL])){
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE_COM=$tab_rq_info_config_table[$COM_SQL];	
		}else{
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE_COM='non';
		}
		$LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID;
		$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID;
		if(!isset($NB_checkbox)){$NB_checkbox=0;}
		if(isset($tab_var[$LISTE_CONFIG])){
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
		$NB_checkbox=$NB_checkbox+1;
		}else{
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]='off';
		}
		
		if($CHANGEMENT_RESSOURCES_CONFIG_TABLE_COM=='oui'){
		$LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_COM';
		$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_COM';
		if(isset($tab_var[$LISTE_CONFIG])){
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
		
		}else{
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]='';
		}
		
		}
		if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]=='on'){
		$sql="INSERT INTO `changement_ressources` (`CHANGEMENT_RESSOURCES_ID` ,`CHANGEMENT_LISTE_ID` ,`CHANGEMENT_RESSOURCES_CONFIG_ID` ,`CHANGEMENT_RESSOURCES_INFO_AUTRE_ID` ,`CHANGEMENT_RESSOURCES_VALEUR`,`ENABLE` ) VALUES (NULL , '".$CHANGEMENT_ID."', '".$CHANGEMENT_RESSOURCES_CONFIG_ID."', '".$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID."', '".$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]."', '0');";
		mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
		$TABLE_SQL_SQL='changement_ressources';       
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
		$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID=0;
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		$sql="INSERT INTO `changement_ressources` (`CHANGEMENT_RESSOURCES_ID` ,`CHANGEMENT_LISTE_ID` ,`CHANGEMENT_RESSOURCES_CONFIG_ID` ,`CHANGEMENT_RESSOURCES_INFO_AUTRE_ID` ,`CHANGEMENT_RESSOURCES_VALEUR`,`ENABLE` ) VALUES (NULL , '".$CHANGEMENT_ID."', '".$CHANGEMENT_RESSOURCES_CONFIG_ID."', '".$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID."', '".$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]."', '0');";
		mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
		$TABLE_SQL_SQL='changement_ressources';       
		historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');
	break;
	
	case "text": 
		$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID=0;
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		$sql="INSERT INTO `changement_ressources` (`CHANGEMENT_RESSOURCES_ID` ,`CHANGEMENT_LISTE_ID` ,`CHANGEMENT_RESSOURCES_CONFIG_ID` ,`CHANGEMENT_RESSOURCES_INFO_AUTRE_ID` ,`CHANGEMENT_RESSOURCES_VALEUR`,`ENABLE` ) VALUES (NULL , '".$CHANGEMENT_ID."', '".$CHANGEMENT_RESSOURCES_CONFIG_ID."', '".$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID."', '".$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]."', '0');";
		mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
		$TABLE_SQL_SQL='changement_ressources';       
		historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');
	break;
	case "risque": 
		$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID=0;
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		$LISTE_CONFIG_COMMENTAIRE=$LISTE_CONFIG.'_COMMENTAIRE';
		$CHANGEMENT_RESSOURCES_CONFIG_ID_COMMENTAIRE=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_COMMENTAIRE';
		if(!isset($tab_var[$LISTE_CONFIG_COMMENTAIRE])){$tab_var[$LISTE_CONFIG_COMMENTAIRE]='';}
		$CHANGEMENT_RESSOURCES_CONFIG[$LISTE_CONFIG_COMMENTAIRE]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG_COMMENTAIRE])));
		$sql="INSERT INTO `changement_ressources` (`CHANGEMENT_RESSOURCES_ID` ,`CHANGEMENT_LISTE_ID` ,`CHANGEMENT_RESSOURCES_CONFIG_ID` ,`CHANGEMENT_RESSOURCES_INFO_AUTRE_ID` ,`CHANGEMENT_RESSOURCES_VALEUR`,`CHANGEMENT_RESSOURCES_COMMENTAIRE`,`ENABLE` ) VALUES (NULL , '".$CHANGEMENT_ID."', '".$CHANGEMENT_RESSOURCES_CONFIG_ID."', '".$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID."', '".$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]."','".$CHANGEMENT_RESSOURCES_CONFIG[$LISTE_CONFIG_COMMENTAIRE]."', '0');";
		mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
		$TABLE_SQL_SQL='changement_ressources';       
		historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');
	break;
	case "oui-non": 
		$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID=0;
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		$LISTE_CONFIG_COMMENTAIRE=$LISTE_CONFIG.'_COMMENTAIRE';
		$CHANGEMENT_RESSOURCES_CONFIG_ID_COMMENTAIRE=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_COMMENTAIRE';
		if(!isset($tab_var[$LISTE_CONFIG_COMMENTAIRE])){$tab_var[$LISTE_CONFIG_COMMENTAIRE]='';}
		$CHANGEMENT_RESSOURCES_CONFIG[$LISTE_CONFIG_COMMENTAIRE]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG_COMMENTAIRE])));
		$sql="INSERT INTO `changement_ressources` (`CHANGEMENT_RESSOURCES_ID` ,`CHANGEMENT_LISTE_ID` ,`CHANGEMENT_RESSOURCES_CONFIG_ID` ,`CHANGEMENT_RESSOURCES_INFO_AUTRE_ID` ,`CHANGEMENT_RESSOURCES_VALEUR`,`CHANGEMENT_RESSOURCES_COMMENTAIRE`,`ENABLE` ) VALUES (NULL , '".$CHANGEMENT_ID."', '".$CHANGEMENT_RESSOURCES_CONFIG_ID."', '".$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID."', '".$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]."','".$CHANGEMENT_RESSOURCES_CONFIG[$LISTE_CONFIG_COMMENTAIRE]."', '0');";
		mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
		$TABLE_SQL_SQL='changement_ressources';       
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
	
	$sql="OPTIMIZE TABLE `changement_ressources` ";
	mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
	
	$TABLE_SQL_SQL='changement_ressources';       
	historique_sql_new($sql,$TABLE_SQL_SQL,'OPTIMIZE');
	
	mysql_free_result($res_rq_info_ressources);
	if($STOP==0){

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
    $TRACE_TABLE='changement_ressources';
    $TRACE_REF_ID=$ID;
    $TRACE_ACTION='Ajout';
    moteur_trace($TRACE_CATEGORIE,$TRACE_TABLE,$TRACE_REF_ID,$TRACE_ACTION,$TRACE_ETAT);
	
	echo '
	<script language="JavaScript">
	url=("./index.php?ITEM=changement_Modif_Ressources&action=Modif&ID='.$ID.'");
	window.location=url;
	</script>
	';
	}
	}
	
	$_GET['action']="Ajout";
  }
  if($action=="Modif"){
	$nb_modif=0;
  	$ID=$tab_var['ID'];
  	$CHANGEMENT_ID=$ID;
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

//verification
$rq_info_config="
	SELECT  
	`changement_ressources_config`.`CHANGEMENT_RESSOURCES_CONFIG_ID`,
	`changement_ressources_config`.`CHANGEMENT_RESSOURCES_CONFIG_LIB`, 
	`changement_ressources_config`.`CHANGEMENT_RESSOURCES_CONFIG_TYPE`,
	`changement_ressources_config`.`CHANGEMENT_RESSOURCES_CONFIG_TABLE`,
	`changement_ressources_config`.`CHANGEMENT_RESSOURCES_CONFIG_OBLIGATOIRE` 
	FROM `changement_ressources_config` 
	WHERE `changement_ressources_config`.`ENABLE`='0'
	AND `changement_ressources_config`.`CHANGEMENT_RESSOURCES_CONFIG_ID` IN(
	SELECT `CHANGEMENT_RESSOURCES_CONFIG_ID` FROM `changement_ressources` WHERE `CHANGEMENT_LISTE_ID`='".$ID."'
	)
	";
	$res_rq_info_config = mysql_query($rq_info_config, $mysql_link) or die(mysql_error());
	$tab_rq_info_config = mysql_fetch_assoc($res_rq_info_config);
	$total_ligne_rq_info_config=mysql_num_rows($res_rq_info_config);
	
	if($total_ligne_rq_info_config!=0){
	do {
		
	$CHANGEMENT_RESSOURCES_CONFIG_ID=$tab_rq_info_config['CHANGEMENT_RESSOURCES_CONFIG_ID'];
	$CHANGEMENT_RESSOURCES_CONFIG_LIB=$tab_rq_info_config['CHANGEMENT_RESSOURCES_CONFIG_LIB'];
	$CHANGEMENT_RESSOURCES_CONFIG_TYPE=$tab_rq_info_config['CHANGEMENT_RESSOURCES_CONFIG_TYPE'];
	$CHANGEMENT_RESSOURCES_CONFIG_OBLIGATOIRE=$tab_rq_info_config['CHANGEMENT_RESSOURCES_CONFIG_OBLIGATOIRE'];
	if(isset($tab_rq_info_config['CHANGEMENT_RESSOURCES_INFO_AUTRE_ID'])){
		$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID=$tab_rq_info_config['CHANGEMENT_RESSOURCES_INFO_AUTRE_ID'];
	}
	if(isset($tab_rq_info_config['CHANGEMENT_RESSOURCES_INFO_LIB'])){
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]=$tab_rq_info_config['CHANGEMENT_RESSOURCES_INFO_LIB'];
	}
	if(!isset($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID])){$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]='';}
	$LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID;
	
	switch ($CHANGEMENT_RESSOURCES_CONFIG_TYPE)
	{
	case "liste": 
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_RESSOURCES_CONFIG_TABLE'];
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
		$LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID;
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		$CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=0;
		if($CHANGEMENT_STATUS!='Brouillon'){
      if($CHANGEMENT_RESSOURCES_CONFIG_OBLIGATOIRE=='oui'){
        if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]=='0'){
          $CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=1;
          $STOP=1;
        }
      }
		}
	break;
	case "liste_acteur": 
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_RESSOURCES_CONFIG_TABLE'];
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
		$rq_info_config_table="
		SELECT  *
		FROM `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE."`
		WHERE 
		`ENABLE`='0'
		AND `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI."`!='vide'
		ORDER BY `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI."`";
		$res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
		$tab_rq_info_ressources_table = mysql_fetch_assoc($res_rq_info_config_table);
		$total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);
		
		if($total_ligne_rq_info_config_table!=0){
		do {
		  $ID_SQL=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE).'_ID';
		  $ID_LIB=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
		  $CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=0;
		  $CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID=$tab_rq_info_ressources_table[$ID_SQL];
		  $CHANGEMENT_RESSOURCES_CONFIG_TABLE_LIB=$tab_rq_info_ressources_table[$ID_LIB];
		  $CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID;
		  $LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE;
		  $CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		  
		  $CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_COM';
		  $LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM;
		  $CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		  
		  $CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_INFO=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_INFO';
		  $LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_INFO;
		  $CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_INFO]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		  
		  $CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM_INFO=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_COM_INFO';
		  $LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM_INFO;
		  $CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM_INFO]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		  if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]=='0'){
			$CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=1;
			}
			if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]=='0'){
			$CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=1;
			}
			if(!is_numeric($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE])){
				$CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=1;
				$STOP=2;
			}
			if(!is_numeric($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM])){
				$CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=1;
				$STOP=2;
			}
      } while ($tab_rq_info_ressources_table = mysql_fetch_assoc($res_rq_info_config_table));
		$ligne= mysql_num_rows($res_rq_info_config_table);
		if($ligne > 0) {
		  mysql_data_seek($res_rq_info_config_table, 0);
		  $tab_rq_info_ressources_table = mysql_fetch_assoc($res_rq_info_config_table);
		}
		}
		if($CHANGEMENT_STATUS!='Brouillon'){
			if($CHANGEMENT_RESSOURCES_CONFIG_OBLIGATOIRE=='oui'){
				if($CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=='1'){
					$CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=1;
					$STOP=1;
				}
			}else{
				$CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=0;
			}
		}else{
			$CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=0;
		}

            break;
	case "checkbox": 
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_RESSOURCES_CONFIG_TABLE'];
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]='0';
		$rq_info_config_table="
		SELECT  *
		FROM `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE."`
		WHERE 
		`ENABLE`='0'
		AND `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI."`!='vide'
		ORDER BY `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI."`";
		$res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
		$tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
		$total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);
		if($total_ligne_rq_info_config_table!=0){
		do {
			$ID_SQL=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE).'_ID';
			$ID_LIB=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
			$COM_SQL=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE).'_COM';
			$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID=$tab_rq_info_config_table[$ID_SQL];
			$CHANGEMENT_RESSOURCES_CONFIG_TABLE_LIB=$tab_rq_info_config_table[$ID_LIB];
			if(isset($tab_rq_info_config_table[$COM_SQL])){
				$CHANGEMENT_RESSOURCES_CONFIG_TABLE_COM=$tab_rq_info_config_table[$COM_SQL];	
			}else{
				$CHANGEMENT_RESSOURCES_CONFIG_TABLE_COM='non';
			}
			$LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID;
			$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID;
			if(!isset($NB_checkbox)){$NB_checkbox=0;}
			if(isset($tab_var[$LISTE_CONFIG])){
				$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
				$NB_checkbox=$NB_checkbox+1;
			}else{
				$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]='off';
			}
			
			if($CHANGEMENT_RESSOURCES_CONFIG_TABLE_COM=='oui'){
				$LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_COM';
				$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_COM';
				if(isset($tab_var[$LISTE_CONFIG])){
					$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
				}else{
					$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]='';
				}
				if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]=='on'){
					if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]==''){
						$STOP=1;	
						$CHANGEMENT_RESSOURCES_CONFIG_STOP_COM[$CHANGEMENT_RESSOURCES_CONFIG_ID]=1;
					}
				}
			}
			
		
		} while ($tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table));
		$ligne= mysql_num_rows($res_rq_info_config_table);
		if($ligne > 0) {
			mysql_data_seek($res_rq_info_config_table, 0);
			$tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
		}
		$CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=0;
      if($CHANGEMENT_STATUS!='Brouillon'){
        if($CHANGEMENT_RESSOURCES_CONFIG_OBLIGATOIRE=='oui'){
          if($NB_checkbox==0){
            $CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=1;
            $STOP=1;
          }
        }
      }
		}
		mysql_free_result($res_rq_info_config_table);
	break;
	
	case "checkbox_horizontal": 
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_RESSOURCES_CONFIG_TABLE'];
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]='0';
		$rq_info_config_table="
		SELECT  *
		FROM `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE."`
		WHERE 
		`ENABLE`='0'
		AND `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI."`!='vide'
		ORDER BY `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI."`";
		$res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
		$tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
		$total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);
		if($total_ligne_rq_info_config_table!=0){
		do {
			$ID_SQL=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE).'_ID';
			$ID_LIB=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
			$COM_SQL=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE).'_COM';
			$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID=$tab_rq_info_config_table[$ID_SQL];
			$CHANGEMENT_RESSOURCES_CONFIG_TABLE_LIB=$tab_rq_info_config_table[$ID_LIB];
			if(isset($tab_rq_info_config_table[$COM_SQL])){
				$CHANGEMENT_RESSOURCES_CONFIG_TABLE_COM=$tab_rq_info_config_table[$COM_SQL];	
			}else{
				$CHANGEMENT_RESSOURCES_CONFIG_TABLE_COM='non';
			}
			$LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID;
			$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID;
			if(!isset($NB_checkbox)){$NB_checkbox=0;}
			if(isset($tab_var[$LISTE_CONFIG])){
				$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
				$NB_checkbox=$NB_checkbox+1;
			}else{
				$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]='off';
			}
			
			if($CHANGEMENT_RESSOURCES_CONFIG_TABLE_COM=='oui'){
				$LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_COM';
				$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_COM';
				if(isset($tab_var[$LISTE_CONFIG])){
					$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
				}else{
					$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]='';
				}
				if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]=='on'){
					if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]==''){
						$STOP=1;	
						$CHANGEMENT_RESSOURCES_CONFIG_STOP_COM[$CHANGEMENT_RESSOURCES_CONFIG_ID]=1;
					}
				}
			}
			
		
		} while ($tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table));
		$ligne= mysql_num_rows($res_rq_info_config_table);
		if($ligne > 0) {
			mysql_data_seek($res_rq_info_config_table, 0);
			$tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
		}
		$CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=0;
      if($CHANGEMENT_STATUS!='Brouillon'){
        if($CHANGEMENT_RESSOURCES_CONFIG_OBLIGATOIRE=='oui'){
          if($NB_checkbox==0){
            $CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=1;
            $STOP=1;
          }
        }
			}
		}
		mysql_free_result($res_rq_info_config_table);
	break;
	
	case "varchar": 
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		$CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=0;
		if($CHANGEMENT_STATUS!='Brouillon'){
      if($CHANGEMENT_RESSOURCES_CONFIG_OBLIGATOIRE=='oui'){
        if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]==''){
          $CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=1;
          $STOP=1;
        }
      }
		}
	break;
	
	case "text": 
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		$CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=0;
		if($CHANGEMENT_STATUS!='Brouillon'){
      if($CHANGEMENT_RESSOURCES_CONFIG_OBLIGATOIRE=='oui'){
        if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]==''){
          $CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=1;
          $STOP=1;
        }
      }
		}
	break;
	case "oui-non": 
		if(!isset($tab_var[$LISTE_CONFIG])){$tab_var[$LISTE_CONFIG]='';}
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		$LISTE_CONFIG_COMMENTAIRE=$LISTE_CONFIG.'_COMMENTAIRE';
		$CHANGEMENT_RESSOURCES_CONFIG_ID_COMMENTAIRE=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_COMMENTAIRE';
		if(!isset($tab_var[$LISTE_CONFIG_COMMENTAIRE])){$tab_var[$LISTE_CONFIG_COMMENTAIRE]='';}
		$CHANGEMENT_RESSOURCES_CONFIG[$LISTE_CONFIG_COMMENTAIRE]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG_COMMENTAIRE])));
		$CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=0;
		if($CHANGEMENT_STATUS!='Brouillon'){
      if($CHANGEMENT_RESSOURCES_CONFIG_OBLIGATOIRE=='oui'){
        if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]==''){
          $CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=1;
          $STOP=1;
        }
      }
		}
	break;
	case "risque": 
		if(!isset($tab_var[$LISTE_CONFIG])){$tab_var[$LISTE_CONFIG]='';}
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		$LISTE_CONFIG_COMMENTAIRE=$LISTE_CONFIG.'_COMMENTAIRE';
		$CHANGEMENT_RESSOURCES_CONFIG_ID_COMMENTAIRE=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_COMMENTAIRE';
		if(!isset($tab_var[$LISTE_CONFIG_COMMENTAIRE])){$tab_var[$LISTE_CONFIG_COMMENTAIRE]='';}
		$CHANGEMENT_RESSOURCES_CONFIG[$LISTE_CONFIG_COMMENTAIRE]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG_COMMENTAIRE])));
		$CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=0;
		if($CHANGEMENT_STATUS!='Brouillon'){
      if($CHANGEMENT_RESSOURCES_CONFIG_OBLIGATOIRE=='oui'){
        if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]==''){
          $CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=1;
          $STOP=1;
        }
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

        if($STOP==0){
// ajout en base      
if($total_ligne_rq_info_config!=0){
	do {
	$CHANGEMENT_RESSOURCES_CONFIG_ID=$tab_rq_info_config['CHANGEMENT_RESSOURCES_CONFIG_ID'];
	$CHANGEMENT_RESSOURCES_CONFIG_LIB=$tab_rq_info_config['CHANGEMENT_RESSOURCES_CONFIG_LIB'];
	$CHANGEMENT_RESSOURCES_CONFIG_TYPE=$tab_rq_info_config['CHANGEMENT_RESSOURCES_CONFIG_TYPE'];
	$CHANGEMENT_RESSOURCES_CONFIG_OBLIGATOIRE=$tab_rq_info_config['CHANGEMENT_RESSOURCES_CONFIG_OBLIGATOIRE'];
	$CHANGEMENT_RESSOURCES_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_RESSOURCES_CONFIG_TABLE'];
	if(isset($tab_rq_info_config['CHANGEMENT_RESSOURCES_INFO_AUTRE_ID'])){
		$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID=$tab_rq_info_config['CHANGEMENT_RESSOURCES_INFO_AUTRE_ID'];
	}
	if(isset($tab_rq_info_config['CHANGEMENT_RESSOURCES_INFO_LIB'])){
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]=$tab_rq_info_config['CHANGEMENT_RESSOURCES_INFO_LIB'];
	}
	if(!isset($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID])){$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]='';}
	$LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID;
	
	switch ($CHANGEMENT_RESSOURCES_CONFIG_TYPE)
	{
	case "liste": 
	
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_RESSOURCES_CONFIG_TABLE'];
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
		$LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID;
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID=$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID];
		$rq_info="
    SELECT 
    `CHANGEMENT_RESSOURCES_ID`
    FROM `changement_ressources`
    WHERE 
    `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."' 
    AND `CHANGEMENT_RESSOURCES_CONFIG_ID` ='".$CHANGEMENT_RESSOURCES_CONFIG_ID."' 
    AND `CHANGEMENT_RESSOURCES_INFO_AUTRE_ID` ='".$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID."' 
    AND `CHANGEMENT_RESSOURCES_VALEUR` =''  
    AND `ENABLE` ='0'
    LIMIT 1";
    
    $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
    $tab_rq_info = mysql_fetch_assoc($res_rq_info);
    $total_ligne_rq_info=mysql_num_rows($res_rq_info);
    mysql_free_result($res_rq_info);
    if($total_ligne_rq_info==0){
      $nb_modif=$nb_modif+1;
      $sql="
      UPDATE `changement_ressources` SET 
      `CHANGEMENT_RESSOURCES_INFO_AUTRE_ID`= '".$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID."'
      WHERE `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."'
      AND `CHANGEMENT_RESSOURCES_CONFIG_ID`='".$CHANGEMENT_RESSOURCES_CONFIG_ID."'
      AND `ENABLE`='0'
      LIMIT 1 ;";
      mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
      $TABLE_SQL_SQL='changement_ressources';       
      historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');
    }
		
	break;
	case "checkbox": 
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_RESSOURCES_CONFIG_TABLE'];
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
              $rq_info_config_table="
              SELECT  *
              FROM `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE."`
              WHERE 
              `ENABLE`='0'
              ORDER BY `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI."`";
              $res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
              $tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
              $total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);
              if($total_ligne_rq_info_config_table!=0){
                do {
                  $ID_SQL=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE).'_ID';
                  $ID_LIB=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
                  $COM_SQL=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE).'_COM';
                  
                  $CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID=$tab_rq_info_config_table[$ID_SQL];
                  $CHANGEMENT_RESSOURCES_CONFIG_TABLE_LIB=$tab_rq_info_config_table[$ID_LIB];
                  $CHANGEMENT_RESSOURCES_INFO_AUTRE_ID=$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID;
                  if(isset($tab_rq_info_config_table[$COM_SQL])){
                  	$CHANGEMENT_RESSOURCES_CONFIG_TABLE_COM=$tab_rq_info_config_table[$COM_SQL];	
                  }else{
                  	$CHANGEMENT_RESSOURCES_CONFIG_TABLE_COM='non';
                  }
	              $LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID;
	              $CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID;
	              if(!isset($NB_checkbox)){$NB_checkbox=0;}
	              if(isset($tab_var[$LISTE_CONFIG])){
	              	$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
	              	$NB_checkbox=$NB_checkbox+1;
	              }else{
	              	$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]='off';
	              }

	              if($CHANGEMENT_RESSOURCES_CONFIG_TABLE_COM=='oui'){
		              $LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_COM';
		              $CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_COM';
		              if(isset($tab_var[$LISTE_CONFIG])){
		              	$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
		              	
		              }else{
		              	$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]='';
		              }
	              }

	              

	              if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]=='on'){
	              	      $rq_info="
		              SELECT 
		              `CHANGEMENT_RESSOURCES_VALEUR`
		              FROM `changement_ressources`
		              WHERE 
		              `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."' 
		              AND `CHANGEMENT_RESSOURCES_CONFIG_ID` ='".$CHANGEMENT_RESSOURCES_CONFIG_ID."' 
		              AND `CHANGEMENT_RESSOURCES_INFO_AUTRE_ID` ='".$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID."' 
		              AND `ENABLE` ='0'
		              LIMIT 1";
		              $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
		              $tab_rq_info = mysql_fetch_assoc($res_rq_info);
		              $total_ligne_rq_info=mysql_num_rows($res_rq_info);
		              mysql_free_result($res_rq_info);
		              if($total_ligne_rq_info==0){
		              	$nb_modif=$nb_modif+1;
				$sql="INSERT INTO `changement_ressources` (
				`CHANGEMENT_RESSOURCES_ID` ,
				`CHANGEMENT_LISTE_ID` ,
				`CHANGEMENT_RESSOURCES_CONFIG_ID` ,
				`CHANGEMENT_RESSOURCES_INFO_AUTRE_ID` ,
				`CHANGEMENT_RESSOURCES_VALEUR`,
				`ENABLE` ) 
				VALUES (NULL ,
				 '".$CHANGEMENT_ID."', 
				 '".$CHANGEMENT_RESSOURCES_CONFIG_ID."', 
				 '".$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID."', 
				 '".$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]."', 
				 '0');";
				mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
				$TABLE_SQL_SQL='changement_ressources';       
				historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');
		              }else{
		                $TEST_CHANGEMENT_RESSOURCES_INFO_LIB=$tab_rq_info['CHANGEMENT_RESSOURCES_VALEUR'];
		                if($TEST_CHANGEMENT_RESSOURCES_INFO_LIB!=$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]){
		                	$nb_modif=$nb_modif+1;
					$sql="UPDATE  `changement_ressources` SET
					`CHANGEMENT_RESSOURCES_VALEUR`='".$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]."'
					WHERE `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."' 
			              AND `CHANGEMENT_RESSOURCES_CONFIG_ID` ='".$CHANGEMENT_RESSOURCES_CONFIG_ID."' 
			              AND `CHANGEMENT_RESSOURCES_INFO_AUTRE_ID` ='".$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID."';";
					mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
					$TABLE_SQL_SQL='changement_ressources';       
					historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');
		                }
		              }
	              }else{
	              	      $rq_info="
		              SELECT 
		              `CHANGEMENT_RESSOURCES_ID`
		              FROM `changement_ressources`
		              WHERE 
		              `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."' 
		              AND `CHANGEMENT_RESSOURCES_CONFIG_ID` ='".$CHANGEMENT_RESSOURCES_CONFIG_ID."' 
		              AND `CHANGEMENT_RESSOURCES_INFO_AUTRE_ID` ='".$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID."'  
		              AND `ENABLE` ='0'
		              LIMIT 1";
		              $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
		              $tab_rq_info = mysql_fetch_assoc($res_rq_info);
		              $total_ligne_rq_info=mysql_num_rows($res_rq_info);
		              mysql_free_result($res_rq_info);
		              if($total_ligne_rq_info!=0){
		              	$nb_modif=$nb_modif+1;
		              	$sql="
		              	UPDATE `changement_ressources` SET `ENABLE` = '1' 
	              		WHERE `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."' 
				AND `CHANGEMENT_RESSOURCES_CONFIG_ID` ='".$CHANGEMENT_RESSOURCES_CONFIG_ID."'
				AND `CHANGEMENT_RESSOURCES_INFO_AUTRE_ID` ='".$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID."'
				AND `ENABLE`='0'
				LIMIT 1 ;";
				mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
				$TABLE_SQL_SQL='changement_ressources';       
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
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_RESSOURCES_CONFIG_TABLE'];
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
              $rq_info_config_table="
              SELECT  *
              FROM `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE."`
              WHERE 
              `ENABLE`='0'
              ORDER BY `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI."`";
              $res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
              $tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
              $total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);
              if($total_ligne_rq_info_config_table!=0){
                do {
                  $ID_SQL=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE).'_ID';
                  $ID_LIB=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
                  $COM_SQL=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE).'_COM';
                  
                  $CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID=$tab_rq_info_config_table[$ID_SQL];
                  $CHANGEMENT_RESSOURCES_CONFIG_TABLE_LIB=$tab_rq_info_config_table[$ID_LIB];
                  $CHANGEMENT_RESSOURCES_INFO_AUTRE_ID=$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID;
                  if(isset($tab_rq_info_config_table[$COM_SQL])){
                  	$CHANGEMENT_RESSOURCES_CONFIG_TABLE_COM=$tab_rq_info_config_table[$COM_SQL];	
                  }else{
                  	$CHANGEMENT_RESSOURCES_CONFIG_TABLE_COM='non';
                  }
	              $LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID;
	              $CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID;
	              if(!isset($NB_checkbox)){$NB_checkbox=0;}
	              if(isset($tab_var[$LISTE_CONFIG])){
	              	$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
	              	$NB_checkbox=$NB_checkbox+1;
	              }else{
	              	$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]='off';
	              }

	              if($CHANGEMENT_RESSOURCES_CONFIG_TABLE_COM=='oui'){
		              $LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_COM';
		              $CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_COM';
		              if(isset($tab_var[$LISTE_CONFIG])){
		              	$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
		              	
		              }else{
		              	$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]='';
		              }
	              }

	              

	              if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]=='on'){
	              	      $rq_info="
		              SELECT 
		              `CHANGEMENT_RESSOURCES_VALEUR`
		              FROM `changement_ressources`
		              WHERE 
		              `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."' 
		              AND `CHANGEMENT_RESSOURCES_CONFIG_ID` ='".$CHANGEMENT_RESSOURCES_CONFIG_ID."' 
		              AND `CHANGEMENT_RESSOURCES_INFO_AUTRE_ID` ='".$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID."' 
		              AND `ENABLE` ='0'
		              LIMIT 1";
		              $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
		              $tab_rq_info = mysql_fetch_assoc($res_rq_info);
		              $total_ligne_rq_info=mysql_num_rows($res_rq_info);
		              mysql_free_result($res_rq_info);
		              if($total_ligne_rq_info==0){
		              	$nb_modif=$nb_modif+1;
				$sql="INSERT INTO `changement_ressources` (
				`CHANGEMENT_RESSOURCES_ID` ,
				`CHANGEMENT_LISTE_ID` ,
				`CHANGEMENT_RESSOURCES_CONFIG_ID` ,
				`CHANGEMENT_RESSOURCES_INFO_AUTRE_ID` ,
				`CHANGEMENT_RESSOURCES_VALEUR`,
				`ENABLE` ) 
				VALUES (NULL ,
				 '".$CHANGEMENT_ID."', 
				 '".$CHANGEMENT_RESSOURCES_CONFIG_ID."', 
				 '".$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID."', 
				 '".$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]."', 
				 '0');";
				mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
				$TABLE_SQL_SQL='changement_ressources';       
				historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');
		              }else{
		                $TEST_CHANGEMENT_RESSOURCES_INFO_LIB=$tab_rq_info['CHANGEMENT_RESSOURCES_VALEUR'];
		                if($TEST_CHANGEMENT_RESSOURCES_INFO_LIB!=$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]){
		                	$nb_modif=$nb_modif+1;
					$sql="UPDATE  `changement_ressources` SET
					`CHANGEMENT_RESSOURCES_VALEUR`='".$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]."'
					WHERE `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."' 
			              AND `CHANGEMENT_RESSOURCES_CONFIG_ID` ='".$CHANGEMENT_RESSOURCES_CONFIG_ID."' 
			              AND `CHANGEMENT_RESSOURCES_INFO_AUTRE_ID` ='".$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID."';";
					mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
					$TABLE_SQL_SQL='changement_ressources';       
					historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');
		                }
		              }
	              }else{
	              	      $rq_info="
		              SELECT 
		              `CHANGEMENT_RESSOURCES_ID`
		              FROM `changement_ressources`
		              WHERE 
		              `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."' 
		              AND `CHANGEMENT_RESSOURCES_CONFIG_ID` ='".$CHANGEMENT_RESSOURCES_CONFIG_ID."' 
		              AND `CHANGEMENT_RESSOURCES_INFO_AUTRE_ID` ='".$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID."'  
		              AND `ENABLE` ='0'
		              LIMIT 1";
		              $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
		              $tab_rq_info = mysql_fetch_assoc($res_rq_info);
		              $total_ligne_rq_info=mysql_num_rows($res_rq_info);
		              mysql_free_result($res_rq_info);
		              if($total_ligne_rq_info!=0){
		              	$nb_modif=$nb_modif+1;
		              	$sql="
		              	UPDATE `changement_ressources` SET `ENABLE` = '1' 
	              		WHERE `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."' 
				AND `CHANGEMENT_RESSOURCES_CONFIG_ID` ='".$CHANGEMENT_RESSOURCES_CONFIG_ID."'
				AND `CHANGEMENT_RESSOURCES_INFO_AUTRE_ID` ='".$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID."'
				AND `ENABLE`='0'
				LIMIT 1 ;";
				mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
				$TABLE_SQL_SQL='changement_ressources';       
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
	case "liste_acteur": 
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_RESSOURCES_CONFIG_TABLE'];
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
		$rq_info_config_table="
		SELECT  *
		FROM `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE."`
		WHERE 
		`ENABLE`='0'
		AND `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI."`!='vide'
		ORDER BY `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI."`";
		$res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
		$tab_rq_info_ressources_table = mysql_fetch_assoc($res_rq_info_config_table);
		$total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);
		
		if($total_ligne_rq_info_config_table!=0){
		do {
		  $ID_SQL=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE).'_ID';
		  $ID_LIB=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
		  $CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=0;
		  $CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID=$tab_rq_info_ressources_table[$ID_SQL];
		  $CHANGEMENT_RESSOURCES_CONFIG_TABLE_LIB=$tab_rq_info_ressources_table[$ID_LIB];
		  $CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID;
		  $LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE;
		  $CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		  
		  $CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_COM';
		  $LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM;
		  $CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		  
		  $CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_INFO=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_INFO';
		  $LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_INFO;
		  $CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_INFO]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		  
		  $CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM_INFO=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_COM_INFO';
		  $LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM_INFO;
		  $CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM_INFO]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		  
		  $rq_info="
    SELECT 
    `CHANGEMENT_RESSOURCES_ID`
    FROM `changement_ressources`
    WHERE 
    `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."' 
    AND `CHANGEMENT_RESSOURCES_CONFIG_ID` ='".$CHANGEMENT_RESSOURCES_CONFIG_ID."' 
    AND `CHANGEMENT_RESSOURCES_INFO_AUTRE_ID` ='".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID."' 
    AND `CHANGEMENT_RESSOURCES_VALEUR` ='".$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]."|".$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_INFO]."|'  
    AND `CHANGEMENT_RESSOURCES_COMMENTAIRE` ='".$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]."|".$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM_INFO]."|'
    AND `ENABLE` ='0'
    LIMIT 1";
    
    $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
    $tab_rq_info = mysql_fetch_assoc($res_rq_info);
    $total_ligne_rq_info=mysql_num_rows($res_rq_info);
    mysql_free_result($res_rq_info);
    if($total_ligne_rq_info==0){
      $nb_modif=$nb_modif+1;
      $sql="
      UPDATE `changement_ressources` SET 
      `CHANGEMENT_RESSOURCES_VALEUR`= '".$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]."|".$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_INFO]."|',
      `CHANGEMENT_RESSOURCES_COMMENTAIRE`= '".$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]."|".$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM_INFO]."|'
      WHERE `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."'
      AND `CHANGEMENT_RESSOURCES_CONFIG_ID`='".$CHANGEMENT_RESSOURCES_CONFIG_ID."'
      AND `CHANGEMENT_RESSOURCES_INFO_AUTRE_ID`= '".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID."'
      AND `ENABLE`='0'
      LIMIT 1 ;";
      mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
      $TABLE_SQL_SQL='changement_ressources';       
      historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');
    }

      } while ($tab_rq_info_ressources_table = mysql_fetch_assoc($res_rq_info_config_table));
		$ligne= mysql_num_rows($res_rq_info_config_table);
		if($ligne > 0) {
		  mysql_data_seek($res_rq_info_config_table, 0);
		  $tab_rq_info_ressources_table = mysql_fetch_assoc($res_rq_info_config_table);
		}
		}

            break;
	case "varchar": 
		$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID=0;
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		$rq_info="
    SELECT 
    `CHANGEMENT_RESSOURCES_ID`
    FROM `changement_ressources`
    WHERE 
    `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."' 
    AND `CHANGEMENT_RESSOURCES_CONFIG_ID` ='".$CHANGEMENT_RESSOURCES_CONFIG_ID."' 
    AND `CHANGEMENT_RESSOURCES_INFO_AUTRE_ID` ='".$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID."' 
    AND `CHANGEMENT_RESSOURCES_VALEUR` ='".$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]."'  
    AND `ENABLE` ='0'
    LIMIT 1";
    
    $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
    $tab_rq_info = mysql_fetch_assoc($res_rq_info);
    $total_ligne_rq_info=mysql_num_rows($res_rq_info);
    mysql_free_result($res_rq_info);
    if($total_ligne_rq_info==0){
      $nb_modif=$nb_modif+1;
      $sql="
      UPDATE `changement_ressources` SET 
      `CHANGEMENT_RESSOURCES_VALEUR`= '".$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]."'
      WHERE `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."'
      AND `CHANGEMENT_RESSOURCES_CONFIG_ID`='".$CHANGEMENT_RESSOURCES_CONFIG_ID."'
      AND `CHANGEMENT_RESSOURCES_INFO_AUTRE_ID`= '".$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID."'
      AND `ENABLE`='0'
      LIMIT 1 ;";
      mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
      $TABLE_SQL_SQL='changement_ressources';       
      historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');
    }
	break;
	
	case "text": 
		$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID=0;
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		$rq_info="
    SELECT 
    `CHANGEMENT_RESSOURCES_ID`
    FROM `changement_ressources`
    WHERE 
    `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."' 
    AND `CHANGEMENT_RESSOURCES_CONFIG_ID` ='".$CHANGEMENT_RESSOURCES_CONFIG_ID."' 
    AND `CHANGEMENT_RESSOURCES_INFO_AUTRE_ID` ='".$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID."' 
    AND `CHANGEMENT_RESSOURCES_VALEUR` ='".$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]."'  
    AND `ENABLE` ='0'
    LIMIT 1";
    
    $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
    $tab_rq_info = mysql_fetch_assoc($res_rq_info);
    $total_ligne_rq_info=mysql_num_rows($res_rq_info);
    mysql_free_result($res_rq_info);
    if($total_ligne_rq_info==0){
      $nb_modif=$nb_modif+1;
      $sql="
      UPDATE `changement_ressources` SET 
      `CHANGEMENT_RESSOURCES_VALEUR`= '".$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]."'
      WHERE `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."'
      AND `CHANGEMENT_RESSOURCES_CONFIG_ID`='".$CHANGEMENT_RESSOURCES_CONFIG_ID."'
      AND `CHANGEMENT_RESSOURCES_INFO_AUTRE_ID`= '".$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID."'
      AND `ENABLE`='0'
      LIMIT 1 ;";
      mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
      $TABLE_SQL_SQL='changement_ressources';       
      historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');
    }
	break;
	case "risque": 
		$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID=0;
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		$LISTE_CONFIG_COMMENTAIRE=$LISTE_CONFIG.'_COMMENTAIRE';
		$CHANGEMENT_RESSOURCES_CONFIG_ID_COMMENTAIRE=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_COMMENTAIRE';
		if(!isset($tab_var[$LISTE_CONFIG_COMMENTAIRE])){$tab_var[$LISTE_CONFIG_COMMENTAIRE]='';}
		$CHANGEMENT_RESSOURCES_CONFIG[$LISTE_CONFIG_COMMENTAIRE]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG_COMMENTAIRE])));
		$rq_info="
    SELECT 
    `CHANGEMENT_RESSOURCES_ID`
    FROM `changement_ressources`
    WHERE 
    `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."' 
    AND `CHANGEMENT_RESSOURCES_CONFIG_ID` ='".$CHANGEMENT_RESSOURCES_CONFIG_ID."' 
    AND `CHANGEMENT_RESSOURCES_INFO_AUTRE_ID` ='".$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID."' 
    AND `CHANGEMENT_RESSOURCES_VALEUR` ='".$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]."'  
    AND `CHANGEMENT_RESSOURCES_COMMENTAIRE` ='".$CHANGEMENT_RESSOURCES_CONFIG[$LISTE_CONFIG_COMMENTAIRE]."'
    AND `ENABLE` ='0'
    LIMIT 1";
    
    $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
    $tab_rq_info = mysql_fetch_assoc($res_rq_info);
    $total_ligne_rq_info=mysql_num_rows($res_rq_info);
    mysql_free_result($res_rq_info);
    if($total_ligne_rq_info==0){
      $nb_modif=$nb_modif+1;
      $sql="
      UPDATE `changement_ressources` SET 
      `CHANGEMENT_RESSOURCES_VALEUR`= '".$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]."',
      `CHANGEMENT_RESSOURCES_COMMENTAIRE`= '".$CHANGEMENT_RESSOURCES_CONFIG[$LISTE_CONFIG_COMMENTAIRE]."'
      WHERE `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."'
      AND `CHANGEMENT_RESSOURCES_CONFIG_ID`='".$CHANGEMENT_RESSOURCES_CONFIG_ID."'
      AND `CHANGEMENT_RESSOURCES_INFO_AUTRE_ID`= '".$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID."'
      AND `ENABLE`='0'
      LIMIT 1 ;";
      mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
      $TABLE_SQL_SQL='changement_ressources';       
      historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');
    }
	break;
	case "oui-non": 
		$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID=0;
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		$LISTE_CONFIG_COMMENTAIRE=$LISTE_CONFIG.'_COMMENTAIRE';
		$CHANGEMENT_RESSOURCES_CONFIG_ID_COMMENTAIRE=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_COMMENTAIRE';
		if(!isset($tab_var[$LISTE_CONFIG_COMMENTAIRE])){$tab_var[$LISTE_CONFIG_COMMENTAIRE]='';}
		$CHANGEMENT_RESSOURCES_CONFIG[$LISTE_CONFIG_COMMENTAIRE]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG_COMMENTAIRE])));
		$rq_info="
    SELECT 
    `CHANGEMENT_RESSOURCES_ID`
    FROM `changement_ressources`
    WHERE 
    `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."' 
    AND `CHANGEMENT_RESSOURCES_CONFIG_ID` ='".$CHANGEMENT_RESSOURCES_CONFIG_ID."' 
    AND `CHANGEMENT_RESSOURCES_INFO_AUTRE_ID` ='".$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID."' 
    AND `CHANGEMENT_RESSOURCES_VALEUR` ='".$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]."'  
    AND `CHANGEMENT_RESSOURCES_COMMENTAIRE` ='".$CHANGEMENT_RESSOURCES_CONFIG[$LISTE_CONFIG_COMMENTAIRE]."'
    AND `ENABLE` ='0'
    LIMIT 1";
    
    $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
    $tab_rq_info = mysql_fetch_assoc($res_rq_info);
    $total_ligne_rq_info=mysql_num_rows($res_rq_info);
    mysql_free_result($res_rq_info);
    if($total_ligne_rq_info==0){
      $nb_modif=$nb_modif+1;
      $sql="
      UPDATE `changement_ressources` SET 
      `CHANGEMENT_RESSOURCES_VALEUR`= '".$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]."',
      `CHANGEMENT_RESSOURCES_COMMENTAIRE`= '".$CHANGEMENT_RESSOURCES_CONFIG[$LISTE_CONFIG_COMMENTAIRE]."'
      WHERE `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."'
      AND `CHANGEMENT_RESSOURCES_CONFIG_ID`='".$CHANGEMENT_RESSOURCES_CONFIG_ID."'
      AND `CHANGEMENT_RESSOURCES_INFO_AUTRE_ID`= '".$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID."'
      AND `ENABLE`='0'
      LIMIT 1 ;";
      mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
      $TABLE_SQL_SQL='changement_ressources';       
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
		$sql="UPDATE `changement_liste` SET 
		`CHANGEMENT_LISTE_UTILISATEUR_ID`= '".$UTILISATEUR_ID."',
		`CHANGEMENT_LISTE_DATE_MODIFICATION`= '".$DATE_MODIFICATION."'
		WHERE `CHANGEMENT_LISTE_ID` ='".$ID."' LIMIT 1 ;";		
		mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
		$TABLE_SQL_SQL='changement_liste';       
		historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');
      $TRACE_ETAT=$CHANGEMENT_STATUS_ID;
			$TRACE_CATEGORIE='Changement';
			$TRACE_TABLE='changement_ressources';
			$TRACE_REF_ID=$ID;
			$TRACE_ACTION='Modif';
			moteur_trace($TRACE_CATEGORIE,$TRACE_TABLE,$TRACE_REF_ID,$TRACE_ACTION,$TRACE_ETAT);
			
			if($CHANGEMENT_STATUS == 'Brouillon' ){
				$rq_info_etat="
				SELECT `CHANGEMENT_RESSOURCES_ID`
				FROM `changement_ressources` 
				WHERE 
				`CHANGEMENT_LISTE_ID` ='".$ID."' 
				AND `CHANGEMENT_RESSOURCES_ETAT` = 'B'
				AND `ENABLE` = '0'";
				$res_rq_info_etat = mysql_query($rq_info_etat, $mysql_link) or die(mysql_error());
				$tab_rq_info_etat = mysql_fetch_assoc($res_rq_info_etat);
				$total_ligne_rq_info_etat=mysql_num_rows($res_rq_info_etat);

				mysql_free_result($res_rq_info_etat); 
				if($total_ligne_rq_info_etat==0){
					$sql="
					UPDATE `changement_ressources` 
					SET `CHANGEMENT_RESSOURCES_ETAT` = 'B' 
					WHERE `CHANGEMENT_LISTE_ID` ='".$ID."' 
					AND `ENABLE` = '0';";		
					mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
					$TABLE_SQL_SQL='changement_ressources';       
					historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');
				}
			}
			echo '
			<script language="JavaScript">
			url=("./index.php?ITEM=changement_Modif_Ressources&action=Modif&ID='.$ID.'");
			window.location=url;
			</script>
			';
			
        }
	}
	$_GET['action']="Modif";	
  	
  }
  }
##########################################
	# Cas Inscription
  if($tab_var['btn']=="Inscription"){
  	if($action=="Ajout"){
  	
	$ID=$tab_var['ID'];
	$CHANGEMENT_ID=$ID;
	$rq_info_ressources="
	SELECT * 
	FROM `changement_ressources_config`
	WHERE `ENABLE` =0
	";
	$res_rq_info_ressources = mysql_query($rq_info_ressources, $mysql_link) or die(mysql_error());
	$tab_rq_info_ressources = mysql_fetch_assoc($res_rq_info_ressources);
	$total_ligne_rq_info_ressources=mysql_num_rows($res_rq_info_ressources);
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
	`CHANGEMENT_RESSOURCES_CONFIG_ID`,
	`CHANGEMENT_RESSOURCES_CONFIG_LIB`, 
	`CHANGEMENT_RESSOURCES_CONFIG_TYPE`,
	`CHANGEMENT_RESSOURCES_CONFIG_TABLE`,
	`CHANGEMENT_RESSOURCES_CONFIG_OBLIGATOIRE` 
	FROM `changement_ressources_config` 
	WHERE `ENABLE`='0'";
	$res_rq_info_config = mysql_query($rq_info_config, $mysql_link) or die(mysql_error());
	$tab_rq_info_config = mysql_fetch_assoc($res_rq_info_config);
	$total_ligne_rq_info_config=mysql_num_rows($res_rq_info_config);
	
	if($total_ligne_rq_info_config!=0){
	do {
		
	$CHANGEMENT_RESSOURCES_CONFIG_ID=$tab_rq_info_config['CHANGEMENT_RESSOURCES_CONFIG_ID'];
	$CHANGEMENT_RESSOURCES_CONFIG_LIB=$tab_rq_info_config['CHANGEMENT_RESSOURCES_CONFIG_LIB'];
	$CHANGEMENT_RESSOURCES_CONFIG_TYPE=$tab_rq_info_config['CHANGEMENT_RESSOURCES_CONFIG_TYPE'];
	$CHANGEMENT_RESSOURCES_CONFIG_OBLIGATOIRE=$tab_rq_info_config['CHANGEMENT_RESSOURCES_CONFIG_OBLIGATOIRE'];
	if(isset($tab_rq_info_config['CHANGEMENT_RESSOURCES_INFO_AUTRE_ID'])){
		$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID=$tab_rq_info_config['CHANGEMENT_RESSOURCES_INFO_AUTRE_ID'];
	}
	if(isset($tab_rq_info_config['CHANGEMENT_RESSOURCES_INFO_LIB'])){
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]=$tab_rq_info_config['CHANGEMENT_RESSOURCES_INFO_LIB'];
	}
	if(!isset($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID])){$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]='';}
	$LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID;
	
	switch ($CHANGEMENT_RESSOURCES_CONFIG_TYPE)
	{
	case "liste": 
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_RESSOURCES_CONFIG_TABLE'];
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
		$LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID;
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		$CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=0;
		if($CHANGEMENT_RESSOURCES_CONFIG_OBLIGATOIRE=='oui'){
			if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]=='0'){
				$CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=1;
				$STOP=1;
			}
		}
	break;
case "liste_acteur": 
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_RESSOURCES_CONFIG_TABLE'];
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
		$rq_info_config_table="
		SELECT  *
		FROM `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE."`
		WHERE 
		`ENABLE`='0'
		AND `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI."`!='vide'
		ORDER BY `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI."`";
		$res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
		$tab_rq_info_ressources_table = mysql_fetch_assoc($res_rq_info_config_table);
		$total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);
		
		if($total_ligne_rq_info_config_table!=0){
		do {
		  $ID_SQL=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE).'_ID';
		  $ID_LIB=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
		  $CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=0;
		  $CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID=$tab_rq_info_ressources_table[$ID_SQL];
		  $CHANGEMENT_RESSOURCES_CONFIG_TABLE_LIB=$tab_rq_info_ressources_table[$ID_LIB];
		  $CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID;
		  $LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE;
		  $CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		  
		  $CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_COM';
		  $LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM;
		  $CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		  
		  $CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_INFO=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_INFO';
		  $LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_INFO;
		  $CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_INFO]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		  
		  $CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM_INFO=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_COM_INFO';
		  $LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM_INFO;
		  $CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM_INFO]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		  if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]=='0'){
        $CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=1;
      }
      if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]=='0'){
        $CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=1;
      }
      if(!is_numeric($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE])){
				$CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=1;
				$STOP=2;
			}
			if(!is_numeric($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM])){
				$CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=1;
				$STOP=2;
			}
      } while ($tab_rq_info_ressources_table = mysql_fetch_assoc($res_rq_info_config_table));
		$ligne= mysql_num_rows($res_rq_info_config_table);
		if($ligne > 0) {
		  mysql_data_seek($res_rq_info_config_table, 0);
		  $tab_rq_info_ressources_table = mysql_fetch_assoc($res_rq_info_config_table);
		}
		}

		if($CHANGEMENT_RESSOURCES_CONFIG_OBLIGATOIRE=='oui'){
			if($CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=='1'){
				$CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=1;
				$STOP=1;
			}
		}else{
			$CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=0;
		}

            break;
	case "checkbox": 
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_RESSOURCES_CONFIG_TABLE'];
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]='0';
		$rq_info_config_table="
		SELECT  *
		FROM `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE."`
		WHERE 
		`ENABLE`='0'
		AND `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI."`!='vide'
		ORDER BY `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI."`";
		$res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
		$tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
		$total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);
		if($total_ligne_rq_info_config_table!=0){
		do {
			$ID_SQL=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE).'_ID';
			$ID_LIB=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
			$COM_SQL=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE).'_COM';
			$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID=$tab_rq_info_config_table[$ID_SQL];
			$CHANGEMENT_RESSOURCES_CONFIG_TABLE_LIB=$tab_rq_info_config_table[$ID_LIB];
			if(isset($tab_rq_info_config_table[$COM_SQL])){
				$CHANGEMENT_RESSOURCES_CONFIG_TABLE_COM=$tab_rq_info_config_table[$COM_SQL];	
			}else{
				$CHANGEMENT_RESSOURCES_CONFIG_TABLE_COM='non';
			}
			$LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID;
			$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID;
			if(!isset($NB_checkbox)){$NB_checkbox=0;}
			if(isset($tab_var[$LISTE_CONFIG])){
				$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
				$NB_checkbox=$NB_checkbox+1;
			}else{
				$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]='off';
			}
			
			if($CHANGEMENT_RESSOURCES_CONFIG_TABLE_COM=='oui'){
				$LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_COM';
				$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_COM';
				if(isset($tab_var[$LISTE_CONFIG])){
					$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
				}else{
					$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]='';
				}
				if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]=='on'){
					if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]==''){
						$STOP=1;	
						$CHANGEMENT_RESSOURCES_CONFIG_STOP_COM[$CHANGEMENT_RESSOURCES_CONFIG_ID]=1;
					}
				}
			}
			
		
		} while ($tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table));
		$ligne= mysql_num_rows($res_rq_info_config_table);
		if($ligne > 0) {
			mysql_data_seek($res_rq_info_config_table, 0);
			$tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
		}
		$CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=0;
			if($CHANGEMENT_RESSOURCES_CONFIG_OBLIGATOIRE=='oui'){
				if($NB_checkbox==0){
					$CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=1;
					$STOP=1;
				}
			}
		}
		mysql_free_result($res_rq_info_config_table);
	break;
	
	case "checkbox_horizontal": 
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_RESSOURCES_CONFIG_TABLE'];
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]='0';
		$rq_info_config_table="
		SELECT  *
		FROM `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE."`
		WHERE 
		`ENABLE`='0'
		AND `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI."`!='vide'
		ORDER BY `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI."`";
		$res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
		$tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
		$total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);
		if($total_ligne_rq_info_config_table!=0){
		do {
			$ID_SQL=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE).'_ID';
			$ID_LIB=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
			$COM_SQL=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE).'_COM';
			$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID=$tab_rq_info_config_table[$ID_SQL];
			$CHANGEMENT_RESSOURCES_CONFIG_TABLE_LIB=$tab_rq_info_config_table[$ID_LIB];
			if(isset($tab_rq_info_config_table[$COM_SQL])){
				$CHANGEMENT_RESSOURCES_CONFIG_TABLE_COM=$tab_rq_info_config_table[$COM_SQL];	
			}else{
				$CHANGEMENT_RESSOURCES_CONFIG_TABLE_COM='non';
			}
			$LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID;
			$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID;
			if(!isset($NB_checkbox)){$NB_checkbox=0;}
			if(isset($tab_var[$LISTE_CONFIG])){
				$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
				$NB_checkbox=$NB_checkbox+1;
			}else{
				$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]='off';
			}
			
			if($CHANGEMENT_RESSOURCES_CONFIG_TABLE_COM=='oui'){
				$LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_COM';
				$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_COM';
				if(isset($tab_var[$LISTE_CONFIG])){
					$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
				}else{
					$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]='';
				}
				if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]=='on'){
					if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]==''){
						$STOP=1;	
						$CHANGEMENT_RESSOURCES_CONFIG_STOP_COM[$CHANGEMENT_RESSOURCES_CONFIG_ID]=1;
					}
				}
			}
			
		
		} while ($tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table));
		$ligne= mysql_num_rows($res_rq_info_config_table);
		if($ligne > 0) {
			mysql_data_seek($res_rq_info_config_table, 0);
			$tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
		}
		$CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=0;
			if($CHANGEMENT_RESSOURCES_CONFIG_OBLIGATOIRE=='oui'){
				if($NB_checkbox==0){
					$CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=1;
					$STOP=1;
				}
			}
		}
		mysql_free_result($res_rq_info_config_table);
	break;
	
	case "varchar": 
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		$CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=0;
		if($CHANGEMENT_RESSOURCES_CONFIG_OBLIGATOIRE=='oui'){
			if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]==''){
				$CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=1;
				$STOP=1;
			}
		}
	break;
	
	case "text": 
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		$CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=0;
		if($CHANGEMENT_RESSOURCES_CONFIG_OBLIGATOIRE=='oui'){
			if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]==''){
				$CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=1;
				$STOP=1;
			}
		}
	break;
	case "oui-non": 
		if(!isset($tab_var[$LISTE_CONFIG])){$tab_var[$LISTE_CONFIG]='';}
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		$LISTE_CONFIG_COMMENTAIRE=$LISTE_CONFIG.'_COMMENTAIRE';
		$CHANGEMENT_RESSOURCES_CONFIG_ID_COMMENTAIRE=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_COMMENTAIRE';
		if(!isset($tab_var[$LISTE_CONFIG_COMMENTAIRE])){$tab_var[$LISTE_CONFIG_COMMENTAIRE]='';}
		$CHANGEMENT_RESSOURCES_CONFIG[$LISTE_CONFIG_COMMENTAIRE]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG_COMMENTAIRE])));
		$CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=0;
		if($CHANGEMENT_RESSOURCES_CONFIG_OBLIGATOIRE=='oui'){
			if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]==''){
				$CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=1;
				$STOP=1;
			}
		}
	break;
	case "risque": 
		if(!isset($tab_var[$LISTE_CONFIG])){$tab_var[$LISTE_CONFIG]='';}
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		$LISTE_CONFIG_COMMENTAIRE=$LISTE_CONFIG.'_COMMENTAIRE';
		$CHANGEMENT_RESSOURCES_CONFIG_ID_COMMENTAIRE=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_COMMENTAIRE';
		if(!isset($tab_var[$LISTE_CONFIG_COMMENTAIRE])){$tab_var[$LISTE_CONFIG_COMMENTAIRE]='';}
		$CHANGEMENT_RESSOURCES_CONFIG[$LISTE_CONFIG_COMMENTAIRE]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG_COMMENTAIRE])));
		$CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=0;
		if($CHANGEMENT_RESSOURCES_CONFIG_OBLIGATOIRE=='oui'){
			if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]==''){
				$CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=1;
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
	`CHANGEMENT_RESSOURCES_CONFIG_ID`,
	`CHANGEMENT_RESSOURCES_CONFIG_LIB`, 
	`CHANGEMENT_RESSOURCES_CONFIG_TYPE`, 
	`CHANGEMENT_RESSOURCES_CONFIG_OBLIGATOIRE`,
	`CHANGEMENT_RESSOURCES_CONFIG_TABLE`
	FROM `changement_ressources_config` 
	WHERE `ENABLE`='0'";
	$res_rq_info_config = mysql_query($rq_info_config, $mysql_link) or die(mysql_error());
	$tab_rq_info_config = mysql_fetch_assoc($res_rq_info_config);
	$total_ligne_rq_info_config=mysql_num_rows($res_rq_info_config);
	
	if($total_ligne_rq_info_config!=0){
	do {
	$CHANGEMENT_RESSOURCES_CONFIG_ID=$tab_rq_info_config['CHANGEMENT_RESSOURCES_CONFIG_ID'];
	$CHANGEMENT_RESSOURCES_CONFIG_LIB=$tab_rq_info_config['CHANGEMENT_RESSOURCES_CONFIG_LIB'];
	$CHANGEMENT_RESSOURCES_CONFIG_TYPE=$tab_rq_info_config['CHANGEMENT_RESSOURCES_CONFIG_TYPE'];
	$CHANGEMENT_RESSOURCES_CONFIG_OBLIGATOIRE=$tab_rq_info_config['CHANGEMENT_RESSOURCES_CONFIG_OBLIGATOIRE'];
	$CHANGEMENT_RESSOURCES_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_RESSOURCES_CONFIG_TABLE'];
	if(isset($tab_rq_info_config['CHANGEMENT_RESSOURCES_INFO_AUTRE_ID'])){
		$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID=$tab_rq_info_config['CHANGEMENT_RESSOURCES_INFO_AUTRE_ID'];
	}
	if(isset($tab_rq_info_config['CHANGEMENT_RESSOURCES_INFO_LIB'])){
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]=$tab_rq_info_config['CHANGEMENT_RESSOURCES_INFO_LIB'];
	}
	if(!isset($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID])){$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]='';}
	$LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID;
	
	switch ($CHANGEMENT_RESSOURCES_CONFIG_TYPE)
	{
	case "liste": 
	
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_RESSOURCES_CONFIG_TABLE'];
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
		$LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID;
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID=$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID];

		$sql="INSERT INTO `changement_ressources` (`CHANGEMENT_RESSOURCES_ID` ,`CHANGEMENT_LISTE_ID` ,`CHANGEMENT_RESSOURCES_CONFIG_ID` ,`CHANGEMENT_RESSOURCES_INFO_AUTRE_ID` ,`CHANGEMENT_RESSOURCES_VALEUR`,`ENABLE` ) VALUES (NULL , '".$CHANGEMENT_ID."', '".$CHANGEMENT_RESSOURCES_CONFIG_ID."', '".$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID."', '', '0');";
		mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
		$TABLE_SQL_SQL='changement_ressources';       
		historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');
	break;
	case "liste_acteur": 
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_RESSOURCES_CONFIG_TABLE'];
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
		$rq_info_config_table="
		SELECT  *
		FROM `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE."`
		WHERE 
		`ENABLE`='0'
		AND `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI."`!='vide'
		ORDER BY `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI."`";
		$res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
		$tab_rq_info_ressources_table = mysql_fetch_assoc($res_rq_info_config_table);
		$total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);
		
		if($total_ligne_rq_info_config_table!=0){
		do {
		  $ID_SQL=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE).'_ID';
		  $ID_LIB=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
		  $CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=0;
		  $CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID=$tab_rq_info_ressources_table[$ID_SQL];
		  $CHANGEMENT_RESSOURCES_CONFIG_TABLE_LIB=$tab_rq_info_ressources_table[$ID_LIB];
		  $CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID;
		  $LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE;
		  $CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		  
		  $CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_COM';
		  $LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM;
		  $CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		  
		  $CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_INFO=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_INFO';
		  $LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_INFO;
		  $CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_INFO]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		  
		  $CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM_INFO=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_COM_INFO';
		  $LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM_INFO;
		  $CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM_INFO]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		  
		  $sql="INSERT INTO `changement_ressources` (`CHANGEMENT_RESSOURCES_ID` ,`CHANGEMENT_LISTE_ID` ,`CHANGEMENT_RESSOURCES_CONFIG_ID` ,`CHANGEMENT_RESSOURCES_INFO_AUTRE_ID` ,`CHANGEMENT_RESSOURCES_VALEUR`,`CHANGEMENT_RESSOURCES_COMMENTAIRE`,`ENABLE` ) VALUES (NULL , '".$CHANGEMENT_ID."', '".$CHANGEMENT_RESSOURCES_CONFIG_ID."', '".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID."','".$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]."|".$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_INFO]."|', '".$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]."|".$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM_INFO]."|', '0');";
		mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
		$TABLE_SQL_SQL='changement_ressources';       
		historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');

      } while ($tab_rq_info_ressources_table = mysql_fetch_assoc($res_rq_info_config_table));
		$ligne= mysql_num_rows($res_rq_info_config_table);
		if($ligne > 0) {
		  mysql_data_seek($res_rq_info_config_table, 0);
		  $tab_rq_info_ressources_table = mysql_fetch_assoc($res_rq_info_config_table);
		}
		}

            break;
	case "checkbox": 
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_RESSOURCES_CONFIG_TABLE'];
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
		$rq_info_config_table="
		SELECT  *
		FROM `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE."`
		WHERE 
		`ENABLE`='0'
		AND `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI."`!='vide'
		ORDER BY `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI."`";
		$res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
		$tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
		$total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);
		if($total_ligne_rq_info_config_table!=0){
		do {
		$ID_SQL=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE).'_ID';
		$ID_LIB=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
		$COM_SQL=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE).'_COM';
		
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID=$tab_rq_info_config_table[$ID_SQL];
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE_LIB=$tab_rq_info_config_table[$ID_LIB];
		$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID=$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID;
		if(isset($tab_rq_info_config_table[$COM_SQL])){
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE_COM=$tab_rq_info_config_table[$COM_SQL];	
		}else{
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE_COM='non';
		}
		$LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID;
		$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID;
		if(!isset($NB_checkbox)){$NB_checkbox=0;}
		if(isset($tab_var[$LISTE_CONFIG])){
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
		$NB_checkbox=$NB_checkbox+1;
		}else{
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]='off';
		}
		
		if($CHANGEMENT_RESSOURCES_CONFIG_TABLE_COM=='oui'){
		$LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_COM';
		$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_COM';
		if(isset($tab_var[$LISTE_CONFIG])){
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
		
		}else{
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]='';
		}
		}
		if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]=='on'){
		$sql="INSERT INTO `changement_ressources` (`CHANGEMENT_RESSOURCES_ID` ,`CHANGEMENT_LISTE_ID` ,`CHANGEMENT_RESSOURCES_CONFIG_ID` ,`CHANGEMENT_RESSOURCES_INFO_AUTRE_ID` ,`CHANGEMENT_RESSOURCES_VALEUR`,`ENABLE` ) VALUES (NULL , '".$CHANGEMENT_ID."', '".$CHANGEMENT_RESSOURCES_CONFIG_ID."', '".$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID."', '".$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]."', '0');";
		mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
		$TABLE_SQL_SQL='changement_ressources';       
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
		FROM `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE."`
		WHERE 
		`ENABLE`='0'
		AND `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI."`='vide'
		ORDER BY `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI."`";
		$res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
		$tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
		$total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);
		if($total_ligne_rq_info_config_table!=0){
		do {
		$ID_SQL=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE).'_ID';
		$ID_LIB=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
		$COM_SQL=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE).'_COM';
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID=$tab_rq_info_config_table[$ID_SQL];
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE_LIB=$tab_rq_info_config_table[$ID_LIB];
		$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID=$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID;
		if(isset($tab_rq_info_config_table[$COM_SQL])){
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE_COM=$tab_rq_info_config_table[$COM_SQL];	
		}else{
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE_COM='non';
		}
		$LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID;
		$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID;
		if(!isset($NB_checkbox)){$NB_checkbox=0;}
		if(isset($tab_var[$LISTE_CONFIG])){
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
		$NB_checkbox=$NB_checkbox+1;
		}else{
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]='off';
		}
		
		if($CHANGEMENT_RESSOURCES_CONFIG_TABLE_COM=='oui'){
		$LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_COM';
		$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_COM';
		if(isset($tab_var[$LISTE_CONFIG])){
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
		
		}else{
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]='';
		}
		
		}
		if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]=='on'){
		$sql="INSERT INTO `changement_ressources` (`CHANGEMENT_RESSOURCES_ID` ,`CHANGEMENT_LISTE_ID` ,`CHANGEMENT_RESSOURCES_CONFIG_ID` ,`CHANGEMENT_RESSOURCES_INFO_AUTRE_ID` ,`CHANGEMENT_RESSOURCES_VALEUR`,`ENABLE` ) VALUES (NULL , '".$CHANGEMENT_ID."', '".$CHANGEMENT_RESSOURCES_CONFIG_ID."', '".$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID."', '".$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]."', '0');";
		mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
		$TABLE_SQL_SQL='changement_ressources';       
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
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_RESSOURCES_CONFIG_TABLE'];
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
		$rq_info_config_table="
		SELECT  *
		FROM `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE."`
		WHERE 
		`ENABLE`='0'
		AND `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI."`!='vide'
		ORDER BY `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI."`";
		$res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
		$tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
		$total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);
		if($total_ligne_rq_info_config_table!=0){
		do {
		$ID_SQL=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE).'_ID';
		$ID_LIB=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
		$COM_SQL=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE).'_COM';
		
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID=$tab_rq_info_config_table[$ID_SQL];
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE_LIB=$tab_rq_info_config_table[$ID_LIB];
		$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID=$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID;
		if(isset($tab_rq_info_config_table[$COM_SQL])){
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE_COM=$tab_rq_info_config_table[$COM_SQL];	
		}else{
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE_COM='non';
		}
		$LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID;
		$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID;
		if(!isset($NB_checkbox)){$NB_checkbox=0;}
		if(isset($tab_var[$LISTE_CONFIG])){
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
		$NB_checkbox=$NB_checkbox+1;
		}else{
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]='off';
		}
		
		if($CHANGEMENT_RESSOURCES_CONFIG_TABLE_COM=='oui'){
		$LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_COM';
		$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_COM';
		if(isset($tab_var[$LISTE_CONFIG])){
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
		
		}else{
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]='';
		}
		}
		if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]=='on'){
		$sql="INSERT INTO `changement_ressources` (`CHANGEMENT_RESSOURCES_ID` ,`CHANGEMENT_LISTE_ID` ,`CHANGEMENT_RESSOURCES_CONFIG_ID` ,`CHANGEMENT_RESSOURCES_INFO_AUTRE_ID` ,`CHANGEMENT_RESSOURCES_VALEUR`,`ENABLE` ) VALUES (NULL , '".$CHANGEMENT_ID."', '".$CHANGEMENT_RESSOURCES_CONFIG_ID."', '".$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID."', '".$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]."', '0');";
		mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
		$TABLE_SQL_SQL='changement_ressources';       
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
		FROM `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE."`
		WHERE 
		`ENABLE`='0'
		AND `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI."`='vide'
		ORDER BY `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI."`";
		$res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
		$tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
		$total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);
		if($total_ligne_rq_info_config_table!=0){
		do {
		$ID_SQL=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE).'_ID';
		$ID_LIB=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
		$COM_SQL=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE).'_COM';
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID=$tab_rq_info_config_table[$ID_SQL];
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE_LIB=$tab_rq_info_config_table[$ID_LIB];
		$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID=$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID;
		if(isset($tab_rq_info_config_table[$COM_SQL])){
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE_COM=$tab_rq_info_config_table[$COM_SQL];	
		}else{
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE_COM='non';
		}
		$LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID;
		$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID;
		if(!isset($NB_checkbox)){$NB_checkbox=0;}
		if(isset($tab_var[$LISTE_CONFIG])){
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
		$NB_checkbox=$NB_checkbox+1;
		}else{
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]='off';
		}
		
		if($CHANGEMENT_RESSOURCES_CONFIG_TABLE_COM=='oui'){
		$LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_COM';
		$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_COM';
		if(isset($tab_var[$LISTE_CONFIG])){
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
		
		}else{
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]='';
		}
		
		}
		if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]=='on'){
		$sql="INSERT INTO `changement_ressources` (`CHANGEMENT_RESSOURCES_ID` ,`CHANGEMENT_LISTE_ID` ,`CHANGEMENT_RESSOURCES_CONFIG_ID` ,`CHANGEMENT_RESSOURCES_INFO_AUTRE_ID` ,`CHANGEMENT_RESSOURCES_VALEUR`,`ENABLE` ) VALUES (NULL , '".$CHANGEMENT_ID."', '".$CHANGEMENT_RESSOURCES_CONFIG_ID."', '".$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID."', '".$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]."', '0');";
		mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
		$TABLE_SQL_SQL='changement_ressources';       
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
		$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID=0;
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		$sql="INSERT INTO `changement_ressources` (`CHANGEMENT_RESSOURCES_ID` ,`CHANGEMENT_LISTE_ID` ,`CHANGEMENT_RESSOURCES_CONFIG_ID` ,`CHANGEMENT_RESSOURCES_INFO_AUTRE_ID` ,`CHANGEMENT_RESSOURCES_VALEUR`,`ENABLE` ) VALUES (NULL , '".$CHANGEMENT_ID."', '".$CHANGEMENT_RESSOURCES_CONFIG_ID."', '".$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID."', '".$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]."', '0');";
		mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
		$TABLE_SQL_SQL='changement_ressources';       
		historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');
	break;
	
	case "text": 
		$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID=0;
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		$sql="INSERT INTO `changement_ressources` (`CHANGEMENT_RESSOURCES_ID` ,`CHANGEMENT_LISTE_ID` ,`CHANGEMENT_RESSOURCES_CONFIG_ID` ,`CHANGEMENT_RESSOURCES_INFO_AUTRE_ID` ,`CHANGEMENT_RESSOURCES_VALEUR`,`ENABLE` ) VALUES (NULL , '".$CHANGEMENT_ID."', '".$CHANGEMENT_RESSOURCES_CONFIG_ID."', '".$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID."', '".$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]."', '0');";
		mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
		$TABLE_SQL_SQL='changement_ressources';       
		historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');
	break;
	case "risque": 
		$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID=0;
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		$LISTE_CONFIG_COMMENTAIRE=$LISTE_CONFIG.'_COMMENTAIRE';
		$CHANGEMENT_RESSOURCES_CONFIG_ID_COMMENTAIRE=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_COMMENTAIRE';
		if(!isset($tab_var[$LISTE_CONFIG_COMMENTAIRE])){$tab_var[$LISTE_CONFIG_COMMENTAIRE]='';}
		$CHANGEMENT_RESSOURCES_CONFIG[$LISTE_CONFIG_COMMENTAIRE]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG_COMMENTAIRE])));
		$sql="INSERT INTO `changement_ressources` (`CHANGEMENT_RESSOURCES_ID` ,`CHANGEMENT_LISTE_ID` ,`CHANGEMENT_RESSOURCES_CONFIG_ID` ,`CHANGEMENT_RESSOURCES_INFO_AUTRE_ID` ,`CHANGEMENT_RESSOURCES_VALEUR`,`CHANGEMENT_RESSOURCES_COMMENTAIRE`,`ENABLE` ) VALUES (NULL , '".$CHANGEMENT_ID."', '".$CHANGEMENT_RESSOURCES_CONFIG_ID."', '".$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID."', '".$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]."','".$CHANGEMENT_RESSOURCES_CONFIG[$LISTE_CONFIG_COMMENTAIRE]."', '0');";
		mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
		$TABLE_SQL_SQL='changement_ressources';       
		historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');
	break;
	case "oui-non": 
		$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID=0;
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		$LISTE_CONFIG_COMMENTAIRE=$LISTE_CONFIG.'_COMMENTAIRE';
		$CHANGEMENT_RESSOURCES_CONFIG_ID_COMMENTAIRE=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_COMMENTAIRE';
		if(!isset($tab_var[$LISTE_CONFIG_COMMENTAIRE])){$tab_var[$LISTE_CONFIG_COMMENTAIRE]='';}
		$CHANGEMENT_RESSOURCES_CONFIG[$LISTE_CONFIG_COMMENTAIRE]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG_COMMENTAIRE])));
		$sql="INSERT INTO `changement_ressources` (`CHANGEMENT_RESSOURCES_ID` ,`CHANGEMENT_LISTE_ID` ,`CHANGEMENT_RESSOURCES_CONFIG_ID` ,`CHANGEMENT_RESSOURCES_INFO_AUTRE_ID` ,`CHANGEMENT_RESSOURCES_VALEUR`,`CHANGEMENT_RESSOURCES_COMMENTAIRE`,`ENABLE` ) VALUES (NULL , '".$CHANGEMENT_ID."', '".$CHANGEMENT_RESSOURCES_CONFIG_ID."', '".$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID."', '".$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]."','".$CHANGEMENT_RESSOURCES_CONFIG[$LISTE_CONFIG_COMMENTAIRE]."', '0');";
		mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
		$TABLE_SQL_SQL='changement_ressources';       
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
	
	$sql="OPTIMIZE TABLE `changement_ressources` ";
	mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
	
	$TABLE_SQL_SQL='changement_ressources';       
	historique_sql_new($sql,$TABLE_SQL_SQL,'OPTIMIZE');
	
	mysql_free_result($res_rq_info_ressources);
	if($STOP==0){
		if($CHANGEMENT_STATUS == 'Brouillon' ){
			$rq_info_etat="
			SELECT `CHANGEMENT_RESSOURCES_ID`
			FROM `changement_ressources` 
			WHERE 
			`CHANGEMENT_LISTE_ID` ='".$ID."' 
			AND `CHANGEMENT_RESSOURCES_ETAT` = 'I'
			AND `ENABLE` = '0'";
			$res_rq_info_etat = mysql_query($rq_info_etat, $mysql_link) or die(mysql_error());
			$tab_rq_info_etat = mysql_fetch_assoc($res_rq_info_etat);
			$total_ligne_rq_info_etat=mysql_num_rows($res_rq_info_etat);
			mysql_free_result($res_rq_info_etat); 
			if($total_ligne_rq_info_etat==0){
				$sql="
				UPDATE `changement_ressources` 
				SET `CHANGEMENT_RESSOURCES_ETAT` = 'I' 
				WHERE `CHANGEMENT_LISTE_ID` ='".$ID."' 
				AND `ENABLE` = '0';";		
				mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
				$TABLE_SQL_SQL='changement_ressources';       
				historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');
			}
		}
		$rq_info_etat="
		SELECT `CHANGEMENT_FAR_ID` 
		FROM `changement_far` 
		WHERE `CHANGEMENT_FAR_ETAT` = 'I'
		AND `CHANGEMENT_LISTE_ID`='".$ID."'
		AND `ENABLE`='0'";
		$res_rq_info_etat = mysql_query($rq_info_etat, $mysql_link) or die(mysql_error());
		$tab_rq_info_etat = mysql_fetch_assoc($res_rq_info_etat);
		$total_ligne_rq_info_etat=mysql_num_rows($res_rq_info_etat);
		if($total_ligne_rq_info_etat==0){
			$rq_info_etat="
			SELECT `CHANGEMENT_FAR_ID` 
			FROM `changement_far` 
			WHERE 
			`CHANGEMENT_LISTE_ID`='".$ID."'
			AND `ENABLE`='0'";
			$res_rq_info_etat = mysql_query($rq_info_etat, $mysql_link) or die(mysql_error());
			$tab_rq_info_etat = mysql_fetch_assoc($res_rq_info_etat);
			$total_ligne_rq_info_etat=mysql_num_rows($res_rq_info_etat);
			if($total_ligne_rq_info_etat==0){ 
				echo '
				<script language="JavaScript">
				url=("./index.php?ITEM=changement_Ajout_FAR&action=Ajout&ID='.$ID.'");
				window.location=url;
				</script>
				';
			}else{
				 
				echo '
				<script language="JavaScript">
				url=("./index.php?ITEM=changement_Modif_FAR&action=Modif&ID='.$ID.'");
				window.location=url;
				</script>
				';
			}
			
		}else{
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
			$TRACE_TABLE='changement_ressources';
			$TRACE_REF_ID=$ID;
			$TRACE_ACTION='Ajout';
			moteur_trace($TRACE_CATEGORIE,$TRACE_TABLE,$TRACE_REF_ID,$TRACE_ACTION,$TRACE_ETAT);
			
			// mail d'inscription			
			echo '
			<script language="JavaScript">
			url=("./index.php?ITEM=changement_Send_Mail&type=inscription&ID='.$ID.'");
			window.location=url;
			</script>
			';
		}
		mysql_free_result($res_rq_info_etat);
	
	
	}
	}
	
	$_GET['action']="Ajout";
  }
  }
##########################################  

  
  # Cas Modifier
  if($action=="Modif"){
	$nb_modif=0;
  	$ID=$tab_var['ID'];
  	$CHANGEMENT_ID=$ID;

//verification
$rq_info_config="
	SELECT  
	`changement_ressources_config`.`CHANGEMENT_RESSOURCES_CONFIG_ID`,
	`changement_ressources_config`.`CHANGEMENT_RESSOURCES_CONFIG_LIB`, 
	`changement_ressources_config`.`CHANGEMENT_RESSOURCES_CONFIG_TYPE`,
	`changement_ressources_config`.`CHANGEMENT_RESSOURCES_CONFIG_TABLE`,
	`changement_ressources_config`.`CHANGEMENT_RESSOURCES_CONFIG_OBLIGATOIRE` 
	FROM `changement_ressources_config` 
	WHERE `changement_ressources_config`.`ENABLE`='0'
	AND `changement_ressources_config`.`CHANGEMENT_RESSOURCES_CONFIG_ID` IN(
	SELECT `CHANGEMENT_RESSOURCES_CONFIG_ID` FROM `changement_ressources` WHERE `CHANGEMENT_LISTE_ID`='".$ID."'
	)
	";
	$res_rq_info_config = mysql_query($rq_info_config, $mysql_link) or die(mysql_error());
	$tab_rq_info_config = mysql_fetch_assoc($res_rq_info_config);
	$total_ligne_rq_info_config=mysql_num_rows($res_rq_info_config);
	
	if($total_ligne_rq_info_config!=0){
	do {
		
	$CHANGEMENT_RESSOURCES_CONFIG_ID=$tab_rq_info_config['CHANGEMENT_RESSOURCES_CONFIG_ID'];
	$CHANGEMENT_RESSOURCES_CONFIG_LIB=$tab_rq_info_config['CHANGEMENT_RESSOURCES_CONFIG_LIB'];
	$CHANGEMENT_RESSOURCES_CONFIG_TYPE=$tab_rq_info_config['CHANGEMENT_RESSOURCES_CONFIG_TYPE'];
	$CHANGEMENT_RESSOURCES_CONFIG_OBLIGATOIRE=$tab_rq_info_config['CHANGEMENT_RESSOURCES_CONFIG_OBLIGATOIRE'];
	if(isset($tab_rq_info_config['CHANGEMENT_RESSOURCES_INFO_AUTRE_ID'])){
		$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID=$tab_rq_info_config['CHANGEMENT_RESSOURCES_INFO_AUTRE_ID'];
	}
	if(isset($tab_rq_info_config['CHANGEMENT_RESSOURCES_INFO_LIB'])){
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]=$tab_rq_info_config['CHANGEMENT_RESSOURCES_INFO_LIB'];
	}
	if(!isset($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID])){$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]='';}
	$LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID;
	
	switch ($CHANGEMENT_RESSOURCES_CONFIG_TYPE)
	{
	case "liste": 
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_RESSOURCES_CONFIG_TABLE'];
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
		$LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID;
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		$CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=0;
		if($CHANGEMENT_RESSOURCES_CONFIG_OBLIGATOIRE=='oui'){
			if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]=='0'){
				$CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=1;
				$STOP=1;
			}
		}
	break;
	case "liste_acteur": 
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_RESSOURCES_CONFIG_TABLE'];
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
		$rq_info_config_table="
		SELECT  *
		FROM `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE."`
		WHERE 
		`ENABLE`='0'
		AND `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI."`!='vide'
		ORDER BY `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI."`";
		$res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
		$tab_rq_info_ressources_table = mysql_fetch_assoc($res_rq_info_config_table);
		$total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);
		
		if($total_ligne_rq_info_config_table!=0){
		do {
		  $ID_SQL=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE).'_ID';
		  $ID_LIB=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
		  $CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=0;
		  $CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID=$tab_rq_info_ressources_table[$ID_SQL];
		  $CHANGEMENT_RESSOURCES_CONFIG_TABLE_LIB=$tab_rq_info_ressources_table[$ID_LIB];
		  $CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID;
		  $LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE;
		  $CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		  
		  $CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_COM';
		  $LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM;
		  $CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		  
		  $CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_INFO=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_INFO';
		  $LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_INFO;
		  $CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_INFO]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		  
		  $CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM_INFO=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_COM_INFO';
		  $LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM_INFO;
		  $CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM_INFO]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		  if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]=='0'){
        $CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=1;
      }
      if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]=='0'){
        $CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=1;
      }
      if(!is_numeric($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE])){
				$CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=1;
				$STOP=2;
			}
			if(!is_numeric($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM])){
				$CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=1;
				$STOP=2;
			}
      } while ($tab_rq_info_ressources_table = mysql_fetch_assoc($res_rq_info_config_table));
		$ligne= mysql_num_rows($res_rq_info_config_table);
		if($ligne > 0) {
		  mysql_data_seek($res_rq_info_config_table, 0);
		  $tab_rq_info_ressources_table = mysql_fetch_assoc($res_rq_info_config_table);
		}
		}

		if($CHANGEMENT_RESSOURCES_CONFIG_OBLIGATOIRE=='oui'){
			if($CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=='1'){
				$CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=1;
				$STOP=1;
			}
		}else{
			$CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=0;
		}

            break;
	case "checkbox": 
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_RESSOURCES_CONFIG_TABLE'];
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]='0';
		$rq_info_config_table="
		SELECT  *
		FROM `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE."`
		WHERE 
		`ENABLE`='0'
		AND `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI."`!='vide'
		ORDER BY `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI."`";
		$res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
		$tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
		$total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);
		if($total_ligne_rq_info_config_table!=0){
		do {
			$ID_SQL=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE).'_ID';
			$ID_LIB=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
			$COM_SQL=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE).'_COM';
			$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID=$tab_rq_info_config_table[$ID_SQL];
			$CHANGEMENT_RESSOURCES_CONFIG_TABLE_LIB=$tab_rq_info_config_table[$ID_LIB];
			if(isset($tab_rq_info_config_table[$COM_SQL])){
				$CHANGEMENT_RESSOURCES_CONFIG_TABLE_COM=$tab_rq_info_config_table[$COM_SQL];	
			}else{
				$CHANGEMENT_RESSOURCES_CONFIG_TABLE_COM='non';
			}
			$LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID;
			$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID;
			if(!isset($NB_checkbox)){$NB_checkbox=0;}
			if(isset($tab_var[$LISTE_CONFIG])){
				$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
				$NB_checkbox=$NB_checkbox+1;
			}else{
				$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]='off';
			}
			
			if($CHANGEMENT_RESSOURCES_CONFIG_TABLE_COM=='oui'){
				$LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_COM';
				$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_COM';
				if(isset($tab_var[$LISTE_CONFIG])){
					$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
				}else{
					$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]='';
				}
				if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]=='on'){
					if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]==''){
						$STOP=1;	
						$CHANGEMENT_RESSOURCES_CONFIG_STOP_COM[$CHANGEMENT_RESSOURCES_CONFIG_ID]=1;
					}
				}
			}
			
		
		} while ($tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table));
		$ligne= mysql_num_rows($res_rq_info_config_table);
		if($ligne > 0) {
			mysql_data_seek($res_rq_info_config_table, 0);
			$tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
		}
		$CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=0;
			if($CHANGEMENT_RESSOURCES_CONFIG_OBLIGATOIRE=='oui'){
				if($NB_checkbox==0){
					$CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=1;
					$STOP=1;
				}
			}
		}
		mysql_free_result($res_rq_info_config_table);
	break;
	
	case "checkbox_horizontal": 
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_RESSOURCES_CONFIG_TABLE'];
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]='0';
		$rq_info_config_table="
		SELECT  *
		FROM `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE."`
		WHERE 
		`ENABLE`='0'
		AND `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI."`!='vide'
		ORDER BY `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI."`";
		$res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
		$tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
		$total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);
		if($total_ligne_rq_info_config_table!=0){
		do {
			$ID_SQL=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE).'_ID';
			$ID_LIB=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
			$COM_SQL=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE).'_COM';
			$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID=$tab_rq_info_config_table[$ID_SQL];
			$CHANGEMENT_RESSOURCES_CONFIG_TABLE_LIB=$tab_rq_info_config_table[$ID_LIB];
			if(isset($tab_rq_info_config_table[$COM_SQL])){
				$CHANGEMENT_RESSOURCES_CONFIG_TABLE_COM=$tab_rq_info_config_table[$COM_SQL];	
			}else{
				$CHANGEMENT_RESSOURCES_CONFIG_TABLE_COM='non';
			}
			$LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID;
			$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID;
			if(!isset($NB_checkbox)){$NB_checkbox=0;}
			if(isset($tab_var[$LISTE_CONFIG])){
				$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
				$NB_checkbox=$NB_checkbox+1;
			}else{
				$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]='off';
			}
			
			if($CHANGEMENT_RESSOURCES_CONFIG_TABLE_COM=='oui'){
				$LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_COM';
				$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_COM';
				if(isset($tab_var[$LISTE_CONFIG])){
					$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
				}else{
					$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]='';
				}
				if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]=='on'){
					if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]==''){
						$STOP=1;	
						$CHANGEMENT_RESSOURCES_CONFIG_STOP_COM[$CHANGEMENT_RESSOURCES_CONFIG_ID]=1;
					}
				}
			}
			
		
		} while ($tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table));
		$ligne= mysql_num_rows($res_rq_info_config_table);
		if($ligne > 0) {
			mysql_data_seek($res_rq_info_config_table, 0);
			$tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
		}
		$CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=0;
			if($CHANGEMENT_RESSOURCES_CONFIG_OBLIGATOIRE=='oui'){
				if($NB_checkbox==0){
					$CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=1;
					$STOP=1;
				}
			}
		}
		mysql_free_result($res_rq_info_config_table);
	break;
	
	case "varchar": 
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		$CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=0;
		if($CHANGEMENT_RESSOURCES_CONFIG_OBLIGATOIRE=='oui'){
			if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]==''){
				$CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=1;
				$STOP=1;
			}
		}
	break;
	
	case "text": 
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		$CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=0;
		if($CHANGEMENT_RESSOURCES_CONFIG_OBLIGATOIRE=='oui'){
			if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]==''){
				$CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=1;
				$STOP=1;
			}
		}
	break;
	case "oui-non": 
		if(!isset($tab_var[$LISTE_CONFIG])){$tab_var[$LISTE_CONFIG]='';}
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		$LISTE_CONFIG_COMMENTAIRE=$LISTE_CONFIG.'_COMMENTAIRE';
		$CHANGEMENT_RESSOURCES_CONFIG_ID_COMMENTAIRE=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_COMMENTAIRE';
		if(!isset($tab_var[$LISTE_CONFIG_COMMENTAIRE])){$tab_var[$LISTE_CONFIG_COMMENTAIRE]='';}
		$CHANGEMENT_RESSOURCES_CONFIG[$LISTE_CONFIG_COMMENTAIRE]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG_COMMENTAIRE])));
		$CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=0;
		if($CHANGEMENT_RESSOURCES_CONFIG_OBLIGATOIRE=='oui'){
			if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]==''){
				$CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=1;
				$STOP=1;
			}
		}
	break;
	case "risque": 
		if(!isset($tab_var[$LISTE_CONFIG])){$tab_var[$LISTE_CONFIG]='';}
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		$LISTE_CONFIG_COMMENTAIRE=$LISTE_CONFIG.'_COMMENTAIRE';
		$CHANGEMENT_RESSOURCES_CONFIG_ID_COMMENTAIRE=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_COMMENTAIRE';
		if(!isset($tab_var[$LISTE_CONFIG_COMMENTAIRE])){$tab_var[$LISTE_CONFIG_COMMENTAIRE]='';}
		$CHANGEMENT_RESSOURCES_CONFIG[$LISTE_CONFIG_COMMENTAIRE]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG_COMMENTAIRE])));
		$CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=0;
		if($CHANGEMENT_RESSOURCES_CONFIG_OBLIGATOIRE=='oui'){
			if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]==''){
				$CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=1;
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
	$CHANGEMENT_RESSOURCES_CONFIG_ID=$tab_rq_info_config['CHANGEMENT_RESSOURCES_CONFIG_ID'];
	$CHANGEMENT_RESSOURCES_CONFIG_LIB=$tab_rq_info_config['CHANGEMENT_RESSOURCES_CONFIG_LIB'];
	$CHANGEMENT_RESSOURCES_CONFIG_TYPE=$tab_rq_info_config['CHANGEMENT_RESSOURCES_CONFIG_TYPE'];
	$CHANGEMENT_RESSOURCES_CONFIG_OBLIGATOIRE=$tab_rq_info_config['CHANGEMENT_RESSOURCES_CONFIG_OBLIGATOIRE'];
	$CHANGEMENT_RESSOURCES_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_RESSOURCES_CONFIG_TABLE'];
	if(isset($tab_rq_info_config['CHANGEMENT_RESSOURCES_INFO_AUTRE_ID'])){
		$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID=$tab_rq_info_config['CHANGEMENT_RESSOURCES_INFO_AUTRE_ID'];
	}
	if(isset($tab_rq_info_config['CHANGEMENT_RESSOURCES_INFO_LIB'])){
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]=$tab_rq_info_config['CHANGEMENT_RESSOURCES_INFO_LIB'];
	}
	if(!isset($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID])){$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]='';}
	$LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID;
	
	switch ($CHANGEMENT_RESSOURCES_CONFIG_TYPE)
	{
	case "liste": 
	
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_RESSOURCES_CONFIG_TABLE'];
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
		$LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID;
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID=$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID];
		$rq_info="
    SELECT 
    `CHANGEMENT_RESSOURCES_ID`
    FROM `changement_ressources`
    WHERE 
    `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."' 
    AND `CHANGEMENT_RESSOURCES_CONFIG_ID` ='".$CHANGEMENT_RESSOURCES_CONFIG_ID."' 
    AND `CHANGEMENT_RESSOURCES_INFO_AUTRE_ID` ='".$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID."' 
    AND `CHANGEMENT_RESSOURCES_VALEUR` =''  
    AND `ENABLE` ='0'
    LIMIT 1";
    
    $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
    $tab_rq_info = mysql_fetch_assoc($res_rq_info);
    $total_ligne_rq_info=mysql_num_rows($res_rq_info);
    mysql_free_result($res_rq_info);
    if($total_ligne_rq_info==0){
      $nb_modif=$nb_modif+1;
      $sql="
      UPDATE `changement_ressources` SET 
      `CHANGEMENT_RESSOURCES_INFO_AUTRE_ID`= '".$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID."'
      WHERE `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."'
      AND `CHANGEMENT_RESSOURCES_CONFIG_ID`='".$CHANGEMENT_RESSOURCES_CONFIG_ID."'
      AND `ENABLE`='0'
      LIMIT 1 ;";
      mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
      $TABLE_SQL_SQL='changement_ressources';       
      historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');
    }
		
	break;
	case "liste_acteur": 
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_RESSOURCES_CONFIG_TABLE'];
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
		$rq_info_config_table="
		SELECT  *
		FROM `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE."`
		WHERE 
		`ENABLE`='0'
		AND `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI."`!='vide'
		ORDER BY `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI."`";
		$res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
		$tab_rq_info_ressources_table = mysql_fetch_assoc($res_rq_info_config_table);
		$total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);
		
		if($total_ligne_rq_info_config_table!=0){
		do {
		  $ID_SQL=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE).'_ID';
		  $ID_LIB=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
		  $CHANGEMENT_RESSOURCES_CONFIG_STOP[$CHANGEMENT_RESSOURCES_CONFIG_ID]=0;
		  $CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID=$tab_rq_info_ressources_table[$ID_SQL];
		  $CHANGEMENT_RESSOURCES_CONFIG_TABLE_LIB=$tab_rq_info_ressources_table[$ID_LIB];
		  $CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID;
		  $LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE;
		  $CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		  
		  $CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_COM';
		  $LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM;
		  $CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		  
		  $CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_INFO=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_INFO';
		  $LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_INFO;
		  $CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_INFO]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		  
		  $CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM_INFO=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_COM_INFO';
		  $LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM_INFO;
		  $CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM_INFO]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		  
		  $rq_info="
    SELECT 
    `CHANGEMENT_RESSOURCES_ID`
    FROM `changement_ressources`
    WHERE 
    `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."' 
    AND `CHANGEMENT_RESSOURCES_CONFIG_ID` ='".$CHANGEMENT_RESSOURCES_CONFIG_ID."' 
    AND `CHANGEMENT_RESSOURCES_INFO_AUTRE_ID` ='".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID."' 
    AND `CHANGEMENT_RESSOURCES_VALEUR` ='".$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]."|".$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_INFO]."|'  
    AND `CHANGEMENT_RESSOURCES_COMMENTAIRE` ='".$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]."|".$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM_INFO]."|'
    AND `ENABLE` ='0'
    LIMIT 1";
    
    $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
    $tab_rq_info = mysql_fetch_assoc($res_rq_info);
    $total_ligne_rq_info=mysql_num_rows($res_rq_info);
    mysql_free_result($res_rq_info);
    if($total_ligne_rq_info==0){
      $nb_modif=$nb_modif+1;
      $sql="
      UPDATE `changement_ressources` SET 
      `CHANGEMENT_RESSOURCES_VALEUR`= '".$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]."|".$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_INFO]."|',
      `CHANGEMENT_RESSOURCES_COMMENTAIRE`= '".$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]."|".$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM_INFO]."|'
      WHERE `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."'
      AND `CHANGEMENT_RESSOURCES_CONFIG_ID`='".$CHANGEMENT_RESSOURCES_CONFIG_ID."'
      AND `CHANGEMENT_RESSOURCES_INFO_AUTRE_ID`= '".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID."'
      AND `ENABLE`='0'
      LIMIT 1 ;";
      mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
      $TABLE_SQL_SQL='changement_ressources';       
      historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');
    }

      } while ($tab_rq_info_ressources_table = mysql_fetch_assoc($res_rq_info_config_table));
		$ligne= mysql_num_rows($res_rq_info_config_table);
		if($ligne > 0) {
		  mysql_data_seek($res_rq_info_config_table, 0);
		  $tab_rq_info_ressources_table = mysql_fetch_assoc($res_rq_info_config_table);
		}
		}

            break;
	case "checkbox": 
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_RESSOURCES_CONFIG_TABLE'];
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
              $rq_info_config_table="
              SELECT  *
              FROM `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE."`
              WHERE 
              `ENABLE`='0'
              ORDER BY `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI."`";
              $res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
              $tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
              $total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);
              if($total_ligne_rq_info_config_table!=0){
                do {
                  $ID_SQL=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE).'_ID';
                  $ID_LIB=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
                  $COM_SQL=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE).'_COM';
                  
                  $CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID=$tab_rq_info_config_table[$ID_SQL];
                  $CHANGEMENT_RESSOURCES_CONFIG_TABLE_LIB=$tab_rq_info_config_table[$ID_LIB];
                  $CHANGEMENT_RESSOURCES_INFO_AUTRE_ID=$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID;
                  if(isset($tab_rq_info_config_table[$COM_SQL])){
                  	$CHANGEMENT_RESSOURCES_CONFIG_TABLE_COM=$tab_rq_info_config_table[$COM_SQL];	
                  }else{
                  	$CHANGEMENT_RESSOURCES_CONFIG_TABLE_COM='non';
                  }
	              $LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID;
	              $CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID;
	              if(!isset($NB_checkbox)){$NB_checkbox=0;}
	              if(isset($tab_var[$LISTE_CONFIG])){
	              	$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
	              	$NB_checkbox=$NB_checkbox+1;
	              }else{
	              	$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]='off';
	              }

	              if($CHANGEMENT_RESSOURCES_CONFIG_TABLE_COM=='oui'){
		              $LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_COM';
		              $CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_COM';
		              if(isset($tab_var[$LISTE_CONFIG])){
		              	$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
		              	
		              }else{
		              	$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]='';
		              }
	              }

	              

	              if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]=='on'){
	              	      $rq_info="
		              SELECT 
		              `CHANGEMENT_RESSOURCES_VALEUR`
		              FROM `changement_ressources`
		              WHERE 
		              `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."' 
		              AND `CHANGEMENT_RESSOURCES_CONFIG_ID` ='".$CHANGEMENT_RESSOURCES_CONFIG_ID."' 
		              AND `CHANGEMENT_RESSOURCES_INFO_AUTRE_ID` ='".$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID."' 
		              AND `ENABLE` ='0'
		              LIMIT 1";
		              $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
		              $tab_rq_info = mysql_fetch_assoc($res_rq_info);
		              $total_ligne_rq_info=mysql_num_rows($res_rq_info);
		              mysql_free_result($res_rq_info);
		              if($total_ligne_rq_info==0){
		              	$nb_modif=$nb_modif+1;
				$sql="INSERT INTO `changement_ressources` (
				`CHANGEMENT_RESSOURCES_ID` ,
				`CHANGEMENT_LISTE_ID` ,
				`CHANGEMENT_RESSOURCES_CONFIG_ID` ,
				`CHANGEMENT_RESSOURCES_INFO_AUTRE_ID` ,
				`CHANGEMENT_RESSOURCES_VALEUR`,
				`ENABLE` ) 
				VALUES (NULL ,
				 '".$CHANGEMENT_ID."', 
				 '".$CHANGEMENT_RESSOURCES_CONFIG_ID."', 
				 '".$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID."', 
				 '".$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]."', 
				 '0');";
				mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
				$TABLE_SQL_SQL='changement_ressources';       
				historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');
		              }else{
		                $TEST_CHANGEMENT_RESSOURCES_INFO_LIB=$tab_rq_info['CHANGEMENT_RESSOURCES_VALEUR'];
		                if($TEST_CHANGEMENT_RESSOURCES_INFO_LIB!=$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]){
		                	$nb_modif=$nb_modif+1;
					$sql="UPDATE  `changement_ressources` SET
					`CHANGEMENT_RESSOURCES_VALEUR`='".$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]."'
					WHERE `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."' 
			              AND `CHANGEMENT_RESSOURCES_CONFIG_ID` ='".$CHANGEMENT_RESSOURCES_CONFIG_ID."' 
			              AND `CHANGEMENT_RESSOURCES_INFO_AUTRE_ID` ='".$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID."';";
					mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
					$TABLE_SQL_SQL='changement_ressources';       
					historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');
		                }
		              }
	              }else{
	              	      $rq_info="
		              SELECT 
		              `CHANGEMENT_RESSOURCES_ID`
		              FROM `changement_ressources`
		              WHERE 
		              `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."' 
		              AND `CHANGEMENT_RESSOURCES_CONFIG_ID` ='".$CHANGEMENT_RESSOURCES_CONFIG_ID."' 
		              AND `CHANGEMENT_RESSOURCES_INFO_AUTRE_ID` ='".$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID."'  
		              AND `ENABLE` ='0'
		              LIMIT 1";
		              $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
		              $tab_rq_info = mysql_fetch_assoc($res_rq_info);
		              $total_ligne_rq_info=mysql_num_rows($res_rq_info);
		              mysql_free_result($res_rq_info);
		              if($total_ligne_rq_info!=0){
		              	$nb_modif=$nb_modif+1;
		              	$sql="
		              	UPDATE `changement_ressources` SET `ENABLE` = '1' 
	              		WHERE `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."' 
				AND `CHANGEMENT_RESSOURCES_CONFIG_ID` ='".$CHANGEMENT_RESSOURCES_CONFIG_ID."'
				AND `CHANGEMENT_RESSOURCES_INFO_AUTRE_ID` ='".$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID."'
				AND `ENABLE`='0'
				LIMIT 1 ;";
				mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
				$TABLE_SQL_SQL='changement_ressources';       
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
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_RESSOURCES_CONFIG_TABLE'];
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
              $rq_info_config_table="
              SELECT  *
              FROM `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE."`
              WHERE 
              `ENABLE`='0'
              ORDER BY `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI."`";
              $res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
              $tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
              $total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);
              if($total_ligne_rq_info_config_table!=0){
                do {
                  $ID_SQL=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE).'_ID';
                  $ID_LIB=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
                  $COM_SQL=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE).'_COM';
                  
                  $CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID=$tab_rq_info_config_table[$ID_SQL];
                  $CHANGEMENT_RESSOURCES_CONFIG_TABLE_LIB=$tab_rq_info_config_table[$ID_LIB];
                  $CHANGEMENT_RESSOURCES_INFO_AUTRE_ID=$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID;
                  if(isset($tab_rq_info_config_table[$COM_SQL])){
                  	$CHANGEMENT_RESSOURCES_CONFIG_TABLE_COM=$tab_rq_info_config_table[$COM_SQL];	
                  }else{
                  	$CHANGEMENT_RESSOURCES_CONFIG_TABLE_COM='non';
                  }
	              $LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID;
	              $CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID;
	              if(!isset($NB_checkbox)){$NB_checkbox=0;}
	              if(isset($tab_var[$LISTE_CONFIG])){
	              	$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
	              	$NB_checkbox=$NB_checkbox+1;
	              }else{
	              	$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]='off';
	              }

	              if($CHANGEMENT_RESSOURCES_CONFIG_TABLE_COM=='oui'){
		              $LISTE_CONFIG='CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_COM';
		              $CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_COM';
		              if(isset($tab_var[$LISTE_CONFIG])){
		              	$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
		              	
		              }else{
		              	$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]='';
		              }
	              }

	              

	              if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]=='on'){
	              	      $rq_info="
		              SELECT 
		              `CHANGEMENT_RESSOURCES_VALEUR`
		              FROM `changement_ressources`
		              WHERE 
		              `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."' 
		              AND `CHANGEMENT_RESSOURCES_CONFIG_ID` ='".$CHANGEMENT_RESSOURCES_CONFIG_ID."' 
		              AND `CHANGEMENT_RESSOURCES_INFO_AUTRE_ID` ='".$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID."' 
		              AND `ENABLE` ='0'
		              LIMIT 1";
		              $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
		              $tab_rq_info = mysql_fetch_assoc($res_rq_info);
		              $total_ligne_rq_info=mysql_num_rows($res_rq_info);
		              mysql_free_result($res_rq_info);
		              if($total_ligne_rq_info==0){
		              	$nb_modif=$nb_modif+1;
				$sql="INSERT INTO `changement_ressources` (
				`CHANGEMENT_RESSOURCES_ID` ,
				`CHANGEMENT_LISTE_ID` ,
				`CHANGEMENT_RESSOURCES_CONFIG_ID` ,
				`CHANGEMENT_RESSOURCES_INFO_AUTRE_ID` ,
				`CHANGEMENT_RESSOURCES_VALEUR`,
				`ENABLE` ) 
				VALUES (NULL ,
				 '".$CHANGEMENT_ID."', 
				 '".$CHANGEMENT_RESSOURCES_CONFIG_ID."', 
				 '".$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID."', 
				 '".$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]."', 
				 '0');";
				mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
				$TABLE_SQL_SQL='changement_ressources';       
				historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');
		              }else{
		                $TEST_CHANGEMENT_RESSOURCES_INFO_LIB=$tab_rq_info['CHANGEMENT_RESSOURCES_VALEUR'];
		                if($TEST_CHANGEMENT_RESSOURCES_INFO_LIB!=$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]){
		                	$nb_modif=$nb_modif+1;
					$sql="UPDATE  `changement_ressources` SET
					`CHANGEMENT_RESSOURCES_VALEUR`='".$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]."'
					WHERE `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."' 
			              AND `CHANGEMENT_RESSOURCES_CONFIG_ID` ='".$CHANGEMENT_RESSOURCES_CONFIG_ID."' 
			              AND `CHANGEMENT_RESSOURCES_INFO_AUTRE_ID` ='".$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID."';";
					mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
					$TABLE_SQL_SQL='changement_ressources';       
					historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');
		                }
		              }
	              }else{
	              	      $rq_info="
		              SELECT 
		              `CHANGEMENT_RESSOURCES_ID`
		              FROM `changement_ressources`
		              WHERE 
		              `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."' 
		              AND `CHANGEMENT_RESSOURCES_CONFIG_ID` ='".$CHANGEMENT_RESSOURCES_CONFIG_ID."' 
		              AND `CHANGEMENT_RESSOURCES_INFO_AUTRE_ID` ='".$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID."'  
		              AND `ENABLE` ='0'
		              LIMIT 1";
		              $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
		              $tab_rq_info = mysql_fetch_assoc($res_rq_info);
		              $total_ligne_rq_info=mysql_num_rows($res_rq_info);
		              mysql_free_result($res_rq_info);
		              if($total_ligne_rq_info!=0){
		              	$nb_modif=$nb_modif+1;
		              	$sql="
		              	UPDATE `changement_ressources` SET `ENABLE` = '1' 
	              		WHERE `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."' 
				AND `CHANGEMENT_RESSOURCES_CONFIG_ID` ='".$CHANGEMENT_RESSOURCES_CONFIG_ID."'
				AND `CHANGEMENT_RESSOURCES_INFO_AUTRE_ID` ='".$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID."'
				AND `ENABLE`='0'
				LIMIT 1 ;";
				mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
				$TABLE_SQL_SQL='changement_ressources';       
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
		$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID=0;
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		$rq_info="
    SELECT 
    `CHANGEMENT_RESSOURCES_ID`
    FROM `changement_ressources`
    WHERE 
    `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."' 
    AND `CHANGEMENT_RESSOURCES_CONFIG_ID` ='".$CHANGEMENT_RESSOURCES_CONFIG_ID."' 
    AND `CHANGEMENT_RESSOURCES_INFO_AUTRE_ID` ='".$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID."' 
    AND `CHANGEMENT_RESSOURCES_VALEUR` ='".$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]."'  
    AND `ENABLE` ='0'
    LIMIT 1";
    
    $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
    $tab_rq_info = mysql_fetch_assoc($res_rq_info);
    $total_ligne_rq_info=mysql_num_rows($res_rq_info);
    mysql_free_result($res_rq_info);
    if($total_ligne_rq_info==0){
      $nb_modif=$nb_modif+1;
      $sql="
      UPDATE `changement_ressources` SET 
      `CHANGEMENT_RESSOURCES_VALEUR`= '".$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]."'
      WHERE `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."'
      AND `CHANGEMENT_RESSOURCES_CONFIG_ID`='".$CHANGEMENT_RESSOURCES_CONFIG_ID."'
      AND `CHANGEMENT_RESSOURCES_INFO_AUTRE_ID`= '".$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID."'
      AND `ENABLE`='0'
      LIMIT 1 ;";
      mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
      $TABLE_SQL_SQL='changement_ressources';       
      historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');
    }
	break;
	
	case "text": 
		$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID=0;
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		$rq_info="
    SELECT 
    `CHANGEMENT_RESSOURCES_ID`
    FROM `changement_ressources`
    WHERE 
    `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."' 
    AND `CHANGEMENT_RESSOURCES_CONFIG_ID` ='".$CHANGEMENT_RESSOURCES_CONFIG_ID."' 
    AND `CHANGEMENT_RESSOURCES_INFO_AUTRE_ID` ='".$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID."' 
    AND `CHANGEMENT_RESSOURCES_VALEUR` ='".$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]."'  
    AND `ENABLE` ='0'
    LIMIT 1";
    
    $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
    $tab_rq_info = mysql_fetch_assoc($res_rq_info);
    $total_ligne_rq_info=mysql_num_rows($res_rq_info);
    mysql_free_result($res_rq_info);
    if($total_ligne_rq_info==0){
      $nb_modif=$nb_modif+1;
      $sql="
      UPDATE `changement_ressources` SET 
      `CHANGEMENT_RESSOURCES_VALEUR`= '".$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]."'
      WHERE `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."'
      AND `CHANGEMENT_RESSOURCES_CONFIG_ID`='".$CHANGEMENT_RESSOURCES_CONFIG_ID."'
      AND `CHANGEMENT_RESSOURCES_INFO_AUTRE_ID`= '".$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID."'
      AND `ENABLE`='0'
      LIMIT 1 ;";
      mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
      $TABLE_SQL_SQL='changement_ressources';       
      historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');
    }
	break;
	case "risque": 
		$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID=0;
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		$LISTE_CONFIG_COMMENTAIRE=$LISTE_CONFIG.'_COMMENTAIRE';
		$CHANGEMENT_RESSOURCES_CONFIG_ID_COMMENTAIRE=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_COMMENTAIRE';
		if(!isset($tab_var[$LISTE_CONFIG_COMMENTAIRE])){$tab_var[$LISTE_CONFIG_COMMENTAIRE]='';}
		$CHANGEMENT_RESSOURCES_CONFIG[$LISTE_CONFIG_COMMENTAIRE]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG_COMMENTAIRE])));
		$rq_info="
    SELECT 
    `CHANGEMENT_RESSOURCES_ID`
    FROM `changement_ressources`
    WHERE 
    `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."' 
    AND `CHANGEMENT_RESSOURCES_CONFIG_ID` ='".$CHANGEMENT_RESSOURCES_CONFIG_ID."' 
    AND `CHANGEMENT_RESSOURCES_INFO_AUTRE_ID` ='".$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID."' 
    AND `CHANGEMENT_RESSOURCES_VALEUR` ='".$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]."'  
    AND `CHANGEMENT_RESSOURCES_COMMENTAIRE` ='".$CHANGEMENT_RESSOURCES_CONFIG[$LISTE_CONFIG_COMMENTAIRE]."'
    AND `ENABLE` ='0'
    LIMIT 1";
    
    $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
    $tab_rq_info = mysql_fetch_assoc($res_rq_info);
    $total_ligne_rq_info=mysql_num_rows($res_rq_info);
    mysql_free_result($res_rq_info);
    if($total_ligne_rq_info==0){
      $nb_modif=$nb_modif+1;
      $sql="
      UPDATE `changement_ressources` SET 
      `CHANGEMENT_RESSOURCES_VALEUR`= '".$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]."',
      `CHANGEMENT_RESSOURCES_COMMENTAIRE`= '".$CHANGEMENT_RESSOURCES_CONFIG[$LISTE_CONFIG_COMMENTAIRE]."'
      WHERE `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."'
      AND `CHANGEMENT_RESSOURCES_CONFIG_ID`='".$CHANGEMENT_RESSOURCES_CONFIG_ID."'
      AND `CHANGEMENT_RESSOURCES_INFO_AUTRE_ID`= '".$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID."'
      AND `ENABLE`='0'
      LIMIT 1 ;";
      mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
      $TABLE_SQL_SQL='changement_ressources';       
      historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');
    }
	break;
	case "oui-non": 
		$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID=0;
		$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
		$LISTE_CONFIG_COMMENTAIRE=$LISTE_CONFIG.'_COMMENTAIRE';
		$CHANGEMENT_RESSOURCES_CONFIG_ID_COMMENTAIRE=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_COMMENTAIRE';
		if(!isset($tab_var[$LISTE_CONFIG_COMMENTAIRE])){$tab_var[$LISTE_CONFIG_COMMENTAIRE]='';}
		$CHANGEMENT_RESSOURCES_CONFIG[$LISTE_CONFIG_COMMENTAIRE]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG_COMMENTAIRE])));
		$rq_info="
    SELECT 
    `CHANGEMENT_RESSOURCES_ID`
    FROM `changement_ressources`
    WHERE 
    `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."' 
    AND `CHANGEMENT_RESSOURCES_CONFIG_ID` ='".$CHANGEMENT_RESSOURCES_CONFIG_ID."' 
    AND `CHANGEMENT_RESSOURCES_INFO_AUTRE_ID` ='".$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID."' 
    AND `CHANGEMENT_RESSOURCES_VALEUR` ='".$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]."'  
    AND `CHANGEMENT_RESSOURCES_COMMENTAIRE` ='".$CHANGEMENT_RESSOURCES_CONFIG[$LISTE_CONFIG_COMMENTAIRE]."'
    AND `ENABLE` ='0'
    LIMIT 1";
    
    $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
    $tab_rq_info = mysql_fetch_assoc($res_rq_info);
    $total_ligne_rq_info=mysql_num_rows($res_rq_info);
    mysql_free_result($res_rq_info);
    if($total_ligne_rq_info==0){
      $nb_modif=$nb_modif+1;
      $sql="
      UPDATE `changement_ressources` SET 
      `CHANGEMENT_RESSOURCES_VALEUR`= '".$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]."',
      `CHANGEMENT_RESSOURCES_COMMENTAIRE`= '".$CHANGEMENT_RESSOURCES_CONFIG[$LISTE_CONFIG_COMMENTAIRE]."'
      WHERE `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."'
      AND `CHANGEMENT_RESSOURCES_CONFIG_ID`='".$CHANGEMENT_RESSOURCES_CONFIG_ID."'
      AND `CHANGEMENT_RESSOURCES_INFO_AUTRE_ID`= '".$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID."'
      AND `ENABLE`='0'
      LIMIT 1 ;";
      mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
      $TABLE_SQL_SQL='changement_ressources';       
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
		if($CHANGEMENT_STATUS == 'Brouillon' ){
			$rq_info_etat="
			SELECT `CHANGEMENT_RESSOURCES_ID`
			FROM `changement_ressources` 
			WHERE 
			`CHANGEMENT_LISTE_ID` ='".$ID."' 
			AND `CHANGEMENT_RESSOURCES_ETAT` = 'I'
			AND `ENABLE` = '0'";
			$res_rq_info_etat = mysql_query($rq_info_etat, $mysql_link) or die(mysql_error());
			$tab_rq_info_etat = mysql_fetch_assoc($res_rq_info_etat);
			$total_ligne_rq_info_etat=mysql_num_rows($res_rq_info_etat);
			mysql_free_result($res_rq_info_etat); 
			if($total_ligne_rq_info_etat==0){
				$sql="
				UPDATE `changement_ressources` 
				SET `CHANGEMENT_RESSOURCES_ETAT` = 'I' 
				WHERE `CHANGEMENT_LISTE_ID` ='".$ID."' 
				AND `ENABLE` = '0';";		
				mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
				$TABLE_SQL_SQL='changement_ressources';       
				historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');
			}
		}
		$rq_info_etat="
		SELECT `CHANGEMENT_FAR_ID` 
		FROM `changement_far` 
		WHERE `CHANGEMENT_FAR_ETAT` = 'I'
		AND `CHANGEMENT_LISTE_ID`='".$ID."'
		AND `ENABLE`='0'";
		$res_rq_info_etat = mysql_query($rq_info_etat, $mysql_link) or die(mysql_error());
		$tab_rq_info_etat = mysql_fetch_assoc($res_rq_info_etat);
		$total_ligne_rq_info_etat=mysql_num_rows($res_rq_info_etat);
		if($total_ligne_rq_info_etat==0){
			$TRACE_ETAT=$CHANGEMENT_STATUS_ID;
			$TRACE_CATEGORIE='Changement';
			$TRACE_TABLE='changement_ressources';
			$TRACE_REF_ID=$ID;
			$TRACE_ACTION='Modif';
			moteur_trace($TRACE_CATEGORIE,$TRACE_TABLE,$TRACE_REF_ID,$TRACE_ACTION,$TRACE_ETAT);
			$rq_info_etat="
			SELECT `CHANGEMENT_FAR_ID` 
			FROM `changement_far` 
			WHERE 
			`CHANGEMENT_LISTE_ID`='".$ID."'
			AND `ENABLE`='0'";
			$res_rq_info_etat = mysql_query($rq_info_etat, $mysql_link) or die(mysql_error());
			$tab_rq_info_etat = mysql_fetch_assoc($res_rq_info_etat);
			$total_ligne_rq_info_etat=mysql_num_rows($res_rq_info_etat);
			if($total_ligne_rq_info_etat==0){ 
				echo '
				<script language="JavaScript">
				url=("./index.php?ITEM=changement_Ajout_FAR&action=Ajout&ID='.$ID.'");
				window.location=url;
				</script>
				';
			}else{
				echo '
				<script language="JavaScript">
				url=("./index.php?ITEM=changement_Modif_FAR&action=Modif&ID='.$ID.'");
				window.location=url;
				</script>
				';
			}
		}else{
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
			$TRACE_TABLE='changement_ressources';
			$TRACE_REF_ID=$ID;
			$TRACE_ACTION='Modif';
			moteur_trace($TRACE_CATEGORIE,$TRACE_TABLE,$TRACE_REF_ID,$TRACE_ACTION,$TRACE_ETAT);
			
			// mail d'inscription			
			echo '
			<script language="JavaScript">
			url=("./index.php?ITEM=changement_Send_Mail&type=inscription&ID='.$ID.'");
			window.location=url;
			</script>
			';
		}
		mysql_free_result($res_rq_info_etat);
	
	
	}
	}
	$_GET['action']="Modif";
  	
  }
}
//# Cas action Info
if($action=="Info"){
	$rq_info_ressources="
	SELECT * 
	FROM `changement_ressources`
	WHERE `CHANGEMENT_LISTE_ID`='".$ID."'
	AND`ENABLE` = '0'
	";
	$res_rq_info_ressources = mysql_query($rq_info_ressources, $mysql_link) or die(mysql_error());
	$tab_rq_info_ressources = mysql_fetch_assoc($res_rq_info_ressources);
	$total_ligne_rq_info_ressources=mysql_num_rows($res_rq_info_ressources);
	
	if($total_ligne_rq_info_ressources!=0){
		do {
			$CHANGEMENT_RESSOURCES_CONFIG_ID=$tab_rq_info_ressources['CHANGEMENT_RESSOURCES_CONFIG_ID'];
			$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]=$tab_rq_info_ressources['CHANGEMENT_RESSOURCES_VALEUR'];  
			$LISTE_CONFIG_COMMENTAIRE=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_COMMENTAIRE';
			$CHANGEMENT_RESSOURCES_CONFIG[$LISTE_CONFIG_COMMENTAIRE]=$tab_rq_info_ressources['CHANGEMENT_RESSOURCES_COMMENTAIRE'];      
		} while ($tab_rq_info_ressources = mysql_fetch_assoc($res_rq_info_ressources));
		$ligne= mysql_num_rows($res_rq_info_ressources);
		if($ligne > 0) {
		  mysql_data_seek($res_rq_info_ressources, 0);
		  $tab_rq_info_ressources = mysql_fetch_assoc($res_rq_info_ressources);
		}
	}
	mysql_free_result($res_rq_info_ressources);

}
//# Cas action modifer
if($action=="Modif"){
	$rq_info_ressources="
	SELECT * 
	FROM `changement_ressources`
	WHERE `CHANGEMENT_LISTE_ID`='".$ID."'
	AND`ENABLE` = '0'
	";
	$res_rq_info_ressources = mysql_query($rq_info_ressources, $mysql_link) or die(mysql_error());
	$tab_rq_info_ressources = mysql_fetch_assoc($res_rq_info_ressources);
	$total_ligne_rq_info_ressources=mysql_num_rows($res_rq_info_ressources);
	
	if($total_ligne_rq_info_ressources!=0){
		do {
			$CHANGEMENT_RESSOURCES_CONFIG_ID=$tab_rq_info_ressources['CHANGEMENT_RESSOURCES_CONFIG_ID'];
			$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]=$tab_rq_info_ressources['CHANGEMENT_RESSOURCES_VALEUR'];   
			$LISTE_CONFIG_COMMENTAIRE=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_COMMENTAIRE';
			$CHANGEMENT_RESSOURCES_CONFIG[$LISTE_CONFIG_COMMENTAIRE]=$tab_rq_info_ressources['CHANGEMENT_RESSOURCES_COMMENTAIRE'];       
		} while ($tab_rq_info_ressources = mysql_fetch_assoc($res_rq_info_ressources));
		$ligne= mysql_num_rows($res_rq_info_ressources);
		if($ligne > 0) {
		  mysql_data_seek($res_rq_info_ressources, 0);
		  $tab_rq_info_ressources = mysql_fetch_assoc($res_rq_info_ressources);
		}
	}
	mysql_free_result($res_rq_info_ressources);

}

//# Cas action Info
if($action=="Info"){
	$rq_info_ressources="
	SELECT `changement_status`.`CHANGEMENT_STATUS` 
	FROM `changement_liste` , `changement_status` 
	WHERE `changement_liste`.`CHANGEMENT_LISTE_ID` = '".$ID."'
	AND `changement_liste`.`CHANGEMENT_STATUS_ID` = `changement_status`.`CHANGEMENT_STATUS_ID` 
	AND `changement_liste`.`ENABLE` = '0'
	";
	$res_rq_info_ressources = mysql_query($rq_info_ressources, $mysql_link) or die(mysql_error());
	$tab_rq_info_ressources = mysql_fetch_assoc($res_rq_info_ressources);
	$total_ligne_rq_info_ressources=mysql_num_rows($res_rq_info_ressources);
	if($total_ligne_rq_info_ressources==0){
		$CHANGEMENT_STATUS='Brouillon';
	}else{
		$CHANGEMENT_STATUS=$tab_rq_info_ressources['CHANGEMENT_STATUS'];;		
	}
	mysql_free_result($res_rq_info_ressources);
}
if($action=="Modif"){
	$rq_info_ressources="
	SELECT `changement_status`.`CHANGEMENT_STATUS` 
	FROM `changement_liste` , `changement_status` 
	WHERE `changement_liste`.`CHANGEMENT_LISTE_ID` = '".$ID."'
	AND `changement_liste`.`CHANGEMENT_STATUS_ID` = `changement_status`.`CHANGEMENT_STATUS_ID` 
	AND `changement_liste`.`ENABLE` = '0'
	";
	$res_rq_info_ressources = mysql_query($rq_info_ressources, $mysql_link) or die(mysql_error());
	$tab_rq_info_ressources = mysql_fetch_assoc($res_rq_info_ressources);
	$total_ligne_rq_info_ressources=mysql_num_rows($res_rq_info_ressources);
	if($total_ligne_rq_info_ressources==0){
		$CHANGEMENT_STATUS='Brouillon';
	}else{
		$CHANGEMENT_STATUS=$tab_rq_info_ressources['CHANGEMENT_STATUS'];
		
	}
	mysql_free_result($res_rq_info_ressources);
}
echo '
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
	ORDER BY `moteur_trace`.`MOTEUR_TRACE_DATE_TRI` DESC";
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
      Cr&eacute;ation de la Fiche de ressouces du changement n&deg; <a href="#" class="infobulledroite">'.$ID.'<span>'.$AFF_SPAN.'</span></a>';
    }
    if($action=="Modif"){
      if($CHANGEMENT_STATUS=='Brouillon'){
        echo 'Modification de la Fiche de ressouces du changement n&deg; <a href="#" class="infobulledroite">'.$ID.'<span>'.$AFF_SPAN.'</span></a>';
      }else{
        if($ROLE==0){
          echo 'Modification de la Fiche de ressouces du changement n&deg; <a href="#" class="infobulledroite">'.$ID.'<span>'.$AFF_SPAN.'</span></a>';
        }else{
          echo 'Information de la Fiche de ressouces du changement n&deg; <a href="#" class="infobulledroite">'.$ID.'<span>'.$AFF_SPAN.'</span></a>';
        }
      }
    }
    if($action=="Info"){
    	echo 'Information de la Fiche de ressouces du changement n&deg; <a href="#" class="infobulledroite">'.$ID.'<span>'.$AFF_SPAN.'</span></a>';
    }
    
    echo '&nbsp;]&nbsp;</h2></td>		
      </tr>';
	if($action=="Ajout"){
    $rq_info_ressources_lib="
      SELECT DISTINCT (`CHANGEMENT_RESSOURCES_CONFIG_LIB`) AS `CHANGEMENT_RESSOURCES_CONFIG_LIB`
      FROM `changement_ressources_config` 
      WHERE 
      `ENABLE` ='0'
      ORDER BY `CHANGEMENT_RESSOURCES_CONFIG_LIB` 
      ";
	}else{
        $rq_info_ressources_lib="
        SELECT DISTINCT (`changement_ressources_config`.`CHANGEMENT_RESSOURCES_CONFIG_LIB`) AS `CHANGEMENT_RESSOURCES_CONFIG_LIB`
        FROM `changement_ressources_config` , `changement_ressources`
        WHERE `changement_ressources_config`.`CHANGEMENT_RESSOURCES_CONFIG_ID` = `changement_ressources`.`CHANGEMENT_RESSOURCES_CONFIG_ID`
        AND `changement_ressources`.`CHANGEMENT_LISTE_ID` = '".$ID."'
        AND `changement_ressources`.`ENABLE` = '0'
        ORDER BY `changement_ressources_config`.`CHANGEMENT_RESSOURCES_CONFIG_LIB`";
      }
      $res_rq_info_ressources_lib = mysql_query($rq_info_ressources_lib, $mysql_link) or die(mysql_error());
      $tab_rq_info_ressources_lib = mysql_fetch_assoc($res_rq_info_ressources_lib);
      $total_ligne_rq_info_ressources_lib=mysql_num_rows($res_rq_info_ressources_lib);
      if($total_ligne_rq_info_ressources_lib!=0){
        do {
        	$CHANGEMENT_RESSOURCES_CONFIG_LIB=$tab_rq_info_ressources_lib['CHANGEMENT_RESSOURCES_CONFIG_LIB'];
      echo '
      <tr align="center" class="titre">
        <td colspan="2">&nbsp;'.stripslashes(substr($CHANGEMENT_RESSOURCES_CONFIG_LIB,strpos($CHANGEMENT_RESSOURCES_CONFIG_LIB,"-")+1)).'&nbsp;</td>		
      </tr>';
		if($action=="Ajout"){
              	$rq_info_ressources="
              	SELECT 
              	`changement_ressources_config`.`CHANGEMENT_RESSOURCES_CONFIG_ID` , 
              	`changement_ressources_config`.`CHANGEMENT_RESSOURCES_CONFIG_LIB` , 
              	`changement_ressources_config`.`CHANGEMENT_RESSOURCES_CONFIG_CRITERE` ,
              	`changement_ressources_config`.`CHANGEMENT_RESSOURCES_CONFIG_TYPE` , 
              	`changement_ressources_config`.`CHANGEMENT_RESSOURCES_CONFIG_TABLE` , 
              	`changement_ressources_config`.`CHANGEMENT_RESSOURCES_CONFIG_OBLIGATOIRE` , 
              	`changement_ressources_config`.`CHANGEMENT_RESSOURCES_CONFIG_ORDRE` , 
              	`changement_ressources_config`.`ENABLE` 
              	FROM `changement_ressources_config` 
              	WHERE `changement_ressources_config`.`CHANGEMENT_RESSOURCES_CONFIG_LIB`='".$CHANGEMENT_RESSOURCES_CONFIG_LIB."' AND 
              	`changement_ressources_config`.`ENABLE` = '0'
              	ORDER BY `changement_ressources_config`.`CHANGEMENT_RESSOURCES_CONFIG_ORDRE` 

              	";
        	}else{
                  $rq_info_ressources="
                  SELECT 
                  `changement_ressources_config`.`CHANGEMENT_RESSOURCES_CONFIG_ID` , 
              	  `changement_ressources_config`.`CHANGEMENT_RESSOURCES_CONFIG_LIB` , 
              	  `changement_ressources_config`.`CHANGEMENT_RESSOURCES_CONFIG_CRITERE` , 
              	  `changement_ressources_config`.`CHANGEMENT_RESSOURCES_CONFIG_TYPE` , 
              	  `changement_ressources_config`.`CHANGEMENT_RESSOURCES_CONFIG_TABLE` , 
              	  `changement_ressources_config`.`CHANGEMENT_RESSOURCES_CONFIG_OBLIGATOIRE` , 
              	  `changement_ressources_config`.`CHANGEMENT_RESSOURCES_CONFIG_ORDRE` , 
              	  `changement_ressources_config`.`ENABLE` 
                  FROM `changement_ressources_config` , `changement_ressources`
                  WHERE `changement_ressources_config`.`CHANGEMENT_RESSOURCES_CONFIG_ID` = `changement_ressources`.`CHANGEMENT_RESSOURCES_CONFIG_ID`
                  AND `changement_ressources`.`CHANGEMENT_LISTE_ID` = '".$ID."'
                  AND `changement_ressources_config`.`CHANGEMENT_RESSOURCES_CONFIG_LIB`='".$CHANGEMENT_RESSOURCES_CONFIG_LIB."' 
                  AND `changement_ressources`.`ENABLE` = '0'
                  GROUP BY `changement_ressources_config`.`CHANGEMENT_RESSOURCES_CONFIG_ID`
                  ORDER BY `changement_ressources_config`.`CHANGEMENT_RESSOURCES_CONFIG_ORDRE`
                   ";
              	}
	      $res_rq_info_ressources = mysql_query($rq_info_ressources, $mysql_link) or die(mysql_error());
	      $tab_rq_info_ressources = mysql_fetch_assoc($res_rq_info_ressources);
	      $total_ligne_rq_info_ressources=mysql_num_rows($res_rq_info_ressources);
	      if($total_ligne_rq_info_ressources!=0){
	        do {
	        	$CHANGEMENT_RESSOURCES_CONFIG_ID=$tab_rq_info_ressources['CHANGEMENT_RESSOURCES_CONFIG_ID'];
	        	$CHANGEMENT_RESSOURCES_CONFIG_CRITERE=$tab_rq_info_ressources['CHANGEMENT_RESSOURCES_CONFIG_CRITERE'];
	        	$CHANGEMENT_RESSOURCES_CONFIG_TYPE=$tab_rq_info_ressources['CHANGEMENT_RESSOURCES_CONFIG_TYPE'];
	        	$CHANGEMENT_RESSOURCES_CONFIG_OBLIGATOIRE=$tab_rq_info_ressources['CHANGEMENT_RESSOURCES_CONFIG_OBLIGATOIRE'];
	        	if(isset($tab_rq_info_ressources['CHANGEMENT_RESSOURCES_INFO_AUTRE_ID'])){
	      			$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID=$tab_rq_info_ressources['CHANGEMENT_RESSOURCES_INFO_AUTRE_ID'];
			}else{
				$CHANGEMENT_RESSOURCES_INFO_AUTRE_ID=0;
			}
			if(isset($tab_rq_info_ressources['CHANGEMENT_RESSOURCES_INFO_LIB'])){
	      			$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]=$tab_rq_info_ressources['CHANGEMENT_RESSOURCES_INFO_LIB'];
			}
			if(!isset($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID])){$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]='';}
		
	        	$info_OBLIGATOIRE='';
	       		if($CHANGEMENT_RESSOURCES_CONFIG_OBLIGATOIRE=='oui'){$info_OBLIGATOIRE='*';}
	        	if(!isset($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID])){$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]='';}
	        	$LISTE_CONFIG_COMMENTAIRE=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_COMMENTAIRE';
	        	if(!isset($CHANGEMENT_RESSOURCES_CONFIG[$LISTE_CONFIG_COMMENTAIRE])){$CHANGEMENT_RESSOURCES_CONFIG[$LISTE_CONFIG_COMMENTAIRE]='';}
	       $j++;
		    if ($j%2) { $class = "pair";}else{$class = "impair";} 
		  echo '
		    <tr class="'.$class.'">
		     <td align="left">&nbsp;'.stripslashes($CHANGEMENT_RESSOURCES_CONFIG_CRITERE).'&nbsp;'.$info_OBLIGATOIRE.'&nbsp;</td>
		     <td align="left">';
		      switch ($CHANGEMENT_RESSOURCES_CONFIG_TYPE)
            {
            case "oui-non": 
              $AFF_SPAN_AIDE='';
              echo '
              <INPUT type="radio" '.$disabled_var.' name="CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'" value="Oui"';
              if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]=='Oui'){echo ' CHECKED';} 
              echo '>
              &nbsp;Oui&nbsp;/&nbsp;Non&nbsp;
              <INPUT type="radio" '.$disabled_var.' name="CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'" value="Non"'; 
              if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]=='Non'){echo ' CHECKED';} 
              echo '>';

              if($AFF_SPAN_AIDE!=''){
              	echo '&nbsp;<a href="#" class="infobullegauche"><font color=#000000>&nbsp;- Aide&nbsp;</font><span>'.$AFF_SPAN_AIDE.'</span></a>';
              }
              
            break;
            case "risque": 
            $AFF_SPAN_AIDE='';
            $AFF_SPAN_AIDE.='1 Faible < > 4 Fort';
              echo '
              &nbsp;1&nbsp;<INPUT type="radio" '.$disabled_var.' name="CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'" value="1"';
              if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]=='1'){echo ' CHECKED';} 
              echo '>
              &nbsp;2&nbsp;<INPUT type="radio" '.$disabled_var.' name="CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'" value="2"'; 
              if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]=='2'){echo ' CHECKED';} 
              echo '>
              &nbsp;3&nbsp;<INPUT type="radio" '.$disabled_var.' name="CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'" value="3"'; 
              if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]=='3'){echo ' CHECKED';} 
              echo '>
              &nbsp;4&nbsp;<INPUT type="radio" '.$disabled_var.' name="CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'" value="4"'; 
              if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]=='4'){echo ' CHECKED';} 
              echo '>';
              if($AFF_SPAN_AIDE!=''){
              	echo '&nbsp;<a href="#" class="infobullegauche"><font color=#000000>&nbsp;- Aide&nbsp;</font><span>'.$AFF_SPAN_AIDE.'</span></a>';
              }
            break;
            case "liste": 
              $AFF_SPAN_AIDE='';
              $CHANGEMENT_RESSOURCES_CONFIG_TABLE=$tab_rq_info_ressources['CHANGEMENT_RESSOURCES_CONFIG_TABLE'];
              $CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
              $rq_info_config_table="
              SELECT  *
              FROM `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE."`
              WHERE 
              `ENABLE`='0'
              AND `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI."`!='vide'
              ORDER BY `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI."`";
              $res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
              $tab_rq_info_ressources_table = mysql_fetch_assoc($res_rq_info_config_table);
              $total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);
              if($action!='Ajout'){
              $rq_info_config_table_info="
              SELECT  `CHANGEMENT_RESSOURCES_INFO_AUTRE_ID`
              FROM `changement_ressources`
              WHERE 
              `CHANGEMENT_LISTE_ID`='".$ID."'
              AND `CHANGEMENT_RESSOURCES_CONFIG_ID`='".$CHANGEMENT_RESSOURCES_CONFIG_ID."'
              AND `ENABLE`='0'
              LIMIT 1";
              //echo $rq_info_config_table_info.'</BR>';
              $res_rq_info_config_table_info = mysql_query($rq_info_config_table_info, $mysql_link) or die(mysql_error());
              $tab_rq_info_ressources_table_info = mysql_fetch_assoc($res_rq_info_config_table_info);
              $total_ligne_rq_info_config_table_info=mysql_num_rows($res_rq_info_config_table_info);
              if($total_ligne_rq_info_config_table_info!=0){
                $CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]=$tab_rq_info_ressources_table_info['CHANGEMENT_RESSOURCES_INFO_AUTRE_ID'];
              }else{
                $CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]='';
              }
              mysql_free_result($res_rq_info_config_table_info);
        	}
        	if($_GET['action']=="Info"){
        		if($total_ligne_rq_info_config_table!=0){
                do {
                	$ID_SQL=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE).'_ID';
                  $ID_LIB=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
                  $CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID=$tab_rq_info_ressources_table[$ID_SQL];
                  $CHANGEMENT_RESSOURCES_CONFIG_TABLE_LIB=$tab_rq_info_ressources_table[$ID_LIB];
                  if($CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID == $CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID] ){echo $CHANGEMENT_RESSOURCES_CONFIG_TABLE_LIB;}
                	
                	} while ($tab_rq_info_ressources_table = mysql_fetch_assoc($res_rq_info_config_table));
                $ligne= mysql_num_rows($res_rq_info_config_table);
                if($ligne > 0) {
                  mysql_data_seek($res_rq_info_config_table, 0);
                  $tab_rq_info_ressources_table = mysql_fetch_assoc($res_rq_info_config_table);
                }
                }
        	}else{
              echo '
              <Select name="CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'" size="1" id="CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'" onChange="">
              <option value="0">&nbsp;</option>';
              if($total_ligne_rq_info_config_table!=0){
                do {
                  $ID_SQL=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE).'_ID';
                  $ID_LIB=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
                  $CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID=$tab_rq_info_ressources_table[$ID_SQL];
                  $CHANGEMENT_RESSOURCES_CONFIG_TABLE_LIB=$tab_rq_info_ressources_table[$ID_LIB];
                  echo '<option value="'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'"';
                  if(!isset($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID])){
                    $CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]='';
                  }
                  if (empty($_POST)){}else{
                    if ($_POST['btn']=="Enregistrer"){
                      if($CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID == $CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID] ){echo " SELECTED ";}
                    }
                  }
                  if (empty($_GET)){}else{
                    if(isset($_GET['action'])){
                      if($_GET['action']=="Modif"){
                        if($CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID == $CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID] ){echo " SELECTED ";}
                      }
                      if($_GET['action']=="Info"){
                        if($CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID == $CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID] ){echo " SELECTED ";}
                      }
                    }
                  }
                  echo '>'.stripslashes($CHANGEMENT_RESSOURCES_CONFIG_TABLE_LIB).'</option>';
                
                } while ($tab_rq_info_ressources_table = mysql_fetch_assoc($res_rq_info_config_table));
                $ligne= mysql_num_rows($res_rq_info_config_table);
                if($ligne > 0) {
                  mysql_data_seek($res_rq_info_config_table, 0);
                  $tab_rq_info_ressources_table = mysql_fetch_assoc($res_rq_info_config_table);
                }
              }
              echo '
              </Select>';
        	}
              mysql_free_result($res_rq_info_config_table);
              if($AFF_SPAN_AIDE!=''){
              	echo '&nbsp;<a href="#" class="infobullegauche"><font color=#000000>&nbsp;- Aide&nbsp;</font><span>'.$AFF_SPAN_AIDE.'</span></a>';
              }
              
            break;
            case "checkbox": 
              $AFF_SPAN_AIDE='';
              $CHANGEMENT_RESSOURCES_CONFIG_TABLE=$tab_rq_info_ressources['CHANGEMENT_RESSOURCES_CONFIG_TABLE'];
              $CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
              $rq_info_config_table="
              SELECT  *
              FROM `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE."`
              WHERE 
              `ENABLE`='0'
              AND `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI."`!='vide'
              
              ORDER BY `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI."`";
              $res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
              $tab_rq_info_ressources_table = mysql_fetch_assoc($res_rq_info_config_table);
              $total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);

              if($total_ligne_rq_info_config_table!=0){
              	$NB_total_ligne_rq_info_config_table=0;
                do {
                  $ID_SQL=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE).'_ID';
                  $ID_LIB=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
                  $COM_SQL=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE).'_COM';
                  $CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID=$tab_rq_info_ressources_table[$ID_SQL];
                  $CHANGEMENT_RESSOURCES_CONFIG_TABLE_LIB=$tab_rq_info_ressources_table[$ID_LIB];
                  $CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID;
                  	$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_COM';
                  if($action!='Ajout'){

                  	$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]='off';
                  	$rq_info_config_liste_id="
                  	SELECT `CHANGEMENT_RESSOURCES_INFO_AUTRE_ID`,`CHANGEMENT_RESSOURCES_VALEUR`
			FROM `changement_ressources` 
			WHERE 
			`CHANGEMENT_LISTE_ID` = '".$ID."'
			AND `CHANGEMENT_RESSOURCES_CONFIG_ID` = '".$CHANGEMENT_RESSOURCES_CONFIG_ID."'
			AND `CHANGEMENT_RESSOURCES_INFO_AUTRE_ID`='".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID."'
			AND `ENABLE` = '0'
			LIMIT 1";
			$res_rq_info_config_liste_id = mysql_query($rq_info_config_liste_id, $mysql_link) or die(mysql_error());
			$tab_rq_info_ressources_liste_id = mysql_fetch_assoc($res_rq_info_config_liste_id);
			$total_ligne_rq_info_config_liste_id=mysql_num_rows($res_rq_info_config_liste_id);
			
			if($total_ligne_rq_info_config_liste_id==0){
				$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]='off';
				$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]='';
			}else{
				$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]='on';
				$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]=$tab_rq_info_ressources_liste_id['CHANGEMENT_RESSOURCES_VALEUR'];
			}
			mysql_free_result($res_rq_info_config_liste_id);
                  }
                  if(!isset($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM])){
                  	$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]='';
                  }
                  if(!isset($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE])){
                  	$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]='';
                  }
                  if(isset($tab_rq_info_ressources_table[$COM_SQL])){
                  	$CHANGEMENT_RESSOURCES_CONFIG_TABLE_COM=$tab_rq_info_ressources_table[$COM_SQL];	
                  }else{
                  	$CHANGEMENT_RESSOURCES_CONFIG_TABLE_COM='non';
                  }
                  
                  $NB_total_ligne_rq_info_config_table++;
                  echo '<INPUT TYPE="CHECKBOX" '.$disabled_var.' NAME="CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'"';
                  if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]=='on'){echo ' checked';}
                  echo '>'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_LIB.'';
                  if($CHANGEMENT_RESSOURCES_CONFIG_TABLE_COM=='oui'){
                  	if($action!='Info'){
                  		echo '&nbsp;&nbsp;<input '.$readonly_var.' id="CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_COM" name="CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_COM" type="text" value="'.stripslashes($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]).'" size="80" maxlength="150"/>&nbsp;*';
                	}else{
                		echo '&nbsp;:&nbsp;'.stripslashes($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]);
                	}
                  }
                  

                  	if($NB_total_ligne_rq_info_config_table < $total_ligne_rq_info_config_table){
                  		echo '</BR>';	
                  	}
    
                
                } while ($tab_rq_info_ressources_table = mysql_fetch_assoc($res_rq_info_config_table));
                $ligne= mysql_num_rows($res_rq_info_config_table);
                if($ligne > 0) {
                  mysql_data_seek($res_rq_info_config_table, 0);
                  $tab_rq_info_ressources_table = mysql_fetch_assoc($res_rq_info_config_table);
                }
              }
              mysql_free_result($res_rq_info_config_table);
              $rq_info_config_table="
              SELECT  *
              FROM `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE."`
              WHERE 
              `ENABLE`='0'
              AND `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI."`='vide'
              ORDER BY `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI."`";
              $res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
              $tab_rq_info_ressources_table = mysql_fetch_assoc($res_rq_info_config_table);
              $total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);

              if($total_ligne_rq_info_config_table!=0){
                do {
                  $ID_SQL=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE).'_ID';
                  $ID_LIB=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
                  $CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID=$tab_rq_info_ressources_table[$ID_SQL];
                  echo '<input type="hidden" name="CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'" value="on">';
                
                } while ($tab_rq_info_ressources_table = mysql_fetch_assoc($res_rq_info_config_table));
                $ligne= mysql_num_rows($res_rq_info_config_table);
                if($ligne > 0) {
                  mysql_data_seek($res_rq_info_config_table, 0);
                  $tab_rq_info_ressources_table = mysql_fetch_assoc($res_rq_info_config_table);
                }
              }
              mysql_free_result($res_rq_info_config_table);
              if($AFF_SPAN_AIDE!=''){
              	echo '&nbsp;<a href="#" class="infobullegauche"><font color=#000000>&nbsp;- Aide&nbsp;</font><span>'.$AFF_SPAN_AIDE.'</span></a>';
              }
              
            break;
            case "text": 
            $AFF_SPAN_AIDE='';
            if($AFF_SPAN_AIDE!=''){
              	echo '&nbsp;<a href="#" class="infobullegauche"><font color=#000000>Aide&nbsp;</font><span>'.$AFF_SPAN_AIDE.'</span></a></BR>';
            }
            if($action!='Info'){
              echo '<textarea '.$readonly_var.' id="CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'" name="CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'" cols="70" rows="5">'.stripslashes($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]).'</textarea>';
        	}else{
        	echo nl2br(stripslashes($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]));
        	}
            
            break;
            case "varchar": 
            $AFF_SPAN_AIDE='';
            if($action!='Info'){
              echo '<input '.$readonly_var.' id="CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'" name="CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'" type="text" value="'.stripslashes($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]).'" size="67" maxlength="100"/>';
            }else{
        	echo stripslashes($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]);
	    }
	    if($AFF_SPAN_AIDE!=''){
              	echo '&nbsp;<a href="#" class="infobullegauche"><font color=#000000>&nbsp;- Aide&nbsp;</font><span>'.$AFF_SPAN_AIDE.'</span></a>';
            }
            break;
            case "liste_acteur": 
            	$k=0;
            	$AFF_SPAN_AIDE='';
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE=$tab_rq_info_ressources['CHANGEMENT_RESSOURCES_CONFIG_TABLE'];
		$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
		$rq_info_config_table="
		SELECT  *
		FROM `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE."`
		WHERE 
		`ENABLE`='0'
		AND `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI."`!='vide'
		ORDER BY `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI."`";
		$res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
		$tab_rq_info_ressources_table = mysql_fetch_assoc($res_rq_info_config_table);
		$total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);
		
		if($total_ligne_rq_info_config_table!=0){
		echo '
		<table class="table_inc">
		<tr class="titre">
		<td align="center">Type de Ressources</td>
		<td align="center">NB</BR>Pr&eacute;sence</td>
		<td align="center">Info Pr&eacute;sence</td>
		<td align="center">NB</BR>Astreinte</td>
		<td align="center">Info Astreinte</td>
		</tr>';
		do {
		  $ID_SQL=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE).'_ID';
		  $ID_LIB=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
		  $CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID=$tab_rq_info_ressources_table[$ID_SQL];
		  $CHANGEMENT_RESSOURCES_CONFIG_TABLE_LIB=$tab_rq_info_ressources_table[$ID_LIB];
		  $CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID;
		  $CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_COM';
		  $CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_INFO=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_INFO';
		  $CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM_INFO=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_COM_INFO';
		  
		  if($STOP==0){
		  if($action!='Ajout'){
		  	$rq_info_config_liste_id="
		  	SELECT `CHANGEMENT_RESSOURCES_INFO_AUTRE_ID`,`CHANGEMENT_RESSOURCES_VALEUR`,`CHANGEMENT_RESSOURCES_COMMENTAIRE`
			FROM `changement_ressources` 
			WHERE 
			`CHANGEMENT_LISTE_ID` = '".$ID."'
			AND `CHANGEMENT_RESSOURCES_CONFIG_ID` = '".$CHANGEMENT_RESSOURCES_CONFIG_ID."'
			AND `CHANGEMENT_RESSOURCES_INFO_AUTRE_ID`='".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID."'
			AND `ENABLE` = '0'
			LIMIT 1";
			$res_rq_info_config_liste_id = mysql_query($rq_info_config_liste_id, $mysql_link) or die(mysql_error());
			$tab_rq_info_ressources_liste_id = mysql_fetch_assoc($res_rq_info_config_liste_id);
			$total_ligne_rq_info_config_liste_id=mysql_num_rows($res_rq_info_config_liste_id);
			
			if($total_ligne_rq_info_config_liste_id==0){
				$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]='0';
				$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]='0';
				$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_INFO]='';
				$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM_INFO]='';
			}else{
				$VAL1=explode("|",$tab_rq_info_ressources_liste_id['CHANGEMENT_RESSOURCES_VALEUR']);
				$VAL2=explode("|",$tab_rq_info_ressources_liste_id['CHANGEMENT_RESSOURCES_COMMENTAIRE']);
				if(isset($VAL1[0])){
					$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]=$VAL1[0];
				}else{
					$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]='0';
				}
				if(isset($VAL1[1])){
					$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_INFO]=$VAL1[1];
				}else{
					$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_INFO]='';
				}
				if(isset($VAL2[0])){
					$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]=$VAL2[0];
				}else{
					$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]='0';
				}
				if(isset($VAL2[1])){
					$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM_INFO]=$VAL2[1];
				}else{
					$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM_INFO]='';
				}
			}
			mysql_free_result($res_rq_info_config_liste_id);
		  }
		  }
		  
		  if(!isset($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE])){
		  	$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]='0';
		  }
		  if(!isset($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM])){
		  	$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]='0';
		  }
		  if(!isset($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_INFO])){
		  	$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_INFO]='';
		  }
		  if(!isset($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM_INFO])){
		  	$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM_INFO]='';
		  }
		  $k++;
		  if ($k%2) { $class = "pair";}else{$class = "impair";} 
		  echo '
		  	<tr class="'.$class.'">
			<td align="center">'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_LIB.'</td>
			<td align="center">';
			if($action!='Info'){
				echo '<input '.$readonly_var.' id="CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE.'" name="CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE.'" type="text" value="'.stripslashes($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]).'" size="2" maxlength="4"/>';
			}else{
				echo stripslashes($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]);
			}
	    		echo '
			</td>
			<td align="center">';
			if($action!='Info'){
				echo '<input '.$readonly_var.' id="CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_INFO.'" name="CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_INFO.'" type="text" value="'.stripslashes($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_INFO]).'" size="30" maxlength="150"/>';
			}else{
				echo stripslashes($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_INFO]);
			}
	    		echo '
			</td>
			<td align="center">';
			if($action!='Info'){
				echo '<input '.$readonly_var.' id="CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM.'" name="CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM.'" type="text" value="'.stripslashes($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]).'" size="2" maxlength="4"/>';
			}else{
				echo stripslashes($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]);
			}
	    		echo '			
			</td>
			<td align="center">';
			if($action!='Info'){
				echo '<input '.$readonly_var.' id="CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM_INFO.'" name="CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM_INFO.'" type="text" value="'.stripslashes($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM_INFO]).'" size="30" maxlength="150"/>';
			}else{
				echo stripslashes($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM_INFO]);
			}
	    		echo '
			</td>
			</tr>';
			
		} while ($tab_rq_info_ressources_table = mysql_fetch_assoc($res_rq_info_config_table));
		$ligne= mysql_num_rows($res_rq_info_config_table);
		if($ligne > 0) {
		  mysql_data_seek($res_rq_info_config_table, 0);
		  $tab_rq_info_ressources_table = mysql_fetch_assoc($res_rq_info_config_table);
		}
		}
            echo '
		<tr class="titre">
		<td align="center" colspan="5">&nbsp;</td>
		</tr>';
            echo '</table>';
            break;

            case "checkbox_horizontal": 
            $AFF_SPAN_AIDE='';

              $CHANGEMENT_RESSOURCES_CONFIG_TABLE=$tab_rq_info_ressources['CHANGEMENT_RESSOURCES_CONFIG_TABLE'];
              $CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
              $rq_info_config_table="
              SELECT  *
              FROM `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE."`
              WHERE 
              `ENABLE`='0'
              AND `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI."`!='vide'
              
              ORDER BY `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI."`";
              $res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
              $tab_rq_info_ressources_table = mysql_fetch_assoc($res_rq_info_config_table);
              $total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);

              if($total_ligne_rq_info_config_table!=0){
                do {
                  $ID_SQL=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE).'_ID';
                  $ID_LIB=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
                  $COM_SQL=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE).'_COM';
                  $CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID=$tab_rq_info_ressources_table[$ID_SQL];
                  $CHANGEMENT_RESSOURCES_CONFIG_TABLE_LIB=$tab_rq_info_ressources_table[$ID_LIB];
                  $CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID;
                  	$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_COM';
                  if($action!='Ajout'){

                  	$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]='off';
                  	$rq_info_config_liste_id="
                  	SELECT `CHANGEMENT_RESSOURCES_INFO_AUTRE_ID`,`CHANGEMENT_RESSOURCES_VALEUR`
			FROM `changement_ressources` 
			WHERE 
			`CHANGEMENT_LISTE_ID` = '".$ID."'
			AND `CHANGEMENT_RESSOURCES_CONFIG_ID` = '".$CHANGEMENT_RESSOURCES_CONFIG_ID."'
			AND `CHANGEMENT_RESSOURCES_INFO_AUTRE_ID`='".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID."'
			AND `ENABLE` = '0'
			LIMIT 1";
			$res_rq_info_config_liste_id = mysql_query($rq_info_config_liste_id, $mysql_link) or die(mysql_error());
			$tab_rq_info_ressources_liste_id = mysql_fetch_assoc($res_rq_info_config_liste_id);
			$total_ligne_rq_info_config_liste_id=mysql_num_rows($res_rq_info_config_liste_id);
			
			if($total_ligne_rq_info_config_liste_id==0){
				$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]='off';
				$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]='';
			}else{
				$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]='on';
				$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]=$tab_rq_info_ressources_liste_id['CHANGEMENT_RESSOURCES_VALEUR'];
			}
			mysql_free_result($res_rq_info_config_liste_id);
                  }
                  if(!isset($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM])){
                  	$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM]='';
                  }
                  if(!isset($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE])){
                  	$CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]='';
                  }

                  
                  if(isset($tab_rq_info_ressources_table[$COM_SQL])){
                  	$CHANGEMENT_RESSOURCES_CONFIG_TABLE_COM=$tab_rq_info_ressources_table[$COM_SQL];	
                  }else{
                  	$CHANGEMENT_RESSOURCES_CONFIG_TABLE_COM='non';
                  }

                  echo '<INPUT TYPE="CHECKBOX" '.$disabled_var.' NAME="CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'"';
                  if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]=='on'){echo ' checked';}
                  echo '>'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_LIB.'&nbsp;&nbsp;';
                  if($CHANGEMENT_RESSOURCES_CONFIG_TABLE_COM=='oui'){
                  	echo '<input '.$readonly_var.' id="CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_COM" name="CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_COM" type="hidden" value="vide" size="50" maxlength="100"/>';
                  }
                
                } while ($tab_rq_info_ressources_table = mysql_fetch_assoc($res_rq_info_config_table));
                $ligne= mysql_num_rows($res_rq_info_config_table);
                if($ligne > 0) {
                  mysql_data_seek($res_rq_info_config_table, 0);
                  $tab_rq_info_ressources_table = mysql_fetch_assoc($res_rq_info_config_table);
                }
              }
              mysql_free_result($res_rq_info_config_table);
              $rq_info_config_table="
              SELECT  *
              FROM `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE."`
              WHERE 
              `ENABLE`='0'
              AND `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI."`='vide'
              ORDER BY `".$CHANGEMENT_RESSOURCES_CONFIG_TABLE_TRI."`";
              $res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
              $tab_rq_info_ressources_table = mysql_fetch_assoc($res_rq_info_config_table);
              $total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);

              if($total_ligne_rq_info_config_table!=0){
                do {
                  $ID_SQL=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE).'_ID';
                  $ID_LIB=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
                  $CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID=$tab_rq_info_ressources_table[$ID_SQL];
                  echo '<input type="hidden" name="CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'" value="on">';
                
                } while ($tab_rq_info_ressources_table = mysql_fetch_assoc($res_rq_info_config_table));
                $ligne= mysql_num_rows($res_rq_info_config_table);
                if($ligne > 0) {
                  mysql_data_seek($res_rq_info_config_table, 0);
                  $tab_rq_info_ressources_table = mysql_fetch_assoc($res_rq_info_config_table);
                }
              }
              mysql_free_result($res_rq_info_config_table);
              if($AFF_SPAN_AIDE!=''){
              	echo '&nbsp;<a href="#" class="infobullegauche"><font color=#000000>&nbsp;- Aide&nbsp;</font><span>'.$AFF_SPAN_AIDE.'</span></a>';
              }
              
            break;
            
            }
		     echo '
		     </td>
		   </tr>';
	        	
	        } while ($tab_rq_info_ressources = mysql_fetch_assoc($res_rq_info_ressources));
	        $ligne= mysql_num_rows($res_rq_info_ressources);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_ressources, 0);
	          $tab_rq_info_ressources = mysql_fetch_assoc($res_rq_info_ressources);
	        }
	        mysql_free_result($res_rq_info_ressources);
	}

        	
        } while ($tab_rq_info_ressources_lib = mysql_fetch_assoc($res_rq_info_ressources_lib));
        $ligne= mysql_num_rows($res_rq_info_ressources_lib);
        if($ligne > 0) {
          mysql_data_seek($res_rq_info_ressources_lib, 0);
          $tab_rq_info_ressources_lib = mysql_fetch_assoc($res_rq_info_ressources_lib);
        }
        mysql_free_result($res_rq_info_ressources_lib);
      }      

if($STOP!=0){
   	$j++;
	   if ($j%2) { $class = "pair";}else{$class = "impair";} 
	   echo '
	   <tr class="'.$class.'">
	    <td align="center" colspan="4"><font color=#993333><b>Merci de saisir l\'ensemble des champs obligatoire.</b></font>
	    </td>
	   </tr>';
   }
   echo '
   <tr class="titre">
	<td colspan="2" align="center">
	<h2>';
	if($action=='Ajout'){
	echo '
	<input name="btn" type="submit" id="btn" value="Enregistrer">
	<input name="btn" type="submit" id="btn" value="Inscription">	
	<input type="hidden" name="ID" value="'.$ID.'">
	<input type="hidden" name="action" value="'.$action.'">
	';
	}
	if($action=='Modif'){
		echo '
		<input type="hidden" name="ID" value="'.$ID.'">
		<input type="hidden" name="action" value="'.$action.'">';
		if($CHANGEMENT_STATUS=='Brouillon'){
			echo '
			<input name="btn" type="submit" id="btn" value="Enregistrer">
			<input name="btn" type="submit" id="btn" value="Inscription">	
			';
		}else{
			if($ROLE==0){
				echo '<input name="btn" type="submit" id="btn" value="Enregistrer">';
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
	</tr>
</table>
</form>
</div>
';

mysql_close($mysql_link); 
?>