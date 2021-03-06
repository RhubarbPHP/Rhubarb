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

namespace Rhubarb\Crown\Tests\Fixtures\Modules;

use Rhubarb\Crown\Sendables\Email\EmailProvider;
use Rhubarb\Crown\Layout\LayoutModule;
use Rhubarb\Crown\LoginProviders\UrlHandlers\ValidateLoginUrlHandler;
use Rhubarb\Crown\Module;
use Rhubarb\Crown\Tests\Fixtures\Layout\TestLayout;
use Rhubarb\Crown\Tests\Fixtures\LoginProviders\UnitTestingLoginProvider;
use Rhubarb\Crown\Tests\Fixtures\SimpleContent;
use Rhubarb\Crown\Tests\Fixtures\UnitTestingEmailProvider;
use Rhubarb\Crown\Tests\Fixtures\UrlHandlers\UnitTestComputedUrlHandler;
use Rhubarb\Crown\UrlHandlers\ClassMappedUrlHandler;
use Rhubarb\Crown\UrlHandlers\NamespaceMappedUrlHandler;
use Rhubarb\Crown\UrlHandlers\StaticResourceUrlHandler;
use Rhubarb\Stem\Repositories\Offline\Offline;
use Rhubarb\Stem\Repositories\Repository;

class UnitTestingModule extends Module
{
    public function getModules()
    {
        return [ new LayoutModule(TestLayout::class) ];
    }

    protected function initialise()
    {
        require_once __DIR__ . '/../../unit/UrlHandlers/UrlHandlerTestUnitTest.php';

        parent::initialise();

        $login = new ValidateLoginUrlHandler(UnitTestingLoginProvider::singleton(), "/login/index");
        $login->SetPriority(20);

        $this->addUrlHandlers(
            ["/cant/be/here" => $login]
        );

        $login = new ValidateLoginUrlHandler(UnitTestingLoginProvider::singleton(), "/defo/not/here/login/index/",
            [
                "login/index/" => new ClassMappedUrlHandler(SimpleContent::class)
                // We have to give it something to render!
            ]);

        $login->setPriority(20);

        $this->addUrlHandlers(
            ["/defo/not/here/" => $login]
        );

        $this->addUrlHandlers(
            [
                new UnitTestComputedUrlHandler()
            ]
        );

        $this->addUrlHandlers(
            [
                "/" => new ClassMappedUrlHandler(SimpleContent::class,
                    [
                        "nmh/" => new NamespaceMappedUrlHandler('Rhubarb\Crown\Tests\Fixtures\UrlHandlers\NamespaceMappedHandlerTests'),
                        "simple/" => new ClassMappedUrlHandler(SimpleContent::class),
                        "files/" => new StaticResourceUrlHandler(__DIR__ . "/../UrlHandlers/")
                    ])
            ]
        );

        $this->addUrlHandlers("/priority-test/",
            new ValidateLoginUrlHandler(UnitTestingLoginProvider::singleton(), "/login/index"));

        $test = new NamespaceMappedUrlHandler('Rhubarb\Leaf\Presenters');
        $test->setPriority(100);

        $this->addUrlHandlers("/priority-test/", $test);

        EmailProvider::setProviderClassName(UnitTestingEmailProvider::class);
    }
}
