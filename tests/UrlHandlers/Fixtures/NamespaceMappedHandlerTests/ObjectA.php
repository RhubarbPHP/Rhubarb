<?php

namespace Rhubarb\Crown\Tests\UrlHandlers\Fixtures\NamespaceMappedHandlerTests;

use Rhubarb\Crown\Response\GeneratesResponse;

class ObjectA implements GeneratesResponse
{
    public function generateResponse($request = null)
    {
        return "ObjectA Response";
    }
}
