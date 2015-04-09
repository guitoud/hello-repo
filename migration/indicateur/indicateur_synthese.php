<?PHP
//redirection si acces dirrect
if(substr_count($_SERVER['PHP_SELF'], 'index.php')==0){
  header("Location: ../"); 
  exit(); 
}
/*******************************************************************
   Interface Gestion des indicateurs
   Version 1.0.0   
  01/02/2010 - VGU - Creation fichier
**************************************************Guibert Vincent***
## la partie par mois est en cours de dev
*******************************************************************/

require("./cf/conf_outil_icdc.php"); 
require_once("./cf/fonctions.php");
require_once("./cf/autre_fonctions.php");
// iniialisation des variables
if(isset($_GET['action'])){
	$action=$_GET['action'];
}else{
	$action='PROD';
}
if($action=='PROD'){
	$actionENV='E';	
}
if($action=='VA'){
	$actionENV='V';	
}
if(isset($_GET['ANNEE'])){
	$ANNEE=$_GET['ANNEE'];
}else{
	$ANNEE=date("Y");
}
if(empty($_GET['MOIS'])){
	$MOIS='';
	$info_mois=0;
	$colspan=14;
	$nb_colonne=12;
}else{
	$MOIS=$_GET['MOIS'];
	$info_mois=1;
	if($MOIS<10){
		$DATE_RECHERCHE=$ANNEE."0".$MOIS;
	}else{
		$DATE_RECHERCHE=$ANNEE."".$MOIS;
	}
	# Liste les semaines du mois
	$rq_info_semaine="
	SELECT DISTINCT(`DATE_SEMAINE`)
	FROM `indicateur_calcul` 
	WHERE `DATE_INDICATEUR` = '".$DATE_RECHERCHE."'
	AND `DATE_MOIS` ='".$MOIS."'
	AND `DATE_ANNEE` ='".$ANNEE."'
	ORDER BY `DATE_SEMAINE`"; 
	$res_rq_info_semaine = mysql_query($rq_info_semaine, $mysql_link) or die(mysql_error());
	$tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine);
	$total_ligne_rq_info_semaine=mysql_num_rows($res_rq_info_semaine);
	$colspan=1+$total_ligne_rq_info_semaine+1;
	$nb_colonne=$total_ligne_rq_info_semaine;
}
$j=0;
$ID='';
for($i=1;$i<=13;$i++){
	$NB_ALL_ALL[$i]['ALL']=0;
	$NB_ALL_ALL[$i]['AP']=0;
	$NB_ALL_ALL[$i]['LEV']=0;
	$NB_ALL_ALL[$i]['LHVP']=0;
	$NB_ALL_ALL[$i]['LHVJ']=0;
	$NB_ALL_ALL[$i]['LHVA']=0;
}


function Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link)
{
	if($ENV==''){$ENV_SQL='';}else{$ENV_SQL="`ENV`='".$ENV."' AND";}
	if($STATUS==''){$STATUS_SQL='';}else{$STATUS_SQL="`STATUS`='".$STATUS."' AND";}
	if($ACTION==''){$ACTION_SQL='';}else{$ACTION_SQL="`ACTION`='".$ACTION."' AND";}
	if($NATURE==''){$NATURE_SQL='';}else{$NATURE_SQL="`NATURE`='".$NATURE."' AND";}
	if($EN_VERSION==''){$EN_VERSION_SQL='';}else{$EN_VERSION_SQL="`EN_VERSION`='".$EN_VERSION."' AND";}
	if($EN_HVP==''){$EN_HVP_SQL='';}else{$EN_HVP_SQL="`EN_HVP`='".$EN_HVP."' AND";}
	if($EN_HVH==''){$EN_HVH_SQL='';}else{$EN_HVH_SQL="`EN_HVH`='".$EN_HVH."' AND";}
	if($APP==''){$APP_SQL='';}else{$APP_SQL="`APPLI`='".$APP."' AND";}
	if($SEMAINE==''){$SEMAINE_SQL='';}else{$SEMAINE_SQL="`DATE_SEMAINE`='".$SEMAINE."' AND";}
## Calcul de nombre de ref odti en fonction des crit&egrave;res ci-dessus.
	$rq_info="
	SELECT COUNT(`REF`) AS `NB` 
	FROM `indicateur_calcul` 
	WHERE
	".$STATUS_SQL."
	`DATE_ANNEE`='".$ANNEE."' AND
	`DATE_MOIS`=".$MOIS." AND
	".$SEMAINE_SQL." 
	".$ENV_SQL."
	".$ACTION_SQL." 
	".$NATURE_SQL." 
	".$EN_VERSION_SQL." 
	".$EN_HVP_SQL." 
	".$EN_HVH_SQL." 
	".$APP_SQL." 
	`DATE_INDICATEUR`='".$DATE_RECHERCHE."'";
	$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
	$tab_rq_info = mysql_fetch_assoc($res_rq_info);
	$total_ligne_rq_info=mysql_num_rows($res_rq_info);
	$NB=$tab_rq_info['NB'];
	mysql_free_result($res_rq_info);
	return $NB;
}

  echo '
   <div align="center">
    <br/>
    <table class="table_inc" cellspacing="1" cellpading="0">';
    
    if($info_mois==0){
    	echo '
      <tr align="center" class="titre">
        <td align="center" colspan="'.$colspan.'">&nbsp;[&nbsp;<a href="./index.php?ITEM=indicateur_menu">Retour</a>&nbsp;]&nbsp;-&nbsp;<a href="./indicateur/indicateur_synthese_csv.php?ANNEE='.$ANNEE.'&action='.$action.'"><img src="./img/logo_excel.png" border="0"/></a>&nbsp;</td>
      </tr>
      <tr align="center" class="titre">
        <td align="center" colspan="'.$colspan.'"><b>&nbsp;Synth&egrave;se ODTI de '.$action.' pour '.$ANNEE.'&nbsp;</b></td>
      </tr>
      <tr align="center" class="titre">
        <td align="center"><b>&nbsp;</b></td>
        <td align="center"><b>&nbsp;Janvier&nbsp;</b></td>
        <td align="center"><b>&nbsp;F&eacute;vrier&nbsp;</b></td>
        <td align="center"><b>&nbsp;Mars&nbsp;</b></td>
        <td align="center"><b>&nbsp;Avril&nbsp;</b></td>
        <td align="center"><b>&nbsp;Mai&nbsp;</b></td>
        <td align="center"><b>&nbsp;Juin&nbsp;</b></td>
        <td align="center"><b>&nbsp;Juillet&nbsp;</b></td>
        <td align="center"><b>&nbsp;Aout&nbsp;</b></td>
        <td align="center"><b>&nbsp;Septembre&nbsp;</b></td>
        <td align="center"><b>&nbsp;Octobre&nbsp;</b></td>
        <td align="center"><b>&nbsp;Novembre&nbsp;</b></td>
        <td align="center"><b>&nbsp;D&eacute;cembre&nbsp;</b></td>
        <td align="center"><b>&nbsp;'.$ANNEE.'&nbsp;</b></td>
      </tr>';
	}else{
	echo '
      <tr align="center" class="titre">
        <td align="center" colspan="'.$colspan.'"><b>&nbsp;Synth&egrave;se ODTI de '.$action.' pour '.$Tab_des_Mois[$MOIS-1].' '.$ANNEE.'&nbsp;</b></td>
      </tr>';
      echo '
	<tr align="center" class="titre">
	  <td align="center">&nbsp;</td>';
        do {
        	echo '<td align="center">&nbsp;'.$tab_rq_info_semaine['DATE_SEMAINE'].'&nbsp;</td>';

        } while ($tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine));
        $ligne= mysql_num_rows($res_rq_info_semaine);
        if($ligne > 0) {
          mysql_data_seek($res_rq_info_semaine, 0);
          $tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine);
        }
        echo '
        <td align="center"><b>&nbsp;'.$Tab_des_Mois[$MOIS-1].'&nbsp;</b></td>
      </tr>';
	}
