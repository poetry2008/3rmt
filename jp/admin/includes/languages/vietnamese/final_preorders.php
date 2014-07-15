<?php
/*
  $Id$
*/

define('HEADING_TITLE', 'Chỉnh sửa nội dung đặt trước');
define('HEADING_TITLE_SEARCH', 'ID đặt trước:');
define('HEADING_TITLE_STATUS', 'Status:');
define('ADDING_TITLE', 'Thêm sản phẩm');

define('ENTRY_UPDATE_TO_CC', '(Update to <b>Credit Card</b> to view CC fields.)');
define('TABLE_HEADING_COMMENTS', 'Nhận xét');
define('TABLE_HEADING_CUSTOMERS', 'Tên khách hàng');
define('TABLE_HEADING_ORDER_TOTAL', 'Tổng số tiền đặt trước');
define('TABLE_HEADING_DATE_PURCHASED', 'Ngày đặt trước');
define('TABLE_HEADING_STATUS', 'Status');
define('TABLE_HEADING_ACTION', 'Thao tác');
define('TABLE_HEADING_QUANTITY', 'Số lượng');
define('TABLE_HEADING_PRODUCTS_MODEL', 'Số mẫu');
define('TABLE_HEADING_PRODUCTS', 'Tên sản phẩm');
define('TABLE_HEADING_TAX', 'Thuế tiêu thụ');
define('TABLE_HEADING_TOTAL', 'Tổng cộng');
define('TABLE_HEADING_UNIT_PRICE', 'Đơn giá');
define('TABLE_HEADING_TOTAL_PRICE', 'Tổng cộng');

define('TABLE_HEADING_CUSTOMER_NOTIFIED', 'Thông báo tình trạng xử lý');
define('TABLE_HEADING_DATE_ADDED', 'Ngày cập nhật');

define('ENTRY_CUSTOMER', 'Tên khách hàng:');
define('ENTRY_CUSTOMER_NAME', 'Tên');
//add
define('ENTRY_CUSTOMER_NAME_F', 'Tên(Phiên âm)');
define('ENTRY_CUSTOMER_COMPANY', 'Tên công ty');
define('ENTRY_CUSTOMER_ADDRESS', 'Địa chỉ');
define('ENTRY_CUSTOMER_SUBURB', 'Tên tòa nhà');
define('ENTRY_CUSTOMER_CITY', 'Quận huyện thành phố');
define('ENTRY_CUSTOMER_STATE', 'Tỉnh thành');
define('ENTRY_CUSTOMER_POSTCODE', 'Mã bưu điện');
define('ENTRY_CUSTOMER_COUNTRY', 'Tên nước');

define('ENTRY_SOLD_TO', 'Người mua:');
define('ENTRY_DELIVERY_TO', 'Nơi giao hàng:');
define('ENTRY_SHIP_TO', 'Nơi giao hàng:');
define('ENTRY_SHIPPING_ADDRESS', 'Nơi giao hàng:');
define('ENTRY_BILLING_ADDRESS', 'Nơi thnah toán:');
define('ENTRY_PAYMENT_METHOD', 'Phương thức thanh toán:');
define('ENTRY_CREDIT_CARD_TYPE', 'Loại thẻ tín dụng:');
define('ENTRY_CREDIT_CARD_OWNER', 'Tên thẻ:');
define('ENTRY_CREDIT_CARD_NUMBER', 'Số thẻ:');
define('ENTRY_CREDIT_CARD_EXPIRES', 'Thời hạn hiệu lực thẻ:');
define('ENTRY_SUB_TOTAL', 'Tổng phụ:');
define('ENTRY_TAX', 'Thuế tiêu thụ:');
define('ENTRY_SHIPPING', 'Phương thức giao hàng:');
define('ENTRY_TOTAL', 'Tổng cộng:');
define('ENTRY_DATE_PURCHASED', 'Ngày đặt trước:');
define('ENTRY_STATUS', 'Status:');
define('ENTRY_DATE_LAST_UPDATED', 'Ngày cập nhật:');
define('ENTRY_NOTIFY_CUSTOMER', 'Thông báo tình trạng xử lý:');
define('ENTRY_NOTIFY_COMMENTS', 'Thêm nhận xét:');
define('ENTRY_PRINTABLE', 'In hóa đơn giao hàng');

