FOML PHP Implementation
=======================

Installation
------------

1. Copy the files from the repository into your PHP code library.

2. Download the binaries for FOP from [The Apache FOP site](http://xmlgraphics.apache.org/fop/download.html) 

3. Install the binaries into a directory under the Foml library.

4. Install Java if necessary, and ensure that the fop binary will launch okay.

You should end up with a directory structure something like this:

```
  +-application/
    +--foml/
       +--examples/
          +--foml/
       +--fop-1.0/
```

Usage
-----

```
<?php
require_once 'foml/Foml.php';
Foml::RenderInline('report-template.foml', array('var'=$value, ...));
?>
```

FOML Syntax
-----------

