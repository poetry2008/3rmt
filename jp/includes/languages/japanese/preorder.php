<?php
/*
  $Id: tell_a_friend.php,v 1.7 2003/05/06 12:10:03 hawk Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

define('NAVBAR_TITLE', '���ʤ�ͽ�󤹤�');
define('HEADING_TITLE', '\'%s\' ��ͽ�󤹤�');
define('HEADING_TITLE_ERROR', '���ʤ�ͽ�󤹤�');
define('ERROR_INVALID_PRODUCT', '���ʤ����Ĥ���ޤ���...');

define('FORM_TITLE_CUSTOMER_DETAILS', '�����ͤˤĤ���');
define('FORM_TITLE_FRIEND_DETAILS', 'ͽ���ʤˤĤ���');
define('FORM_TITLE_FRIEND_MESSAGE', '����˾');

define('FORM_FIELD_CUSTOMER_NAME', '��̾��:');
define('FORM_FIELD_CUSTOMER_EMAIL', '�᡼�륢�ɥ쥹:');
define('FORM_FIELD_FRIEND_NAME', '����˾�Ŀ�:');
define('FORM_FIELD_FRIEND_EMAIL', '����:');

define('TEXT_EMAIL_SUCCESSFUL_SENT', '<p><b>��ͽ���ǧ�᡼��פ�&nbsp;<span class="red">%s</span>&nbsp;�������������ޤ�����</b><br>
Ǽ���ˤĤ��ޤ��Ƥ�24���ְ���ˤ������������ޤ���<br>
<br>
�������Żҥ᡼��򤴳�ǧ������������ͽ���ǧ�᡼��פ��Ϥ��Ƥ��ʤ����ϡ����դ���λ���Ƥ���ޤ���
�᡼�륢�ɥ쥹�򤴳�ǧ�ξ塢���٤��������ߤ򤪴ꤤ�������ޤ���<br></p>
<div class="pageHeading">���</div>
<p>�᡼�뤬�Ϥ��ʤ��Ȥ��ϡ��ʲ��Τ��Ȥ�ɬ������ǧ����������<br>
<b>�����ǥ᡼��ե�����γ�ǧ��</b><br>
���ҤΥ᡼�뤬 �����ǥ᡼��ե�����פ�֥���Ȣ�פ˿���ʬ�����츫��Ȥ��Ƥ��ޤ��󤫡�<br>
<b>��᡼��ɥᥤ��μ������¤����ꤷ�Ƥ����</b><br>
iimy.co.jp�Υ᡼��ɥᥤ����������褦������򤪴ꤤ�������ޤ���<br>
<b>�㤽��Ǥ��Ϥ��ʤ��Ȥ��ϡ��᡼�륢�ɥ쥹�ѹ���</b><br>
�᡼�륢�ɥ쥹�򤴳�ǧ�ξ塢���٤��������ߤ򤪴ꤤ�������ޤ���<br>
</p>
<div class="pageHeading">ͽ������</div>
<table>
<tr><td class="main">����̾</td><td>��</td><td class="main"><b>%s</b></td></tr>
<tr><td class="main">��˾�Ŀ�</td><td>��</td><td class="main"><b>%s��</b></td></tr>
<tr><td class="main">����</td><td>��</td><td class="main"><b>%s</b></td></tr>
</table>
');

define('TEXT_EMAIL_SUBJECT', '%s��ͽ��򾵤�ޤ�����%s��');
define('TEXT_EMAIL_INTRO', '%s ��' . "\n\n"
. '�����٤ϡ�%s�����Ѥ������������ˤ��꤬�Ȥ��������ޤ���' . "\n\n"
. '���������ƤˤƤ�ͽ��򾵤�ޤ���������ǧ����������' . "\n"
. '����Ǽ���ˤĤ��ޤ��Ƥ�24���ְ���ˤ������������ޤ���' . "\n\n"
. '������������������������������������������' . "\n"
. '����̾��������������%s' . "\n"
. '���᡼�륢�ɥ쥹����%s' . "\n"
. '������������������������������������������' . "\n\n"
. '��ͽ������' . "\n"
. '	------------------------------------------' . "\n"
. '	ͽ����      ��%s' . "\n"
. '	��˾�Ŀ���������%s��' . "\n"
. '	���¡�����������%s' . "\n"
. '	------------------------------------------');
define('TEXT_EMAIL_LINK', '���ξ��ʤξܺ٤ϡ������Υ�󥯤򥯥�å����뤫����󥯤�֥饦����' . "\n"
. '���ԡ����ڡ����Ȥ��Ƥ���������' . "\n\n" . '%s' . "\n\n");
define('TEXT_EMAIL_SIGNATURE', '[��Ϣ�����䤤��碌��]������������������������' . "\n"
. '������� iimy' . "\n"
. 'support@iimy.co.jp' . "\n"
. 'http://www.iimy.co.jp/' . "\n"
. '����������������������������������������������');
?>
