<?php
/**
 * 定义 FLEA_View_Exception_NotConfigurationSmarty 类
 * FLEA_View_Exception_NotConfigurationSmartySmarty 表示开发者
 * 没有为 FLEA_View_Smarty 提供初始化 Smarty 模版引擎需要的设置
 */
class FLEA_View_Exception_NotConfigurationSmarty extends FLEA_Exception
{
    function FLEA_View_Exception_NotConfigurationSmarty()
    {
        $code = 0x0902001;
        parent::FLEA_Exception(_ET($code), $code);
    }
}
