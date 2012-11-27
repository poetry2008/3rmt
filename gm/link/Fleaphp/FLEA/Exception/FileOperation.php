<?php
/**
 * FLEA_Exception_FileOperation 异常指示文件系统操作失败
 */
class FLEA_Exception_FileOperation extends FLEA_Exception
{
    /**
     * 正在进行的文件操作
     *
     * @var string
     */
    var $operation;

    /**
     * 操作的参数
     *
     * @var array
     */
    var $args;

    /**
     * 构造函数
     *
     * @param string $opeation
     *
     * @return FLEA_Exception_FileOperation
     */
    function FLEA_Exception_FileOperation($opeation)
    {
        $this->operation = $opeation;
        $args = func_get_args();
        array_shift($args);
        $this->args = $args;
        $func = $opeation . '(' . implode(', ', $args) . ')';
        parent::FLEA_Exception(sprintf(_ET(0x0102005), $func));
    }
}
