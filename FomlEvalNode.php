<?php
class FomlEvalNode extends FomlNode
{
    public $mode = Foml::PHP_MODE;
    public $xmlEscape = true;
    const MATCH_RE = "/^=\s*(.*)/";

    function __construct($Matches)
    {
        $this->code = $Matches[1];
    }

    function RenderPrefix()
    {
        print "print ";
        // revisit - would be nice to be able to turn off htmlentities
        if ($this->xmlEscape) {
            print "htmlentities({$this->code},ENT_XML1)";
        } else {
            print $this->code;
        }
            
        print "\n;\n";
    }
}