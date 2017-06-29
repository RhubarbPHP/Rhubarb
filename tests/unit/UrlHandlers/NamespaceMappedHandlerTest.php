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

namespace Rhubarb\Crown\Tests\unit\UrlHandlers;

use Rhubarb\Crown\Layout\LayoutModule;
use Rhubarb\Crown\Tests\Fixtures\TestCases\RhubarbTestCase;

class NamespaceMappedHandlerTest extends RhubarbTestCase
{
    protected $request = null;

    protected function setUp()
    {
        parent::setUp();

        $this->application->context()->simulateNonCli = true;
        $this->request = $this->application->request();
        $this->request->IsWebRequest = true;

        LayoutModule::disableLayout();
    }

    public function testHandlerFindsTestObject()
    {
        $this->request->urlPath = "/nmh/ObjectA/";

        $response = $this->application->generateResponseForRequest($this->request);
        $this->assertEquals("ObjectA Response", $response->getContent());

        $this->request->urlPath = "/nmh/SubFolder/ObjectB/";

        $response = $this->application->generateResponseForRequest($this->request);
        $this->assertEquals("ObjectB Response", $response->getContent());
    }

    public function testHandlerRedirectsWhenTrailingSlashMissing()
    {
        $this->request->urlPath = "/nmh/ObjectA";

        $response = $this->application->generateResponseForRequest($this->request);

        $headers = $response->getHeaders();

        $this->assertEquals("/nmh/ObjectA/", $headers["Location"]);

        // Because our own processing stack unwraps all buffering PHP Unit will throw a
        // warning message as it's expecting buffering to be still engaged after the test
        // has finished. Here we 'pretend' to start buffering again just to get rid of the
        // warning.
        ob_start();
    }

    public function testHandlerRedirectsToIndexPage()
    {
        // This folder does contain an index so it should redirect.
        $this->request->urlPath = "/nmh/SubFolder/";

        $response = $this->application->generateResponseForRequest($this->request);

        $headers = $response->getHeaders();

        $this->assertEquals("/nmh/SubFolder/index/", $headers["Location"]);

        // Because our own processing stack unwraps all buffering PHP Unit will throw a
        // warning message as it's expecting buffering to be still engaged after the test
        // has finished. Here we 'pretend' to start buffering again just to get rid of the
        // warning.
        ob_start();
    }
}
