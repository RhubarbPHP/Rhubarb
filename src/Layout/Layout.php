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

namespace Rhubarb\Crown\Layout;

use Rhubarb\Crown\Response\Response;

/**
 * Base class for layouts.
 *
 * Sub classes should override PrintLayout and simply print the HTML for the layout. The inner content
 * of the response is passed in the $content parameter
 */
class Layout
{
    protected function printLayout($content)
    {
        print $content;
    }

    final public function processResponse(Response $response)
    {
        ob_start();

        $this->printLayout($response->getContent());

        $fullHtml = ob_get_clean();

        $response->setContent($fullHtml);
    }
}
