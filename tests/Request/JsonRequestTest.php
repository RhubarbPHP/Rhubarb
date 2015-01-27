<?php

namespace Gcd\Tests;

use Rhubarb\Crown\Context;
use Rhubarb\Crown\UnitTesting\RhubarbTestCase;

class JsonRequestTest extends RhubarbTestCase
{
	/**
	 * @var Context
	 */
	private $_context;

	protected function setUp()
	{
		parent::setUp();

		$this->_context = new Context();
		$this->_context->Request = null;
		$this->_context->SimulateNonCli = true;

		$_SERVER[ "CONTENT_TYPE" ] = "application/json";
	}

	public function testPayload()
	{
		$testPayload = new \stdClass();
		$testPayload->a = 1;
		$testPayload->b = 2;

		$this->_context->SimulatedRequestBody = json_encode( $testPayload );

		$request = Context::CurrentRequest();

		$this->assertEquals( $testPayload, $request->GetPayload() );
	}
}
 