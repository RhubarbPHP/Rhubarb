<?php

namespace Rhubarb\Crown\Tests\Request;

use Rhubarb\Crown\Context;
use Rhubarb\Crown\Tests\RhubarbTestCase;

class JsonRequestTest extends RhubarbTestCase
{
	/**
	 * @var Context
	 */
	private $context;

	protected function setUp()
	{
		parent::setUp();

		$this->context = new Context();
		$this->context->Request = null;
		$this->context->SimulateNonCli = true;

		$_SERVER[ "CONTENT_TYPE" ] = "application/json";
	}

	public function testPayload()
	{
		$testPayload = new \stdClass();
		$testPayload->a = 1;
		$testPayload->b = 2;

		$this->context->SimulatedRequestBody = json_encode( $testPayload );

		$request = Context::currentRequest();

		$this->assertEquals( $testPayload, $request->getPayload() );
	}
}
 