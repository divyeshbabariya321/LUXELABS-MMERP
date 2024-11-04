#!/bin/bash

. /opt/etc/mysql-creds.conf 

function HELP {
        echo "-w|--website: website"
        echo "-s|--server: Server ip"
        echo "-d|--rootdir: Username"
        echo "-p|--path path"
        echo "-v|--value value"

}

args=("$@")
idx=0
while [[ $idx -lt $# ]]
do
        case ${args[$idx]} in
                -a|--website)
                action="${args[$((idx+1))]}"
                idx=$((idx+2))
                ;;
                -u|--server)
                path="${args[$((idx+1))]}"
                idx=$((idx+2))
                ;;
                -d|--dirtype)
                isdir="${args[$((idx+1))]}"
                idx=$((idx+2))
                ;;
                -t|--path)
                root="${args[$((idx+1))]}"
                idx=$((idx+2))
                ;;
                -p|--alue)
                project="${args[$((idx+1))]}"
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
echo "=============$action $root"

if [ "$action" == "add" ]
then

	if [ -d /opt/rawapps/brandsrepo ]
	then
	        cd /opt/rawapps/brandsrepo
		echo "Existing"
	        GIT_SSH_COMMAND="ssh -o UserKnownHostsFile=/dev/null -o StrictHostKeyChecking=no" git reset --hard  &> /dev/null
	        GIT_SSH_COMMAND="ssh -o UserKnownHostsFile=/dev/null -o StrictHostKeyChecking=no" git checkout THEME_STRUCTURE_UPDATE  &> /dev/null
	else
	        mkdir -p /opt/rawapps/
	        cd /opt/rawapps/
	        GIT_SSH_COMMAND="ssh -o UserKnownHostsFile=/dev/null -o StrictHostKeyChecking=no" git clone git@github.com:ludxb/brands-labels.git brandsrepo  &> /dev/null
	        cd /opt/rawapps/brandsrepo
	        GIT_SSH_COMMAND="ssh -o UserKnownHostsFile=/dev/null -o StrictHostKeyChecking=no" git checkout -b THEME_STRUCTURE_UPDATE  &> /dev/null
		git push --set-upstram origin THEME_STRUCTURE_UPDATE
	fi
	if [ "$isdir" == "1" ]
	then
		mkdir app/design/frontend/LuxuryUnlimited/$path
	else
		echo "Existing file"
		touch app/design/frontend/LuxuryUnlimited/$path
	fi
	git add app/design/frontend/LuxuryUnlimited/$path
	git commit -m "added $path from ERP"
	git push --set-upstream origin THEME_STRUCTURE_UPDATE
	pull_number=`curl -s -L   -H "Accept: application/vnd.github+json"   -H "Authorization: Bearer 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"  -H "X-GitHub-Api-Version: 2022-11-28"  https://api.github.com/repos/ludxb/brands-labels/pulls  -d '{"head":"THEME_STRUCTURE_UPDATE","base":"stage","title":"Structure Update from erp"}' |grep '"number"'|awk '{print $2}'|cut -d',' -f1`

		
fi

echo "{\"status\":\"1\",\"message\":\"Directory created\"}"





QUERY="SELECT t1.name AS lev1, t2.name as lev2, t3.name as lev3, t4.name as lev4 FROM dir AS t1
LEFT JOIN theme_structure AS t2 ON t2.parent_id = t1.id
LEFT JOIN theme_structure AS t3 ON t3.parent_id = t2.id
LEFT JOIN theme_structure AS t4 ON t4.parent_id = t3.id
WHERE t1.name = 'root'"

#mysql -u $DB_USERNAME -p$DB_PASSWORD -D erp_live -N -se "$QUERY"
