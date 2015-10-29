<?php

namespace Rhubarb\Crown\Tests;

/**
 * This test case class should be used for unit testing site specific code.
 */
use Rhubarb\Crown\Context;
use Rhubarb\Crown\Http\HttpClient;
use Rhubarb\Crown\Tests\Fixtures\UnitTestingHttpClient;

class AppTestCase extends \Codeception\TestCase\Test
{
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        $context = new Context();
        $context->UnitTesting = true;

        HttpClient::setDefaultHttpClientClassName(UnitTestingHttpClient::class);
    }
}
