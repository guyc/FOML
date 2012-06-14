<?php
class FomlParseTree
{
    public $text; // text for this node 
    public $indent;
    public $lineNo;
    public $children = array();  // array of child nodes 

    function __construct($Text=null)
    {
        $this->text = $Text;
    }

    /* 
     * Converts the string in $Text into a parse
     * tree with a FomlParseTree for each line, and
     * all child-lines stored as an array of FomlParseTree's
     * in $tree->children[]
     */
    static function Parse($Text)
    {
        // stack keeps the ordered list of all open objects
        // the top item has a magic negative indent so it can't be popped

        $tree = new FomlParseTree();
        $tree->indent = -1;  // unbeatable indent!
        $stack = array($tree);
        $top = $tree;
        foreach (FomlParseTree::ParseLines($Text) as $line) {
            
            // pop items from stack with greater indents - they are closed
            while ($top->indent >= $line->indent) {
                array_pop($stack);
                $top = $stack[count($stack)-1];
            }

            // add self as child of the last open item
            if ($top) {
                $top->children[] = $line;
            }

            // push self to the stack
            $stack[] = $top = $line;
        }
        return $tree;
    }

    static function ParseLines($Text)
    {
        $continuation = "/(.*)\s+\|$/";
        $lines = preg_split('/$\R?^/m', $Text);
        $nodes = array();
        while (!empty($lines)) {
            $line = array_shift($lines);
            
            // join subsequent lines ending in "\s+|"
            // What if only a single line ends in \s+|?
            // we treat it as a 1 line continuation (ie drop the |)
            if (preg_match($continuation, $line, $matches)) {
                $line = $matches[1];
                while (!empty($lines) && preg_match($continuation, $lines[0], $matches)) {
                    $line .= $matches[1];
                    array_shift($lines);

                }
            }

            // requires at least one non-white character to match
            // so pure whitespace lines will be skipped here
            if (preg_match("/^(\s*)([^\s].*)/", $line, $matches)) {
                $tree = new FomlParseTree();
                $tree->indent = strlen($matches[1]);
                $tree->text = $matches[2];
                $nodes[] = $tree;
            }
        }
        return $nodes;
    }

    /*
     * Flatten this node and all of its children to plain text
     */
    function ToText()
    {
        $text = $this->text;
        foreach ($this->children as $child) {
            $text.=" ".$child->ToText();
        }
        return $text;
    }

    /*
     * Generates a FomlDoc from the parse tree.
     * Returns a FomlDoc().  Walks the FomlParseTree
     * and generates a corresponding tree of FomlNode 
     * objects.
     */
    function Generate()
    {
        // The top node of the tree is distinguised by having $text==null
        // and that causes it to be a FomlDoc which is really just a container
        // for the child nodes.
        if ($this->text === null) {
            $node = new FomlDoc();
        } else {
            $node = null;
            // try each class in order and keep the first one that returns a node
            // since the last one is a text node which will match anything, this should always match
            foreach (FomlParser::$NODE_CLASSES as $nodeClass) {
                if (preg_match($nodeClass::MATCH_RE, $this->text, $matches)) {
                    $node = new $nodeClass($matches);
                    break;
                }
            }
        }
        // TODO : assert $node is not null here

        $node->AddChildren($this->children);

        return $node;
    }

}
?>
