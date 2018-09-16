# Rhubarb PHP

Rhubarb is an application development framework for PHP. Its focus is on allowing developers to build enterprise ready applications that are fast, scale well, allow for architectural rethinks late in the project and that maximise the potential for code reuse.

## Projects and Modules

Rhubarb is a modular system. Your application only brings in the modules it needs. So if you're building an API you will use the RestAPI module but not the MVP module. This keeps the burden on autoloaders down and makes your application easier to deploy and maintain.

The main framework resides in the `rhubarb` project. This includes the platform bootstraps and a core set of classes called 'Crown'.

Rhubarb uses Composer to import additional packages into the solution including it's own modules. To keep our github organisation tidy other Rhubarb modules reside in projects called `module.[modulename]`. For example `module.modelling` or `module.sendgrid`

## Contributing

Rhubarb is an open source project and as such anyone may make a contribution. Contributions can be made by forking any of the rhubarb projects and making a pull request back to the base fork.

Rhubarb has a list of senior contributors who guard and protect the values of Rhubarb and make the final decision on the merits of each pull request.
