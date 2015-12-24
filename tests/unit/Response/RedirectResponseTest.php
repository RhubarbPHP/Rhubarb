<?php

namespace Rhubarb\Crown\Tests\unit\Response;

use Rhubarb\Crown\Response\RedirectResponse;
use Rhubarb\Crown\Tests\Fixtures\TestCases\RhubarbTestCase;

class RedirectResponseTest extends RhubarbTestCase
{
    public function testRedirect()
    {
        $response = new RedirectResponse("/go/to/here");

        $headers = $response->getHeaders();

        $this->assertEquals("/go/to/here", $headers["Location"]);
    }
}