<?php
/**
 * 定义 FLEA_Db_Exception_MissingLink 异常
 * FLEA_Db_Exception_MissingLink 异常指示尝试访问的关联不存在
 
 */
class FLEA_Db_Exception_MissingLink extends FLEA_Exception
{
    var $name;

    /**
     * 构造函数
     *
     * @param $name
     *
     * @return FLEA_Db_Exception_MissingLink
     */
    function FLEA_Db_Exception_MissingLink($name)
    {
        $this->name = $name;
        $code = 0x06ff009;
        parent::FLEA_Exception(sprintf(_ET($code), $name), $code);
    }
}
