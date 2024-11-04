#!/bin/bash

SCRIPT_NAME=`basename $0`

SSHPORT="22480 2112 22"
MY_CREDS=/opt/etc/mysql-creds.conf
source $MY_CREDS

function Add {
		
	ssh -p $PORT -i ~/.ssh/id_rsa root@$SERVER "ufw insert 1 allow proto tcp from $IP to any port '80,443' comment '$comment'" | tee -a ${SCRIPT_NAME}.log
}

function List {
	ssh -p $PORT -i ~/.ssh/id_rsa root@$SERVER "ufw status numbered|tr '][' ' '|grep 80,443|awk '{print \$1,\$5,\$7}'" | tee -a ${SCRIPT_NAME}.log
}

function Delete {
	ssh -p $PORT -i ~/.ssh/id_rsa root@$SERVER "ufw status numbered|tr '][' ' '|grep 80,443|awk '{print \$1}' |grep -w \"$IP_Numbered\"" | tee -a ${SCRIPT_NAME}.log
	if [ $? -eq 0 ]
	then
		ssh -p $PORT -i ~/.ssh/id_rsa root@$SERVER "yes|ufw delete $IP_Numbered" | tee -a ${SCRIPT_NAME}.log
	else
		echo "Number is not in the list" | tee -a ${SCRIPT_NAME}.log
		exit 1
	fi
}

function HELP {
	echo " -f: Function (add - Add New Ip for web access)
		(delete - Delete Ip for web access)
		(list - List of ips who have access to erp)"
	echo "-n|--number: Number in list of ips which need to delete for web access"
	echo "-i|--ip: Ip address to add in whitelist for erp access"
	echo "-c|--comment: Comment to show ip belongs to which user/system for whitelist for erp access"
}

args=("$@")
idx=0
while [[ $idx -lt $# ]]
do
        case ${args[$idx]} in
	        -f|--function)
	        function="${args[$((idx+1))]}"
	        idx=$((idx+2))
	        ;;
	        -n|--number)
	        IP_Numbered="${args[$((idx+1))]}"
	        idx=$((idx+2))
	        ;;
	        -i|--ip)
	        IP="${args[$((idx+1))]}"
	        idx=$((idx+2))
	        ;;
	        -c|--comment)
	        comment="${args[$((idx+1))]}"
	        idx=$((idx+2))
	        ;;
		-s|--server)
                SERVER="${args[$((idx+1))]}"
                idx=$((idx+2))
                ;;
		-e|--email)
                EMAIL="${args[$((idx+1))]}"
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


if [ -z $SERVER ]
then
	SERVER=`echo $HOSTNAME`
fi

if [ -z $EMAIL ]
then
        EMAIL="security@thluxuryunlimited.com"
fi


for portssh in $SSHPORT
do
	ssh -p $portssh  -i ~/.ssh/id_rsa -q root@$SERVER 'exit' &>> ${SCRIPT_NAME}.log
	if [ $? -ne 255 ]
	then
	        PORT=`echo $portssh`
	fi
done

if [ "$function" = "add" ]
then
	Add
elif [ "$function" = "delete" ]
then
	Delete
elif [ "$function" = "list" ]
then
	List
fi

if [ ! -z $EMAIL ]
then
        mysql -u $DB_USERNAME -h $DB_HOST -p$DB_PASSWORD erp_live -e "insert into ip_logs(server_name,email,ip,is_user,status,message,created_at,updated_at) values('$SERVER','$EMAIL','$IP','1','0','Rule Adde by ERP',now(),now())"
fi

if [[ $? -eq 0 ]]
then
   STATUS="Successful"
else
   STATUS="Failed"
fi

#Call monitor_bash_scripts

sh $SCRIPTS_PATH/monitor_bash_scripts.sh ${SCRIPT_NAME} ${STATUS} ${SCRIPT_NAME}.log
