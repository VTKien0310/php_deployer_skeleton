<?php

namespace Deployer;

require_once __DIR__.'/../api.php';

/*
 * Hosts
 */
host('development')
    ->setLabels([
        'stage' => 'development',
        'type' => 'api',
    ])
    ->set('branch', 'development')
    ->set('git_ssh_command', 'ssh -o StrictHostKeyChecking=no')
    ->setHostname('')
    ->setRemoteUser('username')
    ->set('identity_file', $_ENV['API_DEVELOPMENT_PEM'])
    ->setPort(0)
    ->setDeployPath('/var/www/html/');
