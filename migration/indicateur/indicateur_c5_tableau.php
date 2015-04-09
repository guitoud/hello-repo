<?PHP
//redirection si acces dirrect
if(substr_count($_SERVER['PHP_SELF'], 'index.php')==0){
  header("Location: ../"); 
  exit(); 
}
/*******************************************************************
   Interface Gestion des indicateurs c5
   Version 1.0.0   
  15/02/2011 - VGU - Creation fichier
**************************************************Guibert Vincent***
*******************************************************************/

require_once("./cf/autre_fonctions.php");

function Return_NB($DATE_RECHERCHE,$APPLI,$TYPE,$mysql_link)
{
	$NB=0;
	$APPLI_SQL='';
	$mois=substr($DATE_RECHERCHE,4,2);
	$annee=substr($DATE_RECHERCHE,0,4);
	if($APPLI!=''){
		$APPLI_SQL="AND `APPLI`='".$APPLI."'";
	}
	$rq_info="
	SELECT COUNT(DISTINCT (`REF`)) AS `NB` 
	FROM `indicateur_calcul` 
	WHERE `DATE_INDICATEUR` LIKE '".$DATE_RECHERCHE."'
	AND  `DATE` LIKE '".$DATE_RECHERCHE."%'
	".$APPLI_SQL."
	AND `ACTION_NEW` = '".$TYPE."'
	AND `ENV` ='E'
	";
	//echo $rq_info.'</BR>';
	$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
	$tab_rq_info = mysql_fetch_assoc($res_rq_info);
	$total_ligne_rq_info=mysql_num_rows($res_rq_info);
	if($total_ligne_rq_info==0){
		$NB=0;
	}else{
		$NB=$tab_rq_info['NB'];
		if($tab_rq_info['NB']==''){$NB=0;}
	}
	mysql_free_result($res_rq_info);
	return $NB;
}

if(isset($_GET['ANNEE'])){
	$ANNEE=$_GET['ANNEE'];
}else{
	$ANNEE=date("Y");
}
$j=0;
$ID='';

