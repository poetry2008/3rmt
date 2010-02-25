<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce
  
  Released under the GNU General Public License
*/

define('HEADING_TITLE', '订单的编辑·详细');
define('HEADING_TITLE_NUMBER', '订单号:');
define('HEADING_TITLE_DATE', ' - ');
define('HEADING_SUBTITLE', '输入想要编辑的部分的内容，点击更新按钮。');
define('HEADING_TITLE_SEARCH', '订单ID:');
define('HEADING_TITLE_STATUS', '状态:');
define('ADDING_TITLE', '添加商品');

define('HINT_UPDATE_TO_CC', '<font color="#FF0000">提示: </font>Set payment to "Credit Card" to show some additional fields.');
define('HINT_DELETE_POSITION', '<font color="#FF0000">提示: </font>删除商品的时候，在个数处输入「0」，然后更新。');
define('HINT_TOTALS', '<font color="#FF0000">提示: </font>Feel free to give discounts by adding negative amounts to the list.<br>Fields with "0" values are deleted when updating the order (exception: shipping).');
define('HINT_PRESS_UPDATE', '点击更新按钮、更新编辑过的内容。');

define('TABLE_HEADING_COMMENTS', 'comment');
define('TABLE_HEADING_CUSTOMERS', '顾客信息');
define('TABLE_HEADING_ORDER_TOTAL', '合计金额');
define('TABLE_HEADING_DATE_PURCHASED', '订购日期');
define('TABLE_HEADING_STATUS', '新状态');
define('TABLE_HEADING_ACTION', '操作');
define('TABLE_HEADING_QUANTITY', '数量');
define('TABLE_HEADING_PRODUCTS_MODEL', '商品型号');
define('TABLE_HEADING_PRODUCTS', '商品');
define('TABLE_HEADING_TAX', '消费税');
define('TABLE_HEADING_TOTAL', '合计');
define('TABLE_HEADING_UNIT_PRICE', '价格 (不含税)');
define('TABLE_HEADING_UNIT_PRICE_TAXED', '价格 (含税)');
define('TABLE_HEADING_TOTAL_PRICE', '合计 (不含税)');
define('TABLE_HEADING_TOTAL_PRICE_TAXED', '合计 (含税)');
define('TABLE_HEADING_TOTAL_MODULE', '价格的构成要素');
define('TABLE_HEADING_TOTAL_AMOUNT', '金额');

define('TABLE_HEADING_CUSTOMER_NOTIFIED', '通知顾客');
define('TABLE_HEADING_DATE_ADDED', '发送日期');

define('ENTRY_CUSTOMER', '顾客信息');
define('ENTRY_CUSTOMER_NAME', '姓名');
define('ENTRY_CUSTOMER_COMPANY', '公司名');
define('ENTRY_CUSTOMER_ADDRESS', '地址1');
define('ENTRY_CUSTOMER_SUBURB', '地址2');
define('ENTRY_CUSTOMER_CITY', '市町村');
define('ENTRY_CUSTOMER_STATE', '都道府県');
define('ENTRY_CUSTOMER_POSTCODE', '邮编');
define('ENTRY_CUSTOMER_COUNTRY', '国名');
define('ENTRY_CUSTOMER_PHONE', '电话号码');
define('ENTRY_CUSTOMER_EMAIL', '邮箱地址');

define('ENTRY_SOLD_TO', '订购者:');
define('ENTRY_DELIVERY_TO', '送货地址:');
define('ENTRY_SHIP_TO', 'Shipping to:');
define('ENTRY_SHIPPING_ADDRESS', '送货地址住所');
define('ENTRY_BILLING_ADDRESS', '购买者地址住所');
define('ENTRY_PAYMENT_METHOD', '支付方法:');
define('ENTRY_CREDIT_CARD_TYPE', '卡类型:');
define('ENTRY_CREDIT_CARD_OWNER', '持卡人:');
define('ENTRY_CREDIT_CARD_NUMBER', '卡号:');
define('ENTRY_CREDIT_CARD_EXPIRES', '有效期限:');
define('ENTRY_SUB_TOTAL', '小计:');
define('ENTRY_TAX', '消费税:');
define('ENTRY_SHIPPING', '邮费:');
define('ENTRY_TOTAL', '合计:');
define('ENTRY_DATE_PURCHASED', '订购日期:');
define('ENTRY_STATUS', '状态:');
define('ENTRY_DATE_LAST_UPDATED', '最后更新日期期:');
define('ENTRY_NOTIFY_CUSTOMER', '告诉顾客:');
define('ENTRY_NOTIFY_COMMENTS', '发送comment:');
define('ENTRY_PRINTABLE', '打印发票');

