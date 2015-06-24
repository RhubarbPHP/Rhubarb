<?php

namespace Rhubarb\Crown\Tests;

/**
 * This test case class should be used for unit testing site specific code.
 */
use Rhubarb\Crown\Context;

class AppTestCase extends \PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        $context = new Context();
        $context->UnitTesting = true;
    }
}
