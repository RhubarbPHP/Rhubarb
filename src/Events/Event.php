<?php

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