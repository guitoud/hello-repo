#!/bin/ksh
#set -x 
#Variable
date_log=`date +%Y%m%d%H%M%S`
#
if [ `hostname` = "rlvmdeva5" ]; 
then 
	ENV=`pwd | cut -c2-2`
else 
	ENV="$(dirname $0 | cut -c2)"
fi
#ENV=`pwd | cut -c2-2` # pour dev
#ENV="$(dirname $0 | cut -c2)" # pour prod
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
# Fonction W_Correspondance_ear_jvm                                             #
#################################################################################
W_Correspondance_ear_jvm()
{
	TYPE="Correspondance_ear_jvm"
	NB=$(ls -1 ${DOSSIER_WWW_pour_portail_ope} |grep ${TYPE}|wc -l)
	if [ "${NB}" != "0" ]	
	then
	TRUNCATE=$(mysql --user=${MYSQL_USER} --password=${MYSQL_PASS} --database=${MYSQL_BASE} -e "TRUNCATE TABLE \`correspondance_ear_jvm\`")	
	for file in `ls -1 ${DOSSIER_WWW_pour_portail_ope} |grep ${TYPE}`
	do
		>${DOSSIER_WWW}scripts/flag_import_correspondance_jvm
		echo "traitement de : "$file

		cat ${DOSSIER_WWW_pour_portail_ope}${file} |grep -v ^# |grep "|" >${DOSSIER_WWW_pour_portail_ope}${file}_tempw
		while read LIGNE
		do
			CodeAppl=$(echo ${LIGNE}|awk -F "|" '{print $1}'| sed 's/ //g')
			Cible=$(echo ${LIGNE}|awk -F "|" '{print $2}'| sed 's/ //g')
			Identifiant=$(echo ${LIGNE}|awk -F "|" '{print $3}'| sed 's/ //g')
			JVM=$(echo ${LIGNE}|awk -F "|" '{print $4}'| sed 's/ //g')
			
          		mysql --user=${MYSQL_USER} --password=${MYSQL_PASS} --database=${MYSQL_BASE} -e "INSERT INTO \`correspondance_ear_jvm\` (\`CORRESPONDANCE_EAR_JVM_ID\`, \`CODEAPPLI\`, \`CIBLE\`, \`IDENTIFIANT\`, \`JVM\`) VALUES (NULL, '${CodeAppl}', '${Cible}', '${Identifiant}', '${JVM}');"
          		
          	done<${DOSSIER_WWW_pour_portail_ope}${file}_tempw
          	
          	SVG_FILE ${file}
          	rm ${DOSSIER_WWW_pour_portail_ope}${file}_tempw

	done
	fi
	OPTIMIZE=$(mysql --user=${MYSQL_USER} --password=${MYSQL_PASS} --database=${MYSQL_BASE} -e "OPTIMIZE TABLE \`correspondance_ear_jvm\`")
}

#################################################################################
# Fonction W_correspondance_jvm_serveur                                         #
#################################################################################
W_correspondance_jvm_serveur()
{
	TYPE="correspondance_jvm_serveur"
	NB=$(ls -1 ${DOSSIER_WWW_pour_portail_ope} |grep ${TYPE}|wc -l)
        if [ "${NB}" != "0" ]
        then
	TRUNCATE=$(mysql --user=${MYSQL_USER} --password=${MYSQL_PASS} --database=${MYSQL_BASE} -e "TRUNCATE TABLE \`correspondance_jvm_serveur\`")
	TRUNCATE=$(mysql --user=${MYSQL_USER} --password=${MYSQL_PASS} --database=${MYSQL_BASE} -e "TRUNCATE TABLE \`correspondance_liste_serveur\`")
	
	for file in `ls -1 ${DOSSIER_WWW_pour_portail_ope} |grep ${TYPE}`
	do
		>${DOSSIER_WWW}scripts/flag_import_correspondance_jvm
		echo "traitement de : "$file
		cat ${DOSSIER_WWW_pour_portail_ope}${file} |grep -v ^# >${DOSSIER_WWW_pour_portail_ope}${file}_tempw
		cat ${DOSSIER_WWW_pour_portail_ope}${file} |grep -v ^#|grep -v ^$| awk '{print $NF}' | tr "," "\n" | sort | uniq>${DOSSIER_WWW_pour_portail_ope}${file}_tempsrv
		while read LIGNE
		do
			jvm=$(echo ${LIGNE}|awk '{print $1}'| sed 's/ //g')
			type_serv_app=$(echo ${LIGNE}|awk '{print $2}'| sed 's/ //g')
			user=$(echo ${LIGNE}|awk '{print $3}'| sed 's/ //g')
			application=$(echo ${LIGNE}|awk '{print $4}'| sed 's/ //g')
			serveur=$(echo ${LIGNE}|awk '{print $NF}'| sed 's/ //g')
			
			if [ "${jvm}" != "" ]
			then			
          			mysql --user=${MYSQL_USER} --password=${MYSQL_PASS} --database=${MYSQL_BASE} -e "INSERT INTO \`correspondance_jvm_serveur\` (\`CORRESPONDANCE_JVM_SERVEUR_ID\`, \`JVM\`, \`TYPE_SERV_APP\`, \`USER\`, \`APPLICATION\`, \`SERVEURS\`) VALUES (NULL, '${jvm}', '${type_serv_app}', '${user}', '${application}', '${serveur}');"
          		fi
          	done<${DOSSIER_WWW_pour_portail_ope}${file}_tempw
          
          	while read LIGNE
		do
			serveur=$(echo ${LIGNE}| sed 's/ //g')
			if [ "${serveur}" != "" ]
			then
				mysql --user=${MYSQL_USER} --password=${MYSQL_PASS} --database=${MYSQL_BASE} -e "INSERT INTO \`correspondance_liste_serveur\` (\`CORRESPONDANCE_LISTE_SERVEUR_ID\`, \`NOM_SERVEUR\`) VALUES (NULL, '${serveur}');"		
			fi
		done<${DOSSIER_WWW_pour_portail_ope}${file}_tempsrv
		SVG_FILE ${file}
          	rm ${DOSSIER_WWW_pour_portail_ope}${file}_tempw 
		rm ${DOSSIER_WWW_pour_portail_ope}${file}_tempsrv

	done
	fi
	OPTIMIZE=$(mysql --user=${MYSQL_USER} --password=${MYSQL_PASS} --database=${MYSQL_BASE} -e "OPTIMIZE TABLE \`correspondance_jvm_serveur\`")
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
	W_correspondance_jvm_serveur
	W_Correspondance_ear_jvm
	

  else
  	echo "pas de mysql up"
  fi
else
  echo "pas de fichier ${file_conf}"
fi
