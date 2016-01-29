<?php

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

        $this->context = $this->application->getPhpContext();
        $this->context->simulateNonCli = true;

        $_SERVER["CONTENT_TYPE"] = "application/json";
    }

    public function testPayload()
    {
        $testPayload =
            [ "a" => 1,
              "b" => 2
            ];

        $this->context->simulatedRequestBody = json_encode($testPayload);

        $request = $this->application->currentRequest();

        $this->assertEquals($testPayload, $request->getPayload());
    }
}
