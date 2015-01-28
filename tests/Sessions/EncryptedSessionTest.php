<?php

namespace Rhubarb\Crown\Tests\Sessions;

use Rhubarb\Crown\Encryption\EncryptionProvider;
use Rhubarb\Crown\Sessions\EncryptedSession;
use Rhubarb\Crown\Tests\RhubarbTestCase;

class EncryptedSessionTest extends RhubarbTestCase
{
    private static $oldEncryptionProvider = "";

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        self::$oldEncryptionProvider = EncryptionProvider::setEncryptionProviderClassName('\Rhubarb\Crown\Encryption\Aes256ComputedKeyEncryptionProvider');
    }

    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();

        EncryptionProvider::setEncryptionProviderClassName(self::$oldEncryptionProvider);
    }

    public function testSessionEncrypts()
    {
        $session = new UnitTestEncryptedSession();
        $session->TestValue = "123456";
        $raw = $session->exportRawData();

        $this->assertEquals("lu3RCzBb/lz4HIqFnlHc7A==", $raw["TestValue"]);
        $this->assertEquals("123456", $session->TestValue);
    }
}

class UnitTestEncryptedSession extends EncryptedSession
{
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