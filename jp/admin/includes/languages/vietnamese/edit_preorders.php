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
define('TABLE_HEADING_ORDER_TOTAL', 'Tổng tiền của đặt hàng trước');
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
define('ENTRY_CUSTOMER_NAME_F', 'Tên(Furigana)');
define('ENTRY_CUSTOMER_COMPANY', 'Tên công ty');
define('ENTRY_CUSTOMER_ADDRESS', 'Địa chỉ');
define('ENTRY_CUSTOMER_SUBURB', 'Tên tòa nhà');
define('ENTRY_CUSTOMER_CITY', 'Quận huyện thành phố');
define('ENTRY_CUSTOMER_STATE', 'Tỉnh thành');
define('ENTRY_CUSTOMER_POSTCODE', 'Mã bưu điện');
define('ENTRY_CUSTOMER_COUNTRY', 'Tên nước');

define('ENTRY_SOLD_TO', 'Người mua:');
define('ENTRY_DELIVERY_TO', 'Nơi chuyển đến:');
define('ENTRY_SHIP_TO', 'Nơi chuyển đến:');
define('ENTRY_SHIPPING_ADDRESS', 'Nơi chuyển đến:');
define('ENTRY_BILLING_ADDRESS', 'Nơi thanh toán:');
define('ENTRY_PAYMENT_METHOD', 'Phương thức thanh toán:');
define('ENTRY_CREDIT_CARD_TYPE', 'Loại thẻ tín dụng:');
define('ENTRY_CREDIT_CARD_OWNER', 'Tên thẻ:');
define('ENTRY_CREDIT_CARD_NUMBER', 'Số thẻ:');
define('ENTRY_CREDIT_CARD_EXPIRES', 'Thời hạn hiệu lực của thẻ:');
define('ENTRY_SUB_TOTAL', 'Tổng phụ:');
define('ENTRY_TAX', 'Thuế tiêu thụ:');
define('ENTRY_SHIPPING', 'Phương thức giao hàng:');
define('ENTRY_TOTAL', 'Tổng cộng:');
define('ENTRY_DATE_PURCHASED', 'Ngày đặt hàng trước:');
define('ENTRY_STATUS', 'Status:');
define('ENTRY_DATE_LAST_UPDATED', 'Ngày cập nhật:');
define('ENTRY_NOTIFY_CUSTOMER', 'Thông báo tình trạng xử lý:');
define('ENTRY_NOTIFY_COMMENTS', 'Thêm nhận xét:');
define('ENTRY_PRINTABLE', 'In hóa đơn giao hàng');

define('TEXT_INFO_HEADING_DELETE_ORDER', 'Xóa đặt hàng trước');
define('TEXT_INFO_DELETE_INTRO', 'Bạn có thật sự muốn xóa đơn đặt hàng trước này?');
define('TEXT_INFO_RESTOCK_PRODUCT_QUANTITY', 'Trở về số tồn kho ban đầu');
define('TEXT_DATE_ORDER_CREATED', 'Ngày tạo:');
define('TEXT_DATE_ORDER_LAST_MODIFIED', 'Ngày cập nhật:');
define('TEXT_DATE_ORDER_ADDNEW', 'Thêm sản phẩm');
define('TEXT_INFO_PAYMENT_METHOD', 'Phương thức thanh toán:');

define('TEXT_ALL_ORDERS', 'Toàn bộ đơn đặt hàng trước');
define('TEXT_NO_ORDER_HISTORY', 'Không có lịch sử đặt hàng trước');

