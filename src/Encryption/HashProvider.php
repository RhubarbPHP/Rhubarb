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

namespace Rhubarb\Crown\Encryption;

use Rhubarb\Crown\Application;
use Rhubarb\Crown\DependencyInjection\Container;
use Rhubarb\Crown\DependencyInjection\ProviderInterface;
use Rhubarb\Crown\DependencyInjection\ProviderTrait;
use Rhubarb\Crown\Exceptions\ImplementationException;

/**
 * Provides a framework for providing hash services to your application.
 *
 * While you can instantiate an instance of an individual hash provider, the best
 * practice is to call the static HashProvider::getHashProvider() method so that the hashing
 * provider can be set with a dependency injection.
 */
abstract class HashProvider implements ProviderInterface
{
    use ProviderTrait;

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
    abstract public function createHash($data, $salt = "");

    /**
     * Computes the hash of the supplied data using the salt contained within an existing hash.
     *
     * If the resultant value matches the hash the hash and data are equivilant.
     *
     * @param $data
     * @param $hash
     * @return bool
     */
    abstract public function compareHash($data, $hash);
}
