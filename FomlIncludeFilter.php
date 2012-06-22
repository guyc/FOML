<?php
class FomlIncludeFilter extends FomlFilter
{
    public $args;

    function RenderPrefix()
    {
        // REVISIT : can't just print the PHP here because we are
        // called from within the eval context.  However doing it this
        // way hides all of the variables, which might be a problem.
        $fileName = eval("return {$this->arg};");
        eval('?'.'>'. FomlParser::ParseFile($fileName, $this->node->state));
    }
}

?>