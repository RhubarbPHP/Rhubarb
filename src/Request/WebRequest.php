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

namespace Rhubarb\Crown\Request;

require_once __DIR__ . "/Request.php";

use Rhubarb\Crown;

/**
 * Encapsulates the current web request.
 *
 * @property-read array $serverData
 * @property-read array $getData
 * @property-read array $postData
 * @property-read array $filesData
 * @property-read array $cookieData
 * @property-read array $sessionData
 * @property-read array $requestData
 * @property-read array $headerData
 *
 * @property string $URI
 * @property string $Host
 * @property string $UrlPath
 * @property string $UrlBase Base url for the current request (e.g. http://localhost) without trailing slash
 *
 * @method mixed get(string $property, string $defaultValue=null) Return a value from the query string optionally using a default value.
 * @method mixed post(string $property, string $defaultValue=null) Return a value from the post data optionally using a default value.
 * @method mixed files(string $property, string $defaultValue=null) Return a value from the files collection optionally using a default value.
 * @method mixed cookie(string $property, string $defaultValue=null) Return a value from the cookies optionally using a default value.
 * @method mixed session(string $property, string $defaultValue=null) Return a value from the session optionally using a default value.
 * @method mixed header(string $property, string $defaultValue=null) Return a value from the headers optionally using a default value.
 */
class WebRequest extends Request
{
    private $serverData;
    private $getData;
    private $postData;
    private $filesData;
    private $cookieData;
    private $sessionData;
    private $headerData;

    public $host;
    public $uri;
    public $urlPath;

    private $urlBase;

    public function initialise()
    {
        $this->superglobalMethodNames = [
            'env',
            'server',
            'get',
            'post',
            'files',
            'cookie',
            'session',
            'header'
        ];

        $this->serverData = isset($_SERVER) ? $_SERVER : [];
        $this->getData = isset($_GET) ? $_GET : [];
        $this->postData = isset($_POST) ? $_POST : [];
        $this->filesData = isset($_FILES) ? $_FILES : [];
        $this->cookieData = isset($_COOKIE) ? $_COOKIE : [];
        $this->sessionData = isset($_SESSION) ? $_SESSION : [];
        $this->headerData = [];

        if (function_exists("getallheaders")) {
            $this->headerData = \getallheaders();
        } else {
            foreach ($_SERVER as $key => $value) {
                if (stripos($key, "http_") === 0) {
                    $key = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($key, 5)))));
                    $this->header($key, $value);
                }
            }
        }

        $this->host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : (isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : '');
        $this->uri = isset($_SERVER['SCRIPT_URI']) ? $_SERVER['SCRIPT_URI'] : '';
        $this->urlPath = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '';
    }

    /**
     * Gets the Base url for the current request (e.g. http://localhost/), with optional appended path.
     *
     * The Base Url will not normally have a trailing URL, but if a path to append is included, one will be added.
     *
     * @param string $append Optional path to append to the URL
     * @return string
     */
    public function getUrlBase($append = '')
    {
        if (!isset($this->urlBase)) {
            $ssl = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on');
            $protocol = strtolower($_SERVER['SERVER_PROTOCOL']);
            $protocol = substr($protocol, 0, strpos($protocol, '/')) . (($ssl) ? 's' : '');

            $host = $this->host;
            if (strpos($host, ':') === false) {
                $port = $_SERVER['SERVER_PORT'];
                $port = ((!$ssl && $port == '80') || ($ssl && $port == '443')) ? '' : ':' . $port;
                $host = $this->host . $port;
            }

            $this->urlBase = $protocol . '://' . $host;
        }

        if ($append !== '' && strpos($append, '/') === false) {
            $append = '/' . $append;
        }

        return $this->urlBase.$append;
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
}
