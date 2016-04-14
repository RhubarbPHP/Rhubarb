<?php

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

        Repository::setDefaultRepositoryClassName(Offline::class);

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
