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

namespace Rhubarb\Crown\Scripts;

class ProjectTemplates
{
    public static function createMinimumProject()
    {
        mkdir("settings");
        file_put_contents("settings/app.config.php", '<?php

namespace YourNamespace;

use Rhubarb\Crown\Layout\LayoutModule;
use Rhubarb\Crown\Module;
use Rhubarb\Crown\UrlHandlers\ClassMappedUrlHandler;

class WebsiteApp extends Module
{
    protected function registerUrlHandlers()
    {
        parent::registerUrlHandlers();

        $this->addUrlHandlers(
            [
                "/" => new ClassMappedUrlHandler(__NAMESPACE__ . "\Index");
            ]
        );
    }

    protected function initialise()
    {
        // Put application initialisation code here.
    }
}

Module::registerModule(new LayoutModule(__NAMESPACE__ . "\\Layouts\\DefaultLayout"));
Module::registerModule(new WebsiteApp());
');

        mkdir("src");
        mkdir("tests");

        file_put_contents("src/Layouts/DefaultLayout.php", '
<?php

namespace YourNamespace\Layouts;

use Rhubarb\Crown\Layout\Layout;

class DefaultLayout extends Layout
{
    protected function printLayout($content)
    {
        ?><html>
<head>
</head>
<body>
<?php
        parent::printLayout($content);
?>
</body>
</html><?php
    }
}
');

    }
}
