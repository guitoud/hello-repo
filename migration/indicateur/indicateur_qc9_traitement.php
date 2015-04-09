<?php
//redirection si acces dirrect
if(substr_count($_SERVER['PHP_SELF'], 'index.php')==0){
  header("Location: ../"); 
  exit(); 
}
$j=0;
$Mois_critere=date("m"); 
$Annee_critere=date("Y"); 
if($Mois_critere==1){
	$Mois_critere=12;
	$Annee_critere=$Annee_critere-1;
}else{
	$Mois_critere=$Mois_critere-1;
}

$NUMERO='';
$STATUS='';
$AFFECTE='';
$DATE_SOUHAITEE='';
$APPLICATION_PROCESSUS='';
$DATE_PREVUE='';
$DATE_TRANSMISE='';
$DEMANDEUR='';
$ENVIRONNEMENT='';
$NATURE='';
$RECETTE='';
$TEMPS_JOURS='';
$TEMPS_MINUTES='';
$DATE_J_P='';
$DATE_M_P='';
$DATE_A_P='';
$DATE_J_S='';
$DATE_M_S='';
$DATE_A_S='';
$DATE_J_T='';
$DATE_M_T='';
$DATE_A_T='';
$APPLICATION='';
$PROCESSUS='';
$TEMPS_MINUTES_OK='';
$DATE_INDICATEUR='';
$paramJourHVH = 4;
require_once("./cf/autre_fonctions.php");
// donne des information sur une date anne , mois , jour
function Return_info_date($date_en)
{
	if($date_en==''){
		$jour_en='';
		$mois_en='';
		$annee_en='';
		$date_en='';
	}else{
		$liste = explode('/', $date_en); // lecture du s&eacute;parateur ;
		$jour_en=$liste[0];
		$mois_en=$liste[1];
		$annee_en=$liste[2];
		$date_en=$annee_en.$mois_en.$jour_en;
	}
	return array($date_en,$jour_en,$mois_en,$annee_en);	
}
// donne le temps en minutes
function Return_minutes($TEMPS_MINUTES)
{
	$TEMPS_MINUTES_OK=$TEMPS_MINUTES;
	if($TEMPS_MINUTES==''){
		$TEMPS_MINUTES_OK=15;
	}
	if($TEMPS_MINUTES<=15){
		$TEMPS_MINUTES_OK=15;
	}
	return $TEMPS_MINUTES_OK;	
}
// donne l'acteur et si sogeti
function Return_acteur($ASSIGNE,$mysql_link)
{
	if($ASSIGNE==''){
		$total_ligne_rq_info=0;
	}else{
		$liste = explode('_', $ASSIGNE); // lecture du s&eacute;parateur ;
		$PRENOM=strtoupper($liste[1]);
		$NOM=strtoupper($liste[0]);
		$rq_info="
		SELECT LEFT(UPPER(`LOGIN`),3) AS `LOGIN` ,UPPER(`SOCIETE`) AS `SOCIETE`
		FROM `moteur_utilisateur` 
		WHERE UPPER(`NOM`)='".$NOM."' AND UPPER(`PRENOM`) LIKE '".$PRENOM."%'
		LIMIT 1";
		$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
		$tab_rq_info = mysql_fetch_assoc($res_rq_info);
		$total_ligne_rq_info=mysql_num_rows($res_rq_info);
		mysql_free_result($res_rq_info);
	}
	if($total_ligne_rq_info==0){
		$SOGETI="N";
	}else{
		if($tab_rq_info['SOCIETE']=='SOGETI'){
			$SOGETI="Y";
		}else{
			$SOGETI="N";
		}
		
	}
	return array($ASSIGNE,$SOGETI);
}

