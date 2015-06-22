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

namespace Rhubarb\Crown\Email;

require_once __DIR__ . '/Email.php';

/**
 * Provides a simple Email implementation letting the user set the text for
 * subject and message externally instead of creating an email class.
 *
 * Useful for short debug email messages, however normal transactional emails
 * should derive from TemplateEmail instead.
 */
class SimpleEmail extends Email
{
    private $html;

    private $text;

    private $subject;

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @return string
     */
    public function getHtml()
    {
        return $this->html;
    }

    public function setHtml($html)
    {
        $this->html = $html;

        return $this;
    }

    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }
}