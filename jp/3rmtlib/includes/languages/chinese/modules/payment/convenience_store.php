<?php
/*
  $Id$
*/

// コンビニ決済
  define('TS_MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_TITLE', '便利店结算');
  define('TS_MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_DESCRIPTION', '便利店结算(根据货款计算手续费)');
  define('TS_MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_EXPLAIN', '请使用LAWSON、3f、MINI STOP、circleK、SUNKUS。<br>不到30000日元的结算另算200日元的手续费，30000日元以上的结算另算350日元的手续费。');
  define('TS_MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_PROCESS_CON', '请使用LAWSON、3f、MINI STOP、circleK、SUNKUS。');
  define('TS_MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_FOOTER', '<p color="#ff0000">※ 便利店结算用的电子邮箱在营业时间内（10时00分から24时00分）发送邮件。<br>※ 订单多的时候发送邮件时间也可能不是营业时间。</p><br><img src="images/rmt_cr.gif">');
  /*
  define('TS_MODULE_PAYMENT_CONVENIENCE_STORE_IMG_FOOTER', '
  <table width="100%" cellspacing="3" cellpadding="0" border="0" id="convenience_img">
  <tr>
  <td align="center"><img src="images/rmt_cr.gif"></td>
  </tr>
  </table>
  ');
  */
  define('TS_MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_FEE', '便利店结算手续费:');
  define('TS_MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_OVERFLOW_ERROR', '因为购买金额超过了便利店结算的限制所以不能受理。');
  define('TS_MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_MAILFOOTER',C_CONVENIENCE_STORE."\n\n". EMAIL_SIGNATURE);
  define('TS_MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_EMAIL_FOOTER', '');
  
  define('TS_MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_SID', '交易代码:');
  define('TS_MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_ZIP_CODE', '邮箱编码:');
  define('TS_MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_ADDRESS', '住处:');
  define('TS_MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_NAME', '姓名:');
  define('TS_MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_L_NAME', '姓:');
  define('TS_MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_F_NAME', '名:');
  define('TS_MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_TEL', '电话号码:');
  define('TS_MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_ERROR_MESSAGE', '便利店结算处理时发生错误。请确认输入内容后再次输入。');
  define('TS_MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_ERROR_MESSAGE_NOE', 'PC邮箱地址和PC邮箱地址(用于确认)不一致。');
  define('TS_MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_ERROR_MESSAGE_NOM', '输入的邮箱地址无法注册。请输入PC邮箱。');
  define('TS_MODULE_PAYMENT_CONVENIENCE_EMAIL_TEXT', 'PC邮箱地址:');
  define('TS_MODULE_PAYMENT_CONVENIENCE_EMAIL_CONFIRMATION_TEXT', 'PC邮箱地址(用于确认):');
  if(NEW_STYLE_WEB===true){
  define('TS_MODULE_PAYMENT_CONVENIENCE_MUST_INPUT', '<span><font color="red">*必须</font></span>');
  }else{
  define('TS_MODULE_PAYMENT_CONVENIENCE_MUST_INPUT', '<small><font color="#AE0E30">(*必须)</font></small>');
  }
  define('TS_MODULE_PAYMENT_CONVENIENCE_STORE_FIELDS_DESCRIPTION', '手机邮箱地址不能用。请在下面的输入框中输入PC邮箱地址。<br>');
  define('TS_MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_CONFIRMATION',"
便利店结算
	请使用LAWSON、3f、MINI STOP、circleK、SUNKUS。 		
	PC邮箱地址:#USER_MAIL#");
//define('TS_MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_DESCRIPTION',"LAWSON、スリーエフ、MINI STOP、サークルK、SUNKUSがご利用いただけます。
//30,000円未満の決済の場合200円、30,000円以上の決済の場合は350円の手数料が別途必要となります。");

define('TS_TEXT_HANDLE_FEE_CONFIRMATION', '手续费:');
