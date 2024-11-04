#!/bin/bash

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

. /opt/etc/mysql-creds.conf

for portssh in $possible_ssh_port
do
        ssh -p $portssh  -i ~/.ssh/id_rsa -q root@$server 'exit' &>> ${SCRIPT_NAME}.log
        if [ $? -ne 255 ]
        then
                PORT=`echo $portssh`
        fi
done
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
    ssh -i $SSH_KEY -p $PORT root@$server "useradd -d /home/$user -s /bin/bash -p '$password' $user"
    ssh -i $SSH_KEY -p $PORT root@$server "echo '$user:$password' >> /etc/webmin/miniserv.users"
    ssh -i $SSH_KEY -p $PORT root@$server "echo '$user: backup-config change-user webmincron usermin webminlog webmin help servers acl bacula-backup init passwd quota mount fsdump inittab jailkit ldap-client ldap-useradmin logrotate mailcap mon pam certmgr proc at cron sentry package-updates software man syslog syslog-ng system-status useradmin security-updates apache bind8 pserver dhcpd dhcp-dns dovecot exim fetchmail foobar frox jabber ldap-server majordomo htpasswd-file minecraft mysql openslp postfix postgresql proftpd procmail qmailadmin mailboxes sshd samba sendmail spam squid sarg wuftpd webalizer link adsl-client bandwidth fail2ban firewalld ipsec krb5 firewall firewall6 exports exports-nfs4 nis net xinetd inetd pap ppp-client pptp-client pptp-server stunnel shorewall shorewall6 itsecur-firewall tcpwrappers idmapd filter burner grub lilo raid lvm fdisk lpadmin smart-status time vgetty iscsi-client iscsi-server iscsi-tgtd iscsi-target cluster-passwd cluster-copy cluster-cron cluster-shell cluster-shutdown cluster-software cluster-usermin cluster-useradmin cluster-webmin cfengine heartbeat shell custom disk-usage export-test ftelnet filemin flashterm tunnel file phpini php-pear cpan htaccess-htpasswd ruby-gems telnet ssh ssh2 shellinabox status ajaxterm updown vnc dfsadmin ipfilter ipfw smf bsdexports bsdfdisk format hpuxexports rbac sgiexports zones dnsadmin' >> /etc/webmin/webmin.acl"
    ssh -i $SSH_KEY -p $PORT root@$server "/usr/share/webmin/changepass.pl /etc/webmin $user $password"

    if [ "$?" -eq 1 ]
    then
        status=fail
    fi

}

function listuser()
{

    ssh -i $SSH_KEY -p $PORT root@$server "awk -F':' '{ print $1}' /etc/passwd"
    if [ "$?" -eq 1 ]
    then
        status=fail
    fi
}

function deleteuser()
{
        ssh -i $SSH_KEY -p $PORT root@$server "deluser $user "
    if [ "$?" -eq 1 ]
    then
        status=fail
    fi
}

case $function in

  add)
      createuser  &>> ${SCRIPT_NAME}.log
    ;;

  list)
          listuser  &>> ${SCRIPT_NAME}.log
    ;;

  delete)
          deleteuser  &>> ${SCRIPT_NAME}.log
    ;;
  sync)
          sync  &>> ${SCRIPT_NAME}.log
    ;;
  status)
          getstatus  &>> ${SCRIPT_NAME}.log
    ;;

  *)
          echo "Failed"  &>> ${SCRIPT_NAME}.log
    ;;
esac


echo "{\"status\":\"$status\"}"





SCRIPT_NAME=`basename $0`
if [[ $? -eq 0 ]]
then
   STATUS="Successful"
else
   STATUS="Failed"
fi

#Call monitor_bash_scripts
sh $SCRIPTS_PATH/monitor_bash_scripts.sh ${SCRIPT_NAME} ${STATUS} ${SCRIPT_NAME}.log
