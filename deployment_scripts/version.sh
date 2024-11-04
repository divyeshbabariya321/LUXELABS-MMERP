#/bin/bash
. /opt/etc/mysql-creds.conf
DATE=$(date "+%Y-%m-%d %H:%M:%S")
STORE_WEBSITE_ID=$1
curl -m 10 --retry 5 --data-raw "Entering data into store_website_version" https://health.theluxuryunlimited.com/ping/b97e7c33-06af-4f26-bf69-199782203c72/start
mysql -h $DB_HOST -u $DB_USERNAME -p$DB_PASSWORD -D $DB_NAME  -se "INSERT INTO store_website_version (store_website_id,build_id,version,created_at,updated_at) values ('$STORE_WEBSITE_ID','$BUILD_NUMBER','$BUILD_NUMBER','$DATE','$DATE')"

if [ $? -eq 1 ]
then
	echo "Unable to insert data"
	curl -m 10 --retry 5 --data-raw "unable to insert data into store_website_version" https://health.theluxuryunlimited.com/ping/b97e7c33-06af-4f26-bf69-199782203c72/fail
else
	curl -m 10 --retry 5 --data-raw "successfully inserted data into store_website_version" https://health.theluxuryunlimited.com/ping/b97e7c33-06af-4f26-bf69-199782203c72
fi
curl -m 10 --retry 5 --data-raw "Entering data into deployment_versioning" https://health.theluxuryunlimited.com/ping/b97e7c33-06af-4f26-bf69-199782203c72/start
mysql -h $DB_HOST -u $DB_USERNAME -p$DB_PASSWORD -D $DB_NAME -se "INSERT INTO deployment_versioning (version_number,build_number,job_name,revision,branch_name,pull_no,deployment_date,pr_date,created_at,updated_at) values ('$BUILD_NUMBER','$BUILD_NUMBER','$JOB_NAME','$GIT_COMMIT','$GIT_BRANCH','$PULL_NUMBER','$DATE','$DATE','$DATE','$DATE')"

if [ $? -eq 1 ]
then
        echo "Unable to insert data"
	curl -m 10 --retry 5 --data-raw "unable to insert data into deployment_versioning" https://health.theluxuryunlimited.com/ping/b97e7c33-06af-4f26-bf69-199782203c72/fail
else
        curl -m 10 --retry 5 --data-raw "successfully inserted data into deployment_versioning" https://health.theluxuryunlimited.com/ping/b97e7c33-06af-4f26-bf69-199782203c72
fi

