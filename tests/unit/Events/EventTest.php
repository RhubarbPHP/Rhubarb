<?php

namespace Rhubarb\Crown\Tests\unit\Events;

use Rhubarb\Crown\Events\Event;
use Rhubarb\Crown\Tests\Fixtures\TestCases\RhubarbTestCase;

class EventTest extends RhubarbTestCase
{
    /**
     * @var Event
     */
    private $testEvent;

    protected function setUp()
    {
        parent::setUp();

        $this->testEvent = new Event();
    }


    public function testEventFires()
    {
        $triggered = false;

        $this->testEvent->attachHandler(function() use (&$triggered){
            $triggered = true;
        });

        $this->testEvent->raise();

        $this->assertTrue($triggered);
    }

    public function testEventTakesArguments()
    {
        $triggered = false;

        $this->testEvent->attachHandler(function($input) use (&$triggered){
            $triggered = $input;
        });

        $this->testEvent->raise("abc123");

        $this->assertEquals("abc123", $triggered);
    }

    public function testEventReturnsValues()
    {
        // Only the first of these two handlers should have their response returned.
        $this->testEvent->attachHandler(function(){
            return "def234";
        });

        $this->testEvent->attachHandler(function(){
            return "xyz";
        });

        $response = $this->testEvent->raise();

        $this->assertEquals("def234", $response);
    }

    public function testEventSupportsCallback()
    {
        $this->testEvent->attachHandler(function(){
            return "def234";
        });

        $this->testEvent->attachHandler(function(){
            return "xyz";
        });

        $response = "";

        $this->testEvent->raise(function($answer) use (&$response){
            $response .= $answer;
        });

        $this->assertEquals("def234xyz", $response);
    }
}