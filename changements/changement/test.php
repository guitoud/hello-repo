<?PHP
require("../cf/conf_outil_icdc.php"); 
require_once("../cf/fonctions.php");
require_once("../cf/autre_fonctions.php");


$ENV =substr($_SERVER["SCRIPT_FILENAME"], 1, 1);

$j=0;

if(isset($_GET['ID'])){
  if(is_numeric($_GET['ID'])){
    $ID=$_GET['ID'];
  }
}


$message_RESSOUCES  ='';
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


$message_RESSOUCES .='<div align="left">'."\n"; 
$message_RESSOUCES .='<table>'."\n"; 
$message_RESSOUCES .='<tr align="center">'."\n"; 
$message_RESSOUCES .='<td colspan="2"><b>&nbsp;Information de la Fiche de Ressouce du changement n&deg; '.$ID.'&nbsp;</b></td>'."\n"; 		
$message_RESSOUCES .='</tr>'."\n"; 
        $rq_info_ressources_lib="
        SELECT DISTINCT (`changement_ressources_config`.`CHANGEMENT_RESSOURCES_CONFIG_LIB`) AS `CHANGEMENT_RESSOURCES_CONFIG_LIB`
        FROM `changement_ressources_config` , `changement_ressources`
        WHERE `changement_ressources_config`.`CHANGEMENT_RESSOURCES_CONFIG_ID` = `changement_ressources`.`CHANGEMENT_RESSOURCES_CONFIG_ID`
        AND `changement_ressources`.`CHANGEMENT_LISTE_ID` = '".$ID."'
        AND `changement_ressources`.`ENABLE` = '0'
        ORDER BY `changement_ressources_config`.`CHANGEMENT_RESSOURCES_CONFIG_LIB`";

      $res_rq_info_ressources_lib = mysql_query($rq_info_ressources_lib, $mysql_link) or die(mysql_error());
      $tab_rq_info_ressources_lib = mysql_fetch_assoc($res_rq_info_ressources_lib);
      $total_ligne_rq_info_ressources_lib=mysql_num_rows($res_rq_info_ressources_lib);
      if($total_ligne_rq_info_ressources_lib!=0){
        do {
        	$CHANGEMENT_RESSOURCES_CONFIG_LIB=$tab_rq_info_ressources_lib['CHANGEMENT_RESSOURCES_CONFIG_LIB'];
$message_RESSOUCES .='<tr align="center">'."\n"; 
$message_RESSOUCES .='<td colspan="2">&nbsp;'.stripslashes(substr($CHANGEMENT_RESSOURCES_CONFIG_LIB,strpos($CHANGEMENT_RESSOURCES_CONFIG_LIB,"-")+1)).'&nbsp;</td>'."\n"; 
$message_RESSOUCES .='</tr>'."\n"; 
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
$message_RESSOUCES .='<tr>'."\n"; 
$message_RESSOUCES .='<td align="left">&nbsp;'.stripslashes($CHANGEMENT_RESSOURCES_CONFIG_CRITERE).'&nbsp;'.$info_OBLIGATOIRE.'&nbsp;</td>'."\n"; 
$message_RESSOUCES .='<td align="left">'."\n"; 
		      switch ($CHANGEMENT_RESSOURCES_CONFIG_TYPE)
            {
            case "oui-non": 
$message_RESSOUCES .='<INPUT type="radio" DISABLED name="CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'" value="Oui"';
              if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]=='Oui'){$message_RESSOUCES .=' CHECKED'."\n"; } 
$message_RESSOUCES .='>&nbsp;Oui&nbsp;/&nbsp;Non&nbsp;'."\n"; 
$message_RESSOUCES .='<INPUT type="radio" DISABLED name="CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'" value="Non"'; 
              if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]=='Non'){$message_RESSOUCES .=' CHECKED'."\n"; } 
$message_RESSOUCES .='>'."\n"; 
              
            break;
            case "risque": 
$message_RESSOUCES .='&nbsp;1&nbsp;<INPUT type="radio" DISABLED name="CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'" value="1"'."\n";
              if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]=='1'){$message_RESSOUCES .=' CHECKED'."\n";} 
$message_RESSOUCES .='>'."\n";
$message_RESSOUCES .='&nbsp;2&nbsp;<INPUT type="radio" DISABLED name="CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'" value="2"'."\n"; 
              if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]=='2'){$message_RESSOUCES .=' CHECKED'."\n";} 
