<?php

namespace Rhubarb\Crown\Tests\Fixtures;

use Rhubarb\Crown\Response\GeneratesResponseInterface;

class SimpleContent implements GeneratesResponseInterface
{
    const CONTENT = "Don't change this content - it should match the unit test.";

    public function generateResponse($request = null)
    {
        return self::CONTENT;
    }
}