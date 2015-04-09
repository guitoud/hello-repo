<?php session_start();

if (empty($_SESSION['utilisateur']) and empty($_SESSION['droit'])) {
        header('Location: login.php');
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
 	"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Cahier d'exploitation</title>
<link rel="icon" type="image/png" href="chg.PNG" />

<!-- GRID -->
	<link rel='STYLESHEET' type='text/css' href='dhtmlxGrid/codebase/dhtmlxgrid.css'>
	<script src='dhtmlxGrid/codebase/dhtmlxcommon.js'></script>
	<script src='dhtmlxGrid/codebase/dhtmlxgrid.js'></script>	
	<script src='dhtmlxGrid/codebase/ext/dhtmlxgrid_srnd.js'></script>	
	<script src='dhtmlxGrid/codebase/ext/dhtmlxgrid_filter.js'></script>	
	<script src='dhtmlxGrid/codebase/dhtmlxgridcell.js'></script>
<!-- LAYOUT -->	
	<link rel='STYLESHEET' type='text/css' href='dhtmlxLayout/codebase/dhtmlxlayout.css'>
	<link rel="stylesheet" type="text/css" href="dhtmlxLayout/codebase/skins/dhtmlxlayout_dhx_skyblue.css">
	<script src='dhtmlxLayout/codebase/dhtmlxcommon.js'></script>
	<script src='dhtmlxLayout/codebase/dhtmlxlayout.js'></script>	
	<script src='dhtmlxLayout/codebase/dhtmlxcontainer.js'></script>
<!-- TABBAR -->	
	<link rel="STYLESHEET" type="text/css" href="dhtmlxTabbar/codebase/dhtmlxtabbar.css">
	<script  src="dhtmlxTabbar/codebase/dhtmlxcommon.js"></script>
	<script  src="dhtmlxTabbar/codebase/dhtmlxtabbar.js"></script>
<!-- TREE -->
	<link rel="STYLESHEET" type="text/css" href="dhtmlxTree/codebase/dhtmlxtree.css">
	<script  src="dhtmlxTree/codebase/dhtmlxcommon.js"></script>
	<script  src="dhtmlxTree/codebase/dhtmlxtree.js"></script>
<!-- TOOLBAR -->
	<link rel="STYLESHEET" type="text/css" href="dhtmlxToolbar/codebase/skins/dhtmlxtoolbar_dhx_skyblue.css">
	<script  src="dhtmlxToolbar/codebase/dhtmlxcommon.js"></script>
	<script  src="dhtmlxToolbar/codebase/dhtmlxtoolbar.js"></script>
<!-- FORM -->
	<link rel="STYLESHEET" type="text/css" href="dhtmlxForm/codebase/skins/dhtmlxform_dhx_skyblue.css">
	<script  src="dhtmlxForm/codebase/dhtmlxcommon.js"></script>
	<script  src="dhtmlxForm/codebase/dhtmlxform.js"></script>
<!-- CONNECTEUR PHP -->
	<script src="codebase/connector.js" type="text/javascript" charset="utf-8"></script>
<!-- EDITOR -->
        <link rel="STYLESHEET" type="text/css" href="dhtmlxEditor/codebase/skins/dhtmlxeditor_dhx_skyblue.css">
        <script  src="dhtmlxEditor/codebase/dhtmlxcommon.js"></script>
        <script  src="dhtmlxEditor/codebase/dhtmlxeditor.js"></script>
<!-- WINDOWS -->
        <link rel="STYLESHEET" type="text/css" href="dhtmlxWindows/codebase/skins/dhtmlxwindows_dhx_skyblue.css">
        <link rel="STYLESHEET" type="text/css" href="dhtmlxWindows/codebase/dhtmlxwindows.css">
        <script  src="dhtmlxWindows/codebase/dhtmlxcommon.js"></script>
        <script  src="dhtmlxWindows/codebase/dhtmlxcontainer.js"></script>
        <script  src="dhtmlxWindows/codebase/dhtmlxwindows.js"></script>


<!-- todo, mettre dans un CSS -->	
	<style>
		html, body {
			width: 100%;
			height: 100%;
			margin: 0px;
			padding: 0px;
			overflow: hidden;
		}
	</style>
	
</head>

<body>
<!-- structure des DIV -->	
	<div id="div_gridServeur_tab_layout_a" style="width:100%; height:100%;"></div>
	<div id="div_treeAppli_tab_layout_a" style="width:100%; height:100%;"></div>
	<div id="div_layout3L_tabbar" style="width:100%; height:100%;"></div>
	<div id="div_tabbar_modif" style="width:100%; height:100%;"></div>
	<div id="div_tabbar_modif_serveur" style="width:600px;
        height:400px;margin:20px;border-width: 1px; border-style: solid; border-color:#8db6cd"></div>
	<div id="div_tabbar_modif_appli" style="width:600px;
                                            height:400px;margin:20px;border-width: 1px; border-style: solid; border-color:#8db6cd"></div>
	<div id="div_tabbar_modif_asso" style="width:600px;
                                           height:400px;margin:20px;border-width: 1px; border-style: solid; border-color:#8db6cd"></div>
	<div id="div_grid1_layout_b_vide" style="width:100%;
                                             height:100%;"><font face="verdana" size=1> -- Aucune fiche disponible  --</font></div>
	<div id="div_grid1_layout_b" style="width:100%; height:100%;"></div>
	<div id="div_tabbarModif" style="width:100%; height:100%;"></div>
	<div id="div_tdb" style="width:100%; height:100%;"></div>
	<div id="formPanneauBas" style="width:auto; height:99%;overflow:auto;border-width: 2px; border-style: solid; border-color: white;"></div>



<script>
// ##### variable globale ####-->
var nbFiche = 0;
var ficheSelectionner = 0;
var serveurSelectionner;
var editor;
var creationFiche = 0;
var nbFenetreAsso = 0;
var Obj = new Date;
var Jour = Obj.getDate();
var Mois = Obj.getMonth()+1;
var Annee = Obj.getFullYear();
var datedujour = Jour + "." + Mois + "." + Annee;
var assoServeur;
var assoAppli;
var assoAppliFinal;
</script>
<?php
	print("<script>var utilisateur='".$_SESSION['utilisateur']."'</script>");
?>
<script>

// ##### FONCTION JAVASCRIPT ####-->
	function getSelectValue(selectId)
	{
        	var elmt = document.getElementById(selectId);
        	if(elmt.multiple == false)
        	{
                	return elmt.options[elmt.selectedIndex].value;
        	}
        	var values = new Array();
        	for(var i=0; i< elmt.options.length; i++)
        	{
                	if(elmt.options[i].selected == true)
                	{
                        	values[values.length] = elmt.options[i].value;
                	}
        	}
        	return values;
	}	

	function dupliquer(source,destination)
	{
                                var req = null;
                                if (window.XMLHttpRequest)
                                {
                                req = new XMLHttpRequest();
                                if (req.overrideMimeType)
                                        {
                                                req.overrideMimeType('text/xml');
                                        }
                                }
                                else if (window.ActiveXObject)
                                {
                                        try {
                                                req = new ActiveXObject("Msxml2.XMLHTTP");
                                        } catch (e)
                                        {
                                                try {
                                                        req = new ActiveXObject("Microsoft.XMLHTTP");
                                                } catch (e) {}
                                        }
                                }

                                req.onreadystatechange = function()
                                {
                                if(req.readyState == 4)
                                        {
                                                if(req.status == 200)
                                                {
                                                        document.getElementById("formPanneauBas").innerHTML = "";
                        				document.getElementById("formPanneauBas").style.border = "2px solid white";
			                                toolbar_layout_c.disableItem("toolbar_c_sauvegarder");
                        			        toolbar_layout_c.disableItem("toolbar_c_annuler");
                                			toolbar_layout_c.enableItem("toolbar_c_modifier");
                                			toolbar_layout_c.enableItem("toolbar_c_dupliquer");
                                			toolbar_layout_c.enableItem("toolbar_c_supprimer");
                                			toolbar_layout_b.enableItem("toolbar_b_imprimer");
                                			toolbar_layout_c.enableItem("toolbar_c_imprimer");
							Eve_gridServeur(serveurSelectionner);
							gridListFiche_event(ficheSelectionner,"consul");
                                                }
                                        }
                                }
                                req.open("GET", "sauvDupli.php?src="+ficheSelectionner+"&dest="+ficheSelectionner, true);
	                        req.send(null);

	}

        function toolbar_m_event(id_button)
        {

// NOUVEAU SERVEUR
        if (id_button == "toolbar_modif_nouveau")
                {
                reponse = prompt('Nom du nouveau serveur ?');
                if (reponse)
                        {
			modif_BDD("nouveau","serveur","null",reponse)
                        }
                }
// NOUVELLE APPLI
	        if (id_button == "toolbar_modif_nouveau_appli")
                {
                reponse = prompt('Nom de la nouvelle application ?');
                if (reponse)
                        {
                        modif_BDD("nouveau","application","null",reponse)
                        }
                }
// BOUTON ANNULER
        if ((id_button == "toolbar_modif_annuler") || (id_button == "toolbar_modif_annuler_appli") || (id_button == "toolbar_modif_annuler_asso"))
                {
		dhxLayout.cells("c").view("def").setActive();
                }
	
// BOUTON ANNULER
        if (id_button == "toolbar_modif_renommer")
                {
		serveur_en_cours=gridServeur_tabModif.cells(gridServeur_tabModif.getSelectedId(),gridServeur_tabModif.getSelectedCellIndex()).getValue();
		reponse = prompt('Renommer le serveur '+serveur_en_cours+' ?',serveur_en_cours);
		if (reponse)
			{
			modif_BDD("renommer","serveur",serveur_en_cours,reponse)
			}
                }
        if (id_button == "toolbar_modif_renommer_appli")
                {
		appli_en_cours=gridAppli_tabModif.cells(gridAppli_tabModif.getSelectedId(),gridAppli_tabModif.getSelectedCellIndex()).getValue();
                reponse = prompt('Renommer l\'application '+appli_en_cours+' ?',appli_en_cours);
                if (reponse)
                        {
                        modif_BDD("renommer","application",appli_en_cours,reponse);
                        }
                }

// BOUTON SUPPRIMER
	if (id_button == "toolbar_modif_supprimer")
                {
		serveur_en_cours=gridServeur_tabModif.cells(gridServeur_tabModif.getSelectedId(),gridServeur_tabModif.getSelectedCellIndex()).getValue();
                reponse = confirm("Confirmez vous la suppression du serveur "+serveur_en_cours+" ?");
		if (reponse)
			{
			modif_BDD("supprimer","serveur",serveur_en_cours,serveur_en_cours);
			}
                }

        if (id_button == "toolbar_modif_supprimer_appli")
                {
                appli_en_cours=gridAppli_tabModif.cells(gridAppli_tabModif.getSelectedId(),gridAppli_tabModif.getSelectedCellIndex()).getValue();
                reponse = confirm("Confirmez vous la suppression de l'application "+appli_en_cours+" ?");
                if (reponse)
                        {
                        modif_BDD("supprimer","application",appli_en_cours,appli_en_cours);
                        }
                }
                if (id_button == "toolbar_modif_supprimer_asso")
                {
                        assoServeur=gridAsso_tabModif.cells(gridAsso_tabModif.getSelectedId(),0).getValue();
                        assoAppli=gridAsso_tabModif.cells(gridAsso_tabModif.getSelectedId(),1).getValue();
			if(confirm("Supprimer l'association "+assoServeur+" / "+assoAppliFinal+" ?"))
			{
				modif_BDD("suppasso",assoServeur,assoAppli);
			}
                }


// BOUTON MODIFIER
	        if (id_button == "toolbar_modif_modifier_asso")
                {
			association(assoServeur,assoAppli);
                }
		if (id_button == "toolbar_modif_annuler_appli_fenetre")
		{
			dhxWins.window("fenetreAlerte").close();
			nbFenetreAsso = 0;
		}
		if (id_button == "toolbar_modif_modifier_asso_fenetre")
		{
			assoAppliFinal=gridfenetre.cells(gridfenetre.getSelectedId(),0).getValue();
			if(confirm("associer le serveur "+assoServeur+" avec l'application "+assoAppliFinal+" ?"))
			{
				if (assoAppli == "")
				{
					assoAppli = "empty";
				}
				modif_BDD("associer",assoServeur,assoAppli,assoAppliFinal);
				dhxWins.window("fenetreAlerte").close();
				nbFenetreAsso = 0;
			}
		}
        }

// ------------------------------

        function association(asso_serveur_en_cours,asso_appli_en_cours){
			if(nbFenetreAsso==0){
				nbFenetreAsso = 1;
                                var fenetreSuppAlerte = dhxWins.createWindow("fenetreAlerte",0 ,0, 300, 400);
                                dhxWins.window("fenetreAlerte").center();
                                dhxWins.window("fenetreAlerte").denyResize();
                                dhxWins.window("fenetreAlerte").denyPark();
				dhxWins.window("fenetreAlerte").button("close").attachEvent("onClick", function(){nbFenetreAsso=0;alert("debug")});
                                fenetreSuppAlerte.button("close").disable();
                                fenetreSuppAlerte.setText("Serveur : "+asso_serveur_en_cours+" / "+asso_appli_en_cours);
				dhxWins.window("fenetreAlerte").attachURL("fenetreAsso.html", true);
			}
        }

	function modif_BDD(action,type,source,cible)
	{
                                var req = null;
                                if (window.XMLHttpRequest)
                                {
                                req = new XMLHttpRequest();
                                if (req.overrideMimeType)
                                        {
                                                req.overrideMimeType('text/xml');
                                        }
                                }
                                else if (window.ActiveXObject)
                                {
                                        try {
                                                req = new ActiveXObject("Msxml2.XMLHTTP");
                                        } catch (e)
                                        {
                                                try {
                                                        req = new ActiveXObject("Microsoft.XMLHTTP");
                                                } catch (e) {}
                                        }
                                }

                                req.onreadystatechange = function()
                                {
                                if(req.readyState == 4)
                                        {
                                                if(req.status == 200)
                                                {
							if(req.responseText == "ko")
							{
								alert("Le nom est deja utilise !");
							}
							else if(req.responseText == "serveurAppli")
							{
								alert("Impossible de supprimer le serveur car il est associe a une application");
							}	
							else
							{
								modif_BDD_refresh();
							}
                                                }
                                        }
                                }
                                req.open("GET", "gerer.php?action="+action+"&type="+type+"&source="+source+"&cible="+cible, true);
                                req.send(null);

	}

	function modif_BDD_refresh()
	{
		gridServeur_tabModif.clearAll();
		gridServeur_tabModif.loadXML("grid_gerer_serveur.php?connector=true&dhx_sort[0]=asc");
		gridAppli_tabModif.clearAll();
		gridAppli_tabModif.loadXML("grid_gerer_appli.php?connector=true&dhx_sort[0]=asc");
		gridAsso_tabModif.clearAll();
		gridAsso_tabModif.loadXML("gridServeur_tab.php?connector=true&dhx_sort[0]=asc");
		gridServeur_tab.clearAll();
		gridServeur_tab.loadXML("gridServeur_tab.php?connector=true&dhx_sort[1]=asc");
		Eve_gridServeur(serveurSelectionner);
		treeAppli_tab.deleteChildItems(0);
		treeAppli_tab.loadXML("treeAppli_tab.php");
	}


        function toolbar_b_event(id_button)
        {
	if (id_button == "toolbar_b_fermer_tdb")
		{
		dhxLayout.cells("b").view("def").setActive();
		}
	if (id_button == "toolbar_b_tdb")
		{
		Eve_gridServeur("1","oui");
		dhxLayout.cells("b").view("tdb").setActive();
		}
	if (id_button == "toolbar_b_imprimer")
		{
		alert("fonction indisponible");
		}	

	if (id_button == "toolbar_b_parametrer")
		{
		alert("fonction indisponible");
		}

        if (id_button == "toolbar_b_deconnexion")
                {
		document.location.href="logout.php"
		}

        if (id_button == "toolbar_b_newFiche")
                {
                        toolbar_layout_c.enableItem("toolbar_c_sauvegarder");
                        toolbar_layout_c.enableItem("toolbar_c_annuler");
                        toolbar_layout_c.disableItem("toolbar_c_modifier");
                        toolbar_layout_c.disableItem("toolbar_c_dupliquer");
                        toolbar_layout_c.disableItem("toolbar_c_supprimer");
                        toolbar_layout_b.disableItem("toolbar_b_imprimer");
                        toolbar_layout_c.disableItem("toolbar_c_imprimer");
			dhxLayout.cells("c").view("def").setActive();
                        gridListFiche_event(ficheSelectionner,"nouveau");
		}
        if (id_button == "toolbar_b_newServeur")
                {
			dhxLayout.cells("c").view("modif").setActive();
			        tabbarModif.setTabActive("tabaM1");
                }
	}

	function toolbar_c_event(id_button)
	{
	if (id_button == "toolbar_c_imprimer")
		{
		window.open('impConsul.php?numChg='+ficheSelectionner);
		}
	if (id_button == "toolbar_c_supprimer")
		{
			if(confirm("Supprimer la fiche numero "+ficheSelectionner+" ?"))
			{
			        var req = null;
                        	if (window.XMLHttpRequest)
                        	{
                                req = new XMLHttpRequest();
                                if (req.overrideMimeType)
                                	{
                                        	req.overrideMimeType('text/xml');
                                	}
                        	}
                        	else if (window.ActiveXObject)
                        	{
                        		try {
                                		req = new ActiveXObject("Msxml2.XMLHTTP");
                        		} catch (e)
                                	{
                                 		try {
                                        		req = new ActiveXObject("Microsoft.XMLHTTP");
                                		} catch (e) {}
                                	}
                        	}

                        	req.onreadystatechange = function()
                        	{
                                if(req.readyState == 4)
                                	{
                                        	if(req.status == 200)
                                        	{
							document.getElementById("formPanneauBas").innerHTML = "";
                        				document.getElementById("formPanneauBas").style.border = "2px solid white";
			                                toolbar_layout_c.disableItem("toolbar_c_sauvegarder");
                        			        toolbar_layout_c.disableItem("toolbar_c_modifier");
                                			toolbar_layout_c.disableItem("toolbar_c_annuler");
                                			toolbar_layout_c.disableItem("toolbar_c_dupliquer");
                                			toolbar_layout_c.disableItem("toolbar_c_supprimer");
                                			toolbar_layout_b.disableItem("toolbar_b_imprimer");
                                			toolbar_layout_c.disableItem("toolbar_c_imprimer");
                                                	Eve_gridServeur(serveurSelectionner);
                                        	}
                                	}
                        	}
                                req.open("POST", "formSupp.php", true);
				req.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
                                req.send("su="+ficheSelectionner);
			}
		}
	if (id_button == "toolbar_c_modifier")
		{
                        toolbar_layout_c.enableItem("toolbar_c_sauvegarder");
                        toolbar_layout_c.enableItem("toolbar_c_annuler");
                        toolbar_layout_c.disableItem("toolbar_c_modifier");
                        toolbar_layout_c.disableItem("toolbar_c_dupliquer");
                        toolbar_layout_c.disableItem("toolbar_c_supprimer");
                        toolbar_layout_b.disableItem("toolbar_b_imprimer");
                        toolbar_layout_c.disableItem("toolbar_c_imprimer");
			gridListFiche_event(ficheSelectionner,"modif");
                        document.getElementById("formPanneauBas").style.border = "2px solid red";
                        creationFiche=0;
		}
        if (id_button == "toolbar_c_annuler")
                {
			if ( creationFiche == 1)
			{
	                        toolbar_layout_c.disableItem("toolbar_c_sauvegarder");
        	                toolbar_layout_c.disableItem("toolbar_c_modifier");
                	        toolbar_layout_c.disableItem("toolbar_c_annuler");
                       	 	toolbar_layout_c.disableItem("toolbar_c_dupliquer");
                        	toolbar_layout_c.disableItem("toolbar_c_supprimer");
                        	toolbar_layout_b.disableItem("toolbar_b_imprimer");
                        	document.getElementById("formPanneauBas").innerHTML = "";
                        	document.getElementById("formPanneauBas").style.border = "2px solid white";
				Eve_gridServeur(serveurSelectionner);	
                        	creationFiche=0;
			}
			else
			{
                        	toolbar_layout_c.disableItem("toolbar_c_sauvegarder");
                       		toolbar_layout_c.disableItem("toolbar_c_annuler");
                        	toolbar_layout_c.enableItem("toolbar_c_modifier");
                        	toolbar_layout_c.enableItem("toolbar_c_dupliquer");
                        	toolbar_layout_c.enableItem("toolbar_c_supprimer");
                        	toolbar_layout_b.enableItem("toolbar_b_imprimer");
                        	toolbar_layout_c.enableItem("toolbar_c_imprimer");
                        	gridListFiche_event(ficheSelectionner,"consul");
                        	document.getElementById("formPanneauBas").style.border = "2px solid white";
                        	creationFiche=0;
			}
                }
	if (id_button == "toolbar_c_sauvegarder")
		{
			ti=document.getElementById("modifTitre").value;
			se=document.getElementById("modifServeur").value;
			te=document.getElementById("modifTech").value;	
			ty=document.getElementById("modifType").value;	
			tu=document.getElementById("modifTypeNu").value;	
			zi=document.getElementById("modifZelosI").value;	
			ds=encodeURIComponent(editor.getContent());
			nu=ficheSelectionner;
			<!-- window.location= 'formSauv.php?ti='+modifTitre; -->

                	var req = null;
                	if (window.XMLHttpRequest)
                	{
                        	req = new XMLHttpRequest();
                        	if (req.overrideMimeType)
                        	{
                                	req.overrideMimeType('text/xml');
                        	}
                	}
                	else if (window.ActiveXObject)
                	{
                        try {
                                req = new ActiveXObject("Msxml2.XMLHTTP");
                       	} catch (e)
                        	{
                               	 try {
                                        	req = new ActiveXObject("Microsoft.XMLHTTP");
                               	} catch (e) {}
                        	}
                       	}

                	req.onreadystatechange = function()
                	{
                        	if(req.readyState == 4)
                        	{
                                	if(req.status == 200)
                                	{
                        			toolbar_layout_c.disableItem("toolbar_c_annuler");
                        			toolbar_layout_c.disableItem("toolbar_c_sauvegarder");
                        			toolbar_layout_c.enableItem("toolbar_c_modifier");
                        			toolbar_layout_c.enableItem("toolbar_c_dupliquer");
                	        		toolbar_layout_c.enableItem("toolbar_c_supprimer");
        	                		toolbar_layout_b.enableItem("toolbar_b_imprimer");
	                        		toolbar_layout_c.enableItem("toolbar_c_imprimer");
						gridListFiche_event(nu,"consul");
						Eve_gridServeur(se);

                                	}
                                	else
                                	{
                                        	document.getElementById("formPanneauBas").innerHTML="Code erreur :  " + req.status + " " + req.statusText;
                                	}
                        	}
                	}
				if ( creationFiche ==1 )
				{
					req.open("POST", "formSauv.php",true);
					req.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
					req.send("ti="+ti+"&se="+se+"&te="+te+"&ty="+ty+"&zi="+zi+"&ds="+ds+"&nu="+nu+"&tu="+tu+"&cr=1");
				}
				else
				{
                        		req.open("POST", "formSauv.php",true);
					req.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
                        		req.send("ti="+ti+"&se="+se+"&te="+te+"&ty="+ty+"&zi="+zi+"&ds="+ds+"&nu="+nu+"&tu="+tu+"&cr=0");
				}
	                 
		}
        if (id_button == "toolbar_c_dupliquer")
                {
                        toolbar_layout_c.enableItem("toolbar_c_annuler");
                        toolbar_layout_c.disableItem("toolbar_c_modifier");
                        toolbar_layout_c.disableItem("toolbar_c_dupliquer");
                        toolbar_layout_c.disableItem("toolbar_c_supprimer");
                        toolbar_layout_b.disableItem("toolbar_b_imprimer");
                        toolbar_layout_c.disableItem("toolbar_c_imprimer");
			document.getElementById("div_desc").innerHTML= "Dupliquer la fiche sur :";
			document.getElementById("Consul").style.display = "none";
			document.getElementById("dupli").style.display = "block";
                }
	}


	function gridListFiche_event(numChg,typeConsul)
	{ 
		ficheSelectionner=numChg;
		var req = null; 
		if (window.XMLHttpRequest)
		{
 			req = new XMLHttpRequest();
			if (req.overrideMimeType) 
			{
				req.overrideMimeType('text/xml');
			}
		} 
		else if (window.ActiveXObject) 
		{
			try {
				req = new ActiveXObject("Msxml2.XMLHTTP");
			} catch (e)
			{
				try {
					req = new ActiveXObject("Microsoft.XMLHTTP");
				} catch (e) {}
			}
	        	}

		req.onreadystatechange = function()
		{ 
			if(req.readyState == 4)
			{
				if(req.status == 200)
				{
				        if (typeConsul == "nouveau")
                			{
                                        document.getElementById("formPanneauBas").innerHTML= req.responseText;
                        		document.getElementById("formPanneauBas").style.border = "2px solid red";
                                        dhtmlx.image_path = "dhtmlxEditor/codebase/imgs/";
                                        editor = new dhtmlXEditor("editor");
                                        editor.init();
					editor.setContent("<b>------- "+datedujour+" / "+utilisateur+"</b> -------<br><br><br>");
					creationFiche=1;
					}
					else
					{
						if (req.responseText.length == 0)
						{
						document.getElementById("formPanneauBas").innerHTML="";
                        			document.getElementById("formPanneauBas").style.border = "2px solid white";
                                                toolbar_layout_c.disableItem("toolbar_c_annuler");
                                                toolbar_layout_c.disableItem("toolbar_c_sauvegarder");
                                                toolbar_layout_c.disableItem("toolbar_c_modifier");
                                                toolbar_layout_c.disableItem("toolbar_c_dupliquer");
                                                toolbar_layout_c.disableItem("toolbar_c_supprimer");
                                                toolbar_layout_b.disableItem("toolbar_b_imprimer");
                                                toolbar_layout_c.disableItem("toolbar_c_imprimer");

						}
						else
						{
							document.getElementById("formPanneauBas").innerHTML= req.responseText;	
							if (typeConsul == "consul")
							{
                        					document.getElementById("formPanneauBas").style.border = "2px solid white";
							}
							dhtmlx.image_path = "dhtmlxEditor/codebase/imgs/";
							editor = new dhtmlXEditor("editor");
							editor.init();
							editor.setContentHTML("formDesc.php?numChg="+numChg+"&type=modif");
						}

					}	
				}
				else	
				{
					document.getElementById("formPanneauBas").innerHTML="Code erreur :  " + req.status + " " + req.statusText;

				}	
			} 
		}; 
		if (typeConsul == "consul")
		{
			req.open("POST", "formConsul.php", true); 
			req.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
			req.send("numChg="+numChg); 
		}
                if (typeConsul == "modif")
                {
                        req.open("POST", "formModif.php", true);
			req.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
                        req.send("numChg="+numChg);
                }
		if (typeConsul == "nouveau")
                {
                        req.open("POST", "formCreat.php", true);
			req.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
                        req.send("id="+serveurSelectionner);
                }

	} 

function Eve_gridServeur_gerer(id) {
	Eve_gridServeur(id);
	serveur_A_renommer=id;
	toolbar_layout_c_Modif_Serveur.enableItem("toolbar_modif_nouveau");
	toolbar_layout_c_Modif_Serveur.enableItem("toolbar_modif_renommer");
	toolbar_layout_c_Modif_Serveur.enableItem("toolbar_modif_supprimer");
}

function Eve_gridServeur_gerer_appli(id) {
	appli_en_cours=gridAppli_tabModif.cells(gridAppli_tabModif.getSelectedId(),gridAppli_tabModif.getSelectedCellIndex()).getValue();
	Eve_gridServeur(appli_en_cours);
        toolbar_layout_c_Modif_Appli.enableItem("toolbar_modif_nouveau_appli");
        toolbar_layout_c_Modif_Appli.enableItem("toolbar_modif_renommer_appli");
        toolbar_layout_c_Modif_Appli.enableItem("toolbar_modif_supprimer_appli");
}

function Eve_gridServeur_gerer_appli_asso(id) {
        Eve_gridServeur(id);
}

function Eve_gridServeur(id,tdb) {
        dhxLayout.cells("b").view("def").setActive();
	        creationFiche=0;
		serveurSelectionner=id;
		dhxLayout.cells("b").detachObject();
		if(tdb=="oui")
		{
                	gridListFicheTdb.clearAndLoad("gridListFiche.php?tdb=oui&posStart=0&count=8",function(){
                                this.nbFiche=gridListFicheTdb.getRowsNum();
                                if (nbFiche==0){
                                        toolbar_layout_c.disableItem("toolbar_c_sauvegarder");
                                        toolbar_layout_c.disableItem("toolbar_c_modifier");
                                        toolbar_layout_c.disableItem("toolbar_c_annuler");
                                        toolbar_layout_c.disableItem("toolbar_c_dupliquer");
                                        toolbar_layout_c.disableItem("toolbar_c_supprimer");
                                        toolbar_layout_b.disableItem("toolbar_b_imprimer");
                                        toolbar_layout_c.disableItem("toolbar_c_imprimer");
                                        dhxLayout.cells("b").attachObject("div_grid1_layout_b_vide");
                                        document.getElementById("formPanneauBas").innerHTML = "";
                                } else {
                                        dhxLayout.cells("b").view("def").attachObject("div_grid1_layout_b");
                                }
                        });

		}
		else
		{
			gridListFiche.clearAndLoad("gridListFiche.php?id="+id,function(){
				this.nbFiche=gridListFiche.getRowsNum();
                		if (nbFiche==0){
                        		toolbar_layout_c.disableItem("toolbar_c_sauvegarder");
                        		toolbar_layout_c.disableItem("toolbar_c_modifier");
                        		toolbar_layout_c.disableItem("toolbar_c_annuler");
                        		toolbar_layout_c.disableItem("toolbar_c_dupliquer");
                        		toolbar_layout_c.disableItem("toolbar_c_supprimer");
                        		toolbar_layout_b.disableItem("toolbar_b_imprimer");
                        		toolbar_layout_c.disableItem("toolbar_c_imprimer");
					dhxLayout.cells("b").attachObject("div_grid1_layout_b_vide");
					document.getElementById("formPanneauBas").innerHTML = "";
                		} else {
					dhxLayout.cells("b").view("def").attachObject("div_grid1_layout_b");
				}
			});
		}
}

function Eve_gridListFiche(id) {
		ficheSelectionner=id;
                creationFiche=0;
                if (nbFiche==0){
                        toolbar_layout_c.disableItem("toolbar_c_sauvegarder");
                        toolbar_layout_c.disableItem("toolbar_c_modifier");
                        toolbar_layout_c.disableItem("toolbar_c_annuler");
                        toolbar_layout_c.disableItem("toolbar_c_dupliquer");
                        toolbar_layout_c.disableItem("toolbar_c_supprimer");
                        toolbar_layout_c.disableItem("toolbar_c_imprimer");
                } else {
                        toolbar_layout_c.enableItem("toolbar_c_modifier");
                        toolbar_layout_c.enableItem("toolbar_c_dupliquer");
                        toolbar_layout_c.enableItem("toolbar_c_supprimer");
                        toolbar_layout_b.enableItem("toolbar_b_imprimer");
                        toolbar_layout_c.enableItem("toolbar_c_imprimer");
                        toolbar_layout_c.disableItem("toolbar_c_sauvegarder");
                        toolbar_layout_c.disableItem("toolbar_c_annuler");
			gridListFiche_event(id,"consul");
                }
}

function verifForm(zelos){
        //alert(zelos.value);
        if (zelos.value != "")
        {
	toolbar_layout_c.disableItem("toolbar_c_sauvegarder");
        zelos.style.backgroundColor = "#FED0D0";
                var zelosMaj=zelos.value.toUpperCase();
                var zelosLong=zelosMaj.length;
                var posI = zelosMaj.indexOf('D');
                if (posI == 0 && zelosLong > 1)
                {
                var testNb = zelosMaj.split('D');
                if(isNaN(testNb[1])== false)
                        {
                        //alert("un nombre");
                        if(zelosLong == 9)
                                {
                                zelos.style.backgroundColor = "#C0FE9C";
				toolbar_layout_c.enableItem("toolbar_c_sauvegarder");
                                }
                        }
                }
        }
        else if (zelos.value == "")
        {
				toolbar_layout_c.enableItem("toolbar_c_sauvegarder");
        zelos.style.backgroundColor = "#FFFFFF";
        document.getElementById('buttonZelos').style.display = 'none';
        }
}


<!-- #### MISE EN PAGE DHTMLX #### -->

<!-- Layout 3L General -->
	var dhxLayout = new dhtmlXLayoutObject(document.body, "3L");
	dhxLayout.cells("a").setText("Cahier d'exploit v.0.8");
	dhxLayout.cells("a").setWidth(200);
	dhxLayout.cells("a").attachObject("div_layout3L_tabbar");
	dhxLayout.cells("a").fixSize(true,true);
	dhxLayout.cells("b").hideHeader();
	dhxLayout.cells("b").setHeight(250);
	dhxLayout.cells("b").fixSize(true,true);
	<!-- dhxLayout.cells("b").attachObject("div_grid1_layout_b"); -->
	dhxLayout.cells("c").hideHeader();
	

<!-- #### PANNEAU DE GAUCHE #### -->	
<!-- Bouton de tabulation dans le panneau de gauche -->
	var tabbar = new dhtmlXTabBar("div_layout3L_tabbar", "top");
	tabbar.setSkin('dhx_skyblue');
	tabbar.setImagePath("dhtmlxTabbar/codebase/imgs/");
	tabbar.addTab("taba1", "Serveurs", "90px");
	tabbar.setContent("taba1", "div_gridServeur_tab_layout_a");
	tabbar.addTab("taba2", "Applications", "90px");
	tabbar.setContent("taba2", "div_treeAppli_tab_layout_a");
	tabbar.setTabActive("taba1");

<!-- Liste des serveurs dans le bouton 1 de la barre de tabulation du panneau de gauche -->
	var gridServeur_tab = new dhtmlXGridObject('div_gridServeur_tab_layout_a');
	gridServeur_tab.setImagePath('dhtmlxGrid/codebase/imgs/');
	gridServeur_tab.setHeader("Serveur, Appli");
	gridServeur_tab.attachHeader("#text_filter,#text_filter");
	gridServeur_tab.setColSorting("connector,connector")
	gridServeur_tab.attachEvent("onRowSelect", Eve_gridServeur);
	gridServeur_tab.setInitWidths("*,*");
	gridServeur_tab.setColTypes("ro,ro");
	gridServeur_tab.setSkin("light");
	gridServeur_tab.init();
	gridServeur_tab.loadXML("gridServeur_tab.php?connector=true&dhx_sort[1]=asc");
	<!-- gridServeur_tab.load("dhtmlxGrid/serveur.xml"); -->

<!-- Liste des applications dans le bouton 2 de la barre de tabulation du panneau de gauche -->	
	var treeAppli_tab = new dhtmlXTreeObject("div_treeAppli_tab_layout_a","100%","100%",0);
	treeAppli_tab.setSkin('dhx_skyblue');
	treeAppli_tab.setImagePath('dhtmlxTree/codebase/imgs/');
	treeAppli_tab.setOnClickHandler(Eve_gridServeur);
	treeAppli_tab.loadXML("treeAppli_tab.php");

<!-- #### PANNEAU DU HAUT #### -->		
<!-- panneau d outil dans le panneau du haut -->	
	var toolbar_layout_b = dhxLayout.cells("b").attachToolbar();
	toolbar_layout_b.setIconsPath("dhtmlxToolbar/imgs/");
	toolbar_layout_b.loadXML("dhtmlxToolbar/toolbar_panneau_haut.xml");
	toolbar_layout_b.attachEvent("onClick",toolbar_b_event);
	dhxLayout.cells("b").view("tdb").attachObject("div_tdb");
	
<!-- Liste des fiches de changement dans le panneau du haut -->	
	<!-- var gridListFiche = dhxLayout.cells("b").attachGrid(); -->
	gridListFiche = new dhtmlXGridObject("div_grid1_layout_b");
	gridListFiche.setImagePath('dhtmlxGrid/codebase/imgs/');
	gridListFiche.attachEvent("onRowSelect", Eve_gridListFiche);
	gridListFiche.setHeader("Serveur, Date, Type, Ref N&deg;,Titre,Tech, Inc. Zelos, Fiche N&deg");
	gridListFiche.setInitWidths("80,80,80,150,*,80,80,80");
	gridListFiche.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro");
	gridListFiche.setSkin("light");
	gridListFiche.enableSmartRendering(true,5); 
	gridListFiche.init();

<!-- Liste des fiches du TABLEAU DE BORD changement dans le panneau du haut -->
        var toolbar_layout_b_tdb = dhxLayout.cells("b").view("tdb").attachToolbar();
        toolbar_layout_b_tdb.setIconsPath("dhtmlxToolbar/imgs/");
        toolbar_layout_b_tdb.loadXML("dhtmlxToolbar/toolbar_panneau_haut_tdb.xml");
        toolbar_layout_b_tdb.attachEvent("onClick",toolbar_b_event);

        <!-- var gridListFiche = dhxLayout.cells("b").attachGrid(); -->
        gridListFicheTdb = new dhtmlXGridObject("div_tdb");
        gridListFicheTdb.setImagePath('dhtmlxGrid/codebase/imgs/');
        gridListFicheTdb.attachEvent("onRowSelect", Eve_gridListFiche);
        gridListFicheTdb.setHeader("Serveur, Date, Type, Ref N&deg;,Titre,Tech, Inc. Zelos, Fiche N&deg");
        gridListFicheTdb.setInitWidths("80,80,80,150,*,80,80,80");
        gridListFicheTdb.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro");
        gridListFicheTdb.setSkin("light");
        gridListFicheTdb.enableSmartRendering(true,5);
        gridListFicheTdb.init();
        //Eve_gridServeur("1","oui");

<!-- #### PANNEAU DU BAS #### -->		
<!-- panneau d outil dans le panneau du bas -->	
	var toolbar_layout_c = dhxLayout.cells("c").attachToolbar();
	toolbar_layout_c.setIconsPath("dhtmlxToolbar/imgs/");
	toolbar_layout_c.loadXML("dhtmlxToolbar/toolbar_panneau_bas.xml");
	toolbar_layout_c.attachEvent("onClick",toolbar_c_event); 
	dhxLayout.cells("c").attachObject("formPanneauBas");

<!-- #### PANNEAU MODIF APPLICATION / SERVEUR #### -->
	dhxLayout.cells("c").view("modif").attachObject("div_tabbar_modif");
        var tabbarModif = new dhtmlXTabBar("div_tabbar_modif", "top");
        tabbarModif.setSkin('dhx_skyblue');
        tabbarModif.setImagePath("dhtmlxTabbar/codebase/imgs/");

<!-- tab1 -->
        tabbarModif.addTab("tabaM1", "Gerer les Serveurs", "160px");

        var toolbar_layout_c_Modif_Serveur = tabbarModif.cells("tabaM1").attachToolbar();
        toolbar_layout_c_Modif_Serveur.setIconsPath("dhtmlxToolbar/imgs/");
        toolbar_layout_c_Modif_Serveur.loadXML("dhtmlxToolbar/toolbar_panneau_modif_serveur.xml");
        toolbar_layout_c_Modif_Serveur.attachEvent("onClick",toolbar_m_event);

        var gridServeur_tabModif = new dhtmlXGridObject("div_tabbar_modif_serveur");
        gridServeur_tabModif.setImagePath('dhtmlxGrid/codebase/imgs/');
        gridServeur_tabModif.setHeader("Serveur");
        gridServeur_tabModif.setColSorting("connector,connector")
        gridServeur_tabModif.attachEvent("onRowSelect", Eve_gridServeur_gerer);
        gridServeur_tabModif.setInitWidths("*");
        gridServeur_tabModif.setColTypes("ro");
        gridServeur_tabModif.setSkin("light");
        gridServeur_tabModif.init();
        gridServeur_tabModif.loadXML("grid_gerer_serveur.php?connector=true&dhx_sort[0]=asc");

	tabbarModif.setContent("tabaM1", "div_tabbar_modif_serveur");	

<!-- tab2 -->	
        tabbarModif.addTab("tabaM2", "Gerer les Applications", "160px");

        var toolbar_layout_c_Modif_Appli = tabbarModif.cells("tabaM2").attachToolbar();
        toolbar_layout_c_Modif_Appli.setIconsPath("dhtmlxToolbar/imgs/");
        toolbar_layout_c_Modif_Appli.loadXML("dhtmlxToolbar/toolbar_panneau_modif_appli.xml");
        toolbar_layout_c_Modif_Appli.attachEvent("onClick",toolbar_m_event);

        var gridAppli_tabModif = new dhtmlXGridObject("div_tabbar_modif_appli");
        gridAppli_tabModif.setImagePath('dhtmlxGrid/codebase/imgs/');
        gridAppli_tabModif.setHeader("Appli");
        gridAppli_tabModif.setColSorting("connector,connector")
        gridAppli_tabModif.attachEvent("onRowSelect", Eve_gridServeur_gerer_appli);
        gridAppli_tabModif.setInitWidths("*");
        gridAppli_tabModif.setColTypes("ro");
        gridAppli_tabModif.setSkin("light");
        gridAppli_tabModif.init();
        gridAppli_tabModif.loadXML("grid_gerer_appli.php?connector=true&dhx_sort[0]=asc");

	tabbarModif.setContent("tabaM2", "div_tabbar_modif_appli");	

<!-- tab3 -->	
        tabbarModif.addTab("tabaM3", "Gerer les Associations", "160px");

        var toolbar_layout_c_Modif_Asso = tabbarModif.cells("tabaM3").attachToolbar();
        toolbar_layout_c_Modif_Asso.setIconsPath("dhtmlxToolbar/imgs/");
        toolbar_layout_c_Modif_Asso.loadXML("dhtmlxToolbar/toolbar_panneau_modif_asso.xml");
        toolbar_layout_c_Modif_Asso.attachEvent("onClick",toolbar_m_event);

        var gridAsso_tabModif = new dhtmlXGridObject("div_tabbar_modif_asso");
        gridAsso_tabModif.setImagePath('dhtmlxGrid/codebase/imgs/');
        gridAsso_tabModif.setHeader("Serveur,Appli");
        gridAsso_tabModif.attachEvent("onRowSelect", function(id){
		Eve_gridServeur_gerer(id);
		//Eve_gridServeur_gerer_appli_asso(gridAsso_tabModif.cells(gridAsso_tabModif.getSelectedId(),1).getValue());
		if(nbFenetreAsso==0){
			assoServeur=gridAsso_tabModif.cells(gridAsso_tabModif.getSelectedId(),0).getValue();
			assoAppli=gridAsso_tabModif.cells(gridAsso_tabModif.getSelectedId(),1).getValue();
			toolbar_layout_c_Modif_Asso.enableItem("toolbar_modif_modifier_asso");
			if (assoAppli == "")
			{
				toolbar_layout_c_Modif_Asso.disableItem("toolbar_modif_supprimer_asso");
			}
			else
			{
				toolbar_layout_c_Modif_Asso.enableItem("toolbar_modif_supprimer_asso");
			}
		}
	});
        gridAsso_tabModif.setInitWidths("*,*");
        gridAsso_tabModif.setColTypes("ro,ro");
        gridAsso_tabModif.setSkin("light");

        gridAsso_tabModif.setColSorting("connector,connector")

        gridAsso_tabModif.init();
        gridAsso_tabModif.loadXML("gridServeur_tab.php?connector=true&dhx_sort[0]=asc");

        tabbarModif.setContent("tabaM3", "div_tabbar_modif_asso");

<!-- fenetre tab3 -->
        // Creation d un Objet fenetre mais non initialise
        var dhxWins = new dhtmlXWindows();
		
	window.onload = function() {
                Eve_gridServeur("1","oui");
                dhxLayout.cells("b").view("tdb").setActive();
	}

</script>
</body>
</html>
