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
 * Provides a framework for providing hash services to your application.
 *
 * While you can instantiate an instance of an individual hash provider, the best
 * practice is to call the static HashProvider::GetHashProvider() method so that the hashing
 * provider can be set with a dependency injection.
 */
abstract class HashProvider
{
    private static $defaultHashProviderClassName = null;

    /**
     * Sets the class to be used for the default hash provider.
     *
     * @param $providerClassName
     */
    public static function setHashProviderClassName($providerClassName)
    {
        self::$defaultHashProviderClassName = $providerClassName;
    }

    /**
     * Get's an instance of the default hash provider.
     *
     * @return HashProvider
     * @throws ImplementationException
     */
    public static function getHashProvider()
    {
        if (self::$defaultHashProviderClassName == null) {
            throw new ImplementationException("No default hash provider class name has been set.");
        }

        $providerClassName = self::$defaultHashProviderClassName;
        $provider = new $providerClassName();

        if (!is_a($provider, "Rhubarb\Crown\Encryption\HashProvider")) {
            throw new ImplementationException("The default hash provider must extend Rhubarb\Crown\Encryption\HashProvider");
        }

        return $provider;
    }

    /**
     * Create's a new hash of the supplied data using the optionally supplied salt.
     *
     * It's expected that the salt can be randomised if not supplied. This is fact
     * preferable in nearly all occasions as the salt should be included in the return
     * value.
     *
     * @param string $data
     * @param string $salt
     * @return string
     */
    public abstract function createHash($data, $salt = "");

    /**
     * Computes the hash of the supplied data using the salt contained within an existing hash.
     *
     * If the resultant value matches the hash the hash and data are equivilant.
     *
     * @param $data
     * @param $hash
     * @return bool
     */
    public abstract function compareHash($data, $hash);
}
