### 1.0.x

* Added codeception
* Added a changelog
* Fixed failing tests
* Added build.xml
* Added depending injection container
* Created Application object
* Refactored Context to PhpContext
* Removed all independant top level statics except for the new Application::current(). All other statics
  come back to this one allowing more than one Rhubarb application to be resident for unit testing.
* Changed providers to use the dependency injection container by way of a Provider trait
* Renamed WebRequest::getUrlBase to WebRequest::createUrl
* Revised a lot of documentation
* Renamed DataStream to RecordStream
* Removed HttpHeaders class and move functionality to the Response object
* Removed send() method on Email and Sendable classes. Call SendableProvider::selectProviderAndSend() instead.
* Added support for Monolog
* Log::writeEntry is now passed the log level