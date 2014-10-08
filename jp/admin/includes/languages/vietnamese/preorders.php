<?php
/*
  $Id$ 
*/

define('HEADING_TITLE', 'Quản lí đặt hàng trước ');
define('HEADING_TITLE_SEARCH', 'ID đặt hàng:');
define('HEADING_TITLE_STATUS', 'Status:');

define('TEXT_PREORDERS_TRANSACTION_FINISH', 'Hoàn tất đặt trước');
define('TABLE_HEADING_COMMENTS', 'Nhận xét');
define('TABLE_HEADING_CUSTOMERS', 'Tên khách hàng');
define('TABLE_HEADING_ORDER_TOTAL', 'Số tiền đặt hàng');
define('TABLE_HEADING_DATE_PURCHASED', 'Ngày đặt hàng');
define('TABLE_HEADING_STATUS', 'Status');
define('TABLE_HEADING_ACTION', 'Thao tác');
define('TABLE_HEADING_QUANTITY', 'Số lượng');
define('TABLE_HEADING_PRODUCTS_MODEL', 'Số mẫu');
define('TABLE_HEADING_PRODUCTS', 'Số lượng / Tên sản phẩm');
define('TABLE_HEADING_TAX', 'Mức thuế');
define('TABLE_HEADING_TOTAL', 'Tổng cộng');
define('TABLE_HEADING_PRICE_EXCLUDING_TAX', 'Giá (không thuế)');
define('TABLE_HEADING_PRICE_INCLUDING_TAX', 'Giá (có thuế)');
define('TABLE_HEADING_TOTAL_EXCLUDING_TAX', 'Tổng cộng (không thuế)');
define('TABLE_HEADING_TOTAL_INCLUDING_TAX', 'Tổng cộng (có thuế)');
define('TEXT_PREORDER_AMOUNT_SEARCH','Tìm kiếm số tiền đặt hàng');


define('TABLE_HEADING_CUSTOMER_NOTIFIED', 'Thông báo cho khách hàng');
define('TABLE_HEADING_DATE_ADDED', 'Ngày xử lý');

define('ENTRY_CUSTOMER', 'Tên khách hàng:');
define('ENTRY_SOLD_TO', 'Tên người đặt hàng:');
!defined('ENTRY_STREET_ADDRESS') && define('ENTRY_STREET_ADDRESS', 'Địa chỉ số 1:');
!defined('ENTRY_SUBURB')          && define('ENTRY_SUBURB', 'Địa chỉ số 2:');
!defined('ENTRY_CITY')            && define('ENTRY_CITY', 'Quận huyện thành phố:');
!defined('ENTRY_POST_CODE')       && define('ENTRY_POST_CODE', 'Số bưu điện:');
!defined('ENTRY_STATE')           && define('ENTRY_STATE', 'Tỉnh thành:');
!defined('ENTRY_COUNTRY')         && define('ENTRY_COUNTRY', 'Tên nước:');
!defined('ENTRY_TELEPHONE')       && define('ENTRY_TELEPHONE', 'Số điện thoại:');
!defined('ENTRY_EMAIL_ADDRESS')   && define('ENTRY_EMAIL_ADDRESS', 'Địa chỉE-Mail :');
define('ENTRY_DELIVERY_TO', 'Địa chỉ giao hàng:');
define('ENTRY_SHIP_TO', 'Địa chỉ giao hàng:');
define('ENTRY_SHIPPING_ADDRESS', 'Địa chỉ giao hàng:');
define('ENTRY_BILLING_ADDRESS', 'Địa chỉthanh toán:');
define('ENTRY_PAYMENT_METHOD', 'Phương thức thanh toán:');
define('ENTRY_CREDIT_CARD_TYPE', 'Loại thẻ tín dụng:');
define('ENTRY_CREDIT_CARD_OWNER', 'Chủ thẻ tín dụng:');
define('ENTRY_CREDIT_CARD_NUMBER', 'Số thẻ tín dụng:');
define('ENTRY_CREDIT_CARD_EXPIRES', 'Thời han hiệu lực của thẻ tín dụng:');
define('ENTRY_SUB_TOTAL', 'Tổng phụ:');
define('ENTRY_TAX', 'Tiền thuế:');
define('ENTRY_SHIPPING', 'Giao hàng:');
define('ENTRY_TOTAL', 'Tổng cộng:');
define('ENTRY_DATE_PURCHASED', 'Ngày đặt hàng:');
define('ENTRY_STATUS', 'Status:');
define('ENTRY_DATE_LAST_UPDATED', 'Ngày cập nhập');
define('ENTRY_NOTIFY_CUSTOMER', 'Thông báo tình trạng xử lý:');
define('ENTRY_NOTIFY_COMMENTS', 'Thêm bình luận:');
define('ENTRY_PRINTABLE', 'In hóa đơn giao hàng');

