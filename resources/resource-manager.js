/*
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

/**
 * The client side script manager for Rhubarb.
 *
 * Handles loading of script files and execution of code dependant on that loading
 * including in AJAX callback situations.
 */
window.resourceManager =
{
    singleRequests: [],

    loadResource: function (url, onLoaded) {
        this.loadResources([url], onLoaded);
    },
    addLoadRequest: function (loadRequest) {
        var url = loadRequest.url;

        for (var i = 0; i < this.singleRequests.length; i++) {
            var singleRequest = this.singleRequests[i];

            var result = (function () {
                var request = singleRequest;

                if (request.url == url) {
                    if (request.loaded) {
                        loadRequest.onLoaded();
                        return false;
                    }

                    if (request.loading && loadRequest.onLoaded) {
                        if (request.onLoaded) {
                            var oldOnLoaded = request.onLoaded;

                            request.onLoaded = function () {
                                oldOnLoaded();
                                loadRequest.onLoaded();
                            }
                        }
                        else {
                            request.onLoaded = loadRequest.onLoaded;
                        }

                        return false;
                    }
                }

                return true;
            })();

            if (!result) {
                return;
            }
        }

        this.singleRequests[this.singleRequests.length] = loadRequest;
    },
    loadResources: function (urls, onLoaded) {
        var oldOnLoaded = onLoaded;
        var self = this;

        onLoaded = function () {
            self.runWhenDocumentReady(oldOnLoaded);
        };

        if (urls.length > 1) {
            this.addLoadRequest(
                {
                    "url": urls[0],
                    onLoaded: function () {
                        self.loadResources(urls.slice(1), onLoaded);
                    }
                });
        }
        else {
            this.addLoadRequest(
                {
                    "url": urls[0],
                    "onLoaded": onLoaded
                });
        }

        this.monitorResources();
    },

    getHostnameFromURL: function (url) {
        var regex = new RegExp('^[a-z]+\://([^/]+)', 'im');
        if (regex.test(url)) {
            return regex.exec(url)[1];
        }
        return false;
    },

    monitorResources: function () {
        var self = this;

        for (var i in this.singleRequests) {
            var singleRequest = this.singleRequests[i];

            // This scope ensures that singleRequest is scoped to request for the sake of our callback.
            (function () {
                var request = singleRequest;

                if (request.loaded) {
                    return;
                }

                if (request.loading) {
                    return;
                }

                request.loading = true;

                var hostName = self.getHostnameFromURL(request.url);

                if (false && ( hostName === false || hostName === document.location.host )) {
                    var xmlHttp = self.getHttpRequest();

                    xmlHttp.request = request;
                    xmlHttp.onreadystatechange = function () {
                        if (this.readyState == 4) {
                            if (this.status == 200 || this.status == 304) {
                                self.addResourceToPage(this.request, this.responseText);
                            }
                            else {
                                alert('XML request error: ' + this.statusText + ' (' + this.status + ')');
                            }
                        }
                    };

                    xmlHttp.open('GET', request.url, true);
                    xmlHttp.send(null);
                }
                else {
                    var head;

                    if (document.head) {
                        head = document.head;
                    }
                    else {
                        head = document.getElementsByTagName("head")[0];
                    }

                    var loadedEvent = function () {
                        if (request.loaded) {
                            return;
                        }

                        request.loaded = true;

                        if (request.onLoaded) {
                            request.onLoaded();
                        }
                    };

                    var parts = request.url.split(".");
                    var extension = parts[parts.length - 1].toLowerCase();

                    switch (extension) {
                        case "js":

                            // Check if it's already loaded.
                            var scripts = document.getElementsByTagName('script');

                            for (var s in scripts) {
                                var scriptToCheck = scripts[s];

                                if (scriptToCheck.attributes && scriptToCheck.attributes["src"] && ( scriptToCheck.attributes["src"].value == request.url )) {
                                    request.loaded = true;

                                    if (request.onLoaded) {
                                        request.onLoaded();
                                    }

                                    return;
                                }
                            }

                            var script = document.createElement('script');

                            head.appendChild(script);

                            script.type = 'text/javascript';
                            script.src = request.url;

                            if (script.readyState) {
                                script.onreadystatechange = function () {
                                    if (script.readyState == "loaded" || script.readyState == "complete") {
                                        loadedEvent();
                                    }
                                }
                            }
                            else {
                                script.onload = loadedEvent;
                            }

                            break;
                        case "css":

                            // Check if it's already loaded.
                            var links = document.getElementsByTagName('link');

                            for (var i in links) {
                                if (links.hasOwnProperty(i)) {
                                    var link = links[i];

                                    if (link.attributes && ( link.attributes["href"].value == request.url )) {
                                        request.loaded = true;

                                        if (request.onLoaded) {
                                            request.onLoaded();
                                        }

                                        return;
                                    }
                                }
                            }

                            var newLink = document.createElement("link");

                            head.appendChild(newLink);

                            newLink.type = "text/css";
                            newLink.href = request.url;
                            newLink.rel = "stylesheet";
                            newLink.media = "screen";
                            newLink.onload = loadedEvent;
                            newLink.onreadystatechange = loadedEvent;

                            break;
                    }
                }
            })();
        }
    },

    getHttpRequest: function () {
        if (window.XMLHttpRequest) // Gecko
        {
            return new XMLHttpRequest();
        }
        else if (window.ActiveXObject) // IE
        {
            return new ActiveXObject("MsXml2.XmlHttp");
        }
    },

    addResourceToPage: function (request, source) {
        if (source != null) {
            var parts = request.url.split(".");
            var extension = parts[parts.length - 1].toLowerCase();

            var head = document.getElementsByTagName('HEAD').item(0);

            switch (extension) {
                case "js":
                    var script = document.createElement("script");

                    script.language = "javascript";
                    script.type = "text/javascript";
                    script.defer = true;
                    script.text = source;
                    script.src = request.url;

                    head.appendChild(script);
                    break;
                case "css":
                case "scss":
                    var link = document.createElement("link");

                    link.type = "text/css";
                    link.defer = true;
                    link.text = source;
                    link.href = request.url;
                    link.rel = "stylesheet";
                    link.media = "screen";

                    head.appendChild(link);
                    break;
            }

            request.loaded = true;

            if (request.onLoaded) {
                request.onLoaded();
            }
        }
    },
    documentReady: false,
    runWhenDocumentReady: function (callBack) {
        if (this.documentReady) {
            if (callBack) {
                callBack();
            }

            return;
        }

        var self = this;

        var interval = setInterval(function () {
            if (document.readyState == "complete") {
                clearInterval(interval);

                self.documentReady = true;
                self.runWhenDocumentReady(callBack)
            }
        }, 11);
    }
};