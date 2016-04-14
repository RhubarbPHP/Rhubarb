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

Simply call the `getInstance` method and pass the name of the class you want to create:

``` php
$rocket = $container->getInstance(Rocket::class);
```

In this example you will receive a rocket instance or to be more specific an object that either is a Rocket
instance or something that derives from Rocket as a base class.

If you know your object constructor carries arguments you can pass those as arguments to the `getInstance` method:

``` php
$name = "Vesuvius";
$rocket = $container->getInstance(Rocket::class, $name);
```

## Registering mappings

If the container has no specific mappings for a particular class name it will simply try to instantiate that
class. To remap a class to a specific sub class simply call `registerClass`:

``` php
$container->registerClass(Rocket::class, NasaShuttle::class);

$name = "Vesuvius";
$rocket = $container->getInstance(Rocket::class, $name);
// $rocket is now an instance of NasaShuttle
```

The `registerClass` call should normally be made in the initialise method of a Module.

## Satisfying Dependencies

If the constructor requires other objects as arguments the container will try and satisfy those arguments by
making a call to its `getInstance` method for each of the arguments required. Consider the following improvement
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
$rocket = $container->getInstance(Rocket::class, $name);
// $rocket is now an instance of NasaShuttle with Houston as its ground controller.
```

Note that here we're still passing the name as an argument to the `getInstance` method - the container is
supplying all the other arguments as it understands them to be dependencies.

If you do need to supply a specific dependency instead of using the container's mappings you can simply
pass it as an argument:

``` php
$name = "Vesuvius";
$control = new StarCity();
$rocket = $container->getInstance(Rocket::class, $control, $name);
// $rocket is now an instance of NasaShuttle with StarCity as its ground controller...
```

This works for any number of arguments and will descend through every constructor. The dependencies must be
instantiable without any arguments and once the container meets an argument that is not an object or not supplied
in the call to `getInstance()` it will stop trying to complete any more arguments.

## Singletons

The [singleton pattern](https://en.wikipedia.org/wiki/Singleton_pattern) is a way of ensuring that when an instance
of a class is needed only one is created and the same instance is shared with all who need it. The Rhubarb
container allows classes to be marked as singletons when you configure the mapping by passing true as the third
argument:

``` php
$container->registerClass(Rocket::class, NasaShuttle::class, true);
\\\ Now we'll only ever have one rocket...

$a = $container->getInstance(Rocket::class);
$b = $container->getInstance(Rocket::class);

\\ $a is the same instance as $b
```

Alternatively the caller can request that any object be returned as a singleton simply by calling `getSingleton()`
instead of `getInstance()`. This will work whether or not the class has a singleton mapping, although without the
mapping it can't be guaranteed that there won't be other instances in use elsewhere.

``` php
$container->registerClass(Rocket::class, NasaShuttle::class);

$a = $container->getSingleton(Rocket::class);
$a->foo = "bar";

$b = $container->getSingleton(Rocket::class);

\\ $a is the same instance as $b
\\ and $b->foo == "bar";
```

If you need to control the creation of the singleton more precisely you can supply a callback function as a second
argument to `getSingleton`. Only in the event that the singleton doesn't already exist will the call back be called.
The call back should return the instance that will now serve as the singleton instance.

``` php
$container->registerClass(Rocket::class, NasaShuttle::class);

$shuttle = $container->getSingleton(Rocket::class, function(){
    $shuttle = new NasaShuttle();
    $shuttle->upgradeEngines();

    return $shuttle;
});

