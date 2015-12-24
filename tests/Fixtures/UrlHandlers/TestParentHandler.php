<?php

namespace Rhubarb\Crown\Tests\Fixtures\UrlHandlers;

use Rhubarb\Crown\Request\Request;
use Rhubarb\Crown\Response\HtmlResponse;
use Rhubarb\Crown\UrlHandlers\UrlHandler;

class TestParentHandler extends UrlHandler
{
    public $stub = "/";

    /**
     * Return the response when appropriate or false if no response could be generated.
     *
     * @param mixed $request
     * @param string $currentUrlFragment
     * @return bool
     */
    protected function generateResponseForRequest($request = null, $currentUrlFragment = "")
    {
        $response = new HtmlResponse();
        $response->setContent("parent");

        return $response;
    }

    /**
     * Should be implemented to return a true or false as to whether this handler supports the given request.
     *
     * Normally this involves testing the request URI.
     *
     * @param \Rhubarb\Crown\Request\Request $request
     * @param string $currentUrlFragment
     * @return bool
     */
    protected function getMatchingUrlFragment(Request $request, $currentUrlFragment = "")
    {
        return (stripos($currentUrlFragment, $this->stub) === 0);
    }
}