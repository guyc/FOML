<?php
class FomlParser 
{
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
                                   'include' => 'FomlIncludeFilter',
                                   'namespace' => 'FomlNamespaceFilter'
                                   );

    // returns a FomlDocument instance
    static function ParseFile($FileName, $State=null)
    {
        $foml = file_get_contents($FileName);
        return FomlParser::ParseString($foml, $State);
    }

    static function ParseString($Foml, $State)
    {
        $tree = FomlParseTree::Parse($Foml);
        $doc = $tree->Generate();
        if ($State) $doc->state = $State;  // for subdocuments, pass the parent document state along
        return $doc->RenderToString();  // returns php code
    }
}
?>