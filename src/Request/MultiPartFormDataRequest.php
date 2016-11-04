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
     * This code came from a stackexchange post which I don't have time to neaten up
     * @link http://codereview.stackexchange.com/questions/69882/parsing-multipart-form-data-in-php-for-put-requests
     */
    private function parsePut()
    {
        $contentType = (isset($_SERVER['CONTENT_TYPE'])) ? $_SERVER['CONTENT_TYPE'] : 'application/x-www-form-urlencoded';

        $tmp = explode(';', $contentType);
        $boundary = '';
        $encoding = '';

        $contentType = array_shift($tmp);

        foreach ($tmp as $t) {
            if (strpos($t, 'boundary') !== false) {
                $t = explode('=', $t, 2);
                if (isset($t[1])) {
                    $boundary = '--' . $t[1];
                }
            } elseif (strpos($t, 'charset') !== false) {
                $t = explode('=', $t, 2);
                if (isset($t[1])) {
                    $encoding = $t[1];
                }
            }
            if ($boundary !== '' && $encoding !== '') {
                break;
            }
        }

        switch ($contentType) {
            case 'multipart/form-data':
                #grab multipart boundary from content type header
                if (!empty($boundary)) {
                    break;
                }
                $contentType = 'application/x-www-form-urlencoded';
            // Fallthrough intentional - no boundary supplied with multipart content-type, so treat as url encoded
            case 'application/x-www-form-urlencoded':
                parse_str(file_get_contents('php://input'), $_POST);
                return;
            default:
                return;
        }

        $_FILES = [];
        $_POST = [];
        $chunkLength = 8096;
        $raw_headers = '';

        $stream = fopen('php://input', 'rb');

        $sanity = fgets($stream, strlen($boundary) + 5);

        // Skip initial blank line
        if ($sanity == "\r\n") {
            $sanity = fgets($stream, strlen($boundary) + 5);
        }

        if (rtrim($sanity) !== $boundary) {
            #malformed file, boundary should be first item
            return;
        }

        while (($chunk = fgets($stream)) !== false) {
            if ($chunk === $boundary) {
                continue;
            }

            if (rtrim($chunk) == '') { #blank line means we have all the headers and are going to read content
                $raw_headers = explode("\r\n", $raw_headers);
                $headers = [];
                $matches = [];

                foreach ($raw_headers as $header) {
                    if (strpos($header, ':') === false) {
                        continue;
                    }
                    list($name, $value) = explode(':', $header, 2);
                    $headers[strtolower($name)] = ltrim($value, ' ');
                }

                $raw_headers = '';

                if (!isset($headers['content-disposition'])) {
                    continue;
                }

                $filename = null;
                preg_match(
                    '/^(.+); *name="([^"]+)"(; *filename="([^"]+)")?/',
                    $headers['content-disposition'],
                    $matches
                );
                list(, $type, $name) = $matches;

                #process data
                if (isset($matches[4])) { #pull in file
                    $error = UPLOAD_ERR_OK;

                    $filename = $matches[4];
                    $filename_parts = pathinfo($filename);
                    $contentType = 'unknown';

                    if (isset($headers['content-type'])) {
                        $tmp = explode(';', $headers['content-type']);
                        $contentType = $tmp[0];
                    }

                    $tmpnam = tempnam(ini_get('upload_tmp_dir'), 'php');
                    $fileHandle = fopen($tmpnam, 'wb');


                    if ($fileHandle === false) {
                        $error = UPLOAD_ERR_CANT_WRITE;
                    } else {
                        $lastLine = null;
                        while (($chunk = fgets($stream, $chunkLength)) !== false && strpos($chunk, $boundary) !== 0) {
                            if ($lastLine !== null) {
                                if (fwrite($fileHandle, $lastLine) === false) {
                                    $error = UPLOAD_ERR_CANT_WRITE;
                                    break;
                                }
                            }
                            $lastLine = $chunk;
                        }

                        if ($lastLine !== null && $error !== UPLOAD_ERR_CANT_WRITE) {
                            if (fwrite($fileHandle, rtrim($lastLine, "\r\n")) === false) {
                                $error = UPLOAD_ERR_CANT_WRITE;
                            }
                        }
                    }

                    $_FILES[$name] = [
                        'name' => $filename,
                        'type' => $contentType,
                        'tmp_name' => $tmpnam,
                        'error' => $error,
                        'size' => filesize($tmpnam)
                    ];

                    continue;
                } else { #pull in variable
                    $fullValue = '';
                    $lastLine = null;
                    while (($chunk = fgets($stream)) !== false && strpos($chunk, $boundary) !== 0) {
                        if ($lastLine !== null) {
                            $fullValue .= $lastLine;
                        }

                        $lastLine = $chunk;
                    }

                    if ($lastLine !== null) {
                        $fullValue .= rtrim($lastLine, "\r\n");
                    }

                    if (isset($headers['content-type'])) {
                        $tmp = explode(';', $headers['content-type']);
                        $encoding = '';

                        foreach ($tmp as $t) {
                            if (strpos($t, 'charset') !== false) {
                                $t = explode($t, '=', 2);
                                if (isset($t[1])) {
                                    $encoding = $t[1];
                                }
                                break;
                            }
                        }

                        if ($encoding !== '' && strtoupper($encoding) !== 'UTF-8' && strtoupper($encoding) !== 'UTF8') {
                            $tmp = mb_convert_encoding($fullValue, 'UTF-8', $encoding);
                            if ($tmp !== false) {
                                $fullValue = $tmp;
                            }
                        }

                    }

                    $fullValue = $name . '=' . $fullValue;
                    $origName = $name;
                    $tmp = [];
                    parse_str($fullValue, $tmp);
                    $_POST = $this->recursiveSetter($origName, $_POST, $tmp);
                }
                continue;
            }

            $raw_headers .= $chunk;
        }

        $GLOBALS['_PUT'] = $_POST;
        fclose($stream);
    }

    private function recursiveSetter($spec, $array, $array2)
    {
        if (!is_array($spec)) {
            $spec = explode('[', (string)$spec);
        }
        $currLev = array_shift($spec);
        $currLev = rtrim($currLev, ']');
        if ($currLev !== '') {
            $currLev = $currLev . '=p';
            $tmp = [];
            parse_str($currLev, $tmp);
            $tmp = array_keys($tmp);
            $currLev = reset($tmp);
        }

        if (!is_array($array)) {
            $array = $array2;
        } elseif ($currLev === '') {
            $array[] = reset($array2);
        } elseif (isset($array[$currLev]) && isset($array2[$currLev])) {
            $array[$currLev] = $this->recursiveSetter($spec, $array[$currLev], $array2[$currLev]);
        } elseif (isset($array2[$currLev])) {
            $array[$currLev] = $array2[$currLev];
        }
        return $array;
    }
}
