<?php

namespace Deployer;

require_once __DIR__.'/../main.php';

require_once BASE_PATH.'/lib/laravel.php';

/*
 * Configurations
 */
set('repository', '');

add('shared_files', []);
add('shared_dirs', []);
add('writable_dirs', []);