define('TEXT_INFO_HEADING_DELETE_ORDER', 'Xóa đặt hàng');
define('TEXT_INFO_DELETE_INTRO', 'Có chắc xóa đơn đặt hàng này không?');
define('TEXT_INFO_RESTOCK_PRODUCT_QUANTITY', 'Trở lại số tồn kho hàng ban đầu'); // 'Restock product quantity'
define('TEXT_DATE_ORDER_CREATED', 'Ngày đặt trước:');
define('TEXT_DATE_ORDER_LAST_MODIFIED', 'Ngày cập nhập:');
define('TEXT_INFO_PAYMENT_METHOD', 'Phương thức thanh toán:');

define('TEXT_ALL_ORDERS', 'Tất cả các đơn đặt hàng');
define('TEXT_NO_ORDER_HISTORY', 'Không có lịch sử đặt hàng');

define('EMAIL_SEPARATOR', '--------------------------------------------------------------------------------');
define('EMAIL_TEXT_SUBJECT', 'Thông báo tình trạng tiếp nhận  đặt hàng');
define('EMAIL_TEXT_ORDER_NUMBER', 'Số tiếp nhận đặt hàng:');
define('EMAIL_TEXT_INVOICE_URL', 'Có thể xem  thông tin  đơn đặt hàng ở URL dưới đây .' . "\n");
define('EMAIL_TEXT_DATE_ORDERED', 'Ngày đăt hàng:');
define('EMAIL_TEXT_STATUS_UPDATE',
'Tình trạng tiếp nhận đơn đặt hàng sẽ theo như sau đây.' . "\n"
.'Tình trạng tiếp nhận hiện tại: [ %s ]' . "\n\n");
define('EMAIL_TEXT_COMMENTS_UPDATE', '[MỤc liên hệ]' . "\n%s");

define('ERROR_ORDER_DOES_NOT_EXIST', 'Lỗi:  Không tồn tại đơn đặt hàng');
define('SUCCESS_ORDER_UPDATED', 'Thành công:  Tình trạng đặt hàng đã được cập nhập.');
define('WARNING_ORDER_NOT_UPDATED', 'Thông báo: Trạng thái đặt hàng đã không thay đổi bất cứ điều gì.');

define('EMAIL_TEXT_STORE_CONFIRMATION', ' Chân thành cảm ơn bạn rất nhiều vì đã đặt hàng.' . "\n\n"
.'Chúng tôi xin được hướng dẫn về mục mục liên lạc và tình trạng tiếp nhận đơn hàng như sau.');
define('EMAIL_TEXT_STORE_CONFIRMATION_FOOTER', 
'Nếu có câu hỏi nào về tình trạng đặt hàng, vui lòng kiên hệ với của hàng của chúng tôi' . "\n"
.'để được giải đápt.' . "\n\n"
. EMAIL_SIGNATURE);

define('ENTRY_EMAIL_TITLE', 'Tiêu đề mail:');
define('TEXT_CODE_HANDLE_FEE', 'Lệ phí:');

