Settings
========

Settings in Rhubarb are encapsulated in classes that extend the base `Settings` class. A settings class
should be instantiated as a singleton - it is always the same instance of the object.

Rather than having a single class or container for all settings, we create individually named settings classes for
each area of concern. This makes sure that settings classes are easily understood, extended and managed.

## Using a settings class

There are a number of settings classes which already exist in the Rhubarb framework and core modules. A very
common one is the `StemSettings` class which stores the default database credentials for stem to access a data
provider.

Use the class by getting the instance and setting or retrieving it's properties.

```php
$stemSettings = StemSettings::singleton();

// Set properties like this:
$stemSettings->host = "localhost";

// Get them like this:
print $stemSettings->database;
```

The most common place to set setting properties in your [application module or site.config.php](files-and-directories)

## Creating a settings class

When you're building a module or scaffold, or any sort of reusuable element that can be configured you should
create your own settings class. Setting objects are plain old PHP objects and so you simply need to define
public fields or setters.

```php
/**
 * Provides settings to MyWidget
 */
class MyWidgetSettings extends Settings
{
    /**
    * @var string The token required to access the widget service
    */
    public $serviceToken;

    /**
    * @var bool True to allow anyone to use the widget
    */
    public $allowPublicUsers = false;
}
```

### Setting default values for properties

Some properties must be set by users of the class, however others might have sensible default values. If the value
would be a simple scalar type like a string or int these can be set simply by initialising the class fields. If you
need to initialise a more complex type like an object you can either create the default late using a public getter
function or you can create it early by overriding the `initialiseDefaultValues()` method.

As a rule of thumb if the default value is expensive to create (an object with a large constructor or one that
contacts a database for example) you should create it late using the getter approach.

Both approaches are demonstrated in the following example:

```php
/**
 * Provides settings to MyWidget
 */
class MyWidgetSettings extends Settings
{
    private $reallyComplexObjectValue = null;

    //////////// Late creation approach //////////////

    public function getReallyComplexObjectValue()
    {
        // As this value has a high creation overhead we don't
        // create it until it's been requested.
        if ($this->reallyComplexObjectValue == null){
            $this->reallyComplexObjectValue = new ReallyComplexObject();
        }

        return $this->reallyComplexObjectValue;
    }

    public function setReallyComplexObjectValue($value)
    {
        $this->reallyComplexObjectValue;
    }

    //////////// Early creation approach //////////////

    public $reallySimpleObjectValue;

    protected function initialiseDefaultValues()
    {
        // As this value has a low creation overhead we can always instantiate it
        $this->reallySimpleObjectValue = new ReallySimpleObject();
    }
}
```