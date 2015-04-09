<?php
//redirection si acces dirrect
if(substr_count($_SERVER['PHP_SELF'], 'index.php')==0){
header("Location: ../");
exit();
}
//connexion base de donnees
require("./cf/conf_outil_icdc.php");
//require_once("./cf/fonctions.php");

// Debut page HTML
//centrage dans la page
echo '<div align="center">';

$j=0;
$tab_var=$_POST;
$Select_Appli="";
$Select_Table="";
$Select_Group="";
$Select_Rep="";
$Select_Jctlm="";
$Select_Shell="";
$Select_Jappli="";

//requete affichage liste appli
$req_liste_appli = "
select distinct `EXTRACT_DATA_APPLICATION`
from `ctlm_extract_ctlm`
where `ENABLE`=0
order by `EXTRACT_DATA_APPLICATION` ASC ;";
$res_req_liste_appli = mysql_query($req_liste_appli, $mysql_link) or die(mysql_error());
$tab_req_liste_appli = mysql_fetch_assoc($res_req_liste_appli);
$total_ligne_req_liste_appli = mysql_num_rows($res_req_liste_appli);

if(empty($tab_var['btn'])){
  # récuperation variables requete
  if(isset($_GET['a'])){
   $Select_Appli=$_GET['a'];
  }
   
     $req_liste_table = "
     select distinct `EXTRACT_DATA_TABLE_CTLM`
     from `ctlm_extract_ctlm`
     where `EXTRACT_DATA_APPLICATION`='$Select_Appli'
     and `ENABLE`=0
     order by `EXTRACT_DATA_TABLE_CTLM` ASC ;";
     $res_req_liste_table = mysql_query($req_liste_table, $mysql_link) or die(mysql_error());
     $tab_req_liste_table = mysql_fetch_assoc($res_req_liste_table);
     $total_ligne_req_liste_table = mysql_num_rows($res_req_liste_table);
     
     $req_liste_group = "
     select distinct `EXTRACT_DATA_GROUP_CTLM`
     from `ctlm_extract_ctlm`
     where `EXTRACT_DATA_APPLICATION`='$Select_Appli'
     and `ENABLE`=0
     order by `EXTRACT_DATA_GROUP_CTLM` ASC ;";
     $res_req_liste_group = mysql_query($req_liste_group, $mysql_link) or die(mysql_error());
     $tab_req_liste_group = mysql_fetch_assoc($res_req_liste_group);
     $total_ligne_req_liste_group = mysql_num_rows($res_req_liste_group);
     
     $req_liste_repertoire = "
     select distinct `EXTRACT_DATA_REPERTOIRE`
     from `ctlm_extract_ctlm`
     where `EXTRACT_DATA_APPLICATION`='$Select_Appli'
     and `ENABLE`=0
     order by `EXTRACT_DATA_REPERTOIRE` ASC ;";
     $res_req_liste_repertoire = mysql_query($req_liste_repertoire, $mysql_link) or die(mysql_error());
     $tab_req_liste_repertoire = mysql_fetch_assoc($res_req_liste_repertoire);
     $total_ligne_req_liste_repertoire = mysql_num_rows($res_req_liste_repertoire);
     
     $req_liste_jctlm = "
     select distinct `EXTRACT_DATA_JOB_CTLM`
     from `ctlm_extract_ctlm`
     where `EXTRACT_DATA_APPLICATION`='$Select_Appli'
     and `ENABLE`=0
     order by `EXTRACT_DATA_JOB_CTLM` ASC ;";
     $res_req_liste_jctlm = mysql_query($req_liste_jctlm, $mysql_link) or die(mysql_error());
     $tab_req_liste_jctlm = mysql_fetch_assoc($res_req_liste_jctlm);
     $total_ligne_req_liste_jctlm = mysql_num_rows($res_req_liste_jctlm);

  if(isset($_GET['t'])){
   $Select_Table=$_GET['t'];
  }
  if(isset($_GET['g'])){
   $Select_Group=$_GET['g'];
  }
  if(isset($_GET['r'])){
   $Select_Rep=$_GET['r'];
  }
  if(isset($_GET['jc'])){
   $Select_Jctlm=$_GET['jc'];
  }
  if(isset($_GET['s'])){
   $Select_Shell=$_GET['s'];
  }
  if(isset($_GET['ja'])){
   $Select_Jappli=$_GET['ja'];
  }
  
}else{

  # Cas Selection Application
  if($tab_var['btn']=="Valider Application"){
    $Select_Appli=$tab_var['appli'];
    
     $req_liste_table = "
     select distinct `EXTRACT_DATA_TABLE_CTLM`
     from `ctlm_extract_ctlm`
     where `EXTRACT_DATA_APPLICATION`='$Select_Appli'
     and `ENABLE`=0
     order by `EXTRACT_DATA_TABLE_CTLM` ASC ;";
     $res_req_liste_table = mysql_query($req_liste_table, $mysql_link) or die(mysql_error());
     $tab_req_liste_table = mysql_fetch_assoc($res_req_liste_table);
     $total_ligne_req_liste_table = mysql_num_rows($res_req_liste_table);
     
     $req_liste_group = "
     select distinct `EXTRACT_DATA_GROUP_CTLM`
     from `ctlm_extract_ctlm`
     where `EXTRACT_DATA_APPLICATION`='$Select_Appli'
     and `ENABLE`=0
     order by `EXTRACT_DATA_GROUP_CTLM` ASC ;";
     $res_req_liste_group = mysql_query($req_liste_group, $mysql_link) or die(mysql_error());
     $tab_req_liste_group = mysql_fetch_assoc($res_req_liste_group);
     $total_ligne_req_liste_group = mysql_num_rows($res_req_liste_group);
     
     $req_liste_repertoire = "
     select distinct `EXTRACT_DATA_REPERTOIRE`
     from `ctlm_extract_ctlm`
     where `EXTRACT_DATA_APPLICATION`='$Select_Appli'
     and `ENABLE`=0
     order by `EXTRACT_DATA_REPERTOIRE` ASC ;";
     $res_req_liste_repertoire = mysql_query($req_liste_repertoire, $mysql_link) or die(mysql_error());
     $tab_req_liste_repertoire = mysql_fetch_assoc($res_req_liste_repertoire);
     $total_ligne_req_liste_repertoire = mysql_num_rows($res_req_liste_repertoire);
     
     $req_liste_jctlm = "
     select distinct `EXTRACT_DATA_JOB_CTLM`
     from `ctlm_extract_ctlm`
     where `EXTRACT_DATA_APPLICATION`='$Select_Appli'
     and `ENABLE`=0
     order by `EXTRACT_DATA_JOB_CTLM` ASC ;";
     $res_req_liste_jctlm = mysql_query($req_liste_jctlm, $mysql_link) or die(mysql_error());
     $tab_req_liste_jctlm = mysql_fetch_assoc($res_req_liste_jctlm);
     $total_ligne_req_liste_jctlm = mysql_num_rows($res_req_liste_jctlm);
  }
}

