Settings
========

Settings are handled in Rhubarb using classes that extend the base `Settings` class. A settings class
is a singleton - every time you instantiate it, it is a copy of the same object.

Rather than having a single container for all settings, we create individually named settings classes for
each area of concern. This makes sure that settings classes are easily understood and managed.

## Using a settings class

There are a number of settings classes which already exist in the Rhubarb framework and core modules. A very
common one is the `StemSettings` class which stores the default database credentials for stem to access a database
provider.

Use the class by simply instantiating it and setting or retrieving it's properties.

```php
$stemSettings = new StemSettings();

// Set properties like this:
$stemSettings->Host = "localhost";

// Get them like this:
print $stemSettings->Database;
```

The most common place to set setting properties in in your [app.config.php or site.config.php](files-and-directories)

## Creating a settings class

When you're building a module or scaffold, or any sort of reusuable element that can be configured you should
create your own settings classes to contain relevant settings.

Setting classes are based upon `ModelState` and so you don't need to define any actual code for handling the
properties, however because of this it is essential that you put a doc comment at the top to describe the
settings properties that exist.

```php
/**
 * Provides settings to MyWidget
 *
 * @property string $ServiceToken       The token required to access the widget service
 * @property bool   $AllowPublicUsers   True to allow anyone to use the widget
 */
class MyWidgetSettings extends Settings
{
}
```

Note that the pattern of using UpperCamelCase because these are magical getter/setters is recommended but not
mandatory.

### Setting default values for properties

Some properties must be supplied by users of the class, however others might have sensible default values. Set
these by overriding the `initialiseDefaultValues()` method. This is simply leveraging behaviour already found
in `ModelState`.

```php
/**
 * Provides settings to MyWidget
 *
 * @property string $ServiceToken       The token required to access the widget service
 * @property bool   $AllowPublicUsers   True to allow anyone to use the widget. Defaults to true.
 */
class MyWidgetSettings extends Settings
{
    protected function initialiseDefaultValues()
    {
        parent::initialiseDefaultValues();

        $this->AllowPublicUsers = true;
    }
}
```