<?php

namespace Rhubarb\Crown\Encryption;

/**
 *
 * @author acuthbert
 * @copyright GCD Technologies 2013
 */
use Rhubarb\Crown\Encryption\UnitTesting\UnitTestingAes256EncryptionProvider;
use Rhubarb\Crown\UnitTesting\RhubarbTestCase;

class Aes256EncryptionProviderTest extends RhubarbTestCase
{
	public static function setUpBeforeClass()
	{
		parent::setUpBeforeClass();
	}

	public function testEncrypts()
	{
		$encrypter = new UnitTestingAes256EncryptionProvider();
		$result = $encrypter->Encrypt( "somedata" );

		$this->assertEquals( "PrHKcixewGccUfgyQsMWjg==", $result );
	}

	public function testDecrypts()
	{
		$encrypter = new UnitTestingAes256EncryptionProvider();
		$result = $encrypter->Encrypt( "somedata" );
		$original = $encrypter->Decrypt( $result );

		$this->assertEquals( "somedata", $original );
	}
}