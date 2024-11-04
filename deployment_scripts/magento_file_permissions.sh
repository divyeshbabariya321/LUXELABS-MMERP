#!/bin/bash

SCRIPT_NAME=`basename $0`
. /opt/etc/mysql-creds.conf

for i in "$@"
do
case $i in
    -s=*|--server=*)
    SERVER="${i#*=}"
    ;;
    -w=*|--searchpath=*)
    WEBSITE="${i#*=}"
    ;;
    -d=*|--lib=*)
    CWDDIR="${i#*=}"
    ;;
    --default)
    DEFAULT=YES
    ;;
    *)
            echo "Please Verify options"
    ;;
esac
done



#SERVER="212.90.120.84"
USER="root"
#CWDDIR="/home/prod-1-1/current/var/"
PORT="22"


ssh -i $SSH_KEY -p $PORT $USER@$SERVER "find $CWDDIR/var/ -type d -maxdepth 3 -exec chown -R www-data:www-data {} \;" | tee -a ${SCRIPT_NAME}.log
ssh -i $SSH_KEY -p $PORT $USER@$SERVER "find $CWDDIR/var/ -type d -maxdepth 3 -exec ls --time-style=long-iso -ldh {} \;" | awk -v SERVER="$SERVER" '{print SERVER "," $8 "," $3 "," $4 "," $1 "," $6 " " $7 "," $6 " " $7}' > /tmp/file_permissions.csv | tee -a ${SCRIPT_NAME}.log
mysqlimport -h $DB_HOST -u $DB_USERNAME -p$DB_PASSWORD -f --local  --columns server,instance,owner,groupowner,permission,created_at,updated_at  --fields-terminated-by=, --lines-terminated-by="\n" erp_live /tmp/file_permissions.csv | tee -a ${SCRIPT_NAME}.log


if [[ $? -eq 0 ]]
then
   STATUS="Successful"
else
   STATUS="Failed"
fi

#Call monitor_bash_scripts

sh $SCRIPTS_PATH/monitor_bash_scripts.sh ${SCRIPT_NAME} ${STATUS} ${SCRIPT_NAME}.log
