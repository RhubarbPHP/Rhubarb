<?php

namespace Rhubarb\Crown\Deployment;

use Rhubarb\Crown\UnitTesting\RhubarbTestCase;

class RelocationResourceDeploymentHandlerTest extends RhubarbTestCase
{
	public function testUrlCreated()
	{
		$deploymentPackage = new RelocationResourceDeploymentHandler();
		$url = $deploymentPackage->GetDeployedResourceUrl( __FILE__ );

		$cwd = getcwd();
		$deployedUrl = "/deployed/".str_replace( "\\", "/", str_replace( $cwd, "", __FILE__ ) );

		$this->assertEquals( $deployedUrl, $url );
	}

	public function testDeploymentCopiesFiles()
	{
		$cwd = getcwd();

		$deploymentPackage = new RelocationResourceDeploymentHandler();
		$deploymentPackage->DeployResource( __FILE__ );

		$deployedFile = "deployed/".str_replace( $cwd, "", __FILE__ );

		$this->assertFileExists( $deployedFile );

		unlink( $deployedFile );
	}

	public function testDeploymentThrowsExceptions()
	{
		$this->setExpectedException( "Rhubarb\Crown\Exceptions\DeploymentException" );

		$deploymentPackage = new RelocationResourceDeploymentHandler();
		$deploymentPackage->DeployResource( "a/b/c.txt" );
	}

	public function testDeploymentCreateFiles()
	{
		$deploymentPackage = new RelocationResourceDeploymentHandler();
		$deploymentPackage->DeployResourceContent( "This is a test", "temp/folder/file.txt" );

		$deployedFile = "deployed/temp/folder/file.txt";

		$this->assertFileExists( $deployedFile );

		$content = file_get_contents( $deployedFile );

		$this->assertEquals( "This is a test", $content );

		unlink( $deployedFile );
	}
}
