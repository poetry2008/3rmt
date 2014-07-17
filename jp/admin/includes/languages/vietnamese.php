<?php
/*
  $Id$
*/

//
// mb_internal_encoding() is set for PHP-4.3.x(Zend Multibyte)
//
// A compatible module is loaded for environment without mbstring-extension
//
if (extension_loaded('mbstring')) {
  mb_internal_encoding('UTF-8'); // 指定内部代码
} else {
  include_once(DIR_WS_LANGUAGES . $language . '/jcode.phps');
  include_once(DIR_WS_LANGUAGES . $language . '/mbstring_wrapper.php');
}

// look in your $PATH_LOCALE/locale directory for available locales..
// on RedHat6.0 I used 'en_US'
// on FreeBSD 4.0 I use 'en_US.ISO_8859-1'
// this may not work under win32 environments..
setlocale(LC_TIME, 'ja_JP.UTF-8');
define('DATE_FORMAT_SHORT', '%Y/%m/%d');  // this is used for strftime()
define('DATE_FORMAT_LONG', '%Y年%B%e日 %A'); // this is used for strftime()
define('DATE_FORMAT', 'Y/m/d'); // this is used for date()
define('PHP_DATE_TIME_FORMAT', 'Y/m/d H:i:s'); // this is used for date()
define('DATE_TIME_FORMAT', DATE_FORMAT_SHORT . ' %H:%M:%S');
define('DATE_TIME_FORMAT_TORIHIKI', '%Y/%m/%d %H:%M');

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
define('CHARSET', 'UTF-8'); 

// page title
define('TITLE', STORE_NAME);  //请记述商店名。

// header text in includes/header.php
define('HEADER_TITLE_TOP', 'Top');
define('HEADER_TITLE_SUPPORT_SITE', 'Trang web hỗ trợ');
define('HEADER_TITLE_ONLINE_CATALOG', 'Danh bạ trực tuyến');
define('HEADER_TITLE_ADMINISTRATION', 'Menu quản lý');

// text for gender
define('MALE', 'Nam');
define('FEMALE', 'NỮ');

// text for date of birth example
define('DOB_FORMAT_STRING', 'yyyy-mm-dd');

// configuration box text in includes/boxes/configuration.php
define('BOX_HEADING_CONFIGURATION', 'Thiết lập cơ bản');
define('BOX_CONFIGURATION_MYSTORE', 'Shop');
define('BOX_CONFIGURATION_LOGGING', 'Log');
define('BOX_CONFIGURATION_CACHE', 'Cache');

// modules box text in includes/boxes/modules.php
define('BOX_HEADING_MODULES', 'Thiết lập module');
define('BOX_MODULES_PAYMENT', 'Module thanh toán');
define('BOX_MODULES_SHIPPING', 'Module giao hàng');
define('BOX_MODULES_ORDER_TOTAL', 'Module tổng cộng');
define('BOX_MODULES_METASEO', 'Meta SEO');

// categories box text in includes/boxes/catalog.php
define('BOX_HEADING_CATALOG', 'Quản lý danh mục');
define('BOX_CATALOG_CATEGORIES_PRODUCTS', 'Đăng kí sản phẩm/danh mục');
define('BOX_CATALOG_CATEGORIES_PRODUCTS_ATTRIBUTES', 'Đăng kí tùy chọn sản phẩm');
define('BOX_CATALOG_MANUFACTURERS', 'Đăng ký nhà sản xuất');
define('BOX_CATALOG_REVIEWS', 'Quản lý review');
define('BOX_CATALOG_SPECIALS', 'Đăng ký sản phẩm ưu đãi giá');
define('BOX_CATALOG_PRODUCTS_EXPECTED', 'Quản lý sản phẩm dự định nhập hàng');

// customers box text in includes/boxes/customers.php
define('BOX_HEADING_CUSTOMERS', 'Quản lý khách hàng');
define('BOX_CUSTOMERS_CUSTOMERS', 'Quản lý khách hàng');
define('BOX_CUSTOMERS_ORDERS', 'Quản lý đơn hàng');

// taxes box text in includes/boxes/taxes.php
define('BOX_HEADING_LOCATION_AND_TAXES', 'Thiết lập khu vực/thuế suất');
define('BOX_TAXES_COUNTRIES', 'Thiết lập tên nước');
define('BOX_TAXES_ZONES', 'Thiết lập khu vực');
define('BOX_TAXES_GEO_ZONES', 'Thiết lập thuế khu vực ');
define('BOX_TAXES_TAX_CLASSES', 'Thiết lập phân loại thuế');
define('BOX_TAXES_TAX_RATES', 'Thiết lập thuế suất');

// reports box text in includes/boxes/reports.php
define('BOX_HEADING_REPORTS', 'Báo cáo');
define('BOX_REPORTS_PRODUCTS_VIEWED', 'Số lần xem từng sản phẩm');
define('BOX_REPORTS_PRODUCTS_PURCHASED', 'Số lần bán từng sản phẩm');
define('BOX_REPORTS_ORDERS_TOTAL', 'Thứ tự doanh thu của từng khách hàng');
define('BOX_REPORTS_SALES_REPORT', 'Quản lý doanh thu');
define('BOX_REPORTS_NEW_CUSTOMERS', 'Khách hàng mới');
define('BOX_REPORTS_ASSETS', 'Quản lý tài sản');

// tools text in includes/boxes/tools.php
define('BOX_HEADING_TOOLS', 'Các loại công cụ');
define('BOX_TOOLS_BACKUP', 'Quản lý backup cơ sở dữ liệu');
define('BOX_TOOLS_SEARCH', 'Tìm kiếm');
define('BOX_TOOLS_BANNER_MANAGER', 'Quản lý banner');
define('BOX_TOOLS_CACHE', 'Kiểm soát cache');
define('BOX_TOOLS_DEFINE_LANGUAGE', 'Quản lý file ngôn ngữ');
define('BOX_TOOLS_FILE_MANAGER', 'Quản lý file');
define('BOX_TOOLS_MAIL', 'Gửi E-mail');
define('BOX_TOOLS_NEWSLETTER_MANAGER', 'Quản lý Mail magazine');
define('BOX_TOOLS_MAIL_TEMPLATES', 'Thiết lập mẫu Mail');
define('BOX_TOOLS_SERVER_INFO', 'Thông tin server');
define('BOX_TOOLS_WHOS_ONLINE', 'Người dùng trực tuyến');
define('BOX_TOOLS_PRESENT','Chức năng tặng quà');

// localizaion box text in includes/boxes/localization.php
define('BOX_HEADING_LOCALIZATION', 'Nội địa hóa');
define('BOX_LOCALIZATION_CURRENCIES', 'Thiết lập tiền tệ');
define('BOX_LOCALIZATION_LANGUAGES', 'Thiệt lập ngôn ngữ');
define('BOX_LOCALIZATION_ORDERS_STATUS', 'Thiết lập Status đặt hàng');

// javascript messages
define('JS_ERROR', 'Đã xảy ra lỗi trong quá trình xử lý form!\nXin tiến hành chỉnh sửa bên dưới:\n\n');

define('JS_OPTIONS_VALUE_PRICE', '*Xin chỉ định giá của thuộc tính sản phẩm mới.\n');
define('JS_OPTIONS_VALUE_PRICE_PREFIX', '* Hãy quy định tiền tố giá cả thuộc tính sản phẩm mới.\n');

define('JS_PRODUCTS_NAME', '* Hãy quy định tên sản phẩm mới.\n');
define('JS_PRODUCTS_DESCRIPTION', '* Vui lòng nhập câu mô tả sản phẩm mới.\n');
define('JS_PRODUCTS_PRICE', '* Hãy quy định giá cả sản phẩm mới.\n');
define('JS_PRODUCTS_WEIGHT', '* Hãy quy định trọng lượng sản phẩm mới.\n');
define('JS_PRODUCTS_QUANTITY', '* Hãy quy định số lượng sản phẩm mới.\n');
define('JS_PRODUCTS_MODEL', '* Hãy quy định số mẫu sản phẩm mới.\n');
define('JS_PRODUCTS_IMAGE', '* Hãy quy định hình ảnh sản phẩm mới.\n');

define('JS_SPECIALS_PRODUCTS_PRICE', '* Hãy quy định giá mới của sản phẩm.\n');

define('JS_GENDER', '* \'Giới tính\' Không thể chọn.\n');
define('JS_FIRST_NAME', '* \'Tên\' tối thiểu ' . ENTRY_FIRST_NAME_MIN_LENGTH . ' Cần thêm kí tự.\n');
define('JS_LAST_NAME', '* \'Họ\' tối thiểu' . ENTRY_LAST_NAME_MIN_LENGTH . ' Cần thêm kí tự.\n');

define('JS_FIRST_NAME_F', '* \'Tên(Phiên âm)\' tối thiểu ' . ENTRY_FIRST_NAME_MIN_LENGTH . ' Cần thêm kí tự.\n');
define('JS_LAST_NAME_F', '* \'Họ(Phiên âm)\' tối thiểu ' . ENTRY_LAST_NAME_MIN_LENGTH . ' Cần thêm kí tự.\n');

