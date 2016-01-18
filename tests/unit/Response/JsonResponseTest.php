<?php

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
