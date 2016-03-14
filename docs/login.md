Handling Logins
===============

A login provider connects your application with session management and presents information about the state
of a user's login.

While the most common login provider is the `ModelLoginProvider` the login provider approach allows logins
to be connected with REST APIs, oAuth and social logins.

You do not need to register your login provider in any special way; it extends `Session` and so you can
simply call the `singleton()` function to instantiate it.

~~~ php
class SiteLogin extends ModelLoginProvider
{
	public function __construct()
	{
		parent::__construct( "User", "Email", "Password", "Activated" );
	}
}

$siteLogin = SiteLogin::singleton();

if ( $siteLogin->isLoggedIn() )
{
	print "Hello ".$siteLogin->getModel()->Forename;
}
else
{
	$siteLogin->login( "joe@hotmail.com", "abc123" );
}
~~~

### Validating Login Status

Call `isLoggedIn()` to determine if the current user is logged in or not.

### Attempting a Login

Most login providers have a `login()` method which you normally pass the username and password.
The method will return true if the login succeeds or false if it does not. In addition your login
provider will usually update the session to record the logged in status.

Note that most login providers require your application to have selected a hash provider.

### Logging Out

To logout simply call the `logout` function in your user interface.

### Redirecting users when not logged in

The most common purpose of a login is to restrict portions of your application from view until the user
authenticates. The login framework provides a simple way to redirect users to your login page if
they aren't logged in using [URL Handlers](url-handlers). This can of course be done manually on
a page by page basis, however it is hard to maintain and error prone.

To restrict users simply add the following URL Handler in your application's module configuration:

~~~ php
$this->addUrlHandlers( "/restricted-area/", new ValidateLoginUrlHandler( new SiteLogin(), "/login/"
) );
~~~

The parameters to the `ValidateLoginUrlHandler` are:

$loginProvider
:    The instance of your login provider

$loginUrl
:    The URL of where to redirect the user to make their login attempt.

The URL registered with the handler (/restricted-area/) is the area that will be kept secure behind
the login.

### Common Login Providers

#### ModelLoginProvider

This provider binds a model object as it's authentication source. It provides support for
logging in using a username and password and optionally tracking an activation status using a boolean column.
Extend the class and supply the model class name, and username, password and active column names
through the constructor:

~~~ php
class SiteLogin extends ModelLoginProvider
{
	public function __construct()
	{
		parent::__construct("User", "Email", "Password", "Activated");
	}
}
~~~

Once logged in you can call the `getModel()` method to return the Model representing the logged in
user. Note that `getModel()` throws a `NotLoggedInException` if the user isn't logged in at the
time.

###### Password Hashing

ModelLoginProvider will expect the passwords to be hashed and will use the standard HashProvider
configured for your application to attempt to rehash the user supplied password.

###### Attempting a Login

Call the Login function and pass the user supplied username and password as arguments:

~~~ php
$login = SiteLogin::singleton();
$login->login($username, $password);
~~~

If the login attempt is successful `true` will be returned. If it's not successful an exception
will be thrown, either:

LoginFailedException
:    Thrown if the login username or password was incorrect.

LoginDisabledException
:    Thrown if the login was correct but the login is inactive

### Forcing a Login

On occasion you may need to log a user in, without a username and a password. An example of such
an occasion is to automatically log someone in when you have just registered on a site. Another
example is when you need to provide a means for an Administrator to 'become' any of the users on
a site or system.

`LoginProvider` has a `forceLogin()` method which should be called to achieve this. Most
login providers, for example `ModelLoginProvider` will extend this method and require that a
`User` model is passed to it.

~~~ php
$billyTheKid = User::fromUsername( "bkid" );

$loginProvider = new MySitesLoginProvider();
$loginProvider->forceLogin( $billyTheKid );
~~~

It goes without saying that you should be very careful how this function is called in your code
to be completely sure it isn't possible for a user to log themselves in without a password.