<?php

/**
 * Copyright (c) 2016 RhubarbPHP.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Rhubarb\Crown\Sendables\Email;

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

    public function toDictionary()
    {
        $dataArray = [
            "Subject" => $this->getSubject()
        ];

        return $dataArray;
    }

    public function toArray()
    {
        $recipientList = [];

        foreach($this->getRecipients() as $recipient){
            $recipientList[] = [ "name" => $recipient->name, "email" => $recipient->email ];
        }

        $attachmentList = [];

        foreach($this->getAttachments() as $attachment){
            $attachmentList[] = [ "path" => $attachment->path, "name" => $attachment->name ];
        }

        $data =
            [
                "subject" => $this->getSubject(),
                "recipients" => $recipientList,
                "text" => $this->getText(),
                "html" => $this->getHtml(),
                "sender" => [
                    "email" => $this->getSender()->email,
                    "name" => $this->getSender()->name
                ],
                "attachments" => $attachmentList

            ];

        return $data;
    }

    /**
     * Create's an email from an array of data previously returned via toArray()
     *
     * @param $data
     * @return SimpleEmail
     */
    public static function fromArray($data)
    {
        $email = new SimpleEmail();
        $email->setSubject($data["subject"]);

        foreach($data["recipients"] as $recipient){
            $email->addRecipientByEmail($recipient["email"], $recipient["name"]);
        }

        foreach($data["attachments"] as $attachment){
            $email->addAttachment($attachment["path"], $attachment["name"]);
        }

        $email->setText($data["text"]);
        $email->setHtml($data["html"]);
        $email->setSender($data["sender"]["email"], $data["sender"]["name"]);

        return $email;
    }
}
