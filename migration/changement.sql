INSERT INTO `moteur_role` (`ROLE_ID`, `ROLE`) VALUES
(NULL, 'ADMIN-CHANGEMENT');
OPTIMIZE TABLE `moteur_role` ;

INSERT INTO `moteur_role_utilisateur` 
SELECT NULL ,(SELECT `ROLE_ID` FROM `moteur_role` WHERE `ROLE`='ADMIN-CHANGEMENT') AS `ROLE_ID`,`UTILISATEUR_ID`,'0'
FROM `moteur_utilisateur`
WHERE `LOGIN` IN ('djacquemard', 'pbousquet', 'csauvaitre','gsantacreu');
OPTIMIZE TABLE `moteur_role_utilisateur` ;

INSERT INTO `moteur_pages` (`PAGES_ID`, `ITEM`, `URLP`, `LEGEND`, `LEGEND_MENU`, `PAGES_INFO`, `ENABLE`) VALUES
(NULL, 'changement_Gestion_Technologie', './changement/changement_Gestion_Technologie.php', 'Gestion des Technologies', 'Gestion des Technologies', 'Gestion des Technologies', 0),
(NULL, 'changement_Ajout_Technologie', './changement/changement_Action_Technologie.php', 'Ajout d''une technologie', 'NO MENU - Ajout d''une technologie', 'Ajout d''une technologie', 0),
(NULL, 'changement_Modification_Technologie', './changement/changement_Action_Technologie.php', 'Modification d''une technologie', 'NO MENU - Modification d''une technologie', 'Modification d''une technologie', 0),
(NULL, 'changement_Gestion_Entitee', './changement/changement_Gestion_Entite.php', 'Gestion des Entitées', 'Gestion des Entitées', 'Gestion des Entitées', 0),
(NULL, 'changement_Modification_Entitee', './changement/changement_Action_Entite.php', 'Modification d''une entitée', 'NO MENU - Modification d''une entitée', 'Modification d''une entitée', 0),
(NULL, 'changement_Ajout_Entitee', './changement/changement_Action_Entite.php', 'Ajout d''une entitée', 'NO MENU - Ajout d''une entitée', 'Ajout d''une entitée', 0),
(NULL, 'changement_Gestion_Impact', './changement/changement_Gestion_Impact.php', 'Gestion des Impacts', 'Gestion des Impacts', 'Gestion des Impacts', 0),
(NULL, 'changement_Ajout_Impact', './changement/changement_Action_Impact.php', 'Ajout d''un impact', 'NO MENU - Ajout d''un impact', 'Ajout d''un impact', 0),
(NULL, 'changement_Modification_Impact', './changement/changement_Action_Impact.php', 'Modification d''un impact', 'NO MENU - Modification d''un impact', 'Modification d''un impact', 0),
(NULL, 'changement_Gestion_Site', './changement/changement_Gestion_Site.php', 'Gestion des sites', 'Gestion des sites', 'Gestion des sites', 0),
(NULL, 'changement_Modification_Site', './changement/changement_Action_Site.php', 'Modification d''un site', 'NO MENU - Modification d''un site', 'Modification d''un site', 0),
(NULL, 'changement_Ajout_Site', './changement/changement_Action_Site.php', 'Ajout d''un site', 'NO NEMU - Ajout d''un site', 'Ajout d''un site', 0),
(NULL, 'changement_Procedure', './changement/changement_Procedure.php', 'Procédure de la gestion des Changements', 'Procédure', 'Procédure de la gestion des Changements', 0),
(NULL, 'changement_Gestion_Liste', './changement/changement_Gestion_Liste.php', 'Planning des Changements', 'Planning des Changements', 'Planning des Changements', 0),
(NULL, 'changement_Gestion_Histo', './changement/changement_Gestion_Histo.php', 'Historique des Changements', 'Historique des Changements', 'Historique des Changements', 0),
(NULL, 'changement_Gestion_Mail', './changement/changement_Gestion_Mail.php', 'Gestion des Mails', 'Gestion des Mails', 'Gestion des Mails', 0),
(NULL, 'changement_Ajout_Mail', './changement/changement_Action_Mail.php', 'Ajout d''un mail', 'NO MENU - Ajout d''un mail', 'Ajout d''un mail', 0),
(NULL, 'changement_Modif_Mail', './changement/changement_Action_Mail.php', 'Modification d''un Mail', 'NO MENU - Modification d''un Mail', 'Modification d''un Mail', 0),
(NULL, 'changement_Calendrier', './changement/changement_Calendrier.php', 'Calendrier des changements', 'Calendrier des changements', 'Calendrier des changements', 0),
(NULL, 'changement_Ajout_Changement', './changement/changement_Action_Changement.php', 'Ajout d''un changement - 1/3', 'NO MENU - Ajout d''un changement', 'Ajout d''un changement', 0),
(NULL, 'changement_Ajout_FAR', './changement/changement_Action_FAR.php', 'Création d''une FAR - 2/3', 'NO MENU - Création d''une FAR', 'Création d''une FAR', 0),
(NULL, 'changement_Modif_FAR', './changement/changement_Action_FAR.php', 'Modification d''une FAR - 2/3', 'NO MENU - Modification d''une FAR', 'Modification d''une FAR', 0),
(NULL, 'changement_Modif_Changement', './changement/changement_Action_Changement.php', 'Modification d''un changement - 1/3', 'NO MENU - Modification d''un changement', 'Modification d''un changement', 0),
(NULL, 'changement_Gestion_Liste_all', './changement/changement_Gestion_Liste_all.php', 'Liste des changements pour les administrateurs', 'Liste des changements', 'Liste des changements pour les administrateurs', 0),
(NULL, 'changement_Recherche', './changement/changement_Recherche.php', 'Recherche d''un changement', 'Recherche d''un changement', 'Recherche d''un changement', 0),
(NULL, 'changement_Ajout_Bilan', './changement/changement_Action_Bilan.php', 'Ajout d''une fiche Bilan', 'NO MENU - Ajout d''une fiche Bilan', 'Ajout d''une fiche Bilan', 0),
(NULL, 'changement_Modif_Bilan', './changement/changement_Action_Bilan.php', 'Modification d''une fiche bilan', 'NO MENU - Modification d''une fiche bilan', 'Modification d''une fiche bilan', 0),
(NULL, 'changement_Info_Bilan', './changement/changement_Action_Bilan.php', 'Fiche bilan', 'NO MENU - Fiche bilan', 'Fiche bilan', 0),
(NULL, 'changement_Ajout_CompteRendu', './changement/changement_Action_CompteRendu.php', 'Ajout d''un compte rendu', 'NO MENU - Ajout d''un compte rendu', 'Ajout d''un compte rendu', 0),
(NULL, 'changement_Modif_CompteRendu', './changement/changement_Action_CompteRendu.php', 'Modification d''un compte rendu', 'NO MENU - Modification d''un compte rendu', 'Modification d''un compte rendu', 0),
(NULL, 'changement_Info_CompteRendu', './changement/changement_Action_CompteRendu.php', 'Compte Rendu', 'NO MENU - Compte Rendu', 'Compte Rendu', 0),
(NULL, 'changement_Info_FAR', './changement/changement_Action_FAR.php', 'Fiche d''Analyse de Risque - 2/3', 'NO MENU - Fiche d''Analyse de Risque', 'Fiche d''Analyse de Risque', 0),
(NULL, 'changement_Info_Changement', './changement/changement_Action_Changement.php', 'Information sur un changement - 1/3', 'NO MENU - Information sur un changement', 'Information sur un changement', 0),
(NULL, 'changement_Send_Mail', './changement/changement_Send_Mail.php', 'Envoi d''un email', 'NO MENU - Envoi d''un email', 'Envoi d''un email', 0),
(NULL, 'changement_Ancien_Mail', './changement/changement_Ancien_Mail.php', 'Historique des Mails', 'Historique des Mails', 'Historique des Mails', 0),
(NULL, 'changement_Gestion_Liste_Inscrit', './changement/changement_Gestion_Liste_all.php', 'Liste des Inscriptions', 'Liste des Inscriptions', 'Liste des Inscriptions', 0),
(NULL, 'changement_Action_Changement_Status', './changement/changement_Action_Changement_Status.php', 'Modification du status du changement', 'NO MENU - Modification du status du changement', 'Modification du status du changement', 0),
(NULL, 'vide_changements_admin', './vide.php', '-- Admin Changements --', '-- Admin Changements --', '-- Admin Changements --', 0),
(NULL, 'vide_changements', './vide.php', '-- Changements --', '-- Changements --', '-- Changements --', 0),
(NULL, 'changement_Ajout_Ressources', './changement/changement_Action_Ressources.php', 'Ajout d''un changement - 3/3', 'NO MENU - Ajout d''un changement - 3/3', 'Ajout d''un changement - 3/3', 0),
(NULL, 'changement_Modif_Ressources', './changement/changement_Action_Ressources.php', 'Modification d''un changement - 3/3', 'NO MENU - Modification d''un changement - 3/3', 'Modification d''un changement - 3/3', 0),
(NULL, 'changement_Info_Ressources', './changement/changement_Action_Ressources.php', 'Information sur un changement - 3/3', 'NO MENU - Information sur un changement - 3/3', 'Information sur un changement - 3/3', 0);


INSERT INTO `moteur_droit` 
SELECT NULL,(SELECT `ROLE_ID` FROM `moteur_role` WHERE `ROLE`='CHANGEMENT') AS `ROLE_ID`,`PAGES_ID` ,"OK"
FROM `moteur_pages`
WHERE 
`ITEM` IN('changement_Calendrier','changement_Ajout_Ressources','changement_Modif_Ressources','changement_Info_Ressources','changement_Ajout_Changement','changement_Gestion_Liste','changement_Modif_FAR','changement_Ajout_FAR','changement_Modif_Changement','vide_changements','changement_Ajout_Bilan','changement_Modif_Bilan','changement_Info_Bilan','changement_Ajout_CompteRendu','changement_Info_CompteRendu','changement_Modif_CompteRendu','changement_Info_FAR','changement_Info_Changement','changement_Send_Mail','changement_Action_Changement_Status');

