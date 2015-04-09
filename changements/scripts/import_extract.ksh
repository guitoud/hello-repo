#!/bin/ksh
#set -x 
#Variable
date_log=`date +%Y%m%d%H%M%S`
#
#ENV=`pwd | cut -c2-2`
ENV="$(dirname $0 | cut -c2)"
#
DOSSIER_WWW="/"${ENV}"exploit/php/iab/"
DOSSIER_WWW_pour_portail_ope="${DOSSIER_WWW}/extract/pour_portail_ope/"
DOSSIER_WWW_sav_traitement="${DOSSIER_WWW}/extract/sav_traitement/"
file_conf="${DOSSIER_WWW}cf/conf_outil_icdc.php"

## Fonctions
#################################################################################
# Fonction sauvegarde des fichiers                                              #
#################################################################################
SVG_FILE()
{
	mv ${DOSSIER_WWW_pour_portail_ope}${1} ${DOSSIER_WWW_sav_traitement}${1}_${date_log}
}

#################################################################################
# Fonction W_comptages_compilables                                              #
#################################################################################
W_comptages_compilables()
{
	TYPE="comptages_compilables"
	for file in `ls -1 ${DOSSIER_WWW_pour_portail_ope} |grep ${TYPE}`
	do
		echo "traitement de : "$file
		date_file=$( echo $file |sed 's/comptages_compilables_//g')
		#echo $date_file
		mois_file=$( echo $date_file |awk -F '-' '{print $1}')
		annee_file=$( echo $date_file |awk -F '-' '{print $2}')
		#echo $annee_file
		#echo $mois_file
		date_file=${annee_file}${mois_file}
		#echo $date_file
		
		mysql --user=${MYSQL_USER} --password=${MYSQL_PASS} --database=${MYSQL_BASE} -e "SELECT COUNT(\`INDICATEUR_COMPTAGES_ID\`) AS \`NB\` FROM \`indicateur_comptages\` WHERE \`TYPE\`='${TYPE}' AND \`DATE_INDICATEUR\`='${date_file}'">${DOSSIER_WWW_sav_traitement}temp_sql

		if [ $(cat ${DOSSIER_WWW_sav_traitement}temp_sql  | grep -v NB) -ne 0 ]
		then
			DELETE=$(mysql --user=${MYSQL_USER} --password=${MYSQL_PASS} --database=${MYSQL_BASE} -e "DELETE FROM \`indicateur_comptages\` WHERE \`TYPE\`='${TYPE}' AND \`DATE_INDICATEUR\`='${date_file}'")
			OPTIMIZE=$(mysql --user=${MYSQL_USER} --password=${MYSQL_PASS} --database=${MYSQL_BASE} -e "OPTIMIZE TABLE \`indicateur_comptages\`")
		fi
		
		/bin/rm ${DOSSIER_WWW_sav_traitement}temp_sql
		
		while read LIGNE
		do
			#echo "LIGNE :"$LIGNE
			APP=$(echo ${LIGNE}|awk -F ";" '{print $1}')
         		VALEUR=$(echo ${LIGNE}|awk -F ";" '{print $2}')
          		if [ "$APP" != "" ]
          		then
          			if [ "$APP" != "Application" ]
          			then
          				#echo ${APP}";"${VALEUR}
          				mysql --user=${MYSQL_USER} --password=${MYSQL_PASS} --database=${MYSQL_BASE} -e "INSERT INTO \`indicateur_comptages\` (\`INDICATEUR_COMPTAGES_ID\` ,\`TYPE\` ,\`VALEUR\` ,\`APPLICATION\` ,\`DATE_INDICATEUR\` ) VALUES (NULL , '${TYPE}', '${VALEUR}', '${APP}', '${date_file}');"
          			fi
          		fi
          	done<${DOSSIER_WWW_pour_portail_ope}${file} |grep ";"
          	
          	SVG_FILE ${file}

	done
	OPTIMIZE=$(mysql --user=${MYSQL_USER} --password=${MYSQL_PASS} --database=${MYSQL_BASE} -e "OPTIMIZE TABLE \`indicateur_comptages\`")
}

