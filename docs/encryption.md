Encryption
===

The Core provides a framework for implementing two types of encryption: normal two way encryption and one way
or 'hashing'. As reviewing and updating encryption techniques is so critical we've implemented a dependency injection
approach to selecting the particular type of encryption or hashing required by a project.

The core module includes the following encryption methods as standard:

### Encryption

* Aes256

### Hashing

* Sha512

## Hashing

### Setting the default hash provider

The default hash provider should be set by your application's modules.php file and simply involves calling the static
function `HashProvider::SetHashProviderClassName()`:

~~~ php
\Gcd\Core\Encryption\HashProvider::SetHashProviderClassName( "Gcd\Core\Encryption\Sha512HashProvider" );
~~~

### Creating a hash

While you can instantiate HashProvider classes and use them directly we *strongly recommend* using the dependency injection pattern and getting the hash provider by using:

~~~ php
$hashProvider = \Gcd\Core\Encryption\HashProvider::GetHashProvider();
~~~

This approach ensures that if the provider for a project is replaced, all coding using it is changed at a stroke.

To create a hash simply call the `CreateHash` function:

~~~ php
$hashProvider = \Gcd\Core\Encryption\HashProvider::GetHashProvider();
$hash = $hashProvider->CreateHash( "stringtobehashed", $salt );
~~~

The second parameter is optional and supplies a salt to the hashing algorithm (if required). If omitted a random salt will be used.

### Comparing hashes

Simply call the `CompareHash` function:

~~~ php
$hashProvider = \Gcd\Core\Encryption\HashProvider::GetHashProvider();
$matched = $hashProvider->CompareHash( $valueToTest, $storedHash );
~~~

A function is required to do the comparison (rather than a simple `==` ) because we must rehash the `$valueToTest` with the same salt as the original. Therefore we must first extract the salt from the stored hash which is a problem for the HashProvider to solve.

`CompareHash` returns a simple boolean.

## Encryption

### Setting the default encryption provider

In addition the default encryption provider should also be set by your application's modules.php file and likewise
simply involves calling the static function `EncryptionProvider::SetEncryptionProviderClassName()`:

~~~ php
\Gcd\Core\Encryption\EncryptionProvider::SetEncryptionProviderClassName( "Gcd\Core\Encryption\MyAes256EncryptionProvider" );
~~~

### Encrypting a value.

In a similar way to the hash providers we recommend using dependency injection patterns to get the encryption provider.

Encrypting is then simply a case of calling the `Encrypt` method with the data to be encrypted:

~~~ php
$cipher = \Gcd\Core\Encryption\EncryptionProvider::GetEncryptionProvider();
$crypt = $cipher->Encrypt( "stringtobeencrypted" );
~~~

The `Encrypt` method can take a second parameter which will supply additional information used by the encryption provider to generate a suitable key.

### Decrypting a value.

Really simple:

~~~ php
$cipher = \Gcd\Core\Encryption\EncryptionProvider::GetEncryptionProvider();
$plainText = $cipher->Decrypt( "verysecretencryptedstring" );
~~~

Of course if you supplied the second parameter to the `Encrypt` method you need to pass the same value here too.