INSERT INTO `moteur_droit` 
SELECT NULL ,(SELECT `ROLE_ID` FROM `moteur_role` WHERE `ROLE`='ADMIN-CHANGEMENT') AS `ROLE_ID`,`PAGES_ID`,"OK"
FROM `moteur_pages`
WHERE 
`ITEM`IN('changement_Gestion_Liste','changement_Ajout_Ressources','changement_Modif_Ressources','changement_Info_Ressources','changement_Calendrier','changement_Ajout_Changement','changement_Modif_FAR','changement_Ajout_FAR','changement_Modif_Changement','changement_Gestion_Liste_all','vide_changements_admin','vide_changements','changement_Recherche','changement_Ajout_Bilan','changement_Modif_Bilan','changement_Info_Bilan','changement_Ajout_CompteRendu','changement_Info_CompteRendu','changement_Modif_CompteRendu','changement_Info_FAR','changement_Info_Changement','changement_Modif_Mail','changement_Gestion_Mail','changement_Ajout_Mail','changement_Send_Mail','changement_Ancien_Mail','changement_Gestion_Liste_Inscrit','changement_Action_Changement_Status');

INSERT INTO `moteur_droit` 
SELECT NULL ,(SELECT `ROLE_ID` FROM `moteur_role` WHERE `ROLE`='ROOT') AS `ROLE_ID`,`PAGES_ID`,"OK"
FROM `moteur_pages`
WHERE 
`ITEM` IN('changement_Ajout_Entitee','changement_Ajout_Ressources','changement_Modif_Ressources','changement_Info_Ressources','changement_Modification_Entitee','changement_Ajout_Impact','changement_Modification_Impact','changement_Procedure','changement_Ajout_Site','changement_Modification_Site','changement_Ajout_Technologie','changement_Modification_Technologie','changement_Gestion_Entitee','changement_Gestion_Histo','changement_Gestion_Impact','changement_Gestion_Liste','changement_Gestion_Site','changement_Gestion_Technologie','vide_changements_admin','changement_Gestion_Mail','changement_Ajout_Mail','changement_Modif_Mail','changement_Calendrier','changement_Ajout_Changement','changement_Ajout_FAR','changement_Modif_FAR','changement_Modif_Changement','changement_Gestion_Liste_all','vide_changements','changement_Recherche','changement_Ajout_Bilan','changement_Modif_Bilan','changement_Info_Bilan','changement_Ajout_CompteRendu','changement_Modif_CompteRendu','changement_Info_CompteRendu','changement_Info_FAR','changement_Info_Changement','changement_Send_Mail','changement_Ancien_Mail','changement_Gestion_Liste_Inscrit','changement_Action_Changement_Status');

INSERT INTO `moteur_droit` 
SELECT NULL ,(SELECT `ROLE_ID` FROM `moteur_role` WHERE `ROLE`='GUEST') AS `ROLE_ID`,`PAGES_ID`,"OK"
FROM `moteur_pages`
WHERE 
`ITEM` IN('changement_Calendrier','changement_Ajout_Ressources','changement_Modif_Ressources','changement_Info_Ressources','changement_Ajout_Changement','changement_Gestion_Liste','changement_Modif_FAR','changement_Ajout_FAR','changement_Modif_Changement','vide_changements','changement_Ajout_Bilan','changement_Modif_Bilan','changement_Info_Bilan','changement_Ajout_CompteRendu','changement_Info_CompteRendu','changement_Modif_CompteRendu','changement_Info_FAR','changement_Info_Changement','changement_Send_Mail','changement_Action_Changement_Status');

OPTIMIZE TABLE `moteur_droit` ;

INSERT INTO `moteur_menu` (`MENU_ID`, `NOM_MENU`, `ORDRE`, `MENU_INFO`, `ORDRE_DEFAULT`) VALUES
(NULL, 'Changements', '', 'Gestion des Changements', 'G');
OPTIMIZE TABLE `moteur_menu` ;

INSERT INTO `moteur_sous_menu` VALUES (NULL, (SELECT `MENU_ID` FROM `moteur_menu` WHERE `NOM_MENU` = 'Changements'),
(SELECT `PAGES_ID` FROM `moteur_pages` WHERE `ITEM` = 'vide_changements_admin') , 1);
INSERT INTO `moteur_sous_menu` VALUES (NULL, (SELECT `MENU_ID` FROM `moteur_menu` WHERE `NOM_MENU` = 'Changements'),
(SELECT `PAGES_ID` FROM `moteur_pages` WHERE `ITEM` = 'changement_Gestion_Entitee') , 2);
INSERT INTO `moteur_sous_menu` VALUES (NULL, (SELECT `MENU_ID` FROM `moteur_menu` WHERE `NOM_MENU` = 'Changements'),
(SELECT `PAGES_ID` FROM `moteur_pages` WHERE `ITEM` = 'changement_Gestion_Impact') , 3);
INSERT INTO `moteur_sous_menu` VALUES (NULL, (SELECT `MENU_ID` FROM `moteur_menu` WHERE `NOM_MENU` = 'Changements'),
(SELECT `PAGES_ID` FROM `moteur_pages` WHERE `ITEM` = 'changement_Gestion_Site') , 4);
INSERT INTO `moteur_sous_menu` VALUES (NULL, (SELECT `MENU_ID` FROM `moteur_menu` WHERE `NOM_MENU` = 'Changements'),
(SELECT `PAGES_ID` FROM `moteur_pages` WHERE `ITEM` = 'changement_Gestion_Technologie') , 5);
INSERT INTO `moteur_sous_menu` VALUES (NULL, (SELECT `MENU_ID` FROM `moteur_menu` WHERE `NOM_MENU` = 'Changements'),
(SELECT `PAGES_ID` FROM `moteur_pages` WHERE `ITEM` = 'changement_Gestion_Mail') , 6);
INSERT INTO `moteur_sous_menu` VALUES (NULL, (SELECT `MENU_ID` FROM `moteur_menu` WHERE `NOM_MENU` = 'Changements'),
(SELECT `PAGES_ID` FROM `moteur_pages` WHERE `ITEM` = 'changement_Ancien_Mail') , 7); 
INSERT INTO `moteur_sous_menu` VALUES (NULL, (SELECT `MENU_ID` FROM `moteur_menu` WHERE `NOM_MENU` = 'Changements'),
(SELECT `PAGES_ID` FROM `moteur_pages` WHERE `ITEM` = 'changement_Gestion_Liste_all') , 8); 
INSERT INTO `moteur_sous_menu` VALUES (NULL, (SELECT `MENU_ID` FROM `moteur_menu` WHERE `NOM_MENU` = 'Changements'),
(SELECT `PAGES_ID` FROM `moteur_pages` WHERE `ITEM` = 'changement_Gestion_Liste_Inscrit') , 9); 
INSERT INTO `moteur_sous_menu` VALUES (NULL, (SELECT `MENU_ID` FROM `moteur_menu` WHERE `NOM_MENU` = 'Changements'),
(SELECT `PAGES_ID` FROM `moteur_pages` WHERE `ITEM` = 'changement_Recherche') , 10); 
INSERT INTO `moteur_sous_menu` VALUES (NULL, (SELECT `MENU_ID` FROM `moteur_menu` WHERE `NOM_MENU` = 'Changements'),
(SELECT `PAGES_ID` FROM `moteur_pages` WHERE `ITEM` = 'vide_changements') , 20); 
INSERT INTO `moteur_sous_menu` VALUES (NULL, (SELECT `MENU_ID` FROM `moteur_menu` WHERE `NOM_MENU` = 'Changements'),
(SELECT `PAGES_ID` FROM `moteur_pages` WHERE `ITEM` = 'changement_Procedure') , 21); 
INSERT INTO `moteur_sous_menu` VALUES (NULL, (SELECT `MENU_ID` FROM `moteur_menu` WHERE `NOM_MENU` = 'Changements'),
(SELECT `PAGES_ID` FROM `moteur_pages` WHERE `ITEM` = 'changement_Calendrier') , 22); 
INSERT INTO `moteur_sous_menu` VALUES (NULL, (SELECT `MENU_ID` FROM `moteur_menu` WHERE `NOM_MENU` = 'Changements'),
(SELECT `PAGES_ID` FROM `moteur_pages` WHERE `ITEM` = 'changement_Gestion_Liste') , 23); 
INSERT INTO `moteur_sous_menu` VALUES (NULL, (SELECT `MENU_ID` FROM `moteur_menu` WHERE `NOM_MENU` = 'Changements'),
(SELECT `PAGES_ID` FROM `moteur_pages` WHERE `ITEM` = 'changement_Gestion_Histo') , 24); 
OPTIMIZE TABLE `moteur_sous_menu` ;


--
-- Structure de la table `calendrier_couleur`
--

