<?php

namespace Rhubarb\Crown\Tests\unit\LoginProviders;

use Rhubarb\Crown\Application;
use Rhubarb\Crown\PhpContext;
use Rhubarb\Crown\Module;
use Rhubarb\Crown\Request\WebRequest;
use Rhubarb\Crown\Response\HtmlResponse;
use Rhubarb\Crown\Response\RedirectResponse;
use Rhubarb\Crown\Tests\Fixtures\LoginProviders\UnitTestingLoginProvider;
use Rhubarb\Crown\Tests\Fixtures\TestCases\RhubarbTestCase;

class ValidateLoginUrlHandlerTest extends RhubarbTestCase
{
    public function testInvalidLoginRedirects()
    {
        $login = new UnitTestingLoginProvider();
        $login->logOut();

        $_SERVER["SCRIPT_NAME"] = "/cant/be/here";

        $request = new WebRequest();
        $request->initialise();

        $response = Application::runningApplication()->generateResponseForRequest($request);

        $this->assertInstanceOf(RedirectResponse::class, $response);
    }

    public function testTheLoginUrlisExcludedFromRedirect()
    {
        $_SERVER["SCRIPT_NAME"] = "/defo/not/here/login/index/";

        $request = new WebRequest();
        $context = new PhpContext();
        $context->Request = $request;

        $request->initialise();

        $response = Application::runningApplication()->generateResponseForRequest($request);

        $this->assertInstanceOf(HtmlResponse::class, $response);
    }
}