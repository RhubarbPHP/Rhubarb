URL Handlers
===

URL Handlers perform the role of handling incoming requests to generate a response with the collection of
URL Handlers essentially becoming a *router* for incoming URLs.

URL Handlers extend the `\Gcd\Core\UrlHandlers\UrlHandler` class and must be created and registered
within a Module.

Some modules may create one or more UrlHandlers to implement behaviour essential to the module.
Others will simple make the new handlers available as part of it's class library.

URL Handlers are registered to handle a particular stub of the URL 'tree'. However even if the
handler matches it's registered URL 'stub' the handler may decided not to handle the request due to
other factors (e.g. a REST handler might refuse to handle the URL because the client will not accept
XML)

## Using a URL Handler

Consider the following configuration file `settings/app.config.php`:

~~~ php
<?php

namespace Site;

use Gcd\Core\Module;
use Gcd\Core\StaticResource\StaticResourceModule;
use Gcd\Core\StaticResource\UrlHandlers\StaticResource;

include( "libraries/core/modules/StaticResource/StaticResourceModule.class.php" );

class SiteModule extends Module
{
	public function __construct()
	{
		$this->namespace = __NAMESPACE__;
		$this->AddClassPath( "classes" );

		parent::__construct();
	}

	public function Initialise()
	{
		$this->AddUrlHandlers(
		[
			"/images/" => new StaticResource( "public/images" ),
			"/css/" => new StaticResource( "public/css" ),
			"/js/" => new StaticResource( "public/js" )
		] );
	}
}

Module::RegisterModule( new StaticResourceModule() );
Module::RegisterModule( new SiteModule() );
~~~

Here we create three StaticResource handlers to process resources under /images, /css and /js by
instantiating them and passing them to the `AddUrlHandlers()` method on the `Module` object.

This method can take a range of parameter formats:

### Parameter Format 1: Pass the URL stub and the handler as two parameters.

~~~ php
$this->AddUrlHandlers( "/login/", new ClassMappedUrlHandler( "\MySite\Presenters\LoginPresenter" )
);
~~~

### Parameter Format 2: Pass an array structure:

~~~ php
$this->AddUrlHandlers(
[
	"/login/" => new ClassMappedUrlHandler( "\MySite\Presenters\LoginPresenter" )
] );
~~~

This allows for the registration of multiple handlers in one go.

## Common Url Handlers

Core comes with a number of commonly used URL handlers:

ClassMappedUrlHandler
:	This maps a single URL to a single class to generate the response
NamespaceMappedUrlHandler
:	This maps a stub URL to a stub class namespace, with any 'folders' appear aftering the stub URL being treated as
	additional parts to the class name.
MvpUrlHandler
:	A variant of NamespaceMappedUrlHandler that expects class names to end in the word 'Presenter'
ValidateLoginUrlHandler
:	Checks the status of a login provider and redirects to a login page if not logged in. Used to protected branches
	of the URL tree from unauthorised access
UrlCapturedDataUrlHandler
:	Allows a single piece of data to be captured inside the URL as a 'folder' after the handler's registered URL.
CrudUrlHandler
:	Uses two different generating mvp presenters, one for collections, and one for items if a numeric ID has been
	added to the URL.

## Sub Handlers

While in most applications all handlers can be registered in this way it can be neater, and
sometimes required, to consider a handler as being a sub handler of a parent. In nearly all cases
this is when the URL of the child is 'beneath' that of the parent.

In some cases this is the only way to ensure the child handler gets considered as the URL the parent
is handling could contain variable data that the child would not be able to predicate. For example
consider these two UrlHandlers and the URLS they need to handle:

~~~ php
$this->AddUrlHandlers(
[
	"/tickets/" => new CrudUrlHandler( "Ticket", "\MySite\Presenters\Tickets" ),
	"/tickets/{x}/files/" => new CrudUrlHandler( "Attachment",
"\MySite\Presenters\Tickets\Attachments" )
] );
~~~

The CrudUrlHandler for tickets will process a URL such as:

`/tickets/3/`

However there is no way the UrlHandler for ticket attachments can register itself as the full URL
can't be predicated.

Instead we make the attachment handler a child of the ticket handler:

~~~ php
$this->AddUrlHandlers(
[
	"/tickets/" => new CrudUrlHandler( "Ticket", "\MySite\Presenters\Tickets", "", [],
	[
		"files/" => new CrudUrlHandler( "Attachment",
"\MySite\Presenters\Tickets\Attachments" )
	] )
] );
~~~

Notice that the URL has changed to simply `files/`. This is because it will be given the URL that
the parent decided to handle during execution. Notice also that the children are passed in array
with an identical structure. All UrlHandlers can accept an array of children - this should be the
last parameter in the constructor.

In general using sub handlers is strongly recommended as it greatly clarifies your application
config.

## Creating a URL Handler

~~~ php
class MyHandler extends UrlHandler
{
	public function GenerateResponseForRequest( Request $request, $currentUrlFragment
= "" )
	{
		if ( $someConditionThatChecksTheUrlOrOtherContextualConditions )
		{
			$response = new Response();

			// Do the content generation here and return a Reponse object:
			$response->SetContent( "Sample Response Output" );

			return $response;
		}

		return false;
	}
}
~~~

This is the most basic type of URL Handler you can implement. We extend
`UrlHandler` and only override the `GenerateResponseForRequest()` method.

`GenerateResponseForRequest()` should return either a `Response` object encapsulating the generated
response *or* `false` if it was unsuitable for handling the given request.

## Prioritisation of UrlHandlers

With a module url handlers are considered in turn in the order in which they are added to the
`$urlHandlers` array. Sometimes however you have to ensure that your url handler is considered
before the url handlers of another module. The order of module registration may be outside of your
control. In these cases you need to increase the priority of the url handler by calling
`SetPriority()`:

~~~ php
$this->urlHandlers[ "login" ] = $loginValidator = new ValidateLoginUrlHandler( new SiteLogin(),
array( "/restricted-area/" ), "/login/" );
$loginValidator->SetPriority( 100 );
~~~

The higher the number, the more precedence the url handler is given.

Note that this only works if the URL Handler is registered as a top most parent handler (i.e.
registered directly with `Module::AddChildUrlHandlers()`

## Named UrlHandlers

UrlHandlers can be named by calling `SetName()`. Only one top most handler with a given name can
exist in the
handlers collection at any one time. This allows modules to setup handling of known subject areas
but provide for future modules, or indeed the application, to replace the handler with a different
one.

[Request and Response](request-and-response)