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

require_once __DIR__ . '/NodeStrategy.php';

/**
 * Reads a node and executes a callback
 */
class NodeStrategyRead extends NodeStrategy
{

    /** @var callable|null */
    protected $callback = null;

    /**
     * @param callable|null $callback
     */
    public function __construct($callback = null)
    {
        $this->callback = $callback;
    }

    protected function parse(\XMLReader $xmlReader, $startingDepth = 0, $parseOne = false)
    {
        if ($this->callback !== null) {
            $node = new Node();
            $node->name = $xmlReader->name;
            $node->depth = $xmlReader->depth;
            $node->text = $xmlReader->readString();

            if ($xmlReader->hasAttributes && $xmlReader->moveToFirstAttribute()) {
                do {
                    $node->attributes[ $xmlReader->name ] = $xmlReader->value;
                } while ($xmlReader->moveToNextAttribute());
                $xmlReader->moveToElement();
            }

            $callback = $this->callback;
            $callback($this->processNode($node, $xmlReader));
        }
    }

    /**
     * Provides an opportunity to modify what is passed to the callback function
     *
     * @param \XMLReader $xmlReader
     * @param Node       $node
     *
     * @return Node The node will be passed to the callback by default
     */
    protected function processNode(Node $node, \XMLReader $xmlReader)
    {
        return $node;
    }

}