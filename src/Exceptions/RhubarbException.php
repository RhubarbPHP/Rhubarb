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

namespace Rhubarb\Crown\Exceptions;

class RhubarbException extends \Exception
{
    protected $publicMessage = "Sorry, something went wrong and we couldn't complete your request. The developers have
been notified.";

    public function __construct($privateMessage = "", \Exception $previous = null)
    {
        parent::__construct($privateMessage, 0, $previous);
    }

    /**
     * Returns the public message attached to this exception.
     *
     * @return string
     */
    public function getPublicMessage()
    {
        return $this->publicMessage;
    }

    /**
     * Returns the private message attached to this exception.
     *
     * @return string
     */
    public function getPrivateMessage()
    {
        return $this->getMessage();
    }
}