define('JS_DOB', '* \'Ngày tháng năm sinh\' nhập theo hình thức sau đây: xxxx/xx/xx (Năm/tháng/ngày)。\n');
define('JS_EMAIL_ADDRESS', '* \'Địa chỉ E-Mail\' tối thiểu ' . ENTRY_EMAIL_ADDRESS_MIN_LENGTH . ' Cần thêm kí tự.\n');
define('JS_EMAIL_ADDRESS_MATCH_ERROR','*  Địa chỉ email đã nhập không chính xác!');
define('JS_ADDRESS', '* \'Địa điểm１\' tối thiểu ' . ENTRY_STREET_ADDRESS_MIN_LENGTH . ' Cần thêm kí tự.\n');
define('JS_POST_CODE', '* \'Mã bưu điện\' tối thiểu ' . ENTRY_POSTCODE_MIN_LENGTH . ' Cần thêm kí tự.\n');
define('JS_CITY', '* \'Quận huyện thành phố\' tối thiểu ' . ENTRY_CITY_MIN_LENGTH . ' Cần thêm kí tự.\n');
define('JS_STATE', '* \'Tỉnh thành\' không được chọn.\n');
define('JS_STATE_SELECT', '-- Chọn từ trên --');
define('JS_ZONE', '* \'Tỉnh thành\' vui lòng chọn từ danh sách.');
define('JS_COUNTRY', '* \'Nước\' vui lòng chọn.\n');
define('JS_TELEPHONE', '* \'Số điện thoại\' tối thiểu ' . ENTRY_TELEPHONE_MIN_LENGTH . ' Cần thêm kí tự.\n');
define('JS_PASSWORD', '* \'Mật khẩu\' và \'Nhập lại mật khẩu\' はtối thiểu ' . ENTRY_PASSWORD_MIN_LENGTH . ' Cần thêm kí tự.\n');

define('JS_ORDER_DOES_NOT_EXIST', 'Không tồn tại số đơn hàng  %s !');

define('CATEGORY_PERSONAL', 'Thông tin cá nhân');
define('CATEGORY_ADDRESS', 'Địa chỉ');
define('CATEGORY_CONTACT', 'Nơi liên hệ');
define('CATEGORY_COMPANY', 'Tên công ty');
define('CATEGORY_PASSWORD', 'Mật khẩu');
define('CATEGORY_OPTIONS', 'Tùy chọn');
define('ENTRY_GENDER', 'Giới tính:');
define('ENTRY_FIRST_NAME', 'Tên');
define('ENTRY_LAST_NAME', 'Họ');
//add
define('TEXT_ADDRESS','Địa chỉ');
define('TEXT_CLEAR','Xóa');
define('TABLE_OPTION_NEW','Gởi đến nơi đăng kí');
define('TABLE_OPTION_OLD','Xác định nơi gởi trong quá khứ'); 
define('TABLE_ADDRESS_SHOW','Chọn từ danh sách nơi gởi:');
define('ENTRY_FIRST_NAME_F', 'Tên(phiên âm):');
define('ENTRY_LAST_NAME_F', 'Họ(phiên âm):');
define('ENTRY_DATE_OF_BIRTH', 'Ngày tháng năm sinh:');
define('ENTRY_EMAIL_ADDRESS', 'Địa chỉ Email');
define('ENTRY_QUITED_DATE','Thời gian rời hội:');
define('ENTRY_COMPANY', 'Tên công ty:');
define('ENTRY_STREET_ADDRESS', 'Địa điểm 1:');
define('ENTRY_SUBURB', 'Địa điểm 2:');
define('ENTRY_POST_CODE', 'Mã bưu điện:');
define('ENTRY_CITY', 'Quận huyện thành phố:');
define('ENTRY_STATE', 'Tỉnh thành:');
define('ENTRY_COUNTRY', 'Tên nước:');
define('ENTRY_TELEPHONE_NUMBER', 'Số điện thoại:');
define('ENTRY_FAX_NUMBER', 'Số FAX:');
define('ENTRY_NEWSLETTER', 'Mail magazine');
define('ENTRY_NEWSLETTER_YES', 'Mua tạp chí');
define('ENTRY_NEWSLETTER_NO', 'Không mua tạp chí');
define('ENTRY_PASSWORD', 'Mật khẩu:');
define('ENTRY_PASSWORD_CONFIRMATION', 'Nhập lại mật khẩu:');
define('PASSWORD_HIDDEN', '********');

// images
define('IMAGE_ANI_SEND_EMAIL', 'Gởi E-Mail');
define('IMAGE_BACK', 'Quay lại');
define('IMAGE_NEXT', 'Tiếp theo ');
define('IMAGE_BACKUP', 'Backup');
define('IMAGE_CANCEL', 'Hủy');
define('IMAGE_CONFIRM', 'Xác nhận');
define('IMAGE_CONFIRM_NEXT', 'Đi đến tiếp theo');
define('IMAGE_COPY', 'Sao chép');
define('IMAGE_COPY_TO', 'Nơi sao chép');
define('IMAGE_DEFINE', 'Định nghĩa');
define('IMAGE_DELETE', 'Xóa');
define('IMAGE_EDIT', 'Chỉnh sửa');
define('IMAGE_EMAIL', 'E-Mail');
define('IMAGE_FILE_MANAGER', 'Quản lý file');
define('IMAGE_ICON_STATUS_GREEN', 'Hữu hiệu');
define('IMAGE_ICON_STATUS_GREEN_LIGHT', 'Kích hoạt');
define('IMAGE_ICON_STATUS_RED', 'Vô hiệu');
define('IMAGE_ICON_STATUS_RED_LIGHT', 'Làm vô hiệu');
define('IMAGE_ICON_INFO', 'Thông tin');
define('IMAGE_INSERT', 'Chèn');
define('IMAGE_LOCK', 'Khóa');
define('IMAGE_MOVE', 'Dời');
define('IMAGE_NEW_PROJECT','Tạo mới');
define('IMAGE_NEW_CATEGORY', 'Danh mục mới');
define('IMAGE_NEW_COUNTRY', 'Tên nước mới');
define('IMAGE_NEW_CURRENCY', 'Tiền mới');
define('IMAGE_NEW_FILE', 'File mới');
define('IMAGE_NEW_FOLDER', 'Thư mục mới');
define('IMAGE_NEW_LANGUAGE', 'Ngôn ngữ mới');
define('IMAGE_NEW_PRODUCT', 'Sản phẩm mới');
define('IMAGE_NEW_TAX_CLASS', 'Phân loại thuế mới');
define('IMAGE_NEW_TAX_RATE', 'Thuế suất mới');
define('IMAGE_NEW_TAX_ZONE', 'Khu vực thuế mới');
define('IMAGE_NEW_ZONE', 'Khu vực mới');
define('IMAGE_NEW_TAG', '新标签'); 
define('IMAGE_ORDERS', 'Lich sử đặt hàng');
define('IMAGE_ORDERS_INVOICE', 'Hóa đơn giao hàng');
define('IMAGE_ORDERS_PACKINGSLIP', 'Phiếu giao hàng');
define('IMAGE_PREVIEW', 'Xem trước');
define('IMAGE_RESTORE', 'Phục hồi');
define('IMAGE_RESET', 'Reset');
define('IMAGE_SAVE', 'Lưu');
define('IMAGE_SEARCH', 'Tìm kiếm');
define('IMAGE_SELECT', 'Chọn');
define('IMAGE_SEND', 'Gởi');
define('IMAGE_SEND_EMAIL', 'Gởi E-Mail');
define('IMAGE_UNLOCK', 'Mở khóa');
define('IMAGE_UPDATE', 'Cập nhật');
define('IMAGE_UPDATE_CURRENCIES', 'Cập nhật tỉ giá hối đoái');
define('IMAGE_UPLOAD', 'Tải lên');
define('IMAGE_EFFECT', 'Hữu hiệu');
define('IMAGE_DEFFECT', 'Vô hiệu');

define('ICON_CROSS', 'Sai logic(False)');
define('ICON_CURRENT_FOLDER', 'Thư mục hiện tại');
define('ICON_DELETE', 'Xóa');
define('ICON_ERROR', 'Lỗi');
define('ICON_FILE', 'File');
define('ICON_FILE_DOWNLOAD', 'Tải');
define('ICON_FOLDER', 'Thư mục');
define('ICON_LOCKED', 'Khóa');
define('ICON_PREVIOUS_LEVEL', 'Level trước');
define('ICON_PREVIEW', 'Preview');
define('ICON_STATISTICS', 'Thống kê');
define('ICON_SUCCESS', 'Thành công');
define('ICON_TICK', 'Chính xác(True)');
define('ICON_UNLOCKED', 'Mở khóa');
define('ICON_WARNING', 'Cảnh báo');

