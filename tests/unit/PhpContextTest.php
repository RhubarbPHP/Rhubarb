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

use Rhubarb\Crown\PhpContext;
use Rhubarb\Crown\Request\CliRequest;
use Rhubarb\Crown\Request\JsonRequest;
use Rhubarb\Crown\Tests\Fixtures\TestCases\RhubarbTestCase;

class PhpContextTest extends RhubarbTestCase
{
    public function testAjaxDetection()
    {
        $this->assertFalse($this->application->context()->isXhrRequest());

        $_SERVER["HTTP_X_REQUESTED_WITH"] = "xmlhttprequest";

        $this->assertTrue($this->application->context()->isXhrRequest());
    }

    public function testCliDetection()
    {
        // not sure we can test the negative for this, as the unit tests
        // are run from the CLI and PHP's SAPI checking isn't something
        // we can hook in and modify

        $this->assertTrue($this->application->context()->isCliInvocation());
    }

    public function testJsonContentTypeDetection()
    {
        $this->application->context()->simulateNonCli = true;

        $_SERVER["CONTENT_TYPE"] = "application/json";

        $request = $this->application->request();

        $this->assertInstanceOf(JsonRequest::class, $request);
    }

    public function testRequestAccess()
    {
        $this->assertInstanceOf(CliRequest::class, $this->application->request());
    }
}
