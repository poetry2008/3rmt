<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

define('NAVBAR_TITLE_1', 'ショッピングカート');
define('NAVBAR_TITLE_2', '手続完了');

define('HEADING_TITLE', 'ご注文の手続きが完了しました!!');

define('TEXT_SUCCESS', '<span class="red"><b>「注文受付」メールをお送りしましたのでご確認ください。</b></span><br>
<b>10分経過してもメールが届かない場合は、再送いたしますのでご連絡ください。</b><br>
注意：メールが届かないときは、以下のことを必ずご確認ください。<br>
＜迷惑メールフォルダの確認＞<br>
弊社のメールが 「迷惑メールフォルダ」や「ゴミ箱」に振り分けされ見落としていませんか？<br>
＜メールドメインの受信制限を設定している＞<br>
ff14-rmt.comのメールドメインを受信するように設定をお願いいたします。<br>
＜それでも届かないときは、メールアドレス変更＞<br>
お客様情報から今すぐご登録メールアドレスの変更をお願いいたします。<br>
<div class="underline">&nbsp;</div>
<span class="red"><b>お取り引きの際の注意点</b></span><br>
電子メールに記載しておりますご注文内容および、弊社キャラクター名を事前にご確認ください。<br>
弊社キャラクターが変更となる場合は、お取り引き前に電子メールにてご案内を差し上げます。
<div class="underline">&nbsp;</div>
<b>買取依頼のお客様へ</b><br>
最近、リネージュ2において詐欺行為を行うキャラクターの存在が多数報告されております。不特定多数のキャラクターへトレードを申し込み、ゲーム通貨やアイテムを不正に取得する手口となります。
トレードの際は、キャラクター名を十分ご確認いただきますようお願い申し上げます。弊社キャラクター以外へトレードされた場合、弊社では一切の保障をいたしかねます。
<div class="underline">&nbsp;</div>
<img src="images/stock.gif" alt="販売アイコン" width="50" height="50"><b>「販売」商品について</b><br>
お客様ご指定の取引日時にログインをお願いいたします。10分経過しましても弊社キャラクターが現れない場合は、サポートセンターへお問い合わせください。
<br><br>
<div class="dot">&nbsp;</div>
<br>
<img src="images/preorder.gif" alt="取り寄せアイコン" width="50" height="50"><b>「取り寄せ」商品について</b><br>
ご注文いただいた商品は、通常１～５営業日でお届けしております。
<br><br>
<div class="dot">&nbsp;</div>
<br>
<img src="images/sell.gif" alt="買取アイコン" width="50" height="50"><b>「買取」商品について</b><br>
お客様ご指定の取引日時にログインをお願いいたします。10分経過しましても弊社キャラクターが現れない場合は、サポートセンターへお問い合わせください。
<br><br>
<div class="dot">&nbsp;</div>');
define('TEXT_NOTIFY_PRODUCTS', '本日ご注文いただいた商品の最新情報を 
電子メールでお届けしております。ご希望の方は、商品ごとにチェックして <b>[次に進む]</b> を押してください。');
define('TEXT_SEE_ORDERS', 'あなたのご注文履歴は、<a href="' . tep_href_link(FILENAME_ACCOUNT, '', 'SSL') . '">\'会員\'</a> ページの <a href="' . tep_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL') . '">\'履歴\'</a>ボタンをクリックするとご覧になれます。');
define('TEXT_CONTACT_STORE_OWNER', 'もしご注文手続きについてご質問がございましたら、直接<a href="' . tep_href_link(FILENAME_CONTACT_US) . '">店主</a>までお問い合わせください。');
define('TEXT_THANKS_FOR_SHOPPING', 'ご注文ありがとうございました。');

define('TABLE_HEADING_COMMENTS', 'ご注文についてのコメント');

define('TABLE_HEADING_DOWNLOAD_DATE', 'ダウンロード有効期限: ');
define('TABLE_HEADING_DOWNLOAD_COUNT', ' 回ダウンロードできます');
define('HEADING_DOWNLOAD', 'こちらから商品をダウンロードしてください:');
define('FOOTER_DOWNLOAD', '後で [%s] ページから商品をダウンロードすることもできます。');
?>
