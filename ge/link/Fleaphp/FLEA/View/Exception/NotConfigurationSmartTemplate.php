<?php
class FLEA_View_Exception_NotConfigurationSmartTemplate extends FLEA_Exception
{
    function FLEA_View_Exception_NotConfigurationSmartTemplate()
    {
        $code = 0x0903001;
        parent::FLEA_Exception(_ET($code), $code);
    }
}
