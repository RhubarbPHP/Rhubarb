Sessions
===

In the Rhubarb user sessions are managed through Session objects. Session objects handle the content of a session
while session providers handle the storage and retrieval of sessions.

Much like settings sessions are scoped into individually named classes. A single application might have multiple
session objects in use at one time handling different session data. It's even possible for these different
session objects to be using different session providers and thereby storing the session data in different ways.

~~~ php
class LoginSession extends Session
{
    public bool $loggedIn;

    public string $username;
}

$loginSession = LoginSession::singleton();
$loginSession->loggedIn = true;
~~~

The `Session` class extends the `Settings` class and as you've seen is used in a very similar way.

To store the session for recovery on the next page make a call to `storeSession()`

~~~ php
$loginSession->storeSession();
~~~

## Changing the default session provider

The default session provider is `PhpSessionProvider` which will store and recover session data using the standard
PHP session functions. To change the default provider call the static method `SessionProvider::setProviderClassName()`
from your application configuration passing the name of the class to be used. Like all provider setup calls we pass
the name of the class and not an instance of it so that scripts which don't require sessions won't waste
resources with objects they don't need.

## Changing the session provider for an individual session class

You can have different sessions using different session providers. Simply override the `getNewSessionProvider()`
method of your individual session provider.

~~~ php
class LoginSession extends Session
{
	protected function getNewSessionProvider()
	{
		return new ModelSessionProvider();
	}
}
~~~
