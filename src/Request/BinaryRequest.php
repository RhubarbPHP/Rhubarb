<?php

namespace Rhubarb\Crown\Request;

use Rhubarb\Crown\Context;

class BinaryRequest extends WebRequest
{
    public function getPayload()
    {
        $context = new Context();
        $requestBody = $context->getRequestBody();

        return $requestBody;
    }
}