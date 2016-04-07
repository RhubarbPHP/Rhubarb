Logging
===

Logging allows you to capture a stream of decisions from the code base into one or more logs for current or
future analysis.

Logs can be used for monitoring the health of a production application or to assist in the development process
by providing debugging information for hard to replicate issues.

For example:

1) APIs: Debugging can be time consuming to configure when an issue could be either the publisher or the consumer
of an API. Logging can sometimes provide a quick insight into where the fault lies.

2) Remote Debugging: Often when in the production environment bugs appear that can't be replicated locally.
Logging can provide enough information to bridge the understanding gap.

3) Hard to catch bugs: Some bugs are only reported by users and can't be replicated easily. Enabling a log
allows you to have some diagnostic data to fall back on the next time it is reported.

4) Performance Diagnostics: Alongside profiling, logging can quickly identify slow running parts of an
application.

## Logging Principles

Log entries are written to one or more 'Logs'. A 'Log' could be the PHP error log, a standard file, a database table
or you could invent your own.

You can attach as many loggers as you need to the logging engine. For example you might have a simple PHP log
logging warnings and errors while an API log logs all requests and responses to a debug folder during early
testing.

Log messages are recorded with a log level. The valid levels are:

ERROR_LEVEL
:   Used for errors that the application could not recover from. Normally the user will also be aware
    an error has occurred.

WARNING_LEVEL
:   Use for issues that we should draw the developers attention to, even if it has been handled
	by the application in some way and with no negative effect on the end user. For example if an
	image cache file couldn't be created, the image could still be displayed but performance might
	be impaired.

DEBUG_LEVEL
:   Use for statements that might assist in debugging complicated code paths and decision logic.

REPOSITORY_LEVEL
:   Use for monitoring repository connections (e.g. database queries)

BULK_DATA_LEVEL
:   Use to log actual data packages, for example API requests and responses.
	Normally this requires a special log implementation - this would not be appropriate for
	the Php Log class for instance.

PERFORMANCE_LEVEL
:   Use to log performance related information.

Log messages can be issued with any combination of log levels (e.g. just DEBUG_LEVEL, OR DEBUG_LEVEL and
PERFORMANCE_LEVEL)

## Writing a log message

Simply call the `Log::createEntry()` method:

~~~ php
// Note this entry is flagged for both debug and warning levels.
Log::createEntry( Log::DEBUG_LEVEL | Log::WARNING_LEVEL, "Started to reticulate" );
~~~

Or if you prefer you can use one of the level alias methods:

~~~ php
Log::debug( "Started to reticulate" );
Log::warning( "Reticulation not going well" );
Log::error( "Reticulation bombed" );
~~~

You can also optionally include a category as the next parameter to these function calls. The category may
appear in the final log output and can be used by custom logs to filter the messages they want to received
in a more meaningful way. Note that categories should be in upper case so they can easily be spotted for
what they are in log output. If possible keep them short so that filtering tools used on the final log
output become easier to use.

~~~ php
Log::debug( "Started to reticulate", "RETIC" );
~~~

On occasion, the generation of the actual log message would involve CPU time. It is wasteful to spend CPU or
memory resources generating a message if no Logs are actually attached. To side step this instead of
passing a string for the message pass a callback which returns the log message:

~~~ php
Log::debug( function()
{
    return "Started to reticulate at ".date( "H:i:s" );
} );
~~~

The callback will only be executed if a Log is attached.

Log messages can optionally carry additional data which custom Logs can understand. If it's appropriate for your
use case, add your custom data to an associative array and pass it as an extra parameter to the log functions:

~~~ php
Log::debug( "Started to reticulate", "RETIC", [ "ReticulationModel" => "Bendy" ] );
// Or
Log::debug( function()
{
    return [ "Started to reticulate at ".date( "H:i:s" ), [ "ReticulationModel" => "Bendy" ] ]
}, "RETIC" );
~~~

Notice the callback approach now returns a two value array. Custom data will not be used by the standard Log
class available to you in Rhubarb but can be accessed if you create your own Log class.

## Indenting and Outdenting

To help scan logs for patterns, it's helpful to indent and outdent the log messages to give some indiciation of
scope and program flow.

Indent future messages by calling `Log::indent()` and `Log::outdent()` to reduce the indent. If you call `indent()`
you must call `outdent()` or the symmetry will be destroyed.

~~~ php
Log::debug( "Started to reticulate" );
Log::indent();
Log::warning( "Reticulation not going well" );
Log::error( "Reticulation bombed" );
Log::outdent();
~~~

## Attaching a Log

In your application module or `site.config.php` simply create the Log and call the `Log::attachLog()` method:

~~~ php
Log::attachLog(new PhpLog(Log::ALL));
~~~

Most Log constructors take a single parameter which is the level of log messages they are interested in. Any number
of log levels can be supplied by ORing the values together (or using Log::ALL which has already combined all the
log levels).

~~~ php
Log::attachLog(new PhpLog(Log::DEBUG_LEVEL | Log::PERFORMANCE_LEVEL));
~~~

## Standard Log classes

### PhpLog

This will push messages to the PHP error log with an output like:

<pre>
[04-Dec-2013 21:56:40 Europe/London] RETIC	529fa518a9684	+6	6.1	127.0.0.1	Starting to Reticulate
</pre>

Fields are:

1. Time
2. Category
3. UniqueID
4. Time since log creation (milliseconds)
5. Time since last log entry (milliseconds)
6. IP Address
7. Message (indented)

### RepositoryLog

`RepositoryLog` is provided by the RepositoryLog scaffold (as it requires creation of a logging table)

As the name suggests this will record entries in a database table called `tblRhubarbLogEntry`

> Note: Database logging is an expensive logging option and should not be seen as a log
> which can be left on indefinitely. It can easily double your execution time for complex
> requests and should be used primarily in debugging for short periods where being able
> to query the entries as a database will help expose the issue at large, or provide
> quick aggregations on execution times etc.

### MonologLog

[Monolog](https://github.com/Seldaek/monolog) is a powerful logging framework used in many open source
projects. It has great community support and provides a whole host of log destinations. Rhubarb requires
monolog and so it is available to everyone to use.

To get Monolog working in Rhubarb simply create your logger class as described by the Monolog docs and then
use the `MonologLog` Rhubarb class to wrap it. For example here we use the ChromeLogHandler to inject log
messages into the console of Chrome:

``` php
$logger = new Logger("rhubarb");
$logger->pushHandler( new ChromePHPHandler() );

Log::AttachLog( new MonologLog(Log::ALL, $logger) );
```

> If you're wondering why Rhubarb doesn't just use Monolog directly it's because for performance we
> need to have the delayed calculation of log messages only when an interested log is attached as
> described above.

## Creating a Log

Logs are created by extending the `Log` class.

You will have to implement the `WriteEntry` method to put the message where it needs to go. You will have access
to the following functions from the base Log class:

* `getExecutionTime()`
* `getTimeSinceLastLog()`
* `getRemoteIP()`
* `getPhpSessionID()`

If you would like to restrict logging to certain conditions beyond the log level, you should override the
`shouldLog($category)` function and return true of false depending on your conditions. This can be used only to log
if an IP address is matched, if the URL matches a target etc or the category is correct etc.

## Reading the PHP Error Log (Windows)

On unix systems you can use the `tail -f` command to follow the PHP error log (if using the PhpLog log). For
windows you can use [mTail](http://ophilipp.free.fr/op_tail.htm) or [baretail](http://www.baremetalsoft.com/baretail/)
Both programs look basic but provide some powerful features such as regular expression filtering and colour coding
and of course you can stop the tail from scrolling when you need to actually read it!