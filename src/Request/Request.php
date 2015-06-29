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
 * @method mixed env(string $name, mixed $value = null) access an environment value
 * @method mixed server(string $name, mixed $value = null) access a server value
 * @method mixed get(string $name, mixed $value = null) access a GET value
 * @method mixed post(string $name, mixed $value = null) access a POST value
 * @method mixed files(string $name, mixed $value = null) access details of a posted file
 * @method mixed cookie(string $name, mixed $value = null) access a cookie value
 * @method mixed session(string $name, mixed $value = null) access a session value
 * @method mixed request(string $name, mixed $value = null) access a request value (get/post/cookie)
 * @method mixed header(string $name, mixed $value = null) access a header value
 */
abstract class Request extends Crown\Settings
{
    /**
     * @var array
     */
    static protected $originalRequestData = null;

    /**
     * @var bool Have we copied the superglobals yet?
     */
    protected $hasInitialised = false;

    /**
     * Subclasses may not override the constructor.
     */
    final public function __construct()
    {
        parent::__construct();

        if (!$this->hasInitialised) {
            $this->modelData['EnvData'] = $_ENV;
            $this->initialise();

            $this->hasInitialised = true;
        }

        // Stash the initial state of the request for reference in
        // case this instance gets modified
        if (self::$originalRequestData === null) {
            self::$originalRequestData = $this->modelData;
        }
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
     * Magical getter, taking account of Original request data.
     *
     * @param mixed $propertyName
     *
     * @return mixed
     */
    public function __get($propertyName)
    {
        if (substr($propertyName, 0, 8) === 'Original') {
            if (method_exists($this, "get" . $propertyName)) {
                return call_user_func([$this, "get" . $propertyName]);
            }

            $originalPropertyName = substr($propertyName, 8);

            if (isset(self::$originalRequestData[$originalPropertyName])) {
                return self::$originalRequestData[$originalPropertyName];
            }

            return null;
        }

        return parent::__get($propertyName);
    }

    /**
     * Magic setter, guarding against attempts to set Original... properties.
     *
     * @param string $propertyName
     * @param mixed $value
     * @throws AttemptToModifyReadOnlyPropertyException
     */
    public function __set($propertyName, $value)
    {
        if (substr($propertyName, 0, 8) === 'Original') {
            throw new AttemptToModifyReadOnlyPropertyException("Attempt to modify Original Data of a request");
        }

        parent::__set($propertyName, $value);
    }

    /**
     * Magic method to handle superglobal accessors.
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, array $arguments)
    {
        $superglobalMethodNames = [
            'env',
            'server',
            'get',
            'post',
            'files',
            'cookie',
            'session',
            'request',
            'header'
        ];

        if (in_array(strtolower($name), $superglobalMethodNames)) {
            if (count($arguments) == 1) {
                return $this->getSuperglobalValue($name, $arguments[0]);
            } else {
                if (count($arguments) == 2) {
                    $this->setSuperglobalValue($name, $arguments[0], $arguments[1]);
                }
            }
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
    protected function getSuperglobalValue($superglobal, $index)
    {
        $propertyName = ucfirst(strtolower($superglobal)) . 'Data';

        if (!isset($this->modelData[$propertyName]) ||
            !isset($this->modelData[$propertyName][$index])
        ) {
            return null;
        }

        return $this->modelData[$propertyName][$index];
    }

    /**
     * Set the value stored against one of the request superglobals.
     *
     * Passing a value of null unsets the index.
     *
     * @param string $superglobal
     * @param mixed $index
     * @param mixed $value
     */
    protected function setSuperglobalValue($superglobal, $index, $value = null)
    {
        $propertyName = ucfirst(strtolower($superglobal)) . 'Data';

        if (!isset($this->modelData[$propertyName])) {
            $this->modelData[$propertyName] = [];
        }

        $this->modelData[$propertyName][$index] = $value;

        if ($value === null) {
            unset($this->modelData[$propertyName][$index]);
        }
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
