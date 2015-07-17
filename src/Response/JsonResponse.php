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

namespace Rhubarb\Crown\Response;

use Rhubarb\Crown\Modelling\ModelState;

require_once __DIR__ . "/Response.php";

/**
 * Encapsulates a JSON response.
 *
 * The object or structure to encode should be set as the content of the request.
 *
 */
class JsonResponse extends Response
{
    public function __construct($generator = null)
    {
        parent::__construct($generator);

        $this->setHeader('Content-Type', 'application/json');
    }

    public function formatContent()
    {
        $object = $this->getContent();

        if (is_array($object) || $object instanceof ModelState || $object instanceof \stdClass) {
            $jsonString = json_encode($object, JSON_PRETTY_PRINT);

            return $jsonString;
        }

        return false;
    }
}