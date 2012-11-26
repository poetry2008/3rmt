<?php
/**
 * 定义 FLEA_View_Smarty 类
 */

// {{{ includes

do {
    if (PHP5) {
        if (class_exists('Smarty', false)) { break; }
    } else {
        if (class_exists('Smarty')) { break; }
    }

    $viewConfig = FLEA::getAppInf('viewConfig');
    if (!isset($viewConfig['smartyDir']) && !defined('SMARTY_DIR')) {
        FLEA::loadClass('FLEA_View_Exception_NotConfigurationSmarty');
        return __THROW(new FLEA_View_Exception_NotConfigurationSmarty());
    }

    $filename = $viewConfig['smartyDir'] . '/Smarty.class.php';
    if (!is_readable($filename)) {
        FLEA::loadClass('FLEA_View_Exception_InitSmartyFailed');
        return __THROW(new FLEA_View_Exception_InitSmartyFailed($filename));
    }

    require($filename);
} while (false);

// }}}

/**
 * FLEA_View_Smarty 提供了对 Smarty 模板引擎的支持
 */
class FLEA_View_Smarty extends Smarty
{
    /**
     * 构造函数
     *
     * @return FLEA_View_Smarty
     */
    function FLEA_View_Smarty() {
        parent::Smarty();

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
