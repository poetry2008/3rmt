<?php
class FLEA_Db_Exception_InvalidInsertID extends FLEA_Exception
{
    /**
     * 构造函数
     *
     * @return FLEA_Db_Exception_InvalidInsertID
     */
    function FLEA_Db_Exception_InvalidInsertID()
    {
        $code = 0x06ff008;
        parent::FLEA_Exception(_ET($code), $code);
    }
}
