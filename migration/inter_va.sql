
INSERT INTO `moteur_pages` (`PAGES_ID`, `ITEM`, `URLP`, `LEGEND`, `LEGEND_MENU`, `PAGES_INFO`, `ENABLE`) VALUES
(NULL, 'inter_va_Gestion_Liste', './inter_va/inter_va_Gestion_Liste.php', 'Planning des interventions de VA', 'Planning Interventions VA', 'Planning des interventions de VA', 0),
(NULL, 'vide_planning_VA', './vide.php', '-- Planning VA --', '-- Planning VA --', '-- Planning VA --', 0),
(NULL, 'inter_va_Calendrier', './inter_va/inter_va_Calendrier.php', 'Calendrier des Interventions en VA', 'Calendrier Interventions VA', 'Calendrier des Interventions en VA', 0),
(NULL, 'inter_va_Gestion_Histo', './inter_va/inter_va_Gestion_Histo.php', 'Historique des interventions en VA', 'Historique Interventions VA', 'Historique des interventions en VA', 0),
(NULL, 'inter_va_Ajout_Intervention', './inter_va/inter_va_Action_Intervention.php', 'Ajout d''une intervention de VA', 'NO MENU - Ajout Intervention VA', 'Ajout d''une intervention de VA', 0),
(NULL, 'inter_va_Modif_Intervention', './inter_va/inter_va_Action_Intervention.php', 'Modification d''une intervention de VA', 'NO MENU - Modif Intervention VA', 'Modification d''une intervention de VA', 0),
(NULL, 'inter_va_Gestion_Liste_all', './inter_va/inter_va_Gestion_Liste_all.php', 'Récapitulatif des Interventions de VA', 'Récapitulatif Interventions VA', 'Récapitulatif des Interventions de VA', 0),
(NULL, 'inter_va_Info_Intervention', './inter_va/inter_va_Action_Intervention.php', 'Informations relatives à une intervention de VA', 'NO MENU - Info Intervention VA', 'Informations relatives à une intervention de VA', 0);
OPTIMIZE TABLE `moteur_pages` ;

INSERT INTO `moteur_droit` 
SELECT NULL ,(SELECT `ROLE_ID` FROM `moteur_role` WHERE `ROLE`='ADMIN-OPERATION') AS `ROLE_ID`,`PAGES_ID`,"OK"
FROM `moteur_pages`
WHERE 
`ITEM` IN('vide_planning_VA','inter_va_Gestion_Liste','inter_va_Calendrier','inter_va_Gestion_Histo','inter_va_Ajout_Intervention','inter_va_Modif_Intervention','inter_va_Gestion_Liste_all','inter_va_Info_Intervention');
INSERT INTO `moteur_droit` 
SELECT NULL ,(SELECT `ROLE_ID` FROM `moteur_role` WHERE `ROLE`='ROOT') AS `ROLE_ID`,`PAGES_ID`,"OK"
FROM `moteur_pages`
WHERE 
`ITEM` IN('vide_planning_VA','inter_va_Gestion_Liste','inter_va_Calendrier','inter_va_Gestion_Histo','inter_va_Ajout_Intervention','inter_va_Modif_Intervention','inter_va_Gestion_Liste_all','inter_va_Info_Intervention');
INSERT INTO `moteur_droit` 
SELECT NULL ,(SELECT `ROLE_ID` FROM `moteur_role` WHERE `ROLE`='GUEST') AS `ROLE_ID`,`PAGES_ID`,"OK"
FROM `moteur_pages`
WHERE 
`ITEM` IN('vide_planning_VA','inter_va_Gestion_Liste','inter_va_Calendrier','inter_va_Gestion_Histo','inter_va_Ajout_Intervention','inter_va_Modif_Intervention','inter_va_Info_Intervention');
OPTIMIZE TABLE `moteur_droit` ;

INSERT INTO `moteur_menu` (`MENU_ID`, `NOM_MENU`, `ORDRE`, `MENU_INFO`,`ORDRE_DEFAULT`) VALUES
(NULL, 'Planning VA', 0, 'Planning VA','D');
OPTIMIZE TABLE `moteur_menu` ;

