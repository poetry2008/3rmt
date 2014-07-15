<?php
/*
  $Id$
*/

define('HEADING_CREATE', 'Check Customer Details');
define('DEFAULT_PAYMENT_METHOD', "Payment on Local Pickup. We accept cash, Interac, Visa and Master Card.");
define('TEXT_SUBTOTAL', "Subtotal :");
define('TEXT_DISCOUNT', "Discount :");
define('TEXT_DELIVERY', "Delivery :");
define('TEXT_TAX', "Tax :");
define('TEXT_TOTAL', "Total :");
define('TEXT_SELECT_CURRENCY', 'Chọn tiền tệ:');

define('HEADING_TITLE', 'Quy trình đặt hàng thủ công');
if(!defined('HEADING_CREATE'))define('HEADING_CREATE', 'Kiểm tra thông tin chi tiết của khách hàng đặt hàng thủ công:'); 

define('TEXT_SELECT_CUST', 'Chọn khách hàng:'); 
if(!defined('TEXT_SELECT_CURRENCY'))define('TEXT_SELECT_CURRENCY', 'Chọn tiền tệ:');
define('BUTTON_TEXT_SELECT_CUST', 'Chọn khách hàng:'); 
define('TEXT_OR_BY', 'Hoặc ID khách hàng:'); 
define('TEXT_STEP_1', 'Step 1 -Vui lòng chọn khách hàng và kiểm tra thông tin chi tiết');
define('BUTTON_SUBMIT', 'Kiểm tra');
define('ENTRY_CURRENCY','Tiền thanh toán');
define('CATEGORY_ORDER_DETAILS','Thiết lập tiền tệ');

define('CATEGORY_CORRECT', 'Thông tin khách hàng');
define('CREATE_ORDER_RED_TITLE_TEXT', 'Có lỗi ở thông tin nhập vào');
define('CREATE_PREORDER_PREDATE', 'Thời hạn hiệu lực');
define('CREATE_PREORDER_MUST_INPUT', 'Bắt buộc');
?>
