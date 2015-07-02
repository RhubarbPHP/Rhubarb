<?php


namespace Rhubarb\Crown\Request;


use Rhubarb\Crown\Context;

class MultiformDataRequest extends WebRequest {

    public function getPayload()
    {
        $context = new Context();
        $requestBody = $context->getRequestBody();

        // merge between POST and File values

        return $requestBody;
    }
}