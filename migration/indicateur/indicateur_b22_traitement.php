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

$DATE_INDICATEUR='';

require_once("./cf/autre_fonctions.php");
ouverture_db();

if (empty($_POST)){
}else{
	if(!empty($_POST['execution'])){
		$ANNEE=$_POST['Annee'];
		$MOIS=$_POST['Mois'];
		$Mois_critere=$MOIS; 
		$Annee_critere=$ANNEE; 
		
		if($MOIS<10){
			$MOIS='0'.$MOIS;		
		}
		
		$DATE_INDICATEUR=$ANNEE.$MOIS;
				
// on supprimme les donn&eacute;es si l'on a deja fait un calcul		
		$sql="DELETE FROM `indicateur_b22_tableau`  WHERE `INDICATEUR_B22_TABLEAU_DATE_INDICATEUR` ='".$DATE_INDICATEUR."'";
		mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
		$OptTable=mysql_query("OPTIMIZE TABLE `indicateur_b22_tableau`",$mysql_link);
		
		$sql="
		INSERT INTO `indicateur_b22_tableau` (`INDICATEUR_B22_TABLEAU_ID`, `INDICATEUR_B22_TABLEAU_APP`, `INDICATEUR_B22_TABLEAU_NB_ENGAG_J`, `INDICATEUR_B22_TABLEAU_NB_ENGAG_H`, `INDICATEUR_B22_TABLEAU_NB_ENGAG_M`,`INDICATEUR_B22_TABLEAU_DATE_INDICATEUR`) 
		SELECT 'NULL', `id_appli` , `nb_engag_j` , `nb_engag_h` , `nb_engag_m` , '".$DATE_INDICATEUR."'
		FROM `referentiel_appli` 
		WHERE `id_appli` NOT IN ('', 'Tout')
		AND `fin_vie`>'".$ANNEE."-".$MOIS."-01' AND `cotation`='Y'
		ORDER BY `id_appli`";
		//echo $sql;
		mysql_query($sql) or die('Erreur SQL !'.$sql.''.mysql_error());
		$OptTable=mysql_query("OPTIMIZE TABLE `indicateur_b22_tableau`");



        }
        $OptTable=mysql_query("OPTIMIZE TABLE `indicateur_b22_tableau`");
        
        echo '
        <script language="JavaScript">
        url=("./index.php?ITEM=indicateur_b22_tableau&ANNEE='.$ANNEE.'");
        window.location=url;
        </script>
        ';
}
echo '
<center>
<form method="POST" action="./index.php?ITEM=indicateur_b22_traitement">
<table class="table_inc" cellspacing="0" cellpading="0">
  <tr align="center" class="titre">
    <td align="center" colspan="2">&nbsp;</td>
  </tr>
  <tr align="center" class="titre">
    <td align="center" colspan="2">&nbsp;traitement des indicateurs B22&nbsp;</td>
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
    <td align="center" colspan="2">&nbsp;';
    if(autre_acces_sql()!="L"){
    echo '<input type="submit" name="execution" value="Execution du traitement">';
    }
    echo '&nbsp;</td>
  </tr>
  <tr align="center" class="titre">
    <td align="center" colspan="2">&nbsp;</td>
  </tr>
</table>     
</form>
</center>
';
close_db();
?>
