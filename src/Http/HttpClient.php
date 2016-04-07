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
use Rhubarb\Crown\DependencyInjection\Container;
use Rhubarb\Crown\DependencyInjection\ProviderInterface;
use Rhubarb\Crown\DependencyInjection\ProviderTrait;

/**
 * A base class to provide for HTTP clients
 */
abstract class HttpClient implements ProviderInterface
{
    use ProviderTrait;

    /**
     * Executes an HTTP transaction and returns the response.
     *
     * @param HttpRequest $request
     * @return HttpResponse
     */
    abstract public function getResponse(HttpRequest $request);

    private static $defaultSet = false;

    /**
     * @return static
     */
    public static function getProvider()
    {
        if (!self::$defaultSet){
            self::setProviderClassName(CurlHttpClient::class);
            self::$defaultSet = true;
        }

        return Container::instance(static::class);
    }

    /**
     * Returns an instance of the default HttpClient
     *
     * @return HttpClient
     */
    public static function getDefaultHttpClient()
    {
        return self::getProvider();
    }
}