<?php

namespace Rhubarb\Crown\Deployment;

use Rhubarb\Crown\UnitTesting\RhubarbTestCase;

class ResourceDeploymentPackageTest extends RhubarbTestCase
{
	public function testAllFilesDeployed()
	{
		$package = new ResourceDeploymentPackage();
		$package->resourcesToDeploy = [ __FILE__, __DIR__."/IDeployable.php" ];
		$package->Deploy();

		$cwd = getcwd();

		$this->assertFileExists( "deployed/".str_replace( $cwd, "", __FILE__ ) );
		$this->assertFileExists( "deployed/".str_replace( $cwd, "", __DIR__."/IDeployable.php" ) );

		unlink( "deployed/".str_replace( $cwd, "", __FILE__ ) );
		unlink( "deployed/".str_replace( $cwd, "", __DIR__."/IDeployable.php" ) );
	}

	public function testDeploymentUrls()
	{
		$package = new ResourceDeploymentPackage();
		$package->resourcesToDeploy = [ __FILE__, __DIR__."/IDeployable.php" ];
		$urls = $package->GetDeployedUrls();

		$cwd = getcwd();

		$this->assertEquals(
			[ "/deployed".str_replace( "\\", "/", str_replace( $cwd, "", __FILE__ ) ),
			  "/deployed".str_replace( "\\", "/", str_replace( $cwd, "", __DIR__."/IDeployable.php" ) )
			], $urls );
	}
}
