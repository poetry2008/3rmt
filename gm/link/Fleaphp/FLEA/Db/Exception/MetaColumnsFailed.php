<?php
/**
 * 定义 FLEA_Db_Exception_MetaColumnsFailed 异常
 * FLEA_Db_Exception_MetaColumnsFailed 异常指示查询数据表的元数据时发生错误
 
 */
class FLEA_Db_Exception_MetaColumnsFailed extends FLEA_Exception
{
    var $tableName;

    /**
     * 构造函数
     *
     * @param string $tableName
     *
     * @return FLEA_Db_Exception_MetaColumnsFailed
     */
    function FLEA_Db_Exception_MetaColumnsFailed($tableName)
    {
        $code = 0x06ff007;
        parent::FLEA_Exception(sprintf(_ET($code), $tableName), $code);
    }
}
