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

namespace Rhubarb\Crown\Encryption;

use Rhubarb\Crown\Exceptions\ImplementationException;

/**
 * Provides a framework for providing encryption services.
 *
 * @author acuthbert
 * @copyright GCD Technologies 2013
 */
abstract class EncryptionProvider
{
    private static $defaultEncryptionProviderClassName = null;

    /**
     * Sets the class to be used for the default hash provider.
     *
     * @param $providerClassName
     * @return string Returns the class name of the previous default provider.
     */
    public static function setEncryptionProviderClassName($providerClassName)
    {
        $oldEncryptionProviderClassName = self::$defaultEncryptionProviderClassName;

        self::$defaultEncryptionProviderClassName = $providerClassName;

        return $oldEncryptionProviderClassName;
    }

    /**
     * Get's an instance of the default hash provider.
     *
     * @return EncryptionProvider
     * @throws ImplementationException
     */
    public static function getEncryptionProvider()
    {
        if (self::$defaultEncryptionProviderClassName == null) {
            throw new ImplementationException("No default encryption provider class name has been set.");
        }

        $providerClassName = self::$defaultEncryptionProviderClassName;
        $provider = new $providerClassName();

        if (!is_a($provider, "Rhubarb\Crown\Encryption\EncryptionProvider")) {
            throw new ImplementationException("The default encryption provider must extend Rhubarb\Crown\Encryption\EncryptionProvider");
        }

        return $provider;
    }

    /**
     * Returns the encrypted data.
     *
     * @param $data
     * @param string $keySalt An optional piece of data possibly used by the encryption algorithm to derive the key
     * @return string
     */
    public abstract function encrypt($data, $keySalt = "");

    /**
     * Returns the decrypted data
     *
     * @param $data
     * @param string $keySalt An optional piece of data possibly used by the encryption algorithm to derive the key
     * @return mixed
     */
    public abstract function decrypt($data, $keySalt = "");
}