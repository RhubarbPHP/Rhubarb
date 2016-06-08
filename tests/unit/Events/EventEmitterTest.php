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

namespace Rhubarb\Crown\Tests\unit\Events;

use Rhubarb\Crown\Events\EventEmitter;
use Rhubarb\Crown\Tests\Fixtures\TestCases\RhubarbTestCase;

class EventEmitterTest extends RhubarbTestCase
{
    public function testEventsCanBeEmitted()
    {
        $emitter = new TestEventEmitter();

        $emitter->attachEventHandler("SomeEvent", [$this, "OnSomeEvent"]);
        $emitter->raiseSomeEvent();

        $this->assertTrue($this->eventWasRaised);
    }

    private static $callbackTriggered = false;

    public function testEventsCallCallback()
    {
        self::$callbackTriggered = false;

        $emitter = new TestEventEmitter();
        $emitter->attachEventHandler("CallBackEvent", function () {
            return true;
        });

        $emitter->raiseCallbackEvent(function ($response) {
            self::$callbackTriggered = $response;
        });

        $this->assertTrue(self::$callbackTriggered);
    }

    public function testEventReturnsResponse()
    {
        $emitter = new TestEventEmitter();
        $emitter->attachEventHandler("SomeEvent", function () {
            return "abc123";
        });

        $response = $emitter->raiseSomeEvent();

        $this->assertEquals("abc123", $response);
    }

    private $eventWasRaised = false;

    public function onSomeEvent($a, $b, $c)
    {
        $this->eventWasRaised = true;

        $this->assertEquals("a", $a);
        $this->assertEquals("b", $b);
        $this->assertEquals("c", $c);
    }

    public function testHasAttachedEventHandlers()
    {
        $emitter = new TestEventEmitter();
        $emitter->attachEventHandler("CallBackEvent", function () {
            return true;
        });

        $this->assertTrue($emitter->hasAttachedEventHandlers());
    }

    public function testEventsCanBeReplaced()
    {
        $a = false;
        $b = false;

        $emitter = new TestEventEmitter();
        $emitter->attachEventHandler("SomeEvent", function () use (&$a) {
            $a = true;
        });

        $emitter->replaceEventHandler("SomeEvent", function () use (&$b) {
            $b = true;
        });

        $emitter->raiseSomeEvent();

        $this->assertFalse($a);
        $this->assertTrue($b);
    }
}

class TestEventEmitter
{
    use EventEmitter;

    public function raiseSomeEvent()
    {
        return $this->raiseEvent("SomeEvent", "a", "b", "c");
    }

    public function raiseCallbackEvent($callback)
    {
        return $this->raiseEvent("CallBackEvent", $callback);
    }
}
