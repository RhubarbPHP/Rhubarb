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

namespace Rhubarb\Crown\Http;

require_once __DIR__ . '/HttpClient.php';

class CurlHttpClient extends HttpClient
{
    /**
     * Executes an HTTP transaction and returns the response.
     *
     * @param HttpRequest $request
     * @return HttpResponse
     */
    public function getResponse(HttpRequest $request)
    {
        $uri = $request->getUrl();

        $headers = $request->getHeaders();
        $flatHeaders = [];

        foreach ($headers as $key => $value) {
            $flatHeaders[] = $key . ": " . $value;
        }

        $flatHeaders[] = 'Connection: Keep-Alive';
        $flatHeaders[] = 'Expect:';
        $flatHeaders[] = 'Accept-Language: en-GB';
        $flatHeaders[] = 'Cache-Control: no-cache';
        $flatHeaders[] = 'User-Agent: Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0)';

        $curl = curl_init($uri);

        curl_setopt($curl, CURLOPT_HEADER, false);

        $payload = $request->getPayload();

        switch ($request->getMethod()) {
            case "head":
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "HEAD");
                break;
            case "delete":
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
                break;
            case "post":
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
                break;
            case "put":
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);

                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                break;
        }

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $flatHeaders);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);

        $responseCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        $httpResponse = new HttpResponse();
        $httpResponse->setResponseBody($response);
        $httpResponse->setResponseCode($responseCode);

        return $httpResponse;
    }
}
