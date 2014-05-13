<?php
/*
  $Id$

*/

define('HEADING_CREATE', 'Check Customer Details');
define('DEFAULT_PAYMENT_METHOD', "Payment on Local Pickup. We accept cash, Interac, Visa and Master Card.");
define('TEXT_SUBTOTAL', "Subtotal :");
define('TEXT_DISCOUNT', "Discount :");
define('TEXT_DELIVERY', "Delivery :");
define('TEXT_TAX', "Tax :");
define('TEXT_TOTAL', "Total :");
define('TEXT_SELECT_CURRENCY', '货币选择:');

define('HEADING_TITLE', '手动进行订购流程');
if(!defined('HEADING_CREATE'))define('HEADING_CREATE', '确认手动下订单的顾客信息:'); 

define('TEXT_SELECT_CUST', '顾客选择:'); 
if(!defined('TEXT_SELECT_CURRENCY'))define('TEXT_SELECT_CURRENCY', '货币选择:');
define('BUTTON_TEXT_SELECT_CUST', '顾客选择:'); 
define('TEXT_OR_BY', '或顾客ID:'); 
define('TEXT_STEP_1', '步骤 1 - 请选择顾客确认信息');
define('BUTTON_SUBMIT', '确定');
define('ENTRY_CURRENCY','结算货币');
define('CATEGORY_ORDER_DETAILS','货币设置');

define('CATEGORY_CORRECT', '顾客信息');
define('CREATE_ORDER_RED_TITLE_TEXT', '填写信息有误');
define('CREATE_PREORDER_PREDATE', '有效期限');
define('CREATE_PREORDER_MUST_INPUT', '必须');
?>
