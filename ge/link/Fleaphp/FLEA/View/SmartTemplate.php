<?php

// {{{ includes

do {
    if (PHP5) {
        if (class_exists('SmartTemplate', false)) { break; }
    } else {
        if (class_exists('SmartTemplate')) { break; }
    }

    $viewConfig = FLEA::getAppInf('viewConfig');
    if (!isset($viewConfig['smartDir'])) {
        FLEA::loadClass('FLEA_View_Exception_NotConfigurationSmartTemplate');
        return __THROW(new FLEA_View_Exception_NotConfigurationSmartTemplate());
    }

    $filename = $viewConfig['smartDir'] . '/class.smarttemplate.php';
    if (!is_readable($filename)) {
        FLEA::loadClass('FLEA_View_Exception_InitSmartTemplateFailed');
        return __THROW(new FLEA_View_Exception_InitSmartTemplateFailed($filename));
    }
    require($filename);
} while (false);

// }}}

/**
* FLEA_View_SmartTemplate 提供了对 SmartTemplate 模板引擎的支持
*/
class FLEA_View_SmartTemplate extends SmartTemplate
{
    /**
     * 构造函数
     *
     * @return FLEA_View_SmartTemplate
     */
    function FLEA_View_SmartTemplate()
    {
        parent::SmartTemplate();

        $viewConfig = FLEA::getAppInf('viewConfig');
        if (is_array($viewConfig)) {
            foreach ($viewConfig as $key => $value) {
                if (isset($this->{$key})) {
                    $this->{$key} = $value;
                }
            }
        }
    }

    /**
     * 输出指定模版的内容
     *
     * @param string $tpl
     */
    function display($tpl)
    {
        $this->tpl_file = $tpl;
        $this->output();
    }
}
