#!/bin/ksh
###############################################################################################################################
# Auteur: Elisabeth HALIN
# Script : Import des infos de jobs controlM en base
#          Ce script traite tous les fichiers de type Correspondance_CTRLM-Applicatif qui sont presents dans le repertoire
#          /${ENV}exploit/php/comm_dei_dpi/extract/pour_portail_ope
# Date de creation : 20/10/2010
# version : le 20/10/2010 V1.0
# USAGE : import_ctlm_extract_ctlm.ksh -environnement (r, x, z ...)
#
# exit 2 : probleme avec les parametres
# exit 3 : aucun fichier a traiter
# exit 4 : traitement deja en cours
# exit 5 : base mysql non demarree
# exit 6 : fichier de conf absent
# exit 7 : probleme pendant l'import
# exit 8 : probleme pendant l'archivage
###############################################################################################################################

#set -x

# Declaration fonctions
USAGE()
{
  echo " ###############################################################"
  echo " #   Erreurs dans l'appel du script                            #"
  echo " #          USAGE :                                            #"
  echo " #   import_ctlm_extract_ctlm.ksh -environnement (r, x, z ...) #"
  echo " ###############################################################"
  exit 2
}

ARCHIVE()
{
  MON_FIC=$1
  RES_ARCH=0
  TIMESTAMP=`date +%Y%m%d%H%M%S`
  echo ${TIMESTAMP}
  echo " Archivage du fichier : ${MON_FIC}"

  mv "${FILE}/${MON_FIC}" "${ARCH}/${MON_FIC}.${TIMESTAMP}"
  if [ $? -ne 0 ]
  then
    echo "Probleme dans l'archivage du fichier : ${MON_FIC}"
    RES_ARCH=2
  fi
  echo "RES_ARCH = ${RES_ARCH}"
  return ${RES_ARCH}
}

