<?php
/*
  $Id: checkout_process.php,v 1.9 2003/05/22 04:56:30 ptosh Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

define('EMAIL_TEXT_SUBJECT', '����ʸ���꤬�Ȥ��������ޤ���RMT���ɥޥ͡��ۡ�');
define('EMAIL_TEXT_SUBJECT2','����ʸ���꤬�Ȥ��������ޤ���RMT���ɥޥ͡��ۡ�');

define('EMAIL_TEXT_STORE_CONFIRMATION', ' �ؤ���ʸ�򤤤������ޤ��ơ����ˤ��꤬�Ȥ��������ޤ���' . "\n\n" . 
'���������ƤˤƤ���ʸ�򾵤�ޤ����Τǡ�����ǧ����������' . "\n\n" . 
'�ʤ����ܥ᡼��˵��ܤ��줿����ʸ���Ƥθ��䡢�����������������ޤ����顢' . "\n" .
'������Ǥ�����ޤ���Ź�ޤǤ��䤤��碌���������ޤ��褦�����ꤤ�����夲�ޤ���' . "\n"); //Add Japanese osCommerce

define('EMAIL_TEXT_ORDER_NUMBER', '����ʸ�����ֹ�:');
define('EMAIL_TEXT_INVOICE_URL', '����ʸ�ˤĤ��Ƥξ���򲼵�URL�Ǥ����ˤʤ�ޤ���' . "\n");
define('EMAIL_TEXT_DATE_ORDERED', '����ʸ��:');
define('EMAIL_TEXT_PRODUCTS', '���� / ����̾');
define('EMAIL_TEXT_SUBTOTAL', '������:');
define('EMAIL_TEXT_TAX', '������:');
define('EMAIL_TEXT_SHIPPING', '������:');
define('EMAIL_TEXT_TOTAL', '��׳�:');
define('EMAIL_TEXT_DELIVERY_ADDRESS', '���Ϥ���');
define('EMAIL_TEXT_BILLING_ADDRESS', '��������');
define('EMAIL_TEXT_PAYMENT_METHOD', '����ʧ����ˡ');

define('EMAIL_SEPARATOR', '---------------------------------------------------------------------------');
define('TEXT_EMAIL_VIA', '(������ˡ)');

//Add Point System
define('TEXT_POINT_NOW', '����γ����ݥ����:');

//�߸��ڤ쥢�顼��
define('ZAIKO_ALART_TITLE','�߸ˤ��ڤ�ޤ�����');
define('ZAIKO_ALART_TITLE2','���ץ����߸ˤ��ڤ�ޤ�����');
define('ZAIKO_ARART_BODY',
'���ʺ߸ˤ��ڤ�Ƥ��ޤ����������̤˥����󤷤Ƥ�������
�߸ˤ����䤷�Ƥ��������������ʤ������Ƥ�������������
���ʤϥ���饤��夫�����ɽ���ˤʤäƤ��ޤ���

�߸ˤ����䤷��ɽ�����ơ�������ON�ˤ���Ⱥ��١�ɽ������
�ޤ����ʲ��Υꥹ�ȤϺ߸ˤ�����ξ��ʤǤ���
'.EMAIL_SEPARATOR."\n");

define('TEXT_BANK_NAME', '��ͻ����̾��������');
define('TEXT_BANK_SHITEN', '��Ź̾������������');
define('TEXT_BANK_KAMOKU', '���¼��̡���������');
define('TEXT_BANK_KOUZA_NUM', '�����ֹ桡��������');
define('TEXT_BANK_KOUZA_NAME', '����̾������������');


?>
