<?php
class FLEA_View_Exception_InitSmartTemplateFailed extends FLEA_Exception
{
    var $filename;

    function FLEA_View_Exception_InitSmartTemplateFailed($filename)
    {
        $this->filename = $filename;
        $code = 0x0903002;
        parent::FLEA_Exception(sprintf(_ET($code), $filename), $code);
    }
}
