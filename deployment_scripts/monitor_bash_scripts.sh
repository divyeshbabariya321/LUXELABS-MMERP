#!/bin/bash

SCRIPT_NAME=`basename $0`

. /opt/etc/mysql-creds.conf

# Data to insert
SCRIPT_NAME=$1
SCRIPT_TYPE="cron"
OUTPUTFILE=$3
LAST_EXECUTION_STATUS=$2
OUTPUT=`cat $OUTPUTFILE | base64 -w 0`
# Define the SQL query to insert data
#SQL_QUERY1="INSERT INTO script_documents(file, script_type, comments, status, last_run) VALUES ('$SCRIPT_NAME', '$SCRIPT_TYPE', '$OUTPUT', '$LAST_EXECUTION_STATUS', now());"

SCRIPTID=`mysql -u "$DB_USERNAME" -p"$DB_PASSWORD" -h "$DB_HOST" -D "$DB_NAME" -se "select id from script_documents where file='$SCRIPT_NAME'"` 

SQL_QUERY1="update script_documents set last_output='$OUTPUT', last_run=now(), status='$LAST_EXECUTION_STATUS' where id='$SCRIPTID'" 
#echo "$SQL_QUERY1"
SQL_QUERY2="INSERT INTO scripts_execution_histories(script_document_id,description,run_time,run_output,run_status,created_at,updated_at) VALUES ($SCRIPTID,'$SCRIPT_NAME',now(),'$OUTPUT','$LAST_EXECUTION_STATUS', now(), now())"
#echo "$SQL_QUERY2"

# Execute the SQL query
mysql -u "$DB_USERNAME" -p"$DB_PASSWORD" -h "$DB_HOST" -D "$DB_NAME" -e "$SQL_QUERY1"
if [ $? -eq 0 ]; then
  echo "Data inserted successfully."
else
  echo "Error: Failed to insert data to script_documents table."
  exit 1
fi

mysql -u "$DB_USERNAME" -p"$DB_PASSWORD" -h "$DB_HOST" -D "$DB_NAME" -e "$SQL_QUERY2"
if [ $? -eq 0 ]; then
  echo "Data inserted successfully."
else
  echo "Error: Failed to insert data to script_documents_histories table."
  exit 1
fi


rm -rf $OUTPUTFILE
