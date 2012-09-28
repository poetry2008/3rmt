<?php
/*
  $Id$
*/

// 楽天銀行
  define('TS_MODULE_PAYMENT_RAKUTEN_BANK_TEXT_TITLE', '乐天银行');
  define('TS_MODULE_PAYMENT_RAKUTEN_BANK_TEXT_DESCRIPTION', '乐天银行(货款包含手续费)');
  define('TS_MODULE_PAYMENT_RAKUTEN_BANK_FIELDS_DESCRIPTION', '请在下列输入框写上电话号码');
  define('TS_MODULE_PAYMENT_RAKUTEN_BANK_TEXT_EXPLAIN','汇入乐天银行时请选择。<br>转账手续费由客户承担。');
  define('TS_MODULE_PAYMENT_RAKUTEN_BANK_TEXT_PROCESS_CON', '使用LAWSON、3F、MINI STOP、circleK、SUNKUS。');
  define('TS_MODULE_PAYMENT_RAKUTEN_BANK_TEXT_FOOTER', '※ 请在敝公司营业时间内发送SmartPitsheet。');
  /*
  define('TS_MODULE_PAYMENT_RAKUTEN_BANK_IMG_FOOTER', '
  <table width="100%" cellspacing="3" cellpadding="0" border="0" id="convenience_img">
  <tr>
  <td align="center"><img src="images/rmt_cr.gif"></td>
  </tr>
  </table>
  ');
  */
  define('TS_MODULE_PAYMENT_RAKUTEN_BANK_TEXT_FEE', '乐天银行手续费:');
  define('TS_MODULE_PAYMENT_RAKUTEN_BANK_TEXT_OVERFLOW_ERROR', '购买金额因超过乐天银行的限制而无法处理。');
  define('TS_MODULE_PAYMENT_RAKUTEN_BANK_TEXT_MAILFOOTER',nl2br(C_RAKUTEN_BANK."\n\n"));
  define('TS_MODULE_PAYMENT_RAKUTEN_BANK_TEXT_TELNUMBER_FOOTER', '');
  
  define('TS_MODULE_PAYMENT_RAKUTEN_BANK_TEXT_SID', '交易代码:');
  define('TS_MODULE_PAYMENT_RAKUTEN_BANK_TEXT_ZIP_CODE', '邮政编码:');
  define('TS_MODULE_PAYMENT_RAKUTEN_BANK_TEXT_ADDRESS', '住址:');
  define('TS_MODULE_PAYMENT_RAKUTEN_BANK_TEXT_NAME', '姓名:');
  define('TS_MODULE_PAYMENT_RAKUTEN_BANK_TEXT_L_NAME', '姓:');
  define('TS_MODULE_PAYMENT_RAKUTEN_BANK_TEXT_F_NAME', '名:');
  define('TS_MODULE_PAYMENT_RAKUTEN_BANK_TEXT_TEL', '电话号码:');
  define('TS_MODULE_PAYMENT_RAKUTEN_BANK_TEXT_ERROR_MESSAGE', '乐天银行的处理中发生错误。请确认输入内容后再次输入。');
  define('TS_MODULE_PAYMENT_RAKUTEN_BANK_TEXT_ERROR_MESSAGE_NOE', '电话号码和电话号码（确认用）不一致。');
  define('TS_MODULE_PAYMENT_RAKUTEN_BANK_TEXT_ERROR_MESSAGE_NOM', '输入错误、电话号码的格式有误或包含无法使用的字符。请确认输入内容后再次输入。');
  define('TS_MODULE_PAYMENT_RAKUTEN_TELNUMBER_TEXT', '电话号码:');
  define('TS_MODULE_PAYMENT_RAKUTEN_TELNUMBER_CONFIRMATION_TEXT', '电话号码(确认用):');
  if(NEW_STYLE_WEB===true){
  define('TS_MODULE_PAYMENT_RAKUTEN_MUST_INPUT', '<span><font color="red">*必须</font></span>');
  }else{
   define('TS_MODULE_PAYMENT_RAKUTEN_MUST_INPUT', '<small><font color="#AE0E30">(*必须)</font></small>');
  }

  define('TS_MODULE_PAYMENT_RAKUTEN_BANK_TEXT_CONFIRMATION', '
电话号码:#TELNUMBER#

请汇入下述账户。
------------------------------------------
银行名　　：　乐天银行（Ebank银行）
分店名　　：　华尔兹分店
账户类别　：　普通
账户持有人　：　1,2,3,4,5,6）iimy
账户号码　：　7003965
------------------------------------------
※ 请务必转入订购时输入的姓名。
※ 转账手续费由客户承担。
※ 交易日期时间前如无法支付、请务必联络。
　 没有联络的情况下、会解除库存抵押。
※ 转账需要在订购后7日内进行。
※ iimy有限公司确认到款后合同生效。');
