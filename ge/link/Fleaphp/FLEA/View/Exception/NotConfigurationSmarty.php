<?php
class FLEA_View_Exception_NotConfigurationSmarty extends FLEA_Exception
{
    function FLEA_View_Exception_NotConfigurationSmarty()
    {
        $code = 0x0902001;
        parent::FLEA_Exception(_ET($code), $code);
    }
}
