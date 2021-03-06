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

define('TEXT_ORDERS_FETCH_CONDITION', '交易情况');
define('TEXT_USER_ADDED','创建者');
define('TEXT_USER_UPDATE','更新者');
define('TEXT_DATE_ADDED','创建日');
define('TEXT_DATE_UPDATE','更新日');
define('TEXT_ORDERS_STATUS_OPTION', '选项');
define('TEXT_ORDERS_STATUS_OPTION_NORMAL', '交易中');
define('TEXT_ORDERS_STATUS_OPTION_SUCCESS', '交易成功');
define('TEXT_ORDERS_STATUS_OPTION_FAIL', '交易失败');
define('TEXT_TRANSACTION_EXPIRED_COMMENT', '超过送货时间，并且是勾选了此项目的状态对应的订单，显示警告图标。可以在多个状态下勾选此项目。');
define('TEXT_ORDERS_STATUS_REORDER_TEXT', '再配送的默认状态');
define('TEXT_ORDERS_STATUS_CONFIRM_TIME_TEXT', '支付时间记录状态');
define('TEXT_ORDERS_STATUS_EDIT_ORDERS_TEXT', '订单内容编辑的默认状态');
?>
