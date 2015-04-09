<?PHP
//redirection si acces dirrect
if(substr_count($_SERVER['PHP_SELF'], 'index.php')==0){
  header("Location: ../"); 
  exit();
}
/*******************************************************************
   Interface changement action
   Version 1.0.0 
  29/09/2010 - VGU - Creation fichier
**************************************************Guibert Vincent***
*******************************************************************/

require("./cf/conf_outil_icdc.php"); 
require_once("./cf/fonctions.php");
require_once("./cf/autre_fonctions.php");

require_once('./changement/changement_Conf_Mail.php') ; 

$ENV =substr($_SERVER["SCRIPT_FILENAME"], 1, 1);
//$ENV='x';
$j=0;
$HEURE_PLANIF_FIN_LISTE=0;
$HEURE_PLANIF_DEBUT_LISTE=0;
$DATE_LISTE=0;
$HEURE_LISTE=0;
$HEURE_PLANIF_DEBUT_H='';
$HEURE_PLANIF_DEBUT_M='';
$HEURE_PLANIF_FIN_H='';
$HEURE_PLANIF_FIN_M='';
$Date_Inter_Fin='';
$Bilan_lib='';
$Bilan_action='';
$Bilan_ITEM='';
$CR_lib='';
$CR_action='';
$CR_ITEM='';
$FAR_action='';
$FAR_lib='';
$FAR_ITEM='';
$AFF_SPAN_type='';
$lib='';
$STOP_lib=0;
$STOP_type=0;
$STOP=0;
$STOP_pb=0;
$STOP_insert=0;
$CHANGEMENT_STATUS='';
$DATE_MODIFICATION=date("d/m/Y H:i:s");
$DATE_DU_JOUR=date("Ymd");
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
    $_GET['ID']=$ID;
  }
}
if(isset($tab_var['action'])){
    $action=$tab_var['action'];
    $_GET['action']=$action;
}
if($action=='Info'){
	$readonly_var='readonly="readonly"';
	$disabled_var='DISABLED';
}else{
	$readonly_var='';
	$disabled_var='';
}
$rq_Selectionner_demande_type ="
SELECT `CHANGEMENT_DEMANDE_ID`,`CHANGEMENT_DEMANDE_LIB`, `CHANGEMENT_DEMANDE_EXEMPLE` FROM `changement_demande` ORDER BY `CHANGEMENT_DEMANDE_LIB`";
$res_rq_Selectionner_demande_type = mysql_query($rq_Selectionner_demande_type, $mysql_link) or die(mysql_error());
$tab_rq_Selectionner_demande_type = mysql_fetch_assoc($res_rq_Selectionner_demande_type);
$total_ligne_rq_Selectionner_demande_type = mysql_num_rows($res_rq_Selectionner_demande_type);

