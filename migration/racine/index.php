<?php
/* Configure le délai d'expiration à 10080 minutes */
session_cache_expire (10080); 
$cache_expire = session_cache_expire();
//date_default_timezone_set('Europe/Paris');
/* Démarre la session */
session_start();
/*********************************************************
  Interface pour le Portail
  Vincent Guibert
*********************************************************/

require("./cf/conf_outil_icdc.php"); 
require_once("./cf/fonctions.php");
$ENV =substr($_SERVER["SCRIPT_FILENAME"], 1, 1);
$j=0;

if(!isset($_GET['ITEM'])){
  $rq_info="
  SELECT *
  FROM `moteur_pages`
  WHERE `ITEM` = '' AND 
  `ENABLE`=0
  LIMIT 1";
  $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
  $tab_rq_info = mysql_fetch_assoc($res_rq_info);
  $total_ligne_rq_info=mysql_num_rows($res_rq_info);
  $URLP=$tab_rq_info['URLP'];
  $LEGEND=$tab_rq_info['LEGEND'];
  $_GET['ITEM'] ="";
  mysql_free_result($res_rq_info);
}else{
  if($_GET['ITEM']=='unlogin'){
  	if(isset($_SESSION['LOGIN'])){
  		$rq_info_guest="
		SELECT `UTILISATEUR_ID`
		FROM `moteur_utilisateur` 
		WHERE 
		UPPER(`LOGIN`)=UPPER('".$_SESSION['LOGIN']."')
		AND `ENABLE`='Y'
		";
		$res_rq_info_guest = mysql_query($rq_info_guest, $mysql_link) or die(mysql_error());
		$tab_rq_info_guest = mysql_fetch_assoc($res_rq_info_guest);
		$total_ligne_rq_info_guest=mysql_num_rows($res_rq_info_guest);
		mysql_free_result($res_rq_info_guest);
		$TRACE_CATEGORIE='Login';
		$TRACE_TABLE='moteur_utilisateur';
		$TRACE_REF_ID=$tab_rq_info_guest['UTILISATEUR_ID'];
		$TRACE_ACTION='UnLogin';
		$TRACE_ETAT='BDD';
		moteur_trace($TRACE_CATEGORIE,$TRACE_TABLE,$TRACE_REF_ID,$TRACE_ACTION,$TRACE_ETAT);
		unset($_SESSION['VALID']);
		unset($_SESSION['LOGIN']);
		unset($_SESSION['NOM']);
		unset($_SESSION['PRENOM']);
		unset($_SESSION['id']);
		unset($_SESSION['TYPE_LOGIN']);
		if(isset($_SESSION["QUERY_STRING"])){
			unset($_SESSION['QUERY_STRING']);	
		}
  	}
    $MDP='';
    $action='Login';
    $_SESSION['VALID']='KO';
  }
}

