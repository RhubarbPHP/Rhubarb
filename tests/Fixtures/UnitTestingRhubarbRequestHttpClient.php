<?php

/**
 * Copyright (c) 2016 RhubarbPHP.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Rhubarb\Crown\Tests\Fixtures;

use Rhubarb\Crown\Application;
use Rhubarb\Crown\PhpContext;
use Rhubarb\Crown\Http\HttpClient;
use Rhubarb\Crown\Http\HttpRequest;
use Rhubarb\Crown\Http\HttpResponse;
use Rhubarb\Crown\Module;
use Rhubarb\Crown\Request\JsonRequest;
use Rhubarb\Crown\Request\WebRequest;

class UnitTestingRhubarbRequestHttpClient extends HttpClient
{
    private static $request;
    private static $requestHistory = [];

    public static function getRequestHistory()
    {
        return self::$requestHistory;
    }

    public static function clearRequestHistory()
    {
        self::$requestHistory = [];
    }

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
        $context = new PhpContext();
        $context->simulatedRequestBody = "";

        $headers = $request->getHeaders();

        foreach ($headers as $header => $value) {
            $_SERVER["HTTP_" . strtoupper($header)] = $value;
        }

        $_SERVER["REQUEST_METHOD"] = "GET";

        switch ($request->getMethod()) {
            case "head":
                $_SERVER["REQUEST_METHOD"] = "HEAD";
                break;
            case "delete":
                $_SERVER["REQUEST_METHOD"] = "DELETE";
                break;
            case "post":
                $_SERVER["REQUEST_METHOD"] = "POST";
                $context->simulatedRequestBody = $request->getPayload();
                break;
            case "put":
                $_SERVER["REQUEST_METHOD"] = "PUT";
                $context->simulatedRequestBody = $request->getPayload();
                break;
        }

        switch ($headers["Accept"]) {
            case "application/xml":
                $simulatedRequest = new JsonRequest();
                break;
            default:
                $simulatedRequest = new WebRequest();
                break;
        }

        $simulatedRequest->uri = $request->getUrl();
        $simulatedRequest->urlPath = $request->getUrl();

        $rawResponse = Application::current()->generateResponseForRequest($simulatedRequest);

        self::$request = $request;
        self::$requestHistory[] = $request;

        $response = new HttpResponse();
        $response->setResponseBody($rawResponse->formatContent());

        return $response;
    }

    protected function getFakeResponse(HttpRequest $request)
    {
        $httpResponse = new HttpResponse();
        $httpResponse->setResponseBody(json_encode([]));

        return $httpResponse;
    }
}