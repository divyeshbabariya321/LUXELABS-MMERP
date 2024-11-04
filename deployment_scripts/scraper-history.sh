while read line


SCRIPT_NAME=`basename $0`

do
	echo "$line"|grep Processing
	if [ $? -eq 0 ]
	then
		scraper=`echo "$line"|cut -d' ' -f1`
		server=`echo "$line"|cut -d' ' -f2`
		year=`echo $line|cut -d' '  -f3|cut -d'-' -f1|tail -c 3`
		monthnum=`echo $line|cut -d' '  -f3|cut -d'-' -f2`
		day=`echo "$line"|cut -d' ' -f3|cut -d'-' -f3`
		month=`date +%b -d "$year-$monthnum-$day"`
		shpass -p $SSHPASSWORD ssh -p $SSHPORT -o ConnectTimeout=5 root@$server.luxelabs.co.uk "ps -eo pid,etimes,args|grep $scraper|grep -v grep" < /dev/null | tee -a ${SCRIPT_NAME}.log
		if [ $? -ne 0 ]
		then
			endtime=`stat -c '%y' /mnt/logs/$server/$scraper-$day$month$year*.log|cut -d'.' -f1|tr ' ' '-'`
			sed -i "s/Processing-$scraper-$day-$server/$endtime/" /opt/scrap_history | tee -a ${SCRIPT_NAME}.log
		fi
	fi
done < /opt/scrap_history

if [[ $? -eq 0 ]]
then
   STATUS="Successful"
else
   STATUS="Failed"
fi

#Call monitor_bash_scripts

sh $SCRIPTS_PATH/monitor_bash_scripts.sh ${SCRIPT_NAME} ${STATUS} ${SCRIPT_NAME}.log