require("./cf/conf_outil_icdc.php"); 
require_once("./cf/fonctions.php");

  echo '
   <div align="center">
    <br/>
    <table class="table_inc" cellspacing="1" cellpading="0">
      <tr align="center" class="titre">
        <td align="center" colspan="40">&nbsp;[&nbsp;<a href="./index.php?ITEM=indicateur_menu">Retour</a>&nbsp;]&nbsp;</td>
      </tr>
      <tr align="center" >
        <td align="center" colspan="40">&nbsp;L = LUP , V = Version , D = D&eacute;rogation &nbsp;</td>
      </tr>
      <tr align="center" class="titre">
        <td align="center" colspan="40"><b>&nbsp;Tableau de Bord C5 pour '.$ANNEE.'&nbsp;</b></td>
      </tr>
      <tr align="center" class="titre">
        <td align="center"><b>&nbsp;Application&nbsp;</b></td>
        <td align="center" colspan="3"><b>&nbsp;Janvier&nbsp;</b></td>
        <td align="center" colspan="3"><b>&nbsp;F&eacute;vrier&nbsp;</b></td>
        <td align="center" colspan="3"><b>&nbsp;Mars&nbsp;</b></td>
        <td align="center" colspan="3"><b>&nbsp;Avril&nbsp;</b></td>
        <td align="center" colspan="3"><b>&nbsp;Mai&nbsp;</b></td>
        <td align="center" colspan="3"><b>&nbsp;Juin&nbsp;</b></td>
        <td align="center" colspan="3"><b>&nbsp;Juillet&nbsp;</b></td>
        <td align="center" colspan="3"><b>&nbsp;Aout&nbsp;</b></td>
        <td align="center" colspan="3"><b>&nbsp;Septembre&nbsp;</b></td>
        <td align="center" colspan="3"><b>&nbsp;Octobre&nbsp;</b></td>
        <td align="center" colspan="3"><b>&nbsp;Novembre&nbsp;</b></td>
        <td align="center" colspan="3"><b>&nbsp;D&eacute;cembre&nbsp;</b></td>
        <td align="center" colspan="3"><b>&nbsp;'.$ANNEE.'&nbsp;</b></td>
      </tr>
      <tr align="center" class="titre">
        <td align="center">&nbsp;</td>';
        
        for ($k=1;$k<=13; $k++)
	{
		echo '
	       <td align="center"><b>&nbsp;L&nbsp;</b></td>
	        <td align="center"><b>&nbsp;V&nbsp;</b></td>
	        <td align="center"><b>&nbsp;D&nbsp;</b></td>';
		$NB_L_ALL[$k]=0;
		$NB_L_LUP_ALL[$k]=0;
		$NB_L_VERS_ALL[$k]=0;
	}
	echo '</tr>';
	echo '<tr align="center" class="titre">
        <td align="center">&nbsp;</td>';
        
        for ($k=1;$k<=12; $k++)
	{
		if($k < 10 ){
			$MOIS='0'.$k;
		}else{
			$MOIS=$k;
		}
		$DATE_RECHERCHE=$ANNEE.''.$MOIS;
		$APPLI='';
		
		$TYPE='L';
		$NB_L_ALL[$k]=Return_NB($DATE_RECHERCHE,$APPLI,$TYPE,$mysql_link);
		$TYPE='L_LUP';
		$NB_L_LUP_ALL[$k]=Return_NB($DATE_RECHERCHE,$APPLI,$TYPE,$mysql_link);
		$TYPE='L_VERS';
		$NB_L_VERS_ALL[$k]=Return_NB($DATE_RECHERCHE,$APPLI,$TYPE,$mysql_link);
		$NB_L_ALL[13]=$NB_L_ALL[13]+$NB_L_ALL[$k];
		$NB_L_LUP_ALL[13]=$NB_L_LUP_ALL[13]+$NB_L_LUP_ALL[$k];
		$NB_L_VERS_ALL[13]=$NB_L_VERS_ALL[13]+$NB_L_VERS_ALL[$k];
		if($NB_L_ALL[$k]==0){$NB_L_ALL[$k]='';}
		if($NB_L_LUP_ALL[$k]==0){$NB_L_LUP_ALL[$k]='';}
		if($NB_L_VERS_ALL[$k]==0){$NB_L_VERS_ALL[$k]='';}
		echo '
		<td align="center">&nbsp;'.$NB_L_LUP_ALL[$k].'&nbsp;</td>
	        <td align="center">&nbsp;'.$NB_L_VERS_ALL[$k].'&nbsp;</td>
	        <td align="center">&nbsp;'.$NB_L_ALL[$k].'&nbsp;</td>';
		
	}  
	$k=13;
	if($NB_L_ALL[$k]==0){$NB_L_ALL[$k]='';}
	if($NB_L_LUP_ALL[$k]==0){$NB_L_LUP_ALL[$k]='';}
	if($NB_L_VERS_ALL[$k]==0){$NB_L_VERS_ALL[$k]='';}
	echo '
	<td align="center">&nbsp;'.$NB_L_LUP_ALL[$k].'&nbsp;</td>
        <td align="center">&nbsp;'.$NB_L_VERS_ALL[$k].'&nbsp;</td>
        <td align="center">&nbsp;'.$NB_L_ALL[$k].'&nbsp;</td>';      
	echo '</tr>';
	
	echo '<tr align="center" class="titre">
        <td align="center">&nbsp;</td>';
        
        for ($k=1;$k<=13; $k++)
	{
		$SOMME=$NB_L_ALL[$k]+$NB_L_LUP_ALL[$k]+$NB_L_VERS_ALL[$k];
          	if($SOMME > 0){
          		$POUR_L=round(($NB_L_ALL[$k]*100) / $SOMME,2);
          		$POUR_L_LUP=round(($NB_L_LUP_ALL[$k]*100) / $SOMME,2);
          		$POUR_L_VERS=round(($NB_L_VERS_ALL[$k]*100) / $SOMME,2);
        	}else{
        		$POUR_L='';
        		$POUR_L_LUP='';
        		$POUR_L_VERS='';
        	}
        	if($POUR_L>0){$POUR_L=$POUR_L.'%';}
        	if($POUR_L_LUP>0){$POUR_L_LUP=$POUR_L_LUP.'%';}
        	if($POUR_L_VERS>0){$POUR_L_VERS=$POUR_L_VERS.'%';}
        	if($POUR_L_VERS==0){$POUR_L_VERS='';}
        	if($POUR_L_LUP==0){$POUR_L_LUP='';}
        	if($POUR_L==0){$POUR_L='';}
        	echo '
        	<td align="center">&nbsp;'.$POUR_L_LUP.'&nbsp;</td>
        	<td align="center">&nbsp;'.$POUR_L_VERS.'&nbsp;</td>
        	<td align="center">&nbsp;'.$POUR_L.'&nbsp;</td>
        	';
		
	}        
	echo '</tr>';
	echo '<tr align="center" class="titre">
        <td align="center">&nbsp;</td>';
        
        for ($k=1;$k<=13; $k++)
	{
		$NB_L_ALL_ALL[$k]=$NB_L_LUP_ALL[$k]+$NB_L_VERS_ALL[$k]+$NB_L_ALL[$k];
		if($NB_L_ALL_ALL[$k]==0){$NB_L_ALL_ALL[$k]='';}
		if($NB_L_ALL_ALL[$k]!=''){
			if($k==13){
				echo '<td align="center" colspan="3">&nbsp;'.$NB_L_ALL_ALL[$k].'&nbsp;</td>';
			}else{
				echo '<td align="center" colspan="3"><a href="./index.php?ITEM=indicateur_c5_liste&ANNEE='.$ANNEE.'&MOIS='.$k.'">&nbsp;'.$NB_L_ALL_ALL[$k].'&nbsp;</a></td>';
			}
		}else{
			echo '<td align="center" colspan="3">&nbsp;'.$NB_L_ALL_ALL[$k].'&nbsp;</td>';
		}
		 
		
		
	}  
	echo '</tr>';

	
      
