<?php

namespace Rhubarb\Crown\Request;

use Rhubarb\Crown\PhpContext;

class BinaryRequest extends WebRequest
{
    public function getPayload()
    {
        $context = new PhpContext();
        $requestBody = $context->getRequestBody();

        return $requestBody;
    }
}