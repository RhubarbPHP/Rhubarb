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

    public function getBinaryData()
    {
        return $this->binaryData;
    }

    protected function printContent()
    {
        ob_clean();

        print $this->binaryData;
    }
}
