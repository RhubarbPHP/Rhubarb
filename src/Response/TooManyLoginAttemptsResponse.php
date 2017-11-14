<?php


namespace Rhubarb\Crown\Response;


class TooManyLoginAttemptsResponse extends Response
{
    public function __construct($realm, $generator = null)
    {
        parent::__construct($generator);

        $this->setHeader("WWW-authenticate", "Basic realm=\"" . $realm . "\"");

        $this->setResponseCode(Response::HTTP_STATUS_CLIENT_ERROR_UNAUTHORIZED);
        $this->setResponseMessage("401 Unauthorized");

        $this->setContent("Sorry, your account has been disabled due too many failed login attempts.");
    }
}
