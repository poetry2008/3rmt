<?php
/*
  $Id$
*/

define('HEADING_TITLE', 'Quản lí đặt hàng');
define('HEADING_TITLE_SEARCH', 'ID đặt hàng:');
define('HEADING_TITLE_STATUS', 'Tình trạng:');

define('TABLE_HEADING_COMMENTS', 'Nhận xét');
define('TABLE_HEADING_CUSTOMERS', 'Tên khách hàng');
define('TABLE_HEADING_ORDER_TOTAL', 'Tổng số đặt hàng');
define('TABLE_HEADING_DATE_PURCHASED', 'Ngày đặt hàng');
define('TABLE_HEADING_STATUS', 'Tình trạng');
define('TABLE_HEADING_ACTION', 'Thao tác');
define('TABLE_HEADING_QUANTITY', 'Số lượng');
define('TABLE_HEADING_PRODUCTS_MODEL', 'Số mô hình');
define('TABLE_HEADING_PRODUCTS', 'Số lượng / tên sản phẩm');
define('TABLE_HEADING_TAX', 'Mức thuế');
define('TABLE_HEADING_TOTAL', 'Tổng kết');
define('TABLE_HEADING_PRICE_EXCLUDING_TAX', 'Giá(không thuế)');
define('TABLE_HEADING_PRICE_INCLUDING_TAX', 'Giá(có thuế)');
define('TABLE_HEADING_TOTAL_EXCLUDING_TAX', 'Tổng cộng(không thuế)');
define('TABLE_HEADING_TOTAL_INCLUDING_TAX', 'Tổng cộng(có thuế)');

define('TABLE_HEADING_CUSTOMER_NOTIFIED', 'Thông báo cho khách hàng');
define('TABLE_HEADING_DATE_ADDED', 'Ngày xử lý');

define('ENTRY_CUSTOMER', 'Tên khách hàng:');
define('ENTRY_SOLD_TO', 'Tên người đặt hàng:');
!defined('ENTRY_STREET_ADDRESS') && define('ENTRY_STREET_ADDRESS', 'Địa chỉ 1:');
!defined('ENTRY_SUBURB')          && define('ENTRY_SUBURB', 'Địa chỈ 2:');
!defined('ENTRY_CITY')            && define('ENTRY_CITY', 'Quận huyện thành phố:');
!defined('ENTRY_POST_CODE')       && define('ENTRY_POST_CODE', 'Số bưu điện:');
!defined('ENTRY_STATE')           && define('ENTRY_STATE', 'Tỉnh thành:');
!defined('ENTRY_COUNTRY')         && define('ENTRY_COUNTRY', 'Tên nước:');
!defined('ENTRY_TELEPHONE')       && define('ENTRY_TELEPHONE', 'Số điện thoại');
!defined('ENTRY_EMAIL_ADDRESS')   && define('ENTRY_EMAIL_ADDRESS', 'E-Mail địa chỉ:');
define('ENTRY_DELIVERY_TO', 'Người gửi');
define('ENTRY_SHIP_TO', 'Nơi giao hàng :');
define('ENTRY_SHIPPING_ADDRESS', 'Nơi giao hàng:');
define('ENTRY_BILLING_ADDRESS', 'Nơithanh toán:');
define('ENTRY_PAYMENT_METHOD', 'Phương pháp thanh toán:');
define('ENTRY_CREDIT_CARD_TYPE', 'Loại thẻ tín dụng');
define('ENTRY_CREDIT_CARD_OWNER', 'Chủ thẻ tín dụng');
define('ENTRY_CREDIT_CARD_NUMBER', 'Số thẻ tín dụng:');
define('ENTRY_CREDIT_CARD_EXPIRES', 'Thời hạn thẻ tín dụng:');
define('ENTRY_SUB_TOTAL', 'Tổng phụ');
define('ENTRY_TAX', 'Thuế:');
define('ENTRY_SHIPPING', 'Giao hàng');
define('ENTRY_TOTAL', 'Tổng cộng:');
define('ENTRY_DATE_PURCHASED', 'Ngày đặt hàng:');
define('ENTRY_STATUS', 'Tình trạng:');
define('ENTRY_DATE_LAST_UPDATED', 'Cập nhập ngày mới:');
define('ENTRY_NOTIFY_CUSTOMER', 'Thông báo tình trạng xử lý');
define('ENTRY_NOTIFY_COMMENTS', 'Thêm lời nhận xét:');
define('ENTRY_PRINTABLE', 'In hóa đơn giao hàng');

