<?php
/*
  $Id: checkout_confirmation.php,v 1.7 2003/05/22 04:56:30 ptosh Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

define('NAVBAR_TITLE_1', '�쥸');
define('NAVBAR_TITLE_2', '�ǽ���ǧ');

define('HEADING_TITLE', '����ʸ���Ƥ��ǧ���Ƥ�������&nbsp;&nbsp;�ʤ���ʸ�Ϥޤ����ꤷ�Ƥ���ޤ��󡪡�');

define('HEADING_DELIVERY_ADDRESS', '���Ϥ���');
define('HEADING_SHIPPING_METHOD', '������ˡ');
define('HEADING_PRODUCTS', '���� / ����̾');
define('HEADING_TAX', '������');
define('HEADING_TOTAL', '���');
define('HEADING_BILLING_INFORMATION', '������ˤĤ���');
define('HEADING_BILLING_ADDRESS', '��������');
define('HEADING_PAYMENT_METHOD', '����ʧ����ˡ');
define('HEADING_PAYMENT_INFORMATION', '����ʧ���ˤĤ���');
define('HEADING_ORDER_COMMENTS', '����ʸ�ˤĤ��ƤΥ�����');

define('TEXT_EDIT', '�ѹ�����');

//Add Point System
define('TEXT_POINT_NOW', '<b>���ϥݥ���Ȥ��Ĥ��ޤ���</b>&nbsp;&nbsp;����γ���ͽ��ݥ����:');

define('TEXT_TORIHIKI_TITLE', '�������&nbsp;');

define('TEXT_CARACTOR', '���Ϥ��襭��饯����̾:');
define('TEXT_TORIHIKIHOUHOU', '���ץ����:');
define('TEXT_TORIHIKIKIBOUBI', '�����˾��:');
define('TEXT_TORIHIKIKIBOUJIKAN', '�����˾����:');

define('TABLE_HEADING_BANK', '��������¾���');
define('TEXT_BANK_NAME', '��ͻ����̾:');
define('TEXT_BANK_SHITEN', '��Ź̾:');
define('TEXT_BANK_KAMOKU', '���¼���:');
define('TEXT_BANK_KOUZA_NUM', '�����ֹ�:');
define('TEXT_BANK_KOUZA_NAME', '����̾��:');

define('TEXT_BANK_SELECT_KAMOKU_F', '����');
define('TEXT_BANK_SELECT_KAMOKU_T', '����');

define('TEXT_BANK_ERROR_NAME', '��'.mb_substr(TEXT_BANK_NAME,0,(mb_strlen(TEXT_BANK_NAME)-1)).'�ۤ����Ϥ���Ƥ��ޤ���');
define('TEXT_BANK_ERROR_SHITEN', '��'.mb_substr(TEXT_BANK_SHITEN,0,(mb_strlen(TEXT_BANK_SHITEN)-1)).'�ۤ����Ϥ���Ƥ��ޤ���');
define('TEXT_BANK_ERROR_KOUZA_NUM', '��'.mb_substr(TEXT_BANK_KOUZA_NUM,0,(mb_strlen(TEXT_BANK_KOUZA_NUM)-1)).'�ۤ����Ϥ���Ƥ��ޤ���');
define('TEXT_BANK_ERROR_KOUZA_NUM2', '��'.mb_substr(TEXT_BANK_KOUZA_NUM,0,(mb_strlen(TEXT_BANK_KOUZA_NUM)-1)).'�ۤ�Ⱦ�Ѥ����Ϥ��Ƥ���������');
define('TEXT_BANK_ERROR_KOUZA_NAME', '��'.mb_substr(TEXT_BANK_KOUZA_NAME,0,(mb_strlen(TEXT_BANK_KOUZA_NAME)-1)).'�ۤ����Ϥ���Ƥ��ޤ���');
?>