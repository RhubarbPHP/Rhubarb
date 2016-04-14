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

/**
 * Throw this exception at any point to immediately stop execution of the response generation.
 *
 * This should be used instead of exit; as it allows headers to be correctly outputted and doesn't abort
 * PHP so abruptly.
 */
class StopGeneratingResponseException extends \Exception
{
    public function __construct()
    {
        parent::__construct();
    }
}
