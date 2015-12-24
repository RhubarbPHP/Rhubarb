<?php

namespace Rhubarb\Crown\Tests\Fixtures\UrlHandlers\NamespaceMappedHandlerTests;

use Rhubarb\Crown\Response\GeneratesResponse;

class ObjectA implements GeneratesResponse
{
    public function generateResponse($request = null)
    {
        return "ObjectA Response";
    }
}
