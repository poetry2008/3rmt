<?php
/*
  $Id: checkout_success.php,v 1.11 2003/05/22 10:55:46 ptosh Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

define('NAVBAR_TITLE_1', '�쥸');
define('NAVBAR_TITLE_2', '��³��λ');

define('HEADING_TITLE', '����ʸ�μ�³������λ���ޤ���!!');

define('TEXT_SUCCESS', '<span class="red"><b>����ʸ���աץ᡼������ꤷ�ޤ����ΤǤ���ǧ����������</b></span><br>
<b>10ʬ�вᤷ�Ƥ�᡼�뤬�Ϥ��ʤ����ϡ������������ޤ��ΤǤ�Ϣ����������</b><br>
��ա��᡼�뤬�Ϥ��ʤ��Ȥ��ϡ��ʲ��Τ��Ȥ�ɬ������ǧ����������<br>
�����ǥ᡼��ե�����γ�ǧ��<br>
���ҤΥ᡼�뤬 �����ǥ᡼��ե�����פ�֥���Ȣ�פ˿���ʬ�����츫��Ȥ��Ƥ��ޤ��󤫡�<br>
��᡼��ɥᥤ��μ������¤����ꤷ�Ƥ����<br>
iimy.co.jp�Υ᡼��ɥᥤ����������褦������򤪴ꤤ�������ޤ���<br>
�㤽��Ǥ��Ϥ��ʤ��Ȥ��ϡ��᡼�륢�ɥ쥹�ѹ���<br>
�����;��󤫤麣��������Ͽ�᡼�륢�ɥ쥹���ѹ��򤪴ꤤ�������ޤ���<br>
<div class="underline">&nbsp;</div>
<span class="red"><b>���������κݤ������</b></span><br>
�Żҥ᡼��˵��ܤ��Ƥ���ޤ�����ʸ���Ƥ���ӡ����ҥ���饯����̾������ˤ���ǧ����������<br>
���ҥ���饯�������ѹ��Ȥʤ���ϡ��������������Żҥ᡼��ˤƤ�����򺹤��夲�ޤ���
<div class="underline">&nbsp;</div>
<b>������Τ����ͤ�</b><br>
�Ƕᡢ��͡�����2�ˤ����ƺ����԰٤�Ԥ�����饯������¸�ߤ�¿����𤵤�Ƥ���ޤ���������¿���Υ���饯�����إȥ졼�ɤ򿽤����ߡ��������̲ߤ䥢���ƥ�������˼����������Ȥʤ�ޤ���
�ȥ졼�ɤκݤϡ�����饯����̾��ʬ����ǧ���������ޤ��褦���ꤤ�����夲�ޤ������ҥ���饯�����ʳ��إȥ졼�ɤ��줿��硢���ҤǤϰ��ڤ��ݾ�򤤤������ͤޤ���
<div class="underline">&nbsp;</div>
<img src="images/stock.gif" alt="���䥢������" width="50" height="50"><b>������׾��ʤˤĤ���</b><br>
�����ͤ�����μ�������˥�����򤪴ꤤ�������ޤ���10ʬ�вᤷ�ޤ��Ƥ����ҥ���饯����������ʤ����ϡ����ݡ��ȥ��󥿡��ؤ��䤤��碌����������<br>
<div class="dot">&nbsp;</div>
<img src="images/preorder.gif" alt="���󤻥�������" width="50" height="50"><b>�ּ��󤻡׾��ʤˤĤ���</b><br>
����ʸ�������������ʤϡ��̾�����Ķ����Ǥ��Ϥ����Ƥ���ޤ���<br>
<div class="dot">&nbsp;</div>
<img src="images/sell.gif" alt="��襢������" width="50" height="50"><b>�����׾��ʤˤĤ���</b><br>
�����ͤ�����μ�������˥�����򤪴ꤤ�������ޤ���10ʬ�вᤷ�ޤ��Ƥ����ҥ���饯����������ʤ����ϡ����ݡ��ȥ��󥿡��ؤ��䤤��碌����������<br>
<div class="dot">&nbsp;</div>');
define('TEXT_NOTIFY_PRODUCTS', '��������ʸ�������������ʤκǿ������ 
�Żҥ᡼��Ǥ��Ϥ����Ƥ���ޤ�������˾�����ϡ����ʤ��Ȥ˥����å����� <b>[���˿ʤ�]</b> �򲡤��Ƥ���������');
define('TEXT_SEE_ORDERS', '���ʤ��Τ���ʸ����ϡ�<a href="' . tep_href_link(FILENAME_ACCOUNT, '', 'SSL') . '">\'���������\'</a> �ڡ����� <a href="' . tep_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL') . '">\'����\'</a>�ܥ���򥯥�å�����Ȥ����ˤʤ�ޤ���');
define('TEXT_CONTACT_STORE_OWNER', '�⤷����ʸ��³���ˤĤ��Ƥ����䤬�������ޤ����顢ľ��<a href="' . tep_href_link(FILENAME_CONTACT_US) . '">Ź��</a>�ޤǤ��䤤��碌����������');
define('TEXT_THANKS_FOR_SHOPPING', '����ʸ���꤬�Ȥ��������ޤ�����');

define('TABLE_HEADING_COMMENTS', '����ʸ�ˤĤ��ƤΥ�����');

define('TABLE_HEADING_DOWNLOAD_DATE', '���������ͭ������: ');
define('TABLE_HEADING_DOWNLOAD_COUNT', ' ���������ɤǤ��ޤ�');
define('HEADING_DOWNLOAD', '�����餫�龦�ʤ��������ɤ��Ƥ�������:');
define('FOOTER_DOWNLOAD', '��� [%s] �ڡ������龦�ʤ��������ɤ��뤳�Ȥ�Ǥ��ޤ���');
?>