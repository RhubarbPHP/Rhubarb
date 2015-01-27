<?php

namespace Rhubarb\Crown\Request\UnitTesting;

/**
 * @author    marramgrass
 * @copyright GCD Technologies 2012
 */
class RequestTestCase extends \Rhubarb\Crown\UnitTesting\RhubarbTestCase
{
	protected $_stashSuperglobals = [];

	protected function setUp()
	{
		$this->_stashSuperglobals[ 'env' ] = isset( $_ENV ) ? $_ENV : [];
		$this->_stashSuperglobals[ 'server' ] = isset( $_SERVER ) ? $_SERVER : [];
		$this->_stashSuperglobals[ 'get' ] = isset( $_GET ) ? $_GET : [];
		$this->_stashSuperglobals[ 'post' ] = isset( $_POST ) ? $_POST : [];
		$this->_stashSuperglobals[ 'files' ] = isset( $_FILES ) ? $_FILES : [];
		$this->_stashSuperglobals[ 'cookie' ] = isset( $_COOKIE ) ? $_COOKIE : [];
		$this->_stashSuperglobals[ 'session' ] = isset( $_SESSION ) ? $_SESSION : [];
		$this->_stashSuperglobals[ 'request' ] = isset( $_REQUEST ) ? $_REQUEST : [];
	}

	protected function tearDown()
	{
		$_ENV = $this->_stashSuperglobals[ 'env' ];
		$_SERVER = $this->_stashSuperglobals[ 'server' ];
		$_GET = $this->_stashSuperglobals[ 'get' ];
		$_POST = $this->_stashSuperglobals[ 'post' ];
		$_FILES = $this->_stashSuperglobals[ 'files' ];
		$_COOKIE = $this->_stashSuperglobals[ 'cookie' ];
		$_SESSION = $this->_stashSuperglobals[ 'session' ];
		$_REQUEST = $this->_stashSuperglobals[ 'request' ];

		$this->_stashSuperglobals = [];

		// WebRequest as Request is an abstract class
		\Rhubarb\Crown\Request\WebRequest::ResetRequest();
	}

}