define('TEXT_INFO_HEADING_DELETE_ORDER', 'Xóa đặt trước');
define('TEXT_INFO_DELETE_INTRO', 'Bạn có thật sự muốn xóa đặt trước?');
define('TEXT_INFO_RESTOCK_PRODUCT_QUANTITY', 'Quay lại tồn kho ban đầu');
define('TEXT_DATE_ORDER_CREATED', 'Ngày tạo:');
define('TEXT_DATE_ORDER_LAST_MODIFIED', 'Ngày cập nhật:');
define('TEXT_DATE_ORDER_ADDNEW', 'Thêm sản phẩm');
define('TEXT_INFO_PAYMENT_METHOD', 'Phương thức thanh toán:');

define('TEXT_ALL_ORDERS', 'Toàn bộ đặt trước');
define('TEXT_NO_ORDER_HISTORY', 'Không có lịch sử đặt trước');

define('EMAIL_SEPARATOR', '--------------------------------------------------------------------------------');
define('EMAIL_TEXT_SUBJECT', 'Thông báo thông tin tiếp nhận đặt trước');
define('EMAIL_TEXT_ORDER_NUMBER', 'Số tiếp nhận đặt trước: ');
define('EMAIL_TEXT_INVOICE_URL', 'Có thể xem thông tin về đặt hàng trước ở URL dưới đây.' . "\n");
define('EMAIL_TEXT_DATE_ORDERED', 'Ngày đặt trước: ');
define('EMAIL_TEXT_STATUS_UPDATE',
'Thông tin tiếp nhận đặt hàng trước sẽ như dưới đây.' . "\n"
.'Thông tin tiếp nhận hiện tại: [ %s ]' . "\n\n");
define('EMAIL_TEXT_COMMENTS_UPDATE', '[Mục liên lạc]' . "\n\n%s");

define('ERROR_ORDER_DOES_NOT_EXIST', 'Lỗi: Không tồn tại đặt hàng trước.');
define('SUCCESS_ORDER_UPDATED', 'Thành công: Tình trạng đặt hàng trước đã được cập nhật.');
define('WARNING_ORDER_NOT_UPDATED', 'Cảnh báo: Tình trạng dặt hàng trước đã không được thay đổi bất cứ điều gì.');

define('ADDPRODUCT_TEXT_CATEGORY_CONFIRM', 'OK');
define('ADDPRODUCT_TEXT_SELECT_PRODUCT', 'Chọn sản phẩm');
define('ADDPRODUCT_TEXT_PRODUCT_CONFIRM', 'OK');
define('ADDPRODUCT_TEXT_SELECT_OPTIONS', 'Chọn tùy chọn');
define('ADDPRODUCT_TEXT_OPTIONS_CONFIRM', 'OK');
define('ADDPRODUCT_TEXT_OPTIONS_NOTEXIST', 'Không có tùy chọn: bỏ qua..');
define('ADDPRODUCT_TEXT_CONFIRM_QUANTITY', 'Số lượng');
define('ADDPRODUCT_TEXT_CONFIRM_ADDNOW', 'Thêm vào');

