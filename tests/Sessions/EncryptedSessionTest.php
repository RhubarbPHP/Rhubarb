<?php

namespace Gcd\Core\Sessions;

use Gcd\Core\Context;
use Gcd\Core\CoreModule;
use Gcd\Core\Encryption\EncryptionProvider;
use Gcd\Core\Integration\IntegrationModule;
use Gcd\Core\Layout\LayoutModule;
use Gcd\Core\Modelling\Models\Model;
use Gcd\Core\Modelling\Repositories\Repository;
use Gcd\Core\Modelling\Schema\SolutionSchema;
use Gcd\Core\Module;
use Gcd\Core\Mvp\MvpModule;
use Gcd\Core\Patterns\PatternsModule;
use Gcd\Core\Scaffolds\AuthenticationWithRoles\AuthenticationWithRolesModule;
use Gcd\Core\Scaffolds\NavigationMenu\NavigationMenuModule;
use Gcd\Core\UnitTesting\CoreTestCase;
use Gcd\Core\UnitTesting\UnitTestingModule;
use PHPUnit_Framework_TestCase;

class EncryptedSessionTest extends CoreTestCase
{
	private static $_oldEncryptionProvider = "";

	public static function setUpBeforeClass()
	{
		parent::setUpBeforeClass();

		self::$_oldEncryptionProvider = EncryptionProvider::SetEncryptionProviderClassName( '\Gcd\Core\Encryption\Aes256ComputedKeyEncryptionProvider' );
	}

	public static function tearDownAfterClass()
	{
		parent::tearDownAfterClass();

		EncryptionProvider::SetEncryptionProviderClassName( self::$_oldEncryptionProvider );
	}

	public function testSessionEncrypts()
	{
		$session = new UnitTestEncryptedSession();
		$session->TestValue = "123456";
		$raw = $session->ExportRawData();

		$this->assertEquals( "lu3RCzBb/lz4HIqFnlHc7A==", $raw[ "TestValue" ] );
		$this->assertEquals( "123456", $session->TestValue );
	}
}

class UnitTestEncryptedSession extends EncryptedSession
{
	/**
	 * Override to return the encryption key to use.
	 *
	 * @return mixed
	 */
	protected function GetEncryptionKeySalt()
	{
		return "simplekey";
	}
}