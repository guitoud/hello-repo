<?PHP
//redirection si acces dirrect
if(substr_count($_SERVER['PHP_SELF'], 'index.php')==0){
  header("Location: ../");
  exit();
}
/*******************************************************************
   Interface Ajout mail
   Version 1.0.0  
  24/03/2010 - VGU - Creation fichier
**************************************************Guibert Vincent***
*******************************************************************/

require("./cf/conf_outil_icdc.php"); 
require_once("./cf/fonctions.php");
$j=0;
$ID='';
$page_test=0;
$STOP=0;
$STOP_INFO_CHANGEMENT_Mail=0;
$CHANGEMENT_Mail='';
$aff_modif=0;

if(isset($_GET['action'])){
  $action=$_GET['action'];
}else{
  $action='Ajout';
}
if(isset($_GET['mail'])){
  $CHANGEMENT_Mail=$_GET['mail'];
}else{
  $CHANGEMENT_Mail='';
}

$tab_var=$_POST;

if(isset($_POST['action'])){
  $action=$_POST['action'];
}

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
if(isset($_POST['ID'])){
  $ID=$_POST['ID'];
}

if(empty($tab_var['btn'])){
}else{


  # Cas RAZ
  if($tab_var['btn']=="RAZ"){
    $CHANGEMENT_Mail='';
    $action='Ajout';
    $_GET['ITEM']='changement_Ajout_Mail';
  }
  
  # Cas Ajouter
  if($tab_var['btn']=="Ajouter"){
    $CHANGEMENT_Mail=addslashes(trim(htmlentities($tab_var['txt_CHANGEMENT_Mail'])));  
    if($CHANGEMENT_Mail==''){
      $STOP=1;
    }
    if(substr_count($CHANGEMENT_Mail, '@')!=1){
      $STOP=1;
    }
    if($STOP==0){
    $rq_info_demande="
    SELECT `CHANGEMENT_DEMANDE_ID`, `CHANGEMENT_DEMANDE_LIB` FROM `changement_demande` WHERE `ENABLE`='0'";
    $res_rq_info_demande = mysql_query($rq_info_demande, $mysql_link) or die(mysql_error());
    $tab_rq_info_demande = mysql_fetch_assoc($res_rq_info_demande);
    $total_ligne_rq_info_demande=mysql_num_rows($res_rq_info_demande);
    $NB_ORDER_SQL=0;
    if($total_ligne_rq_info_demande!=0){
      do {
      
      $CHANGEMENT_DEMANDE_ID=$tab_rq_info_demande['CHANGEMENT_DEMANDE_ID'];
      $CHANGEMENT_DEMANDE_LIB=$tab_rq_info_demande['CHANGEMENT_DEMANDE_LIB'];
      $VAR_LIB='CHANGEMENT_DEMANDE_'.$CHANGEMENT_DEMANDE_ID;
      if(isset($tab_var[$VAR_LIB])){
        $CHANGEMENT_DEMANDE_CONFIG[$CHANGEMENT_DEMANDE_ID]='on';
      }

        $rq_info_status="
        SELECT `CHANGEMENT_STATUS_ID`, `CHANGEMENT_STATUS` FROM `changement_status` WHERE `ENABLE`='0'";
        $res_rq_info_status = mysql_query($rq_info_status, $mysql_link) or die(mysql_error());
        $tab_rq_info_status = mysql_fetch_assoc($res_rq_info_status);
        $total_ligne_rq_info_status=mysql_num_rows($res_rq_info_status);
        if($total_ligne_rq_info_status!=0){
          do {
          
          $CHANGEMENT_STATUS_ID=$tab_rq_info_status['CHANGEMENT_STATUS_ID'];
          $CHANGEMENT_STATUS=$tab_rq_info_status['CHANGEMENT_STATUS'];
          $VAR_LIB='CHANGEMENT_STATUS_'.$CHANGEMENT_DEMANDE_ID.'_'.$CHANGEMENT_STATUS_ID;
          if(isset($tab_var[$VAR_LIB])){
            $CHANGEMENT_STATUS_CONFIG[$CHANGEMENT_DEMANDE_ID][$CHANGEMENT_STATUS_ID]='on';
            
            $rq_Mail_info="
            SELECT `CHANGEMENT_Mail_ID` 
            FROM `changement_mail`
            WHERE `CHANGEMENT_MAIL_LIB`='".$CHANGEMENT_Mail."'
            AND `CHANGEMENT_STATUS_ID`='".$CHANGEMENT_STATUS_ID."'
            AND `CHANGEMENT_DEMANDE_ID`='".$CHANGEMENT_DEMANDE_ID."'
            AND `ENABLE`=0";
            $res_rq_Mail_info = mysql_query($rq_Mail_info, $mysql_link) or die(mysql_error());
            $tab_rq_Mail_info = mysql_fetch_assoc($res_rq_Mail_info);
            $total_ligne_rq_Mail_info=mysql_num_rows($res_rq_Mail_info);
            if($total_ligne_rq_Mail_info==0){

              //ajoute l Mail si non presente dans bdd
              $NB_ORDER_SQL=$NB_ORDER_SQL+1;
              $sql="
              INSERT INTO `changement_mail`( `CHANGEMENT_Mail_ID` , `CHANGEMENT_MAIL_LIB`, `CHANGEMENT_STATUS_ID`, `CHANGEMENT_DEMANDE_ID` ,`ENABLE`)
              VALUES ( NULL , '".$CHANGEMENT_Mail."', '".$CHANGEMENT_STATUS_ID."', '".$CHANGEMENT_DEMANDE_ID."','0' );";
              mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
              
              $TABLE_SQL_SQL='changement_Mail';       
              historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');

            }
            mysql_free_result($res_rq_Mail_info);
            
            
          }

           } while ($tab_rq_info_status = mysql_fetch_assoc($res_rq_info_status));
                $ligne= mysql_num_rows($res_rq_info_status);
                if($ligne > 0) {
                  mysql_data_seek($res_rq_info_status, 0);
                  $tab_rq_info_status = mysql_fetch_assoc($res_rq_info_status);
                }
        }
        mysql_free_result($res_rq_info_status);
      
       } while ($tab_rq_info_demande = mysql_fetch_assoc($res_rq_info_demande));
            $ligne= mysql_num_rows($res_rq_info_demande);
            if($ligne > 0) {
              mysql_data_seek($res_rq_info_demande, 0);
              $tab_rq_info_demande = mysql_fetch_assoc($res_rq_info_demande);
            }
    }
    mysql_free_result($res_rq_info_demande);
    if($NB_ORDER_SQL!=0){     
      $sql="OPTIMIZE TABLE `changement_mail`";
      mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());

      $TABLE_SQL_SQL='changement_Mail';       
      historique_sql_new($sql,$TABLE_SQL_SQL,'OPTIMIZE');        
      echo '
      <script language="JavaScript">
      url=("./index.php?ITEM=changement_Gestion_Mail");
      window.location=url;
      </script>
      ';
      }else{
        $page_test=4;
      }
    }else{
      if($page_test!=3){
        $page_test=2;
      }
    }
  }
  
  # Cas Supprimer
  if($tab_var['btn']=="Supprimer"){
    $rq_Mail_info="
      SELECT  `CHANGEMENT_MAIL_LIB`
      FROM `changement_mail`
      WHERE `CHANGEMENT_Mail_ID`='".$ID."'";
      $res_rq_Mail_info = mysql_query($rq_Mail_info, $mysql_link) or die(mysql_error());
      $tab_rq_Mail_info = mysql_fetch_assoc($res_rq_Mail_info);
      $total_ligne_rq_Mail_info=mysql_num_rows($res_rq_Mail_info);
      $CHANGEMENT_Mail=$tab_rq_Mail_info['CHANGEMENT_MAIL_LIB'];
      mysql_free_result($res_rq_Mail_info);
        //supprime la Mail si pas d utilisation de celle-ci       
        $sql="UPDATE `changement_mail` SET `ENABLE` = '1' WHERE `CHANGEMENT_MAIL_LIB` ='".$CHANGEMENT_Mail."';";
        mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
        
        $TABLE_SQL_SQL='changement_Mail';       
        historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');
       
        $sql="OPTIMIZE TABLE `changement_mail`";
        mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
        
        $TABLE_SQL_SQL='changement_Mail';       
        historique_sql_new($sql,$TABLE_SQL_SQL,'OPTIMIZE');
        
        echo '
        <script language="JavaScript">
        url=("./index.php?ITEM=changement_Gestion_Mail");
        window.location=url;
        </script>
        ';

      
  }
  # Cas Modifier
  if($tab_var['btn']=="Modifier"){
    $CHANGEMENT_Mail=addslashes(trim(htmlentities($tab_var['txt_CHANGEMENT_Mail'])));

    if($STOP==0){
    
    $rq_info_demande="
    SELECT `CHANGEMENT_DEMANDE_ID`, `CHANGEMENT_DEMANDE_LIB` FROM `changement_demande` WHERE `ENABLE`='0'";
    $res_rq_info_demande = mysql_query($rq_info_demande, $mysql_link) or die(mysql_error());
    $tab_rq_info_demande = mysql_fetch_assoc($res_rq_info_demande);
    $total_ligne_rq_info_demande=mysql_num_rows($res_rq_info_demande);
    $NB_ORDER_SQL=0;
    $NB_CONF_SQL=0;
    if($total_ligne_rq_info_demande!=0){
      do {
      
      $CHANGEMENT_DEMANDE_ID=$tab_rq_info_demande['CHANGEMENT_DEMANDE_ID'];
      $CHANGEMENT_DEMANDE_LIB=$tab_rq_info_demande['CHANGEMENT_DEMANDE_LIB'];
      $VAR_LIB='CHANGEMENT_DEMANDE_'.$CHANGEMENT_DEMANDE_ID;
      if(isset($tab_var[$VAR_LIB])){
        $CHANGEMENT_DEMANDE_CONFIG[$CHANGEMENT_DEMANDE_ID]='on';
      

        $rq_info_status="
        SELECT `CHANGEMENT_STATUS_ID`, `CHANGEMENT_STATUS` FROM `changement_status` WHERE `ENABLE`='0'";
        $res_rq_info_status = mysql_query($rq_info_status, $mysql_link) or die(mysql_error());
        $tab_rq_info_status = mysql_fetch_assoc($res_rq_info_status);
        $total_ligne_rq_info_status=mysql_num_rows($res_rq_info_status);
        if($total_ligne_rq_info_status!=0){
          do {
          
          $CHANGEMENT_STATUS_ID=$tab_rq_info_status['CHANGEMENT_STATUS_ID'];
          $CHANGEMENT_STATUS=$tab_rq_info_status['CHANGEMENT_STATUS'];
          $VAR_LIB='CHANGEMENT_STATUS_'.$CHANGEMENT_DEMANDE_ID.'_'.$CHANGEMENT_STATUS_ID;
          if(isset($tab_var[$VAR_LIB])){
            $CHANGEMENT_STATUS_CONFIG[$CHANGEMENT_DEMANDE_ID][$CHANGEMENT_STATUS_ID]='on';
            
            $rq_Mail_info="
            SELECT `CHANGEMENT_Mail_ID` 
            FROM `changement_mail`
            WHERE `CHANGEMENT_MAIL_LIB`='".$CHANGEMENT_Mail."'
            AND `CHANGEMENT_STATUS_ID`='".$CHANGEMENT_STATUS_ID."'
            AND `CHANGEMENT_DEMANDE_ID`='".$CHANGEMENT_DEMANDE_ID."'
            AND `ENABLE`=0";
            $res_rq_Mail_info = mysql_query($rq_Mail_info, $mysql_link) or die(mysql_error());
            $tab_rq_Mail_info = mysql_fetch_assoc($res_rq_Mail_info);
            $total_ligne_rq_Mail_info=mysql_num_rows($res_rq_Mail_info);
            if($total_ligne_rq_Mail_info==0){

              //ajoute l Mail si non presente dans bdd
              $NB_ORDER_SQL=$NB_ORDER_SQL+1;
              $sql="
              INSERT INTO `changement_mail`( `CHANGEMENT_Mail_ID` , `CHANGEMENT_MAIL_LIB`, `CHANGEMENT_STATUS_ID`, `CHANGEMENT_DEMANDE_ID` ,`ENABLE`)
              VALUES ( NULL , '".$CHANGEMENT_Mail."', '".$CHANGEMENT_STATUS_ID."', '".$CHANGEMENT_DEMANDE_ID."','0' );";
              mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
              //echo  $sql.'</BR>';
              $TABLE_SQL_SQL='changement_Mail';       
              historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');

            }else{
              $NB_CONF_SQL=$NB_CONF_SQL+1;
            }
            mysql_free_result($res_rq_Mail_info);
          }else{
            $rq_Mail_info="
            SELECT `CHANGEMENT_Mail_ID` 
            FROM `changement_mail`
            WHERE `CHANGEMENT_MAIL_LIB`='".$CHANGEMENT_Mail."'
            AND `CHANGEMENT_DEMANDE_ID`='".$CHANGEMENT_DEMANDE_ID."'
            AND `CHANGEMENT_STATUS_ID`='".$CHANGEMENT_STATUS_ID."'
            AND `ENABLE`=0";
            $res_rq_Mail_info = mysql_query($rq_Mail_info, $mysql_link) or die(mysql_error());
            $tab_rq_Mail_info = mysql_fetch_assoc($res_rq_Mail_info);
            $total_ligne_rq_Mail_info=mysql_num_rows($res_rq_Mail_info);
            if($total_ligne_rq_Mail_info!=0){
              $sql="UPDATE `changement_mail` SET `ENABLE` = '1' 
              WHERE `CHANGEMENT_MAIL_LIB` ='".$CHANGEMENT_Mail."' 
              AND `CHANGEMENT_DEMANDE_ID`='".$CHANGEMENT_DEMANDE_ID."'
              AND `CHANGEMENT_STATUS_ID`='".$CHANGEMENT_STATUS_ID."';";
              mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
              //echo  $sql.'</BR>';
              $TABLE_SQL_SQL='changement_Mail';       
              historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');

              $NB_ORDER_SQL=$NB_ORDER_SQL+1;
            }
            mysql_free_result($res_rq_Mail_info);
          }

           } while ($tab_rq_info_status = mysql_fetch_assoc($res_rq_info_status));
                $ligne= mysql_num_rows($res_rq_info_status);
                if($ligne > 0) {
                  mysql_data_seek($res_rq_info_status, 0);
                  $tab_rq_info_status = mysql_fetch_assoc($res_rq_info_status);
                }
        }
        mysql_free_result($res_rq_info_status);
        }else{
        $rq_Mail_info="
        SELECT `CHANGEMENT_Mail_ID` 
        FROM `changement_mail`
        WHERE `CHANGEMENT_MAIL_LIB`='".$CHANGEMENT_Mail."'
        AND `CHANGEMENT_DEMANDE_ID`='".$CHANGEMENT_DEMANDE_ID."'
        AND `ENABLE`=0";
        $res_rq_Mail_info = mysql_query($rq_Mail_info, $mysql_link) or die(mysql_error());
        $tab_rq_Mail_info = mysql_fetch_assoc($res_rq_Mail_info);
        $total_ligne_rq_Mail_info=mysql_num_rows($res_rq_Mail_info);
        if($total_ligne_rq_Mail_info!=0){
          $sql="UPDATE `changement_mail` SET `ENABLE` = '1' 
          WHERE `CHANGEMENT_MAIL_LIB` ='".$CHANGEMENT_Mail."' 
          AND `CHANGEMENT_DEMANDE_ID`='".$CHANGEMENT_DEMANDE_ID."';";
          //echo  $sql.'</BR>';
          mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
          
          $TABLE_SQL_SQL='changement_Mail';       
          historique_sql_new($sql,$TABLE_SQL_SQL,'UPDATE');

          $NB_ORDER_SQL=$NB_ORDER_SQL+1;
        }
        mysql_free_result($res_rq_Mail_info);
      }
        
        
      
       } while ($tab_rq_info_demande = mysql_fetch_assoc($res_rq_info_demande));
            $ligne= mysql_num_rows($res_rq_info_demande);
            if($ligne > 0) {
              mysql_data_seek($res_rq_info_demande, 0);
              $tab_rq_info_demande = mysql_fetch_assoc($res_rq_info_demande);
            }
    }
    mysql_free_result($res_rq_info_demande);
    if($NB_CONF_SQL==0){
      if($NB_ORDER_SQL!=0){
        $sql="OPTIMIZE TABLE `changement_mail`";
        //echo  $sql.'</BR>';
        mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());

        $TABLE_SQL_SQL='changement_Mail';       
        historique_sql_new($sql,$TABLE_SQL_SQL,'OPTIMIZE');      
        echo '
        <script language="JavaScript">
        url=("./index.php?ITEM=changement_Gestion_Mail");
        window.location=url;
        </script>
        ';
        }else{
          $page_test=4;
        }
      }else{
        echo '
        <script language="JavaScript">
        url=("./index.php?ITEM=changement_Gestion_Mail");
        window.location=url;
        </script>
        ';
      }
    }else{
      if($page_test!=3){
        $page_test=2;
      }
    }
  }
}
if($action=="Modif"){

  if(isset($_GET['ID'])){
    if(is_numeric($_GET['ID'])){
      $ID=$_GET['ID'];
      $rq_Mail_info="
      SELECT  `CHANGEMENT_MAIL_LIB`
      FROM `changement_mail`
      WHERE `CHANGEMENT_Mail_ID`='".$ID."'";
      $res_rq_Mail_info = mysql_query($rq_Mail_info, $mysql_link) or die(mysql_error());
      $tab_rq_Mail_info = mysql_fetch_assoc($res_rq_Mail_info);
      $total_ligne_rq_Mail_info=mysql_num_rows($res_rq_Mail_info);
      $CHANGEMENT_Mail=$tab_rq_Mail_info['CHANGEMENT_MAIL_LIB'];
      mysql_free_result($res_rq_Mail_info);
      
      $rq_Mail_info="
      SELECT  `CHANGEMENT_STATUS_ID`, `CHANGEMENT_DEMANDE_ID` 
      FROM `changement_mail` 
      WHERE `ENABLE`='0' AND `CHANGEMENT_MAIL_LIB`='".$CHANGEMENT_Mail."'";
      //echo $rq_Mail_info;
      $res_rq_Mail_info = mysql_query($rq_Mail_info, $mysql_link) or die(mysql_error());
      $tab_rq_Mail_info = mysql_fetch_assoc($res_rq_Mail_info);
      $total_ligne_rq_Mail_info=mysql_num_rows($res_rq_Mail_info);
      if($total_ligne_rq_Mail_info!=0){
        do {
        
        $CHANGEMENT_STATUS_ID=$tab_rq_Mail_info['CHANGEMENT_STATUS_ID'];
        $CHANGEMENT_DEMANDE_ID=$tab_rq_Mail_info['CHANGEMENT_DEMANDE_ID'];
        $CHANGEMENT_DEMANDE_CONFIG[$CHANGEMENT_DEMANDE_ID]='on';
        $CHANGEMENT_STATUS_CONFIG[$CHANGEMENT_DEMANDE_ID][$CHANGEMENT_STATUS_ID]='on';

         } while ($tab_rq_Mail_info = mysql_fetch_assoc($res_rq_Mail_info));
              $ligne= mysql_num_rows($res_rq_Mail_info);
              if($ligne > 0) {
                mysql_data_seek($res_rq_Mail_info, 0);
                $tab_rq_Mail_info = mysql_fetch_assoc($res_rq_Mail_info);
              }
      }
      mysql_free_result($res_rq_Mail_info);
    }
  }
}
echo '
<!--Début page HTML -->  
<div align="center">

