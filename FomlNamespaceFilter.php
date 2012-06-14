<?php
class FomlNamespaceFilter extends FomlFilter
{
    public $namespace;

    function __construct($Arg)
    {
        $this->namespace = $Arg;
    }
    
    function Render()
    {
        $oldNamespace = Foml::$defaultNamespace;
        Foml::$defaultNamespace = $this->namespace;
        parent::Render();
        Foml::$defaultNamespace = $oldNamespace;
    }
}

?>