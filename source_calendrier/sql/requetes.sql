CREATE DATABASE `calendrier` ;

CREATE TABLE `evenements` (
`evenement_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`evenement_date` DATE NOT NULL ,
`evenement_comment` TEXT NOT NULL 
) TYPE = innodb;

INSERT INTO `evenements` ( `evenement_id` , `evenement_date` , `evenement_comment` )
VALUES (
'1', '2007-11-04', 'R�union projet "calendrier"'
), (
'2', '2007-11-04', 'D�part � la retraite de thierry'
);

INSERT INTO `evenements` ( `evenement_id` , `evenement_date` , `evenement_comment` )
VALUES (
'3', '2007-11-08', 'D�marrage du projet "SEO"'
), (
'4', '2007-12-24', 'Pr�paration des cadeaux :)'
);


