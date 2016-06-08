The Application object
======================

The Application object is the top level object in the Rhubarb processing stack. It collects the registered modules
for an application, maintains a number of high level objects like the [Request](request) and
[Container](dependency-injection) objects along with provision for shared data arrays. Application objects
are the first object asked to generate a response for a given request.

Normally an application will have just one Application object. It is possible to create secondary application
instances which can be used when unit testing. For example a SaaS application might need to instantiate it's
landlord application in order to test particular API calls. This can be achieved with acceptance or functional
testing using many tools (codeception, selenium, phantomjs) however it is so much easier to unit test it
is more likely to have greater coverage.

## Using the Application object

### Retrieving the current application

Rhubarb can host more than one application in a single execution. The currently running application can be
returend from `Application::current()`

``` php
$app = Application::current();
```

Once you have the current application you can access the following objects in the following ways:

``` php
$app = Application::current();
// Get the PhpContext
$phpContext = $app->context();
// Get the Request
$request = $app->request();
// Get the dependency injection Container
$container = $app->container();
```

The are also a number of other useful states the application keeps track of:

### Live mode

Live mode should only be enabled on production servers. The setting can be used to enable or disable
features that only make sense on production environments.

``` php
// True if the application is running on a production server
$live = $app->live;
```

### Developer Mode

Similar to live mode, developer mode is normally used to switch on or off behaviours that only make sense
during development. Most of these behaviours have some sort of performance penalty but improve debugging or
deployment experiences. It's common to find developer mode on but live mode off on a staging server for example.

``` php
// True if the application is running in developer mode
$developerMode = $app->developerMode;
```

### Application root path

If you need to create paths to folders in your application directory you should use this setting rather than
try to resolve the root path yourself.

``` php
// The path to the root of the application source code
$rootPath = $app->applicationRootPath;
```

### Unit testing mode

During unit testing some behaviours that require a real browser to be making the request need to be turned off.

``` php
// True if the application is currently being unit tested.
$unitTesting = $app->unitTesting;
```

## Creating and registering an Application object

To serve a response Rhubarb needs to boot an application. You can either define your own Application class
or you can create an instance of the base Application class and configure it.

The most important thing to do when creating or configuring an application is to register the modules. The following
two examples show how to do this with an extended Application class or a directly configured class.

``` php
class MyApplication extends Application
{
    protected function getModules()
    {
        return [
            new MyModule()
        ];
    }
}

$app = new MyApplication();
```

``` php
$app = new Application();
$app->registerModule(new MyModule());
```

> The recommended approach is to create a bespoke Application class for your application.

### Registering the application

To serve requests you need to register the application. There are two main ways to do this.

1. Create a `settings/app.config.php` file and create your application object.
2. Set the `rhubarb_app` environment variable to equal the full class name of your application

#### Creating the application in app.config.php

An example app.config.php file might would look like this.

``` php
namespace My\WebsiteApp;

$application = new MyApplication();
```

#### Setting the rhubarb_app environment variable

This can be done from the terminal using the export unix command if running a custard or command line script. If
serving http requests this will usually need configured through your web server configuration files. For apache
you need to add the following directive:

```
SetEnv rhubarb_app "\My\WebApp\MyApplication"
```

## Application as a Module

The Application class extends the Module class and can be considered the "root" or top level module of the
application. As such your application class can define url handlers, initialise components and require other
modules.