$message_RESSOUCES .='>'."\n";
$message_RESSOUCES .='&nbsp;3&nbsp;<INPUT type="radio" DISABLED name="CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'" value="3"'."\n"; 
              if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]=='3'){$message_RESSOUCES .=' CHECKED'."\n";} 
$message_RESSOUCES .='>'."\n";
$message_RESSOUCES .='&nbsp;4&nbsp;<INPUT type="radio" DISABLED name="CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'" value="4"'."\n"; 
              if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]=='4'){$message_RESSOUCES .=' CHECKED'."\n";} 
$message_RESSOUCES .='>'."\n";
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
              $rq_info_config_table_info="
              SELECT  `CHANGEMENT_RESSOURCES_INFO_AUTRE_ID`
              FROM `changement_ressources`
              WHERE 
              `CHANGEMENT_LISTE_ID`='".$ID."'
              AND `CHANGEMENT_RESSOURCES_CONFIG_ID`='".$CHANGEMENT_RESSOURCES_CONFIG_ID."'
              AND `ENABLE`='0'
              LIMIT 1";
              $res_rq_info_config_table_info = mysql_query($rq_info_config_table_info, $mysql_link) or die(mysql_error());
              $tab_rq_info_ressources_table_info = mysql_fetch_assoc($res_rq_info_config_table_info);
              $total_ligne_rq_info_config_table_info=mysql_num_rows($res_rq_info_config_table_info);
              if($total_ligne_rq_info_config_table_info!=0){
                $CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]=$tab_rq_info_ressources_table_info['CHANGEMENT_RESSOURCES_INFO_AUTRE_ID'];
              }else{
                $CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]='';
              }
              mysql_free_result($res_rq_info_config_table_info);

        		if($total_ligne_rq_info_config_table!=0){
                do {
                	$ID_SQL=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE).'_ID';
                  $ID_LIB=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
                  $CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID=$tab_rq_info_ressources_table[$ID_SQL];
                  $CHANGEMENT_RESSOURCES_CONFIG_TABLE_LIB=$tab_rq_info_ressources_table[$ID_LIB];
                  if($CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID == $CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID] ){$message_RESSOUCES .=$CHANGEMENT_RESSOURCES_CONFIG_TABLE_LIB."\n";}
                	
                	} while ($tab_rq_info_ressources_table = mysql_fetch_assoc($res_rq_info_config_table));
                $ligne= mysql_num_rows($res_rq_info_config_table);
                if($ligne > 0) {
                  mysql_data_seek($res_rq_info_config_table, 0);
                  $tab_rq_info_ressources_table = mysql_fetch_assoc($res_rq_info_config_table);
                }
                }

              mysql_free_result($res_rq_info_config_table);
              
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
$message_RESSOUCES .='<INPUT TYPE="CHECKBOX" DISABLED NAME="CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'"'."\n";
                  if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]=='on'){$message_RESSOUCES .=' checked'."\n";}
$message_RESSOUCES .='>'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_LIB."\n";
                  if($CHANGEMENT_RESSOURCES_CONFIG_TABLE_COM=='oui'){
$message_RESSOUCES .='&nbsp;:&nbsp;'.stripslashes($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM])."\n";;
                  }
                  

                  	if($NB_total_ligne_rq_info_config_table < $total_ligne_rq_info_config_table){
$message_RESSOUCES .='</BR>'."\n";	
                  	}
    
                
                } while ($tab_rq_info_ressources_table = mysql_fetch_assoc($res_rq_info_config_table));
                $ligne= mysql_num_rows($res_rq_info_config_table);
                if($ligne > 0) {
                  mysql_data_seek($res_rq_info_config_table, 0);
                  $tab_rq_info_ressources_table = mysql_fetch_assoc($res_rq_info_config_table);
                }
              }
              mysql_free_result($res_rq_info_config_table);
              
            break;
            case "text": 
$message_RESSOUCES .=nl2br(stripslashes($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID]))."\n";;            
            break;
            case "varchar": 
