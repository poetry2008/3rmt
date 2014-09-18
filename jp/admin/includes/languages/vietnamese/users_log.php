<?php
// 页面标题
define('HEADING_TITLE', 'Log người dùng');

// 访问表错误信息
define('TEXT_ERRINFO_DB_NO_LOGINFO', 'Đã không có được thông tin truy cập');

// 信息
define('TEXT_INFO_DELETE_DAY', 'Xóa thông tin truy cập');
define('TEXT_INFO_DELETE_FORMER_DAY', 'Dữ liệu của ngày hôm trước');
// Format: '(id1:val1,id2:val2)'
define('TEXT_INFO_STATUS_OUT', 'i:Đăng nhập,o:Thoát ra,t:Thời gian thoát ra,r:Hệ thống giới hạn IP');
define('TEXT_PAGE', '( %s / %s Page [ %s / %s Rows ] )');

// 按钮
define('BUTTON_DELETE_LOGINLOG', 'Xóa');
define('BUTTON_PREVIOUS_PAGE', 'Trang trước');
define('BUTTON_NEXT_PAGE', 'Trang kế tiếp');
define('BUTTON_JUMP_PAGE', 'Chuyển tiếp trang');

// 项目名称
define('TABLE_HEADING_LOGINID', 'ID');
define('TABLE_HEADING_LOGINTIME', 'Ngày giờ đăng nhập');
define('TABLE_HEADING_LAST_ACCESSTIME', 'Ngày giờ truy cập cuối');
define('TABLE_HEADING_USER', 'Người dùng');
define('TABLE_HEADING_STATUS', 'Status');
define('TABLE_HEADING_ADDRESS', 'Địa chỉ');
define('TABLE_HEADING_PAGE', 'Trang');

// JavaScript的确认信息
define('JAVA_SCRIPT_INFO_DELETE', 'Xóa log truy cập.\nBạn có đồng ý không？');

define('PAGE_TITLE_MENU_USER', '');
define('PAGE_TITLE_MENU_IP','Danh sách IP bị khóa');
define('TABLE_HEADING_PERMISSIONS','Quyền hạn người quản lý');
define('TABLE_HEADING_OPERATE','Thao tác');
define('TEXT_IP_UNLOCK','Mở khóa');
define('TEXT_DELETE_CONFIRM','Bạn có chắc chắn muốn mở khóa địa chỉ IP này không?？');
define('TEXT_CONFIRM_LOCK','Bạn có chắc chắn muốn khóa địa chỉ IP tương ứng không？');
define('TEXT_IP_UNLOCK_NOTES','<font color="#FF0000">※</font>&nbsp;Khi người Admin,Staff,Chief của quyền hạn quản lý trong cùng một IP bị khóa, hãy xóa Admin trước tiên.');
define('TEXT_LOGS_EDIT_SELECT','Mục đã chọn');
define('TEXT_LOGS_EDIT_DELETE','Xóa');
define('TEXT_LOGS_EDIT_MUST_SELECT','Hãy lựa chọn ít nhất 1 sự lựa chọn');
define('TEXT_LOGS_EDIT_CONFIRM','Bạn có chắc chắn muốn xóa không？');
define('TEXT_SORT_ASC','▲');
define('TEXT_SORT_DESC','▼');
define('TEXT_LOCK','Khóa');
?>
