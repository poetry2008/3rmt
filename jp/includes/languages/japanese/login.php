<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/
if (!isset($_GET['origin'])) $_GET['origin']=NULL;
if ($_GET['origin'] == FILENAME_CHECKOUT_PAYMENT) {
  define('NAVBAR_TITLE', 'ご注文');
  define('HEADING_TITLE', 'ご注文は簡単');
  define('TEXT_STEP_BY_STEP', '注文手続きを１ステップごとにお手伝いいたします');
} else {
  define('NAVBAR_TITLE', 'ログイン');
  define('HEADING_TITLE', 'ようこそ!');
  define('TEXT_STEP_BY_STEP', ''); // should be empty
}

define('SEND_MAIL', 'メール受信テストをする');
//define('LINK_SENDMAIL_TITLE', 'メール受信テスト');

define('TEXT_MAIL','メールアドレスを入力してください。');
define('TEXT_FIRST_BUY','以前、お買物をされたことがありますか?');
define('HEADING_NEW_CUSTOMER', 'はじめての方。');
define('TEXT_NEW_CUSTOMER', '<b>［次へ進む］</b>をクリックしてください。');
define('TEXT_NEW_CUSTOMER_INTRODUCTION', '<font color="red"><b>会員登録をしないで注文するお客様もこちらからお手続きください。</b></font><br>会員登録をすると…&nbsp;メールアドレスとパスワードを入力するだけで簡単にログインができて、 '.STORE_NAME.' で便利にお買物ができます。');

define('HEADING_RETURNING_CUSTOMER', STORE_NAME.'会員の方。');
define('TEXT_RETURNING_CUSTOMER', 'メールアドレスとパスワードを入力して、ログインしてください。');
//define('ENTRY_EMAIL_ADDRESS', 'メールアドレス:');
//define('ENTRY_PASSWORD', 'パスワード:');

define('TEXT_PASSWORD_FORGOTTEN', 'パスワードをお忘れの場合はこちらをクリック!');

define('TEXT_LOGIN_ERROR', '<font color="#ff0000"><b>エラー:</b>"メールアドレス" または "パスワード" が一致しませんでした。</font>');
define('TEXT_VISITORS_CART', '<font color="#ff0000"><b>ご注意:</b></font> ログインすると、[ショッピングカート] の商品は [メンバーズ・ショッピングカート] へ自動的に移動します。 <a href="javascript:session_win();"> [詳細情報]</a>');

if(MODULE_ORDER_TOTAL_POINT_STATUS == "true"){
   //通常のポイントシステム
   if(MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVEL != "true") {
      $point_text_1 = MODULE_ORDER_TOTAL_POINT_FEE*100 ;
        if(MODULE_ORDER_TOTAL_POINT_LIMIT != "0"){
          $point_text_2 = '最終購入日より'.MODULE_ORDER_TOTAL_POINT_LIMIT.'日間有効です' ;
        }else{
          $point_text_2 = 'ありません' ;
        }
		define('TEXT_POINT','<p class="main"><i><strong>ポイントシステム</strong></i><br>ポイントサービスは、当店でお買い物をされた場合、購入金額の'.$point_text_1.'%をポイントとして還元しております。<br>
              溜まったポイントは次回のお買い物に1ポイント＝1円で使えます。ポイントの有効期限は'.$point_text_2.'。</p>');
   }else{
   //カスタマーレベル連動型ポイントシステム
    $customer_level_array = explode("||",MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVER_BACK);
	if(!empty($customer_level_array)) {
	   $customer_lebel_string = '<ul>'."\n";
	    for($i=0,$n=sizeof($customer_level_array); $i < $n; $i++){
      	   $customer_lebel_detail = explode(",",$customer_level_array[$i]);
		   $customer_lebel_string .= '<li>今までの当店での購入金額が'.$customer_lebel_detail[2].'円以下のお客様:'.$customer_lebel_detail[0].'&nbsp;&nbsp;<b>'.(int)($customer_lebel_detail[1]*100).'</b>ポイント</li>'."\n" ;
	    }
	   $customer_lebel_string .= '</ul>'."\n";
	   define('TEXT_POINT','<p class="main"><i><strong>ポイントシステム</strong></i><br>ポイントサービスは、当店でお買い物をされた場合、過去'.MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVEL_KIKAN.'日間における購入金額に応じて還元されるポイントレベルが異なります。ポイント還元率は以下の通りです。</p>
              '.$customer_lebel_string.'<p class="main">次回のお買い物に1ポイント＝1円で使えます。ポイントの有効期限は'.MODULE_ORDER_TOTAL_POINT_LIMIT.'日です。</p>');
	 }
  }
//ポイントシステム不採用 		  
 }else{
  define('TEXT_POINT','');
 }  
  		
?>
