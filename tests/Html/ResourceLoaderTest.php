<?php

namespace Gcd\Tests;

use \Gcd\Core\ClientSide\ResourceLoader;

class ResourceLoaderTest extends \Gcd\Core\UnitTesting\CoreTestCase
{
    public function testLoadScript()
    {
		\Gcd\Core\ClientSide\ResourceLoader::AddScriptCode( "alert(123)" );
	    $scripts = \Gcd\Core\ClientSide\ResourceLoader::GetResourceInjectionHtml();

	    $this->assertEquals( "<script src=\"/client/resource-manager.js\" type=\"text/javascript\"></script>
<script type=\"text/javascript\">
window.resourceManager.runWhenDocumentReady( function()
{
	alert(123)
} );
</script>", $scripts );

	    \Gcd\Core\ClientSide\ResourceLoader::ClearResources();
	    \Gcd\Core\ClientSide\ResourceLoader::AddScriptCode( "doThis();", array( "a.js", "b.js" ) );
	    $scripts = \Gcd\Core\ClientSide\ResourceLoader::GetResourceInjectionHtml();

	    $this->assertEquals( "<script src=\"/client/resource-manager.js\" type=\"text/javascript\"></script>
<script type=\"text/javascript\">
window.resourceManager.loadResources( [ \"a.js\", \"b.js\" ], function()
{
	doThis();
} );
</script>", $scripts );
    }

	public function testLoadJquery()
	{
		\Gcd\Core\ClientSide\ResourceLoader::ClearResources();

		ResourceLoader::LoadJquery( "1.8.3", false );

		$scripts = \Gcd\Core\ClientSide\ResourceLoader::GetResourceInjectionHtml();

		$this->assertEquals( "<script src=\"/client/resource-manager.js\" type=\"text/javascript\"></script>
<script type=\"text/javascript\">
window.resourceManager.loadResources( [ \"/client/jquery/jquery-1.8.3.js\" ] );
</script>", $scripts );

		\Gcd\Core\ClientSide\ResourceLoader::ClearResources();

		ResourceLoader::LoadJquery( "1.8.3", true );

		$scripts = \Gcd\Core\ClientSide\ResourceLoader::GetResourceInjectionHtml();

		$this->assertEquals( "<script src=\"/client/resource-manager.js\" type=\"text/javascript\"></script>
<script type=\"text/javascript\">
window.resourceManager.loadResources( [ \"//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js\" ] );
</script>", $scripts );

		\Gcd\Core\ClientSide\ResourceLoader::ClearResources();

		$this->setExpectedException( "\Gcd\Core\ClientSide\Exceptions\ClientSideResourceNotFound" );

		// A very large version number that won't exist locally.
		ResourceLoader::LoadJquery( "1991.8.3", false );
	}

    public function testLoadingStylesheetTwice()
    {
        \Gcd\Core\ClientSide\ResourceLoader::ClearResources();

        ResourceLoader::LoadResource( "/css/base.css" );
        ResourceLoader::LoadResource( "/css/base.css" );

        $scripts = \Gcd\Core\ClientSide\ResourceLoader::GetResourceInjectionHtml();

        $this->assertEquals( '<script src="/client/resource-manager.js" type="text/javascript"></script>
<link type="text/css" rel="stylesheet" href="/css/base.css" />', $scripts );
    }

	public function testLoadingMultipleScriptsWithSameDependancies()
	{
		\Gcd\Core\ClientSide\ResourceLoader::ClearResources();

		ResourceLoader::AddScriptCode( "doThis();", [ "/a.js", "/b.js" ] );
		ResourceLoader::AddScriptCode( "doThat();", [ "/a.js", "/b.js" ] );

		$scripts = \Gcd\Core\ClientSide\ResourceLoader::GetResourceInjectionHtml();

		$this->assertEquals( "<script src=\"/client/resource-manager.js\" type=\"text/javascript\"></script>
<script type=\"text/javascript\">
window.resourceManager.loadResources( [ \"/a.js\", \"/b.js\" ], function()
{
	doThis();
	doThat();
} );
</script>", $scripts );
	}
}
