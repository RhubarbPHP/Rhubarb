<?php

namespace Rhubarb\Crown\LoginProviders;

use Rhubarb\Crown\Context;
use Rhubarb\Crown\Request\WebRequest;
use Rhubarb\Crown\UnitTesting\RhubarbTestCase;
use Rhubarb\Crown\UnitTesting\UnitTestingLoginProvider;

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
		$login->Logout();

		$_SERVER[ "SCRIPT_NAME" ] = "/cant/be/here";

		$request = new WebRequest();
		$request->Initialise();

		$response = \Rhubarb\Crown\Module::GenerateResponseForRequest( $request );

		$this->assertInstanceOf( "\Rhubarb\Crown\Response\RedirectResponse", $response );
	}

	public function testTheLoginUrlisExcludedFromRedirect()
	{
		$_SERVER[ "SCRIPT_NAME" ] = "/defo/not/here/login/index/";

		$request = new WebRequest();
		$context = new Context();
		$context->Request = $request;

		$request->Initialise();

		$response = \Rhubarb\Crown\Module::GenerateResponseForRequest( $request );

		$this->assertInstanceOf( "\Rhubarb\Crown\Response\HtmlResponse", $response );
	}
}