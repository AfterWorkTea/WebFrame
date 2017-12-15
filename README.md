# WebFrame

*Light XML-centric PHP, MySQL framework for developers.*

See demo at YouTube [TechBob-WebFrame](https://youtu.be/rXss-Oyox6I)

## Introduction

The WebFrame framework is meant to help developers build light and liable applications using PHP and MySQL:

- write-it-once-use-many-times
- configuration driven easy menu and page control
- easy linking pages and passing parameters by POST
- table support (DB side sorting on columns, paging, filtering)
- and more... 

IT is not a Content Management System as it requires knowledge of XML/XSLT, HTML, CSS, PHP and DB stored procedure programming. 

## Characteristics 

- Framework - a strategy and basic functions that help to build a web application. 
- Light - minimal programming to achieve basic functionality 
- XML-centric
    - DB stored functions return XML 
    - the XML enters XSLT processor to generate HTML

The example requires PHP with XLST processing and PDO support to run.

## Assumptions 

- one-selectable web frame (menu, header, feed) and multiple pages
- data processing in DB stored procedures (e.g. paging, sorting, filtering)
- stored functions returns XML
- only XSLT processor generates HTML (no HTML in PHP)
- referencing pages by their simple names, e.g. "home"
- easy page linking and parameters passing by POST

## Structure 

- css - formatting
- db - scripts for generating DB and stored procedures
- include - the frame engine, page classes and DB acces class
- pages - configuration files and XSLT format files
- pic - included pictures

Open the folders for more information.

Thank you!

*More is coming soon....*

