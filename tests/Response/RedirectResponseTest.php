<?php

namespace Rhubarb\Crown\Tests\Response;

use Rhubarb\Crown\Response\RedirectResponse;
use Rhubarb\Crown\Tests\RhubarbTestCase;

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

        $headers = $response->getHeaders();

        $this->assertEquals("/go/to/here", $headers["Location"]);
    }
}