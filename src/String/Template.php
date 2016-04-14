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

namespace Rhubarb\Crown\String;

/**
 * Provides a very simple template parser
 *
 */
class Template
{
    /**
     * Parses a template and replaces placeholders with values from $data
     *
     * @param $template
     * @param $data
     * @return string
     */
    public static function parseTemplate($template, $data)
    {
        $t = $template;

        while (preg_match("/[{]([^}]+)[}]/", $t, $match)) {
            $field = $match[1];

            $t = str_replace($match[0], "", $t);
            $template = str_replace($match[0], $data[$field], $template);
        }

        return $template;
    }
}
