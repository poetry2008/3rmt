<?php
/*
  $Id$
*/

// Ngân hàng RAKUTEN
  define('TS_MODULE_PAYMENT_TEST_TEXT_TITLE', 'Ngân hàng RAKUTEN');
  define('TS_MODULE_PAYMENT_TEST_TEXT_DESCRIPTION', 'Ngân hàng RAKUTEN(代金に手数料が連動)');
  define('TS_MODULE_PAYMENT_TEST_FIELDS_DESCRIPTION', 'Hãy nhập số điện thoại vào cột nhập vào dưới đây');
  define('TS_MODULE_PAYMENT_TEST_TEXT_EXPLAIN','Trường hợp chuyển tiền đến ngân hàng RAKUTEN, Hãy vui lòng chọn lựa.<br>Phí chuyển tiền sẽ do khách hàng thanh toán');
  define('TS_MODULE_PAYMENT_TEST_TEXT_PROCESS_CON', 'Vui lòng sử dụng LAWSON、Three F、MINI STOP、Circle K、SUNKUS');
  define('TS_MODULE_PAYMENT_TEST_TEXT_FOOTER', '※Việc gửi sheet SmartPit sẽ tiến hành trong thời gian làm việc của công ty chúng tôi.');
  /*
  define('TS_MODULE_PAYMENT_TEST_IMG_FOOTER', '
  <table width="100%" cellspacing="3" cellpadding="0" border="0" id="convenience_img">
  <tr>
  <td align="center"><img src="images/rmt_cr.gif"></td>
  </tr>
  </table>
  ');
  */
  define('TS_MODULE_PAYMENT_TEST_TEXT_FEE', 'Phí ngân hàng RAKUTEN:');
  define('TS_MODULE_PAYMENT_TEST_TEXT_OVERFLOW_ERROR', 'Vì số tiền mua vượt qua vượt qua giới hạn chuyển tiền ngân hàng, nên không thể xử lí được');
  define('TS_MODULE_PAYMENT_TEST_TEXT_MAILFOOTER',nl2br(C_TEST."\n\n"));
  define('TS_MODULE_PAYMENT_TEST_TEXT_TELNUMBER_FOOTER', '');
  
  define('TS_MODULE_PAYMENT_TEST_TEXT_SID', '取引コード:');
  define('TS_MODULE_PAYMENT_TEST_TEXT_ZIP_CODE', 'Mã bưu chính:');
  define('TS_MODULE_PAYMENT_TEST_TEXT_ADDRESS', 'Địa chỉ:');
  define('TS_MODULE_PAYMENT_TEST_TEXT_NAME', 'Tên:');
  define('TS_MODULE_PAYMENT_TEST_TEXT_L_NAME', 'Họ:');
  define('TS_MODULE_PAYMENT_TEST_TEXT_F_NAME', 'Tên:');
  define('TS_MODULE_PAYMENT_TEST_TEXT_TEL', 'Số điện thoại:');
  define('TS_MODULE_PAYMENT_TEST_TEXT_ERROR_MESSAGE', 'Xảy ra lỗi trong quá trình xử lí của ngân hàng Rakuten. Xin vui lòng kiểm tra lại nội dung nhập vào, và nhập lại lần nữa.');
  define('TS_MODULE_PAYMENT_TEST_TEXT_ERROR_MESSAGE_NOE', 'Số điện thoại và số điện thoại (để xác nhận) không thống nhất.');
  define('TS_MODULE_PAYMENT_TEST_TEXT_ERROR_MESSAGE_NOM', 'Bao gồm những kí tự không thể sử dụng được, do sai hình thức số điện thoại, lỗi nhập vào,..Xin vui lòng kiểm tra lại nội dung nhập vào, và nhập lại một lần nữa.');
  define('TS_MODULE_PAYMENT_RAKUTEN_TELNUMBER_TEXT', 'Số điện thoại:');
  define('TS_MODULE_PAYMENT_RAKUTEN_TELNUMBER_CONFIRMATION_TEXT', 'Số điện thoại(để xác nhận):');
  if(NEW_STYLE_WEB===true){
  define('TS_MODULE_PAYMENT_RAKUTEN_MUST_INPUT', '<span><font color="red">*cần thiết</font></span>');
  }else{
   define('TS_MODULE_PAYMENT_RAKUTEN_MUST_INPUT', '<small><font color="#AE0E30">(*cần thiết)</font></small>');
  }

  define('TS_MODULE_PAYMENT_TEST_TEXT_CONFIRMATION', 'Số điện thoại:#TELNUMBER#

 Hãy chuyển tiền đến tào khoản dưới đây.
------------------------------------------Tên ngân hàng　：　Ngân hàng RAKUTEN（Ngân hàng eBank cũ）
Tên chi nhánh　　：　Loại tài khoản chi nhánh Waltz　：　Tên tài khoản thông thường　：　カ）Số tài khoản IIMY　：　7003965
------------------------------------------
※ Xin vui lòng đảm bảo chuyển tiền bằng tên đã đăng kí khi đặt hàng.
※ Phí chuyển tiền sẽ do khách hàng thanh toán.
※ Xin hãy vui lòng liên hệ với chúng tôi trong trường hợp không thể thanh toán cho đến thời gian giao dịch.
 Trường hợp không liên lạc, tiền dự trữ tồn kho sẽ có thể bị hủy.
※ Xin vui lòng chuyển tiền trong vòng 7 ngày kể từ ngày đặt hàng
※ SẼ thành lập hợp đồng vào thời điểm '.COMPANY_NAME.' xác nhận số tiền đã thanh toánご入金を'.COMPANY_NAME.'が確認した時点でご契約の成立となります。
');
define('TS_MODULE_PAYMENT_RAKUTEN_NORMAL', 'Bình thường');
