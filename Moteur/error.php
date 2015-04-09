<?PHP
echo ' 
<!--D&eacute;but page HTML --> 
<div align="center"> 
  <table width="100%" height="20" border="0" cellpadding="0" cellspacing="0" class="in_Contenu">
    <tr>
      <td>	
	      <div class="titre_in_Contenu">
			      	Bienvenue sur le Portail du service OPERATIONS de la DDR
	      </div>
      <td>	
    </tr>
    <tr>
      <td>	
	      <div class="texte_gauche_in_Contenu">
	      	La page '.$_GET["ITEM"].' n\'existe pas ou vous n\'avez pas les droits sur celle-ci.
	      	</BR>
	      	</BR>
	      	</BR>
	      	<div align="center">
	      		Merci de faire une demande d\'acces par mail en cliquant sur l\'enveloppe<br/><br/>
	      		<a class="LinkDef" href="mailto:mathieu.bordenave@caissedesdepots.fr;Vincent.Guibert-e@caissedesdepots.fr?subject=[Portail] - acces '.$_GET['ITEM'].'&body=Bonjour,"><img src="img/enveloppe.gif" border="0" height="30"></a>
	        </div>

	    </td>
    </tr>
  </table>
</div>';
?>