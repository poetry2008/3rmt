<?php
/* *********************************************************
  模块名: users.php
 * 2001/5/29
 *   modi 2002-05-10
 * Naomi Suzukawa
 * suzukawa@bitscope.co.jp
  ----------------------------------------------------------
用户管理的语言定义

  ■更改记录
********************************************************* */
// 页面标题
define('HEADING_TITLE', '用户管理');

// 错误信息显示标题
define('TABLE_HEADING_ERRINFO', '!!!!! 错误信息 !!!!!');

// 输入错误信息
define('TEXT_ERRINFO_INPUT_NOINPUT', '没有输入');
define('TEXT_ERRINFO_INPUT_ERR', '没有正确输入');
define('TEXT_ERRINFO_INPUT_LENGTH', '请输入%d 字以上');
define('TEXT_ERRINFO_USER_DELETE', '<b>用户信息的删除</b>:不能删除用户本人的信息');
define('TEXT_ERRINFO_USER_GRANT', '<b>给与权限</b>:请选择用户');
define('TEXT_ERRINFO_USER_REVOKE', '<b>取消权限</b>:选择用户');
define('TEXT_ERRINFO_USER_REVOKE_ONESELF', '<b>取消权限</b>:不能取消用户本人的权限');
define('TEXT_ERRINFO_CONFIRM_PASSWORD', '<b>再输入</b>:确认密码错误');

// table访问错误信息
define('TEXT_ERRINFO_DB_NO_USERINFO', '无法获取用户信息');
define('TEXT_ERRINFO_DB_NO_USER', '目标用户不存在');
define('TEXT_ERRINFO_DB_USERCHACK', '用户存在的确认中出现错误');
define('TEXT_ERRINFO_DB_EXISTING_USER', '已登录用户');
define('TEXT_ERRINFO_DB_INSERT_USER', '用户信息无法登录');
define('TEXT_ERRINFO_DB_INSERT_PERMISSION', '用户权限信息不能登录');
define('TEXT_ERRINFO_DB_UPDATE_USER', '用户信息无法更新');
define('TEXT_ERRINFO_DB_DELETE_USER', '用户信息无法删除');
define('TEXT_ERRINFO_DB_CHANGE_PASSWORD', '密码无法更改');
define('TEXT_ERRINFO_DB_CHANGE_USER', '用户权限无法更改');

// 结束信息
define('TEXT_SUCCESSINFO_INSERT_USER', '用户已添加');
define('TEXT_SUCCESSINFO_UPDATE_USER', '用户信息已更新');
define('TEXT_SUCCESSINFO_DELETE_USER', '用户信息已删除');
define('TEXT_SUCCESSINFO_CHANGE_PASSWORD', '密码已修改');
define('TEXT_SUCCESSINFO_PERMISSION', '用户权限已%s');

// 页面标题
define('PAGE_TITLE_MENU_USER', '用户管理菜单');
define('PAGE_TITLE_INSERT_USER', '添加用户');
define('PAGE_TITLE_USERINFO', '用户信息');
define('PAGE_TITLE_PASSWORD', '更改密码');
define('PAGE_TITLE_PERMISSION', '管理者权限');

// 按钮
define('BUTTON_BACK_MENU', '返回用户管理菜单');
define('BUTTON_INSERT_USER', '添加用户');
define('BUTTON_INFO_USER', '用户信息');
define('BUTTON_CHANGE_PASSWORD', '密码更改');
define('BUTTON_PERMISSION', '管理者权限');
define('BUTTON_INSERT', '添加');
define('BUTTON_CLEAR', '清除');
define('BUTTON_UPDATE', '更新');
define('BUTTON_DELETE', '删除');
define('BUTTON_RESET', '返回原来的值');
define('BUTTON_CHANGE', '更改');
define('BUTTON_GRANT', '给与权限 >>');
define('BUTTON_REVOKE', '<< 取消权限');
define('BUTTON_BACK_PERMISSION', '返回管理者权限');

// 項目名
define('TABLE_HEADING_COLUMN', 'column');
define('TABLE_HEADING_DATA', '数据');
define('TABLE_HEADING_USER', '用户');
define('TABLE_HEADING_USER_LIST', '用户一览');
define('TABLE_HEADING_USER_ID', '用户ID');
define('TABLE_HEADING_PASSWORD', '密码');
define('TABLE_HEADING_NAME', '姓名');
define('TABLE_HEADING_EMAIL', 'E-Mail');
define('TABLE_HEADING_NEW_PASSWORD', '新密码');
define('TABLE_HEADING_CONFIRM_PASSWORD', '确认密码');
define('TABLE_HEADING_USER', '一般用户');
define('TABLE_HEADING_ADMIN', '网站管理者');

// JavaScript的确认信息
define('JAVA_SCRIPT_INFO_CHANGE', '更改用户管理信息。\n可以吗？');
define('JAVA_SCRIPT_INFO_DELETE', '删除用户管理信息。\n可以吗？');
define('JAVA_SCRIPT_INFO_PASSWORD', '更改密码。\n可以吗？');
define('JAVA_SCRIPT_INFO_GRANT', '给与管理者权限。\n可以吗？');
define('JAVA_SCRIPT_INFO_REVOKE', '取消管理者权限。\n可以吗？');
?>