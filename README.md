FOML PHP Implementation
=======================

What is FOML?
-------------

Format Objects Markup Language is a concise language for PDF document layout.  FOML provides a clean mechanism for generating [XLS-FO](http://www.w3.org/TR/xsl/).  [Apache FOP](http://xmlgraphics.apache.org/fop/index.html) is then used to render the XLS-FO file to a PDF document.

FOML gives you a concise and powerful way to generate PDF reports, and isolates the layout from your application logic.

The good parts of FOML were stolen from HAML.  See the original [blog post about FOML](http://guy.clearwater.com.au/blog/2012/05/19/a-practical-pdf-generator/) for more background.

Installation
------------

1. Clone the FOML repository into your PHP code library.

2. Download the binaries (compiled Java, but they call them binaries) for FOP from [The Apache FOP site](http://xmlgraphics.apache.org/fop/download.html)

 
3. Untar the binaries into a subdirectory inside the FOML directory.

4. Install Java if you don't already have it, and verify that the fop executable will launch okay from the command line.

You should end up with a directory structure something like this:

```
  +-application/
    +--FOML/
       +--examples/
          +--foml/
       +--fop-1.0/
          +--fop    (this it the fop executable)
```

Usage
-----

```
<?php
require_once 'FOML/FomlConfig.php';
Foml::RenderInline('report-template.foml', array('var'=$value, ...));
?>
```


