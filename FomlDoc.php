<?php
class FomlDoc extends FomlNode
{
    public $state;

    function __construct()
    {
        $this->state = new FomlRenderState();
    }

    function RenderToString()
    {
        ob_start();
        $this->Render();
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }

    function EvalToString($Args)
    {
        foreach ($Args as $key=>$value) {
            $$key = $value;
        }

        $php = $this->RenderToString();
        ob_start();
        eval("?".">".$php);
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }

}
?>
