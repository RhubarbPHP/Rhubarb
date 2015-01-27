<?php

/*
 *	Copyright 2015 RhubarbPHP
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 */

namespace Rhubarb\Crown\Tests;

use Rhubarb\Crown\Context;

/**
 *
 * @author acuthbert
 * @copyright GCD Technologies 2012
 */
class ContextTest extends RhubarbTestCase
{
	protected $_context = null;

	protected function setUp()
	{
		$this->_context = new Context();
		$this->_context->Request = null;
	}

	protected function tearDown()
	{
		$this->_context = null;
	}

	public function testAjaxDetection()
	{
		$this->assertFalse( $this->_context->IsAjaxRequest );

		$_SERVER[ "HTTP_X_REQUESTED_WITH" ] = "xmlhttprequest";

		$this->assertTrue( $this->_context->IsAjaxRequest );
	}

	public function testCliDetection()
	{
		// not sure we can test the negative for this, as the unit tests
		// are run from the CLI and PHP's SAPI checking isn't something
		// we can hook in and modify

		$this->assertTrue( $this->_context->IsCliInvocation );
	}

	public function testJsonContentTypeDetection()
	{
		$context = new Context();
		$context->SimulateNonCli = true;

		$_SERVER[ "CONTENT_TYPE" ] = "application/json";

		$request = Context::CurrentRequest();

		$this->assertInstanceOf( "\Rhubarb\Crown\Request\JsonRequest", $request );
	}

	public function testRequestAccess()
	{
		$context = new Context();
		$context->SimulateNonCli = false;

		unset( $this->context->Request );

		$this->assertNotNull( Context::CurrentRequest(), "Static Request accessor returned NULL" );
		$this->assertNotNull( $this->_context->Request, "Request accessor returned NULL" );


		$this->assertInstanceOf( '\Rhubarb\Crown\Request\CliRequest', Context::CurrentRequest() );
	}
}
