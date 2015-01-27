<?php

namespace Gcd\Core\LoginProviders;

use Gcd\Core\Context;
use Gcd\Core\Request\WebRequest;
use Gcd\Core\UnitTesting\CoreTestCase;
use Gcd\Core\UnitTesting\UnitTestingLoginProvider;

/**
 *
 * @author acuthbert
 * @copyright GCD Technologies 2013
 */
class ValidateLoginUrlHandlerTest extends CoreTestCase
{
	public function testInvalidLoginRedirects()
	{
		$login = new UnitTestingLoginProvider();
		$login->Logout();

		$_SERVER[ "SCRIPT_NAME" ] = "/cant/be/here";

		$request = new WebRequest();
		$request->Initialise();

		$response = \Gcd\Core\Module::GenerateResponseForRequest( $request );

		$this->assertInstanceOf( "\Gcd\Core\Response\RedirectResponse", $response );
	}

	public function testTheLoginUrlisExcludedFromRedirect()
	{
		$_SERVER[ "SCRIPT_NAME" ] = "/defo/not/here/login/index/";

		$request = new WebRequest();
		$context = new Context();
		$context->Request = $request;

		$request->Initialise();

		$response = \Gcd\Core\Module::GenerateResponseForRequest( $request );

		$this->assertInstanceOf( "\Gcd\Core\Response\HtmlResponse", $response );
	}
}