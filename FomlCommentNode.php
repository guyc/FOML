<?php 
class FomlCommentNode extends FomlNode
{
    const MATCH_RE = "/^\/(.*)/";
    
    function __construct($Matches)
    {
        $this->text = $Matches[1];
    }

    function RenderPrefix()
    {
        // todo - should we escape inner "-->" to avoid early closing?
        print "<!-- {$this->text} ";
    }

    function RenderSuffix()
    {
        print "-->\n";
    }
}
?>