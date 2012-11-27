<?php
class FLEA_Exception_MustOverwrite extends FLEA_Exception
{
    var $prototypeMethod;

    /**
     * 构造函数
     *
     * @param string $prototypeMethod
     *
     * @return FLEA_Exception_MustOverwrite
     */
    function FLEA_Exception_MustOverwrite($prototypeMethod)
    {
        $this->prototypeMethod = $prototypeMethod;
        $code = 0x0102008;
        $msg = sprintf(_ET($code), $prototypeMethod);
        parent::FLEA_Exception($msg, $code);
    }
}
