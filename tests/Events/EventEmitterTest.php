<?php

namespace Rhubarb\Crown\Tests\Events;

use Rhubarb\Crown\Events\EventEmitter;
use Rhubarb\Crown\Tests\RhubarbTestCase;

/**
 *
 * @author acuthbert
 * @copyright GCD Technologies 2012
 */
class EventEmitterTest extends RhubarbTestCase
{
	public function testEventsCanBeEmitted()
	{
		$emitter = new TestEventEmitter();

		$emitter->attachEventHandler( "SomeEvent", array( $this, "OnSomeEvent" ) );
		$emitter->raiseSomeEvent();

		$this->assertTrue( $this->eventWasRaised );
	}

	private static $callbackTriggered = false;

	public function testEventsCallCallback()
	{
		self::$callbackTriggered = false;

		$emitter = new TestEventEmitter();
		$emitter->attachEventHandler( "CallBackEvent", function()
		{
			return true;
		});

		$emitter->raiseCallbackEvent( function( $response )
		{
			self::$callbackTriggered = $response;
		});

		$this->assertTrue( self::$callbackTriggered );
	}

	public function testEventReturnsResponse()
	{
		$emitter = new TestEventEmitter();
		$emitter->attachEventHandler( "SomeEvent", function()
		{
			return "abc123";
		});

		$response = $emitter->raiseSomeEvent();

		$this->assertEquals( "abc123", $response );
	}

	private $eventWasRaised = false;

	public function OnSomeEvent( $a, $b, $c )
	{
		$this->eventWasRaised = true;

		$this->assertEquals( "a", $a );
		$this->assertEquals( "b", $b );
		$this->assertEquals( "c", $c );
	}

	public function testHasAttachedEventHandlers()
	{
		$emitter = new TestEventEmitter();
		$emitter->attachEventHandler( "CallBackEvent", function()
		{
			return true;
		});

		$this->assertTrue( $emitter->hasAttachedEventHandlers() );
	}

	public function testEventsCanBeReplaced()
	{
		$a = false;
		$b = false;

		$emitter = new TestEventEmitter();
		$emitter->attachEventHandler( "SomeEvent", function() use (&$a)
		{
			$a = true;
		});

		$emitter->replaceEventHandler( "SomeEvent", function() use (&$b)
		{
			$b = true;
		});

		$emitter->raiseSomeEvent();

		$this->assertFalse( $a );
		$this->assertTrue( $b );
	}
}

class TestEventEmitter
{
	use EventEmitter;

	public function raiseSomeEvent()
	{
		return $this->raiseEvent( "SomeEvent", "a", "b", "c" );
	}

	public function raiseCallbackEvent( $callback )
	{
		return $this->raiseEvent( "CallBackEvent", $callback );
	}
}