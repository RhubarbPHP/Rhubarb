<?php

namespace Rhubarb\Crown\Request;

use Rhubarb\Crown\Xml\SimpleXmlTranscoder;

class XmlRequest extends JsonRequest
{
    public function getPayload()
    {
        $context = $this->getOriginatingPhpContext();
        $requestBody = trim($context->getRequestBody());

        return SimpleXmlTranscoder::decode($requestBody, $this->objectsToAssocArrays);
    }
}
