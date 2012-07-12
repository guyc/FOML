<style type="text/css" media="all">@import "bootstrap.css";</style>

<?php
require_once "../FomlConfig.php";

// This test exercises all node types and variations.
Foml::$keepTempFiles = false;

class FomlFragment
{
    function __construct($Title, $Foml, $Discussion=null)
    {
        $this->title = $Title;
        $this->foml = $Foml;
        $this->discussion = $Discussion;
    }

    function Foml()
    {
        return $this->foml;
    }

    function Php()
    {
        $state = new FomlRenderState();
        $php = FomlParser::ParseString($this->foml, $state);
        return $php;
    }

    function XslFo()
    {
        $php = $this->Php();
        ob_start();
        eval("?".">".$php);  // prefixed with ? > to exit implicit php mode
        $xslFo = ob_get_contents();
        ob_end_clean();
        return $xslFo;
    }
    
}

{
    $fragments = array
        (
         new FomlFragment(
                          'Doctype Node',
                          '!!!',
                          "All complete FOML documents should start with this as the first line."
                          ),
         new FomlFragment(
                          'Shell-style Comments',
                          "-# This generates no XSL-FO at all",
                          'HAML shell style comments are evaluated as PHP, and resolve to the rarely-used PHP shell-style comment.'
                          ),
         new FomlFragment(
                          'XML Comments',
'
/ This should become an XML comment
  and so should this indented continuation.
'
                          ),
         new FomlFragment(
                          'Simple XML Node',
                          "%flow\n",
                          'If a namespace is not explicitly defined, the fo namespace is used.'
                          ),
         new FomlFragment(
                          'XML Node with an explicit namespace',
                          "%xsl:stylesheet\n"
                          ),
         new FomlFragment(
                          'XML Node self-closing',
                          "%flow/",
                          'End the tag with a "/" to make the node self-closing.'
                          ),
         new FomlFragment(
                          'XML Node with attributes',
                          '%flow(flow-name="xsl-region-body")'
                          ),
         new FomlFragment(
                          'XML Node with explicit namespace, arguments and auto-close',
                          '%fo:block(border-after-style="dashed") /'
                          ),
         new FomlFragment(
                          'Simple Text',
                          'We will rule over all this land, & we will call it... "This Land".'
                          ),
         new FomlFragment(
                          'Line Continuation',
"
%table(                            |
  table-layout=\"fixed\"             |
  width=\"100%\"                     |
  border-collapse=\"separate\"       |
  )                                |
",
                          'Note that the final line must end with a pipe character too.'
                          ),
         new FomlFragment(
                          'PHP Execution',
                          '- print "hello"'
                          ),
         new FomlFragment(
                          'PHP Evaluation',
                          '= 24 * 60 * 60'
                          ),
         new FomlFragment(
                          'PHP Conditionals',
'
- $x = 4 * 4
- if ($x>15)
  Thao
- else
  Vang
'
                          ),                          
         new FomlFragment(
                          'PHP Iteration',
'
- for ($i=0;$i<3;$i++)
  %table-cell
    %block
      = $i * $i
'
                          ),                          
         new FomlFragment(
                          'Node with text follow-on',
                          '%fo:block(border-after-style="dashed") text in extension'
                          ),
         new FomlFragment(
                          'Node with evaluation follow-on',
                          '%fo:block(border-after-style="dashed") = 22/7 // evaluation in extension'
                          ),
         new FomlFragment(
                          'Node with execution follow-on',
                          '%fo:block(border-after-style="dashed") - print 22/7 // execution in extension'
                          ),
         new FomlFragment(
                          'Special Case #1 : The Importance of New-Lines in Generated PHP',
'
- $x = 22/7 // these slashes break the auto-semicolon logic
= $x
',
                          'This example illustrates why the automatically-generated semi-colon is preceeded by a new-line.  Without it the semi-colon would be commented out in this example.'
                          ),
         new FomlFragment(
                          'Special Case #2 : Self-closing Node with child-nodes',
                          "%flow/\n  %inline\n    appears to be a child of flow",
                          'Indented subnodes of a self-closing nodes will NOT be enclosed by the closed node.  You wouldn\'t do this on purpose.'
                          ),

         );
    
    print '<div class="container"><div class="row"><div class="span10">';
    print '<h1>FOML Sampler</h1>';
    print '<p>See <a href="https://github.com/guyc/FOML">the FOML Github repo</a> for details.</p>';
    foreach ($fragments as $fragment) 
    {
        print '<div style="margin-top:20px">';
        print "<h3>{$fragment->title}</h3>";
        print "<p>{$fragment->discussion}</p>";

        print '<div style="margin-left:40px">';
        print "<h4>FOML</h4>";
        print "<pre>";
        print htmlentities($fragment->Foml());
        print "</pre>";

        print "<h4>PHP</h4>";
        print "<pre>";
        print htmlentities($fragment->Php());
        print "</pre>";

        print "<h4>XSL-FO</h4>";
        print "<pre>";
        print htmlentities($fragment->XslFo());
        print "</pre>";

        print "</div>";
        print "</div>";
    }

    print '</div></div></div>';

}
?>