#################################################################################
# Fonction W_comptages_jobs_def                                                 #
#################################################################################
W_comptages_jobs_def()
{
	TYPE="comptages_jobs_def"
	for file in `ls -1 ${DOSSIER_WWW_pour_portail_ope} |grep ${TYPE}`
	do
		echo "traitement de : "$file
		date_file=$( echo $file |sed 's/comptages_jobs_def_//g')
		#echo $date_file
		mois_file=$( echo $date_file |awk -F '-' '{print $1}')
		annee_file=$( echo $date_file |awk -F '-' '{print $2}')
		#echo $annee_file
		#echo $mois_file
		date_file=${annee_file}${mois_file}
		#echo $date_file
		
		mysql --user=${MYSQL_USER} --password=${MYSQL_PASS} --database=${MYSQL_BASE} -e "SELECT COUNT(\`INDICATEUR_COMPTAGES_ID\`) AS \`NB\` FROM \`indicateur_comptages\` WHERE \`TYPE\`='${TYPE}' AND \`DATE_INDICATEUR\`='${date_file}'">${DOSSIER_WWW_sav_traitement}temp_sql

		if [ $(cat ${DOSSIER_WWW_sav_traitement}temp_sql  | grep -v NB) -ne 0 ]
		then
			DELETE=$(mysql --user=${MYSQL_USER} --password=${MYSQL_PASS} --database=${MYSQL_BASE} -e "DELETE FROM \`indicateur_comptages\` WHERE \`TYPE\`='${TYPE}' AND \`DATE_INDICATEUR\`='${date_file}'")
			OPTIMIZE=$(mysql --user=${MYSQL_USER} --password=${MYSQL_PASS} --database=${MYSQL_BASE} -e "OPTIMIZE TABLE \`indicateur_comptages\`")
		fi
		
		/bin/rm ${DOSSIER_WWW_sav_traitement}temp_sql
		
		while read LIGNE
		do
			#echo "LIGNE :"$LIGNE
			APP=$(echo ${LIGNE}|awk -F ";" '{print $1}')
         		VALEUR=$(echo ${LIGNE}|awk -F ";" '{print $2}')
          		if [ "$APP" != "" ]
          		then
          			if [ "$APP" != "Application" ]
          			then
          				#echo ${APP}";"${VALEUR}
          				mysql --user=${MYSQL_USER} --password=${MYSQL_PASS} --database=${MYSQL_BASE} -e "INSERT INTO \`indicateur_comptages\` (\`INDICATEUR_COMPTAGES_ID\` ,\`TYPE\` ,\`VALEUR\` ,\`APPLICATION\` ,\`DATE_INDICATEUR\` ) VALUES (NULL , '${TYPE}', '${VALEUR}', '${APP}', '${date_file}');"
          			fi
          		fi
          	done<${DOSSIER_WWW_pour_portail_ope}${file} |grep ";"
          	
          	SVG_FILE ${file}

	done
	OPTIMIZE=$(mysql --user=${MYSQL_USER} --password=${MYSQL_PASS} --database=${MYSQL_BASE} -e "OPTIMIZE TABLE \`indicateur_comptages\`")
}

#################################################################################
# Fonction W_comptages_lanceursCTRLM                                            #
#################################################################################
W_comptages_lanceursCTRLM()
{
	TYPE="comptages_lanceursCTRLM"
	for file in `ls -1 ${DOSSIER_WWW_pour_portail_ope} |grep ${TYPE}`
	do
		echo "traitement de : "$file
		date_file=$( echo $file |sed 's/comptages_lanceursCTRLM_//g')
		#echo $date_file
		mois_file=$( echo $date_file |awk -F '-' '{print $1}')
		annee_file=$( echo $date_file |awk -F '-' '{print $2}')
		#echo $annee_file
		#echo $mois_file
		date_file=${annee_file}${mois_file}
		#echo $date_file
		
		mysql --user=${MYSQL_USER} --password=${MYSQL_PASS} --database=${MYSQL_BASE} -e "SELECT COUNT(\`INDICATEUR_COMPTAGES_ID\`) AS \`NB\` FROM \`indicateur_comptages\` WHERE \`TYPE\`='${TYPE}' AND \`DATE_INDICATEUR\`='${date_file}'">${DOSSIER_WWW_sav_traitement}temp_sql

		if [ $(cat ${DOSSIER_WWW_sav_traitement}temp_sql  | grep -v NB) -ne 0 ]
		then
			DELETE=$(mysql --user=${MYSQL_USER} --password=${MYSQL_PASS} --database=${MYSQL_BASE} -e "DELETE FROM \`indicateur_comptages\` WHERE \`TYPE\`='${TYPE}' AND \`DATE_INDICATEUR\`='${date_file}'")
			OPTIMIZE=$(mysql --user=${MYSQL_USER} --password=${MYSQL_PASS} --database=${MYSQL_BASE} -e "OPTIMIZE TABLE \`indicateur_comptages\`")
		fi
		
		/bin/rm ${DOSSIER_WWW_sav_traitement}temp_sql
		
		while read LIGNE
		do
			#echo "LIGNE :"$LIGNE
			APP=$(echo ${LIGNE}|awk -F ";" '{print $1}')
         		VALEUR=$(echo ${LIGNE}|awk -F ";" '{print $2}')
          		if [ "$APP" != "" ]
          		then
          			if [ "$APP" != "Application" ]
          			then
          				#echo ${APP}";"${VALEUR}
          				mysql --user=${MYSQL_USER} --password=${MYSQL_PASS} --database=${MYSQL_BASE} -e "INSERT INTO \`indicateur_comptages\` (\`INDICATEUR_COMPTAGES_ID\` ,\`TYPE\` ,\`VALEUR\` ,\`APPLICATION\` ,\`DATE_INDICATEUR\` ) VALUES (NULL , '${TYPE}', '${VALEUR}', '${APP}', '${date_file}');"
          			fi
          		fi
          	done<${DOSSIER_WWW_pour_portail_ope}${file} |grep ";"
          	
          	SVG_FILE ${file}

	done
	OPTIMIZE=$(mysql --user=${MYSQL_USER} --password=${MYSQL_PASS} --database=${MYSQL_BASE} -e "OPTIMIZE TABLE \`indicateur_comptages\`")
}

