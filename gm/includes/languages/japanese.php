<?php
/*
  $Id: japanese.php,v 1.24 2003/06/06 01:34:50 ptosh Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

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
// on RedHat try 'en_US'
// on FreeBSD try 'en_US.ISO_8859-1'
// on Windows try 'en', or 'English'
@setlocale(LC_TIME, 'ja_JP');
define('DATE_FORMAT_SHORT', '%Y/%m/%d');  // this is used for strftime()
define('DATE_FORMAT_LONG', '%Yǯ%B%e�� %A'); // this is used for strftime()
define('DATE_FORMAT', 'Y/m/d'); // this is used for date()
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

// if USE_DEFAULT_LANGUAGE_CURRENCY is true, use the following currency, instead of the applications default currency (used when changing language)
define('LANGUAGE_CURRENCY', 'JPY');

// Global entries for the <html> tag
define('HTML_PARAMS','dir="LTR" lang="ja"');

// charset for web pages and emails
define('CHARSET', 'EUC-JP');    // Shift_JIS / euc-jp / iso-2022-jp

// page title
define('TITLE', STORE_NAME);  //����å�̾���򵭽Ҥ��Ƥ����������֥饦����ɽ�������ȥ�ˤʤ�ޤ���

define('META_TAGS',
 '<meta name = "keywords" content ="'.C_KEYWORDS.'">'."\n"
.'<meta name = "description" content ="'.C_DESCRIPTION.'">'."\n"
.'<meta name = "robots" content ="'.C_ROBOTS.'">'."\n"
.'<meta name = "copyright" content ="'.C_AUTHER.'">'
);

// header text in includes/header.php
define('HEADER_TITLE_CREATE_ACCOUNT', '��������Ⱥ���');
define('HEADER_TITLE_ABOUT_US','��ҳ���');
define('HEADER_TITLE_MY_ACCOUNT', '�����;���');
define('HEADER_TITLE_CART_CONTENTS', '�����Ȥ򸫤�');
define('HEADER_TITLE_CHECKOUT', '�쥸�˿ʤ�');
define('HEADER_TITLE_TOP', 'RMT');
define('HEADER_TITLE_CATALOG', '������');
define('HEADER_TITLE_LOGOFF', '��������');
define('HEADER_TITLE_LOGIN', '������');
define('HEADER_TITLE_SITEMAP', '�����ȥޥå�');
define('HEADER_TITLE_NEWS', '�ǿ�����');
define('MYACCOUNT_EDIT', '����������Խ�');
define('MYACCOUNT_ADDRESS', '���ɥ쥹Ģ');
define('MYACCOUNT_HISTORY', '��ʸ����');
define('MYACCOUNT_NOTIFICATION', '���ʤΤ��Τ餻');
define('MENU_MU','�᡼��������');

// footer text in includes/footer.php
define('FOOTER_TEXT_REQUESTS_SINCE', '�ꥯ������ ('); // 'requests since'
define('FOOTER_TEXT_REQUESTS_SINCE_ADD', ' ���)'); // 'requests since' Add Japanese osCommerce

// text for gender
define('MALE', '����');
define('FEMALE', '����');
define('MALE_ADDRESS', ''); //Mr.
define('FEMALE_ADDRESS', '');   //Ms.

// text for date of birth example
define('DOB_FORMAT_STRING', 'yyyy-mm-dd');

// categories box text in includes/boxes/categories.php
define('BOX_HEADING_CATEGORIES', '���ƥ��꡼');

// manufacturers box text in includes/boxes/manufacturers.php
define('BOX_HEADING_MANUFACTURERS', '�᡼����');

// whats_new box text in includes/boxes/whats_new.php
define('BOX_HEADING_WHATS_NEW', '���徦��');

// quick_find box text in includes/boxes/quick_find.php
define('BOX_HEADING_SEARCH', '���ʸ���');
define('BOX_SEARCH_TEXT', '������ɤ����Ϥ��ƾ��ʤ�õ���ޤ�');
define('BOX_SEARCH_ADVANCED_SEARCH', '�ܺٸ���');

// specials box text in includes/boxes/specials.php
define('BOX_HEADING_SPECIALS', '�ò�����');

// reviews box text in includes/boxes/reviews.php
define('BOX_HEADING_REVIEWS', '��ӥ塼');
define('BOX_REVIEWS_WRITE_REVIEW', '��ӥ塼���');
define('BOX_REVIEWS_NO_REVIEWS', '���ߥ�ӥ塼�Ϥ���ޤ���');
define('BOX_REVIEWS_TEXT_OF_5_STARS', '5����� %s��!');

// shopping_cart box text in includes/boxes/shopping_cart.php
define('BOX_HEADING_SHOPPING_CART', '����åԥ󥰥�����');
define('BOX_SHOPPING_CART_EMPTY', '�����Ȥ϶��Ǥ�...');

// order_history box text in includes/boxes/order_history.php
define('BOX_HEADING_CUSTOMER_ORDERS', '����ʸ����');

// best_sellers box text in includes/boxes/best_sellers.php
define('BOX_HEADING_BESTSELLERS', '��󥭥�');
define('BOX_HEADING_BESTSELLERS_IN', '&nbsp;&nbsp;<br>�Υ�󥭥�');

// notifications box text in includes/boxes/products_notifications.php
define('BOX_HEADING_NOTIFICATIONS', '�Żҥ᡼��Ǥ��Τ餻');
define('BOX_NOTIFICATIONS_NOTIFY', '<b>%s</b>�κǿ�������Τ餻��!');
define('BOX_NOTIFICATIONS_NOTIFY_REMOVE', '<b>%s</b>�κǿ�������Τ餻�ʤ���');

// manufacturer box text
define('BOX_HEADING_MANUFACTURER_INFO', '�᡼��������');
define('BOX_MANUFACTURER_INFO_HOMEPAGE', '%s �ۡ���ڡ���');
define('BOX_MANUFACTURER_INFO_OTHER_PRODUCTS', '����¾�ξ���');

// languages box text in includes/boxes/languages.php
define('BOX_HEADING_LANGUAGES', '����');

// currencies box text in includes/boxes/currencies.php
define('BOX_HEADING_CURRENCIES', '�̲�');

// information box text in includes/boxes/information.php
define('BOX_HEADING_INFORMATION', '����ե��᡼�����');

define('BOX_INFORMATION_PRIVACY', '�ץ饤�Х����ˤĤ���');
define('BOX_INFORMATION_COMPANY', '��ҳ���');
define('BOX_INFORMATION_CONDITIONS', '��������ˡ');
define('BOX_INFORMATION_SHIPPING', '���������ʤˤĤ���');
define('BOX_INFORMATION_PAYMENT', '����ʧ���ˤĤ���');
define('BOX_INFORMATION_FAQ', '�褯�������');
define('BOX_INFORMATION_CONTACT', '���䤤��碌');


// tell a friend box text in includes/boxes/tell_a_friend.php
define('BOX_HEADING_TELL_A_FRIEND', 'ͧã���Τ餻��');
define('BOX_TELL_A_FRIEND_TEXT', '���ξ��ʤ�URL��ͧã�˥᡼�뤹��');

// checkout procedure text
define('CHECKOUT_BAR_DELIVERY',    '�������');
define('CHECKOUT_BAR_PAYMENT',     '��ʧ��ˡ');
define('CHECKOUT_BAR_CONFIRMATION','�ǽ���ǧ');
define('CHECKOUT_BAR_FINISHED',    '��³��λ!');

// pull down default text
define('PULL_DOWN_DEFAULT', '���򤷤Ƥ�������');
define('TYPE_BELOW', '��������');

// javascript messages
define('JS_ERROR', '���ϥե�����ǥ��顼�������Ƥ��ޤ�!\n���ι��ܤ������Ƥ�������:\n\n');

define('JS_REVIEW_TEXT', '* ��ӥ塼��ʸ�ϤϾ��ʤ��Ƥ� ' . REVIEW_TEXT_MIN_LENGTH . ' ʸ���ʾ�ɬ�פǤ���\n');
define('JS_REVIEW_RATING', '* ���ʤκ����򤷤Ƥ���������\n');

define('JS_GENDER', '* \'����\' �����򤵤�Ƥ��ޤ���\n');
define('JS_FIRST_NAME', '* \'̾��\' �Ͼ��ʤ��Ƥ� ' . ENTRY_FIRST_NAME_MIN_LENGTH . ' ʸ���ʾ�ɬ�פǤ���\n');
define('JS_LAST_NAME', '* \'��\' �Ͼ��ʤ��Ƥ� ' . ENTRY_LAST_NAME_MIN_LENGTH . ' ʸ���ʾ�ɬ�פǤ���\n');

define('JS_FIRST_NAME_F', '* \'̾��(�եꥬ��)\' �Ͼ��ʤ��Ƥ� ' . ENTRY_FIRST_NAME_MIN_LENGTH . ' ʸ���ʾ�ɬ�פǤ���\n');
define('JS_LAST_NAME_F', '* \'��(�եꥬ��)\' �Ͼ��ʤ��Ƥ� ' . ENTRY_LAST_NAME_MIN_LENGTH . ' ʸ���ʾ�ɬ�פǤ���\n');

define('JS_DOB', '* \'��ǯ����\' �ϼ��η��������Ϥ��Ƥ�������: xxxx/xx/xx (ǯ/��/��).\n');
define('JS_EMAIL_ADDRESS', '* \'�᡼�륢�ɥ쥹\' �Ͼ��ʤ��Ƥ� ' . ENTRY_EMAIL_ADDRESS_MIN_LENGTH . ' ʸ���ʾ�ɬ�פǤ���\n');
define('JS_ADDRESS', '* \'����1\' �Ͼ��ʤ��Ƥ� ' . ENTRY_STREET_ADDRESS_MIN_LENGTH . ' ʸ���ʾ�ɬ�פǤ���\n');
define('JS_POST_CODE', '* \'͹���ֹ�\' �Ͼ��ʤ��Ƥ� ' . ENTRY_POSTCODE_MIN_LENGTH . ' ʸ���ʾ�ɬ�פǤ���\n');
define('JS_CITY', '* \'�Զ�Į¼\' �Ͼ��ʤ��Ƥ� ' . ENTRY_CITY_MIN_LENGTH . ' ʸ���ʾ�ɬ�פǤ���\n');
define('JS_STATE', '* \'��ƻ�ܸ�\' ������ޤ������Ϥ��Ƥ���������\n');
define('JS_COUNTRY', '* \'��\' �����򤷤Ƥ���������');
define('JS_TELEPHONE', '* \'�����ֹ�\' �Ͼ��ʤ��Ƥ� ' . ENTRY_TELEPHONE_MIN_LENGTH . ' ʸ���ʾ�ɬ�פǤ���\n');
define('JS_PASSWORD', '* \'�ѥ����\' �� \'�ѥ���ɤ������\' �ϰ��פ��Ƥ��� ' . ENTRY_PASSWORD_MIN_LENGTH . ' ʸ���ʾ�ɬ�פǤ���\n');
define('JS_AGREEMENT', '* \'���ѵ���\' ��Ʊ�դ��Ƥ���������');

define('JS_ERROR_NO_PAYMENT_MODULE_SELECTED', '* ����ʸ���ʤΤ���ʧ��ˡ�����򤷤Ƥ���������\n');
define('JS_ERROR_SUBMITTED', '���Υե�����ϴ�����������Ƥ��ޤ���Ok�ܥ���򲡤���������λ����ޤǤ⤦���Ф餯���Ԥ�����������');

define('ERROR_NO_PAYMENT_MODULE_SELECTED', '* ����ʸ���ʤΤ���ʧ��ˡ�����򤷤Ƥ���������');

define('CATEGORY_COMPANY', '��Ҿ���');
define('CATEGORY_PERSONAL', '�Ŀ;���');
define('CATEGORY_ADDRESS', '������');
define('CATEGORY_CONTACT', '��Ϣ����');
define('CATEGORY_OPTIONS', '���ץ����');
define('CATEGORY_PASSWORD', '�ѥ����');
define('CATEGORY_AGREEMENT', '���ѵ���');
define('ENTRY_COMPANY', '���/����̾:');
define('ENTRY_COMPANY_ERROR', '');
define('ENTRY_COMPANY_TEXT', '');
define('ENTRY_GENDER', '����:');
define('ENTRY_GENDER_ERROR', '&nbsp;<small><font color="#AABBDD">��ɬ�פǤ���</font></small>');
define('ENTRY_GENDER_TEXT', '&nbsp;<small><font color="#AABBDD">ɬ��</font></small>');
define('ENTRY_FIRST_NAME', '̾:');
define('ENTRY_FIRST_NAME_ERROR', '&nbsp;<small><font color="#FF0000">���ʤ��Ƥ� ' . ENTRY_FIRST_NAME_MIN_LENGTH . ' ʸ��</font></small>');
define('ENTRY_FIRST_NAME_TEXT', '&nbsp;<small>(��. ��Ϻ) <font color="#AABBDD">ɬ��</font></small>');
define('ENTRY_LAST_NAME', '��:');
define('ENTRY_LAST_NAME_ERROR', '&nbsp;<small><font color="#FF0000">���ʤ��Ƥ� ' . ENTRY_LAST_NAME_MIN_LENGTH . ' ʸ��</font></small>');
define('ENTRY_LAST_NAME_TEXT', '&nbsp;<small>(��. ����) <font color="#AABBDD">ɬ��</font></small>');

define('ENTRY_FIRST_NAME_F', '̾(�եꥬ��):');
define('ENTRY_FIRST_NAME_F_ERROR', '&nbsp;<small><font color="#FF0000">���ʤ��Ƥ� ' . ENTRY_FIRST_NAME_MIN_LENGTH . ' ʸ��</font></small>');
define('ENTRY_FIRST_NAME_F_TEXT', '&nbsp;<small>(��. ����) <font color="#AABBDD">ɬ��</font></small>');
define('ENTRY_LAST_NAME_F', '��(�եꥬ��):');
define('ENTRY_LAST_NAME_F_ERROR', '&nbsp;<small><font color="#FF0000">���ʤ��Ƥ� ' . ENTRY_LAST_NAME_MIN_LENGTH . ' ʸ��</font></small>');
define('ENTRY_LAST_NAME_F_TEXT', '&nbsp;<small>(��. ���ʥ�) <font color="#AABBDD">ɬ��</font></small>');

define('ENTRY_DATE_OF_BIRTH', '��ǯ����:');
define('ENTRY_DATE_OF_BIRTH_ERROR', '&nbsp;<small><font color="#FF0000">(��. 1970/05/21)</font></small>');
define('ENTRY_DATE_OF_BIRTH_TEXT', '&nbsp;<small>(��. 1970/05/21) <font color="#AABBDD">ɬ��</font></small>');
define('ENTRY_EMAIL_ADDRESS', '�᡼�륢�ɥ쥹:');
define('ENTRY_EMAIL_ADDRESS_ERROR', '&nbsp;<small><font color="#FF0000">���ʤ��Ƥ� ' . ENTRY_EMAIL_ADDRESS_MIN_LENGTH . ' ʸ��</font></small>');
define('ENTRY_EMAIL_ADDRESS_CHECK_ERROR', '&nbsp;<small><font color="#FF0000">���Ϥ��줿�᡼�륢�ɥ쥹�������Ǥ�!</font></small>');
define('ENTRY_EMAIL_ADDRESS_ERROR_EXISTS', '&nbsp;<small><font color="#FF0000">�᡼�륢�ɥ쥹�Ϥ��Ǥ�¸�ߤ��Ƥ��ޤ�!</font></small>');
define('ENTRY_EMAIL_ADDRESS_TEXT', '&nbsp;<small>(��. sample@example.com) <font color="#AABBDD">ɬ��</font></small>');
define('ENTRY_STREET_ADDRESS', '���꣱:');
define('ENTRY_STREET_ADDRESS_ERROR', '&nbsp;<small><font color="#FF0000">���ʤ��Ƥ� ' . ENTRY_STREET_ADDRESS_MIN_LENGTH . ' ʸ��</font></small>');
define('ENTRY_STREET_ADDRESS_TEXT', '&nbsp;<small>(��. 1-15-6) <font color="#AABBDD">ɬ��</font></small>');
define('ENTRY_SUBURB', '���ꣲ:');
define('ENTRY_SUBURB_ERROR', '');
define('ENTRY_SUBURB_TEXT', '');
define('ENTRY_POST_CODE', '͹���ֹ�:');
define('ENTRY_POST_CODE_ERROR', '&nbsp;<small><font color="#FF0000">���ʤ��Ƥ� ' . ENTRY_POSTCODE_MIN_LENGTH . ' ʸ��</font></small>');
define('ENTRY_POST_CODE_TEXT', '&nbsp;<small>(��. 331-0814) <font color="#AABBDD">ɬ��</font></small>');
define('ENTRY_CITY', '�Զ�Į¼:');
define('ENTRY_CITY_ERROR', '&nbsp;<small><font color="#FF0000">���ʤ��Ƥ� ' . ENTRY_CITY_MIN_LENGTH . ' ʸ��</font></small>');
define('ENTRY_CITY_TEXT', '&nbsp;<small>(��. �������޻�) <font color="#AABBDD">ɬ��</font></small>');
define('ENTRY_STATE', '��ƻ�ܸ�:');
define('ENTRY_STATE_ERROR', '&nbsp;<small><font color="#FF0000">ɬ��</font></small>');
define('ENTRY_STATE_TEXT', '&nbsp;<small>(��. ��̸�) <font color="#AABBDD">ɬ��</font></small>');
define('ENTRY_COUNTRY', '��̾:');
define('ENTRY_COUNTRY_ERROR', '');
define('ENTRY_COUNTRY_TEXT', '&nbsp;<small><font color="#AABBDD">ɬ��</font></small>');
define('ENTRY_TELEPHONE_NUMBER', '�����ֹ�:');
define('ENTRY_TELEPHONE_NUMBER_ERROR', '&nbsp;<small><font color="#FF0000">���ʤ��Ƥ� ' . ENTRY_TELEPHONE_MIN_LENGTH . ' ʸ��</font></small>');
define('ENTRY_TELEPHONE_NUMBER_TEXT', '&nbsp;<small>(��. 012-345-6789) <font color="#AABBDD">ɬ��</font></small>');
define('ENTRY_FAX_NUMBER', '�ե������ֹ�:');
define('ENTRY_FAX_NUMBER_ERROR', '');
define('ENTRY_FAX_NUMBER_TEXT', '');
define('ENTRY_NEWSLETTER', '�᡼��ޥ�����:');
define('ENTRY_NEWSLETTER_TEXT', '');
define('ENTRY_NEWSLETTER_YES', '���ɤ���');
define('ENTRY_NEWSLETTER_NO', '���ɤ��ʤ�');
define('ENTRY_NEWSLETTER_ERROR', '');
define('ENTRY_PASSWORD', '�ѥ����:');
define('ENTRY_PASSWORD_CONFIRMATION', '�ѥ���ɤ������:');
define('ENTRY_PASSWORD_CONFIRMATION_TEXT', '&nbsp;<small>' . ENTRY_PASSWORD_MIN_LENGTH . 'ʸ���ʾ�&nbsp;(��. abcdef) <font color="#AABBDD">ɬ��</font></small>');
define('ENTRY_PASSWORD_ERROR', '&nbsp;<small><font color="#FF0000">���ʤ��Ƥ� ' . ENTRY_PASSWORD_MIN_LENGTH . ' ʸ��</font></small>');
define('ENTRY_PASSWORD_TEXT', '&nbsp;<small>' . ENTRY_PASSWORD_MIN_LENGTH . 'ʸ���ʾ�&nbsp;(��. abcdef) <font color="#AABBDD">ɬ��</font></small>');
define('PASSWORD_HIDDEN', '********');
define('ENTRY_AGREEMENT_TEXT', 'Ʊ�դ���');

// constants for use in tep_prev_next_display function
define('TEXT_RESULT_PAGE', '�ڡ���:');
define('TEXT_DISPLAY_NUMBER_OF_PRODUCTS', '<b>%d</b> - <b>%d</b> ���ܤ�ɽ�� (<b>%d</b> ���뾦�ʤΤ���)');
define('TEXT_DISPLAY_NUMBER_OF_ORDERS', '<b>%d</b> - <b>%d</b> ���ܤ�ɽ�� (<b>%d</b> ������ʸ�Τ���)');
define('TEXT_DISPLAY_NUMBER_OF_REVIEWS', '<b>%d</b> - <b>%d</b> ���ܤ�ɽ�� (<b>%d</b> �����ӥ塼�Τ���)');
define('TEXT_DISPLAY_NUMBER_OF_PRODUCTS_NEW', '<b>%d</b> - <b>%d</b> ���ܤ�ɽ�� (<b>%d</b> ���뿷�徦�ʤΤ���)');
define('TEXT_DISPLAY_NUMBER_OF_SPECIALS', '<b>%d</b> - <b>%d</b> ���ܤ�ɽ�� (<b>%d</b> �����ò����ʤΤ���)');
define('TEXT_DISPLAY_NUMBER_OF_LATEST_NEWS', '<b>%d</b> - <b>%d</b> ���ܤ�ɽ�� (<b>%d</b> ���뤪�Τ餻�Τ���)');

define('PREVNEXT_TITLE_FIRST_PAGE', '�ǽ�Υڡ���');
define('PREVNEXT_TITLE_PREVIOUS_PAGE', '���ڡ���');
define('PREVNEXT_TITLE_NEXT_PAGE', '���ڡ���');
define('PREVNEXT_TITLE_LAST_PAGE', '�Ǹ�Υڡ���');
define('PREVNEXT_TITLE_PAGE_NO', '�ڡ��� %d');
define('PREVNEXT_TITLE_PREV_SET_OF_NO_PAGE', '�� %d �ڡ���');
define('PREVNEXT_TITLE_NEXT_SET_OF_NO_PAGE', '�� %d �ڡ���');
define('PREVNEXT_BUTTON_FIRST', '&lt;&lt;�ǽ�');
define('PREVNEXT_BUTTON_PREV', '���Υڡ���');
define('PREVNEXT_BUTTON_NEXT', '���Υڡ���');
define('PREVNEXT_BUTTON_LAST', '�Ǹ�&gt;&gt;');

define('IMAGE_BUTTON_ADD_ADDRESS', '���ɥ쥹���ɲ�');
define('IMAGE_BUTTON_ADDRESS_BOOK', '���ɥ쥹Ģ');
define('IMAGE_BUTTON_BACK', '�������');
define('IMAGE_BUTTON_CHANGE_ADDRESS', '���ɥ쥹���ѹ�');
define('IMAGE_BUTTON_CHECKOUT', '�쥸�˿ʤ�');
define('IMAGE_BUTTON_CONFIRM_ORDER', '��ʸ����!');
define('IMAGE_BUTTON_CONTINUE', '���˿ʤ�');
define('IMAGE_BUTTON_CONTINUE_SHOPPING', '����åԥ󥰤�³����');
define('IMAGE_BUTTON_DELETE', '�������');
define('IMAGE_BUTTON_EDIT_ACCOUNT', '����������Խ�');
define('IMAGE_BUTTON_HISTORY', '����ʸ����');
define('IMAGE_BUTTON_LOGIN', '������');
define('IMAGE_BUTTON_IN_CART', '�����Ȥ������');
define('IMAGE_BUTTON_NOTIFICATIONS', '���Τ餻������');
define('IMAGE_BUTTON_QUICK_FIND', '���ʸ���');
define('IMAGE_BUTTON_REMOVE_NOTIFICATIONS', '���Τ餻���ä�');
define('IMAGE_BUTTON_REVIEWS', '��ӥ塼���ɤ�');
define('IMAGE_BUTTON_SEARCH', '��������');
define('IMAGE_BUTTON_SHIPPING_OPTIONS', '�������ץ����');
define('IMAGE_BUTTON_TELL_A_FRIEND', 'ͧã���Τ餻��');
define('IMAGE_BUTTON_UPDATE', '��������');
define('IMAGE_BUTTON_UPDATE_CART', '�����Ȥ򹹿�');
define('IMAGE_BUTTON_WRITE_REVIEW', '��ӥ塼���');
define('IMAGE_BUTTON_PRESENT','���礹��');//add present
define('IMAGE_BUTTON_QUT','ŹĹ�˼���');//add present
define('IMAGE_BUTTON_DEC','�ܺ٤Ϥ�����');//add present

define('ICON_ARROW_RIGHT', '������ɽ��'); //more
define('ICON_CART', '�����Ȥ������');
define('ICON_WARNING', '�ٹ�');

define('TEXT_GREETING_PERSONAL', '����ä��㤤�ޤ���<span class="greetUser">%s ����</span>�� <a href="%s"><u>���徦��</u></a> �����ˤʤ�ޤ�����');
define('TEXT_GREETING_PERSONAL_RELOGON', '<small>�⤷���ʤ��� %s ����Ǥʤ���С������;�������Ϥ��� <a href="%s"><u>������</u></a> ���Ƥ���������</small>');
define('TEXT_GREETING_GUEST', '<span class="greetUser">�����Ȥ���</span>������ä��㤤�ޤ��� <a href="%s"><u>������</u></a>���ޤ����� ����Ȥ⡢<a href="%s"><u>���������������</u></a>��������ޤ�����');

define('TEXT_SORT_PRODUCTS', '���ʤ��¤��ؤ��� ');
define('TEXT_DESCENDINGLY', '�߽�');
define('TEXT_ASCENDINGLY', '����');
define('TEXT_BY', ' &sim; ');   //by

define('TEXT_REVIEW_BY', '��Ƽ� %s');
define('TEXT_REVIEW_WORD_COUNT', '%s ʸ��');
define('TEXT_REVIEW_RATING', 'ɾ��: %s [%s]');
define('TEXT_REVIEW_DATE_ADDED', '�����: %s');
define('TEXT_NO_REVIEWS', '�ޤ���ӥ塼�Ϥ���ޤ���...');

define('TEXT_NO_NEW_PRODUCTS', '���߾��ʤ���Ͽ����Ƥ��ޤ���...');

define('TEXT_UNKNOWN_TAX_RATE', '��Ψ����');

define('TEXT_REQUIRED', 'ɬ��');

define('TEXT_TIME_SPECIFY', '���Ϥ����������: '); // add for Japanese update

define('ERROR_TEP_MAIL', '<font face="Verdana, Arial" size="2" color="#ff0000"><b><small>���顼:</small> ���ꤵ�줿SMTP �����Ф���᡼��������Ǥ��ޤ��� php.ini ��SMTP ������������ǧ���ơ�ɬ�פ�����н������Ƥ���������</b></font>');
define('WARNING_INSTALL_DIRECTORY_EXISTS', '�ٹ�: ���󥹥ȡ��롦�ǥ��쥯�ȥ�(/install)��¸�ߤ����ޤޤǤ�: ' . dirname($HTTP_SERVER_VARS['SCRIPT_FILENAME']) . '/install. �ǥ��쥯�ȥ�ϥ������ƥ���δ�������ޤ��ΤǺ�����Ƥ���������');
define('WARNING_CONFIG_FILE_WRITEABLE', '�ٹ�: ����ե�����(/includes/configure.php)�˽񤭹��߸��¤����ꤵ�줿�ޤޤǤ�: ' . dirname($HTTP_SERVER_VARS['SCRIPT_FILENAME']) . '/includes/configure.php. �ե�����Υ桼�����¤��ѹ����Ƥ���������');
define('WARNING_SESSION_DIRECTORY_NON_EXISTENT', '�ٹ�: ���å���󡦥ǥ��쥯�ȥ꤬¸�ߤ��ޤ���: ' . tep_session_save_path() . '. ���å��������Ѥ��뤿��˥ǥ��쥯�ȥ��������Ƥ���������');
define('WARNING_SESSION_DIRECTORY_NOT_WRITEABLE', '�ٹ�: ���å���󡦥ǥ��쥯�ȥ�˽񤭹��ߤ��Ǥ��ޤ���: ' . tep_session_save_path() . '. ���å���󡦥ǥ��쥯�ȥ���������桼�����¤����ꤷ�Ƥ���������');
define('WARNING_SESSION_AUTO_START', '�ٹ�: ���å���󡦥����ȥ������Ȥ�ͭ���ˤʤäƤ��ޤ�������ե������php.ini�ˤ�̵�������ꤷ�������֥����Ф�ꥹ�����Ȥ��Ƥ���������');
define('WARNING_DOWNLOAD_DIRECTORY_NON_EXISTENT', '�ٹ�: ������������侦�ʥǥ��쥯�ȥ꤬¸�ߤ��ޤ���: ' . DIR_FS_DOWNLOAD . '. ���Υǥ��쥯�ȥ��������ʤ���������������侦�ʤμ谷��������ޤ���');

define('TEXT_CCVAL_ERROR_INVALID_DATE', '���쥸�åȥ�����ͭ�����¤�����������ޤ���<br>����ǧ��⤦�������Ϥ��Ƥ���������');
define('TEXT_CCVAL_ERROR_INVALID_NUMBER', '���쥸�åȥ����ɥʥ�С�������������ޤ���<br>����ǧ��⤦�������Ϥ��Ƥ���������');
define('TEXT_CCVAL_ERROR_UNKNOWN_CARD', '���Ϥ������쥸�åȥ����ɥʥ�С��κǽ��4���: %s �Ǥ���<br>�ʥ�С�����������礳�Υ����ɤμ谷��������ޤ���<br>�ְ�äƤ�����Ϥ���ǧ��⤦�������Ϥ��Ƥ���������');

/*
  The following copyright announcement can only be
  appropriately modified or removed if the layout of
  the site theme has been modified to distinguish
  itself from the default osCommerce-copyrighted
  theme.

  For more information please read the following
  Frequently Asked Questions entry on the osCommerce
  support site:

  http://www.oscommerce.com/community.php/faq,26/q,50

  Please leave this comment intact together with the
  following copyright announcement.
*/
define('FOOTER_TEXT_BODY', C_FOOTER_COPY_RIGHT);

