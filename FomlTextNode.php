<?php
class FomlTextNode extends FomlNode
{
    const MATCH_RE = "/.*/";

    public $text;  // text to be printed


    function __construct($Matches)
    {
        $this->text = $Matches[0];
    }

    function RenderPrefix()
    {
        print $this->text;
        print "\n";
    }

}
?>