<?php

// 页面标题
define('HEADING_TITLE', 'Quản lý người dùng');

// 错误信息显示标题
define('TABLE_HEADING_ERRINFO', '!!!!! Thông báo lỗi !!!!!');

// 输入错误信息
define('TEXT_ERRINFO_INPUT_NOINPUT', 'Chưa nhập');
define('TEXT_ERRINFO_INPUT_ERR', 'Không được nhập chính xác');
define('TEXT_ERRINFO_INPUT_LENGTH', 'Vui lòng nhập hơn %d kí tự ');
define('TEXT_ERRINFO_USER_DELETE', '<b>Xóa thông tin người dùng</b>:Không thể xóa thông tin người dùng chính');
define('TEXT_ERRINFO_USER_GRANT', '<b>Cấp quyền</b>:Vui lòng chọn người dùng');
define('TEXT_ERRINFO_USER_REVOKE', '<b>Hủy bỏ quyền</b>:Vui lòng chọn người dùng');
define('TEXT_ERRINFO_USER_REVOKE_ONESELF', '<b>Hủy bỏ quyền</b>:Không thể hủy bỏ quyền người dùng chính');
define('TEXT_ERRINFO_CONFIRM_PASSWORD', '<b>Để kiểm tra hãy nhập lại</b>:Mật khẩu đã nhập lạ để kiểm tra sai');

// 访问表错误信息
define('TEXT_ERRINFO_DB_NO_USERINFO', 'Chưa lấy được thông tin người dùng');
define('TEXT_ERRINFO_DB_NO_USER', 'Không tồn tại người dùng là đối tượng');
define('TEXT_ERRINFO_DB_USERCHACK', 'Đã phát sinh lỗi trong trong quá trình kiểm tra sự tồn tại của người dùng');
define('TEXT_ERRINFO_DB_EXISTING_USER', 'Người dùng đã được đăng kí trước đó.');
define('TEXT_ERRINFO_DB_INSERT_USER', 'Không thể đăng kí thông tin người dùng.');
define('TEXT_ERRINFO_DB_INSERT_PERMISSION', 'Đã không thể đăng kí thông tin quyền hạn người dùng.');
define('TEXT_ERRINFO_DB_UPDATE_USER', 'Không thể cập nhật thông tin người dùng');
define('TEXT_ERRINFO_DB_DELETE_USER', 'Không thể xóa thông tin người dùng');
define('TEXT_ERRINFO_DB_CHANGE_PASSWORD', 'Không thể thay đổi mật khẩu');
define('TEXT_ERRINFO_DB_CHANGE_USER', 'Không thể thay đổi quyền người dùng');
define('TEXT_ERRINFO_DB_CHANGE_PERMISSION','Không thể thay đổi quyền người dùng');

// 完成信息
define('TEXT_SUCCESSINFO_INSERT_USER', 'Đã thêm người dùng');
define('TEXT_SUCCESSINFO_UPDATE_USER', 'Đã cập nhật thông tin người dùng');
define('TEXT_SUCCESSINFO_DELETE_USER', 'Đã xóa thông tin người dùng');
define('TEXT_SUCCESSINFO_CHANGE_PASSWORD', 'Đã hoàn thành thay đổi.');
define('TEXT_SUCCESSINFO_PERMISSION', 'Đã %s quyền người dùng ');
define('TEXT_SUCCESSINFO_CHANGE_PERMISSION','Đã thay đổi quyền');
// 页面标题
define('PAGE_TITLE_MENU_USER', 'Menu quản lý người dùng');
define('PAGE_TITLE_INSERT_USER', 'Thêm người dùng');
define('PAGE_TITLE_USERINFO', 'Thông tin người dùng');
define('PAGE_TITLE_PASSWORD', 'Thay đổi mật khẩu');
define('PAGE_TITLE_PERMISSION', 'Quyền hạn của người quản lý');
define('PAGE_TITLE_CHANGE_PERMISSION','Quản lý quyền hạn site');
// 按钮
define('BUTTON_BACK_MENU', 'Quay trở về menu quản lí người dùng');
define('BUTTON_INSERT_USER', 'Thêm người dùng');
define('BUTTON_INFO_USER', 'Thông tin người dùng');
define('BUTTON_CHANGE_PASSWORD', 'Thay đổi mật khẩu');
define('BUTTON_PERMISSION', 'Quyền hạn người quản lý');
define('BUTTON_INSERT', 'Thêm');
define('BUTTON_CLEAR', 'Clear');
define('BUTTON_UPDATE', 'Cập nhật');
define('BUTTON_DELETE', 'Xóa');
define('BUTTON_RESET', 'Quay trở về giá trị ban đầu');
define('BUTTON_CHANGE', 'Thay đổi');
define('BUTTON_GRANT', 'Cấp quyền >>');
define('BUTTON_REVOKE', '<< Xóa quyền');
define('BUTTON_BACK_PERMISSION', 'Trở về quyền người quản lý');
define('BUTTON_CHANGE_PERMISSION','Quyền site');
// 项目名称
define('TABLE_HEADING_COLUMN', 'Cột');
define('TABLE_HEADING_DATA', 'Dữ liệu');
define('TABLE_HEADING_USER', 'Người dùng');
define('TABLE_HEADING_USER_LIST', 'List người dùng');
define('TABLE_HEADING_USER_ID', 'ID người dùng');
define('TABLE_HEADING_PASSWORD', 'Mật khẩu');
define('TABLE_HEADING_NAME', 'Họ tên');
define('TABLE_HEADING_EMAIL', 'E-Mail');
define('TABLE_HEADING_NEW_PASSWORD', 'Mật khẩu mới');
define('TABLE_HEADING_CONFIRM_PASSWORD', 'Nhập lại để kiểm tra');
!defined('TABLE_HEADING_USER')&& define('TABLE_HEADING_USER', 'Người dùng thông thường');
define('TABLE_HEADING_ADMIN', 'Người quản lý site');

