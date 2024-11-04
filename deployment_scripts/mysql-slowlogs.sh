#!/bin/bash


SCRIPT_NAME=`basename $0`

function HELP {
	echo "-f enable/disable"
}

args=("$@")
idx=0
while [[ $idx -lt $# ]]
do
        case ${args[$idx]} in
	        -f)
	        function="${args[$((idx+1))]}"
	        idx=$((idx+2))
	        ;;
	        *)
	        idx=$((idx+1))
	        ;;
	esac
done

#################################################################################################################################################
#################################################################################################################################################
if [ $function == "enable" ]
then
	ssh -i ~/.ssh/id_rsa root@erp.theluxuryunlimited.com -p2112 "sed -i 's/#slow_query_log/slow_query_log/g' /etc/mysql/mariadb.conf.d/50-server.cnf ; sed -i 's/#long_query_time/long_query_time/g' /etc/mysql/mariadb.conf.d/50-server.cnf ; service mariadb restart" | tee -a ${SCRIPT_NAME}.log
elif [ $function == "disable" ]
then
	ssh -i ~/.ssh/id_rsa root@erp.theluxuryunlimited.com -p2112 "sed -i 's/^slow_query_log/#slow_query_log/g' /etc/mysql/mariadb.conf.d/50-server.cnf ; sed -i 's/^long_query_time/#long_query_time/g' /etc/mysql/mariadb.conf.d/50-server.cnf ; service mariadb restart" | tee -a ${SCRIPT_NAME}.log
fi


if [[ $? -eq 0 ]]
then
   STATUS="Successful"
else
   STATUS="Failed"
fi

#Call monitor_bash_scripts

sh $SCRIPTS_PATH/monitor_bash_scripts.sh ${SCRIPT_NAME} ${STATUS} ${SCRIPT_NAME}.log
