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

require_once __DIR__ . '/NodeStrategyRead.php';

class NodeStrategyCollation extends NodeStrategyRead
{

    private $startDepth;

    /**
     * @param callable $callBack
     */
    public function __construct($callBack)
    {
        parent::__construct($callBack);
    }

    protected function parse(\XMLReader $xmlReader, $startingDepth = 0, $parseOne = false)
    {
        $this->startDepth = $startingDepth;
        parent::parse($xmlReader, $startingDepth, $parseOne);
    }

    /**
     * The collation strategy should to collate all child nodes before callback
     *
     * @param Node       $node
     * @param \XMLReader $xmlReader
     *
     * @return Node The node with children collated will be passed to the callback
     */
    protected function processNode(Node $node, \XMLReader $xmlReader)
    {
        $node = parent::processNode($node, $xmlReader);
        $children = [];

        if (!$xmlReader->isEmptyElement) {
            $scanner = new NodeStrategyTraversal();
            $scanner->addNodeHandler('*', new self(function ($node) use (&$children) {
                $children[] = $node;
            }));
            $scanner->parse($xmlReader, $this->startDepth);
        }

        $node->children = $children;

        return $node;
    }

}