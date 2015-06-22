<?php

namespace Rhubarb\Crown\Tests\Request;

use Rhubarb\Crown\Request\WebRequest;
use Rhubarb\Crown\Tests\RhubarbTestCase;

/**
 * @author    marramgrass
 * @copyright GCD Technologies 2012
 */
class RequestTestCase extends RhubarbTestCase
{
    protected $stashSuperglobals = [];

    protected function setUp()
    {
        $this->stashSuperglobals['env'] = isset($_ENV) ? $_ENV : [];
        $this->stashSuperglobals['server'] = isset($_SERVER) ? $_SERVER : [];
        $this->stashSuperglobals['get'] = isset($_GET) ? $_GET : [];
        $this->stashSuperglobals['post'] = isset($_POST) ? $_POST : [];
        $this->stashSuperglobals['files'] = isset($_FILES) ? $_FILES : [];
        $this->stashSuperglobals['cookie'] = isset($_COOKIE) ? $_COOKIE : [];
        $this->stashSuperglobals['session'] = isset($_SESSION) ? $_SESSION : [];
        $this->stashSuperglobals['request'] = isset($_REQUEST) ? $_REQUEST : [];
    }

    protected function tearDown()
    {
        $_ENV = $this->stashSuperglobals['env'];
        $_SERVER = $this->stashSuperglobals['server'];
        $_GET = $this->stashSuperglobals['get'];
        $_POST = $this->stashSuperglobals['post'];
        $_FILES = $this->stashSuperglobals['files'];
        $_COOKIE = $this->stashSuperglobals['cookie'];
        $_SESSION = $this->stashSuperglobals['session'];
        $_REQUEST = $this->stashSuperglobals['request'];

        $this->stashSuperglobals = [];

        // WebRequest as Request is an abstract class
        WebRequest::ResetRequest();
    }

}
