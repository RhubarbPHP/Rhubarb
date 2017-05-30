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

namespace Rhubarb\Crown\Exceptions\Handlers;

use ErrorException;
use Rhubarb\Crown\Exceptions\ErrorWithTraceException;
use Rhubarb\Crown\Exceptions\NonRhubarbException;
use Rhubarb\Crown\Exceptions\RhubarbException;
use Rhubarb\Crown\Response\Response;

/**
 * The base ExceptionHandler class
 */
abstract class ExceptionHandler
{
    private static $exceptionTrappingOn = false;

    /**
     * Should be overriden by extends of this base class to do the actual processing with the exception
     *
     * @param RhubarbException $er
     * @return Response
     */
    abstract protected function handleException(RhubarbException $er);

    /**
     * @var string
     */
    private static $exceptionHandlerClassName = '\Rhubarb\Crown\Exceptions\Handlers\DefaultExceptionHandler';

    public static function disableExceptionTrapping()
    {
        self::$exceptionTrappingOn = false;

        ini_set("display_errors", true);

        restore_error_handler();
        restore_exception_handler();
    }

    public static function enableExceptionTrapping()
    {
        self::$exceptionTrappingOn = true;

        ini_set("display_errors", false);

        // Register the exception handler. Not that this is only to catch exceptions that happen
        // outside of the normal Module response generation pipeline which should be very rare.
        set_exception_handler($exceptionHandler = function ($er) {
            // Upscale non core exceptions to RhubarbExceptions (via NonRhubarbException)
            if (!($er instanceof RhubarbException)) {
                $er = new NonRhubarbException($er);
            }

            // Dispatch the exception to the handler.
            $response = self::processException($er);
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

            ob_start();
            debug_print_backtrace();
            throw new ErrorWithTraceException($message, 0, $code, $file, $line, ob_get_clean());
        });

        // Make sure we handle fatal errors too.
        register_shutdown_function(function () use ($exceptionHandler) {
            if (!self::$exceptionTrappingOn) {
                return;
            }

            $error = error_get_last();

            if ($error != null) {
                // Ensure we're in the project root directory, as some webservers (e.g. apache) can change
                // the working directory during shutdown.
                chdir(__DIR__ . '/../../../../../../');

                if (!file_exists("shutdown_logs")) {
                    @mkdir("shutdown_logs");
                }

                @file_put_contents(
                    'shutdown_logs/' . date("Y-m-d_H-i-s") . '.txt',
                    "Type: {$error["type"]}\n" .
                    "Message: {$error["message"]}\n" .
                    "File: {$error["file"]}\n" .
                    "Line: {$error["line"]}\n\n",
                    FILE_APPEND
                );
            }

            if ($error != null && ($error["type"] == E_ERROR || $error["type"] == E_COMPILE_ERROR)) {
                ob_start();
                debug_print_backtrace();
                $exceptionHandler(new ErrorWithTraceException(
                    $error["message"],
                    0,
                    $error["type"],
                    $error["file"],
                    $error["line"],
                    ob_get_clean()
                ));
            }
        });
    }

    /**
     * Sets the name of the exception handler class to use when exceptions are raised.
     *
     * @param $exceptionHandlerClassName
     */
    public static function setExceptionHandlerClassName($exceptionHandlerClassName)
    {
        self::$exceptionHandlerClassName = $exceptionHandlerClassName;
    }

    /**
     * @return ExceptionHandler
     */
    protected static function getExceptionHandler()
    {
        $class = self::$exceptionHandlerClassName;
        $handler = new $class();

        return $handler;
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
        return self::$exceptionTrappingOn;
    }

    /**
     * Passes an exception object to the currently registered exception handler.
     *
     * @param RhubarbException $er
     * @return Response
     * @throws RhubarbException
     */
    final public static function processException(RhubarbException $er)
    {
        $exceptionHandler = self::getExceptionHandler();

        if ($exceptionHandler->shouldTrapException($er)) {
            return $exceptionHandler->handleException($er);
        } else {
            // If exception trapping is disabled we should just rethrow the exception.
            throw $er;
        }
    }
}
