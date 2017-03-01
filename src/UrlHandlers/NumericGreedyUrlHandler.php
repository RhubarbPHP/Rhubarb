<?php

/**
 * Copyright (c) 2017 RhubarbPHP.
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

namespace Rhubarb\Crown\UrlHandlers;

class NumericGreedyUrlHandler extends GreedyUrlHandler
{
    /**
     * NumericGreedyUrlHandler constructor.
     * @param callable $callable
     * @param array $childUrlHandlers
     * @param bool $optional True if the extraction is optional (for handling collections for example)
     */
    public function __construct(callable $callable, $optional = false, array $childUrlHandlers = [])
    {
        $expression = ($optional) ? "([0-9]*)(/|$)" : "([0-9]+)(/|$)";

        parent::__construct($callable, $childUrlHandlers, $expression);
    }
}