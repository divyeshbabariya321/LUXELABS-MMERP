#!/bin/bash

USERNAME="apibuild"
PASSWORD="Theluxury@info"
JENKINS_URL="https://deploy.theluxuryunlimited.com"

source /opt/etc/mysql-creds.conf

# MySQL database connection details
DB_NAME="erp_live"
update_code="0"
vendors="0"
update_meta="0"
magento_composer_install="0"
magento_setup_upgrade="0"
magento_compile="0"
magento_content_deploy="0"
lock=0
copyconfig=0
indexes=0
magento_cache_flush=0

jobs=$(curl -sSL --user "$USERNAME:$PASSWORD" "$JENKINS_URL/api/json" | jq -r '.jobs[].name') &>> ${SCRIPT_NAME}.log
echo $jobs
#for job in $jobs; do
curl -sSL --user "$USERNAME:$PASSWORD" "$JENKINS_URL/api/json" | jq -r '.jobs[].name' | while read job;
do
	echo "$job"
	last_build=$(curl -sSL --user "$USERNAME:$PASSWORD" "$JENKINS_URL/job/$job/api/json" | jq -r '.lastBuild.number') &>> ${SCRIPT_NAME}.log
	log_text=$(curl -sSL --user "$USERNAME:$PASSWORD" "$JENKINS_URL/job/$job/$last_build/consoleText") &>> ${SCRIPT_NAME}.log
	escaped_log_text=$(echo "$log_text" | sed "s/'//g") &>> ${SCRIPT_NAME}.log
	escaped_log_text=$(echo "$escaped_log_text" | sed "s/\"//g") &>> ${SCRIPT_NAME}.log
	escaped_log_text=$(echo "$escaped_log_text" | sed "s/\n/ /g") &>> ${SCRIPT_NAME}.log
	WARNING=`curl -sSL --user "$USERNAME:$PASSWORD" "$JENKINS_URL/job/$job/$last_build/consoleText" | grep -i "undefined\|error" | grep -v installing | grep -v error_email | grep -v error-handler ` &>> ${SCRIPT_NAME}.log
	ERROR=`curl -sSL --user "$USERNAME:$PASSWORD" "$JENKINS_URL/job/$job/$last_build/consoleText" | grep "ERROR: Task"` &>> ${SCRIPT_NAME}.log
	escaped_ERROR=$(echo "$ERROR" | sed "s/'/''/g")
	escaped_WARNING=$(echo "$WARNING" | sed "s/'/''/g")
	#echo "curl -sSL --user \"$USERNAME:$PASSWORD\" \"$JENKINS_URL/job/$job/$last_build/consoleText\""

	echo $job-$last_build
	if [ ! -z "$ERROR" ]
	then
		TASKD=`echo $ERROR| awk '{print $3}' | awk -F ":" '{print $2}'`
		echo $TASKD

		case $TASKD in
  			update_code)
				update_code=1
    			;;

  			vendors)
    				vendors=1
    			;;

  			update-meta)
    				update_meta=1
    			;;

  			magento-composer-install)
    				magento_composer_install=1
    			;;
  			
			magento-setup-upgrade)
    				magento_setup_upgrade=1
    			;;
  			
			magento-compile)
    				magento_compile=1
    			;;
	
  			magento-content-deploy)
    				magento_content_deploy=1
    			;;
	
                        lock)
                       		lock=1
                        ;;
                        
                        copyconfig)
                                copyconfig=1
                        ;;
                        
                        indexes)
                                indexes=1
                        ;;

                        magento_cache_flush)
                                magento_cache_flush=1
                        ;;


  			*)
    				echo "use"
    			;;
		esac

		echo "$job:build failed"
		build_status=1
	else
		echo "$job:build Passed"
		build_status=0
		escaped_ERROR="NA"
	
	fi
echo "$last_build : $job : $ERROR"
DUPLICATE=`mysql -h $DB_HOST -u $DB_USER -p$DB_PASSWORD -D $DB_NAME -N -se "select id from monitor_jenkins_builds where build_number='$last_build'"` &>> ${SCRIPT_NAME}.log
if [ -z "$DUPLICATE" ]
then


	echo "======================Inserting DATA for job $job"


	QUERY="INSERT INTO monitor_jenkins_builds(\`meta_update\`,\`build_number\`,\`project\`,\`worker\`,\`store_id\`,\`clone_repository\`,\`lock_build\`,\`update_code\`,\`composer_install\`,\`make_config\`,\`setup_upgrade\`,\`compile_code\`,\`static_content\`,\`reindexes\`,\`magento_cache_flush\`,\`error\`,\`build_status\`,\`full_log\`,\`created_at\`,\`updated_at\`) VALUES ('$update_meta','$last_build','$job','$job','NA','$update_code','$lock','$update_code','$magento_composer_install','$copyconfig','$magento_setup_upgrade','$magento_compile','$magento_content_deploy','$indexes','$magento_cache_flush','$escaped_ERROR',$build_status,'$escaped_WARNING',now(),now())"
	QUERY1="INSERT INTO monitor_jenkins_builds(\`build_number\`,\`project\`,\`worker\`,\`store_id\`,\`clone_repository\`,\`lock_build\`,\`update_code\`,\`composer_install\`,\`make_config\`,\`setup_upgrade\`,\`compile_code\`,\`static_content\`,\`reindexes\`,\`magento_cache_flush\`,\`error\`,\`build_status\`,\`full_log\`,\`created_at\`,\`updated_at\`) VALUES ('$last_build','$job','$job','NA','$update_code','$lock','$update_code','$magento_composer_install','$copyconfig','$magento_setup_upgrade','$magento_compile','$magento_content_deploy','$indexes','$magento_cache_flush','$escaped_ERROR',$build_status,'escaped_log_text',now(),now())"

	echo "$QUERY1"
	mysql -h $DB_HOST -u $DB_USER -p$DB_PASSWORD $DB_NAME -e "$QUERY" &>> ${SCRIPT_NAME}.log
fi
done


SCRIPT_NAME=`basename $0`
if [[ $? -eq 0 ]]
then
   STATUS="Successful"
else
   STATUS="Failed"
fi

#Call monitor_bash_scripts
sh $SCRIPTS_PATH/monitor_bash_scripts.sh ${SCRIPT_NAME} ${STATUS} ${SCRIPT_NAME}.log

 &>> ${SCRIPT_NAME}.log
