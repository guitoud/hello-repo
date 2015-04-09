<?php
//redirection si acces dirrect
if(substr_count($_SERVER['PHP_SELF'], 'index.php')==0){
  header("Location: ../"); 
  exit(); 
}
require_once("./cf/fonctions.php");
$j=0;
echo '
<center>
<table class="table_inc" cellspacing="0" cellpading="0">
  <tr align="center" class="titre">
    <td align="center" colspan="6">&nbsp;</td>
  </tr>
  <tr align="center" class="titre">
    <td align="center" colspan="6">&nbsp;Configuration des indicateurs&nbsp;</td>
  </tr>';
// configuration pour les HNO  
  echo '
  <tr align="center" class="titre">
    <td align="center" colspan="6">&nbsp;HNO&nbsp;</td>
  </tr>';
	$rq_info="
	SELECT * FROM `indicateur_config` WHERE 
	`INDICATEUR_CONFIG_TYPE`='HNO' AND
	UPPER(`INDICATEUR_CONFIG_LIB`)=UPPER('Poids par tranche de 4h')
	ORDER BY `INDICATEUR_CONFIG_DATE` DESC";
	$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
	$tab_rq_info = mysql_fetch_assoc($res_rq_info);
	$total_ligne_rq_info=mysql_num_rows($res_rq_info);
	if ($total_ligne_rq_info==0){
		$j++;
		if ($j%2) { $class = "pair";}else{$class = "impair";} 
		echo '
		<tr align="center" class='.$class.'>
		  <td align="center" colspan="6">&nbsp;Pas d\'information&nbsp;</td>
		</tr>';
        }else{
        do {
		$INDICATEUR_CONFIG_LIB=$tab_rq_info['INDICATEUR_CONFIG_LIB'];
		$INDICATEUR_CONFIG_COEF=$tab_rq_info['INDICATEUR_CONFIG_COEF'];
		$INDICATEUR_CONFIG_DATE=$tab_rq_info['INDICATEUR_CONFIG_DATE'];
		if($INDICATEUR_CONFIG_DATE==0){$INDICATEUR_CONFIG_DATE='d&eacute;but';}
		$j++;
		if ($j%2) { $class = "pair";}else{$class = "impair";} 
		echo '
		<tr align="center" class='.$class.'>
		  <td align="left" colspan="6">&nbsp;'.$INDICATEUR_CONFIG_LIB.' avec '.$INDICATEUR_CONFIG_COEF.' depuis le '.$INDICATEUR_CONFIG_DATE.'&nbsp;</td>
		</tr>';
	} while ($tab_rq_info = mysql_fetch_assoc($res_rq_info));
          $ligne= mysql_num_rows($res_rq_info);
          if($ligne > 0) {
            mysql_data_seek($res_rq_info, 0);
            $tab_rq_info = mysql_fetch_assoc($res_rq_info);
          }
	}
	mysql_free_result($res_rq_info);
