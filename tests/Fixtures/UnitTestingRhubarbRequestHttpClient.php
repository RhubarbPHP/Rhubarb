<?php

/*
 *	Copyright 2015 RhubarbPHP
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 */

namespace Rhubarb\Crown\Tests\Fixtures;

use Rhubarb\Crown\Context;
use Rhubarb\Crown\Http\HttpClient;
use Rhubarb\Crown\Http\HttpRequest;
use Rhubarb\Crown\Http\HttpResponse;
use Rhubarb\Crown\Module;
use Rhubarb\Crown\Request\JsonRequest;
use Rhubarb\Crown\Request\WebRequest;

class UnitTestingRhubarbRequestHttpClient extends HttpClient
{
    /**
     * Executes an HTTP transaction and returns the response.
     *
     * @param HttpRequest $request
     * @return HttpResponse
     */
    public function getResponse(HttpRequest $request)
    {
        $context = new Context();
        $context->SimulatedRequestBody = "";

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
                $context->SimulatedRequestBody = $request->getPayload();
                break;
            case "put":
                $_SERVER["REQUEST_METHOD"] = "PUT";
                $context->SimulatedRequestBody = $request->getPayload();
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

        $simulatedRequest->URI = $request->getUrl();
        $simulatedRequest->UrlPath = $request->getUrl();

        $context->Request = $simulatedRequest;

        $rawResponse = Module::generateResponseForRequest($simulatedRequest);

        $response = new HttpResponse();
        $response->setResponseBody($rawResponse->formatContent());

        return $response;
    }
}
