<?php
class FomlNode
{
    public $children = array();

    function RenderPrefix()
    {
    }

    function RenderSuffix()
    {
    }

    function Render($Indent=0)
    {
        $this->RenderPrefix();
        foreach ($this->children as $child) {
            $child->Render();
        }
        $this->RenderSuffix();
    }

    /*
     *  $Children is an array of FomlParseTree.
     *  The default behaviour here is just to Generate FomlNodes
     *  for each child, but some nodes (like FomlCommentNode)
     *  will override this method to handle their children differently
     */
    function AddChildren($Children)
    {
        // Generate and add children to the node.
        foreach ($Children as $child) {
            $this->children[] = $child->Generate();
        }
    }
}
?>