<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Exemple d'application AJAX avec openrico et prototype</title>
      <?php
      include("./conf/mysql.php");
      ?>
      <!-- Feuille de style -->
      <link rel="stylesheet" type="text/css" href="./css/calendrier.css">

      <!-- On inclut la librairie openrico / prototype -->
      <script src="./js/rico/src/prototype.js" type="text/javascript"></script>
      <!-- script pour gérer les bords arrondis et le panel déroulant -->
      <script src="./js/rico/src/rico.js" type="text/javascript"></script>
      <script src="./js/rico/src/ricoStyles.js" type="text/javascript"></script>
      <script src="./js/rico/src/ricoEffects.js" type="text/javascript"></script>
      <script src="./js/function.js" type="text/javascript"></script>
      <script src="./js/rico/src/ricoComponents.js" type="text/javascript"></script>
      <script type="text/javascript">
              function roundMe() {
                       $$('div.conteneur').each(function(e){Rico.Corner.round(e)});
              }
      </script>



</head>
<body>

      <!-- on crée l'élément "calendrier" dans lequel va s'afficher dynamiquement le calendrier-->

      <script>tableau(<?php echo date("m");?>,<?php echo date("Y");?>);</script>
      <div id="calendrier" class="conteneur calendrier" style="width:260px;background-color:#c6c3de;">
      <table class="tab_calendrier" align="center">
             <tr><td class="titre_calendrier" colspan="7" width="100%"><a id="link_precedent" href="#"><img src="./images/previous.png"></a> <a id="link_suivant" href="#"><img src="./images/next.png"></a> <span id="titre"></span> </td></tr>
             <tr>
                 <td  class="cell_calendrier" >
                 Lun
                 </td>
                 <td  class="cell_calendrier" >
                 Mar.
                 </td>
                 <td  class="cell_calendrier">
                 Mer.
                 </td>
                 <td  class="cell_calendrier">
                 Jeu.
                 </td>
                 <td  class="cell_calendrier" >
                 Ven.
                 </td>
                 <td  class="cell_calendrier">
                 Sam.
                 </td>
                 <td  class="cell_calendrier">
                 Dim.
                 </td>

             </tr>
             <?php
             $compteur_lignes=0;
             $total=1;
             while($compteur_lignes<6){
                echo '<tr>';
                $compteur_colonnes=0;
                while($compteur_colonnes<7){
                   echo '<td id="'.$total.'" class="cell_calendrier" >';
                   echo '</td>';
                   $compteur_colonnes++;
                   $total++;
                }
                echo '</tr>';
                $compteur_lignes++;
             }
             ?>
      </table>
      </div>

      <div style="position: relative; width: 260px;">
          <div id="top-panel" style="background-color : #9791cb;position: relative;width: 260px;z-index: 1500;">
              <a class="voir_plus" href="javascript:void(0);" id="code-button" onclick="PullDown.panel.toggle(); return false;">
                  + Voir les évènements
              </a>
          </div>
          <div id="main-part">
          	<div id="outer_panel" class="panel-top" style="overflow: hidden; position: absolute; z-index: 1600;top: 19px; width: 260px;height: 132px;">
          		<div style="position: relative;top: 1px;background-color: #c6c3de;margin:0px;border: 1px solid #9791cb;" id="inner_panel">
          			    <div id="Evenements" style="height:150px">
                                    </div>

                        </form>
           	</div>
          </div>
      </div>





      <!-- Appel de la fonction qui va arrondir le conteneur du calendrier et des évènements pour le panel déroulant -->
      <script>
              javascript:roundMe()
              Event.observe(window, 'load', function(){
                   PullDown.panel = Rico.SlidingPanel.top( $('outer_panel'), $('inner_panel'));
              })
              var PullDown = {};
      </script>
      <br>
      <br>










</body>
</html>