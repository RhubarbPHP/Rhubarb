<?php

namespace Gcd\Core\Encryption;

/**
 *
 * @author acuthbert
 * @copyright GCD Technologies 2013
 */
use Gcd\Core\Encryption\UnitTesting\UnitTestingAes256EncryptionProvider;
use Gcd\Core\UnitTesting\CoreTestCase;

class Aes256EncryptionProviderTest extends CoreTestCase
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