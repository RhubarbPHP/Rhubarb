<?php

namespace Rhubarb\Crown\Sendables\Email;

class CarbonCopyEmail extends SimpleEmail
{
    private $ccRecipients = [];

    private $bccRecipients = [];

    public function addCcRecipient(EmailRecipient $emailRecipient)
    {
        $this->ccRecipients[] = $emailRecipient;
    }

    public function addCcRecipientByEmail($recipientEmail, $recipientName = "")
    {
        $this->addCcRecipient(new EmailRecipient($recipientEmail, $recipientName));

        return $this;
    }

    public function addCcRecipients($recipients)
    {
        if (!is_array($recipients)){
            $recipients = [$recipients];
        }

        foreach ($recipients as $recipient) {
            if ($recipient instanceof EmailRecipient) {
                $this->addCcRecipient($recipient);
            }

            $this->addCcRecipientByEmail($recipient);
        }

        return $this;
    }

    public function addBccRecipient(EmailRecipient $emailRecipient)
    {
        $this->bccRecipients[] = $emailRecipient;
    }

    public function addBccRecipientByEmail($recipientEmail, $recipientName = "")
    {
        $this->addBccRecipient(new EmailRecipient($recipientEmail, $recipientName));

        return $this;
    }

    public function addBccRecipients($recipients)
    {
        if (!is_array($recipients)){
            $recipients = [$recipients];
        }

        foreach ($recipients as $recipient) {
            if ($recipient instanceof EmailRecipient) {
                $this->addBccRecipient($recipient);
            }

            $this->addBccRecipientByEmail($recipient);
        }

        return $this;
    }

    public function getCcRecipientList()
    {
        return implode(", ", $this->getCcRecipients());
    }

    public function getBccRecipientList()
    {
        return implode(", ", $this->getBccRecipients());
    }

    public function getCcRecipients()
    {
        $emailSettings = EmailSettings::singleton();
        if ($emailSettings->onlyCcRecipient) {
            // Only send emails to a test recipient, to prevent emailing real customers from a development environment
            return [$emailSettings->onlyCcRecipient];
        }

        return $this->ccRecipients;
    }

    public function getBccRecipients()
    {
        $emailSettings = EmailSettings::singleton();
        if ($emailSettings->onlyBccRecipient) {
            // Only send emails to a test recipient, to prevent emailing real customers from a development environment
            return [$emailSettings->onlyBccRecipient];
        }

        return $this->bccRecipients;
    }

    public function getMimeDocument()
    {
        $mime = parent::getMimeDocument();

        $mime->addHeader("Cc", $this->getCcRecipientList());
        $mime->addHeader("Bcc", $this->getBccRecipientList());

        return $mime;
    }

    public function getMailHeaders()
    {
        $headers = parent::getMailHeaders();

        $headers["Cc"] = $this->getCcRecipientList();
        $headers["Bcc"] = $this->getBccRecipientList();

        return $headers;
    }
}
