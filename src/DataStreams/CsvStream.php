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


namespace Rhubarb\Crown\DataStreams;

require_once __DIR__ . '/RecordStream.php';

use Rhubarb\Crown\Exceptions\EndOfStreamException;

/**
 * A Stream for reading and writing CSV files
 *
 * This CSV reader and writer is rfc4180 compliant and will handle encapsulated contents containing the
 * escaped encapsulation character and carriage returns.
 *
 * If the CSV file you need to parse is not rfc4180 compliant and uses an escape character before the
 * enclosure character if it appears in the cell contents, then you should set the $escapeCharacter
 * property before starting to read your stream.
 */
class CsvStream extends RecordStream
{
    private $filePath;

    private $fileStream = null;

    private $externalStream = false;

    private $writable = false;

    private $headers = null;

    private $hasHeaders = true;

    private $needToWriteHeaders = true;

    private $remnantBuffer = "";

    public $trimHeadings = true;

    public $trimValues = false;

    /**
     * The character used to enclose string values that might contain the delimiter.
     * @var string
     */
    public $enclosure = "\"";

    /**
     * The delimiter character, normally a comma or semicolon.
     *
     * @var string
     */
    public $delimiter = ",";

    /**
     * The escape character that precedes legitimate occurrences of the enclosure character within the
     * cell content.
     *
     * Note an RFC4180 compliant CSV file will escape the enclosure character by repeating it so for example
     * the value Summer "Time" becomes Summer ""Time"". In this case you should leave $escapeCharacter null
     * in which case the RFC behaviour will be adopted.
     *
     * @var string|bool
     */
    public $escapeCharacter = null;

    public function __construct($filePathOrStream)
    {
        if (is_resource($filePathOrStream)){
            $this->externalStream = true;
            $this->fileStream = $filePathOrStream;
            $this->readHeaders();
        } else {
            $this->filePath = $filePathOrStream;
        }

        parent::__construct();
    }

    public function getFilePath()
    {
        return $this->filePath;
    }

    public function setHeaders($headers)
    {
        $this->headers = $headers;
        $this->hasHeaders = true;
    }

    public function readHeaders()
    {
        if ($this->headers !== null){
            return $this->headers;
        }

        if (!$this->externalStream) {
            $this->close();

            if (!$this->fileStream) {
                if (!file_exists($this->filePath)) {
                    $this->needToWriteHeaders = true;
                    return $this->headers = [];
                }

                $this->fileStream = fopen($this->filePath, "r");
            }
        }

        $rawCsvData = $this->readCsvLine($this->trimHeadings);

        $this->headers = $rawCsvData;
        $this->needToWriteHeaders = false;

        return $this->headers;
    }

    private function readCsvLine($trimValues = false)
    {
        if (feof($this->fileStream) && ($this->remnantBuffer == "")) {
            throw new EndOfStreamException();
        }

        $inEnclosure = false;
        $readFullLine = false;

        $values = [];

        $addValue = function (&$value) use (&$values, $trimValues) {
            $values[] = utf8_encode($trimValues ? trim($value) : $value);
            $value = "";
        };

        $valueBuffer = "";

        $escapeCharacter = ($this->escapeCharacter !== null) ? $this->escapeCharacter : $this->enclosure;

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

            // If the last character of the data read is our escape character we need to read one more
            // character from the stream as we may have encountered an escape sequence. Without the second
            // character we wouldn't handle this properly.
            if ($csvData[strlen($csvData) - 1] == $escapeCharacter) {
                $csvData .= fread($this->fileStream, 1);
            }

            // Be sure to clear the remnant buffer.
            $this->remnantBuffer = "";

            $csvDataLength = strlen($csvData);

            // Check for and skip UTF8 BOM
            $startByte = $csvDataLength > 2 && substr($csvData, 0, 3) === b"\xEF\xBB\xBF" ? 3 : 0;

            for ($i = $startByte; $i < $csvDataLength; $i++) {
                $byte = $csvData[$i];

                if ($i < $csvDataLength - 1 && $inEnclosure) {
                    // Look for 2 byte escaped enclosure syntax
                    if (($byte == $escapeCharacter) && ($csvData[$i + 1] == $this->enclosure)) {
                        $valueBuffer .= $this->enclosure;
                        $i++;
                        continue;
                    }
                }

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
                return false;
            }
        }

        $mode = ($allowWriting) ? "a+" : "r";

        $this->remnantBuffer = "";
        $this->fileStream = fopen($this->filePath, $mode);
        $this->writable = $allowWriting;
        return $this->fileStream;
    }

    public function readNextItem()
    {
        $this->open();

        try {
            $data = $this->readCsvLine($this->trimValues);
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

    public function appendItem($itemData, $allCells = false)
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

        $this->writeItem($itemData, $allCells);
    }

    private function writeItem($itemData, $allCells = false)
    {
        $this->open(true);

        $dataToWrite = $itemData;

        if ($this->hasHeaders) {
            $dataToWrite = [];

            if ($allCells) {
                $dataToWrite = $itemData;
            } else {

                foreach ($this->headers as $key => $value) {
                    if (isset($itemData[$value])) {
                        $dataToWrite[] = $itemData[$value];
                    } else {
                        $dataToWrite[] = "";
                    }
                }
            }
        }

        $escapeCharacter = ($this->escapeCharacter !== null) ? $this->escapeCharacter : $this->enclosure;

        if ($this->enclosure == "") {
            $enclosedData = $dataToWrite;
        } else {
            $enclosedData = [];

            foreach ($dataToWrite as $value) {
                if ((strpos($value, $this->enclosure) !== false) || (strpos($value, "\n") !== false) || (strpos(
                            $value,
                            $this->delimiter
                        ) !== false)
                ) {
                    $enclosedData[] = $this->enclosure . str_replace(
                            $this->enclosure,
                            $escapeCharacter . $this->enclosure,
                            $value
                        ) . $this->enclosure;
                } else {
                    $enclosedData[] = $value;
                }
            }
        }

        fwrite($this->fileStream, "\n" . implode($this->delimiter, $enclosedData));
    }

    public function writeHeaders()
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

        $this->needToWriteHeaders = false;
    }
}
