Generating a Response
=====================

To generate a response a class should implement the `GeneratesResponseInterface` interface and define the
'generateResponse()' method.

`UrlHandler` objects implement this interface however they usually instantiate a specialist class that generates
the real response.

The class should return a `Response` object, normally a `HtmlResponse` for web requests or a `JsonResponse` for a
REST API.

A simple response generator might look like this:

``` php
class GreetingResponder implements GeneratesResponseInterface
{
    public function generateResponse(Request $request)
    {
        $response = new HtmlResponse();
        $response->setContent("<p>Welcome friend!</p>");

        return $response;
    }
}
```

To connect this with a URL you can use the `ClassMappedUrlHandler`. For example here we configure it to
serve the homepage of our site:

``` php
// In registerUrlHandlers of your module class:
$this->addUrlHandlers(
[
    "/" => new ClassMappedUrlHandler(GreetingResponser::class)
]);
```

Creating HTML directly with a response generating class is usually reserved for very simple, non interactive use cases.
For normal interactive screen design it's better to use a design pattern like MVP which you can find in the [leaf](/manual/module.leaf/) module.