// configuration pour la répartition des applis
  echo '
  <tr align="center" class="titre">
    <td align="center" colspan="6">&nbsp;R&eacute;partition des applications&nbsp;</td>
  </tr>
  <tr align="center" class="titre">
    <td align="center">&nbsp;information&nbsp;</td>
    <td align="center">&nbsp;coef&nbsp;</td>
    <td align="center">&nbsp;nb d\'appli&nbsp;</td>
    <td align="center">&nbsp;r&eacute;partition&nbsp;</td>
    <td align="center">&nbsp;points&nbsp;</td>
    <td align="center">&nbsp;date de début&nbsp;</td>
  </tr>';
  $rq_info="
	SELECT * FROM `indicateur_config` WHERE 
	`INDICATEUR_CONFIG_TYPE`='PERIMETRE'
	ORDER BY `INDICATEUR_CONFIG_DATE` ASC, `INDICATEUR_CONFIG_COEF`";
	$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
	$tab_rq_info = mysql_fetch_assoc($res_rq_info);
	$total_ligne_rq_info=mysql_num_rows($res_rq_info);
	$info_date=0;
	$SUM_NB_APP=0;
	$SUM_REPART=0;
	$SUM_POINT=0;
	if ($total_ligne_rq_info==0){
		
		$j++;
		if ($j%2) { $class = "pair";}else{$class = "impair";} 
		echo '
		<tr align="center" class='.$class.'>
		  <td align="center" colspan="6">&nbsp;Pas d\'information&nbsp;</td>
		</tr>';
        }else{
        do {
        	if($info_date!=$tab_rq_info['INDICATEUR_CONFIG_DATE']){
        		if($info_date==0){$info_date='d&eacute;but';}
        		$j++;
			if ($j%2) { $class = "pair";}else{$class = "impair";} 
			echo '
			<tr align="center" class='.$class.'>
			  <td align="center">&nbsp;<b>Total</b>&nbsp;</td>
			  <td align="center">&nbsp;&nbsp;</td>
			  <td align="center">&nbsp;<b>'.$SUM_NB_APP.'</b>&nbsp;</td>';
			  if($SUM_REPART==100){
			  	echo '<td align="center">&nbsp;<b>'.$SUM_REPART.' %</b>&nbsp;</td>';
			  }else{
			  	echo '<td align="center">&nbsp;<font color=red><b>'.$SUM_REPART.' %</b></font>&nbsp;</td>';
			  }
			  echo '
			  <td align="center">&nbsp;<b>'.$SUM_POINT.'</b>&nbsp;</td>
			  <td align="center">&nbsp;<b>'.$info_date.'</b>&nbsp;</td>
			</tr>';
			$SUM_NB_APP=0;
			$SUM_REPART=0;
			$SUM_POINT=0;
			$info_date=$tab_rq_info['INDICATEUR_CONFIG_DATE'];
        	}else{
        		$info_date=$tab_rq_info['INDICATEUR_CONFIG_DATE'];
        	}
		$INDICATEUR_CONFIG_LIB=$tab_rq_info['INDICATEUR_CONFIG_LIB'];
		$INDICATEUR_CONFIG_COEF=$tab_rq_info['INDICATEUR_CONFIG_COEF'];
		$INDICATEUR_CONFIG_DATE=$tab_rq_info['INDICATEUR_CONFIG_DATE'];
		$INDICATEUR_CONFIG_NB_APPLI=$tab_rq_info['INDICATEUR_CONFIG_NB_APPLI'];
		$INDICATEUR_CONFIG_REPART=$tab_rq_info['INDICATEUR_CONFIG_REPART'];
		if($INDICATEUR_CONFIG_DATE==0){$INDICATEUR_CONFIG_DATE='d&eacute;but';}
		$INDICATEUR_POINT=$INDICATEUR_CONFIG_COEF*$INDICATEUR_CONFIG_NB_APPLI;
		$SUM_NB_APP=$SUM_NB_APP+$INDICATEUR_CONFIG_NB_APPLI;
		$SUM_REPART=$SUM_REPART+$INDICATEUR_CONFIG_REPART;
		$SUM_POINT=$SUM_POINT+$INDICATEUR_POINT;
		
		$j++;
		if ($j%2) { $class = "pair";}else{$class = "impair";} 
		echo '
		<tr align="center" class='.$class.'>
		  <td align="center">&nbsp;'.$INDICATEUR_CONFIG_LIB.'&nbsp;</td>
		  <td align="center">&nbsp;'.$INDICATEUR_CONFIG_COEF.'&nbsp;</td>
		  <td align="center">&nbsp;'.$INDICATEUR_CONFIG_NB_APPLI.'&nbsp;</td>
		  <td align="center">&nbsp;'.$INDICATEUR_CONFIG_REPART.' %&nbsp;</td>
		  <td align="center">&nbsp;'.$INDICATEUR_POINT.'&nbsp;</td>
		  <td align="center">&nbsp;'.$INDICATEUR_CONFIG_DATE.'&nbsp;</td>
		</tr>';
	} while ($tab_rq_info = mysql_fetch_assoc($res_rq_info));
          $ligne= mysql_num_rows($res_rq_info);
          if($ligne > 0) {
            mysql_data_seek($res_rq_info, 0);
            $tab_rq_info = mysql_fetch_assoc($res_rq_info);
          }
          if($info_date==0){$info_date='d&eacute;but';}
          $j++;
	if ($j%2) { $class = "pair";}else{$class = "impair";} 
	echo '
	<tr align="center" class='.$class.'>
	  <td align="center">&nbsp;<b>Total</b>&nbsp;</td>
	  <td align="center">&nbsp;&nbsp;</td>
	  <td align="center">&nbsp;<b>'.$SUM_NB_APP.'</b>&nbsp;</td>';
	  if($SUM_REPART==100){
	  	echo '<td align="center">&nbsp;<b>'.$SUM_REPART.' %</b>&nbsp;</td>';
	  }else{
	  	echo '<td align="center">&nbsp;<font color=red><b>'.$SUM_REPART.' %</b></font>&nbsp;</td>';
	  }
	  echo '
	  <td align="center">&nbsp;<b>'.$SUM_POINT.'</b>&nbsp;</td>
	  <td align="center">&nbsp;<b>'.$info_date.'</b>&nbsp;</td>
	</tr>';
	}
	mysql_free_result($res_rq_info);
	echo '
	<tr align="center" class="titre">
	  <td align="center" colspan="6">&nbsp;<b>utilisation pour le traitement</b>&nbsp;</td>
	</tr>';
