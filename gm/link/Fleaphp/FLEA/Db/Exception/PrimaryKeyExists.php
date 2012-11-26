<?php
/**
 * 定义 FLEA_Db_Exception_PrimaryKeyExists 异常
 * FLEA_Db_Exception_PrimaryKeyExists 异常指示在不需要主键值的时候却提供了主键值
 
 */
class FLEA_Db_Exception_PrimaryKeyExists extends FLEA_Exception
{
    /**
     * 主键字段名
     *
     * @var string
     */
    var $primaryKey;

    /**
     * 主键字段值
     *
     * @var mixed
     */
    var $pkValue;

    /**
     * 构造函数
     *
     * @param string $pk
     * @param mixed $pkValue
     *
     * @return FLEA_Db_Exception_PrimaryKeyExists
     */
    function FLEA_Db_Exception_PrimaryKeyExists($pk, $pkValue = null)
    {
        $this->primaryKey = $pk;
        $this->pkValue = $pkValue;
        $code = 0x06ff004;
        $msg = sprintf(_ET($code), $pk, $pkValue);
        parent::FLEA_Exception($msg, $code);
    }
}
