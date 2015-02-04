<?php

namespace Rhubarb\Crown\Tests;

use Rhubarb\Crown\Context;
use Rhubarb\Crown\Layout\LayoutModule;
use Rhubarb\Crown\LoginProviders\UrlHandlers\ValidateLoginUrlHandler;
use Rhubarb\Crown\Module;
use Rhubarb\Crown\Tests\LoginProviders\UnitTestingLoginProvider;
use Rhubarb\Crown\UrlHandlers\ClassMappedUrlHandler;
use Rhubarb\Crown\UrlHandlers\StaticResourceUrlHandler;
use Rhubarb\Crown\UrlHandlers\NamespaceMappedUrlHandler;

/**
 * This base class adds basic setup and teardown for unit testing within the Core
 *
 * @author acuthbert
 * @copyright GCD Technologies 2012
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

        Module::registerModule( new LayoutModule( '\Rhubarb\Crown\Tests\Layout\TestLayout' ) );
    }

    protected function Initialise()
    {
        parent::Initialise();

        $login = new ValidateLoginUrlHandler(new UnitTestingLoginProvider(), "/login/index");
        $login->SetPriority(20);

        $this->AddUrlHandlers(
            ["/cant/be/here" => $login]
        );

        $login = new ValidateLoginUrlHandler(new UnitTestingLoginProvider(), "/defo/not/here/login/index/",
            [
                "login/index/" => new ClassMappedUrlHandler("\Rhubarb\Crown\Tests\Fixtures\SimpleContent")
                // We have to give it something to render!
            ]);

        $login->SetPriority(20);

        $this->AddUrlHandlers(
            ["/defo/not/here/" => $login]
        );

        $this->AddUrlHandlers(
            [
                "/" => new ClassMappedUrlHandler( "\Rhubarb\Crown\Tests\Fixtures\SimpleContent",
                    [
                        "nmh/" => new NamespaceMappedUrlHandler("Rhubarb\Crown\Tests\UrlHandlers\Fixtures\NamespaceMappedHandlerTests"),
                        "simple/" => new ClassMappedUrlHandler( "\Rhubarb\Crown\Tests\Fixtures\SimpleContent" ),
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

        $test = new NamespaceMappedUrlHandler("Rhubarb\Leaf\Presenters");
        $test->SetPriority(100);

        $this->AddUrlHandlers("/priority-test/", $test);

    }
}