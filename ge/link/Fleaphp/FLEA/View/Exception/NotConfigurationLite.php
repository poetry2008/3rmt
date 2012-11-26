<?php
class FLEA_View_Exception_NotConfigurationLite extends FLEA_Exception
{
    function FLEA_View_Exception_NotConfigurationLite()
    {
        $code = 0x0904001;
        parent::FLEA_Exception(_ET($code), $code);
    }
}