//Autres (ODTI non encore valid&eacute;es)
      echo '
      <tr align="center" class="titre">
        <td align="left" colspan="'.$colspan.'">&nbsp;Autres (ODTI non encore valid&eacute;es)&nbsp;</td>
      </tr>';
      for($i=1;$i<=13;$i++){
      	$NB_ALL[$i]=0;
      }
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Actions Ponctuelles&nbsp</b></td>';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV=$actionENV;
	$STATUS="V";
	$ACTION="E";
	$NATURE="P";
	$EN_VERSION="";
	$EN_HVP="";
	$EN_HVH="";
        if($info_mois==0){
	        for($i=1;$i<=12;$i++){
	        	if($i<10){
				$DATE_RECHERCHE=$ANNEE."0".$i;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$i;
			}        	
			$MOIS=$i;
			$SEMAINE="";
			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL_ALL[$i]['AP']=$NB_ALL_ALL[$i]['AP']+$NB;
			echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
			
	        }
	        echo '<td align="center">&nbsp;'.$NB_ALL['ALL'].'&nbsp;</td>';
	}else{
		do {
	        	if($MOIS<10){
				$DATE_RECHERCHE=$ANNEE."0".$MOIS;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$MOIS;
			}        	
			$SEMAINE=$tab_rq_info_semaine['DATE_SEMAINE'];
			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL_ALL[$SEMAINE]['AP']=$NB_ALL_ALL[$SEMAINE]['AP']+$NB;
			echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
	
	        } while ($tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine));
	        $ligne= mysql_num_rows($res_rq_info_semaine);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_semaine, 0);
	          $tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine);
	        }
	        $SEMAINE='';
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
	        echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';
	}
	
        echo ' 
      </tr>';
// Livraison En Version
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Livraison En Version&nbsp</b></td>';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV=$actionENV;
	$STATUS="V";
	$ACTION="I";
	$NATURE="V";
	$EN_VERSION="V";
	$EN_HVP="F";
	$EN_HVH="F";
	if($info_mois==0){
	        for($i=1;$i<=12;$i++){
	        	if($i<10){
				$DATE_RECHERCHE=$ANNEE."0".$i;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$i;
			}
			$MOIS=$i;
			$SEMAINE="";
	
			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL[$i]=$NB_ALL[$i]+$NB;
			$NB_ALL_ALL[$i]['LEV']=$NB_ALL_ALL[$i]['LEV']+$NB;
			echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
			
	        }
	        echo '<td align="center">&nbsp;'.$NB_ALL['ALL'].'&nbsp;</td>';
	}else{
		do {
	        	if($MOIS<10){
				$DATE_RECHERCHE=$ANNEE."0".$MOIS;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$MOIS;
			}        	
			$SEMAINE=$tab_rq_info_semaine['DATE_SEMAINE'];
			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL_ALL[$SEMAINE]['LEV']=$NB_ALL_ALL[$SEMAINE]['LEV']+$NB;
			echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
	
	        } while ($tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine));
	        $ligne= mysql_num_rows($res_rq_info_semaine);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_semaine, 0);
	          $tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine);
	        }
	        $SEMAINE='';
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
	        echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';
	}
        echo ' 
      </tr>';
// Livraison Hors Version Planifi&eacute;
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Livraison Hors Version Planifi&eacute;&nbsp</b></td>';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV=$actionENV;
	$STATUS="V";
	$ACTION="I";
	$NATURE="H";
	$EN_VERSION="F";
	$EN_HVP="V";
	$EN_HVH="F";
        if($info_mois==0){
	        for($i=1;$i<=12;$i++){
	        	if($i<10){
				$DATE_RECHERCHE=$ANNEE."0".$i;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$i;
			}
			$MOIS=$i;
			$SEMAINE="";

			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL[$i]=$NB_ALL[$i]+$NB;
			$NB_ALL_ALL[$i]['LHVP']=$NB_ALL_ALL[$i]['LHVP']+$NB;
			echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
			
	        }
	        echo '<td align="center">&nbsp;'.$NB_ALL['ALL'].'&nbsp;</td>';
	}else{
		do {
	        	if($MOIS<10){
				$DATE_RECHERCHE=$ANNEE."0".$MOIS;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$MOIS;
			}        	
			$SEMAINE=$tab_rq_info_semaine['DATE_SEMAINE'];
			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL_ALL[$SEMAINE]['LHVP']=$NB_ALL_ALL[$SEMAINE]['LHVP']+$NB;
			echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
	
	        } while ($tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine));
	        $ligne= mysql_num_rows($res_rq_info_semaine);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_semaine, 0);
	          $tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine);
	        }
	        $SEMAINE='';
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
	        echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';
	}
        echo ' 
      </tr>';
// Livraison Hors Version Jeudi
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Livraison Hors Version Jeudi&nbsp</b></td>';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV=$actionENV;
	$STATUS="V";
	$ACTION="I";
	$NATURE="H";
	$EN_VERSION="F";
	$EN_HVP="F";
	$EN_HVH="V";
        if($info_mois==0){
	        for($i=1;$i<=12;$i++){
	        	if($i<10){
				$DATE_RECHERCHE=$ANNEE."0".$i;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$i;
			}
			$MOIS=$i;
			$SEMAINE="";
	
			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL[$i]=$NB_ALL[$i]+$NB;
			$NB_ALL_ALL[$i]['LHVJ']=$NB_ALL_ALL[$i]['LHVJ']+$NB;
			echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
			
	        }
	        echo '<td align="center">&nbsp;'.$NB_ALL['ALL'].'&nbsp;</td>';
	}else{
		do {
	        	if($MOIS<10){
				$DATE_RECHERCHE=$ANNEE."0".$MOIS;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$MOIS;
			}        	
			$SEMAINE=$tab_rq_info_semaine['DATE_SEMAINE'];
			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL_ALL[$SEMAINE]['LHVJ']=$NB_ALL_ALL[$SEMAINE]['LHVJ']+$NB;
			echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
	
	        } while ($tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine));
	        $ligne= mysql_num_rows($res_rq_info_semaine);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_semaine, 0);
	          $tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine);
	        }
	        $SEMAINE='';
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
	        echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';
	}
        echo ' 
      </tr>';
