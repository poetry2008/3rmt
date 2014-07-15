<?php
/*
  $Id$

*/

define('HEADING_TITLE', 'Quản lý banner');

define('TABLE_HEADING_BANNERS', 'Banner');
define('TABLE_HEADING_GROUPS', 'Group');
define('TABLE_HEADING_STATISTICS', 'Hiển thị / Click');
define('TABLE_HEADING_STATUS', 'Status');
define('TABLE_HEADING_ACTION', 'Thao tác');

define('TEXT_BANNERS_TITLE', 'Banner・Tiêu đề:');
define('TEXT_BANNERS_URL', 'Banner URL:');
define('TEXT_BANNERS_GROUP', 'Banner・Group:');
define('TEXT_BANNERS_NEW_GROUP', ' Hoặc đăng kí Banner・Group mới');
define('TEXT_BANNERS_IMAGE', 'File hình ảnh:');
define('TEXT_BANNERS_IMAGE_LOCAL', ' Hoặc nhập tên file hình ảnh trên server vào bên dưới');
define('TEXT_BANNERS_IMAGE_TARGET', 'Nơi lưu hình ảnh:');
define('TEXT_BANNERS_HTML_TEXT', 'HTML Text:');
define('TEXT_BANNERS_EXPIRES_ON', 'Ngày kết thúc:');
define('TEXT_BANNERS_OR_AT', ' Hoặc');
define('TEXT_BANNERS_IMPRESSIONS', 'Số làn hiển thị');
define('TEXT_BANNERS_SCHEDULED_AT', 'Ngày bắt đầu:');
define('TEXT_BANNERS_BANNER_NOTE', 'Ở banner, sẽ sử dụng một trong hai hình ảnh hoặc  HTML text. Không thể sử dụng cả hai cái.<br>Ưu tiên HTML text hơn hình ảnh.');
define('TEXT_BANNERS_INSERT_NOTE', 'Đối với thư mục nơilưu hình ảnh Banner, Vui lòng đưa ra quyền ghi thích hợp. <br>Trường hợp không upload hình ảnh Banner lên web server 、&quot;Nơi lưu hình ảnh &quot; Vui lòng không nhập vào cột.(Trường hợp này sẽ sử dụng hình ảnh của phía Server)<br>&quot;Nơi lưu hình ảnh &quot; Trường hợp bố trí, Cần phải tạo trước thư mục sẽ lưu hoặc thư mục. Hơn nữa, dấu gạch chéo ở cuối thư mục rất quan trọng.(Ví dụ: banners/)');
define('TEXT_BANNERS_EXPIRCY_NOTE', 'Trong 2 cột nhập vào số lần hiển thị và ngày kết thúc, chỉ có một cột được đăng kí.<br>Trường hợp không làm kết thúc Banner một cách tự động, vui lòng giữ nguyên cột này ở trạng thái cột rỗng. ');
define('TEXT_BANNERS_SCHEDULE_NOTE', 'Nếu ngày bắt đầu được đăng kí, Banner sẽ có hiệu lực từ ngày tháng được đăng kí.<br>Banner được đăng kí ngày bắt đầu không được hiển thị cho đến khi đến ngày bắt đầu.');

define('TEXT_BANNERS_DATE_ADDED', 'Ngày đăng kí:');
define('TEXT_BANNERS_SCHEDULED_AT_DATE', 'Ngày bắt đầu : %s');
define('TEXT_BANNERS_EXPIRES_AT_DATE', 'Ngày kết thúc: %s');
define('TEXT_BANNERS_EXPIRES_AT_IMPRESSIONS', 'Ngày kết thúc: %s Số lần click');
define('TEXT_BANNERS_STATUS_CHANGE', 'Thay đổi status: %s');

define('TEXT_BANNERS_DATA', 'lần<br>số');
define('TEXT_BANNERS_LAST_3_DAYS', 'Trong 3 ngày gần nhất');
define('TEXT_BANNERS_BANNER_VIEWS', 'Hiển thi banner');
define('TEXT_BANNERS_BANNER_CLICKS', 'Banner・click');

define('TEXT_INFO_DELETE_INTRO', 'Bạn có thật sự muốn xóa banner này？');
define('TEXT_INFO_DELETE_IMAGE', 'Xóa luôn hình ảnh Banner');

define('SUCCESS_BANNER_INSERTED', "Thành công: Banner đã được chèn vào.");
define('SUCCESS_BANNER_UPDATED', "Thành công: Banner đã được cập nhật.");
define('SUCCESS_BANNER_REMOVED', "Thành công: Banner đã được xóa.");
define('SUCCESS_BANNER_STATUS_UPDATED', "Thành công: Status của Banner đã được cập nhật.");

define('ERROR_BANNER_TITLE_REQUIRED', "Lỗi: Cần tiêu đề Banner.");
define('ERROR_BANNER_GROUP_REQUIRED', "Lỗi: Cần nhóm Banner.");
define('ERROR_IMAGE_DIRECTORY_DOES_NOT_EXIST', "Lỗi: Thư mục nơi lưu trữ không tồn tại.");
define('ERROR_IMAGE_DIRECTORY_NOT_WRITEABLE', "Lỗi: Không thể ghi vào thư mục nơi lưu trữ: %s");
define('ERROR_IMAGE_DOES_NOT_EXIST', "Lỗi: Hình ảnh không tồn tại.");
define('ERROR_IMAGE_IS_NOT_WRITEABLE', "Lỗi: Không thể xóa hình ảnh.");
define('ERROR_UNKNOWN_STATUS_FLAG', "Lỗi: Status không rõ.");

define('ERROR_GRAPHS_DIRECTORY_DOES_NOT_EXIST', "Lỗi:  'graphs' Không tồn tại thu mục. 'images' Hãy vui lòng tạo thư mục 'graphs'vào trong thư mục.");
define('ERROR_GRAPHS_DIRECTORY_NOT_WRITEABLE', "Lỗi:  'images/graphs' Không thể ghi vào thư mục.Vui lòng thiết lập quyền lợi người dùng chính xác.");
define('TEXT_USER_ADDED','Người đăng kí');
define('TEXT_USER_UPDATE','Người cập nhất');
define('TEXT_DATE_ADDED','Nhày đăng kí');
define('TEXT_DATE_UPDATE','Ngày cập nhật');
define('TEXT_ADVERTISEMENT_INFO','Nếu sử dụng tính năng Banner, "hoặc là đăng kí Banner mới・Group" thì phần đầu đã đặt tên sẽ thành adv');
define('TEXT_REVIEWS_SELECT_ACTION','Mục đã chọn');
define('TEXT_REVIEWS_DELETE_ACTION', 'Xóa');
define('TEXT_NEWS_MUST_SELECT', 'Vui lòng chọn ít nhất một sự lựa chọn.');
define('TEXT_DEL_NEWS', 'Bạn có thật sự muốn xóa？');
define('BANNER_TITLE_ERROR','&nbsp;&nbsp;<font color=\'red\'>Vui lòng nhập Banner・Tiêu đề.</font>');
define('BANNER_URL_ERROR','&nbsp;&nbsp;<font color=\'red\'>Vui lòng nhập  URL Banner.</font>');
define('BANNER_GROUP_ERROR','&nbsp;&nbsp;<font color=\'red\'>Vui lòng nhập Banner・Group.</font>');
define('TEXT_CONTENTS','Nội dung');
?>
