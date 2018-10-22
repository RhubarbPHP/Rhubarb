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

use Rhubarb\Crown\Response\GeneratesResponseInterface;
use Rhubarb\Crown\Response\Response;

class CallableUrlHandler extends UrlHandler
{
    /**
     * @var callable
     */
    protected $callable;

    /**
     * @param callable $callable A callable that returns a GeneratesResponseInterface object
     * @param array $childUrlHandlers
     */
    public function __construct(callable $callable, array $childUrlHandlers = [])
    {
        parent::__construct($childUrlHandlers);

        $this->callable = $callable;
    }

    /**
     * Return the response if appropriate or false if no response could be generated.
     *
     * @param mixed $request
     * @return bool|Response
     */
    protected function generateResponseForRequest($request = null)
    {
        $generator = $this->createGenerator();

        if ($generator instanceof Response){
            return $generator;
        } else {
            return $generator->generateResponse($request);
        }
    }

    /**
     * @return GeneratesResponseInterface
     */
    protected function createGenerator()
    {
        $callable = $this->callable;
        $parent = $this->getParentHandler();

        if ($parent){
            $object = $callable($parent);
        } else {
            $object = $callable();
        }

        return $object;
    }
}