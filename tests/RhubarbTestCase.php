<?php

namespace Rhubarb\Crown\Tests;

use Rhubarb\Crown\Context;
use Rhubarb\Crown\Email\EmailProvider;
use Rhubarb\Crown\Layout\LayoutModule;
use Rhubarb\Crown\LoginProviders\UrlHandlers\ValidateLoginUrlHandler;
use Rhubarb\Crown\Module;
use Rhubarb\Crown\Tests\Fixtures\UnitTestingEmailProvider;
use Rhubarb\Crown\Tests\LoginProviders\UnitTestingLoginProvider;
use Rhubarb\Crown\Tests\UrlHandlers\UnitTestComputedUrlHandler;
use Rhubarb\Crown\Tests\UrlHandlers\NamespaceMappedHandlerTest;
use Rhubarb\Crown\UrlHandlers\ClassMappedUrlHandler;
use Rhubarb\Crown\UrlHandlers\NamespaceMappedUrlHandler;
use Rhubarb\Crown\UrlHandlers\StaticResourceUrlHandler;
use Rhubarb\Stem\Repositories\Offline\Offline;
use Rhubarb\Stem\Repositories\Repository;

/**
 * This base class adds basic setup and teardown for unit testing within Rhubarb's core
 */
class RhubarbTestCase extends \PHPUnit_Framework_TestCase
{
    protected static $rolesModule;

    public static function setUpBeforeClass()
    {
        Module::RegisterModule(new UnitTestingModule());
        Module::InitialiseModules();

        $context = new Context();
        $context->UnitTesting = true;
        $context->SimulateNonCli = false;

        $request = Context::CurrentRequest();
        $request->Reset();
    }

    public static function tearDownAfterClass()
    {
        Module::clearModules();

        parent::tearDownAfterClass();
    }
}

class UnitTestingModule extends Module
{
    protected function registerDependantModules()
    {
        parent::registerDependantModules();

        Module::registerModule(new LayoutModule(Layout\TestLayout::class));
    }

    protected function Initialise()
    {
        parent::Initialise();

        Repository::setDefaultRepositoryClassName(Offline::class);

        $login = new ValidateLoginUrlHandler(new UnitTestingLoginProvider(), "/login/index");
        $login->SetPriority(20);

        $this->AddUrlHandlers(
            ["/cant/be/here" => $login]
        );

        $login = new ValidateLoginUrlHandler(new UnitTestingLoginProvider(), "/defo/not/here/login/index/",
            [
                "login/index/" => new ClassMappedUrlHandler(Fixtures\SimpleContent::class)
                // We have to give it something to render!
            ]);

        $login->SetPriority(20);

        $this->AddUrlHandlers(
            ["/defo/not/here/" => $login]
        );

        $this->addUrlHandlers(
            [
                new UnitTestComputedUrlHandler()
            ]
        );

        $this->AddUrlHandlers(
            [
                "/" => new ClassMappedUrlHandler(Fixtures\SimpleContent::class,
                    [
                        "nmh/" => new NamespaceMappedUrlHandler(NamespaceMappedHandlerTest::class),
                        "simple/" => new ClassMappedUrlHandler(Fixtures\SimpleContent::class),
                        "files/" => new StaticResourceUrlHandler(__DIR__ . "/UrlHandlers/Fixtures/")
                    ])
            ]
        );

        /*
        $this->AddUrlHandlers(
            [
                "/" => new NamespaceMappedUrlHandler("Rhubarb\Leaf\Presenters",
                    [
                        "nmh/" => new NamespaceMappedUrlHandler("Rhubarb\Crown\Tests\NamespaceMappedHandlerTests"),
                        "files/" => new StaticResourceUrlHandler(__DIR__ . "/UrlHandlers/Fixtures/")
                    ])
            ]);
        */

        $this->AddUrlHandlers("/priority-test/",
            new ValidateLoginUrlHandler(new UnitTestingLoginProvider(), "/login/index"));

        $test = new NamespaceMappedUrlHandler('Rhubarb\Leaf\Presenters');
        $test->SetPriority(100);

        $this->AddUrlHandlers("/priority-test/", $test);

        EmailProvider::setDefaultEmailProviderClassName(UnitTestingEmailProvider::class);

    }
}