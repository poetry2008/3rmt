<?php
/* *********************************************************

   ユーザ管理の言語定義  
   ■変更履歴
********************************************************* */


define('HEADING_TITLE', '用户管理');


define('TABLE_HEADING_ERRINFO', '!!!!! 错误信息 !!!!!');


define('TEXT_ERRINFO_INPUT_NOINPUT', '没有输入');
define('TEXT_ERRINFO_INPUT_ERR', '没有正确输入');
define('TEXT_ERRINFO_INPUT_LENGTH', '请输入%d 字以上以上');
define('TEXT_ERRINFO_USER_DELETE', '<b>删除客户信息</b>:无法删除用户本人的信息');
define('TEXT_ERRINFO_USER_GRANT', '<b>给予权限</b>:请选择用户');
define('TEXT_ERRINFO_USER_REVOKE', '<b>取消权限</b>:请选择用户');
define('TEXT_ERRINFO_USER_REVOKE_ONESELF', '<b>取消权限</b>:无法取消用户本人的权限');
define('TEXT_ERRINFO_CONFIRM_PASSWORD', '<b>再次输入确认</b>:确认的结果是再次输入的密码有误');


define('TEXT_ERRINFO_DB_NO_USERINFO', '无法获得用户信息');
define('TEXT_ERRINFO_DB_NO_USER', '目标用户不存在');
define('TEXT_ERRINFO_DB_USERCHACK', '用户存在验证发送错误');
define('TEXT_ERRINFO_DB_EXISTING_USER', '已注册的用户');
define('TEXT_ERRINFO_DB_INSERT_USER', '用户信息无法注册');
define('TEXT_ERRINFO_DB_INSERT_PERMISSION', '用户权限信息无法注册');
define('TEXT_ERRINFO_DB_UPDATE_USER', '用户信息无法更新');
define('TEXT_ERRINFO_DB_DELETE_USER', '用户信息无法删除');
define('TEXT_ERRINFO_DB_CHANGE_PASSWORD', '密码无法更改');
define('TEXT_ERRINFO_DB_CHANGE_USER', '用户权限无法更改');
define('TEXT_ERRINFO_DB_CHANGE_PERMISSION','用户权限无法更改');


define('TEXT_SUCCESSINFO_INSERT_USER', '已添加用户');
define('TEXT_SUCCESSINFO_UPDATE_USER', '用户信息已更新');
define('TEXT_SUCCESSINFO_DELETE_USER', '户信息已删除');
define('TEXT_SUCCESSINFO_CHANGE_PASSWORD', '密码已更改');
define('TEXT_SUCCESSINFO_PERMISSION_GIVE', '已给予用户权限');
define('TEXT_SUCCESSINFO_PERMISSION_CLEAR', '已取消用户权限');

define('TEXT_SUCCESSINFO_CHANGE_PERMISSION','已更改权限');

define('PAGE_TITLE_MENU_USER', '用户管理菜单');
define('PAGE_TITLE_INSERT_USER', '添加用户');
define('PAGE_TITLE_USERINFO', '用户信息');
define('PAGE_TITLE_PASSWORD', '密码更改');
define('PAGE_TITLE_PERMISSION', '管理者权限');
define('PAGE_TITLE_CHANGE_PERMISSION','网站权限管理');

define('BUTTON_BACK_MENU', '返回用户管理菜单');
define('BUTTON_INSERT_USER', '添加用户');
define('BUTTON_INFO_USER', '用户信息');
define('BUTTON_CHANGE_PASSWORD', '密码更改');
define('BUTTON_PERMISSION', '管理者权限');
define('BUTTON_INSERT', '添加');
define('BUTTON_CLEAR', '清除');
define('BUTTON_UPDATE', '更新');
define('BUTTON_DELETE', '删除');
define('BUTTON_RESET', '恢复原值');
define('BUTTON_CHANGE', '更改');
define('BUTTON_GRANT', '给予权限 >>');
define('BUTTON_REVOKE', '<< 取消权限');
define('BUTTON_BACK_PERMISSION', '返回管理者权限');
define('BUTTON_CHANGE_PERMISSION','网站权限');

define('TABLE_HEADING_COLUMN', '栏');
define('TABLE_HEADING_DATA', '数据');
define('TABLE_HEADING_USER', '用户');
define('TABLE_HEADING_USER_LIST', '用户一览');
define('TABLE_HEADING_USER_ID', '用户ID');
define('TABLE_HEADING_PASSWORD', '密码');
define('TABLE_HEADING_NAME', '姓名');
define('TABLE_HEADING_EMAIL', 'E-Mail');
define('TABLE_HEADING_NEW_PASSWORD', '新密码');
define('TABLE_HEADING_CONFIRM_PASSWORD', '再次输入确认');
!defined('TABLE_HEADING_USER')&& define('TABLE_HEADING_USER', '普通用户');
define('TABLE_HEADING_ADMIN', '网站管理者');


define('JAVA_SCRIPT_INFO_CHANGE', '更改用户管理信息。\n可以吗？');
define('JAVA_SCRIPT_INFO_DELETE', '删除用户管理信息。\n可以吗？');
define('JAVA_SCRIPT_INFO_PASSWORD', '更改密码。\n可以吗？');
define('JAVA_SCRIPT_INFO_GRANT', '给予管理者权限。\n可以吗？');
define('JAVA_SCRIPT_INFO_REVOKE', '取消管理者权限。\n可以吗？');
define('TABLE_HEADING_IP_LIMIT', '不受限IP设置');
define('JAVA_SCRIPT_INFO_C_PERMISSION','更改用户管理的权限。\n可以吗？');
define('TEXT_RAND_PWD_INFO','<p>格式： 以2011/2/22 01:00为例  位数:算式</p>
    <p>3:Y+n+d　＝2011+2+22的计算结果是2035。位数限制为3位，所以密码为035。</p>
    <p>5:ddd　　＝222222的计算结果是222222。位数限制为5位，所以密码为22222。</p>
    <p>3:Y/n　　＝2011/2=1005.5的计算结果是1005。位数限制为3位，所以密码为100。</p>
    <p>4:(y+y)*2　＝(11+11)*2的计算结果是44。位数限制为4位、加一个0所以密码为0044。</p>
    <p>可以使用算式：</p>
    <p>+　-　*　/　()</p>');
define('TEXT_LOGIN_COUNT','登入次数');
define('TEXT_RAND_PWD','密码');
define('TEXT_RAND_RULES','算式');
define('TEXT_ERROR_RULE','算式的格式不正确');


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
define('USER_EMAIL_ERROR','输入的邮箱地址有误!');
?>
