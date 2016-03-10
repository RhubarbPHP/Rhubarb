Response objects
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

