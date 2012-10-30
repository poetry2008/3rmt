<?php
/* *********************************************************
   用户管理的语言定义

  ■变更历史记录
********************************************************* */

// 页面标题
define('HEADING_TITLE', '用户管理');

// 错误信息表示标题
define('TABLE_HEADING_ERRINFO', '!!!!! 错误提示 !!!!!');

// 输入错误信息
define('TEXT_ERRINFO_INPUT_NOINPUT', '未输入');
define('TEXT_ERRINFO_INPUT_ERR', '未正确输入');
define('TEXT_ERRINFO_INPUT_LENGTH', '请输入%d 字以上');
define('TEXT_ERRINFO_USER_DELETE', '<b>用户信息的删除</b>:无法删除用户本人的信息');
define('TEXT_ERRINFO_USER_GRANT', '<b>给予权限</b>:请选择用户');
define('TEXT_ERRINFO_USER_REVOKE', '<b>取消权限</b>:请选择用户');
define('TEXT_ERRINFO_USER_REVOKE_ONESELF', '<b>取消权限</b>:无法取消用户本人的权限');
define('TEXT_ERRINFO_CONFIRM_PASSWORD', '<b>为了确认再次输入</b>:为了确认再次输入的密码不同');

// 访问表的错误信息
define('TEXT_ERRINFO_DB_NO_USERINFO', '无法获得用户信息');
define('TEXT_ERRINFO_DB_NO_USER', '成为对象的用户不存在');
define('TEXT_ERRINFO_DB_USERCHACK', '用户是否存在的检查发生错误');
define('TEXT_ERRINFO_DB_EXISTING_USER', '已经注册的用户');
define('TEXT_ERRINFO_DB_INSERT_USER', '无法注册用户信息');
define('TEXT_ERRINFO_DB_INSERT_PERMISSION', '无法注册用户权限信息');
define('TEXT_ERRINFO_DB_UPDATE_USER', '无法更新用户信息');
define('TEXT_ERRINFO_DB_DELETE_USER', '无法删除用户信息');
define('TEXT_ERRINFO_DB_CHANGE_PASSWORD', '无法更改密码');
define('TEXT_ERRINFO_DB_CHANGE_USER', '无法变更用户权限');
define('TEXT_ERRINFO_DB_CHANGE_PERMISSION','无法变更用户权限');

// 完了信息
define('TEXT_SUCCESSINFO_INSERT_USER', '追加用户');
define('TEXT_SUCCESSINFO_UPDATE_USER', '更新用户信息');
define('TEXT_SUCCESSINFO_DELETE_USER', '删除用户信息');
define('TEXT_SUCCESSINFO_CHANGE_PASSWORD', '变更完成。');
define('TEXT_SUCCESSINFO_PERMISSION', '%s用户权限');
define('TEXT_SUCCESSINFO_CHANGE_PERMISSION','更改权限');
// 页面标题
define('PAGE_TITLE_MENU_USER', '用户管理菜单');
define('PAGE_TITLE_INSERT_USER', '用户的追加');
define('PAGE_TITLE_USERINFO', '用户信息');
define('PAGE_TITLE_PASSWORD', '更改密码');
define('PAGE_TITLE_PERMISSION', '管理者权限');
define('PAGE_TITLE_CHANGE_PERMISSION','网站权限管理');
// 按钮
define('BUTTON_BACK_MENU', '返回到用户管理菜单');
define('BUTTON_INSERT_USER', '用户的追加');
define('BUTTON_INFO_USER', '用户信息');
define('BUTTON_CHANGE_PASSWORD', '更改密码');
define('BUTTON_PERMISSION', '管理者权限');
define('BUTTON_INSERT', '追加');
define('BUTTON_CLEAR', '清空');
define('BUTTON_UPDATE', '更新');
define('BUTTON_DELETE', '删除');
define('BUTTON_RESET', '返回原值');
define('BUTTON_CHANGE', '更改');
define('BUTTON_GRANT', '给予权限 >>');
define('BUTTON_REVOKE', '<< 取消权限');
define('BUTTON_BACK_PERMISSION', '返回管理者权限');
define('BUTTON_CHANGE_PERMISSION','网站权限');
// 項目名
define('TABLE_HEADING_COLUMN', '列');
define('TABLE_HEADING_DATA', '数据');
define('TABLE_HEADING_USER', '用户');
define('TABLE_HEADING_USER_LIST', '用户一览');
define('TABLE_HEADING_USER_ID', '用户ID');
define('TABLE_HEADING_PASSWORD', '密码');
define('TABLE_HEADING_NAME', '姓名');
define('TABLE_HEADING_EMAIL', 'E-Mail');
define('TABLE_HEADING_NEW_PASSWORD', '新密码');
define('TABLE_HEADING_CONFIRM_PASSWORD', '再输入确认');
!defined('TABLE_HEADING_USER')&& define('TABLE_HEADING_USER', '一般用户');
define('TABLE_HEADING_ADMIN', '网站管理者');

