<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */
function smarty_function_debug($params, &$smarty)
{
    if (isset($params['output'])) {
        $smarty->assign('_smarty_debug_output', $params['output']);
    }
    require_once(SMARTY_CORE_DIR . 'core.display_debug_console.php');
    return smarty_core_display_debug_console(null, $smarty);
}

/* vim: set expandtab: */

?>
