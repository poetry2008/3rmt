<?php
/**
 * 定义 FLEA_View_Exception_NotConfigurationSmartTemplate 类
 * FLEA_View_Exception_NotConfigurationSmartTemplateSmarty 表示开发者
 * 没有为 FLEA_View_SmartTemplate 提供初始化 SmartTemplate 模版引擎需要的设置
 */
class FLEA_View_Exception_NotConfigurationSmartTemplate extends FLEA_Exception
{
    function FLEA_View_Exception_NotConfigurationSmartTemplate()
    {
        $code = 0x0903001;
        parent::FLEA_Exception(_ET($code), $code);
    }
}
