Asset Catalogues
================

Most applications allow for users to upload files or media of some sort. Cataloguing, storing and providing 
access to this media is a chore and is prone to a whole range of security vulnerabilities. It's not uncommon to find
developers dumping uploaded files into a folder that is publically accessible just to allow administrators
to download the files.

In addition storage can quickly become a burden and switching to something like S3 is not a simple migration.
 
Asset Catalogue providers turn these challenges into a simple, secure and flexible pattern. Rolling out 
asset storage becomes a few lines of code that 'just work'.

## Basic concepts

### Categories

When planning out your asset storage you should decide on category names for the content. Category names are
arbitrary but should be unique within your application. Each category represents a set of similar files. For
example "avatars" or "backups".

### AssetCatalogueProviders

Each category is mapped to a particular AssetCatalogueProvider. You can use one of a number of Rhubarb providers
or make your own. The provide is responsible for moving the asset in and out of a particular storage medium,
whether that be a database, S3, or local files.

### Assets

An asset class is a representation of an asset and allows you to save, stream or, if allowed, return a public
URL for the asset.

### Asset Tokens

Assets contain a token which encodes the category, provider and any data the provider needs to locate the asset.
The tokens can be viewed as permanent representations of asset locations and even if the application remaps a
category to a new provider, the asset should still be recoverable using the token.

The token is the value which should be retained and stored by your application
 
### Streams