IMPORT()
{
  echo "###########################################################################################"
  MON_FIC=$1
  RES_IMP=0
  TIMESTAMP1=`date "+%d/%m/%Y %T"`
  TIMESTAMP2=`date "+%Y%m%d%H%M%S"`
  echo "${TIMESTAMP1}"
  echo " Import des datas du fichier ${MON_FIC} en base "
  
# Tracer le traitement du fichier en base
  mysql --user=${MYSQL_USER} --password=${MYSQL_PASS} --database=${MYSQL_BASE} -e "INSERT into \`moteur_trace\` (\`MOTEUR_TRACE_ID\`,\`MOTEUR_TRACE_UTILISATEUR_ID\`,\`MOTEUR_TRACE_DATE\`,\`MOTEUR_TRACE_DATE_TRI\`,\`MOTEUR_TRACE_CATEGORIE\`,\`MOTEUR_TRACE_TABLE\`,\`MOTEUR_TRACE_REF_ID\`,\`MOTEUR_TRACE_ACTION\`,\`MOTEUR_TRACE_ETAT\`) values (NULL, '${USER_SCRIPT}', '${TIMESTAMP1}','${TIMESTAMP2}', 'SCRIPT', 'ctlm_extract_ctlm', '0', 'debut chargement fichier', '${MON_FIC}');"
  TRACE=$?
  if [ ${TRACE} != "0" ]
  then
    echo "Probleme pendant INSERT trace de ${MON_FIC}"
    RES_IMP=2
  fi
  
# Update de la table avant import
  mysql --user=${MYSQL_USER} --password=${MYSQL_PASS} --database=${MYSQL_BASE} -e "UPDATE \`ctlm_extract_ctlm\` set \`ENABLE\`=1 where \`EXTRACT_NOM_FIC_IMPORT\`='${MON_FIC}';"
  UPDATE=$?
  if [ ${UPDATE} != "0" ]
  then
    echo "Probleme pendant UPDATE de la table avant import des datas de ${MON_FIC}"
    RES_IMP=3
  fi
  
# Nombre de lignes a traiter
  LIGNES=`cat "${FILE}/${MON_FIC}" |grep -v ^# |grep ";" |wc -l`
  echo " ${LIGNES} lignes a traiter relatives au fichier ${MON_FIC}"
 
# Import des datas en base
  cat "${FILE}/${MON_FIC}" |grep -v ^# |grep ";" >${FILE}/temp/${file}_tempw
	while read LIGNE
	do
	  application=$(echo ${LIGNE}|awk -F ";" '{print $1}'| sed 's/ //g')
	  table=$(echo ${LIGNE}|awk -F ";" '{print $2}'| sed 's/ //g')
	  group=$(echo ${LIGNE}|awk -F ";" '{print $3}'| sed 's/ //g')
	  repertoire=$(echo ${LIGNE}|awk -F ";" '{print $4}'| sed 's/ //g')
	  shell=$(echo ${LIGNE}|awk -F ";" '{print $5}'| sed 's/ //g')
	  job_ctlm=$(echo ${LIGNE}|awk -F ";" '{print $6}'| sed 's/ //g')
	  nodeid=$(echo ${LIGNE}|awk -F ";" '{print $7}'| sed 's/ //g')
	  cal=$(echo ${LIGNE}|awk -F ";" '{print $8}'| sed 's/ //g')
	  wcal=$(echo ${LIGNE}|awk -F ";" '{print $9}'| sed 's/ //g')
	  scheduling=$(echo ${LIGNE}|awk -F ";" '{print $10}'| sed 's/ //g')
	  job_applicatif=$(echo ${LIGNE}|awk -F ";" '{print $11}'| sed 's/ //g')
			
          mysql --user=${MYSQL_USER} --password=${MYSQL_PASS} --database=${MYSQL_BASE} -e "INSERT INTO \`ctlm_extract_ctlm\` (\`EXTRACT_DATA_ID\`, \`EXTRACT_NOM_FIC_IMPORT\`, \`EXTRACT_DATA_APPLICATION\`, \`EXTRACT_DATA_TABLE_CTLM\`, \`EXTRACT_DATA_GROUP_CTLM\`, \`EXTRACT_DATA_REPERTOIRE\`, \`EXTRACT_DATA_SHELL\`, \`EXTRACT_DATA_JOB_CTLM\`, \`EXTRACT_DATA_NODEID\`, \`EXTRACT_DATA_CAL\`, \`EXTRACT_DATA_WCAL\`, \`EXTRACT_DATA_SCHEDULING\`, \`EXTRACT_DATA_APPLICATIF\`, \`ENABLE\`) VALUES (NULL, '${file}', '${application}', '${table}', '${group}', '${repertoire}', '${shell}', '${job_ctlm}', '${nodeid}', '${cal}', '${wcal}','${scheduling}', '${job_applicatif}', '0');"
          SQL=$?
          if [ ${SQL} != "0" ]
   	  then
     	    #echo "Probleme pendant phase IMPORT des datas de ${MON_FIC}"
     	    echo "donnees non inserees "
   	    RES_IMP=4
   	  fi
          	
        done<${FILE}/temp/${file}_tempw
        
# Verification du nombre de lignes inserees
  LIG_INS=$(mysql --user=${MYSQL_USER} --password=${MYSQL_PASS} --database=${MYSQL_BASE} -e "SELECT COUNT(*) from \`ctlm_extract_ctlm\` where \`EXTRACT_NOM_FIC_IMPORT\`='${MON_FIC}' and \`ENABLE\`='0';" |grep -v COUNT)
  #echo "LIG_INS = ${LIG_INS}"  
  if [ "${LIG_INS}" != "${LIGNES}" ]
  then
    echo " ${LIG_INS} != ${LIGNES} => Le nombre de lignes inserees n'est pas correct"
    RES_IMP=5
  else
    echo " ${LIGNES} lignes ont ete inserees => OK "
  fi
   
# Delete de la table apres import
   mysql --user=${MYSQL_USER} --password=${MYSQL_PASS} --database=${MYSQL_BASE} -e "DELETE from \`ctlm_extract_ctlm\` where \`ENABLE\`=1 and \`EXTRACT_NOM_FIC_IMPORT\`='${MON_FIC}';"
   DELETE=$?
   if [ ${DELETE} != "0" ]
   then
   	echo "Probleme pendant phase SUPPRESSION des datas de ${MON_FIC}"
   	RES_IMP=6
   fi
      
# Suppression du fichier temporaire
   rm ${FILE}/temp/${file}_tempw
   if [ $? -ne 0 ]
   then
   	echo "Probleme dans la suppression du fichier ${FILE}/temp/${file}_tempw"
   	RES_IMP=7
   fi
   
# Optimize des index
   mysql --user=${MYSQL_USER} --password=${MYSQL_PASS} --database=${MYSQL_BASE} -e "OPTIMIZE TABLE \`ctlm_extract_ctlm\`"
   OPTIM=$?
   if [ ${OPTIM} != "0" ]
   then
   	echo "Probleme pendant phase OPTIMISATION des datas de ${MON_FIC}"
   	RES_IMP=8
   fi
   
# Tracer le traitement du fichier en base
   
   TIMESTAMP1=`date "+%d/%m/%Y %T"`
   TIMESTAMP2=`date "+%Y%m%d%H%M%S"`
   mysql --user=${MYSQL_USER} --password=${MYSQL_PASS} --database=${MYSQL_BASE} -e "INSERT into \`moteur_trace\` (\`MOTEUR_TRACE_ID\`,\`MOTEUR_TRACE_UTILISATEUR_ID\`,\`MOTEUR_TRACE_DATE\`,\`MOTEUR_TRACE_DATE_TRI\`,\`MOTEUR_TRACE_CATEGORIE\`,\`MOTEUR_TRACE_TABLE\`,\`MOTEUR_TRACE_REF_ID\`,\`MOTEUR_TRACE_ACTION\`,\`MOTEUR_TRACE_ETAT\`) values (NULL, '${USER_SCRIPT}', '${TIMESTAMP1}', '${TIMESTAMP2}' ,'SCRIPT', 'ctlm_extract_ctlm', '${LIG_INS}', 'fin chargement fichier', '${MON_FIC}');"
   TRACE=$?
   if [ ${TRACE} != "0" ]
   then
    echo "Probleme pendant INSERT trace de ${MON_FIC}"
    RES_IMP=9
   fi
   
   echo "RES_IMP = ${RES_IMP}"
   return ${RES_IMP} 
}

