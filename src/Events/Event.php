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
    /** @var callable[] */
    private $eventHandlers = [];

    /**
     * Attach an event handler
     *
     * @param callable $handler
     * @param string $key An optional unique identifier for the handler in case you wish to specifically remove it later
     */
    public function attachHandler(callable $handler, $key = null)
    {
        if ($key !== null) {
            $this->eventHandlers[$key] = $handler;
        } else {
            $this->eventHandlers[] = $handler;
        }
    }

    /**
     * Call all handlers for the event with the passed arguments.
     * If there are multiple event handlers, this will return the earliest non-null response from the handlers.
     * If you need to receive response data from multiple handlers, you can pass a callback function as the last argument.
     * The callback function will only be called for any handler which returns a value.
     *
     * @param array ...$arguments
     * @return mixed
     */
    public function raise(...$arguments)
    {
        $firstResponse = null;

        /** @var callable $callBack */
        $callBack = false;

        $count = count($arguments);
        if ($count > 0 && is_object($arguments[$count - 1]) && is_callable($arguments[$count - 1])) {
            $callBack = $arguments[$count - 1];
            $arguments = array_slice($arguments, 0, -1);
        }

        foreach ($this->eventHandlers as $handler) {
            $response = $handler(...$arguments);

            if ($response !== null) {
                if ($callBack) {
                    $callBack($response);
                }

                if ($firstResponse === null) {
                    $firstResponse = $response;
                }
            }
        }

        return $firstResponse;
    }

    /**
     * Removes all handlers from the event
     */
    public function clearHandlers()
    {
        $this->eventHandlers = [];
    }

    /**
     * Removes a specific handler based on its key (see the $key argument when calling attachHandler()
     *
     * @param string $key
     */
    public function removeHandlerWithKey($key)
    {
        unset($this->eventHandlers[$key]);
    }

    /**
     * Removes a specific handler. You must pass a reference to the same handler you originally attached.
     *
     * @param callable $handler
     */
    public function removeHandler(callable $handler)
    {
        $key = array_search($handler, $this->eventHandlers, true);

        if ($key !== false) {
            unset($this->eventHandlers[$key]);
        }
    }

    public function __invoke(...$arguments)
    {
        $this->raise(...$arguments);
    }
}
