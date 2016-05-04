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

namespace Rhubarb\Crown\Http;

/**
 * Encapsulates all the details needed to perform a REST client request
 *
 * This encapsulation is necessary as some clients may upon authentication failure try to re-authenticate
 * and then run the request again.
 */
class HttpRequest
{
    private $url;

    private $method;

    private $payload = null;

    private $headers = [];

    public function __construct($url, $method = "get", $payload = null)
    {
        $this->method = $method;
        $this->payload = $payload;
        $this->url = $url;
    }

    public function addHeader($header, $value)
    {
        $this->headers[$header] = $value;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param mixed $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * @param mixed $payload
     */
    public function setPayload($payload)
    {
        $this->payload = $payload;
    }

    /**
     * @param mixed $uri
     */
    public function setUrl($uri)
    {
        $this->url = $uri;
    }

    /**
     * @return mixed
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return mixed
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }
}
