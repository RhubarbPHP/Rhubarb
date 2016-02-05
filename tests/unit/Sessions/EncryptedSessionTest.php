<?php

namespace Rhubarb\Crown\Tests\unit\Sessions;

use Rhubarb\Crown\Container;
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
        $session = UnitTestEncryptedSession::instance();
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