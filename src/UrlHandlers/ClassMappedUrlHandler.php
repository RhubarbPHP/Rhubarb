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

namespace Rhubarb\Crown\UrlHandlers;

class ClassMappedUrlHandler extends UrlHandler
{
    private $className = "";

    public function __construct($className, $children = [])
    {
        parent::__construct($children);

        $this->className = $className;
    }

    public function setClassName($className)
    {
        $this->className = $className;
    }

    protected function createHandlingClass()
    {
        $class = $this->className;
        $object = new $class();

        return $object;
    }

    public function generateResponseForRequest($request = null)
    {
        $object = $this->createHandlingClass();

        return $object->generateResponse($request);
    }
}
