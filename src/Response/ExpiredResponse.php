<?php


namespace Rhubarb\Crown\Response;


class ExpiredResponse extends Response
{
    public function __construct($realm, $generator = null)
    {
        parent::__construct($generator);

        $this->setHeader("WWW-authenticate", "Basic realm=\"" . $realm . "\"");

        $this->setResponseCode(Response::HTTP_STATUS_CLIENT_ERROR_FORBIDDEN);
        $this->setResponseMessage("403 Forbidden");

        $this->setContent("Sorry, your account has now expired.");
    }
}
