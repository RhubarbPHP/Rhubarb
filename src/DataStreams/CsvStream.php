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


namespace Rhubarb\Crown\DataStreams;

require_once __DIR__ . '/DataStream.php';

use Rhubarb\Crown\Exceptions\EndOfStreamException;

class CsvStream extends DataStream
{
    private $filePath;

    private $fileStream = null;

    private $writable = false;

    private $headers = null;

    private $hasHeaders = true;

    private $needToWriteHeaders = true;

    private $enclosure = "\"";

    private $delimiter = ",";

    private $remnantBuffer = "";

    public function __construct($filePath)
    {
        $this->filePath = $filePath;

        parent::__construct();
    }

    public function setHeaders($headers)
    {
        $this->headers = $headers;
        $this->hasHeaders = true;
    }

    private function readHeaders()
    {
        $this->close();

        if (!file_exists($this->filePath)) {
            $this->needToWriteHeaders = true;
            $this->headers = [];

            return;
        }

        $this->fileStream = fopen($this->filePath, "r");

        $rawCsvData = $this->readCsvLine();

        $this->headers = $rawCsvData;
        $this->needToWriteHeaders = false;
    }

    private function readCsvLine()
    {
        if (feof($this->fileStream) && ($this->remnantBuffer == "")) {
            throw new EndOfStreamException();
        }

        $inEnclosure = false;
        $readFullLine = false;

        $values = [];

        $addValue = function (&$value) use (&$values) {
            $values[] = utf8_encode(str_replace("@@@escapedenclosure@@@", $this->enclosure, $value));
            $value = "";
        };

        $valueBuffer = "";

        while (!$readFullLine) {
            if (feof($this->fileStream) && ($this->remnantBuffer == "")) {
                break;
            }

            // Read the next set of bytes and prepend with any remaining buffer from the last read.

            if (strlen($this->remnantBuffer) < 1024) {
                $csvData = $this->remnantBuffer . fread($this->fileStream, 1024);
            } else {
                $csvData = $this->remnantBuffer;
            }

            if ($this->enclosure != "") {
                $csvData = str_replace($this->enclosure . $this->enclosure, "@@@escapedenclosure@@@", $csvData);
            }

            // Be sure to clear the remnant buffer.
            $this->remnantBuffer = "";

            $csvDataLength = strlen($csvData);

            for ($i = 0; $i < $csvDataLength; $i++) {
                $byte = $csvData[$i];

                switch ($byte) {
                    case $this->enclosure:
                        $inEnclosure = !$inEnclosure;
                        continue;
                        break;
                    case $this->delimiter:
                        if (!$inEnclosure) {
                            $addValue($valueBuffer);
                        } else {
                            $valueBuffer .= $byte;
                        }

                        break;
                    case "\r":
                    case "\n":
                        if (!$inEnclosure) {
                            $i++;

                            if (($csvDataLength > $i) && ($csvData[$i] == "\r" || $csvData[$i] == "\n")) {
                                $i++;
                            }

                            $this->remnantBuffer = substr($csvData, $i);

                            break 3;
                        } else {
                            $valueBuffer .= $byte;
                        }

                        break;
                    default:
                        $valueBuffer .= $byte;
                        break;
                }
            }
        }

        if ($valueBuffer != "") {
            $addValue($valueBuffer);
        }

        return $values;
    }

    public function close()
    {
        if ($this->fileStream != null) {
            fclose($this->fileStream);
            $this->fileStream = null;
            $this->remnantBuffer = "";
        }
    }

    private function open($allowWriting = false)
    {
        if ($this->fileStream !== null) {
            if ($allowWriting && !$this->writable) {
                $this->close();
            } else {
                return $this->fileStream;
            }
        }

        if ($this->hasHeaders && $this->headers === null) {
            // Headers can't be read in write mode (as the stream position is at the end of the file)
            // so we must read headers first.
            $this->readHeaders();

            if ($allowWriting) {
                // For writing we need to reclose and open the stream in write mode.
                $this->close();
            } else {
                $this->writable = false;
                return;
            }
        }

        $mode = ($allowWriting) ? "a+" : "r";

        $this->remnantBuffer = "";
        $this->fileStream = fopen($this->filePath, $mode);
        $this->writable = $allowWriting;
    }

    public function readNextItem()
    {
        $this->open();

        try {
            $data = $this->readCsvLine();
        } catch (EndOfStreamException $er) {
            return false;
        }

        if ($this->hasHeaders) {
            $newData = [];

            foreach ($this->headers as $key => $value) {
                if (isset($data[$key])) {
                    $newData[$value] = $data[$key];
                } else {
                    $newData[$value] = "";
                }
            }

            $data = $newData;
        }

        return $data;
    }

    public function appendItem($itemData)
    {
        $this->open(true);

        if ($this->needToWriteHeaders) {
            if (sizeof($this->headers) == 0) {
                // As we don't have any headers we will use all the headers from the item being passed instead.
                $this->headers = array_keys($itemData);
            }

            if (sizeof($this->headers)) {
                $this->writeHeaders();
                $this->needToWriteHeaders = false;
            }
        }

        $this->writeItem($itemData);
    }

    private function writeItem($itemData)
    {
        $this->open(true);

        $dataToWrite = $itemData;

        if ($this->hasHeaders) {
            $dataToWrite = [];

            foreach ($this->headers as $key => $value) {
                $dataToWrite[] = $itemData[$value];
            }
        }

        if ($this->enclosure == "") {
            $enclosedData = $dataToWrite;
        } else {
            $enclosedData = [];

            foreach ($dataToWrite as $value) {
                if ((strpos($value, $this->enclosure) !== false) || (strpos($value, "\n") !== false) || (strpos($value, $this->delimiter) !== false)) {
                    $enclosedData[] = $this->enclosure . str_replace($this->enclosure, $this->enclosure . $this->enclosure, $value) . $this->enclosure;
                } else {
                    $enclosedData[] = $value;
                }
            }
        }

        fwrite($this->fileStream, "\n" . implode($this->delimiter, $enclosedData));
    }

    private function writeHeaders()
    {
        $this->open(true);

        $dataToWrite = $this->headers;

        if ($this->enclosure == "") {
            $enclosedData = $dataToWrite;
        } else {
            $enclosedData = [];

            foreach ($dataToWrite as $value) {
                if (strpos($value, $this->enclosure) !== false) {
                    $enclosedData[] = $this->enclosure . $value . $this->enclosure;
                } else {
                    $enclosedData[] = $value;
                }
            }
        }

        fwrite($this->fileStream, implode($this->delimiter, $enclosedData));
    }
}