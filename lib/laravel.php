<?php

namespace Deployer;

use RuntimeException;

require 'recipe/laravel.php';

/*
 * Hooks
 */
after('deploy:vendors', 'env:upload');
after('deploy:vendors', 'cloudfront_key:upload');
after('deploy:success', 'service:reload');
after('deploy:success', 'worker:restart');
after('deploy:failed', 'deploy:unlock');

/*
 * Tasks
 */
task('env:upload', function () {
    $localEnvFile = get('local_env_file') ?: runLocally('pwd').'/.env';

    // Check if .env file exists and is not empty
    if (! file_exists($localEnvFile) || filesize($localEnvFile) === 0) {
        throw new RuntimeException("Error: .env file not found or is empty at $localEnvFile");
    }

    $releasePath = get('release_path');
    $remoteEnvFile = "$releasePath/.env";

    upload($localEnvFile, $remoteEnvFile);
})->desc('Upload .env file based on stage');

task('cloudfront_key:upload', function () {
    $defaultCloudFrontKeyFileName = 'cloudfront-private-key.pem';

    $localKeyFile = get('local_cloudfront_key_file') ?: runLocally('pwd').'/'.$defaultCloudFrontKeyFileName;

    if (! file_exists($localKeyFile) || filesize($localKeyFile) === 0) {
        throw new RuntimeException("Error: CloudFront key file not found or is empty at $localKeyFile");
    }

    $releasePath = get('release_path');
    $remoteKeyFile = get('remote_cloudfront_key_file') ?: $defaultCloudFrontKeyFileName;
    $remoteKeyFile = "$releasePath/$remoteKeyFile";

    upload($localKeyFile, $remoteKeyFile);
})->desc('Upload CloudFront private key file based on stage');

task('service:reload', function () {
    run('sudo systemctl reload php-fpm');
    run('sudo systemctl reload nginx');
})->desc('Reload PHP-FPM and Nginx');

task('worker:restart', function () {
    artisan('optimize')();
    artisan('queue:restart', ['showOutput'])();
    run('sudo systemctl restart laravel-scheduler.timer');
})->once()->desc('Restart queue and schedule workers');
