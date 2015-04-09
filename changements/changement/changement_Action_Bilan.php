<?PHP
//redirection si acces dirrect
if(substr_count($_SERVER['PHP_SELF'], 'index.php')==0){
  header("Location: ../"); 
  exit(); 
}
/*******************************************************************
   Interface Bilan action
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
$STOP_OUI_NON=0;
$STOP_TEXT=0;
$STOP_PERSO=0;
$STOP_SOMME=0;
$NB_MAJ=0;
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
       	$rq_info_bilan="
      	SELECT * 
      	FROM `changement_bilan_config`
      	WHERE `ENABLE` ='0'
      	";
      $res_rq_info_bilan = mysql_query($rq_info_bilan, $mysql_link) or die(mysql_error());
      $tab_rq_info_bilan = mysql_fetch_assoc($res_rq_info_bilan);
      $total_ligne_rq_info_bilan=mysql_num_rows($res_rq_info_bilan);
      if($total_ligne_rq_info_bilan!=0){
        do {
        	$CHANGEMENT_BILAN_CONFIG_ID=$tab_rq_info_bilan['CHANGEMENT_BILAN_CONFIG_ID'];
        	$CHANGEMENT_BILAN_CONFIG_CRITERE=$tab_rq_info_bilan['CHANGEMENT_BILAN_CONFIG_CRITERE'];
        	$CHANGEMENT_BILAN_CONFIG_TYPE=$tab_rq_info_bilan['CHANGEMENT_BILAN_CONFIG_TYPE'];
        	$CHANGEMENT_BILAN_CONFIG_COM=$tab_rq_info_bilan['CHANGEMENT_BILAN_CONFIG_COM'];
        	if(!isset($CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_ID])){$CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_ID]='';}
        	if(!isset($CHANGEMENT_BILAN_CONFIG_COM_COM[$CHANGEMENT_BILAN_CONFIG_ID])){$CHANGEMENT_BILAN_CONFIG_COM_COM[$CHANGEMENT_BILAN_CONFIG_ID]='';}
		
		$CHANGEMENT_BILAN_CONFIG_NOM='CHANGEMENT_BILAN_CONFIG_ID_'.$CHANGEMENT_BILAN_CONFIG_ID;
		$CHANGEMENT_BILAN_CONFIG_NOM_COM='CHANGEMENT_BILAN_CONFIG_ID_COM_'.$CHANGEMENT_BILAN_CONFIG_ID;
        	switch ($CHANGEMENT_BILAN_CONFIG_TYPE){
        	case "oui-non": 
	        	if(!isset($tab_var[$CHANGEMENT_BILAN_CONFIG_NOM])){
	        		$CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_ID]='';
	        	}else{
	        		$CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_ID]=$tab_var[$CHANGEMENT_BILAN_CONFIG_NOM];
	        	}
	        	if($CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_ID]==''){
	        		$STOP=1;
	        		$STOP_OUI_NON=1;
	        	}
	        	if(!isset($tab_var[$CHANGEMENT_BILAN_CONFIG_NOM_COM])){
	        		$CHANGEMENT_BILAN_CONFIG_COM_COM[$CHANGEMENT_BILAN_CONFIG_ID]='';
	        	}else{
	        		$CHANGEMENT_BILAN_CONFIG_COM_COM[$CHANGEMENT_BILAN_CONFIG_ID]=$tab_var[$CHANGEMENT_BILAN_CONFIG_NOM_COM];
	        	}
    		break;
    
		case "liste_changement_bilan_personne": 
			$rq_info_personne="
			SELECT `CHANGEMENT_BILAN_PERSONNE_ID` , `CHANGEMENT_BILAN_PERSONNE_LIB` 
			FROM `changement_bilan_personne` 
			WHERE `ENABLE` = '0'
			AND `CHANGEMENT_BILAN_PERSONNE_LIB` != 'Total'
			ORDER BY `CHANGEMENT_BILAN_PERSONNE_LIB`";
			$res_rq_info_personne = mysql_query($rq_info_personne, $mysql_link) or die(mysql_error());
			$tab_rq_info_personne = mysql_fetch_assoc($res_rq_info_personne);
			$total_ligne_rq_info_personne=mysql_num_rows($res_rq_info_personne);
			if($total_ligne_rq_info_personne!=0){
			do {
				$CHANGEMENT_BILAN_PERSONNE_ID=$tab_rq_info_personne['CHANGEMENT_BILAN_PERSONNE_ID'];
				$CHANGEMENT_BILAN_PERSONNE_LIB=$tab_rq_info_personne['CHANGEMENT_BILAN_PERSONNE_LIB'];
				$CHANGEMENT_BILAN_CONFIG_NOM='CHANGEMENT_BILAN_CONFIG_ID_'.$CHANGEMENT_BILAN_CONFIG_ID.'_'.$CHANGEMENT_BILAN_PERSONNE_ID;
				$CHANGEMENT_BILAN_CONFIG_PERSO=$CHANGEMENT_BILAN_CONFIG_ID.'_'.$CHANGEMENT_BILAN_PERSONNE_ID;
				if(!isset($tab_var[$CHANGEMENT_BILAN_CONFIG_NOM])){
		        		$CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_PERSO]='0';
		        	}else{
		        		$CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_PERSO]=$tab_var[$CHANGEMENT_BILAN_CONFIG_NOM];
		        	}
		        	if(!is_numeric($CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_PERSO])){
		        		$STOP=1;
		        		$STOP_PERSO=1;
		        		$CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_PERSO]='0';
		        	}else{
			        	if(!isset($SOMME[$CHANGEMENT_BILAN_CONFIG_ID])){
			        		$SOMME[$CHANGEMENT_BILAN_CONFIG_ID]=0;
			        		$SOMME[$CHANGEMENT_BILAN_CONFIG_ID]=$SOMME[$CHANGEMENT_BILAN_CONFIG_ID]+$CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_PERSO];
			        	}else{
			        		$SOMME[$CHANGEMENT_BILAN_CONFIG_ID]=$SOMME[$CHANGEMENT_BILAN_CONFIG_ID]+$CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_PERSO];
			        	}
		        	}
			
			} while ($tab_rq_info_personne = mysql_fetch_assoc($res_rq_info_personne));
			$ligne= mysql_num_rows($res_rq_info_personne);
			if($ligne > 0) {
			mysql_data_seek($res_rq_info_personne, 0);
			$tab_rq_info_personne = mysql_fetch_assoc($res_rq_info_personne);
			}
			}

			mysql_free_result($res_rq_info_personne);
			$rq_info_personne="
			SELECT `CHANGEMENT_BILAN_PERSONNE_ID` , `CHANGEMENT_BILAN_PERSONNE_LIB` 
			FROM `changement_bilan_personne` 
			WHERE `ENABLE` = '0'
			AND `CHANGEMENT_BILAN_PERSONNE_LIB` = 'Total'
			ORDER BY `CHANGEMENT_BILAN_PERSONNE_LIB`";
			$res_rq_info_personne = mysql_query($rq_info_personne, $mysql_link) or die(mysql_error());
			$tab_rq_info_personne = mysql_fetch_assoc($res_rq_info_personne);
			$total_ligne_rq_info_personne=mysql_num_rows($res_rq_info_personne);
			if($total_ligne_rq_info_personne!=0){
			do {
				$CHANGEMENT_BILAN_PERSONNE_ID=$tab_rq_info_personne['CHANGEMENT_BILAN_PERSONNE_ID'];
				$CHANGEMENT_BILAN_PERSONNE_LIB=$tab_rq_info_personne['CHANGEMENT_BILAN_PERSONNE_LIB'];
				$CHANGEMENT_BILAN_CONFIG_NOM='CHANGEMENT_BILAN_CONFIG_ID_'.$CHANGEMENT_BILAN_CONFIG_ID.'_'.$CHANGEMENT_BILAN_PERSONNE_ID;
				$CHANGEMENT_BILAN_CONFIG_PERSO=$CHANGEMENT_BILAN_CONFIG_ID.'_'.$CHANGEMENT_BILAN_PERSONNE_ID;
				if(!isset($tab_var[$CHANGEMENT_BILAN_CONFIG_NOM])){
		        		$CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_PERSO]='0';
		        	}else{
		        		$CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_PERSO]=$tab_var[$CHANGEMENT_BILAN_CONFIG_NOM];
		        	}
		        	if(!is_numeric($CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_PERSO])){
		        		$STOP=1;
		        		$STOP_PERSO=1;
		        		$CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_PERSO]='0';
		        	}else{
			        	if($CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_PERSO]!=$SOMME[$CHANGEMENT_BILAN_CONFIG_ID]){
                  $STOP=1;
			        		$STOP_SOMME=1;
			        	}
		        	}
			} while ($tab_rq_info_personne = mysql_fetch_assoc($res_rq_info_personne));
			$ligne= mysql_num_rows($res_rq_info_personne);
			if($ligne > 0) {
			mysql_data_seek($res_rq_info_personne, 0);
			$tab_rq_info_personne = mysql_fetch_assoc($res_rq_info_personne);
			}
			}
			mysql_free_result($res_rq_info_personne);
		break;
		
		case "text": 
			if(!isset($tab_var[$CHANGEMENT_BILAN_CONFIG_NOM])){
	        		$CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_ID]='';
	        	}else{
	        		$CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_ID]=$tab_var[$CHANGEMENT_BILAN_CONFIG_NOM];
	        	}
	        	if($CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_ID]==''){
	        		$STOP=1;
	        		$STOP_TEXT=1;
	        	}
		break;
		
		}

        	
        } while ($tab_rq_info_bilan = mysql_fetch_assoc($res_rq_info_bilan));
        $ligne= mysql_num_rows($res_rq_info_bilan);
        if($ligne > 0) {
          mysql_data_seek($res_rq_info_bilan, 0);
          $tab_rq_info_bilan = mysql_fetch_assoc($res_rq_info_bilan);
        }
        if($STOP==0){
      
          do {
        	$CHANGEMENT_BILAN_CONFIG_ID=$tab_rq_info_bilan['CHANGEMENT_BILAN_CONFIG_ID'];
        	$CHANGEMENT_BILAN_CONFIG_CRITERE=$tab_rq_info_bilan['CHANGEMENT_BILAN_CONFIG_CRITERE'];
        	$CHANGEMENT_BILAN_CONFIG_TYPE=$tab_rq_info_bilan['CHANGEMENT_BILAN_CONFIG_TYPE'];
        	$CHANGEMENT_BILAN_CONFIG_COM=$tab_rq_info_bilan['CHANGEMENT_BILAN_CONFIG_COM'];
        	if(!isset($CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_ID])){$CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_ID]='';}
        	if(!isset($CHANGEMENT_BILAN_CONFIG_COM_COM[$CHANGEMENT_BILAN_CONFIG_ID])){$CHANGEMENT_BILAN_CONFIG_COM_COM[$CHANGEMENT_BILAN_CONFIG_ID]='';}
		
        $CHANGEMENT_BILAN_CONFIG_NOM='CHANGEMENT_BILAN_CONFIG_ID_'.$CHANGEMENT_BILAN_CONFIG_ID;
        $CHANGEMENT_BILAN_CONFIG_NOM_COM='CHANGEMENT_BILAN_CONFIG_ID_COM_'.$CHANGEMENT_BILAN_CONFIG_ID;
              switch ($CHANGEMENT_BILAN_CONFIG_TYPE){
              case "oui-non": 
                if(!isset($tab_var[$CHANGEMENT_BILAN_CONFIG_NOM])){
                  $CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_ID]='';
                }else{
                  $CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_ID]=$tab_var[$CHANGEMENT_BILAN_CONFIG_NOM];
                }
                if(!isset($tab_var[$CHANGEMENT_BILAN_CONFIG_NOM_COM])){
                  $CHANGEMENT_BILAN_CONFIG_COM_COM[$CHANGEMENT_BILAN_CONFIG_ID]='';
                }else{
                  $CHANGEMENT_BILAN_CONFIG_COM_COM[$CHANGEMENT_BILAN_CONFIG_ID]=$tab_var[$CHANGEMENT_BILAN_CONFIG_NOM_COM];
                }
                
            $sql="INSERT INTO `changement_bilan` (`CHANGEMENT_BILAN_ID` ,`CHANGEMENT_LISTE_ID` ,`CHANGEMENT_BILAN_CONFIG_ID` ,`CHANGEMENT_BILAN_UTILISATEUR_ID` ,`CHANGEMENT_BILAN_AUTRE_ID`,`CHANGEMENT_BILAN_VALEUR` ,`CHANGEMENT_BILAN_COM` ,`ENABLE`)
VALUES (NULL , '".$ID."', '".$CHANGEMENT_BILAN_CONFIG_ID."', '".$UTILISATEUR_ID."', '0','".$CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_ID]."', '".$CHANGEMENT_BILAN_CONFIG_COM_COM[$CHANGEMENT_BILAN_CONFIG_ID]."', '0');";
            mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
            $TABLE_SQL_SQL='changement_bilan';       
            historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');    
            break;
        
        case "liste_changement_bilan_personne": 
          $rq_info_personne="
          SELECT `CHANGEMENT_BILAN_PERSONNE_ID` , `CHANGEMENT_BILAN_PERSONNE_LIB` 
          FROM `changement_bilan_personne` 
          WHERE `ENABLE` = '0'
          AND `CHANGEMENT_BILAN_PERSONNE_LIB` != 'Total'
          ORDER BY `CHANGEMENT_BILAN_PERSONNE_LIB`";
          $res_rq_info_personne = mysql_query($rq_info_personne, $mysql_link) or die(mysql_error());
          $tab_rq_info_personne = mysql_fetch_assoc($res_rq_info_personne);
          $total_ligne_rq_info_personne=mysql_num_rows($res_rq_info_personne);
          if($total_ligne_rq_info_personne!=0){
          do {
            $CHANGEMENT_BILAN_PERSONNE_ID=$tab_rq_info_personne['CHANGEMENT_BILAN_PERSONNE_ID'];
            $CHANGEMENT_BILAN_PERSONNE_LIB=$tab_rq_info_personne['CHANGEMENT_BILAN_PERSONNE_LIB'];
            $CHANGEMENT_BILAN_CONFIG_NOM='CHANGEMENT_BILAN_CONFIG_ID_'.$CHANGEMENT_BILAN_CONFIG_ID.'_'.$CHANGEMENT_BILAN_PERSONNE_ID;
            $CHANGEMENT_BILAN_CONFIG_PERSO=$CHANGEMENT_BILAN_CONFIG_ID.'_'.$CHANGEMENT_BILAN_PERSONNE_ID;
            if(!isset($tab_var[$CHANGEMENT_BILAN_CONFIG_NOM])){
                    $CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_PERSO]='0';
                  }else{
                    $CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_PERSO]=$tab_var[$CHANGEMENT_BILAN_CONFIG_NOM];
                  }
                  $sql="INSERT INTO `changement_bilan` (`CHANGEMENT_BILAN_ID` ,`CHANGEMENT_LISTE_ID` ,`CHANGEMENT_BILAN_CONFIG_ID` ,`CHANGEMENT_BILAN_UTILISATEUR_ID`,`CHANGEMENT_BILAN_AUTRE_ID` ,`CHANGEMENT_BILAN_VALEUR` ,`CHANGEMENT_BILAN_COM` ,`ENABLE`)
VALUES (NULL , '".$ID."', '".$CHANGEMENT_BILAN_CONFIG_ID."', '".$UTILISATEUR_ID."', '".$CHANGEMENT_BILAN_PERSONNE_ID."','".$CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_PERSO]."', '', '0');";
            mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
            $TABLE_SQL_SQL='changement_bilan';       
            historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');   
          
          } while ($tab_rq_info_personne = mysql_fetch_assoc($res_rq_info_personne));
          $ligne= mysql_num_rows($res_rq_info_personne);
          if($ligne > 0) {
          mysql_data_seek($res_rq_info_personne, 0);
          $tab_rq_info_personne = mysql_fetch_assoc($res_rq_info_personne);
          }
          }

          mysql_free_result($res_rq_info_personne);
          $rq_info_personne="
          SELECT `CHANGEMENT_BILAN_PERSONNE_ID` , `CHANGEMENT_BILAN_PERSONNE_LIB` 
          FROM `changement_bilan_personne` 
          WHERE `ENABLE` = '0'
          AND `CHANGEMENT_BILAN_PERSONNE_LIB` = 'Total'
          ORDER BY `CHANGEMENT_BILAN_PERSONNE_LIB`";
          $res_rq_info_personne = mysql_query($rq_info_personne, $mysql_link) or die(mysql_error());
          $tab_rq_info_personne = mysql_fetch_assoc($res_rq_info_personne);
          $total_ligne_rq_info_personne=mysql_num_rows($res_rq_info_personne);
          if($total_ligne_rq_info_personne!=0){
          do {
            $CHANGEMENT_BILAN_PERSONNE_ID=$tab_rq_info_personne['CHANGEMENT_BILAN_PERSONNE_ID'];
            $CHANGEMENT_BILAN_PERSONNE_LIB=$tab_rq_info_personne['CHANGEMENT_BILAN_PERSONNE_LIB'];
            $CHANGEMENT_BILAN_CONFIG_NOM='CHANGEMENT_BILAN_CONFIG_ID_'.$CHANGEMENT_BILAN_CONFIG_ID.'_'.$CHANGEMENT_BILAN_PERSONNE_ID;
            $CHANGEMENT_BILAN_CONFIG_PERSO=$CHANGEMENT_BILAN_CONFIG_ID.'_'.$CHANGEMENT_BILAN_PERSONNE_ID;
            if(!isset($tab_var[$CHANGEMENT_BILAN_CONFIG_NOM])){
                $CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_PERSO]='0';
              }else{
                $CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_PERSO]=$tab_var[$CHANGEMENT_BILAN_CONFIG_NOM];
              }
              
                  $sql="INSERT INTO `changement_bilan` (`CHANGEMENT_BILAN_ID` ,`CHANGEMENT_LISTE_ID` ,`CHANGEMENT_BILAN_CONFIG_ID` ,`CHANGEMENT_BILAN_UTILISATEUR_ID`,`CHANGEMENT_BILAN_AUTRE_ID` ,`CHANGEMENT_BILAN_VALEUR` ,`CHANGEMENT_BILAN_COM` ,`ENABLE`)
VALUES (NULL , '".$ID."', '".$CHANGEMENT_BILAN_CONFIG_ID."', '".$UTILISATEUR_ID."','".$CHANGEMENT_BILAN_PERSONNE_ID."', '".$CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_PERSO]."', '', '0');";
            mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
            $TABLE_SQL_SQL='changement_bilan';       
            historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');   

          } while ($tab_rq_info_personne = mysql_fetch_assoc($res_rq_info_personne));
          $ligne= mysql_num_rows($res_rq_info_personne);
          if($ligne > 0) {
          mysql_data_seek($res_rq_info_personne, 0);
          $tab_rq_info_personne = mysql_fetch_assoc($res_rq_info_personne);
          }
          }
          mysql_free_result($res_rq_info_personne);
        break;
        
        case "text": 
          if(!isset($tab_var[$CHANGEMENT_BILAN_CONFIG_NOM])){
            $CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_ID]='';
          }else{
            $CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_ID]=$tab_var[$CHANGEMENT_BILAN_CONFIG_NOM];
          }
          $sql="INSERT INTO `changement_bilan` (`CHANGEMENT_BILAN_ID` ,`CHANGEMENT_LISTE_ID` ,`CHANGEMENT_BILAN_CONFIG_ID` ,`CHANGEMENT_BILAN_UTILISATEUR_ID`,`CHANGEMENT_BILAN_AUTRE_ID` ,`CHANGEMENT_BILAN_VALEUR` ,`CHANGEMENT_BILAN_COM` ,`ENABLE`)
VALUES (NULL , '".$ID."', '".$CHANGEMENT_BILAN_CONFIG_ID."', '".$UTILISATEUR_ID."', '0','".$CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_ID]."', '', '0');";
            mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
            $TABLE_SQL_SQL='changement_bilan';       
            historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');   

        break;
        
        }

        	
        } while ($tab_rq_info_bilan = mysql_fetch_assoc($res_rq_info_bilan));
        $ligne= mysql_num_rows($res_rq_info_bilan);
        if($ligne > 0) {
          mysql_data_seek($res_rq_info_bilan, 0);
          $tab_rq_info_bilan = mysql_fetch_assoc($res_rq_info_bilan);
        }
        
        $rq_info_status="
        SELECT `CHANGEMENT_STATUS_ID` 
        FROM `changement_status` 
        WHERE `CHANGEMENT_STATUS` = 'Termin&eacute;'
        AND `ENABLE`='0'";
        $res_rq_info_status = mysql_query($rq_info_status, $mysql_link) or die(mysql_error());
        $tab_rq_info_status = mysql_fetch_assoc($res_rq_info_status);
        $total_ligne_rq_info_status=mysql_num_rows($res_rq_info_status);
        if($total_ligne_rq_info_status==0){
          $sql="INSERT INTO `changement_status` (`CHANGEMENT_STATUS_ID` ,`CHANGEMENT_STATUS`, `ENABLE` )VALUES (NULL , 'Termin&eacute;', '0');";
          mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
          $TABLE_SQL_SQL='changement_status';       
          historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');
          $rq_info_status="
          SELECT `CHANGEMENT_STATUS_ID` 
          FROM `changement_status` 
          WHERE `CHANGEMENT_STATUS` = 'Termin&eacute;'
          AND `ENABLE`='0'";
          $res_rq_info_status = mysql_query($rq_info_status, $mysql_link) or die(mysql_error());
          $tab_rq_info_status = mysql_fetch_assoc($res_rq_info_status);
          $total_ligne_rq_info_status=mysql_num_rows($res_rq_info_status);
        }
        $CHANGEMENT_STATUS_ID=$tab_rq_info_status['CHANGEMENT_STATUS_ID'];
        mysql_free_result($res_rq_info_status); 
        $sql="
        UPDATE `changement_liste` SET 
        `CHANGEMENT_STATUS_ID` = '".$CHANGEMENT_STATUS_ID."'
        WHERE `CHANGEMENT_LISTE_ID` ='".$ID."' LIMIT 1 ;";

        mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
        $TABLE_SQL_SQL='changement_liste';       
        historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');
        
        $sql="OPTIMIZE TABLE `changement_bilan` ";

        mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
        $TABLE_SQL_SQL='changement_bilan';       
        historique_sql_new($sql,$TABLE_SQL_SQL,'OPTIMIZE');

        $TRACE_ETAT=$CHANGEMENT_STATUS_ID;
        
        $TRACE_CATEGORIE='Changement';
        $TRACE_TABLE='changement_bilan';
        $TRACE_REF_ID=$CHANGEMENT_ID;
        $TRACE_ACTION='Ajout';
        $TRACE_ETAT=$CHANGEMENT_STATUS_ID;
        moteur_trace($TRACE_CATEGORIE,$TRACE_TABLE,$TRACE_REF_ID,$TRACE_ACTION,$TRACE_ETAT);

        // mail Termin&eacute;	
        		
	/*	$objet='';
		if ( $ENV != "x" )
		{	
			$objet='[dev]-';
		}		
		$objet.='-=Gestion des changements=- La demande n '.$ID.' est terminee.';
		list ($Personne_abrege,$Personne_complet,$Personne_email,$Personne_email_FULL)=Recherche_SQL_nom_FULL($UTILISATEUR_ID);
		$Corps_mail = Constitue_corps_Message($CHANGEMENT_ID,$Personne_complet,$mysql_link);
		$Corps_mail_SVG = Constitue_corps_Message_SVG($CHANGEMENT_ID,$ENV,$Personne_email_FULL,$Personne_complet,$objet,$mysql_link);
		mailMailInfo_php($CHANGEMENT_ID,$Personne_email_FULL, $Personne_abrege, $objet, $Corps_mail, $TRACE_ETAT, $ENV,$mysql_link);
		sauvegarde_MailInfo( $Corps_mail_SVG, $CHANGEMENT_ID, $TRACE_ETAT,$UTILISATEUR_ID,$ENV,$mysql_link);
        
        echo '
        <script language="JavaScript">
       url=("./index.php?ITEM=changement_Info_Changement&action=Info&ID='.$ID.'");
        window.location=url;
        </script>
        ';  */ 
      echo '
      <script language="JavaScript">
      url=("./index.php?ITEM=changement_Send_Mail&type=terminer&ID='.$ID.'");
      window.location=url;
      </script>
      ';
    
        }
        mysql_free_result($res_rq_info_bilan);
        
     }
  }
  
    # Cas Modifier
  if($tab_var['btn']=="Modifier"){
  	$ID=$tab_var['ID'];
  	$CHANGEMENT_ID=$ID;
       	$rq_info_bilan="
      	SELECT `changement_bilan_config`.`CHANGEMENT_BILAN_CONFIG_ID`,
      	`changement_bilan_config`.`CHANGEMENT_BILAN_CONFIG_CRITERE`,
      	`changement_bilan_config`.`CHANGEMENT_BILAN_CONFIG_TYPE`,
      	`changement_bilan_config`.`CHANGEMENT_BILAN_CONFIG_COM`
        FROM `changement_bilan_config` , `changement_bilan`
        WHERE `changement_bilan_config`.`CHANGEMENT_BILAN_CONFIG_ID` = `changement_bilan`.`CHANGEMENT_BILAN_CONFIG_ID`
        AND `changement_bilan`.`CHANGEMENT_LISTE_ID` = '".$ID."'
        AND `changement_bilan`.`ENABLE` = '0'
        GROUP BY `changement_bilan_config`.`CHANGEMENT_BILAN_CONFIG_ID`
        ORDER BY `changement_bilan_config`.`CHANGEMENT_BILAN_CONFIG_LIB`
      	";
      $res_rq_info_bilan = mysql_query($rq_info_bilan, $mysql_link) or die(mysql_error());
      $tab_rq_info_bilan = mysql_fetch_assoc($res_rq_info_bilan);
      $total_ligne_rq_info_bilan=mysql_num_rows($res_rq_info_bilan);
      if($total_ligne_rq_info_bilan!=0){
        do {
        	$CHANGEMENT_BILAN_CONFIG_ID=$tab_rq_info_bilan['CHANGEMENT_BILAN_CONFIG_ID'];
        	$CHANGEMENT_BILAN_CONFIG_CRITERE=$tab_rq_info_bilan['CHANGEMENT_BILAN_CONFIG_CRITERE'];
        	$CHANGEMENT_BILAN_CONFIG_TYPE=$tab_rq_info_bilan['CHANGEMENT_BILAN_CONFIG_TYPE'];
        	$CHANGEMENT_BILAN_CONFIG_COM=$tab_rq_info_bilan['CHANGEMENT_BILAN_CONFIG_COM'];
        	if(!isset($CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_ID])){$CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_ID]='';}
        	if(!isset($CHANGEMENT_BILAN_CONFIG_COM_COM[$CHANGEMENT_BILAN_CONFIG_ID])){$CHANGEMENT_BILAN_CONFIG_COM_COM[$CHANGEMENT_BILAN_CONFIG_ID]='';}
		
		$CHANGEMENT_BILAN_CONFIG_NOM='CHANGEMENT_BILAN_CONFIG_ID_'.$CHANGEMENT_BILAN_CONFIG_ID;
		$CHANGEMENT_BILAN_CONFIG_NOM_COM='CHANGEMENT_BILAN_CONFIG_ID_COM_'.$CHANGEMENT_BILAN_CONFIG_ID;
        	switch ($CHANGEMENT_BILAN_CONFIG_TYPE){
        	case "oui-non": 
	        	if(!isset($tab_var[$CHANGEMENT_BILAN_CONFIG_NOM])){
	        		$CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_ID]='';
	        	}else{
	        		$CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_ID]=$tab_var[$CHANGEMENT_BILAN_CONFIG_NOM];
	        	}
	        	if($CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_ID]==''){
	        		$STOP=1;
	        		$STOP_OUI_NON=1;
	        	}
	        	if(!isset($tab_var[$CHANGEMENT_BILAN_CONFIG_NOM_COM])){
	        		$CHANGEMENT_BILAN_CONFIG_COM_COM[$CHANGEMENT_BILAN_CONFIG_ID]='';
	        	}else{
	        		$CHANGEMENT_BILAN_CONFIG_COM_COM[$CHANGEMENT_BILAN_CONFIG_ID]=$tab_var[$CHANGEMENT_BILAN_CONFIG_NOM_COM];
	        	}
    		break;
    
		case "liste_changement_bilan_personne": 
			$rq_info_personne="
			SELECT `CHANGEMENT_BILAN_PERSONNE_ID` , `CHANGEMENT_BILAN_PERSONNE_LIB` 
			FROM `changement_bilan_personne` 
			WHERE `ENABLE` = '0'
			AND `CHANGEMENT_BILAN_PERSONNE_LIB` != 'Total'
			ORDER BY `CHANGEMENT_BILAN_PERSONNE_LIB`";
			$res_rq_info_personne = mysql_query($rq_info_personne, $mysql_link) or die(mysql_error());
			$tab_rq_info_personne = mysql_fetch_assoc($res_rq_info_personne);
			$total_ligne_rq_info_personne=mysql_num_rows($res_rq_info_personne);
			if($total_ligne_rq_info_personne!=0){
			do {
				$CHANGEMENT_BILAN_PERSONNE_ID=$tab_rq_info_personne['CHANGEMENT_BILAN_PERSONNE_ID'];
				$CHANGEMENT_BILAN_PERSONNE_LIB=$tab_rq_info_personne['CHANGEMENT_BILAN_PERSONNE_LIB'];
				$CHANGEMENT_BILAN_CONFIG_NOM='CHANGEMENT_BILAN_CONFIG_ID_'.$CHANGEMENT_BILAN_CONFIG_ID.'_'.$CHANGEMENT_BILAN_PERSONNE_ID;
				$CHANGEMENT_BILAN_CONFIG_PERSO=$CHANGEMENT_BILAN_CONFIG_ID.'_'.$CHANGEMENT_BILAN_PERSONNE_ID;
				if(!isset($tab_var[$CHANGEMENT_BILAN_CONFIG_NOM])){
		        		$CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_PERSO]='0';
		        	}else{
		        		$CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_PERSO]=$tab_var[$CHANGEMENT_BILAN_CONFIG_NOM];
		        	}
		        	if(!is_numeric($CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_PERSO])){
		        		$STOP=1;
		        		$STOP_PERSO=1;
		        		$CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_PERSO]='0';
		        	}else{
			        	if(!isset($SOMME[$CHANGEMENT_BILAN_CONFIG_ID])){
			        		$SOMME[$CHANGEMENT_BILAN_CONFIG_ID]=0;
			        		$SOMME[$CHANGEMENT_BILAN_CONFIG_ID]=$SOMME[$CHANGEMENT_BILAN_CONFIG_ID]+$CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_PERSO];
			        	}else{
			        		$SOMME[$CHANGEMENT_BILAN_CONFIG_ID]=$SOMME[$CHANGEMENT_BILAN_CONFIG_ID]+$CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_PERSO];
			        	}
		        	}
			
			} while ($tab_rq_info_personne = mysql_fetch_assoc($res_rq_info_personne));
			$ligne= mysql_num_rows($res_rq_info_personne);
			if($ligne > 0) {
			mysql_data_seek($res_rq_info_personne, 0);
			$tab_rq_info_personne = mysql_fetch_assoc($res_rq_info_personne);
			}
			}

			mysql_free_result($res_rq_info_personne);
			$rq_info_personne="
			SELECT `CHANGEMENT_BILAN_PERSONNE_ID` , `CHANGEMENT_BILAN_PERSONNE_LIB` 
			FROM `changement_bilan_personne` 
			WHERE `ENABLE` = '0'
			AND `CHANGEMENT_BILAN_PERSONNE_LIB` = 'Total'
			ORDER BY `CHANGEMENT_BILAN_PERSONNE_LIB`";
			$res_rq_info_personne = mysql_query($rq_info_personne, $mysql_link) or die(mysql_error());
			$tab_rq_info_personne = mysql_fetch_assoc($res_rq_info_personne);
			$total_ligne_rq_info_personne=mysql_num_rows($res_rq_info_personne);
			if($total_ligne_rq_info_personne!=0){
			do {
				$CHANGEMENT_BILAN_PERSONNE_ID=$tab_rq_info_personne['CHANGEMENT_BILAN_PERSONNE_ID'];
				$CHANGEMENT_BILAN_PERSONNE_LIB=$tab_rq_info_personne['CHANGEMENT_BILAN_PERSONNE_LIB'];
				$CHANGEMENT_BILAN_CONFIG_NOM='CHANGEMENT_BILAN_CONFIG_ID_'.$CHANGEMENT_BILAN_CONFIG_ID.'_'.$CHANGEMENT_BILAN_PERSONNE_ID;
				$CHANGEMENT_BILAN_CONFIG_PERSO=$CHANGEMENT_BILAN_CONFIG_ID.'_'.$CHANGEMENT_BILAN_PERSONNE_ID;
				if(!isset($tab_var[$CHANGEMENT_BILAN_CONFIG_NOM])){
		        		$CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_PERSO]='0';
		        	}else{
		        		$CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_PERSO]=$tab_var[$CHANGEMENT_BILAN_CONFIG_NOM];
		        	}
		        	if(!is_numeric($CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_PERSO])){
		        		$STOP=1;
		        		$STOP_PERSO=1;
		        		$CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_PERSO]='0';
		        	}else{
			        	if($CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_PERSO]!=$SOMME[$CHANGEMENT_BILAN_CONFIG_ID]){
                  $STOP=1;
			        		$STOP_SOMME=1;
			        	}
		        	}
			} while ($tab_rq_info_personne = mysql_fetch_assoc($res_rq_info_personne));
			$ligne= mysql_num_rows($res_rq_info_personne);
			if($ligne > 0) {
			mysql_data_seek($res_rq_info_personne, 0);
			$tab_rq_info_personne = mysql_fetch_assoc($res_rq_info_personne);
			}
			}
			mysql_free_result($res_rq_info_personne);
		break;
		
		case "text": 
			if(!isset($tab_var[$CHANGEMENT_BILAN_CONFIG_NOM])){
	        		$CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_ID]='';
	        	}else{
	        		$CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_ID]=$tab_var[$CHANGEMENT_BILAN_CONFIG_NOM];
	        	}
	        	if($CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_ID]==''){
	        		$STOP=1;
	        		$STOP_TEXT=1;
	        	}
		break;
		
		}

        	
        } while ($tab_rq_info_bilan = mysql_fetch_assoc($res_rq_info_bilan));
        $ligne= mysql_num_rows($res_rq_info_bilan);
        if($ligne > 0) {
          mysql_data_seek($res_rq_info_bilan, 0);
          $tab_rq_info_bilan = mysql_fetch_assoc($res_rq_info_bilan);
        }
        if($STOP==0){
        
          do {
        	$CHANGEMENT_BILAN_CONFIG_ID=$tab_rq_info_bilan['CHANGEMENT_BILAN_CONFIG_ID'];
        	$CHANGEMENT_BILAN_CONFIG_CRITERE=$tab_rq_info_bilan['CHANGEMENT_BILAN_CONFIG_CRITERE'];
        	$CHANGEMENT_BILAN_CONFIG_TYPE=$tab_rq_info_bilan['CHANGEMENT_BILAN_CONFIG_TYPE'];
        	$CHANGEMENT_BILAN_CONFIG_COM=$tab_rq_info_bilan['CHANGEMENT_BILAN_CONFIG_COM'];
        	if(!isset($CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_ID])){$CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_ID]='';}
        	if(!isset($CHANGEMENT_BILAN_CONFIG_COM_COM[$CHANGEMENT_BILAN_CONFIG_ID])){$CHANGEMENT_BILAN_CONFIG_COM_COM[$CHANGEMENT_BILAN_CONFIG_ID]='';}
		
        $CHANGEMENT_BILAN_CONFIG_NOM='CHANGEMENT_BILAN_CONFIG_ID_'.$CHANGEMENT_BILAN_CONFIG_ID;
        $CHANGEMENT_BILAN_CONFIG_NOM_COM='CHANGEMENT_BILAN_CONFIG_ID_COM_'.$CHANGEMENT_BILAN_CONFIG_ID;
              switch ($CHANGEMENT_BILAN_CONFIG_TYPE){
              case "oui-non": 
                if(!isset($tab_var[$CHANGEMENT_BILAN_CONFIG_NOM])){
                  $CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_ID]='';
                }else{
                  $CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_ID]=$tab_var[$CHANGEMENT_BILAN_CONFIG_NOM];
                }
                if(!isset($tab_var[$CHANGEMENT_BILAN_CONFIG_NOM_COM])){
                  $CHANGEMENT_BILAN_CONFIG_COM_COM[$CHANGEMENT_BILAN_CONFIG_ID]='';
                }else{
                  $CHANGEMENT_BILAN_CONFIG_COM_COM[$CHANGEMENT_BILAN_CONFIG_ID]=$tab_var[$CHANGEMENT_BILAN_CONFIG_NOM_COM];
                }
                
                $rq_info_bilan_id="
		SELECT `CHANGEMENT_BILAN_ID` 
		FROM `changement_bilan` 
		WHERE `CHANGEMENT_LISTE_ID` ='".$ID."'
		AND `CHANGEMENT_BILAN_CONFIG_ID`='".$CHANGEMENT_BILAN_CONFIG_ID."'
		AND `CHANGEMENT_BILAN_AUTRE_ID`='0'
		AND `CHANGEMENT_BILAN_VALEUR` = '".$CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_ID]."'
		AND `CHANGEMENT_BILAN_COM` = '".$CHANGEMENT_BILAN_CONFIG_COM_COM[$CHANGEMENT_BILAN_CONFIG_ID]."'
		LIMIT 1 ;";
		$res_rq_info_bilan_id = mysql_query($rq_info_bilan_id, $mysql_link) or die(mysql_error());
		$tab_rq_info_bilan_id = mysql_fetch_assoc($res_rq_info_bilan_id);
		$total_ligne_rq_info_bilan_id=mysql_num_rows($res_rq_info_bilan_id);

		mysql_free_result($res_rq_info_bilan_id);
		if($total_ligne_rq_info_bilan_id==0){
		    $sql="
	            UPDATE `changement_bilan` SET 
	            `CHANGEMENT_BILAN_VALEUR` = '".$CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_ID]."',
	            `CHANGEMENT_BILAN_COM` = '".$CHANGEMENT_BILAN_CONFIG_COM_COM[$CHANGEMENT_BILAN_CONFIG_ID]."',
	            `CHANGEMENT_BILAN_UTILISATEUR_ID`='".$UTILISATEUR_ID."'
	            WHERE `CHANGEMENT_LISTE_ID` ='".$ID."'
	            AND `CHANGEMENT_BILAN_CONFIG_ID`='".$CHANGEMENT_BILAN_CONFIG_ID."'
	            AND `CHANGEMENT_BILAN_AUTRE_ID`='0'
	            LIMIT 1 ;";
	            mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
	            $TABLE_SQL_SQL='changement_bilan';       
	            historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');   
	            $NB_MAJ=$NB_MAJ+1;
		}
	            
            break;
        
        case "liste_changement_bilan_personne": 
          $rq_info_personne="
          SELECT `CHANGEMENT_BILAN_PERSONNE_ID` , `CHANGEMENT_BILAN_PERSONNE_LIB` 
          FROM `changement_bilan_personne` 
          WHERE `ENABLE` = '0'
          AND `CHANGEMENT_BILAN_PERSONNE_LIB` != 'Total'
          ORDER BY `CHANGEMENT_BILAN_PERSONNE_LIB`";
          $res_rq_info_personne = mysql_query($rq_info_personne, $mysql_link) or die(mysql_error());
          $tab_rq_info_personne = mysql_fetch_assoc($res_rq_info_personne);
          $total_ligne_rq_info_personne=mysql_num_rows($res_rq_info_personne);
          if($total_ligne_rq_info_personne!=0){
          do {
            $CHANGEMENT_BILAN_PERSONNE_ID=$tab_rq_info_personne['CHANGEMENT_BILAN_PERSONNE_ID'];
            $CHANGEMENT_BILAN_PERSONNE_LIB=$tab_rq_info_personne['CHANGEMENT_BILAN_PERSONNE_LIB'];
            $CHANGEMENT_BILAN_CONFIG_NOM='CHANGEMENT_BILAN_CONFIG_ID_'.$CHANGEMENT_BILAN_CONFIG_ID.'_'.$CHANGEMENT_BILAN_PERSONNE_ID;
            $CHANGEMENT_BILAN_CONFIG_PERSO=$CHANGEMENT_BILAN_CONFIG_ID.'_'.$CHANGEMENT_BILAN_PERSONNE_ID;
            if(!isset($tab_var[$CHANGEMENT_BILAN_CONFIG_NOM])){
                    $CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_PERSO]='0';
                  }else{
                    $CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_PERSO]=$tab_var[$CHANGEMENT_BILAN_CONFIG_NOM];
                  }
                  $rq_info_bilan_id="
		SELECT `CHANGEMENT_BILAN_ID` 
		FROM `changement_bilan` 
		WHERE `CHANGEMENT_LISTE_ID` ='".$ID."'
		AND `CHANGEMENT_BILAN_CONFIG_ID`='".$CHANGEMENT_BILAN_CONFIG_ID."'
	        AND `CHANGEMENT_BILAN_AUTRE_ID`='".$CHANGEMENT_BILAN_PERSONNE_ID."'
		AND `CHANGEMENT_BILAN_VALEUR` = '".$CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_PERSO]."'
		LIMIT 1 ;";
		$res_rq_info_bilan_id = mysql_query($rq_info_bilan_id, $mysql_link) or die(mysql_error());
		$tab_rq_info_bilan_id = mysql_fetch_assoc($res_rq_info_bilan_id);
		$total_ligne_rq_info_bilan_id=mysql_num_rows($res_rq_info_bilan_id);

		mysql_free_result($res_rq_info_bilan_id);
		if($total_ligne_rq_info_bilan_id==0){

	            $sql="
	            UPDATE `changement_bilan` SET 
	            `CHANGEMENT_BILAN_VALEUR` = '".$CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_PERSO]."',
	            `CHANGEMENT_BILAN_UTILISATEUR_ID`='".$UTILISATEUR_ID."'
	            WHERE `CHANGEMENT_LISTE_ID` ='".$ID."'
	            AND `CHANGEMENT_BILAN_CONFIG_ID`='".$CHANGEMENT_BILAN_CONFIG_ID."'
	            AND `CHANGEMENT_BILAN_AUTRE_ID`='".$CHANGEMENT_BILAN_PERSONNE_ID."'
	            LIMIT 1 ;";
	            mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
	            $TABLE_SQL_SQL='changement_bilan';       
	            historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');   
	            $NB_MAJ=$NB_MAJ+1;
	        }
          
          } while ($tab_rq_info_personne = mysql_fetch_assoc($res_rq_info_personne));
          $ligne= mysql_num_rows($res_rq_info_personne);
          if($ligne > 0) {
          mysql_data_seek($res_rq_info_personne, 0);
          $tab_rq_info_personne = mysql_fetch_assoc($res_rq_info_personne);
          }
          }

          mysql_free_result($res_rq_info_personne);
          $rq_info_personne="
          SELECT `CHANGEMENT_BILAN_PERSONNE_ID` , `CHANGEMENT_BILAN_PERSONNE_LIB` 
          FROM `changement_bilan_personne` 
          WHERE `ENABLE` = '0'
          AND `CHANGEMENT_BILAN_PERSONNE_LIB` = 'Total'
          ORDER BY `CHANGEMENT_BILAN_PERSONNE_LIB`";
          $res_rq_info_personne = mysql_query($rq_info_personne, $mysql_link) or die(mysql_error());
          $tab_rq_info_personne = mysql_fetch_assoc($res_rq_info_personne);
          $total_ligne_rq_info_personne=mysql_num_rows($res_rq_info_personne);
          if($total_ligne_rq_info_personne!=0){
          do {
            $CHANGEMENT_BILAN_PERSONNE_ID=$tab_rq_info_personne['CHANGEMENT_BILAN_PERSONNE_ID'];
            $CHANGEMENT_BILAN_PERSONNE_LIB=$tab_rq_info_personne['CHANGEMENT_BILAN_PERSONNE_LIB'];
            $CHANGEMENT_BILAN_CONFIG_NOM='CHANGEMENT_BILAN_CONFIG_ID_'.$CHANGEMENT_BILAN_CONFIG_ID.'_'.$CHANGEMENT_BILAN_PERSONNE_ID;
            $CHANGEMENT_BILAN_CONFIG_PERSO=$CHANGEMENT_BILAN_CONFIG_ID.'_'.$CHANGEMENT_BILAN_PERSONNE_ID;
            if(!isset($tab_var[$CHANGEMENT_BILAN_CONFIG_NOM])){
                $CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_PERSO]='0';
              }else{
                $CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_PERSO]=$tab_var[$CHANGEMENT_BILAN_CONFIG_NOM];
              }
              
              $rq_info_bilan_id="
		SELECT `CHANGEMENT_BILAN_ID` 
		FROM `changement_bilan` 
		WHERE `CHANGEMENT_LISTE_ID` ='".$ID."'
		AND `CHANGEMENT_BILAN_CONFIG_ID`='".$CHANGEMENT_BILAN_CONFIG_ID."'
	        AND `CHANGEMENT_BILAN_AUTRE_ID`='".$CHANGEMENT_BILAN_PERSONNE_ID."'
		AND `CHANGEMENT_BILAN_VALEUR` = '".$CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_PERSO]."'
		LIMIT 1 ;";
		$res_rq_info_bilan_id = mysql_query($rq_info_bilan_id, $mysql_link) or die(mysql_error());
		$tab_rq_info_bilan_id = mysql_fetch_assoc($res_rq_info_bilan_id);
		$total_ligne_rq_info_bilan_id=mysql_num_rows($res_rq_info_bilan_id);

		mysql_free_result($res_rq_info_bilan_id);
		if($total_ligne_rq_info_bilan_id==0){
	            $sql="
	            UPDATE `changement_bilan` SET 
	            `CHANGEMENT_BILAN_VALEUR` = '".$CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_PERSO]."',
	            `CHANGEMENT_BILAN_UTILISATEUR_ID`='".$UTILISATEUR_ID."'
	            WHERE `CHANGEMENT_LISTE_ID` ='".$ID."'
	            AND `CHANGEMENT_BILAN_CONFIG_ID`='".$CHANGEMENT_BILAN_CONFIG_ID."'
	            AND `CHANGEMENT_BILAN_AUTRE_ID`='".$CHANGEMENT_BILAN_PERSONNE_ID."'
	            LIMIT 1 ;";
	            mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
	            $TABLE_SQL_SQL='changement_bilan';       
	            historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');   
	            $NB_MAJ=$NB_MAJ+1;
        	}

          } while ($tab_rq_info_personne = mysql_fetch_assoc($res_rq_info_personne));
          $ligne= mysql_num_rows($res_rq_info_personne);
          if($ligne > 0) {
          mysql_data_seek($res_rq_info_personne, 0);
          $tab_rq_info_personne = mysql_fetch_assoc($res_rq_info_personne);
          }
          }
          mysql_free_result($res_rq_info_personne);
        break;
        
        case "text": 
          if(!isset($tab_var[$CHANGEMENT_BILAN_CONFIG_NOM])){
            $CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_ID]='';
          }else{
            $CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_ID]=$tab_var[$CHANGEMENT_BILAN_CONFIG_NOM];
          }
          $rq_info_bilan_id="
		SELECT `CHANGEMENT_BILAN_ID` 
		FROM `changement_bilan` 
		WHERE `CHANGEMENT_LISTE_ID` ='".$ID."'
		AND `CHANGEMENT_BILAN_CONFIG_ID`='".$CHANGEMENT_BILAN_CONFIG_ID."'
		AND `CHANGEMENT_BILAN_AUTRE_ID`='0'
		AND `CHANGEMENT_BILAN_VALEUR` = '".$CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_ID]."'
		LIMIT 1 ;";
		$res_rq_info_bilan_id = mysql_query($rq_info_bilan_id, $mysql_link) or die(mysql_error());
		$tab_rq_info_bilan_id = mysql_fetch_assoc($res_rq_info_bilan_id);
		$total_ligne_rq_info_bilan_id=mysql_num_rows($res_rq_info_bilan_id);

		mysql_free_result($res_rq_info_bilan_id);
		if($total_ligne_rq_info_bilan_id==0){
	            $sql="
	            UPDATE `changement_bilan` SET 
	            `CHANGEMENT_BILAN_VALEUR` = '".$CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_ID]."',
	            `CHANGEMENT_BILAN_UTILISATEUR_ID`='".$UTILISATEUR_ID."'
	            WHERE `CHANGEMENT_LISTE_ID` ='".$ID."'
	            AND `CHANGEMENT_BILAN_CONFIG_ID`='".$CHANGEMENT_BILAN_CONFIG_ID."'
	            AND `CHANGEMENT_BILAN_AUTRE_ID`='0'
	            LIMIT 1 ;";
	            mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
	            $TABLE_SQL_SQL='changement_bilan';       
	            historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');   
	            $NB_MAJ=$NB_MAJ+1;
	        }

        break;
        
        }

        	
        } while ($tab_rq_info_bilan = mysql_fetch_assoc($res_rq_info_bilan));
        $ligne= mysql_num_rows($res_rq_info_bilan);
        if($ligne > 0) {
          mysql_data_seek($res_rq_info_bilan, 0);
          $tab_rq_info_bilan = mysql_fetch_assoc($res_rq_info_bilan);
        }
        if($NB_MAJ!=0){
	        $rq_info_status="
	        SELECT `CHANGEMENT_STATUS_ID` 
	        FROM `changement_liste` 
	        WHERE `CHANGEMENT_LISTE_ID` = '".$ID."'
	        LIMIT 1";
	        $res_rq_info_status = mysql_query($rq_info_status, $mysql_link) or die(mysql_error());
	        $tab_rq_info_status = mysql_fetch_assoc($res_rq_info_status);
	        $total_ligne_rq_info_status=mysql_num_rows($res_rq_info_status);
	        $CHANGEMENT_STATUS_ID=$tab_rq_info_status['CHANGEMENT_STATUS_ID'];
	        mysql_free_result($res_rq_info_status); 
	        $sql="
	        UPDATE `changement_liste` SET 
	        `CHANGEMENT_STATUS_ID` = '".$CHANGEMENT_STATUS_ID."'
	        WHERE `CHANGEMENT_LISTE_ID` ='".$ID."' LIMIT 1 ;";
	
	        mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
	        $TABLE_SQL_SQL='changement_liste';       
	        historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');
	        
	        $sql="OPTIMIZE TABLE `changement_bilan` ";
	
	        mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
	        $TABLE_SQL_SQL='changement_bilan';       
	        historique_sql_new($sql,$TABLE_SQL_SQL,'OPTIMIZE');
	
	        $TRACE_ETAT=$CHANGEMENT_STATUS_ID;
	        
	        $TRACE_CATEGORIE='Changement';
	        $TRACE_TABLE='changement_bilan';
	        $TRACE_REF_ID=$CHANGEMENT_ID;
	        $TRACE_ACTION='Modif';
	        $TRACE_ETAT=$CHANGEMENT_STATUS_ID;
	        moteur_trace($TRACE_CATEGORIE,$TRACE_TABLE,$TRACE_REF_ID,$TRACE_ACTION,$TRACE_ETAT);

	        // mail Termin&eacute;			
	        /*
		$objet='';
		if ( $ENV != "x" )
		{	
			$objet='[dev]-';
		}		
		$objet.='-=Gestion des changements=- La demande n '.$ID.' est terminee.';
		list ($Personne_abrege,$Personne_complet,$Personne_email,$Personne_email_FULL)=Recherche_SQL_nom_FULL($UTILISATEUR_ID);
		$Corps_mail = Constitue_corps_Message($CHANGEMENT_ID,$Personne_complet,$mysql_link);
		$Corps_mail_SVG = Constitue_corps_Message_SVG($CHANGEMENT_ID,$ENV,$Personne_email_FULL,$Personne_complet,$objet,$mysql_link);
		mailMailInfo_php($CHANGEMENT_ID,$Personne_email_FULL, $Personne_abrege, $objet, $Corps_mail, $TRACE_ETAT, $ENV,$mysql_link);
		sauvegarde_MailInfo( $Corps_mail_SVG, $CHANGEMENT_ID, $TRACE_ETAT,$UTILISATEUR_ID,$ENV,$mysql_link);
*/
      echo '
      <script language="JavaScript">
      url=("./index.php?ITEM=changement_Send_Mail&type=terminer&ID='.$ID.'");
      window.location=url;
      </script>
      ';
	}
        echo '
        <script language="JavaScript">
        url=("./index.php?ITEM=changement_Info_Changement&action=Info&ID='.$ID.'");
        window.location=url;
        </script>
        ';
       
        }
        mysql_free_result($res_rq_info_bilan);
        
     }
  }
  
  
}

