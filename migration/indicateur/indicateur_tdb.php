<?PHP
//redirection si acces dirrect
if(substr_count($_SERVER['PHP_SELF'], 'index.php')==0){
  header("Location: ../"); 
  exit(); 
}
/*******************************************************************
   Interface Gestion des indicateurs
   Version 1.0.0   
  28/01/2010 - VGU - Creation fichier
**************************************************Guibert Vincent***
*******************************************************************/

require("./cf/conf_outil_icdc.php"); 
require_once("./cf/fonctions.php");
$action=$_GET['action'];
if(isset($_GET['action'])){
	$action=$_GET['action'];
}else{
	$action='PROD';
}
if(isset($_GET['ANNEE'])){
	$ANNEE=$_GET['ANNEE'];
}else{
	$ANNEE=date("Y");
}
$j=0;
$ID='';
function Return_NB($DATE_RECHERCHE,$ANNEE,$MOIS,$action,$type,$mysql_link)
{
	$ENV='I';
	$INFO_SQL="";
	if($action=='PROD'){
		$ENV='E';	
	}
	if($action=='VA'){
		$ENV='V';	
	}
	if($type==1){$INFO_SQL="";}
	if($type==2){$INFO_SQL="`ACTION`='L' AND";}
	if($type==3){$INFO_SQL="`ACTION`='T' AND";}
	if($type==4){$INFO_SQL="`ACTION`='M' AND";}
	if($type==5){$INFO_SQL="`ACTION`='D' AND";}
	if($type==6){$INFO_SQL="`ACTION`='I' AND";}
	$rq_info="
	SELECT COUNT(`REF`) AS `NB` 
	FROM `indicateur_calcul` 
	WHERE
	`STATUS`='V' AND
	`DATE_ANNEE`='".$ANNEE."' AND
	`DATE_MOIS`=".$MOIS." AND
	`ENV`='".$ENV."' AND
	".$INFO_SQL."
	`DATE_INDICATEUR`='".$DATE_RECHERCHE."'";
	$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
	$tab_rq_info = mysql_fetch_assoc($res_rq_info);
	$total_ligne_rq_info=mysql_num_rows($res_rq_info);
	$NB=$tab_rq_info['NB'];
	mysql_free_result($res_rq_info);
	return $NB;
}
function Return_DUREE($DATE_RECHERCHE,$ANNEE,$MOIS,$action,$type,$mysql_link)
{
	$ENV='I';
	$INFO_SQL="";
	if($action=='PROD'){
		$ENV='E';	
	}
	if($action=='VA'){
		$ENV='V';	
	}
	if($type==1){$INFO_SQL="";}
	if($type==2){$INFO_SQL="`ACTION`='L' AND";}
	if($type==3){$INFO_SQL="`ACTION`='T' AND";}
	if($type==4){$INFO_SQL="`ACTION`='M' AND";}
	if($type==5){$INFO_SQL="`ACTION`='D' AND";}
	if($type==6){$INFO_SQL="`ACTION`='I' AND";}
	$rq_info="
	SELECT SUM(`DUREE`) AS `DUREE` 
	FROM `indicateur_calcul` 
	WHERE
	`STATUS`='V' AND
	`DATE_ANNEE`='".$ANNEE."' AND
	`DATE_MOIS`=".$MOIS." AND
	`ENV`='".$ENV."' AND
	".$INFO_SQL."
	`DATE_INDICATEUR`='".$DATE_RECHERCHE."'";
	$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
	$tab_rq_info = mysql_fetch_assoc($res_rq_info);
	$total_ligne_rq_info=mysql_num_rows($res_rq_info);
	$DUREE=$tab_rq_info['DUREE'];
	if($DUREE==''){$DUREE=0;}
	mysql_free_result($res_rq_info);
	return $DUREE;
}
## Calcul des indicateurs
for($i=1;$i<=12;$i++){
  if($i<10){
	$DATE_RECHERCHE=$ANNEE."0".$i;
	$NB_INTER[$i][1]=Return_NB($DATE_RECHERCHE,$ANNEE,$i,$action,1,$mysql_link);
	$TEMPS_INTER[$i][1]=Return_DUREE($DATE_RECHERCHE,$ANNEE,$i,$action,1,$mysql_link);
	$NB_INTER[$i][2]=Return_NB($DATE_RECHERCHE,$ANNEE,$i,$action,2,$mysql_link);
	$TEMPS_INTER[$i][2]=Return_DUREE($DATE_RECHERCHE,$ANNEE,$i,$action,2,$mysql_link);
	$NB_INTER[$i][3]=Return_NB($DATE_RECHERCHE,$ANNEE,$i,$action,3,$mysql_link);
	$TEMPS_INTER[$i][3]=Return_DUREE($DATE_RECHERCHE,$ANNEE,$i,$action,3,$mysql_link);
	$NB_INTER[$i][4]=Return_NB($DATE_RECHERCHE,$ANNEE,$i,$action,4,$mysql_link);
	$TEMPS_INTER[$i][4]=Return_DUREE($DATE_RECHERCHE,$ANNEE,$i,$action,4,$mysql_link);
	$NB_INTER[$i][5]=Return_NB($DATE_RECHERCHE,$ANNEE,$i,$action,5,$mysql_link);
	$TEMPS_INTER[$i][5]=Return_DUREE($DATE_RECHERCHE,$ANNEE,$i,$action,5,$mysql_link);
	$NB_INTER[$i][6]=Return_NB($DATE_RECHERCHE,$ANNEE,$i,$action,6,$mysql_link);
	$TEMPS_INTER[$i][6]=Return_DUREE($DATE_RECHERCHE,$ANNEE,$i,$action,6,$mysql_link);
  }else{
    	$DATE_RECHERCHE=$ANNEE."".$i;  
	$NB_INTER[$i][1]=Return_NB($DATE_RECHERCHE,$ANNEE,$i,$action,1,$mysql_link);
	$TEMPS_INTER[$i][1]=Return_DUREE($DATE_RECHERCHE,$ANNEE,$i,$action,1,$mysql_link);
	$NB_INTER[$i][2]=Return_NB($DATE_RECHERCHE,$ANNEE,$i,$action,2,$mysql_link);
	$TEMPS_INTER[$i][2]=Return_DUREE($DATE_RECHERCHE,$ANNEE,$i,$action,2,$mysql_link);
	$NB_INTER[$i][3]=Return_NB($DATE_RECHERCHE,$ANNEE,$i,$action,3,$mysql_link);
	$TEMPS_INTER[$i][3]=Return_DUREE($DATE_RECHERCHE,$ANNEE,$i,$action,3,$mysql_link);
	$NB_INTER[$i][4]=Return_NB($DATE_RECHERCHE,$ANNEE,$i,$action,4,$mysql_link);
	$TEMPS_INTER[$i][4]=Return_DUREE($DATE_RECHERCHE,$ANNEE,$i,$action,4,$mysql_link);
	$NB_INTER[$i][5]=Return_NB($DATE_RECHERCHE,$ANNEE,$i,$action,5,$mysql_link);
	$TEMPS_INTER[$i][5]=Return_DUREE($DATE_RECHERCHE,$ANNEE,$i,$action,5,$mysql_link);
	$NB_INTER[$i][6]=Return_NB($DATE_RECHERCHE,$ANNEE,$i,$action,6,$mysql_link);
	$TEMPS_INTER[$i][6]=Return_DUREE($DATE_RECHERCHE,$ANNEE,$i,$action,6,$mysql_link);
    }            
}
$NB_INTER[13][1]=0;
$TEMPS_INTER[13][1]=0;
$NB_INTER[13][2]=0;
$TEMPS_INTER[13][2]=0;
$NB_INTER[13][3]=0;
$TEMPS_INTER[13][3]=0;
$NB_INTER[13][4]=0;
$TEMPS_INTER[13][4]=0;
$NB_INTER[13][5]=0;
$TEMPS_INTER[13][5]=0;
$NB_INTER[13][6]=0;
$TEMPS_INTER[13][6]=0;
for($i=1;$i<=12;$i++){
	$NB_INTER[13][1]=$NB_INTER[13][1]+$NB_INTER[$i][1];
	$TEMPS_INTER[13][1]=$TEMPS_INTER[13][1]+$TEMPS_INTER[$i][1];
	$NB_INTER[13][2]=$NB_INTER[13][2]+$NB_INTER[$i][2];
	$TEMPS_INTER[13][2]=$TEMPS_INTER[13][2]+$TEMPS_INTER[$i][2];
	$NB_INTER[13][3]=$NB_INTER[13][3]+$NB_INTER[$i][3];
	$TEMPS_INTER[13][3]=$TEMPS_INTER[13][3]+$TEMPS_INTER[$i][3];
	$NB_INTER[13][4]=$NB_INTER[13][4]+$NB_INTER[$i][4];
	$TEMPS_INTER[13][4]=$TEMPS_INTER[13][4]+$TEMPS_INTER[$i][4];
	$NB_INTER[13][5]=$NB_INTER[13][5]+$NB_INTER[$i][5];
	$TEMPS_INTER[13][5]=$TEMPS_INTER[13][5]+$TEMPS_INTER[$i][5];
	$NB_INTER[13][6]=$NB_INTER[13][6]+$NB_INTER[$i][6];
	$TEMPS_INTER[13][6]=$TEMPS_INTER[13][6]+$TEMPS_INTER[$i][6];
}



  echo '
   <div align="center">
    <br/>
    <table class="table_inc" cellspacing="1" cellpading="0">
      <tr align="center" class="titre">
        <td align="center" colspan="14">&nbsp;[&nbsp;<a href="./index.php?ITEM=indicateur_menu">Retour</a>&nbsp;]&nbsp;&nbsp;-&nbsp;<a href="./indicateur/indicateur_tdb_csv.php?ANNEE='.$ANNEE.'&action='.$action.'"><img src="./img/logo_excel.png" border="0"/></a>&nbsp;</td>
      </tr>
      <tr align="center" class="titre">
        <td align="center"  colspan="14"><b>&nbsp;Tableau de Bord ODTI de '.$action.' pour '.$ANNEE.'&nbsp;</b></td>
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
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
################### Nombre d'interventions IAB 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Nombre d\'interventions IAB&nbsp</b></td>';
        for($i=1;$i<=13;$i++){
        	echo '<td align="center"><b>&nbsp;'.$NB_INTER[$i][1].'&nbsp;</b></td>';	
        }
        echo ' 
      </tr>';
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Livraisons de composants&nbsp</b></td>';
        for($i=1;$i<=13;$i++){
        	echo '<td align="center">&nbsp;'.$NB_INTER[$i][2].'&nbsp;</td>';	
        }
        echo ' 
      </tr>';
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;traitements exceptionnels&nbsp</b></td>';
        for($i=1;$i<=13;$i++){
        	echo '<td align="center">&nbsp;'.$NB_INTER[$i][3].'&nbsp;</td>';	
        }
        echo ' 
      </tr>';
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Modifications IAB&nbsp</b></td>';
        for($i=1;$i<=13;$i++){
        	echo '<td align="center">&nbsp;'.$NB_INTER[$i][4].'&nbsp;</td>';	
        }
        echo ' 
      </tr>';
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Demandes MOA&nbsp</b></td>';
        for($i=1;$i<=13;$i++){
        	echo '<td align="center">&nbsp;'.$NB_INTER[$i][5].'&nbsp;</td>';	
        }
        echo ' 
      </tr>';
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Demandes non qualifi&eacute;es&nbsp</b></td>';
        for($i=1;$i<=13;$i++){
        	echo '<td align="center">&nbsp;'.$NB_INTER[$i][6].'&nbsp;</td>';	
        }
        echo ' 
      </tr>
      <tr align="center" class="titre">
        <td align="center"  colspan="14">&nbsp;</td>
      </tr>';
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
################### Dur&eacute;e des interventions IAB (minutes)
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Dur&eacute;e des interventions IAB (minutes)&nbsp</b></td>';
        for($i=1;$i<=13;$i++){
        	echo '<td align="center"><b>&nbsp;'.$TEMPS_INTER[$i][1].'&nbsp;</b></td>';	
        }
        echo ' 
      </tr>';
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Livraisons de composants&nbsp</b></td>';
        for($i=1;$i<=13;$i++){
        	echo '<td align="center">&nbsp;'.$TEMPS_INTER[$i][2].'&nbsp;</td>';	
        }
        echo ' 
      </tr>';
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;traitements exceptionnels&nbsp</b></td>';
        for($i=1;$i<=13;$i++){
        	echo '<td align="center">&nbsp;'.$TEMPS_INTER[$i][3].'&nbsp;</td>';	
        }
        echo ' 
      </tr>';
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Modifications IAB&nbsp</b></td>';
        for($i=1;$i<=13;$i++){
        	echo '<td align="center">&nbsp;'.$TEMPS_INTER[$i][4].'&nbsp;</td>';	
        }
        echo ' 
      </tr>';
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Demandes MOA&nbsp</b></td>';
        for($i=1;$i<=13;$i++){
        	echo '<td align="center">&nbsp;'.$TEMPS_INTER[$i][5].'&nbsp;</td>';	
        }
        echo ' 
      </tr>';
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Demandes non qualifi&eacute;es&nbsp</b></td>';
        for($i=1;$i<=13;$i++){
        	echo '<td align="center">&nbsp;'.$TEMPS_INTER[$i][6].'&nbsp;</td>';	
        }
        echo ' 
      </tr>
      <tr align="center" class="titre">
        <td align="center"  colspan="14">&nbsp;</td>
      </tr>';
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
################### Dur&eacute;e des interventions IAB (heures)
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Dur&eacute;e des interventions IAB (heures)&nbsp</b></td>';
        for($i=1;$i<=13;$i++){
        	$TEMPS=ceil($TEMPS_INTER[$i][1]/60); 
        	echo '<td align="center"><b>&nbsp;'.$TEMPS.'&nbsp;</b></td>';	
        }
        echo ' 
      </tr>';
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Livraisons de composants&nbsp</b></td>';
        for($i=1;$i<=13;$i++){
        	$TEMPS=ceil($TEMPS_INTER[$i][2]/60); 
        	echo '<td align="center">&nbsp;'.$TEMPS.'&nbsp;</td>';	
        }
        echo ' 
      </tr>';
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;traitements exceptionnels&nbsp</b></td>';
        for($i=1;$i<=13;$i++){
        	$TEMPS=ceil($TEMPS_INTER[$i][3]/60); 
        	echo '<td align="center">&nbsp;'.$TEMPS.'&nbsp;</td>';		
        }
        echo ' 
      </tr>';
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Modifications IAB&nbsp</b></td>';
        for($i=1;$i<=13;$i++){
        	$TEMPS=ceil($TEMPS_INTER[$i][4]/60); 
        	echo '<td align="center">&nbsp;'.$TEMPS.'&nbsp;</td>';	
        }
        echo ' 
      </tr>';
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Demandes MOA&nbsp</b></td>';
        for($i=1;$i<=13;$i++){
        	$TEMPS=ceil($TEMPS_INTER[$i][5]/60); 
        	echo '<td align="center">&nbsp;'.$TEMPS.'&nbsp;</td>';	
        }
        echo ' 
      </tr>';
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Demandes non qualifi&eacute;es&nbsp</b></td>';
        for($i=1;$i<=13;$i++){
        	$TEMPS=ceil($TEMPS_INTER[$i][6]/60); 
        	echo '<td align="center">&nbsp;'.$TEMPS.'&nbsp;</td>';		
        }
        echo ' 
      </tr>
      <tr align="center" class="titre">
        <td align="center"  colspan="14">&nbsp;</td>
      </tr>';
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
################### Dur&eacute;e des interventions IAB (jours)
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Dur&eacute;e des interventions IAB (jours)&nbsp</b></td>';
        for($i=1;$i<=13;$i++){
        	$TEMPS=ceil($TEMPS_INTER[$i][1]/60/8); 
        	echo '<td align="center"><b>&nbsp;'.$TEMPS.'&nbsp;</b></td>';	
        }
        echo ' 
      </tr>';
