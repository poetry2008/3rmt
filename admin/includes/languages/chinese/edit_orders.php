<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

define('HEADING_TITLE', '编集订单内容');
define('HEADING_TITLE_SEARCH', '订单ID:');
define('HEADING_TITLE_STATUS', '状态:');
define('ADDING_TITLE', '商品的添加');

define('ENTRY_UPDATE_TO_CC', '(Update to <b>Credit Card</b> to view CC fields.)');
define('TABLE_HEADING_COMMENTS', 'comment');
define('TABLE_HEADING_CUSTOMERS', '顾客名');
define('TABLE_HEADING_ORDER_TOTAL', '订单总额');
define('TABLE_HEADING_DATE_PURCHASED', '订购日期');
define('TABLE_HEADING_STATUS', '状态');
define('TABLE_HEADING_ACTION', '操作');
define('TABLE_HEADING_QUANTITY', '数量');
define('TABLE_HEADING_PRODUCTS_MODEL', '型号');
define('TABLE_HEADING_PRODUCTS', '商品名');
define('TABLE_HEADING_TAX', '消費税');
define('TABLE_HEADING_TOTAL', '合计');
define('TABLE_HEADING_UNIT_PRICE', '单价');
define('TABLE_HEADING_TOTAL_PRICE', '合计');

define('TABLE_HEADING_CUSTOMER_NOTIFIED', '通知处理状态');
define('TABLE_HEADING_DATE_ADDED', '更新日期期');

define('ENTRY_CUSTOMER', '顾客姓名:');
define('ENTRY_CUSTOMER_NAME', '姓名');
//add
define('ENTRY_CUSTOMER_NAME_F', '姓名(平假名)');
define('ENTRY_CUSTOMER_COMPANY', '公司名');
define('ENTRY_CUSTOMER_ADDRESS', '住址');
define('ENTRY_CUSTOMER_SUBURB', '建物名');
define('ENTRY_CUSTOMER_CITY', '市区町村');
define('ENTRY_CUSTOMER_STATE', '都道府県');
define('ENTRY_CUSTOMER_POSTCODE', '邮政编码');
define('ENTRY_CUSTOMER_COUNTRY', '国名');

define('ENTRY_SOLD_TO', '购买者:');
define('ENTRY_DELIVERY_TO', '送货地址:');
define('ENTRY_SHIP_TO', '送货地址:');
define('ENTRY_SHIPPING_ADDRESS', '送货地址:');
define('ENTRY_BILLING_ADDRESS', '申请地址:');
define('ENTRY_PAYMENT_METHOD', '支付方法:');
define('ENTRY_CREDIT_CARD_TYPE', '信用卡类型:');
define('ENTRY_CREDIT_CARD_OWNER', '卡名:');
define('ENTRY_CREDIT_CARD_NUMBER', '卡号:');
define('ENTRY_CREDIT_CARD_EXPIRES', '卡有效期限:');
define('ENTRY_SUB_TOTAL', '小计:');
define('ENTRY_TAX', '消费税:');
define('ENTRY_SHIPPING', '配送方法:');
define('ENTRY_TOTAL', '合计:');
define('ENTRY_DATE_PURCHASED', '订购日期:');
define('ENTRY_STATUS', '状态:');
define('ENTRY_DATE_LAST_UPDATED', '更新日期期:');
define('ENTRY_NOTIFY_CUSTOMER', '通知出来状态:');
define('ENTRY_NOTIFY_COMMENTS', '添加comment:');
define('ENTRY_PRINTABLE', '打印发票');

define('TEXT_INFO_HEADING_DELETE_ORDER', '删除订单');
define('TEXT_INFO_DELETE_INTRO', '确定删除订单吗?');
define('TEXT_INFO_RESTOCK_PRODUCT_QUANTITY', '归还库存');
define('TEXT_DATE_ORDER_CREATED', '建成日期:');
define('TEXT_DATE_ORDER_LAST_MODIFIED', '更新日期期:');
define('TEXT_DATE_ORDER_ADDNEW', '添加商品');
define('TEXT_INFO_PAYMENT_METHOD', '支付方法:');

define('TEXT_ALL_ORDERS', '全部订单');
define('TEXT_NO_ORDER_HISTORY', '没有订购记录');

define('EMAIL_SEPARATOR', '--------------------------------------------------------------------------------');
define('EMAIL_TEXT_SUBJECT', '订单处理状态的通知');
define('EMAIL_TEXT_ORDER_NUMBER', '订单受理号: ');
define('EMAIL_TEXT_INVOICE_URL', '关于订单的信息在下面URL中查看。' . "\n");
define('EMAIL_TEXT_DATE_ORDERED', '订购日期: ');
define('EMAIL_TEXT_STATUS_UPDATE',
'订单受理状况如下。' . "\n"
.'现在的受理状况: [ %s ]' . "\n\n");
define('EMAIL_TEXT_COMMENTS_UPDATE', '[联络事项]' . "\n\n%s");

define('ERROR_ORDER_DOES_NOT_EXIST', '错误: 订单不存在。');
define('SUCCESS_ORDER_UPDATED', '成功: 订单状态已更新。');
define('WARNING_ORDER_NOT_UPDATED', '警告: 订单状态没有更改。');

define('ADDPRODUCT_TEXT_CATEGORY_CONFIRM', 'OK');
define('ADDPRODUCT_TEXT_SELECT_PRODUCT', '选择商品');
define('ADDPRODUCT_TEXT_PRODUCT_CONFIRM', 'OK');
define('ADDPRODUCT_TEXT_SELECT_OPTIONS', '选择option');
define('ADDPRODUCT_TEXT_OPTIONS_CONFIRM', 'OK');
define('ADDPRODUCT_TEXT_OPTIONS_NOTEXIST', '没有option，跳过..');
define('ADDPRODUCT_TEXT_CONFIRM_QUANTITY', '数量');
define('ADDPRODUCT_TEXT_CONFIRM_ADDNOW', '添加');

// Add Japanese osCommerce
define('EMAIL_TEXT_STORE_CONFIRMATION', ' 感谢订购·····。' . "\n" . 
'订单受理状况及联系事项请参考下面。');
define('TABLE_HEADING_COMMENTS_ADMIN', '[联络事项]');
define('EMAIL_TEXT_STORE_CONFIRMATION_FOOTER', 
'关于受理状况有疑问的话。请联系本店地址' . "\n"
.'   。' . "\n\n"
. EMAIL_SIGNATURE);
define('ADD_A_NEW_PRODUCT', '添加商品');
define('CHOOSE_A_CATEGORY', ' --- 选择商品分类 --- ');
define('SELECT_THIS_CATECORY', '进行分类选择');
define('CHOOSE_A_PRODUCT', ' --- 商品的选择 --- ');
define('SELECT_THIS_PRODUCT', '进行商品选择');
define('NO_OPTION_SKIPPED', '没有option - 跳过....');
define('SELECT_THESE_OPTIONS', '进行option选择');
define('SELECT_QUANTITY', ' 数量');
define('SELECT_ADD_NOW', '实行添加');
define('SELECT_STEP_ONE', 'STEP 1:');
define('SELECT_STEP_TWO', 'STEP 2:');
define('SELECT_STEP_THREE', 'STEP 3:');
define('SELECT_STEP_FOUR', 'STEP 4:');

?>