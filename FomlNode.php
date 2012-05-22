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
}
?>