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

namespace Rhubarb\Crown\Settings;

use Rhubarb\Crown\Settings;

/**
 * A container for settings that generically affect all web hosted projects.
 */
class WebsiteSettings extends Settings
{
    /**
     * The URL root for the website.
     *
     * This should be the full absolute URL including scheme (http/https). It should NOT end in a trailing
     * slash as this is very often concatenated with relative URLs paths to form complete URLs.
     *
     * Note that this is a much better way of creating URLs than WebRequest::createUrl() as this setting
     * will be available to CLI environments like custard commands whereas WebRequest will not.
     *
     * Its normal to see this setting defined in the application's site.config.php.
     *
     * @var string
     */
    public $absoluteWebsiteUrl = "";
}