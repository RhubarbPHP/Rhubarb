<?php

namespace Rhubarb\Crown\Tests\Fixtures\UrlHandlers\NamespaceMappedHandlerTests\SubFolder;

use Rhubarb\Crown\Response\GeneratesResponseInterface;

class ObjectB implements GeneratesResponseInterface
{
    public function generateResponse($request = null)
    {
        return "ObjectB Response";
    }
}
