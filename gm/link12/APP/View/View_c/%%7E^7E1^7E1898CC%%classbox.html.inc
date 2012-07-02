<?php /* Smarty version 2.6.18, created on 2012-05-30 17:55:23
         compiled from Site/classbox.html */

$this->_cache_serials['/home/hm1002/GM/gm/link/APP/View/View_c/%%7E^7E1^7E1898CC%%classbox.html.inc'] = 'e9cc385d4c1ddaf43462531bfa4857d1';

?><?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'url', 'Site/classbox.html', 15, false),array('function', '_t', 'Site/classbox.html', 89, false),array('modifier', 'truncate', 'Site/classbox.html', 73, false),)), $this); ?>
<?php
function _smarty_tplfunc_e9cc385d4c1ddaf43462531bfa4857d1_0(&$_smarty)
{
echo $_smarty->_plugins['function']['url'][0][0]->_pi_func_url(array('controller' => 'site','action' => 'class','id' => $_smarty->_tpl_vars['classkind']['children'][$_smarty->_sections['loopClass']['index']]['class_id']), $_smarty);
}


function _smarty_tplfunc_e9cc385d4c1ddaf43462531bfa4857d1_1(&$_smarty)
{
echo $_smarty->_plugins['function']['url'][0][0]->_pi_func_url(array('controller' => 'site','action' => 'detail','id' => $_smarty->_tpl_vars['class']['sites'][$_smarty->_sections['loopSite']['index']]['id']), $_smarty);
}


function _smarty_tplfunc_e9cc385d4c1ddaf43462531bfa4857d1_2(&$_smarty)
{
echo $_smarty->_plugins['function']['_t'][0][0]->_pi_func_t(array('key' => 'no_datas_msg'), $_smarty);
}



?>
