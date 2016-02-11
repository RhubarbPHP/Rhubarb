<?php

namespace Rhubarb\Crown\Tests\Fixtures;

use Rhubarb\Crown\Http\HttpClient;
use Rhubarb\Crown\Http\HttpRequest;
use Rhubarb\Crown\Http\HttpResponse;

class UnitTestingHttpClient extends HttpClient
{
    private static $request;

    /**
     * @return HttpRequest
     */
    public static function getLastRequest()
    {
        return self::$request;
    }

    /**
     * Executes an HTTP transaction and returns the response.
     *
     * @param HttpRequest $request
     * @return HttpResponse
     */
    public function getResponse(HttpRequest $request)
    {
        self::$request = $request;
        return $this->getFakeResponse($request);
    }

    protected function getFakeResponse(HttpRequest $request)
    {
        $httpResponse = new HttpResponse();
        $httpResponse->setResponseBody(json_encode([]));

        return $httpResponse;
    }
}