// configuration pour les durée 
	echo '
	<tr align="center" class="titre">
	  <td align="center" colspan="6">&nbsp;dur&eacute;e&nbsp;</td>
	</tr>';
	$rq_info="
	SELECT * 
	FROM `indicateur_duree` 
	ORDER BY `INDICATEUR_DUREE_TEMPS_MAX` ASC ";
	$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
	$tab_rq_info = mysql_fetch_assoc($res_rq_info);
	$total_ligne_rq_info=mysql_num_rows($res_rq_info);
	if ($total_ligne_rq_info==0){
		$j++;
		if ($j%2) { $class = "pair";}else{$class = "impair";} 
		echo '
		<tr align="center" class='.$class.'>
		  <td align="center" colspan="6">&nbsp;Pas d\'information&nbsp;</td>
		</tr>';
        }else{
        do {
        	$INDICATEUR_DUREE_LIB=$tab_rq_info['INDICATEUR_DUREE_LIB'];
        	$INDICATEUR_DUREE_POIDS=$tab_rq_info['INDICATEUR_DUREE_POIDS'];
        	$INDICATEUR_DUREE_TEMPS_MINI=$tab_rq_info['INDICATEUR_DUREE_TEMPS_MINI'];
        	$INDICATEUR_DUREE_TEMPS_MAX=$tab_rq_info['INDICATEUR_DUREE_TEMPS_MAX']; 
		$j++;
		if ($j%2) { $class = "pair";}else{$class = "impair";} 
		echo '
		<tr align="center" class='.$class.'>
		  <td align="left" colspan="6">&nbsp;'.$INDICATEUR_DUREE_LIB.' avec un poids de '.$INDICATEUR_DUREE_POIDS.' entre '.$INDICATEUR_DUREE_TEMPS_MINI.' et '.$INDICATEUR_DUREE_TEMPS_MAX.' minutes&nbsp;</td>
		</tr>';
	} while ($tab_rq_info = mysql_fetch_assoc($res_rq_info));
          $ligne= mysql_num_rows($res_rq_info);
          if($ligne > 0) {
            mysql_data_seek($res_rq_info, 0);
            $tab_rq_info = mysql_fetch_assoc($res_rq_info);
          }
	}
	mysql_free_result($res_rq_info);
// configuration pour les actions
	echo '
	<tr align="center" class="titre">
	  <td align="center" colspan="6">&nbsp;Recherche de l\'action (dans les mots cl&eacute;s)&nbsp;</td>
	</tr>';
	$rq_info="
	SELECT * 
	FROM `indicateur_action` 
	WHERE `INDICATEUR_ACTION_TYPE`= 'MOT_CLE'
	ORDER BY `INDICATEUR_ACTION_ORDRE` ASC ";
	$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
	$tab_rq_info = mysql_fetch_assoc($res_rq_info);
	$total_ligne_rq_info=mysql_num_rows($res_rq_info);
	if ($total_ligne_rq_info==0){
		$j++;
		if ($j%2) { $class = "pair";}else{$class = "impair";} 
		echo '
		<tr align="center" class='.$class.'>
		  <td align="center" colspan="6">&nbsp;Pas d\'information&nbsp;</td>
		</tr>';
        }else{
        do {
        	$INDICATEUR_ACTION_LIB=$tab_rq_info['INDICATEUR_ACTION_LIB'];
        	$INDICATEUR_ACTION_INFO=$tab_rq_info['INDICATEUR_ACTION_INFO'];
        	$INDICATEUR_ACTION_TYPE=$tab_rq_info['INDICATEUR_ACTION_TYPE'];
        	$INDICATEUR_ACTION_ORDRE=$tab_rq_info['INDICATEUR_ACTION_ORDRE']; 
		$j++;
		if ($j%2) { $class = "pair";}else{$class = "impair";} 
		echo '
		<tr align="center" class='.$class.'>
		  <td align="left" colspan="6">&nbsp;'.$INDICATEUR_ACTION_LIB.' donne <b>'.$INDICATEUR_ACTION_INFO.'</b> en '.$INDICATEUR_ACTION_ORDRE.'&nbsp;</td>
		</tr>';
	} while ($tab_rq_info = mysql_fetch_assoc($res_rq_info));
          $ligne= mysql_num_rows($res_rq_info);
          if($ligne > 0) {
            mysql_data_seek($res_rq_info, 0);
            $tab_rq_info = mysql_fetch_assoc($res_rq_info);
          }
	}
	mysql_free_result($res_rq_info);