define('TEXT_INFO_HEADING_DELETE_ORDER', 'Xóa đặt hàng');
define('TEXT_INFO_DELETE_INTRO', 'Bạn có chắc xóa đơn hàng này không?');
define('TEXT_INFO_RESTOCK_PRODUCT_QUANTITY', 'Trở lại số tồn kho ban đầu '); // 'Restock product quantity'
define('TEXT_DATE_ORDER_CREATED', 'Ngày đặt hàng?');
define('TEXT_DATE_ORDER_LAST_MODIFIED', 'Ngày cập nhập:');
define('TEXT_INFO_PAYMENT_METHOD', 'Phương thức thanh toán');

define('TEXT_ALL_ORDERS', 'Toàn bộ các đơn đặt hàng');
define('TEXT_NO_ORDER_HISTORY', 'Không có lịch sử  đặt hàng');

define('EMAIL_SEPARATOR', '--------------------------------------------------------------------------------');
define('EMAIL_TEXT_SUBJECT', 'Thông báo tình trạng tiếp nhận đơn đặt hàng .');
define('EMAIL_TEXT_ORDER_NUMBER', 'Số tiếp nhận đơn  hàng');
define('EMAIL_TEXT_INVOICE_URL', 'Bạn có thể nhìn thấy thông tin đơn đặt hàng ở URL dưới đây.' . "\n");
define('EMAIL_TEXT_DATE_ORDERED', 'Ngày đặt hàng:');
define('EMAIL_TEXT_STATUS_UPDATE',
'Tình trạng tiếp nhậ đặt hàng sẽ như sau đây.' . "\n"
.'tình trạng đặt hàng hiện tại: [ %s ]' . "\n\n");
define('EMAIL_TEXT_COMMENTS_UPDATE', '[Mục liên lạc]' . "\n%s");

define('ERROR_ORDER_DOES_NOT_EXIST', 'Lỗi: không tồn tại đơn đặt hàng;');
define('SUCCESS_ORDER_UPDATED', 'Thành công: Tình trạng đặt hàng đã được cập nhập.');
define('WARNING_ORDER_NOT_UPDATED', 'Thông báo: Tình trạng đặt hàng đã không được thay đổi bất cứ điều gì.');

define('EMAIL_TEXT_STORE_CONFIRMATION', ' Chân thành cảm ơn bạn rất nhiều vì đã đặt hàng.' . "\n\n"
.'Chúng tôi xin được phép hướng dẫn như dưới đây về mục liên lạc và tình trạng tiếp nhận đơn hàng.');
define('EMAIL_TEXT_STORE_CONFIRMATION_FOOTER', 
'Nếu bạn có bất kì thắc mắc về tình trạng đơn hàng, xin vui lòng liên hệ với cửa hàng chúng tôi ' . "\n"
.'để giải quyết.' . "\n\n"
. EMAIL_SIGNATURE);

define('ENTRY_EMAIL_TITLE', 'Tiêu đề mail:');
define('TEXT_CODE_HANDLE_FEE', 'Lệ phí:');
define('TEXT_SHIPPING_FEE','Phí vận chuyển:');

// old oa 
define('TEXT_ORDER_ANSWER','Order Answer');
define('TEXT_BUY_BANK','Mua :Trả từ ngân hàng');
define('TEXT_SELL_BANK','Bán :Chuyển từ ngân hàng');
define('TEXT_SELL_CARD','Bán :Thẻ tín dụng');
define('TEXT_CREDIT_FIND','Điều tra tín dụng');

