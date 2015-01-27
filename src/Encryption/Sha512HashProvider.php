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

require_once __DIR__ . "/HashProvider.php";

/**
 *
 * @author acuthbert
 * @copyright GCD Technologies 2013
 */
class Sha512HashProvider extends HashProvider
{

    /**
     * Create's a new hash of the supplied data using the optionally supplied salt.
     *
     * It's expected that the salt can be randomised if not supplied. This is fact
     * preferable in nearly all occasions as the salt should be included in the return
     * value.
     *
     * @param string $data
     * @param string|bool $salt
     * @return string
     */
    public function createHash($data, $salt = false)
    {
        if ($salt === false) {
            $salt = crypt("a34t" . $data . "zxcv@");
            $parts = explode('$', $salt);
            $salt = $parts[sizeof($parts) - 1];
        }

        $raw = crypt($data, '$6$rounds=10000$' . $salt . '$');

        return $raw;
    }

    private function extractSalt($hash)
    {
        $parts = explode('$', $hash);

        if (!isset($parts[3])) {
            return "";
        }

        $salt = $parts[3];

        return $salt;
    }

    /**
     * Computes the hash of the supplied data using the salt contained within an existing hash.
     *
     * If the resultant value matches the hash the hash and data are equivilant.
     *
     * @param $data
     * @param $hash
     * @return bool
     */
    public function compareHash($data, $hash)
    {
        $salt = $this->extractSalt($hash);

        $newHash = $this->createHash($data, $salt);

        return ($newHash == $hash);
    }
}
