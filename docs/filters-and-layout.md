Filters and Layout
==================

After a response has been generated Rhubarb will run the response through a list of filters. Filters are
installed by modules by adding `ResponseFilter` objects to their `$responseFilters` array, usually in the
initialise method.

``` php
protected function initialise()
{
    parent::initialise();

    $this->responseFilters[] = new MyOutputFilter();
}
```

Response filters have a single method `processResponse` which is passed a (Response)[response] object and
can either return a new response or modify the one passed.

The most common use of filters are for Layouts

## Layouts

The normal pattern for generating HTML output is to let your response generating classes create the core
HTML of the content area of a page and the surround the content with HTML provided by a layout class. The layout
of a page frequently includes the header, footer and navigation elements of a page which are usually the same
on every page.

Layout support is provided by the `LayoutModule` which supplies a filter to activate layout support.

### Layout Classes

A layout class extends `Layout` and should at a minimum override the `printLayout($content)` method:

``` php
class MyLayout extends Layout
{
    protected function printLayout($content)
    {
        ?>
        <html>
            <title>My site</title>
        </html>
        <body>
        <?php
        print $content;
        ?>
        </body>
        </html>
        <?php
    }
}
```

The argument `$content` contains the HTML for the centre portion of the page and the class simply needs
to print this string in context of the surrounding HTML.

### The default layout

Every application can have one default layout. This matches the requirements of nearly all web applications where
nearly all pages share a common layout while perhaps only the login page differs radically enough to warrant a
separate layout.

To register the default layout you simply pass it as an argument when constructing the `LayoutModule` class during
module registration. For example in your layout module:

``` php
class MyModule extends Module
{
    public function getModules()
    {
        return [
            new LayoutModule(MyLayout::class)
        ];
    }
}
```

### Changing Layouts

To switch from using the default layout to a specific layout for an individual page you can call the static
function `LayoutModule::setLayoutClassName()` at any point during response generation - *as long as it is
before the layout itself is used to filter the response*. You cannot therefore decide to change the layout from
within the default layout itself.

Alternatively instead of passing a class name of the default layout to the constructor of the `LayoutModule` you
can pass a callback function instead. This function will be called for every request and should return the name
of the class name to use. It can access the request object or any other contextual information to make this
choice:

``` php
class MyModule extends Module
{
    public function getModules()
    {
        return [
            new LayoutModule(function(){
                $request = Application::current()->currentRequest();

                if (stripos("/login", $request->uri) === 0){
                    return LoginLayout::class;
                }

                return MyLayout::class;
            })
        ];
    }
}
```

### Extending Layouts

As layouts are normal PHP classes they are easily extended from boiler plates. Its a good pattern to start
building a library of boiler plates for different type of applications to make layout creation easier.

When adopting this strategy your base class should implement printLayout and call a range of other
protected functions to make it easier to extend without having to cut and paste large blocks of HTML:

``` php
class MyBaseLayout extends Layout
{
    protected function getTitle()
    {
    }

    protected function printNavigation()
    {
    }

    protected function printLayout($content)
    {
        ?>
        <html>
            <title><?=getTitle();?></title>
        </html>
        <body>
            <div class="nav">
            <?php
            $this->printNavigation();
            ?>
            </div>
            <div class="main">
            <?php
            print $content;
            ?>
            </div>
        </body>
        </html>
        <?php
    }
}
```

### Resource Manager and injection of other head and body items

Many modules of Rhubarb use the ResourceLoader to load javascript and css files. In addition many individual
features of websites will require injecting HTML into the head or body tags of the output. To accommodate both
of these features you should make the calls below:


``` php
class MyLayout extends Layout
{
    protected function printLayout($content)
    {
        ?>
        <html>
            <title>My site</title>
            <?= ResourceLoader::getResourceInjectionHtml(); ?>
            <?= LayoutModule::getHeadItemsAsHtml(); ?>
        </html>
        <body>
            <?= LayoutModule::getBodyItemsAsHtml(); ?>
            <?php
            print $content;
            ?>
        </body>
        </html>
        <?php
    }
}
```

With these in place, to inject your own HTML during response generation simply call the static methods
`LayoutModule::addHeadItem()` and `LayoutModule::addBodyItem()` with the HTML you need injected.

### Disabling layouts

If during content generation you decide you need to turn layout filtering off, simply call the static method
`LayoutModule::disableLayout()`. `LayoutModule::enableLayout()` will reverse that decision.