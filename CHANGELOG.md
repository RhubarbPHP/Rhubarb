# Change log

### 1.5.0

* Added:   ResourceLoader now preloads resources, tested to work with apache http2 push.

### 1.4.3

* Added:   CredentialsFailedException so that other login exceptions can be detected

### 1.4.2

* Changed: Moved LoginFailedException back as Module.RestAPI requires it
* Added:   New CredentialsLoginProviderInterface so that other modules can expect a login provider that
           has a login method.

### 1.4.1

* Changed: PhpSessionProvider now sets HttpOnly to true

### 1.4.0

* Changed: Removed Login exceptions that are particular to specific login mechanisms. 

### 1.3.22

* Added:   Response method for setting headers from an array

### 1.3.21

* Changed: Added support for custom PUBLIC_ROOT_DIR to specify a top level folder for writing files to be served by the webserver
* Changed: Support for .htaccess with apache 2.4 where REDIRECT_URL is used in preference to SCRIPT_NAME

### 1.3.20

* Changed: RhubarbDateTime removed type declaration due to warning being produced when running PHP 7

### 1.3.19

* Changed:  JSON serialising RhubarbDateTime with an invalid time will return null instead of an empty string 

### 1.3.18

* Changed:  HttpResponse::setCookie() allow for passing null to $expirySecondsFromNow for a session cookie
* Changed:  HttpResponse::setCookie() doesn't call setcookie() if unit testing

### 1.3.17

* Fixed:    PHP 7.1 support with conditional RhubarbDate definition (new $microseconds argument causing string errors)

### 1.3.16

* Added:    String/Template now supports $keepPlaceHolders

### 1.3.15

* Added:    CallableUrlHandler now passes parent handler into the callback

### 1.3.14

* Added:    Enables support for MultiPartFormData PUT requests

### 1.3.13

* Fixed:    Issue with how empty urls on URL handlers are handled.

### 1.3.12

* Fixed:    Unit tests
* Added:    UnitTestingEmailProvider now can be queried for multiple emails.

### 1.3.11

* Fixed:    AssetUrlHandler no longer exits during unit testing.

### 1.3.10

* Fixed:    Depending on circumentsance the AssetUrlHandler could cause additional headers to be output after content causing warnings and 
            content length issues.

### 1.3.9

* Added:    CsvStream has new method getLastItemSize() which returns the number of bytes the last read item was composed of
            Used to allow progress reports of tasks that eat through CSV files from a stream where only the stream length is known.
* Added:    AssetCatalogueProvider::storeAsset has a new optional argument to allow the asset to be stored with a different name
* Added:    AssetCatalogueProvider::storeAsset would detect CSV files as text/plain mime type. File extension detection overrides this
            to text/csv 

### 1.3.8

* Added:    Allowed overriding AssetUrlHandlers streaming behaviour

### 1.3.7

* Fixed:    readHeaders() in CsvStream prevents reading headers twice

### 1.3.6

* Changed:  Removed some direct require statements to allow manual website to load additional vendor autoloaders

### 1.3.5

* Added:    CsvStream now supports being passed an external stream

### 1.3.4

* Added:    Added body css class to HtmlPageSettings

### 1.3.3

* Fixed:    Fix broken static url resource handler

### 1.3.2

* Fixed:    Fix for static url handler not being nestable

### 1.3.1

* Changed:  GreedyUrlHandler is now passed the parent url handler as the first argument if there is a parent handler

### 1.3.0

* Added:    CallableUrlHandler
* Added:    GreedyUrlHandler
* Added:    NumericGreedyUrlHandler
* Added:    Some logging when asset creation from file fails
* Added:    WebRequest didn't document the server() call properly.

### 1.2.3

* Added:    AssetUrlHandler now supports getMissingAssetDetails() to provide fall back files
* Added:    NotFoundResponse added

### 1.2.2

* Change:   AssetUrlHandler no longer uses attachment for content-disposition

### 1.2.1

* Fixed:    Category was not being given to the asset providers correctly.

