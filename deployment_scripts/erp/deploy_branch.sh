BRANCH_NAME=$1
COMPOSER_UPDATE=$2
scriptPath="$(cd "$(dirname "$0")"; pwd)"

if [ $BRANCH_NAME == "stage" ]
then
	ssh -p2112 root@95.216.202.87 "bash /var/www/erp.theluxuryunlimited.com/deployment_scripts/erp/deploy_branch.sh stage $COMPOSER_UPDATE"
else
	cd $scriptPath;
	cd ../..
	git reset --hard
	git clean -fd
	git pull origin
	git checkout $BRANCH_NAME
	git pull origin $BRANCH_NAME
	git pull --rebase
	./artisan migrate
	echo $BRANCH_NAME;
        if [ ! -z $COMPOSER_UPDATE ] && [ $COMPOSER_UPDATE  == "true" ]
        then
                composer update
                if [ $? -eq 0 ]; then
                        echo "Composer update sucess"
                else
                        echo "Composer update fail"
                fi
        else
                echo "composer update parameter not found" 
        fi
fi
