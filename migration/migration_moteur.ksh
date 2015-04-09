#!/bin/ksh 
#set -x

DOSSIER="/rexploit/php/iab/"
DOSSIER_MIGRATION="/rexploit/php/riasogeti/migration/"
date_log=`date +%Y%m%d%H%M%S`


#Variable
cd ${DOSSIER}
file="${DOSSIER}/cf/conf_outil_icdc.php"
if [ -f ${file} ]
then
	MYSQL_USER=$(cat ${file} |grep "\$user" | grep -v mysql_connect | awk -F "'" '{print $2}' | head -1)
	MYSQL_PASS=$(cat ${file} |grep "\$password" | grep -v mysql_connect | awk -F "'" '{print $2}' | head -1)
	MYSQL_BASE=$(cat ${file} |grep "\$database" | grep -v mysql_connect | awk -F "'" '{print $2}' | head -1)
	mysql --user=${MYSQL_USER} --password=${MYSQL_PASS} --database=${MYSQL_BASE}<${DOSSIER_MIGRATION}/migration_moteur.sql
fi

cp ${DOSSIER}default.php ${DOSSIER}default.php_olda${date_log}
cp ${DOSSIER_MIGRATION}racine/default.php ${DOSSIER}default.php

cp ${DOSSIER}index.php ${DOSSIER}index.phpa${date_log}
cp ${DOSSIER_MIGRATION}racine/index.php ${DOSSIER}index.php

cp ${DOSSIER}login.php ${DOSSIER}login.phpa${date_log}
cp ${DOSSIER_MIGRATION}racine/login.php ${DOSSIER}login.php

cp ${DOSSIER}cf/fonctions.php ${DOSSIER}cf/fonctions.phpa${date_log}
cp ${DOSSIER_MIGRATION}cf/fonctions.php ${DOSSIER}cf/fonctions.php

cp ${DOSSIER}cf/autre_fonctions.php ${DOSSIER}cf/autre_fonctions.phpa${date_log}
cp ${DOSSIER_MIGRATION}cf/autre_fonctions.php ${DOSSIER}cf/autre_fonctions.php

#Si non VA il ne faut pas le #
#cat ${DOSSIER_MIGRATION}cf/autre_fonctions.php|sed 's/portail_ope_copie_prod/portail_ope/g'>${DOSSIER}cf/autre_fonctions.php

cd ${DOSSIER}
if [ -d ${DOSSIER}/lib/menu/ ]
then
	echo "pas de creation de ${DOSSIER}/lib/menu/"
else
	echo "creation de ${DOSSIER}/lib/menu/"
	mkdir ${DOSSIER}/lib/menu/
fi
mv ${DOSSIER}/scripts/*.js  ${DOSSIER}/lib/menu/
cp ${DOSSIER}/scripts/index.php ${DOSSIER}/lib/menu/index.php

if [ -d ${DOSSIER}/scripts/log/ ]
then
	echo "pas de creation de ${DOSSIER}/scripts/log/"
else
	echo "creation de ${DOSSIER}/scripts/log/"
	mkdir ${DOSSIER}/scripts/log/
fi

cd ${DOSSIER}
for fileA in `find ${DOSSIER}Admin/ -name "*.php" | grep -v "php_old"`
do
  if [ -f ${fileA} ]
  then
	cp ${file} ${file}_old_${date_log}
  else
    echo "pas de fichier ${fileA}"
  fi
done

cp ${DOSSIER_MIGRATION}Admin/* ${DOSSIER}/Admin/

for file in `find ${DOSSIER} -name "*.php" |grep -v "./lib/" | grep -v "./cf/calendrier/"| grep -v "php_old"`
do
  if [ -f ${file} ]
  then
  echo "fichier ${file}"
  cp ${file} ${file}_old_${date_log}
  cat ${file}_old_${date_log}|sed 's/`item`/`ITEM`/g'|sed 's/`urlP`/`URLP`/g'|sed 's/`legend_menu`/`LEGEND_MENU`/g'|sed 's/`legend`/`LEGEND`/g'|sed 's/item/ITEM/g'|sed 's/`historique`/`moteur_historique`/g'|sed "s/='historique';/='moteur_historique';/g"|sed 's/`utilisateur`/`moteur_utilisateur`/g'|sed "s/='utilisateur';/='moteur_utilisateur';/g"|sed 's/`sous_menu`/`moteur_sous_menu`/g'|sed "s/='sous_menu';/='moteur_sous_menu';/g"|sed 's/`role_utilisateur`/`moteur_role_utilisateur`/g'|sed "s/='role_utilisateur';/='moteur_role_utilisateur';/g"|sed 's/`role`/`moteur_role`/g'|sed "s/='role';/='moteur_role';/g"|sed 's/`pages`/`moteur_pages`/g'|sed "s/='pages';/='moteur_pages';/g"|sed 's/`menu`/`moteur_menu`/g'|sed "s/='menu';/='moteur_menu';/g"|sed 's/`droit`/`moteur_droit`/g'|sed "s/='droit';/='moteur_droit';/g"|sed 's/urlP/URLP/g'|sed 's/legend_menu/LEGEND_MENU/g'|sed 's/legend/LEGEND/g'|sed 's/`complexite`/`forfait_complexite`/g'|sed "s/='complexite';/='forfait_complexite';/g"|sed 's/`suivie`/`forfait_suivie`/g'|sed "s/='suivie';/='forfait_suivie';/g"|sed 's/`sous_activite`/`forfait_sous_activite`/g'|sed "s/='sous_activite';/='forfait_sous_activite';/g"|sed 's/`activite`/`forfait_activite`/g'|sed "s/='activite';/='forfait_activite';/g"|sed 's/`date`/`forfait_date`/g'|sed "s/='date';/='forfait_date';/g"|sed 's/`heure`/`forfait_heure`/g'|sed "s/='heure';/='forfait_heure';/g"|sed "s/require(/require_once(/g">${file}
  #|sed 's/mysql_close()/mysql_close($mysql_link)/g'
  #séquence recherchée: 72 65 71 75 69 72 65 5F 6F 6E 63 65 28 22 2E 2F 63 66 2F 63 6F 6E 66
  #remplacée par: 72 65 71 75 69 72 65 28 22 2E 2F 63 66 2F 63 6F 6E 66
  perl -pi -e "s/\x72\x65\x71\x75\x69\x72\x65\x5F\x6F\x6E\x63\x65\x28\x22\x2E\x2F\x63\x66\x2F\x63\x6F\x6E\x66/\x72\x65\x71\x75\x69\x72\x65\x28\x22\x2E\x2F\x63\x66\x2F\x63\x6F\x6E\x66/" ${file}
  NB=$(diff ${file} ${file}_old_${date_log}| wc -l)
  if [ "${NB}" -eq "0" ]
  then
	rm ${file}_old_${date_log}
  fi
  else
    echo "pas de fichier ${file}"
  fi
done
#find . -name "*.php_old*" -exec rm -rf {} \;

 