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

namespace Rhubarb\Crown\Sessions;

use Rhubarb\Crown\DependencyInjection\Container;
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

    public function setSessionData($data = [])
    {
        $decryptedData = [];

        $keySalt = $this->getEncryptionKeySalt();
        $provider = Container::instance(EncryptionProvider::class);

        foreach($data as $key => $value){
            $decryptedData[$key] = $provider->decrypt($value, $keySalt);
        }

        parent::setSessionData($decryptedData);
    }

    public function extractSessionData()
    {
        $data = parent::extractSessionData();
        $encrypted = [];

        $keySalt = $this->getEncryptionKeySalt();
        $provider = Container::instance(EncryptionProvider::class);

        foreach($data as $key => $value){
            if (is_object($value) || is_array($value)){
                continue;
            }

            $encrypted[$key] = $provider->encrypt($value, $keySalt);
        }

        return $encrypted;
    }
}
