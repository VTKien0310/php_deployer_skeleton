<?php

namespace Deployer;

const BASE_PATH = __DIR__;

require BASE_PATH.'/vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->load(BASE_PATH.'/.env');

// Put other common code here...