if(!isset($_SESSION['VALID'])) {
  $_SESSION['VALID']='KO';
  if($_GET['ITEM'] !="login"){
    $_SESSION['QUERY_STRING']=$_SERVER['QUERY_STRING'];
    $_SESSION['VALID']='KO';
    $_GET['ITEM'] ="login";
  }
}else{
  if($_GET['ITEM'] !="login"){
    if($_SESSION['VALID']!='OK'){
      $_SESSION['QUERY_STRING']=$_SERVER['QUERY_STRING'];
      $_GET['ITEM'] ="login";
    }
  }else{
    if($_SESSION['VALID']!='OK'){
      $_GET['ITEM'] ="login";
    }
  }
}
// pour acces guest ...
/*
if(!isset($_SESSION['VALID'])) {
   $_SESSION['QUERY_STRING']=$_SERVER['QUERY_STRING'];
   $_SESSION['VALID']='OK';
   $_GET['ITEM'] ='';
   $_SESSION['LOGIN']='guest';
   $_SESSION['NOM']='guest';
   $_SESSION['PRENOM']='guest';
   
    $rq_info_guest="
    SELECT `UTILISATEUR_ID`
    FROM `moteur_utilisateur` 
    WHERE 
    UPPER(`LOGIN`)=UPPER('guest')
    AND `ENABLE`='Y'
    ";
    $res_rq_info_guest = mysql_query($rq_info_guest, $mysql_link) or die(mysql_error());
    $tab_rq_info_guest = mysql_fetch_assoc($res_rq_info_guest);
    $total_ligne_rq_info_guest=mysql_num_rows($res_rq_info_guest);
    mysql_free_result($res_rq_info_guest);
    $TRACE_CATEGORIE='Login';
    $TRACE_TABLE='moteur_utilisateur';
    $TRACE_REF_ID=$tab_rq_info_guest['UTILISATEUR_ID'];
    $TRACE_ACTION='Login';
    $TRACE_ETAT='BDD';
    moteur_trace($TRACE_CATEGORIE,$TRACE_TABLE,$TRACE_REF_ID,$TRACE_ACTION,$TRACE_ETAT);
   echo '
	<script language="JavaScript">
	url=("./index.php?'.$_SESSION['QUERY_STRING'].'");
	window.location=url;
	</script>
	';
}else{
  if($_GET['ITEM'] =="unlogin"){
    $_GET['ITEM'] ="login";
    $_SESSION['VALID']='KO';
    $_SESSION['LOGIN']='';
  }else{
    if($_GET['ITEM'] !="login"){
      if($_SESSION['VALID']!='OK'){
      	$_SESSION['QUERY_STRING']=$_SERVER['QUERY_STRING'];
        //$_GET['ITEM'] ="login";
        $_SESSION['VALID']='OK';
        $_GET['ITEM'] ='';
        $_SESSION['LOGIN']='guest';
        $_SESSION['NOM']='guest';
        $_SESSION['PRENOM']='guest';
        $rq_info_guest="
        SELECT `UTILISATEUR_ID`
        FROM `moteur_utilisateur` 
        WHERE 
        UPPER(`LOGIN`)=UPPER('guest')
        AND `ENABLE`='Y'
        ";
        $res_rq_info_guest = mysql_query($rq_info_guest, $mysql_link) or die(mysql_error());
        $tab_rq_info_guest = mysql_fetch_assoc($res_rq_info_guest);
        $total_ligne_rq_info_guest=mysql_num_rows($res_rq_info_guest);
        mysql_free_result($res_rq_info_guest);
        $TRACE_CATEGORIE='Login';
        $TRACE_TABLE='moteur_utilisateur';
        $TRACE_REF_ID=$tab_rq_info_guest['UTILISATEUR_ID'];
        $TRACE_ACTION='Login';
        $TRACE_ETAT='BDD';
        moteur_trace($TRACE_CATEGORIE,$TRACE_TABLE,$TRACE_REF_ID,$TRACE_ACTION,$TRACE_ETAT);
        echo '
	<script language="JavaScript">
        url=("./index.php?'.$_SESSION['QUERY_STRING'].'");
        window.location=url;
        </script>
        ';
      }
    }else{
      if($_SESSION['VALID']!='OK'){
      	if($_GET['ITEM'] =="login"){
      		$_GET['ITEM'] ="login";
      	}else{
	        //$_GET['ITEM'] ="login";
	        $_SESSION['VALID']='OK';
	        $_GET['ITEM'] ='';
	        $_SESSION['LOGIN']='guest';
	        $_SESSION['NOM']='guest';
	        $_SESSION['PRENOM']='guest';
          $rq_info_guest="
          SELECT `UTILISATEUR_ID`
          FROM `moteur_utilisateur` 
          WHERE 
          UPPER(`LOGIN`)=UPPER('guest')
          AND `ENABLE`='Y'
          ";
          $res_rq_info_guest = mysql_query($rq_info_guest, $mysql_link) or die(mysql_error());
          $tab_rq_info_guest = mysql_fetch_assoc($res_rq_info_guest);
          $total_ligne_rq_info_guest=mysql_num_rows($res_rq_info_guest);
          mysql_free_result($res_rq_info_guest);
          $TRACE_CATEGORIE='Login';
          $TRACE_TABLE='moteur_utilisateur';
          $TRACE_REF_ID=$tab_rq_info_guest['UTILISATEUR_ID'];
          $TRACE_ACTION='Login';
          $TRACE_ETAT='BDD';
          moteur_trace($TRACE_CATEGORIE,$TRACE_TABLE,$TRACE_REF_ID,$TRACE_ACTION,$TRACE_ETAT);
	}
      }
    }
  }
}
// fin pour acces guest ...
*/
$ITEM=$_GET['ITEM'];

