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

namespace Rhubarb\Crown\UrlHandlers;

use Rhubarb\Crown\Request\Request;

/**
 * Adds recognition of one additional folder to be a collection item.
 *
 * @author      acuthbert
 * @copyright   2013 GCD Technologies Ltd.
 */
trait CollectionUrlHandling
{
    protected $isCollection = true;

    protected $resourceIdentifier = null;

    /**
     * Contains the portion of URL matched during IsMatchForRequest
     *
     * This allows sub classes to examine the remainder for further pickings.
     *
     * @var string
     */
    protected $matchedUrl = "";

    /**
     * Should be implemented to return a true or false as to whether this handler supports the given request.
     *
     * Normally this involves testing the request URI.
     *
     * @param Request $request
     * @param string $currentUrlFragment
     * @return bool
     */
    protected function getMatchingUrlFragment(Request $request, $currentUrlFragment = "")
    {
        $uri = $currentUrlFragment;

        $this->matchedUrl = $this->_url;

        if (preg_match("|^" . $this->_url . "/?([[:digit:]]+)/?|", $uri, $match)) {
            $this->resourceIdentifier = $match[1];
            $this->isCollection = false;

            $this->matchedUrl = $match[0];
        }

        return $this->matchedUrl;
    }

    /**
     * Returns true if the handler understands the request as one for a collection. False for an individual item.
     */
    protected function isCollection()
    {
        return $this->isCollection;
    }
}
