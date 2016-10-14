<?php

/**
 * Copyright (c) 2016 RhubarbPHP.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Rhubarb\Crown\Request;

require_once __DIR__ . "/Request.php";

use Rhubarb\Crown;

/**
 * Encapsulates the current web request.
 *
 * @property string $URI
 * @property string $Host
 * @property string $urlPath
 * @property string $UrlBase Base url for the current request (e.g. http://localhost) without trailing slash
 *
 * @method mixed get(string $property, string $defaultValue = null) Return a value from the query string optionally using a default value.
 * @method mixed post(string $property, string $defaultValue = null) Return a value from the post data optionally using a default value.
 * @method mixed request(string $property, string $defaultValue = null) Return a value from the post data optionally using a default value.
 * @method mixed files(string $property, string $defaultValue = null) Return a value from the files collection optionally using a default value.
 * @method mixed cookie(string $property, string $defaultValue = null) Return a value from the cookies optionally using a default value.
 * @method mixed session(string $property, string $defaultValue = null) Return a value from the session optionally using a default value.
 */
class WebRequest extends Request
{
    public $serverData;
    public $getData;
    public $postData;
    public $requestData;
    public $filesData;
    public $cookieData;
    public $sessionData;
    public $headerData;

    public $host;
    public $uri;
    public $urlPath;

    private $urlBase;

    /** @var bool Indicates that header keys have been set to lower case */
    private $headerCaseSet = false;

    public function initialise()
    {
        $this->superGlobalMethodNames = [
            'env',
            'server',
            'get',
            'post',
            'request',
            'files',
            'cookie',
            'session',
            'header'
        ];

        $this->serverData = isset($_SERVER) ? $_SERVER : [];
        $this->getData = isset($_GET) ? $_GET : [];
        $this->postData = isset($_POST) ? $_POST : [];
        $this->postData = isset($_REQUEST) ? $_REQUEST : [];
        $this->filesData = isset($_FILES) ? $_FILES : [];
        $this->cookieData = isset($_COOKIE) ? $_COOKIE : [];
        $this->sessionData = isset($_SESSION) ? $_SESSION : [];
        $this->headerData = [];

        if (function_exists("getallheaders")) {
            $this->headerData = \getallheaders();
        } else {
            $this->headerCaseSet = true;
            foreach ($_SERVER as $key => $value) {
                $key = strtolower($key);
                if (strpos($key, 'http_') === 0) {
                    $key = str_replace('_', '-', substr($key, 5));
                    $this->headerData[$key] = $value;
                }
            }
        }

        $this->host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : (isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : '');
        $this->uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
        $this->urlPath = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '';
    }

    /**
     * Creates a URL for a URI using the existing http scheme, host and port of this request (e.g. http://localhost/)
     *
     * @param string $uri The URI to compose a URL for.
     * @return string
     */
    public function createUrl($uri = '/')
    {
        if (!isset($this->urlBase)) {
            $ssl = $this->isSSL();
            $protocol = 'http' . (($ssl) ? 's' : '');

            $host = $this->host;
            if (strpos($host, ':') === false) {
                $port = $this->serverData['SERVER_PORT'];
                $port = ((!$ssl && $port == '80') || ($ssl && $port == '443')) ? '' : ':' . $port;
                $host = $this->host . $port;
            }

            $this->urlBase = $protocol . '://' . $host;
        }

        if ($uri !== '' && strpos($uri, '/') === false) {
            $uri = '/' . $uri;
        }

        return $this->urlBase . $uri;
    }

    public function isSSL()
    {
        return !(empty($this->serverData['HTTPS']) || $this->serverData['HTTPS'] === 'off');
    }

    /**
     * @return string
     */
    public function getAcceptsRequestMimeType()
    {
        $typeString = strtolower($this->header("Accept"));

        if (strpos($typeString, '*/*') !== false || $typeString == "") {
            return "text/html";
        }

        return $typeString;
    }

    /**
     * @param $name
     * @param null|mixed $defaultValue
     * @return mixed|null
     */
    public function header($name, $defaultValue = null)
    {
        // RFC2616 (HTTP 1.1) requires headers to be case insensitive, so convert all headers to lower case
        if (!$this->headerCaseSet) {
            $this->headerCaseSet = true;
            $lowerCaseHeaders = [];
            foreach ($this->headerData as $key => $value) {
                $lowerCaseHeaders[strtolower($key)] = $value;
            }
            $this->headerData = $lowerCaseHeaders;
        }
        return $this->getSuperglobalValue('header', strtolower($name), $defaultValue);
    }
}