if($_SESSION['VALID']=='OK'){
	$LOGIN=$_SESSION['LOGIN'];
	if($LOGIN==''){
		$rq_info="
		SELECT  `moteur_role`.`ROLE`,`moteur_droit`.`DROIT`, `moteur_pages`.`ITEM`, `moteur_pages`.`URLP`, `moteur_pages`.`LEGEND`, `moteur_pages`.`LEGEND_MENU` 
		FROM `moteur_droit`, `moteur_pages`,`moteur_role`
		WHERE `moteur_pages`.`PAGES_ID`=`moteur_droit`.`PAGES_ID` AND
		`moteur_role`.`ROLE_ID`=`moteur_droit`.`ROLE_ID` AND
		`moteur_pages`.`ITEM`='".$ITEM."' AND
		`moteur_role`.`ROLE`='GUEST' AND
		`moteur_droit`.`DROIT`='OK' AND 
		`moteur_pages`.`ENABLE`='0'
		";
	}else{
		$rq_info="
		SELECT  `moteur_role`.`ROLE`,`moteur_droit`.`DROIT`, `moteur_pages`.`ITEM`, `moteur_pages`.`URLP`, `moteur_pages`.`LEGEND`, `moteur_pages`.`LEGEND_MENU` 
		FROM `moteur_droit`, `moteur_pages`,`moteur_role`
		WHERE `moteur_pages`.`PAGES_ID`=`moteur_droit`.`PAGES_ID` AND
		`moteur_role`.`ROLE_ID`=`moteur_droit`.`ROLE_ID` AND
		`moteur_pages`.`ITEM`='".$ITEM."' AND
		`moteur_droit`.`ROLE_ID` IN(
		SELECT `moteur_role_utilisateur`.`ROLE_ID` 
		FROM `moteur_role_utilisateur`,`moteur_utilisateur` 
		WHERE `moteur_utilisateur`.`UTILISATEUR_ID`=`moteur_role_utilisateur`.`UTILISATEUR_ID` AND
		 `moteur_utilisateur`.`LOGIN`='".$LOGIN."' AND 
		 `moteur_role_utilisateur`.`ROLE_UTILISATEUR_ACCES`='0'
		 ) AND
		`moteur_droit`.`DROIT`='OK' AND 
		`moteur_pages`.`ENABLE`='0'
		";
	}

}else{
	if($_GET['ITEM']=="login"){
		$rq_info="
		SELECT  `moteur_role`.`ROLE`,`moteur_droit`.`DROIT`, `moteur_pages`.`ITEM`, `moteur_pages`.`URLP`, `moteur_pages`.`LEGEND`, `moteur_pages`.`LEGEND_MENU` 
		FROM `moteur_droit`, `moteur_pages`,`moteur_role`
		WHERE `moteur_pages`.`PAGES_ID`=`moteur_droit`.`PAGES_ID` AND
		`moteur_role`.`ROLE_ID`=`moteur_droit`.`ROLE_ID` AND
		`moteur_pages`.`ITEM`='".$ITEM."' AND
		`moteur_role`.`ROLE`='GUEST' AND
		`moteur_droit`.`DROIT`='OK' AND 
		`moteur_pages`.`ENABLE`='0'
		";
	}else{
		$rq_info="
		SELECT  `moteur_role`.`ROLE`,`moteur_droit`.`DROIT`, `moteur_pages`.`ITEM`, `moteur_pages`.`URLP`, `moteur_pages`.`LEGEND`, `moteur_pages`.`LEGEND_MENU` 
		FROM `moteur_droit`, `moteur_pages`,`moteur_role`
		WHERE `moteur_pages`.`PAGES_ID`=`moteur_droit`.`PAGES_ID` AND
		`moteur_role`.`ROLE_ID`=`moteur_droit`.`ROLE_ID` AND
		`moteur_pages`.`ITEM`='' AND
		`moteur_role`.`ROLE`='GUEST' AND
		`moteur_droit`.`DROIT`='OK' AND 
		`moteur_pages`.`ENABLE`='0'
		";
	}
}

$res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
$tab_rq_info = mysql_fetch_assoc($res_rq_info);
$total_ligne_rq_info=mysql_num_rows($res_rq_info);
if($total_ligne_rq_info==0){
  mysql_free_result($res_rq_info);
  if($ITEM!='' && $_SESSION['VALID']=='OK'){
  	  $rq_info="
	  SELECT *
	  FROM `moteur_pages`
	  WHERE `ITEM` = 'error' AND 
	  `ENABLE`=0
	  LIMIT 1";
  }else{
	  $rq_info="
	  SELECT *
	  FROM `moteur_pages`
	  WHERE `ITEM` = '' AND 
	  `ENABLE`=0
	  LIMIT 1";
	  $_GET['ITEM'] ="";
  }	
  
  $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
  $tab_rq_info = mysql_fetch_assoc($res_rq_info);
  $total_ligne_rq_info=mysql_num_rows($res_rq_info);
  $URLP=$tab_rq_info['URLP'];
  $LEGEND=$tab_rq_info['LEGEND'];
  $LEGEND_MENU=$tab_rq_info['LEGEND']; 
  
  mysql_free_result($res_rq_info);
}else{
  $URLP=$tab_rq_info['URLP'];
  $LEGEND=$tab_rq_info['LEGEND'];
  mysql_free_result($res_rq_info);
}
mysql_close(); 

