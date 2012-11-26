<?php

class FLEA_View_Exception_InitSmartyFailed extends FLEA_Exception
{
    var $filename;

    function FLEA_View_Exception_InitSmartyFailed($filename)
    {
        $this->filename = $filename;
        $code = 0x0902002;
        parent::FLEA_Exception(sprintf(_ET($code), $filename), $code);
    }
}
