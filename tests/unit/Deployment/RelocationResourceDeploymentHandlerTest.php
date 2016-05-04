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
use Rhubarb\Crown\Deployment\RelocationResourceDeploymentProvider;
use Rhubarb\Crown\Exceptions\DeploymentException;
use Rhubarb\Crown\Tests\Fixtures\TestCases\RhubarbTestCase;

class RelocationResourceDeploymentHandlerTest extends RhubarbTestCase
{
    public function testUrlCreated()
    {
        $deploymentPackage = new RelocationResourceDeploymentProvider();
        $url = $deploymentPackage->getDeployedResourceUrl(__FILE__);

        $cwd = Application::current()->applicationRootPath;
        $deployedUrl = "/deployed" . str_replace("\\", "/", str_replace($cwd, "", __FILE__));

        $this->assertEquals($deployedUrl, $url);
    }

    public function testDeploymentCopiesFiles()
    {
        $cwd = Application::current()->applicationRootPath;

        $deploymentPackage = new RelocationResourceDeploymentProvider();
        $deploymentPackage->deployResource(__FILE__);

        $deployedFile = "deployed" . str_replace($cwd, "", __FILE__);

        $this->assertFileExists($deployedFile);

        unlink($deployedFile);
    }

    public function testDeploymentThrowsExceptions()
    {
        $this->setExpectedException(DeploymentException::class);

        $deploymentPackage = new RelocationResourceDeploymentProvider();
        $deploymentPackage->deployResource("a/b/c.txt");
    }

    public function testDeploymentCreateFiles()
    {
        $deploymentPackage = new RelocationResourceDeploymentProvider();
        $deploymentPackage->deployResourceContent("This is a test", "temp/folder/file.txt");

        $deployedFile = "deployed/temp/folder/file.txt";

        $this->assertFileExists($deployedFile);

        $content = file_get_contents($deployedFile);

        $this->assertEquals("This is a test", $content);

        unlink($deployedFile);
    }
}