If you're not familiar with streams, and in particular PHP streams, you will get better performance from handling
large files if you learn a [little bit about them](http://php.net/manual/en/intro.stream.php).

## Setting up asset catalogues

A `LocalStorageAssetCatalogueProvider` is provided with the main Rhubarb project and we'll use this in the following
examples.

To setup a provider you need to register a provider for a given category. The best place to do this is in your
main Application `initialise()` function:

``` php
class MyApplication extends Application
{
    protected function initialise()
    {
        parent::initialise();
        
        AssetCatalogueProvider::setProviderClassName(
            LocalStorageAssetCatalogueProvider::class,
            "avatars"
            );
    }
}
```

The category name is optional. If you don't provide one the provider
will be treated as the default provider for all unmapped categories. 

Providers are registered only with a class name. As asset tokens carry just the name of the provider needed to
fetch an asset, it is important that providers don't have parameters that are easy to change.

Where a provider does have settings that need configured these are usually handled via Settings objects.
 
 ``` php
 class MyApplication extends Application
 {
     protected function initialise()
     {
         parent::initialise();
         
         // Set the top level storage directory for local storage.
         $settings = LocalStorageAssetCatalogueProviderSettings::singleton();
         $settings->storageRootPath = __DIR__."/../data/";
         
         AssetCatalogueProvider::setProviderClassName(
             LocalStorageAssetCatalogueProvider::class,
             "avatars"
             );
     }
 }
 ```
 
Each provider class can be seen as a repository of asset storage for a range of asset categories. While you
need to map each category to that provider it's important to realise there is just one instance of the provider
handling storage and retrieval of the assets. How it chooses to store them isn't a detail you need to worry about.

An application can have several providers registered at any one time for different asset categories.

## Storing an asset

To store an asset you should not use the asset providers directly. Instead use the static `storeAsset` function
on the abstract AssetCatalogueProvider class passing the file path and category of the asset needing stored.

``` php
$asset = AssetCatalogueProvider::storeAsset($uploadedFile, 'avatars');
```

This returns an Asset object containing a token which will need stored in your main application data.

Assets contain the following public properties:

name
:   The name of the original asset file

size
:   The length of the original asset in bytes

mimeType
:   The mime type of the original asset
 
``` php
$token = $asset->getToken();
// Store this token for future retrieval.
```

Tokens are in fact [JWT tokens](https://jwt.io/) and can be between 200 and 300 characters long depending on what data the 
provider is storing in them.

## Retrieving an asset

If you have a token and want to use the stored asset you call the static function `getAsset()`
on `AssetCatalogueProvider`:

``` php
$asset = AssetCatalogueProvider($token);
```

Here `$asset` is the same Asset object we had in the previous example.

## Using an asset

### Writing to file

Use the `writeToFile()` function to create a local file from the asset:

``` php
$asset->writeToFile(DATA_DIR.'/temp.txt');
```

`writeToFile()` uses streams so is memory efficient even with very large files.

### Getting a stream

For custom stream operations simply call `getStream()`. For example if assets are stored in S3 containers the most
efficient way to fetch and transmit them to the client is via streaming. Otherwise for large files you will
quickly exhaust available memory. Additionally if you download the whole asset first the connection will appear
to 'hang' during the download phase before the server starts 'uploading' to the client. It follows also that
overall transfer speed will be much slower. Here's an example of streaming straight from source to the client:

``` php
$streamHandle = $asset->getStream();

while (!feof($streamHandle)) {
    $buffer = fread($streamHandle, 8192);
    echo $buffer;
    ob_flush();
    flush();
    }
    
fclose($streamHandle);
```

### Getting a public URL

Asset catalogue providers can in some cases provide URLs which allow for faster asset retrieval either by using
CDNs or simply allow a web server direct access to the files side stepping the overhead of PHP.

To get the URL simply call `getUrl()` on the Asset object:

``` php
$url = $asset->getUrl();
```

If there is no publically available URL this method will throw an `AssetExposureException`.

### Deleting an asset

Simply call `delete()` on the Asset object:

``` php
$asset->delete();
```

## Available Providers

### LocalStorageAssetCatalogueProvider

This provides storage of files on the filesystem local to the running PHP instance. For most applications this
is the cheapest and simplest option however the biggest issue is that disk space will become a limitation at some
point.

This provider has only two settings in LocalStorageAssetCatalogueProviderSettings:

`$storageRootPath`
:   Sets the folder where all assets are stored.
 
`$rootUrl`
:   If you can provide access to uploaded files publically via the
    webserver directly you can tell the provider what URL stub finds
    the root of the storage directory.
    
    Once set to a non empty string the provider will be able to
    return URLs when `getUrl()` is called on an Asset object.
    
    Consider carefully if exposing assets is something you should allow.
    Public URLs can be favourited, shared and even indexed
    by search engines depending on the circumstances. For more granular
    control you can extend LocalStorageAssetCatalogueProvide into
    multiple derived classes with separate root paths - one for
    publically accessible assets and one for private assets.
    
    Alternatively you can use the AssetUrlHandler to get programmatical
    control over access. 

### S3AssetCatalogueProvider

`gcdtech/module-amazon-s3-asset-catalogue-provider`

Stores assets in an S3 bucket.

This module depends upon `gcdtech/module-aws` and requires you to complete
the standard AWS connection settings:

`$iniCredentialsFile`
:   Optional: The path to an AWS ini credentials file. Read the
    [AWS SDK](http://docs.aws.amazon.com/aws-sdk-php/v3/guide/guide/credentials.html)
    documentation for more details. If no ini file path is specified the AWS SDK will
    try to determine credentials via it's alternative mechanisms.
    
`$region`
:   The region should match the region in which your bucket will reside e.g. eu-west-1

`$profile`
:   Optional: If your credentials file contains multiple profiles you can specify which
    named profile to use otherwise the default profile is assumed.
    
To configure the provider you should use S3AssetCatalogueProviderSettings:

`$bucket`
:   The name of the bucket in AWS which will act as the storage container for your
    assets. The bucket must already exist.
 
`$categoryUrlMap`
:   A mapping array using category names as keys to partial CDN URL stubs.
    Each category needs mapped individually to allow this behaviour to be controlled
    at that level and additionally a CDN like Cloud Front allows you to give different
    URLS to different slices of the bucket with different settings if required.

### MigratableAssetCatalogueProvider

`gcdtech/scaffold-migratable-asset-catalogue-provider`

While assets are represented by tokens which are very robust, sometimes
a particular asset catalogue needs retired and replaced with a new
repository. Most often local storage needs to be 'upgraded' with
cloud storage. Assets themselves are easily migrated from one
provider to another but how can we continue to use the original tokens?

We could 'find and replace' during migration but this might mean
scanning all tables and columns or maintaining a 'map' of where
asset tokens are being stored which is unlikely to be maintained and
when it's really needed would be inaccurate.

A solution is to use the MigratableAssetCatalogueProvider scaffold.
Its benefits are only had by using it from the start - you can't
retro fit it later.

Essentially it proxies the tokens returned by 'real' asset providers,
stores them in repository and returns a new token that just represents
the ID in the repository.

All subsequent interactions are with the 'proxy' asset.

This allows migrations to happen in the background while your
application need never be aware.

> Note while this provides a solution for the application's own use
> of the tokens, it cannot provide a solution if asset public URLs
> have been used or distributed. Once migrated the asset should issue
> correct URLs but anyone trying to access the old URL will probably
> be disappointed to find the asset 'missing'.

## Fallback asset URLs

If the provider you are using doesn't support public URLs or you need
fine grained control over access you can use the AssetUrlHandler to
make individual categories of assets available. This handler  
extracts a category and token from the URL and providing the category
matches that from the token it will retrieve and stream the resource to
the client.

``` php
class MyApplication extends Application
{
    protected function registerUrlHandlers()
    {
        $this->addUrlHandlers(
          [
              "/avatars/" => new AssetUrlHandler('avatars')
          ]
        );
    }
}
```

### Customising asset exposure

If you need to permit asset exposure in a more controlled way simply
extend the AssetUrlHandler class and override the `isPermitted()`
function. You can access the `token` property to analyse the asset
or as is more common consider the status of a login provider for
example.

``` php
class MySecureAssetUrlHandler exteds AssetUrlHandler
{
    protected function isPermitted()
    {
        $login = MyLoginProvider::singleton();
        
        return $login->isLoggedIn();
    }
}
```

Basing exposure permission on login status is so common a stock
version of this is available in Rhubarb called
`LoginValidatedAssetUrlHandler`.