// constants for use in tep_prev_next_display function
define('TEXT_RESULT_PAGE', ' %s / %d Page');
define('TEXT_DISPLAY_NUMBER_OF_USELESS_ITEM', '<b>%d</b> &sim; <b>%d</b> Hiển thị số thứ tự (<b>%d</b> Trong mục chưa sử dụng)');
define('TEXT_DISPLAY_NUMBER_OF_USELESS_OPTION', '<b>%d</b> &sim; <b>%d</b> Hiển thị số thứ tự (<b>%d</b> Trong mục chưa sử dụng)');
define('TEXT_DISPLAY_NUMBER_OF_OPTION_GROUP', '<b>%d</b> &sim; <b>%d</b> Hiển thị số thứ tự (<b>%d</b> Trong mục chưa sử dụng)');
define('TEXT_DISPLAY_NUMBER_OF_OPTION_ITEM', '<b>%d</b> &sim; <b>%d</b> Hiển thị số thứ tự (<b>%d</b> Trong mục)');
define('TEXT_DISPLAY_NUMBER_OF_ADDRESS', '<b>%d</b> &sim; <b>%d</b> Hiển thị số thứ tự (<b>%d</b> Trong mục)');
define('TEXT_DISPLAY_NUMBER_OF_COUNTRY_FEE', '<b>%d</b> &sim; <b>%d</b> Hiển thị số thứ tự (<b>%d</b> Trong tên nước)');
define('TEXT_DISPLAY_NUMBER_OF_COUNTRY_AREA', '<b>%d</b> &sim; <b>%d</b> Hiển thị số thứ tự(<b>%d</b> Trong khu vực)');
define('TEXT_DISPLAY_NUMBER_OF_COUNTRY_CITY', '<b>%d</b> &sim; <b>%d</b> Hiển thị số thứ tự (<b>%d</b> Trong các tỉnh thành)');
define('TEXT_DISPLAY_NUMBER_OF_SHIPPING_TIME', '<b>%d</b> &sim; <b>%d</b> Hiển thị số thứ tự (<b>%d</b> Trong mục');
define('TEXT_DISPLAY_NUMBER_OF_PREORDERS_STATUS', '<b>%d</b> &sim; <b>%d</b> Hiển thị số thứ tự  (<b>%d</b> Trong tình trạng đặt hàng trước)');
define('TEXT_DISPLAY_NUMBER_OF_PREORDERS', '<b>%d</b> &sim; <b>%d</b> Hiển thị số thứ tự (<b>%d</b> Trong đặt hàng trước)');
define('TEXT_DISPLAY_NUMBER_OF_LATEST_NEWS', '<b>%d</b> &sim; <b>%d</b> Hiển thị số thứ tự (<b>%d</b> Trong số các thông tin mới nhất)');
define('TEXT_DISPLAY_NUMBER_OF_CAMPAIGN', '<b>%d</b> &sim; <b>%d</b> Hiển thị số thứ tự (<b>%d</b> Trong các mã chiến dịch)');
define('TEXT_DISPLAY_NUMBER_OF_HELP_INFO', '<b>%d</b> &sim; <b>%d</b> Hiển thị số thứ tự (<b>%d</b> Trong các nội dung)');
define('TEXT_DISPLAY_NUMBER_OF_CATEGORIES', '<b>%d</b> &sim; <b>%d</b> Hiển thị số thứ tự (<b>%d</b> Trong các sản phẩm)');
define('TEXT_DISPLAY_NUMBER_OF_BANNERS', '<b>%d</b> &sim; <b>%d</b> Hiển thị số thứ tự (<b>%d</b> Trong các Banner)');
define('TEXT_DISPLAY_NUMBER_OF_COUNTRIES', '<b>%d</b> &sim; <b>%d</b> Hiển thị số thứ tự (<b>%d</b> Trong các nước)');
define('TEXT_DISPLAY_NUMBER_OF_CUSTOMERS', '<b>%d</b> &sim; <b>%d</b> Hiển thị số thứ tự (<b>%d</b> Trong các khách hàng)');
define('TEXT_DISPLAY_NUMBER_OF_SEARCH', '<b>%d</b> &sim; <b>%d</b> Hiển thị số thứ tự (<b>%d</b> Trong số thông tin khách hàng)');
define('TEXT_DISPLAY_NUMBER_OF_FAQ', '<b>%d</b> &sim; <b>%d</b> Hiển thị số thứ tự (<b>%d</b> Trong số FAQ/danh mục)');
define('TEXT_DISPLAY_NUMBER_OF_CURRENCIES', '<b>%d</b> &sim; <b>%d</b> Hiển thị số thứ tự (<b>%d</b> Trong số tiền tệ )');
define('TEXT_DISPLAY_NUMBER_OF_LANGUAGES', '<b>%d</b> &sim; <b>%d</b> Hiển thị số thứ tự (<b>%d</b> Trong các ngôn ngữ)');
define('TEXT_DISPLAY_NUMBER_OF_MANUFACTURERS', '<b>%d</b> &sim; <b>%d</b> Hiển thị số thứ tự (<b>%d</b> Trong các nhà sản xuất)');
define('TEXT_DISPLAY_NUMBER_OF_NEWSLETTERS', '<b>%d</b> &sim; <b>%d</b> Hiển thị số thứ tự (<b>%d</b>Trong số mail magazine )');
define('TEXT_DISPLAY_NUMBER_OF_ORDERS', '<b>%d</b> &sim; <b>%d</b> Hiển thị số thứ tự (<b>%d</b> Trong số đơn hàng)');
define('TEXT_DISPLAY_NUMBER_OF_PW_MANAGERS', '<b>%d</b> &sim; <b>%d</b> Hiển thị số thứ tự(<b>%d</b> Trong các mật khẩu)');
define('TEXT_DISPLAY_NUMBER_OF_MAIL', '<b>%d</b> &sim; <b>%d</b> Hiển thị số thứ tự(<b>%d</b> Trong các Page)');
define('TEXT_DISPLAY_NUMBER_OF_PW_MANAGER_LOG', '<b>%d</b> &sim; <b>%d</b>Hiển thị số thứ tự(<b>%d</b> Trong lịch sử mật khẩu)');
define('TEXT_DISPLAY_NUMBER_OF_NIVENTORY', '<b>%d</b> &sim; <b>%d</b> Hiển thị số thứ tự ( Tổng hợp： <b>%d</b> )');
define('TEXT_DISPLAY_NUMBER_OF_ORDERS_STATUS', '<b>%d</b> &sim; <b>%d</b> Hiển thị số thứ tự (<b>%d</b> Trong tình trạng đặt hàng)');
define('TEXT_DISPLAY_NUMBER_OF_PRODUCTS', '<b>%d</b> &sim; <b>%d</b> Hiển thị số thứ tự (<b>%d</b> Trong các sản phẩm)');
define('TEXT_DISPLAY_NUMBER_OF_PRODUCTS_EXPECTED', '<b>%d</b> &sim; <b>%d</b> Hiển thị số thứ tự (<b>%d</b> Trong số sản phẩm dự định đặt hàng)');
define('TEXT_DISPLAY_NUMBER_OF_REVIEWS', '<b>%d</b> &sim; <b>%d</b> Hiển thị số thứ tự (<b>%d</b> Trong review sản phẩm)');
define('TEXT_DISPLAY_NUMBER_OF_SPECIALS', '<b>%d</b> &sim; <b>%d</b> Hiển thị số thứ tự (<b>%d</b> Trong sản phẩm ưu đãi)');
define('TEXT_DISPLAY_NUMBER_OF_TAX_CLASSES', '<b>%d</b> &sim; <b>%d</b> Hiển thị số thứ tự (<b>%d</b> Trong phân loại thuế)');
define('TEXT_DISPLAY_NUMBER_OF_TAX_ZONES', '<b>%d</b> &sim; <b>%d</b> Hiển thị số thứ tự (<b>%d</b> Trong khu vực thuế)');
define('TEXT_DISPLAY_NUMBER_OF_TAX_RATES', '<b>%d</b> &sim; <b>%d</b>Hiển thị số thứ tự (<b>%d</b> Trong thuế suất)');
define('TEXT_DISPLAY_NUMBER_OF_ZONES', '<b>%d</b> &sim; <b>%d</b> Hiển thị số thứ tự (<b>%d</b> Trong khu vực)');

define('PREVNEXT_BUTTON_PREV', '&lt;&lt;');
define('PREVNEXT_BUTTON_NEXT', '&gt;&gt;');
define('BUTTON_PREV', 'Về phía trước');
define('BUTTON_NEXT', 'Tiếp theo');
//define('PREVNEXT_BUTTON_PREV', 'Trang trước');
//define('PREVNEXT_BUTTON_NEXT', 'Trang kế');

define('PREVNEXT_TITLE_FIRST_PAGE', 'Trang đầu tiên');
define('PREVNEXT_TITLE_PREVIOUS_PAGE', 'Trang trước');
define('PREVNEXT_TITLE_NEXT_PAGE', 'Trang kế');
define('PREVNEXT_TITLE_LAST_PAGE', 'Trang cuối');
define('PREVNEXT_TITLE_PAGE_NO', 'Page %d');
define('PREVNEXT_TITLE_PREV_SET_OF_NO_PAGE', 'Trang %d trước');
define('PREVNEXT_TITLE_NEXT_SET_OF_NO_PAGE', 'Trang %d kế');
define('PREVNEXT_BUTTON_FIRST', '&lt;&lt;Đầu tiên');
define('PREVNEXT_BUTTON_LAST', 'Cuối cùng&gt;&gt;');

define('TEXT_DEFAULT', 'Mặc định');
define('TEXT_SET_DEFAULT', 'Thiết lập mặc định');
define('TEXT_FIELD_REQUIRED', '&nbsp;<span class="fieldRequired">* Bắt buộc</span>');

define('ERROR_NO_DEFAULT_CURRENCY_DEFINED', 'Lỗi: Tiền tệ cơ bản chưa được thiết lập.Menu quản lý ->Nội địa hóa->Thiết lập tiền tệ: Kiểm tra thiết lập ở.');

define('TEXT_CACHE_CATEGORIES', 'Box danh mục');
define('TEXT_CACHE_MANUFACTURERS', 'Box nhà sản xuất');
define('TEXT_CACHE_ALSO_PURCHASED', 'Modun sản phẩm liên quan');

define('TEXT_NONE', '--Không có--');
define('TEXT_TOP', 'Top');

define('EMAIL_SIGNATURE',C_EMAIL_FOOTER);

