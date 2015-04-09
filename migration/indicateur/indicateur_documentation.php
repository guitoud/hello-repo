<?PHP
//redirection si acces dirrect
if(substr_count($_SERVER['PHP_SELF'], 'index.php')==0){
  header("Location: ../");
  exit();
}
$Mois_critere=date("m"); 
$Annee_critere=date("Y"); 
if($Mois_critere==1){
	$Mois_critere=12;
	$Annee_critere=$Annee_critere-1;
}else{
	$Mois_critere=$Mois_critere-1;
}
echo '
<div align="center"> 
  <table width="100%" height="20" border="0" cellpadding="0" cellspacing="0" class="in_Contenu">
    <tr>
      <td>	
	<div class="texte_gauche_in_Contenu">
	<center>
	<a class="LinkDef" href=./index.php?ITEM=indicateur_menu_creation>Retour</a></BR>
	</BR>
	<b>Voici le modop pour la r�alisation des indicateurs ODTI iab.</b>
	</center>
    <ul type="circle">
      <li>1 Faire une recherche ODTI telle que champs : (normalement une recherche pr�-enregistr�e existe d�j�) (conserver l\'ordre)</li>
      <ul type="square">
        <li>R�f Intervention</li>	
        <li>Resum�</li>
        <li>Statut Intervention</li>
        <li>Date Creation</li>  	
        <li>Assign� �</li>  
        <li>Date Fin R�elle</li>  
        <li>Date pr�vue</li>  
        <li>Environnement</li>  
        <li>Last Updated</li>  
        <li>Mots cl�s</li>  
        <li>Nature Demande</li>  
        <li>No Demande</li>  
        <li>Temps Pass� (mn)</li>
        <li>Date Creation Dde</li>
        <li>Date D�but R�elle:</li>
        <li>Date M�J Dde</li>
        <li>Date souhait�e:</li>
        <li>Demandeur</li>
        <li>Document Attach�s ?</li>
        <li>Domaine d\'intervention</li>
        <li>Entite MOA:</li>
        <li>Entite MOE:</li>
        <li>Groupe de Travail</li>
        <li>Implantation G�ographique</li>
        <li>Implantation demande:</li>
        <li>Manager Grpe de Travail</li>
        <li>Message</li>
        <li>Nom application</li>	
        <li>Ordre d\'intervention</li>
        <li>Ref Derogation</li>
        <li>Service:</li>
        <li>Telephone:</li>
        <li>Temps Pass� (Assistant jour)</li>
        <li>Temps Pass� (j)</li>
        <li>Description</li>
        <li>Commentaire</li>
      </ul>
    </ul>
&nbsp;filtre : 	</BR>
&nbsp;&nbsp;&nbsp;Domaine d\'intervention UNIX - Application </BR>
&nbsp;&nbsp;&nbsp;Groupe de Travail Bordeaux-DPI-Exploit-Appli </BR>		
&nbsp;&nbsp;&nbsp;Date Creation Dde 1/'.$Mois_critere.'/'.$Annee_critere.' </BR>
</BR>
&nbsp;v�rifier le nombre de r�sultats (1000 par d�faut) � passer � 5000 au moins </BR>
&nbsp;v�rifier que les demandes closes et non closes sont extraites (case � cocher) </BR>				
&nbsp;ouvrir et enregistrer le fichier Excel de r�sultats </BR>
&nbsp;faire un enregistrement au format csv du r�sultat (avec s�parateur de champs ;). </BR>	
</BR>
    <ul type="circle">
      <li>2 Faire la configuration des indicateurs sogeti</li>
      <ul type="square">
        <li>Choisir <a class="LinkDef" href=./index.php?ITEM=indicateur_configuration_sogeti>Configuration des indicateurs</a> dans le <a class="LinkDef" href=./index.php?ITEM=indicateur_menu_creation>Menu</a></li>	
        <li>Faire la s�l�ction de l\'ann�e et du mois des indicateurs (par d�fault la page affiche le mois -1)</li>  
        <li>Faire "S�lection Ann�e Mois"</li>  
        <li>Saisir les valeurs pour les diff�rentes valeurs</li>  
        <li>(par d�faut il y a les valeurs du mois n - 1 ou des valeurs issu de calcul automatique)</li>  
        <li>Faire enfin "Insertion des valeurs"</li>  
      </ul>
    </ul>
    <ul type="circle">
      <li>3 Faire la v�rification des variables de traitement (date de versionning, ...)</li>
      <ul type="square">
        <li>Choisir <a class="LinkDef" href=./index.php?ITEM=indicateur_info_configuration>Information sur la configuration des indicateurs</a> dans le <a class="LinkDef" href=./index.php?ITEM=indicateur_menu_creation>Menu</a></li>	
        <li>Faire la v�rification des valeurs, notamment les dates de versionning</li>
        <li>S\'il faut faire l\'ajout d\'une valeur, faire Ajout</li>
      </ul>
    </ul>
    <ul type="circle">
      <li>4 Faire le traitement du csv</li>
      <ul type="square">
        <li>ATTENTION - il ne faut pas avoir un fichier csv de plus de 2 mo (limitation de la configuration php du serveur)</li>
        <li>Choisir <a class="LinkDef" href=./index.php?ITEM=indicateur_upload>Upload de l\'extration ODTI pour la gestion applicative</a> dans le <a class="LinkDef" href=./index.php?ITEM=indicateur_menu_creation>Menu</a></li>	
        <li>Faire parcourir pour faire la s�lection du fichier csv</li>
        <li>Cliqueur sur "Envoyer le fichier"</li>
        <li>Une nouvelle fen�tre arrive pour faire le traitement</li>  	
        <li>Faire la s�l�ction de l\'ann�e et du mois des indicateurs (par d�fault la page affiche le mois -1)</li>  
        <li>Faire ensuite "Execution du traitement"</li>  
      </ul>
    </ul>
    <ul type="circle">
      <li>5 Exploitation des r�sultats</li>
      <ul type="square">
        <li>Choisir <a class="LinkDef" href=./index.php?ITEM=indicateur_menu>Menu des indicateurs</a> sur la page d\'accueil</li>	
      </ul>
    </ul>
    <center>
    </BR>
    <b>Voici le modop pour la r�alisation des indicateurs QC9 iab.</b>
    </center>


    <ul type="circle">
      <li>1 Faire une recherche <a class="LinkDef" href="http://qualitycenterv9.re.cdc.fr:8080/qcbin/start_a.htm">QC9</a> avec les champs : (conserver l\'ordre)</li>
      <ul type="square">
        <li>No de demande</li>	
        <li>Statut</li>
        <li>Affect� � </li>
        <li>Date souhait�e</li>  	
        <li>Application-Processus</li>  
        <li>Date ex�cution pr�vue</li>  
        <li>Date Transmise </li>  
        <li>Demand� par</li>  
        <li>Environnement</li>  
        <li>Nature demande</li>  
        <li>Recette ?</li>  
        <li>Temps pass� (en jours)</li>  
        <li>Temps pass� (en minutes) </li>  	
      </ul>
    </ul>
    <ul type="circle">
      <li>2 Export du fichier txt</li>
      <ul type="square">
        <li>Dans le menu Anomalies, Faire Exporter, Tous et choisir Document texte</li>   
        <li>Si le fichier txt est trop gros, le r�duire en gardant les 3 derniers mois.</li>   
      </ul>
    </ul>
    <ul type="circle">
      <li>3 Faire le traitement du txt</li>
      <ul type="square">
        <li>ATTENTION - il ne faut pas avoir un fichier txt de plus de 2 mo (limitation de la configuration php du serveur)</li>
        <li>Choisir <a class="LinkDef" href=./index.php?ITEM=indicateur_qc9_upload>Upload de l\'extration QC9 pour la gestion applicative</a> dans le <a class="LinkDef" href=./index.php?ITEM=indicateur_menu_creation>Menu</a></li>	
        <li>Faire parcourir pour faire la s�lection du fichier txt</li>
        <li>Cliqueur sur "Envoyer le fichier"</li>
        <li>Une nouvelle fen�tre arrive pour faire le traitement</li>  	
        <li>Faire la s�l�ction de l\'ann�e et du mois des indicateurs (par d�fault la page affiche le mois -1)</li>  
        <li>Faire ensuite "Execution du traitement"</li>  
      </ul>
    </ul>
    <ul type="circle">
      <li>4 Exploitation des r�sultats</li>
      <ul type="square">
        <li>Choisir <a class="LinkDef" href=./index.php?ITEM=indicateur_menu>Menu des indicateurs</a> sur la page d\'accueil</li>	
      </ul>
    </ul>
    
    <center>
    </BR>
    <b>Voici le modop pour la r�alisation des indicateurs B22.</b>
    </center>

    <ul type="circle">
      <li>1 Faire le traitement B22</li>
      <ul type="square">
        <li>Choisir <a class="LinkDef" href=./index.php?ITEM=indicateur_b22_traitement>Cr�ation du B22 pour la gestion applicative</a> dans le <a class="LinkDef" href=./index.php?ITEM=indicateur_menu_creation>Menu</a></li>	  	
        <li>Faire la s�l�ction de l\'ann�e et du mois des indicateurs (par d�fault la page affiche le mois -1)</li>  
        <li>Faire ensuite "Execution du traitement"</li>  
      </ul>
    </ul>
    <ul type="circle">
      <li>2 Exploitation des r�sultats</li>
      <ul type="square">
        <li>Choisir <a class="LinkDef" href=./index.php?ITEM=indicateur_menu>Menu des indicateurs</a> sur la page d\'accueil</li>	
      </ul>
    </ul>
    
    <center>
    </BR>
    <b>Information sur la s�lection des donn�es.</b>
    </center>

    <ul type="circle">
      <li>Info Liste des colonnes de la table des calculs ODTI</li>
      <ul type="square">
        <li>REF : reference ODTI (5 chiffres)</li>	
        <li>DATE : date de fin realisation ou sinon date de derniere maj ODTI</li>
        <li>ACTION : L (livraison) M (modification IAB) T (traitement exceptionnel) D (demande MOA) I (inconnue)</li>
        <li>STATUS : V valid�e</li>  	
        <li>ACTEUR : tri-gramme</li>  
        <li>ENV : E (exploitation)</li>  
        <li>NATURE : A (action ponctuelle) V (mep en version) H (mep hors version)</li>  
        <li>DEMANDE : reference de la demande ODTI</li>  
        <li>DUREE : duree de realisation en minutes</li>  
        <li>DATE_ANNEE : annee</li>  
        <li>DATE_MOIS : mois</li>  
        <li>DATE_SEMAINE : numero de la semaine</li>  
        <li>DATE_JOUR : jour du mois</li>  	
        <li>DATE_JOURSEM : jour de la semaine</li>  
        <li>DATE_PREVUE : date prevue demandee</li>  
        <li>EN_VERSION : date prevue fait partie des Versions (V, F)</li>  
        <li>EN_HVP : date prevue fait partie des Hors-Versions planifies (V, F)</li>  
        <li>EN_HVH : date prevue fait partie des Hors-Versions hebdomadaire (V, F)</li>  
        <li>APPLI : code application en 3 caracteres</li>  
        <li>DATE_MOIS : mois</li>  
        <li>SOGETI : acteur Sogeti (Y, N)</li>  
        <li>POIDS : poids de le demande (1, 4, 8) en fonction du temps</li>  
        <li>NIVEAU : niveau de la demande (Simple, Moyenne, Complexe)</li>  
        <li>VALEUR : poids de la demande en fonction du type</li>    
        <li>INDICATEUR_REGLE_ID : information sur la regle du calcul</li>        
        <li>DATE_INDICATEUR : date des indicateurs</li>  
        <li>ACTION_NEW : L (livraison) L_LUP (livraison LUP) L_VERS (livraison versionning) M (modification IAB) T (traitement exceptionnel) D (demande MOA) I (inconnue)</li>  
        <li>INDICATEUR_ACTION_ID : information sur l\'action du calcul</li>  
      </ul>
    </ul>
    <ul type="circle">
      <li>Info Liste des colonnes de la table des calculs QC9</li>
      <ul type="square">
        <li>NUMERO : reference QC9</li>	
        <li>STATUS : Status de la demande (les stats sont sur les 6- Termin�e)</li>
        <li>AFFECTE : Nom de l\'intervenant</li>
        <li>SOGETI : Y si l\'invervemant est Sogeti sinon F</li>  	
        <li>DATE_SOUHAITEE : Date souhait�e de la demande</li>  
        <li>APPLICATION : Nom de l\'application sur laquelle il faut faire la demande</li>  
        <li>PROCESSUS : Nom du processus de l\'application</li>  
        <li>DATE_PREVUE : Date ex�cution pr�vue</li>  
        <li>DATE_TRANSMISE : Date Transmise</li>  
        <li>DEMANDEUR : Nom du demandeur</li>  
        <li>ENVIRONNEMENT : Environnement sur lequelle il faut faire la demande</li>  
        <li>NATURE : Type de demande</li>  
        <li>RECETTE : La demande a-t-elle connu une recette</li>  	
        <li>TEMPS_JOURS : Dur�e de traitement en jours de la demande</li>  
        <li>TEMPS_MINUTES : Dur�e de traitement en minutes de la demande</li>  
        <li>TEMPS_MINUTES_OK : Dur�e de traitement en minutes corrig�e de la demande</li>  
        <li>VALEUR : poids de la demande</li>  
        <li>DATE_INDICATEUR : date des indicateurs</li>  
      </ul>
    </ul>
	<center>
	</BR>
	</BR>
	<a class="LinkDef" href=./index.php?ITEM=indicateur_menu_creation>Retour</a>
	</center>
	</div>
      <td>	
    </tr>
  </table>
</div>';
?>