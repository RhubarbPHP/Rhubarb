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

namespace Rhubarb\Crown\Mime;

require_once __DIR__ . '/MimePart.php';

class MimePartBinaryFile extends MimePart
{
    public function __construct($mimeType = "application/binary")
    {
        $this->headers["Content-Type"] = $mimeType;
        $this->headers["Content-Transfer-Encoding"] = "base64";
    }

    /**
     * Creates an instance to represent a file stored locally.
     *
     * @param $path
     * @param string $name
     * @return MimePartBinaryFile
     */
    public static function fromLocalPath($path, $name = "")
    {
        if ($name == "") {
            $name = basename($path);
        }

        $part = new MimePartBinaryFile();
        $part->setTransformedBody(file_get_contents($path));
        $part->addHeader("Content-Disposition", "attachment; filename=\"" . $name . "\"");

        return $part;
    }
}
