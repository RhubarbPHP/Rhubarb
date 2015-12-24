<?php

namespace Rhubarb\Crown\Tests\unit\Request;

use Rhubarb\Crown\Request\CliRequest;
use Rhubarb\Crown\Tests\Fixtures\TestCases\RequestTestCase;

class CliRequestTest extends RequestTestCase
{
    protected $request = null;

    protected function setUp()
    {
        parent::setUp();

        $this->request = new CliRequest();
    }

    protected function tearDown()
    {
        $this->request = null;
    }

    public function testIsCliInvocation()
    {
        $this->assertTrue($this->request->IsCliInvocation);

        parent::tearDown();
    }
}
