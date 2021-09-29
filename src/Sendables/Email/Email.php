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

use Rhubarb\Crown\Logging\Log;
use Rhubarb\Crown\Mime\MimeDocument;
use Rhubarb\Crown\Mime\MimePartBinaryFile;
use Rhubarb\Crown\Mime\MimePartText;
use Rhubarb\Crown\Sendables\Sendable;

/**
 * Represents and channels delivery of an email.
 *
 * This class is abstract as the getText(), getSubject() and getHtml() methods must be implemented to satisfy
 * the implementation.
 */
abstract class Email extends Sendable
{
    private $sender;

    private $attachments = [];

    /**
     * Adds an attachment
     *
     * @param string $path The path to the local file
     * @param string $newName Optionally specify a new name for the file.
     * @return $this
     */
    public function addAttachment($path, $newName = "")
    {
        if ($newName == "") {
            $newName = basename($path);
        }

        $file = new \stdClass();
        $file->path = $path;
        $file->name = $newName;

        $this->attachments[] = $file;

        return $this;
    }

    public function getAttachments()
    {
        return $this->attachments;
    }


    /**
     * Returns the Reply-To recipient for the email.
     *
     * Defaults to calling getSender()
     *
     * @return EmailRecipient
     */
    public function getReplyToRecipient()
    {
        return $this->getSender();
    }

    /**
     * @return string
     */
    abstract public function getSubject();


    /**
     * @return string
     */
    abstract public function getHtml();

    /**
     * @return EmailRecipient
     */
    public function getSender()
    {
        if ($this->sender == null) {
            return $this->getDefaultSender();
        }

        return $this->sender;
    }

    /**
     * Returns a default sender when none is supplied.
     *
     * @return EmailRecipient
     */
    protected function getDefaultSender()
    {
        $emailSettings = EmailSettings::singleton();

        return $emailSettings->defaultSender;
    }

    /**
     * @param EmailRecipient|string $senderEmail Email address as a string, or an EmailRecipient instance
     * @param string $name The name of the sender. This is ignored if passing an EmailRecipient instance for $senderEmail
     * @return $this
     */
    public function setSender($senderEmail, $name = null)
    {
        if (!($senderEmail instanceof EmailRecipient)) {
            $senderEmail = new EmailRecipient($senderEmail, $name);
        }
        $this->sender = $senderEmail;

        return $this;
    }

    public function addRecipientByEmail($recipientEmail, $recipientName = "")
    {
        $this->addRecipient(new EmailRecipient($recipientEmail, $recipientName));

        return $this;
    }

    public function addRecipientsByEmail($recipients)
    {
        if (!is_array($recipients)){
            $recipients = [$recipients];
        }

        foreach ($recipients as $recipient) {
            $this->addRecipientByEmail($recipient);
        }

        return $this;
    }

    public function getRecipientList()
    {
        return implode(", ", $this->getRecipients());
    }

    public function getRecipients()
    {
        $emailSettings = EmailSettings::singleton();

        if ($emailSettings->onlyRecipient) {
            // Only send emails to a test recipient, to prevent emailing real customers from a development environment
            return [$emailSettings->onlyRecipient];
        }

        return parent::getRecipients();
    }

    public function getSendableType()
    {
        return "Email";
    }

    /**
     * Gets a MimeDocument to represent this email.
     *
     * @return MimeDocument
     */
    public function getMimeDocument()
    {
        $textPart = false;
        $htmlPart = false;

        $html = $this->getHtml();
        $text = $this->getText();
        $subject = $this->getSubject();

        $contentType = $this->getContentType();

        /**
         * @var string|bool Tracks which part should contain the text and html parts.
         */
        $alternativePart = false;

        if (count($this->attachments) > 0) {
            $mime = new MimeDocument("multipart/mixed", crc32($html) . crc32($text) . crc32($subject));
        } else {
            $mime = new MimeDocument("multipart/alternative", crc32($html) . crc32($text) . crc32($subject));

            // The outer mime part is our alternative part
            $alternativePart = $mime;
        }

        if ($text != "") {
            $textPart = new MimePartText("text/plain");
            $textPart->setTransformedBody($text);
        }

        if ($html != "") {
            $htmlPart = new MimePartText("text/html");

            if($contentType != ""){
                $htmlPart->addHeader("Content-Type", $contentType);
            }

            $htmlPart->setTransformedBody($html);
        }

        if ($text != "" && $html != "") {
            if (count($this->attachments) > 0) {
                // As this email has attachments we need to create an alternative part to store the text and html
                $alternativePart = new MimeDocument("multipart/alternative");

                $mime->addPart($alternativePart);
            }

            $alternativePart->addPart($textPart);
            $alternativePart->addPart($htmlPart);
        } else {
            if ($text != "") {
                $mime->addPart($textPart);
            }

            if ($html != "") {
                $mime->addPart($htmlPart);
            }
        }

        foreach ($this->attachments as $attachment) {
            $mime->addPart(MimePartBinaryFile::fromLocalPath($attachment->path, $attachment->name));
        }

        $mime->addHeader("To", $this->getRecipientList());
        $mime->addHeader("From", $this->getSender()->getRfcFormat());
        $mime->addHeader("Subject", $this->getSubject());
        $mime->addHeader("Reply-To", $this->getReplyToRecipient());

        return $mime;
    }

    /**
     * Returns a string value which will set the Content-Type header for the html part of the email.
     *
     * @return string
     */
    protected function getContentType(){
        return "";
    }

    public function getBodyRaw()
    {
        $html = $this->getHtml();
        $text = $this->getText();

        if ($html != "" || count($this->attachments) > 0) {
            $mime = $this->getMimeDocument();

            return $mime->getTransformedBody();
        } else {
            return $text;
        }
    }

    /**
     * Returns the headers as a string.
     */
    public function getMailHeadersAsString()
    {
        $headerString = "";

        foreach ($this->getMailHeaders() as $header => $value) {
            $headerString .= $header . ": " . $value . "\r\n";
        }

        return trim($headerString);
    }

    /**
     * Returns the mail headers as an array of key value pairs.
     *
     * @return array
     */
    public function getMailHeaders()
    {
        $headers = [];

        $html = $this->getHtml();
        $text = $this->getText();
        $subject = $this->getSubject();

        if ($html != "" || count($this->attachments) > 0) {
            $mime = new MimeDocument("multipart/alternative", crc32($html) . crc32($text) . crc32($subject));
            $headers = $mime->getHeaders();
        } else {
            $headers["Content-Type"] = "text/plain; charset=utf-8";
        }

        $headers["From"] = (string)$this->getSender();
        $headers["Subject"] = $subject;

        return $headers;
    }

    public function getProviderClassName()
    {
        return EmailProvider::class;
    }

    protected function logSending()
    {
        $subject = $this->getSubject();
        $html = $this->getHtml();
        $text = $this->getText();

        Log::debug("Sending email `" . $subject . "` to recipients: " . $this->getRecipientList(), "EMAIL");

        Log::bulkData(
            "Email content",
            "EMAIL",
            $this->getMailHeadersAsString() . "\r\n\r\n" .
            ($html != "") ? $html : $text
        );
    }
}
