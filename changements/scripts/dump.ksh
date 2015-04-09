#!/bin/ksh
#set -x 
DOSSIER=$(pwd)
#Variable

for file in `ls -1 ${DOSSIER}/../cf/conf* |grep php`
do
  if [ -f ${file} ]
  then
      echo "il y a le fichier ${file}"
      MYSQL_USER=$(cat ${file} |grep "\$user" | grep -v mysql_connect | awk -F "'" '{print $2}')
      MYSQL_PASS=$(cat ${file} |grep "\$password" | grep -v mysql_connect | awk -F "'" '{print $2}')
      MYSQL_BASE=$(cat ${file} |grep "\$database" | grep -v mysql_connect | awk -F "'" '{print $2}')
#      mysql --user=${MYSQL_USER} --password=${MYSQL_PASS} --database=${MYSQL_BASE} -e"TRUNCATE historique"
	mysqldump --databases ${MYSQL_BASE} --complete-insert --force --compress --user=${MYSQL_USER} --password=${MYSQL_PASS} >${DOSSIER}/../extract/${MYSQL_BASE}.sql
	if [ -f ${DOSSIER}/../extract/${MYSQL_BASE}.sql.gz ]
	then
	 	rm ${DOSSIER}/../extract/${MYSQL_BASE}.sql.gz
	fi
	gzip ${DOSSIER}/../extract/${MYSQL_BASE}.sql
#     echo "databases=${MYSQL_BASE} user=${MYSQL_USER} password=${MYSQL_PASS}"
      ls -hltr ${DOSSIER}/../extract/${MYSQL_BASE}*
  else
    echo "pas de fichier ${file}"
  fi
done
