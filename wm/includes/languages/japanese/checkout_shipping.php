<?php
/*
  $Id: checkout_shipping.php,v 1.8 2003/05/22 04:56:30 ptosh Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

define('NAVBAR_TITLE_1', '�쥸');
define('NAVBAR_TITLE_2', '��������λ���');

define('HEADING_TITLE', '��������λ���');

define('TABLE_HEADING_SHIPPING_ADDRESS', '����˾�μ����������ꤷ�Ƥ�������');
define('TEXT_CHOOSE_SHIPPING_DESTINATION', '���Ϥ���Τ�����򤴳�ǧ����������<br>�ʲ��Υܥ���򥯥�å����ơ����Ϥ�����ѹ����뤳�Ȥ�Ǥ��ޤ�����');
define('TITLE_SHIPPING_ADDRESS', '���Ϥ���:');

define('TABLE_HEADING_SHIPPING_METHOD', '������ˡ');
define('TEXT_CHOOSE_SHIPPING_METHOD', '������ˡ������Ǥ���������');
define('TITLE_PLEASE_SELECT', '����Ǥ�������');
define('TEXT_ENTER_SHIPPING_INFORMATION', '������������ˡ�Ǿ��ʤ��Ϥ����ޤ���');

define('TABLE_HEADING_COMMENTS', '����ʸ�ˤĤ��ƤΥ�����');

define('TITLE_CONTINUE_CHECKOUT_PROCEDURE', '�������μ�³����ʤ�Ƥ���������');
define('TEXT_CONTINUE_CHECKOUT_PROCEDURE', '����ʧ��ˡ�������');

# Add ds-style
define('TEXT_CARACTOR', '���Ϥ��襭��饯����̾:');
define('TEXT_TORIHIKIHOUHOU', '���ץ����:');
define('TEXT_TORIHIKIKIBOUBI', '�����˾��:');
define('TEXT_TORIHIKIKIBOUJIKAN', '�����˾����:');

define('TEXT_CHECK_EIJI', '(�ѻ�)');
define('TEXT_CHECK_24JI', '<b>(24����ɽ��)</b>');
define('TEXT_PRESE_SELECT', '���򤷤Ƥ�������');

define('TEXT_ERROR_BAHAMUTO', '<span class="errorText">��'.mb_substr(TEXT_CARACTOR,0,(mb_strlen(TEXT_CARACTOR)-1)).'�ۤ����Ϥ���Ƥ��ޤ���</span>');
define('TEXT_ERROR_BAHAMUTO_EIJI', '<span class="errorText">��'.mb_substr(TEXT_CARACTOR,0,(mb_strlen(TEXT_CARACTOR)-1)).'�ۤǻ��ѤǤ���ʸ����Ⱦ�ѱѻ��ΤߤǤ�</span>');
define('TEXT_ERROR_TORIHIKIHOUHOU', '<span class="errorText">��'.mb_substr(TEXT_TORIHIKIHOUHOU,0,(mb_strlen(TEXT_TORIHIKIHOUHOU)-1)).'�ۤ����򤷤Ƥ���������</span>');
define('TEXT_ERROR_DATE', '<span class="errorText">��'.mb_substr(TEXT_TORIHIKIKIBOUBI,0,(mb_strlen(TEXT_TORIHIKIKIBOUBI)-1)).'�ۤ����򤷤Ƥ���������</span>');
define('TEXT_ERROR_JIKAN', '<span class="errorText">��'.mb_substr(TEXT_TORIHIKIKIBOUJIKAN,0,(mb_strlen(TEXT_TORIHIKIKIBOUJIKAN)-1)).'�ۤ����򤷤Ƥ���������</span>');
?>