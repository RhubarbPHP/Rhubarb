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

require_once __DIR__ . "/Settings.php";
require_once __DIR__ . "/Request/Request.php";

use Rhubarb\Crown\Request;
use Rhubarb\Crown\UrlHandlers\UrlHandler;

/**
 * A class providing the rest of the platform some contextual information
 *
 * @property bool $UnitTesting
 * @property bool $IsAjaxRequest
 * @property bool $IsCliInvocation
 * @property bool $Live                        True to indicate this is a live production server
 * @property bool $DeveloperMode            True to enable developer only functionality
 * @property bool $SimulateNonCli            True to pretend that the request is not a CLI request (even if it is - used by unit testing)
 * @property mixed $SimulatedRequestBody    For unit testing - simulates the request body instead of using php://input
 * @property UrlHandler $UrlHandler            The URL handler currently generating the response
 *
 * @author acuthbert
 * @copyright GCD Technologies 2012
 */
class Context extends Settings
{
    protected function initialiseDefaultValues()
    {
        global $unitTesting;

        parent::initialiseDefaultValues();

        // $unitTesting is set in execute-test.php
        $this->UnitTesting = (isset($unitTesting) && $unitTesting) ? true : false;
        $this->DeveloperMode = false;
        $this->Live = false;
    }

    public function getIsAjaxRequest()
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
    public function getIsCliInvocation()
    {
        if ($this->SimulateNonCli) {
            return false;
        }

        return 'cli' === php_sapi_name();
    }

    /**
     * A static accessor for the Request property
     *
     * @return \Rhubarb\Crown\Request\Request The current Request
     */
    public static function currentRequest()
    {
        $contextInstance = new static();

        return $contextInstance->Request;
    }

    /**
     * Lazily initialise and then return the current Request.
     *
     * @return \Rhubarb\Crown\Request\Request
     */
    public function getRequest()
    {
        if (!isset($this->modelData['Request'])) {
            if ($this->IsCliInvocation) {
                $request = new Request\CliRequest();
            } else {
                $contentType = (isset($_SERVER["CONTENT_TYPE"])) ? strtolower($_SERVER["CONTENT_TYPE"]) : "";

                switch ($contentType) {
                    case "application/json":
                        $request = new Request\JsonRequest();
                        break;
                    default:
                        $request = new Request\WebRequest();
                        break;
                }
            }

            $this->modelData['Request'] = $request;
        }

        return $this->modelData['Request'];
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
        if ($this->UnitTesting) {
            return $this->SimulatedRequestBody;
        }

        $requestBody = file_get_contents("php://input");

        return $requestBody;
    }
}
