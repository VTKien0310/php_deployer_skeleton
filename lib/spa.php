<?php

namespace Deployer;

use RuntimeException;

const DEFAULT_BUILD_DIR = 'build';
const DEFAULT_BUILD_ENV_FILE = '.env';
const DEFAULT_BUILD_COMMAND = 'build';
const DEFAULT_DEPLOY_DIR = 'dist';
const DEFAULT_PACKAGE_MANAGER = 'yarn';

/*
 * Tasks
 */
task('deploy', [
    'deploy:prepare',
    'deploy:build_spa_app',
    'deploy:upload_to_s3',
    'deploy:cloudfront_invalidate',
    'deploy:cleanup',
]);

task('deploy:prepare', function () {
    // remove old build if exists
    $buildDir = get('build_dir') ?: DEFAULT_BUILD_DIR;
    runLocally("rm -rf $buildDir");
})->desc('Prepare to deploy SPA app to S3');

task('deploy:build_spa_app', function () {
    // clone SPA source code to local
    $buildDir = get('build_dir') ?: DEFAULT_BUILD_DIR;
    $repository = get('repository');
    $branch = get('branch');
    runLocally("git clone -b $branch $repository $buildDir");

    // prepare .env
    $localEnvFile = get('local_env_file') ?: runLocally('pwd').'/.env';
    if (! file_exists($localEnvFile) || filesize($localEnvFile) === 0) {
        throw new RuntimeException("Error: .env file not found or is empty at $localEnvFile");
    }
    $buildEnvFile = get('build_env_file') ?: DEFAULT_BUILD_ENV_FILE;
    runLocally("cp $localEnvFile $buildDir/$buildEnvFile");

    // build SPA app
    $buildCommand = get('build_command') ?: DEFAULT_BUILD_COMMAND;
    $packageManager = get('package_manager') ?: DEFAULT_PACKAGE_MANAGER;
    runLocally("cd $buildDir && $packageManager install");
    runLocally("cd $buildDir && $packageManager run $buildCommand");
})->desc('Clone SPA app repository and build the application');

task('deploy:upload_to_s3', function () {
    $buildDir = get('build_dir') ?: DEFAULT_BUILD_DIR;
    $deployDir = get('deploy_dir') ?: DEFAULT_DEPLOY_DIR;

    $deployDir = $buildDir.'/'.$deployDir;

    $bucket = get('bucket');
    $awsAccessKeyId = get('aws_access_key_id');
    $awsSecretAccessKey = get('aws_secret_access_key');

    // upload to S3 using AWS CLI
    runLocally("AWS_ACCESS_KEY_ID=$awsAccessKeyId AWS_SECRET_ACCESS_KEY=$awsSecretAccessKey aws s3 sync $deployDir s3://$bucket --cache-control 'no-cache' --delete");
})->desc('Upload built SPA app to S3 using AWS CLI');

task('deploy:cloudfront_invalidate', function () {
    $distributionId = get('cloudfront_distribution_id');
    $awsAccessKeyId = get('aws_access_key_id');
    $awsSecretAccessKey = get('aws_secret_access_key');

    runLocally("AWS_ACCESS_KEY_ID=$awsAccessKeyId AWS_SECRET_ACCESS_KEY=$awsSecretAccessKey aws cloudfront create-invalidation --distribution-id $distributionId --paths '/*'");
})->desc('Create CloudFront invalidation');

task('deploy:cleanup', function () {
    // remove build directory
    $buildDir = get('build_dir') ?: DEFAULT_BUILD_DIR;
    runLocally("rm -rf $buildDir");
})->desc('Cleanup after deployment');
