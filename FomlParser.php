<?php
class FomlParser 
{
    static $DEFAULT_NAMESPACE = "fo";
    static $NODE_CLASSES = array(
                                 'FomlExecNode',
                                 'FomlEvalNode',
                                 'FomlElementNode',
                                 'FomlCommentNode',
                                 'FomlDoctypeNode',
                                 'FomlFilterNode',
                                 'FomlTextNode'  // this must be last because it matches everything
                                 );

    static $FILTER_CLASSES = array(
                                   'include' => 'FomlIncludeFilter'
                                   );

    // returns a FomlDocument instance
    static function ParseFile($FileName)
    {
        $foml = file_get_contents($FileName);
        return FomlParser::ParseString($foml);
    }

    static function ParseString($Foml)
    {
        $tree = FomlParseTree::Parse($Foml);
        $doc = $tree->Generate();
        return $doc->RenderToString();  // returns php code
    }
}
?>