<?php
/**
 * 定义 FLEA_Db_Exception_InvalidDSN 异常
 * FLEA_Db_Exception_InvalidDSN 异常指示没有提供有效的 DSN 设置

 */
class FLEA_Db_Exception_InvalidDSN extends FLEA_Exception
{
    var $dsn;

    /**
     * 构造函数
     *
     * @param $dsn
     *
     * @return FLEA_Db_Exception_InvalidDSN
     */
    function FLEA_Db_Exception_InvalidDSN($dsn)
    {
        unset($this->dsn['password']);
        $this->dsn = $dsn;
        $code = 0x06ff001;
        parent::FLEA_Exception(_ET($code), $code);
    }
}
