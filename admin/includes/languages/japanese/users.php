<?php
/* *********************************************************
  �⥸�塼��̾: users.php
 * 2001/5/29
 *   modi 2002-05-10
 * Naomi Suzukawa
 * suzukawa@bitscope.co.jp
  ----------------------------------------------------------
�桼�������θ������

  ���ѹ�����
********************************************************* */
// �ڡ��������ȥ�
define('HEADING_TITLE', '�桼������');

// ���顼��å�����ɽ�������ȥ�
define('TABLE_HEADING_ERRINFO', '!!!!! ���顼��å����� !!!!!');

// ���ϥ��顼��å�����
define('TEXT_ERRINFO_INPUT_NOINPUT', '̤���ϤǤ�');
define('TEXT_ERRINFO_INPUT_ERR', '���������Ϥ���Ƥ��ޤ���');
define('TEXT_ERRINFO_INPUT_LENGTH', '%d ʸ���ʾ����Ϥ��Ƥ�������');
define('TEXT_ERRINFO_USER_DELETE', '<b>�桼������κ��</b>:�桼���ܿͤξ���Ϻ���Ǥ��ޤ���');
define('TEXT_ERRINFO_USER_GRANT', '<b>���¤�Ϳ����</b>:�桼�������򤷤Ƥ�������');
define('TEXT_ERRINFO_USER_REVOKE', '<b>���¤��ä�</b>:�桼�������򤷤Ƥ�������');
define('TEXT_ERRINFO_USER_REVOKE_ONESELF', '<b>���¤��ä�</b>:�桼���ܿͤθ��¤��ä����ȤϤǤ��ޤ���');
define('TEXT_ERRINFO_CONFIRM_PASSWORD', '<b>��ǧ�Τ��������</b>:��ǧ�Τ�������Ϥ����ѥ���ɤ��㤤�ޤ�');

// �ơ��֥륢���������顼��å�����
define('TEXT_ERRINFO_DB_NO_USERINFO', '�桼�����󤬼����Ǥ��ޤ���Ǥ���');
define('TEXT_ERRINFO_DB_NO_USER', '�оݤȤʤ�桼����¸�ߤ��ޤ���');
define('TEXT_ERRINFO_DB_USERCHACK', '�桼����¸�ߥ����å��ǥ��顼��ȯ�����ޤ���');
define('TEXT_ERRINFO_DB_EXISTING_USER', '������Ͽ����Ƥ���桼���Ǥ�');
define('TEXT_ERRINFO_DB_INSERT_USER', '�桼���������Ͽ���Ǥ��ޤ���Ǥ���');
define('TEXT_ERRINFO_DB_INSERT_PERMISSION', '�桼�����¾������Ͽ���Ǥ��ޤ���Ǥ���');
define('TEXT_ERRINFO_DB_UPDATE_USER', '�桼������ι������Ǥ��ޤ���Ǥ���');
define('TEXT_ERRINFO_DB_DELETE_USER', '�桼������κ�����Ǥ��ޤ���Ǥ���');
define('TEXT_ERRINFO_DB_CHANGE_PASSWORD', '�ѥ���ɤ��ѹ����Ǥ��ޤ���Ǥ���');
define('TEXT_ERRINFO_DB_CHANGE_USER', '�桼�����¤��ѹ����Ǥ��ޤ���Ǥ���');

// ��λ��å�����
define('TEXT_SUCCESSINFO_INSERT_USER', '�桼�����ɲä��ޤ���');
define('TEXT_SUCCESSINFO_UPDATE_USER', '�桼������򹹿����ޤ���');
define('TEXT_SUCCESSINFO_DELETE_USER', '�桼������������ޤ���');
define('TEXT_SUCCESSINFO_CHANGE_PASSWORD', '�ѥ���ɤ��ѹ����ޤ���');
define('TEXT_SUCCESSINFO_PERMISSION', '�桼�����¤�%s�ޤ���');

// �ڡ��������ȥ�
define('PAGE_TITLE_MENU_USER', '�桼��������˥塼');
define('PAGE_TITLE_INSERT_USER', '�桼�����ɲ�');
define('PAGE_TITLE_USERINFO', '�桼������');
define('PAGE_TITLE_PASSWORD', '�ѥ�����ѹ�');
define('PAGE_TITLE_PERMISSION', '�����Ը���');

// �ܥ���
define('BUTTON_BACK_MENU', '�桼��������˥塼�����');
define('BUTTON_INSERT_USER', '�桼�����ɲ�');
define('BUTTON_INFO_USER', '�桼������');
define('BUTTON_CHANGE_PASSWORD', '�ѥ�����ѹ�');
define('BUTTON_PERMISSION', '�����Ը���');
define('BUTTON_INSERT', '�ɲ�');
define('BUTTON_CLEAR', '���ꥢ');
define('BUTTON_UPDATE', '����');
define('BUTTON_DELETE', '���');
define('BUTTON_RESET', '�����ͤ��᤹');
define('BUTTON_CHANGE', '�ѹ�');
define('BUTTON_GRANT', '���¤�Ϳ���� >>');
define('BUTTON_REVOKE', '<< ���¤��ä�');
define('BUTTON_BACK_PERMISSION', '�����Ը��¤����');

// ����̾
define('TABLE_HEADING_COLUMN', '�����');
define('TABLE_HEADING_DATA', '�ǡ���');
define('TABLE_HEADING_USER', '�桼��');
define('TABLE_HEADING_USER_LIST', '�桼������');
define('TABLE_HEADING_USER_ID', '�桼��ID');
define('TABLE_HEADING_PASSWORD', '�ѥ����');
define('TABLE_HEADING_NAME', '��̾');
define('TABLE_HEADING_EMAIL', 'E-Mail');
define('TABLE_HEADING_NEW_PASSWORD', '�������ѥ����');
define('TABLE_HEADING_CONFIRM_PASSWORD', '��ǧ�Τ��������');
define('TABLE_HEADING_USER', '���̥桼��');
define('TABLE_HEADING_ADMIN', '�����ȴ�����');

// JavaScript�γ�ǧ��å�����
define('JAVA_SCRIPT_INFO_CHANGE', '�桼������������ѹ����ޤ���\n������Ǥ�����');
define('JAVA_SCRIPT_INFO_DELETE', '�桼����������������ޤ���\n������Ǥ�����');
define('JAVA_SCRIPT_INFO_PASSWORD', '�ѥ���ɤ��ѹ����ޤ���\n������Ǥ�����');
define('JAVA_SCRIPT_INFO_GRANT', '�����Ը��¤�Ϳ���ޤ���\n������Ǥ�����');
define('JAVA_SCRIPT_INFO_REVOKE', '�����Ը��¤���ä��ޤ���\n������Ǥ�����');
?>