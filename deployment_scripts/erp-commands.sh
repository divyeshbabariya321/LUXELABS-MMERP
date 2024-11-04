#!/bin/bash

SCRIPT_NAME=`basename $0`

function HELP {
	echo "--command: custom commands" | tee -a ${SCRIPT_NAME}.log
}

args=("$@")
idx=0
while [[ $idx -lt $# ]]
do
        case ${args[$idx]} in
                --command)
		command="${args[$((idx+1))]}"
                idx=$((idx+2))
                ;;
                *)
                idx=$((idx+1))
                ;;
        esac
done
### Load environment variables
. /var/www/erp.theluxuryunlimited.com/.env

cd /var/www/erp.theluxuryunlimited.com

$command | tee -a ${SCRIPT_NAME}.log &>>${SCRIPT_NAME}.log

if [[ $? -eq 0 ]]
then
   STATUS="Successful"
else
   STATUS="Failed"
fi

#Call monitor_bash_scripts

sh $SCRIPTS_PATH/monitor_bash_scripts.sh ${SCRIPT_NAME} ${STATUS} ${SCRIPT_NAME}.log
