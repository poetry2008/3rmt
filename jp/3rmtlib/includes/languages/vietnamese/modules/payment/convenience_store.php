<?php
/*
  $Id$
*/

// 便利店决算
  define('TS_MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_TITLE', 'Thanh toán cửa hàng tiện ích');
  define('TS_MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_DESCRIPTION', 'Thanh toán cửa hàng tiện ích (Khớp với phí chi trả)');
  define('TS_MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_EXPLAIN', 'Xin sử dụng các dịch vụ của LAWSON、スリーエフ、MINI STOP、サークルK、SUNKUS。<br>Cần phân biệt trường hợp đóng phí 200 yên khi số tiền thanh toán chưa đủ 30.000 yên và trường hợp đóng phí 350 yên khi số tiền thanh toán vượt trên 30.000 yên');
  define('TS_MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_PROCESS_CON', 'Xin sử dụng dịch vụ của LAWSON、スリーエフ、MINI STOP、サークルK、SUNKUS');
  define('TS_MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_FOOTER', '<p color="#ff0000">※ E-mail thanh toán tiện ích sẽ được gửi trong giờ làm việc (Từ 10h đến 24h)<br>※ Cũng có trường hợp gia hạn thời gian gửi mail do tình trạng đơn hàng lẫn lộn</p><br><img src="images/rmt_cr.gif">');
  /*
  define('TS_MODULE_PAYMENT_CONVENIENCE_STORE_IMG_FOOTER', '
  <table width="100%" cellspacing="3" cellpadding="0" border="0" id="convenience_img">
  <tr>
  <td align="center"><img src="images/rmt_cr.gif"></td>
  </tr>
  </table>
  ');
  */
  define('TS_MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_FEE', 'Phí thanh toán tiện ích:');
  define('TS_MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_OVERFLOW_ERROR', 'Không thể xử lý do số tiền mua đã vượt quá giới hạn thanh toán tiện ích');
  define('TS_MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_MAILFOOTER',C_CONVENIENCE_STORE."\n\n". EMAIL_SIGNATURE);
  define('TS_MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_EMAIL_FOOTER', '');
  
  define('TS_MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_SID', 'Mã giao dịch:');
  define('TS_MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_ZIP_CODE', 'Mã bưu điện:');
  define('TS_MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_ADDRESS', 'Địa chỉ:');
  define('TS_MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_NAME', 'Tên:');
  define('TS_MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_L_NAME', 'Giới tính:');
  define('TS_MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_F_NAME', 'Tên:');
  define('TS_MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_TEL', 'Số điện thoại:');
  define('TS_MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_ERROR_MESSAGE', 'Đã phát sinh lỗi khi xử lý thanh toán tiện ích, xin xác nhận lại nội dung đã nhập và thử lại lần nữa');
  define('TS_MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_ERROR_MESSAGE_NOE', 'Địa chỉ mail máy tính và địa chỉ mail máy tính dùng cho việc xác nhận không thống nhất với nhau');
  define('TS_MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_ERROR_MESSAGE_NOM', 'Không thể đăng ký bằng địa chỉ mal đã nhập. Xin nhập mail máy tính');
  define('TS_MODULE_PAYMENT_CONVENIENCE_EMAIL_TEXT', 'Địa chỉ mail máy tính:');
  define('TS_MODULE_PAYMENT_CONVENIENCE_EMAIL_CONFIRMATION_TEXT', 'Địa chỉ mail máy tính (Dùng cho việc xác nhận):');
  if(NEW_STYLE_WEB===true){
  define('TS_MODULE_PAYMENT_CONVENIENCE_MUST_INPUT', '<span><font color="red">*Cần thiết</font></span>');
  }else{
  define('TS_MODULE_PAYMENT_CONVENIENCE_MUST_INPUT', '<small><font color="#AE0E30">(*Cần thiết)</font></small>');
  }
  define('TS_MODULE_PAYMENT_CONVENIENCE_STORE_FIELDS_DESCRIPTION', 'Không sử dụng địa chỉ mail điện thoại di động. Xin nhập địa chỉ mail máy tính ở hàng bên dưới<br>');
  define('TS_MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_CONFIRMATION',"
Có thể sử dụng các dịch vụ thanh toán cửa hàng tiện ích 
LAWSON、スリーエフ、MINI STOP、サークルK、SUNKUS 		
	Địa chỉ mail máy tính:#USER_MAIL#");
//define('TS_MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_DESCRIPTION',"Xin sử dụng dịch vụ của LAWSON、スリーエフ、MINI STOP、サークルK、SUNKUS
//Cần phân biệt trường hợp đóng phí 200 yên khi số tiền thanh toán chưa đủ 30.000 yên và trường hợp đóng phí 350 yên khi số tiền thanh toán vượt trên 30.000 yên");

  define('TS_TEXT_HANDLE_FEE_CONFIRMATION', 'Phí:');
  define('TS_MODULE_PAYMENT_CONVENIENCE_STORE_NORMAL', 'Bình thường');
