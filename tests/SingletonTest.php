<?php

namespace Rhubarb\Crown\Tests;

use Rhubarb\Crown\Singleton;

class SingletonTest extends RhubarbTestCase
{
    public function testSingletonClassName()
    {
        $testSingleton = SingletonExample::getSingleton();

        $this->assertInstanceOf("Rhubarb\Crown\Tests\SingletonExample", $testSingleton);
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