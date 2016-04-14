<?php

namespace Rhubarb\Crown\Tests\unit\unit\Request;

use Rhubarb\Crown\Request\Request;
use Rhubarb\Crown\Request\WebRequest;
use Rhubarb\Crown\Tests\Fixtures\TestCases\RequestTestCase;

class RequestTest extends RequestTestCase
{
    protected $request = null;

    protected $testEnvKey = 'REQUEST_TEST';
    protected $testEnvValue = 42;

    protected function setUp()
    {
        parent::setUp();

        $_ENV[$this->testEnvKey] = 42;

        $this->request = new WebRequest();
    }

    protected function tearDown()
    {
        unset($_ENV[$this->testEnvKey]);

        parent::tearDown();
    }

    public function testGettingOfSuperGlobals()
    {
        $this->assertEquals($this->testEnvValue, $this->request->env($this->testEnvKey));
        $this->assertEquals("defaultValue", $this->request->env("default", "defaultValue"));
    }
}