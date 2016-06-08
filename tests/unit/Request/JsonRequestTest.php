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

use Rhubarb\Crown\PhpContext;
use Rhubarb\Crown\Tests\Fixtures\TestCases\RhubarbTestCase;

class JsonRequestTest extends RhubarbTestCase
{
    /**
     * @var PhpContext
     */
    private $context;

    protected function setUp()
    {
        parent::setUp();

        $this->context = $this->application->context();
        $this->context->simulateNonCli = true;

        $_SERVER["CONTENT_TYPE"] = "application/json";
    }

    public function testPayload()
    {
        $testPayload =
            [
                "a" => 1,
                "b" => 2
            ];

        $this->context->simulatedRequestBody = json_encode($testPayload);

        $request = $this->application->request();

        $this->assertEquals($testPayload, $request->getPayload());
    }
}
