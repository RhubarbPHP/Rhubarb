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

namespace Rhubarb\Crown\Deployment;

use Rhubarb\Crown\Exceptions\DeploymentException;

abstract class ResourceDeploymentProvider
{
    private static $resourceDeploymentProvider = null;

    private static $resourceDeploymentProviderClassName = "Rhubarb\Crown\Deployment\RelocationResourceDeploymentProvider";

    public static function setResourceDeploymentProviderClassName($resourceDeploymentProviderClassName)
    {
        self::$resourceDeploymentProviderClassName = $resourceDeploymentProviderClassName;
        self::$resourceDeploymentProvider = null;
    }

    public static function getResourceDeploymentProvider()
    {
        if (self::$resourceDeploymentProvider == null) {
            $class = self::$resourceDeploymentProviderClassName;

            if (!class_exists($class)) {
                throw new DeploymentException("The resource deployment Provider class " . $class . " could not be found");
            }

            $Provider = new $class();

            if (!($Provider instanceof ResourceDeploymentProvider)) {
                throw new DeploymentException("The resource deployment Provider class " . $class . " is not a ResourceDeploymentProvider.");
            }

            self::$resourceDeploymentProvider = $Provider;
        }

        return self::$resourceDeploymentProvider;
    }


    public function getDeployedResourceUrl($resourceFilePath)
    {
        return "";
    }

    public function deployResource($resourceFilePath)
    {
        return "";
    }

    /**
     * Deploys a string of content by creating a deployment file using the simulatedFilePath
     *
     * @param $resourceContent
     * @param $simulatedFilePath
     */
    public function deployResourceContent($resourceContent, $simulatedFilePath)
    {

    }
}