// JavaScript的确认信息
define('JAVA_SCRIPT_INFO_CHANGE', 'Thay đổi thông tin quản lý người dùng.\nCó được không？');
define('JAVA_SCRIPT_INFO_DELETE', 'Xóa thông tin quản lý người dùng.\nCó được không？');
define('JAVA_SCRIPT_INFO_PASSWORD', 'Thay đổi mật khẩu.\nCó được không？');
define('JAVA_SCRIPT_INFO_GRANT', 'Cấp quyền quản lý.\nCó được không？');
define('JAVA_SCRIPT_INFO_REVOKE', 'Xóa quyền người quản lý.\nCó được không？');
define('TABLE_HEADING_IP_LIMIT', 'Thiết lập giới hạn IP');
define('JAVA_SCRIPT_INFO_C_PERMISSION','Thay đổi quyền hạn quản lý người dùng.\nCó được không？');
define('TEXT_RAND_PWD_INFO','<p>Hình thức văn bản： Ví dụ ở 2011/2/22 01:00  Số chữ số:Công thức tính toán</p>
    <p>3:Y+n+d　＝Kết quả là 2011+2+22 bằng 2035.Giới hạn chữ số từ chỗ này sẽ là 3, nên mật khẩu sẽ trở thành 035.</p>
    <p>5:ddd　　＝Kết quả bằng 222222.Giới hạn chữ số từ chỗ này sẽ là 5, Mật khẩu là 22222.</p>
    <p>3:Y/n　　＝2011/2=1005.5 Kết quả bằng 1005.Giới hạn chữ số từ chỗ này sẽ trở thành 3, mật khẩu sẽ thành 100.</p>
    <p>4:(y+y)*2　＝(11+11)*2 Kết quả bằng 44.Giới hạn chữ số từ chỗ này sẽ trở thành 4, nên mật khẩu gắn thêm 0 sẽ là 0044.</p>
    <p>Công thức thanh toán có thể sử dụng：</p>
    <p>+　-　*　/　()</p>');
define('TEXT_LOGIN_COUNT','Số lần đăng nhập');
define('TEXT_RAND_PWD','Mật khẩu');
define('TEXT_RAND_RULES','Công thức tính toán');
define('TEXT_ERROR_RULE','Kiểu cách tính toán không đúng');

define('TABLE_HEADING_USER_STAFF', 'Staff');
define('TABLE_HEADING_USER_CHIEF', 'Chief');
define('TABLE_HEADING_USER_ADMIN', 'Admin');
define('JAVA_SCRIPT_INFO_STAFF2CHIEF', 'Cấp quyền hạn Chief.\nCó được không？');
define('JAVA_SCRIPT_INFO_CHIEF2STAFF', 'Xóa quyền hạn Chief.\n Có được không？');
define('JAVA_SCRIPT_INFO_CHIEF2ADMIN', 'Cấp quyền Admin\nCó được không？');
define('JAVA_SCRIPT_INFO_ADMIN2CHIEF', 'Xóa quyền Admin.\nCó được không？');
define('TEXT_ERRINFO_USER_STAFF', '<b>Cấp quyền hạn</b>:Vuilòng chọn Staff');
define('TEXT_ERRINFO_USER_CHIEF', '<b>Xóa quyền</b>:Vui lòng chọn Chief');
define('TEXT_ERRINFO_USER_ADMIN', '<b>Xóa quyền hạn</b>:Vui lòng chọn Admin');


define('JAVA_SCRIPT_ERRINFO_CONFIRM_PASSWORD','Mật khẩu mới và mật khẩu nhập lại để kiểm tra không thống nhất');
?>
