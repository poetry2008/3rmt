<?php
/**
 * 定义 FLEA_Rbac_Exception_InvalidACTFile 异常
 * FLEA_Rbac_Exception_InvalidACTFile 异常指示控制器的 ACT 文件无效
 */
class FLEA_Rbac_Exception_InvalidACTFile extends FLEA_Exception
{
    /**
     * ACT 文件名
     *
     * @var string
     */
    var $actFilename;

    /**
     * 控制器名字
     *
     * @var string
     */
    var $controllerName;

    /**
     * 无效的 ACT 内容
     *
     * @var mixed
     */
    var $act;

    /**
     * 构造函数
     *
     * @param string $actFilename
     * @param string $controllerName
     * @param mixed $act
     *
     * @return FLEA_Rbac_Exception_InvalidACTFile
     */
    function FLEA_Rbac_Exception_InvalidACTFile($actFilename, $act, $controllerName = null)
    {
        $this->actFilename = $actFilename;
        $this->act = $act;
        $this->controllerName = $controllerName;

        if ($controllerName) {
            $code = 0x0701002;
            $msg = sprintf(_ET($code), $actFilename, $controllerName);
        } else {
            $code = 0x0701003;
            $msg = sprintf(_ET($code), $actFilename);
        }
        parent::FLEA_Exception($msg, $code);
    }
}
