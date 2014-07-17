<?php
/*
  $Id$
*/
define('HEADING_TITLE', 'Quy trình đặt hàng thủ công');
define('HEADING_CREATE', 'Kiểm tra thông tin chi tiết của khách hàng đặt hàng thủ công:'); 

define('TEXT_SELECT_CUST', 'Chọn khách hàng:'); 
define('TEXT_SELECT_CURRENCY', 'Chọn tiền tệ:');
define('BUTTON_TEXT_SELECT_CUST', 'Chọn khách hàng:'); 
define('TEXT_OR_BY', 'Hoặc ID của khách hàng:'); 
define('TEXT_STEP_1', 'Step 1 - Hãy chọn khách hàng và kiểm tra thông tin chi tiết. ');
define('BUTTON_SUBMIT', 'Kiểm tra');
define('ENTRY_CURRENCY','Tiền thanh toán');
define('CATEGORY_ORDER_DETAILS','Thiết lập tiền tệ');

define('CATEGORY_CORRECT', 'Thông tin khách hàng');

define('CREATE_ORDER_STEP_ONE', 'Step 1 - Tìm kiếm khách hàng');
define('CREATE_ORDER_TITLE_TEXT', 'Xác nhận có hay không có dữ liệu đăng kí:');
define('CREATE_ORDER_SEARCH_TEXT',
    'Hãy nhập địa chỉ mail của khách hàng và click vào nút "Tìm kiếm"<br>
 Trường hợp tồn tại địa chỉ email của khách hàng, thông tin khách hàng sẽ được tự động nhập vào.<br>
 Trường hợp không tồn tại địa chỉ email của khách hàng, sẽ dời đến trang tạo guest account.');
define('CREATE_ORDER_EMAIL_TEXT', 'Địa chỉ mail:');
define('CREATE_ORDER_SEARCH_BUTTON_TEXT', 'Tìm kiếm');
define('CREATE_ORDER_NOTICE_ONE', 'Nếu có thay đổi, xin hãy vui lòng chỉnh sửa.');
define('CREATE_ORDER_CUSTOMERS_ERROR','Hãy chỉ định khách hàng của đơn đặt hàng.');
define('CREATE_ORDER_PAYMENT_TITLE', 'Phương pháp thanh toán');
define('CREATE_ORDER_PRODUCTS_ADD_TITLE','Thêm sản phẩm ');
define('ADDPRODUCT_TEXT_STEP', 'Step');
define('ADDPRODUCT_TEXT_STEP1', ' &laquo; Chọn danh mục. ');
define('ADDPRODUCT_TEXT_STEP2', ' &laquo; Chọn sản phẩm. ');
define('ADDING_TITLE', 'Thêm sản phẩm');
define('ADDPRODUCT_TEXT_CATEGORY_CONFIRM', 'OK');
define('ADDPRODUCT_TEXT_SELECT_PRODUCT', 'Chọn sản phẩm');
define('ADDPRODUCT_TEXT_PRODUCT_CONFIRM', 'OK');
define('ADDPRODUCT_TEXT_SELECT_OPTIONS', 'Chọn tùy chọn');
define('ADDPRODUCT_TEXT_OPTIONS_CONFIRM', 'OK');
define('ADDPRODUCT_TEXT_OPTIONS_NOTEXIST', 'Không có tùy chọn: skip..');
define('ADDPRODUCT_TEXT_CONFIRM_QUANTITY', 'Số lượng');
define('ADDPRODUCT_TEXT_CONFIRM_ADDNOW', 'Thêm vào');
define('EDIT_ORDERS_NUM_UNIT', 'Đơn vị ko');
define('TABLE_HEADING_UNIT_PRICE', 'Đơn giá');
define('EDIT_ORDERS_PRICE_UNIT', '円');
define('EDIT_ORDERS_PRO_LIST_TITLE', 'Sản phẩm đặt hàng');
define('TABLE_HEADING_NUM_PRO_NAME', 'Số lượng / Tên sản phẩm');
define('TABLE_HEADING_CURRENICY', 'Thuế suất');
define('TABLE_HEADING_PRODUCTS_PRICE', 'Đơn giá');
define('TABLE_HEADING_PRICE_BEFORE', 'Gía(chưa bao gồm thuế)');
define('TABLE_HEADING_PRICE_AFTER', 'Gía(đã bao gồm thuế)');
define('TABLE_HEADING_TOTAL_BEFORE', 'Tổng cộng(chưa bao gồm thuế)');
define('TABLE_HEADING_TOTAL_AFTER', 'Tổng cộng(đã bao gồm thuế)');
define('EDIT_ORDERS_ADD_PRO_READ', 'Không thể thay đổi đồng thời mục thêm sản phẩm và các mục khác.<b>Xin vui lòng tiến hành "Thêm sản phẩm " một cách đơn thể.</b>');
define('TABLE_HEADING_PRODUCTS_MODEL', 'Số mẫu');
define('CREATE_ORDER_PRODUCTS_WEIGHT','Tổng trọng lượng đã vượt quá phạm vi quy định. Xóa sản phẩm , hay là thay đổi số lượng（');
define('CREATE_ORDER_PRODUCTS_WEIGHT_ONE','）Xin vui lòng đặt hàng trong phạm vi kg.');
define('PRODUCTS_WEIGHT_ERROR_TOTAL_WEIGHT','Tổng trọng lượng（');
define('PRODUCTS_WEIGHT_ERROR_EXCESS_WEIGHT','）vượt quá trọng lượng quy định ');
define('PRODUCTS_WEIGHT_ERROR_DELETE_OR_NUMBER','Xóa sản phẩm, hoặc là hãy thay đổi số lượng（');
define('PRODUCTS_WEIGHT_ERROR_PLEASE_SET_KG','）Xin vui lòng đặt hàng trong phạm vi kg.');
define('CREATE_ORDER_FETCH_TIME_TITLE_TEXT', 'Ngày giờ giao hàng');
define('CREATE_ORDER_FETCH_DATE_TEXT', 'Ngày giao hàng:');
define('CREATE_ORDER_FETCH_TIME_TEXT', 'Giờ giao hàng:');
define('CREATE_ORDER_FETCH_ALLTIME_TEXT', '（Hiển thi 24 giờ đồng hồ）');
define('CREATE_ORDER_FETCH_TIME_SELECT_TEXT', 'Tùy chọn:');
define('CREATE_ORDER_COMMUNITY_TITLE_TEXT', 'Cột công ty chúng tôi sử dụng');
define('CREATE_ORDER_COMMUNITY_SEARCH_TEXT', ' Điều tra tín dụng:');
define('CREATE_ORDER_SEARCH_TWO_TEXT','Hãy chọn tên nhà cung cấp và click nút"Tìm kiếm".');
define('CREATE_ORDER_YEZHE_NAME_TEXT', 'Tên nhà cung cấp：');
define('CREATE_ORDER_SHIRU_TEXT', 'Purchase order');
define('CREATE_ORDER_TEL_TEXT', 'Số điện thoại:');
define('TEXT_CREATE_ADDRESS_BOOK','Xác định nơi giao hàng');
define('TEXT_USE_ADDRESS_BOOK','Giao hàng đến nơi đăng kí');
define('TEXT_TORIHIKIBOUBI_DEFAULT_SELECT','Hãy xác định ngày giờ giao hàng mong muốn');
define('ERROR_ORDER_DOES_NOT_EXIST','Lỗi: Đặt hàng không tồn tại.');
define('PRODUCT_ERROR','Không có sản phẩm. Khi tạo đơn hàng, trước tiên xin vui lòng thêm sản phẩm.');
define('ADDPRODUCT_TEXT_STEP1_TITLE', 'Chọn danh mục:');
define('ADDPRODUCT_TEXT_STEP2_TITLE', 'Chọn sản phẩm:');
define('TEXT_CREATE_CUSTOMERS_CONFIRM','Không có dữ liệu.Bạn có tạo mới dữ liệu khách hàng ？');
define('TEXT_MUST_ENTER','Vui lòng nhập vào.');
define('TEXT_EMAIL_ADDRESS_ERROR','Địa chỉ email đã được nhập vào chưa chính xác.');
?>
