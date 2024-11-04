#!/bin/bash

SCRIPT_NAME=`basename $0`

function HELP {
	echo "--server: dev servername dev1/dev2"
	echo "--site brands/sololuxury/suvandnat"
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
                --site)
		site="${args[$((idx+1))]}"
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


ssh -i $SSH_KEY -p $PORT root@$server "cd /home/$site/public_html/ ; giturl=\$(git config --get remote.origin.url|sed "s/github.com/$GITHUB_TOKEN:$GITHUB_TOKEN@github.com/g") ; git clean -fd ; git checkout stage ; git reset --hard origin/stage ; git checkout stage ; git pull \$giturl stage ; php bin/magento app:config:dump ; php bin/magento setup:upgrade ; php bin/magento setup:di:compile ; php bin/magento s:s:d -f ; php bin/magento cache:f ; chgrp -R www-data . ; chown -R $site ." | tee -a ${SCRIPT_NAME}.log
#if [ $? -ne 0 ]
#then
#	exit 1
#fi

if [[ $? -eq 0 ]]
then
   STATUS="Successful"
else
   STATUS="Failed"
fi

#Call monitor_bash_scripts

sh $SCRIPTS_PATH/monitor_bash_scripts.sh ${SCRIPT_NAME} ${STATUS} ${SCRIPT_NAME}.log

