
SCRIPT_NAME=`basename $0`

########## Script will take 2 Command line argument first as Server id , 2nd as scrapper command
server=$1
command=$2

sshpass -p $SSHPASSWORD ssh root@$server.luxelabs.co.uk "nohup node /root/scraper_nodejs/commands/completeScraps/$command &> /root/logs/manual/$command.out &
"

if [[ $? -eq 0 ]]
then
   STATUS="Successful"
else
   STATUS="Failed"
fi

#Call monitor_bash_scripts

sh $SCRIPTS_PATH/monitor_bash_scripts.sh ${SCRIPT_NAME} ${STATUS} ${SCRIPT_NAME}.log
