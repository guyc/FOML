<?php
require_once "../FomlConfig.php";

// This test exercises all node types and variations.
Foml::$keepTempFiles = false;
$debug = false;

if (!$debug) {
    Foml::RenderInline("foml/Sampler.foml");
 } else {
    $xslFo = Foml::GenerateXslFo('foml/Sampler.foml');
    print "<pre>";
    print htmlspecialchars($xslFo);
    print "</pre>";
 }

?>