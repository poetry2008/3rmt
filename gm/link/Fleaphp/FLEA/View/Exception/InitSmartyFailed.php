<?php
/**
 * 定义 FLEA_View_Exception_InitSmartyFailed 类
 * FLEA_View_Exception_InitSmartyFailed 指示 FLEA_View_Smarty 无法初始化 Smarty 模版引擎
 */
class FLEA_View_Exception_InitSmartyFailed extends FLEA_Exception
{
    var $filename;

    function FLEA_View_Exception_InitSmartyFailed($filename)
    {
        $this->filename = $filename;
        $code = 0x0902002;
        parent::FLEA_Exception(sprintf(_ET($code), $filename), $code);
    }
}
