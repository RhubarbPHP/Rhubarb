Filters and Layout
==================

After a response has been generated Rhubarb will run the response through a list of filters. Filters are
installed by modules by adding `ResponseFilter` objects to their `$responseFilters` array, usually in the
initialise method.

``` php
protected function initialise()
{
    parent::initialise();

    $this->responseFilters[] = new MyOutputFilter();
}
```

Response filters have a single method `processResponse` which is passed a (Response)[response] object and
can either return a new response or modify the one passed.

The most common use of filters are for Layouts

## Layouts

A 