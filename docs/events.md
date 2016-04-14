Events
======

PHP does not provide events as a first class feature. However using closures we can achieve much the same thing.

Rhubarb provides the `EventEmitter` trait which you can use in any class to add event dispatching support. Any
object can listen for events.

## Dispatching Events

Firstly use the trait on your class:

``` php
class MyClass
{
    use EventEmitter;
}
```

Now to raise an event simply call the `raiseEvent` function. Pass any arguments you wish to the listeners. For
example here we raise an event called SomethingInterestingHappened and pass two arguments to any listener.

``` php
class MyClass
{
    use EventEmitter;

    private function somethingInteresting()
    {
        $arg1 = "foo";
        $arg2 = "bar";

        $this->raiseEvent("SomethingInterestingHappened", $arg1, $arg2);
    }
}
```

## Listening to events

Simply call the public `attachEventHandler()` method of the object that raises events and pass the name of the
event to listen to and an anonymous function to be used as a callback. The callback can define arguments that
will receive the values passed when the event was raised.

``` php
$myClass = new MyClass();
$myClass->attachEventHandler("SomethingInterestingHappened", function($arg1, $arg2){
    // Have fun with $arg1 and $arg2 here...
});
```

## Returning Values

An event handling callback can return a value. The first listener to an event to return a value will itself be
returned as the result of `raiseEvent` to the event emitter.

``` php
class MyClass
{
    use EventEmitter;

    private function somethingInteresting()
    {
        $arg1 = "foo";
        $arg2 = "bar";

        $arg3 = $this->raiseEvent("SomethingInterestingHappened", $arg1, $arg2);
        // $arg3 will be foobar - see handler below.
    }
}

$myClass = new MyClass();
$myClass->attachEventHandler("SomethingInterestingHappened", function($arg1, $arg2){
    return $arg1.$arg2;
});
```

Sometimes you need to allow all listeners to return a value. This is achieved by passing another closure
as the last argument when calling `raiseEvent`. If passed this closure will be called with the return
value from each listener:

``` php
class MyClass
{
    use EventEmitter;

    private function somethingInteresting()
    {
        $arg1 = "foo";
        $arg2 = "bar";

        $this->raiseEvent("SomethingInterestingHappened", $arg1, $arg2, function($response){
            // This response callback will be called twice; once for each handler below.
        });
    }
}

$myClass = new MyClass();
$myClass->attachEventHandler("SomethingInterestingHappened", function($arg1, $arg2){
    return $arg1.$arg2;
});
$myClass->attachEventHandler("SomethingInterestingHappened", function($arg1, $arg2){
    return $arg2.$arg1;
});
```

## Clearing and replacing event handlers

To remove all event handlers from an emitter simply call `clearEventHandlers()`. This is protected so must
be called from within the emitter.

To detach a single event handler you can call the public `detachEventHandler($eventName)`. This removes all
the handlers for that event.

``` php
$myClass->detachEventHandler("SomethingInterestingHappened");
```

You can replace all other handlers with a new one by calling `replaceEventHandler()`:

``` php
$myClass->replaceEventHandler("SomethingInterestingHappened", function(){
    // New handler replaces all others...
});
```

## Using constants for event names

As event names are strings it is quite common to find an event handler isn't firing because of a simple misspelling
or perhaps someone has renamed the emitted event. A good practice is to use constants instead of string literals
to make sure the IDE can assist you in getting the right event name. The constant should be defined in the
emitting class:

``` php
class MyClass
{
    use EventEmitter;

    const EVENT_SOMETHING_INTERESTING_HAPPENED = "SomethingInterestingHappened";

    private function somethingInteresting()
    {
        $arg1 = "foo";
        $arg2 = "bar";

        $this->raiseEvent(self::EVENT_SOMETHING_INTERESTING_HAPPENED, $arg1, $arg2, function($response){
            // This response callback will be called twice; once for each handler below.
        });
    }
}

$myClass = new MyClass();
$myClass->attachEventHandler(MyClass::EVENT_SOMETHING_INTERESTING_HAPPENED, function($arg1, $arg2){
    return $arg1.$arg2;
});
```