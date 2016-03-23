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

require_once __DIR__ . '/../Xml/XmlParser.php';
require_once __DIR__ . '/../Xml/NodeStrategyCollationDictionary.php';
require_once __DIR__ . '/RecordStream.php';

use Rhubarb\Crown\Exceptions\ImplementationException;
use Rhubarb\Crown\Xml\NodeStrategyCollationDictionary;
use Rhubarb\Crown\Xml\XmlParser;

/**
 * Scans an XML document for individual nodes and pops them off through a stream.
 */
class XmlStream extends RecordStream
{
    /**
     * @var
     */
    private $xmlFilePath;

    /**
     * @var
     */
    private $nodeName;

    /**
     * @var XmlParser
     */
    private $xmlParser;

    private $lastResult = null;

    public function __construct($nodeName, $xmlFilePath)
    {
        $this->nodeName = $nodeName;
        $this->xmlFilePath = $xmlFilePath;
    }

    private function open()
    {
        if ($this->xmlParser == null) {
            $this->xmlParser = new XmlParser($this->xmlFilePath);
            $this->xmlParser->addNodeHandler($this->nodeName, new NodeStrategyCollationDictionary(function ($node) {
                $this->lastResult = $node;
            }));
        }
    }

    public function readNextItem()
    {
        $this->open();

        $available = $this->xmlParser->parseOne();

        if ($available) {
            return $this->lastResult;
        }

        return false;
    }

    public function appendItem($item)
    {
        throw new ImplementationException();
    }
}
