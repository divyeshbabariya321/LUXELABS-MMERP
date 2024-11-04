#!/bin/bash
###  This script is used to autoselect scraper server which has maximum memory available and start scraper there

SCRIPT_NAME=`basename $0`

ScriptDIR=`dirname "$0"`
datetime=`date +%d%b%y-%H:%M`
SSHPORT=22

rm /tmp/scrap_* /opt/scrap_status /tmp/chromimum_service
echo "" > /opt/scrap_restart | tee -a ${SCRIPT_NAME}.log

####################    Get all running Scraper details in all servers #######
function scraper_status
{
	for server in 0{1..9} {10..10}
	do
	        sshpass -p $SSHPASSWORD ssh -p $SSHPORT -o ConnectTimeout=5 root@s$server.luxelabs.co.uk "ps -eo pid,etimes,args|grep command|grep -v externalScraper|grep -v grep|awk -v var=$server '{print var, \$1 , \$2/3600 , \$4}'" >> /opt/scrap_status 2>/dev/null | tee -a ${SCRIPT_NAME}.log
	done
}

####################    Get all Servers Used Memory in % #########
function scraper_memory
{
	rm /tmp/scrap_memory  > /dev/null
	for server in 0{1..9} {10..10}
	do

		Used_mem=`sshpass -p $SSHPASSWORD ssh -p $SSHPORT -o ConnectTimeout=5 root@s$server.luxelabs.co.uk 'free | grep Mem | awk '\''{print $3/$2 * 100.0}'\''' 2>/dev/null`
		echo "checking server mem : $server $Used_mem"
		if [ -z $Used_mem ]
		then
			Used_mem="100.00"
		fi
		echo $server $Used_mem >> /tmp/scrap_memory | tee -a ${SCRIPT_NAME}.log
	done
}

#################  Get list of Scrapers which need to start/restart ##########
function scraper_restart_list
{
	while read scrap
	do
		status=`grep -w $scrap /opt/scrap_status`
		if [ ! -z "$status" ]						### If Scraper is already running
		then
			processing_time=`echo $status|cut -d' ' -f3|cut -d'.' -f1`
			if [ $processing_time -gt 48 ]				### if Scraper processing from more than 2 days then kill and restart
			then
				server=`echo $status|cut -d' ' -f1`
				pid=`echo $status|cut -d' ' -f2`
				sshpass -p $SSHPASSWORD ssh -p $SSHPORT -o ConnectTimeout=5 root@s$server.luxelabs.co.uk "pkill -9 -P $pid ; kill -9 $pid" < /dev/null | tee -a ${SCRIPT_NAME}.log
				sed -i "/$scrap/d" /opt/scrap_status | tee -a ${SCRIPT_NAME}.log
				echo "$scrap" |tee -a /tmp/scrap_restart |tee -a /opt/scrap_restart |tee -a /opt/scraper/scrap-restart-$datetime  | tee -a ${SCRIPT_NAME}.log
			fi
		else
			scrapfile=`echo $scrap|cut -d'.' -f1`
			logfile=`find /mnt/logs/ -mmin -720 -iname "$scrapfile-*.log"|wc -l`
			if [ $logfile -eq 0 ]
			then
				echo "$scrap" |tee -a /tmp/scrap_restart|tee -a /opt/scraper/scrap-start-$datetime | tee -a ${SCRIPT_NAME}.log
			fi
		fi
	done < $ScriptDIR/scraper-list.txt
}
	
