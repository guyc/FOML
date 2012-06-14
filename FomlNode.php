<?php
class FomlNode
{
    public $children = array();
    public $state = null;  // set to an instance of FomlState before calling render
    public $mode = Foml::XML_MODE;

    function RenderPrefix()
    {
    }

    function RenderSuffix()
    {
    }

    function SetMode($Mode)
    {
        if ($Mode == Foml::PHP_MODE) {
            assert($this->state);
            if ($this->state->mode == Foml::XML_MODE) {
                print "<?php ";
            }
        } elseif ($Mode == Foml::XML_MODE) {
            if ($this->state->mode == Foml::PHP_MODE) {
                print " ?>";
            }
        }
        $this->state->mode = $Mode;
    }

    function Render()
    {
        $this->SetMode($this->mode);
        $this->RenderPrefix();
        foreach ($this->children as $child) {
            $child->state = $this->state;
            $child->Render();
        }
        $this->SetMode($this->mode);
        $this->RenderSuffix();
    }
}
?>