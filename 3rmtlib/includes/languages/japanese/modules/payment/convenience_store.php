<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

// コンビニ決済
  define('MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_TITLE', 'コンビニ決済');
  define('MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_DESCRIPTION', 'コンビニ決済(代金に手数料が連動)');
  define('MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_PROCESS', 'コンビニ決済手数料が別途かかります。');
  define('MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_FEE', 'コンビニ決済手数料:');
  define('MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_OVERFLOW_ERROR', 'お買い上げ金額がコンビニ決済の制限を超えたためお取り扱いできません。');
  define('MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_MAILFOOTER',C_CONVENIENCE_STORE."\n\n". EMAIL_SIGNATURE);
  
  define('MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_SID', '取引コード:');
  define('MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_ZIP_CODE', '郵便番号:');
  define('MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_ADDRESS', '住所:');
  define('MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_NAME', '氏名:');
  define('MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_L_NAME', '姓:');
  define('MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_F_NAME', '名:');
  define('MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_TEL', '電話番号:');
  define('MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_ERROR_MESSAGE', 'コンビニ決済の処理中にエラーが発生しました. 入力内容を訂正しもう一度試してください。　');
?>