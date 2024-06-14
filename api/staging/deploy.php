<?php

namespace Deployer;

require_once __DIR__.'/../api.php';

/*
 * Hosts
 */
host('staging')
    ->setLabels([
        'stage' => 'staging',
        'type' => 'api',
    ])
    ->set('branch', 'staging')
    ->set('git_ssh_command', 'ssh -o StrictHostKeyChecking=no')
    ->setHostname('')
    ->setRemoteUser('username')
    ->set('identity_file', $_ENV['API_STAGING_PEM'])
    ->setPort(0)
    ->setDeployPath('/var/www/html/');
