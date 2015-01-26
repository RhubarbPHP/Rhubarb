<?php

namespace Gcd\Tests;
use Gcd\Core\Modelling\ModelState;
use Gcd\Core\Modelling\UnitTesting\Example;
use Gcd\Core\UnitTesting\CoreTestCase;

/**
 *
 * @author acuthbert
 * @copyright GCD Technologies 2012
 */
class ModelStateTest extends CoreTestCase
{
	public function testUnsetValueReturnsNull()
	{
		$model = new ModelState();

		$this->assertNull( $model->UnsetProperty );
	}

	public function testSetValueReturnsCorrectValueAndIsNotNull()
	{
		$model = new ModelState();
		$model->NewProperty = "abc";

		$this->assertNotNull( $model->NewProperty );
		$this->assertEquals( "abc", $model->NewProperty );
	}

	public function testKnowsModelIsDirty()
	{
		$model = new ModelState();
		$model->NewProperty = "abc";

		$this->assertTrue( $model->HasChanged() );

		$model->TakeChangeSnapshot();

		$this->assertFalse( $model->HasChanged() );

		$model->NewProperty = "abc";

		$this->assertFalse( $model->HasChanged() );

		$model->NewProperty = "123";

		$this->assertTrue( $model->HasChanged() );

		$model->TakeChangeSnapshot();

		unset( $model->NewProperty );

		$this->assertTrue( $model->HasChanged() );
	}

	public function testModelDirtynessIsNotTypeStrict()
	{
		$model = new ModelState();
		$model->NewProperty = "123";

		$model->TakeChangeSnapshot();
		$model->NewProperty = 123;

		$this->assertFalse( $model->HasChanged() );
	}

	public function testChangeSnapshotResetsDirtyness()
	{
		$model = new ModelState();
		$model->NewProperty = "123";

		$this->assertTrue( $model->HasChanged() );

		$model->TakeChangeSnapshot();

		$this->assertFalse( $model->HasChanged() );
	}

	public function testChangeSnapshotCalculatesDifference()
	{
		$model = new ModelState();

		$this->assertFalse( $model->HasPropertyChanged( "NewProperty" ) );

		$model->NewProperty = "123";

		$this->assertTrue( $model->HasPropertyChanged( "NewProperty" ) );

		$model->TakeChangeSnapshot();

		$this->assertFalse( $model->HasPropertyChanged( "NewProperty" ) );

		$model->TakeChangeSnapshot();

		$model->NewProperty = "123";

		$this->assertFalse( $model->HasPropertyChanged( "NewProperty" ) );

		$model->TakeChangeSnapshot();

		$model->NewProperty = "12323";

		$this->assertTrue( $model->HasPropertyChanged( "NewProperty" ) );
	}

	public function testGetModelChanges()
	{
		$model = new ModelState();

		$this->assertEquals( [], $model->GetModelChanges() );

		$model->A = 1;
		$model->B = 2;

		$this->assertEquals( [ "A" => 1, "B" => 2 ], $model->GetModelChanges() );

		$model->TakeChangeSnapshot();

		$this->assertEquals( [], $model->GetModelChanges() );

		$model->A = 2;

		$this->assertEquals( [ "A" => 2 ], $model->GetModelChanges() );
	}

	public function testSupportsGetters()
	{
		$model = new TestModel();

		$this->assertEquals( "TestValue", $model->MyTestValue );
	}

	public function testSupportsSetters()
	{
		$model = new TestModel();
		$model->Name = "Andrew Cuthbert";

		$this->assertEquals( "ANDREW CUTHBERT", $model->Name );
	}

	public function testArrayAccess()
	{
		$model = new TestModel();
		$model->Name = "Andrew Cuthbert";

		$this->assertEquals( "ANDREW CUTHBERT", $model[ "Name" ] );

		$model[ "Name" ] = "John Smith";

		$this->assertEquals( "JOHN SMITH", $model->Name );
	}


	public function testRawDataIsExported()
	{
		$test = new TestModel();
		$test->Forename = "Andrew";
		$test->Surname = "Cuthbert";

		$data = $test->ExportRawData();

		$keys = array_keys( $data );

		$this->assertCount( 2, $data );
		$this->assertEquals( "Andrew", $data[ "Forename" ] );
		$this->assertEquals( "Surname", $keys[1] );
	}

	public function testAllDataIsExported()
	{
		$test = new TestModel();
		$test->Forename = "Andrew";
		$test->Surname = "Cuthbert";
		$test->Mangled = "Mangled";

		$data = $test->ExportData();

		$keys = array_keys( $data );

		$this->assertCount( 4, $data );
		$this->assertEquals( "Andrew", $data[ "Forename" ] );
		$this->assertEquals( "Surname", $keys[1] );
		$this->assertEquals( "delgnaM", $data[ "Mangled" ] );
		$this->assertEquals( "TestValue", $data[ "MyTestValue" ] );
	}

