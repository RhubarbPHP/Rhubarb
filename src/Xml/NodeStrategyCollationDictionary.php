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

require_once __DIR__ . '/NodeStrategyCollation.php';

/**
 * Extends the collation strategy by merging unique child text elements with the attributes into a single
 * associative array
 */
class NodeStrategyCollationDictionary extends NodeStrategyCollation
{
    protected function getArrayForNode(Node $node)
    {

    }

    protected function processNode(Node $node)
    {
        $result = $node->attributes;

        $containerKeys = [];

        foreach ($node->children as $child) {
            $arrayChild = $this->processNode($child);

            $childName = $child->name;
            $childValue = $arrayChild;

            if (sizeof($child->children) == 0) {
                $childValue = $child->text;
            }

            if (isset($result[$childName])) {
                if (!in_array($childName, $containerKeys)) {
                    $result[$childName] = [$result[$childName]];
                    $containerKeys[] = $childName;
                }

                $result[$childName][] = $childValue;
            } else {
                $result[$childName] = $childValue;
            }
        }

        return $result;
    }
}
