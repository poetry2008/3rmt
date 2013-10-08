<?php
/*
  $Id$
*/
define('INPUT_PREORDER_SEND_MAIL', 'メールアドレス:');
define('PREORDER_SUCCESS_TEXT', 'メールアドレスに間違いがあった場合は上記、送信フォームより正しいメールアドレスに修正後、再度メールを送信してください。<br>'.STORE_NAME.'をご利用いただくには、メール認証を行う必要があります。<br>メール内に記載してあるURLをクリックしメール認証を完了させてから'.STORE_NAME.'を利用することが出来ます。<br>正常に受信できる場合：「送信」後5分以内で'.STORE_NAME.'からの確認メールが届きます。<br>受信できない場合：「送信」後5分以上経過しても受信できない場合は、スパムフィルター等で受け取りを拒否されている可能性が高いです。<br>メール受信の設定に関しては、 %sをご参考ください。');
define('ALREADY_SEND_PREMAIL_TEXT', 'エラーが発生しました。\nお手数をお掛けいたしますが、トップページに戻り再度やり直してください');
define('PREORDER_EMAIL_PATTENR_WRONG', 'メールアドレスを正しくご入力下さい。');
define('PREORDER_SUCCESS_TITLE', '予約注文手続完了');
define('PREORDER_SUCCESS_HEAD_TITLE', '予約注文の手続が完了しました！！');
define('PREORDER_SUCCESS_APPOINT_CONTENT', '予約内容');
define('PREORDER_SUCCESS_APPOINT_PRODUCT_NAME', '商品名：');
define('PREORDER_SUCCESS_APPOINT_PRODUCT_NUM', '希望個数：');
define('PREORDER_SUCCESS_APPOINT_PRODUCT_DATE', '有効期限：');
define('PREORDER_SUCCESS_APPOINT_PAYMENT_NAME', '支払方法:');
define('PREORDER_SUCCESS_APPOINT_COMMENT', '備考:');
define('PREORDER_SUCCESS_UNIT_TEXT', '個');
define('PREORDER_SUCCESS_YEAR_TEXT', '年');
define('PREORDER_SUCCESS_MONTH_TEXT', '月');
define('PREORDER_SUCCESS_DAY_TEXT', '日');
define('PREORDER_SUCCESS_READ_INFO', STORE_NAME.'では、%sの予約サービスを行っております。 <br>ご希望する数量が弊社在庫にある場合は「%s」をクリックしてお手続きください。<br><br>「予約注文受付」メールをお送りしましたのでご確認ください。<br>納期に付きましては、24時間以内にご回答いたします。<br><br>今すぐ電子メールをご確認ください。「予約注文受付」メールが届いていない場合は、受付が完了しておりません。<br>メールアドレスをご確認の上、再度お申し込みをお願いいたします。<br><br>10分経過してもメールが届かない場合は、再送いたしますのでご連絡ください。<br>注意：メールが届かないときは、以下のことを必ずご確認ください。<br>＜迷惑メールフォルダの確認＞弊社のメールが
    「迷惑メールフォルダ」や「ゴミ箱」に振り分けされ見落としていませんか？<br>＜メールドメインの受信制限を設定している＞<br>'.STORE_DOMAIN.'のメールドメインを受信するように設定をお願いいたします。<br>＜それでも届かないときは、メールアドレス変更＞<br>お客様情報から今すぐご登録メールアドレスの変更をお願いいたします。');
define('PREORDER_SUCCESS_UNACTIVE_HEAD_TITLE', 'メール認証');
define('PREORDER_SUCCESS_UNACTIVE_TITLE', 'メール認証');
define('PREORDER_SUCCESS_TEXT_LINK', '「フリーメールでメールが受け取れない方へ」');
define('PREORDER_SUCCESS_ACTIVE_INFO_TEXT', '<font size="3" color="#ff0000"><b>メールを送信致しました。</b></font><br><br>予約注文の手続を完了するには、お客様のメールアドレスを'.STORE_NAME.'に登録していただく必要がございます。<br>メール本文よりメール認証URLをクリックしてください。メール認証完了後、自動で予約手続が完了いたします。<br><br> メールが届かない場合は、下記入力フォームにて認証メールをもう一度送信してください');
define('PREORDER_SUCCESS_ACTIVE_HEAD_TITLE', '予約手続が完了しました');
define('PREORDER_ACTIVE_SUCCESS_READ_INFO', '今すぐ電子メールをご確認ください。「予約注文受付」メールが届いていない場合は、受付が完了しておりません。<br> メールアドレスをご確認の上、再度お申し込みをお願いいたします。<br> 10分経過してもメールが届かない場合は、再送いたしますのでご連絡ください。<br>注意：メールが届かないときは、以下のことを必ずご確認ください。<br> ＜迷惑メールフォルダの確認＞<br> 弊社のメールが 「迷惑メールフォルダ」や「ゴミ箱」に振り分けされ見落としていませんか？<br> ＜メールドメインの受信制限を設定している＞<br> '.STORE_DOMAIN.'のメールドメインを受信するように設定をお願いいたします。<br> ＜それでも届かないときは、メールアドレス変更＞<br> お手数ではございますが、別のメールアドレスをご用意いただき、再度お手続きをお願いいたします。<br>');
define('PREORDER_ACTIVE_SUCCESS_READ_HEAD', '「予約注文受付」メールをお送りしましたのでご確認ください。<br>納期に付きましては、入荷目処が立ち次第、ご回答いたします。');
define('PREORDER_ACTIVE_SUCCESS_READ_BOTTOM', STORE_NAME.'では、%sの予約サービスを行っております。 <br>ご希望する数量が弊社在庫にある場合は「%s」をクリックしてお手続きください。');
define('TEXT_HEADER_INFO','<table width="100%" cellspacing="0" cellpadding="0"
    border="0"> <tbody><tr> <td width="20%"><table width="100%" cellspacing="0"
    cellpadding="0" border="0"> <tbody><tr> <td width="50%" align="right"><img
    width="1" height="5" alt="" src="images/pixel_silver.gif"></td> <td
    width="50%"><img width="100%" height="1" alt=""
    src="images/pixel_silver.gif"></td> </tr> </tbody></table></td> <td width="20%"><img width="100%"
    height="1" alt="" src="images/pixel_silver.gif"></td> <td width="20%"><table
    width="100%" cellspacing="0" cellpadding="0" border="0"> <tbody><tr> <td
    width="50%"><img width="100%" height="1" alt=""
    src="images/pixel_silver.gif"></td> <td width="50%"><img alt=""
    src="images/checkout_bullet.gif"></td> </tr> </tbody></table></td> </tr> <tr>
    <td width="20%" align="center" class="checkoutBarFrom">'.CHECKOUT_BAR_PAYMENT.'</td> <td width="20%"
    align="center" class="checkoutBarFrom">'.CHECKOUT_BAR_CONFIRMATION.'</td> <td width="20%"
    align="center" class="checkoutBarCurrent">'.CHECKOUT_BAR_FINISHED.'</td> </tr> </tbody></table>');
define('TEXT_NOTIFY_PRODUCTS', '本日ご注文いただいた商品の最新情報を 電子メールでお届けしております。ご希望の方は、商品ごとにチェックして <b>[次へ進む]</b> を押してください。');
define('TEXT_SEE_ORDERS', 'あなたのご注文履歴は、<a href="' . tep_href_link(FILENAME_ACCOUNT, '', 'SSL') . '">\'会員\'</a> ページの <a href="' . tep_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL') . '">\'履歴\'</a>ボタンをクリックするとご覧になれます。');
define('TEXT_CONTACT_STORE_OWNER', 'もしご注文手続きについてご質問がございましたら、直接<a href="' . tep_href_link(FILENAME_CONTACT_US) . '">店主</a>までお問い合わせください。');