#Recuperation et test sur les parametres
ENV=$1

if [ "${ENV}" == "" ]
then USAGE
fi


#Variables
DATE=`date +%Y%m%d%H%M%S`
REP="/"${ENV}"exploit/php/iab"
TYPE="Correspondance_CTRLM-Applicatif"
FLAG=fichier_flag
file_conf="${REP}/cf/conf_outil_icdc.php"
FILE="${REP}/extract/pour_portail_ope"
ARCH="${REP}/extract/sav_traitement"
SCRIPT="${REP}/scripts"
LOG="${SCRIPT}/log/import_ctlm_extract_ctlm."${DATE}".log"
RESULT=0
SQL=0
UPDATE=0
OPTIM=0
DELETE=0

echo " fichier de LOG : ${LOG}"

#Main

# verification presence du fichier flag
if [ -f "${SCRIPT}/${FLAG}" ]
then
  echo `ls -lrt ${REP}/script/${FLAG}`
  echo " Ce batch est deja en cours de traitement "
  RESULT=4
else
  	touch "${SCRIPT}/${FLAG}"
  	echo " Traitement en cours => positionnement du fichier flag "${SCRIPT}/${FLAG}" " >> ${LOG}

	# verification presence ficher de conf
	if [ -f ${file_conf} ]
	then
	
		# verification presence de la base
		if [  -f /var/run/mysqld/mysqld.pid ]
		  then
			MYSQL_USER=$(cat ${file_conf} |grep "\$user" | grep -v mysql_connect | awk -F "'" '{print $2}')
			MYSQL_PASS=$(cat ${file_conf} |grep "\$password" | grep -v mysql_connect | awk -F "'" '{print $2}')
			MYSQL_BASE=$(cat ${file_conf} |grep "\$database" | grep -v mysql_connect | awk -F "'" '{print $2}')
			#echo "databases=${MYSQL_BASE} user=${MYSQL_USER} password=${MYSQL_PASS}"
			
			# verification existence du user SCRIPT dans la base
			  USER_SCRIPT=$(mysql --user=${MYSQL_USER} --password=${MYSQL_PASS} --database=${MYSQL_BASE} -e "SELECT \`UTILISATEUR_ID\` from \`moteur_utilisateur\` where \`LOGIN\`='SCRIPT';" |grep -v UTILISATEUR_ID)
			  # echo "USER_SCRIPT=${USER_SCRIPT}"
   			  if [ "${USER_SCRIPT}" == "" ]
   			  then
    			   mysql --user=${MYSQL_USER} --password=${MYSQL_PASS} --database=${MYSQL_BASE} -e "INSERT into \`moteur_utilisateur\` (\`UTILISATEUR_ID\`,\`LOGIN\`,\`NOM\`,\`PRENOM\`,\`EMAIL\`,\`EMAIL_FULL\`,\`SOCIETE\`, \`COMPLEMENT\`,\`MDP_MD5\`,\`ACCES\`,\`TYPE_LOGIN\`,\`ENABLE\`) values (NULL, 'SCRIPT', 'SCRIPT', 'SCRIPT', 'SCRIPT','SCRIPT@SCRIPT.com', '','', MD5('SCRIPT'), 'E', 'BDD', 'N' );"
     			   TRACE=$?
      		           if [ ${TRACE} != "0" ]
      			   then
      			     USER_SCRIPT=0
        		     echo "Probleme pendant la creation du USER_CTLM --- Verifier que le user existe bien en base, sinon le creer"
        		     RESULT=9
      			   else
      			     USER_SCRIPT=$(mysql --user=${MYSQL_USER} --password=${MYSQL_PASS} --database=${MYSQL_BASE} -e "SELECT \`UTILISATEUR_ID\` from \`moteur_utilisateur\` where \`LOGIN\`='SCRIPT';" |grep -v UTILISATEUR_ID)
      			     if [ "${USER_SCRIPT}" == "" ]
   			     then
    			      USER_SCRIPT=0
    			     fi
      			   fi
   			  fi
			  # echo "USER_SCRIPT=${USER_SCRIPT}"
			
			# Nombre de fichiers a traiter
			NB=`ls -1 "${FILE}" |grep ${TYPE} |wc -l`
			echo "Nombre de fichiers a traiter :" ${NB} >> ${LOG}
			if [ ${NB} != "0" ]
			then
				# traitement des fichiers
				for file in `ls -1 "${FILE}" |grep ${TYPE}`
				do
					IMPORT ${file} >> ${LOG}
					if [ $? -ne 0 ]
					then 
					  echo "Probleme pendant l'import du fichier ${file} ">>${LOG}
					  RESULT=7
					else
					  echo " Insert datas du fichier ${file} OK "
					  ARCHIVE ${file} >> ${LOG}
					  if [ $? -ne 0 ]
					  then 
					    echo "Probleme pendant l'archivage du fichier ${file} ">>${LOG}
					    RESULT=8
					  else
					    echo " Archivage du fichier ${file} OK "
					  fi
					fi
				done
			else
				echo "Aucun fichier de type ${TYPE} a traiter"
				echo "Verifier les traitements en amont"
				RESULT=3
			fi
	
		  else
		  	echo "la base mysql est arretee"
		  	RESULT=5
		  fi
	
	
	
	else
	  echo " le fichier ${file_conf} est absent"
	  RESULT=6
	fi
	
	# Suppression du fichier FLAG
	rm "${SCRIPT}/${FLAG}"
fi

echo "CODE RETOUR = ${RESULT}"
exit ${RESULT}
