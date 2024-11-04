#!/bin/bash


SCRIPT_NAME=`basename $0`

function HELP {
	echo "-f|--function: add/delete"
	echo "-s|--server: Server Name"
	echo "-u|--user: Username"
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
	        -s|--server)
	        server="${args[$((idx+1))]}"
	        idx=$((idx+2))
	        ;;
	        -u|--user)
	        user="${args[$((idx+1))]}"
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

if [ -z $server ] || [ -z $user ] || [ -z $function ]
then
	HELP
	exit
fi

if [ "$function" = "add" ]
then
	########### Command to Create user and Generate Ssh key file for input server name and user ############
	command="id -u $user &>/dev/null || useradd -m -G sudo -s /bin/bash $user -p $(openssl passwd -crypt $user) ; su - $user -c \"echo 'y'|ssh-keygen -f ~/.ssh/id_rsa -N '' ; cat ~/.ssh/id_rsa.pub > ~/.ssh/authorized_keys ; chmod 600 /home/$user/.ssh/authorized_keys ; cat /home/$user/.ssh/id_rsa\""
elif [ "$function" = "delete" ]
then
	####### Command to delete user access to specific server #####
	command="userdel -r $user -f"
fi

#################################################################################################################################################
#################################################################################################################################################
if [ "$server" == "Erp-Server" ]		### Check for Erp Server
then
	ssh -i ~/.ssh/id_rsa root@erp.theluxuryunlimited.com -p2112 "$command" | tee -a ${SCRIPT_NAME}.log

elif [ "$server" == "s01" ] || [ "$server" == "s02" ] || [ "$server" == "s03" ] || [ "$server" == "s04" ] || [ "$server" == "s05" ] || [ "$server" == "s06" ] || [ "$server" == "s07" ] || [ "$server" == "s08" ] || [ "$server" == "s09" ] || [ "$server" == "s10" ] || [ "$server" == "s11" ] || [ "$server" == "s12" ] || [ "$server" == "s13" ] || [ "$server" == "s14" ] || [ "$server" == "s15" ]
then
	ssh -i ~/.ssh/id_rsa root@$server.theluxuryunlimited.com "$command" | tee -a ${SCRIPT_NAME}.log

elif [ "$server" == "Cropper-Server" ]		### Check for Cropper Server
then
	ssh -i ~/.ssh/id_rsa root@178.62.200.246 "$command" | tee -a ${SCRIPT_NAME}.log
else
	hostip=`grep $server'_HOST' /var/www/erp.theluxuryunlimited.com/.env|cut -d'=' -f2`
	ssh -i ~/.ssh/id_rsa root@$hostip "$command" | tee -a ${SCRIPT_NAME}.log
fi


if [[ $? -eq 0 ]]
then
   STATUS="Successful"
else
   STATUS="Failed"
fi

#Call monitor_bash_scripts

sh $SCRIPTS_PATH/monitor_bash_scripts.sh ${SCRIPT_NAME} ${STATUS} ${SCRIPT_NAME}.log
