<?php
     header('Content-Type: text/html; charset: UTF-8');
     include("../conf/mysql.php");
     $date=$_GET['date'];
     $requete="select * from evenements where evenement_date='".$date."'";
     $ress=mysql_query($requete);
     $retour='';
     if($ress){
               while($liste_evenements=mysql_fetch_assoc($ress)){
                  $retour.=htmlentities($liste_evenements[evenement_comment],ENT_QUOTES).'<br>';
               }
               //on affiche le retour dans la div dédiée à cet effet
               echo $retour;
     }
?>