<?php

namespace Rhubarb\Crown\Response;

use Rhubarb\Crown\UnitTesting\RhubarbTestCase;

/**
 *
 * @author acuthbert
 * @copyright GCD Technologies 2013
 */
class RedirectResponseTest extends RhubarbTestCase
{
    public function testRedirect()
    {
        $response = new RedirectResponse("/go/to/here");

        $headers = $response->GetHeaders();

        $this->assertEquals("/go/to/here", $headers["Location"]);
    }
}