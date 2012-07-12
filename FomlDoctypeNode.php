<?php
class FomlDoctypeNode extends FomlNode
{
    public $mode = Foml::PHP_MODE;
    const MATCH_RE = "/^\!\!\!\s*/";

    function __construct($Matches)
    {
        // Nothing to save.
    }

    function RenderPrefix()
    {
        print 'echo \'<?xml version="1.0" encoding="utf-8"?>\'';
    }

    function RenderSuffix()
    {
    }

        

}
?>