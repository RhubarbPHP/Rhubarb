<?php

namespace Rhubarb\Crown\Sendables\SMS;

require_once __DIR__ . '/SMS.php';

class SimpleSMS extends SMS
{
    private $text;

    public function setText($text)
    {
        $this->text = $text;
    }

    public function getText()
    {
        return $this->getText();
    }

    public function toArray()
    {
        $recipientList = [];

        foreach ($this->getRecipients() as $recipient) {
            $recipientList[] = ["name" => $recipient->name, "number" => $recipient->number];
        }

        $data =
            [
                "recipients" => $recipientList,
                "text" => $this->getText()
            ];

        return $data;
    }

    /**
     * Create's an sms from an array of data previously returned via toArray()
     *
     * @param $data
     * @return SimpleSMS
     */
    public static function fromArray($data)
    {
        $email = new SimpleSMS();

        foreach ($data["recipients"] as $recipient) {
            $email->addRecipient($recipient["number"], $recipient["name"]);
        }

        $email->setText($data["text"]);

        return $email;
    }
}
