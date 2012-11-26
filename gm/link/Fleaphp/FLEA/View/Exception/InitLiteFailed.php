<?php
/**
 * 定义 FLEA_View_Exception_InitLiteFailed 类
 * FLEA_View_Exception_InitLiteFailed 指示 FLEA_View_Lite 无法初始化 TemplateLite 模版引擎
 */
class FLEA_View_Exception_InitLiteFailed extends FLEA_Exception
{
    var $filename;

    function FLEA_View_Exception_InitLiteFailed($filename)
    {
        $this->filename = $filename;
        $code = 0x0904002;
        parent::FLEA_Exception(sprintf(_ET($code), $filename), $code);
    }
}
