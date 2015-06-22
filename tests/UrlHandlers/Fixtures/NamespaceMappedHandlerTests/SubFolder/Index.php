<?php

namespace Rhubarb\Crown\Tests\UrlHandlers\Fixtures\NamespaceMappedHandlerTests\SubFolder;

use Rhubarb\Crown\Response\GeneratesResponse;

class index implements GeneratesResponse
{
    public function generateResponse($request = null)
    {
        return "index";
    }
}
