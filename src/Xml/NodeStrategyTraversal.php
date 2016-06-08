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

require_once __DIR__ . '/NodeStrategyRead.php';

class NodeStrategyTraversal extends NodeStrategyRead
{
    protected $nodeHandlers = [];

    public function addNodeHandler($nodeName, NodeStrategy $strategy)
    {
        $this->nodeHandlers[ $nodeName ] = $strategy;

        return $this;
    }

    public function parse(\XMLReader $xmlReader, $startingDepth = 0, $parseOne = false)
    {
        parent::parse($xmlReader, $startingDepth, $parseOne);

        // Keep scanning elements while we have elements to scan and we are still within our scope
        // namely that the depth is greater than our own depth.
        while ($xmlReader->read() && ( $xmlReader->depth > $startingDepth )) {
            if ($xmlReader->nodeType == \XMLReader::ELEMENT) {
                foreach ($this->nodeHandlers as $name => $strategy) {
                    if ($name == "*" || $name == $xmlReader->name) {
                        $strategy->parse($xmlReader, $xmlReader->depth);

                        if ($parseOne) {
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }
}
