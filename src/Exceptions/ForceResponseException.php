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

namespace Rhubarb\Crown\Exceptions;

use Rhubarb\Crown\Response\Response;

/**
 * Thrown when an executing system wants to force delivery of a Response
 *
 * This should only be used if it does not have an easy way to suggest to the running code that the
 * response be returned. For example deep within MVP it would be impractical to signal all the way up
 * to the MVP entry point that a redirect response needs delivered.
 *
 * @author acuthbert
 * @copyright GCD Technologies 2013
 */
class ForceResponseException extends \Exception
{
    private $_response;

    public function __construct(Response $response)
    {
        $this->_response = $response;
    }

    /**
     * Get's the response object.
     *
     * @return \Rhubarb\Crown\Response\Response
     */
    public function getResponse()
    {
        return $this->_response;
    }
}