define('EMAIL_SEPARATOR', '--------------------------------------------------------------------------------');
define('EMAIL_TEXT_SUBJECT', 'Thông báo tình trạng tiếp nhận đặt hàng trước');
define('EMAIL_TEXT_ORDER_NUMBER', 'Số tiếp nhận đặt hàng trước: ');
define('EMAIL_TEXT_INVOICE_URL', 'Có thể xem thông tin về đặt hàng trước ở URL dưới đây.' . "\n");
define('EMAIL_TEXT_DATE_ORDERED', 'Ngày đặt hàng trước: ');
define('EMAIL_TEXT_STATUS_UPDATE',
'Tình trạng tiếp nhận đặt hàng trước sẽ trở thàn như sau.' . "\n"
.'Tình trạng tiếp nhận hiện tại: [ %s ]' . "\n\n");
define('EMAIL_TEXT_COMMENTS_UPDATE', '[Mục liên lạc]' . "\n\n%s");

define('ERROR_ORDER_DOES_NOT_EXIST', 'Lỗi: Không tồn tại đặt hàng trước.');
define('SUCCESS_ORDER_UPDATED', 'Thành công: Tính năng đặt hàng trước đã được cập nhật.');
define('WARNING_ORDER_NOT_UPDATED', 'Đã không thể thay đổi bất cứ thứ gì của tính năng đặt hàng.');

define('ADDPRODUCT_TEXT_CATEGORY_CONFIRM', 'OK');
define('ADDPRODUCT_TEXT_SELECT_PRODUCT', 'Chọn sản phẩm');
define('ADDPRODUCT_TEXT_PRODUCT_CONFIRM', 'OK');
define('ADDPRODUCT_TEXT_SELECT_OPTIONS', 'Chọn tùy chọn');
define('ADDPRODUCT_TEXT_OPTIONS_CONFIRM', 'OK');
define('ADDPRODUCT_TEXT_OPTIONS_NOTEXIST', 'Không có tùy chọn: bỏ qua..');
define('ADDPRODUCT_TEXT_CONFIRM_QUANTITY', 'Số lượng');
define('ADDPRODUCT_TEXT_CONFIRM_ADDNOW', 'Thêm');
define('CREATE_ORDER_PRODUCTS_WEIGHT','Tổng trọng lượng đã vượt quá phạm vi quy định, hãy đổi số lượng hoặc xóa sản phẩm（');
define('CREATE_ORDER_PRODUCTS_WEIGHT_ONE','）Vui lòng đặt hàng trong phạm vi kg.');
define('PRODUCTS_WEIGHT_ERROR_ONE','Tổng trọng lượng（');
define('PRODUCTS_WEIGHT_ERROR_TWO','）đã vượt quá trọng lượng quy định.');
define('PRODUCTS_WEIGHT_ERROR_THREE','hãy đổi số lượng hoặc xóa sản phẩm（');
define('PRODUCTS_WEIGHT_ERROR_FOUR','）Vui lòng đặt hàng trong phạm vi kg.');

