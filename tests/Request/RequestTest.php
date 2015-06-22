<?php

namespace Rhubarb\Crown\Tests\Request;

use Rhubarb\Crown\Request\Request;

class RequestTest extends RequestTestCase
{
    protected $request = null;

    protected $testEnvKey = 'REQUEST_TEST';
    protected $testEnvValue = 42;

    protected function setUp()
    {
        parent::setUp();

        $_ENV[$this->testEnvKey] = 42;

        $this->request = new TestRequest();
    }

    protected function tearDown()
    {
        $this->request = null;

        unset($_ENV[$this->testEnvKey]);

        parent::tearDown();
    }

    public function testInstantiation()
    {
        $this->assertInstanceOf('\Rhubarb\Crown\Tests\Request\TestRequest', $this->request);
    }

    public function testMagicOriginalGetter()
    {
        $this->assertEquals(42, $this->request->AnotherTestProperty);

        $this->request->AnotherTestProperty = 1337;
        $this->assertEquals(1337, $this->request->AnotherTestProperty);

        $this->assertEquals(42, $this->request->OriginalAnotherTestProperty);
    }

    /**
     * @expectedException \Rhubarb\Crown\Exceptions\AttemptToModifyReadOnlyPropertyException
     */
    public function testMagicOriginalSetterException()
    {
        $this->request->OriginalATestProperty = 'Two';
    }

    public function testStaticData()
    {
        // check test ENV - currently == 42 from setUp()
        $this->assertEquals($this->testEnvValue, $this->request->EnvData[$this->testEnvKey]);
        $this->assertEquals($this->testEnvValue, $this->request->OriginalEnvData[$this->testEnvKey]);
        $this->assertEquals($this->testEnvValue, $this->request->Env($this->testEnvKey));

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
class TestRequest extends Request
{
    public function initialise()
    {
        $this->ATestProperty = 'One';
        $this->AnotherTestProperty = 42;
        $this->YetAnotherTestProperty = [5, 'Green', 'Speckled', 'Frogs'];
    }
}
