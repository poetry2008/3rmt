<?php
/*
  $Id: default.php,v 1.7 2003/05/06 12:10:02 hawk Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

define('TEXT_MAIN', '���Υڡ����ϡ�����饤�󥷥�åפΥǥ�󥹥ȥ졼�����Ǥ���<b>�������ˤʤä����ʤϡ�����������⤵��ޤ���</b>���ʤ���¾�ˤĤ��Ƥξ�������ƲͶ��Τ�ΤǤ���<br><br>�⤷�����ʤ������Υ���饤�󥷥�åס��ǥ���������ɤ��ƻȤäƤߤ����Ȼפä��ꡢ���Υץ������Ȥ˹׸����褦�Ȼפä��ʤ顢����<a href="http://oscommerce.com"><u> ���ݡ��ȥ����� </u></a>��ˬ��Ƥ������������Υ���饤�󥷥�åפϡ�<font color="#f0000"><b>' . PROJECT_VERSION . '</b></font>�ǹ��ۤ���Ƥ��ޤ���<br><br>��ɽ������Ƥ�����ɤ�Ǥ���˥ƥ����Ȥϡ����Υե�����˵��Ҥ���Ƥ��ޤ�����:[catalog�ǥ��쥯�ȥ�]/includes/languages/[japanese]/default.php.<br><br>����ϡ�������˥塼��ͳ���Ƽ�ġ���-�������->[language]->Define���ץ���󡢤ޤ��ϳƼ�ġ���->�ե����������ǽ��Ȥ����Ȥˤ�äƼ�ư���Խ����뤳�Ȥ��Ǥ��ޤ���');
define('TABLE_HEADING_NEW_PRODUCTS', '%s �ο��徦��');
define('TABLE_HEADING_UPCOMING_PRODUCTS', '����ͽ��ξ���');
define('TABLE_HEADING_DATE_EXPECTED', '����ͽ����');

define('HEADING_COLOR_TITLE', '���顼��������: ');

if ( ($category_depth == 'products') || ($HTTP_GET_VARS['manufacturers_id']) ||  ($HTTP_GET_VARS['colors'])) {
  define('HEADING_TITLE', '�谷������');
  define('TABLE_HEADING_IMAGE', '');
  define('TABLE_HEADING_MODEL', '����');
  define('TABLE_HEADING_PRODUCTS', '����̾');
  define('TABLE_HEADING_MANUFACTURER', '�᡼����');
  define('TABLE_HEADING_QUANTITY', '����');
  define('TABLE_HEADING_PRICE', '����');
  define('TABLE_HEADING_WEIGHT', '����');
  define('TABLE_HEADING_BUY_NOW', '����������');
  define('TEXT_NO_PRODUCTS', '���Υ��ƥ��꡼�ξ��ʤϤ���ޤ���...');
  define('TEXT_NO_PRODUCTS2', '���Υ᡼�����ξ��ʤϤ���ޤ���...');
  define('TEXT_NUMBER_OF_PRODUCTS', '�߸˿�: ');
  define('TEXT_SHOW', '<b>�ʹ���:</b> ');
  define('TEXT_BUY', '������ ');
  define('TEXT_NOW', ' ���������');	
  define('TEXT_ALL', '����');
  define('TEXT_NO_COLORS', '���Υ��顼�ξ��ʤϤ���ޤ���...');
} elseif ($category_depth == 'top') {
  define('HEADING_TITLE', 'What\'s New!');
} elseif ($category_depth == 'nested') {
  define('HEADING_TITLE', '���ƥ��꡼');
}
?>
