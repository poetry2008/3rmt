<?php
/*
  $Id$

*/

define('HEADING_TITLE', 'Database・Quản lý backup');

define('TABLE_HEADING_TITLE', 'Tiêu đề');
define('TABLE_HEADING_FILE_DATE', 'Ngày tháng');
define('TABLE_HEADING_FILE_SIZE', 'KÍch cỡ');
define('TABLE_HEADING_ACTION', 'Hành động');

define('TEXT_INFO_HEADING_NEW_BACKUP', 'Backup mới');
define('TEXT_INFO_HEADING_RESTORE_LOCAL', 'Khôi phục từ local file');
define('TEXT_INFO_NEW_BACKUP', 'Vui lòng không làm gián đoạn trong quá trình xử lý back up. Có trường hợp mất một vài phút cho quá trình xử lý.');
define('TEXT_INFO_UNPACK', '<br><br>(Thực hiện sau khi giải nén file )');
define('TEXT_INFO_RESTORE', 'Vui lòng không làm gián đoạn trong quá trình khôi phục.<br><br> Nếu kích thước file lớn, sẽ mất thời gian khôi phục.!<br><br>※ Nên khôi phục bằng cách sử dụng tính năng client MySQL<br><br>Ví dụ nhận xét:<br><b>mysql -h ' . DB_SERVER . ' -u ' . DB_SERVER_USERNAME . ' -p ' . DB_DATABASE . ' < %s </b> %s');
define('TEXT_INFO_RESTORE_LOCAL', 'Vui lòng không làm gián đoạn trong quá trình khôi phục.<br><br>Nếu kích thước file lớn, sẽ mất thời gian khôi phục.!');
define('TEXT_INFO_RESTORE_LOCAL_RAW_FILE', 'File có thể upload, chỉ là file văn bản SQL thuần túy.');
define('TEXT_INFO_DATE', 'Ngày tháng:');
define('TEXT_INFO_SIZE', 'Kích thước:');
define('TEXT_INFO_COMPRESSION', 'Phương pháp nén:');
define('TEXT_INFO_USE_GZIP', 'Lưu trong file nén GZIP');
define('TEXT_INFO_USE_ZIP', 'Lưu trong file nén ZIP');
define('TEXT_INFO_USE_NO_COMPRESSION', 'Lưu không nén(File SQL thuần túy)');
define('TEXT_INFO_DOWNLOAD_ONLY', 'Lưu download (Không để lại ở phía server)');
define('TEXT_INFO_BEST_THROUGH_HTTPS', 'Đề nghị thông qua kết nối HTTPS');
define('TEXT_DELETE_INTRO', 'Bạn có thật sự muốn xóa file backup này?');
define('TEXT_NO_EXTENSION', 'Không');
define('TEXT_BACKUP_DIRECTORY', 'Back up・Thư mục:');
define('TEXT_LAST_RESTORATION', 'Khôi phục cuối cùng:');
define('TEXT_FORGET', '(Đã lỡ quên mất)');

define('ERROR_BACKUP_DIRECTORY_DOES_NOT_EXIST', 'LỖi: Không tồn tại back up・thư mục.Vui lòng kiểm tra thiết lập của includes/configure.php');
define('ERROR_BACKUP_DIRECTORY_NOT_WRITEABLE', 'Lỗi: Không thể ghi vào thư mục・back up.');
define('ERROR_DOWNLOAD_LINK_NOT_ACCEPTABLE', 'Lỗi: Không được phép download.');
define('ERROR_FILE_NOT_REMOVEABLE', 'Lỗi: Không thể xóa file back up.Vui lòng kiểm tra quyền lợi  người dùng file.');

define('SUCCESS_LAST_RESTORE_CLEARED', 'Thành công: Dữ liệu khôi phục mới nhất đã được dọn dẹp.');
define('SUCCESS_DATABASE_SAVED', 'Thành công: Databse đã được lưu.');
define('SUCCESS_DATABASE_RESTORED', 'Thành công: Databse đã được khôi phục');
define('SUCCESS_BACKUP_DELETED', 'Thành công: File・back up đã được xóa bỏ.');
?>
