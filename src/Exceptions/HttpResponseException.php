<?php

namespace Rhubarb\Crown\Exceptions;

use Rhubarb\Crown\Http\HttpRequest;
use Rhubarb\Crown\Http\HttpResponse;

class HttpResponseException extends RhubarbException
{
    public $response;
    public $request;

    public function __construct($privateMessage = "", \Exception $previous = null, HttpResponse $response = null, HttpRequest $request = null)
    {
        parent::__construct($privateMessage, $previous);
        $this->response = $response;
        $this->request = $request;
    }
}
