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
use Rhubarb\Crown\Mime\MimePart;

class MultiPartFormDataRequest extends WebRequest
{

    /** @var null|string */
    private $rawRequest = null;

    public function getPayload()
    {
        // Support multipart file PUT operations
        if (isset($this->serverData['REQUEST_METHOD']) && $this->serverData['REQUEST_METHOD'] === 'PUT') {
            $this->parsePut();
        }

        $requestBody = array_merge($this->filesData, $this->postData);
        return $requestBody;
    }

    /**
     * @return string
     */
    public function parsePut()
    {
        // return raw request if set - this is for testing purposes
        if ($this->rawRequest !== null) {
            $rawRequest = $this->rawRequest;
        } else {
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

            $rawRequest = $headers . "\n" . $requestBody;
        }

        $document = MimeDocument::fromString($rawRequest);
        foreach ($document->getParts() as $part) {
            /** @var MimePart $part */
            foreach ($part->getHeaders() as $header => $value) {
                if (strtolower($header) === 'content-disposition') {
                    $partName = preg_replace('/.*? name="(.+?)".*/', '$1', $value);
                    if (stripos($value, ' filename=') !== false) {
                        $fileName = preg_replace('/.*? filename="(.+?)"/', '$1', $value);
                        $tempFile = tempnam(sys_get_temp_dir(), 'put-file-');
                        $fileContents = $part->getTransformedBody();
                        file_put_contents($tempFile, $fileContents);
                        $this->filesData[$partName] = [
                            'name' => $fileName,
                            'type' => @mime_content_type($fileName),
                            'tmp_name' => $tempFile,
                            'error' => UPLOAD_ERR_OK,
                            'size' => strlen($fileContents),
                        ];
                    } else {
                        //this does not look like a file, add it to the post data
                        $this->postData[$partName] = $part->getTransformedBody();
                    }
                }
            }
        }
    }

    /**
     * @param string $rawRequest
     */
    public function setRawRequest($rawRequest)
    {
        $this->rawRequest = $rawRequest;
    }
}
