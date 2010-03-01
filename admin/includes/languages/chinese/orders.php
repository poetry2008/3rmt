<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

define('HEADING_TITLE', '订单管理');
define('HEADING_TITLE_SEARCH', '订单ID:');
define('HEADING_TITLE_STATUS', '状态:');

define('TABLE_HEADING_COMMENTS', 'comment');
define('TABLE_HEADING_CUSTOMERS', '顾客名');
define('TABLE_HEADING_ORDER_TOTAL', '订单总额');
define('TABLE_HEADING_DATE_PURCHASED', '订购日期');
define('TABLE_HEADING_STATUS', '状态');
define('TABLE_HEADING_ACTION', '操作');
define('TABLE_HEADING_QUANTITY', '数量');
define('TABLE_HEADING_PRODUCTS_MODEL', '型号');
define('TABLE_HEADING_PRODUCTS', '数量/ 商品名');
define('TABLE_HEADING_CHARACTER', '人物名');
define('TABLE_HEADING_TAX', '说率');
define('TABLE_HEADING_TOTAL', '合计');
define('TABLE_HEADING_PRICE_EXCLUDING_TAX', '价格(不含税)');
define('TABLE_HEADING_PRICE_INCLUDING_TAX', '价格(含税)');
define('TABLE_HEADING_TOTAL_EXCLUDING_TAX', '合计(不含税)');
define('TABLE_HEADING_TOTAL_INCLUDING_TAX', '合计(含税)');

define('TABLE_HEADING_CUSTOMER_NOTIFIED', '通知顾客');
define('TABLE_HEADING_DATE_ADDED', '处理日期');

define('ENTRY_CUSTOMER', '顾客名:');
define('ENTRY_SOLD_TO', '订购者名:');
define('ENTRY_TELEPHONE', '电话号码:');
define('ENTRY_DELIVERY_TO', '配送地址:');
define('ENTRY_SHIP_TO', '配送地址:');
define('ENTRY_SHIPPING_ADDRESS', '配送地址:');
define('ENTRY_BILLING_ADDRESS', '請求先:');
define('ENTRY_PAYMENT_METHOD', '支払方法:');
define('ENTRY_CREDIT_CARD_TYPE', '信用卡类型:');
define('ENTRY_CREDIT_CARD_OWNER', '信用卡所有人:');
define('ENTRY_CREDIT_CARD_NUMBER', '信用卡号:');
define('ENTRY_CREDIT_CARD_EXPIRES', '信用卡有效期间:');
define('ENTRY_SUB_TOTAL', '小计:');
define('ENTRY_TAX', '税金:');
define('ENTRY_SHIPPING', '配送:');
define('ENTRY_TOTAL', '合计:');
define('ENTRY_DATE_PURCHASED', '订购日期:');
define('ENTRY_STATUS', '状态:');
define('ENTRY_DATE_LAST_UPDATED', '更新日期:');
define('ENTRY_NOTIFY_CUSTOMER', '通知处理状况:');
define('ENTRY_NOTIFY_COMMENTS', '添加comments:');
define('ENTRY_PRINTABLE', '打印发票');

define('TEXT_INFO_HEADING_DELETE_ORDER', '删除订单');
define('TEXT_INFO_DELETE_INTRO', '确定删除订单吗?');
define('TEXT_INFO_RESTOCK_PRODUCT_QUANTITY', '归还库存'); // 'Restock product quantity'
define('TEXT_DATE_ORDER_CREATED', '订单日期:');
define('TEXT_DATE_ORDER_LAST_MODIFIED', '更新日期:');
define('TEXT_INFO_PAYMENT_METHOD', '支付方法:');

define('TEXT_ALL_ORDERS', '全部订单');
define('TEXT_NO_ORDER_HISTORY', '没有订购记录');

define('EMAIL_SEPARATOR', '--------------------------------------------------------------------------------');
define('EMAIL_TEXT_SUBJECT', '订单受理状况的通知');
define('EMAIL_TEXT_ORDER_NUMBER', '订单受理号:');
define('EMAIL_TEXT_INVOICE_URL', '关于订单的信息请参考下面的URL。' . "\n");
define('EMAIL_TEXT_DATE_ORDERED', '订购日期:');
define('EMAIL_TEXT_STATUS_UPDATE',
'订单的受理状况如下。' . "\n"
.'现在的受理状态: [ %s ]' . "\n\n");
define('EMAIL_TEXT_COMMENTS_UPDATE', '[联络事项]' . "\n%s");

define('ERROR_ORDER_DOES_NOT_EXIST', '错误: 订单不存在。');
define('SUCCESS_ORDER_UPDATED', '成功:订单状态已更新。');
define('WARNING_ORDER_NOT_UPDATED', '警告: 订单状态没有更改。');

// Add Japanese osCommerce
define('EMAIL_TEXT_STORE_CONFIRMATION', ' 感谢订购~~~~。' . "\n\n".'订单处理状态及联络事项、请参考下面。');
define('EMAIL_TEXT_STORE_CONFIRMATION_FOOTER', '关于订单状态有疑问的话，请联络本店地址' . "\n".' 。' . "\n\n". EMAIL_SIGNATURE);
define('ENTRY_EMAIL_TITLE', '邮件标题：');
?>
