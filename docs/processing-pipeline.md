The Rhubarb Processing Pipeline
===============================

Rhubarb answers requests from a client (presented by a web server) with a response. Determining which response
and how that response is formatted is the job of the Rhubarb processing pipeline. Understanding the
pipeline is an important step to understanding how many key Rhubarb classes work.

## Stage 1: Application is booted

In the `vendor/rhubarbphp/rhubarb/platform` folder there are a number of PHP scripts starting with the word
"execute". These files are the entry point of all requests to the Rhubarb engine. Sometimes these are called
['Front Controllers'](http://en.wikipedia.org/wiki/Front_Controller_pattern). Which script depends upon
the context of the request:

execute-http.php
:   Called by a webserver serving an HttpRequest to Rhubarb. The webserver must be instructed to route all
    (with some exceptions) requests to this PHP script.

execute-cli.php
:   Called from a machine terminal serving a CliRequest to Rhubarb. This is used for running scheduled tasks
    or other terminal scripts.

execute-test.php
:   Called as a bootstrap for PHP Unit when running unit tests for your application.

All three will boot the application by setting up the environment (like changing the working directory) and then
including the `settings/app.config.php` script.

## Stage 2: Module registration

`app.config.php` will define a Module for the application and **register it**. Upon registration Rhubarb will
ask the module to register any dependant modules. Again those dependencies are given a chance to register their
dependencies and so on.

All modules are resolved and registered before further execution as the registration order of the modules can
be important.

## Stage 3: Module initialisation

All modules will now be asked to initialise. Modules are initialised in reverse depth order - that is the module
loaded furthest down the dependency chain is initialised first and then we work backwards right up to the
initial application module. This is because some modules depend on another in order to replace sections of its
functionality e.g. changing UrlHandlers or database schemas.

## Stage 4: Url Handler resolution (http requests only)

Lastly all Modules are asked to return any defined UrlHandlers. One by one the UrlHandlers are compared with the
incoming request and if suitable for that URL then the UrlHandler is asked to return a response for the request.

## Stage 5: Response generation

UrlHandlers rarely (but occasionally) generate response directly. Mostly they pass control to another response
generating class and pass through the response it generates.