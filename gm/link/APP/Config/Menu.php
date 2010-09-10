<?php

/**
 * 定义后台管理界面左侧的菜单
 */
//dump($_SESSION);

$catalog = array();

$menu = array();
//$menu[] = array(_T("menu_actlist"), 'Actmanager', 'List');
//$menu[] = array(_T("menu_FlushAuth"), 'Actmanager', 'FlushAuth');

$menu[] = array(_T("menu_Class"), 'Class', 'Index');
$menu[] = array(_T("menu_frequentsite"), 'frequentsite', 'Index');
$menu[] = array(_T("menu_frequentclass"), 'frequentclass', 'Index');
$menu[] = array(_T("menu_frequentspecial2"), 'frequentspecial2', 'Index');
$menu[] = array(_T("menu_frequentspecial"), 'frequentspecial', 'Index');
//$menu[] = array(_T("menu_submit"), 'submit', 'list');
//$menu[] = array(_T("menu_UserManager"), 'UserManager', 'Index');
$menu[] = array(_T("menu_UpdatePassword"), 'Admin', 'UpdatePassword');
$menu[] = array(_T("menu_Global"), 'Global', 'Index');
$menu[] = array(_T("menu_SetSeo"), 'Setseo', 'Index');
$menu[] = array(_T("menu_Seoplink_Admin_Top"), 'Seoplink', 'Admin_top');
$menu[] = array(_T("menu_Welcome"), 'Admin', 'Index');
$menu[] = array(_T("menu_Logout"), 'Admin', 'Logout');
return $menu;
