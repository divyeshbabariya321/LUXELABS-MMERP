#!/bin/bash

function HELP {
  echo "-r|--repo: Repo Name"
  echo "-s|--scope: Scope"
  echo "-c|--code: Scope Code"
  echo "-p|--path: Path variable"
  echo "-v|--value: Value"
  echo "-f|--file: Sync file path"
  echo "-t|--type: sensitive / shared"
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
        -r|--repo)
        repo="${args[$((idx+1))]}"
        idx=$((idx+2))
        ;;
        -s|--scope)
        scope="${args[$((idx+1))]}"
        idx=$((idx+2))
        ;;
        -c|--code)
        code="${args[$((idx+1))]}"
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
        -f|--file)
        file="${args[$((idx+1))]}"
        idx=$((idx+2))
        ;;
        -t|--type)
        type="${args[$((idx+1))]}"
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

function set_variable {
    if [ $type != "sensitive" ]; then
        echo "Shared = php bin/magento --lock-env config:set --scope=$scope --scope-code=$code $path $value"
        kubectl exec -n $namespace -c $container $pod -- php bin/magento --lock-env config:set --scope=$scope --scope-code=$code $path "$value"
    else
        echo "Sensitive configuration not supported directly in Kubernetes setup"
        exit 1
    fi
}

if [ $type != "sensitive" ]; then
    # Perform Docker build and Kubernetes deployment
    docker build -t $repo:latest .
    kubectl set image deployment/$deployment $container=$repo:latest -n $namespace

    # Apply configuration
    if [ -z $file ]; then
        set_variable
    else
        while read line; do
            scope=$(echo $line | cut -d',' -f1)
            code=$(echo $line | cut -d',' -f2)
            path=$(echo $line | cut -d',' -f3)
            value=$(echo $line | cut -d',' -f4)
            set_variable
        done < $file
    fi
fi

# Check status
if [ $? -eq 0 ]; then
    echo "{\"status\":\"true\",\"message\":\"Deployment Successful\"}"
else
    echo "{\"status\":\"FAILED\",\"message\":\"Deployment Failed\"}"
fi
