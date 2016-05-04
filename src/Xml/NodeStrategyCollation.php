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

namespace Rhubarb\Crown\Xml;

require_once __DIR__ . '/NodeStrategy.php';

class NodeStrategyCollation extends NodeStrategy
{
    private $callBack;

    public function __construct($callBack)
    {
        $this->callBack = $callBack;
    }

    public function parse(\XMLReader $xmlReader, $startingDepth = 0, $parseOne = false)
    {
        $node = new Node();
        $node->name = $xmlReader->name;
        $node->depth = $xmlReader->depth;
        $node->text = $xmlReader->readString();

        if ($xmlReader->moveToFirstAttribute()) {
            do {
                $node->attributes[$xmlReader->name] = $xmlReader->value;
            } while ($xmlReader->moveToNextAttribute());
        }

        $children = [];

        $scanner = new NodeStrategyTraversal();
        $scanner->addNodeHandler("*", new self(function ($node) use (&$children) {
            $children[] = $node;
        }));

        $scanner->parse($xmlReader, $startingDepth);

        $node->children = $children;

        $node = $this->processNode($node);

        $callBack = $this->callBack;
        $callBack($node);
    }

    protected function processNode(Node $node)
    {
        return $node;
    }
}
