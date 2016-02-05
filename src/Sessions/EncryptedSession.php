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

namespace Rhubarb\Crown\Sessions;

use Rhubarb\Crown\Container;
use Rhubarb\Crown\Encryption\EncryptionProvider;

/**
 * Provides encryption support for storage of vulnerable data in sessions.
 *
 * Only to be used if it's absolutely essential the data is stored in the first place.
 */
abstract class EncryptedSession extends Session
{
    protected $encryptedData = [];

    /**
     * Override to return the encryption key salt to use.
     *
     * @return mixed
     */
    abstract protected function getEncryptionKeySalt();

    public function __set($propertyName, $value)
    {
        $keySalt = $this->getEncryptionKeySalt();
        $provider = Container::instance(EncryptionProvider::class);

        $value = $provider->encrypt($value, $keySalt);

        $this->encryptedData[$propertyName] = $value;
    }

    public function __get($propertyName)
    {
        $keySalt = $this->getEncryptionKeySalt();
        $provider = Container::instance(EncryptionProvider::class);

        return $provider->decrypt($this->encryptedData[$propertyName], $keySalt);
    }
}
