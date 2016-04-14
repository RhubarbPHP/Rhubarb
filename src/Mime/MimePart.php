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

namespace Rhubarb\Crown\Mime;

class MimePart
{
    const MIME_TYPE_APPLICATION_JSON = 'application/json';
    const MIME_TYPE_IMAGE_GIF = 'image/gif';
    const MIME_TYPE_IMAGE_JPEG = 'image/jpeg';
    const MIME_TYPE_IMAGE_PNG = 'image/png';
    const MIME_TYPE_TEXT_PLAIN = 'text/plain';
    const MIME_TYPE_TEXT_HTML = 'text/html';
    const MIME_TYPE_TEXT_CSS = 'text/css';


    /**
     * @var string[]
     */
    protected $headers;

    /**
     * @var string
     */
    protected $body;

    public function getHeaders()
    {
        return $this->headers;
    }

    public function getRawBody()
    {
        return $this->body;
    }

    /**
     * Set's the raw body of this part from a string.
     *
     * @param $body
     */
    public function setRawBody($body)
    {
        $this->body = $body;
    }

    public function addHeader($header, $value)
    {
        $this->headers[$header] = $value;
    }

    /**
     * Transforms the part as per the transform encoding header and returns the transformed result.
     */
    final public function getTransformedBody()
    {
        $encoding = (isset($this->headers["Content-Transfer-Encoding"])) ? $this->headers["Content-Transfer-Encoding"] : "";

        $rawBody = $this->getRawBody();

        switch ($encoding) {
            case "quoted-printable":
                return quoted_printable_decode($rawBody);
                break;
            case "base64":
                return base64_decode($rawBody);
                break;
            default:
                return $rawBody;
        }
    }

    /**
     * Takes a transformed body and turns it into the raw body string needed for the mime encoding.
     *
     * @param $transformedBody
     */
    final public function setTransformedBody($transformedBody)
    {
        $encoding = (isset($this->headers["Content-Transfer-Encoding"])) ? $this->headers["Content-Transfer-Encoding"] : "";

        switch ($encoding) {
            case "quoted-printable":
                $this->setRawBody(quoted_printable_encode($transformedBody));
                break;
            case "base64":
                $this->setRawBody(chunk_split(base64_encode($transformedBody), 76));
                break;
            default:
                $this->setRawBody($transformedBody);
        }
    }

    public function setHeaders($headers)
    {
        $this->headers = $headers;
    }

    protected function getHeadersAsString()
    {
        $headers = $this->getHeaders();
        $headerString = "";

        foreach ($headers as $header => $value) {
            $headerString .= $header . ": " . $value . "\r\n";
        }

        return trim($headerString);
    }

    public static function fromLines($lines)
    {
        $headers = [];
        $body = "";
        $headersScan = true;

        foreach ($lines as $line) {
            if ($line == "") {
                $headersScan = false;
                continue;
            }

            if ($headersScan) {
                $pair = explode(":", $line, 2);
                $headers[$pair[0]] = trim($pair[1]);
            } else {
                $body .= $line . "\r\n";
            }
        }

        // The body normally has a blank line at the end.
        $body = rtrim($body);

        $mimeType = (isset($headers["Content-Type"])) ? $headers["Content-Type"] : "text/html";
        $part = null;

        switch ($mimeType) {
            case "image/jpeg":
            case "image/png":
            case "image/gif":
            case "image/jpg":
                $part = new MimePartImage($mimeType);
                break;
            default:
                $part = new MimePartText($mimeType);
                break;
        }

        $part->setHeaders($headers);
        $part->setRawBody($body);

        return $part;
    }

    public function __toString()
    {
        $string = "";

        foreach ($this->headers as $header => $value) {
            $string .= $header . ": " . $value . "\r\n";
        }

        $string .= "\r\n" . $this->getRawBody();

        return $string;
    }
}