// Livraison Hors Version autres jours
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Livraison Hors Version autres jours&nbsp</b></td>';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV=$actionENV;
	$STATUS="V";
	$ACTION="I";
	$NATURE="H";
	$EN_VERSION="F";
	$EN_HVP="F";
	$EN_HVH="F";
        if($info_mois==0){
	        for($i=1;$i<=12;$i++){
	        	if($i<10){
				$DATE_RECHERCHE=$ANNEE."0".$i;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$i;
			}
			$MOIS=$i;
			$SEMAINE="";

			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL[$i]=$NB_ALL[$i]+$NB;
			$NB_ALL_ALL[$i]['LHVA']=$NB_ALL_ALL[$i]['LHVA']+$NB;
			echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
			
	        }
	        echo '<td align="center">&nbsp;'.$NB_ALL['ALL'].'&nbsp;</td>';
	}else{
		do {
	        	if($MOIS<10){
				$DATE_RECHERCHE=$ANNEE."0".$MOIS;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$MOIS;
			}        	
			$SEMAINE=$tab_rq_info_semaine['DATE_SEMAINE'];
			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL_ALL[$SEMAINE]['LHVA']=$NB_ALL_ALL[$SEMAINE]['LHVA']+$NB;
			echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
	
	        } while ($tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine));
	        $ligne= mysql_num_rows($res_rq_info_semaine);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_semaine, 0);
	          $tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine);
	        }
	        $SEMAINE='';
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
	        echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';
	}
        echo ' 
      </tr>';
// Autres (ODTI non encore valid&eacute;es) - Total
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Autres (ODTI non encore valid&eacute;es) - Total&nbsp</b></td>';
        
        for($i=1;$i<=$nb_colonne;$i++){
        	$NB_ALL[13]=$NB_ALL[13]+$NB_ALL[$i];
        	$NB_ALL_ALL[$i]['ALL']=$NB_ALL_ALL[$i]['ALL']+$NB_ALL[$i];
        	$NB_ALL_ALL[13]['ALL']=$NB_ALL_ALL[13]['ALL']+$NB_ALL[$i];
        	echo '<td align="center"><b>&nbsp;'.$NB_ALL[$i].'&nbsp;</b></td>';	
        }
        echo '<td align="center"><b>&nbsp;'.$NB_ALL[13].'&nbsp;</b></td>';
        echo ' 
      </tr>';
//Livraisons de composants
      echo '
      <tr align="center" class="titre">
        <td align="left" colspan="'.$colspan.'">&nbsp;Livraisons de composants&nbsp;</td>
      </tr>';
      for($i=1;$i<=13;$i++){
      	$NB_ALL[$i]=0;
      }
// Actions Ponctuelles
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Actions Ponctuelles&nbsp</b></td>';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV=$actionENV;
	$STATUS="V";
	$ACTION="L";
	$NATURE="P";
	$EN_VERSION="";
	$EN_HVP="";
	$EN_HVH="";
        if($info_mois==0){
	        for($i=1;$i<=12;$i++){
	        	if($i<10){
				$DATE_RECHERCHE=$ANNEE."0".$i;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$i;
			}
			$MOIS=$i;
			$SEMAINE="";

			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL[$i]=$NB_ALL[$i]+$NB;
			$NB_ALL_ALL[$i]['AP']=$NB_ALL_ALL[$i]['AP']+$NB;
			echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
	        }
	        echo '<td align="center">&nbsp;'.$NB_ALL['ALL'].'&nbsp;</td>';
	}else{
		do {
	        	if($MOIS<10){
				$DATE_RECHERCHE=$ANNEE."0".$MOIS;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$MOIS;
			}        	
			$SEMAINE=$tab_rq_info_semaine['DATE_SEMAINE'];
			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL_ALL[$SEMAINE]['AP']=$NB_ALL_ALL[$SEMAINE]['AP']+$NB;
			echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
	
	        } while ($tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine));
	        $ligne= mysql_num_rows($res_rq_info_semaine);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_semaine, 0);
	          $tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine);
	        }
	        $SEMAINE='';
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
	        echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';
	}
        echo ' 
      </tr>';
// Livraison En Version (date = V)
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Livraison En Version (date = V)&nbsp</b></td>';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV=$actionENV;
	$STATUS="V";
	$ACTION="L";
	$NATURE="V";
	$EN_VERSION="V";
	$EN_HVP="F";
	$EN_HVH="F";
        if($info_mois==0){
	        for($i=1;$i<=12;$i++){
	        	if($i<10){
				$DATE_RECHERCHE=$ANNEE."0".$i;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$i;
			}        	
			$MOIS=$i;
			$SEMAINE="";

			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL[$i]=$NB_ALL[$i]+$NB;
			$NB_ALL_ALL[$i]['LEV']=$NB_ALL_ALL[$i]['LEV']+$NB;
			echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
			
	        }
	        echo '<td align="center">&nbsp;'.$NB_ALL['ALL'].'&nbsp;</td>';
	}else{
		do {
	        	if($MOIS<10){
				$DATE_RECHERCHE=$ANNEE."0".$MOIS;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$MOIS;
			}        	
			$SEMAINE=$tab_rq_info_semaine['DATE_SEMAINE'];
			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL_ALL[$SEMAINE]['LEV']=$NB_ALL_ALL[$SEMAINE]['LEV']+$NB;
			echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
	
	        } while ($tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine));
	        $ligne= mysql_num_rows($res_rq_info_semaine);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_semaine, 0);
	          $tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine);
	        }
	        $SEMAINE='';
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
	        echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';
	}
        echo ' 
      </tr>';
// Livraison En Version (hors V, hors Jeudi)
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Livraison En Version (hors V, hors Jeudi)&nbsp</b></td>';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV=$actionENV;
	$STATUS="V";
	$ACTION="L";
	$NATURE="V";
	$EN_VERSION="F";
	$EN_HVP="F";
	$EN_HVH="F";
        if($info_mois==0){
	        for($i=1;$i<=12;$i++){
	        	if($i<10){
				$DATE_RECHERCHE=$ANNEE."0".$i;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$i;
			}        	
			$MOIS=$i;
			$SEMAINE="";

			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL[$i]=$NB_ALL[$i]+$NB;
			$NB_ALL_ALL[$i]['LEV']=$NB_ALL_ALL[$i]['LEV']+$NB;
			echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
			
	        }
	        echo '<td align="center">&nbsp;'.$NB_ALL['ALL'].'&nbsp;</td>';
        }else{
		do {
	        	if($MOIS<10){
				$DATE_RECHERCHE=$ANNEE."0".$MOIS;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$MOIS;
			}        	
			$SEMAINE=$tab_rq_info_semaine['DATE_SEMAINE'];
			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL_ALL[$SEMAINE]['LEV']=$NB_ALL_ALL[$SEMAINE]['LEV']+$NB;
			echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
	
	        } while ($tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine));
	        $ligne= mysql_num_rows($res_rq_info_semaine);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_semaine, 0);
	          $tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine);
	        }
	        $SEMAINE='';
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
	        echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';
	}
        echo ' 
      </tr>';
// Livraison En Version (hors V, Jeudi)
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Livraison En Version (hors V, Jeudi)&nbsp</b></td>';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV=$actionENV;
	$STATUS="V";
	$ACTION="L";
	$NATURE="V";
	$EN_VERSION="F";
	$EN_HVP="F";
	$EN_HVH="V";
        if($info_mois==0){
	        for($i=1;$i<=12;$i++){
	        	if($i<10){
				$DATE_RECHERCHE=$ANNEE."0".$i;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$i;
			}        	
			$MOIS=$i;
			$SEMAINE="";

			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL[$i]=$NB_ALL[$i]+$NB;
			$NB_ALL_ALL[$i]['LEV']=$NB_ALL_ALL[$i]['LEV']+$NB;
			echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
			
	        }
	        echo '<td align="center">&nbsp;'.$NB_ALL['ALL'].'&nbsp;</td>';
        }else{
		do {
	        	if($MOIS<10){
				$DATE_RECHERCHE=$ANNEE."0".$MOIS;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$MOIS;
			}        	
			$SEMAINE=$tab_rq_info_semaine['DATE_SEMAINE'];
			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL_ALL[$SEMAINE]['LEV']=$NB_ALL_ALL[$SEMAINE]['LEV']+$NB;
			echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
	
	        } while ($tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine));
	        $ligne= mysql_num_rows($res_rq_info_semaine);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_semaine, 0);
	          $tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine);
	        }
	        $SEMAINE='';
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
	        echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';
	}
        echo ' 
      </tr>';