define('EMAIL_TEXT_STORE_CONFIRMATION', '  Cảm ơn bạn đã đặt hàng trước.' . "\n" . 
'Chúng tôi xin giải thích về các mục liên lạc và tình trạng tiếp nhận đặt hàng như dưới đây.');
define('TABLE_HEADING_COMMENTS_ADMIN', '[Mục liên lạc]');
define('EMAIL_TEXT_STORE_CONFIRMATION_FOOTER', 
'Nếu có những câu hỏi liên quan đến tình trạng tiếp nhận đơn hàng, xin vui lòng liên lạc đến cửa hàng của chúng tôi' . "\n"
.'để được giải quyết' . "\n\n"
. EMAIL_SIGNATURE);
define('ADD_A_NEW_PRODUCT', 'Thêm sản phẩm');
define('CHOOSE_A_CATEGORY', ' --- Chọn danh mục sản phẩm --- ');
define('SELECT_THIS_CATECORY', 'Thực hiện chọn danh mục');
define('CHOOSE_A_PRODUCT', ' --- Chọn sản phẩm --- ');
define('SELECT_THIS_PRODUCT', 'Thực hiện chọn sản phẩm');
define('NO_OPTION_SKIPPED', 'Không có tùy chọn - bỏ qua....');
define('SELECT_THESE_OPTIONS', 'Thực hiện chọn tùy chọn');
define('SELECT_QUANTITY', ' Số lượng');
define('SELECT_ADD_NOW', 'Tiến hành thêm vào');
define('SELECT_STEP_ONE', 'STEP 1:');
define('SELECT_STEP_TWO', 'STEP 2:');
define('SELECT_STEP_THREE', 'STEP 3:');
define('SELECT_STEP_FOUR', 'STEP 4:');
define('TEXT_CODE_HANDLE_FEE', 'Lệ phí:');
define('EDIT_ORDERS_UPDATE_NOTICE', 'Hãy nhập nội dung muốn thay đổi một cách cẩn trọng.Hãy cố gắng kiểm tra xem liệu có nhập những kí tự thừa như khoảng trống,..hay không！');
define('EDIT_ORDERS_ID_TEXT', 'Số đặt hàng trước:');
define('EDIT_ORDERS_DATE_TEXT', 'Ngay đặt hàng trước:');
define('EDIT_ORDERS_CUSTOMER_NAME', 'Tên khách hàng:');
define('EDIT_ORDERS_EMAIL', 'Địa chỉ mail:');
define('EDIT_ORDERS_PAYMENT_METHOD', 'Phương thức thanh toán:');
define('EDIT_ORDERS_FETCHTIME', 'Ngày giờ giao hàng:');
define('EDIT_ORDERS_TORI_TEXT', 'Tùy chọn:');
define('EDIT_ORDERS_CUSTOMER_NAME_READ', '<font color="red">※</font>&nbsp;ở giữa tên và họ<font color="red">Vui lòng nhập khoảng trống 1 byte</font>.');
define('EDIT_ORDERS_FETCHTIME_READ', 
    '<font color="red">※</font>&nbsp;Ngày giờ・Định dạng thời gian:&nbsp;2008-01-01 10:30:00～10:45:00');
