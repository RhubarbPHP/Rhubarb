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

use Rhubarb\Crown\Mime\MimeDocument;

class MultiPartFormDataRequest extends WebRequest
{

    /** @var null|string */
    private $rawRequest = null;

    /**
     * @return string
     */
    public function getRawRequest()
    {
        // return raw request if set - this is for testing purposes
        if ($this->rawRequest !== null) {
            return $this->rawRequest;
        }

        /* PUT data only comes in on the stdin stream, so I'm reading for that for POST/PUT/FILES*/
        $requestStream = fopen('php://input', 'r');

        $requestBody = '';
        // read
        while ($chunk = fread($requestStream, 1024)) {
            $requestBody .= $chunk;
        }
        $requestBody = ltrim($requestBody);

        fclose($requestStream);

        $headers = '';
        foreach ($this->headerData as $header => $value) {
            $headers .= "{$header}: {$value}\n";
        }

        return $headers . "\n" . $requestBody;
    }

    /**
     * @param string $rawRequest
     */
    public function setRawRequest($rawRequest)
    {
        $this->rawRequest = $rawRequest;
    }

    /**
     * @return MimeDocument
     */
    public function getPayload()
    {
        return MimeDocument::fromString($this->getRawRequest());
    }
}
