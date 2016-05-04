<?php
/**
 * Copyright (c) 2016 RhubarbPHP.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

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
        $login = UnitTestingLoginProvider::singleton();
        $login->logOut();

        $_SERVER["SCRIPT_NAME"] = "/cant/be/here";

        $request = new WebRequest();
        $request->initialise();

        $response = Application::current()->generateResponseForRequest($request);

        $this->assertInstanceOf(RedirectResponse::class, $response);
    }

    public function testTheLoginUrlisExcludedFromRedirect()
    {
        $_SERVER["SCRIPT_NAME"] = "/defo/not/here/login/index/";

        $request = new WebRequest();
        $context = new PhpContext();
        $context->Request = $request;

        $request->initialise();

        $response = Application::current()->generateResponseForRequest($request);

        $this->assertInstanceOf(HtmlResponse::class, $response);
    }
}