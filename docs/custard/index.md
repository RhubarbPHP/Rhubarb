Custard
=======

Custard is an extension of the [Symfony Command](http://symfony.com/doc/current/console.html) object that
provides a familiar interface for running command line scripts on the terminal.

The Symfony Command object is used by a number of frameworks and composer so
is instantly recognisable in it's usage.

## Running Custard

Custard is a development requirement of the main Rhubarb framework however if you
project needs it for running scripts in a deployed environment you should add it
to your composer.json:

``` javascript
"require": {
    "rhubarbphp/custard": "^1.0.9"
}
```

As per composer's default behaviour the command itself will be found in the vendor
folder on the following path:

``` bash
vendor/rhubarbphp/custard/bin/custard
```

As it's such a popular tool it's common for projects to tell composer to put binaries
in a folder closer to your top level folder. This is achieved by adding a `bin-dir`
section to your composer.json:

``` javascript
"config": {
    "bin-dir": "bin/"
}
```

Now you can run custard with:

``` bash
bin/custard
```

This is the form used in examples in this documentation.

## Listing available commands.

Running custard with no arguments returns the list of available commands. Commands
are prefixed with a short 'namespace' which usually hints at the module providing
the command.

``` bash
bin/custard
```

## Running a command

Simply add the name of the command on the end:

``` bash
bin/custard stem:document-models
```

Many commands require interaction on the terminal to answer questions and some provide
switches and arguments you can supply to skip the interactions. Consult the documentation
for each individual command for those details.
