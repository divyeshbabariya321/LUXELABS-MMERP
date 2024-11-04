#!/bin/bash
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

USER="root"
#CWDDIR="/home/prod-1-1/current/var/"
for portssh in $possible_ssh_port
do
        ssh -p $portssh  -i ~/.ssh/id_rsa -q root@$server 'exit' &>> ${SCRIPT_NAME}.log
        if [ $? -ne 255 ]
        then
                PORT=`echo $portssh`
        fi
done

ssh -i $SSH_KEY -p $PORT $USER@$SERVER "find $CWDDIR/var/ -type d -maxdepth 3 -exec chown -R www-data:www-data {} \;"
ssh -i $SSH_KEY -p $PORT $USER@$SERVER "find $CWDDIR/var/ -type d -maxdepth 3 -exec ls --time-style=long-iso -ldh {} \;" | awk -v SERVER="$SERVER" '{print SERVER "," $8 "," $3 "," $4 "," $1 "," $6 " " $7 "," $6 " " $7}' > /tmp/file_permissions.csv
mysqlimport -h $DB_HOST -u $DB_USERNAME -p$DB_PASSWORD -f --local  --columns server,instance,owner,groupowner,permission,created_at,updated_at  --fields-terminated-by=, --lines-terminated-by="\n" $DB_NAME /tmp/file_permissions.csv