//	$rq_info_appli="
//	SELECT DISTINCT(UPPER(`id_appli`)) AS `id_appli` 
//	FROM `referentiel_appli` 
//	WHERE UPPER(`id_appli`) NOT IN ('TOUT','TEST')
//	ORDER BY `id_appli` ASC "; 
	
	$rq_info_appli="
	SELECT DISTINCT(UPPER(`INDICATEUR_APPLICATION_REF`)) AS `id_appli`
	FROM `indicateur_application` 
	WHERE `INDICATEUR_APPLICATION_REF` NOT IN ('','Tout')
	ORDER BY `INDICATEUR_APPLICATION_REF` ASC "; 

	$res_rq_info_appli = mysql_query($rq_info_appli, $mysql_link) or die(mysql_error());
	$tab_rq_info_appli = mysql_fetch_assoc($res_rq_info_appli);
	$total_ligne_rq_info_appli=mysql_num_rows($res_rq_info_appli);
	if($total_ligne_rq_info_appli==0){
		echo '
	        <tr align="center" class="titre">
	          <td align="center" colspan="40">&nbsp;Pas d\'application dans la base.</td>
	      	</tr>';	
	}else{

		do {
			$j++;
		        if ($j%2) { $class = "pair";}else{$class = "impair";} 
			$APP=$tab_rq_info_appli['id_appli'];
			echo '
		        <tr align="center" class='.$class.'>
		          <td align="center">&nbsp;'.$APP.'&nbsp;</td>';
			        for ($k=1;$k<=13; $k++)
				{
					if($k < 10 ){
						$MOIS='0'.$k;
					}else{
						$MOIS=$k;
					}
					$DATE_RECHERCHE=$ANNEE.''.$MOIS;
					$rq_info_appli_detail="
					SELECT DISTINCT(UPPER(`INDICATEUR_APPLICATION_AUTRE`)) AS `id_appli`
					FROM `indicateur_application` 
					WHERE `INDICATEUR_APPLICATION_REF` ='".$APP."'
					ORDER BY `INDICATEUR_APPLICATION_REF` ASC "; 
					$res_rq_info_appli_detail = mysql_query($rq_info_appli_detail, $mysql_link) or die(mysql_error());
					$tab_rq_info_appli_detail = mysql_fetch_assoc($res_rq_info_appli_detail);
					$total_ligne_rq_info_appli_detail=mysql_num_rows($res_rq_info_appli_detail);
					$NB_L=0;
					$NB_L_LUP=0;
					$NB_L_VERS=0;
					do {
						$APPLI=$tab_rq_info_appli_detail['id_appli'];;
						
						$TYPE='L';
						$NB_L=$NB_L+Return_NB($DATE_RECHERCHE,$APPLI,$TYPE,$mysql_link);
						$TYPE='L_LUP';
						$NB_L_LUP=$NB_L_LUP+Return_NB($DATE_RECHERCHE,$APPLI,$TYPE,$mysql_link);
						$TYPE='L_VERS';
						$NB_L_VERS=$NB_L_VERS+Return_NB($DATE_RECHERCHE,$APPLI,$TYPE,$mysql_link);
					} while ($tab_rq_info_appli_detail = mysql_fetch_assoc($res_rq_info_appli_detail));
				        $ligne= mysql_num_rows($res_rq_info_appli_detail);
				        if($ligne > 0) {
				          mysql_data_seek($res_rq_info_appli_detail, 0);
				          $tab_rq_info_appli_detail = mysql_fetch_assoc($res_rq_info_appli_detail);
				        } 
					
					
					if($NB_L==0){$NB_L='';}
					if($NB_L_LUP==0){$NB_L_LUP='';}
					if($NB_L_VERS==0){$NB_L_VERS='';}
					echo '
					<td align="center">&nbsp;'.$NB_L_LUP.'&nbsp;</td>
				        <td align="center">&nbsp;'.$NB_L_VERS.'&nbsp;</td>
				        <td align="center">&nbsp;'.$NB_L.'&nbsp;</td>';
					
				}        
			echo '</tr>';
		} while ($tab_rq_info_appli = mysql_fetch_assoc($res_rq_info_appli));
	        $ligne= mysql_num_rows($res_rq_info_appli);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_appli, 0);
	          $tab_rq_info_appli = mysql_fetch_assoc($res_rq_info_appli);
	        }    
	}
        mysql_free_result($res_rq_info_appli);
        echo '
        <tr align="center" class="titre">
          <td align="center" colspan="40">&nbsp;</td>
        </tr>';	
      	$rq_info_appli="
	SELECT DISTINCT (UPPER(`APPLI`)) AS `APPLI` 
	FROM `indicateur_calcul` 
	WHERE UPPER(`APPLI`) NOT IN (
		SELECT DISTINCT(UPPER(`INDICATEUR_APPLICATION_AUTRE`)) AS `APPLI` 
		FROM `indicateur_application` ) 
	AND `APPLI`!='' 
	ORDER BY `APPLI` ASC "; 
	$res_rq_info_appli = mysql_query($rq_info_appli, $mysql_link) or die(mysql_error());
	$tab_rq_info_appli = mysql_fetch_assoc($res_rq_info_appli);
	$total_ligne_rq_info_appli=mysql_num_rows($res_rq_info_appli);
	if($total_ligne_rq_info_appli!=0){
		do {
			$j++;
		        if ($j%2) { $class = "pair";}else{$class = "impair";} 
			$APP=$tab_rq_info_appli['APPLI'];
			echo '
		        <tr align="center" class='.$class.'>
		          <td align="center">&nbsp;'.$APP.'&nbsp;</td>';
			        for ($k=1;$k<=13; $k++)
				{
					if($k < 10 ){
						$MOIS='0'.$k;
					}else{
						$MOIS=$k;
					}
					$DATE_RECHERCHE=$ANNEE.''.$MOIS;
					$APPLI=$APP;
					
					$TYPE='L';
					$NB_L=Return_NB($DATE_RECHERCHE,$APPLI,$TYPE,$mysql_link);
					$TYPE='L_LUP';
					$NB_L_LUP=Return_NB($DATE_RECHERCHE,$APPLI,$TYPE,$mysql_link);
					$TYPE='L_VERS';
					$NB_L_VERS=Return_NB($DATE_RECHERCHE,$APPLI,$TYPE,$mysql_link);
					if($NB_L==0){$NB_L='';}
					if($NB_L_LUP==0){$NB_L_LUP='';}
					if($NB_L_VERS==0){$NB_L_VERS='';}
					echo '
					<td align="center">&nbsp;'.$NB_L_LUP.'&nbsp;</td>
				        <td align="center">&nbsp;'.$NB_L_VERS.'&nbsp;</td>
				        <td align="center">&nbsp;'.$NB_L.'&nbsp;</td>';
					
				}        
			echo '</tr>';
		} while ($tab_rq_info_appli = mysql_fetch_assoc($res_rq_info_appli));
	        $ligne= mysql_num_rows($res_rq_info_appli);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_appli, 0);
	          $tab_rq_info_appli = mysql_fetch_assoc($res_rq_info_appli);
	        }    
	}
        mysql_free_result($res_rq_info_appli);
      echo '
      <tr align="center" class="titre">
        <td align="center" colspan="40">&nbsp;</td>
      </tr>
      <tr align="center" class="titre">
        <td align="center" colspan="40">&nbsp;[&nbsp;<a href="./index.php?ITEM=indicateur_menu">Retour</a>&nbsp;]&nbsp;</td>
      </tr>
      <tr align="center" class="titre">
        <td align="center" colspan="40">&nbsp;</td>
      </tr>
    </table>
  </div>
  ';
mysql_close($mysql_link);
?>