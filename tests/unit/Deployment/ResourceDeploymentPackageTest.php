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

namespace Rhubarb\Crown\Tests\unit\Deployment;

use Rhubarb\Crown\Application;
use Rhubarb\Crown\Deployment\ResourceDeploymentPackage;
use Rhubarb\Crown\Tests\Fixtures\TestCases\RhubarbTestCase;

class ResourceDeploymentPackageTest extends RhubarbTestCase
{
    public function testAllFilesDeployed()
    {
        $package = new ResourceDeploymentPackage();
        $package->resourcesToDeploy = [__FILE__, __DIR__ . "/../../../src/Deployment/Deployable.php"];
        $package->deploy();

        $cwd = Application::current()->applicationRootPath;

        $this->assertFileExists(APPLICATION_ROOT_DIR."/deployed/" . str_replace($cwd, "", __FILE__));
        $this->assertFileExists(APPLICATION_ROOT_DIR."/deployed/" . str_replace($cwd, "", __DIR__ . "/../../../src/Deployment/Deployable.php"));

        unlink(APPLICATION_ROOT_DIR."/deployed/" . str_replace($cwd, "", __FILE__));
        unlink(APPLICATION_ROOT_DIR."/deployed/" . str_replace($cwd, "", __DIR__ . "/../../../src/Deployment/Deployable.php"));
    }

    public function testDeploymentUrls()
    {
        $package = new ResourceDeploymentPackage();
        $package->resourcesToDeploy = [__FILE__, __DIR__ . "/../../../src/Deployment/Deployable.php"];
        $urls = $package->getDeployedUrls();

        $cwd = Application::current()->applicationRootPath;

        $this->assertEquals(
            [
                "/deployed" . str_replace("\\", "/", str_replace($cwd, "", __FILE__)),
                "/deployed" . str_replace(
                    "\\",
                    "/",
                    str_replace($cwd, "", realpath(__DIR__ . "/../../../src/Deployment/Deployable.php"))
                )
            ],
            $urls
        );
    }
}