echo '
<form name="requete_infos_jobs" method="post" action="./index.php?ITEM=corres_ctlm_Chercher_Job">
<table class="table_inc" cellspacing="0" cellpading="0">
  <tr align="center" class="titre">
    <td align="center" colspan="2">&nbsp;</td>
  </tr>
  <tr align="center" class="titre">
    <td align="center" colspan="2"><b>&nbsp;Choisir l\'application concernée :&nbsp;</b></td>
  </tr>
  <tr align="center" class="titre">
    <td align="center" colspan="2">&nbsp;</td>
  </tr>
  <tr align="center" class="pair">
    <td>
    </td>
  </tr>
';
$j++;
if ($j%2) { $class = "pair";}else{$class = "impair";}
echo '
  <tr align="center" class="'.$class.'">
    <td align="left"><u>Application CTLM</u> : </td>
    <td align="left">
       <SELECT name="appli" size="1" id="appli" onChange="">
	<OPTION value="0"></OPTION>
	';
	do
	{
  	  $APPLI = $tab_req_liste_appli['EXTRACT_DATA_APPLICATION'];
  	   if ($Select_Appli==$APPLI){
  	    echo'
  	    <OPTION value="'.$APPLI.'" selected="selected">'.$APPLI.'</OPTION>
  	    ';
  	   }else{
  	    echo'
  	    <OPTION value="'.$APPLI.'">'.$APPLI.'</OPTION>
  	    ';
  	   }
	}while ($tab_req_liste_appli = mysql_fetch_assoc($res_req_liste_appli));
	echo'
       </SELECT>
    </td>
  </tr>
  <tr align="center" class="'.$class.'">
    <td>
    </td>
  </tr>
    <tr align="center" class="titre">
    <td align="center" colspan="2">&nbsp;</td>
  </tr>
  <tr align="center" class="titre">
    <td align="center" colspan="2">&nbsp;<input type="submit" Id="btn" name="btn" value="Valider Application" >&nbsp;</td>
  </tr>

