BRANCH_NAME=$1
scriptPath="$(cd "$(dirname "$0")"; pwd)"
cd $scriptPath;
cd ../../vendor/seo2websites/magento-helper/
git checkout $BRANCH_NAME;
git pull;
echo $BRANCH_NAME;