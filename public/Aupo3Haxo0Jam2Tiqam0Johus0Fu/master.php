<?php

$cmd = '/bin/sh ' . __DIR__ . '/../../deployment_scripts/';
echo exec($cmd . 'erp/deploy_branch.sh master');
