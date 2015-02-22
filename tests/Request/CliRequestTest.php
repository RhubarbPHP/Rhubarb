<?php

namespace Rhubarb\Crown\Tests\Request;

use Rhubarb\Crown\Request\CliRequest;

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
		$this->assertTrue( $this->request->IsCliInvocation );

		parent::tearDown();
	}
}
