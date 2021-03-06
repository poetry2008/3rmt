<?php
/*
  $Id$

*/

//define('HEADING_TITLE', 'Chi tiết・Chỉnh sửa đặt trước');
define('HEADING_TITLE_NUMBER', 'Số đặt trước:');
define('HEADING_TITLE_DATE', ' - ');
define('HEADING_SUBTITLE', 'Nhập nội dung phần muốn chỉnh sửa và click nút cập nhật.');
//define('HEADING_TITLE_SEARCH', 'ID đặt trước:');
//define('HEADING_TITLE_STATUS', 'Status:');
//define('ADDING_TITLE', 'Thêm sản phẩm');

define('HINT_UPDATE_TO_CC', '<font color="#FF0000">hint: </font>Set payment to "Credit Card" to show some additional fields.');
define('HINT_TOTALS', '<font color="#FF0000">hint: </font>Feel free to give discounts by adding negative amounts to the list.<br>Fields with "0" values are deleted when updating the order (exception: shipping).');
define('HINT_PRESS_UPDATE', 'Vui lòng click vào nút lưu và nội dung đã chỉnh sửa.');

//define('TABLE_HEADING_COMMENTS', 'Nhận xét');
//define('TABLE_HEADING_CUSTOMERS', 'Thông tin khách hàng');
//define('TABLE_HEADING_ORDER_TOTAL', 'Tổng tiền');
//define('TABLE_HEADING_DATE_PURCHASED', 'Ngày đặt trước');
//define('TABLE_HEADING_STATUS', 'Status mới');
//define('TABLE_HEADING_ACTION', 'Thao tác');
//define('TABLE_HEADING_QUANTITY', 'Số lượng');
//define('TABLE_HEADING_PRODUCTS_MODEL', 'Số mẫu sản phẩm');
//define('TABLE_HEADING_PRODUCTS', 'Sản phẩm');
//define('TABLE_HEADING_TAX', 'Thuế tiêu thụ');
//define('TABLE_HEADING_TOTAL', 'Tổng cộng');
//define('TABLE_HEADING_UNIT_PRICE', 'Gía (Chưa bao gồm thuế)');
define('TABLE_HEADING_UNIT_PRICE_TAXED', 'Gía (Đã bao gồm thuế)');
//define('TABLE_HEADING_TOTAL_PRICE', 'Tổng cộng(Chưa bao gồm thuế)');
define('TABLE_HEADING_TOTAL_PRICE_TAXED', 'Tổng cộng(đã bao gồm thuế)');
define('TABLE_HEADING_TOTAL_MODULE', 'Yếu tố cấu thành giá');
define('TABLE_HEADING_TOTAL_AMOUNT', 'Số tiền');

//define('TABLE_HEADING_CUSTOMER_NOTIFIED', 'Thông báo cho khách hàng');
//define('TABLE_HEADING_DATE_ADDED', 'Ngày gởi');

//define('ENTRY_CUSTOMER', 'Thông tin khách hàng');
//define('ENTRY_CUSTOMER_NAME', 'Tên');
//define('ENTRY_CUSTOMER_COMPANY', 'Tên công ty');
//define('ENTRY_CUSTOMER_ADDRESS', 'Địa chỉ 1');
//define('ENTRY_CUSTOMER_SUBURB', 'Địa chỉ 2');
//define('ENTRY_CUSTOMER_CITY', 'Huyện tỉnh thành phố');
//define('ENTRY_CUSTOMER_STATE', 'Tỉnh thành');
//define('ENTRY_CUSTOMER_POSTCODE', 'Mã bưu điện');
//define('ENTRY_CUSTOMER_COUNTRY', 'Tên nước');
define('ENTRY_CUSTOMER_PHONE', 'Số điện thoại');
define('ENTRY_CUSTOMER_EMAIL', 'Địa chỉ Email');

//define('ENTRY_SOLD_TO', 'Người đặt trước:');
//define('ENTRY_DELIVERY_TO', 'Nơi chuyển đến:');
//define('ENTRY_SHIP_TO', 'Shipping to:');
//define('ENTRY_SHIPPING_ADDRESS', 'Địa chỉ nơi chuyển đến');
//define('ENTRY_BILLING_ADDRESS', 'Địa chỉ nơi thanh toán');
//define('ENTRY_PAYMENT_METHOD', 'Phương pháp thanh toán:');
//define('ENTRY_CREDIT_CARD_TYPE', 'Loại thẻ:');
//define('ENTRY_CREDIT_CARD_OWNER', 'Chủ sở hữu thẻ:');
//define('ENTRY_CREDIT_CARD_NUMBER', 'Số thẻ:');
//define('ENTRY_CREDIT_CARD_EXPIRES', 'Thời hạn hiệu lực:');
//define('ENTRY_SUB_TOTAL', 'Tổng phụ:');
//define('ENTRY_TAX', 'Thuế tiêu thụ:');
//define('ENTRY_SHIPPING', 'Phí vận chuyển:');
//define('ENTRY_TOTAL', 'Tổng cộng:');
//define('ENTRY_DATE_PURCHASED', 'Ngày đặt trước:');
//define('ENTRY_STATUS', 'Status:');
//define('ENTRY_DATE_LAST_UPDATED', 'Ngày cập nhật mới nhất:');
//define('ENTRY_NOTIFY_CUSTOMER', 'Thông báo đến khách hàng:');
//define('ENTRY_NOTIFY_COMMENTS', 'Gửi nhận xét:');
//define('ENTRY_PRINTABLE', 'In hóa đơn giao hàng');

