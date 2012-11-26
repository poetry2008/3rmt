<?php
class FLEA_Exception_ExistsKeyName extends FLEA_Exception
{
    var $keyname;

    /**
     * 构造函数
     *
     * @param string $keyname
     *
     * @return FLEA_Exception_ExistsKeyName
     */
    function FLEA_Exception_ExistsKeyName($keyname)
    {
        $this->keyname = $keyname;
        parent::FLEA_Exception(sprintf(_ET(0x0102004), $keyname));
    }
}