#################################################################################
# Fonction W_comptages_scripts                                                  #
#################################################################################
W_comptages_scripts()
{
	TYPE="comptages_scripts"
	for file in `ls -1 ${DOSSIER_WWW_pour_portail_ope} |grep ${TYPE}`
	do
		echo "traitement de : "$file
		date_file=$( echo $file |sed 's/comptages_scripts_//g')
		#echo $date_file
		mois_file=$( echo $date_file |awk -F '-' '{print $1}')
		annee_file=$( echo $date_file |awk -F '-' '{print $2}')
		#echo $annee_file
		#echo $mois_file
		date_file=${annee_file}${mois_file}
		#echo $date_file
		
		mysql --user=${MYSQL_USER} --password=${MYSQL_PASS} --database=${MYSQL_BASE} -e "SELECT COUNT(\`INDICATEUR_COMPTAGES_ID\`) AS \`NB\` FROM \`indicateur_comptages\` WHERE \`TYPE\`='${TYPE}' AND \`DATE_INDICATEUR\`='${date_file}'">${DOSSIER_WWW_sav_traitement}temp_sql

		if [ $(cat ${DOSSIER_WWW_sav_traitement}temp_sql  | grep -v NB) -ne 0 ]
		then
			DELETE=$(mysql --user=${MYSQL_USER} --password=${MYSQL_PASS} --database=${MYSQL_BASE} -e "DELETE FROM \`indicateur_comptages\` WHERE \`TYPE\`='${TYPE}' AND \`DATE_INDICATEUR\`='${date_file}'")
			OPTIMIZE=$(mysql --user=${MYSQL_USER} --password=${MYSQL_PASS} --database=${MYSQL_BASE} -e "OPTIMIZE TABLE \`indicateur_comptages\`")
		fi
		
		/bin/rm ${DOSSIER_WWW_sav_traitement}temp_sql
		
		while read LIGNE
		do
			#echo "LIGNE :"$LIGNE
			APP=$(echo ${LIGNE}|awk -F ";" '{print $1}')
         		VALEUR=$(echo ${LIGNE}|awk -F ";" '{print $2}')
          		if [ "$APP" != "" ]
          		then
          			if [ "$APP" != "Application" ]
          			then
          				#echo ${APP}";"${VALEUR}
          				mysql --user=${MYSQL_USER} --password=${MYSQL_PASS} --database=${MYSQL_BASE} -e "INSERT INTO \`indicateur_comptages\` (\`INDICATEUR_COMPTAGES_ID\` ,\`TYPE\` ,\`VALEUR\` ,\`APPLICATION\` ,\`DATE_INDICATEUR\` ) VALUES (NULL , '${TYPE}', '${VALEUR}', '${APP}', '${date_file}');"
          			fi
          		fi
          	done<${DOSSIER_WWW_pour_portail_ope}${file} |grep ";"
          	
          	SVG_FILE ${file}

	done
	OPTIMIZE=$(mysql --user=${MYSQL_USER} --password=${MYSQL_PASS} --database=${MYSQL_BASE} -e "OPTIMIZE TABLE \`indicateur_comptages\`")
}

