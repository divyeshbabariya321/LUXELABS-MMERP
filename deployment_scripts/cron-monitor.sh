#!/bin/bash
LOGDIR="/var/www/erp.theluxuryunlimited.com/storage/logs"
WORK_DIR=/opt/BKPSCRIPTS/
FDATE=`date +%Y-%m-%d`
EXPRESSION=$1
command=$2
REPLACEDCMD=`echo $command | tr ':' '-'`
TMPLOG="$WORK_DIR/logs/$command-$FDATE.tmp"
if [ ! -d $WORK_DIR/logs ]
then
	mkdir $WORK_DIR/logs
fi
#echo "$LOGDIR/$REPLACEDCMD-$FDATE.log"

if [ -f $LOGDIR/$REPLACEDCMD-$FDATE.log ]
then
	FFLAG=0
	/usr/bin/retail $LOGDIR/$REPLACEDCMD-$FDATE.log > $TMPLOG
	RCOUNT=`cat $TMPLOG | grep -i "Cron was started to run" | wc -l`
	ECOUNT=`cat $TMPLOG | grep -i "exception"`

	if [ -z $ECOUNT ]
	then
		EFLAG=0
	else
		EFLAG=1
	fi
else
	RCOUNT=0
	EFLAG=0
	FFLAG=1
fi

#echo "$RCOUNT $EFLAG $FFLAG"
case $EXPRESSION in

  execnt)
	  echo $RCOUNT
    ;;

  iferror)
	  echo $EFLAG
    ;;

  ifexists)
	  echo $FFLAG
    ;;

  *)
	  echo "Failed"
    ;;
esac