if(isset($_SESSION['LOGIN'])){
	$LOGIN=$_SESSION['LOGIN'];
	$rq_Selectionner_user ="
	SELECT `UTILISATEUR_ID`,`LOGIN`, `NOM`, `PRENOM` 
	FROM `moteur_utilisateur` 
	WHERE `LOGIN` = '".$LOGIN."'";
	$res_rq_Selectionner_user = mysql_query($rq_Selectionner_user, $mysql_link) or die(mysql_error());
	$tab_rq_Selectionner_user = mysql_fetch_assoc($res_rq_Selectionner_user);
	$total_ligne_rq_Selectionner_demande_type = mysql_num_rows($res_rq_Selectionner_user);
	if($total_ligne_rq_Selectionner_demande_type==0){
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
	(`moteur_role`.`ROLE`='ROOT' OR `moteur_role`.`ROLE`='ADMIN-CHANGEMENT') AND
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

if(empty($tab_var['btn'])){
}else{

  # Cas Ajouter
  if($tab_var['btn']=="Ajouter"){

    $Date_Inter_Debut=addslashes(trim(htmlentities($tab_var['txt_Date_Inter_Debut'])));
    $HEURE_PLANIF_DEBUT_H=addslashes(trim(htmlentities($tab_var['txt_HEURE_PLANIF_DEBUT_H'])));
    $HEURE_PLANIF_DEBUT_M=addslashes(trim(htmlentities($tab_var['txt_HEURE_PLANIF_DEBUT_M'])));
    $Date_Inter_Fin=addslashes(trim(htmlentities($tab_var['txt_Date_Inter_Fin'])));
    $HEURE_PLANIF_FIN_H=addslashes(trim(htmlentities($tab_var['txt_HEURE_PLANIF_FIN_H'])));
    $HEURE_PLANIF_FIN_M=addslashes(trim(htmlentities($tab_var['txt_HEURE_PLANIF_FIN_M'])));
    $CHANGEMENT_DEMANDE_ID=addslashes(trim(htmlentities($tab_var['CHANGEMENT_DEMANDE_ID'])));
    $lib=substr(addslashes(trim(htmlentities($tab_var['txt_lib']))),0,100);
    
    if(strlen($HEURE_PLANIF_DEBUT_H) == 1 ){
    	$HEURE_PLANIF_DEBUT_H='0'.$HEURE_PLANIF_DEBUT_H;
    }
    if(strlen($HEURE_PLANIF_DEBUT_M) == 1 ){
    	$HEURE_PLANIF_DEBUT_M='0'.$HEURE_PLANIF_DEBUT_M;
    }
    if(strlen($HEURE_PLANIF_FIN_H) == 1 ){
    	$HEURE_PLANIF_FIN_H='0'.$HEURE_PLANIF_FIN_H;
    }
    if(strlen($HEURE_PLANIF_FIN_M) == 1 ){
    	$HEURE_PLANIF_FIN_M='0'.$HEURE_PLANIF_FIN_M;
    }
// test des heures
    $HEURE_PLANIF_DEBUT=$HEURE_PLANIF_DEBUT_H.'h'.$HEURE_PLANIF_DEBUT_M;
    $HEURE_PLANIF_DEBUT_info=$HEURE_PLANIF_DEBUT_H.''.$HEURE_PLANIF_DEBUT_M;
    if($HEURE_PLANIF_DEBUT_H!=''){
      if(is_numeric($HEURE_PLANIF_DEBUT_H)){
        if($HEURE_PLANIF_DEBUT_H < 0 OR $HEURE_PLANIF_DEBUT_H >= 24){
          $STOP=1;
          $HEURE_PLANIF_DEBUT_LISTE=1;
        }
      }else{
        $STOP=1;
        $HEURE_PLANIF_DEBUT_LISTE=1;
      }
      if(is_numeric($HEURE_PLANIF_DEBUT_M)){
        if($HEURE_PLANIF_DEBUT_M < 0 OR $HEURE_PLANIF_DEBUT_M >= 60){
          $STOP=1;
          $HEURE_PLANIF_DEBUT_LISTE=1;
        }
      }else{
        $STOP=1;
        $HEURE_PLANIF_DEBUT_LISTE=1;
      }
    }

    $HEURE_PLANIF_FIN=$HEURE_PLANIF_FIN_H.'h'.$HEURE_PLANIF_FIN_M;
    $HEURE_PLANIF_FIN_info=$HEURE_PLANIF_FIN_H.''.$HEURE_PLANIF_FIN_M;
    if($HEURE_PLANIF_FIN_H!=''){
      if(is_numeric($HEURE_PLANIF_FIN_H)){
        if($HEURE_PLANIF_FIN_H < 0 OR $HEURE_PLANIF_FIN_H >= 24){
          $STOP=1;
          $HEURE_PLANIF_FIN_LISTE=1;
        }
      }else{
        $STOP=1;
        $HEURE_PLANIF_FIN_LISTE=1;
      }
      if(is_numeric($HEURE_PLANIF_FIN_M)){
        if($HEURE_PLANIF_FIN_M < 0 OR $HEURE_PLANIF_FIN_M >= 60){
          $STOP=1;
          $HEURE_PLANIF_FIN_LISTE=1;
        }
      }else{
        $STOP=1;
        $HEURE_PLANIF_FIN_LISTE=1;
      }
    }
// test des jours    
    $jour='';
    $mois='';
    $annee='';
    $jour=substr($Date_Inter_Debut,0,2);
    $mois=substr($Date_Inter_Debut,3,2);
    $annee=substr($Date_Inter_Debut,6,4);
    $Date_Inter_Debut_bdd=$annee.''.$mois.''.$jour;
    $jour='';
    $mois='';
    $annee='';
    $jour=substr($Date_Inter_Fin,0,2);
    $mois=substr($Date_Inter_Fin,3,2);
    $annee=substr($Date_Inter_Fin,6,4);
    $Date_Inter_Fin_bdd=$annee.''.$mois.''.$jour;
    if($Date_Inter_Fin_bdd < $Date_Inter_Debut_bdd){
      $STOP=1;
      $DATE_LISTE=1;
    }
    if($Date_Inter_Debut_bdd == $Date_Inter_Fin_bdd){
    	if($HEURE_PLANIF_DEBUT_info >= $HEURE_PLANIF_FIN_info){
    		$STOP=1;
    		$HEURE_LISTE=1;
    	}
    }
    if ( $ENV == "x" ){	 	
      if($Date_Inter_Debut_bdd < $DATE_DU_JOUR){
        $STOP=1;
        $DATE_LISTE=2;
      }
	  }
    
// test libelle
    if($lib==''){
    	$STOP=1;
    	$STOP_lib=1;
   }
// test type demande
    if($CHANGEMENT_DEMANDE_ID==0){
    	$STOP=1;
    	$STOP_type=1;
   }
   
   
///
   $rq_info_config="
	 SELECT  `CHANGEMENT_LISTE_CONFIG_ID`,
	 `CHANGEMENT_LISTE_CONFIG_LIB`, 
	 `CHANGEMENT_LISTE_CONFIG_TYPE`,
	 `CHANGEMENT_LISTE_CONFIG_TABLE`,
	 `CHANGEMENT_LISTE_CONFIG_OBLIGATOIRE` 
	 FROM `changement_liste_config` 
	 WHERE `ENABLE`='0'";
	$res_rq_info_config = mysql_query($rq_info_config, $mysql_link) or die(mysql_error());
	$tab_rq_info_config = mysql_fetch_assoc($res_rq_info_config);
	$total_ligne_rq_info_config=mysql_num_rows($res_rq_info_config);
	if($total_ligne_rq_info_config!=0){
		do {
		$CHANGEMENT_LISTE_CONFIG_ID=$tab_rq_info_config['CHANGEMENT_LISTE_CONFIG_ID'];
		$CHANGEMENT_LISTE_CONFIG_LIB=$tab_rq_info_config['CHANGEMENT_LISTE_CONFIG_LIB'];
		$CHANGEMENT_LISTE_CONFIG_TYPE=$tab_rq_info_config['CHANGEMENT_LISTE_CONFIG_TYPE'];
		$CHANGEMENT_LISTE_CONFIG_OBLIGATOIRE=$tab_rq_info_config['CHANGEMENT_LISTE_CONFIG_OBLIGATOIRE'];
		if(isset($tab_rq_info_config['CHANGEMENT_LISTE_INFO_AUTRE_ID'])){
      $CHANGEMENT_LISTE_INFO_AUTRE_ID=$tab_rq_info_config['CHANGEMENT_LISTE_INFO_AUTRE_ID'];
		}
		if(isset($tab_rq_info_config['CHANGEMENT_LISTE_INFO_LIB'])){
      $CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID]=$tab_rq_info_config['CHANGEMENT_LISTE_INFO_LIB'];
		}
		if(!isset($CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID])){$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID]='';}
		$LISTE_CONFIG='CHANGEMENT_LISTE_CONFIG_ID_'.$CHANGEMENT_LISTE_CONFIG_ID;
		
		      switch ($CHANGEMENT_LISTE_CONFIG_TYPE)
            {
            case "liste": 
              $CHANGEMENT_LISTE_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_LISTE_CONFIG_TABLE'];
              $CHANGEMENT_LISTE_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE);
              $LISTE_CONFIG='CHANGEMENT_LISTE_CONFIG_ID_'.$CHANGEMENT_LISTE_CONFIG_ID;
              $CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
              $CHANGEMENT_LISTE_CONFIG_STOP[$CHANGEMENT_LISTE_CONFIG_ID]=0;
              if($CHANGEMENT_LISTE_CONFIG_OBLIGATOIRE=='oui'){
                if($CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID]=='0'){
                  $CHANGEMENT_LISTE_CONFIG_STOP[$CHANGEMENT_LISTE_CONFIG_ID]=1;
                  $STOP=1;
                }
              }
            break;
            
            case "checkbox_horizontal": 
              $CHANGEMENT_LISTE_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_LISTE_CONFIG_TABLE'];
              $CHANGEMENT_LISTE_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE);
              $CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID]='0';
              $rq_info_config_table="
              SELECT  *
              FROM `".$CHANGEMENT_LISTE_CONFIG_TABLE."`
              WHERE 
              `ENABLE`='0'
              AND `".$CHANGEMENT_LISTE_CONFIG_TABLE_TRI."`!='vide'
              ORDER BY `".$CHANGEMENT_LISTE_CONFIG_TABLE_TRI."`";
              $res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
              $tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
              $total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);
              if($total_ligne_rq_info_config_table!=0){
                do {
                  $ID_SQL=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE).'_ID';
                  $ID_LIB=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE);
                  $COM_SQL=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE).'_COM';
                  $CHANGEMENT_LISTE_CONFIG_TABLE_ID=$tab_rq_info_config_table[$ID_SQL];
                  $CHANGEMENT_LISTE_CONFIG_TABLE_LIB=$tab_rq_info_config_table[$ID_LIB];
                  if(isset($tab_rq_info_config_table[$COM_SQL])){
                  	$CHANGEMENT_LISTE_CONFIG_TABLE_COM=$tab_rq_info_config_table[$COM_SQL];	
                  }else{
                  	$CHANGEMENT_LISTE_CONFIG_TABLE_COM='non';
                  }
	              $LISTE_CONFIG='CHANGEMENT_LISTE_CONFIG_ID_'.$CHANGEMENT_LISTE_CONFIG_ID.'_'.$CHANGEMENT_LISTE_CONFIG_TABLE_ID;
	              $CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE=$CHANGEMENT_LISTE_CONFIG_ID.'_'.$CHANGEMENT_LISTE_CONFIG_TABLE_ID;
	              if(!isset($NB_checkbox)){$NB_checkbox=0;}
	              if(isset($tab_var[$LISTE_CONFIG])){
	              	$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
	              	$NB_checkbox=$NB_checkbox+1;
	              }else{
	              	$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE]='off';
	              }
	              
	              if($CHANGEMENT_LISTE_CONFIG_TABLE_COM=='oui'){
		              $LISTE_CONFIG='CHANGEMENT_LISTE_CONFIG_ID_'.$CHANGEMENT_LISTE_CONFIG_ID.'_'.$CHANGEMENT_LISTE_CONFIG_TABLE_ID.'_COM';
		              $CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM=$CHANGEMENT_LISTE_CONFIG_ID.'_'.$CHANGEMENT_LISTE_CONFIG_TABLE_ID.'_COM';
		              if(isset($tab_var[$LISTE_CONFIG])){
		              	$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
		              	
		              }else{
		              	$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM]='';
		              }
		              if($CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE]=='on'){
		              		if($CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM]==''){
		              			$STOP=1;	
		              			$CHANGEMENT_LISTE_CONFIG_STOP_COM[$CHANGEMENT_LISTE_CONFIG_ID]=1;
		              		}
		              }
	              }
	              
                
                } while ($tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table));
                $ligne= mysql_num_rows($res_rq_info_config_table);
                if($ligne > 0) {
                  mysql_data_seek($res_rq_info_config_table, 0);
                  $tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
                }
                $CHANGEMENT_LISTE_CONFIG_STOP[$CHANGEMENT_LISTE_CONFIG_ID]=0;
                if($CHANGEMENT_LISTE_CONFIG_OBLIGATOIRE=='oui'){
			if($NB_checkbox==0){
				$CHANGEMENT_LISTE_CONFIG_STOP[$CHANGEMENT_LISTE_CONFIG_ID]=1;
				$STOP=1;
			}
		}
              }
              mysql_free_result($res_rq_info_config_table);
            break;
            case "checkbox": 
              $CHANGEMENT_LISTE_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_LISTE_CONFIG_TABLE'];
              $CHANGEMENT_LISTE_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE);
              $CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID]='0';
              $rq_info_config_table="
              SELECT  *
              FROM `".$CHANGEMENT_LISTE_CONFIG_TABLE."`
              WHERE 
              `ENABLE`='0'
              AND `".$CHANGEMENT_LISTE_CONFIG_TABLE_TRI."`!='vide'
              ORDER BY `".$CHANGEMENT_LISTE_CONFIG_TABLE_TRI."`";
              $res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
              $tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
              $total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);
              if($total_ligne_rq_info_config_table!=0){
                do {
                  $ID_SQL=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE).'_ID';
                  $ID_LIB=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE);
                  $COM_SQL=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE).'_COM';
                  $CHANGEMENT_LISTE_CONFIG_TABLE_ID=$tab_rq_info_config_table[$ID_SQL];
                  $CHANGEMENT_LISTE_CONFIG_TABLE_LIB=$tab_rq_info_config_table[$ID_LIB];
                  if(isset($tab_rq_info_config_table[$COM_SQL])){
                  	$CHANGEMENT_LISTE_CONFIG_TABLE_COM=$tab_rq_info_config_table[$COM_SQL];	
                  }else{
                  	$CHANGEMENT_LISTE_CONFIG_TABLE_COM='non';
                  }
	              $LISTE_CONFIG='CHANGEMENT_LISTE_CONFIG_ID_'.$CHANGEMENT_LISTE_CONFIG_ID.'_'.$CHANGEMENT_LISTE_CONFIG_TABLE_ID;
	              $CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE=$CHANGEMENT_LISTE_CONFIG_ID.'_'.$CHANGEMENT_LISTE_CONFIG_TABLE_ID;
	              if(!isset($NB_checkbox)){$NB_checkbox=0;}
	              if(isset($tab_var[$LISTE_CONFIG])){
	              	$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
	              	$NB_checkbox=$NB_checkbox+1;
	              }else{
	              	$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE]='off';
	              }
	              
	              if($CHANGEMENT_LISTE_CONFIG_TABLE_COM=='oui'){
		              $LISTE_CONFIG='CHANGEMENT_LISTE_CONFIG_ID_'.$CHANGEMENT_LISTE_CONFIG_ID.'_'.$CHANGEMENT_LISTE_CONFIG_TABLE_ID.'_COM';
		              $CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM=$CHANGEMENT_LISTE_CONFIG_ID.'_'.$CHANGEMENT_LISTE_CONFIG_TABLE_ID.'_COM';
		              if(isset($tab_var[$LISTE_CONFIG])){
		              	$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
		              	
		              }else{
		              	$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM]='';
		              }
		              if($CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE]=='on'){
		              		if($CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM]==''){
		              			$STOP=1;	
		              			$CHANGEMENT_LISTE_CONFIG_STOP_COM[$CHANGEMENT_LISTE_CONFIG_ID]=1;
		              		}
		              }
	              }
	              
                
                } while ($tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table));
                $ligne= mysql_num_rows($res_rq_info_config_table);
                if($ligne > 0) {
                  mysql_data_seek($res_rq_info_config_table, 0);
                  $tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
                }
                $CHANGEMENT_LISTE_CONFIG_STOP[$CHANGEMENT_LISTE_CONFIG_ID]=0;
                if($CHANGEMENT_LISTE_CONFIG_OBLIGATOIRE=='oui'){
			if($NB_checkbox==0){
				$CHANGEMENT_LISTE_CONFIG_STOP[$CHANGEMENT_LISTE_CONFIG_ID]=1;
				$STOP=1;
			}
		}
              }
              mysql_free_result($res_rq_info_config_table);
            break;
            
            case "varchar": 
              $CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
              $CHANGEMENT_LISTE_CONFIG_STOP[$CHANGEMENT_LISTE_CONFIG_ID]=0;
              if($CHANGEMENT_LISTE_CONFIG_OBLIGATOIRE=='oui'){
                if($CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID]==''){
                  $CHANGEMENT_LISTE_CONFIG_STOP[$CHANGEMENT_LISTE_CONFIG_ID]=1;
                  $STOP=1;
                }
              }
              break;
              
            case "text": 
              $CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
              $CHANGEMENT_LISTE_CONFIG_STOP[$CHANGEMENT_LISTE_CONFIG_ID]=0;
              if($CHANGEMENT_LISTE_CONFIG_OBLIGATOIRE=='oui'){
                if($CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID]==''){
                  $CHANGEMENT_LISTE_CONFIG_STOP[$CHANGEMENT_LISTE_CONFIG_ID]=1;
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
   
   
   
//   
// recuperation du status

	$rq_info_status="
	SELECT `CHANGEMENT_STATUS_ID` 
	FROM `changement_status` 
	WHERE `CHANGEMENT_STATUS` = 'Brouillon'
	AND `ENABLE`='0'";
	$res_rq_info_status = mysql_query($rq_info_status, $mysql_link) or die(mysql_error());
	$tab_rq_info_status = mysql_fetch_assoc($res_rq_info_status);
	$total_ligne_rq_info_status=mysql_num_rows($res_rq_info_status);
	if($total_ligne_rq_info_status==0){
		$sql="INSERT INTO `changement_status` (`CHANGEMENT_STATUS_ID` ,`CHANGEMENT_STATUS`, `ENABLE` )VALUES (NULL , 'Brouillon', '0');";
        	mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
	        $TABLE_SQL_SQL='changement_status';       
	        historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');
	        $rq_info_status="
		SELECT `CHANGEMENT_STATUS_ID` 
		FROM `changement_status` 
		WHERE `CHANGEMENT_STATUS` = 'Brouillon'
		AND `ENABLE`='0'";
		$res_rq_info_status = mysql_query($rq_info_status, $mysql_link) or die(mysql_error());
		$tab_rq_info_status = mysql_fetch_assoc($res_rq_info_status);
		$total_ligne_rq_info_status=mysql_num_rows($res_rq_info_status);
	}
	$CHANGEMENT_STATUS_ID=$tab_rq_info_status['CHANGEMENT_STATUS_ID'];
	mysql_free_result($res_rq_info_status);   
    if($STOP==0){
    	$sql="INSERT INTO `changement_liste` (`CHANGEMENT_LISTE_ID` ,`CHANGEMENT_LISTE_UTILISATEUR_ID` ,`CHANGEMENT_LISTE_DATE_DEBUT` ,`CHANGEMENT_LISTE_DATE_FIN` ,`CHANGEMENT_LISTE_HEURE_DEBUT` ,`CHANGEMENT_LISTE_HEURE_FIN` ,`CHANGEMENT_LISTE_DATE_MODIFICATION` ,`CHANGEMENT_LISTE_LIB`,`CHANGEMENT_STATUS_ID`,`CHANGEMENT_DEMANDE_ID` ,`ENABLE` )VALUES (NULL , '".$UTILISATEUR_ID."', '".$Date_Inter_Debut_bdd."', '".$Date_Inter_Fin_bdd."', '".$HEURE_PLANIF_DEBUT."', '".$HEURE_PLANIF_FIN."', '".$DATE_MODIFICATION."', '".$lib."','".$CHANGEMENT_STATUS_ID."','".$CHANGEMENT_DEMANDE_ID."', '0');";
        mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
        $TABLE_SQL_SQL='changement_liste';       
        historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');
        
        $sql="OPTIMIZE TABLE `changement_liste` ";
	mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
	
	$TABLE_SQL_SQL='changement_liste';       
	historique_sql_new($sql,$TABLE_SQL_SQL,'OPTIMIZE');
	
  	$rq_info="
	SELECT `CHANGEMENT_LISTE_ID` 
	FROM `changement_liste`
	WHERE `CHANGEMENT_LISTE_UTILISATEUR_ID` ='".$UTILISATEUR_ID."' 
	AND `CHANGEMENT_LISTE_DATE_DEBUT` ='".$Date_Inter_Debut_bdd."' 
	AND `CHANGEMENT_LISTE_DATE_FIN` ='".$Date_Inter_Fin_bdd."' 
	AND `CHANGEMENT_LISTE_HEURE_DEBUT` ='".$HEURE_PLANIF_DEBUT."' 
	AND `CHANGEMENT_LISTE_HEURE_FIN` ='".$HEURE_PLANIF_FIN."' 
	AND `CHANGEMENT_LISTE_LIB` ='".$lib."' 
	AND `ENABLE` ='0'
	LIMIT 1";
	$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
	$tab_rq_info = mysql_fetch_assoc($res_rq_info);
	$total_ligne_rq_info=mysql_num_rows($res_rq_info);
	if($total_ligne_rq_info==1){
	$CHANGEMENT_ID=$tab_rq_info['CHANGEMENT_LISTE_ID'];
	
	   $rq_info_config="
	 SELECT  `CHANGEMENT_LISTE_CONFIG_ID`,`CHANGEMENT_LISTE_CONFIG_LIB`, `CHANGEMENT_LISTE_CONFIG_TYPE`, `CHANGEMENT_LISTE_CONFIG_OBLIGATOIRE`,`CHANGEMENT_LISTE_CONFIG_TABLE`
	 FROM `changement_liste_config` 
	 WHERE `ENABLE`='0'";
	$res_rq_info_config = mysql_query($rq_info_config, $mysql_link) or die(mysql_error());
	$tab_rq_info_config = mysql_fetch_assoc($res_rq_info_config);
	$total_ligne_rq_info_config=mysql_num_rows($res_rq_info_config);
	if($total_ligne_rq_info_config!=0){
		do {
		$CHANGEMENT_LISTE_CONFIG_ID=$tab_rq_info_config['CHANGEMENT_LISTE_CONFIG_ID'];
		$CHANGEMENT_LISTE_CONFIG_LIB=$tab_rq_info_config['CHANGEMENT_LISTE_CONFIG_LIB'];
		$CHANGEMENT_LISTE_CONFIG_TYPE=$tab_rq_info_config['CHANGEMENT_LISTE_CONFIG_TYPE'];
		$CHANGEMENT_LISTE_CONFIG_OBLIGATOIRE=$tab_rq_info_config['CHANGEMENT_LISTE_CONFIG_OBLIGATOIRE'];
		$CHANGEMENT_LISTE_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_LISTE_CONFIG_TABLE'];
		if(isset($tab_rq_info_config['CHANGEMENT_LISTE_INFO_AUTRE_ID'])){
      $CHANGEMENT_LISTE_INFO_AUTRE_ID=$tab_rq_info_config['CHANGEMENT_LISTE_INFO_AUTRE_ID'];
		}
		if(isset($tab_rq_info_config['CHANGEMENT_LISTE_INFO_LIB'])){
      $CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID]=$tab_rq_info_config['CHANGEMENT_LISTE_INFO_LIB'];
		}
		if(!isset($CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID])){$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID]='';}
		$LISTE_CONFIG='CHANGEMENT_LISTE_CONFIG_ID_'.$CHANGEMENT_LISTE_CONFIG_ID;
		
		      switch ($CHANGEMENT_LISTE_CONFIG_TYPE)
            {
            case "liste": 
              
              $CHANGEMENT_LISTE_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_LISTE_CONFIG_TABLE'];
              $CHANGEMENT_LISTE_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE);
              $LISTE_CONFIG='CHANGEMENT_LISTE_CONFIG_ID_'.$CHANGEMENT_LISTE_CONFIG_ID;
              $CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
              $CHANGEMENT_LISTE_INFO_AUTRE_ID=$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID];
              $sql="INSERT INTO `changement_liste_info` (`CHANGEMENT_LISTE_INFO_ID` ,`CHANGEMENT_LISTE_ID` ,`CHANGEMENT_LISTE_CONFIG_ID` ,`CHANGEMENT_LISTE_INFO_AUTRE_ID` ,`CHANGEMENT_LISTE_INFO_LIB`,`ENABLE` )VALUES (NULL , '".$CHANGEMENT_ID."', '".$CHANGEMENT_LISTE_CONFIG_ID."', '".$CHANGEMENT_LISTE_INFO_AUTRE_ID."', '', '0');";
              mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
              $TABLE_SQL_SQL='changement_liste_info';       
              historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');
            break;
            
            case "checkbox_horizontal": 
              $CHANGEMENT_LISTE_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_LISTE_CONFIG_TABLE'];
              $CHANGEMENT_LISTE_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE);
              $rq_info_config_table="
              SELECT  *
              FROM `".$CHANGEMENT_LISTE_CONFIG_TABLE."`
              WHERE 
              `ENABLE`='0'
              AND `".$CHANGEMENT_LISTE_CONFIG_TABLE_TRI."`!='vide'
              ORDER BY `".$CHANGEMENT_LISTE_CONFIG_TABLE_TRI."`";
              $res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
              $tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
              $total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);
              if($total_ligne_rq_info_config_table!=0){
                do {
                  $ID_SQL=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE).'_ID';
                  $ID_LIB=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE);
                  $COM_SQL=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE).'_COM';
                  
                  $CHANGEMENT_LISTE_CONFIG_TABLE_ID=$tab_rq_info_config_table[$ID_SQL];
                  $CHANGEMENT_LISTE_CONFIG_TABLE_LIB=$tab_rq_info_config_table[$ID_LIB];
                  $CHANGEMENT_LISTE_INFO_AUTRE_ID=$CHANGEMENT_LISTE_CONFIG_TABLE_ID;
                  if(isset($tab_rq_info_config_table[$COM_SQL])){
                  	$CHANGEMENT_LISTE_CONFIG_TABLE_COM=$tab_rq_info_config_table[$COM_SQL];	
                  }else{
                  	$CHANGEMENT_LISTE_CONFIG_TABLE_COM='non';
                  }
	              $LISTE_CONFIG='CHANGEMENT_LISTE_CONFIG_ID_'.$CHANGEMENT_LISTE_CONFIG_ID.'_'.$CHANGEMENT_LISTE_CONFIG_TABLE_ID;
	              $CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE=$CHANGEMENT_LISTE_CONFIG_ID.'_'.$CHANGEMENT_LISTE_CONFIG_TABLE_ID;
	              if(!isset($NB_checkbox)){$NB_checkbox=0;}
	              if(isset($tab_var[$LISTE_CONFIG])){
	              	$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
	              	$NB_checkbox=$NB_checkbox+1;
	              }else{
	              	$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE]='off';
	              }
	              
	              if($CHANGEMENT_LISTE_CONFIG_TABLE_COM=='oui'){
		              $LISTE_CONFIG='CHANGEMENT_LISTE_CONFIG_ID_'.$CHANGEMENT_LISTE_CONFIG_ID.'_'.$CHANGEMENT_LISTE_CONFIG_TABLE_ID.'_COM';
		              $CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM=$CHANGEMENT_LISTE_CONFIG_ID.'_'.$CHANGEMENT_LISTE_CONFIG_TABLE_ID.'_COM';
		              if(isset($tab_var[$LISTE_CONFIG])){
		              	$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
		              	
		              }else{
		              	$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM]='';
		              }
	              }
	              if($CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE]=='on'){
	              	if(!isset($CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM)){
	              		$SQL_TEMP_COM='';
	              	}else{
	              		if(isset($CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM])){
	              			$SQL_TEMP_COM=$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM];
	              		}else{
	              			$SQL_TEMP_COM='';
	              		}
	              		
	        	}
		              $sql="INSERT INTO `changement_liste_info` (`CHANGEMENT_LISTE_INFO_ID` ,`CHANGEMENT_LISTE_ID` ,`CHANGEMENT_LISTE_CONFIG_ID` ,`CHANGEMENT_LISTE_INFO_AUTRE_ID` ,`CHANGEMENT_LISTE_INFO_LIB`,`ENABLE` )VALUES (NULL , '".$CHANGEMENT_ID."', '".$CHANGEMENT_LISTE_CONFIG_ID."', '".$CHANGEMENT_LISTE_INFO_AUTRE_ID."', '".$SQL_TEMP_COM."', '0');";
		              mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
		              $TABLE_SQL_SQL='changement_liste_info';       
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
              FROM `".$CHANGEMENT_LISTE_CONFIG_TABLE."`
              WHERE 
              `ENABLE`='0'
              AND `".$CHANGEMENT_LISTE_CONFIG_TABLE_TRI."`='vide'
              ORDER BY `".$CHANGEMENT_LISTE_CONFIG_TABLE_TRI."`";
              $res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
              $tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
              $total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);
              if($total_ligne_rq_info_config_table!=0){
                do {
                  $ID_SQL=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE).'_ID';
                  $ID_LIB=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE);
                  $COM_SQL=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE).'_COM';
                  $CHANGEMENT_LISTE_CONFIG_TABLE_ID=$tab_rq_info_config_table[$ID_SQL];
                  $CHANGEMENT_LISTE_CONFIG_TABLE_LIB=$tab_rq_info_config_table[$ID_LIB];
                  $CHANGEMENT_LISTE_INFO_AUTRE_ID=$CHANGEMENT_LISTE_CONFIG_TABLE_ID;
                  if(isset($tab_rq_info_config_table[$COM_SQL])){
                  	$CHANGEMENT_LISTE_CONFIG_TABLE_COM=$tab_rq_info_config_table[$COM_SQL];	
                  }else{
                  	$CHANGEMENT_LISTE_CONFIG_TABLE_COM='non';
                  }
	              $LISTE_CONFIG='CHANGEMENT_LISTE_CONFIG_ID_'.$CHANGEMENT_LISTE_CONFIG_ID.'_'.$CHANGEMENT_LISTE_CONFIG_TABLE_ID;
	              $CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE=$CHANGEMENT_LISTE_CONFIG_ID.'_'.$CHANGEMENT_LISTE_CONFIG_TABLE_ID;
	              if(!isset($NB_checkbox)){$NB_checkbox=0;}
	              if(isset($tab_var[$LISTE_CONFIG])){
	              	$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
	              	$NB_checkbox=$NB_checkbox+1;
	              }else{
	              	$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE]='off';
	              }
	              
	              if($CHANGEMENT_LISTE_CONFIG_TABLE_COM=='oui'){
		              $LISTE_CONFIG='CHANGEMENT_LISTE_CONFIG_ID_'.$CHANGEMENT_LISTE_CONFIG_ID.'_'.$CHANGEMENT_LISTE_CONFIG_TABLE_ID.'_COM';
		              $CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM=$CHANGEMENT_LISTE_CONFIG_ID.'_'.$CHANGEMENT_LISTE_CONFIG_TABLE_ID.'_COM';
		              if(isset($tab_var[$LISTE_CONFIG])){
		              	$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
		              	
		              }else{
		              	$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM]='';
		              }
		              
	              }
	              if($CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE]=='on'){
	              	if(!isset($CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM)){
	              		$SQL_TEMP_COM='';
	              	}else{
	              		if(isset($CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM])){
	              			$SQL_TEMP_COM=$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM];
	              		}else{
	              			$SQL_TEMP_COM='';
	              		}
	        	}
		              $sql="INSERT INTO `changement_liste_info` (`CHANGEMENT_LISTE_INFO_ID` ,`CHANGEMENT_LISTE_ID` ,`CHANGEMENT_LISTE_CONFIG_ID` ,`CHANGEMENT_LISTE_INFO_AUTRE_ID` ,`CHANGEMENT_LISTE_INFO_LIB`,`ENABLE` )VALUES (NULL , '".$CHANGEMENT_ID."', '".$CHANGEMENT_LISTE_CONFIG_ID."', '".$CHANGEMENT_LISTE_INFO_AUTRE_ID."', '".$SQL_TEMP_COM."', '0');";
		              mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
		              $TABLE_SQL_SQL='changement_liste_info';       
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
            case "checkbox": 
              $CHANGEMENT_LISTE_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_LISTE_CONFIG_TABLE'];
              $CHANGEMENT_LISTE_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE);
              $rq_info_config_table="
              SELECT  *
              FROM `".$CHANGEMENT_LISTE_CONFIG_TABLE."`
              WHERE 
              `ENABLE`='0'
              AND `".$CHANGEMENT_LISTE_CONFIG_TABLE_TRI."`!='vide'
              ORDER BY `".$CHANGEMENT_LISTE_CONFIG_TABLE_TRI."`";
              $res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
              $tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
              $total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);
              if($total_ligne_rq_info_config_table!=0){
                do {
                  $ID_SQL=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE).'_ID';
                  $ID_LIB=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE);
                  $COM_SQL=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE).'_COM';
                  
                  $CHANGEMENT_LISTE_CONFIG_TABLE_ID=$tab_rq_info_config_table[$ID_SQL];
                  $CHANGEMENT_LISTE_CONFIG_TABLE_LIB=$tab_rq_info_config_table[$ID_LIB];
                  $CHANGEMENT_LISTE_INFO_AUTRE_ID=$CHANGEMENT_LISTE_CONFIG_TABLE_ID;
                  if(isset($tab_rq_info_config_table[$COM_SQL])){
                  	$CHANGEMENT_LISTE_CONFIG_TABLE_COM=$tab_rq_info_config_table[$COM_SQL];	
                  }else{
                  	$CHANGEMENT_LISTE_CONFIG_TABLE_COM='non';
                  }
	              $LISTE_CONFIG='CHANGEMENT_LISTE_CONFIG_ID_'.$CHANGEMENT_LISTE_CONFIG_ID.'_'.$CHANGEMENT_LISTE_CONFIG_TABLE_ID;
	              $CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE=$CHANGEMENT_LISTE_CONFIG_ID.'_'.$CHANGEMENT_LISTE_CONFIG_TABLE_ID;
	              if(!isset($NB_checkbox)){$NB_checkbox=0;}
	              if(isset($tab_var[$LISTE_CONFIG])){
	              	$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
	              	$NB_checkbox=$NB_checkbox+1;
	              }else{
	              	$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE]='off';
	              }
	              
	              if($CHANGEMENT_LISTE_CONFIG_TABLE_COM=='oui'){
		              $LISTE_CONFIG='CHANGEMENT_LISTE_CONFIG_ID_'.$CHANGEMENT_LISTE_CONFIG_ID.'_'.$CHANGEMENT_LISTE_CONFIG_TABLE_ID.'_COM';
		              $CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM=$CHANGEMENT_LISTE_CONFIG_ID.'_'.$CHANGEMENT_LISTE_CONFIG_TABLE_ID.'_COM';
		              if(isset($tab_var[$LISTE_CONFIG])){
		              	$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
		              	
		              }else{
		              	$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM]='';
		              }
	              }
	              if($CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE]=='on'){
		              $sql="INSERT INTO `changement_liste_info` (`CHANGEMENT_LISTE_INFO_ID` ,`CHANGEMENT_LISTE_ID` ,`CHANGEMENT_LISTE_CONFIG_ID` ,`CHANGEMENT_LISTE_INFO_AUTRE_ID` ,`CHANGEMENT_LISTE_INFO_LIB`,`ENABLE` )VALUES (NULL , '".$CHANGEMENT_ID."', '".$CHANGEMENT_LISTE_CONFIG_ID."', '".$CHANGEMENT_LISTE_INFO_AUTRE_ID."', '".$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM]."', '0');";
		              mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
		              $TABLE_SQL_SQL='changement_liste_info';       
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
              FROM `".$CHANGEMENT_LISTE_CONFIG_TABLE."`
              WHERE 
              `ENABLE`='0'
              AND `".$CHANGEMENT_LISTE_CONFIG_TABLE_TRI."`='vide'
              ORDER BY `".$CHANGEMENT_LISTE_CONFIG_TABLE_TRI."`";
              $res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
              $tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
              $total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);
              if($total_ligne_rq_info_config_table!=0){
                do {
                  $ID_SQL=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE).'_ID';
                  $ID_LIB=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE);
                  $COM_SQL=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE).'_COM';
                  $CHANGEMENT_LISTE_CONFIG_TABLE_ID=$tab_rq_info_config_table[$ID_SQL];
                  $CHANGEMENT_LISTE_CONFIG_TABLE_LIB=$tab_rq_info_config_table[$ID_LIB];
                  $CHANGEMENT_LISTE_INFO_AUTRE_ID=$CHANGEMENT_LISTE_CONFIG_TABLE_ID;
                  if(isset($tab_rq_info_config_table[$COM_SQL])){
                  	$CHANGEMENT_LISTE_CONFIG_TABLE_COM=$tab_rq_info_config_table[$COM_SQL];	
                  }else{
                  	$CHANGEMENT_LISTE_CONFIG_TABLE_COM='non';
                  }
	              $LISTE_CONFIG='CHANGEMENT_LISTE_CONFIG_ID_'.$CHANGEMENT_LISTE_CONFIG_ID.'_'.$CHANGEMENT_LISTE_CONFIG_TABLE_ID;
	              $CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE=$CHANGEMENT_LISTE_CONFIG_ID.'_'.$CHANGEMENT_LISTE_CONFIG_TABLE_ID;
	              if(!isset($NB_checkbox)){$NB_checkbox=0;}
	              if(isset($tab_var[$LISTE_CONFIG])){
	              	$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
	              	$NB_checkbox=$NB_checkbox+1;
	              }else{
	              	$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE]='off';
	              }
	              
	              if($CHANGEMENT_LISTE_CONFIG_TABLE_COM=='oui'){
		              $LISTE_CONFIG='CHANGEMENT_LISTE_CONFIG_ID_'.$CHANGEMENT_LISTE_CONFIG_ID.'_'.$CHANGEMENT_LISTE_CONFIG_TABLE_ID.'_COM';
		              $CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM=$CHANGEMENT_LISTE_CONFIG_ID.'_'.$CHANGEMENT_LISTE_CONFIG_TABLE_ID.'_COM';
		              if(isset($tab_var[$LISTE_CONFIG])){
		              	$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
		              	
		              }else{
		              	$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM]='';
		              }
		              
	              }
	              if($CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE]=='on'){
		              $sql="INSERT INTO `changement_liste_info` (`CHANGEMENT_LISTE_INFO_ID` ,`CHANGEMENT_LISTE_ID` ,`CHANGEMENT_LISTE_CONFIG_ID` ,`CHANGEMENT_LISTE_INFO_AUTRE_ID` ,`CHANGEMENT_LISTE_INFO_LIB`,`ENABLE` )VALUES (NULL , '".$CHANGEMENT_ID."', '".$CHANGEMENT_LISTE_CONFIG_ID."', '".$CHANGEMENT_LISTE_INFO_AUTRE_ID."', '".$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM]."', '0');";
		              mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
		              $TABLE_SQL_SQL='changement_liste_info';       
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
              $CHANGEMENT_LISTE_INFO_AUTRE_ID=0;
              $CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
              $sql="INSERT INTO `changement_liste_info` (`CHANGEMENT_LISTE_INFO_ID` ,`CHANGEMENT_LISTE_ID` ,`CHANGEMENT_LISTE_CONFIG_ID` ,`CHANGEMENT_LISTE_INFO_AUTRE_ID` ,`CHANGEMENT_LISTE_INFO_LIB`,`ENABLE` )VALUES (NULL , '".$CHANGEMENT_ID."', '".$CHANGEMENT_LISTE_CONFIG_ID."', '".$CHANGEMENT_LISTE_INFO_AUTRE_ID."', '".$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID]."', '0');";
              mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
              $TABLE_SQL_SQL='changement_liste_info';       
              historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');
              break;
              
            case "text": 
              $CHANGEMENT_LISTE_INFO_AUTRE_ID=0;
              $CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
              $sql="INSERT INTO `changement_liste_info` (`CHANGEMENT_LISTE_INFO_ID` ,`CHANGEMENT_LISTE_ID` ,`CHANGEMENT_LISTE_CONFIG_ID` ,`CHANGEMENT_LISTE_INFO_AUTRE_ID` ,`CHANGEMENT_LISTE_INFO_LIB`,`ENABLE` )VALUES (NULL , '".$CHANGEMENT_ID."', '".$CHANGEMENT_LISTE_CONFIG_ID."', '".$CHANGEMENT_LISTE_INFO_AUTRE_ID."', '".$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID]."', '0');";
              mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
              $TABLE_SQL_SQL='changement_liste_info';       
              historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');
            break;
            }

        $sql="OPTIMIZE TABLE `changement_liste_info` ";
	mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
		
		
		 } while ($tab_rq_info_config = mysql_fetch_assoc($res_rq_info_config));
	        $ligne= mysql_num_rows($res_rq_info_config);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_config, 0);
	          $tab_rq_info_config = mysql_fetch_assoc($res_rq_info_config);
	        }
	}
	mysql_free_result($res_rq_info_config);  

	$TRACE_CATEGORIE='Changement';
        $TRACE_TABLE='changement_liste';
        $TRACE_REF_ID=$CHANGEMENT_ID;
        $TRACE_ACTION='Ajout';
        $TRACE_ETAT=$CHANGEMENT_STATUS_ID;
        moteur_trace($TRACE_CATEGORIE,$TRACE_TABLE,$TRACE_REF_ID,$TRACE_ACTION,$TRACE_ETAT);
	mysql_free_result($res_rq_info);
