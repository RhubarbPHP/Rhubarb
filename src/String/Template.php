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
     * @param $keepPlaceholders
     *
     * @return string
     */
    public static function parseTemplate($template, $data, $keepPlaceholders = false)
    {
        $t = $template;

        $hasMatches = preg_match_all("/[{]([^}]+)[}]/", $t, $matches);

        if ($hasMatches) {
            foreach ($matches[ 0 ] as $key => $match) {
                if (isset($data[$matches[1][$key]])) {
                    $template = str_replace($match, $data[$matches[1][$key]], $template);
                } else if (!$keepPlaceholders) {
                    $template = str_replace($match, '', $template);
                }
            }
        }

        return $template;
    }
}
