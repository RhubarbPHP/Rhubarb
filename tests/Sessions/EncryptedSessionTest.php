<?php

namespace Rhubarb\Crown\Sessions;

use Rhubarb\Crown\Context;
use Rhubarb\Crown\CoreModule;
use Rhubarb\Crown\Encryption\EncryptionProvider;
use Rhubarb\Crown\Integration\IntegrationModule;
use Rhubarb\Crown\Layout\LayoutModule;
use Rhubarb\Crown\Modelling\Models\Model;
use Rhubarb\Crown\Modelling\Repositories\Repository;
use Rhubarb\Crown\Modelling\Schema\SolutionSchema;
use Rhubarb\Crown\Module;
use Rhubarb\Crown\Mvp\MvpModule;
use Rhubarb\Crown\Patterns\PatternsModule;
use Rhubarb\Crown\Scaffolds\AuthenticationWithRoles\AuthenticationWithRolesModule;
use Rhubarb\Crown\Scaffolds\NavigationMenu\NavigationMenuModule;
use Rhubarb\Crown\UnitTesting\RhubarbTestCase;
use Rhubarb\Crown\UnitTesting\UnitTestingModule;
use PHPUnit_Framework_TestCase;

class EncryptedSessionTest extends RhubarbTestCase
{
	private static $_oldEncryptionProvider = "";

	public static function setUpBeforeClass()
	{
		parent::setUpBeforeClass();

		self::$_oldEncryptionProvider = EncryptionProvider::SetEncryptionProviderClassName( '\Rhubarb\Crown\Encryption\Aes256ComputedKeyEncryptionProvider' );
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