</table>
</form>';

if ($Select_Appli=='0'||$Select_Appli==''){
  echo '
  <form name="requete_select_jobs" method="post" action="./index.php?ITEM=corres_ctlm_Trouver_Job&appli='.$Select_Appli.'">
  <table class="table_inc" cellspacing="0" cellpading="0">
    <tr align="center" class="titre">
      <td align="center" colspan="2">&nbsp;</td>
    </tr>
    <tr align="center" class="titre">
      <td align="center" colspan="2"><b>&nbsp;Trouver la Correspondance Job CTLM / Job Applicatif : &nbsp;</b></td>
    </tr>
    <tr align="center" class="titre">
      <td align="center" colspan="2">&nbsp;</td>
    </tr>
';
$j++;
if ($j%2) { $class = "pair";}else{$class = "impair";}
echo '
    <tr align="center" class="'.$class.'">
      <td colspan="2">
      </td>
    </tr>
    <tr align="center" class="'.$class.'">
      <td align="left"><u>Shell UNIX (DPI)</u> : </td>
      <td align="left"><input name="shell" type="text" size="50" value="'.$Select_Shell.'"/></td>
    </tr>
    <tr align="center" class="'.$class.'">
      <td colspan="2">
      </td>
    </tr>
';
$j++;
if ($j%2) { $class = "pair";}else{$class = "impair";}
echo '    
    <tr align="center" class="'.$class.'">
      <td colspan="2">
      </td>
    </tr>
    <tr align="center" class="'.$class.'">
      <td align="left"><u>Job Applicatif (DEI)</u> : </td>
      <td align="left"><input name="jappli" type="text" size="50" value="'.$Select_Jappli.'"/></td>
    </tr>
    <tr align="center" class="'.$class.'">
      <td colspan="2">
      </td>
    </tr>
    
    <tr align="center" class="titre">
      <td align="center" colspan="2">&nbsp;</td>
    </tr>
    <tr align="center" class="titre">
      <td align="center" colspan="2">&nbsp;<input type="submit" Id="btn" name="btn" value="Executer la recherche" >&nbsp;</td>
    </tr>
  </table>
  </form>';
}else{
  echo '
  <form name="requete_select_jobs" method="post" action="./index.php?ITEM=corres_ctlm_Trouver_Job&appli='.$Select_Appli.'">
  <table class="table_inc" cellspacing="0" cellpading="0">
    <tr align="center" class="titre">
      <td align="center" colspan="2">&nbsp;</td>
    </tr>
    <tr align="center" class="titre">
      <td align="center" colspan="2"><b>&nbsp;Trouver la Correspondance Job CTLM / Job Applicatif : &nbsp;</b></td>
    </tr>
    <tr align="center" class="titre">
      <td align="center" colspan="2">&nbsp;</td>
    </tr>
';
$j++;
if ($j%2) { $class = "pair";}else{$class = "impair";}
echo '    
    <tr align="center" class="'.$class.'">
      <td colspan="2">
      </td>
    </tr>
    <tr align="center" class="'.$class.'">
      <td align="left"><u>Table CTLM</u> : </td>
      <td align="left">
        <SELECT name="table" size="1" id="table" onChange="">
  	 <OPTION value="0"></OPTION>
  	';
  	do
  	{
    	  $TABLE = $tab_req_liste_table['EXTRACT_DATA_TABLE_CTLM'];
    	  if ($Select_Table==$TABLE){
    	    echo'
    	    <OPTION value="'.$TABLE.'" selected="selected">'.$TABLE.'</OPTION>
    	    ';
    	    }else{
  	    echo'
  	    <OPTION value="'.$TABLE.'">'.$TABLE.'</OPTION>
  	    ';
  	   }
  	}while ($tab_req_liste_table = mysql_fetch_assoc($res_req_liste_table));
  	echo'
         </SELECT>
      </td>
    </tr>
    <tr align="center" class="'.$class.'">
      <td colspan="2">
      </td>
    </tr>
';
$j++;
if ($j%2) { $class = "pair";}else{$class = "impair";}
echo '    
    <tr align="center" class="'.$class.'">    
      <td colspan="2">
      </td>
    </tr>
    <tr align="center" class="'.$class.'">  
      <td align="left"><u>Groupe CTLM</u> : </td>
      <td align="left">
        <SELECT name="group" size="1" id="group" onChange="">
  	 <OPTION value="0"></OPTION>
  	';
  	do
  	{
    	  $GROUP = $tab_req_liste_group['EXTRACT_DATA_GROUP_CTLM'];
    	  if ($Select_Group==$GROUP){
    	    echo'
    	    <OPTION value="'.$GROUP.'" selected="selected">'.$GROUP.'</OPTION>
    	    ';
    	  }else{
  	    echo'
  	    <OPTION value="'.$GROUP.'">'.$GROUP.'</OPTION>
  	    ';
	  }
  	}while ($tab_req_liste_group = mysql_fetch_assoc($res_req_liste_group));
  	echo'
         </SELECT>
      </td>
    </tr>
    <tr align="center" class="'.$class.'">  
      <td colspan="2">
      </td>
    </tr>
';
$j++;
if ($j%2) { $class = "pair";}else{$class = "impair";}
echo '    
    <tr align="center" class="'.$class.'">
      <td colspan="2">
      </td>
    </tr>
    <tr align="center" class="'.$class.'">
      <td align="left"><u>Répertoire physique</u> : </td>
      <td align="left">
        <SELECT name="rep" size="1" id="rep" onChange="">
  	 <OPTION value="0"></OPTION>
  	';
  	do
  	{
    	  $REP = $tab_req_liste_repertoire['EXTRACT_DATA_REPERTOIRE'];
    	  if ($Select_Rep==$REP){
    	    echo'
    	    <OPTION value="'.$REP.'" selected="selected">'.$REP.'</OPTION>
    	    ';
    	  }else{
  	    echo'
  	    <OPTION value="'.$REP.'">'.$REP.'</OPTION>
  	    ';
	  }
  	}while ($tab_req_liste_repertoire = mysql_fetch_assoc($res_req_liste_repertoire));
  	echo'
         </SELECT>
      </td>
    </tr>
    <tr align="center" class="'.$class.'">
      <td colspan="2">
      </td>
    </tr>
';
$j++;
if ($j%2) { $class = "pair";}else{$class = "impair";}
echo '    
    <tr align="center" class="'.$class.'">
      <td colspan="2">
      </td>
    </tr>    
    <tr align="center" class="'.$class.'">
      <td align="left"><u>Job CTLM </u> : </td>
      <td align="left">
        <SELECT name="jctlm" size="1" id="jctlm" onChange="">
  	 <OPTION value="0"></OPTION>
  	';
  	do
  	{
    	  $JCTLM = $tab_req_liste_jctlm['EXTRACT_DATA_JOB_CTLM'];
    	  if ($Select_Jctlm==$JCTLM){
    	    echo'
    	    <OPTION value="'.$JCTLM.'" selected="selected">'.$JCTLM.'</OPTION>
    	    ';
    	  }else{
  	    echo'
  	    <OPTION value="'.$JCTLM.'">'.$JCTLM.'</OPTION>
  	    ';
	  }
  	}while ($tab_req_liste_jctlm = mysql_fetch_assoc($res_req_liste_jctlm));
  	echo'
         </SELECT>
      </td>
    </tr>
    <tr align="center" class="'.$class.'">
      <td colspan="2">
      </td>
    </tr>
';
$j++;
if ($j%2) { $class = "pair";}else{$class = "impair";}
echo '    
    <tr align="center" class="'.$class.'">
      <td colspan="2">
      </td>
    </tr>
    <tr align="center" class="'.$class.'">
      <td align="left"><u>Shell UNIX (DPI)</u> : </td>
      <td align="left"><input name="shell" type="text" size="50" value="'.$Select_Shell.'" /></td>
    </tr>
    <tr align="center" class="'.$class.'">
      <td colspan="2">
      </td>
    </tr>
';
$j++;
if ($j%2) { $class = "pair";}else{$class = "impair";}
echo '    
    <tr align="center" class="'.$class.'">
      <td colspan="2">
      </td>
    </tr>
    <tr align="center" class="'.$class.'">
      <td align="left"><u>Job Applicatif (DEI)</u> : </td>
      <td align="left"><input name="jappli" type="text" size="50" value="'.$Select_Jappli.'" /></td>
    </tr>
    <tr align="center" class="'.$class.'">
      <td colspan="2">
      </td>
    </tr>
    
    <tr align="center" class="titre">
      <td align="center" colspan="2">&nbsp;</td>
    </tr>
    <tr align="center" class="titre">
      <td align="center" colspan="2">&nbsp;<input type="submit" Id="btn" name="btn" value="Executer la recherche" >&nbsp;</td>
    </tr>
  </table>
  </form>';
}
echo '</div>';
mysql_close();
?>