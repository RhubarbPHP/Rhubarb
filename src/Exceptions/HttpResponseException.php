<?php

namespace Rhubarb\Crown\Exceptions;

use Rhubarb\Crown\Http\HttpResponse;

class HttpResponseException extends RhubarbException
{
    public $response;

    public function __construct($privateMessage = "", \Exception $previous = null, HttpResponse $response = null)
    {
        parent::__construct($privateMessage, $previous);
        $this->response = $response;
    }

}