// old oa 
define('TEXT_ORDER_ANSWER','Order Answer');
define('TEXT_BUY_BANK','Mua: thanh toán qua ngân hàng');
define('TEXT_SELL_BANK','Bán: Chuyển khoản qua ngân hàng');
define('TEXT_SELL_CARD','Bán: thẻ tín dụng');
define('TEXT_CREDIT_FIND','Điều tra tín dụng');

define('TEXT_ORDER_SAVE','Lưu');
define('TEXT_ORDER_TEST_TEXT','Trong khi vận  dụng thí nghiệm<font color="red">（Kiểm tra xem có  phù hợp với giá trị trên không）</font>Dùng coppy paste để mua:');
define('TEXT_MAIL_CONTENT_INFO',' Tự động xuống dòng và hiển thị, có chứa dòng mới trong mail đã được gởi.');
define('TEXT_ORDER_COPY','Dùng coppy paste:');
define('TEXT_ORDER_LOGIN','Tuy nhiên sẽ đăng nhập nhiều hơn bây giờ.');
define('TEXT_ORDER_SEND_MAIL','Gởi mail');
define('TEXT_ORDER_STATUS','Thông báo Status');
define('TEXT_ORDER_HAS_ERROR','Bạn đã tìm lỗi sai ?');
define('TEXT_ORDER_FIND','Tìm kiếm :');
define('TEXT_ORDER_FIND_SELECT','--------Hãy lựa chọn--------');
define('TEXT_ORDER_FIND_NAME','Tìm kiếm từ tên');
define('TEXT_ORDER_FIND','Tìm kiếm :');
define('TEXT_ORDER_FIND_PRODUCT_NAME','Tìm kiếm từ tên sản phẩm');
define('TEXT_ORDER_FIND_MAIL_ADD','Tìm kiếm từ địa chị mail');
define('TEXT_ORDER_QUERYER_NAME','Tên người xác nhận:');
define('TEXT_ORDER_OK_ORDER_NIMBER','Xong số tiếp nhận:');
define('TEXT_ORDER_BANK','Ngân hàng:');
define('TEXT_ORDER_JNB','JNB');
define('TEXT_ORDER_EBANK','eBank');
define('TEXT_ORDER_POST_BANK','yuucho');
define('TEXT_EDIT_MAIL_TEXT','Chỉnh sửa nguyên văn mail');
define('TEXT_SELECT_MORE','Bạn không thể chọn nhiều.');
define('TEXT_ORDER_SELECT','Đơn hàng vẫn chưa chọn.');
define('TEXT_ORDER_WAIT','Đợi giao dịch');
define('TEXT_ORDER_CARE','Chú ý xử lý');
define('TEXT_ORDER_OROSHI','Nhà phân phối');
define('TEXT_ORDER_CUSTOMER_INFO','Thông tin khách hàng');
define('TEXT_ORDER_HISTORY_ORDER','Đơn hàng trước đây');
define('TEXT_ORDER_NEXT_ORDER','Đơn hàng tiếp theo');
define('TEXT_ORDER_ORDER_DATE','Thời hạn hiệu lực');
define('TEXT_ORDER_CONVENIENCE','Thanh toán cửa hàng tiện lợi');
define('TEXT_ORDER_CREDIT_CARD','Thanh toán thẻ tín dụng');
define('TEXT_ORDER_POST','Ngân hàng yuucho（Bưu điện）');
define('TEXT_ORDER_BANK_REMIT_MONEY','Chuyển từ ngân hàng');
define('TEXT_ORDER_MIX','Trộn');
define('TEXT_ORDER_BUY','Mua');
define('TEXT_ORDER_SELL','Bán');
define('TEXT_ORDER_NOTICE','【Chú ý】');
define('TEXT_ORDER_AUTO_RUN_ON','Tính năng tải lại tự động đang bị vô hiệu hóa　→ ');
define('TEXT_ORDER_AUTO_POWER_OFF','Vô hiệu hóa');
define('TEXT_ORDER_AUTO_RUN_OFF','Tính năng tải lại tự động đang bị vô hiệu hóa　→ ');
define('TEXT_ORDER_AUTO_POWER_ON','Kích hoạt');
define('TEXT_ORDER_SHOW_LIST','Hiển thị danh sách');
define('TEXT_ORDER_STATUS_SET','Thiết lập Status đặt hàng');
define('TEXT_ORDER_CSV_OUTPUT','Xuất CVS');
define('TEXT_ORDER_DAY','日');
define('TEXT_ORDER_MONTH','月');
define('TEXT_ORDER_YEAR','年');
define('TEXT_ORDER_END_DATE','Ngày kết thúc:');
define('TEXT_ORDER_START_DATE','Ngày bắt đầu:');
define('TEXT_ORDER_SITE_TEXT','Site đặt hàng');
define('TEXT_ORDER_SERVER_BUSY','Trong khi tải về sẽ thành tải trọng nặng đối với server.');
define('TEXT_ORDER_DOWNLOPAD','Xuất dữ liệu đặt');