//Add languages
//------------------------
//contents
define('BOX_TOOLS_CONTENTS', 'Quản lý nội dung');
define('TEXT_DISPLAY_NUMBER_OF_CONTENS', '<b>%d</b> &sim; <b>%d</b> Hiển thị số thứ tự (<b>%d</b> Trong các nội dung)');

//latest news
define('BOX_TOOLS_LATEST_NEWS', 'Quản lý thông tin mới nhất');

//faq
define('BOX_TOOLS_FAQ', 'FAQ Manager');

//leftbox
define('BOX_CATALOG_PRODUCTS_UP', 'Tải dữ liệu sản phẩm mới lên');
define('BOX_CATALOG_PRODUCTS_DL', 'Tải dữ liệu sản phẩm mới xuống');
define('BOX_TOOLS_CL', 'Lịch');
define('BOX_CATALOG_PRODUCTS_TAGS', 'Đăng kí tag');
define('BOX_CATALOG_IMAGE_DOCUMENT', 'Quản lý file image');


define('TABLE_HEADING_SITE', 'Site');

define('IMAGE_BUTTON_BACK', '');
define('IMAGE_BUTTON_CONFIRM', '');
define('IMAGE_DETAILS', 'Chi tiết');

define('CATEGORY_SITE', 'Site thuộc về');
define('ENTRY_SITE', 'Site');
define('ENTRY_SITE_TEXT', 'Site thuộc về');

define('TEXT_IMAGE_NONEXISTENT', 'Không tồn tại hình ảnh');
define('SITE_ID_NOT_NULL', 'Vui lòng chọn site');
define('IMAGE_NEW_DOCUMENT_TYPE', '');
define('MSG_UPLOAD_IMG', '');
define('JS_ERROR_SUBMITTED', '');

define('BOX_CATALOG_COLORS', 'Đăng kí màu sản phẩm');
define('BOX_CATALOG_CATEGORIES_ADMIN', 'Quản lý giá sỉ sản phẩm');
//define('HEADING_TITLE', 'Quản lý giá sỉ sản phẩm');
define('HEADING_TITLE_SEARCH', 'Tìm kiếm');
define('HEADING_TITLE_GOTO', 'Chuyển đến');
define('TABLE_HEADING_ACTION', 'Thao tác');
define('TABLE_HEADING_STATUS', 'Status');
define('TABLE_HEADING_CATEGORIES_PRODUCTS', 'Sản phẩm/danh mục');
define('OROSHI_DATA_MANAGE','Dữ liệu');
define('BOX_ONCE_PWD_LOG','Log thao tác');
define('BANK_CL_TITLE_TEXT', 'Thiết lập lịch');
define('BANK_CL_COMMON_WORK_TIME', 'Kinh doanh thông thường');
define('BANK_CL_REST_TIME', 'Đóng cửa ngân hàng');
define('BANK_CL_SEND_MAIL', 'Đóng gởi mail');
define('HISTORY_TITLE_ONE', 'Đăng kí lịch sử các nhà đồng cung ứng');
define('HISTORY_TITLE_TWO', 'Hiển thị lịch sử các nhà cung ứng');
define('HISTORY_TITLE_THREE', 'Đăng kí lịch sử các nhà cung ứng');
define('KEYWORDS_TITLE_TEXT', 'Xếp hạng từ khóa');
define('KEYWORDS_SEARCH_START_TEXT', 'Ngày bắt đầu:');
define('KEYWORDS_SEARCH_END_TEXT', 'Ngày kết thúc:');
define('KEYWORDS_TABLE_COLUMN_ONE_TEXT', 'Từ khóa');
define('KEYWORDS_TABLE_COLUMN_TWO_TEXT', 'Số lượng đơn hàng');
define('KEYWORDS_TABLE_COLUMN_THREE_TEXT', 'Thứ tự');
define('LIST_DISPLAY_PRODUCT_SELECT', 'Chọn sản phẩm');
define('LIST_DISPLAY_JIAKONGZAIKU', 'Tồn kho hư cấu');
define('LIST_DISPLAY_YEZHE_PRICE', 'Đơn giá của nhà kinh doanh');
define('MAG_DL_TITLE_TEXT', 'Tải về dữ liệu người mua mail magazine');
define('MAG_UP_TITLE_TEXT', 'Tải lên hàng loạt người thuê bao mail magazine');
define('PRODUCTS_TO_TAGS_TITLE', 'Thiết lập liên quan đến tag');
define('REFERER_TITLE_TEXT', 'Thứ hạng truy cập');
define('REFERER_TITLE_URL', 'Nguồn đã truy cập');
define('REFERER_TITLE_NUM', 'Số lượng đơn hàng');
define('REFERER_TITLE_SORT_NUM', 'Thứ tự');
define('TELECOM_UNKNOW_TITLE', 'Quản lý thanh toán');
define('TELECOM_UNKNOW_SEARCH_SUCCESS', 'Thành công');
define('TELECOM_UNKNOW_SEARCH_FAIL', 'Thất bại');
define('TELECOM_UNKNOW_TABLE_CAL_METHOD', 'Phương pháp thanh toán');
define('TELECOM_UNKNOW_TABLE_TIME', 'Thời gian');
define('TELECOM_UNKNOW_TABLE_CAL', 'Thanh toán');
define('TELECOM_UNKNOW_TABLE_YIN', 'Trợ cấp');
define('TELECOM_UNKNOW_TABLE_SURNAME', 'Họ tên');
define('TELECOM_UNKNOW_TABLE_TEL', 'Điện thoại');
define('TELECOM_UNKNOW_TABLE_EMAIL', 'Địa chỉ mail');
define('TELECOM_UNKNOW_TABLE_PRICE', 'Số tiền');
define('TELECOM_UNKNOW_SELECT_NOTICE', 'Bạn có muốn ẩn dòng đã chọn？');
define('TELECOM_UNKNOW_TABLE_DISPLAY','ẩn hàng loạt');
define('CLEATE_DOUGYOUSYA_TITLE', 'Thiết lập tên các nhà đồng cung ứng');
define('CLEATE_DOUGYOUSYA_ADD_BUTTON', 'Thêm hình thức nhập vào');
define('CLEATE_DOUGYOUSYA_TONGYE', 'Đồng cung ứng：');
define('CLEATE_DOUGYOUSYA_EDIT', 'Chỉnh sủa');
define('CLEATE_DOUGYOUSYA_DEL', 'Xóa');
define('CLEATE_DOUGYOUSYA_HISTORY', 'Lịch sử');
define('CLEATE_DOUGYOUSYA_LOGIN', 'Đăng kí nhà đồng cung ứng');
define('CLEATE_DOUGYOUSYA_UPDATE_SORT', 'Cập nhật trật tự');
define('CLEATE_LIST_TITLE', 'Đăng kí dữ liệu nhà phân phối');
define('CLEATE_LIST_SETNAME_BUTTON', 'Thiết lập tên nhà phân phối');
define('CLEATE_LIST_LOGIN_BUTTON', 'Đăng kí nhà phân phối');
define('CUSTOMERS_CSVEXE_TITLE', 'Tải về dữ liệu khách hàng');
define('CUSTOMERS_CSVEXE_READ_TEXT', 'Trong quá trình tải về, sẽ thành tải nặng đối với máy chủ.Hãy chạy trong thời gian truy cập ngắn.');
define('YEAR_TEXT', '年');
define('MONTH_TEXT', '月');
define('DAY_TEXT', '日');
define('CUSTOMERS_CSV_EXE_NOTICE_TITLE', 'Thông tin dưới đây trong số các thông tin khách hàng sẽ được tải về dưới dạng file CSV.');
define('CUSTOMERS_CSV_EXE_READ_ONE', '<tr> <td width="20" align="center" class="infoBoxContent">&nbsp;</td> <td width="120" class="menuBoxHeading">Mục</td> <td class="menuBoxHeading">Mô tả</td> </tr> <tr> <td align="center" class="infoBoxContent">A</td> <td class="menuBoxHeading">Ngày tạo tài khoản</td> <td class="menuBoxHeading">Xuất ngày giờ đã tạo tài khoản.（Hình thức：2005/11/11 10:15:32）</td> </tr> <tr> <td align="center" class="infoBoxContent">B</td> <td class="menuBoxHeading">Giới tính</td> <td class="menuBoxHeading">Xuất giới tính khách hàng "Nam"/"Nữ"</td> </tr> <tr> <td align="center" class="infoBoxContent">C</td> <td class="menuBoxHeading">Họ</td> <td class="menuBoxHeading">Xuất họ khách hàng</td> </tr> <tr> <td align="center" class="infoBoxContent">D</td> <td class="menuBoxHeading">Tên</td> <td class="menuBoxHeading">Xuất tên khách hàng</td> </tr> <tr> <td align="center" class="infoBoxContent">E</td> <td class="menuBoxHeading">Ngày tháng năm sinh</td> <td class="menuBoxHeading">Xuất ngày tháng năm sinh khách hàng（Hình thức：1999/11/11）</td> </tr> <tr> <td align="center" class="infoBoxContent">F</td> <td class="menuBoxHeading">Địa chỉ mail</td> <td class="menuBoxHeading">Xuất địa chỉ mail</td> </tr> <tr> <td align="center" class="infoBoxContent">G</td> <td class="menuBoxHeading">Tên công ty</td> <td class="menuBoxHeading">Nếu tên công ty đã được nhập vào sẽ xuất ra</td> </tr> <tr> <td align="center" class="infoBoxContent">H</td> <td class="menuBoxHeading">Mã bưu điện</td> <td class="menuBoxHeading">Xuất mã bưu điện.</td> </tr> <tr> <td align="center" class="infoBoxContent">I</td> <td class="menuBoxHeading">Tỉnh thành</td> <td class="menuBoxHeading">Xuất tên tỉnh thành（Ví dụ：Thành phốTokyo）</td> </tr> <tr> <td align="center" class="infoBoxContent">J</td> <td class="menuBoxHeading">Quận huyện thành phố</td> <td class="menuBoxHeading">Xuất tên quận huyện thành phố(Ví dụ：quận Minato）</td> </tr> <tr> <td align="center" class="infoBoxContent">K</td> <td class="menuBoxHeading">Địa chỉ 1</td> <td class="menuBoxHeading">Xuất địa chỉ nhà (công ty)（Ví dụ： Công viên SHIBA〇〇 ）</td> </tr> <tr> <td align="center" class="infoBoxContent">L</td> <td class="menuBoxHeading">Địa chỉ 2</td> <td class="menuBoxHeading">Nếu đã nhập tên tòa nhà/chung cư hãy xuất ra（Ví dụ：〇〇Tòa nhà5F）</td> </tr> <tr> <td align="center" class="infoBoxContent">M</td> <td class="menuBoxHeading">Tên nước</td> <td class="menuBoxHeading">Xuất tên nước（Japan,..）を出力します</td> </tr> <tr> <td align="center" class="infoBoxContent">N</td> <td class="menuBoxHeading">Số điện thoại</td> <td class="menuBoxHeading">Xuất số điện thoại</td> </tr> <tr> <td align="center" class="infoBoxContent">O</td>'); 

