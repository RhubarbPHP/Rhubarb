<?php

namespace Rhubarb\Crown\Tests\LoginProviders;

use Rhubarb\Crown\Context;
use Rhubarb\Crown\Module;
use Rhubarb\Crown\Request\WebRequest;
use Rhubarb\Crown\Tests\RhubarbTestCase;

/**
 *
 * @author acuthbert
 * @copyright GCD Technologies 2013
 */
class ValidateLoginUrlHandlerTest extends RhubarbTestCase
{
	public function testInvalidLoginRedirects()
	{
		$login = new UnitTestingLoginProvider();
		$login->logout();

		$_SERVER[ "SCRIPT_NAME" ] = "/cant/be/here";

		$request = new WebRequest();
		$request->initialise();

		$response = Module::generateResponseForRequest( $request );

		$this->assertInstanceOf( "\Rhubarb\Crown\Response\RedirectResponse", $response );
	}

	public function testTheLoginUrlisExcludedFromRedirect()
	{
		$_SERVER[ "SCRIPT_NAME" ] = "/defo/not/here/login/index/";

		$request = new WebRequest();
		$context = new Context();
		$context->Request = $request;

		$request->initialise();

		$response = Module::generateResponseForRequest( $request );

		$this->assertInstanceOf( "\Rhubarb\Crown\Response\HtmlResponse", $response );
	}
}