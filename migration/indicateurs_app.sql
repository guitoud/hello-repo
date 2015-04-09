
INSERT INTO `moteur_pages` ( `PAGES_ID` , `ITEM` , `URLP` , `LEGEND` , `LEGEND_MENU`,`PAGES_INFO` ,`ENABLE`) VALUES 
( NULL, 'indicateur_Action_Application', './indicateur/indicateur_Action_Application.php', 'indicateur - modification des applications', 'NO MENU - indicateur - modification des applications', 'indicateur - modification des applications', '0'),
( NULL, 'indicateur_Gestion_Application', './indicateur/indicateur_Gestion_Application.php', 'indicateur - Gestion des pplications', 'NO MENU - indicateur - Gestion des applications', 'indicateur - Gestion des applications', '0');


INSERT INTO `moteur_droit` 
SELECT NULL,(SELECT `ROLE_ID` FROM `moteur_role` WHERE `ROLE`='INDICATEUR ') AS `ROLE_ID`,`PAGES_ID` ,"OK"
FROM `moteur_pages`
WHERE 
`ITEM` IN('indicateur_Action_Application','indicateur_Gestion_Application');

INSERT INTO `moteur_droit` 
SELECT NULL ,(SELECT `ROLE_ID` FROM `moteur_role` WHERE `ROLE`='ROOT') AS `ROLE_ID`,`PAGES_ID`,"OK"
FROM `moteur_pages`
WHERE 
`ITEM` IN('indicateur_Action_Application','indicateur_Gestion_Application');

OPTIMIZE TABLE `moteur_droit` ;
