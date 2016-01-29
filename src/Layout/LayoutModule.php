<?php

/*
 *	Copyright 2015 RhubarbPHP
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 */

namespace Rhubarb\Crown\Layout;

require_once __DIR__ . "/../Module.php";

use Rhubarb\Crown\Application;
use Rhubarb\Crown\PhpContext;
use Rhubarb\Crown\Layout\ResponseFilters\LayoutFilter;
use Rhubarb\Crown\Module;

/**
 * Registering this module will surround supported responses with the HTML from a
 * layout template.
 */
class LayoutModule extends Module
{
    /**
     * The path to the chosen layout file.
     *
     * @var string
     */
    private static $layoutClassName = "";

    /**
     * True if enveloping with a layout should be disabled
     *
     * @var bool
     */
    private static $layoutDisabled = false;

    /**
     * A collection of items to add to the head.
     *
     * @var array
     */
    private static $headItems = [];

    /**
     * A collection of items to add to the body
     *
     * @var array
     */
    private static $bodyItems = [];

    public function __construct($defaultLayoutClassName)
    {
        parent::__construct();

        self::setLayoutClassName($defaultLayoutClassName);
    }

    /**
     * Check for an AJAX request and disable layouts if it exists.
     */
    private function checkForAjaxRequest()
    {
        if (Application::runningApplication()->getPhpContext()->isXhrRequest()) {
            self::disableLayout();
        }
    }

    /**
     * Returns the class name of the layout that should be used for the current request.
     *
     * @return string
     */
    public static function getLayoutClassName()
    {
        if (is_callable(self::$layoutClassName)) {
            $func = self::$layoutClassName;
            return $func();
        }

        return self::$layoutClassName;
    }

    /**
     * Turns layout filtering off.
     */
    public static function disableLayout()
    {
        self::$layoutDisabled = true;
    }

    /**
     * Turns layout filtering on.
     */
    public static function enableLayout()
    {
        self::$layoutDisabled = false;
    }

    /**
     * Returns true if the layout has been disabled
     *
     * @return bool
     */
    public static function isDisabled()
    {
        return self::$layoutDisabled;
    }

    protected function initialise()
    {
        parent::initialise();

        $this->responseFilters[] = new LayoutFilter();

        $this->checkForAjaxRequest();
    }

    /**
     * Sets the layout class to use.
     *
     * @param $layoutClassName
     * @throws Exceptions\LayoutNotFoundException
     * @return void
     */
    public static function setLayoutClassName($layoutClassName)
    {
        self::$layoutClassName = $layoutClassName;
    }

    /**
     * Adds some HTML to the head
     *
     * @param $html
     */
    public static function addHeadItem($html)
    {
        self::$headItems[] = $html;
    }

    /**
     * Returns the head items as an HTML string including any script tags required by the script loader.
     *
     * @return string
     */
    public static function getHeadItemsAsHtml()
    {
        return implode("\n", self::$headItems);
    }

    /**
     * Adds an item to the body.
     *
     * @param $html
     */
    public static function addBodyItem($html)
    {
        self::$bodyItems[] = $html;
    }

    /**
     * Gets the body items as an HTML string.
     *
     * @return string
     */
    public static function getBodyItemsAsHtml()
    {
        return implode("
", self::$bodyItems);
    }
}
