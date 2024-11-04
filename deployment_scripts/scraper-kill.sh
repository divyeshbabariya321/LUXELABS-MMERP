
SCRIPT_NAME=`basename $0`

server=$1
pid=$2
sshpass -p $SSHPASSWORD ssh root@$server.luxelabs.co.uk "kill -9 $pid" | tee -a ${SCRIPT_NAME}.log

if [[ $? -eq 0 ]]
then
   STATUS="Successful"
else
   STATUS="Failed"
fi

#Call monitor_bash_scripts

sh $SCRIPTS_PATH/monitor_bash_scripts.sh ${SCRIPT_NAME} ${STATUS} ${SCRIPT_NAME}.log
