#!/bin/bash

SCRIPT_NAME=`basename $0`
. /opt/etc/mysql-creds.conf
function HELP {
	echo "-f|--function: reindex"
	echo "-s|--server: Server Name"
}

args=("$@")
idx=0
while [[ $idx -lt $# ]]
do
        case ${args[$idx]} in
	        -f|--function)
	        function="${args[$((idx+1))]}"
	        idx=$((idx+2))
	        ;;
	        -s|--server)
	        server="${args[$((idx+1))]}"
	        idx=$((idx+2))
	        ;;
                -h|--help)
	        HELP
	        exit 1
	        ;;
	        *)
	        idx=$((idx+1))
	        ;;
	esac
done

if [ -z $server ] || [ -z $function ]
then
	HELP
	exit
fi

for portssh in $possible_ssh_port
do
        ssh -p $portssh  -i ~/.ssh/id_rsa -q root@$server 'exit' &>> ${SCRIPT_NAME}.log
        if [ $? -ne 255 ]
        then
                PORT=`echo $portssh`
        fi
done

#################################################################################################################################################
#################################################################################################################################################
if [ "$function" = "reindex" ]
then
	hostip=`grep $server'_HOST' /var/www/erp.theluxuryunlimited.com/.env|cut -d'=' -f2`
	ssh -i $SSHKEY -p $PORT root@$hostip "cd /home/*/current/ ; php bin/magento index:reset ; php bin/magento index:reindex ; chown -R www-data.www-data * ; redis-cli -n 0 FLUSHDB; redis-cli -n 1 FLUSHDB; service varnish restart" | tee -a ${SCRIPT_NAME}.log
	#if [ $? -eq 0 ]
	#then
	#	exit 0
	#else
	#	exit 1
	#fi
fi

if [[ $? -eq 0 ]]
then
   STATUS="Successful"
else
   STATUS="Failed"
fi

#Call monitor_bash_scripts

sh $SCRIPTS_PATH/monitor_bash_scripts.sh ${SCRIPT_NAME} ${STATUS} ${SCRIPT_NAME}.log

