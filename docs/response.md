Responses
================

The purpose of Rhubarb is to generate responses. Responses are encapsulated in `Response` objects and just like
the `Request` object it has sub types which best represent individual types of response.

Response objects have a content which should be set by calling `setContent` on the response object:

``` php
$response = new JsonResponse();
$response->setContent([
    "foo" => "bar"
    ]);
```

The actual content type should match the type of response object being returned.

Response objects store an HTTP response code and a list of response headers in addition to the content. If you
need to modify the response code it is usually best to extend the response class. For example a 401 Not Authorised
response should be issued by returning a `NotAuthorisedResponse` object rather than trying to change the
response code of a normal HtmlResponse.

### Standard response objects

HtmlResponse
:   The normal response for HTML page responses

JsonResponse
:   A response encapsulating a JSON payload

XmlReponse
:   A response encapsulating an XML payload

BinaryResponse
:   Used to push binary data to the client

FileResponse
:   Used to push a local file to the client as a download

NotAuthorisedResponse
:   Used to issue a 401 response

RedirectResponse
:   Issues a Location header to a new URL

## Setting the response code

A response should carry an appropriate HTTP response code. The default will be a 200 response for a normal
HtmlResponse. To change the response code call `setResponseCode()` and `setResponseMessage()` with the appropriate
code and message.

The Response object defines a range of constants mapped to common response codes for your convenience.

## Setting HTTP headers

To set a header call the `setHeader` function on the response object:

``` php
$response = new HtmlResponse();
$response->setHeader('Cache-Control', 'no-cache');
```

Headers are not set until the response is transmitted to the browser. Thus if you set a header on the response
but a later exception causes a different response to be transmitted your headers will not (correctly) end up
with the client.

## Forcing a response

Sometimes in the middle of generating a standard response your application may need to abort the generation of the
standard response and issue another type of response instead. If you're using a pattern like MVP the code that
needs to issue the new response has no control over the actual generation of the response class.

On occasions like this simply throw a `ForceResponseException` passing the response you would like returned to the client.

This exception is handled by the Application class and it will immediately push your response to the client and
terminate.

``` php
if (!$user->isAdministrator()){
    // This user shouldn't have access - kick them out!
    throw new ForceResponseException(new NotAuthorisedResponse());
}
```