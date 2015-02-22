<?php

namespace Rhubarb\Crown\Tests\Modelling;

use Rhubarb\Crown\Modelling\ModelState;
use Rhubarb\Crown\Tests\RhubarbTestCase;

/**
 *
 * @author acuthbert
 * @copyright GCD Technologies 2012
 */
class ModelStateTest extends RhubarbTestCase
{
    public function testUnsetValueReturnsNull()
    {
        $model = new ModelState();

        $this->assertNull($model->UnsetProperty);
    }

    public function testSetValueReturnsCorrectValueAndIsNotNull()
    {
        $model = new ModelState();
        $model->NewProperty = "abc";

        $this->assertNotNull($model->NewProperty);
        $this->assertEquals("abc", $model->NewProperty);
    }

    public function testKnowsModelIsDirty()
    {
        $model = new ModelState();
        $model->NewProperty = "abc";

        $this->assertTrue($model->hasChanged());

        $model->takeChangeSnapshot();

        $this->assertFalse($model->hasChanged());

        $model->NewProperty = "abc";

        $this->assertFalse($model->hasChanged());

        $model->NewProperty = "123";

        $this->assertTrue($model->hasChanged());

        $model->takeChangeSnapshot();

        unset($model->NewProperty);

        $this->assertTrue($model->hasChanged());
    }

    public function testModelDirtynessIsNotTypeStrict()
    {
        $model = new ModelState();
        $model->NewProperty = "123";

        $model->takeChangeSnapshot();
        $model->NewProperty = 123;

        $this->assertFalse($model->hasChanged());
    }

    public function testChangeSnapshotResetsDirtyness()
    {
        $model = new ModelState();
        $model->NewProperty = "123";

        $this->assertTrue($model->hasChanged());

        $model->takeChangeSnapshot();

        $this->assertFalse($model->hasChanged());
    }

    public function testChangeSnapshotCalculatesDifference()
    {
        $model = new ModelState();

        $this->assertFalse($model->hasPropertyChanged("NewProperty"));

        $model->NewProperty = "123";

        $this->assertTrue($model->hasPropertyChanged("NewProperty"));

        $model->takeChangeSnapshot();

        $this->assertFalse($model->hasPropertyChanged("NewProperty"));

        $model->takeChangeSnapshot();

        $model->NewProperty = "123";

        $this->assertFalse($model->hasPropertyChanged("NewProperty"));

        $model->takeChangeSnapshot();

        $model->NewProperty = "12323";

        $this->assertTrue($model->hasPropertyChanged("NewProperty"));
    }

    public function testGetModelChanges()
    {
        $model = new ModelState();

        $this->assertEquals([], $model->getModelChanges());

        $model->A = 1;
        $model->B = 2;

        $this->assertEquals(["A" => 1, "B" => 2], $model->getModelChanges());

        $model->takeChangeSnapshot();

        $this->assertEquals([], $model->getModelChanges());

        $model->A = 2;

        $this->assertEquals(["A" => 2], $model->getModelChanges());
    }

    public function testSupportsGetters()
    {
        $model = new TestModel();

        $this->assertEquals("TestValue", $model->MyTestValue);
    }

    public function testSupportsSetters()
    {
        $model = new TestModel();
        $model->Name = "Andrew Cuthbert";

        $this->assertEquals("ANDREW CUTHBERT", $model->Name);
    }

    public function testArrayAccess()
    {
        $model = new TestModel();
        $model->Name = "Andrew Cuthbert";

        $this->assertEquals("ANDREW CUTHBERT", $model["Name"]);

        $model["Name"] = "John Smith";

        $this->assertEquals("JOHN SMITH", $model->Name);
    }


    public function testRawDataIsExported()
    {
        $test = new TestModel();
        $test->Forename = "Andrew";
        $test->Surname = "Cuthbert";

        $data = $test->exportRawData();

        $keys = array_keys($data);

        $this->assertCount(2, $data);
        $this->assertEquals("Andrew", $data["Forename"]);
        $this->assertEquals("Surname", $keys[1]);
    }

    public function testAllDataIsExported()
    {
        $test = new TestModel();
        $test->Forename = "Andrew";
        $test->Surname = "Cuthbert";
        $test->Mangled = "Mangled";

        $data = $test->exportData();

        $keys = array_keys($data);

        $this->assertCount(4, $data);
        $this->assertEquals("Andrew", $data["Forename"]);
        $this->assertEquals("Surname", $keys[1]);
        $this->assertEquals("delgnaM", $data["Mangled"]);
        $this->assertEquals("TestValue", $data["MyTestValue"]);
    }