// IF there wasn't already a singleton in use for Rocket THEN $a will be a NasaShuttle with upgraded engines
```

If you want to initialise the container with a particular singleton instance so that future requests to singleton
return that instance you can call `registerSingleton`. This example achieves the same result as the code above
with the advantage it can be ran first (in your application configuration for example) to guarantee no other
singleton instance could have been created first.

``` php
$shuttle = new NasaShuttle();
$shuttle->upgradeEngines();
$container->registerSingleton(Rocket::class, $shuttle);
```

Creating classes in your configuration is something you should avoid however as there is no guarantee the current
request is going to need your singleton. It's much better to register a callback function with `registerSingleton`
instead:

``` php
$container->registerSingleton(Rocket::class, function(){
    $shuttle = new NasaShuttle();
    $shuttle->upgradeEngines();

    return $shuttle;
});
```

This code has the same affect as the previous example with the additional advantage that the shuttle class
isn't created unless the singleton is requested.

To clarify passing a callback to `getSingleton()` and `registerSingleton()` has mostly the same effect with the
main difference being that `getSingleton()` will call the callback immediately if the singleton doesn't already
exist. `getSingleton()` also returns a singleton instance whereas `registerSingleton()` returns void.

### Static shortcuts

To make the container easier to use there are two shortcut methods. Instead of

``` php
$container = Container::current();
$rocket = $container->getInstance(Rocket::class);
```

You can use the static `instance` method on the container instead:

``` php
$rocket = Container::instance(Rocket::class);
```

This static call will use the active container.

Similarly there is a shortcut for getSingleton:

``` php
$rocket = Container::singleton(Rocket::class);
```

> Using the static calls is the preferred way of using the container to request objects.

### The SingletonInterface and SingletonTrait

Instead of having to instantiate singletons by calling the `getSingleton` method on the container and passing a
class name, it's possible to give any class a static function called `getSingleton` of its own. This makes using
the singleton pattern more straight forward.

To apply this behaviour you should implement the `SingletonInterface` and then use the `SingletonTrait`:

``` php
class Rocket implements SingletonInterface
{
    use SingletonTrait;
}

$rocket = Rocket::singleton();
```

This is the preferred pattern for getting singleton instances. To register specific singleton instances you still
need to call the container methods directly.

## The Provider Pattern

Many services in Rhubarb are handled by providers. A provider is essentially an abstract class that sets a pattern
for providing some essential service. The actual concrete implementation of the provider is registered at the
application level. A good example of a provider is the [HashProvider](encryption#content):

``` php
// Set the registered hash provider for the application.
HashProvider::setProviderClassName(Sha512HashProvider::class);
```

Internally providers use the dependency injection container to implement the mapping. Therefore when getting an
instance of the provider it should be done using the dependency injection pattern where possible:

``` php
class MyClassThatNeedsToHashSomething
{
    private $hashProvider;

    public function __construct(HashProvider $hashProvider)
    {
        $this->hashProvider = $hashProvider;
    }

    public function hashIt()
    {
        $this->hashProvider->createHash(...);
        // ....
    }
}

$hasher = Container::current()->getInstance(MyClassThatNeedsToHashSomething::class);
```

Alternatively a provider can be given to you directly within your code:

``` php
class MyClassThatNeedsToHashSomething
{
    public function hashIt()
    {
        $hashProvider = HashProvider::getProvider();
        $hashProvider->createHash(...);
        // ....
    }
}

$hasher = new MyClassThatNeedsToHashSomething();
```

The dependency injection approach is preferred because it makes unit testing easier and makes it more apparent
what the dependencies for this class really are.

However if a provider is particularly slow or expensive to create and the likelyhood of it being used within
a class is small the direct approach could improve performance. Consider:

``` php
class MyHeavyWeightClass
{
    private $mysteryProvider;

    public function __construct(ReallySlowToCreateProvider $mysteryProvider)
    {
        $this->mysteryProvider = $mysteryProvider;
    }

    public function mysteryFunction()
    {
        if ($this->somethingReallyUnlikely()){
            $this->mysteryProvider->doSomethingMysterious();
        }
        // ....
    }
}

$mystery = Container::current()->getInstance(MyHeavyWeightClass::class);
```

As soon as the `MyHeavyWeightClass` is created a `ReallySlowToCreateProvider` is created regardless of the fact
that it will very rarely be needed.

### Creating a provider

Providers should be abstract and should implement the ProviderInterface and use the ProviderTrait:

``` php
abstract class MyProvider implements ProviderInterface
{
    use ProviderTrait;
}
```

The interface and trait give the class two static methods, `setProviderClassName($className)` and `getProvider()`.

### Singleton Providers

Normally providers are inexpensive to create and share no state between instances. As such they are created new
each time they're requested. Sometimes your provider needs to be a singleton to remain efficient. In this case
simply switch from using `ProviderTrait` to `SingletonProviderTrait`. Thereafter calls to `getProvider` will
return a singleton.