// Livraison Hors Version Planifi&eacute;
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Livraison Hors Version Planifi&eacute;&nbsp</b></td>';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV=$actionENV;
	$STATUS="V";
	$ACTION="L";
	$NATURE="H";
	$EN_VERSION="F";
	$EN_HVP="V";
	$EN_HVH="F";
        if($info_mois==0){
	        for($i=1;$i<=12;$i++){
	        	if($i<10){
				$DATE_RECHERCHE=$ANNEE."0".$i;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$i;
			}        	
			$MOIS=$i;
			$SEMAINE="";

			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL[$i]=$NB_ALL[$i]+$NB;
			echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
			
	        }
	        echo '<td align="center">&nbsp;'.$NB_ALL['ALL'].'&nbsp;</td>';
        }else{
		do {
	        	if($MOIS<10){
				$DATE_RECHERCHE=$ANNEE."0".$MOIS;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$MOIS;
			}        	
			$SEMAINE=$tab_rq_info_semaine['DATE_SEMAINE'];
			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			//$NB_ALL_ALL[$SEMAINE]['AP']=$NB_ALL_ALL[$SEMAINE]['AP']+$NB;
			$NB_ALL[$SEMAINE]=$NB_ALL[$SEMAINE]+$NB;
			echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
	
	        } while ($tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine));
	        $ligne= mysql_num_rows($res_rq_info_semaine);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_semaine, 0);
	          $tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine);
	        }
	        $SEMAINE='';
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
	        echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';
	}
        echo ' 
      </tr>';
// Livraison Hors Version Jeudi
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Livraison Hors Version Jeudi&nbsp</b></td>';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV=$actionENV;
	$STATUS="V";
	$ACTION="L";
	$NATURE="H";
	$EN_VERSION="F";
	$EN_HVP="F";
	$EN_HVH="V";
        if($info_mois==0){
	        for($i=1;$i<=12;$i++){
	        	if($i<10){
				$DATE_RECHERCHE=$ANNEE."0".$i;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$i;
			}        	
			$MOIS=$i;
			$SEMAINE="";

			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL[$i]=$NB_ALL[$i]+$NB;
			$NB_ALL_ALL[$i]['LHVJ']=$NB_ALL_ALL[$i]['LHVJ']+$NB;
			echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
			
	        }
	        echo '<td align="center">&nbsp;'.$NB_ALL['ALL'].'&nbsp;</td>';
        }else{
		do {
	        	if($MOIS<10){
				$DATE_RECHERCHE=$ANNEE."0".$MOIS;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$MOIS;
			}        	
			$SEMAINE=$tab_rq_info_semaine['DATE_SEMAINE'];
			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL_ALL[$SEMAINE]['LHVJ']=$NB_ALL_ALL[$SEMAINE]['LHVJ']+$NB;
			echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
	
	        } while ($tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine));
	        $ligne= mysql_num_rows($res_rq_info_semaine);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_semaine, 0);
	          $tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine);
	        }
	        $SEMAINE='';
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
	        echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';
	}
        echo ' 
      </tr>';
// Livraison Hors Version autres jours
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Livraison Hors Version autres jours&nbsp</b></td>';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV=$actionENV;
	$STATUS="V";
	$ACTION="L";
	$NATURE="H";
	$EN_VERSION="F";
	$EN_HVP="F";
	$EN_HVH="F";
        if($info_mois==0){
	        for($i=1;$i<=12;$i++){
	        	if($i<10){
				$DATE_RECHERCHE=$ANNEE."0".$i;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$i;
			}        	
			$MOIS=$i;
			$SEMAINE="";

			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL[$i]=$NB_ALL[$i]+$NB;
			$NB_ALL_ALL[$i]['LHVA']=$NB_ALL_ALL[$i]['LHVA']+$NB;
			echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
			
	        }
	        echo '<td align="center">&nbsp;'.$NB_ALL['ALL'].'&nbsp;</td>';
        }else{
		do {
	        	if($MOIS<10){
				$DATE_RECHERCHE=$ANNEE."0".$MOIS;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$MOIS;
			}        	
			$SEMAINE=$tab_rq_info_semaine['DATE_SEMAINE'];
			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL_ALL[$SEMAINE]['LHVA']=$NB_ALL_ALL[$SEMAINE]['LHVA']+$NB;
			echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
	
	        } while ($tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine));
	        $ligne= mysql_num_rows($res_rq_info_semaine);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_semaine, 0);
	          $tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine);
	        }
	        $SEMAINE='';
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
	        echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';
	}
        echo ' 
      </tr>';
// Livraisons de composants - Total
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Livraisons de composants - Total&nbsp</b></td>';
        for($i=1;$i<=$nb_colonne;$i++){
        	$NB_ALL[13]=$NB_ALL[13]+$NB_ALL[$i];
        	$NB_ALL_ALL[$i]['ALL']=$NB_ALL_ALL[$i]['ALL']+$NB_ALL[$i];
        	$NB_ALL_ALL[13]['ALL']=$NB_ALL_ALL[13]['ALL']+$NB_ALL[$i];
        	echo '<td align="center"><b>&nbsp;'.$NB_ALL[$i].'&nbsp;</b></td>';	
        }
        echo '<td align="center"><b>&nbsp;'.$NB_ALL[13].'&nbsp;</b></td>';	
        echo ' 
      </tr>';
//traitement exceptionnels
      echo '
      <tr align="center" class="titre">
        <td align="left" colspan="'.$colspan.'">&nbsp;traitements exceptionnels&nbsp;</td>
      </tr>';
      for($i=1;$i<=13;$i++){
      	$NB_ALL[$i]=0;
      }
// Actions Ponctuelles
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Actions Ponctuelles&nbsp</b></td>';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV=$actionENV;
	$STATUS="V";
	$ACTION="T";
	$NATURE="P";
	$EN_VERSION="";
	$EN_HVP="";
	$EN_HVH="";
        if($info_mois==0){
	        for($i=1;$i<=12;$i++){
	        	if($i<10){
				$DATE_RECHERCHE=$ANNEE."0".$i;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$i;
			}        	
			$MOIS=$i;
			$SEMAINE="";

			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL[$i]=$NB_ALL[$i]+$NB;
			$NB_ALL_ALL[$i]['AP']=$NB_ALL_ALL[$i]['AP']+$NB;
			echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
			
	        }
	        echo '<td align="center">&nbsp;'.$NB_ALL['ALL'].'&nbsp;</td>';
        }else{
		do {
	        	if($MOIS<10){
				$DATE_RECHERCHE=$ANNEE."0".$MOIS;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$MOIS;
			}        	
			$SEMAINE=$tab_rq_info_semaine['DATE_SEMAINE'];
			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL_ALL[$SEMAINE]['AP']=$NB_ALL_ALL[$SEMAINE]['AP']+$NB;
			echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
	
	        } while ($tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine));
	        $ligne= mysql_num_rows($res_rq_info_semaine);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_semaine, 0);
	          $tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine);
	        }
	        $SEMAINE='';
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
	        echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';
	}
        echo ' 
      </tr>';
