<?PHP
//redirection si acces dirrect
if(substr_count($_SERVER['PHP_SELF'], 'index.php')==0){
  header("Location: ../");
  exit();
}
/*******************************************************************
   Interface Ajout application indicateurs
   Version 1.0.0   
  31/08/2010 - VGU - Creation fichier
**************************************************Guibert Vincent***
*******************************************************************/

require("./cf/conf_outil_icdc.php"); 
require_once("./cf/fonctions.php");
$j=0;

if(isset($_GET['action'])){
  $action=$_GET['action'];
}else{
  $action='AUTRE';
}
if(isset($_GET['APP'])){
  $APP=$_GET['APP'];
}else{
  $APP='';
}


if(isset($_POST['action'])){
  $action=$_POST['action'];
}
if(isset($_POST['APP'])){
  $APP=$_POST['APP'];
}
if(isset($_POST['Appli'])){
  $Appli_critere=$_POST['Appli'];
}else{
  $Appli_critere='';
}
if($action=='REF'){
	if($APP!=''){
		if(isset($_POST['btn'])){
			if($_POST['btn']=='Oui'){
				
				$sql="INSERT INTO `indicateur_application` (`INDICATEUR_APPLICATION_ID` ,`INDICATEUR_APPLICATION_REF` ,`INDICATEUR_APPLICATION_AUTRE` ,`ENABLE` ) VALUES (NULL , '".$APP."', '".$APP."', '0');";
			        mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
			                
			        $TABLE_SQL_SQL='indicateur_application';       
			        historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');
			        			        
			         echo '
			        <script language="JavaScript">
			        url=("./index.php?ITEM=indicateur_Gestion_Application");
			        window.location=url;
			        </script>
			        ';
			}
			if($_POST['btn']=='Non'){
			         echo '
			        <script language="JavaScript">
			        url=("./index.php?ITEM=indicateur_Gestion_Application");
			        window.location=url;
			        </script>
			        ';
			}
		}
		
	        echo '
<div align="center">
<form method="post" name="frm" id="frm" action="index.php?ITEM='.$_GET['ITEM'].'">
  <table class="table_inc">
    <tr align="center" class="titre">
      <td colspan="2"><h2>&nbsp;Ajout D\'une application du référentiel application&nbsp;</h2>
      </BR>&nbsp;dans la liste des Applications indicateurs&nbsp;
      </td>
    </tr>';
    $j++;
    if ($j%2) { $class = "pair";}else{$class = "impair";} 
    echo '
    <tr class='.$class.'>
      <td align="left">&nbsp;Application&nbsp;</td>
      <td align="left">
      <input name="APP" type="text" readonly value="'.$APP.'" size="10"/>
      </td>
    </tr>';
    
    echo '
    <tr class="titre">
    <td colspan="2" align="center">
    <h2>
    <input name="btn" type="submit" id="btn" value="Oui">
    <input name="btn" type="submit" id="btn" value="Non">
    <input type="hidden" name="action" id="action" value="'.$action.'">
    </h2>
    </td>
	</tr>
	<tr class="titre">
    <td colspan="2" align="center"><h2>&nbsp;[&nbsp;<a href="./index.php?ITEM=indicateur_Gestion_Application">Retour</a>&nbsp;]&nbsp;</h2></td>
	</tr>
	</table>
</form>
</div>';
	       
		
	}else{
		 echo '
	        <script language="JavaScript">
	        url=("./index.php?ITEM=indicateur_Gestion_Application");
	        window.location=url;
	        </script>
	        ';
	}
}
if($action=='AUTRE'){
	if($APP==''){	        
	        echo '
	        <script language="JavaScript">
	        url=("./index.php?ITEM=indicateur_Gestion_Application");
	        window.location=url;
	        </script>
	        ';
		
	}else{
	if($Appli_critere!=''){	        
		if($_POST['btn']=='Oui'){
				
				$sql="INSERT INTO `indicateur_application` (`INDICATEUR_APPLICATION_ID` ,`INDICATEUR_APPLICATION_REF` ,`INDICATEUR_APPLICATION_AUTRE` ,`ENABLE` ) VALUES (NULL , '".$Appli_critere."', '".$APP."', '0');";
			        mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
			                
			        $TABLE_SQL_SQL='indicateur_application';       
			        historique_sql_new($sql,$TABLE_SQL_SQL,'INSERT');
			        			        
			         echo '
			        <script language="JavaScript">
			        url=("./index.php?ITEM=indicateur_Gestion_Application");
			        window.location=url;
			        </script>
			        ';
			}
			if($_POST['btn']=='Non'){
			         echo '
			        <script language="JavaScript">
			        url=("./index.php?ITEM=indicateur_Gestion_Application");
			        window.location=url;
			        </script>
			        ';
			}
	}
		echo '
<div align="center">
<form method="post" name="frm" id="frm" action="index.php?ITEM='.$_GET['ITEM'].'">
  <table class="table_inc">
    <tr align="center" class="titre">
      <td colspan="2"><h2>&nbsp;Ajout D\'une application du référentiel ODTI/QC9&nbsp;</h2>
      </BR>&nbsp;dans la liste des Applications indicateurs&nbsp;
      </td>
    </tr>';
    $j++;
    if ($j%2) { $class = "pair";}else{$class = "impair";} 
    echo '
    <tr class='.$class.'>
      <td align="left">&nbsp;Application ODTI/QC9&nbsp;</td>
      <td align="left">
      <input name="APP" type="text" readonly value="'.$APP.'" size="10"/>
      </td>
    </tr>';
    $j++;
    if ($j%2) { $class = "pair";}else{$class = "impair";} 
    echo '  
    <tr align="center" class='.$class.'>
    <td align="left"><u>Application du référentiel</u> : 
    </td>
    <td align="left">
       <select name="Appli" id="Appli" value="">
         <option '; if($Appli_critere == ""){echo "selected ";} echo 'value=""> </option>';
         
           $Resultat = mysql_query("SELECT id_appli FROM referentiel_appli ORDER BY id_appli;");

           for ($k=0; $k<mysql_numrows($Resultat); $k++)
           {
             echo '<option '; if($Appli_critere == mysql_result($Resultat, $k, "id_appli")){echo "selected ";} echo 'value="'.mysql_result($Resultat, $k, "id_appli").'">'.mysql_result($Resultat, $k, "id_appli").'</option>';
           }
         echo '
       </select>
    </td>
    </tr>
    <tr class="titre">
    <td colspan="2" align="center">
    <h2>
    <input name="btn" type="submit" id="btn" value="Oui">
    <input name="btn" type="submit" id="btn" value="Non">
    <input type="hidden" name="action" id="action" value="'.$action.'">
    </h2>
    </td>
	</tr>
	<tr class="titre">
    <td colspan="2" align="center"><h2>&nbsp;[&nbsp;<a href="./index.php?ITEM=indicateur_Gestion_Application">Retour</a>&nbsp;]&nbsp;</h2></td>
	</tr>
	</table>
</form>
</div>';
	}
}
 
mysql_close(); 

?>