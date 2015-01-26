<?php

namespace Gcd\Tests;

/**
 * @author marramgrass
 * @copyright GCD Technologies 2012
 */
class CliRequestTest extends \Gcd\Core\Request\UnitTesting\RequestTestCase
{
	protected $_request = null;

	protected function setUp()
	{
		parent::setUp();

		$this->_request = new \Gcd\Core\Request\CliRequest();
	}

	protected function tearDown()
	{
		$this->_request = null;
	}

	public function testIsCliInvocation()
	{
		$this->assertTrue( $this->_request->IsCliInvocation );

		parent::tearDown();
	}
}
