<?php
class FomlTagNode extends FomlNode
{
    const MATCH_RE = "/^%(([-a-zA-Z0-9]+):)?([-a-zA-Z0-9]+)(\/?)\s*(.*)/";
    
    // %tag(opts) 
    public $namespace;
    public $tag;
    public $selfClose;
    public $args;

    function __construct($Matches)
    {
        $namespace = $Matches[2];
        $tag       = $Matches[3];
        $close     = $Matches[4];  // '/' if tag should self-close, '' otherwise
        $args      = $Matches[5];

        if ($namespace=="") $namespace = FomlParser::$DEFAULT_NAMESPACE;
        $this->namespace = $namespace;
        $this->selfClose = $close=='/';
        $this->tag = $tag;
        $this->ParseArgs($args);
    }

    function ParseArgs($Args)
    {
        // hack for now until I write a real arg parser which support #{$this->count+1}
        if (preg_match("/^\((.*)\)(.*)/", $Args, $matches)) {
            $this->args = $matches[1];
            $Args = $matches[2];
        }
        return $Args;  // return unconsumed code, not currently used.
    }

    function RenderPrefix()
    {
        print "<{$this->namespace}:{$this->tag} {$this->args}";
        if ($this->selfClose) {
            print "/>\n"; 
        } else {
            print ">\n"; 
        }
    }

    function RenderSuffix()
    {
        if (!$this->selfClose) {
            print "</{$this->namespace}:{$this->tag}>\n";
        }
    }

}
?>