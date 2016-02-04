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

namespace Rhubarb\Crown\Sendables\Email;

require_once __DIR__ . '/Email.php';

use Rhubarb\Crown\String\Template;

/**
 * An extension of the base Email class that supports templates for the text and html parts instead of fixed strings
 *
 * This class is the preferred base class for all transactional emails. Only quick debug emails should be sent
 * using the underlying Email class. Individual classes per email make supporting the solution much easier.
 *
 * This class is abstract to ensure that all emails being sent by our systems have a named class and so much easier
 * to find.
 */
abstract class TemplateEmail extends Email
{
    private $recipientData = [];

    public function __construct($recipientData = [])
    {
        $this->recipientData = $recipientData;
    }

    /**
     * Optionally returns a text string to surround the HTML content with.
     *
     * The content should be marked using a placeholder of {Content}
     */
    protected function getTextLayout()
    {
        return "{Content}";
    }

    /**
     * Optionally returns a layout string to surround the HTML content with.
     *
     * The content should be marked using a placeholder of {Content}
     */
    protected function getHtmlLayout()
    {
        return "{Content}";
    }

    abstract protected function getTextTemplateBody();

    abstract protected function getHtmlTemplateBody();

    abstract protected function getSubjectTemplate();

    public function getSubject()
    {
        return Template::parseTemplate($this->getSubjectTemplate(), $this->recipientData);
    }

    public function getText()
    {
        $body = Template::parseTemplate($this->getTextTemplateBody(), $this->recipientData);
        $data = $this->recipientData;
        $data["Content"] = $body;

        return Template::parseTemplate($this->getTextLayout(), $data);
    }

    public function getHtml()
    {
        $body = Template::parseTemplate($this->getHtmlTemplateBody(), $this->recipientData);
        $data = $this->recipientData;
        $data["Content"] = $body;

        return Template::parseTemplate($this->getHtmlLayout(), $data);
    }
}
