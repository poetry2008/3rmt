<?php
/*
  $Id$
*/

define('HEADING_TITLE', 'Danh sách thẻ tín dụng không rõ');
define('HEADING_TITLE_SEARCH', 'Đặt hàng ID:');
define('HEADING_TITLE_STATUS', 'Status:');

define('TABLE_HEADING_COMMENTS', 'Nhận xét');
define('TABLE_HEADING_CUSTOMERS', 'Tên khách hàng');
define('TABLE_HEADING_ORDER_TOTAL', 'Tổng số tiền đặt hàng');
define('TABLE_HEADING_DATE_PURCHASED', 'Ngày đặt hàng');
define('TABLE_HEADING_STATUS', 'Status');
define('TABLE_HEADING_ACTION', 'Thao tác');
define('TABLE_HEADING_QUANTITY', 'Số lượng');
define('TABLE_HEADING_PRODUCTS_MODEL', 'Số mẫu');
define('TABLE_HEADING_PRODUCTS', 'Số lượng / Tên sản phẩm');
define('TABLE_HEADING_TAX', 'Mức thuế');
define('TABLE_HEADING_TOTAL', 'Tổng cộng');
define('TABLE_HEADING_PRICE_EXCLUDING_TAX', 'Giá(không bao gồm thuế)');
define('TABLE_HEADING_PRICE_INCLUDING_TAX', 'Giá(bao gồm thuế)');
define('TABLE_HEADING_TOTAL_EXCLUDING_TAX', 'Tổng cộng(chưa thuế)');
define('TABLE_HEADING_TOTAL_INCLUDING_TAX', 'Tổng cộng(bao gồm thuế)');

define('TABLE_HEADING_CUSTOMER_NOTIFIED', 'Thông báo cho khách hàng');
define('TABLE_HEADING_DATE_ADDED', 'Ngày xử lý');

define('ENTRY_CUSTOMER', 'Tên khách hàng:');
define('ENTRY_SOLD_TO', 'Tên người đặt hàng:');
!defined('ENTRY_STREET_ADDRESS') && define('ENTRY_STREET_ADDRESS', 'Địa chỉ１:');
!defined('ENTRY_SUBURB')          && define('ENTRY_SUBURB', 'Địa chỉ２:');
!defined('ENTRY_CITY')            && define('ENTRY_CITY', 'Quận huyện thành phố:');
!defined('ENTRY_POST_CODE')       && define('ENTRY_POST_CODE', 'Mã bưu điện:');
!defined('ENTRY_STATE')           && define('ENTRY_STATE', 'Tỉnh thành:');
!defined('ENTRY_COUNTRY')         && define('ENTRY_COUNTRY', 'Tên nước:');
!defined('ENTRY_TELEPHONE')       && define('ENTRY_TELEPHONE', 'Số điện thoại:');
!defined('ENTRY_EMAIL_ADDRESS')   && define('ENTRY_EMAIL_ADDRESS', 'Đại chỉ E-Mail :');
define('ENTRY_DELIVERY_TO', 'Địa chỉ giao hàng:');
define('ENTRY_SHIP_TO', 'Địa chỉ giao hàng:');
define('ENTRY_SHIPPING_ADDRESS', 'Địa chỉ giao hàng:');
define('ENTRY_BILLING_ADDRESS', 'Nơi thanh toán:');
define('ENTRY_PAYMENT_METHOD', 'Phương pháp thanh toán:');
define('ENTRY_CREDIT_CARD_TYPE', 'Loại thẻ tín dụng:');
define('ENTRY_CREDIT_CARD_OWNER', 'Chủ thẻ tín dụng:');
define('ENTRY_CREDIT_CARD_NUMBER', 'Số thẻ tín dụng:');
define('ENTRY_CREDIT_CARD_EXPIRES', 'Thời hạn thẻ tín dụng:');
define('ENTRY_SUB_TOTAL', 'Tổng phụ:');
define('ENTRY_TAX', 'Tiền thuế:');
define('ENTRY_SHIPPING', 'Giao hàng :');
define('ENTRY_TOTAL', 'Tổng cộng:');
define('ENTRY_DATE_PURCHASED', 'Ngày đặt hàng:');
define('ENTRY_STATUS', 'Tình trạng:');
define('ENTRY_DATE_LAST_UPDATED', 'Ngày cập nhật:');
define('ENTRY_NOTIFY_CUSTOMER', 'Thông báo tình trạng xử lý:');
define('ENTRY_NOTIFY_COMMENTS', 'Thêm bình luận:');
define('ENTRY_PRINTABLE', 'In hóa đơn giao hàng');
define('TEXT_INFO_HEADING_DELETE_ORDER', 'Xóa đơn hàng');
define('TEXT_INFO_DELETE_INTRO', 'Bạn có chắc chắn xóa đơn hàng không?');
define('TEXT_INFO_RESTOCK_PRODUCT_QUANTITY', 'Trở lại số tồn kho ban đầu'); // 'Restock product quantity'
define('TEXT_DATE_ORDER_CREATED', 'Ngày đặt hàng:');
define('TEXT_DATE_ORDER_LAST_MODIFIED', 'Ngày cập nhật:');
define('TEXT_INFO_PAYMENT_METHOD', 'Phương pháp thanh toán:');
define('TEXT_ALL_ORDERS', 'Tất cả các đơn hàng');
define('TEXT_NO_ORDER_HISTORY', 'Không có lịch sử đơn hàng');
define('EMAIL_SEPARATOR', '--------------------------------------------------------------------------------');
define('EMAIL_TEXT_SUBJECT', 'Thông báo tình trạng tiếp nhận đơn hàng');
define('EMAIL_TEXT_ORDER_NUMBER', 'Số tiếp nhận đơn hàng:');
define('EMAIL_TEXT_INVOICE_URL', 'Có thể xem thông tin về đơn hàng ở URL dưới đây.' . "\n");
define('EMAIL_TEXT_DATE_ORDERED', 'Ngày đặt hàng:');
define('EMAIL_TEXT_COMMENTS_UPDATE', '[Mục liên hệ]' . "\n%s");
define('ERROR_ORDER_DOES_NOT_EXIST', 'Lỗi: Không tồn tại đơn đặt hàng。');
define('SUCCESS_ORDER_UPDATED', 'Thành công: Tình trạng đơn hàng đã được cập nhập.');
define('WARNING_ORDER_NOT_UPDATED', 'Thông báo: Tình trạng đơn hàng đã không được thay đổi bất cứ điiều gì.');
define('ENTRY_EMAIL_TITLE', 'Tiêu đề mail：');
define('TEXT_CODE_HANDLE_FEE', 'Lệ phí:');
define('TEXT_CAN_NOT_SHOW','Không thể che giấu những vật đã dự trữ [Xong]');
define('TEXT_NOT_SHOW','Bạn có muốn ẩn không？');
define('TEXT_TELECOM_FINISH','Đã hoàn tất');
define('TEXT_TELECOM_UNFINISH','Chưa hoàn tất');
define('TEXT_TELECOM_SUCCESS','Thành công');
define('TEXT_TELECOM_UNSUCCESS','Thất bại');
define('TEXT_PAYPAL','Paypal');
define('TEXT_TELECOM','Viễn thông');
