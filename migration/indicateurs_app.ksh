#!/bin/ksh 
#set -x

DOSSIER="/rexploit/php/iab_va/"
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
	mysql --user=${MYSQL_USER} --password=${MYSQL_PASS} --database=${MYSQL_BASE}<${DOSSIER_MIGRATION}/indicateurs_app.sql
fi


cd ${DOSSIER}
cp ${DOSSIER_MIGRATION}indicateur_app/indicateur* ${DOSSIER}/indicateur/
cp ${DOSSIER_MIGRATION}indicateur_app/historique.html ${DOSSIER}/Admin/


 
