<?php
/*
  $Id: backup.php,v 1.5 2003/05/06 12:10:00 hawk Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

define('HEADING_TITLE', '�ǡ����١������Хå����å״���');

define('TABLE_HEADING_TITLE', '�����ȥ�');
define('TABLE_HEADING_FILE_DATE', '����');
define('TABLE_HEADING_FILE_SIZE', '������');
define('TABLE_HEADING_ACTION', 'ư��');

define('TEXT_INFO_HEADING_NEW_BACKUP', '�������Хå����å�');
define('TEXT_INFO_HEADING_RESTORE_LOCAL', '������ե����뤫������');
define('TEXT_INFO_NEW_BACKUP', '�Хå����å׽�������������Ǥ��ʤ��Ǥ��������������˿�ʬ�������礬����ޤ���');
define('TEXT_INFO_UNPACK', '<br><br>(���̥ե�����β����˼¹�)');
define('TEXT_INFO_RESTORE', '������������������Ǥ��ʤ��Ǥ���������<br><br>�ե����륵�������礭���ȡ����������˻��֤�������ޤ�!<br><br>�� MySQL���饤����Ȥε�ǽ����Ѥ��������������侩���ޤ���<br><br>���ޥ����:<br><b>mysql -h ' . DB_SERVER . ' -u ' . DB_SERVER_USERNAME . ' -p ' . DB_DATABASE . ' < %s </b> %s');
define('TEXT_INFO_RESTORE_LOCAL', '������������������Ǥ��ʤ��Ǥ���������<br><br>�ե����륵�������礭���ȡ����������˻��֤�������ޤ�!');
define('TEXT_INFO_RESTORE_LOCAL_RAW_FILE', '���åץ��ɽ����ե�����ϡ�����SQL�ƥ����ȥե�����ΤߤǤ���');
define('TEXT_INFO_DATE', '����:');
define('TEXT_INFO_SIZE', '������:');
define('TEXT_INFO_COMPRESSION', '������ˡ:');
define('TEXT_INFO_USE_GZIP', 'GZIP���̤���¸');
define('TEXT_INFO_USE_ZIP', 'ZIP���̤���¸');
define('TEXT_INFO_USE_NO_COMPRESSION', '�󰵽���¸ (����SQL�ե�����)');
define('TEXT_INFO_DOWNLOAD_ONLY', '�����������¸ (������¦�ˤϻĤ��ޤ���)');
define('TEXT_INFO_BEST_THROUGH_HTTPS', 'HTTPS���ͥ�������ͳ��侩');
define('TEXT_DELETE_INTRO', '�����ˤ��ΥХå����åץե�����������ޤ���?');
define('TEXT_NO_EXTENSION', '�ʤ�');
define('TEXT_BACKUP_DIRECTORY', '�Хå����åס��ǥ��쥯�ȥ�:');
define('TEXT_LAST_RESTORATION', '�Ǹ������:');
define('TEXT_FORGET', '(<u>˺��Ƥ��ޤä�</u>)');

define('ERROR_BACKUP_DIRECTORY_DOES_NOT_EXIST', '���顼: �Хå����åס��ǥ��쥯�ȥ꤬¸�ߤ��ޤ���includes/configure.php��������ǧ���Ƥ���������');
define('ERROR_BACKUP_DIRECTORY_NOT_WRITEABLE', '���顼: �Хå����åס��ǥ��쥯�ȥ�˽񤭹��ߤǤ��ޤ���');
define('ERROR_DOWNLOAD_LINK_NOT_ACCEPTABLE', '���顼: ��������ɤ�������Ƥ��ޤ���');
define('ERROR_FILE_NOT_REMOVEABLE', '���顼: �Хå����åץե�����κ�����Ǥ��ޤ���Ǥ������ե�����Υ桼�����¤��ǧ���Ƥ���������');	//Add Japanese osCommerce

define('SUCCESS_LAST_RESTORE_CLEARED', '����: �ǿ��������ǡ����Ͼõ��ޤ�����');
define('SUCCESS_DATABASE_SAVED', '����: �ǡ����١�������¸����ޤ�����');
define('SUCCESS_DATABASE_RESTORED', '����: �ǡ����١�������������ޤ�����');
define('SUCCESS_BACKUP_DELETED', '����: �Хå����åס��ե����뤬�������ޤ�����');
?>