<?php
class FLEA_Exception_ExpectedFile extends FLEA_Exception
{
    var $filename;

    /**
     * 构造函数
     *
     * @param string $filename
     *
     * @return FLEA_Exception_ExpectedFile
     */
    function FLEA_Exception_ExpectedFile($filename)
    {
        $this->filename = $filename;
        $code = 0x0102001;
        $msg = sprintf(_ET($code), $filename);
        parent::FLEA_Exception($msg, $code);
    }
}