echo '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>.: ';
if ( $ENV != "x" )
{
	if(substr_count($_SERVER["SCRIPT_FILENAME"], '/php/iab/')==1){
		echo 'DEV - ';
	}else{
		if(substr_count($_SERVER["SCRIPT_FILENAME"], '/php/iab_va/')==1){
			echo 'VA - ';
		}else{
			echo 'DEV - ';
		}
	}
}
echo $Var_NOM_SITE.' :.</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="./css/design_perso.css" rel="stylesheet" type="text/css">';
?>

<!-- Script pour l'affiche du Menu Super Beau ! -->
<SCRIPT language="javascript" type="text/javascript">

//The following line is critical for menu operation, and MUST APPEAR ONLY ONCE. If you have more than one menu_array.js file rem out this line in subsequent files
menunum=0;menus=new Array();_d=document;function addmenu(){menunum++;menus[menunum]=menu;}function dumpmenus(){mt="<scr"+"ipt language=javascript>";for(a=1;a<menus.length;a++){mt+=" menu"+a+"=menus["+a+"];"}mt+="<\/scr"+"ipt>";_d.write(mt)}
//Please leave the above line intact. The above also needs to be enabled if it not already enabled unless this file is part of a multi pack.

////////////////////////////////////
// Editable properties START here //
////////////////////////////////////

// Special effect string for IE5.5 or above please visit http://www.milonic.co.uk/menu/filters_sample.php for more filters
effect1 = "Fade(duration=0.2);Alpha(style=0,opacity=88);Shadow(color='#777777', Direction=135, Strength=5)"
effect2 = "Fade(duration=0.1);Alpha(style=0,opacity=94);Shadow(color='#CCCCCC', Direction=130, Strength=8)"

timegap=100			// The time delay for menus to remain visible
followspeed=5		// Follow Scrolling speed
followrate=40		// Follow Scrolling Rate
suboffset_top=2;	// Sub menu offset Top position 
suboffset_left=2;	// Sub menu offset Left position
closeOnClick = true

style1=[			// style1 is an array of properties. You can have as many property arrays as you need. This means that menus can have their own style.
"666666",				// Mouse Off Font Color
"FFFFFF",			// Mouse Off Background Color
"993333",			// Mouse On Font Color
"FFFFFF",			// Mouse On Background Color
"FFFFFF",			// Menu Border Color 
12,					// Font Size in pixels
"normal",			// Font Style (italic or normal)
"bold",				// Font Weight (bold or normal)
"Verdana, Arial",	// Font Name
4,					// Menu ITEM Padding
"",		// Sub Menu Image (Leave this blank if not needed)
2,					// 3D Border & Separator bar
"D7D7D7",			// 3D High Color
"993333",			// 3D Low Color
"FF9900",			// Current Page ITEM Font Color (leave this blank to disable)
"",				// Current Page ITEM Background Color (leave this blank to disable)
"./img/fl_menu_b.gif",		// Top Bar image (Leave this blank to disable)
"",			// Menu Header Font Color (Leave blank if headers are not needed)
"",			// Menu Header Background Color (Leave blank if headers are not needed)
"",				// Menu ITEM Separator Color
]