    public function testModelImportsRawData()
    {
        $test = new TestModel();
        $test->Forename = "Andrew";
        $test->Town = "Belfast";

        $data = array(
            "Forename" => "John",
            "Surname" => "Smith"
        );

        $test->importRawData($data);

        $this->assertEquals("John", $test->Forename);
        $this->assertEquals("Smith", $test->Surname);
        $this->assertNull($test->Town);
    }

    public function testModelMergesData()
    {
        $test = new TestModel();
        $test->Forename = "Andrew";
        $test->Town = "Belfast";

        $data = array(
            "Forename" => "John",
            "Surname" => "Smith"
        );

        $test->mergeRawData($data);

        $this->assertEquals("John", $test->Forename);
        $this->assertEquals("Smith", $test->Surname);
        $this->assertEquals("Belfast", $test->Town);
    }

    public function testIsset()
    {
        $test = new TestModel();
        $test->Forename = "Andrew";
        $test->Town = "Belfast";

        $this->assertTrue(isset($test->MyTestValue));
        $this->assertTrue(isset($test["MyTestValue"]));
    }

    public function testPropertyChangeNotifications()
    {
        $property = "";
        $oldForename1 = "";
        $newForename1 = "";

        $example = new ModelState();
        $example->Forename = "Ryan";

        $example->addPropertyChangedNotificationHandler("Forename",
            function ($newValue, $propertyName, $oldValue) use (&$property, &$oldForename1, &$newForename1) {
                $property = $propertyName;
                $oldForename1 = $oldValue;
                $newForename1 = $newValue;
            });

        $example->Forename = "Bert";

        $this->assertEquals("Forename", $property);
        $this->assertEquals("Ryan", $oldForename1);
        $this->assertEquals("Bert", $newForename1);

        $example->Surname = "Kilfedder";
        $this->assertNotEquals("Kilfedder", $newForename1);

        $oldForename2 = "";
        $newForename2 = "";

        $example->addPropertyChangedNotificationHandler("Forename",
            function ($new, $propertyName, $old) use (&$oldForename2, &$newForename2) {
                $oldForename2 = $old;
                $newForename2 = $new;
            });

        $example->Forename = "Alan";

        $this->assertEquals("Alan", $newForename1);
        $this->assertEquals("Alan", $newForename2);

        $oldSurname = "";
        $newSurname = "";
        $example->addPropertyChangedNotificationHandler("Surname",
            function ($new, $propertyName, $old) use (&$oldSurname, &$newSurname) {
                $oldSurname = $old;
                $newSurname = $new;
            }
        );

        $example->Surname = "Smythe";
        $this->assertEquals("Kilfedder", $oldSurname);
        $this->assertEquals("Smythe", $newSurname);

        $example->addPropertyChangedNotificationHandler("Surname",
            function ($new, $propertyName, $old) use (&$oldSurname, &$newSurname) {
                $this->fail("This shouldn't have run because old is the same as new");

            }
        );

        $example->Surname = "Smythe";

        $hit1 = false;
        $hit2 = false;

        $example = new ModelState();
        $example->Forename = "Forename";
        $example->Surname = "Surname";

        $example->addPropertyChangedNotificationHandler(
            ["Forename", "Surname"],
            function ($new, $propertyName, $old) use (&$hit1, &$hit2) {
                if ($propertyName == "Forename") {
                    $hit1 = $new;
                }

                if ($propertyName == "Surname") {
                    $hit2 = $new;
                }
            }
        );

        $example->Forename = "Bert";
        $example->Surname = "Smith";

        $this->assertEquals("Bert", $hit1);
        $this->assertEquals("Smith", $hit2);
    }
}

class TestModel extends ModelState
{
    public function setName($name)
    {
        $this->modelData["Name"] = strtoupper($name);
    }

    public function getMyTestValue()
    {
        return "TestValue";
    }

    protected function getExportedPropertyList()
    {
        $list = parent::getExportedPropertyList();
        $list[] = "MyTestValue";

        return $list;
    }

    public function getMangled()
    {
        if (isset($this->modelData["Mangled"])) {
            return strrev($this->modelData["Mangled"]);
        }

        return "";
    }
}