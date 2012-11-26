<?php
/**
 * 定义 FLEA_View_Exception_InitSmartTemplateFailed 类
 * FLEA_View_Exception_InitSmartTemplateFailed 指示 FLEA_View_SmartTemplate 无法初始化 SmartTemplate 模版引擎
 */
class FLEA_View_Exception_InitSmartTemplateFailed extends FLEA_Exception
{
    var $filename;

    function FLEA_View_Exception_InitSmartTemplateFailed($filename)
    {
        $this->filename = $filename;
        $code = 0x0903002;
        parent::FLEA_Exception(sprintf(_ET($code), $filename), $code);
    }
}