// donne le poids en valeur
function Return_poids_valeur($NATURE,$mysql_link)
{
	if($NATURE==''){
		$total_ligne_rq_info=0;
	}else{
		$rq_info="
		SELECT `INDICATEUR_REGLE_POIDS` 
		FROM `indicateur_regles` 
		WHERE `INDICATEUR_REGLE_TYPE`='QC9' AND 
		UPPER(`INDICATEUR_REGLE_INFO`)=UPPER('".$NATURE."') AND 
		`ENABLE`=0
		LIMIT 1";
		$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
		$tab_rq_info = mysql_fetch_assoc($res_rq_info);
		$total_ligne_rq_info=mysql_num_rows($res_rq_info);
		mysql_free_result($res_rq_info);
	}
	if($total_ligne_rq_info==0){
		$VALEUR=0;
	}else{
		$VALEUR=$tab_rq_info['INDICATEUR_REGLE_POIDS'];
	}
	return $VALEUR;
}
// donne le poids et le niveau
function Return_NIVEAU_POIDS($DUREE,$mysql_link)
{
	if($DUREE==0){
		$NIVEAU='VIDE';
		$POIDS=0;
	}else{
		$rq_complex_info="SELECT * FROM `indicateur_duree` WHERE '".$DUREE."' >= `INDICATEUR_DUREE_TEMPS_MINI` AND '".$DUREE."' < `INDICATEUR_DUREE_TEMPS_MAX` AND `ENABLE` =0 LIMIT 1 ";
		$res_rq_complex_info = mysql_query($rq_complex_info, $mysql_link) or die(mysql_error());
		$tab_rq_complex_info = mysql_fetch_assoc($res_rq_complex_info);
		$total_ligne_rq_complex_info=mysql_num_rows($res_rq_complex_info);
		if($total_ligne_rq_complex_info==0){
			$NIVEAU='VIDE';
			$POIDS=0;
		}else{
			$NIVEAU=$tab_rq_complex_info['INDICATEUR_DUREE_LIB'];
			$POIDS=$tab_rq_complex_info['INDICATEUR_DUREE_POIDS'];
		}
		mysql_free_result($res_rq_complex_info);
	}
	return array($NIVEAU,$POIDS);
}
// donne l'appli et le processus
function Return_app_proc($APPLICATION_PROCESSUS,$mysql_link)
{
	if($APPLICATION_PROCESSUS==''){
		$APPLICATION='';
		$PROCESSUS='';
	}else{
		if(substr_count($APPLICATION_PROCESSUS, '-')==0){
			$APPLICATION=$APPLICATION_PROCESSUS;
			$PROCESSUS='';
		}else{
			$liste = explode('-', $APPLICATION_PROCESSUS); // lecture du s&eacute;parateur ;
			$APPLICATION=strtoupper($liste[0]);
			$PROCESSUS=strtoupper($liste[1]);
		}
	}
	return array($APPLICATION,$PROCESSUS);
}

