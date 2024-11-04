#!/bin/bash

LOG_DIR="/var/log/scriptlog/"
SCRIPTFILE=$1


if [ -f $LOG_DIR/$SCRIPTFILE.log ]
then
	retail $LOG_DIR/$SCRIPTFILE.log
else
	echo "File Not Found"
fi