define('TEXT_INFO_HEADING_DELETE_ORDER', '删除订单');
define('TEXT_INFO_DELETE_INTRO', '确认删除订单？');
define('TEXT_INFO_RESTOCK_PRODUCT_QUANTITY', '返回库存');
define('TEXT_DATE_ORDER_CREATED', '创建日期:');
define('TEXT_DATE_ORDER_LAST_MODIFIED', '最后更新日期期:');
define('TEXT_DATE_ORDER_ADDNEW', '添加新商品');
define('TEXT_INFO_PAYMENT_METHOD', '支付方法:');

define('TEXT_ALL_ORDERS', '全部的订单');
define('TEXT_NO_ORDER_HISTORY', '订单不存在。');

define('EMAIL_SEPARATOR', '------------------------------------------------------');
define('EMAIL_TEXT_SUBJECT', '订单受理状态的通知');
define('EMAIL_TEXT_ORDER_NUMBER', '订单受理号:');
define('EMAIL_TEXT_INVOICE_URL', '关于订购的信息，通过下面的URL查看。' . "\n");
define('EMAIL_TEXT_DATE_ORDERED', '订购日期:');
define('EMAIL_TEXT_STATUS_UPDATE',
'订单的受理状态如下。' . "\n"
.'现在的受理状态: [ %s ]' . "\n\n");
define('EMAIL_TEXT_COMMENTS_UPDATE', '[联系事项]' . "\n%s");

define('ERROR_ORDER_DOES_NOT_EXIST', '错误: 订单不存在。');
define('SUCCESS_ORDER_UPDATED', '成功: 订单状态已更新。');
define('WARNING_ORDER_NOT_UPDATED', '警告: 订单状态没有变化。');

// Add Japanese osCommerce
define('EMAIL_TEXT_STORE_CONFIRMATION', ' 感谢订购~~~~`。' . "\n\n"
.'订单的受理状态及联络事项如下。');
define('EMAIL_TEXT_STORE_CONFIRMATION_FOOTER', 
'关于订单状态，如果有疑问时，请联系本店的地址' . "\n"
.'。' . "\n\n"
. EMAIL_SIGNATURE);


define('ADDPRODUCT_TEXT_CATEGORY_CONFIRM', 'OK');
define('ADDPRODUCT_TEXT_SELECT_PRODUCT', '选择商品');
define('ADDPRODUCT_TEXT_PRODUCT_CONFIRM', 'OK');
define('ADDPRODUCT_TEXT_SELECT_OPTIONS', '选择商品option');
define('ADDPRODUCT_TEXT_OPTIONS_CONFIRM', 'OK');
define('ADDPRODUCT_TEXT_OPTIONS_NOTEXIST', '商品没有option，跳过....');
define('ADDPRODUCT_TEXT_CONFIRM_QUANTITY', '商品数量');
define('ADDPRODUCT_TEXT_CONFIRM_ADDNOW', '添加');
define('ADDPRODUCT_TEXT_STEP', 'step');
define('ADDPRODUCT_TEXT_STEP1', ' &laquo; 分类选择. ');
define('ADDPRODUCT_TEXT_STEP2', ' &laquo; 商品选择. ');
define('ADDPRODUCT_TEXT_STEP3', ' &laquo; option选择. ');

define('MENUE_TITLE_CUSTOMER', '1. 顾客信息');
define('MENUE_TITLE_PAYMENT', '2. 支付方法');
define('MENUE_TITLE_ORDER', '3. 订购商品');
define('MENUE_TITLE_TOTAL', '4. 发送、结算、税金');
define('MENUE_TITLE_STATUS', '5. 订单状态、comment通知');
define('MENUE_TITLE_UPDATE', '6. 更新数据');
?>