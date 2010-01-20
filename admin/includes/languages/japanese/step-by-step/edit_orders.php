<?php
/*
  $Id: edit_orders.php,v 1.25 2003/08/07 00:28:44 jwh Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce
  
  Released under the GNU General Public License
*/

define('HEADING_TITLE', '��ʸ���Խ����ܺ�');
define('HEADING_TITLE_NUMBER', '��ʸ�ֹ�:');
define('HEADING_TITLE_DATE', ' - ');
define('HEADING_SUBTITLE', '�Խ���������ʬ�����Ƥ����Ϥ��������ܥ���򥯥�å����Ƥ���������');
define('HEADING_TITLE_SEARCH', '��ʸ ID:');
define('HEADING_TITLE_STATUS', '���ơ�����:');
define('ADDING_TITLE', '���ʤ��ɲä���');

define('HINT_UPDATE_TO_CC', '<font color="#FF0000">�ҥ��: </font>Set payment to "Credit Card" to show some additional fields.');
define('HINT_DELETE_POSITION', '<font color="#FF0000">�ҥ��: </font>���ʤ���������ϸĿ��ˡ�0�פ����Ϥ��ƹ������Ƥ���������');
define('HINT_TOTALS', '<font color="#FF0000">�ҥ��: </font>Feel free to give discounts by adding negative amounts to the list.<br>Fields with "0" values are deleted when updating the order (exception: shipping).');
define('HINT_PRESS_UPDATE', '�����ܥ���򥯥�å����ơ��Խ��������Ƥ򹹿����Ƥ���������');

define('TABLE_HEADING_COMMENTS', '������');
define('TABLE_HEADING_CUSTOMERS', '�ܵҾ���');
define('TABLE_HEADING_ORDER_TOTAL', '��׶��');
define('TABLE_HEADING_DATE_PURCHASED', '��ʸ��');
define('TABLE_HEADING_STATUS', '���������ơ�����');
define('TABLE_HEADING_ACTION', '���');
define('TABLE_HEADING_QUANTITY', '����');
define('TABLE_HEADING_PRODUCTS_MODEL', '���ʷ���');
define('TABLE_HEADING_PRODUCTS', '����');
define('TABLE_HEADING_TAX', '������');
define('TABLE_HEADING_TOTAL', '���');
define('TABLE_HEADING_UNIT_PRICE', '���� (��ȴ��)');
define('TABLE_HEADING_UNIT_PRICE_TAXED', '���� (�ǹ���)');
define('TABLE_HEADING_TOTAL_PRICE', '��� (��ȴ��)');
define('TABLE_HEADING_TOTAL_PRICE_TAXED', '��� (�ǹ���)');
define('TABLE_HEADING_TOTAL_MODULE', '���ʹ�������');
define('TABLE_HEADING_TOTAL_AMOUNT', '���');

define('TABLE_HEADING_CUSTOMER_NOTIFIED', '�ܵҤ�����');
define('TABLE_HEADING_DATE_ADDED', '������');

define('ENTRY_CUSTOMER', '�ܵҾ���');
define('ENTRY_CUSTOMER_NAME', '��̾��');
define('ENTRY_CUSTOMER_COMPANY', '���̾');
define('ENTRY_CUSTOMER_ADDRESS', '����1');
define('ENTRY_CUSTOMER_SUBURB', '����2');
define('ENTRY_CUSTOMER_CITY', '��Į¼');
define('ENTRY_CUSTOMER_STATE', '��ƻ�ܸ�');
define('ENTRY_CUSTOMER_POSTCODE', '͹���ֹ�');
define('ENTRY_CUSTOMER_COUNTRY', '��̾');
define('ENTRY_CUSTOMER_PHONE', '�����ֹ�');
define('ENTRY_CUSTOMER_EMAIL', 'E�᡼�륢�ɥ쥹');

define('ENTRY_SOLD_TO', '��ʸ��:');
define('ENTRY_DELIVERY_TO', '������:');
define('ENTRY_SHIP_TO', 'Shipping to:');
define('ENTRY_SHIPPING_ADDRESS', '�����轻��');
define('ENTRY_BILLING_ADDRESS', '�����轻��');
define('ENTRY_PAYMENT_METHOD', '��ʧ��ˡ:');
define('ENTRY_CREDIT_CARD_TYPE', '�����ɥ�����:');
define('ENTRY_CREDIT_CARD_OWNER', '��������ͭ��:');
define('ENTRY_CREDIT_CARD_NUMBER', '�������ֹ�:');
define('ENTRY_CREDIT_CARD_EXPIRES', 'ͭ������:');
define('ENTRY_SUB_TOTAL', '����:');
define('ENTRY_TAX', '������:');
define('ENTRY_SHIPPING', '����:');
define('ENTRY_TOTAL', '���:');
define('ENTRY_DATE_PURCHASED', '��ʸ��:');
define('ENTRY_STATUS', '���ơ�����:');
define('ENTRY_DATE_LAST_UPDATED', '�ǽ�������:');
define('ENTRY_NOTIFY_CUSTOMER', '�ܵҤ�����:');
define('ENTRY_NOTIFY_COMMENTS', '�����Ȥ�����:');
define('ENTRY_PRINTABLE', 'Ǽ�ʽ����');

