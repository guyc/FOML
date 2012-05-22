<?php

// TODO - filter doesn't yet pass child nodes to filter
// For now I'm just using it for :include('file.foml')

class FomlFilterNode extends FomlNode
{
    // this re is probably not rich enough to support args using variable expansion
    const MATCH_RE = "/^:([a-zA-Z0-9]+)(\((.*)\))?/";

    public $filterClass;

    function __construct($Matches)
    {
        $filter = $Matches[1];
        $args   = $Matches[3];
        if (isset(FomlParser::$FILTER_CLASSES[$filter]))
        {
            $this->filterClass = FomlParser::$FILTER_CLASSES[$filter];
            $this->args = $args;
        } else {
            throw new FomlException("Unknown filter '{$filter}'");
        }
    }

    function RenderPrefix()
    {
        print "<?php {$this->filterClass}::CreateAndRender({$this->args}) ?>\n";
    }

}
