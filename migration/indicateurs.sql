
INSERT INTO `moteur_pages` ( `PAGES_ID` , `ITEM` , `URLP` , `LEGEND` , `LEGEND_MENU`,`PAGES_INFO` ,`ENABLE`) VALUES 
( NULL , 'indicateur_c5_tableau', './indicateur/indicateur_c5_tableau.php', 'D&eacute;tail C5', 'NO MENU - D&eacute;tail C5', 'D&eacute;tail C5', '0'),
( NULL , 'indicateur_c5_liste', './indicateur/indicateur_c5_liste.php', 'C5 - Liste des livraisons', 'NO MENU - C5 - Liste des livraisons', 'C5 - Liste des livraisons', '0');


INSERT INTO `moteur_droit` 
SELECT NULL,(SELECT `ROLE_ID` FROM `moteur_role` WHERE `ROLE`='INDICATEUR ') AS `ROLE_ID`,`PAGES_ID` ,"OK"
FROM `moteur_pages`
WHERE 
`ITEM` IN('indicateur_c5_tableau','indicateur_c5_liste');

INSERT INTO `moteur_droit` 
SELECT NULL ,(SELECT `ROLE_ID` FROM `moteur_role` WHERE `ROLE`='ROOT') AS `ROLE_ID`,`PAGES_ID`,"OK"
FROM `moteur_pages`
WHERE 
`ITEM` IN('indicateur_c5_tableau','indicateur_c5_liste');

OPTIMIZE TABLE `moteur_droit` ;

--
-- Structure de la table `indicateur_action`
--

