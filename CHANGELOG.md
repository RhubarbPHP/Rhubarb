# Change log

### 1.0.0

* Added:    codeception
* Added:    **A changelog!**
* Fixed:    Failing tests
* Added:    build.xml
* Added:    **depending injection container**
* Added:    **Application object**
* Changed:  Context to PhpContext
* Removed:  All independant top level statics except for the new Application::current(). All other statics
            come back to this one allowing more than one Rhubarb application to be resident for unit testing.
* Changed:  Providers to use the dependency injection container by way of a Provider trait
* Renamed:  WebRequest::getUrlBase to WebRequest::createUrl
* Revised:  A lot of documentation
* Renamed:  DataStream to RecordStream
* Removed:  HttpHeaders class and move functionality to the Response object
* Removed:  send() method on Email and Sendable classes. Call SendableProvider::selectProviderAndSend() instead.
* Added:    Support for Monolog
* Changed:  Log::writeEntry is now passed the log level
* Removed:  References to jquery in composer.json and ResourceLoader
* Removed:  Test and function in LayoutModule to disable layouts for XHR requests - already handled in Application