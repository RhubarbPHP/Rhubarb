Rhubarb Basics
==============================

## Table of Contents

### 1. Rhubarb's Processing Pipeline

The purpose of Rhubarb is to generate a "Response" to a "Request". Starting with an overview of Rhubarb
files and folders we learn about how to generate responses by matching requests to url handlers and then
surrounding those responses with a layout.

* [Essential Files and Directories](files-and-directories)
* [Processing Pipeline](processing-pipeline)
* [Response Generators](response-generating)
* [UrlHandler](url-handlers)
* [Request](request)
* [Response](response)
* [Filters and Layout](filters-and-layout)

### 3. Providers

Rhubarb delegates much of its responsibilities to "providers". A provider supplies specific functionality by
following the pattern of a base abstract "Provider" class or interface. Your application can choose which
of the actual providers you want to use and providers can be changed without rebuilding any of your application.
For example you might switch your EmailProvider from the Send Grid provider to the PostmarkApp provider with one
line of code.

* [Session Providers](session-providers)
* [Login Providers](login-providers)
* [Email Providers](email-providers)
* [Encryption and Hash Providers](encryption)
* [Deployment Providers](deployment)

### 4. Logging

Find out how Rhubarb handles logging, how to control what is logged and where it is logged to.

### 5. Useful Classes

The base Rhubarb package includes a range of useful classes used within Rhubarb that you can use in your
applications.

* [Settings](settings)
* [RhubarbDateTime](date-time)
* [StringTools](string-tools)
* [Mime](mime)
* [Xml](xml)
* [DataStreams](data-streams)