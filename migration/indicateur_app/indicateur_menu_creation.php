<?PHP
//redirection si acces dirrect
if(substr_count($_SERVER['PHP_SELF'], 'index.php')==0){
  header("Location: ../"); 
  exit(); 
}
/*******************************************************************
   Interface Gestion des indicateurs
   Version 1.0.0   
  10/02/2010 - VGU - Creation fichier
**************************************************Guibert Vincent***
*******************************************************************/

require("./cf/conf_outil_icdc.php"); 
require_once("./cf/fonctions.php");
$j=0;
$ID='';
  echo '
   <div align="center">
    <br/>
    <table class="table_inc" cellspacing="1" cellpading="0">
      <tr align="center" class="titre">
        <td align="center" colspan="2">
        <b>&nbsp;Menu cr&eacute;ation des indicateurs - IAB&nbsp;</b>
        </td>
      </tr>';
        $j++;
        if ($j%2) { $class = "pair";}else{$class = "impair";} 
        echo '
        <tr align="center" class='.$class.'>
          <td align="left" colspan="2"><a class="LinkDef" href="./index.php?ITEM=indicateur_documentation">&nbsp;Modop des indicateurs&nbsp;</a></td>
        </tr>';
        $j++;
        if ($j%2) { $class = "pair";}else{$class = "impair";} 
        echo '
        <tr align="center" class='.$class.'>
          <td align="left" colspan="2"><a class="LinkDef" href="./index.php?ITEM=indicateur_Gestion_Application">&nbsp;Gestion des applications&nbsp;</a></td>
        </tr>';
        $j++;
        if ($j%2) { $class = "pair";}else{$class = "impair";} 
        echo '
        <tr align="center" class='.$class.'>
          <td align="left" colspan="2"><a class="LinkDef" href="./index.php?ITEM=indicateur_rapport_pondere_doc">&nbsp;Doc sur le rapport pond&eacute;r&eacute; ODTI/QC9&nbsp;</a></td>
        </tr>';
        echo '
        <tr align="center" class="titre">
        <td align="center" colspan="2">
        <b>&nbsp;ODTI&nbsp;</b>
        </td>
        </tr>';
        if(acces_sql()!="L"){
	        $j++;
	        if ($j%2) { $class = "pair";}else{$class = "impair";} 
	        echo '
	        <tr align="center" class='.$class.'>
	          <td align="left" colspan="2"><a class="LinkDef" href="./index.php?ITEM=indicateur_configuration_sogeti">&nbsp;Configuration des indicateurs&nbsp;</a></td>
	        </tr>';
	}
        $j++;
        if ($j%2) { $class = "pair";}else{$class = "impair";} 
        echo '
        <tr align="center" class='.$class.'>
          <td align="left" colspan="2"><a class="LinkDef" href="./index.php?ITEM=indicateur_info_configuration">&nbsp;Information sur la configuration des indicateurs&nbsp;</a></td>
        </tr>';
        if(acces_sql()!="L"){
	        $j++;
	        if ($j%2) { $class = "pair";}else{$class = "impair";} 
	        echo '
	        <tr align="center" class='.$class.'>
	          <td align="left" colspan="2"><a class="LinkDef" href="./index.php?ITEM=indicateur_upload">&nbsp;Upload de l\'extration ODTI pour la gestion applicative&nbsp;</a></td>
	        </tr>';
	}
        
        if(acces_sql()!="L"){
        	echo '
	        <tr align="center" class="titre">
	        <td align="center" colspan="2">
	        <b>&nbsp;QC9&nbsp;</b>
	        </td>
	        </tr>';
	        $j++;
	        if ($j%2) { $class = "pair";}else{$class = "impair";} 
	        echo '
	        <tr align="center" class='.$class.'>
	          <td align="left" colspan="2"><a class="LinkDef" href="./index.php?ITEM=indicateur_qc9_upload">&nbsp;Upload de l\'extration QC9 pour la gestion applicative&nbsp;</a></td>
	        </tr>';
	}
       
        if(acces_sql()!="L"){
        	 echo '
	        <tr align="center" class="titre">
	        <td align="center" colspan="2">
	        <b>&nbsp;B22&nbsp;</b>
	        </td>
	        </tr>';
	        $j++;
	        if ($j%2) { $class = "pair";}else{$class = "impair";} 
	        echo '
	        <tr align="center" class='.$class.'>
	          <td align="left" colspan="2"><a class="LinkDef" href="./index.php?ITEM=indicateur_b22_traitement">&nbsp;Cr&eacute;ation du B22 pour la gestion applicative&nbsp;</a></td>
	        </tr>';
	}
    echo '
    <tr align="center" class="titre">
      <td align="center" colspan="2">&nbsp;</td>
    </tr>
    <tr align="center" class="titre">
      <td align="center" colspan="2">&nbsp;[&nbsp;<a href="./index.php?ITEM=indicateur_menu">Menu des indicateurs</a>&nbsp;]&nbsp;</td>
    </tr>
    <tr align="center" class="titre">
      <td align="center" colspan="2">&nbsp;</td>
    </tr>
  </table>
</div>';
mysql_close($mysql_link);
?>