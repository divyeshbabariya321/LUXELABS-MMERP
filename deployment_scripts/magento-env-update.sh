#!/bin/bash
. /opt/etc/mysql-creds.conf
function HELP {
        echo "-w|--website: website"
        echo "-s|--server: Server ip"
        echo "-d|--rootdir: Username"
        echo "-p|--path path"
        echo "-v|--value value"

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
                -v|--value)
                value="${args[$((idx+1))]}"
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

for portssh in $possible_ssh_port
do
        ssh -p $portssh  -i ~/.ssh/id_rsa -q root@$server 'exit' &>> ${SCRIPT_NAME}.log
        if [ $? -ne 255 ]
        then
                PORT=`echo $portssh`
        fi
done

scp -i $SSH_KEY -P $PORT root@$server:$rootdir/app/etc/env.php . &> /dev/null

if [ $? -eq 1 ]
then
	MESSAGE="Unable to copy env.php from remote server $server"
	exit 1;
fi

MESSAGE=`php /var/www/erp.theluxuryunlimited.com/deployment_scripts/magento-env-update.php env.php $path $value` &> /dev/null

if [ "$?" -eq "0" ]
then
        echo "{\"status\":\"true\",\"message\":\"ENV file updated\"}" | tee -a /var/www/erp.theluxuryunlimited.com/storage/app/download_db/test.log
else
        echo "{\"status\":\"FAILED\",\"message\"\:\"$MESSAGE\"}" | tee -a /var/www/erp.theluxuryunlimited.com/storage/app/download_db/test.log
fi

#scp -P $PORT temp_env.php root@$server:$rootdir/current/app/etc/env.php
