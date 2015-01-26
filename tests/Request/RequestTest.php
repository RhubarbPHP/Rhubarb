<?php

namespace Gcd\Tests;

/**
 * @author marramgrass
 * @copyright GCD Technologies 2012
 */
class RequestTest extends \Gcd\Core\Request\UnitTesting\RequestTestCase
{
	protected $_request = null;

    protected $testEnvKey = 'REQUEST_TEST';
    protected $testEnvValue = 42;

	protected function setUp()
	{
		parent::setUp();

		$_ENV[ $this->testEnvKey ] = 42;

        $this->_request = new TestRequest();
	}

	protected function tearDown()
	{
		$this->_request = null;

		unset( $_ENV[ $this->testEnvKey ] );

		parent::tearDown();
	}

	public function testInstantiation()
	{
		$this->assertInstanceOf( '\Gcd\Tests\TestRequest', $this->_request );
	}

	public function testMagicOriginalGetter()
	{
		$this->assertEquals( 42, $this->_request->AnotherTestProperty );

		$this->_request->AnotherTestProperty = 1337;
		$this->assertEquals( 1337, $this->_request->AnotherTestProperty );

		$this->assertEquals( 42, $this->_request->OriginalAnotherTestProperty );
	}

	/**
	 * @expectedException \Gcd\Core\Exceptions\AttemptToModifyReadOnlyPropertyException
	 */
	public function testMagicOriginalSetterException()
	{
        $this->_request->OriginalATestProperty = 'Two';
	}

    public function testStaticData()
    {
        // check test ENV - currently == 42 from setUp()
        $this->assertEquals( $this->testEnvValue, $this->_request->EnvData[ $this->testEnvKey ] );
        $this->assertEquals( $this->testEnvValue, $this->_request->OriginalEnvData[ $this->testEnvKey ] );
		$this->assertEquals( $this->testEnvValue, $this->_request->Env( $this->testEnvKey ) );

	    /*
	     * Static data does now reparse the global arrays each time a request is created.
	     *
        $_ENV[ $this->testEnvKey ] = 43;

        // test ENV value should be unchanged

        $differentRequest = new TestRequest();
        $this->assertEquals( $this->testEnvValue, $differentRequest->EnvData[ $this->testEnvKey ] );
        $this->assertEquals( $this->testEnvValue, $differentRequest->OriginalEnvData[ $this->testEnvKey] );
		$this->assertEquals( $this->testEnvValue, $differentRequest->Env( $this->testEnvKey ) );
	    */
    }
}

// Request is an abstract class, so needs a concrete implementation for testing
class TestRequest extends \Gcd\Core\Request\Request
{
	public function Initialise()
	{
		$this->ATestProperty = 'One';
		$this->AnotherTestProperty = 42;
		$this->YetAnotherTestProperty = [ 5, 'Green', 'Speckled', 'Frogs' ];
	}
}