define('EMAIL_TEXT_STORE_CONFIRMATION', ' Cảm ơn bạn đã đặt hàng trước.' . "\n" . 
'Chúng tôi xin được phép hướng dẫn như dưới đây về mục liên lạc và tình trạng tiếp nhận đặt hàng trước.');
define('TABLE_HEADING_COMMENTS_ADMIN', '[Mục liên lạc]');
define('EMAIL_TEXT_STORE_CONFIRMATION_FOOTER', 
'Nếu có câu hỏi nào liên quan đến thông tin tiếp nhận, vui lòng liên hệ đến cửa hàng của công ty chúng tôi' . "\n"
.'để được giải đáp.' . "\n\n"
. EMAIL_SIGNATURE);
define('ADD_A_NEW_PRODUCT', 'Thêm sản phẩm');
define('CHOOSE_A_CATEGORY', ' --- Chọn danh mục sản phẩm --- ');
define('SELECT_THIS_CATECORY', 'Thực hiện chọn danh mục sản phẩm');
define('CHOOSE_A_PRODUCT', ' --- Chọn danh mục sản phẩm --- ');
define('SELECT_THIS_PRODUCT', 'Thực hiện chọn danh mục sản phẩm');
define('NO_OPTION_SKIPPED', 'Không có tùy chọn - bỏ qua....');
define('SELECT_THESE_OPTIONS', 'Thực hiện chọn tùy chọn');
define('SELECT_QUANTITY', ' Số lượng');
define('SELECT_ADD_NOW', 'Thực hiện thêm vào');
define('SELECT_STEP_ONE', 'STEP 1:');
define('SELECT_STEP_TWO', 'STEP 2:');
define('SELECT_STEP_THREE', 'STEP 3:');
define('SELECT_STEP_FOUR', 'STEP 4:');
define('TEXT_CODE_HANDLE_FEE', 'Phí:');
define('EDIT_ORDERS_UPDATE_NOTICE', 'Vui lòng nhập cẩn thận nội dung đã thay đổi.hãy cố gắng kiểm tra xem có nhập những kí tự thừ như khoảng trắng hay không！');
define('EDIT_ORDERS_ID_TEXT', 'Số đặt trước:');
define('EDIT_ORDERS_DATE_TEXT', 'Ngày đặt trước:');
define('EDIT_ORDERS_CUSTOMER_NAME', 'Tên khách hàng:');
define('EDIT_ORDERS_EMAIL', 'Địa chỉ mail:');
define('EDIT_ORDERS_PAYMENT_METHOD', 'Phương pháp thanh toán:');
define('EDIT_ORDERS_FETCHTIME', 'Thời hạn hiệu lực:');
define('EDIT_ORDERS_TORI_TEXT', 'Tùy chọn:');
define('EDIT_ORDERS_CUSTOMER_NAME_READ', '<font color="red">※</font>&nbsp;Giữa họ và tên<font color="red">hãy nhập</font>khoảng trắng 1 byte.');
define('EDIT_ORDERS_TORI_READ', '<table><tr class="smalltext"><td><font color="red">※</font>&nbsp;Dùng coppy paste:</td><td>Muốn giao dịch theo thời gian đã quy định</td><td>Nếu có thể sớm hơn thời gian đã quy định thì muốn làm sớm hơn</td></tr></table>');
define('EDIT_ORDERS_PRO_LIST_TITLE', '2. Sản phẩm đặt trước');
define('TABLE_HEADING_NUM_PRO_NAME', ' Số lượng/ Tên sản phẩm');
define('TABLE_HEADING_CURRENICY', 'Thuế suất');
define('TABLE_HEADING_PRICE_BEFORE', 'Gía(chưa bao gồm thuế)');
define('TABLE_HEADING_PRICE_AFTER', 'Gía(đã bao gồm thuế)');
define('TABLE_HEADING_TOTAL_BEFORE', 'Tổng cộng(chưa bao gồm thuế)');
define('TABLE_HEADING_TOTAL_AFTER', 'Tổng cộng(đã bao gồm thuế)');
define('EDIT_ORDERS_ADD_PRO_READ', 'Không thể thay đổi đồng thời mục thêm sản phẩm và các mục khác.<b>Vui lòng tiến hành riêng mục "thêm sản phẩm".</b>');
define('EDIT_ORDERS_FEE_TITLE_TEXT', '3. Điểm chiết khấu, lệ phí, hạ giá');
define('TABLE_HEADING_FEE_MUST', 'Mục chú ý');
define('EDIT_ORDERS_OTTOTAL_READ', 'Vui lòng kiểm tra chắc chắn tổng số tiền có đúng hay không.');
define('EDIT_ORDERS_TOTALDETAIL_READ', 'Khách hàng là guest.Không thể nhập điểm chiết khấu.');
define('EDIT_ORDERS_TOTALDETAIL_READ_ONE', 'Trường hợp giảm giá, vui lòng nhập kí hiệu − (âm).');
define('EDIT_ORDERS_CONFIRMATION_READ', '<font color="red">Quan trọng:</font>&nbsp;<b>Trường hợp đã thay đổi yếu tố cấu thành giáVui lòng click vào nút [<font color="red">Kiểm tra nội dung đặt trước</font>] và kiểm tra tổng số tiền có thống nhất hay không.&nbsp;⇒</b>');
define('EDIT_ORDERS_CONFIRM_BUTTON', 'Kiểm tra nội dung đặt trước');
define('EDIT_ORDERS_ITEM_FOUR_TITLE', '4. Thông báo nhận xét, status đặt trước');
define('EDIT_ORDERS_SEND_MAIL_TEXT', 'Gởi mail:');
define('EDIT_ORDERS_RECORD_TEXT', 'Ghi nhận xét:');
define('EDIT_ORDERS_RECORD_READ', '←Không kiểm tra chỗ này');
define('EDIT_ORDERS_RECORD_ARTICLE', 'Câu văn đã được nhập ở đây sẽ được chèn vào nguyên văn mail.');
define('EDIT_ORDERS_ITEM_FIVE_TITLE', '5. Cập nhật dữ liệu');
define('EDIT_ORDERS_FINAL_CONFIRM_TEXT', 'Đã thực hiện xác nhận cuối cùng chưa?');
define('EDIT_NEW_ORDERS_CREATE_TITLE', 'Tạo đơn hàng đặt trước');
define('EDIT_NEW_ORDERS_CREATE_READ', '【Quan trọng】Không có trong chỉnh sử đặt trước. Hệ thống tạo đặt trước mới');
define('EDIT_ORDERS_ORIGIN_VALUE_TEXT', '（Gía trị khởi tạo）');
define('ERROR_INPUT_PRICE_NOTICE', 'Vui lòng viết đơn giá');
define('EDIT_ORDERS_PRICE_UNIT', 'Yên');
define('EDIT_ORDERS_NUM_UNIT', 'Ko');
define('EDIT_ORDERS_NOTICE_UPDATE_FAIL_TEXT', 'Đã xóa cập nhật.');
define('EDIT_ORDERS_NOTICE_DATE_WRONG_TEXT', 'Định dạng ngày giờ sai. "2008-01-01 10:30:00"');
define('EDIT_ORDERS_NOTICE_NOUSE_DATE_TEXT', 'Ngày tháng không hợp lệ hoặc vượt quá số bên phải. "23:59:59"');
define('EDIT_ORDERS_NOTICE_MUST_INPUT_DATE_TEXT', 'Ngày giờ đã không được nhập.');
define('EDIT_ORDERS_NOTICE_POINT_ERROR', 'Không đủ điểm. Điêm có khả năng nhập là');
define('EDIT_ORDERS_NOTICE_POINT_ERROR_LINK', ' .');
define('EDIT_ORDERS_NOTICE_PRODUCT_DEL', 'Đã xóa sản phẩm.<font color="red">Mail không được gởi.</font>');
define('EDIT_ORDERS_NOTICE_ERROR_OCCUR', 'Đã phát sinh lỗi.Có khả năng không tiến hành xử lí thông thường.');

define('EDIT_ORDERS_ENSUREDATE', 'Thời hạn bảo đảm:');
define('NOTICE_INPUT_ENSURE_DEADLINE', 'Thiết lập thời hạn bảo đảm.');
define('FORDERS_NOTICE_INPUT_ONCE_PWD', 'Nhập mật khẩu sử dụng một lần.');
define('FORDERS_NOTICE_ONCE_PWD_WRONG', 'Mật khẩu sai');
define('PREORDER_PRODUCT_UNIT_TEXT', 'Ko');
define('ENTRY_EMAIL_TITLE', 'Tiêu đề mail：');
define('BUTTON_WRITE_PREORDER', 'Sao chép đơn đặt hàng trước');
define('TABLE_HEADING_PRODUCTS_PRICE', 'Đơn giá');
define('EDIT_ORDERS_NOTICE_EMAIL_MATCH_TEXT','Địa chỉ mial đã nhập không chính xác!');
define('TEXT_SELECT_PAYMENT_ERROR','<font color="red">Phương thức thanh toán đã chọn hiện nay đã bị vô hiệu hóa. Vui lòng chọn lại.</font>');
?>
