<?php

/*
 *	Copyright 2015 RhubarbPHP
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 */

namespace Rhubarb\Crown\Html;

use Rhubarb\Crown\Context;
use Rhubarb\Crown\Deployment\ResourceDeploymentPackage;
use Rhubarb\Crown\Deployment\ResourceDeploymentProvider;

class ResourceLoader
{
    private static $resources = [];

    public static $jqueryUiThemePath = "vendor/components/jqueryui/themes/base/";

    /**
     * Adds javascript script code to the collection
     *
     * @param $scriptCode
     * @param array $dependantResourceUrls
     */
    public static function addScriptCode($scriptCode, $dependantResourceUrls = [])
    {
        $dependantResourceUrls = array_unique($dependantResourceUrls);

        self::$resources[] = [$scriptCode, $dependantResourceUrls];
    }

    public static function addScriptCodeOnReady($scriptCode, $dependantResourceUrls = [])
    {
        // Deploy the composer jquery libraries.
        $package = new ResourceDeploymentPackage();
        $package->resourcesToDeploy[] = __DIR__."/../../../../components/jquery/jquery.min.js";
        $package->resourcesToDeploy[] = __DIR__."/../../../../components/jqueryui/jquery-ui.min.js";
        $package->resourcesToDeploy[] = __DIR__."/../../../../components/jqueryui/themes/ui-lightness/jquery-ui.min.css";
        $package->resourcesToDeploy[] = __DIR__."/../../../../components/jqueryui/themes/ui-lightness/theme.css";
        $jQueryUrls = $package->deploy();

        array_splice(
            $dependantResourceUrls,
            0,
            0,
            $jQueryUrls
        );

        self::addScriptCode($scriptCode, $dependantResourceUrls);
    }

    /**
     * Loads a single resource
     *
     * @param $resourceUrls
     */
    public static function loadResource($resourceUrls)
    {
        if (!is_array($resourceUrls)) {
            $resourceUrls = [$resourceUrls];
        }
        self::$resources[] = ["", $resourceUrls];
    }

    /**
     * Removes all scripts from the collection.
     */
    public static function clearResources()
    {
        self::$resources = [];
    }

    /**
     * Returns two script tags, one loading the script manager and the second executing all other
     * scripts registered with AddScript.
     *
     * This takes a new approach - instead of adding all the scripts to the <head> tag when
     * rendering full HTML, we use the JS script manager. This means the approach is the same
     * whether the request is a normal HTML page, or an AJAX post. This is borne out of frustration
     * with finding some libraries like Google maps not working if added to the page via AJAX. Any client
     * side issues with the script loader will now become a major concern and should get addressed quickly.
     *
     * @see ResourceLoader::AddResource()
     * @return string
     */
    public static function getResourceInjectionHtml()
    {
        $package = new ResourceDeploymentPackage();
        $package->resourcesToDeploy[] = __DIR__ . "/../../resources/resource-manager.js";
        $urls = $package->deploy();

        $html = "<script src=\"" . $urls[0] . "\" type=\"text/javascript\"></script>";

        $context = new Context();

        $preLoadedFiles = [];

        // CSS files are safe to load immediately and might avoid 'flicker' by so doing.
        if (!$context->IsAjaxRequest) {
            foreach (self::$resources as $item) {
                $dependantResources = $item[1];

                foreach ($dependantResources as $resource) {
                    if (in_array($resource, $preLoadedFiles)) {
                        continue;
                    }

                    $parts = explode(".", $resource);
                    $extension = strtolower($parts[sizeof($parts) - 1]);

                    if ($extension == "css") {
                        $html .= "
<link type=\"text/css\" rel=\"stylesheet\" href=\"" . $resource . "\" />";

                        $preLoadedFiles[] = $resource;
                    }

                    if ($extension == "js") {
                        $html .= "
<script type=\"text/javascript\" src=\"" . $resource . "\"></script>";

                        $preLoadedFiles[] = $resource;
                    }
                }
            }
        }

        $groupedItems = [];

        foreach (self::$resources as $index => $resource) {
            $dependantResources = $resource[1];
            $dependantResources = array_diff($dependantResources, $preLoadedFiles);

            $urls = implode("", $dependantResources);

            if (!isset($groupedItems[$urls])) {
                $groupedItems[$urls] = [$resource[0], $dependantResources];
            } else {
                $groupedItems[$urls][0] .= "
	" . $resource[0];
            }
        }

        $tags = "";

        array_walk(
            $groupedItems,
            function ($item) use (&$tags, $preLoadedFiles) {
                $source = $item[0];
                $dependantResources = $item[1];

                if (sizeof($dependantResources) > 0) {
                    $resourcesArray = '[ "' . implode('", "', $dependantResources) . '" ]';

                    if ($source == "") {
                        $source = 'window.resourceManager.loadResources( ' . $resourcesArray . ' );';
                    } else {
                        $source = 'window.resourceManager.loadResources( ' . $resourcesArray . ', function()
{
	' . $source . '
} );';
                    }
                } else {
                    if ($source != "") {
                        $source = 'window.resourceManager.runWhenDocumentReady( function()
{
	' . $source . '
} );';
                    }
                }

                $tags .= $source . "
";
            }
        );

        if (trim($tags) != "") {
            $html .= <<<HTML
<script type="text/javascript">
//<![CDATA[
$tags
//]]>
</script>
HTML;
        }

        return $html;
    }

    public static function getJqueryUrl()
    {
        $deployer = ResourceDeploymentProvider::getResourceDeploymentProvider();
        return $deployer->deployResource(__DIR__."/../../../../components/jquery/jquery.min.js");
    }

    public static function loadJquery()
    {
        self::loadResource(self::getJqueryUrl());
    }

    public static function getJqueryUIUrl()
    {
        $deployer = ResourceDeploymentProvider::getResourceDeploymentProvider();
        return $deployer->deployResource(__DIR__."/../../../../components/jqueryui/jquery-ui.min.js");
    }

    public static function loadJqueryUI()
    {
        $deployer = ResourceDeploymentProvider::getResourceDeploymentProvider();

        self::loadResource($deployer->deployResource(self::$jqueryUiThemePath."jquery-ui.css"));

        $imagePath = self::$jqueryUiThemePath . "images/";
        $imageExtensions = ["png", "jpg", "jpeg", "gif"];

        $dh = opendir($imagePath);
        while ($file = readdir($dh)) {
            if (!is_dir($imagePath.$file) && in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), $imageExtensions)) {
                $deployer->deployResource($imagePath.$file);
            }
        }

        self::loadResource(self::getJqueryUIUrl());
    }
}
