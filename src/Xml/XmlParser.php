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

namespace Rhubarb\Crown\Xml;

class XmlParser
{
    /**
     * @var string The path to the source document.
     */
    private $xmlFilePath;

    /**
     * @var NodeStrategyTraversal The parser itself relies on the scan node strategy for performance.
     */
    private $topLevelNodeStrategy;

    /**
     * @var \XMLReader
     */
    private $reader;

    public function __construct($xmlFilePath)
    {
        $this->xmlFilePath = $xmlFilePath;
        $this->topLevelNodeStrategy = new NodeStrategyTraversal();
    }

    private function open()
    {
        if (!$this->reader) {
            $this->reader = new \XMLReader();
            $this->reader->open($this->xmlFilePath);
        }
    }

    private function close()
    {
        $this->reader->close();
    }

    public function parse()
    {
        $this->open();

        $this->topLevelNodeStrategy->parse($this->reader, -1);

        $this->close();
    }

    public function parseOne()
    {
        $this->open();

        return $this->topLevelNodeStrategy->parse($this->reader, -1, true);
    }

    public function addNodeHandler($nodeName, NodeStrategy $strategy)
    {
        $this->topLevelNodeStrategy->addNodeHandler($nodeName, $strategy);
    }
}
