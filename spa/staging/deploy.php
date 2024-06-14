<?php

namespace Deployer;

require_once __DIR__.'/../spa.php';

/*
 * Hosts
 */
localhost()->setLabels([
    'stage' => 'staging',
    'type' => 'spa',
]);

set('branch', 'staging');

set('aws_access_key_id', $_ENV['SPA_STAGING_ACCESS_KEY']);
set('aws_secret_access_key', $_ENV['SPA_STAGING_SECRET_KEY']);

set('bucket', '');
set('cloudfront_distribution_id', '');
