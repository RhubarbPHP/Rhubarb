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

use ErrorException;
use Rhubarb\Crown\DependencyInjection\Container;
use Rhubarb\Crown\DependencyInjection\ProviderInterface;
use Rhubarb\Crown\DependencyInjection\SingletonProviderTrait;
use Rhubarb\Crown\Exceptions\NonRhubarbException;
use Rhubarb\Crown\Exceptions\RhubarbException;
use Rhubarb\Crown\Response\Response;

/**
 * The base ExceptionHandler class
 */
abstract class ExceptionHandler implements ProviderInterface
{
    use SingletonProviderTrait;

    /**
     * Should be overriden by extends of this base class to do the actual processing with the exception
     *
     * @param RhubarbException $er
     * @return Response
     */
    abstract protected function handleException(RhubarbException $er);

    private static $defaultSet = false;

    /**
     * @return static
     */
    public static function getProvider()
    {
        if (!self::$defaultSet) {
            self::setProviderClassName(DefaultExceptionHandler::class);
            self::$defaultSet = true;
        }
        return Container::instance(static::class);
    }

    public static function disableExceptionTrapping()
    {
        /**
         * @var ExceptionSettings $exceptionSettings
         */
        $exceptionSettings = ExceptionSettings::singleton();
        $exceptionSettings->exceptionTrappingOn = false;

        ini_set("display_errors", true);

        restore_error_handler();
        restore_exception_handler();
    }

    public static function enableExceptionTrapping()
    {
        /**
         * @var ExceptionSettings $exceptionSettings
         */
        $exceptionSettings = ExceptionSettings::singleton();
        $exceptionSettings->exceptionTrappingOn = true;

        ini_set("display_errors", false);

        // Register the exception handler. Not that this is only to catch exceptions that happen
        // outside of the normal Module response generation pipeline which should be very rare.
        set_exception_handler($exceptionHandler = function ($er) {
            // Upscale non core exceptions to RhubarbExceptions (via NonRhubarbException)
            if (!($er instanceof RhubarbException)) {
                $er = new NonRhubarbException($er);
            }

            // Dispatch the exception to the handler.
            /**
             * @var ExceptionHandler $handler
             */
            $handler = Container::instance(ExceptionHandler::class);
            $response = $handler->processException($er);
            $response->send();
        });

        // Register the default php error handler to convert errors to exceptions.
        set_error_handler(function ($code, $message, $file, $line) {
            $reportingLevel = error_reporting();
            // If errors are turned off or they are being suppressed with an '@' we don't do anything with
            // them. This is to allow for optimistic auto loading in the Module class.
            if ($reportingLevel == 0) {
                return;
            }

            $fatalErrors = [E_ERROR, E_RECOVERABLE_ERROR];

            if (!in_array($code, $fatalErrors)) {
                return;
            }

            throw new ErrorException($message, 0, $code, $file, $line);
        });

        // Make sure we handle fatal errors too.
        register_shutdown_function(function () use ($exceptionHandler) {
            /**
             * @var ExceptionSettings $exceptionSettings
             */
            $exceptionSettings = ExceptionSettings::singleton();
            if ($exceptionSettings->exceptionTrappingOn) {
                $error = error_get_last();

                if ($error != null) {
                    // Ensure we're in the project root directory, as some webservers (e.g. apache) can change
                    // the working directory during shutdown.
                    chdir(__DIR__.'/../../../../../../');

                    if (!file_exists("shutdown_logs")) {
                        @mkdir("shutdown_logs");
                    }

                    @file_put_contents(
                        'shutdown_logs/' . date("Y-m-d_H-i-s") . '.txt',
                        "Type: {$error["type"]}\n".
                        "Message: {$error["message"]}\n".
                        "File: {$error["file"]}\n".
                        "Line: {$error["line"]}\n\n",
                        FILE_APPEND
                    );
                }

                if ($error != null && ($error["type"] == E_ERROR || $error["type"] == E_COMPILE_ERROR)) {
                    $exceptionHandler(new ErrorException(
                        $error["message"],
                        0,
                        $error["type"],
                        $error["file"],
                        $error["line"]
                    ));
                }
            }
        });
    }

    /**
     * Returns true if the handler should handle the exception.
     *
     * Normally this is controlled by enableExceptionTrapping() and disableExceptionTrapping() but this
     * can be modified by an extending class.
     *
     * @param RhubarbException $er
     * @return bool
     */
    protected function shouldTrapException(RhubarbException $er)
    {
        /**
         * @var ExceptionSettings $exceptionSettings
         */
        $exceptionSettings = ExceptionSettings::singleton();
        return $exceptionSettings->exceptionTrappingOn;
    }

    /**
     * Passes an exception object to the currently registered exception handler.
     *
     * @param RhubarbException $er
     * @return Response
     * @throws RhubarbException
     */
    final public function processException(RhubarbException $er)
    {
        if ($this->shouldTrapException($er)) {
            return $this->handleException($er);
        } else {
            // If exception trapping is disabled we should just rethrow the exception.
            throw $er;
        }
    }
}
