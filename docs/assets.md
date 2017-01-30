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
         $settings = LocalStorageAssetCatalogueSettings::singleton();
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
    $buffer = fread($handle, 8192);
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

This provider has only two settings in LocalStorageAssetCatalogueSettings:

`$storageRootPath`
:   Sets the folder where all assets are stored.
 
`$urlMap`
:   If you can provide access to uploaded files publically via the webserver you can tell the provider about the
    mappings that are in place to allow it to fulfil getUrl()

### S3AssetCatalogueProvider
