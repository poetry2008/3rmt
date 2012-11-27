<?php
/**
 * FLEA_Exception_InvalidArguments 异常指示一个参数错误
 */
class FLEA_Exception_InvalidArguments extends FLEA_Exception
{
    var $arg;
    var $value;

    /**
     * 构造函数
     *
     * @param string $arg
     * @param mixed $value
     *
     * @return FLEA_Exception_InvalidArguments
     */
    function FLEA_Exception_InvalidArguments($arg, $value = null)
    {
        $this->arg = $arg;
        $this->value = $value;
        parent::FLEA_Exception(sprintf(_ET(0x0102006), $arg));
    }
}
