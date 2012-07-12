<?php
class FomlExecNode extends FomlNode
{
    public $mode = Foml::PHP_MODE;
    const MATCH_RE = "/^-\s*(.*)/";
    /*
     * Examples
     *   - $x = 0
     *   - foreach ($i=0;$i<10;$i++)
     */

    /* 
     * else and elseif are interesting.  They don't need special handling - the tree 
     * will naturally render like this:
     * < ? php if (condition) { ? >
     *   stuff
     * < ? php } ? >
     * < ? php else { ? > 
     *    more stuff
     * < ? php } ? >
     * because 
     */

    public $code;
    public $hasBlock;   // if true enclose block in {} otherwise end command with ";"

    function __construct($Matches)
    {
        $this->code = $Matches[1];
        $this->hasBlock = preg_match("/^(foreach|for|while|if|else|elseif)/", $this->code);
    }

    function RenderPrefix()
    {
        // The first \n is required to escape any trailing // or # comment.
        // It would also be possible to end and restart the PHP code block to abort the comment.'
        print $this->code;
        if ($this->hasBlock && count($this->children)>0) {
            print "\n{\n";
        } else {
            print "\n;\n";
        }
    }

    function RenderSuffix()
    {
        if ($this->hasBlock && count($this->children)>0) {
            print "}\n";
        }
    }
}