$message_RESSOUCES .=stripslashes($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID])."\n";;
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
$message_RESSOUCES .='<table>'."\n";
$message_RESSOUCES .='<tr>'."\n";
$message_RESSOUCES .='<td align="center">Type de Ressources</td>'."\n";
$message_RESSOUCES .='<td align="center">NB</BR>Pr&eacute;sence</td>'."\n";
$message_RESSOUCES .='<td align="center">Info Pr&eacute;sence</td>'."\n";
$message_RESSOUCES .='<td align="center">NB</BR>Astreinte</td>'."\n";
$message_RESSOUCES .='<td align="center">Info Astreinte</td>'."\n";
$message_RESSOUCES .='</tr>'."\n";
		do {
		  $ID_SQL=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE).'_ID';
		  $ID_LIB=strtoupper($CHANGEMENT_RESSOURCES_CONFIG_TABLE);
		  $CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID=$tab_rq_info_ressources_table[$ID_SQL];
		  $CHANGEMENT_RESSOURCES_CONFIG_TABLE_LIB=$tab_rq_info_ressources_table[$ID_LIB];
		  $CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID;
		  $CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_COM';
		  $CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_INFO=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_INFO';
		  $CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM_INFO=$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_COM_INFO';

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
$message_RESSOUCES .='<tr>'."\n";
$message_RESSOUCES .='<td align="center">'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_LIB.'</td>'."\n";
$message_RESSOUCES .='<td align="center">'."\n";
$message_RESSOUCES .=stripslashes($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE])."\n";
$message_RESSOUCES .='</td>'."\n";
$message_RESSOUCES .='<td align="center">'."\n";
$message_RESSOUCES .=stripslashes($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_INFO])."\n";
$message_RESSOUCES .='</td>'."\n";
$message_RESSOUCES .='<td align="center">'."\n";
$message_RESSOUCES .=stripslashes($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM])."\n";
$message_RESSOUCES .='</td>'."\n";
$message_RESSOUCES .='<td align="center">'."\n";
$message_RESSOUCES .=stripslashes($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE_COM_INFO])."\n";
$message_RESSOUCES .='</td>'."\n";
$message_RESSOUCES .='</tr>'."\n";
			
		} while ($tab_rq_info_ressources_table = mysql_fetch_assoc($res_rq_info_config_table));
		$ligne= mysql_num_rows($res_rq_info_config_table);
		if($ligne > 0) {
		  mysql_data_seek($res_rq_info_config_table, 0);
		  $tab_rq_info_ressources_table = mysql_fetch_assoc($res_rq_info_config_table);
		}
		}
$message_RESSOUCES .='<tr>'."\n";
$message_RESSOUCES .='<td align="center" colspan="5">&nbsp;</td>'."\n";
$message_RESSOUCES .='</tr>'."\n";
$message_RESSOUCES .='</table>'."\n";
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

$message_RESSOUCES .='<INPUT TYPE="CHECKBOX" DISABLED NAME="CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'"'."\n";
                  if($CHANGEMENT_RESSOURCES_CONFIG[$CHANGEMENT_RESSOURCES_CONFIG_ID_ID_AUTRE]=='on'){$message_RESSOUCES .=' checked'."\n";}
$message_RESSOUCES .='>'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_LIB.'&nbsp;&nbsp;'."\n";
                  if($CHANGEMENT_RESSOURCES_CONFIG_TABLE_COM=='oui'){
$message_RESSOUCES .='<input readonly="readonly" id="CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_COM" name="CHANGEMENT_RESSOURCES_CONFIG_ID_'.$CHANGEMENT_RESSOURCES_CONFIG_ID.'_'.$CHANGEMENT_RESSOURCES_CONFIG_TABLE_ID.'_COM" type="hidden" value="vide" size="50" maxlength="100"/>'."\n";
                  }
                
                } while ($tab_rq_info_ressources_table = mysql_fetch_assoc($res_rq_info_config_table));
                $ligne= mysql_num_rows($res_rq_info_config_table);
                if($ligne > 0) {
                  mysql_data_seek($res_rq_info_config_table, 0);
                  $tab_rq_info_ressources_table = mysql_fetch_assoc($res_rq_info_config_table);
                }
              }
              mysql_free_result($res_rq_info_config_table);

              
            break;
            
            }
$message_RESSOUCES .='</td>'."\n";
$message_RESSOUCES .='</tr>'."\n";
	        	
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


$message_RESSOUCES .='</table>'."\n";
$message_RESSOUCES .='</div>'."\n";
echo $message_RESSOUCES;
mysql_close($mysql_link); 
?>