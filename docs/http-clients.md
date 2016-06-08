HTTP Clients
============

To make an HTTP client you should try to avoid using PHP libraries such as Curl directly. There are many
open source HTTP client libraries that make the job of integrating with HTTP services much easier. Rhubarb
includes a similar library simply called `HttpClient`.

HttpClient is an abstract base class that implements the provider pattern. It has one method
`getResponse(HttpRequest $request)` that is supplied by concrete types.

This approach allows the HTTP client to be swapped with one using a different library or for unit testing
a mock client that can extend the reach of testing with Rhubarb REST APIs.

The default HTTP client is the CurlHttpClient which as the name suggests uses the Curl library to make its
requests.

As a provider you can call the `setProviderClassName()` function to set the required provider for your application:

``` php
HttpClient::setProviderClassName(GuzzleHttpClient::class);
```

To make a request create a `HttpRequest` object, get the client and call `getResponse`:

``` php
$myObject = [1, 2, 3];
$request = new HttpRequest('http://api.myservice.com/', 'put', json_encode($myObject));
```