define('CUSTOMERS_CSV_EXE_READ_TWO', '<td class="menuBoxHeading">Số FAX</td> <td class="menuBoxHeading">Nếu đã nhập số FAX,sẽ xuất ra</td> </tr> <tr> <td align="center" class="infoBoxContent">P</td> <td class="menuBoxHeading">Mail magazine</td> <td class="menuBoxHeading">(chưa dịch)メールマガジンの行動区状況を出力します。<br> Trường hợp mua tạp chí：[mua]｜Trường hợp chưa mua tạp chí：[Chưa mua]</td> </tr> <tr> <td align="center" class="infoBoxContent">Q</td> <td class="menuBoxHeading">Điểm</td> <td class="menuBoxHeading">Xuất số điểm đang có hiện tại của khách hàng.</td> </tr>');
define('BOX_TOOLS_POINT_EMAIL_MANAGER','Thông báo điểm');
define('BOX_CAL_SITES_INFO_TEXT', 'Thống kê');

//catalog language
define('FILENAME_CLEATE_OROSHI_TEXT','Thiết lập tên nhà phân phối');
define('FILENAME_CLEATE_DOUGYOUSYA_TEXT','Thiết lập tên nhà đồng cung ứng');
define('FILENAME_CATEGORIES_ADMIN_TEXT','Quản lý giá bán sỉ sản phẩm');

//coustomers language
define('FILENAME_TELECOM_UNKNOW_TEXT','Quản lý thanh toán');
define('FILENAME_BILL_TEMPLATES_TEXT','Mẫu hóa đơn');

//reports language
define('FILENAME_REFERER_TEXT','Thứ hạng truy cập');
define('FILENAME_KEYWORDS_TEXT','Thứ hạng từ khóa');

//tools language 
define('FILENAME_BANK_CL_TEXT','Thiết lập lịch');
define('FILENAME_PW_MANAGER_TEXT','Quản lý ID');
define('FILENAME_BUTTONS_TEXT','Quản lý nút');
define('FILENAME_MAG_UP_TEXT','Đăng kí đồng loạt mail magazine');
define('FILENAME_MAG_DL_TEXT','DL dữ liệu mail magazine');

//header language
define('HEADER_TEXT_SITE_NAME',COMPANY_NAME);
define('HEADER_TEXT_LOGINED','Đang login ở.');
define('HEADER_TEXT_ORDERS','Danh sách đơn hàng');
define('HEADER_TEXT_TELECOM_UNKNOW','LỊch sử thanh toán');
define('HEADER_TEXT_TUTORIALS','▼Điều chỉnh sản phẩm');
define('HEADER_TEXT_CATEGORIES','Đăng kí sản phẩm');
define('HEADER_TEXT_LOGOUT','Logout');
define('HEADER_TEXT_REDIRECTURL','▼Dời đến site');
define('HEADER_TEXT_USERS','Đổi mật khẩu');
define('HEADER_TEXT_PW_MANAGER','Quản lý ID');
define('HEADER_TEXT_MANAGERMENU','▼Công cụ');
define('HEADER_TEXT_MICRO_LOG','Ghi chú tiếp theo');
define('HEADER_TEXT_LATEST_NEWS','Thông tin mới nhất');
define('HEADER_TEXT_CUSTOMERS','Danh sách khách hàng');
define('HEADER_TEXT_CREATE_ORDER2','Tạo mua vào');
define('HEADER_TEXT_CREATE_ORDER','Tạo đặt hàng');
define('HEADER_TEXT_ORDERMENU','Đơn đặt hàng▼');
define('HEADER_TEXT_INVENTORY','Cấp độ tồn kho');
define('HEADER_TEXT_CATEGORIES_ADMIN','Điều chỉnh giá cả');
//footer start 
define('TEXT_FOOTER_ONE_TIME','Nếu sử dụng một lần quyền hạn đã được kiểm tra, sẽ truy cập được');
define('TEXT_FOOTER_CHECK_SAVE','Lưu');
//footer end
define('RIGHT_ORDER_INFO_ORDER_FROM', 'Site đơn đặt hàng');
define('RIGHT_ORDER_INFO_ORDER_FETCH_TIME', 'Thời gian giao hàng');
define('RIGHT_ORDER_INFO_ORDER_OPTION', 'Tùy chọn：');
define('RIGHT_ORDER_INFO_ORDER_ID', 'Số đơn hàng');
define('RIGHT_ORDER_INFO_ORDER_DATE', 'Ngày đặt hàng');
define('RIGHT_ORDER_INFO_ORDER_CUSTOMER_TYPE', 'Phân loại khách hàng');
define('RIGHT_CUSTOMER_INFO_ORDER_IP', 'Địa chỉ IP：');
define('RIGHT_CUSTOMER_INFO_ORDER_HOST', 'Tên host：');
define('RIGHT_CUSTOMER_INFO_ORDER_USER_AGEMT', 'User Agent ：');
define('RIGHT_CUSTOMER_INFO_ORDER_OS', 'OS：');
define('RIGHT_CUSTOMER_INFO_ORDER_BROWSE_TYPE', 'Loại trình duyệt：');
define('RIGHT_CUSTOMER_INFO_ORDER_BROWSE_LAN', 'Ngôn ngữ trình duyệt：');
define('RIGHT_CUSTOMER_INFO_ORDER_COMPUTER_LAN', 'Môi trường ngôn ngữ máy tính：');
define('RIGHT_CUSTOMER_INFO_ORDER_USER_LAN', 'Môi trường ngôn ngữ người dùng：');
define('RIGHT_CUSTOMER_INFO_ORDER_PIXEL', 'Độ phân giải màn hình：');
define('RIGHT_CUSTOMER_INFO_ORDER_COLOR', 'Màu sắc màn hình：');
define('RIGHT_CUSTOMER_INFO_ORDER_FLASH', 'Flash：');
define('RIGHT_CUSTOMER_INFO_ORDER_FLASH_VERSION', 'Phiên bản Flash：');
define('RIGHT_CUSTOMER_INFO_ORDER_DIRECTOR', 'Director：');
define('RIGHT_CUSTOMER_INFO_ORDER_QUICK_TIME', 'Quick time：');
define('RIGHT_CUSTOMER_INFO_ORDER_REAL_PLAYER', 'Real player：');
define('RIGHT_CUSTOMER_INFO_ORDER_WINDOWS_MEDIA', 'Windows media：');
define('RIGHT_CUSTOMER_INFO_ORDER_PDF', 'Pdf：');
define('RIGHT_CUSTOMER_INFO_ORDER_JAVA', 'Java：');
define('RIGHT_TICKIT_ID_TITLE', 'Tạo mới số truy vấn');
define('RIGHT_TICKIT_EMAIL', 'Mail');
define('RIGHT_TICKIT_CARD', 'Thẻ tín dụng');
define('RIGHT_ORDER_INFO_ORDER_CUSTOMER_NAME', 'Tên khách hàng');
define('RIGHT_ORDER_INFO_ORDER_EMAIL', 'Địa chỉ E-Mail');
define('RIGHT_ORDER_INFO_ORDER_CREDITCARD_TYPE', 'Phân loại thẻ tín dụng：');
define('RIGHT_ORDER_INFO_ORDER_CREDITCARD_OWNER', 'Tên chủ sở hữu thẻ tín dụng：');
define('RIGHT_ORDER_INFO_ORDER_CREDITCARD_ID', 'Mã số thẻ：');
define('RIGHT_ORDER_INFO_ORDER_CREDITCARD_EXPIRE_TIME', 'Thời hạn hiệu lực của thẻ：');
define('RIGHT_ORDER_INFO_TRANS_NOTICE', 'Chú ý xử lý');
define('RIGHT_ORDER_INFO_TRANS_WAIT', 'Chờ giao dịch');
define('RIGHT_ORDER_INFO_INPUT_FINISH', 'Nhập xong');
define('RIGHT_ORDER_INFO_REPUTAION_SEARCH', 'Điều tra tín dụng：');
//user pama
define('TEXT_ECECUTE_PASSWORD_USER','Thay đổi mật khẩu');
define('RIGHT_ORDER_COMMENT_TITLE', 'Nhận xét');
define('BOX_LOCALIZATION_PREORDERS_STATUS', 'Thiết lập status đặt trước');
define('HEADER_TEXT_PREORDERS', 'Danh sách đặt hàng trước');


