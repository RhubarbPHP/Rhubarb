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

namespace Rhubarb\Crown;

require_once __DIR__ . "/Request/Request.php";

use Rhubarb\Crown\Request\Request;
use Rhubarb\Crown\UrlHandlers\UrlHandler;

/**
 * A class providing the rest of the platform some contextual information
 *
 * @property string $ApplicationModuleFile      The path to the file containing the application module
 * @property UrlHandler $UrlHandler             The URL handler currently generating the response
 * @property Request
 */
class PhpContext
{
    /**
     * True to pretend that the request is not a CLI request (even if it is - used by unit testing)
     *
     * @var bool
     */
    public $simulateNonCli = false;

    /**
     * For unit testing - simulates the request body instead of using php://input
     *
     * @var mixed
     */
    public $simulatedRequestBody = null;

    /**
     * A cached instance of the request derived from this context.
     *
     * @see currentRequest()
     * @var Request
     */
    private $request = null;

    public function isXhrRequest()
    {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            return true;
        }

        return false;
    }

    /**
     * Check if the script was invoked via PHP's CLI
     *
     * @return bool
     */
    public function isCliInvocation()
    {
        if ($this->simulateNonCli) {
            return false;
        }

        return 'cli' === php_sapi_name();
    }

    /**
     * Gets the current request derived from the PHP context.
     */
    public function currentRequest()
    {
        if ($this->request == null){
            $this->request = Request::fromPhpContext($this);
        }

        return $this->request;
    }

    /**
     * Returns the body of the request.
     *
     * This is not automatically passed to the request as this might be an expensive operation
     * that may never actually get used (e.g. a File upload)
     *
     * @return bool|mixed|string
     */
    public function getRequestBody()
    {
        if (Application::runningApplication()->isUnitTesting()){
            return $this->simulatedRequestBody;
        }

        $requestBody = file_get_contents("php://input");

        return $requestBody;
    }
}
