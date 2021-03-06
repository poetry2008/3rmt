<?php
/**
 * 定义 FLEA_Db_Exception_InvalidLinkType 异常
 * FLEA_Db_Exception_InvalidLinkType 异常指示无效的数据表关联类型
 
 */
class FLEA_Db_Exception_InvalidLinkType extends FLEA_Exception
{
    var $type;

    /**
     * 构造函数
     *
     * @param $type
     *
     * @return FLEA_Db_Exception_InvalidDSN
     */
    function FLEA_Db_Exception_InvalidLinkType($type)
    {
        $this->type = $type;
        $code = 0x0202001;
        parent::FLEA_Exception(sprintf(_ET($code), $type), $code);
    }
}
