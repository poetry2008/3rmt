<?php
/**
 * 定义 FLEA_View_Exception_NotConfigurationLite 类
 * FLEA_View_Exception_NotConfigurationLiteLite 表示开发者
 * 没有为 FLEA_View_Lite 提供初始化 TemplateLite 模版引擎需要的设置
 */
class FLEA_View_Exception_NotConfigurationLite extends FLEA_Exception
{
    function FLEA_View_Exception_NotConfigurationLite()
    {
        $code = 0x0904001;
        parent::FLEA_Exception(_ET($code), $code);
    }
}