define('TEXT_ORDER_SAVE','Lưu');
define('TEXT_ORDER_TEST_TEXT','Trong khi vận dụng thí nghiệm<font color="red">（Xác nhận xem giá trị ghi trên có phù hợp ）</font>Sao chép và dán để mua:');
define('TEXT_MAIL_CONTENT_INFO',' Sẽ tự động xuống dòng và hiển thị, dòng mới sẽ có chứa trong thư đã được gởi.');
define('TEXT_ORDER_COPY','Dùng copy paste:');
define('TEXT_ORDER_LOGIN','Tuy nhiên sẽ đăng nhập nhiều hơn bây giờ.');
define('TEXT_ORDER_SEND_MAIL','Gửi mail');
define('TEXT_ORDER_STATUS','Thông báo tình trạng');
define('TEXT_ORDER_HAS_ERROR','Bạn đã tìm kiếm lỗi sai?');
define('TEXT_ORDER_FIND','Tìm kiếm :');
define('TEXT_ORDER_AMOUNT_SEARCH','Tìm kiếm số tiền đặt hàng');
define('TEXT_ORDER_FIND_SELECT','--------Hãy lựa chọnい--------');
define('TEXT_ORDER_FIND_NAME','Tìm kiếm từ tên');
define('TEXT_ORDER_FIND','Tìm kiếm :');
define('TEXT_ORDER_FIND_PRODUCT_NAME','Tìm kiếm từ tên sản phẩm');
define('TEXT_ORDER_FIND_MAIL_ADD','Tìm kiếm từ địa chỉ mail');
define('TEXT_ORDER_QUERYER_NAME','Tên người xác nhận');
define('TEXT_EDIT_MAIL_TEXT','Sửa chữa nguyên văn mail');
define('TEXT_SELECT_MORE','Không thể lựa chọn nhiều.');
define('TEXT_ORDER_SELECT','Chưa chọn đơn hàng .');
define('TEXT_ORDER_WAIT','Chờ giao dịch');
define('TEXT_ORDER_CARE','Chú ý xử lý');
define('TEXT_ORDER_OROSHI','Nhà phân phối');
define('TEXT_ORDER_CUSTOMER_INFO','Thông tin khách hàng');
define('TEXT_ORDER_HISTORY_ORDER','Đặt hàng trước đây ');
define('TEXT_ORDER_NEXT_ORDER','Đặt hàng lần tới');
define('TEXT_ORDER_ORDER_DATE','Ngày giờ giao hàng');
define('TEXT_ORDER_MIX','Trộn');
define('TEXT_ORDER_BUY','Mua');
define('TEXT_ORDER_SELL','Bán');
define('TEXT_ORDER_NOTICE','【Chú ý】');
define('TEXT_ORDER_AUTO_RUN_ON','Tính năng tự đông tải lại hiện đang được kích hoạt　→ ');
define('TEXT_ORDER_AUTO_POWER_OFF','Vô hiệu hóa');
define('TEXT_ORDER_AUTO_RUN_OFF','Tính năng tự động tải lại hiện đang bị vô hiệu hóa　→ ');
define('TEXT_ORDER_AUTO_POWER_ON','Để kích hoạt');
define('TEXT_ORDER_SHOW_LIST','Tóm tắt được hiển thị');
define('TEXT_ORDER_STATUS_SET','Thiết lập status đặt hàng');
define('TEXT_ORDER_CSV_OUTPUT','Xuất CSV');
define('TEXT_ORDER_DAY','');
define('TEXT_ORDER_MONTH','/');
define('TEXT_ORDER_YEAR','/');
define('TEXT_ORDER_END_DATE','Kết thúc ngày:');
define('TEXT_ORDER_START_DATE','Ngày bắt đầu:');
define('TEXT_ORDER_SITE_TEXT','Site đơn đặt hàng');
define('TEXT_ORDER_SERVER_BUSY','ダウンロード中はサーバに対して高負荷となります。Hãy thực hiện trong thời gian truy cập ít hơn.');
define('TEXT_ORDER_DOWNLOPAD','Dữ liệu đặt hàng xuất khẩu');

