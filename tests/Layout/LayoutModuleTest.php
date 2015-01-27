<?php

namespace Gcd\Core\Layout;

/**
 *
 * @author acuthbert
 * @copyright GCD Technologies 2012
 */
use Gcd\Core\ClientSide\ResourceLoader;
use Gcd\Core\Exceptions\Handlers\ExceptionHandler;
use Gcd\Core\Layout\LayoutModule;
use Gcd\Core\Layout\ResponseFilters\LayoutFilter;
use Gcd\Core\Modelling\UnitTesting\User;
use Gcd\Core\Module;
use Gcd\Core\Request\WebRequest;
use Gcd\Core\Response\JsonResponse;
use Gcd\Core\UnitTesting\CoreTestCase;

class LayoutModuleTest extends CoreTestCase
{
	public static function setUpBeforeClass()
	{
		parent::setUpBeforeClass();

		LayoutModule::EnableLayout();
	}

	public function testLayoutPathIsRemembered()
	{
		new LayoutModule( "Gcd\Core\Layout\UnitTesting\LayoutTest2" );

		$this->assertEquals(
			"Gcd\Core\Layout\UnitTesting\LayoutTest2",
			LayoutModule::GetLayoutClassName() );
	}

	public function testAjaxRequestDisablesLayout()
	{
		LayoutModule::EnableLayout();

		new LayoutModule( "Gcd\Core\Layout\UnitTesting\LayoutTest2" );

		// Normal request
		$this->assertFalse( LayoutModule::IsDisabled() );

		$_SERVER[ "HTTP_X_REQUESTED_WITH" ] = "some-odd-request";

		new LayoutModule( "Gcd\Core\Layout\UnitTesting\LayoutTest2" );

		// Some odd request
		$this->assertFalse( LayoutModule::IsDisabled() );

		$_SERVER[ "HTTP_X_REQUESTED_WITH" ] = "XMLHttpRequest";

		new LayoutModule( "Gcd\Core\Layout\UnitTesting\LayoutTest2" );

		// Ajax request
		$this->assertTrue( LayoutModule::IsDisabled() );

		unset( $_SERVER[ "HTTP_X_REQUESTED_WITH" ] );

		LayoutModule::EnableLayout();
	}

	public function testLayoutCanBeDisabled()
	{
		LayoutModule::DisableLayout();

		$this->assertTrue( LayoutModule::IsDisabled() );

		LayoutModule::EnableLayout();

		$this->assertFalse( LayoutModule::IsDisabled() );
	}

	public function testLayoutCanBeChanged()
	{
		new LayoutModule( "Gcd\Core\Layout\UnitTesting\TestLayout" );
		LayoutModule::SetLayoutClassName( "Gcd\Core\Layout\UnitTesting\LayoutTest2" );

		$this->assertEquals( "Gcd\Core\Layout\UnitTesting\LayoutTest2", LayoutModule::GetLayoutClassName() );
	}

	public function testLayoutDoesntWorkForJsonResponse()
	{
		LayoutModule::SetLayoutClassName( "Gcd\Core\Layout\UnitTesting\TestLayout" );

		$model = new User();
		$model->Field = "Value";

		$response = new JsonResponse();
		$response->SetContent( $model );

		$layoutFilter = new LayoutFilter();
		$layoutFilter->ProcessResponse( $response );

		$this->assertEquals( $model, $response->GetContent() );
	}

	public function testLayoutWorks()
	{
		LayoutModule::SetLayoutClassName( "Gcd\Core\Layout\UnitTesting\TestLayout" );

		$request = new WebRequest();
		$request->UrlPath = "/simple/";
		$request->IsWebRequest = true;

		$response = Module::GenerateResponseForRequest( $request );

		$this->assertEquals( "TopDon't change this content - it should match the unit test.Tail", $response->GetContent() );
	}

	public function testLayoutFilterThrowsException()
	{
		LayoutModule::SetLayoutClassName( "Gcd\Core\Layout\UnitTesting\NonExistant" );

		$request = new WebRequest();
		$request->UrlPath = "/simple/";
		$request->IsWebRequest = true;

		$this->setExpectedException( "Gcd\Core\Layout\Exceptions\LayoutNotFoundException" );

		ExceptionHandler::DisableExceptionTrapping();

		Module::GenerateResponseForRequest( $request );
	}

    public function testLayoutCanBeAnonymousFunction()
    {
        LayoutModule::SetLayoutClassName( function()
        {
            return "Gcd\Core\Layout\UnitTesting\TestLayout";
        } );

        $request = new WebRequest();
        $request->UrlPath = "/simple/";
        $request->IsWebRequest = true;

        $response = Module::GenerateResponseForRequest( $request );

        $this->assertEquals( "TopDon't change this content - it should match the unit test.Tail", $response->GetContent() );
    }

	public function testHeadItems()
	{
		// Reenable this as it was disabled in the previous test.
		ExceptionHandler::EnableExceptionTrapping();

		LayoutModule::AddHeadItem( "this is some html" );
		LayoutModule::AddHeadItem( "this is more html" );

		$head = LayoutModule::GetHeadItemsAsHtml();

		$this->assertEquals( "this is some html
this is more html", $head );

	}

	public function testBodyItems()
	{
		LayoutModule::AddBodyItem( "this is some html" );
		LayoutModule::AddBodyItem( "this is more html" );

		$head = LayoutModule::GetBodyItemsAsHtml();

		$this->assertEquals( "this is some html
this is more html", $head );
	}

	public static function tearDownAfterClass()
	{
		parent::tearDownAfterClass();

		LayoutModule::SetLayoutClassName( "Gcd\Core\Layout\UnitTesting\TestLayout" );
		LayoutModule::DisableLayout();
	}
}
