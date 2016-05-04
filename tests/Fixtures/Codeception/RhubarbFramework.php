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

namespace Rhubarb\Crown\Tests\Fixtures\Codeception;

use Codeception\Lib\Framework;
use Codeception\TestCase;
use PHPUnit_Framework_Assert;

class RhubarbFramework extends Framework
{
    public function _initialize()
    {
        $this->client = new RhubarbConnector();
    }

    public function seeHeader($header, $headerValue = null)
    {
        $response = $this->client->getInternalResponse();
        PHPUnit_Framework_Assert::assertContains($header, $response->getHeaders());

        if ($headerValue != null) {
            PHPUnit_Framework_Assert::assertEquals($headerValue, $response->getHeader($header));
        }
    }
}