if($action=="Info"){
	$rq_info_bilan="
	SELECT `changement_status`.`CHANGEMENT_STATUS` 
	FROM `changement_liste` , `changement_status` 
	WHERE `changement_liste`.`CHANGEMENT_LISTE_ID` = '".$ID."'
	AND `changement_liste`.`CHANGEMENT_STATUS_ID` = `changement_status`.`CHANGEMENT_STATUS_ID` 
	AND `changement_liste`.`ENABLE` = '0'
	";
	$res_rq_info_bilan = mysql_query($rq_info_bilan, $mysql_link) or die(mysql_error());
	$tab_rq_info_bilan = mysql_fetch_assoc($res_rq_info_bilan);
	$total_ligne_rq_info_bilan=mysql_num_rows($res_rq_info_bilan);
	if($total_ligne_rq_info_bilan==0){
		$CHANGEMENT_STATUS='Brouillon';
	}else{
		$CHANGEMENT_STATUS=$tab_rq_info_bilan['CHANGEMENT_STATUS'];;		
	}
	mysql_free_result($res_rq_info_bilan);
	if($STOP==0){
	$rq_info_bilan="
	SELECT *
	FROM `changement_bilan`
        WHERE `CHANGEMENT_LISTE_ID` = '".$ID."'
        AND ENABLE ='0'
	";
	$res_rq_info_bilan = mysql_query($rq_info_bilan, $mysql_link) or die(mysql_error());
	$tab_rq_info_bilan = mysql_fetch_assoc($res_rq_info_bilan);
	$total_ligne_rq_info_bilan=mysql_num_rows($res_rq_info_bilan);
	if($total_ligne_rq_info_bilan!=0){
          do {
            $CHANGEMENT_BILAN_ID=$tab_rq_info_bilan['CHANGEMENT_BILAN_ID'];
            $CHANGEMENT_LISTE_ID=$tab_rq_info_bilan['CHANGEMENT_LISTE_ID'];
            $CHANGEMENT_BILAN_CONFIG_ID=$tab_rq_info_bilan['CHANGEMENT_BILAN_CONFIG_ID'];
            $CHANGEMENT_BILAN_UTILISATEUR_ID=$tab_rq_info_bilan['CHANGEMENT_BILAN_UTILISATEUR_ID'];
            $CHANGEMENT_BILAN_AUTRE_ID=$tab_rq_info_bilan['CHANGEMENT_BILAN_AUTRE_ID'];
            $CHANGEMENT_BILAN_VALEUR=$tab_rq_info_bilan['CHANGEMENT_BILAN_VALEUR'];
            $CHANGEMENT_BILAN_COM=$tab_rq_info_bilan['CHANGEMENT_BILAN_COM'];
            $CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_ID]=$CHANGEMENT_BILAN_VALEUR;
            $CHANGEMENT_BILAN_CONFIG_COM_COM[$CHANGEMENT_BILAN_CONFIG_ID]=$CHANGEMENT_BILAN_COM;
            $CHANGEMENT_BILAN_CONFIG_NOM=$CHANGEMENT_BILAN_CONFIG_ID.'_'.$CHANGEMENT_BILAN_AUTRE_ID;
            $CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_NOM]=$CHANGEMENT_BILAN_VALEUR;

          } while ($tab_rq_info_bilan = mysql_fetch_assoc($res_rq_info_bilan));
          $ligne= mysql_num_rows($res_rq_info_bilan);
          if($ligne > 0) {
          mysql_data_seek($res_rq_info_bilan, 0);
          $tab_rq_info_bilan = mysql_fetch_assoc($res_rq_info_bilan);
          }
          }
	mysql_free_result($res_rq_info_bilan);
	}
}


