<?php
/*
  $Id: tell_a_friend.php,v 1.7 2003/05/06 12:10:03 hawk Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

define('NAVBAR_TITLE', '商品を予約する');
define('HEADING_TITLE', '\'%s\' を予約する');
define('HEADING_TITLE_ERROR', '商品を予約する');
define('ERROR_INVALID_PRODUCT', '商品が見つかりません...');

define('FORM_TITLE_CUSTOMER_DETAILS', 'お客様について');
define('FORM_TITLE_FRIEND_DETAILS', '予約商品について');
define('FORM_TITLE_FRIEND_MESSAGE', 'ご要望');

define('FORM_FIELD_CUSTOMER_NAME', 'お名前:');
define('FORM_FIELD_CUSTOMER_EMAIL', 'メールアドレス:');
define('FORM_FIELD_FRIEND_NAME', 'ご希望個数:');
define('FORM_FIELD_FRIEND_EMAIL', '期限:');

define('TEXT_EMAIL_SUCCESSFUL_SENT', '<p><b>「予約確認メール」を&nbsp;<span class="red">%s</span>&nbsp;へ送信いたしました。</b><br>
納期につきましては24時間以内にご回答いたします。<br>
<br>
今すぐ電子メールをご確認ください。「予約確認メール」が届いていない場合は、受付が完了しておりません。
メールアドレスをご確認の上、再度お申し込みをお願いいたします。<br></p>
<br>
<div class="dot"></div>
<br>
<h4>注意</h4>
<br>
<p>メールが届かないときは、以下のことを必ずご確認ください。<br>
<b>＜迷惑メールフォルダの確認＞</b><br>
弊社のメールが 「迷惑メールフォルダ」や「ゴミ箱」に振り分けされ見落としていませんか？<br>
<b>＜メールドメインの受信制限を設定している＞</b><br>
worldmoney.jpのメールドメインを受信するように設定をお願いいたします。<br>
<b>＜それでも届かないときは、メールアドレス変更＞</b><br>
メールアドレスをご確認の上、再度お申し込みをお願いいたします。<br>
</p>
<br>
<div class="dot"></div>
<br>
<h4>予約内容</h4>
<br>
<table>
<tr><td class="main">商品名</td><td>：</td><td class="main"><b>%s</b></td></tr>
<tr><td class="main">希望個数</td><td>：</td><td class="main"><b>%s個</b></td></tr>
<tr><td class="main">期限</td><td>：</td><td class="main"><b>%s</b></td></tr>
</table>
');

define('TEXT_EMAIL_SUBJECT', '%sの予約を承りました【%s】');
define('TEXT_EMAIL_INTRO', '%s 様' . "\n\n"
. 'この度は、%sをご利用いただき、誠にありがとうございます。' . "\n\n"
. '下記の内容にてご予約を承りました。ご確認ください。' . "\n"
. '尚、納期につきましては24時間以内にご回答いたします。' . "\n\n"
. '━━━━━━━━━━━━━━━━━━━━━' . "\n"
. '▼お名前　　　　　：%s' . "\n"
. '▼メールアドレス　：%s' . "\n"
. '━━━━━━━━━━━━━━━━━━━━━' . "\n\n"
. '▼予約内容' . "\n"
. '	------------------------------------------' . "\n"
. '	予約商品      ：%s' . "\n"
. '	希望個数　　　：%s個' . "\n"
. '	期限　　　　　：%s' . "\n"
. '	------------------------------------------');
define('TEXT_EMAIL_LINK', 'この商品の詳細は、下記のリンクをクリックするか、リンクをブラウザに' . "\n"
. 'コピー＆ペーストしてください。' . "\n\n" . '%s' . "\n\n");
define('TEXT_EMAIL_SIGNATURE', '[ご連絡・お問い合わせ先]━━━━━━━━━━━━' . "\n"
. '株式会社 iimy' . "\n"
. 'support@worldmoney.jp' . "\n"
. 'http://rmt.worldmoney.jp/' . "\n"
. '━━━━━━━━━━━━━━━━━━━━━━━');
?>