define('DEL_CONFIRM_PAYMENT_TIME', 'Xóa');
define('NOTICE_DEL_CONFIRM_PAYEMENT_TIME', 'Bạn có muốn xóa thời gian?');
define('NOTICE_DEL_CONFIRM_PAYMENT_TIME_SUCCESS', 'Xóa thành công');

//for function
define('TEXT_FUNCTION_INPUT_FINISH','Nhập xong');
define('TEXT_FUNCTION_NOTICE','Chú ý đối đãi');
define('TEXT_FUNCTION_HAVE_HISTORY','Có ghi chú');
define('TEXT_FUNCTION_PAYMENT_METHOD','Phương thức thanh toán :');
define('TEXT_FUNCTION_DATE_STRING','Y/n/j');
define('TEXT_FUNCTION_UN_GIVE_MONY','Chưa nhận tiền');
define('TEXT_FUNCTION_UN_GIVE_MONY_DAY','Ngày nhận tiền :');
define('TEXT_FUNCTION_OPTION','Tùy chọn :');
define('TEXT_FUNCTION_CATEGORY','Sản phẩm :');
define('TEXT_FUNCTION_FINISH','「Nhập」');
define('TEXT_FUNCTION_UNFINISH','「Chưa được」');
define('TEXT_FUNCTION_NUMBER','Số lượng:');
define('TEXT_FUNCTION_NUM','Ko');
define('TEXT_FUNCTION_PC','PC：');
define('ORDERS_STATUS_SELECT_PRE', 'Status「');
define('ORDERS_STATUS_SELECT_LAST', '」Tìm kiếm từ ');
define('TEXT_ORDER_FIND_OID', 'Tìm kiến từ số đơn hàng');
define('TEXT_SORT_ASC','▲');
define('TEXT_SORT_DESC','▼');
define('ORDERS_PAYMENT_METHOD_PRE', 'Phương thức thanh toán「');
define('ORDERS_PAYMENT_METHOD_LAST', '」tìm kiếm từ ');