if($action=="Modif"){
	$rq_info_bilan="
	SELECT `changement_status`.`CHANGEMENT_STATUS` 
	FROM `changement_liste` , `changement_status` 
	WHERE `changement_liste`.`CHANGEMENT_LISTE_ID` = '".$ID."'
	AND `changement_liste`.`CHANGEMENT_STATUS_ID` = `changement_status`.`CHANGEMENT_STATUS_ID` 
	AND `changement_liste`.`ENABLE` = '0'
	";
	$res_rq_info_bilan = mysql_query($rq_info_bilan, $mysql_link) or die(mysql_error());
	$tab_rq_info_bilan = mysql_fetch_assoc($res_rq_info_bilan);
	$total_ligne_rq_info_bilan=mysql_num_rows($res_rq_info_bilan);
	if($total_ligne_rq_info_bilan==0){
		$CHANGEMENT_STATUS='Brouillon';
	}else{
		$CHANGEMENT_STATUS=$tab_rq_info_bilan['CHANGEMENT_STATUS'];;		
	}
	mysql_free_result($res_rq_info_bilan);
	if($STOP==0){
	$rq_info_bilan="
	SELECT *
	FROM `changement_bilan`
        WHERE `CHANGEMENT_LISTE_ID` = '".$ID."'
        AND ENABLE ='0'
	";
	$res_rq_info_bilan = mysql_query($rq_info_bilan, $mysql_link) or die(mysql_error());
	$tab_rq_info_bilan = mysql_fetch_assoc($res_rq_info_bilan);
	$total_ligne_rq_info_bilan=mysql_num_rows($res_rq_info_bilan);
	if($total_ligne_rq_info_bilan!=0){
          do {
            $CHANGEMENT_BILAN_ID=$tab_rq_info_bilan['CHANGEMENT_BILAN_ID'];
            $CHANGEMENT_LISTE_ID=$tab_rq_info_bilan['CHANGEMENT_LISTE_ID'];
            $CHANGEMENT_BILAN_CONFIG_ID=$tab_rq_info_bilan['CHANGEMENT_BILAN_CONFIG_ID'];
            $CHANGEMENT_BILAN_UTILISATEUR_ID=$tab_rq_info_bilan['CHANGEMENT_BILAN_UTILISATEUR_ID'];
            $CHANGEMENT_BILAN_AUTRE_ID=$tab_rq_info_bilan['CHANGEMENT_BILAN_AUTRE_ID'];
            $CHANGEMENT_BILAN_VALEUR=$tab_rq_info_bilan['CHANGEMENT_BILAN_VALEUR'];
            $CHANGEMENT_BILAN_COM=$tab_rq_info_bilan['CHANGEMENT_BILAN_COM'];
            $CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_ID]=$CHANGEMENT_BILAN_VALEUR;
            $CHANGEMENT_BILAN_CONFIG_COM_COM[$CHANGEMENT_BILAN_CONFIG_ID]=$CHANGEMENT_BILAN_COM;
            $CHANGEMENT_BILAN_CONFIG_NOM=$CHANGEMENT_BILAN_CONFIG_ID.'_'.$CHANGEMENT_BILAN_AUTRE_ID;
            $CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_NOM]=$CHANGEMENT_BILAN_VALEUR;

          } while ($tab_rq_info_bilan = mysql_fetch_assoc($res_rq_info_bilan));
          $ligne= mysql_num_rows($res_rq_info_bilan);
          if($ligne > 0) {
          mysql_data_seek($res_rq_info_bilan, 0);
          $tab_rq_info_bilan = mysql_fetch_assoc($res_rq_info_bilan);
          }
          }
	mysql_free_result($res_rq_info_bilan);
	}
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
      Cr&eacute;ation de la fiche Bilan du changement n&deg; <a href="#" class="infobulledroite">'.$ID.'<span>'.$AFF_SPAN.'</span></a>';
    }
    if($action=="Modif"){
        if($ROLE==0){
          echo 'Modification de la fiche Bilan du changement n&deg; <a href="#" class="infobulledroite">'.$ID.'<span>'.$AFF_SPAN.'</span></a>';
        }else{
          echo 'Information de la fiche Bilan du changement n&deg; <a href="#" class="infobulledroite">'.$ID.'<span>'.$AFF_SPAN.'</span></a>';
        }
    }
    if($action=="Info"){
          echo 'Information de la fiche Bilan du changement n&deg; <a href="#" class="infobulledroite">'.$ID.'<span>'.$AFF_SPAN.'</span></a>';
    }
    
    echo '&nbsp;]&nbsp;</h2></td>		
      </tr>';

    $rq_info_bilan_lib="
	SELECT DISTINCT (`CHANGEMENT_BILAN_CONFIG_LIB`) AS `CHANGEMENT_BILAN_CONFIG_LIB` 
	FROM `changement_bilan_config` 
	WHERE `ENABLE` = '0'
	ORDER BY `CHANGEMENT_BILAN_CONFIG_LIB` 
      ";
      if($action=='Modif'||$action=='Info'){
        $rq_info_bilan_lib="       
        SELECT DISTINCT (`changement_bilan_config`.`CHANGEMENT_BILAN_CONFIG_LIB`) AS `CHANGEMENT_BILAN_CONFIG_LIB`
        FROM `changement_bilan_config` , `changement_bilan`
        WHERE `changement_bilan_config`.`CHANGEMENT_BILAN_CONFIG_ID` = `changement_bilan`.`CHANGEMENT_BILAN_CONFIG_ID`
        AND `changement_bilan`.`CHANGEMENT_LISTE_ID` = '".$ID."'
        AND `changement_bilan`.`ENABLE` = '0'
        ORDER BY `changement_bilan_config`.`CHANGEMENT_BILAN_CONFIG_LIB`";
      }
      $res_rq_info_bilan_lib = mysql_query($rq_info_bilan_lib, $mysql_link) or die(mysql_error());
      $tab_rq_info_bilan_lib = mysql_fetch_assoc($res_rq_info_bilan_lib);
      $total_ligne_rq_info_bilan_lib=mysql_num_rows($res_rq_info_bilan_lib);
      if($total_ligne_rq_info_bilan_lib!=0){
        do {
        	$CHANGEMENT_BILAN_CONFIG_LIB=$tab_rq_info_bilan_lib['CHANGEMENT_BILAN_CONFIG_LIB'];
      echo '
      <tr align="center" class="titre">
        <td colspan="2">&nbsp;'.stripslashes(substr($CHANGEMENT_BILAN_CONFIG_LIB,strpos($CHANGEMENT_BILAN_CONFIG_LIB,"-")+1)).'&nbsp;</td>		
      </tr>';
              	$rq_info_bilan="
              	SELECT * 
              	FROM `changement_bilan_config`
              	WHERE `CHANGEMENT_BILAN_CONFIG_LIB`='".$CHANGEMENT_BILAN_CONFIG_LIB."' AND 
              	`ENABLE` ='0'
              	ORDER BY `CHANGEMENT_BILAN_CONFIG_ORDRE`
              	";
              	if($action=='Modif'||$action=='Info'){
                  $rq_info_bilan="
                  SELECT 
                  `changement_bilan_config`.`CHANGEMENT_BILAN_CONFIG_ID`, 
                  `changement_bilan_config`.`CHANGEMENT_BILAN_CONFIG_CRITERE`, 
                  `changement_bilan_config`.`CHANGEMENT_BILAN_CONFIG_TYPE`,
                  `changement_bilan_config`.`CHANGEMENT_BILAN_CONFIG_COM`
                  FROM `changement_bilan_config` , `changement_bilan`
                  WHERE `changement_bilan_config`.`CHANGEMENT_BILAN_CONFIG_ID` = `changement_bilan`.`CHANGEMENT_BILAN_CONFIG_ID`
                  AND `changement_bilan`.`CHANGEMENT_LISTE_ID` = '".$ID."'
                  AND `changement_bilan_config`.`CHANGEMENT_BILAN_CONFIG_LIB`='".$CHANGEMENT_BILAN_CONFIG_LIB."' 
                  AND `changement_bilan`.`ENABLE` = '0'
                  GROUP BY `changement_bilan_config`.`CHANGEMENT_BILAN_CONFIG_CRITERE`
                  ORDER BY `changement_bilan_config`.`CHANGEMENT_BILAN_CONFIG_LIB`
                  
                  
                  ";
              	}
	      $res_rq_info_bilan = mysql_query($rq_info_bilan, $mysql_link) or die(mysql_error());
	      $tab_rq_info_bilan = mysql_fetch_assoc($res_rq_info_bilan);
	      $total_ligne_rq_info_bilan=mysql_num_rows($res_rq_info_bilan);
	      if($total_ligne_rq_info_bilan!=0){
	        do {
	        	$CHANGEMENT_BILAN_CONFIG_ID=$tab_rq_info_bilan['CHANGEMENT_BILAN_CONFIG_ID'];
	        	$CHANGEMENT_BILAN_CONFIG_CRITERE=$tab_rq_info_bilan['CHANGEMENT_BILAN_CONFIG_CRITERE'];
	        	$CHANGEMENT_BILAN_CONFIG_TYPE=$tab_rq_info_bilan['CHANGEMENT_BILAN_CONFIG_TYPE'];
	        	$CHANGEMENT_BILAN_CONFIG_COM=$tab_rq_info_bilan['CHANGEMENT_BILAN_CONFIG_COM'];
	        	if(!isset($CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_ID])){$CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_ID]='';}
	        	if(!isset($CHANGEMENT_BILAN_CONFIG_COM_COM[$CHANGEMENT_BILAN_CONFIG_ID])){$CHANGEMENT_BILAN_CONFIG_COM_COM[$CHANGEMENT_BILAN_CONFIG_ID]='';}
	       $j++;
		    if ($j%2) { $class = "pair";}else{$class = "impair";} 
		  echo '
		    <tr class="'.$class.'">
		     <td align="left">&nbsp;'.stripslashes($CHANGEMENT_BILAN_CONFIG_CRITERE).'&nbsp;*&nbsp;</td>
		     <td align="left">&nbsp;';
		     
		      switch ($CHANGEMENT_BILAN_CONFIG_TYPE)
            {
            case "oui-non": 
              echo '
              <INPUT type="radio" '.$disabled_var.' name="CHANGEMENT_BILAN_CONFIG_ID_'.$CHANGEMENT_BILAN_CONFIG_ID.'" value="Oui"';
              if($CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_ID]=='Oui'){echo ' CHECKED';} 
              echo '>
              &nbsp;Oui&nbsp;/&nbsp;Non&nbsp;
              <INPUT type="radio" '.$disabled_var.' name="CHANGEMENT_BILAN_CONFIG_ID_'.$CHANGEMENT_BILAN_CONFIG_ID.'" value="Non"'; 
              if($CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_ID]=='Non'){echo ' CHECKED';} 
              echo '>';
              if($CHANGEMENT_BILAN_CONFIG_COM=='oui'){
              	if($action!='Info'){
              	echo '&nbsp;-&nbsp;<input '.$readonly_var.' id="CHANGEMENT_BILAN_CONFIG_ID_COM_'.$CHANGEMENT_BILAN_CONFIG_ID.'" name="CHANGEMENT_BILAN_CONFIG_ID_COM_'.$CHANGEMENT_BILAN_CONFIG_ID.'" type="text" value="'.stripslashes($CHANGEMENT_BILAN_CONFIG_COM_COM[$CHANGEMENT_BILAN_CONFIG_ID]).'" size="70" maxlength="70"/>';
        	}else{
        	echo '&nbsp;-&nbsp;'.stripslashes($CHANGEMENT_BILAN_CONFIG_COM_COM[$CHANGEMENT_BILAN_CONFIG_ID]).'';
        	}
              }
            break;
            
            case "liste_changement_bilan_personne": 
		$rq_info_personne="
		SELECT `CHANGEMENT_BILAN_PERSONNE_ID` , `CHANGEMENT_BILAN_PERSONNE_LIB` 
		FROM `changement_bilan_personne` 
		WHERE `ENABLE` = '0'
		AND `CHANGEMENT_BILAN_PERSONNE_LIB` != 'Total'
		ORDER BY `CHANGEMENT_BILAN_PERSONNE_LIB`";
		$res_rq_info_personne = mysql_query($rq_info_personne, $mysql_link) or die(mysql_error());
		$tab_rq_info_personne = mysql_fetch_assoc($res_rq_info_personne);
		$total_ligne_rq_info_personne=mysql_num_rows($res_rq_info_personne);
		if($total_ligne_rq_info_personne!=0){
		do {
			$CHANGEMENT_BILAN_PERSONNE_ID=$tab_rq_info_personne['CHANGEMENT_BILAN_PERSONNE_ID'];
			$CHANGEMENT_BILAN_PERSONNE_LIB=$tab_rq_info_personne['CHANGEMENT_BILAN_PERSONNE_LIB'];
			$CHANGEMENT_BILAN_CONFIG_NOM=$CHANGEMENT_BILAN_CONFIG_ID.'_'.$CHANGEMENT_BILAN_PERSONNE_ID;
			if(!isset($CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_NOM])){
	        		$CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_NOM]='0';
	        	}
			if($action!='Info'){
			echo '&nbsp;'.$CHANGEMENT_BILAN_PERSONNE_LIB.': <input '.$readonly_var.' id="CHANGEMENT_BILAN_CONFIG_ID_'.$CHANGEMENT_BILAN_CONFIG_ID.'_'.$CHANGEMENT_BILAN_PERSONNE_ID.'" name="CHANGEMENT_BILAN_CONFIG_ID_'.$CHANGEMENT_BILAN_CONFIG_ID.'_'.$CHANGEMENT_BILAN_PERSONNE_ID.'" type="text" value="'.stripslashes($CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_NOM]).'" size="2" maxlength="2"/>&nbsp;';
			}else{
			echo '&nbsp;'.$CHANGEMENT_BILAN_PERSONNE_LIB.': '.stripslashes($CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_NOM]).'&nbsp;';
			}
		
		} while ($tab_rq_info_personne = mysql_fetch_assoc($res_rq_info_personne));
		$ligne= mysql_num_rows($res_rq_info_personne);
		if($ligne > 0) {
		mysql_data_seek($res_rq_info_personne, 0);
		$tab_rq_info_personne = mysql_fetch_assoc($res_rq_info_personne);
		}
		}
		mysql_free_result($res_rq_info_personne);
		$rq_info_personne="
		SELECT `CHANGEMENT_BILAN_PERSONNE_ID` , `CHANGEMENT_BILAN_PERSONNE_LIB` 
		FROM `changement_bilan_personne` 
		WHERE `ENABLE` = '0'
		AND `CHANGEMENT_BILAN_PERSONNE_LIB` = 'Total'
		ORDER BY `CHANGEMENT_BILAN_PERSONNE_LIB`";
		$res_rq_info_personne = mysql_query($rq_info_personne, $mysql_link) or die(mysql_error());
		$tab_rq_info_personne = mysql_fetch_assoc($res_rq_info_personne);
		$total_ligne_rq_info_personne=mysql_num_rows($res_rq_info_personne);
		if($total_ligne_rq_info_personne!=0){
		do {
			$CHANGEMENT_BILAN_PERSONNE_ID=$tab_rq_info_personne['CHANGEMENT_BILAN_PERSONNE_ID'];
			$CHANGEMENT_BILAN_PERSONNE_LIB=$tab_rq_info_personne['CHANGEMENT_BILAN_PERSONNE_LIB'];
			$CHANGEMENT_BILAN_CONFIG_NOM=$CHANGEMENT_BILAN_CONFIG_ID.'_'.$CHANGEMENT_BILAN_PERSONNE_ID;
			if(!isset($CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_NOM])){
	        		$CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_NOM]='0';
	        	}
	        	if($action!='Info'){
			echo '&nbsp;'.$CHANGEMENT_BILAN_PERSONNE_LIB.': <input '.$readonly_var.' id="CHANGEMENT_BILAN_CONFIG_ID_'.$CHANGEMENT_BILAN_CONFIG_ID.'_'.$CHANGEMENT_BILAN_PERSONNE_ID.'" name="CHANGEMENT_BILAN_CONFIG_ID_'.$CHANGEMENT_BILAN_CONFIG_ID.'_'.$CHANGEMENT_BILAN_PERSONNE_ID.'" type="text" value="'.stripslashes($CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_NOM]).'" size="2" maxlength="2"/>&nbsp;';
			}else{
			echo '&nbsp;'.$CHANGEMENT_BILAN_PERSONNE_LIB.': '.stripslashes($CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_NOM]).'&nbsp;';
			}
		
		} while ($tab_rq_info_personne = mysql_fetch_assoc($res_rq_info_personne));
		$ligne= mysql_num_rows($res_rq_info_personne);
		if($ligne > 0) {
		mysql_data_seek($res_rq_info_personne, 0);
		$tab_rq_info_personne = mysql_fetch_assoc($res_rq_info_personne);
		}
		}
		mysql_free_result($res_rq_info_personne);
            break;
            
            case "text": 
            if($action!='Info'){
              echo '<textarea '.$readonly_var.' id="CHANGEMENT_BILAN_CONFIG_ID_'.$CHANGEMENT_BILAN_CONFIG_ID.'" name="CHANGEMENT_BILAN_CONFIG_ID_'.$CHANGEMENT_BILAN_CONFIG_ID.'" cols="70" rows="2">'.stripslashes($CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_ID]).'</textarea>';
        	}else{
        	echo nl2br(stripslashes($CHANGEMENT_BILAN_CONFIG[$CHANGEMENT_BILAN_CONFIG_ID]));
        	}
            break;
            
            }
            
		     echo '
		     &nbsp;</td>
		   </tr>';
	        	
	        } while ($tab_rq_info_bilan = mysql_fetch_assoc($res_rq_info_bilan));
	        $ligne= mysql_num_rows($res_rq_info_bilan);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_bilan, 0);
	          $tab_rq_info_bilan = mysql_fetch_assoc($res_rq_info_bilan);
	        }
	        mysql_free_result($res_rq_info_bilan);
	}

        	
        } while ($tab_rq_info_bilan_lib = mysql_fetch_assoc($res_rq_info_bilan_lib));
        $ligne= mysql_num_rows($res_rq_info_bilan_lib);
        if($ligne > 0) {
          mysql_data_seek($res_rq_info_bilan_lib, 0);
          $tab_rq_info_bilan_lib = mysql_fetch_assoc($res_rq_info_bilan_lib);
        }
        mysql_free_result($res_rq_info_bilan_lib);
        
      }

   if($STOP==1){
   	$j++;
	   if ($j%2) { $class = "pair";}else{$class = "impair";} 
	   echo '
	   <tr class="'.$class.'">
	    <td align="center" colspan="4"><font color=#993333><b>Merci de saisir l\'ensemble des champs obligatoire.</b></font>
	    </td>
	   </tr>';
   }
   if($STOP_OUI_NON==1){
   	$j++;
	   if ($j%2) { $class = "pair";}else{$class = "impair";} 
	   echo '
	   <tr class="'.$class.'">
	    <td align="center" colspan="4"><font color=#993333><b>Merci de saisir l\'ensemble des champs OUI-NON obligatoire.</b></font>
	    </td>
	   </tr>';
   }
   if($STOP_TEXT==1){
   	$j++;
	   if ($j%2) { $class = "pair";}else{$class = "impair";} 
	   echo '
	   <tr class="'.$class.'">
	    <td align="center" colspan="4"><font color=#993333><b>Merci de saisir l\'ensemble des champs Commentaires obligatoire.</b></font>
	    </td>
	   </tr>';
   }
   if($STOP_PERSO==1){
   	$j++;
	   if ($j%2) { $class = "pair";}else{$class = "impair";} 
	   echo '
	   <tr class="'.$class.'">
	    <td align="center" colspan="4"><font color=#993333><b>Merci de saisir des nombres.</b></font>
	    </td>
	   </tr>';
   }
   if($STOP_SOMME==1){
   	$j++;
	   if ($j%2) { $class = "pair";}else{$class = "impair";} 
	   echo '
	   <tr class="'.$class.'">
	    <td align="center" colspan="4"><font color=#993333><b>Merci de faire la verification d\'une somme.</b></font>
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
		switch ($CHANGEMENT_STATUS){
		case "Brouillon": 
		break;
		
		case "Inscrit": 
		break;
		
		case "Valid&eacute;": 
			if($ROLE==0){
				echo '<input name="btn" type="submit" id="btn" value="Modifier">';
			}
		break;
		
		case "Termin&eacute;": 
			if($ROLE==0){
		        		echo '<input name="btn" type="submit" id="btn" value="Modifier">';
			}
		break;
		
		case "Clotur&eacute;": 
		break;
		
		case "Abandonn&eacute;": 
		break;
		}
		 
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
	echo '</td>
	</tr>';

echo '
</table>
</form>
</div>
';

mysql_close($mysql_link); 
?>