<?php

namespace Deployer;

require_once __DIR__.'/../api.php';

/*
 * Hosts
 */
host('production')
    ->setLabels([
        'stage' => 'production',
        'type' => 'api',
    ])
    ->set('branch', 'production')
    ->set('git_ssh_command', 'ssh -o StrictHostKeyChecking=no')
    ->setHostname('')
    ->setRemoteUser('username')
    ->set('identity_file', $_ENV['API_PRODUCTION_PEM'])
    ->setPort(0)
    ->setDeployPath('/var/www/html/');
