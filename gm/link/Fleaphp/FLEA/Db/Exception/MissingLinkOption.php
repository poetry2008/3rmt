<?php
/**
 * 定义 FLEA_Db_Exception_MissingLinkOption 异常
 * FLEA_Db_Exception_MissingLinkOption 异常指示创建 TableLink 对象时没有提供必须的选项
 
 */
class FLEA_Db_Exception_MissingLinkOption extends FLEA_Exception
{
    /**
     * 缺少的选项名
     *
     * @var string
     */
    var $option;

    /**
     * 构造函数
     *
     * @param string $option
     *
     * @return FLEA_Db_Exception_MissingLinkOption
     */
    function FLEA_Db_Exception_MissingLinkOption($option)
    {
        $this->option = $option;
        $code = 0x0202002;
        parent::FLEA_Exception(sprintf(_ET($code), $option));
    }
}
