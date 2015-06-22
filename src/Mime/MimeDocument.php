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

require_once __DIR__ . '/MimePart.php';

class MimeDocument extends MimePart
{
    private $boundary;

    /**
     * @var MimePart[];
     */
    private $parts = [];

    protected $message = "";

    public function __construct($mimeType = "multipart/related", $boundary = "")
    {
        if ($boundary == "") {
            $boundary = uniqid();
        }

        $this->boundary = $boundary;

        $this->setContentType($mimeType);
    }

    public function getParts()
    {
        return $this->parts;
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function setBoundary($boundary)
    {
        $this->boundary = $boundary;
    }

    public function setContentType($mimeType)
    {
        $this->headers["Content-Type"] = $mimeType . "; boundary=\"" . $this->boundary . "\"";
    }

    public function addPart(MimePart $part)
    {
        $this->parts[] = $part;
    }

    public function getRawBody()
    {
        $body = "";

        if ($this->message != "") {
            $body .= $this->message . "\r\n\r\n";
        }

        foreach ($this->parts as $part) {
            $body .= "--" . $this->boundary . "\r\n";
            $body .= (string)$part;
            $body .= "\r\n\r\n";
        }

        $body = rtrim($body);

        $body .= "\r\n--" . $this->boundary . "--\r\n";

        return $body;
    }

    public function toString()
    {
        return $this->getDocumentAsString();
    }

    public function addHeader($header, $value)
    {
        $this->headers[$header] = $value;
    }

    public function getHeaders()
    {
        $headers = ["MIME-Version" => "1.0"] + parent::getHeaders();

        return $headers;
    }

    public function getDocumentAsString()
    {
        $documentString = $this->getHeadersAsString();
        $documentString .= "\r\n\r\n";
        $documentString .= $this->getTransformedBody();

        return $documentString;
    }

    /**
     * Creates a document from a MIME string.
     *
     * Note this currently only supports a single level of MIME - no nesting.
     *
     * @param $documentString
     * @return MimeDocument
     */
    public static function fromString($documentString)
    {
        $document = new MimeDocument($documentString);

        $lines = explode("\n", $documentString);

        $boundary = false;

        $nextPartLines = [];

        $firstBoundaryFound = false;
        $mimeMessage = "";

        foreach ($lines as $line) {
            $line = trim($line);

            if (!$boundary) {
                if (preg_match("/Content-Type: (multipart\/.+);\s+boundary=\"([^\"]+)\"/", $line, $match)) {
                    $document->boundary = $match[2];
                    $document->setContentType($match[1]);
                    $boundary = $match[2];
                    continue;
                }
            } else {
                if ($line == "--" . $boundary . "--") {
                    $part = MimePart::fromLines($nextPartLines);
                    $document->addPart($part);
                    break;
                }

                if ($line == "--" . $boundary) {
                    if (!$firstBoundaryFound) {
                        $firstBoundaryFound = true;
                    } else {
                        $part = MimePart::fromLines($nextPartLines);
                        $document->addPart($part);
                        $nextPartLines = [];
                    }
                } else {
                    if ($firstBoundaryFound) {
                        $nextPartLines[] = $line;
                    } else {
                        $mimeMessage .= $line . "\r\n";
                    }
                }
            }
        }

        $document->setMessage(trim($mimeMessage));

        return $document;
    }

    public static function fromFile($filePath)
    {
        $content = file_get_contents($filePath);

        return self::fromString($content);
    }

    public function toFile($filePath)
    {
        $content = $this->toString();

        file_put_contents($filePath, $content);
    }
}