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

namespace Rhubarb\Crown\Request;

require_once __DIR__ . "/../Settings.php";

use Rhubarb\Crown;
use Rhubarb\Crown\PhpContext;
use Rhubarb\Crown\Exceptions\AttemptToModifyReadOnlyPropertyException;

/**
 * Encapsulates the current request.
 *
 * Takes advantage of inherited behaviour from Settings
 * to ensure that any instance of Request is working against
 * the same store of data.
 *
 * This is an abstract class.
 *
 * @method mixed env(string $property, string $defaultValue=null) Return a value from the query string optionally using a default value.
 */
abstract class Request
{
    protected $superGlobalMethodNames = ["env"];

    /**
     * Environment data captured from $_ENV
     *
     * @var string[]
     */
    protected $envData;

    /**
     * @var array
     */
    static protected $originalRequestData = null;

    /**
     * @var bool Have we copied the superglobals yet?
     */
    protected $hasInitialised = false;

    /**
     * @var PhpContext The originating PhpContext object
     */
    private $phpContext;

    /**
     * Subclasses may not override the constructor.
     */
    final public function __construct()
    {
        if (!$this->hasInitialised) {
            $this->envData = $_ENV;
            $this->initialise();

            $this->hasInitialised = true;
        }
    }

    /**
     * Get's the PHP Context that originated this request.
     *
     * @return PhpContext
     */
    protected function getOriginatingPhpContext()
    {
        return $this->phpContext;
    }

    /**
     * Creates a request object from the PHP context
     *
     * @return Request
     */
    public final static function fromPhpContext(PhpContext $phpContext)
    {
        if ($phpContext->isCliInvocation()) {
            $request = new CliRequest();
        } else {
            $contentType = (isset($_SERVER["CONTENT_TYPE"])) ? strtolower($_SERVER["CONTENT_TYPE"]) : "";

            // Special check for multipart, because the header could be multipart/form-data
            // followed by boundary
            $multipartType = "multipart/form-data";
            if (strpos($contentType, $multipartType) === 0) {
                $contentType = $multipartType;
            }

            // Firefox puts a charset on the end of the content type line
            $parts = explode(";", $contentType);
            $contentType = $parts[0];

            switch ($contentType) {
                case "application/octet-stream":
                case "image/jpeg":
                case "image/jpg":
                case "image/gif":
                case "image/png":
                    $request = new BinaryRequest();
                    break;
                case "application/json":
                    $request = new JsonRequest();
                    break;
                case "multipart/form-data":
                    $request = new MultiPartFormDataRequest();
                    break;
                default:
                    $request = new WebRequest();
                    break;
            }
        }

        $request->phpContext = $phpContext;

        return $request;
    }

    /**
     * Subclasses must implement the Initialise() method.
     *
     * That implementation is the entry point for initial
     * customisation, rather than the constructor.
     *
     * @return mixed
     */
    abstract public function initialise();

    /**
     * Magic method to handle superglobal accessors.
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, array $arguments)
    {
        if (in_array(strtolower($name), $this->superGlobalMethodNames)) {
            array_splice($arguments,0,0,[$name]);
            return call_user_func_array([$this, "getSuperglobalValue"], $arguments);
        }
    }

    /**
     * Get the value stored against one of the request superglobals.
     *
     * @param string $superglobal
     * @param mixed $index
     *
     * @return mixed
     */
    protected function getSuperglobalValue($superglobal, $index, $defaultValue = null)
    {
        $propertyName = $superglobal . 'Data';

        if (isset($this->$propertyName)){
            $data = $this->$propertyName;

            if (isset($data[$index])){
                return $data[$index];
            }
        }

        return $defaultValue;
    }

    /**
     * Return the request to its uninitialised state.
     */
    public function reset()
    {
        $this->hasInitialised = false;
        self::$originalRequestData = null;

        $this->modelData = [];
    }

    /**
     * Static method to return the request to its uninitialised state.
     */
    public static function resetRequest()
    {
        $instance = new static();
        $instance->reset();
    }

    /**
     * Will return the payload if one is available.
     *
     * @return mixed
     */
    public function getPayload()
    {
        return "";
    }
}
