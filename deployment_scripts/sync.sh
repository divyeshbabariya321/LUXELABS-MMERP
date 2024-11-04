#!/bin/bash
SSH_KEY="/opt/BKPSCRIPTS/id_rsa_websites"
while read line
do 
	SSH_PORT=`echo $line | awk '{print $4}'`
	IP=`echo $line | awk '{print $1}'`
	BKP_SRC=`echo $line | awk '{print $2}'`
	BKP_DST=`echo $line | awk '{print $3}'`

	/usr/bin/rsync -avz --delete --exclude "*.aoffset" -e "ssh -p $SSH_PORT -i $SSH_KEY" root@$IP:$BKP_SRC/current/var/log/ $BKP_DST
	
done <server_ip.list
