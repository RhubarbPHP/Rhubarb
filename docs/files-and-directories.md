Essential Files and Directories
===============================

All Rhubarb projects look very similar at the root directory level. Some of the files and sub directories are
required by Rhubarb itself and some are just good patterns to follow.

## Essential Rhubarb Files and Directories

vendor/
:   Composer will download Rhubarb, its modules and any other required third-party dependencies here.

vendor/rhubarbphp/
:   The Composer folder containing all of the main Rhubarb modules and scaffolds.

composer.json
:   The Composer configuration file detailing which modules to download and what autoload class paths to
    recognise.

## Recommended Files and Directories

src/
:   You can put your source code in any directory; your projects composer.json simply needs the autoload settings
    configured correctly. However 'src' is a commonly recognised convention for where a projects source code
    will be - especially amongst other Composer projects.

tests/
:   Again unit tests can be in any folder but by convention we usually create a tests folder at the same level as
    the src folder.

settings/site.config.php
:   It's good practice to move any settings unique to the deployment of your app into a separate
    file *which is ignored from VCS*. Settings like database connections and logging will differ greatly from
    your development environment to your production environment and keeping these settings out of your project's
    source control will reduce the chance of deploying development settings into the production environment.

static/
:   Static resources used by the layout of your application like images and CSS files can be stored here.
    However most files required by PHP classes should be `deployed` using a deployment provider instead.

deployed/
:   Deployed files using the simple `RelocationResourceDeploymentProvider` will end up here.