INSERT INTO `moteur_sous_menu` VALUES (NULL, (SELECT `MENU_ID` FROM `moteur_menu` WHERE `NOM_MENU` = 'Planning VA'),
(SELECT `PAGES_ID` FROM `moteur_pages` WHERE `ITEM` = 'vide_planning_VA') , 1);
INSERT INTO `moteur_sous_menu` VALUES (NULL, (SELECT `MENU_ID` FROM `moteur_menu` WHERE `NOM_MENU` = 'Planning VA'),
(SELECT `PAGES_ID` FROM `moteur_pages` WHERE `ITEM` = 'inter_va_Gestion_Liste') , 2);
INSERT INTO `moteur_sous_menu` VALUES (NULL, (SELECT `MENU_ID` FROM `moteur_menu` WHERE `NOM_MENU` = 'Planning VA'),
(SELECT `PAGES_ID` FROM `moteur_pages` WHERE `ITEM` = 'inter_va_Calendrier') , 3);
INSERT INTO `moteur_sous_menu` VALUES (NULL, (SELECT `MENU_ID` FROM `moteur_menu` WHERE `NOM_MENU` = 'Planning VA'),
(SELECT `PAGES_ID` FROM `moteur_pages` WHERE `ITEM` = 'inter_va_Gestion_Histo') , 4);
INSERT INTO `moteur_sous_menu` VALUES (NULL, (SELECT `MENU_ID` FROM `moteur_menu` WHERE `NOM_MENU` = 'Planning VA'),
(SELECT `PAGES_ID` FROM `moteur_pages` WHERE `ITEM` = 'inter_va_Gestion_Liste_all') , 5);
OPTIMIZE TABLE `moteur_sous_menu` ;

-- --------------------------------------------------------

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

--
-- Contenu de la table `calendrier_couleur`
--

INSERT IGNORE INTO `calendrier_couleur` (`CALENDRIER_COULEUR_ID`, `CALENDRIER_COULEUR_FOND`, `CALENDRIER_COULEUR_TEXTE`, `ENABLE`) VALUES
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

-- --------------------------------------------------------

--
-- Structure de la table `va_date`
--

CREATE TABLE IF NOT EXISTS `va_date` (
  `VA_DATE_ID` int(20) NOT NULL auto_increment,
  `VA_DATE` int(20) NOT NULL,
  `VA_DATE_SEMAINE` int(20) NOT NULL,
  `ENABLE` int(1) NOT NULL default '0',
  `VA_INTERVENTION_ID` int(20) NOT NULL,
  PRIMARY KEY  (`VA_DATE_ID`),
  KEY `VA_DATE` (`VA_DATE`),
  KEY `VA_DATE_SEMAINE` (`VA_DATE_SEMAINE`),
  KEY `VA_INTERVENTION_ID` (`VA_INTERVENTION_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;


CREATE TABLE IF NOT EXISTS `va_intervention` (
  `VA_INTERVENTION_ID` int(20) NOT NULL auto_increment,
  `VA_INTERVENTION_LIBELLE` text NOT NULL,
  `VA_INTERVENTION_DATE_CREATION` varchar(30) NOT NULL,
  `VA_INTERVENTION_DATE_DEBUT` int(20) NOT NULL,
  `VA_INTERVENTION_DATE_FIN` int(20) NOT NULL,
  `VA_INTERVENTION_CODE_APPLI` varchar(5) NOT NULL,
  `ENABLE` int(1) NOT NULL default '0',
  `VA_INTERVENTION_UTILISATEUR_ID` int(20) NOT NULL,
  PRIMARY KEY  (`VA_INTERVENTION_ID`),
  KEY `VA_INTERVENTION_DATE_CREATION` (`VA_INTERVENTION_DATE_CREATION`),
  KEY `VA_INTERVENTION_DATE_DEBUT` (`VA_INTERVENTION_DATE_DEBUT`),
  KEY `VA_INTERVENTION_CODE_APPLI` (`VA_INTERVENTION_CODE_APPLI`),
  KEY `VA_INTERVENTION_UTILISATEUR_ID` (`VA_INTERVENTION_UTILISATEUR_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;
