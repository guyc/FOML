<?php
class FomlElementNode extends FomlNode
{
    // matches namespace:element[/]args
    const MATCH_RE = "/^%(([-a-zA-Z0-9]+):)?([-a-zA-Z0-9]+)\s*(.*?)(\/?)\s*$/";
    
    // %element(opts) 
    public $namespace;
    public $tag;
    public $selfClose;
    public $args;

    // REVISIT - should not allow children if it is autoclosed

    function __construct($Matches)
    {
        $namespace = $Matches[2];
        $tag       = $Matches[3];
        $args      = $Matches[4];
        $close     = $Matches[5];  // '/' if tag should self-close, '' otherwise

        if ($namespace=="") $namespace = FomlParser::$DEFAULT_NAMESPACE;
        $this->namespace = $namespace;
        $this->selfClose = $close=='/';
        $this->tag = $tag;
        $extra = $this->ParseArgs($args);
        $this->ParseExtra($extra);
    }

    function ParseArgs($Args)
    {
        // hack for now until I write a real arg parser which support #{$this->count+1}
        if (preg_match("/^\((.*)\)(.*)/", $Args, $matches)) {
            $this->args = $matches[1];
            $Args = $matches[2];
        }
        return $Args;
    }

    // The $Extra string may include a shorthand for certain other node types.
    // -.*  : execution context
    // =.*  : evaluation context
    // .*   : text node
    function ParseExtra($Extra)
    {
        $extNodes = array('FomlExecNode', 'FomlEvalNode', 'FomlTextNode');

        // FomlTextNode matches everything

        $Extra = trim($Extra);
        if ($Extra != "") {
            foreach ($extNodes as $nodeClass) {
                if (preg_match($nodeClass::MATCH_RE, $Extra, $matches)) {
                    $this->children[] = new $nodeClass($matches);
                    break;
                }
            }
        }
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