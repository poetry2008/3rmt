<?php

class FLEA_Exception_MissingArguments extends FLEA_Exception
{
    /**
     * 缺少的参数
     *
     * @var mixed
     */
    var $args;

    /**
     * 构造函数
     *
     * @param mixed $args
     *
     * @return FLEA_Exception_MissingArguments
     */
    function FLEA_Exception_MissingArguments($args)
    {
        $this->args = $args;
        if (is_array($args)) {
            $args = implode(', ', $args);
        }
        $code = 0x0102007;
        $msg = sprintf(_ET($code), $args);
        parent::FLEA_Exception($msg, $code);
    }
}
