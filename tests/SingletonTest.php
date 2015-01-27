<?php

use \Rhubarb\Crown\Singleton;

/**
 * @package Core
 */

/**
 * Test of Singleton
 *
 * @see Singleton
 * @package Core
 */
class SingletonTest extends PHPUnit_Framework_TestCase
{
	public function testSingletonClassName()
	{
		$testSingleton = SingletonExample::GetSingleton();

		$this->assertInstanceOf( "SingletonExample", $testSingleton );
	}

	public function testSingletonIsSingleton()
	{
		$singleton1 = SingletonExample::GetSingleton();
		$singleton1->attribute = "abc";

		$singleton2 = SingletonExample::GetSingleton();

		$this->assertEquals( $singleton2->attribute, "abc" );
	}
}

/**
 * Example of singleton for Singleton tests
 *
 * @see \SingletonTest
 * @package Core
 */
class SingletonExample extends Singleton
{
	public $attribute;
}