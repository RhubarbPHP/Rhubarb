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

class NodeStrategyRead extends NodeStrategy
{
    /** @var null|Callable */
    protected $callBack = null;
    protected $collateChildren;

    /**
     * @param callable|null $callBack
     * @param bool          $collateChildren Use with caution if it is possible there will be many children as these will
     *                                       all be loaded to memory before the callback method is executed
     */
    public function __construct($callBack = null, $collateChildren = false)
    {
        $this->callBack = $callBack;
        $this->collateChildren = $collateChildren;
    }

    public function parse(\XMLReader $xmlReader, $startingDepth = 0, $parseOne = false)
    {
        $node = new Node();
        $node->name = $xmlReader->name;
        $node->depth = $xmlReader->depth;
        $node->text = $xmlReader->readString();
        if ($xmlReader->moveToFirstAttribute()) {
            do {
                $node->attributes[ $xmlReader->name ] = $xmlReader->value;
            } while ($xmlReader->moveToNextAttribute());
        }
        $children = [];
        if ($this->collateChildren) {
            $scanner = new NodeStrategyTraversal();
            $scanner->addNodeHandler('*', new self(function ($node) use (&$children) {
                $children[] = $node;
            }));
            $scanner->parse($xmlReader, $startingDepth);
        }
        $node->children = $children;
        $node = $this->processNode($node);
        if ($this->callBack !== null) {
            $callBack = $this->callBack;
            $callBack($node);
        }
    }

    protected function processNode(Node $node)
    {
        return $node;
    }
}