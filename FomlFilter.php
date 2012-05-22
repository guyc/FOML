<?php
class FomlFilter
{
    static function CreateAndRender($Args)
    {
        $filterClass = get_called_class();
        $filter = new $filterClass($Args);
        $filter->Render();
    }
}

?>