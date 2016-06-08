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

namespace Rhubarb\Crown\Tests\unit\unit\Request;

use Rhubarb\Crown\Request\WebRequest;
use Rhubarb\Crown\Tests\Fixtures\TestCases\RequestTestCase;

class WebRequestSSLOffWindowsTest extends RequestTestCase
{
    /**
     * @var WebRequest
     */
    protected $request = null;

    protected function setUp()
    {
        parent::setUp();

        // inject some data for testing and cover the absence of web server-type
        // superglobals in the testing CLI context
        $_SERVER['HTTP_HOST'] = 'gcdtech.com';
        $_SERVER['SCRIPT_URI'] = 'http://gcdtech.com/foo';
        $_SERVER['SCRIPT_URL'] = '/foo';
        $_SERVER['HTTPS'] = 'off';

        $_GET = [];
        $_POST = [];
        $_FILES = [];
        $_COOKIE = [];
        $_SESSION = [];
        $_REQUEST = [];

        $this->request = new WebRequest();
    }

    protected function tearDown()
    {
        $this->request = null;

        parent::tearDown();
    }

    public function testNoSSL()
    {
        $this->assertFalse($this->request->isSSL());
    }
}
