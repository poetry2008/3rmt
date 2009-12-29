<?php
/*
  $Id: banner_manager.php,v 1.7 2003/05/06 12:10:00 hawk Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

define('HEADING_TITLE', '�Хʡ�����');

define('TABLE_HEADING_BANNERS', '�Хʡ�');
define('TABLE_HEADING_GROUPS', '���롼��');
define('TABLE_HEADING_STATISTICS', 'ɽ�� / ����å�');
define('TABLE_HEADING_STATUS', '���ơ�����');
define('TABLE_HEADING_ACTION', '���');

define('TEXT_BANNERS_TITLE', '�Хʡ��������ȥ�:');
define('TEXT_BANNERS_URL', '�Хʡ� URL:');
define('TEXT_BANNERS_GROUP', '�Хʡ������롼��:');
define('TEXT_BANNERS_NEW_GROUP', ' �ޤ��ϲ��˿������Хʡ������롼�פ���Ͽ');
define('TEXT_BANNERS_IMAGE', '�����ե�����:');
define('TEXT_BANNERS_IMAGE_LOCAL', ' �ޤ��ϲ��˥����о�β����ե�����̾������');
define('TEXT_BANNERS_IMAGE_TARGET', '��������¸��:');
define('TEXT_BANNERS_HTML_TEXT', 'HTML �ƥ�����:');
define('TEXT_BANNERS_EXPIRES_ON', '��λ��:');
define('TEXT_BANNERS_OR_AT', ' �ޤ���');
define('TEXT_BANNERS_IMPRESSIONS', 'ɽ�����');
define('TEXT_BANNERS_SCHEDULED_AT', '������:');
define('TEXT_BANNERS_BANNER_NOTE', '<b>�Хʡ��ˤĤ���:</b><ul><li>�Хʡ��ˤϡ�������HTML�ƥ����ȤΤ����줫����Ѥ��ޤ���ξ���ϻ��ѤǤ��ޤ���</li><li>HTML�ƥ����Ȥ���������ͥ�褵��ޤ���</li></ul>');
define('TEXT_BANNERS_INSERT_NOTE', '<b>�����ˤĤ���:</b><ul><li>�Хʡ�������¸��ǥ��쥯�ȥ�ˤϡ�Ŭ�ڤʽ񤭹��߸��¤�Ϳ���Ƥ���������</li><li>�����֥����Ф˥Хʡ������򥢥åץ��ɤ��ʤ����ϡ�&quot;��������¸��&quot; ������Ϥ��ʤ��Ǥ������� ��(���ξ��ϡ�������¦�β�������Ѥ��뤳�Ȥˤʤ�ޤ�)</li><li>&quot;��������¸��&quot; ����ꤹ����ϡ�¸�ߤ���ǥ��쥯�ȥꡢ�ޤ��ϥǥ��쥯�ȥ����˺������Ƥ���ɬ�פ�����ޤ����ޤ����ǥ��쥯�ȥ�������˥���å��夬ɬ�פȤʤ�ޤ���(��: banners/)</li></ul>');
define('TEXT_BANNERS_EXPIRCY_NOTE', '<b>��λ���ˤĤ���:</b><ul><li>��λ����ɽ������Σ��Ĥ�������Τ������ҤȤĤ�������Ͽ����ޤ���</li><li>�Хʡ���ưŪ�˽�λ�����ʤ����ϡ�������������Τޤޤˤ��Ƥ���������</li></ul>');
define('TEXT_BANNERS_SCHEDULE_NOTE', '<b>�������ˤĤ���:</b><ul><li>����������Ͽ�����ȡ��Хʡ�����Ͽ���줿���դ���ͭ���ˤʤ�ޤ���</li><li>����������Ͽ���줿�Хʡ��ϡ������������ޤ�ɽ������ޤ���</li></ul>');

define('TEXT_BANNERS_DATE_ADDED', '��Ͽ��:');
define('TEXT_BANNERS_SCHEDULED_AT_DATE', '������: <b>%s</b>');
define('TEXT_BANNERS_EXPIRES_AT_DATE', '��λ��: <b>%s</b>');
define('TEXT_BANNERS_EXPIRES_AT_IMPRESSIONS', '��λ��: <b>%s</b> ����å����');
define('TEXT_BANNERS_STATUS_CHANGE', '���ơ������ѹ�: %s');

define('TEXT_BANNERS_DATA', '��<br>��');
define('TEXT_BANNERS_LAST_3_DAYS', '�Ƕᣳ����');
define('TEXT_BANNERS_BANNER_VIEWS', '�Хʡ�ɽ��');
define('TEXT_BANNERS_BANNER_CLICKS', '�Хʡ�������å�');

define('TEXT_INFO_DELETE_INTRO', '�����ˤ��ΥХʡ��������ޤ�����');
define('TEXT_INFO_DELETE_IMAGE', '�Хʡ���������');

define('SUCCESS_BANNER_INSERTED', "����: �Хʡ�����������ޤ�����");
define('SUCCESS_BANNER_UPDATED', "����: �Хʡ�����������ޤ�����");
define('SUCCESS_BANNER_REMOVED', "����: �Хʡ����������ޤ�����");
define('SUCCESS_BANNER_STATUS_UPDATED', "����: �Хʡ��Υ��ơ���������������ޤ�����");

define('ERROR_BANNER_TITLE_REQUIRED', "���顼: �Хʡ��Υ����ȥ뤬ɬ�פǤ���");
define('ERROR_BANNER_GROUP_REQUIRED', "���顼: �Хʡ��Υ��롼�פ�ɬ�פǤ���");
define('ERROR_IMAGE_DIRECTORY_DOES_NOT_EXIST', "���顼: ��¸��ǥ��쥯�ȥ꤬¸�ߤ��ޤ���");
define('ERROR_IMAGE_DIRECTORY_NOT_WRITEABLE', "���顼: ��¸��ǥ��쥯�ȥ�˽񤭹��ߤ��Ǥ��ޤ���: %s");
define('ERROR_IMAGE_DOES_NOT_EXIST', "���顼: ������¸�ߤ��ޤ���");
define('ERROR_IMAGE_IS_NOT_WRITEABLE', "���顼: ����������Ǥ��ޤ���");
define('ERROR_UNKNOWN_STATUS_FLAG', "���顼: �����ʥ��ơ������Ǥ���");

define('ERROR_GRAPHS_DIRECTORY_DOES_NOT_EXIST', "���顼:  'graphs' �ǥ��쥯�ȥ꤬¸�ߤ��ޤ��� 'images' �ǥ��쥯�ȥ����'graphs'�ǥ��쥯�ȥ��������Ƥ���������");
define('ERROR_GRAPHS_DIRECTORY_NOT_WRITEABLE', "���顼:  'images/graphs' �ǥ��쥯�ȥ�˽񤭹��ߤ��Ǥ��ޤ����������桼�����¤����ꤷ�Ƥ���������");
?>