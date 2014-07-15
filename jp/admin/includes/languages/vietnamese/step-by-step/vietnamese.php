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

!defined('CATEGORY_COMPANY')  && define('CATEGORY_COMPANY', 'Thông tin công ty');
!defined('CATEGORY_PERSONAL') && define('CATEGORY_PERSONAL', 'Thông tin cá nhân');
!defined('CATEGORY_ADDRESS')  && define('CATEGORY_ADDRESS', 'Địa chỉ');
!defined('CATEGORY_CONTACT')  && define('CATEGORY_CONTACT', 'Địa chỉ liên lạc');
!defined('CATEGORY_OPTIONS')  && define('CATEGORY_OPTIONS', 'Tùy chọn');
!defined('CATEGORY_PASSWORD') && define('CATEGORY_PASSWORD', 'Mật khẩu');
!defined('ENTRY_COMPANY')     && define('ENTRY_COMPANY', 'Tên bộ phận/công ty:');
define('ENTRY_COMPANY_ERROR', '');
define('ENTRY_COMPANY_TEXT', '');
!defined('ENTRY_GENDER') && define('ENTRY_GENDER', 'Giới tính:');
define('ENTRY_GENDER_ERROR', '&nbsp;<small><font color="#AABBDD">Cần thiết</font></small>');
define('ENTRY_GENDER_TEXT', '&nbsp;<small><font color="#AABBDD">Bắt buộc</font></small>');
!defined('ENTRY_FIRST_NAME') && define('ENTRY_FIRST_NAME', 'Tên:');
define('ENTRY_FIRST_NAME_ERROR', '&nbsp;<small><font color="#FF0000"> tối thiểu ' . ENTRY_FIRST_NAME_MIN_LENGTH . ' Kí tự</font></small>');
define('ENTRY_FIRST_NAME_TEXT', '&nbsp;<small><font color="#AABBDD">Bắt buộc</font></small>');
!defined('ENTRY_LAST_NAME') && define('ENTRY_LAST_NAME', 'Họ:');
define('ENTRY_LAST_NAME_ERROR', '&nbsp;<small><font color="#FF0000">tối thiểu' . ENTRY_LAST_NAME_MIN_LENGTH . ' Kí tự</font></small>');
define('ENTRY_LAST_NAME_TEXT', '&nbsp;<small><font color="#AABBDD">Bắt buộc</font></small>');
!defined('ENTRY_DATE_OF_BIRTH') && define('ENTRY_DATE_OF_BIRTH', 'Ngày tháng năm sinh:');
define('ENTRY_DATE_OF_BIRTH_ERROR', '&nbsp;<small><font color="#FF0000">(Ví dụ. 1970/05/21)</font></small>');
define('ENTRY_DATE_OF_BIRTH_TEXT', '&nbsp;<small>(Ví dụ. 1970/05/21) <font color="#AABBDD">Bắt buộc</font></small>');
!defined('ENTRY_EMAIL_ADDRESS') && define('ENTRY_EMAIL_ADDRESS', 'Địa chỉ E-Mail:');
define('ENTRY_EMAIL_ADDRESS_ERROR', '&nbsp;<small><font color="#FF0000">tối thiểu ' . ENTRY_EMAIL_ADDRESS_MIN_LENGTH . ' Kí tự</font></small>');
define('ENTRY_EMAIL_ADDRESS_CHECK_ERROR', '&nbsp;<small><font color="#FF0000">Địa chỉ  E-Mail đã nhập chưa chính xác!</font></small>');
define('ENTRY_EMAIL_ADDRESS_ERROR_EXISTS', '&nbsp;<small><font color="#FF0000">Địa chỉ E-Mail đã tồn tại trước đó!</font></small>');
define('ENTRY_EMAIL_ADDRESS_TEXT', '&nbsp;<small><font color="#AABBDD">Bắt buộc</font></small>');
!defined('ENTRY_STREET_ADDRESS') && define('ENTRY_STREET_ADDRESS', 'Địa chỉ１:');
define('ENTRY_STREET_ADDRESS_ERROR', '&nbsp;<small><font color="#FF0000">tối thiểu ' . ENTRY_STREET_ADDRESS_MIN_LENGTH . ' Kí tự</font></small>');
define('ENTRY_STREET_ADDRESS_TEXT', '&nbsp;<small><font color="#AABBDD">Bắt buộc</font></small>');
!defined('ENTRY_SUBURB') && define('ENTRY_SUBURB', 'Địa chỉ ２:');
define('ENTRY_SUBURB_ERROR', '');
define('ENTRY_SUBURB_TEXT', '');
!defined('ENTRY_POST_CODE') && define('ENTRY_POST_CODE', 'Mã bưu điện:');
define('ENTRY_POST_CODE_ERROR', '&nbsp;<small><font color="#FF0000">ít nhất ' . ENTRY_POSTCODE_MIN_LENGTH . ' Kí tự</font></small>');
define('ENTRY_POST_CODE_TEXT', '&nbsp;<small><font color="#AABBDD">Bắt buộc</font></small>');
!defined('ENTRY_CITY') && define('ENTRY_CITY', 'Quận huyện tỉnh thành phố:');
define('ENTRY_CITY_ERROR', '&nbsp;<small><font color="#FF0000">tổi thiểu ' . ENTRY_CITY_MIN_LENGTH . ' Kí tự</font></small>');
define('ENTRY_CITY_TEXT', '&nbsp;<small><font color="#AABBDD">Bắt buộc</font></small>');
!defined('ENTRY_STATE') && define('ENTRY_STATE', 'Tỉnh thành:');
define('ENTRY_STATE_ERROR', '&nbsp;<small><font color="#FF0000">Bắt buộc</font></small>');
define('ENTRY_STATE_TEXT', '&nbsp;<small><font color="#AABBDD">Bắt buộc</font></small>');
!defined('ENTRY_COUNTRY') && define('ENTRY_COUNTRY', 'Tên nước:');
define('ENTRY_COUNTRY_ERROR', '');
define('ENTRY_COUNTRY_TEXT', '&nbsp;<small><font color="#AABBDD">Bắt buộc</font></small>');
!defined('ENTRY_TELEPHONE_NUMBER') && define('ENTRY_TELEPHONE_NUMBER', 'Số điện thoại:');
define('ENTRY_TELEPHONE_NUMBER_ERROR', '&nbsp;<small><font color="#FF0000">tối thiểu ' . ENTRY_TELEPHONE_MIN_LENGTH . ' Kí tự</font></small>');
define('ENTRY_TELEPHONE_NUMBER_TEXT', '&nbsp;<small><font color="#AABBDD">Bắt buộc</font></small>');
!defined('ENTRY_FAX_NUMBER') && define('ENTRY_FAX_NUMBER', 'Số fax:');
define('ENTRY_FAX_NUMBER_ERROR', '');
define('ENTRY_FAX_NUMBER_TEXT', '');
!defined('ENTRY_NEWSLETTER') && define('ENTRY_NEWSLETTER', 'mail magazine:');
define('ENTRY_NEWSLETTER_TEXT', '');
!defined('ENTRY_NEWSLETTER_YES') && define('ENTRY_NEWSLETTER_YES', 'Đặt mua báo');
!defined('ENTRY_NEWSLETTER_NO')  && define('ENTRY_NEWSLETTER_NO', 'Không đặt mua báo');
define('ENTRY_NEWSLETTER_ERROR', '');
!defined('ENTRY_PASSWORD')              && define('ENTRY_PASSWORD', 'Mật khẩu:');
!defined('ENTRY_PASSWORD_CONFIRMATION') && define('ENTRY_PASSWORD_CONFIRMATION', 'Nhập lại mật khẩu:');
define('ENTRY_PASSWORD_CONFIRMATION_TEXT', '&nbsp;<small><font color="#AABBDD">Bắt buộc</font></small>');
define('ENTRY_PASSWORD_ERROR', '&nbsp;<small><font color="#FF0000">tối thiểu ' . ENTRY_PASSWORD_MIN_LENGTH . ' Kí tự</font></small>');
define('ENTRY_PASSWORD_TEXT', '&nbsp;<small><font color="#AABBDD">Bắt buộc</font></small>');
!defined('PASSWORD_HIDDEN')   && define('PASSWORD_HIDDEN', '********');

// pull down default text
!defined('PULL_DOWN_DEFAULT') && define('PULL_DOWN_DEFAULT', 'Xin vui lòng chọn');
!defined('TYPE_BELOW')        && define('TYPE_BELOW', 'Nhập vào bên dưới');

// add create_order
define('ENTRY_CUSTOMERS_ID', 'ID khách hàng:');
define('ENTRY_CUSTOMERS_ID_TEXT', '&nbsp;<small><font color="#AABBDD">Bắt buộc</font></small>');

?>
