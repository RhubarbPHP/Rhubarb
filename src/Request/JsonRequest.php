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

namespace Rhubarb\Crown\Request;

use Rhubarb\Crown\Context;

/**
 * Represents a Json request
 *
 * Normally created when the Content-Type of the request is application/json
 *
 * @property boolean $ObjectsToAssocArrays If true, objects in the request will be converted to PHP associative arrays. Otherwise they will be stdClass objects.
 */
class JsonRequest extends WebRequest
{
    /**
     * Override this class to set default values for settings.
     */
    protected function initialiseDefaultValues()
    {
        parent::initialiseDefaultValues();

        $this->ObjectsToAssocArrays = true;
    }

    public function getPayload()
    {
        $context = new Context();
        $requestBody = trim($context->getRequestBody());

        return json_decode($requestBody, $this->ObjectsToAssocArrays);
    }
}