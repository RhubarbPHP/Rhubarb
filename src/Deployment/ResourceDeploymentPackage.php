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

require_once __DIR__ . "/DeploymentPackage.php";

/**
 * A deployment package specifically designed to consolidate resources into a public folder.
 */
class ResourceDeploymentPackage extends DeploymentPackage
{
    public $resourcesToDeploy = [];

    /**
     * Returns the urls with which the resources can be accessed after deployment.
     */
    public function getDeployedUrls()
    {
        $deploymentHandler = ResourceDeploymentProvider::getProvider();

        $urls = [];

        foreach ($this->resourcesToDeploy as $path) {
            $urls[] = $deploymentHandler->getDeployedResourceUrl($path);
        }

        return $urls;
    }

    protected function onDeploy()
    {
        $deploymentHandler = ResourceDeploymentProvider::getProvider();

        $urls = [];

        foreach ($this->resourcesToDeploy as $path) {
            $urls[] = $deploymentHandler->deployResource($path);
        }

        return $urls;
    }
}
