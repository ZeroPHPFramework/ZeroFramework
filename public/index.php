<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

$_ENV['BASE_PATH'] = dirname(__DIR__);

require_once('../core/libraries/Config/Helper.php');
require_once('../core/libraries/Storage/Helper.php');

$helpers = require_once('../core/helpers.php');

// Only load helpers that are relevant to the current environment [http]
foreach ($helpers as $helper) {
    if (php_sapi_name() === 'cli') {
        if ($helper['cli'] === false) {
            continue;
        }
    }
    require_once($helper['path']);
}

require_once('../core/bootstrap.php');

