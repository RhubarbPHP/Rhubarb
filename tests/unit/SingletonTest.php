<?php

namespace Rhubarb\Crown\Tests;

use Rhubarb\Crown\Singleton;
use Rhubarb\Crown\Tests\Fixtures\TestCases\RhubarbTestCase;

class SingletonTest extends RhubarbTestCase
{
    public function testSingletonClassName()
    {
        $testSingleton = SingletonExample::getSingleton();

        $this->assertInstanceOf(SingletonExample::class, $testSingleton);
    }

    public function testSingletonIsSingleton()
    {
        $singleton1 = SingletonExample::getSingleton();
        $singleton1->attribute = "abc";

        $singleton2 = SingletonExample::getSingleton();

        $this->assertEquals($singleton2->attribute, "abc");
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