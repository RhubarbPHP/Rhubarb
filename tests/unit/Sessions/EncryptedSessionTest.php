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

namespace Rhubarb\Crown\Tests\unit\Sessions;

use Rhubarb\Crown\DependencyInjection\Container;
use Rhubarb\Crown\Encryption\Aes256ComputedKeyEncryptionProvider;
use Rhubarb\Crown\Encryption\EncryptionProvider;
use Rhubarb\Crown\Sessions\EncryptedSession;
use Rhubarb\Crown\Tests\Fixtures\TestCases\RhubarbTestCase;

class EncryptedSessionTest extends RhubarbTestCase
{
    public function setUp()
    {
        parent::setUp();

        Container::current()->registerClass(EncryptionProvider::class, Aes256ComputedKeyEncryptionProvider::class);
    }

    public function testSessionEncrypts()
    {
        $session = UnitTestEncryptedSession::singleton();
        $session->TestValue = "123456";
        $raw = get_object_vars($session);

        $this->assertEquals("lu3RCzBb/lz4HIqFnlHc7A==", $session->getEncryptedData()["TestValue"]);
        $this->assertEquals("123456", $session->TestValue);
    }
}

class UnitTestEncryptedSession extends EncryptedSession
{
    public function getEncryptedData()
    {
        return $this->encryptedData;
    }

    /**
     * Override to return the encryption key to use.
     *
     * @return mixed
     */
    protected function getEncryptionKeySalt()
    {
        return "simplekey";
    }
}