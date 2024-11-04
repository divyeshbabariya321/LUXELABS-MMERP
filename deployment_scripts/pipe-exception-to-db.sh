#!/bin/bash
SCRIPT_NAME=`basename $0`

. /opt/etc/mysql-creds.conf

while read line
do 
	SSH_PORT=`echo $line | awk '{print $4}'`
	IP=`echo $line | awk '{print $1}'`
	BKP_SRC=`echo $line | awk '{print $2}'`
	BKP_DST=`echo $line | awk '{print $3}'`
	echo $line

	function logparse()
	{

	for path in $(ssh -n -p $SSH_PORT -i $SSH_KEY root@$IP ls $BKP_SRC/current/var/log/*.log | grep current)
	do
		echo $path
		ERROR=`ssh -n -p $SSH_PORT -i $SSH_KEY root@$IP retail $path | grep -v mixins.min.js | grep -i -A 4 -B 1 "Exception"` &>> ${SCRIPT_NAME}.log
		TYPE="$(basename $path .log)" 
		echo $TYPE

		function quoteSQL() {
		    printf "FROM_BASE64('%s')" "$(echo -n "$ERROR" | base64 -w0 )" 
		}

		echo "$ERROR"
		WEB_ID=`echo $line | awk '{print $5}'`
		if [ ! -z "$ERROR" ]
		then

			mysql -h $DB_HOST -u $DB_USERNAME -p$DB_PASSWORD -e "insert into $DB_NAME.website_logs (sql_query,module,website_id,error,type,file_path,created_at,updated_at,time) values('Stack Trace','NA','$WEB_ID',$(quoteSQL "$PASSWORD"),'$TYPE','$path',now(),now(),'0.0000');"  &>> ${SCRIPT_NAME}.log
		fi

	done
	}
	logparse &
done </opt/BKPSCRIPTS/server_ip.list

if [[ $? -eq 0 ]]
then
   STATUS="Successful"
else
   STATUS="Failed"
fi

#Call monitor_bash_scripts
sh $SCRIPTS_PATH/monitor_bash_scripts.sh ${SCRIPT_NAME} ${STATUS} ${SCRIPT_NAME}.log
~