// Livraison En Version
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Livraison En Version&nbsp</b></td>';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV=$actionENV;
	$STATUS="V";
	$ACTION="T";
	$NATURE="V";
	$EN_VERSION="V";
	$EN_HVP="F";
	$EN_HVH="F";
        if($info_mois==0){
	        for($i=1;$i<=12;$i++){
	        	if($i<10){
				$DATE_RECHERCHE=$ANNEE."0".$i;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$i;
			}        	
			$MOIS=$i;
			$SEMAINE="";

			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL[$i]=$NB_ALL[$i]+$NB;
			$NB_ALL_ALL[$i]['LEV']=$NB_ALL_ALL[$i]['LEV']+$NB;
			echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
			
	        }
	        echo '<td align="center">&nbsp;'.$NB_ALL['ALL'].'&nbsp;</td>';
        }else{
		do {
	        	if($MOIS<10){
				$DATE_RECHERCHE=$ANNEE."0".$MOIS;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$MOIS;
			}        	
			$SEMAINE=$tab_rq_info_semaine['DATE_SEMAINE'];
			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL_ALL[$SEMAINE]['LEV']=$NB_ALL_ALL[$SEMAINE]['LEV']+$NB;
			echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
	
	        } while ($tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine));
	        $ligne= mysql_num_rows($res_rq_info_semaine);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_semaine, 0);
	          $tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine);
	        }
	        $SEMAINE='';
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
	        echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';
	}
        echo ' 
      </tr>';
// Livraison Hors Version Planifi&eacute;
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Livraison Hors Version Planifi&eacute;&nbsp</b></td>';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV=$actionENV;
	$STATUS="V";
	$ACTION="T";
	$NATURE="H";
	$EN_VERSION="F";
	$EN_HVP="V";
	$EN_HVH="F";
        if($info_mois==0){
	        for($i=1;$i<=12;$i++){
	        	if($i<10){
				$DATE_RECHERCHE=$ANNEE."0".$i;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$i;
			}        	
			$MOIS=$i;
			$SEMAINE="";

			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL[$i]=$NB_ALL[$i]+$NB;
			$NB_ALL_ALL[$i]['LHVP']=$NB_ALL_ALL[$i]['LHVP']+$NB;
			echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
			
	        }
	        echo '<td align="center">&nbsp;'.$NB_ALL['ALL'].'&nbsp;</td>';
        }else{
		do {
	        	if($MOIS<10){
				$DATE_RECHERCHE=$ANNEE."0".$MOIS;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$MOIS;
			}        	
			$SEMAINE=$tab_rq_info_semaine['DATE_SEMAINE'];
			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL_ALL[$SEMAINE]['LHVP']=$NB_ALL_ALL[$SEMAINE]['LHVP']+$NB;
			echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
	
	        } while ($tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine));
	        $ligne= mysql_num_rows($res_rq_info_semaine);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_semaine, 0);
	          $tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine);
	        }
	        $SEMAINE='';
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
	        echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';
	}
        echo ' 
      </tr>';
// Livraison Hors Version Jeudi
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Livraison Hors Version Jeudi&nbsp</b></td>';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV=$actionENV;
	$STATUS="V";
	$ACTION="T";
	$NATURE="H";
	$EN_VERSION="F";
	$EN_HVP="F";
	$EN_HVH="V";
        if($info_mois==0){
	         for($i=1;$i<=12;$i++){
	        	if($i<10){
				$DATE_RECHERCHE=$ANNEE."0".$i;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$i;
			}        	
			$MOIS=$i;
			$SEMAINE="";

			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL[$i]=$NB_ALL[$i]+$NB;
			$NB_ALL_ALL[$i]['LHVJ']=$NB_ALL_ALL[$i]['LHVJ']+$NB;
			echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
			
	        }
	        echo '<td align="center">&nbsp;'.$NB_ALL['ALL'].'&nbsp;</td>';
        }else{
		do {
	        	if($MOIS<10){
				$DATE_RECHERCHE=$ANNEE."0".$MOIS;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$MOIS;
			}        	
			$SEMAINE=$tab_rq_info_semaine['DATE_SEMAINE'];
			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL_ALL[$SEMAINE]['LHVJ']=$NB_ALL_ALL[$SEMAINE]['LHVJ']+$NB;
			echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
	
	        } while ($tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine));
	        $ligne= mysql_num_rows($res_rq_info_semaine);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_semaine, 0);
	          $tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine);
	        }
	        $SEMAINE='';
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
	        echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';
	}
        echo ' 
      </tr>';
// Livraison Hors Version autres jours
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Livraison Hors Version autres jours&nbsp</b></td>';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV=$actionENV;
	$STATUS="V";
	$ACTION="T";
	$NATURE="H";
	$EN_VERSION="F";
	$EN_HVP="F";
	$EN_HVH="F";
        if($info_mois==0){
		for($i=1;$i<=12;$i++){
			if($i<10){
				$DATE_RECHERCHE=$ANNEE."0".$i;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$i;
			}        	
			$MOIS=$i;
			$SEMAINE="";

			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL[$i]=$NB_ALL[$i]+$NB;
			$NB_ALL_ALL[$i]['LHVA']=$NB_ALL_ALL[$i]['LHVA']+$NB;
			echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
			
		}
		echo '<td align="center">&nbsp;'.$NB_ALL['ALL'].'&nbsp;</td>';
	}else{
		do {
	        	if($MOIS<10){
				$DATE_RECHERCHE=$ANNEE."0".$MOIS;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$MOIS;
			}        	
			$SEMAINE=$tab_rq_info_semaine['DATE_SEMAINE'];
			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL_ALL[$SEMAINE]['LHVA']=$NB_ALL_ALL[$SEMAINE]['LHVA']+$NB;
			echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
	
	        } while ($tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine));
	        $ligne= mysql_num_rows($res_rq_info_semaine);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_semaine, 0);
	          $tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine);
	        }
	        $SEMAINE='';
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
	        echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';
	}
        echo ' 
      </tr>';
// traitements exceptionnels - Total
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;traitements exceptionnels - Total&nbsp</b></td>';
        for($i=1;$i<=$nb_colonne;$i++){
        	$NB_ALL[13]=$NB_ALL[13]+$NB_ALL[$i];
        	$NB_ALL_ALL[$i]['ALL']=$NB_ALL_ALL[$i]['ALL']+$NB_ALL[$i];
        	$NB_ALL_ALL[13]['ALL']=$NB_ALL_ALL[13]['ALL']+$NB_ALL[$i];
        	echo '<td align="center"><b>&nbsp;'.$NB_ALL[$i].'&nbsp;</b></td>';	
        }
        echo '<td align="center"><b>&nbsp;'.$NB_ALL[13].'&nbsp;</b></td>';
        echo ' 
      </tr>';
