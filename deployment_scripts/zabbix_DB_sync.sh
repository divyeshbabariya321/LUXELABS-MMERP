#!/bin/bash

SCRIPT_NAME=`basename $0`

subject=$1
message="DUMMY"
host=$2
etime=$4
odata=$7
severity=$5
eid=$8
dbuser="user"
dbpass="pass"
dbname="db"
dbhost="ip"


echo "eid $subject $message $host $etime $odata $severity $eid" &>> /tmp/hook.log | tee -a ${SCRIPT_NAME}.log

EVENTCHECK=`mysql -h $dbhost -u $dbuser -p$dbpass  -D $dbname -N -se "select event_id from zabbix_webhook_data where event_id = '$eid' order by id limit 1"`
if [ ! -z $EVENTCHECK ]
then
        IS_RESOLVED=`echo "$subject" | grep -i resolved | wc -l`
        echo "RES=======$IS_RESOLVED" &>> /tmp/hook.log | tee -a ${SCRIPT_NAME}.log
        if [ "$IS_RESOLVED" -gt "0" ]
        then
                echo "RES=======Marking resolved" &>> /tmp/hook.log | tee -a ${SCRIPT_NAME}.log
                mysql -h $dbhost -u $dbuser -p$dbpass  -D $dbname -N -se "update zabbix_webhook_data set zabbix_status_id = '1' where event_id = '$eid'" | tee -a ${SCRIPT_NAME}.log
                exit 0
        fi
fi

mysql -h $dbhost -u $dbuser -p$dbpass  -D $dbname -N -se "INSERT INTO zabbix_webhook_data (event_id,subject,message,event_start,host,severity,operational_data,created_at,updated_at) values ('$eid','$subject','$message','$etime','$host','$severity','$odata',now(),now())" | tee -a ${SCRIPT_NAME}.log
echo "INSERT INTO zabbix_webhook_data (event_id,subject,message,event_start,host,severity,operational_data,created_at,updated_at) values ('$eid','$subject','$message','$etime','$host','$severity','$odata',now(),now())" | tee -a ${SCRIPT_NAME}.log

if [[ $? -eq 0 ]]
then
   STATUS="Successful"
else
   STATUS="Failed"
fi

#Call monitor_bash_scripts

sh $SCRIPTS_PATH/monitor_bash_scripts.sh ${SCRIPT_NAME} ${STATUS} ${SCRIPT_NAME}.log
