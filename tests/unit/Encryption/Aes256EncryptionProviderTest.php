<?php

namespace Rhubarb\Crown\Tests\unit\Encryption;

use Rhubarb\Crown\Tests\Fixtures\TestCases\RhubarbTestCase;

class Aes256EncryptionProviderTest extends RhubarbTestCase
{
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
    }

    public function testEncrypts()
    {
        $encrypter = new UnitTestingAes256EncryptionProvider();
        $result = $encrypter->encrypt("somedata");

        $this->assertEquals("PrHKcixewGccUfgyQsMWjg==", $result);
    }

    public function testDecrypts()
    {
        $encrypter = new UnitTestingAes256EncryptionProvider();
        $result = $encrypter->encrypt("somedata");
        $original = $encrypter->decrypt($result);

        $this->assertEquals("somedata", $original);
    }
}