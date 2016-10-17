<?php
/**
 * Copyright (c) 2016 RhubarbPHP.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Rhubarb\Crown\Tests\unit\Html;

use Rhubarb\Crown\Html\ResourceLoader;
use Rhubarb\Crown\Tests\Fixtures\TestCases\RhubarbTestCase;

class ResourceLoaderTest extends RhubarbTestCase
{
    public function testLoadScript()
    {
        ResourceLoader::addScriptCode("alert(123)");
        $scripts = ResourceLoader::getResourceInjectionHtml();

        $this->assertEquals("<script src=\"/deployed/resources/resource-manager.js?1476715053.js\" type=\"text/javascript\"></script><script type=\"text/javascript\">
//<![CDATA[
window.resourceManager.runWhenDocumentReady( function()
{
	alert(123)
} );

//]]>
</script>", $scripts);

        ResourceLoader::clearResources();
        ResourceLoader::addScriptCode("doThis();", ["a.js", "b.js"]);
        $scripts = ResourceLoader::getResourceInjectionHtml();

        $this->assertEquals("<script src=\"/deployed/resources/resource-manager.js?1476715053.js\" type=\"text/javascript\"></script>
<script type=\"text/javascript\" src=\"a.js\"></script>
<script type=\"text/javascript\" src=\"b.js\"></script><script type=\"text/javascript\">
//<![CDATA[
window.resourceManager.runWhenDocumentReady( function()
{
	doThis();
} );

//]]>
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

            $this->setExpectedException(\Rhubarb\Crown\ClientSide\Exceptions\ClientSideResourceNotFound::class);

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

        $this->assertEquals('<script src="/deployed/resources/resource-manager.js?1476715053.js" type="text/javascript"></script>
<link type="text/css" rel="stylesheet" href="/css/base.css" />', $scripts);
    }

    public function testLoadingMultipleScriptsWithSameDependancies()
    {
        ResourceLoader::clearResources();

        ResourceLoader::addScriptCode("doThis();", ["/a.js", "/b.js"]);
        ResourceLoader::addScriptCode("doThat();", ["/a.js", "/b.js"]);

        $scripts = ResourceLoader::getResourceInjectionHtml();

        $this->assertEquals("<script src=\"/deployed/resources/resource-manager.js?1476715053.js\" type=\"text/javascript\"></script>
<script type=\"text/javascript\" src=\"/a.js\"></script>
<script type=\"text/javascript\" src=\"/b.js\"></script><script type=\"text/javascript\">
//<![CDATA[
window.resourceManager.runWhenDocumentReady( function()
{
	doThis();
	doThat();
} );

//]]>
</script>", $scripts);
    }
}
