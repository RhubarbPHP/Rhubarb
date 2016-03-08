URL Handlers
===

URL Handlers perform the role of matching incoming requests based on their URL and generating a response. The
collection of URL Handlers essentially becomes a *router* for incoming URLs.

URL Handlers extend the `UrlHandler` class and must be created and registered within the 'registerUrlHandlers()'
method of a Module.

URL Handlers are registered to handle a particular URL 'stub'. Rhubarb iterates over all the registered handlers
in turn and if the stub is found withing the current URL then the URL is asked to generate the response. A URL
handler may refuse to generate the response in which case Rhubarb will continue to the next matching handler.

For example a URL handler with a stub of '/app/contacts/' would be given the opportunity to handle the following
requests:

~~~
/app/contacts/
/app/contacts/dashboard/
/app/contacts/1/
/app/contacts/1/history/
~~~

A handler can have child handlers in which case the child handlers will normally be asked if they would be able
to generate a response instead. Child handlers also are registered with a stub however this stub should be set
to match against the remaining portion of the URL.

For example if our handler in the above example had a child handler with a stub of "dashboard/" it would accept
the responsibility of generating the response for the second example in the list.

URL handlers can also extract information from the URL. For example if our handler registered for "/app/contacts/"
was a 'CrudUrlHandler' it would understand that the following digit '1' in "/app/contacts/1/" is really a
record ID. The '1' is removed from the remainder of the URL so any child handlers are matched without it.

## Registering a URL Handler

Consider the following module:

~~~ php
<?php
class SiteModule extends Module
{
	public function registerUrlHandlers()
	{
		$this->addUrlHandlers(
		[
			"/images/" => new StaticResourceUrlHandler( "public/images" ),
			"/css/" => new StaticResourceUrlHandler( "public/css" ),
			"/js/" => new StaticResourceUrlHandler( "public/js" )
		] );
	}
}
~~~

Here we create three StaticResource url handlers to process resources under /images, /css and /js by
instantiating them and passing them to the `addUrlHandlers()` method on the `Module` object.

Either pass a stub and a handler:
~~~ php
$this->addUrlHandlers( "/login/", new ClassMappedUrlHandler(LoginPresenter::class)
);
~~~

or an array to register a sequence of handlers:

~~~ php
$this->addUrlHandlers(
[
	"/login/" => new ClassMappedUrlHandler(LoginPresenter::class),
	"/login/forgot-password/" => new ClassMappedUrlHandler(ForgotPassword::class)
] );
~~~

All constructors for handlers should have a final argument that accepts an array of child handlers in
a similar form. The following registration has exactly the same effect as the one above:

~~~ php
$this->addUrlHandlers(
[
	"/login/" => new ClassMappedUrlHandler(LoginPresenter::class,
	[
		"forgot-password/" => new ClassMappedUrlHandler(ForgotPassword::class)
	])
]);
~~~

While both forms have the same overall effect registering children usually results in greater performance as
the child URL will not be instantiated or even considered for URLs that don't start with, in this case, "/login/".
There might be times when you want to 'flatten' the handlers into top level registrations if analytics reveals the
child URL is actually as common or more so than the parent or other top level handlers.

Performance can also be improved generally by ensuring more commonly accessed URLs are registered first, and higher
up the array if registered with other handlers. The less handlers that need consulted to match a URL the faster the
response will be generated.

## Common Url Handlers

Rhubarb comes with a number of commonly used URL handlers:

ClassMappedUrlHandler
:	This maps a single URL to a single class to generate the response
NamespaceMappedUrlHandler
:	This maps a stub URL to a stub class namespace, with any 'folders' appearing after the stub URL being treated as
	additional parts to the class name.
MvpUrlHandler
:	A variant of NamespaceMappedUrlHandler that expects class names to end in the word 'Presenter' without requiring
	the URL to do so.
ValidateLoginUrlHandler
:	Checks the status of a login provider and redirects to a login page if not logged in. Used to protect branches
	of the URL tree from unauthorised access
UrlCapturedDataUrlHandler
:	Allows a single piece of data to be captured inside the URL as a 'folder' after the handler's registered stub.
CrudUrlHandler
:	Can extract an ID from the URL and will select one of two different generating mvp presenters, one for collections,
	and one for items depending on whether an ID is found or not.

## Creating a URL Handler

~~~ php
class MyHandler extends UrlHandler
{
	public function generateResponseForRequest( Request $request, $currentUrlFragment
= "" )
	{
		if ( $someConditionThatChecksTheUrlOrOtherContextualConditions )
		{
			$response = new Response();

			// Do the content generation here and return a Response object:
			$response->setContent( "Sample Response Output" );

			return $response;
		}

		return false;
	}
}
~~~

This is the most basic type of URL Handler you can implement. We extend
`UrlHandler` and only override the `generateResponseForRequest()` method.

`generateResponseForRequest()` should return either a `Response` object encapsulating the generated
response *or* `false` if it was unsuitable for handling the given request.

## Prioritisation of UrlHandlers

Generally URL handlers are considered in turn in the order in which they are registered. Sometimes however you
have to ensure that your url handler is considered before the url handlers of another module. The order of module
registration may be outside of your control. In these cases you need to increase the priority of the url handler
by calling `setPriority()`:

~~~ php
$loginValidator = new ValidateLoginUrlHandler( new SiteLogin(), [ "/restricted-area/" ], "/login/" );
$loginValidator->setPriority( 100 );

$this->addUrlHandlers(
[
	"/" => $loginValidator
]
~~~

The higher the number, the more precedence the url handler is given.

Note that this only works if the URL Handler is registered as a top level handler (i.e.
registered directly with `addUrlHandlers()` and not as a child handler.

## Named UrlHandlers

UrlHandlers can be named by calling `setName()`. Only one top most handler with a given name can
exist in the handlers collection at any one time. This allows modules to setup handlers
but allow subsequently registered modules to replace the handler with a different one.

[Request and Response](request-and-response)