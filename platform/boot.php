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

error_reporting(E_ALL | E_STRICT);

// As we preform our own exception handling we need to stop fatal errors from showing stack traces.
ini_set("display_errors", "on");

if(file_exists(__DIR__."/../vendor/autoload.php")){
    define("VENDOR_DIR", realpath(__DIR__."/../vendor"));
} else {
    define("VENDOR_DIR", realpath(__DIR__."/../../../"));
}

// Include the composer autoloader
/** @noinspection PhpIncludeInspection */
include_once(VENDOR_DIR . "/autoload.php");

// Move the working directory up one from the application root. This is primarily a security feature
// to ensure any files pushed onto the filesystem through a vulnerability can't be as easily
// referenced in a follow up attack. You should not rely on the current working director when
// performing file IO, instead use file paths relative to the current code file using __DIR__
chdir(VENDOR_DIR."/../");