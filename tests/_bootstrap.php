<?php
// This is global bootstrap for autoloading
require_once __DIR__ . "/../vendor/autoload.php";

define('APPLICATION_ROOT_DIR', realpath(__DIR__ . "/../"));

include_once __DIR__ . "/../platform/execute-test.php";
