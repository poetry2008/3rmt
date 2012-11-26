<?php
class FLEA_Exception_ValidationFailed extends FLEA_Exception
{
    /**
     * 被验证的数据
     *
     * @var mixed
     */
    var $data;

    /**
     * 验证结果
     *
     * @var array
     */
    var $result;

    /**
     * 构造函数
     *
     * @param array $result
     * @param mixed $data
     *
     * @return FLEA_Exception_ValidationFailed
     */
    function FLEA_Exception_ValidationFailed($result, $data = null)
    {
        $this->result = $result;
        $this->data = $data;
        $code = 0x0407001;
        $msg = sprintf(_ET($code), implode(', ', array_keys((array)$result)));
        parent::FLEA_Exception($msg, $code);
    }
}
