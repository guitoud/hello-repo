<?PHP
//redirection si acces dirrect
if(substr_count($_SERVER['PHP_SELF'], 'index.php')==0){
  header("Location: ../"); 
  exit(); 
}
/*******************************************************************
   Interface Gestion des role
   Version 1.0.0    
  08/01/2010 - VGU - Creation fichier
**************************************************Guibert Vincent***
*******************************************************************/
require("./cf/conf_outil_icdc.php"); 
require("./cf/fonctions.php");
$j=0;
if(isset($_GET['action'])){
    $action=$_GET['action'];
    if(isset($_GET['ID'])){
      $ID=$_GET['ID'];
      $rq_info="
      SELECT `ROLE` 
      FROM `moteur_role` 
      WHERE `ROLE_ID`='".$ID."'";
      $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
      $tab_rq_info = mysql_fetch_assoc($res_rq_info);
      $total_ligne_rq_info=mysql_num_rows($res_rq_info);
      $ROLE=$tab_rq_info['ROLE'];
      mysql_free_result($res_rq_info);
    }else{
      $action=1;
    }
    switch ($action) {
    case "users": 
    echo '
     <div align="center">
     <br/>
      <table class="table_inc" cellspacing="0" cellpading="0">
        <tr align="center" class="titre">
          <td align="center" colspan="2">&nbsp;Liste des utilisateurs avec le r&ocirc;le '.$ROLE.'.&nbsp;</b></td>
        </tr>
        <tr align="center" class="titre">
          <td align="center"><b>&nbsp;Nom&nbsp;</b></td>
          <td align="center"><b>&nbsp;Pr&eacute;nom&nbsp;</b></td>
        </tr>';
        $rq_info="
        SELECT `moteur_utilisateur`.`UTILISATEUR_ID`, `moteur_utilisateur`.`NOM`, `moteur_utilisateur`.`PRENOM` 
        FROM `moteur_utilisateur`,`moteur_role_utilisateur`
      WHERE `moteur_role_utilisateur`.`UTILISATEUR_ID`=`moteur_utilisateur`.`UTILISATEUR_ID`
      AND `moteur_role_utilisateur`.`ROLE_UTILISATEUR_ACCES`=0
      AND `moteur_role_utilisateur`.`ROLE_ID`='".$ID."'
      ORDER BY `moteur_utilisateur`.`NOM`, `moteur_utilisateur`.`PRENOM` ";
        $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
        $tab_rq_info = mysql_fetch_assoc($res_rq_info);
        $total_ligne_rq_info=mysql_num_rows($res_rq_info);
        if($total_ligne_rq_info==0){
              echo '
              <tr align="center" class='.$class.'>
                <td align="left" colspan="2">Pas d\'information dans la base</td>
              </tr>';
        }else{
        do {
          $PRENOM=$tab_rq_info['PRENOM'];
          $NOM=$tab_rq_info['NOM'];
                        
          $j++;
          if ($j%2) { $class = "pair";}else{$class = "impair";} 
          echo '
          <tr align="center" class='.$class.'>
            <td align="left">&nbsp;'.$NOM.'&nbsp;</td>
            <td align="left">&nbsp;'.$PRENOM.'&nbsp;</td>
          </tr>';


        } while ($tab_rq_info = mysql_fetch_assoc($res_rq_info));
        $ligne= mysql_num_rows($res_rq_info);
        if($ligne > 0) {
          mysql_data_seek($res_rq_info, 0);
          $tab_rq_info = mysql_fetch_assoc($res_rq_info);
        }
        mysql_free_result($res_rq_info);
    }
        echo '
        <tr align="center" class="titre">
          <td align="center" colspan="2">&nbsp;</td>
        </tr>
        <tr align="center" class="titre">
          <td align="center" colspan="2">&nbsp;[&nbsp;<a href="./index.php?ITEM=Admin_Gestion_Role">Retour</a>&nbsp;]&nbsp;</td>
        </tr>
        <tr align="center" class="titre">
          <td align="center" colspan="2">&nbsp;</td>
        </tr>
        
      </table>
    </div>';
    break;

    case "pages": 
    echo '
     <div align="center">
     <br/>
      <table class="table_inc" cellspacing="0" cellpading="0">
        <tr align="center" class="titre">
          <td align="center" colspan="3">&nbsp;Liste des utilisateurs avec le r&ocirc;le '.$ROLE.'.&nbsp;</b></td>
        </tr>
        <tr align="center" class="titre">
          <td align="center"><b>&nbsp;ITEM&nbsp;</b></td>
          <td align="center"><b>&nbsp;URLP&nbsp;</b></td>
          <td align="center"><b>&nbsp;l&eacute;gend&nbsp;</b></td>
        </tr>';
        $rq_info="
        SELECT `moteur_pages`.`ITEM`, `moteur_pages`.`URLP`, `moteur_pages`.`LEGEND` 
        FROM `moteur_pages`,`moteur_droit`
      WHERE `moteur_droit`.`PAGES_ID`=`moteur_pages`.`PAGES_ID`
      AND `moteur_droit`.`DROIT`='OK'
      AND `moteur_droit`.`ROLE_ID`='".$ID."'
      ORDER BY `moteur_pages`.`URLP`, `moteur_pages`.`ITEM`";
        $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
        $tab_rq_info = mysql_fetch_assoc($res_rq_info);
        $total_ligne_rq_info=mysql_num_rows($res_rq_info);
        if($total_ligne_rq_info==0){
              echo '
              <tr align="center" class='.$class.'>
                <td align="left" colspan="3">Pas d\'information dans la base</td>
              </tr>';
        }else{
        do {
          $ITEM=$tab_rq_info['ITEM'];
          $URLP=$tab_rq_info['URLP'];
          $LEGEND=$tab_rq_info['LEGEND'];
                        
          $j++;
          if ($j%2) { $class = "pair";}else{$class = "impair";} 
          echo '
          <tr align="center" class='.$class.'>
            <td align="left">&nbsp;'.stripslashes($ITEM).'&nbsp;</td>
            <td align="left">&nbsp;'.stripslashes($URLP).'&nbsp;</td>
            <td align="left">&nbsp;'.stripslashes($LEGEND).'&nbsp;</td>
          </tr>';


        } while ($tab_rq_info = mysql_fetch_assoc($res_rq_info));
        $ligne= mysql_num_rows($res_rq_info);
        if($ligne > 0) {
          mysql_data_seek($res_rq_info, 0);
          $tab_rq_info = mysql_fetch_assoc($res_rq_info);
        }
        mysql_free_result($res_rq_info);
    }
        echo '
        <tr align="center" class="titre">
          <td align="center" colspan="3">&nbsp;</td>
        </tr>
        <tr align="center" class="titre">
          <td align="center" colspan="3">&nbsp;[&nbsp;<a href="./index.php?ITEM=Admin_Gestion_Role">Retour</a>&nbsp;]&nbsp;</td>
        </tr>
        <tr align="center" class="titre">
          <td align="center" colspan="3">&nbsp;</td>
        </tr>
        
      </table>
    </div>';

    break;

    default:

    break;
}
}else{
echo '
 <div align="center">
 <br/>
  <table class="table_inc" cellspacing="0" cellpading="0">
    <tr align="center" class="titre">
      <td align="center" colspan="3"><b>&nbsp;[&nbsp;<a href=./index.php?ITEM=Admin_Ajout_Role>Ajout d\'un R&ocirc;le</a>&nbsp;]&nbsp;</b></td>
    </tr>
    <tr align="center" class="titre">
      <td align="center"><b>&nbsp;R&ocirc;le&nbsp;</b></td>
      <td align="center"><b>&nbsp;Nb utilisateurs&nbsp;</b></td>
      <td align="center"><b>&nbsp;Nb pages&nbsp;</b></td>
    </tr>';
    $rq_role="SELECT `ROLE_ID`, `ROLE` FROM `moteur_role` ORDER BY `ROLE`";
    $res_rq_role = mysql_query($rq_role, $mysql_link) or die(mysql_error());
    $tab_rq_role = mysql_fetch_assoc($res_rq_role);
    $total_ligne_rq_role=mysql_num_rows($res_rq_role);
    if($total_ligne_rq_role==0){
          echo '
          <tr align="center" class='.$class.'>
            <td align="left" colspan="3">Pas de r&ocirc;le dans la base</td>
          </tr>';
    }else{
    do {
      $ROLE_ID=$tab_rq_role['ROLE_ID'];
      $ROLE=$tab_rq_role['ROLE'];
      $rq_info_nb="
      SELECT COUNT(`UTILISATEUR_ID`) AS `NB` 
      FROM `moteur_role_utilisateur` 
      WHERE `ROLE_ID`='".$ROLE_ID."' 
      AND `ROLE_UTILISATEUR_ACCES`='0'";
      $res_rq_info_nb = mysql_query($rq_info_nb, $mysql_link) or die(mysql_error());
      $tab_rq_info_nb = mysql_fetch_assoc($res_rq_info_nb);
      $total_ligne_rq_info_nb=mysql_num_rows($res_rq_info_nb);
      $NB_ROLES=$tab_rq_info_nb['NB'];
      mysql_free_result($res_rq_info_nb);
      
      $rq_info_nb="
      SELECT COUNT(`PAGES_ID`) AS `NB`
      FROM `moteur_droit` 
      WHERE `DROIT`='OK' 
      AND `ROLE_ID`='".$ROLE_ID."'";
      $res_rq_info_nb = mysql_query($rq_info_nb, $mysql_link) or die(mysql_error());
      $tab_rq_info_nb = mysql_fetch_assoc($res_rq_info_nb);
      $total_ligne_rq_info_nb=mysql_num_rows($res_rq_info_nb);
      $NB_PAGES=$tab_rq_info_nb['NB'];
      mysql_free_result($res_rq_info_nb);
          
      $j++;
      if ($j%2) { $class = "pair";}else{$class = "impair";} 
      echo '
      <tr align="center" class='.$class.'>';
      if($NB_PAGES==0){
      	echo '<td align="center"><b><a class="LinkDef" href=./index.php?ITEM=Admin_Modif_Role&action=Modif&ID='.$ROLE_ID.'>'.stripslashes($ROLE).'</a></b></td>';
      }else{
      	echo '<td align="center"><b>'.stripslashes($ROLE).'</b></td>';
      }
      if($NB_ROLES==0){
        echo '<td align="center">'.$NB_ROLES.'</td>';
      }else{
        echo '<td align="center"><b><a class="LinkDef" href=./index.php?ITEM=Admin_Gestion_Role&action=users&ID='.$ROLE_ID.'>'.$NB_ROLES.'</a></b></td>';
      }
      if($NB_PAGES==0){
        echo'<td align="center">'.$NB_PAGES.'</td>';
      }else{
        echo '<td align="center"><b><a class="LinkDef" href=./index.php?ITEM=Admin_Gestion_Role&action=pages&ID='.$ROLE_ID.'>'.$NB_PAGES.'</a></b></td>';
      }
      echo '</tr>';


    } while ($tab_rq_role = mysql_fetch_assoc($res_rq_role));
    $ligne= mysql_num_rows($res_rq_role);
    if($ligne > 0) {
      mysql_data_seek($res_rq_role, 0);
      $tab_rq_role = mysql_fetch_assoc($res_rq_role);
    }
    mysql_free_result($res_rq_role);
}
    echo '
    <tr align="center" class="titre">
      <td align="center" colspan="3">&nbsp;</td>
    </tr>
  </table>
</div>';
}
mysql_close(); 
?>