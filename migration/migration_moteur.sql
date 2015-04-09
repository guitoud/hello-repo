RENAME TABLE `utilisateur` TO `moteur_utilisateur` ;
ALTER TABLE `moteur_utilisateur` ADD `TYPE_LOGIN` VARCHAR( 5 ) NOT NULL DEFAULT 'BDD' AFTER `ACCES` ;
ALTER TABLE `moteur_utilisateur` ADD INDEX ( `TYPE_LOGIN` ) ;
ALTER TABLE `moteur_utilisateur` ADD INDEX ( `LOGIN` ) ;
ALTER TABLE `moteur_utilisateur` ADD INDEX ( `NOM` ) ;
ALTER TABLE `moteur_utilisateur` ADD INDEX ( `PRENOM` ) ;
ALTER TABLE `moteur_utilisateur` ADD INDEX ( `ACCES` ) ;
ALTER TABLE `moteur_utilisateur` ADD INDEX ( `ENABLE` ) ;
ALTER TABLE `moteur_utilisateur` ADD INDEX ( `SOCIETE` ) ;
ALTER TABLE `moteur_utilisateur` ADD INDEX ( `COMPLEMENT` ) ;
ALTER TABLE `moteur_utilisateur` ADD INDEX ( `EMAIL` ) ;
ALTER TABLE `moteur_utilisateur` ADD INDEX ( `EMAIL_FULL` ) ;
OPTIMIZE TABLE `moteur_utilisateur` ;

RENAME TABLE `sous_menu` TO `moteur_sous_menu` ;
ALTER TABLE `moteur_sous_menu` DROP INDEX `PAGES_ID_2`;
ALTER TABLE `moteur_sous_menu` DROP INDEX `MENU_ID_2`;
ALTER TABLE `moteur_sous_menu` ADD INDEX ( `ORDRE` ) ;
OPTIMIZE TABLE `moteur_sous_menu` ;

RENAME TABLE `role` TO `moteur_role` ;
OPTIMIZE TABLE `moteur_role` ;

RENAME TABLE `droit` TO `moteur_droit` ;
ALTER TABLE `moteur_droit` DROP INDEX `PAGES_ID_2`;
ALTER TABLE `moteur_droit` DROP INDEX `ROLE_ID_2` ;
ALTER TABLE `moteur_droit` ADD INDEX ( `DROIT` ) ;
OPTIMIZE TABLE `moteur_droit` ;

RENAME TABLE `historique` TO `moteur_historique` ;
ALTER TABLE `moteur_historique` ADD INDEX ( `HISTORIQUE_TYPE` ) ;
OPTIMIZE TABLE `moteur_historique` ;

RENAME TABLE `menu` TO `moteur_menu` ;
OPTIMIZE TABLE `moteur_menu` ;
ALTER TABLE `moteur_menu` ADD `ORDRE_DEFAULT` VARCHAR( 1 ) NOT NULL DEFAULT 'D' AFTER `MENU_INFO` ;
ALTER TABLE `moteur_menu` ADD INDEX ( `ORDRE_DEFAULT` ) ;
UPDATE `moteur_menu` SET `ORDRE_DEFAULT` = 'G' WHERE `NOM_MENU` IN('Administration','Changements','Communication');


