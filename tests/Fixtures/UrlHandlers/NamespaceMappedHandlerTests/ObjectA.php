<?php

namespace Rhubarb\Crown\Tests\Fixtures\UrlHandlers\NamespaceMappedHandlerTests;

use Rhubarb\Crown\Response\GeneratesResponseInterface;

class ObjectA implements GeneratesResponseInterface
{
    public function generateResponse($request = null)
    {
        return "ObjectA Response";
    }
}
