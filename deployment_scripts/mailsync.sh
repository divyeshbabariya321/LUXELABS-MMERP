#!/bin/bash
. /opt/etc/mysql-creds.conf

LOGFILE=/opt/pipe-php-email-reader/sync.log

#mysql -h $dbhost -u $dbuser -p$dbpass -D $dbname -N -se "select username, password from email_addresses"

MAIL_PHP="/opt/pipe-php-email-reader/mailsync.php"
EMAILFILE="/opt/pipe-php-email-reader/email.list"
while read mail;
do

        emailid=`echo $mail | awk '{print $1}'`
        password=`echo $mail | awk '{print $2}'`
        echo "Read EMAIL for mailbox $emailid $password"
        php7.4 $MAIL_PHP $emailid $password &>> $LOGFILE
done<$EMAILFILE

SCRIPT_NAME=`basename $0`
if [[ $? -eq 0 ]]
then
   STATUS="Successful"
else
   STATUS="Failed"
fi

#Call monitor_bash_scripts
sh $SCRIPTS_PATH/monitor_bash_scripts.sh ${SCRIPT_NAME} ${STATUS} $LOGFILE

