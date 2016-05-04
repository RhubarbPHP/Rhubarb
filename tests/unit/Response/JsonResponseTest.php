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

namespace Rhubarb\Crown\Response;

use Rhubarb\Crown\Tests\Fixtures\TestCases\RhubarbTestCase;

class JsonResponseTest extends RhubarbTestCase
{
    public function testResponseIsJsonEncoded()
    {
        $response = new JsonResponse();
        $test = new \stdClass();
        $test->Forename = "abc";
        $test->Surname = "123";
        $response->setContent($test);

        ob_start();
        $response->send();
        $buffer = ob_get_clean();

        $this->assertEquals('{"Forename":"' . $test->Forename . '","Surname":"123"}', $buffer);
    }

    public function testResponseCanCodeNonModels()
    {
        $response = new JsonResponse();
        $test = ["abc", "123"];
        $response->setContent($test);

        ob_start();
        $response->send();
        $buffer = ob_get_clean();

        $this->assertEquals('["abc","123"]', $buffer);

        $response = new JsonResponse();
        $test = new \stdClass();
        $test->abc = "123";

        $response->setContent($test);

        ob_start();
        $response->send();
        $buffer = ob_get_clean();

        $this->assertEquals('{"abc":"123"}', $buffer);
    }
}
