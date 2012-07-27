<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

define('NAVBAR_TITLE', 'ショッピングカート');
define('HEADING_TITLE', 'ショッピングカート');
define('TABLE_HEADING_REMOVE', '削除');
define('TABLE_HEADING_QUANTITY', '数量');
define('TABLE_HEADING_MODEL', '型番');
define('TABLE_HEADING_PRODUCTS', '商品名');
define('TABLE_HEADING_TOTAL', '金額');
define('TEXT_CART_EMPTY', '<b>ショッピングカートには何も入っていません。</b></p><p>当ショッピングシステムは、<b>JavaScript</b>と<b>Cookie</b>を利用しています。ショッピングをご利用いただくためにはブラウザのJavaScriptとCookieの設定が有効になっている必要があります。通常、特に設定を変える必要はございませんが、ご注文がうまくいかない場合は<a href="' . tep_href_link(FILENAME_CONTACT_US) . '">コチラ</a>よりお問い合わせください。</p>
<h3>ブラウザの設定方法</h3>
<p>ご注文ができないお客様へ<br>お手数をおかけいたしますが、下記の設定方法を参照し設定を確認してください。</p>
<img src="images/design/question.gif" alt="" width="16" height="15">&nbsp;<a href="' . tep_href_link(FILENAME_BROWSER_IE6X) . '">Internet&nbsp;Explorer6の設定方法はこちら</a>');
define('SUB_TITLE_SUB_TOTAL', '小計:');
define('SUB_TITLE_TOTAL', '合計:');

define('OUT_OF_STOCK_CANT_CHECKOUT', STOCK_MARK_PRODUCT_OUT_OF_STOCK . ' 印の商品はご希望の数量を確保できません。<br><b>予約注文を承っておりますので、お問い合わせをお願いいたします。</b>');
define('PRODUCTS_WEIGHT_ERROR', STOCK_MARK_PRODUCT_OUT_OF_STOCK . ' 総重量が規定の範囲を超えました。<br>商品を削除するか、または個数を変更して（'.$max_weight_count.'）kg以内にしてください。<br><b>予約注文を承っておりますので、お問い合わせをお願いいたします。</b>');
//define('OUT_OF_STOCK_CAN_CHECKOUT', STOCK_MARK_PRODUCT_OUT_OF_STOCK . ' 印の商品はご希望の数量が在庫にございません。<br>このまま購入手続きを続行していただくと、ご注文の確認画面で発送可能な数量を確認することができます。');
define('OUT_OF_STOCK_CAN_CHECKOUT', STOCK_MARK_PRODUCT_OUT_OF_STOCK . ' 印の商品はご希望の数量が在庫にございません。<br>このまま購入手続きを続行していただくと、後程納期をご連絡させていただきます。');

// '... Make any changes above? Click.' (tamura 2002/03/28 追加)
//define('TEXT_UPDATE_CART_INFO', '<b><font color="#ff0000">※</font> 数量を変更したり削除する場合は更新してください!</b>');   //Add Japanese osCommerce
define('TEXT_UPDATE_CART_INFO', '<b><font color="#ff0000">※</font> 数量をご確認の上「レジへ進む」ボタンをクリックしてください。</b>'); //Add Japanese osCommerce
define('TABLE_HEADING_IMAGE', '画像');
define('TABLE_HEADING_OPERATE', '操作');
define('TEXT_DEL_LINK', '削除');
define('TEXT_SHOPPING_CART_READ_INFO', '【重要】<br> 小計金額が赤色の場合は「買取」となり、表示された金額をお客様へお支払いいたします。<br> 買取金額が200円未満の場合は手数料の関係上、支払方法にて銀行振込を選択することができません。<br> <br> 選択できる支払方法は以下となります。<br> A:来店による支払い<br> B:ポイントの加算（'.STORE_NAME.'会員でなければ表示されません）<br>');
define('TEXT_SHOPPING_CART_NOTICE_TEXT', '"%s未満の注文はできません。合計金額を%s以上にしてから再度お申し込みください。');
define('TEXT_SHOPPING_CART_PICKUP_PRODUCTS', 'こちらの商品もオススメ！！');
define('TEXT_SHOPPING_CART_READ_NOTICE_MONEY','200円未満になる場合は商品名「ウェブマネーの販売」をカートに入れてみてはどうでしょう。');