define('TEXT_INFO_HEADING_DELETE_ORDER', '��ʸ����');
define('TEXT_INFO_DELETE_INTRO', '��������ʸ�������ޤ�����');
define('TEXT_INFO_RESTOCK_PRODUCT_QUANTITY', '�߸ˤ��᤹');
define('TEXT_DATE_ORDER_CREATED', '������:');
define('TEXT_DATE_ORDER_LAST_MODIFIED', '�ǽ�������:');
define('TEXT_DATE_ORDER_ADDNEW', '���������ʤ��ɲ�');
define('TEXT_INFO_PAYMENT_METHOD', '��ʧ��ˡ:');

define('TEXT_ALL_ORDERS', '���Ƥ���ʸ');
define('TEXT_NO_ORDER_HISTORY', '��ʸ��¸�ߤ��ޤ���');

define('EMAIL_SEPARATOR', '------------------------------------------------------');
define('EMAIL_TEXT_SUBJECT', '����ʸ���վ����Τ��Τ餻');
define('EMAIL_TEXT_ORDER_NUMBER', '����ʸ�����ֹ�:');
define('EMAIL_TEXT_INVOICE_URL', '����ʸ�ˤĤ��Ƥξ���򲼵�URL�Ǥ����ˤʤ�ޤ���' . "\n");
define('EMAIL_TEXT_DATE_ORDERED', '��ʸ��:');
define('EMAIL_TEXT_STATUS_UPDATE',
'����ʸ�μ��վ����ϼ��Τ褦�ʤäƤ���ޤ���' . "\n"
.'���ߤμ��վ���: [ %s ]' . "\n\n");
define('EMAIL_TEXT_COMMENTS_UPDATE', '[��Ϣ�����]' . "\n%s");

define('ERROR_ORDER_DOES_NOT_EXIST', '���顼: ��ʸ��¸�ߤ��ޤ���');
define('SUCCESS_ORDER_UPDATED', '����: ��ʸ���֤���������ޤ�����');
define('WARNING_ORDER_NOT_UPDATED', '�ٹ�: ��ʸ���֤Ϥʤˤ��ѹ�����ޤ���Ǥ�����');

// Add Japanese osCommerce
define('EMAIL_TEXT_STORE_CONFIRMATION', ' �ؤΤ���ʸ�����ˤ��꤬�Ȥ��������ޤ���' . "\n\n"
.'����ʸ�μ��վ����ڤӤ�Ϣ�����򡢲����ˤ����⿽���夲�ޤ���');
define('EMAIL_TEXT_STORE_CONFIRMATION_FOOTER', 
'���վ����˴ؤ��Ƥ����������������ޤ����顢��Ź���ˤ�Ϣ��ĺ���ޤ��褦���ꤤ����' . "\n"
.'�夲�ޤ���' . "\n\n"
. EMAIL_SIGNATURE);


define('ADDPRODUCT_TEXT_CATEGORY_CONFIRM', 'OK');
define('ADDPRODUCT_TEXT_SELECT_PRODUCT', '���ʤ�����');
define('ADDPRODUCT_TEXT_PRODUCT_CONFIRM', 'OK');
define('ADDPRODUCT_TEXT_SELECT_OPTIONS', '���ʥ��ץ���������');
define('ADDPRODUCT_TEXT_OPTIONS_CONFIRM', 'OK');
define('ADDPRODUCT_TEXT_OPTIONS_NOTEXIST', '���ʥ��ץ�����¸�ߤ��ޤ��󡣥����å�...');
define('ADDPRODUCT_TEXT_CONFIRM_QUANTITY', '���ξ��ʤο���');
define('ADDPRODUCT_TEXT_CONFIRM_ADDNOW', '�ɲä���');
define('ADDPRODUCT_TEXT_STEP', '���ƥå�');
define('ADDPRODUCT_TEXT_STEP1', ' &laquo; ���ƥ�������. ');
define('ADDPRODUCT_TEXT_STEP2', ' &laquo; ��������. ');
define('ADDPRODUCT_TEXT_STEP3', ' &laquo; ���ץ��������. ');

define('MENUE_TITLE_CUSTOMER', '1. �ܵҾ���');
define('MENUE_TITLE_PAYMENT', '2. ��ʧ��ˡ');
define('MENUE_TITLE_ORDER', '3. ��ʸ����');
define('MENUE_TITLE_TOTAL', '4. ��������ѡ��Ƕ�');
define('MENUE_TITLE_STATUS', '5. ��ʸ���ơ�����������������');
define('MENUE_TITLE_UPDATE', '6. �ǡ����򹹿�');
?>