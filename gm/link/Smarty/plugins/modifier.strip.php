<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


function smarty_modifier_strip($text, $replace = ' ')
{
    return preg_replace('!\s+!', $replace, $text);
}

/* vim: set expandtab: */

?>
