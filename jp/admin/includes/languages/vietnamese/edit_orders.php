<?php
/*
  $Id$
*/

define('HEADING_TITLE', 'Chỉnh sửa nội dung đặt hàng');
define('HEADING_TITLE_SEARCH', 'ID đặt hàng:');
define('HEADING_TITLE_STATUS', 'Status:');
define('ADDING_TITLE', 'Thêm sản phẩm');

define('ENTRY_UPDATE_TO_CC', '(Update to <b>Credit Card</b> to view CC fields.)');
define('TABLE_HEADING_COMMENTS', 'Nhận xét');
define('TABLE_HEADING_EMAIL_COMMENTS', 'Mẫu mail');
define('TABLE_HEADING_CUSTOMERS', 'Tên khách hàng');
define('TABLE_HEADING_ORDER_TOTAL', 'Tổng tiền của đơn đặt hàng');
define('TABLE_HEADING_DATE_PURCHASED', 'Ngày đặt hàng');
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
define('ENTRY_CUSTOMER_NAME', 'Tên khách hàng:');
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
define('ENTRY_DATE_PURCHASED', 'Ngày đặt hàng:');
define('ENTRY_STATUS', 'Status:');
define('ENTRY_EMAIL_TITLE', 'Tiêu đề mail:');
define('ENTRY_DATE_LAST_UPDATED', 'Ngày cập nhật:');
define('ENTRY_NOTIFY_CUSTOMER', 'Thông báo tình trạng xử lý:');
define('ENTRY_NOTIFY_COMMENTS', 'Thêm nhận xét:');
define('ENTRY_PRINTABLE', 'In hóa đơn giao hàng');

define('TEXT_INFO_HEADING_DELETE_ORDER', 'Xóa đặt hàng');
define('TEXT_INFO_DELETE_INTRO', 'Bạn có thật sự muốn xóa đơn đặt hàng này?');
define('TEXT_INFO_RESTOCK_PRODUCT_QUANTITY', 'Trở về số tồn kho ban đầu');
define('TEXT_DATE_ORDER_CREATED', 'Ngày tạo:');
define('TEXT_DATE_ORDER_LAST_MODIFIED', 'Ngày cập nhật:');
define('TEXT_DATE_ORDER_ADDNEW', 'Thêm sản phẩm');
define('TEXT_INFO_PAYMENT_METHOD', 'Phương thức thanh toán:');

define('TEXT_ALL_ORDERS', 'Toàn bộ đơn hàng');
define('TEXT_NO_ORDER_HISTORY', 'Không có lịch sử đặt hàng ');

define('EMAIL_SEPARATOR', '--------------------------------------------------------------------------------');
define('EMAIL_TEXT_SUBJECT', 'Thông báo tình trạng tiếp nhận đặt hàng');
define('EMAIL_TEXT_ORDER_NUMBER', 'Số tiếp nhận đặt hàng');
define('EMAIL_TEXT_INVOICE_URL', 'Có thể xem thông tin về đặt hàng ở URL dưới đây.' . "\n");
define('EMAIL_TEXT_DATE_ORDERED', 'Ngày đặt hàng: ');
define('EMAIL_TEXT_STATUS_UPDATE',
'Tình trạng tiếp nhận đặt hàng sẽ trở thàn như sau.' . "\n"
.'Tình trạng tiếp nhận hiện tại: [ %s ]' . "\n\n");
define('EMAIL_TEXT_COMMENTS_UPDATE', '[Mục liên lạc]' . "\n\n%s");

define('ERROR_ORDER_DOES_NOT_EXIST', 'Lỗi: Không tồn tại đặt hàng.');
define('SUCCESS_ORDER_UPDATED', 'Thành công: Tính năng đặt hàng đã được cập nhật.');
define('WARNING_ORDER_NOT_UPDATED', 'Cảnh báo: Đã không thể thay đổi bất cứ thứ gì của tính năng đặt hàng.');

define('ADDPRODUCT_TEXT_CATEGORY_CONFIRM', 'OK');
define('ADDPRODUCT_TEXT_SELECT_PRODUCT', 'Chọn sản phẩm');
define('ADDPRODUCT_TEXT_PRODUCT_CONFIRM', 'OK');
define('ADDPRODUCT_TEXT_SELECT_OPTIONS', 'Chọn tùy chọn');
define('ADDPRODUCT_TEXT_OPTIONS_CONFIRM', 'OK');
define('ADDPRODUCT_TEXT_OPTIONS_NOTEXIST', 'Không có tùy chọn: bỏ qua..');
define('ADDPRODUCT_TEXT_CONFIRM_QUANTITY', 'Số lượng');
define('ADDPRODUCT_TEXT_CONFIRM_ADDNOW', 'Thêm');