define('TEXT_ORDER_TYPE_PRE', 'Phân loại đặt hàng 「');
define('TEXT_ORDER_TYPE_LAST', '」Tìm kiếm từ ');
define('TEXT_ORDER_TYPE_SELL', 'Bán');
define('TEXT_ORDER_TYPE_BUY', 'Mua');
define('TEXT_ORDER_TYPE_MIX', 'Hợp trộn');
define('TEXT_ORDER_HISTORY_FROM_ORDER', 'Đơn hàng');
define('TEXT_ORDER_HISTORY_FROM_PREORDER', 'Đặt trước');
define('TEXT_SHIPPING_METHOD','Phương pháp giao hàng');
define('TEXT_SHIPPING_ADDRESS','Nơi giao hàng');
define('SHOW_MANUAL','Manual');
define('SHOW_MANUAL_TITLE','Manual của');
define('SHOW_MANUAL_SEARCH','Tìm kiếm');
define('SHOW_MANUAL_NONE','Manual đã không được thiết lập！！！');
define('SHOW_MANUAL_RETURN','Trở lại');
define('SEARCH_MANUAL_PRODUCTS_FAIL','Không có manual đã tìm kiếm！！！');
define('SEARCH_CAT_PRO_TITLE','Danh mục / sản phẩm');
define('SEARCH_MANUAL_CONTENT','Manual');
define('SEARCH_MANUAL_LOOK','Thao tác');
define('MANUAL_SEARCH_HEAD', 'Kết quả tìm kiếm của');
define('MANUAL_SEARCH_EDIT', 'Chỉnh sữa');
define('MANUAL_SEARCH_NORES','Manual hiện tại đã không được đăng ký... ');
define('TEXT_NO_RECEIVABLES','Chưa nhận tiền');
define('TEXT_YEN','Yên');
define('TEXT_HOUR','Giờ');
define('TEXT_MIN','Phút');
define('TEXT_TWENTY_FOUR_HOUR','　（Hiển thị 24 giờ）');
define('TEXT_SEND_MAIL','Gửi xong :');
define('TEXT_ORDERS_ID','ID đặt hàng');
define('TEXT_OF','Của');
define('TEXT_PAYMENT_NOTICE','Thông báo thanh toán*');
define('TEXT_INPUT_ONE_TIME_PASSWORD','Hãy nhập mật khẩu  một lần');
define('TEXT_INPUT_PASSWORD_ERROR','Mật khẩu sai');
define('TEXT_STATUS_HANDLING_WARNING','Chú ý xử lý');
define('TEXT_STATUS_WAIT_TRADE','Chờ giao dịch');
define('TEXT_STATUS_READY_ENTER','Nhập xong');
define('TEXT_SITE_ORDER_FORM','Site đặt hàng :');
define('TEXT_TRADE_DATE','Ngày giờ giao hàng :');
define('TEXT_ORDERS_OID','Số đơn đặt hàng');
define('TEXT_ORDERS_DATE','Ngày đặt hàng :');
define('TEXT_CUSTOMER_CLASS','Phân loại khách hàng :');
define('TEXT_GUEST','Guest');
define('TEXT_MEMBER','Hội viên');
define('TEXT_CREATE_NEW_NUMBER_SEARCH','Tạo mới số truy vấn');
define('TEXT_EMAIL_ADDRESS','Mail');
define('TEXT_TEL_UNKNOW','Thẻ tín dụng');
define('TEXT_ADDRESS_INFO','Thông tin địa chỉ');
define('TEXT_IP_ADDRESS','Địa chỉ IP');
define('TEXT_HOST_NAME','Tên host :');
define('TEXT_USER_AGENT','User agent:');
define('TEXT_BROWSER_TYPE','Loại trình duyệt:');
define('TEXT_BROWSER_LANGUAGE','Ngôn ngữ trình duyệt:');
define('TEXT_PC_LANGUAGE','Môi trường ngôn ngữ máy tính:');
define('TEXT_USERS_LANGUAGE','Môi trường ngôn ngữ của người dùng:');
define('TEXT_SCREEN_RESOLUTION','Độ phân giải màn hình:');
define('TEXT_SCREEN_COLOR','Màu sắc màn hình:');
define('TEXT_FLASH_VERSION','Phiên bàn Flash:');
define('TEXT_CART_INFO','Thông tin thẻ tín dụng');
define('TEXT_CART_HOLDER','Tên trên thẻ:');
define('TEXT_TEL_NUMBER','Số điện thoại:');
define('TEXT_EMAIL_ADDRESS_INFO','Địa chỉ mail:');
define('TEXT_PRICE','Số tiền:');
define('TEXT_COUNTRY_CODE','Nước cư trú:');
define('TEXT_PAYER_STATUS','Xác thực:');
define('TEXT_PAYMENT_STATUS','Status thanh toán:');
define('TEXT_PAYMENT_TYPE','Loại thanh toán:');
define('TEXT_SAVE','Lưu');
define('TEXT_POINT','Chiết khấu　　　　　　：-');
define('TEXT_HANDLE_FEE','Lệ phí　　　　　：');
define('TEXT_PAYMENT_AMOUNT','Số tiền chi trả　　　：');
define('TEXT_TRANSACTION_FEE','Lệ phí thanh toán:');
define('TEXT_REPLACE_HANDLE_FEE','Lệ phí');
define('ORDERS_PRODUCTS', 'Sản phẩm đặt hàng');
define('QTY_NUM', 'Số lượng ');
define('ORDERS_NUM_UNIT', 'Ko');
define('PRODUCT_SINGLE_PRICE', 'Đơn giá');
define('TEXT_CHARACTER_NAME_SEND_MAIL','※ Công ty chúng tôi sẽ thông báo tên nhân vật vào 10 phút trước khi giao dịch.');
define('TEXT_CHARACTER_NAME_CONFIRM_SEND_MAIL','※ Công ty chúng tôi sẽ thông báo tên nhân vật sau khi đã xác nhận thanh toán qua email.');
define('ORDER_TOP_MANUAL_TEXT', 'Cập nhất');
define('ORDER_MANUAL_ALL_SHOW', 'Đọc tiếp');
define('ORDER_MANUAL_ALL_HIDE', 'Thu gọn cửa sổ');
?>
