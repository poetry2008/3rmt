<?php
/*
  $Id: password_forgotten.php,v 1.7 2003/05/22 10:55:46 ptosh Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

define('NAVBAR_TITLE_1', '������');
define('NAVBAR_TITLE_2', '�ѥ���ɺ�ȯ��');
define('HEADING_TITLE', '�ѥ���ɺ�ȯ�Լ�³��');
define('ENTRY_FORGOTTEN_EMAIL_ADDRESS', '����Ͽ�Υ᡼�륢�ɥ쥹:'); // 2003.03.06 nagata Edit Japanese osCommerce
define('TEXT_NO_EMAIL_ADDRESS_FOUND', '<font color="#ff0000"><b>�����:</b></font> �����Ϥ��줿�᡼�륢�ɥ쥹�ϸ��Ĥ���ޤ���Ǥ������⤦�������Ϥ��Ƥ���������');
define('EMAIL_PASSWORD_REMINDER_SUBJECT', STORE_NAME . '�ο������ѥ����');
define('EMAIL_PASSWORD_REMINDER_BODY',
'�������ѥ���ɤ�ȯ�԰��꤬ ' . $REMOTE_ADDR . ' ���餢��ޤ�����' . "\n\n"
. '���ʤ��� \'' . STORE_NAME . '\' �ؤο������ѥ���ɤ�' . "\n"
. '---------------------------------------------------------------------------' . "\n"
. '   %s' . "\n"
. '---------------------------------------------------------------------------' . "\n"
. '�Ȥʤ�ޤ���' . "\n\n"
. '���Υ᡼��˴ؤ��Ƥ����Ф����ʤ����ϡ�������ҤޤǤ�Ϣ����������' . "\n\n"
. EMAIL_SIGNATURE);
define('TEXT_PASSWORD_SENT', '�������ѥ���ɤ���Ͽ�Υ᡼�륢�ɥ쥹���������ޤ�����');
?>