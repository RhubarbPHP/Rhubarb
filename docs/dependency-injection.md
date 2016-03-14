Dependency Injection
====================

[Dependency Injection](https://en.wikipedia.org/wiki/Dependency_injection) is one way to follow the
[dependency inversion principle](https://en.wikipedia.org/wiki/Dependency_inversion_principle). When using
dependency injection you aren't responsible for supplying the dependencies for an object, a broker or container
performs that task. The actual dependencies are configured at highest possible level through a configuration
for the application. This means an application can have a heavily used, critical dependency, changed to use a
different class by changing a single line in one file.

Rhubarb implements the [constructor injection](https://en.wikipedia.org/wiki/Dependency_injection#Constructor_injection)
pattern. This means that when you need to create a non trivial object you should let the Rhubarb container do it
for you so it can resolve and meet any dependencies.

## Getting the container

Each Application object has its own container. To get the container you can get it from the current application:

``` php
$container = Application::current()->getContainer();
```

To make this easier there is a static method on the Container which does this for you:

``` php
$container = Container::current();
```

## Creating objects

Simply call the `instance` method and pass the name of the class you want to create:

``` php
$rocket = $container->instance(Rocket::class);
```

In this example you will receive a rocket instance or to be more specific an object that either is a Rocket
instance or something that derives from Rocket as a base class.

If you know your object constructor carries arguments you can pass those as arguments to the `instance` method:

``` php
$name = "Vesuvius";
$rocket = $container->instance(Rocket::class, $name);
```

## Registering mappings

If the container has no specific mappings for a particular class name it will simply try to instantiate that
class. To remap a class to a specific sub class simply call `registerClass`:

``` php
$container->registerClass(Rocket::class, NasaShuttle::class);

$name = "Vesuvius";
$rocket = $container->instance(Rocket::class, $name);
// $rocket is now an instance of NasaShuttle
```

The `registerClass` call should normally be made in the initialise method of a Module.

## Satisfying Dependencies

If the constructor requires other objects as arguments the container will try and satisfy those arguments by
making a call to its `instance` method for each of the arguments required. Consider the following improvement
on the Rocket class:

``` php
class Rocket
{
    public function __construct(GroundControl $control, $name)
    {
        // ...
    }
}
```

Now when we create an instance the container will try and create a GroundControl object. Most often arguments
like this will be abstract and so this will fail until we create and register a mapping for GroundControl:

``` php
class Houston extends GroundControl
{
}

$container->registerClass(GroundControl::class, Houston::class);

$name = "Vesuvius";
$rocket = $container->instance(Rocket::class, $name);
// $rocket is now an instance of NasaShuttle with Houston as its ground controller.
```

Note that here we're still passing the name as an argument to the `instance` method - the container is
supplying all the other arguments as it understands them to be dependencies.

If you do need to supply a specific dependency instead of using the container's mappings you can simply
pass it as an argument:

``` php
$name = "Vesuvius";
$control = new StarCity();
$rocket = $container->instance(Rocket::class, $control, $name);
// $rocket is now an instance of NasaShuttle with StarCity as its ground controller...
```

This works for any number of arguments and will descend through every constructor. The dependencies must be
instantiable without any arguments and once the container meets an argument that is not an object or not supplied
in the call to `instance()` it will stop trying to complete any more arguments.

## Singletons

The [singleton pattern](https://en.wikipedia.org/wiki/Singleton_pattern) is a way of ensuring that when an instance
of a class is needed only one is created and the same instance is shared with all who need it. The Rhubarb
container allows classes to be marked as singletons when you configure the mapping by passing true as the third
argument:

``` php
$container->registerClass(Rocket::class, NasaShuttle::class, true);
\\\ Now we'll only ever have one rocket...

$a = $container->instance(Rocket::class);
$b = $container->instance(Rocket::class);

\\ $a is the same instance as $b
```

Alternatively the caller can request that any object be returned as a singleton simply by calling `singleton()`
instead of `instance()`. This will work whether or not the class has a singleton mapping, although without the
mapping it can't be guaranteed that there won't be other instances in use elsewhere.

``` php
$container->registerClass(Rocket::class, NasaShuttle::class);

$a = $container->singleton(Rocket::class);
$b = $container->singleton(Rocket::class);

\\ $a is the same instance as $b
```

