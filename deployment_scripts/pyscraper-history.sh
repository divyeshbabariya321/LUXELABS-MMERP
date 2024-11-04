
SCRIPT_NAME=`basename $0`

while read line
do
	echo "$line"|grep Processing | tee -a ${SCRIPT_NAME}.log
	if [ $? -eq 0 ]
	then
		scraper=`echo "$line"|cut -d' ' -f1`
		server=`echo "$line"|cut -d' ' -f2`
		day=`echo "$line"|cut -d' ' -f3|cut -d'-' -f3`
		ssh -o ConnectTimeout=5 root@$server.theluxuryunlimited.com "ps -eo pid,etimes,args|grep $scraper|grep -v grep" < /dev/null | tee -a ${SCRIPT_NAME}.log
		if [ $? -ne 0 ]
		then
			endtime=`stat -c '%y' /mnt/logs/$server/$scraper-$day.log|cut -d'.' -f1|tr ' ' '-'`
			sed -i "s/Processing-$scraper-$day-$server/$endtime/" /opt/pyscrap_history | tee -a ${SCRIPT_NAME}.log
		fi
	fi
done < /opt/pyscrap_history

if [[ $? -eq 0 ]]
then
   STATUS="Successful"
else
   STATUS="Failed"
fi

#Call monitor_bash_scripts

sh $SCRIPTS_PATH/monitor_bash_scripts.sh ${SCRIPT_NAME} ${STATUS} ${SCRIPT_NAME}.log
