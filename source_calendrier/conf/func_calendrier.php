<?php
     //Quelques fonctions utiles pour la gestion d'un calendrier
     
     //Transforme un num�ro de mois en son nom en fran�ais
     function getMois($mois){
       $mois=sprintf("%d",$mois);
       $tab_mois=array(1=>"Janvier","F�vrier","Mars","Avril","Mai","Juin","Juillet","Ao�t","Septembre","Octobre","Novembre","D�cembre");
       return $tab_mois[$mois];
     }
     
     //fonction qui renvoit le mois et l'ann�e  dans le calendrier en fonction du pas
     //un pas de 1 signifie la date pour le mois suivant celui pass� en param�tre
     //un pas de -1 signifie la date pour le mois pr�c�dent celui pass� en param�tre
     function getSuivant($mois,$annee,$pas){
       $tmstp_suivant=mktime(0,0,0,($mois+$pas),1,$annee);
       $date_suivante[mois]=date("m",$tmstp_suivant);
       $date_suivante[annee]=date("Y",$tmstp_suivant);
       return $date_suivante;
     }
     
     //fonction qui retourne le premier jour du mois
     //0->Lundi
     //1->Mardi
     //...
     //6->Dimanche
     function getFirstDay($mois,$annee){
       $tmstp=mktime(0,0,0,$mois,1,$annee);
       //on r�cup�re le num�ro du jour de la semaine... mais au format anglais (0->dimanche, ...) mais
       //nous on veut un calendrier commen�ant le lundi !!
       $jour=date("w",$tmstp);

       $tab_jour=array(0=>7,1=>1,2=>2,3=>3,4=>4,5=>5,6=>6);
       
       return $tab_jour[$jour];
     }
?>