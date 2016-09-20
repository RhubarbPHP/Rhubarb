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

class WebRequestTest extends RequestTestCase
{
    /** @var Webrequest */
    protected $request;

    protected function setUp()
    {
        parent::setUp();

        // inject some data for testing and cover the absence of web server-type
        // superglobals in the testing CLI context
        $_SERVER['HTTP_HOST'] = 'gcdtech.com';
        $_SERVER['SCRIPT_URI'] = 'http://gcdtech.com/foo';
        $_SERVER['REQUEST_URI'] = '/foo';
        $_SERVER['SCRIPT_NAME'] = '/foo';
        $_SERVER['HTTP_authorization'] = 'test';

        $_GET = ["test" => "value"];
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

    public function testHostValue()
    {
        $this->assertEquals('gcdtech.com', $this->request->host);
    }

    public function testURIValue()
    {
        $this->assertEquals('/foo', $this->request->uri);
    }

    public function testPathValue()
    {
        $this->assertEquals('/foo', $this->request->urlPath);
    }

    public function testNoSSL()
    {
        $this->assertFalse($this->request->isSSL());
    }

    public function testCaseSensitiveHeaders()
    {
        $this->assertEquals('test', $this->request->header('Authorization'));
    }
}
