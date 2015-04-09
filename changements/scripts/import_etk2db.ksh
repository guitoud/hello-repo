#!/usr/bin/ksh

host=${1}
if [ "${host}" == "" ]
then
  echo "pas de host"
  exit
fi
file_conf="/rexploit/php/riasogeti/changements/cf/conf_outil_icdc.php"
MYSQL_USER=$(cat ${file_conf} |grep "\$user" | grep -v mysql_connect | awk -F "'" '{print $2}')
MYSQL_PASS=$(cat ${file_conf} |grep "\$password" | grep -v mysql_connect | awk -F "'" '{print $2}')
MYSQL_BASE=$(cat ${file_conf} |grep "\$database" | grep -v mysql_connect | awk -F "'" '{print $2}')

if [ -f ${file_conf} ]
then
  if [ -d /rexploit/php/riasogeti/changements/extract/etk/${host} ]
  then
    for ETK in `ls -1 /rexploit/php/riasogeti/changements/extract/etk/${host} |egrep "^[0-9]*.etk"`
    do
      ETK_ID=$(echo ${ETK}|awk -F "." '{print $1}')
      NB_ENR=$(mysql --user=${MYSQL_USER} --password=${MYSQL_PASS} --database=${MYSQL_BASE} -e "SELECT \`etk_num\` FROM \`editique_etk\` WHERE \`etk_num\`='${ETK_ID}'"| egrep "[0-9]"|wc -l)
      if [ $NB_ENR -eq 0 ]
      then
        echo "mise en base de ${ETK_ID} - NB_ENR = ${NB_ENR}."
        while read line
        do
          ETK_NUM=$(echo ${line}|awk -F "!" '{print $1}'| sed "s/'/./g")
          ETK_SCRIPT=$(echo ${line}|awk -F "!" '{print $2}'| sed 's/ //g'| sed "s/'/./g")
          ETK_CODE=$(echo ${line}|awk -F "!" '{print $3}'| sed 's/ //g'| sed "s/'/./g")
          ETK_DATE=$(echo ${line}|awk -F "!" '{print $4}'|sed 's/\//-/g'| sed "s/'/./"g)
          ETK_TIME=$(echo ${line}|awk -F "!" '{print $5}'| sed "s/'/./g")
          ETK_DATETIME=$(echo "20${ETK_DATE} ${ETK_TIME}")
          ETK_DATEINT=$(echo ${ETK_DATETIME}| sed 's/-//g' | sed 's/ //g'| sed 's/://g'| sed "s/'/./g")
          ETK_DETAIL_PARAM=$(echo ${line}|awk -F "!" '{print $6}'|awk -F "=" '{print $1}'| sed "s/ //g"| sed "s/'/./g")
          ETK_DETAIL_VALUE=$(echo ${line}|awk -F "!" '{print $6}'|awk -F "=" '{print $2}'| sed "s/'/./"g)
          ETK_HOST=$(echo ${host} | sed "s/_old//")
          

         mysql --user=${MYSQL_USER} --password=${MYSQL_PASS} --database=${MYSQL_BASE} -e "INSERT INTO \`editique_etk\` (\`etk_ID\` ,\`etk_num\` ,\`etk_host\` ,\`etk_script\` ,\`etk_code\` ,\`etk_date\` ,\`etk_dateint\` ,\`etk_detail_param\` ,\`etk_detail_value\`)VALUES (NULL , '${ETK_NUM}', '${ETK_HOST}', '${ETK_SCRIPT}', '${ETK_CODE}', '${ETK_DATETIME}', '${ETK_DATEINT}', '${ETK_DETAIL_PARAM}', '${ETK_DETAIL_VALUE}');"
        done < /rexploit/php/riasogeti/changements/extract/etk/${host}/${ETK}
	NB_ENR=$(mysql --user=${MYSQL_USER} --password=${MYSQL_PASS} --database=${MYSQL_BASE} -e "SELECT \`etk_num\` FROM \`editique_etk\` WHERE \`etk_num\`='${ETK_ID}'"| egrep "[0-9]"|wc -l)
	echo "fin mise en base de ${ETK_ID} - NB_ENR = ${NB_ENR}."
	rm /rexploit/php/riasogeti/changements/extract/etk/${host}/${ETK}
      else
        echo "${ETK_ID} deja en base - NB_ENR = ${NB_ENR}."
        rm /rexploit/php/riasogeti/changements/extract/etk/${host}/${ETK}
      fi
    done
  else
    echo "pas de host "${host}
  fi
  OPTIMIZE=$(mysql --user=${MYSQL_USER} --password=${MYSQL_PASS} --database=${MYSQL_BASE} -e "OPTIMIZE TABLE \`editique_etk\`")
fi

