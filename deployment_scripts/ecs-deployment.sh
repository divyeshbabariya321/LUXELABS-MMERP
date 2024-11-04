#!/bin/bash

#assumeJson=$(aws sts assume-role --role-arn arn:aws:iam::211125407730:role/CrossAccountAdminRole --role-session-name ecs-access)


AccessKeyId=$(echo $assumeJson|jq -r '.Credentials.AccessKeyId')
SecretAccessKey=$(echo $assumeJson|jq -r '.Credentials.SecretAccessKey')
SessionToken=$(echo $assumeJson|jq -r '.Credentials.SessionToken')

export AWS_ACCESS_KEY_ID="$AccessKeyId"
export AWS_SECRET_ACCESS_KEY="$SecretAccessKey"
export AWS_SESSION_TOKEN="$SessionToken"

CODEBUILD_SOURCE_VERSION="prod"

ARTISAN_DEFINITION=$(aws ecs describe-task-definition --task-definition erp-artisan )
aws ecs run-task --cluster brands-labels-prod --task-definition erp-artisan --overrides "{\"containerOverrides\": [{\"name\" : \"erp-artisan\", \"command\": [\"artisan clear:all\"]}]}"

declare -A array
array[erp-app]="erp-prod"
array[erp-totem]="erp-prod"
array[erp-horizon]="erp-prod-horizon"

for i in "${!array[@]}"
do
    echo "key  : $i"
    echo "value: ${array[$i]}"

    TASK_DEFINITION=$(aws ecs describe-task-definition --task-definition  $i )
    fullimage=$(echo $TASK_DEFINITION | jq -r '.taskDefinition.containerDefinitions[0].image')

    #NEW_TASK_INFO=$(aws ecs register-task-definition --region "$AWS_DEFAULT_REGION" --cli-input-json "$NEW_TASK_DEFINITION")
    #NEW_REVISION=$(echo $NEW_TASK_INFO | jq '.taskDefinition.revision')

    #IMAGE_META="$( aws ecr describe-images --repository-name="mmerp/erp-app" --image-ids=imageTag=$CODEBUILD_RESOLVED_SOURCE_VERSION 2> /dev/null )"
    echo $fullimage

    image=$(echo ${fullimage%%:*})
    version=${fullimage//*:}

    version=$CODEBUILD_SOURCE_VERSION

    echo "$image:$version"

    NEW_TASK_DEFINITION=$(echo $TASK_DEFINITION | jq --arg IMAGE "$image:$version" '.taskDefinition | .containerDefinitions[0].image = $IMAGE | del(.taskDefinitionArn) | del(.revision) | del(.status) | del(.requiresAttributes) | del(.compatibilities) |  del(.registeredAt)  | del(.registeredBy)')
    echo $NEW_TASK_DEFINITION | jq .

    NEW_TASK_INFO=$(aws ecs register-task-definition --cli-input-json "$NEW_TASK_DEFINITION")
    NEW_REVISION=$(echo $NEW_TASK_INFO | jq '.taskDefinition.revision')
    echo $NEW_REVISION
    aws ecs update-service --cluster ${array[$i]} --service $i --task-definition $i:${NEW_REVISION}


    #aws ecs update-service --force-new-deployment --service my-service --cluster cluster-name
done

