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

namespace Rhubarb\Crown\UrlHandlers;

require_once __DIR__ . "/UrlHandler.php";

use Rhubarb\Crown\Exceptions\StaticResource404Exception;
use Rhubarb\Crown\Exceptions\StaticResourceNotFoundException;
use Rhubarb\Crown\Layout\LayoutModule;
use Rhubarb\Crown\Request\Request;
use Rhubarb\Crown\Response\Response;

/**
 * Recognises and retrieves static resources according to the mapping parameters defined in the constructor.
 */
class StaticResourceUrlHandler extends UrlHandler
{
    private $folderOrFilePath = "";

    private $isFolder = false;

    protected $staticFile = false;

    public function __construct($folderOrFilePath, $children = [])
    {
        parent::__construct($children);

        $this->folderOrFilePath = rtrim($folderOrFilePath, "/");

        if (!file_exists($this->folderOrFilePath)) {
            throw new StaticResourceNotFoundException($this->folderOrFilePath);
        }

        if (is_dir($this->folderOrFilePath)) {
            $this->isFolder = true;
        }
    }

    protected function generateResponseForRequest($request = false)
    {
        if ($this->staticFile !== false) {
            $response = new Response();
            LayoutModule::disableLayout();

            if (substr($this->staticFile, -4) == ".css") {
                $mime = "text/css";
            } else {
                $info = new \finfo(FILEINFO_MIME);
                $mime = $info->file($this->staticFile);
            }

            if ($mime !== false) {
                if (substr($this->staticFile, -3) == ".js") {
                    $mime = str_replace("text/plain", "application/javascript", $mime);
                }

                $response->setHeader('Content-Type', $mime);
            }

            ob_start();

            readfile($this->staticFile);

            $response->setContent(ob_get_clean());

            return $response;
        }

        return false;
    }

    public function setUrl($url)
    {
        $this->url = rtrim($url, "/");
    }

    /**
     * Should be implemented to return a true or false as to whether this handler supports the given request.
     *
     * Normally this involves testing the request URI.
     *
     * @param \Rhubarb\Crown\Request\Request $request
     * @throws \Rhubarb\Crown\Exceptions\StaticResource404Exception
     * @return bool
     */
    protected function getMatchingUrlFragment(Request $request, $currentUrlFragment = '')
    {
        $url = $currentUrlFragment;
        if ($this->isFolder) {
            $urlDirectory = dirname($url);
            if (strpos($urlDirectory, $this->url) === 0) {
                $this->staticFile = $this->folderOrFilePath . preg_replace('|^'.$this->url.'|', "", $url);
                if (!file_exists($this->staticFile)) {
                    throw new StaticResource404Exception($url);
                }
            }
        } else {
            if ($this->url == $url) {
                $this->staticFile = $this->folderOrFilePath;
            }
        }
        return ($this->staticFile !== false);
    }
}
