#!/bin/bash

SCRIPT_NAME=`basename $0`

function HELP {
	echo "-f|--function: add/delete/disable"
	echo "-s|--server: Server IP"
	echo "-t|--type: User Type ssh/db"
	echo "-u|--user: Username"
	echo "-p|--password: Password"
	echo "-l|--ltype: login type"
	echo "-r|--keygen: generate / regenerate new key"
	echo "-R|--role user role"
}

SSH_KEY="/opt/BKPSCRIPTS/id_rsa_websites"
args=("$@")
idx=0
while [[ $idx -lt $# ]]
do
        case ${args[$idx]} in
	        -f|--function)
	        function="${args[$((idx+1))]}"
	        idx=$((idx+2))
	        ;;
	        -s|--server)
	        server="${args[$((idx+1))]}"
	        idx=$((idx+2))
	        ;;
	        -t|--type)
	        type="${args[$((idx+1))]}"
	        idx=$((idx+2))
	        ;;
	        -u|--user)
	        user="${args[$((idx+1))]}"
	        idx=$((idx+2))
	        ;;
	        -p|--password)
	        password="${args[$((idx+1))]}"
	        idx=$((idx+2))
	        ;;
	        -l|--ltype)
	        ltype="${args[$((idx+1))]}"
	        idx=$((idx+2))
	        ;;
	        -r|--keygen)
	        keygen="${args[$((idx+1))]}"
	        idx=$((idx+2))
	        ;;
	        -R|--role)
	        role="${args[$((idx+1))]}"
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


status=success

function createuser()
{
	ssh -i $SSH_KEY root@$server "adduser $user"
	if [ "$?" -eq 1 ]
	then
		ssh root@$server "echo '$user:$password' | chpasswd" | tee -a ${SCRIPT_NAME}.log
	else
		echo "db" | tee -a ${SCRIPT_NAME}.log
    status=fail
	fi
}	
	
function listuser()
{

  ssh -i $SSH_KEY root@$server "awk -F':' '{ print $1}' /etc/passwd"
	if [ "$?" -eq 1 ]
	then
		status=fail
	fi
}

function deleteuser()
{
  ssh -i $SSH_KEY root@$server "deluser $user "
	if [ "$?" -eq 1 ]
	then
		status=fail
	fi
}

case $function in

  createuser)
	  createuser
    ;;

  listuser)
          listuser
    ;;

  deleteuser)
          deleteuser
    ;;
  sync)
          sync
    ;;
  status)
          getstatus
    ;;

  *)
          echo "Failed"
    ;;
esac


echo "{\"status\":\"$status\"}"

if [[ $? -eq 0 ]]
then
   STATUS="Successful"
else
   STATUS="Failed"
fi

#Call monitor_bash_scripts

sh $SCRIPTS_PATH/monitor_bash_scripts.sh ${SCRIPT_NAME} ${STATUS} ${SCRIPT_NAME}.log

