<?php
/**
 * 定义 FLEA_Db_Exception_MissingDSN 异常
 * FLEA_Db_Exception_MissingDSN 异常指示没有提供连接数据库需要的 dbDSN 应用程序设置

 */
class FLEA_Db_Exception_MissingDSN extends FLEA_Exception
{
    /**
     * 构造函数
     *
     * @return FLEA_Db_Exception_MissingDSN
     */
    function FLEA_Db_Exception_MissingDSN()
    {
        $code = 0x06ff002;
        parent::FLEA_Exception(_ET($code), $code);
    }
}
