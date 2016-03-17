Handling Exceptions
===================

When exceptions can be trapped and handled in your code it is important you do so. However sometimes
exceptions are thrown that were not predicted or there might be no sensible action your code can take
to rescue the situation. In these cases you must let the exception bubble up.

> Catching the base `Exception` object in your code is a bad idea. Only catch
> the exceptions you know you can actually handle and let others bubble up to the exception
> handling system.

When an **unhandled** exception is thrown we need to consider three things when we
handle it:

1. What do we tell the user?
2. How does the developer find out about the error so it can be fixed?
3. What format is the response to the user give in (html, json etc.)?

As a modular system Rhubarb lets you tailor how these three concerns are handled in your project
through the use of the `ExceptionHandler` class.

Before we look at the handling of exceptions it's worth looking at these three concerns
in a bit more detail.

## What do we tell the user?

Showing the exception message to the user would in many cases be a dangerous security hole. Consider that if the
exception thrown was a SecurityException, the exception text might carry enough information to expose an
attack vector. You could make sure all exceptions are thrown without secure details, however then most
of the useful information we need to fix the issue is lost; and you can't guarantee someone won't slip up and put
secure details in an exception at some point in the future.

The Rhubarb solution is to throw exceptions that carry two messages, a private message and a public message.

We do this by using Rhubarb's base exception class `RhubarbException`. This exception stores two
messages however the public message is only changed by extending the class. In practice most exceptions
are of a very specific type which extend some more general base exception class. The public message on the
general base exception class is usually sufficient so occasions where the public message needs changed
are fairly rare.

Usually you don't want the user to know much more than the fact that something went wrong. The important thing
is that you are informed about the issue so it can be corrected.

## How do we find out about the error?

The default exception handler simply passes the exception details to the Log class as an error. Unhandled
exceptions will therefore end up in whatever error logs you have configured.

The recommended way to customise the reporting of errors is to attach a new Log class that targets
error messages specifically. For example if you wanted to integrate with an error aggregation service
you could build a custom Log class which only registered for error messages and forwarded them to the
third party service.

## How is the response to the user formatted into an appropriate format?

By their nature, unhandled exceptions can happen at any point during a request, and often the
exception thrown is relevant in lots of different contexts. For example a RepositoryException could be
thrown when rendering HTML or when returning a JSON response to a REST API call. That being the case
how can a top level error handling system know what format is appropriate for the user? In fact it cannot and so
Rhubarb passes the public message of the exception to the currently selected URL handler and asks it to generate a
suitable response.

This way REST URL handlers can encode the message into a JSON error payload, MVP handlers can supply the message as
HTML in the normal way and a dynamic image creation handler could generate a 'no photo' image.

## Custom Exception Handlers

The combination of `RhubarbException` classes, `URL Handlers` and custom `Log` classes should mean that there are
very few occasions where a completely custom exception handler is required. However should you still need this
you can create your own exception handler and register it.

Only one exception handler can be registered with Rhubarb. If you want to extend the original behaviour you should
extend the previously registered class (normally the DefaultExceptionHandler class) and call the parent
`HandleException()` method.

### Designing an Exception Handler

To build your own custom exception handler you simply need to extend the abstract ExceptionHandler class and
implement the ``handleException` method:

~~~php
class MyExceptionHandler extends DefaultExceptionHandler
{
    protected function handleException(RhubarbException $er)
    {
        // Do your thing here.
        // ...

        // Now pass the exception to our parent implementation to make sure the
        // exception still gets logged and passed to the URL handler for response
        // generation
        parent::handleException( $er );
    }
}
~~~

### Registering an Exception Handler

To switch on your chosen exception handler you must register it in your application setup like this:

~~~php
ExceptionHandler::setExceptionHandlerClassName(MyExceptionHandlerClass::class);
~~~

## Turning off exception handling

It is possible to turn off exception handling if you would like PHP to resume its normal behaviour for unhandled
exceptions. This is very common when developing the application locally as it is faster to diagnose and respond
to issues. This is normally set in the `site.config.php`:

``` php
// Exceptions off...
ExceptionHandler::disableExceptionTrapping();

// And back on again...
ExceptionHandler::enableExceptionTrapping();
```