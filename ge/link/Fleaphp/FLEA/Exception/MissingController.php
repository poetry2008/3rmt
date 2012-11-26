<?php
class FLEA_Exception_MissingController extends FLEA_Exception
{
    /**
     * 控制器的名字
     *
     * @var string
     */
    var $controllerName;

    /**
     * 控制器类名称
     *
     * @var string
     */
    var $controllerClass;

    /**
     * 动作名
     *
     * @var string
     */
    var $actionName;

    /**
     * 动作方法名
     *
     * @var string
     */
    var $actionMethod;

    /**
     * 调用参数
     *
     * @var mixed
     */
    var $arguments;

    /**
     * 控制器的类定义文件
     *
     * @var string
     */
    var $controllerClassFilename;

    /**
     * 构造函数
     *
     * @param string $controllerName
     * @param string $actionName
     * @param mixed $arguments
     * @param string $controllerClass
     * @param string $actionMethod
     *
     * @return FLEA_Exception_MissingController
     */
    function FLEA_Exception_MissingController($controllerName, $actionName,
             $arguments = null, $controllerClass = null, $actionMethod = null,
             $controllerClassFilename = null)
    {
        $this->controllerName = $controllerName;
        $this->actionName = $actionName;
        $this->arguments = $arguments;
        $this->controllerClass = $controllerClass;
        $this->actionMethod = $actionMethod;
        $this->controllerClassFilename = $controllerClassFilename;
        $code = 0x0103002;
        parent::FLEA_Exception(sprintf(_ET($code), $controllerName));
    }
}
