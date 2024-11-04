#!/bin/bash

SCRIPT_NAME=`basename $0`

function HELP {
        echo "-w|--website: website"
        echo "-s|--server: Server ip"
        echo "-d|--rootdir: rootdir"
	echo "-m|--modulename modulename"
        echo "-p|--path path"
	echo "-a|--action value(sync,add,enable,disable)"

}

args=("$@")
idx=0
while [[ $idx -lt $# ]]
do
        case ${args[$idx]} in
                -w|--website)
                website="${args[$((idx+1))]}"
                idx=$((idx+2))
                ;;
                -s|--server)
                server="${args[$((idx+1))]}"
                idx=$((idx+2))
                ;;
                -d|--rootdir)
                rootdir="${args[$((idx+1))]}"
                idx=$((idx+2))
                ;;
                -p|--path)
                path="${args[$((idx+1))]}"
                idx=$((idx+2))
                ;;
		-a|--action)
                action="${args[$((idx+1))]}"
                idx=$((idx+2))
                ;;
		-m|--modulename)
                modulename="${args[$((idx+1))]}"
                idx=$((idx+2))
                ;;
                -f|--filename)
		filename="${args[$((idx+1))]}"
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

SSH_KEY="/opt/BKPSCRIPTS/id_rsa_websites"

LOCAL_CSV_FILE="$filename"
REMOTE_CSV_FILE="app/i18n/$filename"
local_storage="/var/www/erp.theluxuryunlimited.com/storage/app/magento/lang/csv"

pull_action() {
    # Step 1: Run Magento command on the remote server to download CSV
    ssh -i $SSH_KEY "root@$server" "cd $rootdir && bin/magento i18n:collect-phrases app" | grep -v "Dictionary successfully processed" > $local_storage/$LOCAL_CSV_FILE | tee -a ${SCRIPT_NAME}.log
    if [ $? -eq 0 ]
    then
	    STATUS="success"
    else
	    STATUS="fail"
    fi

    # Step 2: Download CSV from the remote server to the local machine
#    scp -i $SSH_KEY "root@$server:$rootdir/$REMOTE_CSV_FILE" "$local_storage/$LOCAL_CSV_FILE"
	echo "{\"status\":\"$STATUS\",\"message\":\"$STATUS\",\"path\":\"$local_storage/$LOCAL_CSV_FILE\"}" | tee -a ${SCRIPT_NAME}.log
}

push_action() {
    # Step 1: Transfer the local CSV file to the remote server
#   echo "ssh -i $SSH_KEY \"root@$server\" \"[ -d '$rootdir/app/i18n' ] || mkdir -p '$rootdir/app/i18n'\""
    ssh -i $SSH_KEY "root@$server" "[ -d '$rootdir/app/i18n' ] || mkdir -p '$rootdir/app/i18n'" | tee -a ${SCRIPT_NAME}.log
    if [ $? -eq 0 ]
    then
    	STATUS="success"
    	scp -q -i $SSH_KEY "$local_storage/$LOCAL_CSV_FILE" "root@$server:$rootdir/$REMOTE_CSV_FILE" &> /dev/null tee -a ${SCRIPT_NAME}.log
    else
            STATUS="fail"
    fi
	echo "{\"status\":\"$STATUS\",\"message\":\"$STATUS\",\"path\":\"$local_storage/$LOCAL_CSV_FILE\"}" | tee -a ${SCRIPT_NAME}.log

}

case "$action" in
    pull)
        pull_action
        ;;
    push)
        push_action
        ;;
    *)
	HELP
        exit 1
        ;;
esac

if [[ $? -eq 0 ]]
then
   STATUS="Successful"
else
   STATUS="Failed"
fi

#Call monitor_bash_scripts

sh $SCRIPTS_PATH/monitor_bash_scripts.sh ${SCRIPT_NAME} ${STATUS} ${SCRIPT_NAME}.log