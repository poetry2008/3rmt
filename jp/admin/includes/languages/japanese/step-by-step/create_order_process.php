<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
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

define('TEXT_BANK_NAME_PROCESS','金融機関名　　　　：');
define('TEXT_BANK_SHITEN_PROCESS','支店名　　　　　　：');
define('TEXT_BANK_KAMOKU_PROCESS','口座種別　　　　　：');
define('TEXT_BANK_KOUZA_NUM_PROCESS','口座番号　　　　　：');
define('TEXT_BANK_KOUZA_NAME_PROCESS','口座名義　　　　　：');
?>
