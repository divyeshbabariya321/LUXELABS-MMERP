#!/bin/bash
SCRIPT_NAME=`basename $0`

. /opt/etc/mysql-creds.conf

read -p "Please enter db name  " a 
mysqldump -h $DB_HOST -u $DB_USERNAME -p$DB_PASSWORD  --no-data $a > $a.schema.sql | tee -a ${SCRIPT_NAME}.log

if [[ $? -eq 0 ]]
then
   STATUS="Successful"
else
   STATUS="Failed"
fi

#Call monitor_bash_scripts

sh $SCRIPTS_PATH/monitor_bash_scripts.sh ${SCRIPT_NAME} ${STATUS} ${SCRIPT_NAME}.log