//define('TEXT_INFO_HEADING_DELETE_ORDER', 'Xóa đặt trước');
//define('TEXT_INFO_DELETE_INTRO', 'Bạn có chắc chắn muốn xóa đặt trước？');
//define('TEXT_INFO_RESTOCK_PRODUCT_QUANTITY', 'Trở về tồn kho');
//define('TEXT_DATE_ORDER_CREATED', 'Ngày tạo:');
//define('TEXT_DATE_ORDER_LAST_MODIFIED', 'Ngày cập nhật mới:');
//define('TEXT_DATE_ORDER_ADDNEW', 'Thêm sản phẩm mới');
//define('TEXT_INFO_PAYMENT_METHOD', 'Phương pháp thanh toán:');

//define('TEXT_ALL_ORDERS', 'Toàn bộ đơn đặt hàng trước');
//define('TEXT_NO_ORDER_HISTORY', 'Không tồn tại đặt trước');

//define('EMAIL_SEPARATOR', '------------------------------------------------------');
//define('EMAIL_TEXT_SUBJECT', 'Thông báo tình trạng tiếp nhận đặt trước');
//define('EMAIL_TEXT_ORDER_NUMBER', 'Số tiếp nhận đặt trước:');
//define('EMAIL_TEXT_INVOICE_URL', 'Có thể xem thông tin về đặt hàng trước ở URL dưới đây.' . "\n");
//define('EMAIL_TEXT_DATE_ORDERED', 'Ngày đặt trước:');
//define('EMAIL_TEXT_STATUS_UPDATE',
//'Tình trạng tiếp nhận đặt hàng trước như sau.' . "\n"
//.'Tình trạng tiếp nhận hiện tại: [ %s ]' . "\n\n");
//define('EMAIL_TEXT_COMMENTS_UPDATE', '[Mục liên lạc]' . "\n%s");

//define('ERROR_ORDER_DOES_NOT_EXIST', 'Lỗi: Không tồn tại đơn đặt hàng trước.');
//define('SUCCESS_ORDER_UPDATED', 'Thành công: Tình trạng đặt hàng trước đã được cập nhật.');
//define('WARNING_ORDER_NOT_UPDATED', 'Cảnh báo: Tình trạng đặt hàng trước đã không được thay đổi bất cứ điều gì.');

//define('EMAIL_TEXT_STORE_CONFIRMATION', ' Rất cảm ơn bạn đã đặttrước.' . "\n\n"
//.'Chúng tôi xin được phép hướng dẫn theo như dưới đây về mục liên lạc và tình trạng tiếp nhận đặt hàng trước.');
//define('EMAIL_TEXT_STORE_CONFIRMATION_FOOTER', 
//'Nếu có thắc mắc về tình trạng tiếp nhận,..vv Xin vui lòng liên hệ đến cửa hàng của chúng tôi ' . "\n"
//.'để được giải quyết' . "\n\n"
//. EMAIL_SIGNATURE);


//define('ADDPRODUCT_TEXT_CATEGORY_CONFIRM', 'OK');
//define('ADDPRODUCT_TEXT_SELECT_PRODUCT', 'Chọn sản phẩm');
//define('ADDPRODUCT_TEXT_PRODUCT_CONFIRM', 'OK');
//define('ADDPRODUCT_TEXT_SELECT_OPTIONS', 'Chọn tùy chọn sản phẩm');
//define('ADDPRODUCT_TEXT_OPTIONS_CONFIRM', 'OK');
//define('ADDPRODUCT_TEXT_OPTIONS_NOTEXIST', 'Không tồn tại tùy chọn sản phẩm. Bỏ qua...');
//define('ADDPRODUCT_TEXT_CONFIRM_QUANTITY', 'Số lượng sản phẩm này');
//define('ADDPRODUCT_TEXT_CONFIRM_ADDNOW', 'Thêm');
define('ADDPRODUCT_TEXT_STEP', 'Step');
define('ADDPRODUCT_TEXT_STEP1', 'Chọn danh mục:');
define('ADDPRODUCT_TEXT_STEP2', 'Chọn sản phẩm:');
define('ADDPRODUCT_TEXT_STEP3', 'Chọn tùy chọn:');

define('MENUE_TITLE_CUSTOMER', '1. Thông tin khách hàng');
define('MENUE_TITLE_PAYMENT', '2. Phương pháp thanh toán');
define('MENUE_TITLE_ORDER', '3. Sản phẩm đặt trước');
define('MENUE_TITLE_TOTAL', '4. Vận chuyển, thanh toán, thuế');
define('MENUE_TITLE_STATUS', '5. Status đặt trước, thông báo nhận xét');
define('MENUE_TITLE_UPDATE', '6. Cập nhật dữ liệu');
define('TEXT_CODE_HANDLE_FEE', 'Lệ phí:');
define('ORDERS_PRODUCT_ERROR','Không có sản phẩm. Để tạo đặt hàng trước, trướt hết hãy vui lòng thêm tên sản phẩm.');
?>
