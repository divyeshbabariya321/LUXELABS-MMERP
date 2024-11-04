#!/bin/bash
SCRIPT_NAME=`basename $0`

date=`date +%d-%m-%y` 
bkproot=/mnt/backup/erp_backup
bkpdir=$bkproot/$date

mkdir -p $bkpdir
mysqldump erp_live |gzip -v > $bkpdir/erp_live.sql.gz | tee -a ${SCRIPT_NAME}.log

find $bkproot -mtime +8 -exec rm -rf {} \; | tee -a ${SCRIPT_NAME}.log


############### Magento Servers Database Backup #############
envfile='/var/www/erp.theluxuryunlimited.com/.env'
magento_servers='BRANDS AVOIRCHIC SOLOLUXURY SUVANDNAT VERALUSSO'
mageroot=/mnt/backup/magento_backup
magebkp=$bkproot/$date

user=`grep MAGENTO_DB_USER $envfile|cut -d'=' -f2`  | tee -a ${SCRIPT_NAME}.log
pass=`grep MAGENTO_DB_PASSWORD $envfile|cut -d"'" -f2`  | tee -a ${SCRIPT_NAME}.log

for server in $magento_servers
do
	mkdir -p $magebkp/$server
	db=`grep "$server"_DB $envfile|cut -d'=' -f2`
	host=`grep "$server"_HOST $envfile|cut -d'=' -f2`
	mysqldump -h $host -u $user -p"$pass" $db |gzip -v > $magebkp/$server/$db.sql.gz  | tee -a ${SCRIPT_NAME}.log
done
find $mageroot -mtime +8 -exec rm -rf {} \;  | tee -a ${SCRIPT_NAME}.log

if [[ $? -eq 0 ]]
then
   STATUS="Successful"
else
   STATUS="Failed"
fi

#Call monitor_bash_scripts

sh $SCRIPTS_PATH/monitor_bash_scripts.sh ${SCRIPT_NAME} ${STATUS} ${SCRIPT_NAME}.log
