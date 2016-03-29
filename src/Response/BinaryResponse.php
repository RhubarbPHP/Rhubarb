<?php

namespace Rhubarb\Crown\Response;

class BinaryResponse extends Response
{
    private $binaryData;

    public function __construct($generator, $binaryData, $contentType, $fileName = "")
    {
        parent::__construct($generator);

        $this->binaryData = $binaryData;
        $this->setHeader('Content-Type', $contentType);

        if ($fileName != "") {
            $this->setHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"');
        }

        $this->setHeader('Content-Transfer-Encoding', 'binary');
        $this->setHeader('Expires', '0');
        $this->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0');
        $this->setHeader('Pragma', 'public');
        $this->setHeader('Content-Length', strlen($binaryData));
    }

    protected function printContent()
    {
        ob_clean();

        print $this->binaryData;
    }
}
