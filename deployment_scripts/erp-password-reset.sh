#!/bin/bash
. /opt/etc/mysql-creds.conf
if [ $# -eq 0 ]; then
  echo "Usage: $0 [username] [password]"
  exit 1
fi

USERNAME=$1
PASSWORD=$2

if [ -z $USERNAME ]
then
        read -p "Please enter email : " USERNAME
fi

if [ -z $PASSWORD ]
then
	read -p "Please enter password : " PASSWORD
fi


USERSTATUS=`mysql -h $DB_HOST -u $DB_USERNAME -p$DB_PASSWORD -D $DB_NAME -N -se "select id from users u2 where email = '$USERNAME'"`
if [ ! -z $USERSTATUS ]
then
	echo "User available in ERP. resetting password now"
	#RPASSWORD=`htpasswd -bnBC 8 "" $PASSWORD | grep -oP '\$2[ayb]\$.{56}' | sed -e "s/'/'\\\\''/g; 1s/^/'/; \$s/\$/'/"`
#	RPASSWORD=`htpasswd -bnBC 8 "" $PASSWORD | grep -oP '\$2[ayb]\$.{56}' | sed 's/\$/\\$/g'`
#	htpasswd -bnBC 8 "" $PASSWORD | grep -oP '\$2[ayb]\$.{56}'
#	print "$RPASSWORD"
#	mysql -h $DB_HOST_READ -u $DB_USERNAME -p$DB_PASSWORD -D $DB_NAME -N -se "update users set password = '$RPASSWORD' where email = '$USERNAME'"
#	echo "update users set password = '$RPASSWORD' where email = '$USERNAME'"
#	echo $RPASSWORD
	php pass.php $USERNAME $PASSWORD
else
	echo "User not found"
fi

