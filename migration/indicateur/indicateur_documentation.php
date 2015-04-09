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
	<b>Voici le modop pour la réalisation des indicateurs ODTI iab.</b>
	</center>
    <ul type="circle">
      <li>1 Faire une recherche ODTI telle que champs : (normalement une recherche pré-enregistrée existe déjà) (conserver l\'ordre)</li>
      <ul type="square">
        <li>Réf Intervention</li>	
        <li>Resumé</li>
        <li>Statut Intervention</li>
        <li>Date Creation</li>  	
        <li>Assigné à</li>  
        <li>Date Fin Réelle</li>  
        <li>Date prévue</li>  
        <li>Environnement</li>  
        <li>Last Updated</li>  
        <li>Mots clés</li>  
        <li>Nature Demande</li>  
        <li>No Demande</li>  
        <li>Temps Passé (mn)</li>
        <li>Date Creation Dde</li>
        <li>Date Début Réelle:</li>
        <li>Date MàJ Dde</li>
        <li>Date souhaitée:</li>
        <li>Demandeur</li>
        <li>Document Attachés ?</li>
        <li>Domaine d\'intervention</li>
        <li>Entite MOA:</li>
        <li>Entite MOE:</li>
        <li>Groupe de Travail</li>
        <li>Implantation Géographique</li>
        <li>Implantation demande:</li>
        <li>Manager Grpe de Travail</li>
        <li>Message</li>
        <li>Nom application</li>	
        <li>Ordre d\'intervention</li>
        <li>Ref Derogation</li>
        <li>Service:</li>
        <li>Telephone:</li>
        <li>Temps Passé (Assistant jour)</li>
        <li>Temps Passé (j)</li>
        <li>Description</li>
        <li>Commentaire</li>
      </ul>
    </ul>
&nbsp;filtre : 	</BR>
&nbsp;&nbsp;&nbsp;Domaine d\'intervention UNIX - Application </BR>
&nbsp;&nbsp;&nbsp;Groupe de Travail Bordeaux-DPI-Exploit-Appli </BR>		
&nbsp;&nbsp;&nbsp;Date Creation Dde 1/'.$Mois_critere.'/'.$Annee_critere.' </BR>
</BR>
&nbsp;vérifier le nombre de résultats (1000 par défaut) à passer à 5000 au moins </BR>
&nbsp;vérifier que les demandes closes et non closes sont extraites (case à cocher) </BR>				
&nbsp;ouvrir et enregistrer le fichier Excel de résultats </BR>
&nbsp;faire un enregistrement au format csv du résultat (avec séparateur de champs ;). </BR>	
</BR>
    <ul type="circle">
      <li>2 Faire la configuration des indicateurs sogeti</li>
      <ul type="square">
        <li>Choisir <a class="LinkDef" href=./index.php?ITEM=indicateur_configuration_sogeti>Configuration des indicateurs</a> dans le <a class="LinkDef" href=./index.php?ITEM=indicateur_menu_creation>Menu</a></li>	
        <li>Faire la séléction de l\'année et du mois des indicateurs (par défault la page affiche le mois -1)</li>  
        <li>Faire "Sélection Année Mois"</li>  
        <li>Saisir les valeurs pour les différentes valeurs</li>  
        <li>(par défaut il y a les valeurs du mois n - 1 ou des valeurs issu de calcul automatique)</li>  
        <li>Faire enfin "Insertion des valeurs"</li>  
      </ul>
    </ul>
    <ul type="circle">
      <li>3 Faire la vérification des variables de traitement (date de versionning, ...)</li>
      <ul type="square">
        <li>Choisir <a class="LinkDef" href=./index.php?ITEM=indicateur_info_configuration>Information sur la configuration des indicateurs</a> dans le <a class="LinkDef" href=./index.php?ITEM=indicateur_menu_creation>Menu</a></li>	
        <li>Faire la vérification des valeurs, notamment les dates de versionning</li>
        <li>S\'il faut faire l\'ajout d\'une valeur, faire Ajout</li>
      </ul>
    </ul>
    <ul type="circle">
      <li>4 Faire le traitement du csv</li>
      <ul type="square">
        <li>ATTENTION - il ne faut pas avoir un fichier csv de plus de 2 mo (limitation de la configuration php du serveur)</li>
        <li>Choisir <a class="LinkDef" href=./index.php?ITEM=indicateur_upload>Upload de l\'extration ODTI pour la gestion applicative</a> dans le <a class="LinkDef" href=./index.php?ITEM=indicateur_menu_creation>Menu</a></li>	
        <li>Faire parcourir pour faire la sélection du fichier csv</li>
        <li>Cliqueur sur "Envoyer le fichier"</li>
        <li>Une nouvelle fenêtre arrive pour faire le traitement</li>  	
        <li>Faire la séléction de l\'année et du mois des indicateurs (par défault la page affiche le mois -1)</li>  
        <li>Faire ensuite "Execution du traitement"</li>  
      </ul>
    </ul>
    <ul type="circle">
      <li>5 Exploitation des résultats</li>
      <ul type="square">
        <li>Choisir <a class="LinkDef" href=./index.php?ITEM=indicateur_menu>Menu des indicateurs</a> sur la page d\'accueil</li>	
      </ul>
    </ul>
    <center>
    </BR>
    <b>Voici le modop pour la réalisation des indicateurs QC9 iab.</b>
    </center>


    <ul type="circle">
      <li>1 Faire une recherche <a class="LinkDef" href="http://qualitycenterv9.re.cdc.fr:8080/qcbin/start_a.htm">QC9</a> avec les champs : (conserver l\'ordre)</li>
      <ul type="square">
        <li>No de demande</li>	
        <li>Statut</li>
        <li>Affecté à </li>
        <li>Date souhaitée</li>  	
        <li>Application-Processus</li>  
        <li>Date exécution prévue</li>  
        <li>Date Transmise </li>  
        <li>Demandé par</li>  
        <li>Environnement</li>  
        <li>Nature demande</li>  
        <li>Recette ?</li>  
        <li>Temps passé (en jours)</li>  
        <li>Temps passé (en minutes) </li>  	
      </ul>
    </ul>
    <ul type="circle">
      <li>2 Export du fichier txt</li>
      <ul type="square">
        <li>Dans le menu Anomalies, Faire Exporter, Tous et choisir Document texte</li>   
        <li>Si le fichier txt est trop gros, le réduire en gardant les 3 derniers mois.</li>   
      </ul>
    </ul>
    <ul type="circle">
      <li>3 Faire le traitement du txt</li>
      <ul type="square">
        <li>ATTENTION - il ne faut pas avoir un fichier txt de plus de 2 mo (limitation de la configuration php du serveur)</li>
        <li>Choisir <a class="LinkDef" href=./index.php?ITEM=indicateur_qc9_upload>Upload de l\'extration QC9 pour la gestion applicative</a> dans le <a class="LinkDef" href=./index.php?ITEM=indicateur_menu_creation>Menu</a></li>	
        <li>Faire parcourir pour faire la sélection du fichier txt</li>
        <li>Cliqueur sur "Envoyer le fichier"</li>
        <li>Une nouvelle fenêtre arrive pour faire le traitement</li>  	
        <li>Faire la séléction de l\'année et du mois des indicateurs (par défault la page affiche le mois -1)</li>  
        <li>Faire ensuite "Execution du traitement"</li>  
      </ul>
    </ul>
    <ul type="circle">
      <li>4 Exploitation des résultats</li>
      <ul type="square">
        <li>Choisir <a class="LinkDef" href=./index.php?ITEM=indicateur_menu>Menu des indicateurs</a> sur la page d\'accueil</li>	
      </ul>
    </ul>
    
    <center>
    </BR>
    <b>Voici le modop pour la réalisation des indicateurs B22.</b>
    </center>

    <ul type="circle">
      <li>1 Faire le traitement B22</li>
      <ul type="square">
        <li>Choisir <a class="LinkDef" href=./index.php?ITEM=indicateur_b22_traitement>Création du B22 pour la gestion applicative</a> dans le <a class="LinkDef" href=./index.php?ITEM=indicateur_menu_creation>Menu</a></li>	  	
        <li>Faire la séléction de l\'année et du mois des indicateurs (par défault la page affiche le mois -1)</li>  
        <li>Faire ensuite "Execution du traitement"</li>  
      </ul>
    </ul>
    <ul type="circle">
      <li>2 Exploitation des résultats</li>
      <ul type="square">
        <li>Choisir <a class="LinkDef" href=./index.php?ITEM=indicateur_menu>Menu des indicateurs</a> sur la page d\'accueil</li>	
      </ul>
    </ul>
    
    <center>
    </BR>
    <b>Information sur la sélection des données.</b>
    </center>

    <ul type="circle">
      <li>Info Liste des colonnes de la table des calculs ODTI</li>
      <ul type="square">
        <li>REF : reference ODTI (5 chiffres)</li>	
        <li>DATE : date de fin realisation ou sinon date de derniere maj ODTI</li>
        <li>ACTION : L (livraison) M (modification IAB) T (traitement exceptionnel) D (demande MOA) I (inconnue)</li>
        <li>STATUS : V validée</li>  	
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
        <li>STATUS : Status de la demande (les stats sont sur les 6- Terminée)</li>
        <li>AFFECTE : Nom de l\'intervenant</li>
        <li>SOGETI : Y si l\'invervemant est Sogeti sinon F</li>  	
        <li>DATE_SOUHAITEE : Date souhaitée de la demande</li>  
        <li>APPLICATION : Nom de l\'application sur laquelle il faut faire la demande</li>  
        <li>PROCESSUS : Nom du processus de l\'application</li>  
        <li>DATE_PREVUE : Date exécution prévue</li>  
        <li>DATE_TRANSMISE : Date Transmise</li>  
        <li>DEMANDEUR : Nom du demandeur</li>  
        <li>ENVIRONNEMENT : Environnement sur lequelle il faut faire la demande</li>  
        <li>NATURE : Type de demande</li>  
        <li>RECETTE : La demande a-t-elle connu une recette</li>  	
        <li>TEMPS_JOURS : Durée de traitement en jours de la demande</li>  
        <li>TEMPS_MINUTES : Durée de traitement en minutes de la demande</li>  
        <li>TEMPS_MINUTES_OK : Durée de traitement en minutes corrigée de la demande</li>  
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