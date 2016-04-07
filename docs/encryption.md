Encryption
==========

Rhubarb provides a framework for implementing two types of encryption: two way encryption and one way
or 'hashing'.

Both types of encryption follow the provider pattern: you can set a default hash provider and a default encryption
provider for your application. Systems that require encryption and decryption will use these default providers.

Because making choices about encryption are so critical there are provider engaged by default. You must configure
the provider you want for your encryption before encryption can be used.

Rhubarb includes the following encryption methods as standard:

### Encryption

* Aes256

### Hashing

* Sha512

## Hashing

### Setting the default hash provider

The default hash provider should be set in your application's main module and simply involves calling the static
function `HashProvider::setProviderClassName()`:

~~~ php
HashProvider::setProviderClassName(Sha512HashProvider::class);
~~~

### Creating a hash

While you can instantiate HashProvider classes and use them directly we *strongly recommend* using the default
provider by using:

~~~ php
$hashProvider = HashProvider::getProvider();
~~~

This way if the provider for a project is changed all code using the hash provider starts using the new one
immediately.

To create a hash simply call the `createHash` function:

~~~ php
$hashProvider = HashProvider::getProvider();
$hash = $hashProvider->createHash("stringtobehashed", $salt);
~~~

The second parameter is optional and supplies a salt to the hashing algorithm (if required). If omitted a
random salt will be generated for you.

### Checking hash values

To check if a value matches that of the hash simply call the `compareHash` function:

~~~ php
$hashProvider = HashProvider::getProvider();
$matched = $hashProvider->compareHash($valueToTest, $storedHash);
~~~

This extracts the salt from the stored hash and hashes the comparison value ($valueToTest) and then compares
the two hashes.

`compareHash()` returns true if the hash matches the tested value and false if it does not.

## Encryption

### Setting the default encryption provider

The default encryption provider should also be set by your application's main module and likewise
simply involves calling a static function `EncryptionProvider::setProviderClassName()`:

~~~ php
EncryptionProvider::setProviderClassName("MyAes256EncryptionProvider");
~~~

### Encrypting a value.

In a similar way to hash providers we recommend using the provider pattern to get the encryption provider.

Encrypting is then simply a case of calling the `encrypt` method with the data to be encrypted:

~~~ php
$cipher = EncryptionProvider::getProvider();
$crypt = $cipher->encrypt( "stringtobeencrypted" );
~~~

The `Encrypt` method can take a second parameter which will might be used by the encryption provider to
generate a unique key for the encryption process. This allows you to have a unique key per record rather than
one key for the whole application.

### Decrypting a value.

Decryption is straightforward - simply call `decrypt`

~~~ php
$cipher = EncryptionProvider::getProvider();
$plainText = $cipher->decrypt( "verysecretencryptedstring" );
~~~

If you supplied an additional argument to the `encrypt` method you will need to pass the same arguments here too.

### Creating an encryption provider

Most encryption providers are abstract as two way encryption requires a key. Storage and supply of the key
is a potential weak point that must be carefully thought about in your application. Usually you must extend an
encryption provider and override the `getEncryptionKey()` method:

``` php
class MyAes256EncryptionProvider extends Aes256EncryptionProvider
{
    protected function getEncryptionKey($keySalt = "")
    {
        if (!$keySalt){
            $keySalt = uniqid();
        }

        return sha1($keySalt.microtime());
    }
}
```

