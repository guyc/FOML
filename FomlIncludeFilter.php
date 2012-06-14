<?php
class FomlIncludeFilter extends FomlFilter
{
    public $args;

    function __construct($Args)
    {
        // REVISIT - this expression should be evaluated 
        // in the eval context where all variables are available.
        $this->args = $Args;
    }

    function RenderPrefix()
    {
        // REVISIT : can't just print the PHP here because we are
        // called from within the eval context.  However doing it this
        // way hides all of the variables, which might be a problem.
        $fileName = eval("return {$this->args};");
        eval('?'.'>'. FomlParser::ParseFile($fileName));
    }
}

?>