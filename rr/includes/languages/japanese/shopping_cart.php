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
define('TABLE_HEADING_TOTAL', '合計');
define('TEXT_CART_EMPTY', '<p class="red"><b>ショッピングカートには何も入っていません。</b></p>
当ショッピングシステムは、<b>JavaScript</b>と<b>Cookie</b>を利用しています。ショッピングをご利用いただくためにはブラウザのJavaScriptとCookieの設定が有効になっている必要があります。通常、特に設定を変える必要はございませんが、ご注文がうまくいかない場合は<a href="' . tep_href_link(FILENAME_CONTACT_US) . '">コチラ</a>よりお問い合わせください。<br><br>
<div class="dot"></div>
<br>
<div style="color: #C85050; font-size: 12px;"><strong>ブラウザの設定方法</strong></div>
<p>下記の設定方法を参照し、設定を確認してください。</p>
<img src="images/design/question.gif" alt="" width="16" height="15">&nbsp;<a href="' . tep_href_link(FILENAME_BROWSER_IE6X) . '">Internet&nbsp;Explorer6の設定方法はこちら</a>');
define('SUB_TITLE_SUB_TOTAL', '小計:');
define('SUB_TITLE_TOTAL', '合計:');

define('OUT_OF_STOCK_CANT_CHECKOUT', STOCK_MARK_PRODUCT_OUT_OF_STOCK . ' 印の商品はご希望の数量を確保できません。<br><b>予約注文を承っておりますので、お問い合わせをお願いいたします。</b>');
//define('OUT_OF_STOCK_CAN_CHECKOUT', STOCK_MARK_PRODUCT_OUT_OF_STOCK . ' 印の商品はご希望の数量が在庫にございません。<br>このまま購入手続きを続行していただくと、ご注文の確認画面で発送可能な数量を確認することができます。');
define('OUT_OF_STOCK_CAN_CHECKOUT', STOCK_MARK_PRODUCT_OUT_OF_STOCK . ' 印の商品はご希望の数量が在庫にございません。<br>このまま購入手続きを続行していただくと、後程納期をご連絡させていただきます。');

// '... Make any changes above? Click.' (tamura 2002/03/28 追加)
//define('TEXT_UPDATE_CART_INFO', '<b><font color="#ff0000">※</font> 数量を変更したり削除する場合は更新してください!</b>');   //Add Japanese osCommerce
define('TEXT_UPDATE_CART_INFO', '<b><font color="#ff0000">※</font> 数量をご確認の上「レジへ進む」ボタンをクリックしてください。</b>'); //Add Japanese osCommerce
define('TABLE_HEADING_IMAGE', '画像');
define('TABLE_HEADING_OPERATE', '削除');
define('TEXT_DEL_LINK', '削除');
