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
 * execute-http.php is the entry point for all HTTP requests for Rhubarb applications.
 * The only exceptions to this are when webserver URL rewriting goes directly to
 * a resource for performance reasons, e.g. accessing static content like images
 * and CSS files.
 */

use Rhubarb\Crown\Application;
use Rhubarb\Crown\Logging\Log;

// Initiate our bootstrap script to boot all libraries required.
require_once __DIR__ . "/boot.php";

require_once __DIR__ . "/../src/Logging/Log.php";
require_once __DIR__ . "/../src/Module.php";
require_once __DIR__ . "/../src/PhpContext.php";

Log::performance("Rhubarb booted", "ROUTER");

/**
 * @var Application $application
 */

if (isset($_ENV["rhubarb_app"])) {
    $appClass = $_ENV["rhubarb_app"];
    $application = new $appClass();
} elseif (file_exists("settings/app.config.php")) {
    include_once "settings/app.config.php";
}

if (!isset($application)) {
    Log::warning("HTTP request made with no application loaded.", "ROUTER");
} else {
    // Pass control to the Module class and ask it to generate a response for the
    // incoming request.
    try {
        // Pass control to the application and ask it to generate a response for the
        // incoming request.
        $response = $application->generateResponseForRequest($application->currentRequest());

        Log::performance("Response generated", "ROUTER");
        $response->send();
        Log::performance("Response sent", "ROUTER");

    } catch (\Exception $er) {

        if ($application->developerMode) {
            Log::error($er->getMessage(), "ERROR");

            print "<pre>Exception: " . get_class($er) . "
Message: " . $er->getMessage() . "
Stack Trace:
" . $er->getTraceAsString();

        }
    }
}


Log::debug("Request Complete", "ROUTER");