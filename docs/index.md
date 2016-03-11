Rhubarb Basics
==============================

## Table of Contents

### 1. Essential Concepts

The purpose of any web framework like Rhubarb is to generate a "Response" to answer a "Request". Find out how
Rhubarb selects and generates responses.

* [Essential Files and Directories](files-and-directories)
* [Processing Overview](processing-overview)
* [Generating Responses](response-generating)
* [UrlHandler](url-handlers)
* [Request](request)
* [Response](response)
* [Filters and Layout](filters-and-layout)

### 2. Sendables

### 3. Sessions

### 4. Handling Logins

### 5. Handling Logins

Many real world integrations are delegated in Rhubarb to "providers". A provider gives you functionality by
following the pattern of a base abstract "Provider" class or interface. Your application can choose which
of the actual providers you want to use and providers can be changed without rebuilding any of your application.
For example you might switch your EmailProvider from the Send Grid provider to the PostmarkApp provider with one
line of code.

* [Session Providers](session-providers)
* [Login Providers](login-providers)
* [Email Providers](email-providers)
* [Encryption and Hash Providers](encryption)
* [Deployment Providers](deployment)

### 3. Logging

Find out how Rhubarb handles logging, how to control what is logged and where it is logged to.

### 4. Useful Classes

The base Rhubarb package includes a range of useful classes used within Rhubarb that you can use in your
applications.

* [Settings](settings)
* [RhubarbDateTime](date-time)
* [StringTools](string-tools)
* [Mime](mime)
* [Xml](xml)
* [DataStreams](data-streams)

### 5. Dependency Injection

Rhubarb implements a dependency injection container. Find out what it is and how to use it.