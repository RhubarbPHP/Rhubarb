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

namespace Rhubarb\Crown\Exceptions\Handlers;

use Rhubarb\Crown\Exceptions\RhubarbException;
use Rhubarb\Crown\HttpHeaders;
use Rhubarb\Crown\Logging\Log;
use Rhubarb\Crown\Response\Response;
use Rhubarb\Crown\UrlHandlers\UrlHandler;

/**
 * The standard exception handler registered by default.
 *
 * Pushes exceptions onto the log
 * Asks the running URL handler to format the exception message.
 */
class DefaultExceptionHandler extends ExceptionHandler
{
    protected function logException(RhubarbException $er)
    {
        Log::error(
            "Unhandled " . basename(str_replace('\\','/',get_class($er))) . " `" . $er->getMessage() . "` in line " . $er->getLine() . " in " . $er->getFile(),
            "ERROR",
            ["Exception: " . $er]
        );
    }

    protected function generateResponseForException(RhubarbException $er)
    {
        $urlHandler = UrlHandler::getExecutingUrlHandler();
        if ($urlHandler != null) {
            return $urlHandler->generateResponseForException($er);
        }

        $response = new Response();
        $response->setResponseCode(HttpHeaders::HTTP_STATUS_SERVER_ERROR_GENERIC);        
        $response->setContent("Unhandled " . basename(str_replace('\\','/',get_class($er))) . " `" . $er->getMessage() . "` in line " . $er->getLine() . " in " . $er->getFile() . " (" . $er->getPrivateMessage() . ")");
        return $response;
    }

    protected function handleException(RhubarbException $er)
    {
        $this->logException($er);

        return $this->generateResponseForException($er);
    }
}
