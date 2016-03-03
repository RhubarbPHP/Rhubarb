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

/**
 * Handles setting of http headers
 *
 * This class abstracts access to the PHP header function to allow for unit tests that test
 * if certain headers have been set.
 *
 * Note this class can not be unit tested as the header function has no effect in the PHP CLI
 */
class HttpHeaders
{
    const MIME_TYPE_APPLICATION_JSON = 'application/json';
    const MIME_TYPE_IMAGE_GIF = 'image/gif';
    const MIME_TYPE_IMAGE_JPEG = 'image/jpeg';
    const MIME_TYPE_IMAGE_PNG = 'image/png';
    const MIME_TYPE_TEXT_PLAIN = 'text/plain';
    const MIME_TYPE_TEXT_HTML = 'text/html';
    const MIME_TYPE_TEXT_CSS = 'text/css';

    const HTTP_STATUS_SUCCESS_OK = 200;
    const HTTP_STATUS_REDIRECTION_PERMANENT = 301;
    const HTTP_STATUS_REDIRECTION_TEMPORARY = 302;
    const HTTP_STATUS_CLIENT_ERROR_BAD_REQUEST = 400;
    const HTTP_STATUS_CLIENT_ERROR_UNAUTHORIZED = 401;
    const HTTP_STATUS_CLIENT_ERROR_FORBIDDEN = 403;
    const HTTP_STATUS_CLIENT_ERROR_NOT_FOUND = 404;
    const HTTP_STATUS_CLIENT_ERROR_METHOD_NOT_ALLOWED = 405;
    const HTTP_STATUS_CLIENT_ERROR_CONFLICT = 409;
    const HTTP_STATUS_SERVER_ERROR_GENERIC = 500;

    /**
     * Set to true to indicate headers have already been flushed.
     *
     * @var bool
     */
    public static $flushed = false;

    /**
     * The collection of headers to output.
     *
     * @var array
     */
    private static $headers = [];

    /**
     * Sets a header
     *
     * @param $name
     * @param $value
     */
    public static function setHeader($name, $value)
    {
        self::$headers[$name] = $value;
    }

    /**
     * Sets the MIME content type and optionally character set of the response.
     * Common MIME content types are provided as constants in this class.
     *
     * @param string $contentMimeType MIME type of the response content
     * @param string $charset Optional character set specification. If empty, it will not be sent in the header. Defaults to utf-8
     */
    public static function setContentType($contentMimeType, $charset = 'utf-8')
    {
        self::setHeader('Content-Type', $contentMimeType . (empty($charset) ? '' : '; ' . $charset));
    }

    /**
     * Returns the headers currently set.
     *
     * @return array
     */
    public static function getHeaders()
    {
        return self::$headers;
    }

    /**
     * Clears any headers already set.
     */
    public static function clearHeaders()
    {
        self::$headers = [];
    }

    /**
     * Flushes any registered headers to the output buffer.
     */
    public static function flushHeaders()
    {
        foreach (self::$headers as $name => $content) {
            // We can't set headers when unit testing.
            if (!Settings::getSetting("Context", "UnitTesting", false)) {
                if ($content === false) {
                    header($name);
                } else {
                    header($name . ": " . $content);
                }
            }
        }

        self::$flushed = true;
    }
}