// ajout date pour le calendrier    	
    	$DATE_INSERT=$Date_Inter_Debut_bdd;

        while ($STOP_insert != 1) {
            if($DATE_INSERT == $Date_Inter_Fin_bdd ){
            	$STOP_insert=1;
            }
            $date=$DATE_INSERT;
            $jour=substr($date,6,2);
            $mois=substr($date,4,2);
            $annee=substr($date,0,4);
            $CHANGEMENT_SEMAINE=date("W", mktime(12, 0, 0, $mois, $jour , $annee));
            $rq_date_histo_info="
              SELECT `CHANGEMENT_DATE_ID` 
              FROM `changement_date` 
              WHERE `CHANGEMENT_DATE` ='".$DATE_INSERT."' 
              AND `CHANGEMENT_ID` ='".$CHANGEMENT_ID."' 
              AND `CHANGEMENT_SEMAINE` ='".$CHANGEMENT_SEMAINE."' 
              AND `ENABLE` ='0' 
              LIMIT 1";
              $res_rq_date_histo_info = mysql_query($rq_date_histo_info, $mysql_link) or die(mysql_error());
              $tab_rq_date_histo_info = mysql_fetch_assoc($res_rq_date_histo_info);
              $total_ligne_rq_date_histo_info=mysql_num_rows($res_rq_date_histo_info);
              mysql_free_result($res_rq_date_histo_info);
              if($total_ligne_rq_date_histo_info==0){
                $sql="
                INSERT INTO `changement_date` (`CHANGEMENT_DATE_ID` ,`CHANGEMENT_DATE` ,`CHANGEMENT_SEMAINE` ,`CHANGEMENT_ID` ,`ENABLE` )
                VALUES (NULL , '".$DATE_INSERT."', '".$CHANGEMENT_SEMAINE."', '".$CHANGEMENT_ID."', '0');";
                mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
                $TABLE_SQL_SQL='changement_date';       
        	historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');
              }
            $DATE_INSERT=date("Ymd", mktime(12, 0, 0, $mois, $jour + 1, $annee));
            
        }
        $sql="OPTIMIZE TABLE `changement_date` ";
	mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
	
	$TABLE_SQL_SQL='changement_date';       
	historique_sql_new($sql,$TABLE_SQL_SQL,'OPTIMIZE');
	
        $MOIS_Inter_Debut=substr($Date_Inter_Debut_bdd,4,2);
        $ANNEE_Inter_Debut=substr($Date_Inter_Debut_bdd,0,4);

        echo '
        <script language="JavaScript">
        url=("./index.php?ITEM=changement_Info_Changement&action=Info&ID='.$CHANGEMENT_ID.'");
        window.location=url;
        </script>
        ';
	}else{
		$STOP=1;
		$STOP_pb=1;
	}
        
     } 
    $_GET['action']="Ajout";
  }
  
   # Cas Inscription
  if($tab_var['btn']=="Inscription"){
      $rq_far_info="
      SELECT `CHANGEMENT_FAR_ID`
      FROM `changement_far`
      WHERE `CHANGEMENT_LISTE_ID` ='".$ID."' 
      AND `ENABLE` = '0'
      ";
      $res_rq_far_info = mysql_query($rq_far_info, $mysql_link) or die(mysql_error());
      $tab_rq_far_info = mysql_fetch_assoc($res_rq_far_info);
      $total_ligne_rq_far_info=mysql_num_rows($res_rq_far_info);
      mysql_free_result($res_rq_far_info);
      if($total_ligne_rq_far_info > 1){
        echo '
        <script language="JavaScript">
        url=("./index.php?ITEM=changement_Modif_FAR&action=Modif&ID='.$ID.'");
        window.location=url;
        </script>
        ';
      }else{
      	echo '
        <script language="JavaScript">
        url=("./index.php?ITEM=changement_Ajout_FAR&action=Ajout&ID='.$ID.'");
        window.location=url;
        </script>
        ';
      }

  }
  
  # Cas Abandon
  if($tab_var['btn']=="Abandon"){
        echo '
        <script language="JavaScript">
        url=("./index.php?ITEM=changement_Action_Changement_Status&action=Abandon&ID='.$ID.'");
        window.location=url;
        </script>
        ';
  }
    
 
# Cas ReInscription
  if($tab_var['btn']=="ReInscription"){
	echo '
        <script language="JavaScript">
        url=("./index.php?ITEM=changement_Action_Changement_Status&action=ReInscription&ID='.$ID.'");
        window.location=url;
        </script>
        ';
  }
  
  # Cas Brouillon
  if($tab_var['btn']=="Brouillon"){
  	echo '
        <script language="JavaScript">
        url=("./index.php?ITEM=changement_Action_Changement_Status&action=Brouillon&ID='.$ID.'");
        window.location=url;
        </script>
        ';

  }

# Cas Validation
  if($tab_var['btn']=="Validation"){
  	echo '
        <script language="JavaScript">
        url=("./index.php?ITEM=changement_Action_Changement_Status&action=Validation&ID='.$ID.'");
        window.location=url;
        </script>
        ';
  }
# Cas Fiche Bilan
  if($tab_var['btn']=="Fiche Bilan"){
  	if(isset($tab_var['Bilan_action'])){
	  $Bilan_action=$tab_var['Bilan_action'];
	}else{
	  $Bilan_action='Ajout';
	}
	switch ($Bilan_action)
	{
	  case "Ajout": 
		$Bilan_action='Ajout';
            	$Bilan_ITEM='changement_Ajout_Bilan';
	  break;
	  case "Modif": 
		$Bilan_action='Modif';
            	$Bilan_ITEM='changement_Modif_Bilan';
	  break;
	  case "Info": 
		$Bilan_action='Info';
		$Bilan_ITEM='changement_Info_Bilan';
	  break;
	  default:
		$Bilan_action='Info';
		$Bilan_ITEM='changement_Info_Bilan';
	  break;
	}

  	echo '
        <script language="JavaScript">
        url=("./index.php?ITEM='.$Bilan_ITEM.'&action='.$Bilan_action.'&ID='.$ID.'");
        window.location=url;
        </script>
        ';
  }
  # Cas FAR
  if($tab_var['btn']=="FAR"){
  	if(isset($tab_var['FAR_action'])){
	  $FAR_action=$tab_var['FAR_action'];
	}else{
	  $FAR_action='Info';
	}
	switch ($FAR_action)
	{
	  case "Ajout": 
		$FAR_action='Ajout';
            	$FAR_ITEM='changement_Ajout_FAR';
	  break;
	  case "Modif": 
		$FAR_action='Modif';
            	$FAR_ITEM='changement_Modif_FAR';
	  break;
	  case "Info": 
		$FAR_action='Info';
		$FAR_ITEM='changement_Info_FAR';
	  break;
	  default:
		$FAR_action='Info';
		$FAR_ITEM='changement_Info_FAR';
	  break;
	}

  	echo '
        <script language="JavaScript">
        url=("./index.php?ITEM='.$FAR_ITEM.'&action='.$FAR_action.'&ID='.$ID.'");
        window.location=url;
        </script>
        ';
  }
