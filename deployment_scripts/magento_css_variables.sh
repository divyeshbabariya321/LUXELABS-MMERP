#!/bin/bash

function HELP {
        echo "-p|--project: Project Name"
        echo "-f|--filepath: path of the environment file"
        echo "-k|--key: variable Key"
        echo "-v|--value value"
}

args=("$@")
idx=0
while [[ $idx -lt $# ]]
do
        case ${args[$idx]} in
                -p|--project)
                project="${args[$((idx+1))]}"
                idx=$((idx+2))
                ;;
                -f|--filepath)
                filepath="${args[$((idx+1))]}"
                idx=$((idx+2))
                ;;
                -k|--key)
                key="${args[$((idx+1))]}"
                idx=$((idx+2))
                ;;
                -v|--value)
                value="${args[$((idx+1))]}"
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

CHECKS=`grep -R "$key\:" $filepath | wc -l`
if [ "$CHECKS" -eq "0" ]
then
        MESSAGE="Variable $key Not Found"
	ERROR="true"
else
        MESSAGE=`sed -i "s/^.*$key\:.*$/$key: $value/" $filepath 2>&1`
	if [ $? -eq "1" ]
	then
	        MESSAGE="Variable update failed : $MESSAGE"
		ERROR="true"
	else
	        MESSAGE="Variable updated"
	        ERROR="false"

	fi

fi

echo "{\"status\":\"$ERROR\",\"message\":\"$MESSAGE\"}"