define('EMAIL_SIGNATURE',C_EMAIL_FOOTER);  //Add Japanese osCommerce

// Include OSC-AFFILIATE 
include("affiliate_japanese.php");

//Add languages
//------------------------

//create_account
define('ENTRY_DATE_OF_BIRTH_ERROR2', '&nbsp;<small><font color="#FF0000">18��̤����������Ͽ�Ϥ���θ����������</font></small>');

//page - ����ե��᡼�����ڡ��������Ĥ���ʤ��ä�����ɽ��
define('PAGE_TEXT_NOT_FOUND', '�ڡ��������Ĥ���ޤ���...');
define('PAGE_ERR_NAVBER_TITLE', '�ڡ��������Ĥ���ޤ���...');

//latest_news
define('TABLE_HEADING_LATEST_NEWS', '�������'); //Add for latest_news

//product_listing
define('LIST_FILTER', 'ɽ�����ץ����');
define('LISTING_DISPLAY', '-- ɽ����ˡ --');
define('LISTING_DEFAULT', '�����ȥ�Ȳ���');
define('LISTING_IMAGE', '�����Τ�');
define('LISTING_TEXT', '�ƥ����ȤΤ�');

// calender box text in includes/boxes/cl.php
define('BOX_HEADING_CL', '&nbsp;�αĶ���');
define('BOX_CL_COLOR01', '&nbsp;&raquo;&nbsp;Ź�޵ٶ���');
define('BOX_CL_COLOR02', '&nbsp;&raquo;&nbsp;�᡼���ֿ��ٶ���');