// configuration pour les actions
	echo '
	<tr align="center" class="titre">
	  <td align="center" colspan="6">&nbsp;Recherche de l\'action (dans les r&eacute;sum&eacute;s)&nbsp;</td>
	</tr>';
	$rq_info="
	SELECT * 
	FROM `indicateur_action` 
	WHERE `INDICATEUR_ACTION_TYPE`= 'RESUME'
	ORDER BY `INDICATEUR_ACTION_ORDRE` ASC ";
	$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
	$tab_rq_info = mysql_fetch_assoc($res_rq_info);
	$total_ligne_rq_info=mysql_num_rows($res_rq_info);
	if ($total_ligne_rq_info==0){
		$j++;
		if ($j%2) { $class = "pair";}else{$class = "impair";} 
		echo '
		<tr align="center" class='.$class.'>
		  <td align="center" colspan="6">&nbsp;Pas d\'information&nbsp;</td>
		</tr>';
        }else{
        do {
        	$INDICATEUR_ACTION_LIB=$tab_rq_info['INDICATEUR_ACTION_LIB'];
        	$INDICATEUR_ACTION_INFO=$tab_rq_info['INDICATEUR_ACTION_INFO'];
        	$INDICATEUR_ACTION_TYPE=$tab_rq_info['INDICATEUR_ACTION_TYPE'];
        	$INDICATEUR_ACTION_ORDRE=$tab_rq_info['INDICATEUR_ACTION_ORDRE']; 
		$j++;
		if ($j%2) { $class = "pair";}else{$class = "impair";} 
		echo '
		<tr align="center" class='.$class.'>
		  <td align="left" colspan="6">&nbsp;'.$INDICATEUR_ACTION_LIB.' donne <b>'.$INDICATEUR_ACTION_INFO.'</b> en '.$INDICATEUR_ACTION_ORDRE.'&nbsp;</td>
		</tr>';
	} while ($tab_rq_info = mysql_fetch_assoc($res_rq_info));
          $ligne= mysql_num_rows($res_rq_info);
          if($ligne > 0) {
            mysql_data_seek($res_rq_info, 0);
            $tab_rq_info = mysql_fetch_assoc($res_rq_info);
          }
	}
	mysql_free_result($res_rq_info);
// configuration pour les natures
	echo '
	<tr align="center" class="titre">
	  <td align="center" colspan="6">&nbsp;Recherche de la nature&nbsp;</td>
	</tr>';
	$rq_info="
	SELECT * 
	FROM `indicateur_nature` 
	ORDER BY `INDICATEUR_NATURE_LIB` ASC ";
	$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
	$tab_rq_info = mysql_fetch_assoc($res_rq_info);
	$total_ligne_rq_info=mysql_num_rows($res_rq_info);
	if ($total_ligne_rq_info==0){
		$j++;
		if ($j%2) { $class = "pair";}else{$class = "impair";} 
		echo '
		<tr align="center" class='.$class.'>
		  <td align="center" colspan="6">&nbsp;Pas d\'information&nbsp;</td>
		</tr>';
        }else{
        do {
        	$INDICATEUR_NATURE_LIB=$tab_rq_info['INDICATEUR_NATURE_LIB'];
        	$INDICATEUR_NATURE_INFO=$tab_rq_info['INDICATEUR_NATURE_INFO'];
		$j++;
		if ($j%2) { $class = "pair";}else{$class = "impair";} 
		echo '
		<tr align="center" class='.$class.'>
		  <td align="left" colspan="6">&nbsp;'.$INDICATEUR_NATURE_LIB.' donne <b>'.$INDICATEUR_NATURE_INFO.'</b>&nbsp;</td>
		</tr>';
	} while ($tab_rq_info = mysql_fetch_assoc($res_rq_info));
          $ligne= mysql_num_rows($res_rq_info);
          if($ligne > 0) {
            mysql_data_seek($res_rq_info, 0);
            $tab_rq_info = mysql_fetch_assoc($res_rq_info);
          }
	}
	mysql_free_result($res_rq_info);
