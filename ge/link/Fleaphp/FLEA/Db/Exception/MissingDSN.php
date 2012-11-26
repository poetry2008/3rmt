<?php

class FLEA_Db_Exception_MissingDSN extends FLEA_Exception
{
    /**
     * 构造函数
     *
     * @return FLEA_Db_Exception_MissingDSN
     */
    function FLEA_Db_Exception_MissingDSN()
    {
        $code = 0x06ff002;
        parent::FLEA_Exception(_ET($code), $code);
    }
}