//Modification IAB (Action Ponctuelle)
      echo '
      <tr align="center" class="titre">
        <td align="left" colspan="'.$colspan.'">&nbsp;Modification IAB (Action Ponctuelle)&nbsp;</td>
      </tr>';
      for($i=1;$i<=13;$i++){
      	$NB_ALL[$i]=0;
      }
// Actions Ponctuelles
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Actions Ponctuelles&nbsp</b></td>';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV=$actionENV;
	$STATUS="V";
	$ACTION="M";
	$NATURE="P";
	$EN_VERSION="";
	$EN_HVP="";
	$EN_HVH="";
        if($info_mois==0){
	        for($i=1;$i<=12;$i++){
	        	if($i<10){
				$DATE_RECHERCHE=$ANNEE."0".$i;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$i;
			}        	
			$MOIS=$i;
			$SEMAINE="";

			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL[$i]=$NB_ALL[$i]+$NB;
			$NB_ALL_ALL[$i]['AP']=$NB_ALL_ALL[$i]['AP']+$NB;
			echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
			
	        }
	        echo '<td align="center">&nbsp;'.$NB_ALL['ALL'].'&nbsp;</td>';
        }else{
		do {
	        	if($MOIS<10){
				$DATE_RECHERCHE=$ANNEE."0".$MOIS;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$MOIS;
			}        	
			$SEMAINE=$tab_rq_info_semaine['DATE_SEMAINE'];
			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL_ALL[$SEMAINE]['AP']=$NB_ALL_ALL[$SEMAINE]['AP']+$NB;
			echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
	
	        } while ($tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine));
	        $ligne= mysql_num_rows($res_rq_info_semaine);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_semaine, 0);
	          $tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine);
	        }
	        $SEMAINE='';
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
	        echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';
	}
        echo ' 
      </tr>';
// Livraison En Version
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Livraison En Version&nbsp</b></td>';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV=$actionENV;
	$STATUS="V";
	$ACTION="M";
	$NATURE="V";
	$EN_VERSION="V";
	$EN_HVP="F";
	$EN_HVH="F";
        if($info_mois==0){
	        for($i=1;$i<=12;$i++){
	        	if($i<10){
				$DATE_RECHERCHE=$ANNEE."0".$i;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$i;
			}        	
			$MOIS=$i;
			$SEMAINE="";

			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL[$i]=$NB_ALL[$i]+$NB;
			$NB_ALL_ALL[$i]['LEV']=$NB_ALL_ALL[$i]['LEV']+$NB;
			echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
			
	        }
	        echo '<td align="center">&nbsp;'.$NB_ALL['ALL'].'&nbsp;</td>';
	}else{
		do {
	        	if($MOIS<10){
				$DATE_RECHERCHE=$ANNEE."0".$MOIS;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$MOIS;
			}        	
			$SEMAINE=$tab_rq_info_semaine['DATE_SEMAINE'];
			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL_ALL[$SEMAINE]['LEV']=$NB_ALL_ALL[$SEMAINE]['LEV']+$NB;
			echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
	
	        } while ($tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine));
	        $ligne= mysql_num_rows($res_rq_info_semaine);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_semaine, 0);
	          $tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine);
	        }
	        $SEMAINE='';
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
	        echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';
	}
        echo ' 
      </tr>';
// Livraison Hors Version Planifi&eacute;
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Livraison Hors Version Planifi&eacute;&nbsp</b></td>';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV=$actionENV;
	$STATUS="V";
	$ACTION="M";
	$NATURE="H";
	$EN_VERSION="F";
	$EN_HVP="V";
	$EN_HVH="F";
         if($info_mois==0){
	         for($i=1;$i<=12;$i++){
	        	if($i<10){
				$DATE_RECHERCHE=$ANNEE."0".$i;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$i;
			}        	
			$MOIS=$i;
			$SEMAINE="";

			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL[$i]=$NB_ALL[$i]+$NB;
			$NB_ALL_ALL[$i]['LHVP']=$NB_ALL_ALL[$i]['LHVP']+$NB;
			echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
			
	        }
	        echo '<td align="center">&nbsp;'.$NB_ALL['ALL'].'&nbsp;</td>';
        }else{
		do {
	        	if($MOIS<10){
				$DATE_RECHERCHE=$ANNEE."0".$MOIS;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$MOIS;
			}        	
			$SEMAINE=$tab_rq_info_semaine['DATE_SEMAINE'];
			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL_ALL[$SEMAINE]['LHVP']=$NB_ALL_ALL[$SEMAINE]['LHVP']+$NB;
			echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
	
	        } while ($tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine));
	        $ligne= mysql_num_rows($res_rq_info_semaine);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_semaine, 0);
	          $tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine);
	        }
	        $SEMAINE='';
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
	        echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';
	}
        echo ' 
      </tr>';
// Livraison Hors Version Jeudi
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Livraison Hors Version Jeudi&nbsp</b></td>';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV=$actionENV;
	$STATUS="V";
	$ACTION="M";
	$NATURE="H";
	$EN_VERSION="F";
	$EN_HVP="F";
	$EN_HVH="V";
        if($info_mois==0){
	        for($i=1;$i<=12;$i++){
	        	if($i<10){
				$DATE_RECHERCHE=$ANNEE."0".$i;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$i;
			}        	
			$MOIS=$i;
			$SEMAINE="";

			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL[$i]=$NB_ALL[$i]+$NB;
			$NB_ALL_ALL[$i]['LHVJ']=$NB_ALL_ALL[$i]['LHVJ']+$NB;
			echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
			
	        }
	        echo '<td align="center">&nbsp;'.$NB_ALL['ALL'].'&nbsp;</td>';
	}else{
		do {
	        	if($MOIS<10){
				$DATE_RECHERCHE=$ANNEE."0".$MOIS;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$MOIS;
			}        	
			$SEMAINE=$tab_rq_info_semaine['DATE_SEMAINE'];
			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL_ALL[$SEMAINE]['LHVJ']=$NB_ALL_ALL[$SEMAINE]['LHVJ']+$NB;
			echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
	
	        } while ($tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine));
	        $ligne= mysql_num_rows($res_rq_info_semaine);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_semaine, 0);
	          $tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine);
	        }
	        $SEMAINE='';
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
	        echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';
	}
        echo ' 
      </tr>';
// Livraison Hors Version autres jours
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Livraison Hors Version autres jours&nbsp</b></td>';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV=$actionENV;
	$STATUS="V";
	$ACTION="M";
	$NATURE="H";
	$EN_VERSION="F";
	$EN_HVP="F";
	$EN_HVH="F";
        if($info_mois==0){
	        for($i=1;$i<=12;$i++){
	        	if($i<10){
				$DATE_RECHERCHE=$ANNEE."0".$i;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$i;
			}        	
			$MOIS=$i;
			$SEMAINE="";

			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL[$i]=$NB_ALL[$i]+$NB;
			$NB_ALL_ALL[$i]['LHVA']=$NB_ALL_ALL[$i]['LHVA']+$NB;
			echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
			
	        }
	        echo '<td align="center">&nbsp;'.$NB_ALL['ALL'].'&nbsp;</td>';
	}else{
		do {
	        	if($MOIS<10){
				$DATE_RECHERCHE=$ANNEE."0".$MOIS;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$MOIS;
			}        	
			$SEMAINE=$tab_rq_info_semaine['DATE_SEMAINE'];
			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL_ALL[$SEMAINE]['LHVA']=$NB_ALL_ALL[$SEMAINE]['LHVA']+$NB;
			echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
	
	        } while ($tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine));
	        $ligne= mysql_num_rows($res_rq_info_semaine);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_semaine, 0);
	          $tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine);
	        }
	        $SEMAINE='';
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
	        echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';
	}
        echo ' 
      </tr>';
