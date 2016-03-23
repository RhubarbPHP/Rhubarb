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

namespace Rhubarb\Crown\Events;

/**
 * A simple eventing implementation that should suit most needs.
 *
 * Of course be careful about memory leaks. Once you attach your event handler your object
 * will remain in memory until the emitter itself is out of scope or explicitly unset.
 */
trait EventEmitter
{
    private $eventHandlers = [];

    /**
     * Attach an event handler
     *
     * @param $event
     * @param callable $delegate
     */
    public function attachEventHandler($event, callable $delegate)
    {
        if (!isset($this->eventHandlers[$event])) {
            $this->eventHandlers[$event] = [];
        }

        $this->eventHandlers[$event][] = $delegate;
    }

    /**
     * Attach an event handler as the sole handler for this event.
     *
     * Removes all previously attached handlers.
     *
     * @param $event
     * @param callable $delegate
     */
    public function replaceEventHandler($event, callable $delegate)
    {
        if (isset($this->eventHandlers[$event])) {
            $this->eventHandlers[$event] = [];
        }

        $this->attachEventHandler($event, $delegate);
    }

    /**
     * Removes all previously attached handlers for a given event.
     *
     * @param $event
     */
    public function detachEventHandlers($event)
    {
        $this->eventHandlers[$event] = [];
    }

    /**
     * Returns true if the object has attached event handlers.
     *
     * @return bool
     */
    public function hasAttachedEventHandlers()
    {
        return (sizeof($this->eventHandlers) > 0);
    }

    /**
     * Removes all event handlers attached to this object.
     */
    protected function clearEventHandlers()
    {
        $this->eventHandlers = [];
    }

    public function hasExternallyAttachedEventHandlers()
    {
        return $this->hasAttachedEventHandlers();
    }

    /**
     * Call this function to raise an event.
     *
     * In addition to $event you can pass any number of other events which are passed through
     * to the event handling delegate.
     *
     * @param string $event The name of the event
     * @return mixed|null
     */
    protected function raiseEvent($event)
    {
        if (!isset($this->eventHandlers[$event])) {
            return null;
        }

        $args = func_get_args();
        $args = array_slice($args, 1);

        // Check if the last argument is a callback.
        $count = count($args);
        $callBack = false;

        if (($count > 0) && is_object($args[$count - 1]) && is_callable($args[$count - 1])) {
            $callBack = $args[$count - 1];
            $args = array_slice($args, 0, -1);
        }

        $result = null;

        foreach ($this->eventHandlers[$event] as $delegate) {
            $answer = call_user_func_array($delegate, $args);

            if ($callBack !== false && $answer !== null) {
                call_user_func($callBack, $answer);
            }

            // If we don't have a result yet - make this the result. This way the first event handler to
            // return a non null result will be the overall result of the event.
            if ($result === null) {
                $result = $answer;
            }
        }

        return $result;
    }
}
