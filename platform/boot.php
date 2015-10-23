<?php

/*
 *	Copyright 2015 RhubarbPHP
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 */

/**
 * Sets up the working environment to provide a consistant and predictable ecosystem for user code
 */

use Rhubarb\Crown\Exceptions\Handlers\ExceptionHandler;
use Rhubarb\Crown\Module;

error_reporting(E_ALL | E_STRICT);

// As we preform our own exception handling we need to stop fatal errors from showing stack traces.
ini_set("display_errors", "off");

define( "VENDOR_DIR", realpath("vendor") );

// Include the composer autoloader
/** @noinspection PhpIncludeInspection */
include("vendor/autoload.php");

// Initially we don't have an auto loader as this is handled by the modules. We need to load this first
// module 'core' so that we have an auto loader for subsequent modules. There are also some other classes
// that might be needed by this booting script so we load them aswell.

include(__DIR__ . "/../src/Module.php");
include(__DIR__ . "/../src/Exceptions/ImplementationException.php");
include(__DIR__ . "/../src/Exceptions/Handlers/ExceptionHandler.php");

// Register to handle exceptions and PHP errors. However we don't do this if we are unit testing. It's
// best to let the exceptions report unhindered to phpunit.
if (!isset($unitTesting) || !$unitTesting) {
    ExceptionHandler::EnableExceptionTrapping();
}

$appName = false;

// Is there an app environment setting? This allows the same project to serve multiple solutions
// with one code base (e.g. tenant and landlord together). This is very rare in production systems, however
// for the initial project phase this can be very useful.
if ($envAppSetting = getenv("rhubarb_app")) {
    $appName = $envAppSetting;
}

if ($appName && !preg_match("/\./",$appName)){
    chdir($appName."/");
}

if (file_exists("settings/app.config.php")) {
    include("settings/app.config.php");
}

// Now auto loaders are in place we can initialise the modules properly.
Module::InitialiseModules();