define('DEL_CONFIRM_PAYMENT_TIME', 'Xóa');
define('NOTICE_DEL_CONFIRM_PAYEMENT_TIME', 'Bạn có xóa thời gian không?');
define('NOTICE_DEL_CONFIRM_PAYMENT_TIME_SUCCESS', 'Xóa thành công');
define('NOTICE_ORDER_ID_TEXT', 'ID đặt hàng');
define('NOTICE_ORDER_ID_LINK_TEXT', 'Của');
define('NOTICE_ORDER_INPUT_PASSWORD', 'Vui lòng nhập mật khẩu một lần\r\n');
define('NOTICE_ORDER_INPUT_WRONG_PASSWORD', 'Sai mật khẩu');
define('TEXT_ORDER_INPUTED_FLAG', 'Nhập xong');
define('TEXT_ORDER_DATE_LONG', 'Thời hạn hiệu lực:');
define('TEXT_ORDER_HOUHOU', 'Tùy chọn:');
define('TEXT_PREORDER_ID_TEXT', 'Số đơn đặt hàng trước:');
define('TEXT_PREORDER_DAY', 'Ngày đặt hàng trước:');
define('TEXT_ORDER_CUSTOMER_TYPE', 'Phân loại khách hàng:');
define('TEXT_ORDER_CUSTOMER_VIP', 'Hội viên');
define('TEXT_ORDER_GUEST', 'Guest');
define('TEXT_ORDER_CONCAT_OID_CREATE', 'Tạo mới số truy vấn');
define('TEXT_ORDER_EMAIL_LINK', 'Mail');
define('TEXT_ORDER_CREDIT_LINK', 'Thẻ tín dụng');
define('TEXT_ORDER_IP_ADDRESS', 'Địa chỉ IP:');
define('TEXT_ORDER_HOSTNAME', 'Tên host:');
define('TEXT_ORDER_USERAGENT', 'User agent:');
define('TEXT_ORDER_OS', 'OS:');
define('TEXT_ORDER_BROWSER_INFO', 'Loại trình duyệt:');
define('TEXT_ORDER_HTTP_LAN', 'Ngôn ngữ Trình duyệt :');
define('TEXT_ORDER_SYS_LAN', 'Môi trường ngôn ngữ máy tính:');
define('TEXT_ORDER_USER_LAN', 'Môi trường ngôn ngữ người dùng:');
define('TEXT_ORDER_SCREEN_RES', 'Độ phân giải màn hình:');
define('TEXT_ORDER_COLOR_DEPTH', 'Màu màn hình:');
define('TEXT_ORDER_FLASH_VERS', ' Phiên bản Flash:');
define('TEXT_ORDER_CREDITCARD_TITLE', 'Thông tin thẻ tín dụng');
define('TEXT_ORDER_CREDITCARD_NAME', 'Tên trên thẻ:');
define('TEXT_ORDER_CREDITCARD_TEL', 'Điện thoạisố:');
define('TEXT_ORDER_CREDITCARD_EMAIL', 'Địa chỉ mail:');
define('TEXT_ORDER_CREDITCARD_MONEY', 'Số tiền:');
define('TEXT_ORDER_CREDITCARD_COUNTRY', 'Nước cư trú:');
define('TEXT_ORDER_CREDITCARD_STATUS', 'Xác thực:');
define('TEXT_ORDER_CREDITCARD_PAYMENTSTATUS', 'Status thanh toán:');
define('TEXT_ORDER_CREDITCARD_PAYMENTTYPE', 'Kiểu thanh toán:');
define('ENTRY_ENSURE_DATE', 'Thời hạn bảo đảm:');
define('TEXT_ORDER_EXPECTET_COMMENT', 'Nhu cầu:');
define('PREORDERS_STATUS_SELECT_PRE', 'Status「');
define('PREORDERS_STATUS_SELECT_LAST', '」Tìm kiếm từ');
define('TEXT_ORDER_FIND_OID', 'Tìm kiếm từ số  đặt hàng');
define('NOTICE_INPUT_ENSURE_DEADLINE', 'Hãy thiết lập thời hạn bảo đảm.');
define('PREORDERS_PAYMENT_METHOD_PRE', 'Phương thức thanh toán「');
define('PREORDERS_PAYMENT_METHOD_LAST', '」Tìm kiếm từ');
define('TEXT_SORT_ASC','▲');
define('TEXT_SORT_DESC','▼');
define('PREORDER_PRODUCT_UNIT_TEXT', 'Ko');
define('NOTICE_LIMIT_SHOW_PREORDER_TEXT', 'Không thể thao tác vì đang trong lúc xác thực mail.');

