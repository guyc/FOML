<?php
class FomlFilter
{
    public $children = array();
    public $node;
    public $arg;

    // $Node is an instance of parent FomlFilterNode.
    // it is important because it contains a FomlRenderState 
    // instance that indicates the current output mode of this
    // document.
    
    // REVISIT - this expression should be evaluated 
    // in the eval context where all variables are available.
    function __construct($Node, $Arg)
    {
        $this->node = $Node;
        $this->arg = $Arg;
    }

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