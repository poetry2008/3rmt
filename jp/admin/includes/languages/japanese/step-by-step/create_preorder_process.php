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
define('TEXT_SELECT_CURRENCY', '通貨選択:');

define('HEADING_TITLE', '手動注文手続き');
if(!defined('HEADING_CREATE'))define('HEADING_CREATE', '手動注文する顧客の詳細を確認:'); 

define('TEXT_SELECT_CUST', '顧客選択:'); 
if(!defined('TEXT_SELECT_CURRENCY'))define('TEXT_SELECT_CURRENCY', '通貨選択:');
define('BUTTON_TEXT_SELECT_CUST', '顧客選択:'); 
define('TEXT_OR_BY', 'または顧客ID:'); 
define('TEXT_STEP_1', 'ステップ 1 - 顧客を選択し詳細を確認してください');
define('BUTTON_SUBMIT', '確認する');
define('ENTRY_CURRENCY','決済通貨');
define('CATEGORY_ORDER_DETAILS','通貨設定');

define('CATEGORY_CORRECT', '顧客情報');
define('CREATE_ORDER_RED_TITLE_TEXT', '入力情報に誤りがあります');
define('CREATE_PREORDER_PREDATE', '有効期限');
define('CREATE_PREORDER_MUST_INPUT', '必須');
?>
