<?php
class FomlEvalNode extends FomlNode
{
    public $mode = Foml::PHP_MODE;
    const MATCH_RE = "/^=\s*(.*)/";

    function __construct($Matches)
    {
        $this->code = $Matches[1];
    }

    function RenderPrefix()
    {
        print "print Foml::XmlEntities(";
        print $this->code;
        print ");\n";
    }
}