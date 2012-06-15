<?php
class FomlFilter
{
    public $children = array();

    function RenderPrefix()
    {
    }

    function RenderSuffix()
    {
    }

    function Render()
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
     *  for each child, but some filters might like to 
     *  override this method to handle their children as plain text.
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