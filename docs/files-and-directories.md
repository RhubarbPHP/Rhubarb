Files and Directory Layout of a Rhubarb Project
===============================================

All Rhubarb projects look very similar at the root directory level. As Rhubarb favours configuration over convention
very few files and directories are actually required by Rhubarb itself and most are just good patterns to follow.

## Essential Files and Directories

composer.json
:   The Composer configuration file detailing which modules to download and what autoload class paths to
    recognise.

vendor/
:   Composer will download Rhubarb, its modules and any other required third-party dependencies here.

vendor/rhubarbphp/
:   The Composer folder containing any official Rhubarb modules and scaffolds including the main Rhubarb project
    itself.

## Recommended Files and Directories

src/
:   You can put your source code in any directory; your projects composer.json simply needs the autoload settings
    configured correctly. However 'src' is a commonly recognised convention for where a projects source code
    will be - especially amongst other Composer projects.

tests/
:   Again unit tests can be in any folder but by convention we usually create a tests folder at the same level as
    the src folder.

settings/app.config.php
:   For a normal project serving a single application often this file will create and configure the
    root [Application](application#content) object.

settings/site.config.php
:   It's good practice to move any settings unique to the deployment of your application into a separate
    file *which is ignored or excluded from version control*. Settings like database connections and logging
    will differ greatly from your development environment to your production environment and keeping these settings
    out of your project's source control will reduce the chance of deploying development settings into the
    production environment.

static/
:   Static resources used by the layout of your application like images and CSS files can be stored here.
    However most files required by PHP classes should be `deployed` using a deployment provider instead.

deployed/
:   [Deployed files](deployment#content) using the simple `RelocationResourceDeploymentProvider` will end up here.
