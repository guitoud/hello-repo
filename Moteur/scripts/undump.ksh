#!/bin/ksh
#set -x 
DOSSIER=$(pwd)
#Variable
file="${DOSSIER}/../cf/conf_outil_icdc.php"
if [ -f ${file} ]
then
      echo "il y a le fichier ${file}"
      MYSQL_USER=$(cat ${file} |grep "\$user" | grep -v mysql_connect | awk -F "'" '{print $2}')
      MYSQL_PASS=$(cat ${file} |grep "\$password" | grep -v mysql_connect | awk -F "'" '{print $2}')
      MYSQL_BASE=$(cat ${file} |grep "\$database" | grep -v mysql_connect | awk -F "'" '{print $2}')
      if [ -f ${DOSSIER}/../extract/${MYSQL_BASE}.sql ]
      then
      	rm ${DOSSIER}/../extract/${MYSQL_BASE}.sql
      fi
      gunzip ${DOSSIER}/../extract/${MYSQL_BASE}.sql.gz
      mysql --user=${MYSQL_USER} --password=${MYSQL_PASS} --database=${MYSQL_BASE}<${DOSSIER}/../extract/${MYSQL_BASE}.sql
      gzip ${DOSSIER}/../extract/${MYSQL_BASE}.sql
else
      echo "pas de fichier ${file}"
fi
