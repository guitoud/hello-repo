<?PHP
//redirection si acces dirrect
if(substr_count($_SERVER['PHP_SELF'], 'index.php')==0){
  header("Location: ../"); 
  exit(); 
}
/*******************************************************************
   Interface Gestion des indicateurs
   Version 1.0.0   
  26/03/2010 - VGU - Creation fichier
**************************************************Guibert Vincent***
*******************************************************************/

require("./cf/conf_outil_icdc.php"); 
require_once("./cf/fonctions.php");
require_once("./cf/autre_fonctions.php");

if(isset($_GET['ANNEE'])){
	$ANNEE=$_GET['ANNEE'];
}else{
	$ANNEE=date("Y");
}
if(isset($_GET['MOIS'])){
	$MOIS=$_GET['MOIS'];
	$colspan=11;
}else{
	$MOIS='';
	$colspan=12;
}
$j=0;
$ID='';

function Return_VALEUR_ODTI($DATE_RECHERCHE,$ANNEE,$MOIS,$APPLICATION,$INDICATEUR_REGLE_ID,$mysql_link)
{
	if($INDICATEUR_REGLE_ID==''){
		$INDICATEUR_REGLE_ID_SQL="";	
	}else{
		$INDICATEUR_REGLE_ID_SQL="`INDICATEUR_REGLE_ID`='".$INDICATEUR_REGLE_ID."' AND";
	}
	if($APPLICATION==''){
		$APPLICATION_SQL="";	
	}else{
		$APPLICATION_SQL="`APPLI`='".$APPLICATION."' AND";
	}
	$rq_info_app="
	SELECT SUM(`VALEUR`) AS `NB` 
	FROM `indicateur_calcul` 
	WHERE
	`DATE_ANNEE`='".$ANNEE."' AND
	`DATE_MOIS`=".$MOIS." AND
	".$APPLICATION_SQL."
	".$INDICATEUR_REGLE_ID_SQL."
	`NATURE`='P' AND
	`DATE_INDICATEUR`='".$DATE_RECHERCHE."'";
	$res_rq_info_app = mysql_query($rq_info_app, $mysql_link) or die(mysql_error());
	$tab_rq_info_app = mysql_fetch_assoc($res_rq_info_app);
	$total_ligne_rq_info_app=mysql_num_rows($res_rq_info_app);
	$NB=$tab_rq_info_app['NB'];
	//echo $rq_info_app.'<BR>';
	mysql_free_result($res_rq_info_app);
	return $NB;
}
function Return_VALEUR_QC9($DATE_RECHERCHE,$ANNEE,$MOIS,$ENVIRONNEMENT,$APPLICATION,$SOGETI,$NATURE,$mysql_link)
{
	if($APPLICATION==''){
		$APPLICATION_SQL="";	
	}else{
		$APPLICATION_SQL="`APPLICATION`='".$APPLICATION."' AND";
	}
	if($NATURE==''){
		$NATURE_SQL="";	
	}else{
		$NATURE_SQL="`NATURE`='".$NATURE."' AND";
	}
	if($SOGETI==''){
		$SOGETI_SQL="";	
	}else{
		if($SOGETI=='Y' || $SOGETI=='N'){
			$SOGETI_SQL="`SOGETI`='".$SOGETI."' AND";
		}else{
			$SOGETI_SQL="";	
		}
	}
	if($ENVIRONNEMENT==''){
		$ENVIRONNEMENT_SQL="";	
	}else{
		$ENVIRONNEMENT_SQL="`ENVIRONNEMENT`='".$ENVIRONNEMENT."' AND";	
	}
	$DATE_PREVUE=$DATE_RECHERCHE;
	$rq_info_app="
	SELECT SUM(`VALEUR`) AS `NB` 
	FROM `indicateur_qc9_calcul` 
	WHERE 
	`STATUS`='6- Termin&eacute;e' AND
	`DATE_PREVUE` LIKE '".$DATE_PREVUE."%' AND
	".$ENVIRONNEMENT_SQL."
	".$APPLICATION_SQL."
	".$NATURE_SQL."
	".$SOGETI_SQL."
	`DATE_INDICATEUR`='".$DATE_RECHERCHE."'";
	$res_rq_info_app = mysql_query($rq_info_app, $mysql_link) or die(mysql_error());
	$tab_rq_info_app = mysql_fetch_assoc($res_rq_info_app);
	$total_ligne_rq_info_app=mysql_num_rows($res_rq_info_app);
	$NB=$tab_rq_info_app['NB'];
	//echo $rq_info_app.'<BR>';
	mysql_free_result($res_rq_info_app);
	return $NB;
}
$rq_info_max_date_qc9="
SELECT MAX(`DATE_INDICATEUR`) AS `MAX`
FROM `indicateur_qc9_calcul` 
WHERE 
`STATUS`='6- Termin&eacute;e' AND
`DATE_PREVUE` LIKE '".$ANNEE."%' AND
`DATE_INDICATEUR` LIKE '".$ANNEE."%'";
$res_rq_info_max_date_qc9 = mysql_query($rq_info_max_date_qc9, $mysql_link) or die(mysql_error());
$tab_rq_info_max_date_qc9 = mysql_fetch_assoc($res_rq_info_max_date_qc9);
$total_ligne_rq_info_max_date_qc9=mysql_num_rows($res_rq_info_max_date_qc9);
$DATE_MAX_QC9=$tab_rq_info_max_date_qc9['MAX'];
$MOIS_DATE_MAX_QC9=substr($DATE_MAX_QC9,4,2);
$ANNEE_DATE_MAX_QC9=substr($DATE_MAX_QC9,0,4);
mysql_free_result($res_rq_info_max_date_qc9);
$rq_info_max_date_odti="
SELECT MAX(`DATE_INDICATEUR`) AS `MAX` 
FROM `indicateur_calcul` 
WHERE
`DATE_ANNEE`='".$ANNEE."' AND
`DATE_INDICATEUR` LIKE '".$ANNEE."%'";
$res_rq_info_max_date_odti = mysql_query($rq_info_max_date_odti, $mysql_link) or die(mysql_error());
$tab_rq_info_max_date_odti = mysql_fetch_assoc($res_rq_info_max_date_odti);
$total_ligne_rq_info_max_date_odti=mysql_num_rows($res_rq_info_max_date_odti);
$DATE_MAX_ODTI=$tab_rq_info_max_date_odti['MAX'];
$MOIS_DATE_MAX_ODTI=substr($DATE_MAX_ODTI,4,2);
$ANNEE_DATE_MAX_ODTI=substr($DATE_MAX_ODTI,0,4);
mysql_free_result($res_rq_info_max_date_odti);
  echo '
   <div align="center">
    <br/>
    <table class="table_inc" cellspacing="1" cellpading="0">
      <tr align="center" class="titre">
        <td align="center" colspan="'.$colspan.'">&nbsp;';
        if($MOIS==''){
        	echo '[&nbsp;<a href="./index.php?ITEM=indicateur_menu">Retour</a>&nbsp;]';
        	if($DATE_MAX_ODTI==$DATE_MAX_QC9){
        		echo '-&nbsp;<a href="./indicateur/indicateur_rapport_pondere_csv.php?ANNEE='.$ANNEE.'"><img src="./img/logo_excel.png" border="0"/></a>&nbsp;';
        	}
        }else{
        	echo '[&nbsp;<a href="./index.php?ITEM=indicateur_rapport_pondere&ANNEE='.$ANNEE.'">Retour</a>&nbsp;]';
        	if($DATE_MAX_ODTI==$DATE_MAX_QC9){
        		echo '-&nbsp;<a href="./indicateur/indicateur_rapport_pondere_csv.php?ANNEE='.$ANNEE.'&MOIS='.$MOIS.'"><img src="./img/logo_excel.png" border="0"/></a>&nbsp;';
        	}
	}
        echo '</td>
      </tr>
      <tr align="center" class="titre">
        <td align="center" colspan="'.$colspan.'">
        <b>&nbsp;Tableau de Bord ODTI/QC9 pour ';
        if($MOIS!=''){echo $Tab_des_Mois[$MOIS-1].' ';}echo $ANNEE.'&nbsp;</b></BR>';
        if($MOIS==''){
	        if($DATE_MAX_ODTI!=$DATE_MAX_QC9){
	        	echo 'ATTENTION il y a un delta sur les dates des donn&eacute;es ODTI et QC9.</BR>';
	        }
	        echo '
	        Date des donn&eacute;es odti : '.$Tab_des_Mois[$MOIS_DATE_MAX_ODTI-1].' '.$ANNEE_DATE_MAX_ODTI.' . Date des donn&eacute;es QC9 : '.$Tab_des_Mois[$MOIS_DATE_MAX_QC9 - 1].' '.$ANNEE_DATE_MAX_QC9.'.';
	}
	echo '
        </td>
      </tr>';
      if($MOIS==''){
      	$j++;
        if ($j%2) { $class = "pair";}else{$class = "impair";} 
	echo '
	<tr align="center" class='.$class.'>
        <td align="center" colspan="'.$colspan.'">
        &nbsp;Détail par mois :&nbsp;</BR>';
        for($i=1;$i<=12;$i++){
        	if($i<=$MOIS_DATE_MAX_ODTI){
        		echo '<a class="LinkDef" href="./index.php?ITEM=indicateur_rapport_pondere&ANNEE='.$ANNEE.'&MOIS='.$i.'">'.$Tab_des_Mois[$i-1].'</a> ';
        	}
        } 
	echo '
        </td>
      </tr>';
	}
	echo '
      <tr align="center" class="titre">
        <td align="center"><b>&nbsp;Application&nbsp;</b></td>
        <td align="center"><b>&nbsp;Aramis&nbsp;</b></td>
        <td align="center"><b>&nbsp;Poids&nbsp;<BR>&nbsp;Pond&eacute;r&eacute;&nbsp;<BR>&nbsp;ODTI&nbsp;</b></td>
        <td align="center"><b>&nbsp;% ODTI&nbsp;</b></td>
        <td align="center"><b>&nbsp;Graph ODTI&nbsp;</b></td>
        <td align="center"><b>&nbsp;Poids&nbsp;<BR>&nbsp;Pond&eacute;r&eacute;&nbsp;<BR>&nbsp;QC9&nbsp;</b></td>
        <td align="center"><b>&nbsp;% QC9&nbsp;</b></td>
        <td align="center"><b>&nbsp;Graph QC9&nbsp;</b></td>
        <td align="center"><b>&nbsp;Total&nbsp;<BR>&nbsp;Pond&eacute;r&eacute;&nbsp;</b></td>
        <td align="center"><b>&nbsp;% Total&nbsp;</b></td>
        <td align="center"><b>&nbsp;Graph Total&nbsp;</b></td>';
        if($MOIS==''){
        	echo '<td align="center"><b>&nbsp;Forcet&nbsp;</b></td>';
        }
        echo '
      </tr>';

      $rq_info_app="
      SELECT DISTINCT(UPPER(`INDICATEUR_APPLICATION_REF`)) AS `APPLICATION`
      FROM `indicateur_application` 
      WHERE `INDICATEUR_APPLICATION_REF` NOT IN ('','Tout')
      ORDER BY `INDICATEUR_APPLICATION_REF` ASC 
      ";
      $res_rq_info_app = mysql_query($rq_info_app, $mysql_link) or die(mysql_error());
      $tab_rq_info_app = mysql_fetch_assoc($res_rq_info_app);
      $total_ligne_rq_info_app=mysql_num_rows($res_rq_info_app); 
      if ($total_ligne_rq_info_app==0){
        $j++;
        if ($j%2) { $class = "pair";}else{$class = "impair";} 
        echo '
        <tr align="center" class='.$class.'>
          <td align="left" colspan="'.$colspan.'">&nbsp;Pas d\'information dans la base&nbsp;</td>
        </tr>';
      }else{
      $VALEUR_QC9_ALL['ALL']=0;
      $VALEUR_ODTI_ALL['ALL']=0;
      $VALEUR_ALL['ALL']=0;
      $APPLICATION='';
      $INDICATEUR_REGLE_ID='';
      $ENVIRONNEMENT='';
      $SOGETI='';
      $NATURE='';

      if($MOIS==''){
	      for($i=1;$i<=12;$i++){
			if($i<10){
				$DATE_RECHERCHE=$ANNEE."0".$i;
				$VALEUR_INTER[$i]=Return_VALEUR_ODTI($DATE_RECHERCHE,$ANNEE,$i,$APPLICATION,$INDICATEUR_REGLE_ID,$mysql_link);
				//$VALEUR_ODTI_ALL[$APPLICATION]=$VALEUR_ODTI_ALL[$APPLICATION]+$VALEUR_INTER[$i];
				$VALEUR_ODTI_ALL['ALL']=$VALEUR_ODTI_ALL['ALL']+$VALEUR_INTER[$i];
				$VALEUR_ALL['ALL']=$VALEUR_ALL['ALL']+$VALEUR_INTER[$i];
				$VALEUR_INTER[$i]=Return_VALEUR_QC9($DATE_RECHERCHE,$ANNEE,$i,$ENVIRONNEMENT,$APPLICATION,$SOGETI,$NATURE,$mysql_link);
				//$VALEUR_QC9_ALL[$APPLICATION]=$VALEUR_QC9_ALL[$APPLICATION]+$VALEUR_INTER[$i];
				$VALEUR_QC9_ALL['ALL']=$VALEUR_QC9_ALL['ALL']+$VALEUR_INTER[$i];
				$VALEUR_ALL['ALL']=$VALEUR_ALL['ALL']+$VALEUR_INTER[$i];
			}else{
				$DATE_RECHERCHE=$ANNEE."".$i;  
				$VALEUR_INTER[$i]=Return_VALEUR_ODTI($DATE_RECHERCHE,$ANNEE,$i,$APPLICATION,$INDICATEUR_REGLE_ID,$mysql_link);
				//$VALEUR_ODTI_ALL[$APPLICATION]=$VALEUR_ODTI_ALL[$APPLICATION]+$VALEUR_INTER[$i];
				$VALEUR_ODTI_ALL['ALL']=$VALEUR_ODTI_ALL['ALL']+$VALEUR_INTER[$i];
				$VALEUR_ALL['ALL']=$VALEUR_ALL['ALL']+$VALEUR_INTER[$i];
				$VALEUR_INTER[$i]=Return_VALEUR_QC9($DATE_RECHERCHE,$ANNEE,$i,$ENVIRONNEMENT,$APPLICATION,$SOGETI,$NATURE,$mysql_link);
				//$VALEUR_QC9_ALL[$APPLICATION]=$VALEUR_QC9_ALL[$APPLICATION]+$VALEUR_INTER[$i];
				$VALEUR_QC9_ALL['ALL']=$VALEUR_QC9_ALL['ALL']+$VALEUR_INTER[$i];
				$VALEUR_ALL['ALL']=$VALEUR_ALL['ALL']+$VALEUR_INTER[$i];
			}            
		}
	}else{
		$i=$MOIS;
		if($i<10){
			$DATE_RECHERCHE=$ANNEE."0".$i;
			$VALEUR_INTER[$i]=Return_VALEUR_ODTI($DATE_RECHERCHE,$ANNEE,$i,$APPLICATION,$INDICATEUR_REGLE_ID,$mysql_link);
			//$VALEUR_ODTI_ALL[$APPLICATION]=$VALEUR_ODTI_ALL[$APPLICATION]+$VALEUR_INTER[$i];
			$VALEUR_ODTI_ALL['ALL']=$VALEUR_ODTI_ALL['ALL']+$VALEUR_INTER[$i];
			$VALEUR_ALL['ALL']=$VALEUR_ALL['ALL']+$VALEUR_INTER[$i];
			$VALEUR_INTER[$i]=Return_VALEUR_QC9($DATE_RECHERCHE,$ANNEE,$i,$ENVIRONNEMENT,$APPLICATION,$SOGETI,$NATURE,$mysql_link);
			//$VALEUR_QC9_ALL[$APPLICATION]=$VALEUR_QC9_ALL[$APPLICATION]+$VALEUR_INTER[$i];
			$VALEUR_QC9_ALL['ALL']=$VALEUR_QC9_ALL['ALL']+$VALEUR_INTER[$i];
			$VALEUR_ALL['ALL']=$VALEUR_ALL['ALL']+$VALEUR_INTER[$i];
		}else{
			$DATE_RECHERCHE=$ANNEE."".$i;  
			$VALEUR_INTER[$i]=Return_VALEUR_ODTI($DATE_RECHERCHE,$ANNEE,$i,$APPLICATION,$INDICATEUR_REGLE_ID,$mysql_link);
			//$VALEUR_ODTI_ALL[$APPLICATION]=$VALEUR_ODTI_ALL[$APPLICATION]+$VALEUR_INTER[$i];
			$VALEUR_ODTI_ALL['ALL']=$VALEUR_ODTI_ALL['ALL']+$VALEUR_INTER[$i];
			$VALEUR_ALL['ALL']=$VALEUR_ALL['ALL']+$VALEUR_INTER[$i];
			$VALEUR_INTER[$i]=Return_VALEUR_QC9($DATE_RECHERCHE,$ANNEE,$i,$ENVIRONNEMENT,$APPLICATION,$SOGETI,$NATURE,$mysql_link);
			//$VALEUR_QC9_ALL[$APPLICATION]=$VALEUR_QC9_ALL[$APPLICATION]+$VALEUR_INTER[$i];
			$VALEUR_QC9_ALL['ALL']=$VALEUR_QC9_ALL['ALL']+$VALEUR_INTER[$i];
			$VALEUR_ALL['ALL']=$VALEUR_ALL['ALL']+$VALEUR_INTER[$i];
		}
	}
	$j++;
        if ($j%2) { $class = "pair";}else{$class = "impair";} 
	echo '
	<tr align="center" class='.$class.'>
          <td align="center"><b>&nbsp;Total&nbsp;</b></td>
          <td align="center">&nbsp;</td>';
          if($VALEUR_ODTI_ALL['ALL']==0){$NB='';}else{$NB=$VALEUR_ODTI_ALL['ALL'];}
          echo '<td align="center"><b>&nbsp;'.$NB.'&nbsp;</b></td>';
          if($VALEUR_ALL['ALL']==0){
	    $RATIO=0;
	  }else{
	    $RATIO=Round(($NB / $VALEUR_ALL['ALL'])*100,2);
	  }
	  if($RATIO==0){$NB='';}else{$NB=$RATIO.' %';}
          echo '<td align="center"><b>&nbsp;'.$NB.'&nbsp;</b></td>';
          echo '<td align="center"><b>&nbsp;</b></td>';
          if($VALEUR_QC9_ALL['ALL']==0){$NB='';}else{$NB=$VALEUR_QC9_ALL['ALL'];}
          echo '<td align="center"><b>&nbsp;'.$NB.'&nbsp;</b></td>';
          if($VALEUR_ALL['ALL']==0){
	    $RATIO=0;
	  }else{
	    $RATIO=Round(($NB / $VALEUR_ALL['ALL'])*100,2);
	  }
	  if($RATIO==0){$NB='';}else{$NB=$RATIO.' %';}
          echo '<td align="center"><b>&nbsp;'.$NB.'&nbsp;</b></td>';
          echo '<td align="center"><b>&nbsp;</b></td>';
          if($VALEUR_ALL['ALL']==0){$NB='';}else{$NB=$VALEUR_ALL['ALL'];}
          echo '<td align="center"><b>&nbsp;'.$NB.'&nbsp;</b></td>';
          if($VALEUR_ALL['ALL']==0){
	    $RATIO=0;
	  }else{
	    $RATIO=Round(($NB / $VALEUR_ALL['ALL'])*100,2);
	  }
	  if($RATIO==0){$NB='';}else{$NB=$RATIO.' %';}
          echo '<td align="center"><b>&nbsp;'.$NB.'&nbsp;</b></td>';
          echo '<td align="center"><b>&nbsp;</b></td>';
          if($MOIS==''){
          	$NB_FORCET=0;
          	if($DATE_MAX_ODTI!=$DATE_MAX_QC9){
          		$NB_FORCET='';
          	}else{
          		if($MOIS_DATE_MAX_QC9!=0){
          			if($MOIS_DATE_MAX_QC9 < 10){
          				$DIV=substr($DATE_MAX_QC9,5,1);
          			}else{
          				$DIV=substr($DATE_MAX_QC9,4,2);
          			}
          			$NB_FORCET=Round(($VALEUR_ALL['ALL'] * 12) / $DIV,0);
          		}else{
          			$NB_FORCET='';
          		}
          		if($NB_FORCET==0){$NB_FORCET='';}
        	}
          	echo '<td align="center"><b>&nbsp;'.$NB_FORCET.'&nbsp;</b></td>';
          }
          echo '
        </tr>';
        echo '
        <tr align="center" class="titre">
          <td align="left" colspan="'.$colspan.'">&nbsp;</td>
        </tr>';
      do {
          $APPLICATION=$tab_rq_info_app['APPLICATION'];
	  $INDICATEUR_REGLE_ID='';
	  $ENVIRONNEMENT='';
	  $SOGETI='';
	  $NATURE='';
          $VALEUR_QC9_ALL[$APPLICATION]=0;
          $VALEUR_ODTI_ALL[$APPLICATION]=0;
          $VALEUR_ALL[$APPLICATION]=0;
	if($MOIS==''){
	          for($i=1;$i<=12;$i++){
			if($i<10){
				$DATE_RECHERCHE=$ANNEE."0".$i;
				$VALEUR_INTER[$i]=Return_VALEUR_ODTI($DATE_RECHERCHE,$ANNEE,$i,$APPLICATION,$INDICATEUR_REGLE_ID,$mysql_link);
				$VALEUR_ODTI_ALL[$APPLICATION]=$VALEUR_ODTI_ALL[$APPLICATION]+$VALEUR_INTER[$i];
				$VALEUR_ALL[$APPLICATION]=$VALEUR_ALL[$APPLICATION]+$VALEUR_INTER[$i];
				$VALEUR_INTER[$i]=Return_VALEUR_QC9($DATE_RECHERCHE,$ANNEE,$i,$ENVIRONNEMENT,$APPLICATION,$SOGETI,$NATURE,$mysql_link);
				$VALEUR_QC9_ALL[$APPLICATION]=$VALEUR_QC9_ALL[$APPLICATION]+$VALEUR_INTER[$i];
				$VALEUR_ALL[$APPLICATION]=$VALEUR_ALL[$APPLICATION]+$VALEUR_INTER[$i];
			}else{
				$DATE_RECHERCHE=$ANNEE."".$i;  
				$VALEUR_INTER[$i]=Return_VALEUR_ODTI($DATE_RECHERCHE,$ANNEE,$i,$APPLICATION,$INDICATEUR_REGLE_ID,$mysql_link);
				$VALEUR_ODTI_ALL[$APPLICATION]=$VALEUR_ODTI_ALL[$APPLICATION]+$VALEUR_INTER[$i];
				$VALEUR_ALL[$APPLICATION]=$VALEUR_ALL[$APPLICATION]+$VALEUR_INTER[$i];
				$VALEUR_INTER[$i]=Return_VALEUR_QC9($DATE_RECHERCHE,$ANNEE,$i,$ENVIRONNEMENT,$APPLICATION,$SOGETI,$NATURE,$mysql_link);
				$VALEUR_QC9_ALL[$APPLICATION]=$VALEUR_QC9_ALL[$APPLICATION]+$VALEUR_INTER[$i];
				$VALEUR_ALL[$APPLICATION]=$VALEUR_ALL[$APPLICATION]+$VALEUR_INTER[$i];
			}              
		}
	}else{
		$i=$MOIS;
		if($i<10){
			$DATE_RECHERCHE=$ANNEE."0".$i;
			$VALEUR_INTER[$i]=Return_VALEUR_ODTI($DATE_RECHERCHE,$ANNEE,$i,$APPLICATION,$INDICATEUR_REGLE_ID,$mysql_link);
			$VALEUR_ODTI_ALL[$APPLICATION]=$VALEUR_ODTI_ALL[$APPLICATION]+$VALEUR_INTER[$i];
			$VALEUR_ALL[$APPLICATION]=$VALEUR_ALL[$APPLICATION]+$VALEUR_INTER[$i];
			$VALEUR_INTER[$i]=Return_VALEUR_QC9($DATE_RECHERCHE,$ANNEE,$i,$ENVIRONNEMENT,$APPLICATION,$SOGETI,$NATURE,$mysql_link);
			$VALEUR_QC9_ALL[$APPLICATION]=$VALEUR_QC9_ALL[$APPLICATION]+$VALEUR_INTER[$i];
			$VALEUR_ALL[$APPLICATION]=$VALEUR_ALL[$APPLICATION]+$VALEUR_INTER[$i];
		}else{
			$DATE_RECHERCHE=$ANNEE."".$i;  
			$VALEUR_INTER[$i]=Return_VALEUR_ODTI($DATE_RECHERCHE,$ANNEE,$i,$APPLICATION,$INDICATEUR_REGLE_ID,$mysql_link);
			$VALEUR_ODTI_ALL[$APPLICATION]=$VALEUR_ODTI_ALL[$APPLICATION]+$VALEUR_INTER[$i];
			$VALEUR_ALL[$APPLICATION]=$VALEUR_ALL[$APPLICATION]+$VALEUR_INTER[$i];
			$VALEUR_INTER[$i]=Return_VALEUR_QC9($DATE_RECHERCHE,$ANNEE,$i,$ENVIRONNEMENT,$APPLICATION,$SOGETI,$NATURE,$mysql_link);
			$VALEUR_QC9_ALL[$APPLICATION]=$VALEUR_QC9_ALL[$APPLICATION]+$VALEUR_INTER[$i];
			$VALEUR_ALL[$APPLICATION]=$VALEUR_ALL[$APPLICATION]+$VALEUR_INTER[$i];
		} 
	}
	$rq_info_app_autre="
      SELECT `INDICATEUR_APPLICATION_AUTRE`
      FROM `indicateur_application` 
      WHERE `INDICATEUR_APPLICATION_REF`='".$APPLICATION."' AND 
      `INDICATEUR_APPLICATION_AUTRE`!='".$APPLICATION."' AND 
      `ENABLE`=0
      ";
      $res_rq_info_app_autre = mysql_query($rq_info_app_autre, $mysql_link) or die(mysql_error());
      $tab_rq_info_app_autre = mysql_fetch_assoc($res_rq_info_app_autre);
      $total_ligne_rq_info_app_autre=mysql_num_rows($res_rq_info_app_autre); 
      if ($total_ligne_rq_info_app_autre!=0){
      	do{
      	$APPLICATION_AUTRE=$tab_rq_info_app_autre['INDICATEUR_APPLICATION_AUTRE'];
	$INDICATEUR_REGLE_ID='';
	$ENVIRONNEMENT='';
	$SOGETI='';
	$NATURE='';
	if($MOIS==''){
		for($i=1;$i<=12;$i++){
			if($i<10){
				$DATE_RECHERCHE=$ANNEE."0".$i;
				$VALEUR_INTER[$i]=Return_VALEUR_ODTI($DATE_RECHERCHE,$ANNEE,$i,$APPLICATION_AUTRE,$INDICATEUR_REGLE_ID,$mysql_link);
				$VALEUR_ODTI_ALL[$APPLICATION]=$VALEUR_ODTI_ALL[$APPLICATION]+$VALEUR_INTER[$i];
				$VALEUR_ALL[$APPLICATION]=$VALEUR_ALL[$APPLICATION]+$VALEUR_INTER[$i];
				$VALEUR_INTER[$i]=Return_VALEUR_QC9($DATE_RECHERCHE,$ANNEE,$i,$ENVIRONNEMENT,$APPLICATION_AUTRE,$SOGETI,$NATURE,$mysql_link);
				$VALEUR_QC9_ALL[$APPLICATION]=$VALEUR_QC9_ALL[$APPLICATION]+$VALEUR_INTER[$i];
				$VALEUR_ALL[$APPLICATION]=$VALEUR_ALL[$APPLICATION]+$VALEUR_INTER[$i];
			}else{
				$DATE_RECHERCHE=$ANNEE."".$i;  
				$VALEUR_INTER[$i]=Return_VALEUR_ODTI($DATE_RECHERCHE,$ANNEE,$i,$APPLICATION_AUTRE,$INDICATEUR_REGLE_ID,$mysql_link);
				$VALEUR_ODTI_ALL[$APPLICATION]=$VALEUR_ODTI_ALL[$APPLICATION]+$VALEUR_INTER[$i];
				$VALEUR_ALL[$APPLICATION]=$VALEUR_ALL[$APPLICATION]+$VALEUR_INTER[$i];
				$VALEUR_INTER[$i]=Return_VALEUR_QC9($DATE_RECHERCHE,$ANNEE,$i,$ENVIRONNEMENT,$APPLICATION_AUTRE,$SOGETI,$NATURE,$mysql_link);
				$VALEUR_QC9_ALL[$APPLICATION]=$VALEUR_QC9_ALL[$APPLICATION]+$VALEUR_INTER[$i];
				$VALEUR_ALL[$APPLICATION]=$VALEUR_ALL[$APPLICATION]+$VALEUR_INTER[$i];
			}              
		}
	}else{
		$i=$MOIS;
		if($i<10){
			$DATE_RECHERCHE=$ANNEE."0".$i;
			$VALEUR_INTER[$i]=Return_VALEUR_ODTI($DATE_RECHERCHE,$ANNEE,$i,$APPLICATION_AUTRE,$INDICATEUR_REGLE_ID,$mysql_link);
			$VALEUR_ODTI_ALL[$APPLICATION]=$VALEUR_ODTI_ALL[$APPLICATION]+$VALEUR_INTER[$i];
			$VALEUR_ALL[$APPLICATION]=$VALEUR_ALL[$APPLICATION]+$VALEUR_INTER[$i];
			$VALEUR_INTER[$i]=Return_VALEUR_QC9($DATE_RECHERCHE,$ANNEE,$i,$ENVIRONNEMENT,$APPLICATION_AUTRE,$SOGETI,$NATURE,$mysql_link);
			$VALEUR_QC9_ALL[$APPLICATION]=$VALEUR_QC9_ALL[$APPLICATION]+$VALEUR_INTER[$i];
			$VALEUR_ALL[$APPLICATION]=$VALEUR_ALL[$APPLICATION]+$VALEUR_INTER[$i];
		}else{
			$DATE_RECHERCHE=$ANNEE."".$i;  
			$VALEUR_INTER[$i]=Return_VALEUR_ODTI($DATE_RECHERCHE,$ANNEE,$i,$APPLICATION_AUTRE,$INDICATEUR_REGLE_ID,$mysql_link);
			$VALEUR_ODTI_ALL[$APPLICATION]=$VALEUR_ODTI_ALL[$APPLICATION]+$VALEUR_INTER[$i];
			$VALEUR_ALL[$APPLICATION]=$VALEUR_ALL[$APPLICATION]+$VALEUR_INTER[$i];
			$VALEUR_INTER[$i]=Return_VALEUR_QC9($DATE_RECHERCHE,$ANNEE,$i,$ENVIRONNEMENT,$APPLICATION_AUTRE,$SOGETI,$NATURE,$mysql_link);
			$VALEUR_QC9_ALL[$APPLICATION]=$VALEUR_QC9_ALL[$APPLICATION]+$VALEUR_INTER[$i];
			$VALEUR_ALL[$APPLICATION]=$VALEUR_ALL[$APPLICATION]+$VALEUR_INTER[$i];
		} 
	}

      
      } while ($tab_rq_info_app_autre = mysql_fetch_assoc($res_rq_info_app_autre));
      $ligne= mysql_num_rows($res_rq_info_app_autre);
      if($ligne > 0) {
        mysql_data_seek($res_rq_info_app_autre, 0);
        $tab_rq_info_app_autre = mysql_fetch_assoc($res_rq_info_app_autre);
      }
	}
	
	$j++;
        if ($j%2) { $class = "pair";}else{$class = "impair";} 
        $rq_info_app_aramis="
	SELECT `code_appli_aramis` 
	FROM `referentiel_appli` 
	WHERE `id_appli`='".$APPLICATION."' OR `code_appli_long`='".$APPLICATION."'";
	$res_rq_info_app_aramis = mysql_query($rq_info_app_aramis, $mysql_link) or die(mysql_error());
	$tab_rq_info_app_aramis = mysql_fetch_assoc($res_rq_info_app_aramis);
	$total_ligne_rq_info_app_aramis=mysql_num_rows($res_rq_info_app_aramis);
	if($tab_rq_info_app_aramis['code_appli_aramis']==0){
		$APPLICATION_ARAMIS='';
	}else{
		$APPLICATION_ARAMIS=$tab_rq_info_app_aramis['code_appli_aramis'];
	}
	//echo $rq_info_app_aramis.'<BR>';
	mysql_free_result($res_rq_info_app_aramis);

        echo '
        <tr align="center" class='.$class.'>
          <td align="center">&nbsp;'.$APPLICATION.'&nbsp;</td>
          <td align="center">&nbsp;'.$APPLICATION_ARAMIS.'&nbsp;</td>';
          if($VALEUR_ODTI_ALL[$APPLICATION]==0){$NB='';}else{$NB=$VALEUR_ODTI_ALL[$APPLICATION];}
          echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';
          if($VALEUR_ODTI_ALL['ALL']==0){
	    $RATIO=0;
	  }else{
	    $RATIO=Round(($VALEUR_ODTI_ALL[$APPLICATION] / $VALEUR_ODTI_ALL['ALL'])*100,2);
	  }
	  
	  if($RATIO==0){$NB='';}else{$NB=$RATIO.' %';}
          echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';
          $pourcent_ODTI=$RATIO;
	  $pourcent_ODTI_100=100-$pourcent_ODTI;
	  if($pourcent_ODTI>100){$pourcent_ODTI=100;}
          echo '<td>';
          if ($pourcent_ODTI!=0){
          	echo '<img src="./img/FS_vide.png" width="'.$pourcent_ODTI.'" height="15">';
          }
          //if($pourcent_ODTI_100!=100){
          	echo '<img src="./img/FS_OK.png" width="'.$pourcent_ODTI_100.'" height="15">';
          //}
          echo '</td>';
          if($VALEUR_QC9_ALL[$APPLICATION]==0){$NB='';}else{$NB=$VALEUR_QC9_ALL[$APPLICATION];}
          echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';
          if($VALEUR_QC9_ALL['ALL']==0){
	    $RATIO=0;
	  }else{
	    $RATIO=Round(($VALEUR_QC9_ALL[$APPLICATION] / $VALEUR_QC9_ALL['ALL'])*100,2);
	  }
	  if($RATIO==0){$NB='';}else{$NB=$RATIO.' %';}
          echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';
          $pourcent_QC9=$RATIO;
	  $pourcent_QC9_100=100-$pourcent_QC9;
	  if($pourcent_QC9>100){$pourcent_QC9=100;}
          echo '<td>';
          if ($pourcent_QC9!=0){
          	echo '<img src="./img/FS_vide.png" width="'.$pourcent_QC9.'" height="15">';
          }
          //if($pourcent_QC9_100!=100){
          	echo '<img src="./img/FS_OK.png" width="'.$pourcent_QC9_100.'" height="15">';
          //}
          echo '</td>';
          if($VALEUR_ALL[$APPLICATION]==0){$NB='';}else{$NB=$VALEUR_ALL[$APPLICATION];}
          echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';
          if($VALEUR_ALL['ALL']==0){
	    $RATIO=0;
	  }else{
	    $RATIO=Round(($VALEUR_ALL[$APPLICATION] / $VALEUR_ALL['ALL'])*100,2);
	  }
	  if($RATIO==0){$NB='';}else{$NB=$RATIO.' %';}
          echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';
          $pourcent_ALL=$RATIO;
	  $pourcent_ALL_100=100-$pourcent_ALL;
	  if($pourcent_ALL>100){$pourcent_ALL=100;}
          echo '<td>';
          if ($pourcent_ALL!=0){
          	echo '<img src="./img/FS_vide.png" width="'.$pourcent_ALL.'" height="15">';
          }
          //if($pourcent_ALL_100!=100){
          	echo '<img src="./img/FS_OK.png" width="'.$pourcent_ALL_100.'" height="15">';
          //}
          echo '</td>';
          if($MOIS==''){
          	$NB_FORCET=0;
          	if($DATE_MAX_ODTI!=$DATE_MAX_QC9){
          		$NB_FORCET='';
          	}else{
          		if($MOIS_DATE_MAX_QC9!=0){
          			if($MOIS_DATE_MAX_QC9 < 10){
          				$DIV=substr($DATE_MAX_QC9,5,1);
          			}else{
          				$DIV=substr($DATE_MAX_QC9,4,2);
          			}
          			$NB_FORCET=Round(($VALEUR_ALL[$APPLICATION] * 12) / $DIV,0);
          		}else{
          			$NB_FORCET='';
          		}
          		if($NB_FORCET==0){$NB_FORCET='';}
        	}
          	echo '<td align="center">&nbsp;'.$NB_FORCET.'&nbsp;</td>';
          }
          echo '
        </tr>';
      } while ($tab_rq_info_app = mysql_fetch_assoc($res_rq_info_app));
      $ligne= mysql_num_rows($res_rq_info_app);
      if($ligne > 0) {
        mysql_data_seek($res_rq_info_app, 0);
        $tab_rq_info_app = mysql_fetch_assoc($res_rq_info_app);
      }

 
    }
    mysql_free_result($res_rq_info_app);
    echo '
      <tr align="center" class="titre">
        <td align="center" colspan="'.$colspan.'">&nbsp;</td>
      </tr>';
      $j++;
        if ($j%2) { $class = "pair";}else{$class = "impair";} 
	echo '
	<tr align="center" class='.$class.'>
          <td align="center"><b>&nbsp;Total&nbsp;</b></td>
          <td align="center">&nbsp;</td>';
          if($VALEUR_ODTI_ALL['ALL']==0){$NB='';}else{$NB=$VALEUR_ODTI_ALL['ALL'];}
          echo '<td align="center"><b>&nbsp;'.$NB.'&nbsp;</b></td>';
          if($VALEUR_ALL['ALL']==0){
	    $RATIO=0;
	  }else{
	    $RATIO=Round(($NB / $VALEUR_ALL['ALL'])*100,2);
	  }
	  if($RATIO==0){$NB='';}else{$NB=$RATIO.' %';}
          echo '<td align="center"><b>&nbsp;'.$NB.'&nbsp;</b></td>';
          echo '<td align="center"><b>&nbsp;</b></td>';
          if($VALEUR_QC9_ALL['ALL']==0){$NB='';}else{$NB=$VALEUR_QC9_ALL['ALL'];}
          echo '<td align="center"><b>&nbsp;'.$NB.'&nbsp;</b></td>';
          if($VALEUR_ALL['ALL']==0){
	    $RATIO=0;
	  }else{
	    $RATIO=Round(($NB / $VALEUR_ALL['ALL'])*100,2);
	  }
	  if($RATIO==0){$NB='';}else{$NB=$RATIO.' %';}
          echo '<td align="center"><b>&nbsp;'.$NB.'&nbsp;</b></td>';
          echo '<td align="center"><b>&nbsp;</b></td>';
          if($VALEUR_ALL['ALL']==0){$NB='';}else{$NB=$VALEUR_ALL['ALL'];}
          echo '<td align="center"><b>&nbsp;'.$NB.'&nbsp;</b></td>';
          if($VALEUR_ALL['ALL']==0){
	    $RATIO=0;
	  }else{
	    $RATIO=Round(($NB / $VALEUR_ALL['ALL'])*100,2);
	  }
	  if($RATIO==0){$NB='';}else{$NB=$RATIO.' %';}
          echo '<td align="center"><b>&nbsp;'.$NB.'&nbsp;</b></td>';
          echo '<td align="center"><b>&nbsp;</b></td>';
          if($MOIS==''){
          	$NB_FORCET=0;
          	if($DATE_MAX_ODTI!=$DATE_MAX_QC9){
          		$NB_FORCET='';
          	}else{
          		if($MOIS_DATE_MAX_QC9!=0){
          			if($MOIS_DATE_MAX_QC9 < 10){
          				$DIV=substr($DATE_MAX_QC9,5,1);
          			}else{
          				$DIV=substr($DATE_MAX_QC9,4,2);
          			}
          			$NB_FORCET=Round(($VALEUR_ALL['ALL'] * 12) / $DIV,0);
          		}else{
          			$NB_FORCET='';
          		}
          		if($NB_FORCET==0){$NB_FORCET='';}
        	}
          	echo '<td align="center"><b>&nbsp;'.$NB_FORCET.'&nbsp;</b></td>';
          }
          echo '
        </tr>
      <tr align="center" class="titre">
        <td align="center" colspan="'.$colspan.'">&nbsp;</td>
      </tr>
      <tr align="center" class="titre">
        <td align="center" colspan="'.$colspan.'">&nbsp;[&nbsp;';
        if($MOIS==''){
        	echo '<a href="./index.php?ITEM=indicateur_menu">Retour</a>';
        }else{
        	echo '<a href="./index.php?ITEM=indicateur_rapport_pondere&ANNEE='.$ANNEE.'">Retour</a>';
	}
        echo '&nbsp;]</td>
      </tr>
      <tr align="center" class="titre">
        <td align="center" colspan="'.$colspan.'">&nbsp;</td>
      </tr>
    </table>
  </div>
  ';
mysql_close($mysql_link);
?>