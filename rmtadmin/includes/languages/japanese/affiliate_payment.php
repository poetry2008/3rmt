<?php
/*
  $Id: affiliate_payment.php,v v 2.00 2003/10/12

  OSC-Affiliate
  
  Contribution based on:
  
  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 - 2003 osCommerce

  Released under the GNU General Public License
*/

define('HEADING_TITLE', '��ʧ������');
define('HEADING_TITLE_SEARCH', '����:');
define('HEADING_TITLE_STATUS','���ơ�����:');

define('TEXT_ALL_PAYMENTS','���٤�ɽ��');
define('TEXT_NO_PAYMENT_HISTORY', '���������¸�ߤ��ޤ���');


define('TABLE_HEADING_ACTION', '���');
define('TABLE_HEADING_STATUS', '���ơ�����');
define('TABLE_HEADING_AFILIATE_NAME', '���̾');
define('TABLE_HEADING_PAYMENT','��ʧ�ۡ��ǹ��ߡ�');
define('TABLE_HEADING_NET_PAYMENT','��ʧ�ۡ���ȴ����');
define('TABLE_HEADING_DATE_BILLED','������');
define('TABLE_HEADING_NEW_VALUE', '�ѹ���ʺǿ���');
define('TABLE_HEADING_OLD_VALUE', '�ѹ���');
define('TABLE_HEADING_AFFILIATE_NOTIFIED', '���ե��ꥨ���Ȳ��������');
define('TABLE_HEADING_DATE_ADDED', '������');

define('TEXT_DATE_PAYMENT_BILLED','������:');
define('TEXT_DATE_ORDER_LAST_MODIFIED','�ǽ�������:');
define('TEXT_AFFILIATE_PAYMENT','��ʧ�����');
define('TEXT_AFFILIATE_BILLED','�󽷳�����');
define('TEXT_AFFILIATE','���̾');
define('TEXT_INFO_DELETE_INTRO','���λ�ʧ�������ɤ������Ƥ������Ǥ�����');
define('TEXT_DISPLAY_NUMBER_OF_PAYMENTS', '<b>%d</b> �� <b>%d</b>��ɽ�� (<b>%d</b> ����)');

define('TEXT_AFFILIATE_PAYING_POSSIBILITIES','��Ͽ����Ƥ����ʧ����:');
define('TEXT_AFFILIATE_PAYMENT_CHECK','Check:');
define('TEXT_AFFILIATE_PAYMENT_CHECK_PAYEE','��ʧ����:');
define('TEXT_AFFILIATE_PAYMENT_PAYPAL','PayPal:');
define('TEXT_AFFILIATE_PAYMENT_PAYPAL_EMAIL','PayPal���������Email:');
define('TEXT_AFFILIATE_PAYMENT_BANK_TRANSFER','��ʧ����:');
define('TEXT_AFFILIATE_PAYMENT_BANK_NAME','���̾:');
define('TEXT_AFFILIATE_PAYMENT_BANK_ACCOUNT_NAME','����̾����:');
define('TEXT_AFFILIATE_PAYMENT_BANK_ACCOUNT_NUMBER','�����ֹ�:');
define('TEXT_AFFILIATE_PAYMENT_BANK_BRANCH_NUMBER','��Ź�ֹ�:');
define('TEXT_AFFILIATE_PAYMENT_BANK_SWIFT_CODE','��Ź̾:');

define('TEXT_INFO_HEADING_DELETE_PAYMENT','���');

define('IMAGE_AFFILIATE_BILLING','����');

define('ERROR_PAYMENT_DOES_NOT_EXIST','��ʧ���쥳���ɤ�¸�ߤ��ޤ���');


define('SUCCESS_BILLING','�������Ƥ�����˹�������ޤ���');
define('SUCCESS_PAYMENT_UPDATED','��ʧ�����ơ�����������˹�������ޤ���');

define('PAYMENT_STATUS','��ʧ�����ơ�����');
define('PAYMENT_NOTIFY_AFFILIATE', '��������Τ���');

define('EMAIL_SEPARATOR', '------------------------------------------------------');
define('EMAIL_TEXT_SUBJECT', ''. STORE_NAME . ' ���ե���ꥨ���ȥץ����ڤ���ʧ�����������Ρ�');
define('EMAIL_TEXT_AFFILIATE_PAYMENT_NUMBER', '����ʧ���ֹ�:');
define('EMAIL_TEXT_INVOICE_URL', '����ʧ�������ܺ�:');
define('EMAIL_TEXT_PAYMENT_BILLED', '�󽷳�����');
define('EMAIL_TEXT_STATUS_UPDATE', '���ʤ��λ�ʧ����������������ޤ�����' . "\n\n" . '���ߤΤ���ʧ������: %s' . "\n\n" . '�᡼������ƤˤĤ��Ƥ����䤬������Ϥ���礻����������'  . "\n\n"
. EMAIL_SIGNATURE);
define('EMAIL_TEXT_NEW_PAYMENT', '���ʤ����Ф��ƤΤ���ʧ����������������ޤ�����' . "\n");
?>
