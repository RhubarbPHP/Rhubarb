<?php

namespace Rhubarb\Crown\Layout;

/**
 *
 * @author acuthbert
 * @copyright GCD Technologies 2012
 */
use Rhubarb\Crown\ClientSide\ResourceLoader;
use Rhubarb\Crown\Exceptions\Handlers\ExceptionHandler;
use Rhubarb\Crown\Layout\LayoutModule;
use Rhubarb\Crown\Layout\ResponseFilters\LayoutFilter;
use Rhubarb\Crown\Modelling\UnitTesting\User;
use Rhubarb\Crown\Module;
use Rhubarb\Crown\Request\WebRequest;
use Rhubarb\Crown\Response\JsonResponse;
use Rhubarb\Crown\UnitTesting\RhubarbTestCase;

class LayoutModuleTest extends RhubarbTestCase
{
	public static function setUpBeforeClass()
	{
		parent::setUpBeforeClass();

		LayoutModule::EnableLayout();
	}

	public function testLayoutPathIsRemembered()
	{
		new LayoutModule( "Rhubarb\Crown\Layout\UnitTesting\LayoutTest2" );

		$this->assertEquals(
			"Rhubarb\Crown\Layout\UnitTesting\LayoutTest2",
			LayoutModule::GetLayoutClassName() );
	}

	public function testAjaxRequestDisablesLayout()
	{
		LayoutModule::EnableLayout();

		new LayoutModule( "Rhubarb\Crown\Layout\UnitTesting\LayoutTest2" );

		// Normal request
		$this->assertFalse( LayoutModule::IsDisabled() );

		$_SERVER[ "HTTP_X_REQUESTED_WITH" ] = "some-odd-request";

		new LayoutModule( "Rhubarb\Crown\Layout\UnitTesting\LayoutTest2" );

		// Some odd request
		$this->assertFalse( LayoutModule::IsDisabled() );

		$_SERVER[ "HTTP_X_REQUESTED_WITH" ] = "XMLHttpRequest";

		new LayoutModule( "Rhubarb\Crown\Layout\UnitTesting\LayoutTest2" );

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
		new LayoutModule( "Rhubarb\Crown\Layout\UnitTesting\TestLayout" );
		LayoutModule::SetLayoutClassName( "Rhubarb\Crown\Layout\UnitTesting\LayoutTest2" );

		$this->assertEquals( "Rhubarb\Crown\Layout\UnitTesting\LayoutTest2", LayoutModule::GetLayoutClassName() );
	}

	public function testLayoutDoesntWorkForJsonResponse()
	{
		LayoutModule::SetLayoutClassName( "Rhubarb\Crown\Layout\UnitTesting\TestLayout" );

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
		LayoutModule::SetLayoutClassName( "Rhubarb\Crown\Layout\UnitTesting\TestLayout" );

		$request = new WebRequest();
		$request->UrlPath = "/simple/";
		$request->IsWebRequest = true;

		$response = Module::GenerateResponseForRequest( $request );

		$this->assertEquals( "TopDon't change this content - it should match the unit test.Tail", $response->GetContent() );
	}

	public function testLayoutFilterThrowsException()
	{
		LayoutModule::SetLayoutClassName( "Rhubarb\Crown\Layout\UnitTesting\NonExistant" );

		$request = new WebRequest();
		$request->UrlPath = "/simple/";
		$request->IsWebRequest = true;

		$this->setExpectedException( "Rhubarb\Crown\Layout\Exceptions\LayoutNotFoundException" );

		ExceptionHandler::DisableExceptionTrapping();

		Module::GenerateResponseForRequest( $request );
	}

    public function testLayoutCanBeAnonymousFunction()
    {
        LayoutModule::SetLayoutClassName( function()
        {
            return "Rhubarb\Crown\Layout\UnitTesting\TestLayout";
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

		LayoutModule::SetLayoutClassName( "Rhubarb\Crown\Layout\UnitTesting\TestLayout" );
		LayoutModule::DisableLayout();
	}
}
