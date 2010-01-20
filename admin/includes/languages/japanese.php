<?php
/*
  $Id: japanese.php,v 1.14 2003/07/22 00:45:16 ptosh Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

//
// mb_internal_encoding() is set for PHP-4.3.x(Zend Multibyte)
//
// A compatible module is loaded for environment without mbstring-extension
//
if (extension_loaded('mbstring')) {
  mb_internal_encoding('EUC-JP'); // ���������ɤ����
} else {
  include_once(DIR_WS_LANGUAGES . $language . '/jcode.phps');
  include_once(DIR_WS_LANGUAGES . $language . '/mbstring_wrapper.php');
}

// look in your $PATH_LOCALE/locale directory for available locales..
// on RedHat6.0 I used 'en_US'
// on FreeBSD 4.0 I use 'en_US.ISO_8859-1'
// this may not work under win32 environments..
setlocale(LC_TIME, 'ja_JP');
define('DATE_FORMAT_SHORT', '%Y/%m/%d');  // this is used for strftime()
define('DATE_FORMAT_LONG', '%Yǯ%B%e�� %A'); // this is used for strftime()
define('DATE_FORMAT', 'Y/m/d'); // this is used for date()
define('PHP_DATE_TIME_FORMAT', 'Y/m/d H:i:s'); // this is used for date()
define('DATE_TIME_FORMAT', DATE_FORMAT_SHORT . ' %H:%M:%S');

////
// Return date in raw format
// $date should be in format mm/dd/yyyy
// raw date is in format YYYYMMDD, or DDMMYYYY
function tep_date_raw($date, $reverse = false) {
  if ($reverse) {
    return substr($date, 8, 2) . substr($date, 5, 2) . substr($date, 0, 4);
  } else {
    return substr($date, 0, 4) . substr($date, 5, 2) . substr($date, 8, 2);
  }
}

// Global entries for the <html> tag
define('HTML_PARAMS','dir="ltr" lang="ja"');

// charset for web pages and emails
define('CHARSET', 'EUC-JP');    // Shift_JIS / euc-jp / iso-2022-jp

// page title
define('TITLE', STORE_NAME);  //����å�̾�򵭽Ҥ��Ƥ���������

// header text in includes/header.php
define('HEADER_TITLE_TOP', '������˥塼');
define('HEADER_TITLE_SUPPORT_SITE', '���ݡ��ȥ�����');
define('HEADER_TITLE_ONLINE_CATALOG', '����饤�󥫥���');
define('HEADER_TITLE_ADMINISTRATION', '������˥塼');

// text for gender
define('MALE', '����');
define('FEMALE', '����');

// text for date of birth example
define('DOB_FORMAT_STRING', 'yyyy-mm-dd');

// configuration box text in includes/boxes/configuration.php
define('BOX_HEADING_CONFIGURATION', '��������');
define('BOX_CONFIGURATION_MYSTORE', '����å�');
define('BOX_CONFIGURATION_LOGGING', '��');
define('BOX_CONFIGURATION_CACHE', '����å���');

// modules box text in includes/boxes/modules.php
define('BOX_HEADING_MODULES', '�⥸�塼������');
define('BOX_MODULES_PAYMENT', '��ʧ�⥸�塼��');
define('BOX_MODULES_SHIPPING', '�����⥸�塼��');
define('BOX_MODULES_ORDER_TOTAL', '��ץ⥸�塼��');
define('BOX_MODULES_METASEO', 'Meta SEO');

// categories box text in includes/boxes/catalog.php
define('BOX_HEADING_CATALOG', '����������');
define('BOX_CATALOG_CATEGORIES_PRODUCTS', '���ƥ��꡼/������Ͽ');
define('BOX_CATALOG_CATEGORIES_PRODUCTS_ATTRIBUTES', '���ʥ��ץ������Ͽ');
define('BOX_CATALOG_MANUFACTURERS', '�᡼������Ͽ');
define('BOX_CATALOG_REVIEWS', '��ӥ塼����');
define('BOX_CATALOG_SPECIALS', '�ò�������Ͽ');
define('BOX_CATALOG_PRODUCTS_EXPECTED', '����ͽ�꾦�ʴ���');

// customers box text in includes/boxes/customers.php
define('BOX_HEADING_CUSTOMERS', '�ܵҴ���');
define('BOX_CUSTOMERS_CUSTOMERS', '�ܵҴ���');
define('BOX_CUSTOMERS_ORDERS', '��ʸ����');

// taxes box text in includes/boxes/taxes.php
define('BOX_HEADING_LOCATION_AND_TAXES', '�ϰ� / ��Ψ����');
define('BOX_TAXES_COUNTRIES', '��̾����');
define('BOX_TAXES_ZONES', '�ϰ�����');
define('BOX_TAXES_GEO_ZONES', '�ϰ�������');
define('BOX_TAXES_TAX_CLASSES', '�Ǽ�������');
define('BOX_TAXES_TAX_RATES', '��Ψ����');

// reports box text in includes/boxes/reports.php
define('BOX_HEADING_REPORTS', '��ݡ���');
define('BOX_REPORTS_PRODUCTS_VIEWED', '�����̤α������');
define('BOX_REPORTS_PRODUCTS_PURCHASED', '�����̤������');
define('BOX_REPORTS_ORDERS_TOTAL', '�ܵ��̤������');
define('BOX_REPORTS_SALES_REPORT2', '������');

// tools text in includes/boxes/tools.php
define('BOX_HEADING_TOOLS', '�Ƽ�ġ���');
define('BOX_TOOLS_BACKUP', 'DB�Хå����å״���');
define('BOX_TOOLS_BANNER_MANAGER', '�Хʡ�����');
define('BOX_TOOLS_CACHE', '����å��女��ȥ���');
define('BOX_TOOLS_DEFINE_LANGUAGE', '����ե��������');
define('BOX_TOOLS_FILE_MANAGER', '�ե��������');
define('BOX_TOOLS_MAIL', 'E-Mail ����');
define('BOX_TOOLS_NEWSLETTER_MANAGER', '�᡼��ޥ��������');
define('BOX_TOOLS_SERVER_INFO', '�����С�����');
define('BOX_TOOLS_WHOS_ONLINE', '����饤��桼��');
define('BOX_TOOLS_PRESENT','�ץ쥼��ȵ�ǽ');

// localizaion box text in includes/boxes/localization.php
define('BOX_HEADING_LOCALIZATION', '�����饤��');
define('BOX_LOCALIZATION_CURRENCIES', '�̲�����');
define('BOX_LOCALIZATION_LANGUAGES', '��������');
define('BOX_LOCALIZATION_ORDERS_STATUS', '��ʸ���ơ���������');

// javascript messages
define('JS_ERROR', '�ե�����ν�����˥��顼��ȯ�����ޤ���!\n�����ν�����ԤäƤ�������:\n\n');

define('JS_OPTIONS_VALUE_PRICE', '* ����������°���β��ʤ���ꤷ�Ƥ���������\n');
define('JS_OPTIONS_VALUE_PRICE_PREFIX', '* ����������°���β��ʤ���Ƭ������ꤷ�Ƥ���������\n');

define('JS_PRODUCTS_NAME', '* ���������ʤ�̾������ꤷ�Ƥ���������\n');
define('JS_PRODUCTS_DESCRIPTION', '* ���������ʤ�����ʸ�����Ϥ��Ƥ���������\n');
define('JS_PRODUCTS_PRICE', '* ���������ʤβ��ʤ���ꤷ�Ƥ���������\n');
define('JS_PRODUCTS_WEIGHT', '* ���������ʤν��̤���ꤷ�Ƥ���������\n');
define('JS_PRODUCTS_QUANTITY', '* ���������ʤο��̤���ꤷ�Ƥ���������\n');
define('JS_PRODUCTS_MODEL', '* ���������ʤη��֤���ꤷ�Ƥ���������\n');
define('JS_PRODUCTS_IMAGE', '* ���������ʤΥ��᡼����������ꤷ�Ƥ���������\n');

define('JS_SPECIALS_PRODUCTS_PRICE', '* ���ξ��ʤο��������ʤ���ꤷ�Ƥ���������\n');

define('JS_GENDER', '* \'����\' �����򤵤�Ƥ��ޤ���\n');
define('JS_FIRST_NAME', '* \'̾��\' �Ͼ��ʤ��Ƥ� ' . ENTRY_FIRST_NAME_MIN_LENGTH . ' ʸ���ʾ�ɬ�פǤ���\n');
define('JS_LAST_NAME', '* \'��\' �Ͼ��ʤ��Ƥ� ' . ENTRY_LAST_NAME_MIN_LENGTH . ' ʸ���ʾ�ɬ�פǤ���\n');

define('JS_FIRST_NAME_F', '* \'̾��(�եꥬ��)\' �Ͼ��ʤ��Ƥ� ' . ENTRY_FIRST_NAME_MIN_LENGTH . ' ʸ���ʾ�ɬ�פǤ���\n');
define('JS_LAST_NAME_F', '* \'��(�եꥬ��)\' �Ͼ��ʤ��Ƥ� ' . ENTRY_LAST_NAME_MIN_LENGTH . ' ʸ���ʾ�ɬ�פǤ���\n');

define('JS_DOB', '* \'��ǯ����\' �ϼ��η��������Ϥ�������: xxxx/xx/xx (ǯ/��/��)��\n');
define('JS_EMAIL_ADDRESS', '* \'E-Mail ���ɥ쥹\' �Ͼ��ʤ��Ƥ� ' . ENTRY_EMAIL_ADDRESS_MIN_LENGTH . ' ʸ���ʾ�ɬ�פǤ���\n');
define('JS_ADDRESS', '* \'���꣱\' �Ͼ��ʤ��Ƥ� ' . ENTRY_STREET_ADDRESS_MIN_LENGTH . ' ʸ���ʾ�ɬ�פǤ���\n');
define('JS_POST_CODE', '* \'͹���ֹ�\' �Ͼ��ʤ��Ƥ� ' . ENTRY_POSTCODE_MIN_LENGTH . ' ʸ���ʾ�ɬ�פǤ���\n');
define('JS_CITY', '* \'�Զ�Į¼\' �Ͼ��ʤ��Ƥ� ' . ENTRY_CITY_MIN_LENGTH . ' ʸ���ʾ�ɬ�פǤ���\n');
define('JS_STATE', '* \'��ƻ�ܸ�\' �����򤵤�Ƥ��ޤ���\n');
define('JS_STATE_SELECT', '-- �夫������ --');
define('JS_ZONE', '* \'��ƻ�ܸ�\' ��ꥹ�Ȥ������򤷤Ƥ���������');
define('JS_COUNTRY', '* \'��\' �����򤷤Ƥ���������\n');
define('JS_TELEPHONE', '* \'�����ֹ�\' �Ͼ��ʤ��Ƥ� ' . ENTRY_TELEPHONE_MIN_LENGTH . ' ʸ���ʾ�ɬ�פǤ���\n');
define('JS_PASSWORD', '* \'�ѥ����\' �� \'�ѥ���ɤ������\' �Ͼ��ʤ��Ȥ� ' . ENTRY_PASSWORD_MIN_LENGTH . ' ʸ���ʾ�ɬ�פǤ���\n');

define('JS_ORDER_DOES_NOT_EXIST', '��ʸ�ֹ� %s ��¸�ߤ��ޤ���!');

define('CATEGORY_PERSONAL', '�Ŀ;���');
define('CATEGORY_ADDRESS', '������');
define('CATEGORY_CONTACT', '��Ϣ����');
define('CATEGORY_COMPANY', '���̾');
define('CATEGORY_PASSWORD', '�ѥ����');
define('CATEGORY_OPTIONS', '���ץ����');
define('ENTRY_GENDER', '����:');
define('ENTRY_FIRST_NAME', '̾:');
define('ENTRY_LAST_NAME', '��:');
//add
define('ENTRY_FIRST_NAME_F', '̾(�եꥬ��):');
define('ENTRY_LAST_NAME_F', '��(�եꥬ��):');
define('ENTRY_DATE_OF_BIRTH', '��ǯ����:');
define('ENTRY_EMAIL_ADDRESS', 'E-Mail ���ɥ쥹:');
define('ENTRY_COMPANY', '���̾:');
define('ENTRY_STREET_ADDRESS', '����1:');
define('ENTRY_SUBURB', '����2:');
define('ENTRY_POST_CODE', '͹���ֹ�:');
define('ENTRY_CITY', '�Զ�Į¼:');
define('ENTRY_STATE', '��ƻ�ܸ�:');
define('ENTRY_COUNTRY', '��̾:');
define('ENTRY_TELEPHONE_NUMBER', '�����ֹ�:');
define('ENTRY_FAX_NUMBER', '�ե��å����ֹ�:');
define('ENTRY_NEWSLETTER', '�᡼��ޥ�����:');
define('ENTRY_NEWSLETTER_YES', '���ɤ���');
define('ENTRY_NEWSLETTER_NO', '���ɤ��ʤ�');
define('ENTRY_PASSWORD', '�ѥ����:');
define('ENTRY_PASSWORD_CONFIRMATION', '�ѥ���ɺ�����:');
define('PASSWORD_HIDDEN', '********');

// images
define('IMAGE_ANI_SEND_EMAIL', 'E-Mail����');
define('IMAGE_BACK', '���');
define('IMAGE_BACKUP', '�Хå����å�');
define('IMAGE_CANCEL', '���ä�');
define('IMAGE_CONFIRM', '��ǧ');
define('IMAGE_COPY', '���ԡ�');
define('IMAGE_COPY_TO', '���ԡ���');
define('IMAGE_DEFINE', '���');
define('IMAGE_DELETE', '���');
define('IMAGE_EDIT', '�Խ�');
define('IMAGE_EMAIL', 'E-Mail');
define('IMAGE_FILE_MANAGER', '�ե��������');
define('IMAGE_ICON_STATUS_GREEN', 'ͭ��');
define('IMAGE_ICON_STATUS_GREEN_LIGHT', 'ͭ���ˤ���');
define('IMAGE_ICON_STATUS_RED', '̵��');
define('IMAGE_ICON_STATUS_RED_LIGHT', '̵���ˤ���');
define('IMAGE_ICON_INFO', '����');
define('IMAGE_INSERT', '����');
define('IMAGE_LOCK', '��å�');
define('IMAGE_MOVE', '��ư');
define('IMAGE_NEW_BANNER', '�������Хʡ�');
define('IMAGE_NEW_CATEGORY', '���������ƥ��꡼');
define('IMAGE_NEW_COUNTRY', '��������̾');
define('IMAGE_NEW_CURRENCY', '�������̲�');
define('IMAGE_NEW_FILE', '�������ե�����');
define('IMAGE_NEW_FOLDER', '�������ե����');
define('IMAGE_NEW_LANGUAGE', '����������');
define('IMAGE_NEW_NEWSLETTER', '�������᡼��ޥ�����');
define('IMAGE_NEW_PRODUCT', '����������');
define('IMAGE_NEW_TAX_CLASS', '�������Ǽ���');
define('IMAGE_NEW_TAX_RATE', '��������Ψ');
define('IMAGE_NEW_TAX_ZONE', '���������ϰ�');
define('IMAGE_NEW_ZONE', '�������ϰ�');
define('IMAGE_ORDERS', '��ʸ');
define('IMAGE_ORDERS_INVOICE', 'Ǽ�ʽ�');
define('IMAGE_ORDERS_PACKINGSLIP', '����ɼ');
define('IMAGE_PREVIEW', '�ץ�ӥ塼');
define('IMAGE_RESTORE', '����');
define('IMAGE_RESET', '�ꥻ�å�');
define('IMAGE_SAVE', '��¸');
define('IMAGE_SEARCH', '����');
define('IMAGE_SELECT', '����');
define('IMAGE_SEND', '����');
define('IMAGE_SEND_EMAIL', 'E-Mail����');
define('IMAGE_UNLOCK', '��å����');
define('IMAGE_UPDATE', '����');
define('IMAGE_UPDATE_CURRENCIES', '���إ졼�Ȥι���');
define('IMAGE_UPLOAD', '���åץ���');

define('ICON_CROSS', '��(False)');
define('ICON_CURRENT_FOLDER', '���ߤΥե����');
define('ICON_DELETE', '���');
define('ICON_ERROR', '���顼');
define('ICON_FILE', '�ե�����');
define('ICON_FILE_DOWNLOAD', '���������');
define('ICON_FOLDER', '�ե����');
define('ICON_LOCKED', '��å�');
define('ICON_PREVIOUS_LEVEL', '���Υ�٥�');
define('ICON_PREVIEW', '�ץ�ӥ塼');
define('ICON_STATISTICS', '����');
define('ICON_SUCCESS', '����');
define('ICON_TICK', '��(True)');
define('ICON_UNLOCKED', '��å����');
define('ICON_WARNING', '�ٹ�');

// constants for use in tep_prev_next_display function
define('TEXT_RESULT_PAGE', ' %s / %d �ڡ���');
define('TEXT_DISPLAY_NUMBER_OF_BANNERS', '<b>%d</b> &sim; <b>%d</b> ���ܤ�ɽ�� (<b>%d</b> �ΥХʡ��Τ���)');
define('TEXT_DISPLAY_NUMBER_OF_COUNTRIES', '<b>%d</b> &sim; <b>%d</b> ���ܤ�ɽ�� (<b>%d</b> �ι�Τ���)');
define('TEXT_DISPLAY_NUMBER_OF_CUSTOMERS', '<b>%d</b> &sim; <b>%d</b> ���ܤ�ɽ�� (<b>%d</b> �θܵҤΤ���)');
define('TEXT_DISPLAY_NUMBER_OF_CURRENCIES', '<b>%d</b> &sim; <b>%d</b> ���ܤ�ɽ�� (<b>%d</b> ���̲ߤΤ���)');
define('TEXT_DISPLAY_NUMBER_OF_LANGUAGES', '<b>%d</b> &sim; <b>%d</b> ���ܤ�ɽ�� (<b>%d</b> �θ���Τ���)');
define('TEXT_DISPLAY_NUMBER_OF_MANUFACTURERS', '<b>%d</b> &sim; <b>%d</b> ���ܤ�ɽ�� (<b>%d</b> �Υ᡼�����Τ���)');
define('TEXT_DISPLAY_NUMBER_OF_NEWSLETTERS', '<b>%d</b> &sim; <b>%d</b> ���ܤ�ɽ�� (<b>%d</b> �Υ᡼��ޥ�����Τ���)');
define('TEXT_DISPLAY_NUMBER_OF_ORDERS', '<b>%d</b> &sim; <b>%d</b> ���ܤ�ɽ�� (<b>%d</b> ����ʸ�Τ���)');
define('TEXT_DISPLAY_NUMBER_OF_ORDERS_STATUS', '<b>%d</b> &sim; <b>%d</b> ���ܤ�ɽ�� (<b>%d</b> ����ʸ�����Τ���)');
define('TEXT_DISPLAY_NUMBER_OF_PRODUCTS', '<b>%d</b> &sim; <b>%d</b> ���ܤ�ɽ�� (<b>%d</b> �ξ��ʤΤ���)');
define('TEXT_DISPLAY_NUMBER_OF_PRODUCTS_EXPECTED', '<b>%d</b> &sim; <b>%d</b> ���ܤ�ɽ�� (<b>%d</b> ������ͽ�꾦�ʤΤ���)');
define('TEXT_DISPLAY_NUMBER_OF_REVIEWS', '<b>%d</b> &sim; <b>%d</b> ���ܤ�ɽ�� (<b>%d</b> �ξ��ʥ�ӥ塼�Τ���)');
define('TEXT_DISPLAY_NUMBER_OF_SPECIALS', '<b>%d</b> &sim; <b>%d</b> ���ܤ�ɽ�� (<b>%d</b> ���ò����ʤΤ���)');
define('TEXT_DISPLAY_NUMBER_OF_TAX_CLASSES', '<b>%d</b> &sim; <b>%d</b> ���ܤ�ɽ�� (<b>%d</b> ���Ǽ��̤Τ���)');
define('TEXT_DISPLAY_NUMBER_OF_TAX_ZONES', '<b>%d</b> &sim; <b>%d</b> ���ܤ�ɽ�� (<b>%d</b> �����ϰ�Τ���)');
define('TEXT_DISPLAY_NUMBER_OF_TAX_RATES', '<b>%d</b> &sim; <b>%d</b> ���ܤ�ɽ�� (<b>%d</b> ����Ψ�Τ���)');
define('TEXT_DISPLAY_NUMBER_OF_ZONES', '<b>%d</b> &sim; <b>%d</b> ���ܤ�ɽ�� (<b>%d</b> ���ϰ�Τ���)');

define('PREVNEXT_BUTTON_PREV', '&lt;&lt;');
define('PREVNEXT_BUTTON_NEXT', '&gt;&gt;');

define('TEXT_DEFAULT', '�ǥե����');
define('TEXT_SET_DEFAULT', '�ǥե���Ȥ�����');
define('TEXT_FIELD_REQUIRED', '&nbsp;<span class="fieldRequired">* ɬ��</span>');

define('ERROR_NO_DEFAULT_CURRENCY_DEFINED', '���顼: ���ܤȤʤ��̲ߤ����ꤵ��Ƥ��ޤ��� ������˥塼->�����饤��->�̲�����: ��������ǧ���Ƥ���������');

define('TEXT_CACHE_CATEGORIES', '���ƥ��꡼�ܥå���');
define('TEXT_CACHE_MANUFACTURERS', '�᡼�����ܥå���');
define('TEXT_CACHE_ALSO_PURCHASED', '��Ϣ�ξ��ʥ⥸�塼��');

define('TEXT_NONE', '--�ʤ�--');
define('TEXT_TOP', '�ȥå�');

define('EMAIL_SIGNATURE',C_EMAIL_FOOTER);  //Add Japanese osCommerce

// Include OSC-AFFILIATE
include("affiliate_japanese.php");

//Add languages
//------------------------
//contents
define('BOX_TOOLS_CONTENTS', '����ƥ�Ĵ���');
define('TEXT_DISPLAY_NUMBER_OF_CONTENS', '<b>%d</b> &sim; <b>%d</b> ���ܤ�ɽ�� (<b>%d</b> �Υ���ƥ�ĤΤ���)');

//latest news
define('BOX_TOOLS_LATEST_NEWS', '����������');

//leftbox
define('BOX_CATALOG_PRODUCTS_UP', '���ʥǡ������åץ���');
define('BOX_CATALOG_PRODUCTS_DL', '���ʥǡ������������');
define('BOX_TOOLS_CL', '��������');
define('BOX_CATALOG_PRODUCTS_TAGS', '������Ͽ');
define('BOX_CATALOG_IMAGE_DOCUMENT', 'image�ե��������');
?>
