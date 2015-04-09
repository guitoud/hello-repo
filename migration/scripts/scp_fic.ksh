#!/bin/ksh
###############################################################################################################################
# Auteur: Elisabeth HALIN
# Script : Recuperation des fichiers à traiter dans le cadre du traitement de mise a jours 
#          de la table de correspondance entre jobs applicatifs et job CTLM.
# Periodicite : mensuelle
# Date de creation : 10/11/10
# version : le 10/11/10 V1.0
#
# USAGE : iab5cp5a01.ksh 
#
# exit 2 : probleme avec les parametres
# exit 3 : serveur injoignable
# exit 4 : Aucun fichier a traiter
# exit 5 : Probleme pendant le transfert
###############################################################################################################################

set -x

# Déclaration fonctions
USAGE()
{
  echo " ############################################"
  echo " #   Erreurs dans l'appel du script         #"
  echo " #          USAGE :   iab5cp5a01.ksh        #"
  echo " ############################################"
  exit 2
}

# Déclaration des variables

CODE_RETOUR=0
DATE=`date +%Y%m%d%H%M%S`
FIC_LOG=${FLOG}/m/$(basename $0 | sed 's#.ksh##g').${DATE}.log
#LOG=${FLOG}/m/$(basename $0).${DATE}.log
SRV_CTLM="raproa1"
SRV_PORTAIL="rlproa5"
REP_SRC="/xiab01/fichiers/fic"
REP_DEST="/xexploit/php/iab/extract/pour_portail_ope"
TYPE="Correspondance_CTRLM-Applicatif*_OK"


# Controle SRV_PORTAIL
ping -c1 $SRV_PORTAIL >> ${LOG}
if [ $? != "0" ]
then
  echo "serveur $SRV_PORTAIL injoignable" >> ${LOG}
  exit 3
fi

# Nombre de fichiers a traiter
NB=`ls -1 "${REP_SRC}" |grep ${TYPE} |grep _OK |wc -l`
echo "Nombre de fichiers à traiter :" ${NB} >> ${LOG}

if [ ${NB} != "0" ]
then
  # traitement des fichiers
  for file in `ls -1 "${REP_SRC}" |grep ${TYPE} |grep _OK`
    do
    ${VARDDRCPD} ${REP_SRC}/${file} ${SERV_PORTAIL}:${REP_DEST}
    if [ $? -eq 0 ]
    then
      echo "Transfert reussi de ${file}" >> ${LOG}
    else
      echo "Transfert en echec de ${file}" >> ${LOG}
      CODE_RETOUR=5
    fi
  done
else
  echo "Aucun fichier de type ${TYPE} a traiter"
  echo "Verifier les traitements en amont"
  CODE_RETOUR=4
fi

#
exit ${CODE_RETOUR}