# Cas Compte Rendu
  if($tab_var['btn']=="Compte Rendu"){
  	if(isset($tab_var['CR_action'])){
	  $CR_action=$tab_var['CR_action'];
	}else{
	  $CR_action='Ajout';
	}
	switch ($CR_action)
	{
	  case "Ajout": 
		$CR_action='Ajout';
            	$CR_ITEM='changement_Ajout_CompteRendu';
	  break;
	  case "Modif": 
		$CR_action='Modif';
            	$CR_ITEM='changement_Modif_CompteRendu';
	  break;
	  case "Info": 
		$CR_action='Info';
		$CR_ITEM='changement_Info_CompteRendu';
	  break;
	  default:
		$CR_action='Info';
		$CR_ITEM='changement_Info_CompteRendu';
	  break;
	}

  	echo '
        <script language="JavaScript">
        url=("./index.php?ITEM='.$CR_ITEM.'&action='.$CR_action.'&ID='.$ID.'");
        window.location=url;
        </script>
        ';
  }
  # Cas Modifier
    if($tab_var['btn']=="Modifier"){
    $action='Modif';
    $CHANGEMENT_ID=$ID;
    $Date_Inter_Debut=addslashes(trim(htmlentities($tab_var['txt_Date_Inter_Debut'])));
    $HEURE_PLANIF_DEBUT_H=addslashes(trim(htmlentities($tab_var['txt_HEURE_PLANIF_DEBUT_H'])));
    $HEURE_PLANIF_DEBUT_M=addslashes(trim(htmlentities($tab_var['txt_HEURE_PLANIF_DEBUT_M'])));
    $Date_Inter_Fin=addslashes(trim(htmlentities($tab_var['txt_Date_Inter_Fin'])));
    $HEURE_PLANIF_FIN_H=addslashes(trim(htmlentities($tab_var['txt_HEURE_PLANIF_FIN_H'])));
    $HEURE_PLANIF_FIN_M=addslashes(trim(htmlentities($tab_var['txt_HEURE_PLANIF_FIN_M'])));

    $lib=substr(addslashes(trim(htmlentities($tab_var['txt_lib']))),0,100);
    $CHANGEMENT_DEMANDE_ID=addslashes(trim(htmlentities($tab_var['CHANGEMENT_DEMANDE_ID'])));
    if(strlen($HEURE_PLANIF_DEBUT_H) == 1 ){
    	$HEURE_PLANIF_DEBUT_H='0'.$HEURE_PLANIF_DEBUT_H;
    }
    if(strlen($HEURE_PLANIF_DEBUT_M) == 1 ){
    	$HEURE_PLANIF_DEBUT_M='0'.$HEURE_PLANIF_DEBUT_M;
    }
    if(strlen($HEURE_PLANIF_FIN_H) == 1 ){
    	$HEURE_PLANIF_FIN_H='0'.$HEURE_PLANIF_FIN_H;
    }
    if(strlen($HEURE_PLANIF_FIN_M) == 1 ){
    	$HEURE_PLANIF_FIN_M='0'.$HEURE_PLANIF_FIN_M;
    }
// test des heures
    $HEURE_PLANIF_DEBUT=$HEURE_PLANIF_DEBUT_H.'h'.$HEURE_PLANIF_DEBUT_M;
    $HEURE_PLANIF_DEBUT_info=$HEURE_PLANIF_DEBUT_H.''.$HEURE_PLANIF_DEBUT_M;
    if($HEURE_PLANIF_DEBUT_H!=''){
      if(is_numeric($HEURE_PLANIF_DEBUT_H)){
        if($HEURE_PLANIF_DEBUT_H < 0 OR $HEURE_PLANIF_DEBUT_H >= 24){
          $STOP=1;
          $HEURE_PLANIF_DEBUT_LISTE=1;
        }
      }else{
        $STOP=2;
        $HEURE_PLANIF_DEBUT_LISTE=1;
      }
      if(is_numeric($HEURE_PLANIF_DEBUT_M)){
        if($HEURE_PLANIF_DEBUT_M < 0 OR $HEURE_PLANIF_DEBUT_M >= 60){
          $STOP=3;
          $HEURE_PLANIF_DEBUT_LISTE=1;
        }
      }else{
        $STOP=4;
        $HEURE_PLANIF_DEBUT_LISTE=1;
      }
    }

    $HEURE_PLANIF_FIN=$HEURE_PLANIF_FIN_H.'h'.$HEURE_PLANIF_FIN_M;
    $HEURE_PLANIF_FIN_info=$HEURE_PLANIF_FIN_H.''.$HEURE_PLANIF_FIN_M;
    if($HEURE_PLANIF_FIN_H!=''){
      if(is_numeric($HEURE_PLANIF_FIN_H)){
        if($HEURE_PLANIF_FIN_H < 0 OR $HEURE_PLANIF_FIN_H >= 24){
          $STOP=5;
          $HEURE_PLANIF_FIN_LISTE=1;
        }
      }else{
        $STOP=6;
        $HEURE_PLANIF_FIN_LISTE=1;
      }
      if(is_numeric($HEURE_PLANIF_FIN_M)){
        if($HEURE_PLANIF_FIN_M < 0 OR $HEURE_PLANIF_FIN_M >= 60){
          $STOP=7;
          $HEURE_PLANIF_FIN_LISTE=1;
        }
      }else{
        $STOP=8;
        $HEURE_PLANIF_FIN_LISTE=1;
      }
    }
// test des jours    
    $jour='';
    $mois='';
    $annee='';
    $jour=substr($Date_Inter_Debut,0,2);
    $mois=substr($Date_Inter_Debut,3,2);
    $annee=substr($Date_Inter_Debut,6,4);
    $Date_Inter_Debut_bdd=$annee.''.$mois.''.$jour;
    $jour='';
    $mois='';
    $annee='';
    $jour=substr($Date_Inter_Fin,0,2);
    $mois=substr($Date_Inter_Fin,3,2);
    $annee=substr($Date_Inter_Fin,6,4);
    $Date_Inter_Fin_bdd=$annee.''.$mois.''.$jour;
    if($Date_Inter_Fin_bdd < $Date_Inter_Debut_bdd){
      $STOP=9;
      $DATE_LISTE=1;
    }
    if($Date_Inter_Debut_bdd == $Date_Inter_Fin_bdd){
    	if($HEURE_PLANIF_DEBUT_info >= $HEURE_PLANIF_FIN_info){
    		$STOP=10;
    		$HEURE_LISTE=1;
    	}
    }
    if ( $ENV == "x" ){	 	
      if($Date_Inter_Debut_bdd < $DATE_DU_JOUR){
        $STOP=11;
        $DATE_LISTE=1;
      }
	  }
// test libelle
    if($lib==''){
    	$STOP=12;
    	$STOP_lib=1;
   }
// test type demande
    if($CHANGEMENT_DEMANDE_ID==0){
    	$STOP=13;
    	$STOP_type=1;
   }
   ///
   $rq_info_config="
	 SELECT 
	 `changement_liste_config`.`CHANGEMENT_LISTE_CONFIG_ID` , 
	 `changement_liste_config`.`CHANGEMENT_LISTE_CONFIG_LIB` , 
	 `changement_liste_config`.`CHANGEMENT_LISTE_CONFIG_TYPE` , 
	 `changement_liste_config`.`CHANGEMENT_LISTE_CONFIG_OBLIGATOIRE` , 
	 `changement_liste_config`.`CHANGEMENT_LISTE_CONFIG_TABLE` 
	FROM `changement_liste_config` , `changement_liste_info` 
	WHERE `changement_liste_config`.`ENABLE` = '0'
	AND `changement_liste_config`.`CHANGEMENT_LISTE_CONFIG_ID` = `changement_liste_info`.`CHANGEMENT_LISTE_CONFIG_ID` 
	AND `changement_liste_info`.`CHANGEMENT_LISTE_ID` ='".$ID."'
	GROUP BY `changement_liste_config`.`CHANGEMENT_LISTE_CONFIG_LIB` ";
	$res_rq_info_config = mysql_query($rq_info_config, $mysql_link) or die(mysql_error());
	$tab_rq_info_config = mysql_fetch_assoc($res_rq_info_config);
	$total_ligne_rq_info_config=mysql_num_rows($res_rq_info_config);
	//echo $rq_info_config;
	if($total_ligne_rq_info_config!=0){
		do {
		$CHANGEMENT_LISTE_CONFIG_ID=$tab_rq_info_config['CHANGEMENT_LISTE_CONFIG_ID'];
		$CHANGEMENT_LISTE_CONFIG_LIB=$tab_rq_info_config['CHANGEMENT_LISTE_CONFIG_LIB'];
		$CHANGEMENT_LISTE_CONFIG_TYPE=$tab_rq_info_config['CHANGEMENT_LISTE_CONFIG_TYPE'];
		$CHANGEMENT_LISTE_CONFIG_OBLIGATOIRE=$tab_rq_info_config['CHANGEMENT_LISTE_CONFIG_OBLIGATOIRE'];
		$CHANGEMENT_LISTE_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_LISTE_CONFIG_TABLE'];
		if(isset($tab_rq_info_config['CHANGEMENT_LISTE_INFO_AUTRE_ID'])){
      $CHANGEMENT_LISTE_INFO_AUTRE_ID=$tab_rq_info_config['CHANGEMENT_LISTE_INFO_AUTRE_ID'];
		}
		if(isset($tab_rq_info_config['CHANGEMENT_LISTE_INFO_LIB'])){
      $CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID]=$tab_rq_info_config['CHANGEMENT_LISTE_INFO_LIB'];
		}
		//if(!isset($CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID])){$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID]='';}
		$LISTE_CONFIG='CHANGEMENT_LISTE_CONFIG_ID_'.$CHANGEMENT_LISTE_CONFIG_ID;
		
		      switch ($CHANGEMENT_LISTE_CONFIG_TYPE)
            {
            	case "varchar": 
            	if(!isset($tab_var[$LISTE_CONFIG])){
            		$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID]='';	
            	}else{
              		$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
        	}
              break;
              
            case "text": 
              if(!isset($tab_var[$LISTE_CONFIG])){
            		$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID]='';	
            	}else{
              		$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
        	}
            break;
            case "liste": 
              $CHANGEMENT_LISTE_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_LISTE_CONFIG_TABLE'];
              $CHANGEMENT_LISTE_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE);
              $LISTE_CONFIG='CHANGEMENT_LISTE_CONFIG_ID_'.$CHANGEMENT_LISTE_CONFIG_ID;
              $CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
              $CHANGEMENT_LISTE_CONFIG_STOP[$CHANGEMENT_LISTE_CONFIG_ID]=0;
              if($CHANGEMENT_LISTE_CONFIG_OBLIGATOIRE=='oui'){
                if($CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID]=='0'){
                  $CHANGEMENT_LISTE_CONFIG_STOP[$CHANGEMENT_LISTE_CONFIG_ID]=1;
                  $STOP=14;
                }
              }
            break;
            case "checkbox": 
              $CHANGEMENT_LISTE_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_LISTE_CONFIG_TABLE'];
              $CHANGEMENT_LISTE_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE);
              $rq_info_config_table="
              SELECT  *
              FROM `".$CHANGEMENT_LISTE_CONFIG_TABLE."`
              WHERE 
              `ENABLE`='0'
              ORDER BY `".$CHANGEMENT_LISTE_CONFIG_TABLE_TRI."`";
              $res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
              $tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
              $total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);
              if($total_ligne_rq_info_config_table!=0){
                do {
                  $ID_SQL=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE).'_ID';
                  $ID_LIB=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE);
                  $COM_SQL=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE).'_COM';
                  $CHANGEMENT_LISTE_CONFIG_TABLE_ID=$tab_rq_info_config_table[$ID_SQL];
                  $CHANGEMENT_LISTE_CONFIG_TABLE_LIB=$tab_rq_info_config_table[$ID_LIB];
                  $CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID]='0';
                  if(isset($tab_rq_info_config_table[$COM_SQL])){
                  	$CHANGEMENT_LISTE_CONFIG_TABLE_COM=$tab_rq_info_config_table[$COM_SQL];	
                  }else{
                  	$CHANGEMENT_LISTE_CONFIG_TABLE_COM='non';
                  }
	              $LISTE_CONFIG='CHANGEMENT_LISTE_CONFIG_ID_'.$CHANGEMENT_LISTE_CONFIG_ID.'_'.$CHANGEMENT_LISTE_CONFIG_TABLE_ID;
	              $CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE=$CHANGEMENT_LISTE_CONFIG_ID.'_'.$CHANGEMENT_LISTE_CONFIG_TABLE_ID;
	              if(!isset($NB_checkbox)){$NB_checkbox=0;}
	              if(isset($tab_var[$LISTE_CONFIG])){
	              	$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
	              	$NB_checkbox=$NB_checkbox+1;
	              }else{
	              	$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE]='off';
	              }
	              
	              if($CHANGEMENT_LISTE_CONFIG_TABLE_COM=='oui'){
		              $LISTE_CONFIG='CHANGEMENT_LISTE_CONFIG_ID_'.$CHANGEMENT_LISTE_CONFIG_ID.'_'.$CHANGEMENT_LISTE_CONFIG_TABLE_ID.'_COM';
		              $CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM=$CHANGEMENT_LISTE_CONFIG_ID.'_'.$CHANGEMENT_LISTE_CONFIG_TABLE_ID.'_COM';
		              if(isset($tab_var[$LISTE_CONFIG])){
		              	$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
		              	
		              }else{
		              	$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM]='';
		              }
		              if($CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE]=='on'){
		              		if($CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM]==''){
		              			$STOP=15;	
		              			$CHANGEMENT_LISTE_CONFIG_STOP_COM[$CHANGEMENT_LISTE_CONFIG_ID]=1;
		              		}
		              }
	              }
	              
                
                } while ($tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table));
                $ligne= mysql_num_rows($res_rq_info_config_table);
                if($ligne > 0) {
                  mysql_data_seek($res_rq_info_config_table, 0);
                  $tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
                }
                $CHANGEMENT_LISTE_CONFIG_STOP[$CHANGEMENT_LISTE_CONFIG_ID]=0;
                if($CHANGEMENT_LISTE_CONFIG_OBLIGATOIRE=='oui'){
			if($NB_checkbox==0){
				$CHANGEMENT_LISTE_CONFIG_STOP[$CHANGEMENT_LISTE_CONFIG_ID]=1;
				$STOP=16;
			}
		}
		
		
              }
              
              mysql_free_result($res_rq_info_config_table);
            break;
            case "checkbox_horizontal": 
              $CHANGEMENT_LISTE_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_LISTE_CONFIG_TABLE'];
              $CHANGEMENT_LISTE_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE);
              $rq_info_config_table="
              SELECT  *
              FROM `".$CHANGEMENT_LISTE_CONFIG_TABLE."`
              WHERE 
              `ENABLE`='0'
              ORDER BY `".$CHANGEMENT_LISTE_CONFIG_TABLE_TRI."`";
              $res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
              $tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
              $total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);
              if($total_ligne_rq_info_config_table!=0){
                do {
                  $ID_SQL=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE).'_ID';
                  $ID_LIB=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE);
                  $COM_SQL=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE).'_COM';
                  $CHANGEMENT_LISTE_CONFIG_TABLE_ID=$tab_rq_info_config_table[$ID_SQL];
                  $CHANGEMENT_LISTE_CONFIG_TABLE_LIB=$tab_rq_info_config_table[$ID_LIB];
                  $CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID]='0';
                  if(isset($tab_rq_info_config_table[$COM_SQL])){
                  	$CHANGEMENT_LISTE_CONFIG_TABLE_COM=$tab_rq_info_config_table[$COM_SQL];	
                  }else{
                  	$CHANGEMENT_LISTE_CONFIG_TABLE_COM='non';
                  }
	              $LISTE_CONFIG='CHANGEMENT_LISTE_CONFIG_ID_'.$CHANGEMENT_LISTE_CONFIG_ID.'_'.$CHANGEMENT_LISTE_CONFIG_TABLE_ID;
	              $CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE=$CHANGEMENT_LISTE_CONFIG_ID.'_'.$CHANGEMENT_LISTE_CONFIG_TABLE_ID;
	              if(!isset($NB_checkbox)){$NB_checkbox=0;}
	              if(isset($tab_var[$LISTE_CONFIG])){
	              	$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
	              	$NB_checkbox=$NB_checkbox+1;
	              }else{
	              	$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE]='off';
	              }
	              
	              if($CHANGEMENT_LISTE_CONFIG_TABLE_COM=='oui'){
		              $LISTE_CONFIG='CHANGEMENT_LISTE_CONFIG_ID_'.$CHANGEMENT_LISTE_CONFIG_ID.'_'.$CHANGEMENT_LISTE_CONFIG_TABLE_ID.'_COM';
		              $CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM=$CHANGEMENT_LISTE_CONFIG_ID.'_'.$CHANGEMENT_LISTE_CONFIG_TABLE_ID.'_COM';
		              if(isset($tab_var[$LISTE_CONFIG])){
		              	$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
		              	
		              }else{
		              	$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM]='';
		              }
		              if($CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE]=='on'){
		              		if($CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM]==''){
		              			$STOP=15;	
		              			$CHANGEMENT_LISTE_CONFIG_STOP_COM[$CHANGEMENT_LISTE_CONFIG_ID]=1;
		              		}
		              }
	              }
	              
                
                } while ($tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table));
                $ligne= mysql_num_rows($res_rq_info_config_table);
                if($ligne > 0) {
                  mysql_data_seek($res_rq_info_config_table, 0);
                  $tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
                }
                $CHANGEMENT_LISTE_CONFIG_STOP[$CHANGEMENT_LISTE_CONFIG_ID]=0;
                if($CHANGEMENT_LISTE_CONFIG_OBLIGATOIRE=='oui'){
			if($NB_checkbox==0){
				$CHANGEMENT_LISTE_CONFIG_STOP[$CHANGEMENT_LISTE_CONFIG_ID]=1;
				$STOP=16;
			}
		}
		
		
              }
              
              mysql_free_result($res_rq_info_config_table);
            break;
            
            }
    $CHANGEMENT_LISTE_CONFIG_STOP[$CHANGEMENT_LISTE_CONFIG_ID]=0;
		if($CHANGEMENT_LISTE_CONFIG_OBLIGATOIRE=='oui'){
      if($CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID]==''){
        $CHANGEMENT_LISTE_CONFIG_STOP[$CHANGEMENT_LISTE_CONFIG_ID]=1;
        $STOP=17;
      }
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
	$MAJ_ID=0;
	$rq_info="
	SELECT 
	`CHANGEMENT_LISTE_ID`,
	`CHANGEMENT_LISTE_DATE_DEBUT`,
	`CHANGEMENT_LISTE_DATE_FIN`,
	`CHANGEMENT_LISTE_HEURE_DEBUT`,
	`CHANGEMENT_LISTE_HEURE_FIN`,
	`CHANGEMENT_LISTE_LIB`
	FROM `changement_liste`
	WHERE 
	`CHANGEMENT_LISTE_ID` ='".$ID."' 
	AND `CHANGEMENT_LISTE_DATE_DEBUT` ='".$Date_Inter_Debut_bdd."' 
	AND `CHANGEMENT_LISTE_DATE_FIN` ='".$Date_Inter_Fin_bdd."' 
	AND `CHANGEMENT_LISTE_HEURE_DEBUT` ='".$HEURE_PLANIF_DEBUT."' 
	AND `CHANGEMENT_LISTE_HEURE_FIN` ='".$HEURE_PLANIF_FIN."' 
	AND `CHANGEMENT_DEMANDE_ID` ='".$CHANGEMENT_DEMANDE_ID."' 
	AND `CHANGEMENT_LISTE_LIB` ='".$lib."' 
	AND `ENABLE` ='0'
	LIMIT 1";
	$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
	$tab_rq_info = mysql_fetch_assoc($res_rq_info);
	$total_ligne_rq_info=mysql_num_rows($res_rq_info);
	mysql_free_result($res_rq_info);
	if($total_ligne_rq_info==0){
		////////////////////////////////////
		$MAJ_ID=$MAJ_ID+1;
		$sql="
		UPDATE `changement_liste` SET 
		`CHANGEMENT_LISTE_DATE_DEBUT`= '".$Date_Inter_Debut_bdd."',
		`CHANGEMENT_LISTE_DATE_FIN`= '".$Date_Inter_Fin_bdd."',
		`CHANGEMENT_LISTE_HEURE_DEBUT`= '".$HEURE_PLANIF_DEBUT."',
		`CHANGEMENT_LISTE_HEURE_FIN`= '".$HEURE_PLANIF_FIN."',
		`CHANGEMENT_DEMANDE_ID`= '".$CHANGEMENT_DEMANDE_ID."',
		`CHANGEMENT_LISTE_LIB`= '".$lib."'
		WHERE `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."' LIMIT 1 ;";
	        mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
	        $TABLE_SQL_SQL='changement_liste';       
	        historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');
        	//echo '</BR>'.$sql;

		$rq_info_date="
		SELECT 
		`CHANGEMENT_LISTE_DATE_DEBUT`,
		`CHANGEMENT_LISTE_DATE_FIN`
		FROM `changement_liste`
		WHERE `CHANGEMENT_LISTE_ID` ='".$ID."' 
		AND `ENABLE` ='0'
		LIMIT 1";
		$res_rq_info_date = mysql_query($rq_info_date, $mysql_link) or die(mysql_error());
		$tab_rq_info_date = mysql_fetch_assoc($res_rq_info_date);
		$total_ligne_rq_info_date=mysql_num_rows($res_rq_info_date);
		$CHANGEMENT_LISTE_DATE_DEBUT_old=$tab_rq_info_date['CHANGEMENT_LISTE_DATE_DEBUT'];
		$CHANGEMENT_LISTE_DATE_FIN_old=$tab_rq_info_date['CHANGEMENT_LISTE_DATE_FIN'];
		mysql_free_result($res_rq_info_date);
		$CHANGEMENT_LISTE_DATE_DEBUT=$Date_Inter_Debut_bdd;
		$CHANGEMENT_LISTE_DATE_FIN=$Date_Inter_Fin_bdd;
		if($CHANGEMENT_LISTE_DATE_DEBUT_old!=$CHANGEMENT_LISTE_DATE_DEBUT&&$CHANGEMENT_LISTE_DATE_FIN_old!=$CHANGEMENT_LISTE_DATE_FIN){
			
	      $sql="UPDATE `changement_date` SET `ENABLE` = '1' WHERE `CHANGEMENT_ID` = '".$CHANGEMENT_ID."'";
	      mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
	      $TABLE_SQL_SQL='changement_date';       
	      historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');
			
			// ajout date pour le calendrier    	
		    	$DATE_INSERT=$Date_Inter_Debut_bdd;
		
		        while ($STOP_insert != 1) {
		            if($DATE_INSERT == $Date_Inter_Fin_bdd ){
		            	$STOP_insert=1;
		            }
		            $date=$DATE_INSERT;
		            $jour=substr($date,6,2);
		            $mois=substr($date,4,2);
		            $annee=substr($date,0,4);
		            $CHANGEMENT_SEMAINE=date("W", mktime(12, 0, 0, $mois, $jour , $annee));
		            $rq_date_histo_info="
		              SELECT `CHANGEMENT_DATE_ID` 
		              FROM `changement_date` 
		              WHERE `CHANGEMENT_DATE` ='".$DATE_INSERT."' 
		              AND `CHANGEMENT_ID` ='".$CHANGEMENT_ID."' 
		              AND `CHANGEMENT_SEMAINE` ='".$CHANGEMENT_SEMAINE."' 
		              AND `ENABLE` ='0' 
		              LIMIT 1";
		              $res_rq_date_histo_info = mysql_query($rq_date_histo_info, $mysql_link) or die(mysql_error());
		              $tab_rq_date_histo_info = mysql_fetch_assoc($res_rq_date_histo_info);
		              $total_ligne_rq_date_histo_info=mysql_num_rows($res_rq_date_histo_info);
		              mysql_free_result($res_rq_date_histo_info);
		              if($total_ligne_rq_date_histo_info==0){
		                $sql="
		                INSERT INTO `changement_date` (`CHANGEMENT_DATE_ID` ,`CHANGEMENT_DATE` ,`CHANGEMENT_SEMAINE` ,`CHANGEMENT_ID` ,`ENABLE` )
		                VALUES (NULL , '".$DATE_INSERT."', '".$CHANGEMENT_SEMAINE."', '".$CHANGEMENT_ID."', '0');";
		                //echo '</BR>'.$sql;
		                mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
		                $TABLE_SQL_SQL='changement_date';       
		        	historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');
		              }
		            $DATE_INSERT=date("Ymd", mktime(12, 0, 0, $mois, $jour + 1, $annee));
		            
		        }
		        $sql="OPTIMIZE TABLE `changement_date` ";
			mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
			
			$TABLE_SQL_SQL='changement_date';       
			historique_sql_new($sql,$TABLE_SQL_SQL,'OPTIMIZE');
		}
		
	}
	$rq_info_config="
	 SELECT 
	 `changement_liste_config`.`CHANGEMENT_LISTE_CONFIG_ID` , 
	 `changement_liste_config`.`CHANGEMENT_LISTE_CONFIG_LIB` , 
	 `changement_liste_config`.`CHANGEMENT_LISTE_CONFIG_TYPE` , 
	 `changement_liste_config`.`CHANGEMENT_LISTE_CONFIG_OBLIGATOIRE` , 
	 `changement_liste_config`.`CHANGEMENT_LISTE_CONFIG_TABLE` 
	FROM `changement_liste_config` , `changement_liste_info` 
	WHERE `changement_liste_config`.`ENABLE` = '0'
	AND `changement_liste_config`.`CHANGEMENT_LISTE_CONFIG_ID` = `changement_liste_info`.`CHANGEMENT_LISTE_CONFIG_ID` 
	AND `changement_liste_info`.`CHANGEMENT_LISTE_ID` ='".$ID."'
	GROUP BY `changement_liste_config`.`CHANGEMENT_LISTE_CONFIG_LIB` ";
	$res_rq_info_config = mysql_query($rq_info_config, $mysql_link) or die(mysql_error());
	$tab_rq_info_config = mysql_fetch_assoc($res_rq_info_config);
	$total_ligne_rq_info_config=mysql_num_rows($res_rq_info_config);
	if($total_ligne_rq_info_config!=0){
		do {
		$CHANGEMENT_LISTE_CONFIG_ID=$tab_rq_info_config['CHANGEMENT_LISTE_CONFIG_ID'];
		$CHANGEMENT_LISTE_CONFIG_LIB=$tab_rq_info_config['CHANGEMENT_LISTE_CONFIG_LIB'];
		$CHANGEMENT_LISTE_CONFIG_TYPE=$tab_rq_info_config['CHANGEMENT_LISTE_CONFIG_TYPE'];
		$CHANGEMENT_LISTE_CONFIG_OBLIGATOIRE=$tab_rq_info_config['CHANGEMENT_LISTE_CONFIG_OBLIGATOIRE'];
		$CHANGEMENT_LISTE_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_LISTE_CONFIG_TABLE'];
		if(isset($tab_rq_info_config['CHANGEMENT_LISTE_INFO_AUTRE_ID'])){
      			$CHANGEMENT_LISTE_INFO_AUTRE_ID=$tab_rq_info_config['CHANGEMENT_LISTE_INFO_AUTRE_ID'];
		}
		if(isset($tab_rq_info_config['CHANGEMENT_LISTE_INFO_LIB'])){
      			$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID]=$tab_rq_info_config['CHANGEMENT_LISTE_INFO_LIB'];
		}
		if(!isset($CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID])){$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID]='';}
		$LISTE_CONFIG='CHANGEMENT_LISTE_CONFIG_ID_'.$CHANGEMENT_LISTE_CONFIG_ID;
		
		      switch ($CHANGEMENT_LISTE_CONFIG_TYPE)
            {
            case "liste": 
              $CHANGEMENT_LISTE_INFO_AUTRE_ID=0;
              $CHANGEMENT_LISTE_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_LISTE_CONFIG_TABLE'];
              $CHANGEMENT_LISTE_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE);
              $LISTE_CONFIG='CHANGEMENT_LISTE_CONFIG_ID_'.$CHANGEMENT_LISTE_CONFIG_ID;
              $CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
              $CHANGEMENT_LISTE_INFO_AUTRE_ID=$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID];
              $rq_info="
              SELECT 
              `CHANGEMENT_LISTE_INFO_ID`
              FROM `changement_liste_info`
              WHERE 
              `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."' 
              AND `CHANGEMENT_LISTE_CONFIG_ID` ='".$CHANGEMENT_LISTE_CONFIG_ID."' 
              AND `CHANGEMENT_LISTE_INFO_AUTRE_ID` ='".$CHANGEMENT_LISTE_INFO_AUTRE_ID."' 
              AND `CHANGEMENT_LISTE_INFO_LIB` =''  
              AND `ENABLE` ='0'
              LIMIT 1";
              
              $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
              $tab_rq_info = mysql_fetch_assoc($res_rq_info);
              $total_ligne_rq_info=mysql_num_rows($res_rq_info);
              mysql_free_result($res_rq_info);
              if($total_ligne_rq_info==0){
                $MAJ_ID=$MAJ_ID+1;
                $sql="
                UPDATE `changement_liste_info` SET 
                `CHANGEMENT_LISTE_INFO_AUTRE_ID`= '".$CHANGEMENT_LISTE_INFO_AUTRE_ID."'
                WHERE `CHANGEMENT_LISTE_ID` ='".$ID."'
                AND `CHANGEMENT_LISTE_CONFIG_ID`='".$CHANGEMENT_LISTE_CONFIG_ID."'
                AND `ENABLE`='0'
                LIMIT 1 ;";
                mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
                $TABLE_SQL_SQL='changement_liste_info';       
                historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');

                $sql="OPTIMIZE TABLE `changement_liste_info` ";
                mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
              }
            break;
///////////////////////////////////////////   
case "checkbox": 
              $CHANGEMENT_LISTE_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_LISTE_CONFIG_TABLE'];
              $CHANGEMENT_LISTE_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE);
              $rq_info_config_table="
              SELECT  *
              FROM `".$CHANGEMENT_LISTE_CONFIG_TABLE."`
              WHERE 
              `ENABLE`='0'
              ORDER BY `".$CHANGEMENT_LISTE_CONFIG_TABLE_TRI."`";
              $res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
              $tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
              $total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);
              if($total_ligne_rq_info_config_table!=0){
                do {
                  $ID_SQL=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE).'_ID';
                  $ID_LIB=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE);
                  $COM_SQL=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE).'_COM';
                  
                  $CHANGEMENT_LISTE_CONFIG_TABLE_ID=$tab_rq_info_config_table[$ID_SQL];
                  $CHANGEMENT_LISTE_CONFIG_TABLE_LIB=$tab_rq_info_config_table[$ID_LIB];
                  $CHANGEMENT_LISTE_INFO_AUTRE_ID=$CHANGEMENT_LISTE_CONFIG_TABLE_ID;
                  if(isset($tab_rq_info_config_table[$COM_SQL])){
                  	$CHANGEMENT_LISTE_CONFIG_TABLE_COM=$tab_rq_info_config_table[$COM_SQL];	
                  }else{
                  	$CHANGEMENT_LISTE_CONFIG_TABLE_COM='non';
                  }
	              $LISTE_CONFIG='CHANGEMENT_LISTE_CONFIG_ID_'.$CHANGEMENT_LISTE_CONFIG_ID.'_'.$CHANGEMENT_LISTE_CONFIG_TABLE_ID;
	              $CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE=$CHANGEMENT_LISTE_CONFIG_ID.'_'.$CHANGEMENT_LISTE_CONFIG_TABLE_ID;
	              if(!isset($NB_checkbox)){$NB_checkbox=0;}
	              if(isset($tab_var[$LISTE_CONFIG])){
	              	$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
	              	$NB_checkbox=$NB_checkbox+1;
	              }else{
	              	$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE]='off';
	              }

	              if($CHANGEMENT_LISTE_CONFIG_TABLE_COM=='oui'){
		              $LISTE_CONFIG='CHANGEMENT_LISTE_CONFIG_ID_'.$CHANGEMENT_LISTE_CONFIG_ID.'_'.$CHANGEMENT_LISTE_CONFIG_TABLE_ID.'_COM';
		              $CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM=$CHANGEMENT_LISTE_CONFIG_ID.'_'.$CHANGEMENT_LISTE_CONFIG_TABLE_ID.'_COM';
		              if(isset($tab_var[$LISTE_CONFIG])){
		              	$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
		              	
		              }else{
		              	$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM]='';
		              }
	              }

	              

	              if($CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE]=='on'){
	              	      $rq_info="
		              SELECT 
		              `CHANGEMENT_LISTE_INFO_LIB`
		              FROM `changement_liste_info`
		              WHERE 
		              `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."' 
		              AND `CHANGEMENT_LISTE_CONFIG_ID` ='".$CHANGEMENT_LISTE_CONFIG_ID."' 
		              AND `CHANGEMENT_LISTE_INFO_AUTRE_ID` ='".$CHANGEMENT_LISTE_INFO_AUTRE_ID."' 
		              AND `ENABLE` ='0'
		              LIMIT 1";
		              $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
		              $tab_rq_info = mysql_fetch_assoc($res_rq_info);
		              $total_ligne_rq_info=mysql_num_rows($res_rq_info);
		              mysql_free_result($res_rq_info);
		              if($total_ligne_rq_info==0){
		              	$MAJ_ID=$MAJ_ID+1;
		              	$sql="INSERT INTO `changement_liste_info` (`CHANGEMENT_LISTE_INFO_ID` ,`CHANGEMENT_LISTE_ID` ,`CHANGEMENT_LISTE_CONFIG_ID` ,`CHANGEMENT_LISTE_INFO_AUTRE_ID` ,`CHANGEMENT_LISTE_INFO_LIB`,`ENABLE` )VALUES (NULL , '".$CHANGEMENT_ID."', '".$CHANGEMENT_LISTE_CONFIG_ID."', '".$CHANGEMENT_LISTE_INFO_AUTRE_ID."', '".$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM]."', '0');";
				mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
				$TABLE_SQL_SQL='changement_liste_info';       
				historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');
		              }else{
		                $TEST_CHANGEMENT_LISTE_INFO_LIB=$tab_rq_info['CHANGEMENT_LISTE_INFO_LIB'];
		                if($TEST_CHANGEMENT_LISTE_INFO_LIB!=$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM]){
		                	$MAJ_ID=$MAJ_ID+1;
					$sql="UPDATE `changement_liste_info` SET `CHANGEMENT_LISTE_INFO_LIB` = '".$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM]."' 
					WHERE `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."' 
					AND `CHANGEMENT_LISTE_CONFIG_ID` ='".$CHANGEMENT_LISTE_CONFIG_ID."'
					AND `CHANGEMENT_LISTE_INFO_AUTRE_ID` ='".$CHANGEMENT_LISTE_INFO_AUTRE_ID."'
					AND `ENABLE`='0'
					LIMIT 1 ;";
					mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
					$TABLE_SQL_SQL='changement_liste_info';       
					historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');
		                }
		              }
	              }else{
	              	      $rq_info="
		              SELECT 
		              `CHANGEMENT_LISTE_INFO_LIB`
		              FROM `changement_liste_info`
		              WHERE 
		              `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."' 
		              AND `CHANGEMENT_LISTE_CONFIG_ID` ='".$CHANGEMENT_LISTE_CONFIG_ID."' 
		              AND `CHANGEMENT_LISTE_INFO_AUTRE_ID` ='".$CHANGEMENT_LISTE_INFO_AUTRE_ID."' 
		              AND `ENABLE` ='0'
		              LIMIT 1";
		              $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
		              $tab_rq_info = mysql_fetch_assoc($res_rq_info);
		              $total_ligne_rq_info=mysql_num_rows($res_rq_info);
		              mysql_free_result($res_rq_info);
		              if($total_ligne_rq_info!=0){
		              	$MAJ_ID=$MAJ_ID+1;
		              	$sql="
		              	UPDATE `changement_liste_info` SET `ENABLE` = '1' 
	              		WHERE `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."' 
				AND `CHANGEMENT_LISTE_CONFIG_ID` ='".$CHANGEMENT_LISTE_CONFIG_ID."'
				AND `CHANGEMENT_LISTE_INFO_AUTRE_ID` ='".$CHANGEMENT_LISTE_INFO_AUTRE_ID."'
				AND `ENABLE`='0'
				LIMIT 1 ;";
				mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
				$TABLE_SQL_SQL='changement_liste_info';       
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
              $CHANGEMENT_LISTE_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_LISTE_CONFIG_TABLE'];
              $CHANGEMENT_LISTE_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE);
              $rq_info_config_table="
              SELECT  *
              FROM `".$CHANGEMENT_LISTE_CONFIG_TABLE."`
              WHERE 
              `ENABLE`='0'
              ORDER BY `".$CHANGEMENT_LISTE_CONFIG_TABLE_TRI."`";
              $res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
              $tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
              $total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);
              if($total_ligne_rq_info_config_table!=0){
                do {
                  $ID_SQL=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE).'_ID';
                  $ID_LIB=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE);
                  $COM_SQL=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE).'_COM';
                  
                  $CHANGEMENT_LISTE_CONFIG_TABLE_ID=$tab_rq_info_config_table[$ID_SQL];
                  $CHANGEMENT_LISTE_CONFIG_TABLE_LIB=$tab_rq_info_config_table[$ID_LIB];
                  $CHANGEMENT_LISTE_INFO_AUTRE_ID=$CHANGEMENT_LISTE_CONFIG_TABLE_ID;
                  if(isset($tab_rq_info_config_table[$COM_SQL])){
                  	$CHANGEMENT_LISTE_CONFIG_TABLE_COM=$tab_rq_info_config_table[$COM_SQL];	
                  }else{
                  	$CHANGEMENT_LISTE_CONFIG_TABLE_COM='non';
                  }
	              $LISTE_CONFIG='CHANGEMENT_LISTE_CONFIG_ID_'.$CHANGEMENT_LISTE_CONFIG_ID.'_'.$CHANGEMENT_LISTE_CONFIG_TABLE_ID;
	              $CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE=$CHANGEMENT_LISTE_CONFIG_ID.'_'.$CHANGEMENT_LISTE_CONFIG_TABLE_ID;
	              if(!isset($NB_checkbox)){$NB_checkbox=0;}
	              if(isset($tab_var[$LISTE_CONFIG])){
	              	$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
	              	$NB_checkbox=$NB_checkbox+1;
	              }else{
	              	$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE]='off';
	              }

	              if($CHANGEMENT_LISTE_CONFIG_TABLE_COM=='oui'){
		              $LISTE_CONFIG='CHANGEMENT_LISTE_CONFIG_ID_'.$CHANGEMENT_LISTE_CONFIG_ID.'_'.$CHANGEMENT_LISTE_CONFIG_TABLE_ID.'_COM';
		              $CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM=$CHANGEMENT_LISTE_CONFIG_ID.'_'.$CHANGEMENT_LISTE_CONFIG_TABLE_ID.'_COM';
		              if(isset($tab_var[$LISTE_CONFIG])){
		              	$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));	
		              	
		              }else{
		              	$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM]='';
		              }
	              }

	              

	              if($CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE]=='on'){
	              	      $rq_info="
		              SELECT 
		              `CHANGEMENT_LISTE_INFO_LIB`
		              FROM `changement_liste_info`
		              WHERE 
		              `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."' 
		              AND `CHANGEMENT_LISTE_CONFIG_ID` ='".$CHANGEMENT_LISTE_CONFIG_ID."' 
		              AND `CHANGEMENT_LISTE_INFO_AUTRE_ID` ='".$CHANGEMENT_LISTE_INFO_AUTRE_ID."' 
		              AND `ENABLE` ='0'
		              LIMIT 1";
		              $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
		              $tab_rq_info = mysql_fetch_assoc($res_rq_info);
		              $total_ligne_rq_info=mysql_num_rows($res_rq_info);
		              mysql_free_result($res_rq_info);
		              if($total_ligne_rq_info==0){
		              	$MAJ_ID=$MAJ_ID+1;
		              	$sql="INSERT INTO `changement_liste_info` (`CHANGEMENT_LISTE_INFO_ID` ,`CHANGEMENT_LISTE_ID` ,`CHANGEMENT_LISTE_CONFIG_ID` ,`CHANGEMENT_LISTE_INFO_AUTRE_ID` ,`CHANGEMENT_LISTE_INFO_LIB`,`ENABLE` )VALUES (NULL , '".$CHANGEMENT_ID."', '".$CHANGEMENT_LISTE_CONFIG_ID."', '".$CHANGEMENT_LISTE_INFO_AUTRE_ID."', '".$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM]."', '0');";
				mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
				$TABLE_SQL_SQL='changement_liste_info';       
				historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');
		              }else{
		                $TEST_CHANGEMENT_LISTE_INFO_LIB=$tab_rq_info['CHANGEMENT_LISTE_INFO_LIB'];
		                if(isset($CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM)){
		                if(isset($CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM])){
		                if($TEST_CHANGEMENT_LISTE_INFO_LIB!=$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM]){
		                	$MAJ_ID=$MAJ_ID+1;
					$sql="UPDATE `changement_liste_info` SET `CHANGEMENT_LISTE_INFO_LIB` = '".$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM]."' 
					WHERE `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."' 
					AND `CHANGEMENT_LISTE_CONFIG_ID` ='".$CHANGEMENT_LISTE_CONFIG_ID."'
					AND `CHANGEMENT_LISTE_INFO_AUTRE_ID` ='".$CHANGEMENT_LISTE_INFO_AUTRE_ID."'
					AND `ENABLE`='0'
					LIMIT 1 ;";
					mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
					$TABLE_SQL_SQL='changement_liste_info';       
					historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');
		                }
		                }
		        	}
		              }
	              }else{
	              	      $rq_info="
		              SELECT 
		              `CHANGEMENT_LISTE_INFO_LIB`
		              FROM `changement_liste_info`
		              WHERE 
		              `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."' 
		              AND `CHANGEMENT_LISTE_CONFIG_ID` ='".$CHANGEMENT_LISTE_CONFIG_ID."' 
		              AND `CHANGEMENT_LISTE_INFO_AUTRE_ID` ='".$CHANGEMENT_LISTE_INFO_AUTRE_ID."' 
		              AND `ENABLE` ='0'
		              LIMIT 1";
		              $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
		              $tab_rq_info = mysql_fetch_assoc($res_rq_info);
		              $total_ligne_rq_info=mysql_num_rows($res_rq_info);
		              mysql_free_result($res_rq_info);
		              if($total_ligne_rq_info!=0){
		              	$MAJ_ID=$MAJ_ID+1;
		              	$sql="
		              	UPDATE `changement_liste_info` SET `ENABLE` = '1' 
	              		WHERE `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."' 
				AND `CHANGEMENT_LISTE_CONFIG_ID` ='".$CHANGEMENT_LISTE_CONFIG_ID."'
				AND `CHANGEMENT_LISTE_INFO_AUTRE_ID` ='".$CHANGEMENT_LISTE_INFO_AUTRE_ID."'
				AND `ENABLE`='0'
				LIMIT 1 ;";
				mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
				$TABLE_SQL_SQL='changement_liste_info';       
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
////////////////////////////////////////////
            case "varchar": 
              $CHANGEMENT_LISTE_INFO_AUTRE_ID=0;
              $CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
              $rq_info="
              SELECT 
              `CHANGEMENT_LISTE_INFO_ID`
              FROM `changement_liste_info`
              WHERE 
              `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."' 
              AND `CHANGEMENT_LISTE_CONFIG_ID` ='".$CHANGEMENT_LISTE_CONFIG_ID."' 
              AND `CHANGEMENT_LISTE_INFO_AUTRE_ID` ='".$CHANGEMENT_LISTE_INFO_AUTRE_ID."' 
              AND `CHANGEMENT_LISTE_INFO_LIB` ='".$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID]."'  
              AND `ENABLE` ='0'
              LIMIT 1";
              $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
              $tab_rq_info = mysql_fetch_assoc($res_rq_info);
              $total_ligne_rq_info=mysql_num_rows($res_rq_info);
              mysql_free_result($res_rq_info);
              if($total_ligne_rq_info==0){
                $MAJ_ID=$MAJ_ID+1;
                $sql="
                UPDATE `changement_liste_info` SET 
                `CHANGEMENT_LISTE_INFO_LIB`= '".$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID]."'
                WHERE `CHANGEMENT_LISTE_ID` ='".$ID."'
                AND `CHANGEMENT_LISTE_CONFIG_ID`='".$CHANGEMENT_LISTE_CONFIG_ID."'
                AND `CHANGEMENT_LISTE_INFO_AUTRE_ID`='".$CHANGEMENT_LISTE_INFO_AUTRE_ID."'
                AND `ENABLE`='0'
                LIMIT 1 ;";
                mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
                $TABLE_SQL_SQL='changement_liste_info';       
                historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');

              }
              break;
              
            case "text": 
              $CHANGEMENT_LISTE_INFO_AUTRE_ID=0;
              $CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID]=addslashes(trim(htmlentities($tab_var[$LISTE_CONFIG])));
              $rq_info="
              SELECT 
              `CHANGEMENT_LISTE_INFO_ID`
              FROM `changement_liste_info`
              WHERE 
              `CHANGEMENT_LISTE_ID` ='".$CHANGEMENT_ID."' 
              AND `CHANGEMENT_LISTE_CONFIG_ID` ='".$CHANGEMENT_LISTE_CONFIG_ID."' 
              AND `CHANGEMENT_LISTE_INFO_AUTRE_ID` ='".$CHANGEMENT_LISTE_INFO_AUTRE_ID."' 
              AND `CHANGEMENT_LISTE_INFO_LIB` ='".$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID]."'  
              AND `ENABLE` ='0'
              LIMIT 1";
              $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
              $tab_rq_info = mysql_fetch_assoc($res_rq_info);
              $total_ligne_rq_info=mysql_num_rows($res_rq_info);
              mysql_free_result($res_rq_info);
              if($total_ligne_rq_info==0){
                $MAJ_ID=$MAJ_ID+1;
                $sql="
                UPDATE `changement_liste_info` SET 
                `CHANGEMENT_LISTE_INFO_LIB`= '".$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID]."'
                WHERE `CHANGEMENT_LISTE_ID` ='".$ID."'
                AND `CHANGEMENT_LISTE_CONFIG_ID`='".$CHANGEMENT_LISTE_CONFIG_ID."'
                AND `CHANGEMENT_LISTE_INFO_AUTRE_ID`='".$CHANGEMENT_LISTE_INFO_AUTRE_ID."'
                AND `ENABLE`='0'
                LIMIT 1 ;";
                mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
                $TABLE_SQL_SQL='changement_liste_info';       
                historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');

                
              }
            break;
            }
            $sql="OPTIMIZE TABLE `changement_liste_info` ";
            mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
            $TABLE_SQL_SQL='changement_liste_info';       
            historique_sql_new($sql,$TABLE_SQL_SQL,'OPTIMIZE');
            
		
		 } while ($tab_rq_info_config = mysql_fetch_assoc($res_rq_info_config));
	        $ligne= mysql_num_rows($res_rq_info_config);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_config, 0);
	          $tab_rq_info_config = mysql_fetch_assoc($res_rq_info_config);
	        }
	        
	}
	mysql_free_result($res_rq_info_config);  
	if($MAJ_ID!=0){
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
        	$TRACE_CATEGORIE='Changement';
	        $TRACE_TABLE='changement_liste';
	        $TRACE_REF_ID=$CHANGEMENT_ID;
	        $TRACE_ACTION='Modif';
	        $TRACE_ETAT=$CHANGEMENT_STATUS_ID;
	        moteur_trace($TRACE_CATEGORIE,$TRACE_TABLE,$TRACE_REF_ID,$TRACE_ACTION,$TRACE_ETAT);
		}

        $MOIS_Inter_Debut=substr($Date_Inter_Debut_bdd,4,2);
        $ANNEE_Inter_Debut=substr($Date_Inter_Debut_bdd,0,4);


        echo '
        <script language="JavaScript">
        url=("./index.php?ITEM=changement_Info_Changement&action=Info&ID='.$ID.'");
        window.location=url;
        </script>
        ';
        
     } 
    $_GET['action']="Modif";

  
  }

}

# Cas action ajout
if($_GET['action']=="Ajout"){
	
	if(isset($_POST['txt_HEURE_PLANIF_DEBUT_H'])){
		$HEURE_PLANIF_DEBUT_H=$_POST['txt_HEURE_PLANIF_DEBUT_H'];
	}else{
		$HEURE_PLANIF_DEBUT_H=date("H");
	}
	if(isset($_POST['txt_HEURE_PLANIF_DEBUT_M'])){
		$HEURE_PLANIF_DEBUT_M=$_POST['txt_HEURE_PLANIF_DEBUT_M'];
	}else{
		$HEURE_PLANIF_DEBUT_M=date("i");
	}
	if(isset($_POST['txt_HEURE_PLANIF_FIN_H'])){
		$HEURE_PLANIF_FIN_H=$_POST['txt_HEURE_PLANIF_FIN_H'];
	}else{
		$HEURE_PLANIF_FIN_H=date("H");
	}
	if(isset($_POST['txt_HEURE_PLANIF_FIN_M'])){
		$HEURE_PLANIF_FIN_M=$_POST['txt_HEURE_PLANIF_FIN_M'];
	}else{
		$HEURE_PLANIF_FIN_M=date("i");
	}
	if(isset($_POST['type_changement'])){
		$CHANGEMENT_DEMANDE_ID=$_POST['type_changement'];
	}
	if(isset($_POST['txt_Date_Inter_Fin'])){
		$Date_Inter_Fin=$_POST['txt_Date_Inter_Fin'];
	}else{
		$Date_Inter_Fin=date("d/m/Y");
	}
	if(isset($_POST['txt_Date_Inter_Debut'])){
		$Date_Inter_Debut=$_POST['txt_Date_Inter_Debut'];
		$MOIS_Inter_Debut=substr($Date_Inter_Debut,3,2);
		$ANNEE_Inter_Debut=substr($Date_Inter_Debut,6,4);
	}else{
		if(isset($_GET['ANNEE'])){
			$ANNEE_Inter_Debut=$_GET['ANNEE'];
		}else{
			$ANNEE_Inter_Debut=date("Y");
		}
		if(isset($_GET['MOIS'])){
			$MOIS_Inter_Debut=$_GET['MOIS'];
			if(strlen($MOIS_Inter_Debut) == 1 ){
				$MOIS_Inter_Debut='0'.$MOIS_Inter_Debut;
			}
		}else{
			$MOIS_Inter_Debut=date("m");
		}
		if(isset($_GET['JOUR'])){
			$JOUR_Inter_Debut=$_GET['JOUR'];
			if(strlen($JOUR_Inter_Debut) == 1 ){
				$JOUR_Inter_Debut='0'.$JOUR_Inter_Debut;
			}
		}else{
			$JOUR_Inter_Debut=date("d");
		}
		$Date_Inter_Debut=$JOUR_Inter_Debut.'/'.$MOIS_Inter_Debut.'/'.$ANNEE_Inter_Debut;
		$Date_Inter_Fin=$JOUR_Inter_Debut.'/'.$MOIS_Inter_Debut.'/'.$ANNEE_Inter_Debut;
	}

}

# Cas action modifer
if($_GET['action']=="Modif" || $_GET['action']=="Info"){
  $rq_info_id="
  SELECT `changement_status`.`CHANGEMENT_STATUS` 
  FROM `changement_liste` , `changement_status` 
  WHERE `changement_liste`.`CHANGEMENT_LISTE_ID` = '".$ID."'
  AND `changement_liste`.`CHANGEMENT_STATUS_ID` = `changement_status`.`CHANGEMENT_STATUS_ID` 
  AND `changement_liste`.`ENABLE` = '0' 
  LIMIT 1";
  $res_rq_info_id = mysql_query($rq_info_id, $mysql_link) or die(mysql_error());
  $tab_rq_info_id = mysql_fetch_assoc($res_rq_info_id);
  $total_ligne_rq_info_id=mysql_num_rows($res_rq_info_id);

  $CHANGEMENT_STATUS=$tab_rq_info_id['CHANGEMENT_STATUS'];	
  mysql_free_result($res_rq_info_id);
  $rq_info_id="
  SELECT `CHANGEMENT_COMPTE_RENDU_ID` 
  FROM `changement_compte_rendu` 
  WHERE `CHANGEMENT_LISTE_ID`='".$ID."' 
  AND `ENABLE`='0'";
  $res_rq_info_id = mysql_query($rq_info_id, $mysql_link) or die(mysql_error());
  $tab_rq_info_id = mysql_fetch_assoc($res_rq_info_id);
  $total_ligne_rq_info_id=mysql_num_rows($res_rq_info_id);
  if($total_ligne_rq_info_id==0){
    $action_cr='Ajout';
  }else{
    if($CHANGEMENT_STATUS!='Clotur&eacute;'){
      $action_cr='Modif';
    }else{
      $action_cr='Info';
    }
  }
  mysql_free_result($res_rq_info_id);

	$rq_far_info="
	SELECT `changement_status`.`CHANGEMENT_STATUS`
	FROM `changement_liste`,`changement_status`
	WHERE 
	`changement_liste`.`CHANGEMENT_STATUS_ID`=`changement_status`.`CHANGEMENT_STATUS_ID` AND
	`changement_liste`.`CHANGEMENT_LISTE_ID` ='".$ID."' AND
	`changement_liste`.`ENABLE` = '0'
	";
	$res_rq_far_info = mysql_query($rq_far_info, $mysql_link) or die(mysql_error());
	$tab_rq_far_info = mysql_fetch_assoc($res_rq_far_info);
	$total_ligne_rq_far_info=mysql_num_rows($res_rq_far_info);
	$CHANGEMENT_STATUS=$tab_rq_far_info['CHANGEMENT_STATUS'];
	mysql_free_result($res_rq_far_info);
    switch ($CHANGEMENT_STATUS){
		case "Brouillon": 
      $rq_far_info="
      SELECT `CHANGEMENT_FAR_ID`
      FROM `changement_far`
      WHERE `CHANGEMENT_LISTE_ID` ='".$ID."' 
      AND `ENABLE` = '0'
      ";
      $res_rq_far_info = mysql_query($rq_far_info, $mysql_link) or die(mysql_error());
      $tab_rq_far_info = mysql_fetch_assoc($res_rq_far_info);
      $total_ligne_rq_far_info=mysql_num_rows($res_rq_far_info);
      mysql_free_result($res_rq_far_info);
      $FAR_action='Ajout';
      $FAR_lib='Cr&eacute;ation de la Fiche d\'Analyse de Risques';
      $FAR_ITEM='changement_Ajout_FAR';
      if($action=="Info"){
	      $FAR_action='';
	      $FAR_lib='';
	      $FAR_ITEM='';
      }
      if($total_ligne_rq_far_info > 1){
        $FAR_action='Modif';
        $FAR_lib='Modification de la Fiche d\'Analyse de Risques';
        $FAR_ITEM='changement_Modif_FAR';
        if($action=="Info"){
		$FAR_action='Info';
		$FAR_lib='Voir la Fiche d\'Analyse de Risques';
		$FAR_ITEM='changement_Info_FAR';
        }
      }
      
		break;
		case "Inscrit": 
      if($ROLE==0){
        $FAR_action='Modif';
        $FAR_lib='Modification de la Fiche d\'Analyse de Risques';
        $FAR_ITEM='changement_Modif_FAR';
        if($action=="Info"){
		$FAR_action='Info';
		$FAR_lib='Voir la Fiche d\'Analyse de Risques';
		$FAR_ITEM='changement_Info_FAR';
        }
      }else{
        $FAR_action='Info';
        $FAR_lib='Voir la Fiche d\'Analyse de Risques';
        $FAR_ITEM='changement_Info_FAR';
      }
		break;
		
		case "Valid&eacute;": 
			if($ROLE==0){
        $FAR_action='Modif';
        $FAR_lib='Modification de la Fiche d\'Analyse de Risques';
        $FAR_ITEM='changement_Modif_FAR';
        if($action=="Info"){
		$FAR_action='Info';
		$FAR_lib='Voir la Fiche d\'Analyse de Risques';
		$FAR_ITEM='changement_Info_FAR';
        }
      }else{
        $FAR_action='Info';
        $FAR_lib='Voir la Fiche d\'Analyse de Risques';
        $FAR_ITEM='changement_Info_FAR';
      }
		break;

		case "Termin&eacute;": 
        $FAR_action='Info';
        $FAR_lib='Voir la Fiche d\'Analyse de Risques';
        $FAR_ITEM='changement_Info_FAR';
		break;
		case "Clotur&eacute;": 
      $FAR_action='Info';
      $FAR_lib='Voir la Fiche d\'Analyse de Risques';
      $FAR_ITEM='changement_Info_FAR';
		break;
		case "Abandonn&eacute;": 
      if($action=="Info"){
	      $FAR_action='';
	      $FAR_lib='';
	      $FAR_ITEM='';
      }
      if($total_ligne_rq_far_info > 1){
		$FAR_action='Info';
		$FAR_lib='Voir la Fiche d\'Analyse de Risques';
		$FAR_ITEM='changement_Info_FAR';

      }
		break;
		
		}

		$ID=$_GET['ID'];
		$rq_info="
		SELECT 
		`changement_liste`.`CHANGEMENT_LISTE_ID`, 
		`changement_liste`.`CHANGEMENT_LISTE_UTILISATEUR_ID`, 
		`changement_liste`.`CHANGEMENT_LISTE_DATE_DEBUT`, 
		`changement_liste`.`CHANGEMENT_LISTE_DATE_FIN`, 
		`changement_liste`.`CHANGEMENT_LISTE_HEURE_DEBUT`, 
		`changement_liste`.`CHANGEMENT_LISTE_HEURE_FIN`, 
		`changement_liste`.`CHANGEMENT_LISTE_DATE_MODIFICATION`, 
		`changement_liste`.`CHANGEMENT_LISTE_LIB`, 
		`changement_liste`.`CHANGEMENT_STATUS_ID`, 
		`changement_status`.`CHANGEMENT_STATUS`,
		`changement_status`.`CHANGEMENT_STATUS_COULEUR_FOND`,
		`changement_status`.`CHANGEMENT_STATUS_COULEUR_TEXT`,
		`changement_liste`.`CHANGEMENT_DEMANDE_ID`,
		`changement_liste`.`ENABLE` 
		FROM `changement_liste`,`changement_status` 
		WHERE `changement_liste`.`CHANGEMENT_STATUS_ID`=`changement_status`.`CHANGEMENT_STATUS_ID`
		AND `changement_liste`.`CHANGEMENT_LISTE_ID` ='".$ID."'
		AND `changement_liste`.`ENABLE` ='0'
		LIMIT 1";
		$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
		$tab_rq_info = mysql_fetch_assoc($res_rq_info);
		$total_ligne_rq_info=mysql_num_rows($res_rq_info);
		if($total_ligne_rq_info!=0){
			$CHANGEMENT_ID=$tab_rq_info['CHANGEMENT_LISTE_ID'];
			$ID=$tab_rq_info['CHANGEMENT_LISTE_ID'];
			$CHANGEMENT_STATUS=$tab_rq_info['CHANGEMENT_STATUS'];
			$CHANGEMENT_STATUS_COULEUR_FOND=$tab_rq_info['CHANGEMENT_STATUS_COULEUR_FOND'];
			$CHANGEMENT_STATUS_COULEUR_TEXT=$tab_rq_info['CHANGEMENT_STATUS_COULEUR_TEXT'];
			$CHANGEMENT_DEMANDE_ID=$tab_rq_info['CHANGEMENT_DEMANDE_ID'];
			
			$CHANGEMENT_LISTE_DATE_DEBUT=$tab_rq_info['CHANGEMENT_LISTE_DATE_DEBUT'];
			$JOUR_Inter_Debut=substr($CHANGEMENT_LISTE_DATE_DEBUT,6,2);
			$MOIS_Inter_Debut=substr($CHANGEMENT_LISTE_DATE_DEBUT,4,2);
			$ANNEE_Inter_Debut=substr($CHANGEMENT_LISTE_DATE_DEBUT,0,4);
			$Date_Inter_Debut=$JOUR_Inter_Debut.'/'.$MOIS_Inter_Debut.'/'.$ANNEE_Inter_Debut;
			
			$CHANGEMENT_LISTE_DATE_FIN=$tab_rq_info['CHANGEMENT_LISTE_DATE_FIN'];
			$JOUR_Inter_Fin=substr($CHANGEMENT_LISTE_DATE_FIN,6,2);
			$MOIS_Inter_Fin=substr($CHANGEMENT_LISTE_DATE_FIN,4,2);
			$ANNEE_Inter_Fin=substr($CHANGEMENT_LISTE_DATE_FIN,0,4);
			$Date_Inter_Fin=$JOUR_Inter_Fin.'/'.$MOIS_Inter_Fin.'/'.$ANNEE_Inter_Fin;
			
			$CHANGEMENT_LISTE_HEURE_DEBUT=$tab_rq_info['CHANGEMENT_LISTE_HEURE_DEBUT'];
			$HEURE_PLANIF_DEBUT_H=substr($CHANGEMENT_LISTE_HEURE_DEBUT,0,2);
			$HEURE_PLANIF_DEBUT_M=substr($CHANGEMENT_LISTE_HEURE_DEBUT,3,2);
			
			$CHANGEMENT_LISTE_HEURE_FIN=$tab_rq_info['CHANGEMENT_LISTE_HEURE_FIN'];
			$HEURE_PLANIF_FIN_H=substr($CHANGEMENT_LISTE_HEURE_FIN,0,2);
			$HEURE_PLANIF_FIN_M=substr($CHANGEMENT_LISTE_HEURE_FIN,3,2);
			
			$lib=$tab_rq_info['CHANGEMENT_LISTE_LIB'];
			//$CHANGEMENT_STATUS_ID=$tab_rq_info['CHANGEMENT_STATUS_ID'];
		
		}
		mysql_free_result($res_rq_info);

}

echo '
<!--D&eacute;but page HTML -->  
<div align="center">
<form method="post" name="frm_inter" id="frm_inter" action="./index.php?ITEM='.$_GET['ITEM'].'">
  <table class="table_inc">';
    if($action=="Ajout"){
      echo '
      <tr align="center" class="titre">
        <td colspan="4"><h2>&nbsp;[&nbsp;Ajout d\'un changement&nbsp;]&nbsp;</h2></td>		
      </tr>';
    }
    if($action=="Modif"||$action=="Info"){
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
	AND `moteur_trace`.`MOTEUR_TRACE_REF_ID`='".$ID."'
	AND `moteur_trace`.`MOTEUR_TRACE_ETAT`=`changement_status`.`CHANGEMENT_STATUS_ID` 
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
    	echo '
      <tr align="center" class="titre">
        <td colspan="4"><h2>&nbsp;[&nbsp;';
        if($action=='Modif'){
    	if($CHANGEMENT_STATUS=='Brouillon'){
        echo 'Modification du Changement n&deg; <a href="#" class="infobulledroite">'.$ID.'<span>'.$AFF_SPAN.'</span></a>';
      }else{
        if($CHANGEMENT_STATUS=='Abandonn&eacute;'){
          echo 'Information sur le Changement n&deg; <a href="#" class="infobulledroite">'.$ID.'<span>'.$AFF_SPAN.'</span></a>';
        }else{
          if($ROLE==0){
            echo 'Modification du Changement n&deg; <a href="#" class="infobulledroite">'.$ID.'<span>'.$AFF_SPAN.'</span></a>';
          }else{
            echo 'Information sur le Changement n&deg; <a href="#" class="infobulledroite">'.$ID.'<span>'.$AFF_SPAN.'</span></a>';
          }
        }
      }
      }
      if($action=='Info'){
        echo 'Information sur le Changement n&deg; <a href="#" class="infobulledroite">'.$ID.'<span>'.$AFF_SPAN.'</span></a>';
      }
      echo '&nbsp;]&nbsp;</h2></td>
      </tr>';

      
    }
    if($action!='Ajout'){
    $j++;
    if ($j%2) { $class = "pair";}else{$class = "impair";} 
    echo '
    <tr class="'.$class.'">
     <td align="center" colspan="4">';
     $rq_info_liste_status="
     SELECT `CHANGEMENT_STATUS_ID`, `CHANGEMENT_STATUS` 
     FROM `changement_status`
     WHERE `ENABLE`='0' AND `CHANGEMENT_STATUS` NOT IN ('Abandonn&eacute;','ReInscription')
     ORDER BY `CHANGEMENT_STATUS_ORDRE`,`CHANGEMENT_STATUS`";
    $res_rq_info_liste_status = mysql_query($rq_info_liste_status, $mysql_link) or die(mysql_error());
    $tab_rq_info_liste_status = mysql_fetch_assoc($res_rq_info_liste_status);
    $total_ligne_rq_info_liste_status=mysql_num_rows($res_rq_info_liste_status);
    if($total_ligne_rq_info_liste_status!=0){
    echo '
    <table>
    <tr>';
    $LISTE_CHANGEMENT_STATUS_COULEUR_FOND='20EA01';
    $LISTE_CHANGEMENT_STATUS_COULEUR_TEXT='000000';
      $NB_LISTE_CHANGEMENT_STATUS_ID=0;
      $LISTE_CHANGEMENT_STATUS_ID_STOP=0;
      do {
      $LISTE_CHANGEMENT_STATUS_ID=$tab_rq_info_liste_status['CHANGEMENT_STATUS_ID'];
      $LISTE_CHANGEMENT_STATUS=$tab_rq_info_liste_status['CHANGEMENT_STATUS'];
      
      if($CHANGEMENT_STATUS=='Abandonn&eacute;'){
        $LISTE_CHANGEMENT_STATUS_COULEUR_FOND='FE0000';
        $LISTE_CHANGEMENT_STATUS_COULEUR_TEXT='FFFFFF';
      }else{
        if($LISTE_CHANGEMENT_STATUS_ID_STOP==1){
          $LISTE_CHANGEMENT_STATUS_COULEUR_FOND='F8F9F8';
          $LISTE_CHANGEMENT_STATUS_COULEUR_TEXT='000000';
        }else{
          $LISTE_CHANGEMENT_STATUS_COULEUR_FOND='20EA01';
          $LISTE_CHANGEMENT_STATUS_COULEUR_TEXT='000000';
        }
      }

      if($NB_LISTE_CHANGEMENT_STATUS_ID==0){
        echo '<td bgcolor="#'.$LISTE_CHANGEMENT_STATUS_COULEUR_FOND.'"><FONT COLOR="#'.$LISTE_CHANGEMENT_STATUS_COULEUR_TEXT.'">&nbsp;'.$LISTE_CHANGEMENT_STATUS.'&nbsp;</FONT></td>';
      }else{
        echo '<td bgcolor="#'.$LISTE_CHANGEMENT_STATUS_COULEUR_FOND.'"><FONT COLOR="#'.$LISTE_CHANGEMENT_STATUS_COULEUR_TEXT.'">&nbsp;->&nbsp;'.$LISTE_CHANGEMENT_STATUS.'&nbsp;</FONT></td>';
      }
      if($CHANGEMENT_STATUS==$LISTE_CHANGEMENT_STATUS){
        $LISTE_CHANGEMENT_STATUS_ID_STOP=1;
      }
      $NB_LISTE_CHANGEMENT_STATUS_ID=$NB_LISTE_CHANGEMENT_STATUS_ID+1;
       } while ($tab_rq_info_liste_status = mysql_fetch_assoc($res_rq_info_liste_status));
            $ligne= mysql_num_rows($res_rq_info_liste_status);
            if($ligne > 0) {
              mysql_data_seek($res_rq_info_liste_status, 0);
              $tab_rq_info_liste_status = mysql_fetch_assoc($res_rq_info_liste_status);
            }
    echo '
    </tr>
    </table>';
    }
    mysql_free_result($res_rq_info_liste_status);
     echo '
    </td>
   </tr>';
    }
    $j++;
    if ($j%2) { $class = "pair";}else{$class = "impair";} 
  echo '
    <tr class="'.$class.'">
     <td align="left">&nbsp;Date de D&eacute;but&nbsp;*&nbsp;</td>
     <td align="left">';
     
     if($action!='Info'){
     	echo '<input name="txt_Date_Inter_Debut" type="text" readonly value="'.$Date_Inter_Debut.'" size="10"/>';
     ?>
     <a href="#" onClick=" window.open('./cf/calendrier/pop.php?frm=frm_inter&amp;ch=txt_Date_Inter_Debut','calendrier','width=350,height=160,scrollbars=0').focus();">
     <img src="./cf/calendrier/petit_calendrier.png" border="0"/></a><?PHP 
	}else{
	echo datebdd_nomjour($Date_Inter_Debut).' '.$Date_Inter_Debut;
       }
     echo '
    </td>
    <td align="left">&nbsp;Heure de D&eacute;but&nbsp;*&nbsp;</td>
    <td align="left">';
    if($action!='Info'){
    	echo '
     <input name="txt_HEURE_PLANIF_DEBUT_H" '.$readonly_var.' type="text" id="txt_HEURE_PLANIF_DEBUT_H" size="2" maxlength="2" value="'.$HEURE_PLANIF_DEBUT_H.'"> h
     <input name="txt_HEURE_PLANIF_DEBUT_M" '.$readonly_var.' type="text" id="txt_HEURE_PLANIF_DEBUT_M" size="2" maxlength="2" value="'.$HEURE_PLANIF_DEBUT_M.'">';
     }else{
	echo $HEURE_PLANIF_DEBUT_H.' h '.$HEURE_PLANIF_DEBUT_M.'';
	}
     if($HEURE_PLANIF_DEBUT_LISTE==1){ echo '<br/><font color=#993333><b>Heure du type 12h30</b></font>';}
     echo '
    </td>
   </tr>';
	$j++;
    	if ($j%2) { $class = "pair";}else{$class = "impair";} 
	echo '
   <tr class="'.$class.'">
    <td align="left">&nbsp;Date de fin&nbsp;*&nbsp;</td>
    <td align="left">';
    if($action!='Info'){
     echo '<input name="txt_Date_Inter_Fin" type="text" readonly value="'.$Date_Inter_Fin.'" size="10"/>';
     ?>
     <a href="#" onClick=" window.open('./cf/calendrier/pop.php?frm=frm_inter&amp;ch=txt_Date_Inter_Fin','calendrier','width=350,height=160,scrollbars=0').focus();">
     <img src="./cf/calendrier/petit_calendrier.png" border="0"/></a><?PHP
	}else{
	echo datebdd_nomjour($Date_Inter_Fin).' '.$Date_Inter_Fin;
       }
      echo '
    </td>
    <td align="left">&nbsp;Heure de Fin&nbsp;*&nbsp;</td>
    <td align="left">';
    if($action!='Info'){
    	echo '
     <input name="txt_HEURE_PLANIF_FIN_H" '.$readonly_var.' type="text" id="txt_HEURE_PLANIF_FIN_H" size="2" maxlength="2" value="'.$HEURE_PLANIF_FIN_H.'"> h
     <input name="txt_HEURE_PLANIF_FIN_M" '.$readonly_var.' type="text" id="txt_HEURE_PLANIF_FIN_M" size="2" maxlength="2" value="'.$HEURE_PLANIF_FIN_M.'">';
	}else{
	echo $HEURE_PLANIF_FIN_H.' h '.$HEURE_PLANIF_FIN_M.'';
	}
     if($HEURE_PLANIF_FIN_LISTE==1){ echo '<br/><font color=#993333><b>Heure du type 12h30</b></font>';} 
     echo '
    </td>
   </tr>';
   
   if($DATE_LISTE==1){
   	$j++;
	   if ($j%2) { $class = "pair";}else{$class = "impair";} 
	   echo '
	   <tr class="'.$class.'">
	    <td align="center" colspan="4"><font color=#993333><b>Erreurs Sur les dates</b></font>
	    </td>
	   </tr>';
   }
   if($DATE_LISTE==2){
   	$j++;
	   if ($j%2) { $class = "pair";}else{$class = "impair";} 
	   echo '
	   <tr class="'.$class.'">
	    <td align="center" colspan="4"><font color=#993333><b>la date de d&eacute;but est inférieur &agrave; la date du jour.</b></font>
	    </td>
	   </tr>';
   }
   if($HEURE_LISTE==1){
   	$j++;
	   if ($j%2) { $class = "pair";}else{$class = "impair";} 
	   echo '
	   <tr class="'.$class.'">
	    <td align="center" colspan="4"><font color=#993333><b>Attention, l\'heure de fin est identique &agrave; l\'heure de d&eacute;but.</b></font>
	    </td>
	   </tr>';
   }
   $j++;
   if ($j%2) { $class = "pair";}else{$class = "impair";} 
   echo '
   <tr class="'.$class.'">
	<td align="left">&nbsp;Type de Changement&nbsp;*&nbsp;</td>
	<td align="left" colspan="3">';
	if($_GET['action']=="Info"){
		if($total_ligne_rq_Selectionner_demande_type!=0){
                	do {	
                	if($tab_rq_Selectionner_demande_type['CHANGEMENT_DEMANDE_ID'] == $CHANGEMENT_DEMANDE_ID ){echo stripslashes($tab_rq_Selectionner_demande_type['CHANGEMENT_DEMANDE_LIB']);}
			} while ($tab_rq_Selectionner_demande_type = mysql_fetch_assoc($res_rq_Selectionner_demande_type));
			$ligne= mysql_num_rows($res_rq_Selectionner_demande_type);
			if($ligne > 0) {
			mysql_data_seek($res_rq_Selectionner_demande_type, 0);
			$tab_rq_Selectionner_demande_type = mysql_fetch_assoc($res_rq_Selectionner_demande_type);
			}
                  }      
                }else{
	echo '
	<Select name="CHANGEMENT_DEMANDE_ID" size="1" id="CHANGEMENT_DEMANDE_ID" onChange="">
	<option value="0">&nbsp;</option>';
	if($total_ligne_rq_Selectionner_demande_type!=0){
    do {
    $AFF_SPAN_type.=stripslashes($tab_rq_Selectionner_demande_type['CHANGEMENT_DEMANDE_LIB']).':</BR>';
    $AFF_SPAN_type.=stripslashes($tab_rq_Selectionner_demande_type['CHANGEMENT_DEMANDE_EXEMPLE']).'</BR></BR>';
    echo '<option value="'.$tab_rq_Selectionner_demande_type['CHANGEMENT_DEMANDE_ID'].'"';
    if (empty($_POST)){}else{
      if($_POST['btn']=="Ajouter"){
        if($tab_rq_Selectionner_demande_type['CHANGEMENT_DEMANDE_ID'] == $CHANGEMENT_DEMANDE_ID ){echo " SELECTED ";}
      }
      if ($_POST['btn']=="Modifier"){
        if($tab_rq_Selectionner_demande_type['CHANGEMENT_DEMANDE_ID'] == $CHANGEMENT_DEMANDE_ID ){echo " SELECTED ";}
      }
    }
    if (empty($_GET)){}else{
      if(isset($_GET['action'])){
        if($_GET['action']=="Modif"){
          if($tab_rq_Selectionner_demande_type['CHANGEMENT_DEMANDE_ID'] == $CHANGEMENT_DEMANDE_ID ){echo " SELECTED ";}
        }
        if($_GET['action']=="Info"){
          if($tab_rq_Selectionner_demande_type['CHANGEMENT_DEMANDE_ID'] == $CHANGEMENT_DEMANDE_ID ){echo " SELECTED ";}
        }
      }
    }
    echo '>'.stripslashes($tab_rq_Selectionner_demande_type['CHANGEMENT_DEMANDE_LIB']).'</option>';
    } while ($tab_rq_Selectionner_demande_type = mysql_fetch_assoc($res_rq_Selectionner_demande_type));
      $ligne= mysql_num_rows($res_rq_Selectionner_demande_type);
      if($ligne > 0) {
          mysql_data_seek($res_rq_Selectionner_demande_type, 0);
        $tab_rq_Selectionner_demande_type = mysql_fetch_assoc($res_rq_Selectionner_demande_type);
      }
}
  echo '
  </Select>&nbsp;<a href="#" class="infobullegauche"><font color=#000000>&nbsp;- Aide&nbsp;</font><span>'.$AFF_SPAN_type.'</span></a>
  ';
 }
  echo '
	</td>
	</tr>';
	if($STOP_type==1){
   	$j++;
	   if ($j%2) { $class = "pair";}else{$class = "impair";} 
	   echo '
	   <tr class="'.$class.'">
	    <td align="center" colspan="4"><font color=#993333><b>Le typde de demande est vide.</b></font>
	    </td>
	   </tr>';
   }
   $j++;
   if ($j%2) { $class = "pair";}else{$class = "impair";} 
   echo '
   <tr class="'.$class.'">
    <td align="left">&nbsp;Titre du Changement&nbsp;*&nbsp;</td>
    <td align="left" colspan="3">';
    if($action!='Info'){
     echo '
    <input id="txt_lib" name="txt_lib" '.$readonly_var.' type="text" value="'.stripslashes($lib).'" size="67" maxlength="100"/>';
	}else{
	echo stripslashes($lib);
	}
    echo '
    </td>
   </tr>';
   if($STOP_lib==1){
   	$j++;
	   if ($j%2) { $class = "pair";}else{$class = "impair";} 
	   echo '
	   <tr class="'.$class.'">
	    <td align="center" colspan="4"><font color=#993333><b>Le libell&eacute; est vide.</b></font>
	    </td>
	   </tr>';
   }

   $rq_info_config="
	 SELECT  
	 `CHANGEMENT_LISTE_CONFIG_ID`,
	 `CHANGEMENT_LISTE_CONFIG_LIB`, 
	 `CHANGEMENT_LISTE_CONFIG_TYPE`, 
	 `CHANGEMENT_LISTE_CONFIG_OBLIGATOIRE`,
	 `CHANGEMENT_LISTE_CONFIG_TABLE`
	 FROM `changement_liste_config` 
	 WHERE `ENABLE`='0'
	 ORDER BY `CHANGEMENT_LISTE_CONFIG_ORDRE`";
	if($action!='Ajout'){
   $rq_info_config="
	 SELECT  
  `changement_liste_config`.`CHANGEMENT_LISTE_CONFIG_ID`,
  `changement_liste_config`.`CHANGEMENT_LISTE_CONFIG_LIB`, 
  `changement_liste_config`.`CHANGEMENT_LISTE_CONFIG_TYPE`, 
  `changement_liste_config`.`CHANGEMENT_LISTE_CONFIG_OBLIGATOIRE`,
  `changement_liste_config`.`CHANGEMENT_LISTE_CONFIG_TABLE` ,
  `changement_liste_info`.`CHANGEMENT_LISTE_INFO_AUTRE_ID`,
  `changement_liste_info`.`CHANGEMENT_LISTE_INFO_LIB`
	 FROM `changement_liste_config`,`changement_liste_info`
	 WHERE 
  `changement_liste_config`.`CHANGEMENT_LISTE_CONFIG_ID`=`changement_liste_info`.`CHANGEMENT_LISTE_CONFIG_ID`
  AND `changement_liste_info`.`CHANGEMENT_LISTE_ID`='".$ID."'
  GROUP BY `changement_liste_config`.`CHANGEMENT_LISTE_CONFIG_ID`
  ORDER BY `changement_liste_config`.`CHANGEMENT_LISTE_CONFIG_ORDRE`";
	}
	$res_rq_info_config = mysql_query($rq_info_config, $mysql_link) or die(mysql_error());
	$tab_rq_info_config = mysql_fetch_assoc($res_rq_info_config);
	$total_ligne_rq_info_config=mysql_num_rows($res_rq_info_config);
	//echo '</BR>'.$rq_info_config;
	if($total_ligne_rq_info_config!=0){
		do {
		$CHANGEMENT_LISTE_CONFIG_ID=$tab_rq_info_config['CHANGEMENT_LISTE_CONFIG_ID'];
		$CHANGEMENT_LISTE_CONFIG_LIB=$tab_rq_info_config['CHANGEMENT_LISTE_CONFIG_LIB'];
		$CHANGEMENT_LISTE_CONFIG_TYPE=$tab_rq_info_config['CHANGEMENT_LISTE_CONFIG_TYPE'];
		$CHANGEMENT_LISTE_CONFIG_OBLIGATOIRE=$tab_rq_info_config['CHANGEMENT_LISTE_CONFIG_OBLIGATOIRE'];
		
		if(isset($tab_rq_info_config['CHANGEMENT_LISTE_INFO_AUTRE_ID'])){
      			$CHANGEMENT_LISTE_INFO_AUTRE_ID=$tab_rq_info_config['CHANGEMENT_LISTE_INFO_AUTRE_ID'];
		}else{
			$CHANGEMENT_LISTE_INFO_AUTRE_ID=0;
		}
		if(isset($tab_rq_info_config['CHANGEMENT_LISTE_INFO_LIB'])){
      			$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID]=$tab_rq_info_config['CHANGEMENT_LISTE_INFO_LIB'];
		}
		if(!isset($CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID])){$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID]='';}
	       $info_OBLIGATOIRE='';
	       if($CHANGEMENT_LISTE_CONFIG_OBLIGATOIRE=='oui'){$info_OBLIGATOIRE='*';}
	       $j++;
	       if ($j%2) { $class = "pair";}else{$class = "impair";} 
		  echo '
		    <tr class="'.$class.'">
		     <td align="left">&nbsp;'.stripslashes($CHANGEMENT_LISTE_CONFIG_LIB).'&nbsp;'.$info_OBLIGATOIRE.'&nbsp;</td>
		     <td align="left" colspan="3">';
		      switch ($CHANGEMENT_LISTE_CONFIG_TYPE)
            {
            case "liste": 
              $CHANGEMENT_LISTE_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_LISTE_CONFIG_TABLE'];
              $CHANGEMENT_LISTE_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE);
              $rq_info_config_table="
              SELECT  *
              FROM `".$CHANGEMENT_LISTE_CONFIG_TABLE."`
              WHERE 
              `ENABLE`='0'
              AND `".$CHANGEMENT_LISTE_CONFIG_TABLE_TRI."`!='vide'
              ORDER BY `".$CHANGEMENT_LISTE_CONFIG_TABLE_TRI."`";
              $res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
              $tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
              $total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);
              if($action!='Ajout'){
              $rq_info_config_table_info="
              SELECT  `CHANGEMENT_LISTE_INFO_AUTRE_ID`
              FROM `changement_liste_info`
              WHERE 
              `CHANGEMENT_LISTE_ID`='".$ID."'
              AND `CHANGEMENT_LISTE_CONFIG_ID`='".$CHANGEMENT_LISTE_CONFIG_ID."'
              AND `ENABLE`='0'
              LIMIT 1";
              //echo $rq_info_config_table_info;
              $res_rq_info_config_table_info = mysql_query($rq_info_config_table_info, $mysql_link) or die(mysql_error());
              $tab_rq_info_config_table_info = mysql_fetch_assoc($res_rq_info_config_table_info);
              $total_ligne_rq_info_config_table_info=mysql_num_rows($res_rq_info_config_table_info);
              if($total_ligne_rq_info_config_table_info!=0){
                $CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID]=$tab_rq_info_config_table_info['CHANGEMENT_LISTE_INFO_AUTRE_ID'];
              }else{
                $CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID]='';
              }
              
              mysql_free_result($res_rq_info_config_table_info);
        	}
        	if($_GET['action']=="Info"){
        		if($total_ligne_rq_info_config_table!=0){
                	do {
				$ID_SQL=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE).'_ID';
				$ID_LIB=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE);
				$CHANGEMENT_LISTE_CONFIG_TABLE_ID=$tab_rq_info_config_table[$ID_SQL];
				$CHANGEMENT_LISTE_CONFIG_TABLE_LIB=$tab_rq_info_config_table[$ID_LIB];
                	if($CHANGEMENT_LISTE_CONFIG_TABLE_ID == $CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID] ){echo $CHANGEMENT_LISTE_CONFIG_TABLE_LIB;}	
                	} while ($tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table));
	                $ligne= mysql_num_rows($res_rq_info_config_table);
	                if($ligne > 0) {
	                  mysql_data_seek($res_rq_info_config_table, 0);
	                  $tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
	                }
                }
                }else{
              echo '
              <Select name="CHANGEMENT_LISTE_CONFIG_ID_'.$CHANGEMENT_LISTE_CONFIG_ID.'" size="1" id="CHANGEMENT_LISTE_CONFIG_ID_'.$CHANGEMENT_LISTE_CONFIG_ID.'" onChange="">
              <option value="0">&nbsp;</option>';
              if($total_ligne_rq_info_config_table!=0){
                do {
                  $ID_SQL=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE).'_ID';
                  $ID_LIB=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE);
                  $CHANGEMENT_LISTE_CONFIG_TABLE_ID=$tab_rq_info_config_table[$ID_SQL];
                  $CHANGEMENT_LISTE_CONFIG_TABLE_LIB=$tab_rq_info_config_table[$ID_LIB];
                  echo '<option value="'.$CHANGEMENT_LISTE_CONFIG_TABLE_ID.'"';
                  if(!isset($CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID])){
                    $CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID]='';
                  }
                  if (empty($_POST)){}else{
                    if($_POST['btn']=="Ajouter"){
                      if($CHANGEMENT_LISTE_CONFIG_TABLE_ID == $CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID]){echo " SELECTED ";}
                    }
                    if ($_POST['btn']=="Modifier"){
                      if($CHANGEMENT_LISTE_CONFIG_TABLE_ID == $CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID] ){echo " SELECTED ";}
                    }
                  }
                  if (empty($_GET)){}else{
                    if(isset($_GET['action'])){
                      if($_GET['action']=="Modif"){
                        if($CHANGEMENT_LISTE_CONFIG_TABLE_ID == $CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID] ){echo " SELECTED ";}
                      }
                      if($_GET['action']=="Info"){
                        if($CHANGEMENT_LISTE_CONFIG_TABLE_ID == $CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID] ){echo " SELECTED ";}
                      }
                    }
                  }
                  echo '>'.stripslashes($CHANGEMENT_LISTE_CONFIG_TABLE_LIB).'</option>';
                
                } while ($tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table));
                $ligne= mysql_num_rows($res_rq_info_config_table);
                if($ligne > 0) {
                  mysql_data_seek($res_rq_info_config_table, 0);
                  $tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
                }
              }
              echo '
              </Select>';
              }
              mysql_free_result($res_rq_info_config_table);
              
            break;
            
            case "checkbox_horizontal": 
              $CHANGEMENT_LISTE_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_LISTE_CONFIG_TABLE'];
              $CHANGEMENT_LISTE_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE);
              $rq_info_config_table="
              SELECT  *
              FROM `".$CHANGEMENT_LISTE_CONFIG_TABLE."`
              WHERE 
              `ENABLE`='0'
              AND `".$CHANGEMENT_LISTE_CONFIG_TABLE_TRI."`!='vide'
              
              ORDER BY `".$CHANGEMENT_LISTE_CONFIG_TABLE_TRI."`";
              $res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
              $tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
              $total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);

              if($total_ligne_rq_info_config_table!=0){
                do {
                  $ID_SQL=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE).'_ID';
                  $ID_LIB=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE);
                  $COM_SQL=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE).'_COM';
                  $CHANGEMENT_LISTE_CONFIG_TABLE_ID=$tab_rq_info_config_table[$ID_SQL];
                  $CHANGEMENT_LISTE_CONFIG_TABLE_LIB=$tab_rq_info_config_table[$ID_LIB];
                  $CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE=$CHANGEMENT_LISTE_CONFIG_ID.'_'.$CHANGEMENT_LISTE_CONFIG_TABLE_ID;
                  	$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM=$CHANGEMENT_LISTE_CONFIG_ID.'_'.$CHANGEMENT_LISTE_CONFIG_TABLE_ID.'_COM';
                  if($action!='Ajout'){

                  	$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE]='off';
                  	$rq_info_config_liste_id="
                  	SELECT `CHANGEMENT_LISTE_INFO_AUTRE_ID` , `CHANGEMENT_LISTE_INFO_LIB` 
			FROM `changement_liste_info` 
			WHERE 
			`CHANGEMENT_LISTE_ID` = '".$ID."'
			AND `CHANGEMENT_LISTE_CONFIG_ID` = '".$CHANGEMENT_LISTE_CONFIG_ID."'
			AND `CHANGEMENT_LISTE_INFO_AUTRE_ID`='".$CHANGEMENT_LISTE_CONFIG_TABLE_ID."'
			AND `ENABLE` = '0'
			LIMIT 1";
			$res_rq_info_config_liste_id = mysql_query($rq_info_config_liste_id, $mysql_link) or die(mysql_error());
			$tab_rq_info_config_liste_id = mysql_fetch_assoc($res_rq_info_config_liste_id);
			$total_ligne_rq_info_config_liste_id=mysql_num_rows($res_rq_info_config_liste_id);
			
			if($total_ligne_rq_info_config_liste_id==0){
				$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE]='off';
				$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM]='';
			}else{
				$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE]='on';
				$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM]=$tab_rq_info_config_liste_id['CHANGEMENT_LISTE_INFO_LIB'];
			}
			mysql_free_result($res_rq_info_config_liste_id);
                  }
                  if(!isset($CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM])){
                  	$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM]='';
                  }
                  if(!isset($CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE])){
                  	$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE]='';
                  }

                  
                  if(isset($tab_rq_info_config_table[$COM_SQL])){
                  	$CHANGEMENT_LISTE_CONFIG_TABLE_COM=$tab_rq_info_config_table[$COM_SQL];	
                  }else{
                  	$CHANGEMENT_LISTE_CONFIG_TABLE_COM='non';
                  }

                  echo '<INPUT TYPE="CHECKBOX" '.$disabled_var.' NAME="CHANGEMENT_LISTE_CONFIG_ID_'.$CHANGEMENT_LISTE_CONFIG_ID.'_'.$CHANGEMENT_LISTE_CONFIG_TABLE_ID.'"';
                  if($CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE]=='on'){echo ' checked';}
                  echo '>'.$CHANGEMENT_LISTE_CONFIG_TABLE_LIB.'';
                  if($CHANGEMENT_LISTE_CONFIG_TABLE_COM=='oui'){
                  	if($action!='Info'){
                  	echo '&nbsp;&nbsp;<input '.$readonly_var.' id="CHANGEMENT_LISTE_CONFIG_ID_'.$CHANGEMENT_LISTE_CONFIG_ID.'_'.$CHANGEMENT_LISTE_CONFIG_TABLE_ID.'_COM" name="CHANGEMENT_LISTE_CONFIG_ID_'.$CHANGEMENT_LISTE_CONFIG_ID.'_'.$CHANGEMENT_LISTE_CONFIG_TABLE_ID.'_COM" type="hidden" value="'.stripslashes($CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM]).'" size="50" maxlength="100"/>&nbsp;*';
                	}else{
                	echo stripslashes($CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM]);
                	}
                  }
                 // echo '</BR>';
                
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
              FROM `".$CHANGEMENT_LISTE_CONFIG_TABLE."`
              WHERE 
              `ENABLE`='0'
              AND `".$CHANGEMENT_LISTE_CONFIG_TABLE_TRI."`='vide'
              ORDER BY `".$CHANGEMENT_LISTE_CONFIG_TABLE_TRI."`";
              $res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
              $tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
              $total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);

              if($total_ligne_rq_info_config_table!=0){
                do {
                  $ID_SQL=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE).'_ID';
                  $ID_LIB=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE);
                  $CHANGEMENT_LISTE_CONFIG_TABLE_ID=$tab_rq_info_config_table[$ID_SQL];
                  echo '<input type="hidden" name="CHANGEMENT_LISTE_CONFIG_ID_'.$CHANGEMENT_LISTE_CONFIG_ID.'_'.$CHANGEMENT_LISTE_CONFIG_TABLE_ID.'" value="on">';
                
                } while ($tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table));
                $ligne= mysql_num_rows($res_rq_info_config_table);
                if($ligne > 0) {
                  mysql_data_seek($res_rq_info_config_table, 0);
                  $tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
                }
              }
              mysql_free_result($res_rq_info_config_table);
              
              
            break;
            case "checkbox": 
              $CHANGEMENT_LISTE_CONFIG_TABLE=$tab_rq_info_config['CHANGEMENT_LISTE_CONFIG_TABLE'];
              $CHANGEMENT_LISTE_CONFIG_TABLE_TRI=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE);
              $rq_info_config_table="
              SELECT  *
              FROM `".$CHANGEMENT_LISTE_CONFIG_TABLE."`
              WHERE 
              `ENABLE`='0'
              AND `".$CHANGEMENT_LISTE_CONFIG_TABLE_TRI."`!='vide'
              
              ORDER BY `".$CHANGEMENT_LISTE_CONFIG_TABLE_TRI."`";
              $res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
              $tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
              $total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);

              if($total_ligne_rq_info_config_table!=0){
                do {
                  $ID_SQL=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE).'_ID';
                  $ID_LIB=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE);
                  $COM_SQL=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE).'_COM';
                  $CHANGEMENT_LISTE_CONFIG_TABLE_ID=$tab_rq_info_config_table[$ID_SQL];
                  $CHANGEMENT_LISTE_CONFIG_TABLE_LIB=$tab_rq_info_config_table[$ID_LIB];
                  $CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE=$CHANGEMENT_LISTE_CONFIG_ID.'_'.$CHANGEMENT_LISTE_CONFIG_TABLE_ID;
                  	$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM=$CHANGEMENT_LISTE_CONFIG_ID.'_'.$CHANGEMENT_LISTE_CONFIG_TABLE_ID.'_COM';
                  if($action!='Ajout'){

                  	$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE]='off';
                  	$rq_info_config_liste_id="
                  	SELECT `CHANGEMENT_LISTE_INFO_AUTRE_ID` , `CHANGEMENT_LISTE_INFO_LIB` 
			FROM `changement_liste_info` 
			WHERE 
			`CHANGEMENT_LISTE_ID` = '".$ID."'
			AND `CHANGEMENT_LISTE_CONFIG_ID` = '".$CHANGEMENT_LISTE_CONFIG_ID."'
			AND `CHANGEMENT_LISTE_INFO_AUTRE_ID`='".$CHANGEMENT_LISTE_CONFIG_TABLE_ID."'
			AND `ENABLE` = '0'
			LIMIT 1";
			$res_rq_info_config_liste_id = mysql_query($rq_info_config_liste_id, $mysql_link) or die(mysql_error());
			$tab_rq_info_config_liste_id = mysql_fetch_assoc($res_rq_info_config_liste_id);
			$total_ligne_rq_info_config_liste_id=mysql_num_rows($res_rq_info_config_liste_id);
			
			if($total_ligne_rq_info_config_liste_id==0){
				$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE]='off';
				$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM]='';
			}else{
				$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE]='on';
				$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM]=$tab_rq_info_config_liste_id['CHANGEMENT_LISTE_INFO_LIB'];
			}
			mysql_free_result($res_rq_info_config_liste_id);
                  }
                  if(!isset($CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM])){
                  	$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM]='';
                  }
                  if(!isset($CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE])){
                  	$CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE]='';
                  }

                  
                  if(isset($tab_rq_info_config_table[$COM_SQL])){
                  	$CHANGEMENT_LISTE_CONFIG_TABLE_COM=$tab_rq_info_config_table[$COM_SQL];	
                  }else{
                  	$CHANGEMENT_LISTE_CONFIG_TABLE_COM='non';
                  }

                  echo '<INPUT TYPE="CHECKBOX" '.$disabled_var.' NAME="CHANGEMENT_LISTE_CONFIG_ID_'.$CHANGEMENT_LISTE_CONFIG_ID.'_'.$CHANGEMENT_LISTE_CONFIG_TABLE_ID.'"';
                  if($CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE]=='on'){echo ' checked';}
                  echo '>'.$CHANGEMENT_LISTE_CONFIG_TABLE_LIB.'';
                  if($CHANGEMENT_LISTE_CONFIG_TABLE_COM=='oui'){
                  	if($action!='Info'){
                  	echo '&nbsp;&nbsp;<input '.$readonly_var.' id="CHANGEMENT_LISTE_CONFIG_ID_'.$CHANGEMENT_LISTE_CONFIG_ID.'_'.$CHANGEMENT_LISTE_CONFIG_TABLE_ID.'_COM" name="CHANGEMENT_LISTE_CONFIG_ID_'.$CHANGEMENT_LISTE_CONFIG_ID.'_'.$CHANGEMENT_LISTE_CONFIG_TABLE_ID.'_COM" type="text" value="'.stripslashes($CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM]).'" size="50" maxlength="100"/>&nbsp;*';
                	}else{
                	echo stripslashes($CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID_ID_AUTRE_COM]);
                	}
                  }
                  echo '</BR>';
                
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
              FROM `".$CHANGEMENT_LISTE_CONFIG_TABLE."`
              WHERE 
              `ENABLE`='0'
              AND `".$CHANGEMENT_LISTE_CONFIG_TABLE_TRI."`='vide'
              ORDER BY `".$CHANGEMENT_LISTE_CONFIG_TABLE_TRI."`";
              $res_rq_info_config_table = mysql_query($rq_info_config_table, $mysql_link) or die(mysql_error());
              $tab_rq_info_config_table = mysql_fetch_assoc($res_rq_info_config_table);
              $total_ligne_rq_info_config_table=mysql_num_rows($res_rq_info_config_table);

              if($total_ligne_rq_info_config_table!=0){
                do {
                  $ID_SQL=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE).'_ID';
                  $ID_LIB=strtoupper($CHANGEMENT_LISTE_CONFIG_TABLE);
                  $CHANGEMENT_LISTE_CONFIG_TABLE_ID=$tab_rq_info_config_table[$ID_SQL];
                  echo '<input type="hidden" name="CHANGEMENT_LISTE_CONFIG_ID_'.$CHANGEMENT_LISTE_CONFIG_ID.'_'.$CHANGEMENT_LISTE_CONFIG_TABLE_ID.'" value="on">';
                
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
    if($action!='Info'){
              echo '<input '.$readonly_var.' id="CHANGEMENT_LISTE_CONFIG_ID_'.$CHANGEMENT_LISTE_CONFIG_ID.'" name="CHANGEMENT_LISTE_CONFIG_ID_'.$CHANGEMENT_LISTE_CONFIG_ID.'" type="text" value="'.stripslashes($CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID]).'" size="67" maxlength="100"/>';
        }else{
        echo stripslashes($CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID]);
}
              break;
              
            case "text": 
            if($action!='Info'){
              echo '<textarea '.$readonly_var.' id="CHANGEMENT_LISTE_CONFIG_ID_'.$CHANGEMENT_LISTE_CONFIG_ID.'" name="CHANGEMENT_LISTE_CONFIG_ID_'.$CHANGEMENT_LISTE_CONFIG_ID.'" cols="50" rows="2">'.stripslashes($CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID]).'</textarea>';
        }else{
        echo nl2br(stripslashes($CHANGEMENT_LISTE_CONFIG[$CHANGEMENT_LISTE_CONFIG_ID]));
	}
            break;
            }
		     echo '
		     </td>
		   </tr>';
		   if(isset($CHANGEMENT_LISTE_CONFIG_STOP[$CHANGEMENT_LISTE_CONFIG_ID])){
        if($CHANGEMENT_LISTE_CONFIG_STOP[$CHANGEMENT_LISTE_CONFIG_ID]==1){
          $j++;
         if ($j%2) { $class = "pair";}else{$class = "impair";} 
         echo '
         <tr class="'.$class.'">
          <td align="center" colspan="4"><font color=#993333><b>'.stripslashes($CHANGEMENT_LISTE_CONFIG_LIB).' obligatoire est vide.</b></font>
          </td>
         </tr>';
        }
        
		   }
		   if(isset($CHANGEMENT_LISTE_CONFIG_STOP_COM[$CHANGEMENT_LISTE_CONFIG_ID])){
	        if($CHANGEMENT_LISTE_CONFIG_STOP_COM[$CHANGEMENT_LISTE_CONFIG_ID]==1){
	          $j++;
	         if ($j%2) { $class = "pair";}else{$class = "impair";} 
	         echo '
	         <tr class="'.$class.'">
	          <td align="center" colspan="4"><font color=#993333><b>Un Commentaire obligatoire '.stripslashes($CHANGEMENT_LISTE_CONFIG_LIB).' est vide.</b></font>
	          </td>
	         </tr>';
	        }
        
		   }
		
		
		 } while ($tab_rq_info_config = mysql_fetch_assoc($res_rq_info_config));
	        $ligne= mysql_num_rows($res_rq_info_config);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_config, 0);
	          $tab_rq_info_config = mysql_fetch_assoc($res_rq_info_config);
	        }
	}
	mysql_free_result($res_rq_info_config);

   
   
   if($action=='Modif'||$action=="Info"){
	   $j++;
	   if ($j%2) { $class = "pair";}else{$class = "impair";} 
	   echo '
	   <tr class="'.$class.'">
	    <td align="left" bgcolor="#'.$CHANGEMENT_STATUS_COULEUR_FOND.'"><FONT COLOR="#'.$CHANGEMENT_STATUS_COULEUR_TEXT.'">&nbsp;Status&nbsp;</FONT></td>
	    <td align="left" colspan="3" bgcolor="#'.$CHANGEMENT_STATUS_COULEUR_FOND.'"><FONT COLOR="#'.$CHANGEMENT_STATUS_COULEUR_TEXT.'">&nbsp;'.$CHANGEMENT_STATUS.'&nbsp;</FONT></td>
	   </tr>';
	   if($CHANGEMENT_STATUS=='Inscrit'){
	   $j++;
	   if ($j%2) { $class = "pair";}else{$class = "impair";} 
	   echo '
	   <tr class="'.$class.'">
	    <td align="center" colspan="4"><b><FONT COLOR="#FF0000">&nbsp;Le changement est en cours de Validation.&nbsp;</FONT></b></td>
	   </tr>';
	   }
	   if($FAR_ITEM!=''){
	   	if($FAR_action=="Info"){
	   $j++;
	   if ($j%2) { $class = "pair";}else{$class = "impair";} 
	   echo '
	   <tr class="'.$class.'">
	    <td align="left">&nbsp;FAR&nbsp;</td>
	    <td align="left" colspan="3"><a class="LinkDef" href="./index.php?ITEM='.$FAR_ITEM.'&action='.$FAR_action.'&ID='.$ID.'">&nbsp;'.$FAR_lib.'&nbsp;</a></td>
	   </tr>';
		}
	   }
  }
  switch ($CHANGEMENT_STATUS){
		case "Brouillon": 
		break;
		
		case "Inscrit": 
		break;
		
		case "ReInscrition": 
		break;
		
		case "Valid&eacute;": 

			if($ROLE==0){
				if ( $ENV == "x" ){
				    	if($CHANGEMENT_LISTE_DATE_FIN<=$DATE_DU_JOUR){
						$Bilan_lib='Faire la Fiche Bilan';
						$Bilan_action='Ajout';
						$Bilan_ITEM='changement_Ajout_Bilan';
						if($action=="Info"){
							$Bilan_lib='';
							$Bilan_action='';
							$Bilan_ITEM='';
					        }

				      		switch ($action_cr){
                  case "Ajout": 
                    $CR_lib='Faire la Fiche compte rendu';
                    $CR_action='Ajout';
                    $CR_ITEM='changement_Ajout_CompteRendu';
                    if($action=="Info"){
				$CR_lib='';
				$CR_action='';
				$CR_ITEM='';
		        }
                  break;
                  case "Modif": 
                    $CR_lib='Modifier la Fiche compte rendu';
                    $CR_action='Modif';
                    $CR_ITEM='changement_Modif_CompteRendu';
                    if($action=="Info"){
				$CR_lib='Voir la Fiche compte rendu';
				$CR_action='Info';
				$CR_ITEM='changement_Info_CompteRendu';
		        }
                  break;
                  case "Info": 
                    $CR_lib='Voir la Fiche compte rendu';
                    $CR_action='Info';
                    $CR_ITEM='changement_Info_CompteRendu';
                  break;
                  }
				      		
				    	}
				}else{
					$Bilan_lib='Faire la Fiche Bilan';
					$Bilan_action='Ajout';
					$Bilan_ITEM='changement_Ajout_Bilan';
					if($action=="Info"){
						$Bilan_lib='';
						$Bilan_action='';
						$Bilan_ITEM='';
				        }
			    switch ($action_cr){
          case "Ajout": 
            $CR_lib='Faire la Fiche compte rendu';
            $CR_action='Ajout';
            $CR_ITEM='changement_Ajout_CompteRendu';
            if($action=="Info"){
			$CR_lib='';
			$CR_action='';
			$CR_ITEM='';
	        }
          break;
          case "Modif": 
            $CR_lib='Modifier la Fiche compte rendu';
            $CR_action='Modif';
            $CR_ITEM='changement_Modif_CompteRendu';
            if($action=="Info"){
			$CR_lib='Voir la Fiche compte rendu';
			$CR_action='Info';
			$CR_ITEM='changement_Info_CompteRendu';
	        }
          break;
          case "Info": 
            $CR_lib='Voir la Fiche compte rendu';
            $CR_action='Info';
            $CR_ITEM='changement_Info_CompteRendu';
          break;
          }
				}
			    	
			}else{
				if($CHANGEMENT_LISTE_DATE_FIN<=$DATE_DU_JOUR){
			      	$Bilan_lib='Faire la Fiche Bilan';
				$Bilan_action='Ajout';
				$Bilan_ITEM='changement_Ajout_Bilan';
				if($action=="Info"){
					$Bilan_lib='';
					$Bilan_action='';
					$Bilan_ITEM='';
			        }
			  }
			}
		break;

		case "Termin&eacute;": 
			if($ROLE==0){
				if ( $ENV == "x" ){
				    	if($CHANGEMENT_LISTE_DATE_FIN<=$DATE_DU_JOUR){
					$Bilan_lib='Modifier la Fiche Bilan';
					$Bilan_action='Modif';
					$Bilan_ITEM='changement_Modif_Bilan';
					if($action=="Info"){
						$Bilan_lib='Voir la Fiche Bilan';
						$Bilan_action='Modif';
						$Bilan_ITEM='changement_Info_Bilan';
				        }
				        
					      	switch ($action_cr){
                  case "Ajout": 
                    $CR_lib='Faire la Fiche compte rendu';
                    $CR_action='Ajout';
                    $CR_ITEM='changement_Ajout_CompteRendu';
                    if($action=="Info"){
			$CR_lib='';
			$CR_action='';
			$CR_ITEM='';
			}
                  break;
                  case "Modif": 
                    $CR_lib='Modifier la Fiche compte rendu';
                    $CR_action='Modif';
                    $CR_ITEM='changement_Modif_CompteRendu';
                    if($action=="Info"){
				$CR_lib='Voir la Fiche compte rendu';
				$CR_action='Info';
				$CR_ITEM='changement_Info_CompteRendu';
		        }
                  break;
                  case "Info": 
                    $CR_lib='Voir la Fiche compte rendu';
                    $CR_action='Info';
                    $CR_ITEM='changement_Info_CompteRendu';
                  break;
                  }
				    	}
				}else{
					$Bilan_lib='Modifier la Fiche Bilan';
					$Bilan_action='Modif';
					$Bilan_ITEM='changement_Modif_Bilan';
					if($action=="Info"){
						$Bilan_lib='Voir la Fiche Bilan';
						$Bilan_action='Modif';
						$Bilan_ITEM='changement_Info_Bilan';
					        }
					switch ($action_cr){
          case "Ajout": 
            $CR_lib='Faire la Fiche compte rendu';
            $CR_action='Ajout';
            $CR_ITEM='changement_Ajout_CompteRendu';
            if($action=="Info"){
			$CR_lib='';
			$CR_action='';
			$CR_ITEM='';
	        }
          break;
          case "Modif": 
            $CR_lib='Modifier la Fiche compte rendu';
            $CR_action='Modif';
            $CR_ITEM='changement_Modif_CompteRendu';
            if($action=="Info"){
			$CR_lib='Voir la Fiche compte rendu';
			$CR_action='Info';
			$CR_ITEM='changement_Info_CompteRendu';
	        }
            
          break;
          case "Info": 
            $CR_lib='Voir la Fiche compte rendu';
            $CR_action='Info';
            $CR_ITEM='changement_Info_CompteRendu';
          break;
          }
				}
          
			}else{
				$Bilan_lib='Voir la Fiche Bilan';
				$Bilan_action='Modif';
				$Bilan_ITEM='changement_Info_Bilan';
			}
		break;
		
		case "Clotur&eacute;": 
		      $Bilan_lib='Voir la Fiche Bilan';
		      $Bilan_action='Info';
		      $Bilan_ITEM='changement_Info_Bilan';
		      $CR_lib='Voir la Fiche compte rendu';
		      $CR_action='Info';
		      $CR_ITEM='changement_Info_CompteRendu';
		break;
		
		case "Abandonn&eacute;": 
		break;
		
		}
		if($Bilan_ITEM!=''){
			if($Bilan_action=="Info"){
			$j++;
	   if ($j%2) { $class = "pair";}else{$class = "impair";} 
	   echo '
	   <tr class="'.$class.'">
	    <td align="left">&nbsp;Fiche Bilan&nbsp;</td>
	    <td align="left" colspan="3"><a class="LinkDef" href="./index.php?ITEM='.$Bilan_ITEM.'&action='.$Bilan_action.'&ID='.$ID.'">&nbsp;'.$Bilan_lib.'&nbsp;</a></td>
	   </tr>';
		}
	   }
	   if($CR_ITEM!=''){
	   	if($CR_action=="Info"){
			$j++;
	   if ($j%2) { $class = "pair";}else{$class = "impair";} 
	   echo '
	   <tr class="'.$class.'">
	    <td align="left">&nbsp;Compte Rendu&nbsp;</td>
	    <td align="left" colspan="3"><a class="LinkDef" href="./index.php?ITEM='.$CR_ITEM.'&action='.$CR_action.'&ID='.$ID.'">&nbsp;'.$CR_lib.'&nbsp;</a></td>
	   </tr>';
		}
	   }	
  
  if($STOP_pb==1){
   	$j++;
	   if ($j%2) { $class = "pair";}else{$class = "impair";} 
	   echo '
	   <tr class="'.$class.'">
	    <td align="center" colspan="4"><font color=#993333><b>Erreur lors de l\'Ajout.</b></font>
	    </td>
	   </tr>';
   }
   echo '
   <tr class="titre">
     <td colspan="4" align="center">';
	if($action=='Ajout'){
		echo '<input name="btn" type="submit" id="btn" value="Ajouter">';
	}
	if($action=='Modif'){
		$NB_BTN_AUTRE=0;
		echo '
		<input type="hidden" name="ID" value="'.$ID.'">
		<input type="hidden" name="action" value="'.$action.'">';
		switch ($CHANGEMENT_STATUS){
		case "Brouillon": 
			echo '<input name="btn" type="submit" id="btn" value="Modifier"> - ';
			if ( $ENV == "x" ){
				if($Date_Inter_Debut_bdd > $DATE_DU_JOUR){
					echo '<input name="btn" type="submit" id="btn" value="Inscription">';
				}
			}else{
				echo '<input name="btn" type="submit" id="btn" value="Inscription">';
			}
			echo '<input name="btn" type="submit" id="btn" value="Abandon">';
			if($FAR_ITEM!=''){
			   	if($FAR_action!="Info"){
			   		if($NB_BTN_AUTRE==0){echo '&nbsp;-&nbsp;';}
			   		echo '<input name="btn" type="submit" id="btn" value="FAR">';
			   		echo '<input type="hidden" name="FAR_action" value="'.$FAR_action.'">'; 
			   		$NB_BTN_AUTRE=$NB_BTN_AUTRE+1;
				}
			}
		break;
		case "Inscrit": 
			if($ROLE==0){
				echo '<input name="btn" type="submit" id="btn" value="Modifier"> - ';
				echo '<input name="btn" type="submit" id="btn" value="Brouillon">';
				echo '<input name="btn" type="submit" id="btn" value="Validation">';
				echo '<input name="btn" type="submit" id="btn" value="Abandon">';
			}
			if($FAR_ITEM!=''){
			   	if($FAR_action!="Info"){
			   		if($NB_BTN_AUTRE==0){echo '&nbsp;-&nbsp;';}
			   		echo '<input name="btn" type="submit" id="btn" value="FAR">';
			   		echo '<input type="hidden" name="FAR_action" value="'.$FAR_action.'">'; 
			   		$NB_BTN_AUTRE=$NB_BTN_AUTRE+1;
				}
			}
		break; 
		case "ReInscrition": 
			if($ROLE==0){
				echo '<input name="btn" type="submit" id="btn" value="Modifier"> - ';
				echo '<input name="btn" type="submit" id="btn" value="Brouillon">';
				echo '<input name="btn" type="submit" id="btn" value="Validation">';
				echo '<input name="btn" type="submit" id="btn" value="Abandon">';
			}
			if($FAR_ITEM!=''){
			   	if($FAR_action!="Info"){
			   		if($NB_BTN_AUTRE==0){echo '&nbsp;-&nbsp;';}
			   		echo '<input name="btn" type="submit" id="btn" value="FAR">';
			   		echo '<input type="hidden" name="FAR_action" value="'.$FAR_action.'">'; 
			   		$NB_BTN_AUTRE=$NB_BTN_AUTRE+1;
				}
			}
		break;
		case "Valid&eacute;": 
			if($ROLE==0){
				echo '<input name="btn" type="submit" id="btn" value="Modifier"> - ';
				echo '<input name="btn" type="submit" id="btn" value="ReInscription">';
				echo '<input name="btn" type="submit" id="btn" value="Abandon">'; 	
			}
			if($FAR_ITEM!=''){
			   	if($FAR_action!="Info"){
			   		if($NB_BTN_AUTRE==0){echo '&nbsp;-&nbsp;';}
			   		echo '<input name="btn" type="submit" id="btn" value="FAR">';
			   		echo '<input type="hidden" name="FAR_action" value="'.$FAR_action.'">';
			   		$NB_BTN_AUTRE=$NB_BTN_AUTRE+1; 
				}
			}
			if($Bilan_ITEM!=''){
				if($Bilan_action!="Info"){
					if($NB_BTN_AUTRE==0){echo '&nbsp;-&nbsp;';}
					echo '<input name="btn" type="submit" id="btn" value="Fiche Bilan">'; 
					echo '<input type="hidden" name="Bilan_action" value="'.$Bilan_action.'">'; 
					$NB_BTN_AUTRE=$NB_BTN_AUTRE+1;
				}
			}
			if($CR_ITEM!=''){
				if($CR_action!="Info"){
					if($NB_BTN_AUTRE==0){echo '&nbsp;-&nbsp;';}
					echo '<input name="btn" type="submit" id="btn" value="Compte Rendu">'; 
					echo '<input type="hidden" name="CR_action" value="'.$CR_action.'">'; 
					$NB_BTN_AUTRE=$NB_BTN_AUTRE+1;
				}
			}
		break;

		case "Termin&eacute;": 
			if($FAR_ITEM!=''){
			   	if($FAR_action!="Info"){
			   		if($NB_BTN_AUTRE==0){echo '&nbsp;-&nbsp;';}
			   		echo '<input name="btn" type="submit" id="btn" value="FAR">';
			   		echo '<input type="hidden" name="FAR_action" value="'.$FAR_action.'">'; 
			   		$NB_BTN_AUTRE=$NB_BTN_AUTRE+1;
				}
			}
			if($CR_ITEM!=''){
				if($CR_action!="Info"){
					if($NB_BTN_AUTRE==0){echo '&nbsp;-&nbsp;';}
					echo '<input name="btn" type="submit" id="btn" value="Compte Rendu">'; 
					echo '<input type="hidden" name="CR_action" value="'.$CR_action.'">'; 
					$NB_BTN_AUTRE=$NB_BTN_AUTRE+1;

				}
			}

		break;
		case "Clotur&eacute;": 
		
		break;
		case "Abandonn&eacute;": 

		break;
		
		}	
	} 
	echo '
	</td>
	</tr>
	<tr class="titre">
	<td colspan="4" align="center">
		<h2>&nbsp;[&nbsp;<a href="./index.php?ITEM=changement_Calendrier&m='.$MOIS_Inter_Debut.'&y='.$ANNEE_Inter_Debut.'">Retour - Calendrier</a>&nbsp;]&nbsp;';
		if($ROLE==0){
      echo '-&nbsp;[&nbsp;<a href="./index.php?ITEM=changement_Gestion_Liste_all">Retour - Liste</a>&nbsp;]&nbsp;';
		}
		echo '</h2>
	</td>
	</tr>';

echo '
</table>
</form>
</div>
';
mysql_free_result($res_rq_Selectionner_demande_type);
mysql_close($mysql_link); 
?>