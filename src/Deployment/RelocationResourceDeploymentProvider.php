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

require_once __DIR__ . "/ResourceDeploymentProvider.php";

use Rhubarb\Crown\Exceptions\DeploymentException;

/**
 * A simple resource deployment handler that simply moves resources to a central, publically available deployed folder.
 */
class RelocationResourceDeploymentProvider extends ResourceDeploymentProvider
{
    private $alreadyDeployed = [];

    public function getDeployedResourceUrl($resourceFilePath)
    {
        if (isset($this->alreadyDeployed[$resourceFilePath])) {
            return $this->alreadyDeployed[$resourceFilePath];
        }

        // Remove the current working directory from the resource path.
        $cwd = APPLICATION_ROOT_DIR;

        $url = "/deployed/" . str_replace("\\", "/", str_replace($cwd, "", realpath($resourceFilePath)));

        $this->alreadyDeployed[$resourceFilePath] = $url . '?' . filemtime($resourceFilePath);

        return $url;
    }

    public function deployResource($resourceFilePath)
    {
        if (isset($this->alreadyDeployed[$resourceFilePath])) {
            return $this->alreadyDeployed[$resourceFilePath];
        }

        $originalResourceFilePath = $resourceFilePath;

        $resourceFilePath = realpath($resourceFilePath);

        if ($resourceFilePath === false) {
            throw new DeploymentException("The file $originalResourceFilePath could not be found. Please check the file exists.");
        }

        if (!file_exists("deployed")) {
            if (!mkdir("deployed", 0777, true)) {
                throw new DeploymentException("The deployment folder could not be created. Check file permissions to the 'deployed' folder.");
            }
        }

        if (!file_exists($resourceFilePath)) {
            throw new DeploymentException("The file $resourceFilePath could not be found. Please check file permissions.");
        }

        // Remove the current working directory from the resource path.
        $cwd = APPLICATION_ROOT_DIR;

        $urlPath = "/deployed" . str_replace("\\", "/", str_replace($cwd, "", $resourceFilePath));
        $localPath = $cwd . $urlPath;

        if (!file_exists(dirname($localPath))) {
            if (!mkdir(dirname($localPath), 0777, true)) {
                throw new DeploymentException("The deployment folder could not be created. Check file permissions to the '" . dirname($localPath) . "' folder.");
            }
        }

        $result = @copy($resourceFilePath, $localPath);

        if (!$result) {
            throw new DeploymentException("The file $resourceFilePath could not be deployed. Please check file permissions.");
        }

        $this->alreadyDeployed[$originalResourceFilePath] = $urlPath;

        return $urlPath;
    }

    public function deployResourceContent($resourceContent, $simulatedFilePath)
    {
        if (!file_exists("deployed")) {
            if (!mkdir("deployed", 0777, true)) {
                throw new DeploymentException("The deployment folder could not be created. Check file permissions to the 'deployed' folder.");
            }
        }

        $urlPath = "deployed/" . $simulatedFilePath;

        if (!file_exists(dirname($urlPath))) {
            if (!mkdir(dirname($urlPath), 0777, true)) {
                throw new DeploymentException("The deployment folder could not be created. Check file permissions to the '" . dirname($urlPath) . "' folder.");
            }
        }

        $result = file_put_contents($urlPath, $resourceContent);

        if (!$result) {
            throw new DeploymentException("The file $urlPath could not be created. Please check file permissions.");
        }

        return "/" . $urlPath;
    }
}