####################    Kill all chromium browser process which scrapers are not running #######
function chromium_kill
{
	for server in 0{1..9} {10..10}
	do
	        sshpass -p $SSHPASSWORD ssh -p $SSHPORT -o ConnectTimeout=5 root@s$server.luxelabs.co.uk "ps -eo pid,etimes,args|grep chromium|grep -v grep|awk -v var=$server '{print var, \$1 , \$2/3600 , \$3}'" >> /tmp/chromimum_service 2>/dev/null | tee -a ${SCRIPT_NAME}.log
	done

	while read process
	do
		chromium_time=`echo $process|cut -d' ' -f3|cut -d'.' -f1`
		if [ $chromium_time -gt 50 ]				### if chromium process running from more than 72 hrs
		then
			server=`echo $process|cut -d' ' -f1`
			pid=`echo $process|cut -d' ' -f2`
			echo "chromium kill for s$server.theluxuryunlimited.com : $pid"
			sshpass -p $SSHPASSWORD ssh -p $SSHPORT -o ConnectTimeout=5 root@s$server.luxelabs.co.uk "kill -9 $pid" < /dev/null | tee -a ${SCRIPT_NAME}.log
		fi
	done < /tmp/chromimum_service
}

########### Restart Scraper #####
function scraper_restart
{
	echo "############ Below Scrappers will be Restarted ##############################" | tee -a ${SCRIPT_NAME}.log
	cat /tmp/scrap_restart | tee -a ${SCRIPT_NAME}.log
	while read scraperjs
	do
		scraper_memory < /dev/null
		scraper=`echo $scraperjs|cut -d'.' -f1`
		server=`cat /tmp/scrap_memory|sort -n -k2|head -n1|cut -d' ' -f1`
		minmemory=`cat /tmp/scrap_memory|sort -n -k2|head -n1|cut -d' ' -f2|cut -d'.' -f1`
		if [ $minmemory -gt 75 ]
		then
			email=`sed -ne "/$scraperjs/,$ p" /tmp/scrap_restart|cut -d' ' -f1`
		#	echo $email |mail -s "No Scraper server has free memory more than 25% so exiting script" sahilkataria.1989@gmail.com | tee -a ${SCRIPT_NAME}.log
		#	echo $email |mail -s "No Scraper server has free memory more than 25% so exiting script" yogeshmordani@icloud.com | tee -a ${SCRIPT_NAME}.log
			echo "No server has free memory more than 10%" | tee -a ${SCRIPT_NAME}.log
			chromium_kill
			exit
		fi
		echo $server $scraper $datetime > /tmp/scrap_process | tee -a ${SCRIPT_NAME}.log
		scraperfile=`sshpass -p $SSHPASSWORD ssh -p $SSHPORT -o ConnectTimeout=5 root@s$server.luxelabs.co.uk "find /root/scraper_nodejs/commands/completeScraps/ -iname $scraper.js" < /dev/null`
		sshpass -p $SSHPASSWORD ssh -p $SSHPORT -o ConnectTimeout=5 root@s$server.luxelabs.co.uk "nohup node $scraperfile &> /root/logs/$server/$scraper-$datetime.log &" < /dev/null | tee -a ${SCRIPT_NAME}.log
		echo "$scraperfile started on s$server.theluxuryunlimited.com"
		if [ $? -eq 0 ]
		then
      			sshpass -p $SSHPASSWORD ssh -p $SSHPORT -o ConnectTimeout=5 root@s$server.luxelabs.co.uk "ps -eo pid,etimes,args|grep $scraperjs|grep -v grep|awk -v var=$server '{print var, \$1 , \$2/3600 , \$4}'" >> /opt/scrap_status 2>/dev/null < /dev/null  | tee -a ${SCRIPT_NAME}.log
			date=`date +'%F-%T'`
			day=`date +'%d'`
			echo "$scraper s$server $date Processing-$scraper-$day-s$server" >> /opt/scrap_history | tee -a ${SCRIPT_NAME}.log
		fi
		echo "Wait for 60 Seconds before starting another scrapper" | tee -a ${SCRIPT_NAME}.log
		sleep 5
	done < /tmp/scrap_restart
}

scraper_status
scraper_restart_list
scraper_restart < /dev/null
chromium_kill

if [[ $? -eq 0 ]]
then
   STATUS="Successful"
else
   STATUS="Failed"
fi

#Call monitor_bash_scripts

sh $SCRIPTS_PATH/monitor_bash_scripts.sh ${SCRIPT_NAME} ${STATUS} ${SCRIPT_NAME}.log