define('EMAIL_TEXT_STORE_CONFIRMATION', ' Cảm ơn bạn đã đặt hàng.' . "\n" . 
'Chúng tôi xin giải thích về các mục liên lạc và tình trạng tiếp nhận đặt hàng như dưới đây.');
define('TABLE_HEADING_COMMENTS_ADMIN', '[Mục liên lạc]');
define('EMAIL_TEXT_STORE_CONFIRMATION_FOOTER', 
'Nếu có những câu hỏi liên quan đến tình trạng tiếp nhận đơn hàng, xin vui lòng liên lạc đến cửa hàng của chúng tôi ' . "\n"
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
define('TEXT_CODE_SHIPPING_FEE', 'Phí vận chuyển:');
define('EDIT_ORDERS_UPDATE_NOTICE', 'Hãy nhập nội dung muốn thay đổi một cách cẩn trọng.Hãy cố gắng kiểm tra xem liệu có nhập những kí tự thừa như khoảng trống,..hay không！');
define('EDIT_ORDERS_ID_TEXT', 'Số đặt hàng:');
define('EDIT_ORDERS_DATE_TEXT', 'Ngay đặt hàng:');
define('EDIT_ORDERS_CUSTOMER_NAME', 'Tên khách hàng:');
define('EDIT_ORDERS_EMAIL', 'Địa chỉ mail:');
define('EDIT_ORDERS_PAYMENT_METHOD', 'Phương thức thanh toán:');
define('EDIT_ORDERS_FETCHTIME', 'Thời gian giao hàng:');
define('EDIT_ORDERS_TORI_TEXT', 'Tùy chọn:');
define('EDIT_ORDERS_CUSTOMER_NAME_READ', '<font color="red">※</font>&nbsp;Ở giữa họ và tên<font color="red">hãy nhập khoảng trống 1byte</font>.');
define('EDIT_ORDERS_TORI_READ', '<table><tr class="smalltext"><td><font color="red">※</font>&nbsp;Dùng coppy paste:</td><td>Muốn giao dịch như thời gian đã quy định</td><td>Nếu có thể giao dịch sớm hơn thời gian đã quy định thì càng tốt</td></tr></table>');
define('EDIT_ORDERS_PRO_LIST_TITLE', '2. Sản phẩm đặt hàng');
define('TABLE_HEADING_NUM_PRO_NAME', 'Số lượng / Tên sản phẩm');
define('TABLE_HEADING_CURRENICY', 'Mức thuế suất');
define('TABLE_HEADING_PRICE_BEFORE', 'Gía (chưa bao gồm thuế)');
define('TABLE_HEADING_PRICE_AFTER', 'Gía(bao gồm thuế)');
define('TABLE_HEADING_TOTAL_BEFORE', 'Tổng cộng(chưa bao gồm thuế)');
define('TABLE_HEADING_TOTAL_AFTER', 'Tổng cộng(bao gồm thuế)');
define('EDIT_ORDERS_ADD_PRO_READ', 'Không thể thay đổi cùng một lúc mục thêm sản phẩm và các mục khác.<b>Hãy thực hiện riêng mục " thêm sản phẩm ".</b>');
define('EDIT_ORDERS_FEE_TITLE_TEXT', '3. Điểm chiết khấu, lệ phí, hạ giá');
define('TABLE_HEADING_FEE_MUST', 'Mục chú ý');
define('EDIT_ORDERS_OTTOTAL_READ', 'Hãy kiểm tra chắc chắn tổng số tiền có khớp hay không.');
define('EDIT_ORDERS_TOTALDETAIL_READ', 'Khách hàng là guest.Không thể nhập điểm chiết khấu.');
define('EDIT_ORDERS_TOTALDETAIL_READ_ONE', 'Trong trường hợp giảm giá, vui lòng nhập kí hiệu −(âm).');
define('EDIT_ORDERS_CONFIRMATION_READ', '<font color="red">Quan trọng:</font>&nbsp;<b>Trường hợp đã thay đổi những yếu tố cấu thành giá, vui lòng click vào nút[<font color="red">xác nhận nội dung đặt hàng</font>] và kiểm tra xem tổng số tiền đã thống nhât hay chưa.&nbsp;⇒</b>');
define('EDIT_ORDERS_CONFIRM_BUTTON', 'Xác nhận nội dung đặt hàng');
define('EDIT_ORDERS_ITEM_FOUR_TITLE', '4. Thông báo nhận xét, status đặt hàng');
define('EDIT_ORDERS_SEND_MAIL_TEXT', 'Gởi mail:');
define('EDIT_ORDERS_RECORD_TEXT', 'Ghi nhận xét:');
define('EDIT_ORDERS_RECORD_READ', '←Không kiểm tra chỗ này');
define('EDIT_ORDERS_RECORD_ARTICLE', 'Những kí tự đã được chèn ở đây sẽ được chèn vào nguyên văn mail.');
define('EDIT_ORDERS_ITEM_FIVE_TITLE', '5. Cập nhật dữ liệu');
define('EDIT_ORDERS_FINAL_CONFIRM_TEXT', 'Đã xác nhận lần cuốichưa?');
define('EDIT_NEW_ORDERS_CREATE_TITLE', 'Tạo đơn đặt hàng');
define('EDIT_NEW_ORDERS_CREATE_READ', '【Quan trọng】Không có chỉnh sửa đặt hàng. Hệ thống tạo đặt hàng mới.');
define('EDIT_ORDERS_ORIGIN_VALUE_TEXT', '（Gía trị ban đầu）');
define('ERROR_INPUT_PRICE_NOTICE', 'Hãy viết đơn giá');
define('EDIT_ORDERS_PRICE_UNIT', 'Yên');
define('EDIT_ORDERS_NUM_UNIT', 'Ko');

define('TEXT_CREATE_ADDRESS_BOOK','Quy định nơi giao hàng');
define('TEXT_USE_ADDRESS_BOOK','Giao hàng đến nơi đăng kí');
define('TEXT_TORIHIKIBOUBI_DEFAULT_SELECT','Vui lòng quy định thời gian chuyển đến theo mong muốn');
define('CREATE_ORDER_FETCH_DATE_TEXT', 'Ngày giao hàng mong muốn:');
define('CREATE_ORDER_FETCH_TIME_TEXT', 'Thời gian giao hàng mong muốn:');


define('TEXT_SHIPPING_FEE','Chi phí vận chuyển:');
define('TEXT_SHIPPING_ADDRESS','Thông tin địa chỉ▼');
define('ADDRESS_ERROR_OPTION_ITEM_TEXT_NULL','Mục bắt buộc');
define('ADDRESS_ERROR_OPTION_ITEM_TEXT_TYPE_WRONG','Vui lòng nhập chính xác');
define('ADDRESS_ERROR_OPTION_ITEM_TEXT_NUM_MAX','Vượt quá số kí tự có thể nhập vào');
define('ADDRESS_ERROR_OPTION_ITEM_TEXT_NUM_MIN','Tối thiểu');
define('ADDRESS_ERROR_OPTION_ITEM_TEXT_NUM_MIN_1','Cần phải nhiều hơn kí tự');
define('TABLE_HEADING_PRODUCTS_PRICE', 'Đơn giá');
define('CALC_PRODUCTS_TEXT', 'Tính toán');
define('CREATE_ORDER_PRODUCTS_WEIGHT','Tổng trọng lượng đã vượt quá phạm vi quy định, hãy đổi số lượng hoặc xóa sản phẩm（');
define('CREATE_ORDER_PRODUCTS_WEIGHT_ONE','）Vui lòng đặt hàng trong phạm vi kg.');
define('PRODUCTS_WEIGHT_ERROR_ONE','Tổng trọng lượng（');
define('PRODUCTS_WEIGHT_ERROR_TWO','）đã vượt quá trọng lượng quy định.');
define('PRODUCTS_WEIGHT_ERROR_THREE','hãy đổi số lượng hoặc xóa sản phẩm（');
define('PRODUCTS_WEIGHT_ERROR_FOUR','）Vui lòng đặt hàng trong phạm vi kg.');
define('TEXT_CANCEL_UPDATE','Đã hủy cập nhật.');
define('TEXT_DATE_ERROR','Định dạng ngày giờ sai. ');
define('TEXT_DATE_NUM_ERROR','Vui lòng nhập chính xác ngày giờ giao hàng.');
define('TEXT_INPUT_DATE_ERROR','Đã không nhập ngày giờ.');
define('TEXT_NO_ENOUGH_POINT','Không đủ điểm.Điểm có thể nhập là ');
define('TEXT_LS',' .');
define('TEXT_HOUR','Giờ');
define('TEXT_MIN','Phút');
define('TEXT_TWENTY_FOUR_HOUR','　（Hiển thị 24 giờ đồng hồ）');
define('TEXT_DATE_YEAR','年');
define('TEXT_DATE_MONTH','月');
define('TEXT_DATE_DAY','日');
define('ORDERS_PRODUCTS','Sản phẩm đặt hàng');
define('QTY_NUM','Số lượng');
define('TEXT_CHARACTER_NAME_SEND_MAIL','※ Chúng tôi sẽ thông báo tên nhân vật vào E-mail 10 phút trước khi giao dịch.');
define('TEXT_CHARACTER_NAME_CONFIRM_SEND_MAIL','※ Chúng tôi sẽ thông báo tên nhận vật vào E-mail sau khi xác nhận thanh toán.');
define('TEXT_POINT','Chiết khấu　　　　　　：-');
define('TEXT_HANDLE_FEE','Lệ phí　　　　　：');
define('TEXT_PAYMENT_AMOUNT','Số tiền thanh toán　　　：');
define('TEXT_TRANSACTION_FEE','Lệ phí thanh toán');
define('TEXT_HANDLE_FEE_ONE','Lệ phí');
define('TEXT_ORDERS_UPDATE','Đã tiếp nhận thay đổi nội dung đặt hàng【');
define('TEXT_EMAIL_ORDERS_UPDATE','Gởi xong：Đã tiếp nhận thay đổi nội dung đặt hàng【');
define('TEXT_PRODUCTS_DELETE','Đã xóa sản phẩm.<font color="red">Không gởi được mail.</font>');
define('TEXT_ERROR_NO_SUCCESS','Phát sinh lỗi.Có khả năng không tiến hành xử lý môt cách thông thường.');
define('TEXT_REQUIRE','*Bắt buộc');
define('TEXT_ADDRESS_INFO_HIDE','Thông tin địa chỉ▲');
define('TEXT_ADDRESS_INFO_SHOW','Thông tin địa chỉ▼');
define('TEXT_CUSTOMER_INPUT','Khách hàng này là hội viên. Điểm có khả năng nhập vào là ');
define('TEXT_REMAINING','Còn lại');
define('TEXT_SUBTOTAL','（Tổng cộng');
define('TEXT_RIGHT_BRACKETS','）');
define('TEXT_INPUT_POSITIVE_NUM',' .Không cần nhập kí hiệu −(âm).Hãy cố gắng nhập con số chính xác（！');
define('TEXT_NOTICE_PAYMENT','Thông báo thanh toán*');
define('TEXT_POINT_ONE','▼Chiết khấu　　　　　　：-');
define('TEXT_HANDLE_FEE_ONE','▼Lệ phí　　　　　：');
define('TEXT_PAYMENT_AMOUNT_ONE','▼Số tiền thanh toán　　　：');
define('TEXT_POINT_DISCOUNT','▼Điểm chiết khấu');
define('TEXT_POINT_DISCOUNT_ONE','▼ Điểm chiết khấu');
define('TEXT_SHIPPING_FEE_ONE','▼Lệ phí　　　　　：');
define('ORDERS_PRODUCTS_ONE','▼Sản phẩm đặt hàng');
define('TEXT_ADDRESS_INFO_LEFT','▼Thông tin địa chỉ');
define('TEXT_ORDERS_SEND_MAIL','Cảm ơn bạn đã đặt hàng【');
define('TEXT_CARD_PAYMENT','Về việc thanh toán thẻ tín dụng【');
define('TEXT_SEND_MAIL_CARD_PAYMENT','Gởi xong：Về việc thanh toán thẻ tín dụng【');
define('TEXT_ORDER_NOT_CHOOSE','Bạn không thể chọn nhiều hơn.');
define('TEXT_SAVE_FINISHED','Hoàn thành lưu');
define('TEXT_COPY_TO_CLIPBOARD','Đã coppy vào clipboard！');
define('TEXT_PASSWORD_NOT','Sai mật khẩu');
define('TEXT_ORDER_NOT_CHOOSE','Bạn không thể chọn nhiều hơn.');
define('TEXT_NO_OPTION_ORDER','Vẫn chưa chọn đơn đặt hàng.');
define('TEXT_COMPLETION_TRANSACTION','Hoàn thành giao dịch');
define('TEXT_PRESERVATION','Lưu');
define('TEXT_BROWER_REJECTED','Đã bị trình duyệt từ chối！\n Nhập "about:config" vào cộtđịa chỉ trình duyệt và nhấn phím Enter \n Và hãy làm số "signed.applets.codebase_principal_support" thành "true"');
define('TEXT_PLEASE_PASSWORD','Vui lòng nhập mật khẩu sử dụng một lần\r\n');
define('TEXT_PASSWORD_NOT','Sai mật khẩu');


define('TEXT_PRODUCTS_NUM','Không đủ số lượng sản phẩm. Bạn có tạo đặt hàng?');
define('TEXT_DATE_TIME_ERROR','Ngày giờ trong quá khứ đã được chọn. Bạn có lưu?');
define('ORDERS_PRODUCT_ERROR','Không có sản phẩm. Vui lòng thêm sản phẩm.');
define('EDIT_ORDERS_TOTAL_DETAIL_READ', 'Khách hàng này là guest. Không thể nhập điểm chiết khấu.');
define('TEXT_SELECT_PAYMENT_ERROR','<font color="red">Phương thức thanh toán được chọn hiện tại đang bị vô hiệu. Vui lòng chọn lại.</font>');
define('TEXT_BILLING_ADDRESS_INFO_HIDE','Nơi thanh toán▲');
define('TEXT_BILLING_ADDRESS_INFO_SHOW','Nơi thanh toán▼');
?>
