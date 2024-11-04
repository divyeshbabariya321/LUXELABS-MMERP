#!/bin/bash

SCRIPT_NAME=`basename $0`

function HELP {
	echo "--server: Server Name"
	echo "--debug: true/false"
}

args=("$@")
idx=0
while [[ $idx -lt $# ]]
do
        case ${args[$idx]} in
                --server)
		server="${args[$((idx+1))]}"
                idx=$((idx+2))
                ;;
                --debug)
		debug="${args[$((idx+1))]}"
                idx=$((idx+2))
                ;;
                *)
                idx=$((idx+1))
                ;;
        esac
done
### Load environment variables
. /var/www/erp.theluxuryunlimited.com/.env
. /opt/etc/mysql-creds.conf


for portssh in $possible_ssh_port
do
        ssh -p $portssh  -i ~/.ssh/id_rsa -q root@$server 'exit' &>> ${SCRIPT_NAME}.log
        if [ $? -ne 255 ]
        then
                PORT=`echo $portssh`
        fi
done

if [ $debug = "true" ]
then
	ssh -i $SSH_KEY -p $PORT root@$server "cd /home/*/current/ ; bin/magento setup:config:set --enable-debug-logging=true ; bin/magento dev:query-log:enable ; bin/magento cache:flush" | tee -a ${SCRIPT_NAME}.log
else
	ssh -i $SSH_KEY -p $PORT root@$server "cd /home/*/current/ ; bin/magento setup:config:set --enable-debug-logging=false ; bin/magento dev:query-log:disable ; bin/magento cache:flush ; rm -f var/debug/db.log" | tee -a ${SCRIPT_NAME}.log
fi

if [[ $? -eq 0 ]]
then
   STATUS="Successful"
else
   STATUS="Failed"
fi

#Call monitor_bash_scripts

sh $SCRIPTS_PATH/monitor_bash_scripts.sh ${SCRIPT_NAME} ${STATUS} ${SCRIPT_NAME}.log
