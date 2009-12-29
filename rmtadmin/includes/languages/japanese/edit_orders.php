<?php
/*
  $Id: edit_orders.php,v 1.25 2003/08/07 00:28:44 jwh Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

define('HEADING_TITLE', '��ʸ�����Խ�');
define('HEADING_TITLE_SEARCH', '��ʸID:');
define('HEADING_TITLE_STATUS', '���ơ�����:');
define('ADDING_TITLE', '���ʤ��ɲ�');

define('ENTRY_UPDATE_TO_CC', '(Update to <b>Credit Card</b> to view CC fields.)');
define('TABLE_HEADING_COMMENTS', '������');
define('TABLE_HEADING_CUSTOMERS', '�ܵ�̾');
define('TABLE_HEADING_ORDER_TOTAL', '��ʸ���');
define('TABLE_HEADING_DATE_PURCHASED', '��ʸ��');
define('TABLE_HEADING_STATUS', '���ơ�����');
define('TABLE_HEADING_ACTION', '���');
define('TABLE_HEADING_QUANTITY', '����');
define('TABLE_HEADING_PRODUCTS_MODEL', '����');
define('TABLE_HEADING_PRODUCTS', '����̾');
define('TABLE_HEADING_TAX', '������');
define('TABLE_HEADING_TOTAL', '���');
define('TABLE_HEADING_UNIT_PRICE', 'ñ��');
define('TABLE_HEADING_TOTAL_PRICE', '���');

define('TABLE_HEADING_CUSTOMER_NOTIFIED', '��������������');
define('TABLE_HEADING_DATE_ADDED', '������');

define('ENTRY_CUSTOMER', '�ܵ�̾:');
define('ENTRY_CUSTOMER_NAME', '̾��');
//add
define('ENTRY_CUSTOMER_NAME_F', '̾��(�եꥬ��)');
define('ENTRY_CUSTOMER_COMPANY', '���̾');
define('ENTRY_CUSTOMER_ADDRESS', '����');
define('ENTRY_CUSTOMER_SUBURB', '��ʪ̾');
define('ENTRY_CUSTOMER_CITY', '�Զ�Į¼');
define('ENTRY_CUSTOMER_STATE', '��ƻ�ܸ�');
define('ENTRY_CUSTOMER_POSTCODE', '͹���ֹ�');
define('ENTRY_CUSTOMER_COUNTRY', '��̾');

define('ENTRY_SOLD_TO', '������:');
define('ENTRY_DELIVERY_TO', '���Ϥ���:');
define('ENTRY_SHIP_TO', '���Ϥ���:');
define('ENTRY_SHIPPING_ADDRESS', '���Ϥ���:');
define('ENTRY_BILLING_ADDRESS', '������:');
define('ENTRY_PAYMENT_METHOD', '��ʧ��ˡ:');
define('ENTRY_CREDIT_CARD_TYPE', '���쥸�åȥ����ɥ�����:');
define('ENTRY_CREDIT_CARD_OWNER', '������̾��:');
define('ENTRY_CREDIT_CARD_NUMBER', '�������ֹ�:');
define('ENTRY_CREDIT_CARD_EXPIRES', '������ͭ������:');
define('ENTRY_SUB_TOTAL', '����:');
define('ENTRY_TAX', '������:');
define('ENTRY_SHIPPING', '������ˡ:');
define('ENTRY_TOTAL', '���:');
define('ENTRY_DATE_PURCHASED', '��ʸ��:');
define('ENTRY_STATUS', '���ơ�����:');
define('ENTRY_DATE_LAST_UPDATED', '������:');
define('ENTRY_NOTIFY_CUSTOMER', '��������������:');
define('ENTRY_NOTIFY_COMMENTS', '�����Ȥ��ɲ�:');
define('ENTRY_PRINTABLE', 'Ǽ�ʽ��ץ���');

define('TEXT_INFO_HEADING_DELETE_ORDER', '��ʸ���');
define('TEXT_INFO_DELETE_INTRO', '�����ˤ�����ʸ�������ޤ���?');
define('TEXT_INFO_RESTOCK_PRODUCT_QUANTITY', '�߸˿��򸵤��᤹');
define('TEXT_DATE_ORDER_CREATED', '������:');
define('TEXT_DATE_ORDER_LAST_MODIFIED', '������:');
define('TEXT_DATE_ORDER_ADDNEW', '���ʤ��ɲ�');
define('TEXT_INFO_PAYMENT_METHOD', '��ʧ��ˡ:');

define('TEXT_ALL_ORDERS', '���٤Ƥ���ʸ');
define('TEXT_NO_ORDER_HISTORY', '��ʸ����Ϥ���ޤ���');

define('EMAIL_SEPARATOR', '--------------------------------------------------------------------------------');
define('EMAIL_TEXT_SUBJECT', '����ʸ���վ����Τ��Τ餻');
define('EMAIL_TEXT_ORDER_NUMBER', '����ʸ�����ֹ�: ');
define('EMAIL_TEXT_INVOICE_URL', '����ʸ�ˤĤ��Ƥξ���򲼵�URL�Ǥ����ˤʤ�ޤ���' . "\n");
define('EMAIL_TEXT_DATE_ORDERED', '����ʸ��: ');
define('EMAIL_TEXT_STATUS_UPDATE',
'����ʸ�μ��վ����ϼ��Τ褦�ʤäƤ���ޤ���' . "\n"
.'���ߤμ��վ���: [ %s ]' . "\n\n");
define('EMAIL_TEXT_COMMENTS_UPDATE', '[��Ϣ�����]' . "\n\n%s");

define('ERROR_ORDER_DOES_NOT_EXIST', '���顼: ��ʸ��¸�ߤ��ޤ���');
define('SUCCESS_ORDER_UPDATED', '����: ��ʸ���֤���������ޤ�����');
define('WARNING_ORDER_NOT_UPDATED', '�ٹ�: ��ʸ���֤Ϥʤˤ��ѹ�����ޤ���Ǥ�����');

define('ADDPRODUCT_TEXT_CATEGORY_CONFIRM', 'OK');
define('ADDPRODUCT_TEXT_SELECT_PRODUCT', '���ʤ�����');
define('ADDPRODUCT_TEXT_PRODUCT_CONFIRM', 'OK');
define('ADDPRODUCT_TEXT_SELECT_OPTIONS', '���ץ���������');
define('ADDPRODUCT_TEXT_OPTIONS_CONFIRM', 'OK');
define('ADDPRODUCT_TEXT_OPTIONS_NOTEXIST', '���ץ����Ϥ���ޤ���: �����åפ��ޤ�..');
define('ADDPRODUCT_TEXT_CONFIRM_QUANTITY', '����');
define('ADDPRODUCT_TEXT_CONFIRM_ADDNOW', '�ɲ�');

// Add Japanese osCommerce
define('EMAIL_TEXT_STORE_CONFIRMATION', ' �ؤΤ���ʸ�����ˤ��꤬�Ȥ��������ޤ���' . "\n" . 
'����ʸ�μ��վ����ڤӤ�Ϣ�����򡢲����ˤ����⿽���夲�ޤ���');
define('TABLE_HEADING_COMMENTS_ADMIN', '[��Ϣ�����]');
define('EMAIL_TEXT_STORE_CONFIRMATION_FOOTER', 
'���վ����˴ؤ��Ƥ����������������ޤ����顢��Ź���ˤ�Ϣ��ĺ���ޤ��褦���ꤤ����' . "\n"
.'�夲�ޤ���' . "\n\n"
. EMAIL_SIGNATURE);
define('ADD_A_NEW_PRODUCT', '���ʤ��ɲ�');
define('CHOOSE_A_CATEGORY', ' --- ���ʥ��ƥ�������� --- ');
define('SELECT_THIS_CATECORY', '���ƥ�������¹�');
define('CHOOSE_A_PRODUCT', ' --- ���ʤ����� --- ');
define('SELECT_THIS_PRODUCT', '��������¹�');
define('NO_OPTION_SKIPPED', '���ץ����Ϥ���ޤ��� - �����åפ��ޤ�....');
define('SELECT_THESE_OPTIONS', '���ץ��������¹�');
define('SELECT_QUANTITY', ' ����');
define('SELECT_ADD_NOW', '�ɲü¹�');
define('SELECT_STEP_ONE', 'STEP 1:');
define('SELECT_STEP_TWO', 'STEP 2:');
define('SELECT_STEP_THREE', 'STEP 3:');
define('SELECT_STEP_FOUR', 'STEP 4:');

?>