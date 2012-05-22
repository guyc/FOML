<?php
require_once "../FomlConfig.php";

// This logic could of course be contained entirely
// within the foml template, but by doing it this
// way we can demonstrate passing variables.

$rows = array();
for ($i=32;$i<128;$i++) {
    $row = new stdClass();
    $row->char = htmlspecialchars(chr($i));
    $row->dec = $i;
    $row->hex = dechex($i);
    $row->bin = sprintf("%08b", $i);
    $rows[] = $row;
}

Foml::RenderInline("foml/ASCIITable.foml", array('rows'=>$rows));

?>