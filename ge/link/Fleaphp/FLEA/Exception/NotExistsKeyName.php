<?php

class FLEA_Exception_NotExistsKeyName extends FLEA_Exception
{
    var $keyname;

    /**
     * 构造函数
     *
     * @param string $keyname
     *
     * @return FLEA_Exception_NotExistsKeyName
     */
    function FLEA_Exception_NotExistsKeyName($keyname)
    {
        $this->keyname = $keyname;
        parent::FLEA_Exception(sprintf(_ET(0x0102009), $keyname));
    }
}
