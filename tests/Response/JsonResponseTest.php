<?php

namespace Rhubarb\Crown\Response;

use Rhubarb\Crown\Modelling\UnitTesting\User;
use Rhubarb\Crown\UnitTesting\RhubarbTestCase;

class JsonResponseTest extends RhubarbTestCase
{
    public function testResponseIsJsonEncoded()
    {
        $response = new JsonResponse();
        $test = new User();
        $test->Forename = "abc";
        $test->Surname = "123";
        $test->Save();
        $response->SetContent($test);

        ob_start();
        $response->Send();
        $buffer = ob_get_clean();

        $this->assertEquals('{"UserID":' . $test->UserID . ',"FullName":"abc 123"}', $buffer);
    }

    public function testResponseCanCodeNonModels()
    {
        $response = new JsonResponse();
        $test = ["abc", "123"];
        $response->SetContent($test);

        ob_start();
        $response->Send();
        $buffer = ob_get_clean();

        $this->assertEquals('["abc","123"]', $buffer);

        $response = new JsonResponse();
        $test = new \stdClass();
        $test->abc = "123";

        $response->SetContent($test);

        ob_start();
        $response->Send();
        $buffer = ob_get_clean();

        $this->assertEquals('{"abc":"123"}', $buffer);
    }
}
