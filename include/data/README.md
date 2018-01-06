# WebFrame /Include/Data

The folder contains configuration and transformation XSLT files:

- website.xml - the frame and page definitions
- format-xslt.xml - frame transformation (header, menu, footer, etc)
- other *-xslt.xml - transformations for page content (specific to pages)

## website.xml

Defines:

- the name of XLST transformation file for common elements (header, menu, footer)
- place holder for current page name
- the menu content
    - control name
    - page name to be displayed in menu
- the list of pages
    - control name, the name is used to select a page, e.g. in menu
    - xslt transformation file for the page
    - name of *Page class that will provide page's content
    - optional: other supporting arguments

It is very important to specified here all page that should be visible to the framework.

## format-xslt.xml

Includes the XSLT transformation for common elements (header, menu, footer, script) by the engine class according to the above definitions.

Please note the 'linkToForm' form for posting request to pages and the usage of `onclick="LinkTo()"` method defined in scripts section.