define('TEXT_FUNCTION_INPUT_FINISH','Nhập xong');
define('TEXT_FUNCTION_NOTICE','Lưu ý xử lý');
define('TEXT_FUNCTION_HAVE_HISTORY','Có ghi chú');
define('TEXT_FUNCTION_PAYMENT_METHOD','Phương thức thanh toán:');
define('TEXT_FUNCTION_DATE_STRING','Y/n/j');
define('TEXT_FUNCTION_UN_GIVE_MONY','Chưa nhận tiền');
define('TEXT_FUNCTION_UN_GIVE_MONY_DAY','Ngày nhận tiền  :');
define('TEXT_FUNCTION_OPTION','Tùy chọn:');
define('TEXT_FUNCTION_CATEGORY','Sản phẩm：');
define('TEXT_FUNCTION_FINISH','[Nhập]');
define('TEXT_FUNCTION_UNFINISH','「Chưa được」');
define('TEXT_FUNCTION_NUMBER','Số lượng :');
define('TEXT_FUNCTION_NUM','Ko');
define('TEXT_FUNCTION_PC','PC：');
define('TEXT_FUNCTION_PREDATE','Thời hạn hiệu lực:');
define('TEXT_FUNCTION_ENSURE_DATE','Thời hạn đảm bảo :');
define('TEXT_FUNCTION_ORDER_FROM_INFO', 'Site đặt hàng trước :');
define('TEXT_ORDER_HISTORY_FROM_ORDER', 'Đặt hàng');
define('TEXT_ORDER_HISTORY_FROM_PREORDER', 'Đặt trước');

define('TEXT_ORDER_NOT_CHOOSE','Không thể lựa chọn nhiều.');
define('TEXT_NO_OPTION_ORDER','Đơn đặt hàng vẫn chưa đươc chọn.')    ;
define('TEXT_COMPLETION_TRANSACTION','Hoàn thành giao dịch');
define('TEXT_PRESERVATION','Lưu');
define('TEXT_SAVE_FINISHED','Đã hoàn tất lưu của');
define('TEXT_BROWER_REJECTED','Đã bị từ chối bởi các trình duyệt！\n Nhập "about:config" vào cột địa chỉ trình duyệt và nhấn Enter\n Và làm số"signed.applets.codebase_principal_support" thành "true"');
define('TEXT_COPY_TO_CLIPBOARD','Đã sao chép vào clipboard!');
define('TEXT_PLEASE_PASSWORD','Hãy nhập mật khẩu một lần\r\n');
define('TEXT_PASSWORD_NOT','Sai mật khẩu');

?>