RENAME TABLE `pages` TO `moteur_pages` ;
ALTER TABLE `moteur_pages` CHANGE `PAGES_ID` `PAGES_ID` INT( 20 ) NOT NULL AUTO_INCREMENT ,
CHANGE `ITEM` `ITEM` VARCHAR( 200 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
CHANGE `URLP` `URLP` VARCHAR( 200 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
CHANGE `LEGEND` `LEGEND` VARCHAR( 200 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
CHANGE `LEGEND_MENU` `LEGEND_MENU` VARCHAR( 200 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
CHANGE `PAGES_INFO` `PAGES_INFO` VARCHAR( 200 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
CHANGE `ENABLE` `ENABLE` INT( 1 ) NOT NULL ;
ALTER TABLE `moteur_pages` CHANGE `URLP` `URLP` VARCHAR( 500 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ;
ALTER TABLE `moteur_pages` ADD INDEX ( `ITEM` ) ;
ALTER TABLE `moteur_pages` ADD INDEX ( `URLP` ) ;
ALTER TABLE `moteur_pages` ADD INDEX ( `LEGEND` ) ;
ALTER TABLE `moteur_pages` ADD INDEX ( `LEGEND_MENU` ) ;
ALTER TABLE `moteur_pages` ADD INDEX ( `PAGES_INFO` ) ;
ALTER TABLE `moteur_pages` ADD INDEX ( `ENABLE` ) ;
UPDATE `moteur_pages` SET `URLP` = './Admin/Admin_Gestion_Utilisateurs.php' WHERE `URLP` = './Admin/Admin_Gestion_Utilisateus.php' LIMIT 1 ;
INSERT INTO `moteur_pages` (`PAGES_ID`, `ITEM`, `URLP`, `LEGEND`, `LEGEND_MENU`, `PAGES_INFO`, `ENABLE`) VALUES (NULL, 'Admin_Action_Email', './Admin/Admin_Action_Email.php', 'Vérification de l''email', 'NO MENU - Vérification de l''email', 'Vérification de l''email', 0);
OPTIMIZE TABLE `moteur_pages` ;
INSERT INTO `moteur_droit` SELECT NULL,`ROLE_ID`,(SELECT `PAGES_ID` FROM `moteur_pages` WHERE `ITEM` LIKE 'Admin_Action_Email') AS `PAGES_ID`,"OK" AS `DROIT` FROM `moteur_role`;
OPTIMIZE TABLE `moteur_droit` ;

RENAME TABLE `role_utilisateur` TO `moteur_role_utilisateur` ;
ALTER TABLE `moteur_role_utilisateur` ADD INDEX ( `ROLE_UTILISATEUR_ACCES` ) ;
OPTIMIZE TABLE `moteur_role_utilisateur` ;

CREATE TABLE IF NOT EXISTS `moteur_trace` (
  `MOTEUR_TRACE_ID` int(20) NOT NULL auto_increment,
  `MOTEUR_TRACE_UTILISATEUR_ID` int(20) NOT NULL,
  `MOTEUR_TRACE_DATE` varchar(30) NOT NULL,
  `MOTEUR_TRACE_DATE_TRI` bigint(30) NOT NULL,
  `MOTEUR_TRACE_CATEGORIE` varchar(50) NOT NULL,
  `MOTEUR_TRACE_TABLE` varchar(50) NOT NULL,
  `MOTEUR_TRACE_REF_ID` int(20) NOT NULL,
  `MOTEUR_TRACE_ACTION` varchar(50) NOT NULL,
  `MOTEUR_TRACE_ETAT` varchar(50) default NULL,
  PRIMARY KEY  (`MOTEUR_TRACE_ID`),
  KEY `MOTEUR_TRACE_UTILISATEUR_ID` (`MOTEUR_TRACE_UTILISATEUR_ID`),
  KEY `MOTEUR_TRACE_DATE` (`MOTEUR_TRACE_DATE`),
  KEY `MOTEUR_TRACE_ACTION` (`MOTEUR_TRACE_ACTION`),
  KEY `MOTEUR_TRACE_CATEGORIE` (`MOTEUR_TRACE_CATEGORIE`),
  KEY `MOTEUR_TRACE_ETAT` (`MOTEUR_TRACE_ETAT`),
  KEY `MOTEUR_TRACE_TABLE` (`MOTEUR_TRACE_TABLE`),
  KEY `MOTEUR_TRACE_REF_ID` (`MOTEUR_TRACE_REF_ID`),
  KEY `MOTEUR_TRACE_DATE_TRI` (`MOTEUR_TRACE_DATE_TRI`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;






RENAME TABLE `complexite` TO `forfait_complexite` ;
ALTER TABLE `forfait_complexite` ADD INDEX ( `COMPLEXITE_LIB` ) ;
ALTER TABLE `forfait_complexite` ADD INDEX ( `COMPLEXITE_POIDS` ) ;
ALTER TABLE `forfait_complexite` ADD INDEX ( `COMPLEXITE_TEMPS_MINI` ) ;
ALTER TABLE `forfait_complexite` ADD INDEX ( `COMPLEXITE_TEMPS_MAX` ) ;
ALTER TABLE `forfait_complexite` ADD INDEX ( `ENABLE` ) ;
OPTIMIZE TABLE `forfait_complexite` ;

RENAME TABLE `suivie` TO `forfait_suivie` ;
ALTER TABLE `forfait_suivie` ADD INDEX ( `CHARGE` ) ;
ALTER TABLE `forfait_suivie` ADD INDEX ( `ENABLE` ) ;
OPTIMIZE TABLE `forfait_suivie` ;

RENAME TABLE `sous_activite` TO `forfait_sous_activite` ;
ALTER TABLE `forfait_sous_activite` ADD INDEX ( `ENABLE` ) ;
OPTIMIZE TABLE `forfait_sous_activite` ;

RENAME TABLE `activite` TO `forfait_activite` ;
ALTER TABLE `forfait_activite` ADD INDEX ( `ENABLE` ) ;
OPTIMIZE TABLE `forfait_activite` ;

RENAME TABLE `date` TO `forfait_date` ;
ALTER TABLE `forfait_date` ADD INDEX ( `DATE` ) ;
OPTIMIZE TABLE `forfait_date` ;

RENAME TABLE `heure` TO `forfait_heure` ;
ALTER TABLE `forfait_heure` ADD INDEX ( `HEURE` ) ;
OPTIMIZE TABLE `forfait_heure` ;


DELETE FROM `moteur_role_utilisateur` WHERE `ROLE_ID` IN (SELECT `ROLE_ID` FROM `moteur_role` WHERE `ROLE`='GUEST');

INSERT INTO `moteur_role_utilisateur` 
SELECT NULL ,(SELECT `ROLE_ID` FROM `moteur_role` WHERE `ROLE`='GUEST') AS `ROLE_ID`,`UTILISATEUR_ID`,'0'
FROM `moteur_utilisateur`;

OPTIMIZE TABLE `moteur_role_utilisateur` ;