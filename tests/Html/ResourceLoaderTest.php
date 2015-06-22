<?php

namespace Rhubarb\Crown\Tests\Html;

use Rhubarb\Crown\Html\ResourceLoader;
use Rhubarb\Crown\Tests\RhubarbTestCase;

class ResourceLoaderTest extends RhubarbTestCase
{
    public function testLoadScript()
    {
        ResourceLoader::addScriptCode("alert(123)");
        $scripts = ResourceLoader::getResourceInjectionHtml();

        $this->assertEquals("<script src=\"/client/resource-manager.js\" type=\"text/javascript\"></script>
<script type=\"text/javascript\">
window.resourceManager.runWhenDocumentReady( function()
{
	alert(123)
} );
</script>", $scripts);

        ResourceLoader::clearResources();
        ResourceLoader::addScriptCode("doThis();", ["a.js", "b.js"]);
        $scripts = ResourceLoader::getResourceInjectionHtml();

        $this->assertEquals("<script src=\"/client/resource-manager.js\" type=\"text/javascript\"></script>
<script type=\"text/javascript\">
window.resourceManager.loadResources( [ \"a.js\", \"b.js\" ], function()
{
	doThis();
} );
</script>", $scripts);
    }

    /*
        public function testLoadJquery()
        {
            ResourceLoader::clearResources();

            ResourceLoader::loadJquery("1.8.3", false);

            $scripts = ResourceLoader::getResourceInjectionHtml();

            $this->assertEquals("<script src=\"/client/resource-manager.js\" type=\"text/javascript\"></script>
    <script type=\"text/javascript\">
    window.resourceManager.loadResources( [ \"/client/jquery/jquery-1.8.3.js\" ] );
    </script>", $scripts);

            ResourceLoader::clearResources();

            ResourceLoader::loadJquery("1.8.3", true);

            $scripts = ResourceLoader::getResourceInjectionHtml();

            $this->assertEquals("<script src=\"/client/resource-manager.js\" type=\"text/javascript\"></script>
    <script type=\"text/javascript\">
    window.resourceManager.loadResources( [ \"//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js\" ] );
    </script>", $scripts);

            ResourceLoader::clearResources();

            $this->setExpectedException("\Rhubarb\Crown\ClientSide\Exceptions\ClientSideResourceNotFound");

            // A very large version number that won't exist locally.
            ResourceLoader::loadJquery("1991.8.3", false);
        }
    */
    public function testLoadingStylesheetTwice()
    {
        ResourceLoader::clearResources();

        ResourceLoader::loadResource("/css/base.css");
        ResourceLoader::loadResource("/css/base.css");

        $scripts = ResourceLoader::getResourceInjectionHtml();

        $this->assertEquals('<script src="/client/resource-manager.js" type="text/javascript"></script>
<link type="text/css" rel="stylesheet" href="/css/base.css" />', $scripts);
    }

    public function testLoadingMultipleScriptsWithSameDependancies()
    {
        ResourceLoader::clearResources();

        ResourceLoader::addScriptCode("doThis();", ["/a.js", "/b.js"]);
        ResourceLoader::addScriptCode("doThat();", ["/a.js", "/b.js"]);

        $scripts = ResourceLoader::getResourceInjectionHtml();

        $this->assertEquals("<script src=\"/client/resource-manager.js\" type=\"text/javascript\"></script>
<script type=\"text/javascript\">
window.resourceManager.loadResources( [ \"/a.js\", \"/b.js\" ], function()
{
	doThis();
	doThat();
} );
</script>", $scripts);
    }
}
