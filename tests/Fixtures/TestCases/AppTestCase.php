<?php

namespace Rhubarb\Crown\Tests\Fixtures\TestCases;

/**
 * This test case class should be used for unit testing site specific code.
 */
use Rhubarb\Crown\Context;
use Rhubarb\Crown\Http\HttpClient;
use Rhubarb\Crown\Tests\Fixtures\UnitTestingRhubarbRequestHttpClient;

class AppTestCase extends \Codeception\TestCase\Test
{
    protected function setUp()
    {
        parent::setUp();

        $context = new Context();
        $context->UnitTesting = true;

        HttpClient::setDefaultHttpClientClassName(UnitTestingRhubarbRequestHttpClient::class);
    }
}
