#!/bin/bash

. /opt/etc/mysql-creds.conf
SCRIPT_NAME=`basename $0`

function HELP {
  echo "-r|--repo: Repo Name"
  echo "-s|--scope: Scope"
	echo "-c|--code: Scope Code"
	echo "-p|--path: Path variable"
	echo "-v|--value: Value"
	echo "-f|--file: Sync file path"
	echo "-t|--type: sensitive / shared"
	echo "-h|--server: Server Name"
}

args=("$@")
idx=0
while [[ $idx -lt $# ]]
do
        case ${args[$idx]} in
                -r|--repo)
                repo="${args[$((idx+1))]}"
                idx=$((idx+2))
                ;;
                -s|--scope)
                scope="${args[$((idx+1))]}"
                idx=$((idx+2))
                ;;
                -c|--code)
                code="${args[$((idx+1))]}"
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
                -f|--file)
		file="${args[$((idx+1))]}"
                idx=$((idx+2))
                ;;
                -t|--type)
		type="${args[$((idx+1))]}"
                idx=$((idx+2))
                ;;
                -h|--server)
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
### Load environment variables
. /var/www/erp.theluxuryunlimited.com/.env

for portssh in $possible_ssh_port
do
        ssh -p $portssh  -i ~/.ssh/id_rsa -q root@$server 'exit' &>> ${SCRIPT_NAME}.log
        if [ $? -ne 255 ]
        then
                PORT=`echo $portssh`
        fi
done

function set_variable {
	if [ $type != "sensitive" ]
	then
		echo "Shared = php bin/magento --lock-env config:set --scope=$scope --scope-code=$code $path $value" | tee -a ${SCRIPT_NAME}.log
		php bin/magento --lock-env config:set --scope=$scope --scope-code=$code $path "$value" | tee -a ${SCRIPT_NAME}.log
	else
		ssh -p PORT -i $SSH_KEY root@$server "cd /home/*/current/ ; php bin/magento config:sensitive:set --scope=$scope --scope-code=$code $path '$value'" | tee -a ${SCRIPT_NAME}.log
        	if [ $? -ne 0 ]
		then
	                exit 1
	        fi
	fi
}

if [ $type != "sensitive" ]
then
	cd /opt/magento/brands-labels/
	git reset --hard origin/stage  | tee -a ${SCRIPT_NAME}.log
	git pull origin stage  | tee -a ${SCRIPT_NAME}.log
	export COMPOSER_ALLOW_SUPERUSER=1; php8.1 /opt/composer install --ignore-platform-reqs   | tee -a ${SCRIPT_NAME}.log
	php8.1 -f bin/magento -- deploy:mode:set production --skip-compilation  | tee -a ${SCRIPT_NAME}.log
	php8.1 bin/magento app:config:dump  | tee -a ${SCRIPT_NAME}.log
fi
if [ -z $file ]
then
	set_variable
else
	while read line
	do
		scope=`echo $line|cut -d',' -f1`
		code=`echo $line|cut -d',' -f2`
		path=`echo $line|cut -d',' -f3`
		value=`echo $line|cut -d',' -f4`
		set_variable
	done < $file
fi

if [ $type != "sensitive" ]
then
	###### Dump changes from database and push to stage branch ###
	php8.1 bin/magento app:config:dump  | tee -a ${SCRIPT_NAME}.log
	if [ "$repo" == "avoirchic" ]
	then
		cp app/etc/config.php app/design/frontend/LuxuryUnlimited/avoirchic/.deploy/
		git add app/design/frontend/LuxuryUnlimited/avoirchic/.deploy/config.php
	fi

	if [ "$repo" == "brands-labels" ]
	then
		cp app/etc/config.php app/design/frontend/LuxuryUnlimited/brands_labels/.deploy/
		git add app/design/frontend/LuxuryUnlimited/brands_labels/.deploy/config.php
	fi

	if [ "$repo" == "sololuxury" ]
	then
		cp app/etc/config.php app/design/frontend/LuxuryUnlimited/sololuxury/.deploy/
		git add app/design/frontend/LuxuryUnlimited/sololuxury/.deploy/config.php
	fi

	if [ "$repo" == "suvandnat" ]
	then
		cp app/etc/config.php app/design/frontend/LuxuryUnlimited/suvandnat/.deploy/
		git add app/design/frontend/LuxuryUnlimited/suvandnat/.deploy/config.php
	fi

	if [ "$repo" == "veralusso" ]
	then
		cp app/etc/config.php app/design/frontend/LuxuryUnlimited/veralusso/.deploy/
		git add app/design/frontend/LuxuryUnlimited/veralusso/.deploy/config.php
	fi

	git commit -m 'Deployment config erp'  | tee -a ${SCRIPT_NAME}.log
	git push origin stage  | tee -a ${SCRIPT_NAME}.log

	sleep 10
	##### Create PR from stage to master ####
	pull_number=`curl -XPOST -H "Authorization: token $GITHUB_TOKEN" -H "Accept: application/vnd.github.v3+json" https://api.github.com/repos/ludxb/$repo/pulls -d '{"head":"stage","base":"master","title":"config deployment from erp"}' |grep '"number"'|awk '{print $2}'|cut -d',' -f1`

	##### Merge PR ####
	curl -XPUT -H "Authorization: token $GITHUB_TOKEN" https://api.github.com/repos/ludxb/$repo/pulls/$pull_number/merge | tee -a ${SCRIPT_NAME}.log
fi


if [[ $? -eq 0 ]]
then
   STATUS="Successful"
else
   STATUS="Failed"
fi

#Call monitor_bash_scripts

sh $SCRIPTS_PATH/monitor_bash_scripts.sh ${SCRIPT_NAME} ${STATUS} ${SCRIPT_NAME}.log