### 1.2.0

* Added:    Assets concept added with AssetCatalogueProvider and LocalStorageAssetCatalogueProvider classes added.

### 1.1.19

* Added:    Methods allowing removal of all/specific handlers from an Event
* Added:    StringTools::camelCaseToSeparated, allowing change of CamelCase to e.g. snake_case or hyphenated-string
* Changed:  Extra parameter to StringTools::parseTemplateString allowing you to leave placeholders that weren't matched by data
            
### 1.1.18

* Added:    Email can now have a different reply to from the sender

### 1.1.17

* Changed:  Trapped exceptions handled by generic UrlHandler on AJAX request will now just output the message without layout HTML
* Fixed:    Call to HttpHeaders for an HTTP code. HttpHeaders class no longer exists, codes are in Request now

### 1.1.16

* Fixed:    Email::getMimeDocument() now does something...

### 1.1.15

* Fixed:    Fix for writeheaders in CsvStream

### 1.1.14

* Fixed:    EncryptedSession wasn't encrypting

### 1.1.13

* Fixed:    BinaryResponse used ob_clean() which threw notices if not in a buffering context
* Fixed:    CsvStream::writeHeaders() is now public to allow writing empty CSV files with a header.

### 1.1.12

* Added:    Support for automatically encoded XML responses

### 1.1.11

* Added:    Support for text/xml http Accept headers
* Added:    XmlRequest object
* Added:    SimpleXMLTranscoder Helper class for replicating json_encode/json_decode functionality with XML

### 1.1.10

* Fixed:    Request (get merged with post) data wasn't handled property

### 1.1.9

* Fixed:    Reference to nonexistant $serverData in WebRequest
* Changed:  Using file modified time on JS and CSS URLs deployed by RelocationResourceDeploymentProvider
* Changed:  FileResponse and Application clear any level of output buffering before output

### 1.1.8

* Changed:  Means to better set a default sender for emails

### 1.1.7

* Changed:  $staticFile in StaticResourceUrlHandler is now protected

* Fixed:    Dependency injection static memory issues in HttpClient
* Changed:  CURLOPT_SSL_VERIFYPEER should be true on all curl requests
* Changed:  A MultiPartFormDataRequest now provides a MimeDocument as it's payload
* Fixed:    MimeDocument RFC 1341 compliant boundary header detection

### 1.1.6

* Fixed:    Header value case bug introduced in 1.1.5

### 1.1.5

* Added:    HTTP request header case insensitivity support per RFC2616
* Fixed:    Setting web request headers when getallheaders is unavailable
* Fixed:    Unit tests running stand alone, without codeception

### 1.1.4

* Added:    Read strategy for XML traversal and other XML node traversal fixes
* Fixed:    Better handling of times and timezones in RhubarbDateTime
* Added:    HttpResponseException can be given the origination request
* Added:    Debug logging in PhpMailEmailProvider
* Changed:  Application:current() will now create an empty application if one hasn't already been registered

### 1.1.3

* Removed:  Removed conflicting AppSettings class in lieu of WebsiteSettings.
* Added:    Means to remove shared array data from the application
* Fixed:    Bug in shouldTrapException

### 1.1.2

* Added:    WebsiteSettings settings class as a place to set a website's root URL.
* Changed:  Container::instance() now throws ClassMappingException if the mapped class is abstract
* Bug:      RelocationResourceDeploymentProvider not deploying if getDeployedUrls already called.

### 1.1.1

* Changed:  Initialisation order of modules is now reversed    

### 1.1.0

* Removed:  **boot.php**
* Added:    boot-rhubarb.php Does what boot.php used to do - includes auto-loader and sets working directory
* Added:    boot-application.php Includes boot-rhubarb.php and then sets up the default application from either
            settings/app.config.php or the Application class using the environment variable `rhubarb_app`
* Added:    The Event object - a new approach to events
* Changed:  Rhubarb no longer changes the working directory but defines an APPLICATION_ROOT_DIR constant instead.

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
