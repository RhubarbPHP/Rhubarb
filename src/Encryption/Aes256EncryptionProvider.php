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

require_once __DIR__ . "/EncryptionProvider.php";

abstract class Aes256EncryptionProvider extends EncryptionProvider
{
    /**
     * Implement this abstract function to return a key for encryption and decryption
     *
     * @param string $keySalt An optional string used to derive the key. Not all use cases will supply this.
     * @return string
     */
    protected abstract function getEncryptionKey($keySalt = "");

    public function encrypt($data, $keySalt = "")
    {
        $key = $this->getEncryptionKey($keySalt);

        return openssl_encrypt($data, "aes-256-cbc", $key, false, "3132333435363738");
    }

    public function decrypt($data, $keySalt = "")
    {
        $key = $this->getEncryptionKey($keySalt);

        return openssl_decrypt($data, "aes-256-cbc", $key, false, "3132333435363738");
    }
}