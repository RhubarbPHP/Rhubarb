Modules
=======

A Module describes a collection of related features. It registers settings required for those features and configures
system components to support them.

A Module can return a list of other modules it requires in order to operate.

A Module is a class that extends Rhubarb\Crown\Module and might look something like this:

``` php
class RocketFlyingModule extends Module
{
    public function initialise()
    {
        // Register a DB schema
        SolutionSchema::registerSchema("Rockets", __NAMESPACE__ . '\RocketSchema');

        // Setup our rocket class in the DI container
        $container = Container::current();
        $container->registerClass(Rocket::class, NasaRocket::class, true);
    }

    protected function registerUrlHandlers()
    {
        // Register a /rockets/ url with a generating class of some sort
        $this->addUrlHandlers([
                "/rockets/" => new ClassMappedUrlHandler(RocketsList::class)
        ]);
    }

    protected function getModules()
    {
        // We need both Stem and Leaf modules for this module to work.
        return [
            new StemModule(),
            new LeafModule(),
        ];
    }
}
```

initialise()
:   Any configuration of settings or other system components should happen here.

registerUrlHandlers()
:   Here you should register classes that [handle urls](./url-handlers)

getModules()
:   If your module depends on other modules you should instantiate and return them here in an array.

> Normally a single project defines only one module, however it might depend upon many.

## Scaffolds

Rhubarb has many official modules and in most cases a module provides a library of reusable classes and patterns.

A scaffold is a special type of module that provides 'out-of-the-box' functionality. Usually this includes some
model schemas and a collection of user interfaces registered as url handlers.

While a scaffold should work just as it is, it's fairly normal to customise the scaffold by replacing or
extending some of the user interface elements and schemas to tailor it to the project at hand.

## Replacing modules

While rare it is possible to completely replace a module with a completely different one even if a dependancy
is requiring the original module. Simply create the new module with the same class name (but different namespace)
and make sure it is depended upon by your application. It should trump the original one because it will be registered after the original.

> Replacing modules like this should be done only with extreme caution. Without replacing the entire
> functionality of the target module it's quite likely something will break. Where only a small portion of
> functionality needs changed it's much safter to simply register an overriding url handler or schema.
