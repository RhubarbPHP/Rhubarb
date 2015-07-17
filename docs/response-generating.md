Generating a Response
=====================

A class is considered a response generator if it implements the `GeneratesResponse` interface. The interface
defines a single function `public function generateResponse($request = null)`. You must implement this
and return a Response object for the given Request object.

It is actually quite rare that you would implement as basic a response generating solution as this, instead
using a Presenter from the Leaf module. However if you need the fastest possible response time this combined
with a `ClassMappedUrlHandler` is the simplest option.