// configuration pour les status
	echo '
	<tr align="center" class="titre">
	  <td align="center" colspan="6">&nbsp;Recherche du status&nbsp;</td>
	</tr>';
	$rq_info="
	SELECT * 
	FROM `indicateur_status` 
	ORDER BY `INDICATEUR_STATUS_LIB` ASC ";
	$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
	$tab_rq_info = mysql_fetch_assoc($res_rq_info);
	$total_ligne_rq_info=mysql_num_rows($res_rq_info);
	if ($total_ligne_rq_info==0){
		$j++;
		if ($j%2) { $class = "pair";}else{$class = "impair";} 
		echo '
		<tr align="center" class='.$class.'>
		  <td align="center" colspan="6">&nbsp;Pas d\'information&nbsp;</td>
		</tr>';
        }else{
        do {
        	$INDICATEUR_STATUS_LIB=$tab_rq_info['INDICATEUR_STATUS_LIB'];
        	$INDICATEUR_STATUS_INFO=$tab_rq_info['INDICATEUR_STATUS_INFO'];
		$j++;
		if ($j%2) { $class = "pair";}else{$class = "impair";} 
		echo '
		<tr align="center" class='.$class.'>
		  <td align="left" colspan="6">&nbsp;'.$INDICATEUR_STATUS_LIB.' donne <b>'.$INDICATEUR_STATUS_INFO.'</b>&nbsp;</td>
		</tr>';
	} while ($tab_rq_info = mysql_fetch_assoc($res_rq_info));
          $ligne= mysql_num_rows($res_rq_info);
          if($ligne > 0) {
            mysql_data_seek($res_rq_info, 0);
            $tab_rq_info = mysql_fetch_assoc($res_rq_info);
          }
	}
	mysql_free_result($res_rq_info);
// configuration pour les versions
	echo '
	<tr align="center" class="titre">
	  <td align="center" colspan="6">&nbsp;Date des versionning&nbsp;[&nbsp;<a href="./index.php?ITEM=indicateur_Gestion_date_version">Ajout</a>&nbsp;]&nbsp; </td>
	</tr>';   
	$rq_info="
	SELECT * 
	FROM `indicateur_version_date` 
	ORDER BY `DATE` DESC ";
	$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
	$tab_rq_info = mysql_fetch_assoc($res_rq_info);
	$total_ligne_rq_info=mysql_num_rows($res_rq_info);
	if ($total_ligne_rq_info==0){
		$j++;
		if ($j%2) { $class = "pair";}else{$class = "impair";} 
		echo '
		<tr align="center" class='.$class.'>
		  <td align="center" colspan="6">&nbsp;Pas d\'information&nbsp;</td>
		</tr>';
        }else{
        do {
        	$DATE=$tab_rq_info['DATE'];
        	$TYPE=$tab_rq_info['TYPE'];
        	$jour=substr($DATE,6,2);
	        $mois=substr($DATE,4,2);
	        $annee=substr($DATE,0,4);
		$j++;
		if ($j%2) { $class = "pair";}else{$class = "impair";} 
		echo '
		<tr align="center" class='.$class.'>
		  <td align="left" colspan="6">&nbsp;'.$jour.'/'.$mois.'/'.$annee.' donne <b>'.$TYPE.'</b>&nbsp;</td>
		</tr>';
	} while ($tab_rq_info = mysql_fetch_assoc($res_rq_info));
          $ligne= mysql_num_rows($res_rq_info);
          if($ligne > 0) {
            mysql_data_seek($res_rq_info, 0);
            $tab_rq_info = mysql_fetch_assoc($res_rq_info);
          }
	}
	mysql_free_result($res_rq_info);
  echo '
  <tr align="center" class="titre">
    <td align="center" colspan="6">&nbsp;</td>
  </tr>
  <tr align="center" class="titre">
    <td align="center" colspan="6">&nbsp;[&nbsp;<a href="./index.php?ITEM=indicateur_menu_creation">Retour</a>&nbsp;]&nbsp;</td>
  </tr>
  <tr align="center" class="titre">
    <td align="center" colspan="6">&nbsp;</td>
  </tr>
</table>     
</center>
';
mysql_close($mysql_link);
?>
