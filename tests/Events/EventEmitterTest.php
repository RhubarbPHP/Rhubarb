<?php

namespace Gcd\Tests;

/**
 *
 * @author acuthbert
 * @copyright GCD Technologies 2012
 */
class EventEmitterTest extends \PHPUnit_Framework_TestCase
{
	public function testEventsCanBeEmitted()
	{
		$emitter = new TestEventEmitter();

		$emitter->AttachEventHandler( "SomeEvent", array( $this, "OnSomeEvent" ) );
		$emitter->RaiseSomeEvent();

		$this->assertTrue( $this->eventWasRaised );
	}

	private static $callbackTriggered = false;

	public function testEventsCallCallback()
	{
		self::$callbackTriggered = false;

		$emitter = new TestEventEmitter();
		$emitter->AttachEventHandler( "CallBackEvent", function()
		{
			return true;
		});

		$emitter->RaiseCallbackEvent( function( $response )
		{
			self::$callbackTriggered = $response;
		});

		$this->assertTrue( self::$callbackTriggered );
	}

	public function testEventReturnsResponse()
	{
		$emitter = new TestEventEmitter();
		$emitter->AttachEventHandler( "SomeEvent", function()
		{
			return "abc123";
		});

		$response = $emitter->RaiseSomeEvent();

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
		$emitter->AttachEventHandler( "CallBackEvent", function()
		{
			return true;
		});

		$this->assertTrue( $emitter->HasAttachedEventHandlers() );
	}

	public function testEventsCanBeReplaced()
	{
		$a = false;
		$b = false;

		$emitter = new TestEventEmitter();
		$emitter->AttachEventHandler( "SomeEvent", function() use (&$a)
		{
			$a = true;
		});

		$emitter->ReplaceEventHandler( "SomeEvent", function() use (&$b)
		{
			$b = true;
		});

		$emitter->RaiseSomeEvent();

		$this->assertFalse( $a );
		$this->assertTrue( $b );
	}

}

class TestEventEmitter
{
	use \Rhubarb\Crown\Events\EventEmitter;

	public function RaiseSomeEvent()
	{
		return $this->RaiseEvent( "SomeEvent", "a", "b", "c" );
	}

	public function RaiseCallbackEvent( $callback )
	{
		return $this->RaiseEvent( "CallBackEvent", $callback );
	}
}