################### Livraisons de composants
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Livraisons de composants&nbsp</b></td>';
        for($i=1;$i<=13;$i++){
        	$TEMPS=ceil($TEMPS_INTER[$i][2]/60/8); 
        	echo '<td align="center">&nbsp;'.$TEMPS.'&nbsp;</td>';		
        }
        echo ' 
      </tr>';
################### traitements exceptionnels
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;traitements exceptionnels&nbsp</b></td>';
        for($i=1;$i<=13;$i++){
        	$TEMPS=ceil($TEMPS_INTER[$i][3]/60/8); 
        	echo '<td align="center">&nbsp;'.$TEMPS.'&nbsp;</td>';		
        }
        echo ' 
      </tr>';
################### Modifications IAB
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Modifications IAB&nbsp</b></td>';
        for($i=1;$i<=13;$i++){
        	$TEMPS=ceil($TEMPS_INTER[$i][4]/60/8); 
        	echo '<td align="center">&nbsp;'.$TEMPS.'&nbsp;</td>';	
        }
        echo ' 
      </tr>';
################### Demandes MOA
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Demandes MOA&nbsp</b></td>';
        for($i=1;$i<=13;$i++){
        	$TEMPS=ceil($TEMPS_INTER[$i][5]/60/8); 
        	echo '<td align="center">&nbsp;'.$TEMPS.'&nbsp;</td>';		
        }
        echo ' 
      </tr>';
################### Demandes non qualifi&eacute;es
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>
        <td align="right"><b>&nbsp;Demandes non qualifi&eacute;es&nbsp</b></td>';
        for($i=1;$i<=13;$i++){
        	$TEMPS=ceil($TEMPS_INTER[$i][6]/60/8); 
        	echo '<td align="center">&nbsp;'.$TEMPS.'&nbsp;</td>';	
        }
        echo ' 
      </tr>
      <tr align="center" class="titre">
        <td align="center" colspan="14">&nbsp;</td>
      </tr>
      <tr align="center" class="titre">
        <td align="center" colspan="14">&nbsp;[&nbsp;<a href="./index.php?ITEM=indicateur_menu">Retour</a>&nbsp;]&nbsp;</td>
      </tr>
      <tr align="center" class="titre">
        <td align="center" colspan="14">&nbsp;</td>
      </tr>
    </table>
  </div>
  ';
mysql_close($mysql_link);
?>