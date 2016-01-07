<?php

namespace Rhubarb\Crown\Tests\Fixtures\UrlHandlers;

use Rhubarb\Crown\UrlHandlers\UrlHandler;

class UnitTestComputedUrlHandler extends UrlHandler
{
    public function getDefaultUrl()
    {
        return "/computed-url/test/";
    }

    /**
     * Return the response if appropriate or false if no response could be generated.
     *
     * @param mixed $request
     * @return bool
     */
    protected function generateResponseForRequest($request = null)
    {
        return "Computed URL Response";
    }
}