DROP TABLE IF EXISTS `indicateur_action`;
CREATE TABLE IF NOT EXISTS `indicateur_action` (
  `INDICATEUR_ACTION_ID` int(20) NOT NULL auto_increment,
  `INDICATEUR_ACTION_LIB` varchar(50) NOT NULL,
  `INDICATEUR_ACTION_INFO` varchar(2) NOT NULL,
  `INDICATEUR_ACTION_INFO_NEW` varchar(10) NOT NULL,
  `INDICATEUR_ACTION_TYPE` varchar(20) NOT NULL,
  `INDICATEUR_ACTION_ORDRE` int(10) NOT NULL,
  PRIMARY KEY  (`INDICATEUR_ACTION_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

--
-- Contenu de la table `indicateur_action`
--

INSERT INTO `indicateur_action` (`INDICATEUR_ACTION_ID`, `INDICATEUR_ACTION_LIB`, `INDICATEUR_ACTION_INFO`, `INDICATEUR_ACTION_INFO_NEW`, `INDICATEUR_ACTION_TYPE`, `INDICATEUR_ACTION_ORDRE`) VALUES
(1, 'LIV.COMPOSANT', 'L', 'L', 'RESUME', 6),
(2, 'MODIF.IAB', 'M', 'M', 'RESUME', 10),
(3, 'MODIFICATION.IAB', 'M', 'M', 'RESUME', 11),
(4, 'TRAIT.EXCEPT', 'T', 'T', 'RESUME', 21),
(5, 'DEMANDE.MOA', 'D', 'D', 'RESUME', 30),
(6, 'ACTION INCONNUE', 'I', 'I', 'RESUME', 99),
(7, 'DDI', 'L', 'T', 'MOT_CLE', 1),
(8, 'COMPOSANT', 'L', 'T', 'MOT_CLE', 2),
(9, 'CORRECTIF', 'L', 'T', 'MOT_CLE', 3),
(10, 'MISE EN PRODUCTION', 'L', 'T', 'MOT_CLE', 4),
(11, 'TRAITEMENT', 'T', 'T', 'MOT_CLE', 5),
(12, 'SOUMISSION', 'T', 'T', 'MOT_CLE', 6),
(13, 'LIV.COMPOSANT.LUP', 'L', 'L_LUP', 'RESUME', 4),
(14, 'LIV.COMPOSANT.VERS', 'L', 'L_VERS', 'RESUME', 2),
(15, 'LIV.COMPOSANTS', 'L', 'L', 'RESUME', 5),
(16, 'LIV.COMPOSANTS.LUP', 'L', 'L_LUP', 'RESUME', 3),
(17, 'LIV.COMPOSANTS.VERS', 'L', 'L_VERS', 'RESUME', 1),
(18, 'TRAIT.EXCEP', 'T', 'T', 'RESUME', 22),
(19, 'TRAIT.EXCEPT.', 'T', 'T', 'RESUME', 20);


OPTIMIZE TABLE `indicateur_action` ;


DROP TABLE IF EXISTS `indicateur_extract_brut`;
CREATE TABLE IF NOT EXISTS `indicateur_extract_brut` (
  `REF` int(20) NOT NULL,
  `RESUME` text NOT NULL,
  `STATUS` varchar(200) NOT NULL,
  `DATE_CREATION` varchar(50) NOT NULL,
  `ASSIGNE` varchar(200) NOT NULL,
  `DATE_FIN_RELLE` varchar(50) NOT NULL,
  `DATE_PREVUE` varchar(50) NOT NULL,
  `ENVIRONNEMENT` varchar(200) NOT NULL,
  `LAST_UPDATE` varchar(50) NOT NULL,
  `MOT_CLES` varchar(500) NOT NULL,
  `NATURE` varchar(200) NOT NULL,
  `NO_DEMANDE` varchar(20) NOT NULL,
  `TEMPS` int(20) NOT NULL,
  `COMMENTAIRE_DESCRIPTION` text NOT NULL,
  `DATE_CREATION_DDE` varchar(50) NOT NULL,
  `DATE_DEBUT_RELLE` varchar(50) NOT NULL,
  `DATE_MAJ_DDE` varchar(50) NOT NULL,
  `DATE_SOUHAITEE` varchar(50) NOT NULL,
  `DEMANDEUR` varchar(200) NOT NULL,
  `DESCRIPTION` text NOT NULL,
  `DOCUMENT_ATTACHES` text NOT NULL,
  `DOMAINE_INTERVENTION` varchar(200) NOT NULL,
  `ENTITE_MOA` varchar(200) NOT NULL,
  `ENTITE_MOE` varchar(200) NOT NULL,
  `GROUPE_DE_TRAVAIL` varchar(200) NOT NULL,
  `IMPLANTATION_GEO` varchar(200) NOT NULL,
  `IMPLANTATION_DEMANDE` varchar(200) NOT NULL,
  `MANAGER_GROUP_TRAVAIL` varchar(200) NOT NULL,
  `MESSAGE` text NOT NULL,
  `NOM_APPLICATION` varchar(500) NOT NULL,
  `ORDRE_INTERVENTION` varchar(20) NOT NULL,
  `REF_DEROGATION` varchar(200) NOT NULL,
  `SERVICE` varchar(200) NOT NULL,
  `TELEPHONE` varchar(200) NOT NULL,
  `TEMPS_J_ASSISTANT` varchar(200) NOT NULL,
  `TEMPS_J` varchar(200) NOT NULL,
  PRIMARY KEY  (`REF`),
  KEY `ASSIGNE` (`ASSIGNE`),
  KEY `ENVIRONNEMENT` (`ENVIRONNEMENT`),
  KEY `NATURE` (`NATURE`),
  KEY `STATUS` (`STATUS`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

ALTER TABLE `indicateur_calcul` 
ADD `ACTION_NEW` VARCHAR( 10 ) NOT NULL ,
ADD `INDICATEUR_ACTION_ID` VARCHAR( 10 ) NOT NULL ;
ALTER TABLE `indicateur_calcul` ADD INDEX ( `ACTION_NEW` ) ;

OPTIMIZE TABLE `indicateur_calcul` ;


ALTER TABLE `indicateur_extract_archive` 
ADD `COMMENTAIRE_DESCRIPTION` text NOT NULL,
ADD `DATE_CREATION_DDE` varchar(50) NOT NULL,
ADD `DATE_DEBUT_RELLE` varchar(50) NOT NULL,
ADD `DATE_MAJ_DDE` varchar(50) NOT NULL,
ADD `DATE_SOUHAITEE` varchar(50) NOT NULL,
ADD `DEMANDEUR` varchar(200) NOT NULL,
ADD `DOCUMENT_ATTACHES` text NOT NULL,
ADD `DOMAINE_INTERVENTION` varchar(200) NOT NULL,
ADD `ENTITE_MOA` varchar(200) NOT NULL,
ADD `ENTITE_MOE` varchar(200) NOT NULL,
ADD `GROUPE_DE_TRAVAIL` varchar(200) NOT NULL,
ADD `IMPLANTATION_GEO` varchar(200) NOT NULL,
ADD `IMPLANTATION_DEMANDE` varchar(200) NOT NULL,
ADD `MANAGER_GROUP_TRAVAIL` varchar(200) NOT NULL,
ADD `MESSAGE` text NOT NULL,
ADD `NOM_APPLICATION` varchar(500) NOT NULL,
ADD `ORDRE_INTERVENTION` varchar(20) NOT NULL,
ADD `REF_DEROGATION` varchar(200) NOT NULL,
ADD `SERVICE` varchar(200) NOT NULL,
ADD `TELEPHONE` varchar(200) NOT NULL,
ADD `TEMPS_J_ASSISTANT` varchar(200) NOT NULL,
ADD `TEMPS_J` varchar(200) NOT NULL;

INSERT INTO `indicateur_version_date` ( `ID`, `DATE`,`TYPE`)VALUES ( NULL, '20110127','LUP' ); 
OPTIMIZE TABLE `indicateur_version_date` ;
