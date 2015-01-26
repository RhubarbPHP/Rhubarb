<?php

namespace Rhubarb\Crown\Response;

use Rhubarb\Crown\UnitTesting\CoreTestCase;

/**
 *
 * @author acuthbert
 * @copyright GCD Technologies 2013
 */
class RedirectResponseTest extends CoreTestCase
{
    public function testRedirect()
    {
        $response = new RedirectResponse("/go/to/here");

        $headers = $response->GetHeaders();

        $this->assertEquals("/go/to/here", $headers["Location"]);
    }
}