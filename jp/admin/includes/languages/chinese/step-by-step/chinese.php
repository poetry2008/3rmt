<?php
////
// NEW_LANGUAGE
////
// for create_account_text define

define('BOX_HEADING_MANUAL_ORDER', 'Manual Orders');
define('BOX_MANUAL_ORDER_CREATE_ACCOUNT', 'Create Account');
define('BOX_MANUAL_ORDER_CREATE_ORDER', 'Create Order');

// pull down default text
define('PULL_DOWN_DEFAULT', 'Please Select');
define('TYPE_BELOW', 'Type Below');

!defined('CATEGORY_COMPANY')  && define('CATEGORY_COMPANY', '公司信息');
!defined('CATEGORY_PERSONAL') && define('CATEGORY_PERSONAL', '个人信息');
!defined('CATEGORY_ADDRESS')  && define('CATEGORY_ADDRESS', '住址');
!defined('CATEGORY_CONTACT')  && define('CATEGORY_CONTACT', '联系方式');
!defined('CATEGORY_OPTIONS')  && define('CATEGORY_OPTIONS', '选项');
!defined('CATEGORY_PASSWORD') && define('CATEGORY_PASSWORD', '密码');
!defined('ENTRY_COMPANY')     && define('ENTRY_COMPANY', '公司/部门名:');
define('ENTRY_COMPANY_ERROR', '');
define('ENTRY_COMPANY_TEXT', '');
!defined('ENTRY_GENDER') && define('ENTRY_GENDER', '性别:');
define('ENTRY_GENDER_ERROR', '&nbsp;<small><font color="#AABBDD">是必要的。</font></small>');
define('ENTRY_GENDER_TEXT', '&nbsp;<small><font color="#AABBDD">必须</font></small>');
!defined('ENTRY_FIRST_NAME') && define('ENTRY_FIRST_NAME', '名:');
define('ENTRY_FIRST_NAME_ERROR', '&nbsp;<small><font color="#FF0000">至少 ' . ENTRY_FIRST_NAME_MIN_LENGTH . ' 字</font></small>');
define('ENTRY_FIRST_NAME_TEXT', '&nbsp;<small><font color="#AABBDD">必须</font></small>');
!defined('ENTRY_LAST_NAME') && define('ENTRY_LAST_NAME', '姓:');
define('ENTRY_LAST_NAME_ERROR', '&nbsp;<small><font color="#FF0000">至少 ' . ENTRY_LAST_NAME_MIN_LENGTH . ' 字</font></small>');
define('ENTRY_LAST_NAME_TEXT', '&nbsp;<small><font color="#AABBDD">必须</font></small>');
!defined('ENTRY_DATE_OF_BIRTH') && define('ENTRY_DATE_OF_BIRTH', '出生年月日:');
define('ENTRY_DATE_OF_BIRTH_ERROR', '&nbsp;<small><font color="#FF0000">(例. 1970/05/21)</font></small>');
define('ENTRY_DATE_OF_BIRTH_TEXT', '&nbsp;<small>(例. 1970/05/21) <font color="#AABBDD">必须</font></small>');
!defined('ENTRY_EMAIL_ADDRESS') && define('ENTRY_EMAIL_ADDRESS', 'E-Mail 地址:');
define('ENTRY_EMAIL_ADDRESS_ERROR', '&nbsp;<small><font color="#FF0000">至少 ' . ENTRY_EMAIL_ADDRESS_MIN_LENGTH . ' 字</font></small>');
define('ENTRY_EMAIL_ADDRESS_CHECK_ERROR', '&nbsp;<small><font color="#FF0000">填写的 E-Mail 地址有误!</font></small>');
define('ENTRY_EMAIL_ADDRESS_ERROR_EXISTS', '&nbsp;<small><font color="#FF0000">E-Mail 地址已存在!</font></small>');
define('ENTRY_EMAIL_ADDRESS_TEXT', '&nbsp;<small><font color="#AABBDD">必须</font></small>');
!defined('ENTRY_STREET_ADDRESS') && define('ENTRY_STREET_ADDRESS', '住址1:');
define('ENTRY_STREET_ADDRESS_ERROR', '&nbsp;<small><font color="#FF0000">至少 ' . ENTRY_STREET_ADDRESS_MIN_LENGTH . ' 字</font></small>');
define('ENTRY_STREET_ADDRESS_TEXT', '&nbsp;<small><font color="#AABBDD">必须</font></small>');
!defined('ENTRY_SUBURB') && define('ENTRY_SUBURB', '住址2:');
define('ENTRY_SUBURB_ERROR', '');
define('ENTRY_SUBURB_TEXT', '');
!defined('ENTRY_POST_CODE') && define('ENTRY_POST_CODE', '邮政编码:');
define('ENTRY_POST_CODE_ERROR', '&nbsp;<small><font color="#FF0000">至少 ' . ENTRY_POSTCODE_MIN_LENGTH . ' 字</font></small>');
define('ENTRY_POST_CODE_TEXT', '&nbsp;<small><font color="#AABBDD">必须</font></small>');
!defined('ENTRY_CITY') && define('ENTRY_CITY', '市区镇村:');
define('ENTRY_CITY_ERROR', '&nbsp;<small><font color="#FF0000">至少 ' . ENTRY_CITY_MIN_LENGTH . ' 字</font></small>');
define('ENTRY_CITY_TEXT', '&nbsp;<small><font color="#AABBDD">必须</font></small>');
!defined('ENTRY_STATE') && define('ENTRY_STATE', '都道府县:');
define('ENTRY_STATE_ERROR', '&nbsp;<small><font color="#FF0000">必须</font></small>');
define('ENTRY_STATE_TEXT', '&nbsp;<small><font color="#AABBDD">必须</font></small>');
!defined('ENTRY_COUNTRY') && define('ENTRY_COUNTRY', '国名:');
define('ENTRY_COUNTRY_ERROR', '');
define('ENTRY_COUNTRY_TEXT', '&nbsp;<small><font color="#AABBDD">必须</font></small>');
!defined('ENTRY_TELEPHONE_NUMBER') && define('ENTRY_TELEPHONE_NUMBER', '电话号码:');
define('ENTRY_TELEPHONE_NUMBER_ERROR', '&nbsp;<small><font color="#FF0000">至少 ' . ENTRY_TELEPHONE_MIN_LENGTH . ' 字</font></small>');
define('ENTRY_TELEPHONE_NUMBER_TEXT', '&nbsp;<small><font color="#AABBDD">必须</font></small>');
!defined('ENTRY_FAX_NUMBER') && define('ENTRY_FAX_NUMBER', '传真号:');
define('ENTRY_FAX_NUMBER_ERROR', '');
define('ENTRY_FAX_NUMBER_TEXT', '');
!defined('ENTRY_NEWSLETTER') && define('ENTRY_NEWSLETTER', '邮件杂志:');
define('ENTRY_NEWSLETTER_TEXT', '');
!defined('ENTRY_NEWSLETTER_YES') && define('ENTRY_NEWSLETTER_YES', '订阅');
!defined('ENTRY_NEWSLETTER_NO')  && define('ENTRY_NEWSLETTER_NO', '不订阅');
define('ENTRY_NEWSLETTER_ERROR', '');
!defined('ENTRY_PASSWORD')              && define('ENTRY_PASSWORD', '密码:');
!defined('ENTRY_PASSWORD_CONFIRMATION') && define('ENTRY_PASSWORD_CONFIRMATION', '再次输入密码:');
define('ENTRY_PASSWORD_CONFIRMATION_TEXT', '&nbsp;<small><font color="#AABBDD">必须</font></small>');
define('ENTRY_PASSWORD_ERROR', '&nbsp;<small><font color="#FF0000">至少 ' . ENTRY_PASSWORD_MIN_LENGTH . ' 字</font></small>');
define('ENTRY_PASSWORD_TEXT', '&nbsp;<small><font color="#AABBDD">必须</font></small>');
!defined('PASSWORD_HIDDEN')   && define('PASSWORD_HIDDEN', '********');

// pull down default text
!defined('PULL_DOWN_DEFAULT') && define('PULL_DOWN_DEFAULT', '请选择');
!defined('TYPE_BELOW')        && define('TYPE_BELOW', '在下面输入');

// add create_order
define('ENTRY_CUSTOMERS_ID', '客户ID:');
define('ENTRY_CUSTOMERS_ID_TEXT', '&nbsp;<small><font color="#AABBDD">必须</font></small>');

?>