if (empty($_POST)){
}else{
	if(!empty($_POST['execution'])){
		$ANNEE=$_POST['Annee'];
		$MOIS=$_POST['Mois'];
		$Mois_critere=$MOIS; 
		$Annee_critere=$ANNEE; 
		$AMMEE_MIN=$ANNEE;
		$AMMEE_MAX=$ANNEE;
		$MOIS_MIN=$MOIS-1;
		$MOIS_MAX=$MOIS+1;
		if($MOIS==1){
			$MOIS_MIN=12;
			$AMMEE_MIN=$ANNEE-1;
		}
		if($MOIS==12){
			$MOIS_MAX=1;
			$AMMEE_MAX=$ANNEE+1;
		}
		if($MOIS<10){
			$MOIS='0'.$MOIS;		
		}
		if($MOIS_MIN<10){
			$MOIS_MIN='0'.$MOIS_MIN;		
		}
		if($MOIS_MAX<10){
			$MOIS_MAX='0'.$MOIS_MAX;		
		}
		$DATE_INDICATEUR=$ANNEE.$MOIS;
		$DATE_MIN=$AMMEE_MIN.$MOIS_MIN;
		$DATE_MAX=$AMMEE_MAX.$MOIS_MAX;
		
// on supprimme les donn&eacute;es si l'on a deja fait un calcul		
		$sql="DELETE FROM `indicateur_qc9_extract_archive` WHERE `DATE_INDICATEUR` ='".$DATE_INDICATEUR."'";
		mysql_query($sql, $mysql_link) or die('Erreur SQL !'.$sql.''.mysql_error());
		$OptTable=mysql_query("OPTIMIZE TABLE `indicateur_qc9_extract_archive `",$mysql_link);
		
		$sql="DELETE FROM `indicateur_qc9_calcul` WHERE `DATE_INDICATEUR` ='".$DATE_INDICATEUR."'";
		mysql_query($sql, $mysql_link) or die('Erreur SQL !'.$sql.''.mysql_error());
		$OptTable=mysql_query("OPTIMIZE TABLE `indicateur_qc9_calcul`",$mysql_link);
// on traite l import
		
	  $rq_info_extract="SELECT * FROM `indicateur_qc9_extract_brut`"; 
          $res_rq_info_extract = mysql_query($rq_info_extract, $mysql_link) or die(mysql_error());
          $tab_rq_info_extract = mysql_fetch_assoc($res_rq_info_extract);
          $total_ligne_rq_info_extract=mysql_num_rows($res_rq_info_extract); 
          if ($total_ligne_rq_info_extract==0){
            $erreur="Pas d\'information dans la base.";
          }else{
          
          do {
          	
            $NUMERO=addslashes($tab_rq_info_extract['NUMERO']);
            $STATUS=addslashes($tab_rq_info_extract['STATUS']);
            $AFFECTE=addslashes($tab_rq_info_extract['AFFECTE']);
            $DATE_SOUHAITEE=addslashes($tab_rq_info_extract['DATE_SOUHAITEE']);
            $APPLICATION_PROCESSUS=addslashes($tab_rq_info_extract['APPLICATION_PROCESSUS']);
            $DATE_PREVUE=addslashes($tab_rq_info_extract['DATE_PREVUE']);
            $DATE_TRANSMISE=addslashes($tab_rq_info_extract['DATE_TRANSMISE']);
            $DEMANDEUR=addslashes($tab_rq_info_extract['DEMANDEUR']);
            $ENVIRONNEMENT=addslashes($tab_rq_info_extract['ENVIRONNEMENT']);
            $NATURE=addslashes($tab_rq_info_extract['NATURE']);
            $RECETTE=addslashes($tab_rq_info_extract['RECETTE']);
            $TEMPS_JOURS=addslashes($tab_rq_info_extract['TEMPS_JOURS']);
            $TEMPS_MINUTES=addslashes($tab_rq_info_extract['TEMPS_MINUTES']);

            ########## Bloc De sauvegarde dans la base ###########
            list($DATE_PREVUE,$DATE_J_P, $DATE_M_P,$DATE_A_P) = Return_info_date($DATE_PREVUE);
            list($DATE_SOUHAITEE,$DATE_J_S, $DATE_M_S,$DATE_A_S) = Return_info_date($DATE_SOUHAITEE);
            list($DATE_TRANSMISE,$DATE_J_T, $DATE_M_T,$DATE_A_T) = Return_info_date($DATE_TRANSMISE);
            list($APPLICATION,$PROCESSUS) = Return_app_proc($APPLICATION_PROCESSUS,$mysql_link);
            $TEMPS_MINUTES_OK=Return_minutes($TEMPS_MINUTES);
            $DATE_TEST=$DATE_A_P.$DATE_M_P;
            if($DATE_A_P==''){
            	$DATE_TEST=$DATE_A_S.$DATE_M_S;
            }
            	if($DATE_TEST >= $DATE_MIN){
            		if($DATE_TEST <= $DATE_MAX){
            			$sql="INSERT INTO `indicateur_qc9_extract_archive` (`ID` ,`NUMERO` ,`STATUS` ,`AFFECTE` ,`DATE_SOUHAITEE` ,`APPLICATION_PROCESSUS` ,`DATE_PREVUE` ,`DATE_TRANSMISE` ,`DEMANDEUR` ,`ENVIRONNEMENT` ,`NATURE` ,`RECETTE` ,`TEMPS_JOURS` ,`TEMPS_MINUTES` ,`DATE_INDICATEUR` ) VALUES (NULL , '".$NUMERO."', '".$STATUS."', '".$AFFECTE."', '".$DATE_SOUHAITEE."', '".$APPLICATION_PROCESSUS."', '".$DATE_PREVUE."', '".$DATE_TRANSMISE."', '".$DEMANDEUR."', '".$ENVIRONNEMENT."', '".$NATURE."', '".$RECETTE."', '".$TEMPS_JOURS."', '".$TEMPS_MINUTES."', '".$DATE_INDICATEUR."');";
		        	mysql_query($sql, $mysql_link) or die('Erreur SQL !'.$sql.''.mysql_error());


			    ########## Bloc De traitement des informations ###########
			   
		
		// on traite l acteur
			   list($ACTEUR,$SOGETI)=Return_acteur($AFFECTE,$mysql_link);
		// on traite la valeur
			$VALEUR=Return_poids_valeur($NATURE,$mysql_link);
		// on traite le poids et le niveau
			list($NIVEAU,$POIDS)=Return_NIVEAU_POIDS($TEMPS_MINUTES_OK,$mysql_link);
		 //echo 'NUMERO : '.$NUMERO.' - ACTEUR : '.$ACTEUR.' - SOGETI : '.$SOGETI.' - DATE_PREVUE : '.$DATE_PREVUE.'</BR>';

			  // echo '</BR>';
			   
			   $sql="INSERT INTO `indicateur_qc9_calcul` (`ID` ,`NUMERO` ,`STATUS` ,`AFFECTE` ,`SOGETI` ,`DATE_SOUHAITEE` ,`APPLICATION` ,`PROCESSUS` ,`DATE_PREVUE` ,`DATE_TRANSMISE` ,`DEMANDEUR` ,`ENVIRONNEMENT` ,`NATURE` ,`RECETTE` ,`TEMPS_JOURS` ,`TEMPS_MINUTES` ,`TEMPS_MINUTES_OK` ,`VALEUR`,`POIDS`,`NIVEAU` ,`DATE_INDICATEUR` ) VALUES (NULL , '".$NUMERO."', '".$STATUS."', '".$ACTEUR."', '".$SOGETI."', '".$DATE_SOUHAITEE."', '".$APPLICATION."', '".$PROCESSUS."', '".$DATE_PREVUE."', '".$DATE_TRANSMISE."', '".$DEMANDEUR."', '".$ENVIRONNEMENT."', '".$NATURE."', '".$RECETTE."', '".$TEMPS_JOURS."', '".$TEMPS_MINUTES."', '".$TEMPS_MINUTES_OK."','".$VALEUR."','".$POIDS."','".$NIVEAU."' , '".$DATE_INDICATEUR."');";
			   //echo $sql.'<BR>';
			   mysql_query($sql, $mysql_link) or die('Erreur SQL !'.$sql.''.mysql_error());
	   
	   		}	
        	}
        	
          } while ($tab_rq_info_extract = mysql_fetch_assoc($res_rq_info_extract));
          $ligne= mysql_num_rows($res_rq_info_extract);
          if($ligne > 0) {
            mysql_data_seek($res_rq_info_extract, 0);
            $tab_rq_info_extract = mysql_fetch_assoc($res_rq_info_extract);
          }
        }
        mysql_free_result($res_rq_info_extract);
        $OptTable=mysql_query("OPTIMIZE TABLE `indicateur_qc9_calcul`",$mysql_link);
        
        echo '
        <script language="JavaScript">
        url=("./index.php?ITEM=indicateur_menu");
        window.location=url;
        </script>
        ';
        
	}
}
echo '
<center>
<form method="POST" action="./index.php?ITEM=indicateur_qc9_traitement">
<table class="table_inc" cellspacing="0" cellpading="0">
  <tr align="center" class="titre">
    <td align="center" colspan="2">&nbsp;</td>
  </tr>
  <tr align="center" class="titre">
    <td align="center" colspan="2">&nbsp;traitement des indicateurs QC9&nbsp;</td>
  </tr>
  <tr align="center" class="titre">
    <td align="center" colspan="2">&nbsp;</td>
  </tr>
  <tr align="center" class="impair">
    <td align="center">
       <u>Ann&eacute;e</u> : 
       <select name="Annee" id="Annee" value="">';
       $Annee_temp=$Annee_critere-4;
       for ($k=0;$k<=4; $k++)
       {
	       echo '<option '; if($Annee_critere == $Annee_temp){echo "selected ";} echo 'value="'.$Annee_temp.'">'.$Annee_temp.'</option>';
	       $Annee_temp=$Annee_temp+1;	
       }
         echo '
       </select>
    </td>
    <td align="center"><u>Mois</u> : 
       <select name="Mois" id="Mois" value="">
        ';
           // gestion de l'affichage par defaut dans la liste deroulante du mois en cours	
           
           for ($k=0; $k<sizeof($Tab_des_Mois); $k++)
           {
             $m=$k+1;
               echo '<option '; if($Mois_critere == $m){echo "selected ";} echo 'value="'.$m.'">'.$Tab_des_Mois[$k].'</option>'."\n";
           }
         echo '
       </select>
     </td>
  </tr>
  <tr align="center" class="titre">
    <td align="center" colspan="2">&nbsp;</td>
  </tr>
  <tr align="center" class="titre">
    <td align="center" colspan="2">&nbsp;<input type="submit" name="execution" value="Execution du traitement">&nbsp;</td>
  </tr>
  <tr align="center" class="titre">
    <td align="center" colspan="2">&nbsp;</td>
  </tr>
</table>     
</form>
</center>
';
mysql_close($mysql_link);
?>
