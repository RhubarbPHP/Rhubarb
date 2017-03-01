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

use Rhubarb\Crown\Request\Request;
use Rhubarb\Crown\Response\GeneratesResponseInterface;

/**
 * A form of CallableUrlHandler that gobbles the next portion of the URL path.
 *
 * By default this includes everything up to the next slash or end of path but this
 * can be changed by passing a custom regular expression.
 *
 * Each part matched is passed to the callable as arguments in the order of extraction.
 *
 * Note the expression must match or the url handler is considered not to be match at all.
 *
 * @package Rhubarb\Crown\UrlHandlers
 */
class GreedyUrlHandler extends CallableUrlHandler
{
    /**
     * @var string
     */
    protected $regularExpression = "([^/]+)(/|$)";

    public function __construct(callable $callable, array $childUrlHandlers = [], $regularExpression = "")
    {
        parent::__construct($callable, $childUrlHandlers);

        if ($regularExpression) {
            $this->regularExpression = $regularExpression;
        }
    }

    /**
     * The extract arguments from the URL
     * @var array
     */
    private $extractedArguments = [];

    /**
     * True if a match was found, false if not.
     *
     * @var bool
     */
    private $matched = false;

    /**
     * @return GeneratesResponseInterface
     */
    protected function createGenerator()
    {
        $callable = $this->callable;

        /**
         * @var GeneratesResponseInterface $generator
         */
        $generator = $callable(...$this->extractedArguments);
        return $generator;
    }

    protected function generateResponseForRequest($request = null)
    {
        if (!$this->matched){
            return false;
        }

        return parent::generateResponseForRequest($request);
    }

    protected function getMatchingUrlFragment(Request $request, $currentUrlFragment = "")
    {
        $uri = $currentUrlFragment;

        $this->matchedUrl = $this->url;

        if (preg_match("`^" . $this->url . "/?".$this->regularExpression."`", $uri, $match)) {
            $this->matchedUrl = $match[0];
            array_splice($match, 0, 1);
            $this->extractedArguments = $match;
            $this->matched = true;
        } else {
            $this->matched = false;
        }

        return $this->matchedUrl;
    }
}