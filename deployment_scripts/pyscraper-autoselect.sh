#!/bin/bash
###  This script is used to autoselect Python scraper server which has maximum memory available and start python scraper there

SCRIPT_NAME=`basename $0`

ScriptDIR=`dirname "$0"`
day=`date +%d`

rm /tmp/pyscrap_* /opt/pyscrap_status

####################    Get all running Scraper details in all servers #######
function pyscraper_status
{
	for server in 0{1..6}
	do
	        ssh -i ~/.ssh/id_rsa -o ConnectTimeout=5 root@s$server.theluxuryunlimited.com "ps -eo pid,etimes,args|grep scrapy|grep -v grep|awk -v var=$server '{print var, \$1 , \$2/3600 , \$6}'" >> /opt/pyscrap_status 2>/dev/null | tee -a ${SCRIPT_NAME}.log
	done
}

####################    Get all Servers Used Memory in % #########
function pyscraper_memory
{
	rm /tmp/pyscrap_memory  > /dev/null
	for server in 0{1..6}
	do
		Used_mem=`ssh -i ~/.ssh/id_rsa -o ConnectTimeout=5 root@s$server.theluxuryunlimited.com 'free | grep Mem | awk '\''{print $3/$2 * 100.0}'\''' 2>/dev/null`
		if [ -z $Used_mem ]
		then
			Used_mem="100.00"
		fi
		echo $server $Used_mem >> /tmp/pyscrap_memory | tee -a ${SCRIPT_NAME}.log
	done
}

#################  Get list of Scrapers which need to start/restart ##########
function pyscraper_restart_list
{
	while read scrap
	do
		status=`grep -w $scrap /opt/pyscrap_status`
		if [ ! -z "$status" ]						### If Scraper is already running
		then
			processing_time=`echo $status|cut -d' ' -f3|cut -d'.' -f1`
			if [ $processing_time -gt 48 ]				### if Scraper processing from more than 2 days then kill and restart
			then
				server=`echo $status|cut -d' ' -f1`
				pid=`echo $status|cut -d' ' -f2`
				ssh -i ~/.ssh/id_rsa root@s$server.theluxuryunlimited.com "kill -9 $pid" < /dev/null | tee -a ${SCRIPT_NAME}.log
				sed -i "/$scrap/d" /opt/pyscrap_status | tee -a ${SCRIPT_NAME}.log
				echo "$scrap restart" >> /tmp/pyscrap_restart | tee -a ${SCRIPT_NAME}.log
			fi
		else
			scrapfile=`echo $scrap|cut -d'.' -f1`
			logfile=`find /mnt/logs/ -mmin -720 -iname "$scrapfile-*.log"|wc -l`
			if [ $logfile -eq 0 ]
			then
				echo "$scrap" >> /tmp/pyscrap_restart
			fi
		fi
	done < $ScriptDIR/pyscraper-list.txt
}
	
########### Restart Scraper #####
function pyscraper_restart
{
	if [ ! -f /tmp/pyscrap_restart ]
	then
		echo "No Python scrappers are pending to scrap in last 12 hrs" | tee -a ${SCRIPT_NAME}.log
		exit
	fi
	echo "############ Below Scrappers will be Restarted ##############################" | tee -a ${SCRIPT_NAME}.log
	cat /tmp/pyscrap_restart | tee -a ${SCRIPT_NAME}.log
	while read scraperjs
	do
		pyscraper_memory < /dev/null
		scraper=`echo $scraperjs|cut -d'.' -f1`
		server=`cat /tmp/pyscrap_memory|sort -n -k2|head -n1|cut -d' ' -f1`
		minmemory=`cat /tmp/pyscrap_memory|sort -n -k2|head -n1|cut -d' ' -f2|cut -d'.' -f1`
		if [ $minmemory -gt 95 ]
		then
			echo "No server has free memory more than 5%" | tee -a ${SCRIPT_NAME}.log
			exit
		fi
		echo $server $scraper | tee -a ${SCRIPT_NAME}.log
		ssh -o ConnectTimeout=5 root@s$server.theluxuryunlimited.com "cd ~/py-scrappers/py-scrappers/$scraper ; nohup scrapy crawl $scraper -o $scraper.json &> /dev/null &" < /dev/null | tee -a ${SCRIPT_NAME}.log
		if [ $? -eq 0 ]
		then
	        	ssh -o ConnectTimeout=5 root@s$server.theluxuryunlimited.com "ps -eo pid,etimes,args|grep scrapy|grep -v grep|awk -v var=$server '{print var, \$1 , \$2/3600 , \$6}'" >> /opt/pyscrap_status 2>/dev/null < /dev/null | tee -a ${SCRIPT_NAME}.log
			date=`date +'%F-%T'`
			day=`date +'%d'`
			echo "$scraper s$server $date Processing-$scraper-$day-s$server" >> /opt/pyscrap_history | tee -a ${SCRIPT_NAME}.log
		fi
		echo "Wait for 30 Seconds before starting another scrapper" | tee -a ${SCRIPT_NAME}.log
		sleep 30
	done < /tmp/pyscrap_restart
}

pyscraper_status
pyscraper_restart_list
pyscraper_restart < /dev/null

if [[ $? -eq 0 ]]
then
   STATUS="Successful"
else
   STATUS="Failed"
fi

#Call monitor_bash_scripts

sh $SCRIPTS_PATH/monitor_bash_scripts.sh ${SCRIPT_NAME} ${STATUS} ${SCRIPT_NAME}.log
