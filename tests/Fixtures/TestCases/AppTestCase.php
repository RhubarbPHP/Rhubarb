<?php

namespace Rhubarb\Crown\Tests\Fixtures\TestCases;

/**
 * This test case class should be used for unit testing site specific code.
 */
use Rhubarb\Crown\PhpContext;
use Rhubarb\Crown\Http\HttpClient;
use Rhubarb\Crown\Tests\Fixtures\UnitTestingHttpClient;

class AppTestCase extends \Codeception\TestCase\Test
{
    protected function setUp()
    {
        parent::setUp();

        $context = new PhpContext();
        $context->UnitTesting = true;

        HttpClient::setDefaultHttpClientClassName(UnitTestingHttpClient::class);
    }
}
