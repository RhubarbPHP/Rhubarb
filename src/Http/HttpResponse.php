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

namespace Rhubarb\Crown\Http;

use Rhubarb\Crown\Context;

class HttpResponse implements \JsonSerializable
{
    private $headers = [];

    private $responseBody = "";
    private $responseCode = "";

    public function getHeader($header, $defaultValue = null)
    {
        return (isset($this->headers[$header])) ? $this->headers[$header] : $defaultValue;
    }

    public function setHeaders($headers)
    {
        $this->headers = $headers;
    }

    /**
     * @param string $responseBody
     */
    public function setResponseBody($responseBody)
    {
        $this->responseBody = $responseBody;
    }

    /**
     * @return string
     */
    public function getResponseBody()
    {
        return $this->responseBody;
    }

    /**
     * @param string $name Cookie name
     * @param string $value Cookie value
     * @param int $expirySecondsFromNow Time the cookie should last for in seconds. Defaults to 2 weeks.
     * @param string $path Web path the cookie should be available to - defaults to "/", the whole site
     * @param string $domain Domain the cookie should be available to - defaults to current subdomain. Set to ".domain.com" to make available to all subdomains.
     */
    public static function setCookie($name, $value, $expirySecondsFromNow = 1209600, $path = "/", $domain = null)
    {
        setcookie($name, $value, time() + $expirySecondsFromNow, $path, $domain);
        $request = Context::currentRequest();
        $request->cookie($name, $value);
    }

    /**
     * @param string $name Cookie name
     * @param string $path Web path the cookie should be available to - defaults to "/", the whole site
     * @param string $domain Domain the cookie should be available to - defaults to current subdomain. Set to ".domain.com" to make available to all subdomains.
     */
    public static function unsetCookie($name, $path = "/", $domain = null)
    {
        self::setCookie($name, null, -1000, $path, $domain);
    }

    /**
     * @return string
     */
    public function getResponseCode()
    {
        return $this->responseCode;
    }

    /**
     * @param string $responseCode
     */
    public function setResponseCode($responseCode)
    {
        $this->responseCode = $responseCode;
    }

    public function isSuccess()
    {
        return $this->responseCode >= 200 && $this->responseCode <= 299;
    }

    public function isRedirect()
    {
        return $this->responseCode >= 300 && $this->responseCode <= 399;
    }

    public function isRequestError()
    {
        return $this->responseCode >= 400 && $this->responseCode <= 499;
    }

    public function isServerError()
    {
        return $this->responseCode >= 500 && $this->responseCode <= 599;
    }

    public function jsonSerialize()
    {
        return [
            'headers' => $this->headers,
            'responseCode' => $this->responseCode,
            'responseBody' => $this->responseBody
        ];
    }
}
