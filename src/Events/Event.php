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

namespace Rhubarb\Crown\Events;

class Event
{
    private $eventHandlers = [];

    /**
     * Attach an event handler
     *
     * @param callable $delegate
     */
    public function attachHandler(callable $delegate)
    {
        $this->eventHandlers[] = $delegate;
    }

    public function raise(...$arguments)
    {
        $firstResponse = null;

        $callBack = false;

        $count = count($arguments);
        if (($count > 0) && is_object($arguments[$count - 1]) && is_callable($arguments[$count - 1])) {
            $callBack = $arguments[$count - 1];
            $arguments = array_slice($arguments, 0, -1);
        }

        foreach($this->eventHandlers as $handler){
            $response = $handler(...$arguments);

            if ($response !== null) {
                if ($callBack){
                    $callBack($response);
                }

                if ($firstResponse === null) {
                    $firstResponse = $response;
                }
            }
        }

        return $firstResponse;
    }
}