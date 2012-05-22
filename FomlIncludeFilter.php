<?php
class FomlIncludeFilter extends FomlFilter
{
    public $fileName;

    function __construct($Arg)
    {
        $this->fileName = $Arg;
    }

    function Render()
    {
        // REVISIT : can't just print the PHP here because we are
        // called from within the eval context.  However doing it this
        // way hides all of the variables, which might be a problem.
        eval('?'.'>'. FomlParser::ParseFile($this->fileName));
    }
}

?>