<?PHP
//redirection si acces dirrect
if(substr_count($_SERVER['PHP_SELF'], 'index.php')==0){
  header("Location: ../");
  exit();
}
/*******************************************************************
   Interface procedure changement
   Version 1.0.0  
  25/03/2010 - VGU - Creation fichier
**************************************************Guibert Vincent***
*******************************************************************/
echo '
<div align="center"> 
  <table width="100%" height="20" border="0" cellpadding="0" cellspacing="0" class="in_Contenu">
    <tr>
      <td>	
	<div class="texte_gauche_in_Contenu">
	<center>
	<b>Voici la Procédure pour faire une déclaration de changement.</b>
	</center>
	</BR>
	en cours de rédaction ....
	</BR>
	</center>
	</div>
      <td>	
    </tr>
  </table>
</div>';
?>