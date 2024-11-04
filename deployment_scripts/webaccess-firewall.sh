#!/bin/bash

SCRIPT_NAME=$(basename "$0")
SECURITY_GROUP_ID="sg-0704dd5d5233c5312"
AWS_REGION="us-east-2"
SSHPORT="22480 2112 22"
MY_CREDS=/opt/etc/mysql-creds.conf
source "$MY_CREDS"

function Add {
    # Assuming you have AWS CLI installed and configured
    aws ec2 authorize-security-group-ingress \
        --group-id "$SECURITY_GROUP_ID" \
        --protocol tcp \
        --port 80 --port 443 \
        --cidr "$IP/32" \
        --region "$AWS_REGION" | tee -a "${SCRIPT_NAME}.log"
}

function List {
    aws ec2 describe-security-groups \
        --group-ids "$SECURITY_GROUP_ID" \
        --query 'SecurityGroups[0].IpPermissions[?ToPort==`80` || ToPort==`443`].[IpRanges[0].CidrIp]' \
        --output text | tee -a "${SCRIPT_NAME}.log"
}

function Delete {
    # Check if IP is in the security group before attempting to revoke
    if aws ec2 describe-security-groups \
        --group-ids "$SECURITY_GROUP_ID" \
        --query "SecurityGroups[0].IpPermissions[?ToPort==\`80\` || ToPort==\`443\`].[IpRanges[?CidrIp==\`$IP/32\`].CidrIp]" \
        --output text | grep -q "$IP/32"
    then
        aws ec2 revoke-security-group-ingress \
            --group-id "$SECURITY_GROUP_ID" \
            --protocol tcp \
            --port 80 --port 443 \
            --cidr "$IP/32" \
            --region "$AWS_REGION" | tee -a "${SCRIPT_NAME}.log"
    else
        echo "IP is not in the security group" | tee -a "${SCRIPT_NAME}.log"
        exit 1
    fi
}

function HELP {
    echo " -f: Function (add - Add New Ip for web access)"
    echo "           (delete - Delete Ip for web access)"
    echo "           (list - List of ips who have access to erp)"
    echo "-n|--number: Number in list of ips which need to delete for web access"
    echo "-i|--ip: Ip address to add in whitelist for erp access"
    echo "-c|--comment: Comment to show ip belongs to which user/system for whitelist for erp access"
    echo "-s|--server: Server name or IP address"
    echo "-e|--email: Email address"
    echo "-r|--region: AWS region (e.g., us-east-1)"
    echo "-g|--security-group: Security Group ID"
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
        -r|--region)
        AWS_REGION="${args[$((idx+1))]}"
        idx=$((idx+2))
        ;;
        -g|--security-group)
        SECURITY_GROUP_ID="${args[$((idx+1))]}"
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

if [ -z "$SERVER" ]; then SERVER="$(hostname)"; fi
if [ -z "$EMAIL" ]; then EMAIL="security@thluxuryunlimited.com"; fi


if [ "$function" = "add" ]; then
    Add
elif [ "$function" = "delete" ]; then
    Delete
elif [ "$function" = "list" ]; then
    List
fi

if [ ! -z "$EMAIL" ]; then
    mysql -u "$DB_USERNAME" -h "$DB_HOST" -p"$DB_PASSWORD" erp_live -e "insert into ip_logs(server_name,email,ip,is_user,status,message,created_at,updated_at) values('$SERVER','$EMAIL','$IP','1','0','Rule Adde by ERP',now(),now())" | tee -a "${SCRIPT_NAME}.log"
fi

if [ $? -eq 0 ]; then
    STATUS="Successful"
else
    STATUS="Failed"
fi

# Call monitor_bash_scripts
sh "$SCRIPTS_PATH/monitor_bash_scripts.sh" "${SCRIPT_NAME}" "${STATUS}" "${SCRIPT_NAME}.log"

