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

namespace Rhubarb\Crown\Deployment;

use Rhubarb\Crown\Application;
use Rhubarb\Crown\Exceptions\DeploymentException;

/**
 * A simple resource deployment handler that simply moves resources to a central, publicly available deployed folder.
 */
class RelocationResourceDeploymentProvider extends ResourceDeploymentProvider
{
    private $alreadyDeployed = [];

    public function getDeployedResourceUrl($resourceFilePath)
    {
        if (isset($this->alreadyDeployed[$resourceFilePath])) {
            return $this->alreadyDeployed[$resourceFilePath];
        }

        return $this->deployFile($resourceFilePath, true);
    }

    protected function deployFile($originalPath, $calculatePathOnly = false)
    {
        // Fix relative paths.
        $originalPath = realpath($originalPath);

        // Remove the current working directory from the resource path.
        $cwd = Application::current()->applicationRootPath;
        $urlPath = "/deployed" . str_replace("\\", "/", str_replace($cwd, "", $originalPath));
        $deployedPath = PUBLIC_ROOT_DIR.$urlPath;

        if (!$calculatePathOnly) {
            if (!file_exists(dirname($deployedPath))) {
                if (!mkdir(dirname($deployedPath), 0777, true)) {
                    throw new DeploymentException("The deployment folder could not be created. Check file permissions to the '" . dirname($deployedPath) . "' folder.");
                }
            }

            if (!file_exists($deployedPath) || (filemtime($originalPath) > filemtime($deployedPath))) {
                $result = @copy($originalPath, $deployedPath);

                if (!$result) {
                    throw new DeploymentException("The file $originalPath could not be deployed. Please check file permissions.");
                }
            }
        }

        return $urlPath;
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

        if (!file_exists(PUBLIC_ROOT_DIR."/deployed")) {
            if (!mkdir(PUBLIC_ROOT_DIR."/deployed", 0777, true)) {
                throw new DeploymentException("The deployment folder could not be created. Check file permissions to the 'deployed' folder.");
            }
        }

        if (!file_exists($resourceFilePath)) {
            throw new DeploymentException("The file $resourceFilePath could not be found. Please check file permissions.");
        }

        $urlPath = $this->deployFile($resourceFilePath);

        if (preg_match('/(\.js|\.css)$/', $resourceFilePath, $match)) {
            $urlPath .= '?' . filemtime($resourceFilePath) . $match[1];
        }

        $this->alreadyDeployed[$originalResourceFilePath] = $urlPath;

        return $urlPath;
    }

    public function deployResourceContent($resourceContent, $simulatedFilePath)
    {
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
