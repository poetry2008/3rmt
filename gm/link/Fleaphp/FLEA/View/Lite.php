<?php
/**
 * 定义 FLEA_View_Lite 类
 */

// {{{ includes

do {
    if (PHP5) {
        if (class_exists('Template_Lite', false)) { break; }
    } else {
        if (class_exists('Template_Lite')) { break; }
    }

    $viewConfig = FLEA::getAppInf('viewConfig');
    if (!isset($viewConfig['liteDir'])) {
        FLEA::loadClass('FLEA_View_Exception_NotConfigurationLite');
        return __THROW(new FLEA_View_Exception_NotConfigurationLite());
    }

    $filename = $viewConfig['liteDir'] . '/class.template.php';
    if (!file_exists($filename)) {
        FLEA::loadClass('FLEA_View_Exception_InitLiteFailed');
        return __THROW(new FLEA_View_Exception_InitLiteFailed($filename));
    }

    require($filename);
} while (false);

// }}}

/**
 * FLEA_View_Lite 提供了对 TemplateLite 模板引擎的支持
 */
class FLEA_View_Lite extends Template_Lite
{
    /**
     * 构造函数
     *
     * @return FLEA_View_Lite
     */
    function FLEA_View_Lite() {
        parent::Template_Lite();

        $viewConfig = FLEA::getAppInf('viewConfig');
        if (is_array($viewConfig)) {
            foreach ($viewConfig as $key => $value) {
                if (isset($this->{$key})) {
                    $this->{$key} = $value;
                }
            }
        }

        FLEA::loadClass('FLEA_View_SmartyHelper');
        new FLEA_View_SmartyHelper($this);
    }
}
