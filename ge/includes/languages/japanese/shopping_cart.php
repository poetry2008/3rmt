<?php
/*
  $Id$
*/

define('NAVBAR_TITLE', 'ショッピングカート');
define('HEADING_TITLE', 'ショッピングカート');
define('TABLE_HEADING_REMOVE', '削除');
define('TABLE_HEADING_QUANTITY', '数量');
define('TABLE_HEADING_MODEL', '型番');
define('TABLE_HEADING_PRODUCTS', '商品名');
define('TABLE_HEADING_TOTAL', '金額');
define('TEXT_CART_EMPTY', '<p class="redtext"><b>ショッピングカートには何も入っていません。</b></p>
<p>当ショッピングシステムは、<b>JavaScript</b>と<b>Cookie</b>を利用しています。ショッピングをご利用いただくためにはブラウザのJavaScriptとCookieの設定が有効になっている必要があります。通常、特に設定を変える必要はございませんが、ご注文がうまくいかない場合は<a href="' . tep_href_link(FILENAME_CONTACT_US) . '">コチラ</a>よりお問い合わせください。</p>
<h3>ブラウザの設定方法</h3>
<p>ご注文ができないお客様へ<br>お手数をおかけいたしますが、下記の設定方法を参照し設定を確認してください。</p>
<img src="images/design/question.gif" alt="" width="16" height="15">&nbsp;<a href="' . tep_href_link(FILENAME_BROWSER_IE6X) . '">Internet&nbsp;Explorer6の設定方法はこちら</a>');
define('SUB_TITLE_SUB_TOTAL', '小計:');
define('SUB_TITLE_TOTAL', '合計:');

define('OUT_OF_STOCK_CANT_CHECKOUT', STOCK_MARK_PRODUCT_OUT_OF_STOCK . ' 印の商品はご希望の数量を確保できません。<br><b>予約注文を承っておりますので、お問い合わせをお願いいたします。</b>');
define('PRODUCTS_WEIGHT_ERROR', STOCK_MARK_PRODUCT_OUT_OF_STOCK . ' 総重量が規定の範囲を超えました。<br>商品を削除するか、または個数を変更して（'.$max_weight_count.'）kg以内にしてください。');
//define('OUT_OF_STOCK_CAN_CHECKOUT', STOCK_MARK_PRODUCT_OUT_OF_STOCK . ' 印の商品はご希望の数量が在庫にございません。<br>このまま購入手続きを続行していただくと、ご注文の確認画面で発送可能な数量を確認することができます。');
define('OUT_OF_STOCK_CAN_CHECKOUT', STOCK_MARK_PRODUCT_OUT_OF_STOCK . ' 印の商品はご希望の数量が在庫にございません。<br>このまま購入手続きを続行していただくと、後程納期をご連絡させていただきます。');

define('TEXT_UPDATE_CART_INFO', '<b><font color="#ff0000">※</font> 数量をご確認の上「レジへ進む」ボタンをクリックしてください。</b>');
define('TABLE_HEADING_IMAGE', '画像');
define('TABLE_HEADING_OPERATE', '操作');
define('TEXT_DEL_LINK', '削除');