//add present
define('BOX_HEADING_PRESENT','�ץ쥼��Ⱦ��ʡ�');
define('TEXT_DISPLAY_NUMBER_OF_PRESENT', '<b>%d</b> - <b>%d</b> ���ܤ�ɽ�� (<b>%d</b> ����ץ쥼��Ⱦ��ʤΤ���)');

define('ENTRY_GUEST', '�����Ͽ:');
define('ENTRY_ACCOUNT_MEMBER', '�����Ͽ�򤹤�');
define('ENTRY_ACCOUNT_GUEST', '�����Ͽ�򤷤ʤ�');

# ��ʸ������ۤ�Ķ�����Ȥ��Υ�å�����
define('DS_LIMIT_PRICE_OVER_ERROR', '���٤�%s�ʾ����ʸ���뤳�ȤϤǤ��ޤ���<br>��׶�ۤ�%s�ʲ��ˤ��Ƥ�����٤��������ߤ���������');

define('INPUT_SEND_MAIL', '�᡼�륢�ɥ쥹');
define('EMAIL_PATTERN_WRONG', '�᡼�륢�ɥ쥹�������������ϲ�������');
define('SENDMAIL_BUTTON', '����');
define('LINK_SENDMAIL_TEXT', '�᡼������ƥ��Ȥ򤹤�');
define('LINK_SENDMAIL_TITLE', '�᡼������ƥ���');
define('SENDMAIL_SUCCESS_TEXT', '�᡼�뤬��������ޤ�����RMT������ޥ͡�����μ����򤴳�ǧ����������');
define('SENDMAIL_READ_TEXT', '<b>'.STORE_NAME.'����Υ᡼���</b> <br><b>����˼����Ǥ����硧</b>�������׸�5ʬ�����<b>'.STORE_NAME.'</b>����γ�ǧ�᡼�뤬�Ϥ��ޤ��� <br><b>�����Ǥ��ʤ���硧</b>�������׸�5ʬ�ʾ�вᤷ�Ƥ�����Ǥ��ʤ����ϡ����ѥ�ե��륿�����Ǽ���������ݤ���Ƥ����ǽ�����⤤�Ǥ���');
define('SENDMAIL_TROUBLE_PRE', '�᡼�����������˴ؤ��Ƥϡ�');
define('SENDMAIL_TROUBLE_LINK', '<b>�֥ե꡼�᡼��ǥ᡼�뤬�������ʤ����ء�</b>');
define('SENDMAIL_TROUBLE_END', '�򤴻��ͤ���������');
define('PAGE_NEW_TITLE', '����ե��᡼�����');
define('TEXT_NO_PRODUCTS', '���߾��ʤ���Ͽ����Ƥ��ޤ���...');
define('SEND_MAIL_HEADING_TITLE', '�᡼������ƥ���');
?>
