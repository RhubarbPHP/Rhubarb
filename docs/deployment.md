Deploying Resources
===

Rapid enterprise development depends upon being able to reuse packages of code to speed development and to
build on the stability of often used classes and patterns. Much of what we reuse requires not only PHP but also
Javascript, CSS and image resources. The challenge to developers is how to create packages of reusable code that
allow for presenting static resources with predictable URLs.

To avoid the dreaded cut-and-paste (effectively importing the resources into your local 'static' folder) and
renaming URLs Rhubarb offers a deployment mechanism that lets PHP code be distributed with other resource files
and have those resource files guarantee to be served zero configuration.

## The Rhubarb approach: Deployment Packages

In the Rhubarb we tackle this problem though the use of DeploymentPackage classes. These classes are created by any
PHP class requiring deployable resources by implementing the `DeployableInterface` interface and the
`getDeploymentPackage()` method.

DeploymentPackages run a particular action when invoked. The most often used DeploymentPackage is the
`ResourceDeploymentPackage` which is responsible for handling the challenge of deploying resource files.

If your application requires actions to be taken upon deployment of the latest version of the code you should develop
a custom DeploymentPackage to handle this.

### Deploying resource files

If your class needs to deploy resources use the `ResourceDeploymentPackage`

~~~ php
class MyReusableThing implements DeployableInterface
{
    public function getDeploymentPackage()
    {
        $package = new ResourceDeploymentPackage();
        $package->resourcesToDeploy[] = __DIR__."/MyReusableThing.js";
        $package->resourcesToDeploy[] = __DIR__."/MyReusableThing.css";

        return $package;
    }
}
~~~

Note that the location of the resources is along side the PHP class allowing the use of `__DIR__` to locate them. This
also ensures the files stay together as a package if moved around.

#### Triggering Deployment

The class `MyReusableThing` now describes a deployment package containing two resources, a Javascript file and a
CSS file.

To ensure these files are available to your application's client you need to trigger the deployment by
either calling the `deploy()` method on the package directly or invoking the deployment tools. It's normal practice
to trigger the deployment during a request but only if the developerMode application setting is true. On a production
server you must then call the deploy scripts to manually invoke deployment.

~~~ php
class MyReusableThing implements DeployableInterface
{
    public function getDeploymentPackage()
    {
        $package = new ResourceDeploymentPackage();
        $package->resourcesToDeploy[] = __DIR__."/MyReusableThing.js";
        $package->resourcesToDeploy[] = __DIR__."/MyReusableThing.css";

        return $package;
    }

    public function DoMyThing()
    {
        // Doing my thing....
        // Done my thing....

        $application = Application::current();

        if ($application->developerMode) {
            $deploymentPackage->deploy();
        }
    }
}
~~~

### Using deployed resources

ResourceDeploymentPackages pass their list of resource files to your applications designated ResourceDeploymentHandler
class. The default for this is the RelocationResourceDeploymentHandler which copies the resource into a deployed
folder while preserving the rest of the folder path from the top level folder of your application. For example given
the paths:

~~~
Application Root: /wwwroot/my-app
Resource Path: /wwwroot/my-app/classes/Controls/MyWidget/MyWidget.js
~~~

The deployed file would reside at:

~~~
/wwwroot/my-app/deployed/classes/Controls/MyWidget/MyWidget.js
~~~

This then needs to be exposed through a StaticResourceUrlHandler or a webserver rewrite rule.

Javascript and CSS files can then be incorporated into the page using the ResourceLoader class.

## Running Deployment with custard

To be continued....
