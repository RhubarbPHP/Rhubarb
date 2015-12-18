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
 * @property-read bool $IsWebRequest
 * @property-read bool $IsSSL
 *
 * @property-read array $ServerData
 * @property-read array $GetData
 * @property-read array $PostData
 * @property-read array $FilesData
 * @property-read array $CookieData
 * @property-read array $SessionData
 * @property-read array $RequestData
 * @property-read array $HeaderData
 *
 * @property string $URI
 * @property string $Host
 * @property string $UrlPath
 */
class WebRequest extends Request
{
    public function initialise()
    {
        $this->modelData['IsWebRequest'] = true;

        // take copies of the relevant superglobals in case they get
        // modified later
        $this->modelData['ServerData'] = isset($_SERVER) ? $_SERVER : [];
        $this->modelData['GetData'] = isset($_GET) ? $_GET : [];
        $this->modelData['PostData'] = isset($_POST) ? $_POST : [];
        $this->modelData['FilesData'] = isset($_FILES) ? $_FILES : [];
        $this->modelData['CookieData'] = isset($_COOKIE) ? $_COOKIE : [];
        $this->modelData['SessionData'] = isset($_SESSION) ? $_SESSION : [];
        $this->modelData['RequestData'] = isset($_REQUEST) ? $_REQUEST : [];
        $this->modelData['HeaderData'] = [];

        if (function_exists("getallheaders")) {
            $this->modelData['HeaderData'] = \getallheaders();
        } else {
            foreach ($_SERVER as $key => $value) {
                if (stripos($key, "http_") === 0) {
                    $this->Header(str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($key, 5))))),
                        $value);
                }
            }
        }

        $this->Host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
        $this->URI = isset($_SERVER['SCRIPT_URI']) ? $_SERVER['SCRIPT_URI'] : '';
        $this->UrlPath = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '';
    }

    public function getIsSSL()
    {
        return !(empty($this->ServerData['HTTPS']) || $this->ServerData['HTTPS'] === 'off');
    }

    /**
     * @return string
     */
    public function getAcceptsRequestMimeType()
    {
        $typeString = strtolower($this->Header("Accept"));

        if (preg_match("/\*\/\*/", $typeString) || $typeString == "") {
            $typeString = "text/html";
            return $typeString;
        }

        return $typeString;
    }
}