// Modification IAB (Action Ponctuelle) - Total
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Modification IAB (Action Ponctuelle) - Total&nbsp</b></td>';
        for($i=1;$i<=$nb_colonne;$i++){
        	$NB_ALL[13]=$NB_ALL[13]+$NB_ALL[$i];
        	$NB_ALL_ALL[$i]['ALL']=$NB_ALL_ALL[$i]['ALL']+$NB_ALL[$i];
        	$NB_ALL_ALL[13]['ALL']=$NB_ALL_ALL[13]['ALL']+$NB_ALL[$i];
        	echo '<td align="center"><b>&nbsp;'.$NB_ALL[$i].'&nbsp;</b></td>';	
        }
        echo '<td align="center"><b>&nbsp;'.$NB_ALL[13].'&nbsp;</b></td>';
        echo ' 
      </tr>';
//Demandes MOA (Action Ponctuelle)
      echo '
      <tr align="center" class="titre">
        <td align="left" colspan="'.$colspan.'">&nbsp;Demandes MOA (Action Ponctuelle)&nbsp;</td>
      </tr>';
      for($i=1;$i<=13;$i++){
      	$NB_ALL[$i]=0;
      }
// Actions Ponctuelles
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Actions Ponctuelles&nbsp</b></td>';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV=$actionENV;
	$STATUS="V";
	$ACTION="D";
	$NATURE="P";
	$EN_VERSION="";
	$EN_HVP="";
	$EN_HVH="";
        if($info_mois==0){
	        for($i=1;$i<=12;$i++){
	        	if($i<10){
				$DATE_RECHERCHE=$ANNEE."0".$i;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$i;
			}        	
			$MOIS=$i;
			$SEMAINE="";

			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL[$i]=$NB_ALL[$i]+$NB;
			$NB_ALL_ALL[$i]['AP']=$NB_ALL_ALL[$i]['AP']+$NB;
			echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
			
	        }
	        echo '<td align="center">&nbsp;'.$NB_ALL['ALL'].'&nbsp;</td>';
        }else{
		do {
	        	if($MOIS<10){
				$DATE_RECHERCHE=$ANNEE."0".$MOIS;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$MOIS;
			}        	
			$SEMAINE=$tab_rq_info_semaine['DATE_SEMAINE'];
			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL_ALL[$SEMAINE]['AP']=$NB_ALL_ALL[$SEMAINE]['AP']+$NB;
			echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
	
	        } while ($tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine));
	        $ligne= mysql_num_rows($res_rq_info_semaine);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_semaine, 0);
	          $tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine);
	        }
	        $SEMAINE='';
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
	        echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';
	}
        echo ' 
      </tr>';
// Livraison En Version
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Livraison En Version&nbsp</b></td>';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV=$actionENV;
	$STATUS="V";
	$ACTION="D";
	$NATURE="V";
	$EN_VERSION="V";
	$EN_HVP="F";
	$EN_HVH="F";
        if($info_mois==0){
	        for($i=1;$i<=12;$i++){
	        	if($i<10){
				$DATE_RECHERCHE=$ANNEE."0".$i;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$i;
			}        	
			$MOIS=$i;
			$SEMAINE="";

			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL[$i]=$NB_ALL[$i]+$NB;
			$NB_ALL_ALL[$i]['LEV']=$NB_ALL_ALL[$i]['LEV']+$NB;
			echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
			
	        }
	        echo '<td align="center">&nbsp;'.$NB_ALL['ALL'].'&nbsp;</td>';
        }else{
		do {
	        	if($MOIS<10){
				$DATE_RECHERCHE=$ANNEE."0".$MOIS;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$MOIS;
			}        	
			$SEMAINE=$tab_rq_info_semaine['DATE_SEMAINE'];
			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL_ALL[$SEMAINE]['LEV']=$NB_ALL_ALL[$SEMAINE]['LEV']+$NB;
			echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
	
	        } while ($tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine));
	        $ligne= mysql_num_rows($res_rq_info_semaine);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_semaine, 0);
	          $tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine);
	        }
	        $SEMAINE='';
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
	        echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';
	}
        echo ' 
      </tr>';
// Livraison Hors Version Planifi&eacute;
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Livraison Hors Version Planifi&eacute;&nbsp</b></td>';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV=$actionENV;
	$STATUS="V";
	$ACTION="D";
	$NATURE="H";
	$EN_VERSION="F";
	$EN_HVP="F";
	$EN_HVH="V";
        if($info_mois==0){
	        for($i=1;$i<=12;$i++){
	        	if($i<10){
				$DATE_RECHERCHE=$ANNEE."0".$i;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$i;
			}        	
			$MOIS=$i;
			$SEMAINE="";

			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL[$i]=$NB_ALL[$i]+$NB;
			$NB_ALL_ALL[$i]['LHVP']=$NB_ALL_ALL[$i]['LHVP']+$NB;
			echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
			
	        }
	        echo '<td align="center">&nbsp;'.$NB_ALL['ALL'].'&nbsp;</td>';
        }else{
		do {
	        	if($MOIS<10){
				$DATE_RECHERCHE=$ANNEE."0".$MOIS;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$MOIS;
			}        	
			$SEMAINE=$tab_rq_info_semaine['DATE_SEMAINE'];
			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL_ALL[$SEMAINE]['LHVP']=$NB_ALL_ALL[$SEMAINE]['LHVP']+$NB;
			echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
	
	        } while ($tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine));
	        $ligne= mysql_num_rows($res_rq_info_semaine);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_semaine, 0);
	          $tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine);
	        }
	        $SEMAINE='';
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
	        echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';
	}
        echo ' 
      </tr>';
// Livraison Hors Version Jeudi
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Livraison Hors Version Jeudi&nbsp</b></td>';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV=$actionENV;
	$STATUS="V";
	$ACTION="D";
	$NATURE="H";
	$EN_VERSION="F";
	$EN_HVP="F";
	$EN_HVH="V";
        if($info_mois==0){
	        for($i=1;$i<=12;$i++){
	        	if($i<10){
				$DATE_RECHERCHE=$ANNEE."0".$i;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$i;
			}        	
			$MOIS=$i;
			$SEMAINE="";

			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL[$i]=$NB_ALL[$i]+$NB;
			$NB_ALL_ALL[$i]['LHVJ']=$NB_ALL_ALL[$i]['LHVJ']+$NB;
			echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
			
	        }
	        echo '<td align="center">&nbsp;'.$NB_ALL['ALL'].'&nbsp;</td>';
        }else{
		do {
	        	if($MOIS<10){
				$DATE_RECHERCHE=$ANNEE."0".$MOIS;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$MOIS;
			}        	
			$SEMAINE=$tab_rq_info_semaine['DATE_SEMAINE'];
			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL_ALL[$SEMAINE]['LHVJ']=$NB_ALL_ALL[$SEMAINE]['LHVJ']+$NB;
			echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
	
	        } while ($tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine));
	        $ligne= mysql_num_rows($res_rq_info_semaine);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_semaine, 0);
	          $tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine);
	        }
	        $SEMAINE='';
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
	        echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';
	}
        echo ' 
      </tr>';
