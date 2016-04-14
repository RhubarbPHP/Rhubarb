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

namespace Rhubarb\Crown\Response;

require_once __DIR__ . "/Response.php";

/**
 * Use in conjunction with ForceResponseException to redirect web requests to alternative URIs.
 */
class RedirectResponse extends Response
{
    private $url;

    const PERMANENT_RESPONSE_CODE = 301;
    const PERMANENT_RESPONSE_MESSAGE = 'Moved Permanently';

    const TEMPORARY_RESPONSE_CODE = 302;
    const TEMPORARY_RESPONSE_MESSAGE = 'Found';

    /**
     * RedirectResponse constructor.
     * @param string $url The new URI to redirect the client to
     * @param null|object $generator A reference to the object that generated this response for traceability.
     * @param bool $permanent If true, the response will use a 302 HTTP code, otherwise it will use 301.
     *     Note that permanent redirects are aggressively cached by browsers and will result in them not even
     *     requesting the current URI in future until the browser's cache of this response expires.
     */
    public function __construct($url, $generator = null, $permanent = false)
    {
        parent::__construct($generator);

        $this->url = $url;

        if ($permanent) {
            $this->setResponseCode(self::PERMANENT_RESPONSE_CODE);
            $this->setResponseMessage(self::PERMANENT_RESPONSE_MESSAGE);
        }

        $this->setHeader("Location", $url);
    }

    /**
     * Makes the response use a 302 (Moved Permanently) HTTP response code.
     * Note that permanent redirects are aggressively cached by browsers and will result in them not even
     * requesting the current URI in future until the browser's cache of this response expires.
     */
    public function makePermanent()
    {
        $this->setResponseCode(self::PERMANENT_RESPONSE_CODE);
        $this->setResponseMessage(self::PERMANENT_RESPONSE_MESSAGE);
    }

    /**
     * Makes the response use a 301 (Found) HTTP response code.
     */
    public function makeTemporary()
    {
        $this->setResponseCode(self::TEMPORARY_RESPONSE_CODE);
        $this->setResponseMessage(self::TEMPORARY_RESPONSE_MESSAGE);
    }

    public function getUrl()
    {
        return $this->url;
    }
}