<form method="post" name="frm_Mail" id="frm_Mail" action="index.php?ITEM='.$_GET['ITEM'].'">
<table class="table_inc">
	<tr align="center" class="titre">';
    if($action=="Ajout"){
      echo '
				<td colspan="2"><h2>&nbsp;[&nbsp;Ajout d\'un Mail&nbsp;]&nbsp;</h2></td>';
    }
    if($action=="Modif"){
      echo '
				<td colspan="2">
				<h2>&nbsp;[&nbsp;Modification d\'un Mail&nbsp;]&nbsp;</h2>
				';
				if($aff_modif==1){
          echo '&nbsp;Pas d\'action possible car on utilise le Mail&nbsp;';
				}
				echo '
				</td>';
    }
    if($action=="AjoutNew"){
      echo '
				<td colspan="2"><h2>&nbsp;[&nbsp;Ajout d\'un Mail&nbsp;]&nbsp;</h2></td>';
    }
    
echo'
	</tr>
	';
  $j++;
  if ($j%2) { $class = "pair";}else{$class = "impair";} 
	echo '
	<tr class='.$class.'>
    <td align="left">&nbsp;Mail&nbsp;</td>
    <td align="left"><input name="txt_CHANGEMENT_Mail" ';
    if($action=='Modif'){echo 'readonly ';}
    echo 'type="text" value="'.stripslashes($CHANGEMENT_Mail).'" size="50"/></td>
	</tr>
	';
	$rq_info_demande="
	SELECT `CHANGEMENT_DEMANDE_ID`, `CHANGEMENT_DEMANDE_LIB` FROM `changement_demande` WHERE `ENABLE`='0'";
	$res_rq_info_demande = mysql_query($rq_info_demande, $mysql_link) or die(mysql_error());
	$tab_rq_info_demande = mysql_fetch_assoc($res_rq_info_demande);
	$total_ligne_rq_info_demande=mysql_num_rows($res_rq_info_demande);
	$AFF_SPAN='';
	if($total_ligne_rq_info_demande!=0){
		do {
		
		$CHANGEMENT_DEMANDE_ID=$tab_rq_info_demande['CHANGEMENT_DEMANDE_ID'];
		$CHANGEMENT_DEMANDE_LIB=$tab_rq_info_demande['CHANGEMENT_DEMANDE_LIB'];
		$j++;
    if ($j%2) { $class = "pair";}else{$class = "impair";} 
    echo '
    <tr class='.$class.'>
      <td align="left">&nbsp;</td>
      <td align="left">
      <INPUT TYPE="CHECKBOX" NAME="CHANGEMENT_DEMANDE_'.$CHANGEMENT_DEMANDE_ID.'"';
                  if(isset($CHANGEMENT_DEMANDE_CONFIG[$CHANGEMENT_DEMANDE_ID])){
                    if($CHANGEMENT_DEMANDE_CONFIG[$CHANGEMENT_DEMANDE_ID]=='on'){echo ' checked';}
                  }
                  echo '>'.$CHANGEMENT_DEMANDE_LIB.'</BR>';
                  	$rq_info_status="
                    SELECT `CHANGEMENT_STATUS_ID`, `CHANGEMENT_STATUS` FROM `changement_status` WHERE `ENABLE`='0'";
                    $res_rq_info_status = mysql_query($rq_info_status, $mysql_link) or die(mysql_error());
                    $tab_rq_info_status = mysql_fetch_assoc($res_rq_info_status);
                    $total_ligne_rq_info_status=mysql_num_rows($res_rq_info_status);
                    if($total_ligne_rq_info_status!=0){
                      do {
                      
                      $CHANGEMENT_STATUS_ID=$tab_rq_info_status['CHANGEMENT_STATUS_ID'];
                      $CHANGEMENT_STATUS=$tab_rq_info_status['CHANGEMENT_STATUS'];

                      echo '
                        &nbsp;&nbsp;&nbsp;&nbsp;<INPUT TYPE="CHECKBOX" NAME="CHANGEMENT_STATUS_'.$CHANGEMENT_DEMANDE_ID.'_'.$CHANGEMENT_STATUS_ID.'"';
                        if(isset($CHANGEMENT_STATUS_CONFIG[$CHANGEMENT_DEMANDE_ID][$CHANGEMENT_STATUS_ID])){
                          if($CHANGEMENT_STATUS_CONFIG[$CHANGEMENT_DEMANDE_ID][$CHANGEMENT_STATUS_ID]=='on'){echo ' checked';}
                        }
                                    echo '>'.$CHANGEMENT_STATUS.'</BR>
                      ';
                      
                       } while ($tab_rq_info_status = mysql_fetch_assoc($res_rq_info_status));
                            $ligne= mysql_num_rows($res_rq_info_status);
                            if($ligne > 0) {
                              mysql_data_seek($res_rq_info_status, 0);
                              $tab_rq_info_status = mysql_fetch_assoc($res_rq_info_status);
                            }
                    }
                    mysql_free_result($res_rq_info_status);
      echo '
      </td>
    </tr>
    ';
		
		 } while ($tab_rq_info_demande = mysql_fetch_assoc($res_rq_info_demande));
	        $ligne= mysql_num_rows($res_rq_info_demande);
	        if($ligne > 0) {
	          mysql_data_seek($res_rq_info_demande, 0);
	          $tab_rq_info_demande = mysql_fetch_assoc($res_rq_info_demande);
	        }
	}
	mysql_free_result($res_rq_info_demande);
	
	if($STOP_INFO_CHANGEMENT_Mail==1){
    echo '
    <tr class="titre">
      <td colspan="2" align="center"><h2>&nbsp;Le Mail '.$CHANGEMENT_Mail.' existe d&eacute;j&agrave;, merci de choisir une autre Mail.&nbsp;</h2></td>
    </tr>';
	}
	if($STOP_INFO_CHANGEMENT_Mail==2){
    echo '
    <tr class="titre">
      <td colspan="2" align="center"><h2>&nbsp;Impossible de faire la suppression du Mail '.$CHANGEMENT_Mail.' car on l\'utilise pour une sous-Mail.&nbsp;</h2></td>
    </tr>';
	}
	if($STOP_INFO_CHANGEMENT_Mail==3){
    echo '
    <tr class="titre">
      <td colspan="2" align="center"><h2>&nbsp;Impossible de faire la modification du Mail '.$CHANGEMENT_Mail.' car on l\'utilise pour une sous-Mail.&nbsp;</h2></td>
    </tr>';
	}
	$STOP_INFO_CHANGEMENT_Mail=0;
	if($page_test==2){
    echo '
    <tr class="titre">
      <td colspan="2" align="center"><h2>&nbsp;Merci de saisir l\'ensemble des Champs.&nbsp;</h2></td>
    </tr>';
	}
	if($page_test==4){
    echo '
    <tr class="titre">
      <td colspan="2" align="center"><h2>&nbsp;Merci de saisir un type de mail en reception.&nbsp;</h2></td>
    </tr>';
	}
echo '
	<tr class="titre">
    <td colspan="2" align="center">
    <h2>';
    if($action=='Ajout'){
    echo '
    <input name="btn" type="submit" id="btn" value="Ajouter">
    <input name="btn" type="submit" id="btn" value="RAZ">
    ';}
    if($action=='Modif'){
      if($aff_modif==0){
        echo '
        <input name="btn" type="submit" id="btn" value="Modifier">
        <input name="btn" type="submit" id="btn" value="Supprimer">
        ';
				}
    echo '
    <input name="btn" type="submit" id="btn" value="RAZ">
    ';} 
    echo '
    <input type="hidden" name="ID" value="'.$ID.'">
    <input type="hidden" name="action" id="action" value="'.$action.'">
    </h2>
    </td>
	</tr>
	<tr class="titre">
    <td colspan="2" align="center"><h2>&nbsp;[&nbsp;<a href="./index.php?ITEM=changement_Gestion_Mail">Retour - Liste des Mails</a>&nbsp;]&nbsp;</h2></td>
	</tr>
	</table>
</form>
</div>';

mysql_close($mysql_link); 
?>