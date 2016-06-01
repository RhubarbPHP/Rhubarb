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

class MultiPartFormDataRequest extends WebRequest
{

    public function getPayload()
    {
        // Support multipart file PUT operations
        if (isset($this->serverData['REQUEST_METHOD']) && $this->serverData['REQUEST_METHOD'] === 'PUT') {
            $this->parsePut();
        }

        $requestBody = array_merge($_FILES, $_POST);
        return $requestBody;
    }

    /**
     * Parses PUT request input into $_FILES
     * @link http://stackoverflow.com/a/18678678
     */
    private function parsePut()
    {
        global $_PUT;

        /* PUT data comes in on the stdin stream */
        $putStream = fopen("php://input", "r");

        $rawData = '';

        /* Read the data 1 KB at a time
           and write to the file */
        while ($chunk = fread($putStream, 1024)) {
            $rawData .= $chunk;
        }

        $rawData = ltrim($rawData);

        /* Close the streams */
        fclose($putStream);

        // Fetch content and determine boundary
        $boundary = substr($rawData, 0, strpos($rawData, "\r\n"));

        if (empty($boundary)) {
            parse_str($rawData, $data);
            $GLOBALS['_PUT'] = $data;
            return;
        }

        // Fetch each part
        $parts = array_slice(explode($boundary, $rawData), 1);
        $data = [];

        foreach ($parts as $part) {
            // If this is the last part, break
            if ($part == "--\r\n") {
                break;
            }

            // Separate content from headers
            $part = ltrim($part, "\r\n");
            list($rawHeaders, $body) = explode("\r\n\r\n", $part, 2);

            // Parse the headers list
            $rawHeaders = explode("\r\n", $rawHeaders);
            $headers = [];
            foreach ($rawHeaders as $header) {
                list($name, $value) = explode(':', $header);
                $headers[strtolower($name)] = ltrim($value, ' ');
            }

            // Parse the Content-Disposition to get the field name, etc.
            if (isset($headers['content-disposition'])) {
                $filename = null;
                $tmpName = null;
                preg_match(
                    '/^(.+); *name="([^"]+)"(; *filename="([^"]+)")?/',
                    $headers['content-disposition'],
                    $matches
                );
                list(, $type, $name) = $matches;

                //Parse File
                if (isset($matches[4])) {
                    //if labeled the same as previous, skip
                    if (isset($_FILES[$matches[2]])) {
                        continue;
                    }

                    //get filename
                    $filename = $matches[4];

                    //get tmp name
                    $filenameParts = pathinfo($filename);
                    $tmpName = tempnam(ini_get('upload_tmp_dir'), $filenameParts['filename']);

                    //populate $_FILES with information, size may be off in multibyte situation
                    $_FILES[$matches[2]] = [
                        'error' => 0,
                        'name' => $filename,
                        'tmp_name' => $tmpName,
                        'size' => strlen($body),
                        'type' => $value,
                    ];

                    //place in temporary directory
                    file_put_contents($tmpName, $body);
                } //Parse Field
                else {
                    $data[$name] = substr($body, 0, strlen($body) - 2);
                }
            }
        }
        $GLOBALS['_PUT'] = $data;
    }
}