	public function testModelImportsRawData()
	{
		$test = new TestModel();
		$test->Forename = "Andrew";
		$test->Town = "Belfast";

		$data = array(
			"Forename" => "John",
			"Surname" => "Smith" );

		$test->ImportRawData( $data );

		$this->assertEquals( "John", $test->Forename );
		$this->assertEquals( "Smith", $test->Surname );
		$this->assertNull( $test->Town );
	}

	public function testModelMergesData()
	{
		$test = new TestModel();
		$test->Forename = "Andrew";
		$test->Town = "Belfast";

		$data = array(
			"Forename" => "John",
			"Surname" => "Smith" );

		$test->MergeRawData( $data );

		$this->assertEquals( "John", $test->Forename );
		$this->assertEquals( "Smith", $test->Surname );
		$this->assertEquals( "Belfast", $test->Town );
	}

	public function testIsset()
	{
		$test = new TestModel();
		$test->Forename = "Andrew";
		$test->Town = "Belfast";

		$this->assertTrue( isset( $test->MyTestValue ) );
		$this->assertTrue( isset( $test[ "MyTestValue" ] ) );
	}

	public function testPropertyChangeNotifications()
	{
		$property = "";
		$oldForename1 = "";
		$newForename1 = "";

		$example = new ModelState();
		$example->Forename = "Ryan";

		$example->AddPropertyChangedNotificationHandler( "Forename", function( $newValue, $propertyName, $oldValue ) use ( &$property, &$oldForename1, &$newForename1 )
		{
			$property = $propertyName;
			$oldForename1 = $oldValue;
			$newForename1 = $newValue;
		});

		$example->Forename = "Bert";

		$this->assertEquals( "Forename", $property );
		$this->assertEquals( "Ryan", $oldForename1 );
		$this->assertEquals( "Bert", $newForename1 );

		$example->Surname = "Kilfedder";
		$this->assertNotEquals( "Kilfedder", $newForename1 );

		$oldForename2 = "";
		$newForename2 = "";

		$example->AddPropertyChangedNotificationHandler( "Forename", function ( $new, $propertyName, $old  ) use ( &$oldForename2, &$newForename2 )
		{
			$oldForename2 = $old;
			$newForename2 = $new;
		} );

		$example->Forename = "Alan";

		$this->assertEquals( "Alan", $newForename1 );
		$this->assertEquals( "Alan", $newForename2 );

		$oldSurname = "";
		$newSurname = "";
		$example->AddPropertyChangedNotificationHandler( "Surname", function ( $new, $propertyName, $old ) use ( &$oldSurname, &$newSurname )
			{
				$oldSurname = $old;
				$newSurname = $new;
			}
		);

		$example->Surname = "Smythe";
		$this->assertEquals( "Kilfedder", $oldSurname );
		$this->assertEquals( "Smythe", $newSurname );

		$example->AddPropertyChangedNotificationHandler( "Surname", function ( $new, $propertyName, $old ) use ( &$oldSurname, &$newSurname )
			{
				$this->fail( "This shouldn't have run because old is the same as new" );

			}
		);

		$example->Surname = "Smythe";

		$hit1 = false;
		$hit2 = false;

		$example = new ModelState();
		$example->Forename = "Forename";
		$example->Surname = "Surname";

		$example->AddPropertyChangedNotificationHandler(
			[ "Forename", "Surname" ],
			function( $new, $propertyName, $old ) use ( &$hit1, &$hit2 )
			{
				if ( $propertyName == "Forename" )
				{
					$hit1 = $new;
				}

				if ( $propertyName == "Surname" )
				{
					$hit2 = $new;
				}
			}
		);

		$example->Forename = "Bert";
		$example->Surname = "Smith";

		$this->assertEquals( "Bert", $hit1 );
		$this->assertEquals( "Smith", $hit2 );
	}
}

class TestModel extends ModelState
{
	public function SetName( $name )
	{
		$this->modelData[ "Name" ] = strtoupper( $name );
	}

	public function GetMyTestValue()
	{
		return "TestValue";
	}

	protected function GetExportedPropertyList()
	{
		$list = parent::GetExportedPropertyList();
		$list[] = "MyTestValue";

		return $list;
	}

	public function GetMangled()
	{
		if ( isset( $this->modelData[ "Mangled" ] ) )
		{
			return strrev( $this->modelData[ "Mangled" ] );
		}

		return "";
	}

}