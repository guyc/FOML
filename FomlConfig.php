<?php

/*
  This file just includes all of the Foml classes.
  If you are NOT using an autoloader you need to 
  require this file.
*/

require_once 'Foml.php';

// FOML node definitions
require_once 'FomlNode.php';
require_once 'FomlCommentNode.php';
require_once 'FomlDoctypeNode.php';
require_once 'FomlEvalNode.php';
require_once 'FomlExecNode.php';
require_once 'FomlFilterNode.php';
require_once 'FomlElementNode.php';
require_once 'FomlTextNode.php';
require_once 'FomlExecNode.php';

// FOML filter definitions
require_once 'FomlFilter.php';
require_once 'FomlIncludeFilter.php';

// FOML document definitions
require_once 'FomlDoc.php';
require_once 'FomlParseTree.php';
require_once 'FomlParser.php';

?>