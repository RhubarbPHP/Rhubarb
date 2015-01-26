<?php

namespace Rhubarb\Crown\Tests;

use Rhubarb\Crown\Context;
use Rhubarb\Crown\CoreModule;
use Rhubarb\Crown\Encryption\EncryptionProvider;
use Rhubarb\Crown\Encryption\HashProvider;
use Rhubarb\Crown\Integration\Email\EmailProvider;
use Rhubarb\Crown\Integration\IntegrationModule;
use Rhubarb\Crown\Layout\LayoutModule;
use Rhubarb\Crown\LoginProviders\ValidateLoginUrlHandler;
use Rhubarb\Crown\Modelling\Models\Model;
use Rhubarb\Crown\Modelling\Repositories\Repository;
use Rhubarb\Crown\Modelling\Schema\SolutionSchema;
use Rhubarb\Crown\Module;
use Rhubarb\Crown\Mvp\MvpModule;
use Rhubarb\Crown\Patterns\PatternsModule;
use Rhubarb\Crown\RestApi\Authentication\AuthenticationProvider;
use Rhubarb\Crown\Scaffolds\AuthenticationWithRoles\AuthenticationWithRolesModule;
use Rhubarb\Crown\Scaffolds\NavigationMenu\NavigationMenuModule;
use Rhubarb\Crown\Scaffolds\TokenBasedRestApi\TokenBasedRestApiModule;
use Rhubarb\Crown\StaticResource\UrlHandlers\StaticResourceUrlHandler;
use Rhubarb\Crown\UrlHandlers\ClassMappedUrlHandler;
use Rhubarb\Crown\UrlHandlers\NamespaceMappedUrlHandler;

/**
 * This base class adds basic setup and teardown for unit testing within the Core
 *
 * @author acuthbert
 * @copyright GCD Technologies 2012
 */
class RhubarbTestCase extends \PHPUnit_Framework_TestCase
{
    protected static $_rolesModule;

    public static function setUpBeforeClass()
    {
        Module::RegisterModule(new UnitTestingModule());
        Module::InitialiseModules();

        $context = new \Rhubarb\Crown\Context();
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
                "login/index/" => new ClassMappedUrlHandler("\Rhubarb\Crown\Mvp\Presenters\Simple")
                // We have to give it something to render!
            ]);

        $login->SetPriority(20);

        $this->AddUrlHandlers(
            ["/defo/not/here/" => $login]
        );

        $this->AddUrlHandlers(
            [
                "/" => new NamespaceMappedUrlHandler("Rhubarb\Crown\Mvp\Presenters",
                    [
                        "nmh/" => new NamespaceMappedUrlHandler("Rhubarb\Crown\UnitTesting\NamespaceMappedHandlerTests"),
                        "files/" => new StaticResourceUrlHandler(__DIR__ . "/../../StaticResource/UnitTesting/")
                    ])
            ]);

        $this->AddUrlHandlers("/priority-test/",
            new ValidateLoginUrlHandler(new UnitTestingLoginProvider(), "/login/index"));

        $test = new NamespaceMappedUrlHandler("Rhubarb\Crown\Mvp\Presenters");
        $test->SetPriority(100);

        $this->AddUrlHandlers("/priority-test/", $test);

        $this->AddClassPath(__DIR__ . "/../../Mvp/UnitTesting/Presenters", "Rhubarb\Crown\Mvp\Presenters");
        $this->AddClassPath(__DIR__ . "/../../Mvp/UnitTesting/Presenters", "Rhubarb\Crown\Mvp\Views");

    }
}