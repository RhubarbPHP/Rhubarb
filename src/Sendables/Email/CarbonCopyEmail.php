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

    public function getRecipients()
    {
        $recipients = [];
        $recipients["To:"] = parent::getRecipients();
        $recipients["CC:"] = $this->getCcRecipients();
        $recipients["BCC:"] = $this->getBccRecipients();

        return $recipients;
    }

    public function toArray()
    {
        $ccRecipientList = [];
        foreach($this->getCcRecipients() as $recipient) {
            $ccRecipientList[] = [
                "name" => $recipient->name,
                "email" => $recipient->email
            ];
        }

        $bccRecipientList = [];
        foreach($this->getBccRecipients() as $recipient) {
            $bccRecipientList[] = [
                "name" => $recipient->name,
                "email" => $recipient->email
            ];
        }

        $data = parent::toArray();
        $data["recipients"] = [
            "To:" => $data["recipients"],
            "CC:" => $ccRecipientList,
            "BCC:" => $bccRecipientList,
        ];

        return $data;
    }

    public static function fromArray($data)
    {
        $email = new CarbonCopyEmail();
        $email->setSubject($data["subject"]);

        foreach($data["recipients"]["To:"] as $recipient){
            $email->addRecipientByEmail($recipient["email"], $recipient["name"]);
        }

        foreach($data["recipients"]["CC:"] as $recipient){
            $email->addCcRecipientByEmail($recipient["email"], $recipient["name"]);
        }

        foreach($data["recipients"]["BCC:"] as $recipient){
            $email->addBccRecipientByEmail($recipient["email"], $recipient["name"]);
        }

        foreach($data["attachments"] as $attachment){
            $email->addAttachment($attachment["path"], $attachment["name"]);
        }

        $email->setReplyToRecipient($data['ReplyTo']['email'], $data['ReplyTo']['name']);

        $email->setText($data["text"]);
        $email->setHtml($data["html"]);
        $email->setSender($data["sender"]["email"], $data["sender"]["name"]);

        return $email;
    }
}
