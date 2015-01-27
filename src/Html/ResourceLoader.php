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
use Rhubarb\Crown\Exceptions\ResourceNotFound;

class ResourceLoader
{
    private static $resources = array();

    /**
     * Adds javascript script code to the collection
     *
     * @param $scriptCode
     * @param array $dependantResourceUrls
     */
    public static function addScriptCode($scriptCode, $dependantResourceUrls = [])
    {
        $dependantResourceUrls = array_unique($dependantResourceUrls);

        foreach (self::$resources as $index => $resource) {
            $urls = $resource[1];

            if ($urls == $dependantResourceUrls) {
                self::$resources[$index][0] .= "
	$scriptCode";
                return;
            }
        }

        self::$resources[] = array($scriptCode, $dependantResourceUrls);
    }

    public static function addScriptCodeOnReady($scriptCode, $dependantResourceUrls = [])
    {
        array_splice(
            $dependantResourceUrls,
            0,
            0,
            array(
                self::getJqueryUrl("1.9.1"),
                self::getJqueryUIUrl("1.10.0"),
                "/client/jquery/css/jquery-ui.css",
                "/client/jquery/css/jquery.ui.theme.css"
            )
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
        $html = "<script src=\"/client/resource-manager.js\" type=\"text/javascript\"></script>";

        $context = new Context();

        $preLoadedCssFiles = [];

        // CSS files are safe to load immediately and might avoid 'flicker' by so doing.
        if (!$context->IsAjaxRequest) {
            foreach (self::$resources as $item) {
                $dependantResources = $item[1];

                foreach ($dependantResources as $resource) {
                    if (in_array($resource, $preLoadedCssFiles)) {
                        continue;
                    }

                    $parts = explode(".", $resource);
                    $extension = strtolower($parts[sizeof($parts) - 1]);

                    if ($extension == "css") {
                        $html .= "
<link type=\"text/css\" rel=\"stylesheet\" href=\"" . $resource . "\" />";

                        $preLoadedCssFiles[] = $resource;
                    }
                }
            }
        }

        $tags = "";

        array_walk(
            self::$resources,
            function ($item) use (&$tags, $preLoadedCssFiles) {
                $source = $item[0];
                $dependantResources = $item[1];

                if ($source == "") {
                    // CSS files have already been loaded, so we don't need to ask for them to be loaded again. We only
                    // do this if there is no source code attached to this load. If source is attached we need to include
                    // the CSS as we don't want the JS to start until the CSS is ready.
                    $dependantResources = array_diff($dependantResources, $preLoadedCssFiles);
                }

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
            $html .= "
<script type=\"text/javascript\">
$tags</script>";
        }

        return $html;
    }

    public static function getJqueryUrl($version, $cdn = true)
    {
        $url = ($cdn) ? "//ajax.googleapis.com/ajax/libs/jquery/" . $version . "/jquery.min.js" :
            "/client/jquery/jquery-" . $version . ".js";

        if (!$cdn) {
            // Check the resource exists!
            $path = __DIR__ . "/Resources" . str_replace("/client/", "/", $url);

            if (!file_exists($path)) {
                throw new ResourceNotFound($url);
            }
        }

        return $url;
    }

    public static function loadJquery($version, $cdn = true)
    {
        self::loadResource(self::getJqueryUrl($version, $cdn));
    }

    public static function getJqueryUIUrl($version, $cdn = true)
    {
        $url = ($cdn) ? "//ajax.googleapis.com/ajax/libs/jqueryui/" . $version . "/jquery-ui.min.js" :
            "/client/jquery-ui/jquery-ui-" . $version . ".js";

        if (!$cdn) {
            // Check the resource exists!
            $path = "libraries/core/modules/ClientSide/Resources" . str_replace("/client/", "/", $url);

            if (!file_exists($path)) {
                throw new ResourceNotFound($url);
            }
        }

        return $url;
    }

    public static function loadJqueryUI($version, $cdn = true)
    {
        self::loadResource(self::getJqueryUIUrl($version, $cdn));
        self::loadResource("/client/jquery/css/jquery-ui.css");
        self::loadResource("/client/jquery/css/jquery.ui.theme.css");
    }
}