// JavaScript的确认信息
define('JAVA_SCRIPT_INFO_CHANGE', '更改用户管理信息。\n可以吗？');
define('JAVA_SCRIPT_INFO_DELETE', '删除用户管理信息。\n可以吗？');
define('JAVA_SCRIPT_INFO_PASSWORD', '更改密码。\n可以吗？');
define('JAVA_SCRIPT_INFO_GRANT', '开管理者权限。\n可以吗？');
define('JAVA_SCRIPT_INFO_REVOKE', '取消管理者权限。\n可以吗？');
define('TABLE_HEADING_IP_LIMIT', 'IP限制设置');
define('JAVA_SCRIPT_INFO_C_PERMISSION','变更用户管理权限。\n可以吗？');
define('TEXT_RAND_PWD_INFO','<p>格式： 2011/2/22 01:00的例子  位数:计算式</p>
    <p>3:Y+n+d　＝2011+2+22的结果是2035。因为这里位数限制是3，所以密码是035。</p>
    <p>5:ddd　　＝222222的结果是222222。因为这里位数限制是5，所以密码是22222。</p>
    <p>3:Y/n　　＝2011/2=1005.5的结果是1005。因为这里位数限制是3，所以密码是100。</p>
    <p>4:(y+y)*2　＝(11+11)*2的结果是44。因为这里位数限制是4，加0的密码是0044。</p>
    <p>可以使用的计算式：</p>
    <p>+　-　*　/　()</p>');
define('TEXT_LOGIN_COUNT','注册次数');
define('TEXT_RAND_PWD','密码');
define('TEXT_RAND_RULES','计算式');
define('TEXT_ERROR_RULE','计算式的格式不正确');

//add by szn
define('TABLE_HEADING_USER_STAFF', 'Staff');
define('TABLE_HEADING_USER_CHIEF', 'Chief');
define('TABLE_HEADING_USER_ADMIN', 'Admin');
define('JAVA_SCRIPT_INFO_STAFF2CHIEF', '给予Chief权限。\n可以吗？');
define('JAVA_SCRIPT_INFO_CHIEF2STAFF', '取消Chief权限。\n可以吗？');
define('JAVA_SCRIPT_INFO_CHIEF2ADMIN', '给予Admin权限。\n可以吗？');
define('JAVA_SCRIPT_INFO_ADMIN2CHIEF', '取消Admin权限。\n可以吗？');
define('TEXT_ERRINFO_USER_STAFF', '<b>给予权限</b>:请选择Staff');
define('TEXT_ERRINFO_USER_CHIEF', '<b>取消权限</b>:请选择Chief');
define('TEXT_ERRINFO_USER_ADMIN', '<b>取消权限</b>:请选择Admin');


define('JAVA_SCRIPT_ERRINFO_CONFIRM_PASSWORD','两次密码输入不一致');
?>
