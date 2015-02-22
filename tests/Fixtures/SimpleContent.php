<?php

namespace Rhubarb\Crown\Tests\Fixtures;

use Rhubarb\Crown\Response\GeneratesResponse;

class SimpleContent implements GeneratesResponse
{
    public function generateResponse($request = null)
    {
        return "Don't change this content - it should match the unit test.";
    }
}