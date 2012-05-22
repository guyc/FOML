<?php
class FomlDoctypeNode extends FomlNode
{
    const MATCH_RE = "/^\!\!\!\s*/";

    function __construct($Matches)
    {
        // Nothing to save.
    }

    function RenderPrefix()
    {
        print '<?php echo \'<?xml version="1.0" encoding="utf-8"?>\'?>';
    }

    function RenderSuffix()
    {
    }

        

}
?>