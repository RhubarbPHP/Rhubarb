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

namespace Rhubarb\Crown\Layout\ResponseFilters;

require_once __DIR__ . "/../../ResponseFilters/ResponseFilter.php";
require_once __DIR__ . "/../../Response/HtmlResponse.php";

use Rhubarb\Crown\Layout\Exceptions\LayoutNotFoundException;
use Rhubarb\Crown\Layout\LayoutModule;
use Rhubarb\Crown\Response\HtmlResponse;
use Rhubarb\Crown\ResponseFilters\ResponseFilter;

/**
 * The response filter that surrounds the response with the HTML provided from a layout template.
 */
class LayoutFilter extends ResponseFilter
{
    public function processResponse($response)
    {
        if (LayoutModule::isDisabled()) {
            return $response;
        }

        if (!($response instanceof HtmlResponse)) {
            return $response;
        }

        $className = LayoutModule::getLayoutClassName();

        if (!class_exists($className)) {
            throw new LayoutNotFoundException($className);
        }

        $layout = new $className();

        $layout->processResponse($response);

        return $response;
    }
}