define('EDIT_ORDERS_TORI_READ', '<table><tr class="smalltext"><td><font color="red">※</font>&nbsp;Dùng copy pase:</td><td>Muốn giao dịch theo thời gian đã quy định</td><td>Nếu có thể nhanh hơn thời gian đã quy định thì càng tốt</td></tr></table>');
define('EDIT_ORDERS_PRO_LIST_TITLE', '2. Sản phẩm đặt hàng trước');
define('TABLE_HEADING_NUM_PRO_NAME', 'Số lượng / Tên sản phẩm');
define('TABLE_HEADING_CURRENICY', 'Thuế suất');
define('TABLE_HEADING_PRICE_BEFORE', 'Gía(chưa bao gồm thuế)');
define('TABLE_HEADING_PRICE_AFTER', 'Gía (đã bao gồm thuế)');
define('TABLE_HEADING_TOTAL_BEFORE', 'Tổng cộng(chưa bao gồm thuế)');
define('TABLE_HEADING_TOTAL_AFTER', 'Tổng cộng(đã bao gồm thuế)');
define('EDIT_ORDERS_ADD_PRO_READ', 'Không thể thay đổi cùng một lúc mục thêm sản phẩm và các mục khác.<b>Vui lòng thực hiện riêng mục "thêm san phẩm".</b>');
define('EDIT_ORDERS_FEE_TITLE_TEXT', '3.Điểm chiết khấu, lệ phí, hạ giá');
define('TABLE_HEADING_FEE_MUST', 'Mục chú ý');
define('EDIT_ORDERS_OTTOTAL_READ', 'Hãy kiểm tra chắc chắn tổng số tiền có khớp hay không.');
define('EDIT_ORDERS_TOTALDETAIL_READ', 'Khách hàng là guest. Không thể nhập điểm chiết khấu.');
define('EDIT_ORDERS_TOTALDETAIL_READ_ONE', 'Trong trường hợp giảm giá, vui lòng nhập kí hiệu −(âm).');
define('EDIT_ORDERS_CONFIRMATION_READ', '<font color="red">Quan trọng:</font>&nbsp;<b>Trường hợp đã thay đổi yếu tố cấu thành giá, vui lòng click vào nút[<font color="red">xác nhận nội dung đặt trước</font>]và kiểm tra xem tổng số tiền đã thống nhât hay chưa.&nbsp;⇒</b>');
define('EDIT_ORDERS_CONFIRM_BUTTON', 'Xác nhận nội dung đặt hàng trước');
define('EDIT_ORDERS_ITEM_FOUR_TITLE', '4. Thông báo nhận xét, status đặt hàng trước');
define('EDIT_ORDERS_SEND_MAIL_TEXT', 'Gởi mail:');
define('EDIT_ORDERS_RECORD_TEXT', 'Ghi nhận xét:');
define('EDIT_ORDERS_RECORD_READ', '←Không kiểm tra chỗ này');
define('EDIT_ORDERS_RECORD_ARTICLE', 'Những kí tự đã được chèn ở đây sẽ được chèn vào nguyên văn mail.');
define('EDIT_ORDERS_ITEM_FIVE_TITLE', '5. Cập nhật dữ liệu');
define('EDIT_ORDERS_FINAL_CONFIRM_TEXT', 'Đã xác nhận lần cuốichưaĐã xác nhận lần cuốichưa？');
define('EDIT_NEW_ORDERS_CREATE_TITLE', 'Tạo đơn đặt hàng trước');
define('EDIT_NEW_ORDERS_CREATE_READ', '【Quan trọng】Không có chỉnh sửa đặt hàng trước.Hệ thống tạo đặt trước mới.');
define('EDIT_ORDERS_ORIGIN_VALUE_TEXT', '（Gía trị ban đầu）');
define('ERROR_INPUT_PRICE_NOTICE', 'Hãy viết đơn giá');
define('EDIT_ORDERS_PRICE_UNIT', 'Yên');
define('EDIT_ORDERS_NUM_UNIT', 'Ko');
define('EDIT_ORDERS_PREDATE_TEXT', 'Thờ hạn hiệu lực:');
define('ERROR_VILADATE_NEW_PREORDERS', 'Đã hủy cập nhật.');
define('ERROR_NEW_PREORDERS_POINT', 'Không đủ điểm. Điểm có thể nhập là <b>%s</b>.');
define('NOTICE_NEW_PRERODERS_PRODUCTS_DEL', 'Đã xóa sản phẩm.<font color="red">Không gởi được mail.</font>');
define('ERROR_NEW_PREORDERS_UPDATE', 'Đã phát sinh lỗi.Có khả năng không tiến hành xử lý thông thường.');
define('NEW_PREORDERS_NOTE_TEXT', 'Lưu ý viết trên mail:');
define('TEXT_EMAIL_TITLE','Tiêu đề mail：');
define('TEXT_DATE_NUM_ERROR','Vui lòng nhập chính xác ngày giờ giao hàng.');
define('NOTICE_NEW_PREORDERS_PRODUCTS_DEL','Đã xóa sản phẩm.');
define('TABLE_HEADING_PRODUCTS_PRICE', 'Đơn giá');
define('TEXT_REQUIRE','*Bắt buộc');
define('ADDRESS_ERROR_OPTION_ITEM_TEXT_NULL','Mục bắt buộc');
define('ADDRESS_ERROR_OPTION_ITEM_TEXT_TYPE_WRONG','Vui lòng nhập chính xác');
define('TEXT_SELECT_PAYMENT_ERROR','<font color="red">Phương thức thanh toán được chọn hiện nay đang bị vô hiệu. Vui lòng chọn lại.</font>');
define('TEXT_NO_PAYMENT_ENABLED','<font color="red">Không có phương thức thanh toán hữu hiệu.</font>');
?>