addmenu(menu=[		// This is the array that contains your menu properties and details
"mainmenu",			// Menu Name - This is needed in order for the menu to be called
114,					// Menu Top - The Top position of the menu in pixels
,				// Menu Left - The Left position of the menu in pixels
130,					// Menu Width - Menus width in pixels
0,					// Menu Border Width 
"center",					// Screen Position - here you can use "center;left;right;middle;top;bottom" or a combination of "center:middle"
style1,				// Properties Array - this is set higher up, as above
1,					// Always Visible - allows the menu ITEM to be visible at all time (1=on/0=off)
"center",				// Alignment - sets the menu elements text alignment, values valid here are: left, right or center
,					// Filter - Text variable for setting transitional effects on menu activation - see above for more info
0,					// Follow Scrolling - Tells the menu ITEM to follow the user down the screen (visible at all times) (1=on/0=off)
1, 					// Horizontal Menu - Tells the menu to become horizontal instead of top to bottom style (1=on/0=off)
,					// Keep Alive - Keeps the menu visible until the user moves over another menu or clicks elsewhere on the page (1=on/0=off)
,					// Position of TOP sub image left:center:right
,					// Set the Overall Width of Horizontal Menu to 100% and height to the specified amount (Leave blank to disable)
,					// Right To Left - Used in Hebrew for example. (1=on/0=off)
,					// Open the Menus OnClick - leave blank for OnMouseover (1=on/0=off)
,					// ID of the div you want to hide on MouseOver (useful for hiding form elements)
,					// Background image for menu when BGColor set to transparent.
,					// Scrollable Menu
,					// Reserved for future use
<?PHP
if($_SESSION['VALID']=='OK'){
if($_SESSION['TYPE_LOGIN']=='LDAP'){
	$rq_info_menu="
	SELECT `moteur_menu`.`MENU_ID`, `moteur_menu`.`NOM_MENU`
	FROM `moteur_menu`,`moteur_sous_menu` 
	WHERE `moteur_sous_menu`.`MENU_ID`=`moteur_menu`.`MENU_ID` AND 
	`PAGES_ID` IN (
	SELECT `moteur_pages`.`PAGES_ID`
	FROM `moteur_droit`,`moteur_pages`
	WHERE 
	`moteur_pages`.`PAGES_ID`=`moteur_droit`.`PAGES_ID` AND
	`moteur_droit`.`ROLE_ID` IN(
	SELECT `moteur_role_utilisateur`.`ROLE_ID` 
	FROM `moteur_role_utilisateur`,`moteur_utilisateur` 
	WHERE `moteur_utilisateur`.`UTILISATEUR_ID`=`moteur_role_utilisateur`.`UTILISATEUR_ID` AND
	 `moteur_utilisateur`.`LOGIN`='".$LOGIN."' AND 
	 `moteur_role_utilisateur`.`ROLE_UTILISATEUR_ACCES`='0'
	 ) AND
	`moteur_droit`.`DROIT`='OK' AND
	`moteur_pages`.`ENABLE`='0'
	ORDER BY `moteur_pages`.`PAGES_ID`
	) 
	AND `PAGES_ID` NOT IN(
  SELECT `PAGES_ID` FROM `moteur_pages` WHERE `ITEM` IN ('Admin_Action_MDP','vide_admin01'))
	GROUP BY `moteur_menu`.`MENU_ID` ORDER BY `moteur_menu`.`ORDRE`
	 ";	
}else{
	$rq_info_menu="
	SELECT `moteur_menu`.`MENU_ID`, `moteur_menu`.`NOM_MENU`
	FROM `moteur_menu`,`moteur_sous_menu` 
	WHERE `moteur_sous_menu`.`MENU_ID`=`moteur_menu`.`MENU_ID` AND 
	`PAGES_ID` IN (
	SELECT `moteur_pages`.`PAGES_ID`
	FROM `moteur_droit`,`moteur_pages`
	WHERE 
	`moteur_pages`.`PAGES_ID`=`moteur_droit`.`PAGES_ID` AND
	`moteur_droit`.`ROLE_ID` IN(
	SELECT `moteur_role_utilisateur`.`ROLE_ID` 
	FROM `moteur_role_utilisateur`,`moteur_utilisateur` 
	WHERE `moteur_utilisateur`.`UTILISATEUR_ID`=`moteur_role_utilisateur`.`UTILISATEUR_ID` AND
	 `moteur_utilisateur`.`LOGIN`='".$LOGIN."' AND 
	 `moteur_role_utilisateur`.`ROLE_UTILISATEUR_ACCES`='0'
	 ) AND
	`moteur_droit`.`DROIT`='OK' AND
	`moteur_pages`.`ENABLE`='0'
	ORDER BY `moteur_pages`.`PAGES_ID`
	) 
	GROUP BY `moteur_menu`.`MENU_ID` ORDER BY `moteur_menu`.`ORDRE`
	 ";
}

$res_rq_info_menu = mysql_query($rq_info_menu, $mysql_link) or die(mysql_error());
$tab_rq_info_menu = mysql_fetch_assoc($res_rq_info_menu);
$total_ligne_rq_info_menu=mysql_num_rows($res_rq_info_menu); 
if($total_ligne_rq_info_menu!=0){
do {
  $MENU_ID=$tab_rq_info_menu['MENU_ID'];
  $NOM_MENU=$tab_rq_info_menu['NOM_MENU'];
  //$NOM_SOUS_MENU=$tab_rq_info_menu['NOM_SOUS_MENU'];
  echo ',"'.$NOM_MENU.'","show-menu='.$MENU_ID.'",,"survol",0 ';
} while ($tab_rq_info_menu = mysql_fetch_assoc($res_rq_info_menu));
$ligne= mysql_num_rows($res_rq_info_menu);
if($ligne > 0) {
  mysql_data_seek($res_rq_info_menu, 0);
  $tab_rq_info_menu = mysql_fetch_assoc($res_rq_info_menu);
}
}

echo '
])
';

$rq_info_menu="
SELECT `moteur_menu`.`MENU_ID`, `moteur_menu`.`NOM_MENU` 
FROM `moteur_menu`,`moteur_sous_menu` 
WHERE `moteur_sous_menu`.`MENU_ID`=`moteur_menu`.`MENU_ID` AND 
`PAGES_ID` IN (
SELECT `moteur_pages`.`PAGES_ID`
FROM `moteur_droit`,`moteur_pages`
WHERE
`moteur_pages`.`PAGES_ID`=`moteur_droit`.`PAGES_ID` AND
`moteur_droit`.`ROLE_ID` IN(
SELECT `moteur_role_utilisateur`.`ROLE_ID` 
FROM `moteur_role_utilisateur`,`moteur_utilisateur` 
WHERE `moteur_utilisateur`.`UTILISATEUR_ID`=`moteur_role_utilisateur`.`UTILISATEUR_ID` AND
 `moteur_utilisateur`.`LOGIN`='".$LOGIN."' AND 
 `moteur_role_utilisateur`.`ROLE_UTILISATEUR_ACCES`='0'
 ) AND
`moteur_droit`.`DROIT`='OK' AND
`moteur_pages`.`ENABLE`=0
ORDER BY `moteur_pages`.`PAGES_ID`
) 
GROUP BY `moteur_menu`.`MENU_ID` ORDER BY `moteur_menu`.`ORDRE`
 ";
$res_rq_info_menu = mysql_query($rq_info_menu, $mysql_link) or die(mysql_error());
$tab_rq_info_menu = mysql_fetch_assoc($res_rq_info_menu);
$total_ligne_rq_info_menu=mysql_num_rows($res_rq_info_menu); 
if($total_ligne_rq_info_menu!=0){
do {
  $MENU_ID=$tab_rq_info_menu['MENU_ID'];
  $NOM_MENU=$tab_rq_info_menu['NOM_MENU'];
  echo '
	addmenu(menu=["'.$MENU_ID.'",,,210,1,"",style1,0,"left",effect2,0,,,,,,,,,,,';
	if($_SESSION['TYPE_LOGIN']=='LDAP'){
		$rq_info_sous_menu="
		SELECT 
		`moteur_pages`.`PAGES_ID`, `moteur_pages`.`ITEM` , `moteur_pages`.`LEGEND`  , `moteur_pages`.`LEGEND_MENU`
		FROM `moteur_pages`,`moteur_sous_menu`
		WHERE 
		`moteur_sous_menu`.`PAGES_ID`=`moteur_pages`.`PAGES_ID` AND
		`moteur_sous_menu`.`MENU_ID`='".$MENU_ID."' AND
		`moteur_pages`.`ITEM` NOT LIKE 'Admin_Action_MDP' AND
		`moteur_sous_menu`.`PAGES_ID` IN (
		SELECT `moteur_pages`.`PAGES_ID`
		FROM `moteur_droit` ,`moteur_pages`
		WHERE 
		`moteur_pages`.`PAGES_ID`=`moteur_droit`.`PAGES_ID` AND
		`moteur_droit`.`ROLE_ID` IN(
		SELECT `moteur_role_utilisateur`.`ROLE_ID` 
		FROM `moteur_role_utilisateur`,`moteur_utilisateur` 
		WHERE `moteur_utilisateur`.`UTILISATEUR_ID`=`moteur_role_utilisateur`.`UTILISATEUR_ID` AND
		`moteur_utilisateur`.`LOGIN`='".$LOGIN."' AND 
		`moteur_role_utilisateur`.`ROLE_UTILISATEUR_ACCES`='0'
		) AND
		`moteur_droit`.`DROIT`='OK' AND
		`moteur_pages`.`ENABLE`=0
		ORDER BY `moteur_pages`.`PAGES_ID`
		) AND
		`moteur_pages`.`ENABLE`=0
		GROUP BY `moteur_sous_menu`.`SOUS_MENU_ID` ORDER BY `moteur_sous_menu`.`ORDRE`";
	}else{
		$rq_info_sous_menu="
		SELECT 
		`moteur_pages`.`PAGES_ID`, `moteur_pages`.`ITEM` , `moteur_pages`.`LEGEND`  , `moteur_pages`.`LEGEND_MENU`
		FROM `moteur_pages`,`moteur_sous_menu`
		WHERE 
		`moteur_sous_menu`.`PAGES_ID`=`moteur_pages`.`PAGES_ID` AND
		`moteur_sous_menu`.`MENU_ID`='".$MENU_ID."' AND
		`moteur_sous_menu`.`PAGES_ID` IN (
		SELECT `moteur_pages`.`PAGES_ID`
		FROM `moteur_droit` ,`moteur_pages`
		WHERE 
		`moteur_pages`.`PAGES_ID`=`moteur_droit`.`PAGES_ID` AND
		`moteur_droit`.`ROLE_ID` IN(
		SELECT `moteur_role_utilisateur`.`ROLE_ID` 
		FROM `moteur_role_utilisateur`,`moteur_utilisateur` 
		WHERE `moteur_utilisateur`.`UTILISATEUR_ID`=`moteur_role_utilisateur`.`UTILISATEUR_ID` AND
		`moteur_utilisateur`.`LOGIN`='".$LOGIN."' AND 
		`moteur_role_utilisateur`.`ROLE_UTILISATEUR_ACCES`='0'
		) AND
		`moteur_droit`.`DROIT`='OK' AND
		`moteur_pages`.`ENABLE`=0
		ORDER BY `moteur_pages`.`PAGES_ID`
		) AND
		`moteur_pages`.`ENABLE`=0
		GROUP BY `moteur_sous_menu`.`SOUS_MENU_ID` ORDER BY `moteur_sous_menu`.`ORDRE`";
	}
	
  $res_rq_info_sous_menu = mysql_query($rq_info_sous_menu, $mysql_link) or die(mysql_error());
  $tab_rq_info_sous_menu = mysql_fetch_assoc($res_rq_info_sous_menu);
  $total_ligne_rq_info_sous_menu=mysql_num_rows($res_rq_info_sous_menu); 
  do {
    $sous_menu_PAGES_ID=$tab_rq_info_sous_menu['PAGES_ID'];
    $sous_menu_ITEM=$tab_rq_info_sous_menu['ITEM'];
    $sous_menu_LEGEND=$tab_rq_info_sous_menu['LEGEND_MENU'];
    if(substr_count($sous_menu_ITEM, 'vide_' )!=0){
      echo ',"'.$sous_menu_LEGEND.'","",,,1 ';
    }else{
      echo ',"'.$sous_menu_LEGEND.'","index.php?ITEM='.$sous_menu_ITEM.'",,,1 ';
    }
    
  } while ($tab_rq_info_sous_menu = mysql_fetch_assoc($res_rq_info_sous_menu));
  $ligne= mysql_num_rows($res_rq_info_sous_menu);
  if($ligne > 0) {
    mysql_data_seek($res_rq_info_sous_menu, 0);
    $tab_rq_info_sous_menu = mysql_fetch_assoc($res_rq_info_sous_menu);
  }
  echo '])';
  
} while ($tab_rq_info_menu = mysql_fetch_assoc($res_rq_info_menu));
$ligne= mysql_num_rows($res_rq_info_menu);
if($ligne > 0) {
  mysql_data_seek($res_rq_info_menu, 0);
  $tab_rq_info_menu = mysql_fetch_assoc($res_rq_info_menu);
}
}
	
}
?>

dumpmenus()
</SCRIPT>
<SCRIPT language='javascript' src="./lib/menu/mmenu.js" type='text/javascript'></SCRIPT>

<!-- Script pour l'affiche du titre Ombré -->
<script type="text/javascript">
function init(color,id) { // couleur du texte et son id

	var e = document.getElementById(id);
	var titre = e.innerHTML; // On récupère le contenu du titre

	// La variable "c" est un tableau contenant les couleurs à utiliser.
	// De gauche à droite : de la couleur la plus claire à la plus foncée, pour finir par la couleur du texte lui-même
	var c = ["#f9f9f9","#fff","#eee","#ddd",color];
	
	a = 0; // Variable utilisée pour décaler les textes les uns par rapport aux autres
	t = ''; // On initialise la variable que l'on retournera par la suite

	for(i=0; i<c.length; i++) {
		// On décale à chaque fois le texte d'un pixel et on change sa couleur
		t += '<span style="color:'+c[i]+';position:absolute;margin:'+(a--)+'px 0 0 '+a+'px">'+titre+'</span>';
	}

	// On affiche pour finir le résultat, en ajoutant le texte qui superpose les autres
	e.innerHTML = t+'<span style="color:'+c[0]+';margin:0">'+titre+'</span>';
}
</script>
<script type="text/javascript">
function afficher(leDivAAfficher)
{
   document.getElementById(leDivAAfficher).style.display ="";
}

function cacher(leDivAAfficher)
{
   document.getElementById(leDivAAfficher).style.display = "none";
}
</script>
</head>
<body>
<table background="./img/bg.jpg" width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="Global">
 	<a name="Haut_de_page"></a>
  <tr>
    <td>
<!--DEBUT HEADER==================================-->
      <table width="100%" border="0" cellspacing="0" cellpadding="0" class="Header">
        <tr> 
          <td align="left" width="200" bgcolor="#FFFFFF">
          <a href="./"><img src="./img/logo_gauche.png" width="198" height="60" border="0"></a>
          </td>
          <td align="center" valign="center">
            <h1 id="ombre">
            <?PHP
		if ( $ENV != "x" )
		{
			if(substr_count($_SERVER["SCRIPT_FILENAME"], '/php/iab/')==1){
				echo 'DEV - ';
			}else{
				if(substr_count($_SERVER["SCRIPT_FILENAME"], '/php/iab_va/')==1){
					echo 'VA - ';
				}else{
					echo 'DEV - ';
				}
			}
		}
		echo $Var_NOM_SITE;
            ?>
            </h1>
            <script type="text/javascript">
            init('#F25B2E','ombre');
            </script>
          </td>
          <td align="right" width="200" >
          <a href="./"><img src="./img/logo_droit.png" width="65" height="65" border="0"></a>
          </td>
        </tr>
      </table>
<!--FIN HEADER===================================-->
<!--DEBUT USERINFO===============================-->
      <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="100%" align="center" valign="top">
            <table width="100%" height="20" border="0" cellpadding="0" cellspacing="0" class="Menu">
              <tr>
                <td height="20" align="left">
                  <b>
                  <table width="100%" height="20" border="0" cellpadding="0" cellspacing="0">
                  <tr>
                  <td align="left">
                    <?php
                      // Affichage infos utilisateur
                     
                      if (isset($_SESSION['VALID'])){
                        if ($_SESSION['VALID']=='KO'){
                          echo '&nbsp; .: Informations utilisateur :.';
                        }else{
                          if ($_SESSION['LOGIN']=='guest'){
                          	echo '&nbsp;.: Bienvenue ';
                          }else{
                          	echo '&nbsp;.: Bienvenue : '.$_SESSION['PRENOM'].' '.$_SESSION['NOM'];
                          }
                          
                          if(isset($_SESSION['ACCES'])){
                          	if($_SESSION['ACCES']=='L'){ echo ' - Acc&egrave;s en lecture';}
                          }
                          echo ' :.';
                        }
                      }else{
                        echo '&nbsp; .: Informations utilisateur :.';
                      }
                      ?>  
                    </td>
                    <td align="right">
                    <?php
                      // Affichage infos utilisateur
                     
                      if (isset($_SESSION['VALID'])){
                        if ($_SESSION['VALID']=='KO'){
                          echo '&nbsp; .: <a class="LinkDef" href="./index.php?ITEM=login">Connexion</a> :.';
                        }else{
                          echo '&nbsp;.: <a class="LinkDef" href="./index.php?ITEM=unlogin&action=UnLogin">D&eacute;connexion</a> :.';
                        }
                      }else{
                        echo '&nbsp; .: <a class="LinkDef" href="./index.php?ITEM=login">Connexion</a> :.';
                      }
                      ?>  
                      </td>
                      </tr>
                      </table>            
                  </b>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
<!--FIN USERINFO=================================-->
<!--DEBUT MENU===================================-->
      <table border="0" cellpadding="0" cellspacing="0" class="Menu">
        <tr>
          <td>
          &nbsp;<br/>&nbsp;
          &nbsp;<br/>&nbsp;
          &nbsp;<br/>&nbsp;
        </td>
        </tr>
      </table>
<!--FIN MENU===================================-->
<!--DEBUT CONTENU==============================-->
      <table width="100%" border="0" cellpadding="0" cellspacing="0" class="Header">
        <tr>
          <td align="center" valign="top">
		  		<div align="center">
          	<table border="0" cellpadding="0" cellspacing="0" class="Contenu">
              <tr>
                <td>
                  <fieldset name='field2'>
                  <?php
								  //Affichage de la légende
									echo '<LEGEND ><b>'.stripslashes($LEGEND).'</b></LEGEND>';
								  //Affichage du contenu
									include($URLP);
                  ?>                  
                  </fieldset>
                </td>
              </tr>
            </table>
		  		</div>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<!--FIN CONTENU==============================-->
<!--DEBUT FOOTER=============================-->	  
      <table width="100%" border="0" cellspacing="0" cellpadding="0" class="Copyright">
        <tr>
          <td>&nbsp;</td>
        </tr>
      </table>
<!--FIN FOOTER===============================-->
  <a name="Bas_de_page"></a>
<!--<?PHP echo session_id(); ?>-->
</body>
</html>