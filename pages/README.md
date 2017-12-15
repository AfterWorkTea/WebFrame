# WebFrame /Pages

The folder contains configuration files and transformation XSLT files:

- data.xml - menu definition and web.js script name for the framework
- website.xml - the frame and page definitions
- web.js - the requires JS to support POST links
- format.xslt.xml - frame transformation (header, menu, footer, etc)
- other *.xslt.xml - transformations for page content (specific to pages)

## website.xml

Defines:

- the name of menu file (here data.xml)
- the name of XLST transformation file for common elements (header, menu, footer)
- definition all available pages giving their:
    - control name, the name is used to select a page, e.g. in menu
    - xslt transformation file for the page
    - name of *Page class that will provide XML string for formatting content

It is very important to specified here all page that should be visible to the framework.

## data.xml

Defines:

- JavaScript file with function to summit post requests.
- Menu content: control names and displayed names. It should include the items, that should be  available from the menu.

## web.js

It includes the function responsible for submitting a routing request to a page with two arguments:

- page that should include the control name of a page to render
- prms might include optional value(s) in a string (access it as `$_POST['prms']` in any *Page class)

## format.xslt.xml

Includes the XSLT transformation for common elements (header, menu, footer) by the engine class according to the above definitions.

Please note the postForm form for posting request to pages and the usage of `onclick="PostFunction()"` method from web.js.

