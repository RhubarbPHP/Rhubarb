<?php


namespace Rhubarb\Crown\Request;


class MultiPartFormDataRequest extends WebRequest {

    public function getPayload()
    {
        $requestBody = array_merge( $_FILES, $_POST);
        return $requestBody;
    }
}