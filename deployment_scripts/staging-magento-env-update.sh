#!/bin/bash

function HELP {
    echo "-w|--website: Website"
    echo "-p|--path: Path variable in env.php"
    echo "-v|--value: Value to set"
    echo "-n|--namespace: Kubernetes namespace"
    echo "-d|--deployment: Kubernetes deployment name"
    echo "-c|--container: Container name in the pod"
    echo "-h|--help: Display help"
}

args=("$@")
idx=0
while [[ $idx -lt $# ]]
do
    case ${args[$idx]} in
        -w|--website)
        website="${args[$((idx+1))]}"
        idx=$((idx+2))
        ;;
        -p|--path)
        path="${args[$((idx+1))]}"
        idx=$((idx+2))
        ;;
        -v|--value)
        value="${args[$((idx+1))]}"
        idx=$((idx+2))
        ;;
        -n|--namespace)
        namespace="${args[$((idx+1))]}"
        idx=$((idx+2))
        ;;
        -d|--deployment)
        deployment="${args[$((idx+1))]}"
        idx=$((idx+2))
        ;;
        -c|--container)
        container="${args[$((idx+1))]}"
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

# Get the pod name from the deployment
pod=$(kubectl get pods -n $namespace -l app=$deployment -o jsonpath='{.items[0].metadata.name}')

# Copy env.php from the pod to local machine
kubectl cp $namespace/$pod:/var/www/html/$website/app/etc/env.php ./env.php -c $container

if [ $? -eq 1 ]; then
    echo "Unable to copy env.php from the pod"
    exit 1
fi

# Run the PHP script to update the env.php file
php /var/www/erp.theluxuryunlimited.com/deployment_scripts/magento-env-update.php env.php $path $value

if [ $? -eq 0 ]; then
    echo "{\"status\":\"true\",\"message\":\"ENV file updated\"}"
    # Copy the updated env.php back to the pod
    kubectl cp ./env.php $namespace/$pod:/var/www/html/$website/app/etc/env.php -c $container
else
    echo "{\"status\":\"FAILED\",\"message\":\"Failed to update ENV file\"}"
    exit 1
fi