// Livraison Hors Version autres jours
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Livraison Hors Version autres jours&nbsp</b></td>';
        $NB_ALL['ALL']=0;
	$APP="";
	$ENV=$actionENV;
	$STATUS="V";
	$ACTION="D";
	$NATURE="H";
	$EN_VERSION="F";
	$EN_HVP="F";
	$EN_HVH="F";
        if($info_mois==0){
	        for($i=1;$i<=12;$i++){
	        	if($i<10){
				$DATE_RECHERCHE=$ANNEE."0".$i;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$i;
			}        	
			$MOIS=$i;
			$SEMAINE="";

			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL[$i]=$NB_ALL[$i]+$NB;
			$NB_ALL_ALL[$i]['LHVA']=$NB_ALL_ALL[$i]['LHVA']+$NB;
			echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
			
	        }
	        echo '<td align="center">&nbsp;'.$NB_ALL['ALL'].'&nbsp;</td>';
        }else{
		do {
	        	if($MOIS<10){
				$DATE_RECHERCHE=$ANNEE."0".$MOIS;
			}else{
				$DATE_RECHERCHE=$ANNEE."".$MOIS;
			}        	
			$SEMAINE=$tab_rq_info_semaine['DATE_SEMAINE'];
			$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
			$NB_ALL['ALL']=$NB_ALL['ALL']+$NB;
			$NB_ALL_ALL[$SEMAINE]['LHVA']=$NB_ALL_ALL[$SEMAINE]['LHVA']+$NB;
			echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';	
	
	        } while ($tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine));
	        $ligne= mysql_num_rows($res_rq_info_semaine);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_semaine, 0);
	          $tab_rq_info_semaine = mysql_fetch_assoc($res_rq_info_semaine);
	        }
	        $SEMAINE='';
		$NB=Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$SEMAINE,$APP,$ENV,$STATUS,$ACTION,$NATURE,$EN_VERSION,$EN_HVP,$EN_HVH,$mysql_link);
	        echo '<td align="center">&nbsp;'.$NB.'&nbsp;</td>';
	}
        echo ' 
      </tr>';
// Demandes MOA (Action Ponctuelle) - Total
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Demandes MOA (Action Ponctuelle) - Total&nbsp</b></td>';
        for($i=1;$i<=$nb_colonne;$i++){
        	$NB_ALL[13]=$NB_ALL[13]+$NB_ALL[$i];
        	$NB_ALL_ALL[$i]['ALL']=$NB_ALL_ALL[$i]['ALL']+$NB_ALL[$i];
        	$NB_ALL_ALL[13]['ALL']=$NB_ALL_ALL[13]['ALL']+$NB_ALL[$i];
        	echo '<td align="center"><b>&nbsp;'.$NB_ALL[$i].'&nbsp;</b></td>';	
        }
        echo '<td align="center"><b>&nbsp;'.$NB_ALL[13].'&nbsp;</b></td>';
        echo ' 
      </tr>';
//Totaux
      echo '
      <tr align="center" class="titre">
        <td align="left" colspan="'.$colspan.'">&nbsp;Totaux&nbsp;</td>
      </tr>';
// Actions Ponctuelles
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Actions Ponctuelles&nbsp</b></td>';
        for($i=1;$i<=$nb_colonne;$i++){
        	$NB_ALL_ALL[13]['AP']=$NB_ALL_ALL[13]['AP']+$NB_ALL_ALL[$i]['AP'];
        	echo '<td align="center">&nbsp;'.$NB_ALL_ALL[$i]['AP'].'&nbsp;</td>';	
        }
        echo '<td align="center">&nbsp;'.$NB_ALL_ALL[13]['AP'].'&nbsp;</td>';
        echo ' 
      </tr>';
// Livraison En Version
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Livraison En Version&nbsp</b></td>';
        for($i=1;$i<=$nb_colonne;$i++){
        	$NB_ALL_ALL[13]['LEV']=$NB_ALL_ALL[13]['LEV']+$NB_ALL_ALL[$i]['LEV'];
        	echo '<td align="center">&nbsp;'.$NB_ALL_ALL[$i]['LEV'].'&nbsp;</td>';	
        }
        echo '<td align="center">&nbsp;'.$NB_ALL_ALL[13]['LEV'].'&nbsp;</td>';
        echo ' 
      </tr>';
// Livraison Hors Version Planifi&eacute;
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Livraison Hors Version Planifi&eacute;&nbsp</b></td>';
        for($i=1;$i<=$nb_colonne;$i++){
        	$NB_ALL_ALL[13]['LHVP']=$NB_ALL_ALL[13]['LHVP']+$NB_ALL_ALL[$i]['LHVP'];
        	echo '<td align="center">&nbsp;'.$NB_ALL_ALL[$i]['LHVP'].'&nbsp;</td>';	
        }
        echo '<td align="center">&nbsp;'.$NB_ALL_ALL[13]['LHVP'].'&nbsp;</td>';
        echo ' 
      </tr>';
// Livraison Hors Version Jeudi
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Livraison Hors Version Jeudi&nbsp</b></td>';
        for($i=1;$i<=$nb_colonne;$i++){
        	$NB_ALL_ALL[13]['LHVJ']=$NB_ALL_ALL[13]['LHVJ']+$NB_ALL_ALL[$i]['LHVJ'];
        	echo '<td align="center">&nbsp;'.$NB_ALL_ALL[$i]['LHVJ'].'&nbsp;</td>';	
        }
        echo '<td align="center">&nbsp;'.$NB_ALL_ALL[13]['LHVJ'].'&nbsp;</td>';
        echo ' 
      </tr>';
// Livraison Hors Version autres jours
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Livraison Hors Version autres jours&nbsp</b></td>';
        for($i=1;$i<=$nb_colonne;$i++){
        	$NB_ALL_ALL[13]['LHVA']=$NB_ALL_ALL[13]['LHVA']+$NB_ALL_ALL[$i]['LHVA'];
        	echo '<td align="center">&nbsp;'.$NB_ALL_ALL[$i]['LHVA'].'&nbsp;</td>';	
        }
        echo '<td align="center">&nbsp;'.$NB_ALL_ALL[13]['LHVA'].'&nbsp;</td>';
        echo ' 
      </tr>';
// Total
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Total&nbsp</b></td>';
        for($i=1;$i<=$nb_colonne;$i++){
        	echo '<td align="center"><b>&nbsp;'.$NB_ALL_ALL[$i]['ALL'].'&nbsp;</b></td>';	
        }
        echo '<td align="center"><b>&nbsp;'.$NB_ALL_ALL[13]['ALL'].'&nbsp;</b></td>';
        echo ' 
      </tr>';
// fin de la page
      echo '
      <tr align="center" class="titre">
        <td align="center" colspan="'.$colspan.'">&nbsp;</td>
      </tr>
      <tr align="center" class="titre">
        <td align="center" colspan="'.$colspan.'">&nbsp;[&nbsp;<a href="./index.php?ITEM=indicateur_menu">Retour</a>&nbsp;]&nbsp;</td>
      </tr>
      <tr align="center" class="titre">
        <td align="center" colspan="'.$colspan.'">&nbsp;</td>
      </tr>
    </table>
  </div>
  ';
mysql_close($mysql_link);
?>