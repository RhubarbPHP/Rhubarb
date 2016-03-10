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
    private $recipients = [];

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
     * @return string
     */
    abstract public function getSubject();


    /**
     * @return string
     */
    abstract public function getHtml();

    /**
     * @return EmailAddress
     */
    public function getSender()
    {
        if ($this->sender == null) {
            $emailSettings = new EmailSettings();

            return $emailSettings->DefaultSender;
        }

        return $this->sender;
    }

    public function setSender($senderEmail, $name = "")
    {
        $this->sender = new EmailAddress($senderEmail, $name);

        return $this;
    }

    public function addRecipient($recipientEmail, $recipientName = "")
    {
        $this->recipients[$recipientEmail] = new EmailAddress($recipientEmail, $recipientName);

        return $this;
    }

    public function addRecipients($recipients)
    {
        foreach ($recipients as $recipient) {
            $this->addRecipient($recipient);
        }

        return $this;
    }

    public function getRecipientList()
    {
        return implode(", ", $this->getRecipients());
    }

    public function getRecipients()
    {
        $emailSettings = new EmailSettings();

        if ($emailSettings->OnlyRecipient) {
            // Only send emails to a test recipient, to prevent emailing real customers from a development environment
            return [$emailSettings->OnlyRecipient];
        }

        return $this->recipients;
    }

    public function getSendableType()
    {
        return "Email";
    }

    public function getMimeDocument()
    {

    }

    public function getBodyRaw()
    {
        $html = $this->getHtml();
        $text = $this->getText();
        $subject = $this->getSubject();

        if ($html != "" || count($this->attachments) > 0) {
            $textPart = false;
            $htmlPart = false;

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

    protected function getProviderClassName()
    {
        return EmailProvider::class;
    }

    protected function logSending()
    {
        $subject = $this->getSubject();
        $html = $this->getHtml();
        $text = $this->getText();

        Log::Debug("Sending email `" . $subject . "` to recipients: " . $this->getRecipientList(), "EMAIL");

        Log::BulkData(
            "Email content",
            "EMAIL",
            $this->getMailHeadersAsString() . "\r\n\r\n" .
            ($html != "") ? $html : $text
        );
    }
}
