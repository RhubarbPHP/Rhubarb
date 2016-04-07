<?php

namespace Rhubarb\Crown\Tests\Fixtures\UrlHandlers\NamespaceMappedHandlerTests\SubFolder;

use Rhubarb\Crown\Response\GeneratesResponseInterface;

class index implements GeneratesResponseInterface
{
    public function generateResponse($request = null)
    {
        return "index";
    }
}
