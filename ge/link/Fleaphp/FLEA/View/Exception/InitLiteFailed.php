<?php

class FLEA_View_Exception_InitLiteFailed extends FLEA_Exception
{
    var $filename;

    function FLEA_View_Exception_InitLiteFailed($filename)
    {
        $this->filename = $filename;
        $code = 0x0904002;
        parent::FLEA_Exception(sprintf(_ET($code), $filename), $code);
    }
}
