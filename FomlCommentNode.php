<?php 
class FomlCommentNode extends FomlNode
{
    public $text;
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

    function AddChildren($Children)
    {
        // don't add children to the tree, but flatten all children
        // out into one big comment.
        foreach ($Children as $child) {
            $this->text .= " ".$child->ToText();
        }
    }
}
?>