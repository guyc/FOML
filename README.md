FOML for PHP
============

What is FOML?
-------------

Format Objects Markup Language (FOML) is a concise language for PDF document layout.  This library provides a framework for converting
FOML into [XLS-FO](http://www.w3.org/TR/xsl/), and bindings to [Apache FOP](http://xmlgraphics.apache.org/fop/index.html) to render
the output as a PDF document.

FOML gives you a concise and powerful way to generate PDF reports, and isolates the layout from your application logic.
It keeps the PDF layout logic the hell off your application's lawn.

The good parts of FOML were stolen from HAML.  See the original [blog post about FOML](http://guy.clearwater.com.au/blog/2012/05/19/a-practical-pdf-generator/) for more background.

Installation
------------

1. Clone the FOML repository into your PHP code library.

2. Download the binaries (compiled Java, but they call them binaries) for FOP from [The Apache FOP site](http://xmlgraphics.apache.org/fop/download.html)

 
3. Untar the FOP binaries into a subdirectory inside the FOML directory.

4. Install Java if you don't already have it, and verify that the fop executable will launch okay from the command line.  "fop-1.0/fop -version" should generate a version number.

Your installation will have a directory structure something like this:

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
Foml::RenderInline('templates/report-template.foml', array('var'=$value, ...));
?>

```

### ```Foml::RenderInline($Template, $ArgsAssocArray)```

Expands the FOML document file named by $Template, making the values passed in $ArgsAssocArray available
as local variables during the expansion.  Finally the resulting PDF document is streamed inline.  Generally
this will cause the PDF to open in the viewers browser window.

### ```Foml::RenderAttachment($Template,$Filename,$ArgsAssocArray)```

Expands the FOML document file named by $Template, making the values passed in $ArgsAssocArray available
as local variables during the expansion.  Finally the resulting PDF document is streamed as an attachment
with the $Filename supplied as the default filename.  Generally this will cause browser to prompt the user
to save the PDF file.

FOML Syntax
===========

The following syntax is currently supported.  Note that HAML-style string expansion #{$likethis} is not yet supported.

XML Elements
------------

### XML Element: %
The percent character at the begining of a line, followed immediately
by an element name will be rendrered as and XML element.  The default namespace
"fo" will be used if a namespace is not specified.  To use the "xsl"
namespace, it should be explicitly specified (eg %xsl:stylesheet).  
A trailing '/' at the end of the line, following any arguments, will cause
the node to be auto-closed.

Attributes may optionally be specified following the element name.  For example

```
%table-cell
  %flow(flow-name="xsl-region-body")
    %xsl:apply-templates(select="/simpdoca/section[4]")
```

renders as

```
<fo:table-cell>
  <fo:flow flow-name="xsl-region-body"> 
    <xsl:apply-templates select="/simpdoca/section[4]"></xsl:apply-templates>
  </fo:flow>
</fo:table-cell>
```

### XML Comments
A line beginning with a single forward-slash renders as an XML comment in the output stream.
Any following lines at deeper indent will be included in the comment.

```
 / Go in peace.
   Oh, I am at peace.
```

renders as

```
<!-- Go in peace. Oh, I am at peace. -->
```

### XML Prolog: !!!

Automatically inserts the XML prolog.  This should be the first
line of any complete FOML document.

```
!!!
```

renders as

```
<?xml version="1.0" encoding="utf-8"?>
```

Code Evaluation
---------------

### PHP Evaluation: =
The equals sign followed by a PHP expression renders the evaluation
of the expression into the output.  A trailing semicolon is NOT required.

```
%flow
 = $firstname.' '.$lastname
```
renders as
```
<fo:flow>
  Walt Kowalski
</fo:flow>
```

### PHP Execution: -
A hyphen flowed by PHP code causes the PHP code to be executed.  Any output
to stdio will be rendered into the output stream.  If the code contains one
of if, else, while, for, foreach or elseif, the {} brackets are NOT required.

```
- $list = array('Walt','Kowalski')
- foreach ($list as $element)
    %table-cell
      %block 
        = $element
```
renders as
```
<fo:table-cell>
  <fo:block>
    Walt
  </fo:block>
</fo:table-cell>
<fo:table-cell>
  <fo:block>
    Kowalski
  </fo:block>
</fo:table-cell>
```

### Line Continuation: |

The pipe character as the last character and following whitespace will cause all subsequent lines also ending in a pipe character
to be treated as if they were part of the same line.  Note that, like HAML, even the final line must end with a pipe character.

```
%table(                            |
  table-layout="fixed"             |
  width="100%"                     |
  border-collapse="separate"       |
)                                  |

```
renders as
```
<fo:table table-layout="fixed" width="100%" border-collapse="separate">
</fo:table>
```

### Code Comments
A line beginning with '-#' is entirely ignored and does not generate any output in the XLS-FO stream.
```
 -# Comment text
```

Filters
-------

### Filter :include(filename)
The :include filter takes one argument; the name of the file to be included.
```
 :include('Filename.foml')
```
