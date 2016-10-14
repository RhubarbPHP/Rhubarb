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

namespace Rhubarb\Crown\Exceptions;

/**
 * A wrapper exception to allow exceptions that don't extend from Core Exception to be treated as though they do.
 */
class NonRhubarbException extends RhubarbException
{
    /**
     * @param \Exception|\Error $nonRhubarbException
     */
    public function __construct($nonRhubarbException)
    {
        parent::__construct(basename(get_class($nonRhubarbException)) . " - " . $nonRhubarbException->getMessage());

        $this->line = $nonRhubarbException->getLine();
        $this->file = $nonRhubarbException->getFile();
    }
}
