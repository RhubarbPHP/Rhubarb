Request Processing Overview
===============================

Rhubarb answers requests from a client with a response. Determining which response and how that response is
formatted is the job of the Rhubarb processing pipeline. Understanding the pipeline is an important step to
understanding how Rhubarb's behaviour can be modified for your needs.

## Step 1: Application is booted

In the `vendor/rhubarbphp/rhubarb/platform` folder there are a number of PHP scripts used to boot Rhubarb.
These files are the entry point of all requests to the Rhubarb engine. Sometimes called
['Front Controllers'](http://en.wikipedia.org/wiki/Front_Controller_pattern) the script called depends upon
the context of the request:

execute-http.php
:   Called by a webserver serving an HttpRequest to Rhubarb. The webserver must be instructed to route all
    requests to this PHP script with the exception of static files that can be served directly.

execute-cli.php
:   Called when executing a script from a terminal.

execute-test.php
:   Called as a bootstrap for PHP Unit when running unit tests for your application.

All three will boot the application by setting up the environment and then loading your main application
configuration.

## Stage 2: Application and Module registration

execute-http.php and execute-cli.php will try and load a Rhubarb [Application](application#content) object. The most common way
to support this is by creating an `app.config.php` file in the settings folder which will define a Module for
the application and **register it** in a new Application instance. A module can return a list of dependant modules
which will also be registered.

All modules are resolved and registered before further execution as the registration order of the modules can
be important.

## Stage 3: Module initialisation

All modules will now be asked to initialise. Modules are initialised in reverse depth order - that is the module
loaded furthest down the dependency chain is initialised first and then Rhubarb works backwards right up to the
initial application module. This order allows modules to replace sections of its functionality e.g. changing
UrlHandlers or database schemas.

## Stage 4: Url Handler resolution (http requests only)

Modules can register a collection of UrlHandlers. One by one the UrlHandlers are compared with the
incoming request and if suitable for that URL then the matching UrlHandler is asked to return a response for the request.

## Stage 5: Response generation (http requests only)

UrlHandlers rarely (but occasionally) generate responses directly. Their primary function is to understand the
URL and then configure a response generating class using whatever settings might be appropriate for the URL.

## Stage 6: Response filtering (http requests only)

Modules can also register response filters which each in turn have an opportunity to process the response before it
is returned to the client. Surrounding generated HTML using the LayoutFilter is the most common example of response
filtering.