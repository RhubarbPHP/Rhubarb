Static Resources
================

Static resources like images, Javascript, stylesheets and documents can be served by Rhubarb using the
`StaticResourceUrlHandler`. This handler can be created with the path to either a single file or a directory
of files:

``` php
$this->addUrlHandlers(
[
    // A single file
    "/static/robots.txt" => new StaticResourceUrlHandler(__DIR__."/../static/robots.txt"),

    // A directory of files
    "/static/images" => new StaticResourceUrlHandler(__DIR__."/../static/images")
]);
```

As static resources require no processing by PHP much higher performance can be achieved by putting
URL rewriting rules to bypass Rhubarb directly into your web server configuration. For example in Apache
the following rules would achieve the same result as above but will be many times faster:

```
RewriteRule ^/static/robots.txt %{DOCUMENT_ROOT}/static/robots.txt [QSA,L,NC]
RewriteRule ^/static/images/$1 %{DOCUMENT_ROOT}/static/images/$1 [QSA,L,NC]
```

To avoid proliferating the configuration file with rewrite rules it's common to find two main rewrite rules
for serving static files:

```
RewriteRule ^/static/(.+) %{DOCUMENT_ROOT}/static/$1 [QSA,L,NC]
RewriteRule ^/deployed/(.+) %{DOCUMENT_ROOT}/deployed/$1 [QSA,L,NC]
```

A common set of rewrite rules for Apache can be found in the
`vendor/rhubarbphp/rhubarb/platform/standard-development-rewrites.conf` directory.

Most project level static resources are then served under the `static` directory. The `deployed` directory is
a requirement of the [RelocationResourceDeploymentProvider](deployment#content) class.

> Remember! A production deployed project should not be serving static resources through a UrlHandler.