#################################################################################
# Fonction W_comptages_livraisons                                               #
#################################################################################
W_comptages_livraisons()
{
	TYPE="comptages_livraisons"
	for file in `ls -1 ${DOSSIER_WWW_pour_portail_ope} |grep ${TYPE}`
	do
		echo "traitement de : "$file
		date_file=$( echo $file |sed 's/comptages_livraisons//g'|sed 's/.csv//g')
		#echo $date_file
		mois_file=$( echo $date_file |awk -F '-' '{print $2}')
		annee_file=$( echo $date_file |awk -F '-' '{print $1}')
		#echo $annee_file
		#echo $mois_file
		date_file=${annee_file}${mois_file}
		#echo $date_file
		
		mysql --user=${MYSQL_USER} --password=${MYSQL_PASS} --database=${MYSQL_BASE} -e "SELECT COUNT(\`INDICATEUR_LIVRAISON_ID\`) AS \`NB\` FROM \`indicateur_livraisons\` WHERE \`DATE_INDICATEUR\`='${date_file}'">${DOSSIER_WWW_sav_traitement}temp_sql

		if [ $(cat ${DOSSIER_WWW_sav_traitement}temp_sql  | grep -v NB) -ne 0 ]
		then
			DELETE=$(mysql --user=${MYSQL_USER} --password=${MYSQL_PASS} --database=${MYSQL_BASE} -e "DELETE FROM \`indicateur_livraisons\` WHERE \`DATE_INDICATEUR\`='${date_file}'")
			OPTIMIZE=$(mysql --user=${MYSQL_USER} --password=${MYSQL_PASS} --database=${MYSQL_BASE} -e "OPTIMIZE TABLE \`indicateur_livraisons\`")
		fi
		
		/bin/rm ${DOSSIER_WWW_sav_traitement}temp_sql
		
		while read LIGNE
		do
			#echo "LIGNE :"$LIGNE
			APPLICATION=$(echo ${LIGNE}|awk -F ";" '{print $1}')
        		DATE_LIVRAISON=$(echo ${LIGNE}|awk -F ";" '{print $2}')
        		NB_PACKAGES=$(echo ${LIGNE}|awk -F ";" '{print $3}')
        		NB_SOURCES=$(echo ${LIGNE}|awk -F ";" '{print $4}')
        		NB_SCRIPTS=$(echo ${LIGNE}|awk -F ";" '{print $5}')
        		NB_IHM=$(echo ${LIGNE}|awk -F ";" '{print $6}')
        		NB_AUTRES=$(echo ${LIGNE}|awk -F ";" '{print $7}')
        		NB_TOTAL=$(echo ${LIGNE}|awk -F ";" '{print $8}')
          		if [ "$APPLICATION" != "" ]
          		then
          			if [ "$APPLICATION" != "appli" ]
          			then
          				mysql --user=${MYSQL_USER} --password=${MYSQL_PASS} --database=${MYSQL_BASE} -e "INSERT INTO \`indicateur_livraisons\` (\`INDICATEUR_LIVRAISON_ID\` ,\`APPLICATION\` ,\`DATE_LIVRAISON\` ,\`NB_PACKAGES\` ,\`NB_SOURCES\` ,\`NB_SCRIPTS\` ,\`NB_IHM\` ,\`NB_AUTRES\` ,\`NB_TOTAL\` ,\`DATE_INDICATEUR\` ) VALUES (NULL , '${APPLICATION}', '${DATE_LIVRAISON}', '${NB_PACKAGES}', '${NB_SOURCES}', '${NB_SCRIPTS}', '${NB_IHM}', '${NB_AUTRES}', '${NB_TOTAL}', '${date_file}');"
          			fi
          		fi
          	done<${DOSSIER_WWW_pour_portail_ope}${file} |grep ";"
          	
          	SVG_FILE ${file}

	done
	OPTIMIZE=$(mysql --user=${MYSQL_USER} --password=${MYSQL_PASS} --database=${MYSQL_BASE} -e "OPTIMIZE TABLE \`indicateur_livraisons\`")
}

if [ -f ${file_conf} ]
then
#    echo "il y a le fichier ${file_conf}"
  if [  -f /var/run/mysqld/mysqld.pid ]
  then
	MYSQL_USER=$(cat ${file_conf} |grep "\$user" | grep -v mysql_connect | awk -F "'" '{print $2}')
	MYSQL_PASS=$(cat ${file_conf} |grep "\$password" | grep -v mysql_connect | awk -F "'" '{print $2}')
	MYSQL_BASE=$(cat ${file_conf} |grep "\$database" | grep -v mysql_connect | awk -F "'" '{print $2}')
	#echo "databases=${MYSQL_BASE} user=${MYSQL_USER} password=${MYSQL_PASS}"
	W_comptages_compilables
	W_comptages_jobs_def
	W_comptages_lanceursCTRLM
	W_comptages_scripts
	W_comptages_livraisons
  else
  	echo "pas de mysql up"
  fi
else
  echo "pas de fichier ${file_conf}"
fi