//order div 

define('TEXT_FUNCTION_ORDER_ORDER_DATE','Ngày giao dịch：');
define('TEXT_FUNCTION_HEADING_CUSTOMERS', 'Tên khách hàng');
define('TEXT_FUNCTION_HEADING_ORDER_TOTAL', 'Số tiền đặt hàng：');
define('TEXT_FUNCTION_HEADING_DATE_PURCHASED', 'Ngày đặt hàng');
define('TEXT_TEP_CFG_PAYMENT_CHECKBOX_OPTION_MEMBER','Hội viên');
define('TEXT_TEP_CFG_PAYMENT_CHECKBOX_OPTION_CUSTOMER','Guest');


define('TEXT_MONEY_SYMBOL','円');

define('FILENAME_ORDER_DOWNLOAD','Xuất dữ liệu đặt hàng');
define('FRONT_CONFIGURATION_TITLE_TEXT', 'Front end：');
define('ADMIN_CONFIGURATION_TITLE_TEXT', 'Back-end：');
define('FRONT_OR_ADMIN_CONFIGURATION_TITLE_TEXT', 'Front-end・
Back-end：');
define('HEADER_TEXT_ORDER_INFO', '▼Thông tin đặt hàng');


//note

define('TEXT_ADD_NOTE','Thêm ghi chú');
define('TEXT_COMMENT_NOTE','Nội dung');
define('TEXT_COLOR','Màu sắc ghi chú');
define('TEXT_TITLE_NOTE','Tiêu đề');
define('TEXT_ATTRIBUTE','Property');
define('TEXT_ATTRIBUTE_PUBLIC','Công khai');
define('TEXT_ATTRIBUTE_PRIVATE','Riêng tư');
define('HEADER_TEXT_CREATE_PREORDER', 'Tạo đặt trước');

define('TEXT_TORIHIKI_REPLACE_STR','～');
define('TEXT_TORIHIKI_HOUR_STR','時');
define('TEXT_TORIHIKI_MIN_STR','分');
define('TEXT_PREORDER_PAYMENT_METHOD', 'Phương thức thanh toán');
define('TEXT_PREORDER_NOT_COST', 'Chưa nhận tiền');
define('TEXT_PREORDER_COST_DATE', 'Ngày nhận tiền');
define('TEXT_PREORDER_PRODUCTS_NAME', 'Sản phẩm');
define('TEXT_PREORDER_PRODUCTS_NOENTRANCE', 'chưa'); 
define('TEXT_PREORDER_PRODUCTS_ENTRANCE', 'nhập');
define('TEXT_PREORDER_PRODUCTS_NUM', 'Số lượng');
define('TEXT_PREORDER_PRODUCTS_UNIT', 'Ko');




define('TEXT_PAYMENT_NULL_TXT','Vui lòng chọn phương pháp thanh toán');
define('TEXT_TORIHIKI_LIST_DEFAULT_TXT','Vui lòng chọn');
define('BOX_TOOLS_CAMPAIGN', 'Cài đặt mã chiến dịch');
define('TEXT_CURRENT_CHARACTER_NAME', 'Ghi chú ý trong mail：');
define('BOX_CATALOG_SHOW_USELESS_OPTION','Xóa tùy chọn chưa sử dụng');
define('TEXT_ORDER_ALARM_LINK', 'Báo động');
define('HOUR_TEXT', '時');
define('MINUTE_TEXT', '分');
define('NOTICE_EXTEND_TITLE', 'Ghi chú tiếp theo');
define('NOTICE_ALARM_TITLE', 'Báo động');
define('NOTICE_DIFF_TIME_TEXT', 'Còn lại');
define('TEXT_DISPLAY_NUMBER_OF_MANUAL', '<b>%d</b> &sim; <b>%d</b> Hiển thị số thứ tự (<b>%d</b> Trong số các đơn hàng)');
define('FILENAME_FILENAME_RESET_PWD_TEXT','Đặt lại mật khẩu hàng loạt');
define('FILENAME_CUSTOMERS_EXIT_TEXT','Quản lý khách hàng rời hội');
define('NEXT_ORDER_TEXT', 'Đặt hàng tiếp theo');
define('BEFORE_ORDER_TEXT', 'Đặt hàng trong quá khứ');
define('CUSTOMER_INFO_TEXT', 'Thông tin khách hàng');
define('BOX_CREATE_ADDRESS', 'Tạo địa chỉ');
define('BOX_COUNTRY_FEE', 'Thiết lập tiền phí');
define('BOX_SHIPPING_TIME', 'Thời gian gởi sản phẩm');
define('TEXT_REQUIRED', 'Bắt buộc');
define('TEXT_TIME_LINK', 'TỪ');
define('TEXT_DATE_MONDAY', 'Thứ hai');
define('TEXT_DATE_TUESDAY', 'Thứ ba');
define('TEXT_DATE_WEDNESDAY', 'Thứ tư');
define('TEXT_DATE_THURSDAY', 'Thứ năm');
define('TEXT_DATE_FRIDAY', 'Thứ sáu');
define('TEXT_DATE_STATURDAY', 'Thứ bảy');
define('TEXT_DATE_SUNDAY', 'Chủ nhật');
define('ERROR_INPUT_RIGHT_DATE', 'Vui lòng nhập ngày tháng chính xác.');
define('TEXT_BUTTON_ADD','Thêm form input');
define('TEXT_ONE_TIME_CONFIG_SAVE','Đã lưu');
define('TEXT_ONE_TIME_ERROR','Lỗi');
define('TEXT_ONE_TIME_CONFIRM','Không có kiểm tra. Hãy nhập kiểm tra');
define('TEXT_ONE_TIME_ADMIN_CONFIRM','Hãy nhập kiểm tra của Admin');
define('TEXT_SITE_COPYRIGHT' ,'Copyright © %s Haomai');

define('SECOND_TEXT','秒');
define('PAYMENT_METHOD','Phương pháp thanh toán');
define('DEPOSIT_STILL','Chưa nhận tiền');
define('PAYMENT_DAY','Ngày nhận tiền：');
define('PRODUCT','Sản phẩm');
define('INPUT','[Nhập]');
define('NOT','[Chưa]');
define('MANUAL','Manual');
define('NUMBERS','Số lượng：');
define('MONTHS','Ko');
define('OPTION','Tùy chọn:');

define('DB_CONFIGURATION_TITLE_SHOP','Thông tin shop');
define('DB_CONFIGURATION_TITLE_MIN','Gía trị tối thiểu');
define('DB_CONFIGURATION_TITLE_MAX','Gía trị tối đa');
define('DB_CONFIGURATION_TITLE_IMAGE_DISPLAY','Hiển thị hình ảnh');
define('DB_CONFIGURATION_TITLE_DISPLAY_ACCOUNT','Hiển thị tài khoản');
define('DB_CONFIGURATION_TITLE_MODULE_OPTIONS','Modun・Tùy chọn');
define('DB_CONFIGURATION_TITLE_CKING_DELIVERY','Giao hàng/Packing');
define('DB_CONFIGURATION_TITLE_PRODUCT_LIST','Hiển thị danh sách sản phẩm');
define('DB_CONFIGURATION_TITLE_INVENTORY_MANAGEMENT','Quản  lý tồn kho');
define('DB_CONFIGURATION_TITLE_RECORDING_LOG','Hiển thị log/đăng kí');
define('DB_CONFIGURATION_TITLE_PAGE_CACHE','Cache');
define('DB_CONFIGURATION_TITLE_EMAIL','Gởi E-Mail');
define('DB_CONFIGURATION_TITLE_DOWNLOAD_SALES','Bán hàng tải về');
define('DB_CONFIGURATION_TITLE_GZIP','Nén GZip');
define('DB_CONFIGURATION_TITLE_SESSION','Session');
define('DB_CONFIGURATION_TITLE_PROGRAM','Chương trình liên kết');
define('DB_CONFIGURATION_TITLE_INITIAL_SETTING_SHOP','Thiết lập khởi tạo shop');
define('DB_CONFIGURATION_TITLE_BUSINESS_CALENDAR','Lịch ngày kinh doanh');
define('DB_CONFIGURATION_TITLE_SEO','SEO URLs');
define('DB_CONFIGURATION_TITLE_DOCUMENTS','Trình quản lý tệp');
define('DB_CONFIGURATION_TITLE_TIME_SETING','Thiết lập thời gian');
define('DB_CONFIGURATION_TITLE_MAXIMUM_VALUE','Gía trị tối đa');
define('DB_CONFIGURATION_TITLE_NEW_REVIEW','Thiết lập mới nhất review');
define('DB_CONFIGURATION_TITLE_INSTALL_SAFETY_REVIEW','Giới hạn review');
define('DB_CONFIGURATION_TITLE_WARNING_SETTINGS','Thiết lập chuỗi kí tự cảnh báo');
define('DB_CONFIGURATION_TITLE_SIMPLE_INFORMATION','Thông tin đặt hàng đơn giản ');
define('DB_CONFIGURATION_TITLE_GRAPH_SET','Thiết lập đồ thị hỗn tạp');


define('DB_CONFIGURATION_DESCRIPTION_SHOP','THông tin thông thường của shop');
define('DB_CONFIGURATION_DESCRIPTION_MAX','Hàm số/ Gía trị tối thiểu của dữ liệu');
define('DB_CONFIGURATION_DESCRIPTION_MIN','Hàm số/ Gía trị tối đa của dữ liệu');
define('DB_CONFIGURATION_DESCRIPTION_IMAGE_DISPLAY','Thông số・HÌnh ảnh');
define('DB_CONFIGURATION_DESCRIPTION_DISPLAY_ACCOUNT','Thiết lập tài khoản khách hàng');
define('DB_CONFIGURATION_DESCRIPTION_MODULE_OPTIONS','Không hiển thị menu thiết lập');
define('DB_CONFIGURATION_DESCRIPTION_CKING_DELIVERY','Tùy chọn giao hàng được tiếp nhận ở shop');
define('DB_CONFIGURATION_DESCRIPTION_PRODUCT_LIST','Thiết lập danh sách sản phẩm');
define('DB_CONFIGURATION_DESCRIPTION_INVENTORY_MANAGEMENT','Thiết lập tồn kho');
define('DB_CONFIGURATION_DESCRIPTION_RECORDING_LOG','Thiết lập log');
define('DB_CONFIGURATION_DESCRIPTION_PAGE_CACHE','Thiết lập Cache');
define('DB_CONFIGURATION_DESCRIPTION_EMAIL','Thiết lập thông thường của mail HTML và gởi E-Mail');
define('DB_CONFIGURATION_DESCRIPTION_DOWNLOAD_SALES','Tùy chọn sản phẩm bán hàng tải về');
define('DB_CONFIGURATION_DESCRIPTION_GZIP','Tùy chọn nén GZip ');
define('DB_CONFIGURATION_DESCRIPTION_SESSION','Tùy chọn lên quan đến kiểm soát session');
define('DB_CONFIGURATION_DESCRIPTION_PROGRAM','Tùy chọn đối với các chương trình liên kết');
define('DB_CONFIGURATION_DESCRIPTION_INITIAL_SETTING_SHOP','Tiến hành thiết lập khởi tạo trang chủ');
define('DB_CONFIGURATION_DESCRIPTION_BUSINESS_CALENDAR','Tiến hành thiết lập lịch ngày kinh doanh');
define('DB_CONFIGURATION_DESCRIPTION_SEO','Options for Ultimate SEO URLs by Chemo');
define('DB_CONFIGURATION_DESCRIPTION_DOCUMENTS','Documents display options');
define('DB_CONFIGURATION_DESCRIPTION_TIME_SETING','Thiết lập đơn vị thời gian.Đơn vị toàn bộ là [giây]');
define('DB_CONFIGURATION_DESCRIPTION_MAXIMUM_VALUE','Tiến hành thiết lập gía trị tối đa（Gía trị giới hạn trên）');
define('DB_CONFIGURATION_DESCRIPTION_DEAL','Tạo danh sách phương pháp giao dịch');
define('DB_CONFIGURATION_DESCRIPTION_NEW_REVIEW','Thời hạn hiển thị NEW（Ngày）');
define('DB_CONFIGURATION_DESCRIPTION_INSTALL_SAFETY_REVIEW','Cài đặt an toàn review');
define('DB_CONFIGURATION_DESCRIPTION_WARNING_SETTINGS','Thiết lập chuỗi kí tự cảnh báo');
define('DB_CONFIGURATION_DESCRIPTION_SIMPLE_INFORMATION','Thông tin đặt hàng đơn giản');
define('DB_CONFIGURATION_DESCRIPTION_GRAPH_SET','Thiết lập đồ thị hỗn tạp');
define('DB_CONFIGURATION_DESCRIPTION_INITIAL_SETTING_SHOPS','Thiết lập đồ thị hỗn tạp');

define('TEXT_KEYWORD','Từ khóa');
define('TEXT_GOOGLE_SEARCH','là kết quả tiềm kiếm xem %s như là từ khóa trên GOOGLE');
define('TEXT_RENAME','Đổi tên');
define('TEXT_INFO_KEYWORD','Thay đổi từ khóa');
define('TEXT_NO_SET_KEYWORD','Không cài đặt từ khóa');
define('TEXT_NO_DATA','Đã không thể tìm thấy thông tin tương ứng');
define('TEXT_LAST_SEARCH_DATA','Từ cuối cùng có &nbsp;%s&nbsp; kết quả tìm kiếm');
define('TEXT_FIND_DATA_STOP','Đã tìm %, nhưng ngừng hiển thị');
define('TEXT_NOT_ENOUGH_DATA','Từ trước &nbsp;50&nbsp;Kết qur không trùng lặp ở kết quả tìm kiếm là &nbsp;%s&nbsp;Có đơn đặt hàng');
define('CLEATE_DOUGYOUSYA_ALERT', 'Đầu tiên hãy thêm input form');
define('BUTTON_MANUAL','Manual');
define('TEXT_JAVASCRIPT_ERROR','Thiết lập JavaScript hoặc Cookie không ở trạng thái On.Phiền bạn hãy chọn thiết lập [On] rồi sửu dụng.<br>※ Nếu thiết lập bị OFF, sẽ có những dịch vụ quý khách không thể sử dụng.');
define('HEADER_TEXT_PERSONAL_SETTING','Thiết lập cá nhân');
define('TEXT_FLAG_CHECKED','Kiểm tra xong');
define('TEXT_FLAG_UNCHECK','Chưa kiểm tra');

define('BOX_HEADING_USER', 'Người dùng');
define('BOX_USER_ADMIN', 'Quản lý người dùng');
define('BOX_USER_LOG', 'log người dùng');
define('BOX_USER_LOGOUT', 'Logout');
define('JUMP_PAGE_TEXT', 'Đến trang');
define('JUMP_PAGE_BUTTON_TEXT', 'Dời');
// javascript language
define('JS_TEXT_ONETIME_PWD_ERROR','Mật khẩu sai');
define('JS_TEXT_INPUT_ONETIME_PWD','Vui lòng nhập mật khẩu dùng một lần\r\n');
define('JS_TEXT_POSTAL_NUMBER_ERROR','Có lỗi ở mã bưu điện');
// cleate_list
define('TEXT_CLEATE_LIST','Đăng kí danh sách');
define('TEXT_CLEATE_HISTORY','Xem lịch sử');
// products_tags
define('TEXT_P_TAGS_NO_TAG','Không có dữ liệu tag, vui lòng thêm vào');
define('UPDATE_MSG_TEXT', 'Đã cập nhật.');
define('CL_TEXT_DATE_MONDAY', 'T2');
define('CL_TEXT_DATE_TUESDAY', 'T3');
define('CL_TEXT_DATE_WEDNESDAY', 'T4');
define('CL_TEXT_DATE_THURSDAY', 'T5');
define('CL_TEXT_DATE_FRIDAY', 'T6');
define('CL_TEXT_DATE_STATURDAY', 'T7');
define('CL_TEXT_DATE_SUNDAY', 'CN');
define('BUTTON_ADD_TEXT', 'Thêm vào');
define('CSV_HEADER_TEXT', 'Ngày tạo tài khoản,giới tín,họ,tên,ngày tháng năm sinh,địa chỉ mail,tên công ty,mã bưu điện,tinht thành,quận huyện thành phố,địa chỉ 1,địa chỉ 2,tên nước,số điện thoại,số FAX, mua mail magazine, điểm');
define('CSV_EXPORT_TEXT', 'Xuất CSV');
define('TEXT_ALL','Toàn bộ');
define('MESSAGE_FINISH_ORDER_TEXT', 'Thành công %s ID đặt hàng：Đã hoàn thành giao dịch');
define('TEXT_USER_ADDED','Người tạo');
define('TEXT_USER_UPDATE','Người cập nhật');
define('TEXT_DATE_ADDED','Ngày tạo');
define('TEXT_DATE_UPDATE','Ngày cập nhật');
define('TEXT_EOF_ERROR_MSG','Đã gởi dữ liệu thất bại.Vui lòng gởi lại lần nữa.');
define('TEXT_UNSET_DATA','Không có dữ liệu');
define('IMAGE_PREV', ' Về trước');
define('TEXT_POPUP_WINDOW_SHOW','Cũ');
define('TEXT_POPUP_WINDOW_EDIT','Mới');
define('SIGNAL_GREEN', 'xanh lá cây');
define('SIGNAL_YELLOW', 'Vàng');
define('SIGNAL_RED', 'Đỏ');
define('NOW_TIME_TEXT', 'Dựa vào thời điểm hiện tại ');
define('NOW_TIME_LINK_TEXT', 'Trước thời gian');
define('SIGNAL_BLNK', 'nhấp nháy');
define('SIGNAL_BLINK_READ_TEXT', 'Nếu vượt qua thời khắc điểm màu đỏ');
define('NOTICE_SET_WRONG_TIME', 'Cài đặt tín hiệu thời điểm cập nhật sản phẩm sai.');
define('TEXT_STATUS_MAIL_TITLE_CHANGED','Mail đã được gởi và status không đồng nhất. Bạn có muốn gởi?');
define('BOX_TOOLS_MARKS_MANAGER', 'Quản lý dấu hiệu');
define('TEXT_OPERATE_USER', 'Người thao tác');
define('TEXT_TIMEOUT_RELOGIN','Sự không thao tác đã vượt quá thời gian quy định, nên sẽ tự động logout. Hãy login lại.');
define('TEXT_PREORDER_ENSURE_DATE', 'Thời hạn bảo đảm');
define('TEXT_PREORDER_ID_NUM', 'Số đặt trước');
define('TEXT_PREORDER_DATE_TEXT', 'Ngày đặt trước');
define('NOTICE_LESS_PRODUCT_OPTION_TEXT', 'Nội dung đăng kí sản phẩm đã được cập nhật. Trường hợp muốn thay đổi sản phẩm này, hãy xóa đi và thêm vào lần nữa.');
define('NOTICE_LESS_PRODUCT_PRE_OPTION_TEXT', 'Nội dung đăng kí sản phẩm đã được cập nhật. Trường hợp muốn thay đổi sản phẩm này,cần phải tọa lại đặt hàng trước ngay từ đầu.');
define('NOTICE_COMPLETE_ERROR_TEXT','Có sự thiếu sót trong dữ liệu của. Vui lòng cập nhật trang và làm lại từ đầu.');
define('NOTICE_STOCK_ERROR_TEXT','Tình hình hiện nay không thống nhất với cơ sở dữ liệu.Thông tin được hiển thị đã cũ, nên sau khi cập nhật trang hãy nhập lại lần nữa.');
define('BUTTON_MAG_UP','Tải lên');
define('BUTTON_MAG_DL','Tải về');
define('REVIEWS_CHARACTER_TOTAL','Số kí tự review ');
define('TEXT_PRODUCTS_TAGS_ALL_CHECK','Chọn toàn bộ');
define('TEXT_PRODUCTS_TAGS_CHECK','Vui lòng chọn ít nhất 1 sản phẩm.');
define('TEXT_CHECK_FILE_EXISTS','File này đã tồn tại trước đó. Bạn có muốn ghi đè lên?');
define('TEXT_CHECK_FILE_EXISTS_DELETE','Đang sử dụng file này trong thiết lập khác.Bạn vẫn muốn xóa？');
define('TEXT_TRANSACTION_EXPIRED', 'Cảnh báo quên giao hàng');
define('TEXT_HEADER_NOTICE_ACCIDENT_HAPPEN', 'Phát sinh lỗi đường dây： ');
define('TEXT_HEADER_NOTICE_CLICK_CONFIRM_LINK', 'Ở đây');
define('TEXT_HEADER_NOTICE_CLICK_CONFIRM', 'Vui lòng click vào  ,và kiểm tra tình trạng.');
define('TEXT_HEADER_NOTICE_ACCIDENT_HAPPEN_FINAL_DAY', 'Ngày cuối cùng lỗi đường dây： ');
define('TEXT_HEADER_NOTICE_SYSTEM_CONDITION', 'Tình trạng hoạt động của hệ thống： Bình thường');
define('IMAGE_ICON_FRONTENT','Front end');
define('IMAGE_ICON_BACKEND','Back end');
define('FOREGROUND_TO_BACKGROUND','FE／BE');
define('PRIVILEGE_SET_TEXT', 'Thiết lập quyền hạn');
define('HEADER_TEXT_ALERT_LOG','Log báo động');
define('HEADER_TEXT_ALERT_TITLE','Nút liên hệ đặt hàng');
define('HEADER_TEXT_ALERT_TITLE_PREORDERS','Nút liên hệ đặt hàng trước');
define('HEADER_TEXT_ALERT_COMMENT','「${ALERT_TITLE}」');
define('HEADER_TEXT_ALERT_NUM','（khác${ALERT_NUM}số đơn hàng）');
define('TEXT_DISPLAY_NUMBER_OF_ALERT', '<b>%d</b> &sim; <b>%d</b> Hiển thị số thứ tự  (<b>%d</b> Trong số các log của)');
define('HEADER_TEXT_ALERT_LINK','là');
define('TEXT_DATA_IS_EMPTY','Không có dữ liệu.');
define('MODULE_ORDER_TOTAL_TAX_TITLE', 'Thuế tiêu thụ');
define('NOTICE_NO_ACCESS_TEXT', 'Không thể truy cập');
define('NOTICE_NO_ACCESS_READ_TEXT', 'Không có quyền, vui lòng liên hệ nhân viên quản lý.');
define('NOTICE_NO_ACCESS_LINK_TEXT', 'Hãy truy cập theo URL dưới đây.');
define('NOTICE_NO_ACCESS_BACK_TEXT', 'Quay lại trang trước');
define('TEXT_IMAGE_MAX','Hình ảnh được tải lên đã vượt quá giá trị tối đa.Vui lòng tải lên lần nữa,');
define('TEXT_IMAGE_TYPE_WRONG','Định dạng file không hợp lệ.');
define('WARNING_LOSING_INFO_TEXT', 'Vì dữ liệu không an toàn nên đã phát sinh ra lỗi.Hãy thao tác lại lần nữa.');
define('TEXT_ERROR_NULL','&nbsp;&nbsp;<font color=\'red\'>Vui lòng nhập lại.</font>');
define('TEXT_DATA_MANAGEMENT','Quản lí dữ liệu.');
define('TEXT_SORT_ASC','▲');
define('TEXT_SORT_DESC','▼');
define('TEXT_DATA_EMPTY','<font color=\'red\'><b>Không có dữ liệu</b></font>');
define('TEXT_CONTENTS_SELECT_ACTION','Mục đã chọn');
define('TEXT_CONTENTS_DELETE_ACTION', 'Xóa');
define('TABLE_HEADING_NUMBER','No.');
define('ERROR_VARIABLE_DATA_TEXT', 'Gía trị %s rỗng.');
define('TEXT_BILLING_ADDRESS','Nơi thanh toán');
define('DB_CONFIGURATION_TITLE_PRICE_SETTING', 'Thiết lập đơn giá sản phẩm');
define('TEXT_INPUT_IS_NO_NUMERIC', 'Vui lòng nhập chữ số lớn hơn 0');
define('ERROR_LOW_PROFIT_MESSAGE', 'Không thể bảo đảm %s lợi ích tối thiểu.Vui lòng nhập đơn giá bán của %s nhiều hơn %s.');
define('ERROR_LOW_PROFIT_OTHER_MESSAGE', 'Không thể đảm bảo %s lợi nhuận tối thiểu. Vui lòng thiết lập đơn giá nhà cung ứng %s theo %s dưới đây.');
define('ERROR_WARNING_TEXT', 'Bỏ qua cảnh báo này？');
define('JS_TEXT_ALL_ORDER_SAVED','Đã hoàn thành lưu.');
define('JS_TEXT_ALL_ORDER_COMPLETION_TRANSACTION','Hoàn thành giao dịch');
define('JS_TEXT_ALL_ORDER_SAVE','Lưu');
define('JS_TEXT_ALL_ORDER_BROWER_REJECTED','Đã bị trình duyệt từ chối.Nhập \'about:config\' vào cột địa chỉ trình duyệt và nhấn phím Enter.\nVà hãy đặt số \'signed.applets.codebase_principal_support\'thành \'true\.');
define('JS_TEXT_ALL_ORDER_NO_OPTION_ORDER','Vẫn chưa chọn đơn đặt hàng.');
define('JS_TEXT_ALL_ORDER_NOT_CHOOSE','Không thể chọn nhiều.');
define('JS_TEXT_ALL_ORDER_COPY_TO_CLIPBOARD','Đã sao chép vào clipboard.');
define('JS_TEXT_NOTICE_INPUT_TITLE','Vui lòng nhập tiêu đề.');
define('JS_TEXT_NOTICE_INPUT_INFO','Vui lòng nhập nội dung.');
define('JS_TEXT_NOTICE_INFO_SAVED','Đã lưu nội dung.');
define('JS_TEXT_NOTICE_IS_DELETE','Bạn có thực hiện xóa？');
define('ERROR_AVG_MESSAGE_PRODUCT','%s thấp hơn nguyên giá trung bình tồn kho hiện tại（%sYên）.<br> ');
define('ERROR_AVG_MESSAGE_END','<br>%s<br>Nếu có thể hiểu vấn đề gì đang xảy ra, vui lòng chọn check box bên trên và click [OK].<br><br>');
define('ERROR_AVG_MESSAGE_CHECKBOX_STR',' Lưu sau khi đã hiểu rủi ro.');
define('DIV_TEXT_CLEAR','Cancel');
define('DIV_TEXT_OK','OK');
define('DELETE_ALL_NOTICE','Xóa toàn bộ nội dung có được không?');
define('SMALL_IMAGE_WIDTH_TEST','60');
define('SMALL_IMAGE_HEIGHT_TEST','60');
define('TEXT_PAYMENT_NAME_ERROR','Phương thức thanh toán được sử dụng trong đơn đặt hàng này（#PAYMENT_METHOD）, đã được xóa khỏi hệ thống.。');
define('HAVE_MESSAGES','Có tin nhắn');
define('MESSAGES_PAGE_LINK_NAME','Quản lý tin nhắn');
define('DATE_FORMAT_TEXT','Y年m月d日 H時i分');
define('HEADER_TEXT_GROUPS','Nhóm text tiêu đề');
define('TEXT_PRODUCTS_PRICE_ERROR','Đơn giá là 0 Yên.');
define('TEXT_PRODUCTS_NUM_ERROR','Tổng số 0.');
define('TEXT_PRODUCTS_PAYMENT_ERROR','Số tiền tổng cộng đã ngoài số tiền có thể thanh toán(%s)của%s.');
