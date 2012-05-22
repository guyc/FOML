<?php
class FomlInsertNode extends FomlNode
{
    const MATCH_RE = "/^=\s*(.*)/";

    function __construct($Matches)
    {
        $this->code = $Matches[1];
    }

    function RenderPrefix()
    {
        print "<?php print ";
        print $this->code;
        print "; ?>";
        print " \n";
    }
}