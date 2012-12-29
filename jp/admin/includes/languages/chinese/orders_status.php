<?php
/*
  $Id$

*/

define('HEADING_TITLE', '订单状态设置');

define('TABLE_HEADING_ORDERS_STATUS', '订单状态');
define('TABLE_HEADING_ACTION', '状态');

define('TEXT_INFO_EDIT_INTRO', '请添加必要的更改');
define('TEXT_INFO_ORDERS_STATUS_NAME', '订单状态:');
define('TEXT_INFO_INSERT_INTRO', '请输入与新订单状态相关的数据');
define('TEXT_INFO_DELETE_INTRO', '确定要删除这个订单状态吗?');
define('TEXT_INFO_HEADING_NEW_ORDERS_STATUS', '订单状态');
define('TEXT_INFO_HEADING_EDIT_ORDERS_STATUS', '编辑订单状态');
define('TEXT_INFO_HEADING_DELETE_ORDERS_STATUS', '删除订单状态');

define('ERROR_REMOVE_DEFAULT_ORDER_STATUS', '错误:默认的订单状态不能删除 。把其他的订单状态设为默认后再试一遍。');
define('ERROR_STATUS_USED_IN_ORDERS', '错误: 这个订单状态、现在正在订单中使用。');
define('ERROR_STATUS_USED_IN_HISTORY', '错误:这个订单状态、现在正在订单历史记录中使用 。');

//mail本文 add
define('TEXT_INFO_ORDERS_STATUS_MAIL', '邮件文本');
define('TEXT_INFO_ORDERS_STATUS_TITLE', '邮件标题');
define('TEXT_EDIT_ORDERS_STATUS_IMAGE', '图标');

define('TEXT_ORDERS_STATUS_FINISHED', '订单完成');
define('TEXT_ORDERS_STATUS_SET_PRICE_CALCULATION','平均单价的计算条件设置');
//mail本文 add end

define('TEXT_ORDERS_STATUS_DESCRIPTION','名称：${NAME}<br>邮件地址：${MAIL}<br>订购日期：${ORDER_D}<br>订单号：${ORDER_N}<br>支付方法：${PAY}<br>订单金额：${ORDER_M}<br>交易方式：${TRADING}<br>订单状态：${ORDER_S}<br>交易人物名：${ORDER_A}<br>网站名：${SITE_NAME}<br>网站URL：${SITE_URL}<br>联系邮箱：${SUPPORT_EMAIL}<br>银行营业日：${PAY_DATE}');
define('TEXT_ORDERS_FETCH_CONDITION', '交易情况');
define('TEXT_USER_ADDED','创建者:');
define('TEXT_USER_UPDATE','更新者:');
define('TEXT_DATE_ADDED','创建日:');
define('TEXT_DATE_UPDATE','更新日:');
define('TEXT_ORDERS_STATUS_OPTION', '选项');
define('TEXT_ORDERS_STATUS_OPTION_NORMAL', '交易中');
define('TEXT_ORDERS_STATUS_OPTION_SUCCESS', '交易成功');
define('TEXT_ORDERS_STATUS_OPTION_FAIL', '交易失败');
?>