CREATE TABLE IF NOT EXISTS `calendrier_couleur` (
  `CALENDRIER_COULEUR_ID` int(20) NOT NULL auto_increment,
  `CALENDRIER_COULEUR_FOND` varchar(6) NOT NULL,
  `CALENDRIER_COULEUR_TEXTE` varchar(6) NOT NULL,
  `ENABLE` int(1) NOT NULL default '0',
  PRIMARY KEY  (`CALENDRIER_COULEUR_ID`),
  KEY `ENABLE` (`ENABLE`),
  KEY `CALENDRIER_COULEUR_FOND` (`CALENDRIER_COULEUR_FOND`),
  KEY `CALENDRIER_COULEUR_TEXTE` (`CALENDRIER_COULEUR_TEXTE`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=17 ;

--
-- Contenu de la table `calendrier_couleur`
--

INSERT INTO `calendrier_couleur` (`CALENDRIER_COULEUR_ID`, `CALENDRIER_COULEUR_FOND`, `CALENDRIER_COULEUR_TEXTE`, `ENABLE`) VALUES
(1, 'FFFF00', '000000', 0),
(2, '990000', 'FFFFFF', 0),
(3, '00CC00', 'FFFFFF', 0),
(4, '66FFFF', '000000', 0),
(5, '9900FF', 'FFFFFF', 0),
(6, 'FF0000', 'FFFFFF', 0),
(7, 'FF00FF', '000000', 0),
(8, 'FF9933', '000000', 0),
(9, 'CC66FF', '000000', 0),
(10, '000099', 'FFFFFF', 0),
(11, '006600', 'FFFFFF', 0),
(12, '0000FF', 'FFFFFF', 0),
(13, '000000', 'FFFFFF', 0),
(14, 'FFCCCC', '000000', 0),
(15, '99FF66', '000000', 0),
(16, 'FFFF99', '000000', 0);


--
-- Structure de la table `changement_acteur`
--

CREATE TABLE IF NOT EXISTS `changement_acteur` (
  `CHANGEMENT_ACTEUR_ID` int(20) NOT NULL auto_increment,
  `CHANGEMENT_ACTEUR` varchar(200) NOT NULL,
  `ENABLE` int(1) NOT NULL,
  PRIMARY KEY  (`CHANGEMENT_ACTEUR_ID`),
  KEY `ENABLE` (`ENABLE`),
  KEY `CHANGEMENT_ACTEUR` (`CHANGEMENT_ACTEUR`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

--
-- Contenu de la table `changement_acteur`
--

INSERT INTO `changement_acteur` (`CHANGEMENT_ACTEUR_ID`, `CHANGEMENT_ACTEUR`, `ENABLE`) VALUES
(1, 'Exploitation Applicative', 0),
(2, 'Pilotage', 0),
(3, 'Ingenierie Oracle', 0),
(4, 'Ingenierie Unix', 0),
(5, 'Ingenierie Tuxedo', 0),
(6, 'Ingenierie Websphere', 0),
(7, 'Windows', 0),
(8, 'Reseau', 0),
(9, 'Access Master', 0),
(10, 'Atelier Editions', 0),
(11, 'Editique', 0),
(12, 'DEI', 0),
(13, 'Citrix', 0),
(14, 'Habilitation', 0),
(15, 'Poste de Travail', 0);

-- --------------------------------------------------------

--
-- Structure de la table `changement_bilan`
--

CREATE TABLE IF NOT EXISTS `changement_bilan` (
  `CHANGEMENT_BILAN_ID` int(20) NOT NULL auto_increment,
  `CHANGEMENT_LISTE_ID` int(20) NOT NULL,
  `CHANGEMENT_BILAN_CONFIG_ID` int(20) NOT NULL,
  `CHANGEMENT_BILAN_UTILISATEUR_ID` int(20) NOT NULL,
  `CHANGEMENT_BILAN_AUTRE_ID` int(20) NOT NULL,
  `CHANGEMENT_BILAN_VALEUR` text NOT NULL,
  `CHANGEMENT_BILAN_COM` text NOT NULL,
  `ENABLE` int(1) NOT NULL default '0',
  PRIMARY KEY  (`CHANGEMENT_BILAN_ID`),
  KEY `CHANGEMENT_LISTE_ID` (`CHANGEMENT_LISTE_ID`),
  KEY `CHANGEMENT_FAR_UTILISATEUR_ID` (`CHANGEMENT_BILAN_UTILISATEUR_ID`),
  KEY `CHANGEMENT_FAR_CONFIG_ID` (`CHANGEMENT_BILAN_CONFIG_ID`),
  KEY `CHANGEMENT_BILAN_AUTRE_ID` (`CHANGEMENT_BILAN_AUTRE_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `changement_bilan`
--


-- --------------------------------------------------------

--
-- Structure de la table `changement_bilan_config`
--

CREATE TABLE IF NOT EXISTS `changement_bilan_config` (
  `CHANGEMENT_BILAN_CONFIG_ID` int(20) NOT NULL auto_increment,
  `CHANGEMENT_BILAN_CONFIG_LIB` varchar(50) NOT NULL,
  `CHANGEMENT_BILAN_CONFIG_CRITERE` varchar(200) NOT NULL,
  `CHANGEMENT_BILAN_CONFIG_TYPE` varchar(50) NOT NULL,
  `CHANGEMENT_BILAN_CONFIG_COM` varchar(10) NOT NULL,
  `CHANGEMENT_BILAN_CONFIG_ORDRE` int(20) NOT NULL,
  `ENABLE` int(1) NOT NULL default '0',
  PRIMARY KEY  (`CHANGEMENT_BILAN_CONFIG_ID`),
  KEY `ENABLE` (`ENABLE`),
  KEY `CHANGEMENT_BILAN_CONFIG_LIB` (`CHANGEMENT_BILAN_CONFIG_LIB`),
  KEY `CHANGEMENT_BILAN_CONFIG_CRITERE` (`CHANGEMENT_BILAN_CONFIG_CRITERE`),
  KEY `CHANGEMENT_BILAN_CONFIG_TYPE` (`CHANGEMENT_BILAN_CONFIG_TYPE`),
  KEY `CHANGEMENT_BILAN_CONFIG_COM` (`CHANGEMENT_BILAN_CONFIG_COM`),
  KEY `CHANGEMENT_BILAN_CONFIG_ORDRE` (`CHANGEMENT_BILAN_CONFIG_ORDRE`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=18 ;

--
-- Contenu de la table `changement_bilan_config`
--

INSERT INTO `changement_bilan_config` (`CHANGEMENT_BILAN_CONFIG_ID`, `CHANGEMENT_BILAN_CONFIG_LIB`, `CHANGEMENT_BILAN_CONFIG_CRITERE`, `CHANGEMENT_BILAN_CONFIG_TYPE`, `CHANGEMENT_BILAN_CONFIG_COM`, `CHANGEMENT_BILAN_CONFIG_ORDRE`, `ENABLE`) VALUES
(1, '1-Bilan Technique', 'R&eacute;ussite de l''op&eacute;ration', 'oui-non', 'oui', 1, 0),
(2, '1-Bilan Technique', 'Pr&eacute;paration de l''op&eacute;ration suffisante', 'oui-non', 'oui', 2, 0),
(3, '3-Commentaires', 'Commentaires', 'text', 'non', 1, 0),
(4, '4-Mise &agrave; jour gestion de configuration', 'Mise &agrave; jour des serveurs physiques', 'oui-non', 'non', 1, 0),
(5, '4-Mise &agrave; jour gestion de configuration', 'Mise &agrave; jour des serveurs logiques', 'oui-non', 'non', 2, 0),
(6, '2-Bilan pour le client', 'Interruption du service', 'oui-non', 'oui', 3, 0),
(7, '5-Consommations', 'Nombre de personnes mobilis&eacute;es', 'liste_changement_bilan_personne', 'non', 1, 0),
(8, '5-Consommations', 'Nombre de jours d astreinte', 'liste_changement_bilan_personne', 'non', 2, 0),
(9, '1-Bilan Technique', 'Utilisation du retour arrière', 'oui-non', 'oui', 3, 0),
(10, '1-Bilan Technique', 'Interruption de service pour DPI', 'oui-non', 'oui', 4, 0),
(11, '2-Bilan pour le client', 'Amélioration du service', 'oui-non', 'oui', 1, 0),
(12, '2-Bilan pour le client', 'Dégradation du service', 'oui-non', 'oui', 2, 0),
(13, '4-Mise &agrave; jour gestion de configuration', 'Mise &agrave; jour des logiciels installés', 'oui-non', 'non', 3, 0),
(14, '4-Mise &agrave; jour gestion de configuration', 'Mise &agrave; jour des applications', 'oui-non', 'non', 4, 0),
(15, '4-Mise &agrave; jour gestion de configuration', 'Mise &agrave; jour des services applicatifs', 'oui-non', 'non', 5, 0),
(16, '5-Consommations', 'Nombre de jours de pr&eacute;sence', 'liste_changement_bilan_personne', 'non', 3, 0),
(17, '6-Commentaires', 'Commentaires', 'text', 'non', 1, 0);

-- --------------------------------------------------------

--
-- Structure de la table `changement_bilan_personne`
--

CREATE TABLE IF NOT EXISTS `changement_bilan_personne` (
  `CHANGEMENT_BILAN_PERSONNE_ID` int(20) NOT NULL auto_increment,
  `CHANGEMENT_BILAN_PERSONNE_LIB` varchar(200) NOT NULL,
  `ENABLE` int(1) NOT NULL,
  PRIMARY KEY  (`CHANGEMENT_BILAN_PERSONNE_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

--
-- Contenu de la table `changement_bilan_personne`
--

INSERT INTO `changement_bilan_personne` (`CHANGEMENT_BILAN_PERSONNE_ID`, `CHANGEMENT_BILAN_PERSONNE_LIB`, `ENABLE`) VALUES
(1, 'ING', 0),
(2, 'OPE', 0),
(3, 'RSX', 0),
(4, 'SLG', 0),
(5, 'CDS', 0),
(6, 'CITI', 0),
(7, 'DEI/Client', 0),
(8, 'Total', 0);

-- --------------------------------------------------------

--
-- Structure de la table `changement_compte_rendu`
--

CREATE TABLE IF NOT EXISTS `changement_compte_rendu` (
  `CHANGEMENT_COMPTE_RENDU_ID` int(20) NOT NULL auto_increment,
  `CHANGEMENT_LISTE_ID` int(20) NOT NULL,
  `CHANGEMENT_COMPTE_RENDU_UTILISATEUR_ID` int(20) NOT NULL,
  `CHANGEMENT_COMPTE_RENDU_INC` varchar(3) NOT NULL,
  `CHANGEMENT_COMPTE_RENDU_NB_INC` varchar(200) NOT NULL,
  `CHANGEMENT_COMPTE_RENDU_COM` text NOT NULL,
  `CHANGEMENT_COMPTE_RENDU_TEMPS_I_V` varchar(100) NOT NULL,
  `CHANGEMENT_COMPTE_RENDU_TEMPS_I_D` varchar(100) NOT NULL,
  `CHANGEMENT_COMPTE_RENDU_TEMPS_V_D` varchar(100) NOT NULL,
  `CHANGEMENT_COMPTE_RENDU_TEMPS_F_T` varchar(100) NOT NULL,
  `ENABLE` int(1) NOT NULL,
  PRIMARY KEY  (`CHANGEMENT_COMPTE_RENDU_ID`),
  KEY `CHANGEMENT_LISTE_ID` (`CHANGEMENT_LISTE_ID`),
  KEY `CHANGEMENT_COMPTE_RENDU_UTILISATEUR_ID` (`CHANGEMENT_COMPTE_RENDU_UTILISATEUR_ID`),
  KEY `CHANGEMENT_COMPTE_RENDU_INC` (`CHANGEMENT_COMPTE_RENDU_INC`),
  KEY `CHANGEMENT_COMPTE_RENDU_NB_INC` (`CHANGEMENT_COMPTE_RENDU_NB_INC`),
  KEY `CHANGEMENT_COMPTE_RENDU_TEMPS_I_V` (`CHANGEMENT_COMPTE_RENDU_TEMPS_I_V`),
  KEY `CHANGEMENT_COMPTE_RENDU_TEMPS_I_D` (`CHANGEMENT_COMPTE_RENDU_TEMPS_I_D`),
  KEY `ENABLE` (`ENABLE`),
  KEY `CHANGEMENT_COMPTE_RENDU_TEMPS_V_D` (`CHANGEMENT_COMPTE_RENDU_TEMPS_V_D`),
  KEY `CHANGEMENT_COMPTE_RENDU_TEMPS_F_T` (`CHANGEMENT_COMPTE_RENDU_TEMPS_F_T`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `changement_compte_rendu`
--


-- --------------------------------------------------------

--
-- Structure de la table `changement_date`
--

CREATE TABLE IF NOT EXISTS `changement_date` (
  `CHANGEMENT_DATE_ID` int(20) NOT NULL auto_increment,
  `CHANGEMENT_DATE` int(20) NOT NULL,
  `CHANGEMENT_SEMAINE` int(2) NOT NULL,
  `CHANGEMENT_ID` int(20) NOT NULL,
  `ENABLE` int(1) NOT NULL default '0',
  PRIMARY KEY  (`CHANGEMENT_DATE_ID`),
  KEY `CHANGEMENT_DATE` (`CHANGEMENT_DATE`),
  KEY `ENABLE` (`ENABLE`),
  KEY `CHANGEMENT_SEMAINE` (`CHANGEMENT_SEMAINE`),
  KEY `CHANGEMENT_ID` (`CHANGEMENT_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `changement_date`
--


-- --------------------------------------------------------

--
-- Structure de la table `changement_demande`
--

CREATE TABLE IF NOT EXISTS `changement_demande` (
  `CHANGEMENT_DEMANDE_ID` int(20) NOT NULL auto_increment,
  `CHANGEMENT_DEMANDE_LIB` varchar(100) NOT NULL,
  `CHANGEMENT_DEMANDE_EXEMPLE` text NOT NULL,
  `CHANGEMENT_DEMANDE_COULEUR_FOND` varchar(6) NOT NULL,
  `CHANGEMENT_DEMANDE_COULEUR_TEXTE` varchar(6) NOT NULL,
  `ENABLE` int(1) NOT NULL,
  PRIMARY KEY  (`CHANGEMENT_DEMANDE_ID`),
  KEY `CHANGEMENT_DEMANDE_LIB` (`CHANGEMENT_DEMANDE_LIB`),
  KEY `CHANGEMENT_DEMANDE_COULEUR_FOND` (`CHANGEMENT_DEMANDE_COULEUR_FOND`),
  KEY `CHANGEMENT_DEMANDE_COULEUR_TEXTE` (`CHANGEMENT_DEMANDE_COULEUR_TEXTE`),
  KEY `ENABLE` (`ENABLE`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Contenu de la table `changement_demande`
--

INSERT INTO `changement_demande` (`CHANGEMENT_DEMANDE_ID`, `CHANGEMENT_DEMANDE_LIB`, `CHANGEMENT_DEMANDE_EXEMPLE`, `CHANGEMENT_DEMANDE_COULEUR_FOND`, `CHANGEMENT_DEMANDE_COULEUR_TEXTE`, `ENABLE`) VALUES
(1, 'Changement applicatif', 'MEP,', 'FF00FF', 'FFFFFF', 0),
(2, 'Changement technique', 'mise a jour systeme,', '0000FF', 'FFFFFF', 0),
(3, 'Intervention reguliere', 'foire', 'FFFF00', '000000', 0),
(4, 'Information', 'Information', '00FFFF', '000000', 0);

-- --------------------------------------------------------

--
-- Structure de la table `changement_entite`
--

CREATE TABLE IF NOT EXISTS `changement_entite` (
  `CHANGEMENT_ENTITE_ID` int(20) NOT NULL auto_increment,
  `CHANGEMENT_ENTITE` varchar(200) NOT NULL,
  `ENABLE` int(1) NOT NULL default '0',
  PRIMARY KEY  (`CHANGEMENT_ENTITE_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

--
-- Contenu de la table `changement_entite`
--

INSERT INTO `changement_entite` (`CHANGEMENT_ENTITE_ID`, `CHANGEMENT_ENTITE`, `ENABLE`) VALUES
(1, 'Exploitation Applicative', 0),
(2, 'Ing&eacute;nierie Windows', 0),
(3, 'Ing&eacute;nierie Unix', 0),
(4, 'Pilotage-Supervision', 0),
(5, 'Poste de Travail', 0),
(6, 'Editique', 1),
(7, 'Pilotage des Changements', 0),
(8, 'Pole Projet DPI', 0);

-- --------------------------------------------------------

--
-- Structure de la table `changement_far`
--

CREATE TABLE IF NOT EXISTS `changement_far` (
  `CHANGEMENT_FAR_ID` int(20) NOT NULL auto_increment,
  `CHANGEMENT_LISTE_ID` int(20) NOT NULL,
  `CHANGEMENT_FAR_CONFIG_ID` int(20) NOT NULL,
  `CHANGEMENT_FAR_INFO_AUTRE_ID` int(20) NOT NULL,
  `CHANGEMENT_FAR_VALEUR` text NOT NULL,
  `CHANGEMENT_FAR_COMMENTAIRE` text NOT NULL,
  `CHANGEMENT_FAR_ETAT` varchar(1) NOT NULL default 'B',
  `ENABLE` int(1) NOT NULL default '0',
  PRIMARY KEY  (`CHANGEMENT_FAR_ID`),
  KEY `CHANGEMENT_LISTE_ID` (`CHANGEMENT_LISTE_ID`),
  KEY `CHANGEMENT_FAR_CONFIG_ID` (`CHANGEMENT_FAR_CONFIG_ID`),
  KEY `CHANGEMENT_FAR_INFO_AUTRE_ID` (`CHANGEMENT_FAR_INFO_AUTRE_ID`),
  KEY `CHANGEMENT_FAR_ETAT` (`CHANGEMENT_FAR_ETAT`),
  KEY `ENABLE` (`ENABLE`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `changement_far`
--


-- --------------------------------------------------------

--
-- Structure de la table `changement_far_config`
--

CREATE TABLE IF NOT EXISTS `changement_far_config` (
  `CHANGEMENT_FAR_CONFIG_ID` int(20) NOT NULL auto_increment,
  `CHANGEMENT_FAR_CONFIG_LIB` varchar(50) NOT NULL,
  `CHANGEMENT_FAR_CONFIG_CRITERE` varchar(200) NOT NULL,
  `CHANGEMENT_FAR_CONFIG_TYPE` varchar(20) NOT NULL,
  `CHANGEMENT_FAR_CONFIG_TABLE` varchar(200) default NULL,
  `CHANGEMENT_FAR_CONFIG_OBLIGATOIRE` varchar(3) NOT NULL default 'oui',
  `CHANGEMENT_FAR_CONFIG_INFO` varchar(200) default NULL,
  `CHANGEMENT_FAR_CONFIG_COMMENTAIRE` varchar(3) NOT NULL default 'non',
  `CHANGEMENT_FAR_CONFIG_ORDRE` int(10) NOT NULL,
  `ENABLE` int(1) NOT NULL default '0',
  PRIMARY KEY  (`CHANGEMENT_FAR_CONFIG_ID`),
  KEY `CHANGEMENT_FAR_CONFIG_LIB` (`CHANGEMENT_FAR_CONFIG_LIB`),
  KEY `CHANGEMENT_FAR_CONFIG_TYPE` (`CHANGEMENT_FAR_CONFIG_TYPE`),
  KEY `ENABLE` (`ENABLE`),
  KEY `CHANGEMENT_FAR_CONFIG_CRITERE` (`CHANGEMENT_FAR_CONFIG_CRITERE`),
  KEY `CHANGEMENT_FAR_CONFIG_TABLE` (`CHANGEMENT_FAR_CONFIG_TABLE`),
  KEY `CHANGEMENT_FAR_CONFIG_OBLIGATOIRE` (`CHANGEMENT_FAR_CONFIG_OBLIGATOIRE`),
  KEY `CHANGEMENT_FAR_CONFIG_ORDRE` (`CHANGEMENT_FAR_CONFIG_ORDRE`),
  KEY `CHANGEMENT_FAR_CONFIG_COMMENTAIRE` (`CHANGEMENT_FAR_CONFIG_COMMENTAIRE`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=33 ;

--
-- Contenu de la table `changement_far_config`
--

INSERT INTO `changement_far_config` (`CHANGEMENT_FAR_CONFIG_ID`, `CHANGEMENT_FAR_CONFIG_LIB`, `CHANGEMENT_FAR_CONFIG_CRITERE`, `CHANGEMENT_FAR_CONFIG_TYPE`, `CHANGEMENT_FAR_CONFIG_TABLE`, `CHANGEMENT_FAR_CONFIG_OBLIGATOIRE`, `CHANGEMENT_FAR_CONFIG_INFO`, `CHANGEMENT_FAR_CONFIG_COMMENTAIRE`, `CHANGEMENT_FAR_CONFIG_ORDRE`, `ENABLE`) VALUES
(1, '1-QUALITE DE SERVICE', 'Interuption de service pour le client', 'risque', NULL, 'oui', NULL, 'non', 1, 1),
(2, '2-ACTION PREVENTIVE', 'Test avant intervention', 'oui-non', NULL, 'oui', NULL, 'non', 3, 1),
(3, '1-QUALITE DE SERVICE', 'Interruption de service pour la DEI', 'risque', NULL, 'oui', NULL, 'non', 2, 1),
(4, '4-RISQUE GLOBAL', 'Risque global de l intervention', 'risque', NULL, 'oui', NULL, 'non', 5, 1),
(5, '4-RISQUE GLOBAL', 'Pourquoi', 'text', NULL, 'oui', NULL, 'non', 6, 1),
(6, '3-TECHNIQUES', 'Nouvelle technologie', 'risque', NULL, 'oui', NULL, 'non', 4, 1),
(7, '5-TEST', 'test', 'varchar', NULL, 'non', NULL, 'non', 7, 1),
(8, '3-TECHNIQUES', 'Localisation', 'checkbox', 'changement_localisation', 'oui', NULL, 'non', 8, 1),
(9, '5-TEST', 'changement_site', 'liste', 'changement_site', 'non', NULL, 'non', 9, 1),
(10, '5-TEST', 'changement_site', 'checkbox_horizontal', 'changement_localisation', 'oui', NULL, 'non', 9, 1),
(12, '1-Impacts infrastructure / mat&eacute;riel', '', 'checkbox_horizontal', 'changement_infrastructure', 'non', NULL, 'non', 1, 0),
(13, '1-Impacts infrastructure / mat&eacute;riel', '', 'checkbox', 'changement_infrastructure_srv_produit', 'non', NULL, 'non', 2, 0),
(14, '2-Impacts fonctionnels', '', 'checkbox_horizontal', 'changement_impact', 'non', NULL, 'non', 1, 0),
(15, '2-Impacts fonctionnels', '', 'checkbox', 'changement_impact_app', 'non', NULL, 'non', 2, 0),
(16, '3-Risques sur le service', 'Interruption du service pour le client', 'risque', NULL, 'oui', NULL, 'oui', 1, 0),
(17, '3-Risques sur le service', 'Interruption du service pour DEI', 'risque', NULL, 'oui', NULL, 'oui', 2, 0),
(18, '3-Risques sur le service ', 'Interruption du service pour DPI', 'risque', NULL, 'oui', NULL, 'oui', 3, 0),
(19, '4-Risques techniques et organisationnels', 'Niveau de complexit&eacute; du changement', 'risque', NULL, 'oui', NULL, 'oui', 1, 0),
(20, '4-Risques techniques et organisationnels', 'Maitrise de la technologie employ&eacute;e', 'risque', NULL, 'oui', NULL, 'oui', 2, 0),
(21, '4-Risques techniques et organisationnels', 'Nombre de ressources mobilis&eacute;es', 'risque', NULL, 'oui', NULL, 'oui', 3, 0),
(22, '4-Risques techniques et organisationnels', 'Dur&eacute;e d''intervention pr&eacute;vue', 'risque', NULL, 'oui', NULL, 'oui', 4, 0),
(23, '4-Risques techniques et organisationnels', 'Urgence du changement', 'risque', NULL, 'oui', NULL, 'oui', 5, 0),
(24, '5-Actions pr&eacute;ventives', 'Tests avant intervention', 'oui-non', NULL, 'oui', NULL, 'oui', 1, 0),
(25, '5-Actions pr&eacute;ventives', 'Chronogramme d''op&eacute;rations', 'oui-non', NULL, 'oui', NULL, 'oui', 2, 0),
(26, '5-Actions pr&eacute;ventives', 'Retour arri&egrave;re pr&eacute;vu', 'oui-non', NULL, 'oui', NULL, 'oui', 3, 0),
(27, '5-Actions pr&eacute;ventives', 'Tests applicatifs apr&egrave;s intervention', 'oui-non', NULL, 'oui', NULL, 'oui', 4, 0),
(28, '5-Actions pr&eacute;ventives', 'Validation DEI', 'oui-non', NULL, 'oui', NULL, 'oui', 5, 0),
(29, '5-Actions pr&eacute;ventives', 'Validation m&eacute;tiers', 'oui-non', NULL, 'oui', NULL, 'oui', 6, 0),
(30, '6-Risque global', 'Risque global du changement', 'risque', NULL, 'oui', NULL, 'oui', 1, 0),
(31, '6-Risque global', 'Risque si non r&eacute;alisation', 'risque', NULL, 'oui', NULL, 'oui', 2, 0),
(32, '7-Commentaires', '', 'text', NULL, 'non', NULL, 'non', 1, 0);

-- --------------------------------------------------------

--
-- Structure de la table `changement_impact`
--

CREATE TABLE IF NOT EXISTS `changement_impact` (
  `CHANGEMENT_IMPACT_ID` int(20) NOT NULL auto_increment,
  `CHANGEMENT_IMPACT` varchar(200) NOT NULL,
  `ENABLE` int(1) NOT NULL default '0',
  PRIMARY KEY  (`CHANGEMENT_IMPACT_ID`),
  KEY `ENABLE` (`ENABLE`),
  KEY `CHANGEMENT_IMPACT` (`CHANGEMENT_IMPACT`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Contenu de la table `changement_impact`
--

INSERT INTO `changement_impact` (`CHANGEMENT_IMPACT_ID`, `CHANGEMENT_IMPACT`, `ENABLE`) VALUES
(1, 'Messagerie', 0),
(2, 'Internet', 0),
(3, 'Intranet', 0),
(4, 'vide', 0);

-- --------------------------------------------------------

--
-- Structure de la table `changement_impact_app`
--

CREATE TABLE IF NOT EXISTS `changement_impact_app` (
  `CHANGEMENT_IMPACT_APP_ID` int(20) NOT NULL auto_increment,
  `CHANGEMENT_IMPACT_APP` varchar(200) NOT NULL,
  `CHANGEMENT_IMPACT_APP_COM` varchar(3) NOT NULL,
  `ENABLE` int(1) NOT NULL default '0',
  PRIMARY KEY  (`CHANGEMENT_IMPACT_APP_ID`),
  KEY `ENABLE` (`ENABLE`),
  KEY `CHANGEMENT_IMPACT_APP` (`CHANGEMENT_IMPACT_APP`),
  KEY `CHANGEMENT_IMPACT_APP_COM` (`CHANGEMENT_IMPACT_APP_COM`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Contenu de la table `changement_impact_app`
--

INSERT INTO `changement_impact_app` (`CHANGEMENT_IMPACT_APP_ID`, `CHANGEMENT_IMPACT_APP`, `CHANGEMENT_IMPACT_APP_COM`, `ENABLE`) VALUES
(1, 'Applications', 'oui', 0),
(4, 'vide', 'non', 0);

-- --------------------------------------------------------

--
-- Structure de la table `changement_infrastructure`
--

CREATE TABLE IF NOT EXISTS `changement_infrastructure` (
  `CHANGEMENT_INFRASTRUCTURE_ID` int(20) NOT NULL auto_increment,
  `CHANGEMENT_INFRASTRUCTURE` varchar(200) NOT NULL,
  `ENABLE` int(1) NOT NULL default '0',
  PRIMARY KEY  (`CHANGEMENT_INFRASTRUCTURE_ID`),
  KEY `CHANGEMENT_INFRASTRUCTURE` (`CHANGEMENT_INFRASTRUCTURE`),
  KEY `ENABLE` (`ENABLE`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

--
-- Contenu de la table `changement_infrastructure`
--

INSERT INTO `changement_infrastructure` (`CHANGEMENT_INFRASTRUCTURE_ID`, `CHANGEMENT_INFRASTRUCTURE`, `ENABLE`) VALUES
(1, 'Unix', 0),
(2, 'Linux', 0),
(3, 'Windows', 0),
(4, 'R&eacute;seaux', 0),
(5, 'DMZ', 0),
(6, 'PC', 0),
(7, 'TL', 0),
(8, 'Editique', 0),
(9, 'Citrix', 0),
(10, 'Appareils mobiles', 0),
(11, 'vide', 0);

-- --------------------------------------------------------

--
-- Structure de la table `changement_infrastructure_srv_produit`
--

CREATE TABLE IF NOT EXISTS `changement_infrastructure_srv_produit` (
  `CHANGEMENT_INFRASTRUCTURE_SRV_PRODUIT_ID` int(20) NOT NULL auto_increment,
  `CHANGEMENT_INFRASTRUCTURE_SRV_PRODUIT` varchar(200) NOT NULL,
  `CHANGEMENT_INFRASTRUCTURE_SRV_PRODUIT_COM` varchar(3) NOT NULL,
  `ENABLE` int(1) NOT NULL default '0',
  PRIMARY KEY  (`CHANGEMENT_INFRASTRUCTURE_SRV_PRODUIT_ID`),
  KEY `ENABLE` (`ENABLE`),
  KEY `CHANGEMENT_INFRASTRUCTURE_SRV_PRODUIT_COM` (`CHANGEMENT_INFRASTRUCTURE_SRV_PRODUIT_COM`),
  KEY `CHANGEMENT_INFRASTRUCTURE_SRV_PRODUIT` (`CHANGEMENT_INFRASTRUCTURE_SRV_PRODUIT`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Contenu de la table `changement_infrastructure_srv_produit`
--

INSERT INTO `changement_infrastructure_srv_produit` (`CHANGEMENT_INFRASTRUCTURE_SRV_PRODUIT_ID`, `CHANGEMENT_INFRASTRUCTURE_SRV_PRODUIT`, `CHANGEMENT_INFRASTRUCTURE_SRV_PRODUIT_COM`, `ENABLE`) VALUES
(1, 'Serveurs', 'oui', 0),
(2, 'Produits ', 'oui', 0),
(3, 'vide', 'non', 0);

-- --------------------------------------------------------

--
-- Structure de la table `changement_liste`
--

CREATE TABLE IF NOT EXISTS `changement_liste` (
  `CHANGEMENT_LISTE_ID` int(20) NOT NULL auto_increment,
  `CHANGEMENT_LISTE_UTILISATEUR_ID` int(20) NOT NULL,
  `CHANGEMENT_LISTE_DATE_DEBUT` int(8) NOT NULL,
  `CHANGEMENT_LISTE_DATE_FIN` int(8) NOT NULL,
  `CHANGEMENT_LISTE_HEURE_DEBUT` varchar(5) NOT NULL,
  `CHANGEMENT_LISTE_HEURE_FIN` varchar(5) NOT NULL,
  `CHANGEMENT_LISTE_DATE_MODIFICATION` varchar(30) NOT NULL,
  `CHANGEMENT_LISTE_LIB` varchar(100) NOT NULL,
  `CHANGEMENT_STATUS_ID` int(20) NOT NULL,
  `CHANGEMENT_DEMANDE_ID` int(20) NOT NULL,
  `ENABLE` int(1) NOT NULL default '0',
  PRIMARY KEY  (`CHANGEMENT_LISTE_ID`),
  KEY `CHANGEMENT_TEST_DATE_DEBUT` (`CHANGEMENT_LISTE_DATE_DEBUT`),
  KEY `CHANGEMENT_TEST_DATE_FIN` (`CHANGEMENT_LISTE_DATE_FIN`),
  KEY `CHANGEMENT_TEST_HEURE_DEBUT` (`CHANGEMENT_LISTE_HEURE_DEBUT`),
  KEY `CHANGEMENT_TEST_HEURE_FIN` (`CHANGEMENT_LISTE_HEURE_FIN`),
  KEY `ENABLE` (`ENABLE`),
  KEY `CHANGEMENT_TEST_UTILISATEUR_ID` (`CHANGEMENT_LISTE_UTILISATEUR_ID`),
  KEY `CHANGEMENT_TEST_LIB` (`CHANGEMENT_LISTE_LIB`),
  KEY `CHANGEMENT_STATUS_ID` (`CHANGEMENT_STATUS_ID`),
  KEY `CHANGEMENT_LISTE_DATE_MODIFICATION` (`CHANGEMENT_LISTE_DATE_MODIFICATION`),
  KEY `CHANGEMENT_DEMANDE_ID` (`CHANGEMENT_DEMANDE_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `changement_liste`
--


-- --------------------------------------------------------

--
-- Structure de la table `changement_liste_config`
--

CREATE TABLE IF NOT EXISTS `changement_liste_config` (
  `CHANGEMENT_LISTE_CONFIG_ID` int(20) NOT NULL auto_increment,
  `CHANGEMENT_LISTE_CONFIG_LIB` varchar(200) NOT NULL,
  `CHANGEMENT_LISTE_CONFIG_TYPE` varchar(200) NOT NULL,
  `CHANGEMENT_LISTE_CONFIG_TABLE` varchar(200) default NULL,
  `CHANGEMENT_LISTE_CONFIG_OBLIGATOIRE` varchar(3) NOT NULL,
  `CHANGEMENT_LISTE_CONFIG_ORDRE` int(10) NOT NULL,
  `ENABLE` int(1) NOT NULL default '0',
  PRIMARY KEY  (`CHANGEMENT_LISTE_CONFIG_ID`),
  KEY `CHANGEMENT_LISTE_CONFIG_LIB` (`CHANGEMENT_LISTE_CONFIG_LIB`),
  KEY `ENABLE` (`ENABLE`),
  KEY `CHANGEMENT_LISTE_CONFIG_TYPE` (`CHANGEMENT_LISTE_CONFIG_TYPE`),
  KEY `CHANGEMENT_LISTE_CONFIG_OBLIGATOIRE` (`CHANGEMENT_LISTE_CONFIG_OBLIGATOIRE`),
  KEY `CHANGEMENT_LISTE_CONFIG_ORDRE` (`CHANGEMENT_LISTE_CONFIG_ORDRE`),
  KEY `CHANGEMENT_LISTE_CONFIG_TABLE` (`CHANGEMENT_LISTE_CONFIG_TABLE`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;

--
-- Contenu de la table `changement_liste_config`
--

INSERT INTO `changement_liste_config` (`CHANGEMENT_LISTE_CONFIG_ID`, `CHANGEMENT_LISTE_CONFIG_LIB`, `CHANGEMENT_LISTE_CONFIG_TYPE`, `CHANGEMENT_LISTE_CONFIG_TABLE`, `CHANGEMENT_LISTE_CONFIG_OBLIGATOIRE`, `CHANGEMENT_LISTE_CONFIG_ORDRE`, `ENABLE`) VALUES
(1, 'Description', 'text', NULL, 'oui', 1, 0),
(2, 'Entit&eacute;', 'liste', 'changement_entite', 'oui', 3, 0),
(3, 'Site', 'liste', 'changement_site', 'oui', 4, 1),
(4, 'Localisation', 'checkbox', 'changement_localisation', 'non', 4, 1),
(5, 'Personnes assurant l''intervention', 'varchar', NULL, 'oui', 6, 1),
(6, 'Personnes assurant l''assistance apr&egrave;s l''intervention', 'varchar', NULL, 'oui', 7, 1),
(7, 'Pour changement applicatif', 'checkbox_horizontal', 'changement_liste_config_applicatif', 'non', 2, 0),
(8, 'Personnes assurant la communication', 'varchar', NULL, 'non', 5, 1),
(9, 'Intervenant', 'varchar', NULL, 'oui', 10, 0),
(10, 'Site', 'checkbox_horizontal', 'changement_site_bx', 'non', 4, 0),
(11, '', 'checkbox_horizontal', 'changement_site_paris', 'non', 5, 0),
(12, '', 'checkbox_horizontal', 'changement_site_angers', 'non', 6, 0);

-- --------------------------------------------------------

--
-- Structure de la table `changement_liste_config_applicatif`
--

CREATE TABLE IF NOT EXISTS `changement_liste_config_applicatif` (
  `CHANGEMENT_LISTE_CONFIG_APPLICATIF_ID` int(20) NOT NULL auto_increment,
  `CHANGEMENT_LISTE_CONFIG_APPLICATIF` varchar(200) NOT NULL,
  `ENABLE` int(1) NOT NULL default '0',
  PRIMARY KEY  (`CHANGEMENT_LISTE_CONFIG_APPLICATIF_ID`),
  KEY `ENABLE` (`ENABLE`),
  KEY `CHANGEMENT_LISTE_CONFIG_APPLICATIF` (`CHANGEMENT_LISTE_CONFIG_APPLICATIF`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Contenu de la table `changement_liste_config_applicatif`
--

INSERT INTO `changement_liste_config_applicatif` (`CHANGEMENT_LISTE_CONFIG_APPLICATIF_ID`, `CHANGEMENT_LISTE_CONFIG_APPLICATIF`, `ENABLE`) VALUES
(1, 'Version', 0),
(2, 'LUP', 0),
(3, 'D&eacute;rogatoire', 0),
(4, 'vide', 0);

-- --------------------------------------------------------

--
-- Structure de la table `changement_liste_info`
--

CREATE TABLE IF NOT EXISTS `changement_liste_info` (
  `CHANGEMENT_LISTE_INFO_ID` int(20) NOT NULL auto_increment,
  `CHANGEMENT_LISTE_ID` int(20) NOT NULL,
  `CHANGEMENT_LISTE_CONFIG_ID` int(20) NOT NULL,
  `CHANGEMENT_LISTE_INFO_AUTRE_ID` int(20) NOT NULL,
  `CHANGEMENT_LISTE_INFO_LIB` text NOT NULL,
  `ENABLE` int(1) NOT NULL,
  PRIMARY KEY  (`CHANGEMENT_LISTE_INFO_ID`),
  KEY `CHANGEMENT_LISTE_ID` (`CHANGEMENT_LISTE_ID`),
  KEY `ENABLE` (`ENABLE`),
  KEY `CHANGEMENT_LISTE_CONFIG_ID` (`CHANGEMENT_LISTE_CONFIG_ID`),
  KEY `CHANGEMENT_LISTE_INFO_AUTRE_ID` (`CHANGEMENT_LISTE_INFO_AUTRE_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `changement_liste_info`
--


-- --------------------------------------------------------

--
-- Structure de la table `changement_localisation`
--

CREATE TABLE IF NOT EXISTS `changement_localisation` (
  `CHANGEMENT_LOCALISATION_ID` int(20) NOT NULL auto_increment,
  `CHANGEMENT_LOCALISATION` varchar(200) NOT NULL,
  `CHANGEMENT_LOCALISATION_COM` varchar(3) NOT NULL,
  `ENABLE` int(1) NOT NULL default '0',
  PRIMARY KEY  (`CHANGEMENT_LOCALISATION_ID`),
  KEY `CHANGEMENT_LOCALISATION` (`CHANGEMENT_LOCALISATION`),
  KEY `ENABLE` (`ENABLE`),
  KEY `CHANGEMENT_LOCALISATION_COM` (`CHANGEMENT_LOCALISATION_COM`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Contenu de la table `changement_localisation`
--

INSERT INTO `changement_localisation` (`CHANGEMENT_LOCALISATION_ID`, `CHANGEMENT_LOCALISATION`, `CHANGEMENT_LOCALISATION_COM`, `ENABLE`) VALUES
(1, 'Unix', 'oui', 0),
(2, 'Windows', 'oui', 0),
(3, 'R&eacute;seaux', 'non', 0),
(4, 'DMZ', 'non', 0),
(5, 'LAN', 'non', 0),
(6, 'WAN', 'non', 0),
(7, 'vide', 'non', 0);

-- --------------------------------------------------------

--
-- Structure de la table `changement_mail`
--

CREATE TABLE IF NOT EXISTS `changement_mail` (
  `CHANGEMENT_MAIL_ID` int(11) NOT NULL auto_increment,
  `CHANGEMENT_MAIL_LIB` varchar(200) NOT NULL,
  `CHANGEMENT_STATUS_ID` int(20) NOT NULL,
  `CHANGEMENT_DEMANDE_ID` int(20) NOT NULL,
  `ENABLE` int(1) NOT NULL default '0',
  PRIMARY KEY  (`CHANGEMENT_MAIL_ID`),
  KEY `CHANGEMENT_MAIL_LIB` (`CHANGEMENT_MAIL_LIB`),
  KEY `ENABLE` (`ENABLE`),
  KEY `CHANGEMENT_STATUS_ID` (`CHANGEMENT_STATUS_ID`),
  KEY `CHANGEMENT_DEMANDE_ID` (`CHANGEMENT_DEMANDE_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=58 ;

--
-- Contenu de la table `changement_mail`
--

INSERT INTO `changement_mail` (`CHANGEMENT_MAIL_ID`, `CHANGEMENT_MAIL_LIB`, `CHANGEMENT_STATUS_ID`, `CHANGEMENT_DEMANDE_ID`, `ENABLE`) VALUES
(1, 'Vincent.Guibert-e@caissedesdepots.fr', 2, 1, 1),
(2, 'Vincent.Guibert-e@caissedesdepots.fr', 3, 1, 0),
(3, 'Vincent.Guibert-e@caissedesdepots.fr', 4, 1, 0),
(4, 'Vincent.Guibert-e@caissedesdepots.fr', 5, 1, 0),
(5, 'Vincent.Guibert-e@caissedesdepots.fr', 6, 1, 0),
(6, 'Vincent.Guibert-e@caissedesdepots.fr', 2, 2, 1),
(7, 'Vincent.Guibert-e@caissedesdepots.fr', 3, 2, 0),
(8, 'Vincent.Guibert-e@caissedesdepots.fr', 4, 2, 0),
(9, 'Vincent.Guibert-e@caissedesdepots.fr', 5, 2, 0),
(10, 'Vincent.Guibert-e@caissedesdepots.fr', 6, 2, 0),
(11, 'Vincent.Guibert-e@caissedesdepots.fr', 2, 3, 1),
(12, 'Vincent.Guibert-e@caissedesdepots.fr', 3, 3, 0),
(13, 'Vincent.Guibert-e@caissedesdepots.fr', 4, 3, 0),
(14, 'Vincent.Guibert-e@caissedesdepots.fr', 5, 3, 0),
(15, 'Vincent.Guibert-e@caissedesdepots.fr', 6, 3, 0),
(16, 'Vincent.Guibert-e@caissedesdepots.fr', 2, 4, 1),
(17, 'Vincent.Guibert-e@caissedesdepots.fr', 3, 4, 0),
(18, 'Vincent.Guibert-e@caissedesdepots.fr', 4, 4, 0),
(19, 'Vincent.Guibert-e@caissedesdepots.fr', 5, 4, 0),
(20, 'Vincent.Guibert-e@caissedesdepots.fr', 6, 4, 0),
(21, 'Cecile.Sauvaitre@caissedesdepots.fr', 6, 1, 0),
(22, 'Cecile.Sauvaitre@caissedesdepots.fr', 6, 2, 0),
(23, 'Cecile.Sauvaitre@caissedesdepots.fr', 6, 3, 0),
(24, 'Gilles.Santacreu@caissedesdepots.fr', 6, 1, 0),
(25, 'Gilles.Santacreu@caissedesdepots.fr', 6, 2, 0),
(26, 'Gilles.Santacreu@caissedesdepots.fr', 6, 3, 0),
(27, 'Gilles.Santacreu@caissedesdepots.fr', 6, 4, 1),
(28, 'demandeur@caissedesdepots.fr', 3, 1, 0),
(29, 'demandeur@caissedesdepots.fr', 4, 1, 0),
(30, 'demandeur@caissedesdepots.fr', 3, 2, 0),
(31, 'demandeur@caissedesdepots.fr', 4, 2, 0),
(32, 'demandeur@caissedesdepots.fr', 3, 3, 0),
(33, 'demandeur@caissedesdepots.fr', 4, 3, 0),
(34, 'demandeur@caissedesdepots.fr', 3, 4, 0),
(35, 'demandeur@caissedesdepots.fr', 4, 4, 0),
(36, 'demandeur@caissedesdepots.fr', 7, 1, 0),
(37, 'demandeur@caissedesdepots.fr', 7, 2, 0),
(38, 'demandeur@caissedesdepots.fr', 7, 3, 0),
(39, 'demandeur@caissedesdepots.fr', 7, 4, 0),
(40, 'utilisateur@caissedesdepots.fr', 3, 1, 0),
(41, 'utilisateur@caissedesdepots.fr', 4, 1, 0),
(42, 'utilisateur@caissedesdepots.fr', 7, 1, 0),
(43, 'utilisateur@caissedesdepots.fr', 3, 2, 0),
(44, 'utilisateur@caissedesdepots.fr', 4, 2, 0),
(45, 'utilisateur@caissedesdepots.fr', 7, 2, 0),
(46, 'utilisateur@caissedesdepots.fr', 3, 3, 0),
(47, 'utilisateur@caissedesdepots.fr', 4, 3, 0),
(48, 'utilisateur@caissedesdepots.fr', 7, 3, 0),
(49, 'utilisateur@caissedesdepots.fr', 3, 4, 0),
(50, 'utilisateur@caissedesdepots.fr', 4, 4, 0),
(51, 'utilisateur@caissedesdepots.fr', 7, 4, 0),
(52, 'Gilles.Santacreu@caissedesdepots.fr', 7, 1, 0),
(53, 'Gilles.Santacreu@caissedesdepots.fr', 7, 2, 0),
(54, 'Gilles.Santacreu@caissedesdepots.fr', 7, 3, 0),
(55, 'Gilles.Santacreu@caissedesdepots.fr', 7, 4, 0),
(56, 'utilisateur@caissedesdepots.fr', 6, 1, 0),
(57, 'Cecile.Sauvaitre@caissedesdepots.fr', 6, 4, 0);

-- --------------------------------------------------------

--
-- Structure de la table `changement_mail_trace`
--

CREATE TABLE IF NOT EXISTS `changement_mail_trace` (
  `CHANGEMENT_MAIL_TRACE_ID` int(20) NOT NULL auto_increment,
  `CHANGEMENT_MAIL_TRACE_UTILISATEUR_ID` int(20) NOT NULL,
  `CHANGEMENT_MAIL_TRACE_DATE` varchar(30) NOT NULL,
  `CHANGEMENT_MAIL_TRACE_TYPE` varchar(50) NOT NULL,
  `CHANGEMENT_MAIL_TRACE_CHANGEMENT_ID` int(20) NOT NULL,
  `CHANGEMENT_MAIL_TRACE_DEST` text NOT NULL,
  `CHANGEMENT_MAIL_TRACE_ARCHIVE` varchar(200) NOT NULL,
  PRIMARY KEY  (`CHANGEMENT_MAIL_TRACE_ID`),
  KEY `CHANGEMENT_MAIL_TRACE_UTILISATEUR_ID` (`CHANGEMENT_MAIL_TRACE_UTILISATEUR_ID`),
  KEY `CHANGEMENT_MAIL_TRACE_DATE` (`CHANGEMENT_MAIL_TRACE_DATE`),
  KEY `CHANGEMENT_MAIL_TRACE_TYPE` (`CHANGEMENT_MAIL_TRACE_TYPE`),
  KEY `CHANGEMENT_MAIL_TRACE_CHANGEMENT_ID` (`CHANGEMENT_MAIL_TRACE_CHANGEMENT_ID`),
  KEY `CHANGEMENT_MAIL_TRACE_ARCHIVE` (`CHANGEMENT_MAIL_TRACE_ARCHIVE`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `changement_mail_trace`
--


-- --------------------------------------------------------

--
-- Structure de la table `changement_ressources`
--

CREATE TABLE IF NOT EXISTS `changement_ressources` (
  `CHANGEMENT_RESSOURCES_ID` int(20) NOT NULL auto_increment,
  `CHANGEMENT_LISTE_ID` int(20) NOT NULL,
  `CHANGEMENT_RESSOURCES_CONFIG_ID` int(20) NOT NULL,
  `CHANGEMENT_RESSOURCES_INFO_AUTRE_ID` int(20) NOT NULL,
  `CHANGEMENT_RESSOURCES_VALEUR` text NOT NULL,
  `CHANGEMENT_RESSOURCES_COMMENTAIRE` text NOT NULL,
  `CHANGEMENT_RESSOURCES_ETAT` varchar(1) NOT NULL default 'B',
  `ENABLE` int(1) NOT NULL default '0',
  PRIMARY KEY  (`CHANGEMENT_RESSOURCES_ID`),
  KEY `CHANGEMENT_LISTE_ID` (`CHANGEMENT_LISTE_ID`),
  KEY `CHANGEMENT_RESSOURCES_CONFIG_ID` (`CHANGEMENT_RESSOURCES_CONFIG_ID`),
  KEY `CHANGEMENT_RESSOURCES_INFO_AUTRE_ID` (`CHANGEMENT_RESSOURCES_INFO_AUTRE_ID`),
  KEY `CHANGEMENT_RESSOURCES_ETAT` (`CHANGEMENT_RESSOURCES_ETAT`),
  KEY `ENABLE` (`ENABLE`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `changement_ressources`
--


-- --------------------------------------------------------

--
-- Structure de la table `changement_ressources_config`
--

CREATE TABLE IF NOT EXISTS `changement_ressources_config` (
  `CHANGEMENT_RESSOURCES_CONFIG_ID` int(20) NOT NULL auto_increment,
  `CHANGEMENT_RESSOURCES_CONFIG_LIB` varchar(200) NOT NULL,
  `CHANGEMENT_RESSOURCES_CONFIG_CRITERE` varchar(200) NOT NULL,
  `CHANGEMENT_RESSOURCES_CONFIG_TYPE` varchar(200) NOT NULL,
  `CHANGEMENT_RESSOURCES_CONFIG_TABLE` varchar(200) default NULL,
  `CHANGEMENT_RESSOURCES_CONFIG_OBLIGATOIRE` varchar(3) NOT NULL,
  `CHANGEMENT_RESSOURCES_CONFIG_ORDRE` int(10) NOT NULL,
  `ENABLE` int(1) NOT NULL default '0',
  PRIMARY KEY  (`CHANGEMENT_RESSOURCES_CONFIG_ID`),
  KEY `CHANGEMENT_RESSOURCES_CONFIG_LIB` (`CHANGEMENT_RESSOURCES_CONFIG_LIB`),
  KEY `CHANGEMENT_RESSOURCES_CONFIG_TYPE` (`CHANGEMENT_RESSOURCES_CONFIG_TYPE`),
  KEY `CHANGEMENT_RESSOURCES_CONFIG_TABLE` (`CHANGEMENT_RESSOURCES_CONFIG_TABLE`),
  KEY `CHANGEMENT_RESSOURCES_CONFIG_OBLIGATOIRE` (`CHANGEMENT_RESSOURCES_CONFIG_OBLIGATOIRE`),
  KEY `CHANGEMENT_RESSOURCES_CONFIG_ORDRE` (`CHANGEMENT_RESSOURCES_CONFIG_ORDRE`),
  KEY `ENABLE` (`ENABLE`),
  KEY `CHANGEMENT_RESSOURCES_CONFIG_CRITERE` (`CHANGEMENT_RESSOURCES_CONFIG_CRITERE`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Contenu de la table `changement_ressources_config`
--

INSERT INTO `changement_ressources_config` (`CHANGEMENT_RESSOURCES_CONFIG_ID`, `CHANGEMENT_RESSOURCES_CONFIG_LIB`, `CHANGEMENT_RESSOURCES_CONFIG_CRITERE`, `CHANGEMENT_RESSOURCES_CONFIG_TYPE`, `CHANGEMENT_RESSOURCES_CONFIG_TABLE`, `CHANGEMENT_RESSOURCES_CONFIG_OBLIGATOIRE`, `CHANGEMENT_RESSOURCES_CONFIG_ORDRE`, `ENABLE`) VALUES
(1, '1-Acteurs', 'Acteurs', 'liste_acteur', 'changement_acteur', 'non', 1, 0),
(2, '2-Conditions de validation', 'Conditions de validation', 'text', NULL, 'oui', 2, 0),
(3, '3-Information', 'Information', 'text', NULL, 'non', 3, 0);

-- --------------------------------------------------------

--
-- Structure de la table `changement_site`
--

CREATE TABLE IF NOT EXISTS `changement_site` (
  `CHANGEMENT_SITE_ID` int(20) NOT NULL auto_increment,
  `CHANGEMENT_SITE` varchar(200) NOT NULL,
  `ENABLE` int(1) NOT NULL default '0',
  PRIMARY KEY  (`CHANGEMENT_SITE_ID`),
  KEY `ENABLE` (`ENABLE`),
  KEY `CHANGEMENT_SITE` (`CHANGEMENT_SITE`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=14 ;

--
-- Contenu de la table `changement_site`
--

INSERT INTO `changement_site` (`CHANGEMENT_SITE_ID`, `CHANGEMENT_SITE`, `ENABLE`) VALUES
(1, 'Bordeaux', 0),
(2, 'Angers', 0),
(3, 'Angers et Bordeaux', 0),
(4, 'Paris Austerlitz', 0),
(5, 'Metz', 0),
(6, 'Cholet', 0),
(7, 'Tous les Sites DdR', 0),
(8, 'Paris L''Isle Adam', 0),
(9, 'Paris S&eacute;gur', 0),
(10, 'Paris RDL', 0),
(11, 'Bordeaux Salle', 0),
(12, 'Bordeaux Tour', 0),
(13, 'Bordeaux ExpoBureau', 0);

-- --------------------------------------------------------

--
-- Structure de la table `changement_site_angers`
--

CREATE TABLE IF NOT EXISTS `changement_site_angers` (
  `CHANGEMENT_SITE_ANGERS_ID` int(20) NOT NULL auto_increment,
  `CHANGEMENT_SITE_ANGERS` varchar(200) NOT NULL,
  `ENABLE` int(1) NOT NULL default '0',
  PRIMARY KEY  (`CHANGEMENT_SITE_ANGERS_ID`),
  KEY `ENABLE` (`ENABLE`),
  KEY `CHANGEMENT_SITE_ANGERS` (`CHANGEMENT_SITE_ANGERS`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Contenu de la table `changement_site_angers`
--

INSERT INTO `changement_site_angers` (`CHANGEMENT_SITE_ANGERS_ID`, `CHANGEMENT_SITE_ANGERS`, `ENABLE`) VALUES
(1, 'vide', 0),
(2, 'Angers', 0),
(3, 'Metz', 0),
(4, 'Cholet', 0),
(5, 'Saint-Serge 3', 0),
(6, 'Louis Gain', 0);

-- --------------------------------------------------------

--
-- Structure de la table `changement_site_autre`
--

CREATE TABLE IF NOT EXISTS `changement_site_autre` (
  `CHANGEMENT_SITE_AUTRE_ID` int(20) NOT NULL auto_increment,
  `CHANGEMENT_SITE_AUTRE` varchar(200) NOT NULL,
  `ENABLE` int(1) NOT NULL default '0',
  PRIMARY KEY  (`CHANGEMENT_SITE_AUTRE_ID`),
  KEY `ENABLE` (`ENABLE`),
  KEY `CHANGEMENT_SITE_AUTRE` (`CHANGEMENT_SITE_AUTRE`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Contenu de la table `changement_site_autre`
--

INSERT INTO `changement_site_autre` (`CHANGEMENT_SITE_AUTRE_ID`, `CHANGEMENT_SITE_AUTRE`, `ENABLE`) VALUES
(1, 'vide', 0),
(2, 'Tous les Sites DdR', 0);

-- --------------------------------------------------------

--
-- Structure de la table `changement_site_bx`
--

CREATE TABLE IF NOT EXISTS `changement_site_bx` (
  `CHANGEMENT_SITE_BX_ID` int(20) NOT NULL auto_increment,
  `CHANGEMENT_SITE_BX` varchar(200) NOT NULL,
  `ENABLE` int(1) NOT NULL default '0',
  PRIMARY KEY  (`CHANGEMENT_SITE_BX_ID`),
  KEY `ENABLE` (`ENABLE`),
  KEY `CHANGEMENT_SITE_BX` (`CHANGEMENT_SITE_BX`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Contenu de la table `changement_site_bx`
--

INSERT INTO `changement_site_bx` (`CHANGEMENT_SITE_BX_ID`, `CHANGEMENT_SITE_BX`, `ENABLE`) VALUES
(2, 'Bordeaux', 0),
(1, 'vide', 0),
(3, 'Bordeaux Salle', 0),
(4, 'Bordeaux Tour', 0),
(5, 'ExpoBureau', 0),
(6, 'Gradignan', 0);

-- --------------------------------------------------------

--
-- Structure de la table `changement_site_paris`
--

CREATE TABLE IF NOT EXISTS `changement_site_paris` (
  `CHANGEMENT_SITE_PARIS_ID` int(20) NOT NULL auto_increment,
  `CHANGEMENT_SITE_PARIS` varchar(200) NOT NULL,
  `ENABLE` int(1) NOT NULL default '0',
  PRIMARY KEY  (`CHANGEMENT_SITE_PARIS_ID`),
  KEY `ENABLE` (`ENABLE`),
  KEY `CHANGEMENT_SITE_PARIS` (`CHANGEMENT_SITE_PARIS`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Contenu de la table `changement_site_paris`
--

INSERT INTO `changement_site_paris` (`CHANGEMENT_SITE_PARIS_ID`, `CHANGEMENT_SITE_PARIS`, `ENABLE`) VALUES
(2, 'Paris Austerlitz', 0),
(3, 'Paris L''Isle Adam', 0),
(1, 'vide', 0),
(4, 'Paris Arcueil', 0);

-- --------------------------------------------------------

--
-- Structure de la table `changement_status`
--

CREATE TABLE IF NOT EXISTS `changement_status` (
  `CHANGEMENT_STATUS_ID` int(20) NOT NULL auto_increment,
  `CHANGEMENT_STATUS` varchar(200) NOT NULL,
  `CHANGEMENT_STATUS_ORDRE` int(5) NOT NULL,
  `CHANGEMENT_STATUS_COULEUR_FOND` varchar(6) NOT NULL,
  `CHANGEMENT_STATUS_COULEUR_TEXT` varchar(6) NOT NULL,
  `ENABLE` int(1) NOT NULL default '0',
  PRIMARY KEY  (`CHANGEMENT_STATUS_ID`),
  KEY `ENABLE` (`ENABLE`),
  KEY `CHANGEMENT_STATUS` (`CHANGEMENT_STATUS`),
  KEY `CHANGEMENT_STATUS_ORDRE` (`CHANGEMENT_STATUS_ORDRE`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Contenu de la table `changement_status`
--

INSERT INTO `changement_status` (`CHANGEMENT_STATUS_ID`, `CHANGEMENT_STATUS`, `CHANGEMENT_STATUS_ORDRE`, `CHANGEMENT_STATUS_COULEUR_FOND`, `CHANGEMENT_STATUS_COULEUR_TEXT`, `ENABLE`) VALUES
(1, 'Brouillon', 1, 'FFCCCC', '000000', 0),
(2, 'Inscrit', 2, '99FF66', '000000', 0),
(3, 'Abandonn&eacute;', 0, 'FFFF99', '000000', 0),
(4, 'Valid&eacute;', 3, '00FFFF', '000000', 0),
(5, 'Termin&eacute;', 4, '006600', 'FFFFFF', 0),
(6, 'Clotur&eacute;', 5, '0000FF', 'FFFFFF', 0),
(7, 'ReInscription', 6, '99FF66', '000000', 0);

-- --------------------------------------------------------

--
-- Structure de la table `changement_technologie`
--

CREATE TABLE IF NOT EXISTS `changement_technologie` (
  `CHANGEMENT_TECHNOLOGIE_ID` int(20) NOT NULL auto_increment,
  `CHANGEMENT_TECHNOLOGIE` varchar(200) NOT NULL,
  `CHANGEMENT_TECHNOLOGIE_COM` varchar(3) NOT NULL,
  `ENABLE` int(1) NOT NULL default '0',
  PRIMARY KEY  (`CHANGEMENT_TECHNOLOGIE_ID`),
  KEY `CHANGEMENT_TECHNOLOGIE` (`CHANGEMENT_TECHNOLOGIE`),
  KEY `ENABLE` (`ENABLE`),
  KEY `CHANGEMENT_TECHNOLOGIE_COM` (`CHANGEMENT_TECHNOLOGIE_COM`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Contenu de la table `changement_technologie`
--

INSERT INTO `changement_technologie` (`CHANGEMENT_TECHNOLOGIE_ID`, `CHANGEMENT_TECHNOLOGIE`, `CHANGEMENT_TECHNOLOGIE_COM`, `ENABLE`) VALUES
(1, 'Unix', '', 0),
(2, 'Windows', '', 0),
(3, 'R&eacute;seaux', '', 0),
(4, 'Stockage', '', 0),
(5, 'PDT', '', 0),
(6, 'SLG', '', 0);
